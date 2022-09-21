<?php defined('BASEPATH') or exit('No direct script access allowed');

class Maintenance2_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
		$this->types = ['', 'Plumbing', 'Electric', 'Appliances', 'Heat/Air', 'Locks & Key', 'Pest', 'Other'];
		$this->priorities = ['Low', 'Normal', 'High'];
		$this->statuses = ['','New', 'In Progress', 'Completed', 'Deffered', 'Closed'];
		$this->dueLabels = ['','Today', 'Yesterday', 'Tommorrow', 'Earlier This Week', 'Later This Week', 'Earlier This month' , 'Later This month', 'Older', 'Newer', 'No Due Date'];
    }

    public function generateFilters() {
    	$result = Array();
		
		$temp = new stdClass();
		$temp->field = 'status';
		$temp->column_name = 'Status';
		$temp->data = json_decode('[{"name": "New", "value": 1, "color":"#fbb03b"},{"name": "In Progress", "value": 2, "color":"#7fafd0"},{"name": "Completed", "value": 3, "color":"#73b680"},{"name": "Defferred", "value": 4, "color":"#a56bda"},{"name": "Closed", "value": 5, "color":"#a5a5a6"}]');
		$result[] = $temp;

		$q = $this->db->select('priority as value')->group_by('priority')->from('maintenance')->get();
		$temp = new stdClass();
		$temp->field = 'priority';
		$temp->column_name = 'Priority';
		$temp->data = $q->result();
		foreach($temp->data as &$data) {
			$data->name = $this->priorities[$data->value];
		}
		$result[] = $temp;

		$q = $this->db->select('p.id as value, p.name as name')->order_by('p.name','asc')->group_by('p.id')->from('maintenance m')->join('properties p', 'property = p.id', 'left')->get();
		$temp = new stdClass();
		$temp->field = 'property_id';
		$temp->column_name = 'Property';
		$temp->data = $q->result();
		foreach($temp->data as &$data) {
			if(!$data->value) {
				$data->name = 'No property';
				$data->value = 0;
				break;
			}
		}
		$result[] = $temp;

		$q = $this->db->select('p.id as value, LTRIM(CONCAT_WS(" ",p.first_name,p.last_name)) as name')->group_by('p.id')->from('maintenance m')->join('profiles p', 'assigned_to = p.id', 'left')->get();
		$temp = new stdClass();
		$temp->field = 'assigned_to';
		$temp->column_name = 'Assigned to';
		$temp->data = $q->result();
		foreach($temp->data as &$data) {
			if(!$data->value) {
				$data->name = 'Not assigned';
				$data->value = 0;
				break;
			}
		}
		$result[] = $temp;

		$q = $this->db->select('category as value')->group_by('category')->from('maintenance')->get();
		$temp = new stdClass();
		$temp->field = 'type';
		$temp->column_name = 'Type';
		$temp->data = $q->result();
		foreach($temp->data as &$data) {
			$data->name = $this->types[$data->value];
		}
		$result[] = $temp;


		$dueCalc = 'if(CURDATE() = due_date,1
		,if(CURDATE() = date_add(due_date, INTERVAL 1 DAY),2
		,if(CURDATE() = date_add(due_date, INTERVAL -1 DAY),3,
		,if((YEARWEEK(CURDATE(), 1) = YEARWEEK(due_date, 1)) and (CURDATE() > due_date),4,
		,if((YEARWEEK(CURDATE(), 1) = YEARWEEK(due_date, 1)) and (CURDATE() < due_date),5,
		,if((month(CURDATE()) = month(due_date)) and (year(CURDATE()) = year(due_date)) and (CURDATE() < due_date),7,
		,if((month(CURDATE()) = month(due_date)) and (year(CURDATE()) = year(due_date)) and (CURDATE() > due_date),6,
		,if((CURDATE() > due_date),8
		,if(CURDATE() < due_date,9,
		,10)))))))))';

		$q = $this->db->select($dueCalc.' as value')->group_by($dueCalc)->from('maintenance')->get();
		$temp = new stdClass();
		$temp->field = 'due_date_calc';
		$temp->column_name = 'Due';
		$temp->data = $q->result();
		foreach($temp->data as &$data) {
			$data->name = $this->dueLabels[$data->value];
		}
		$result[] = $temp;


		$temp = new stdClass();
		$temp->field = 'tags';
		$temp->column_name = 'Tags';
		$temp->data = $this->db->select('id as value, text as name')->from('maintenance_tags')->get()->result();
		$result[] = $temp;

		return $result;
	}

	function categoriesList() {
		$result = Array();
    	foreach($this->types as $i => $type) {
    		if($i == 0) continue;
			$temp = new stdClass();
			$temp->id = $i;
			$temp->name = $type;
    		$result[] = $temp;
		}
    	return $result;
	}

	function statusList() {
		$result = Array();
    	foreach($this->statuses as $i => $status) {
    		if($i == 0) continue;
			$temp = new stdClass();
			$temp->id = $i;
			$temp->name = $status;
    		$result[] = $temp;
		}
    	return $result;
	}

	function addTicket($data, $files=null) {
    	if(strlen($data['message']) > 0)
    		$message = Array("name" => $_SESSION['first_name'] ? $_SESSION['first_name'] : 'Admin', "text" => $data['message'], "internal" => $data['internal'] ? 1 : 0);
    	unset($data['message']);
    	unset($data['internal']);
		$data['attention'] = $data['attention'] ? 1 : 0;
		$this->db->insert('maintenance', $data);
		$tid = $this->db->insert_id();
		if(isset($message)) {
			$message['ticket_id'] = $tid;
			$this->db->insert('maintenance_messages', $message);
		}
		if($files) $this->uploadFiles($files, $tid);
	}

	function editTicket($data, $id, $files=null) {
    	unset($data['message']);
		$data['attention'] = $data['attention'] ? 1 : 0;
		//$data['status'] = $data['complete'] ? 1 : 0;
		foreach($data['filedelete'] as $delete) {
			$file_pointer = $this->db->get_where('maintenance_files', array('id'=>$delete))->row()->url;
			unlink($file_pointer);
			$this->db->delete('maintenance_files', array('id'=>$delete));
		}
		unset($data['filedelete']);
		unset($data['complete']);
		$this->db->where('id', $id)->set($data)->update('maintenance');
		if($files) $this->uploadFiles($files, $id);

	}

	public function uploadFiles($images, $id) {
		$uploadFileDir = 'uploads/tenant/';
		$response = new stdClass();
		$response->images = Array();
		$response->videos = Array();
		$length = count($images['name']);
		for($i = 0; $i < $length; $i++ ) {
			$fileName = $images['name'][$i];
			$fileTmpPath = $images['tmp_name'][$i];
			$fileNameCmps = explode(".", $fileName);
			$fileExtension = strtolower(end($fileNameCmps));
			$newFileName = md5(time() . $fileName) . '.' . $fileExtension;
			$dest_path = $uploadFileDir . $newFileName;
			move_uploaded_file($fileTmpPath, $dest_path);
			$this->db->insert('maintenance_files', Array('url'=>$dest_path, 'type'=>$this->getFileType($dest_path), 'ticket_id'=>$id));
		}

	}

	function getFileType($name) {
		$extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
		return in_array($extension, ['png', 'jpg', 'gif', 'jpeg']) ? 0 : 1;
	}

	function getTicket($id) {
		return $this->db->get_where('maintenance', Array('id' => $id))->row();
	}

	function getAttachments($id) {
		return $this->db->get_where('maintenance_files', Array('ticket_id' => $id))->result();
	}

	function addMessage($data, $tid) {
    	$data['ticket_id'] = $tid;
		$data['name'] = $_SESSION['first_name'] ? $_SESSION['first_name'] : 'Admin';
		$data['profile_id'] = $_SESSION['profileId'] ? $_SESSION['profileId'] : 0;
		$this->db->insert('maintenance_messages', $data);
	}

	function instantUpdate($data, $tid) {
		$insert = Array($data['changedField']=>$data['newValue']);
        $this->db->update('maintenance', $insert, array('id' => $tid));
	}

	function getMessages($tid) {
		return $this->db->get_where('maintenance_messages', Array('ticket_id' => $tid))->result();
	}

	function getEmployeeList() {
		return $this->db->get_where('profiles', Array('profile_type_id' => 4))->result();
	}
}

