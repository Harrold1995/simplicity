<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Managerportal_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('encryption_model');
		$this->load->model('maintenance2_model');
		$this->load->library('JWT');
		$this->CONSUMER_SECRET = JWT_SECRET_KEY;
		$this->CONSUMER_TTL = 86400;
	}

	public function generateToken($user_id, $lease_id = null){
		$array = array(
			'userid'=>$user_id,
			'issuedAt'=>date(DATE_ISO8601, strtotime("now")),
			'ttl'=>$this->CONSUMER_TTL
		);
		if($lease_id) $array['leaseid'] = $lease_id;
		return $this->jwt->encode($array, $this->CONSUMER_SECRET);
	}

	public function checkLogin($data) {
		$email = $data['user']['email'];
		//echo password_hash($data['password'], PASSWORD_DEFAULT);
		$q = $this->db->get_where('users', Array('username' => $email));
		$response = new stdClass();
		if($q->num_rows() == 0) {
			$response->status = 0;
			$response->message = 'User not found.';
		} else {
			if(password_verify($data['user']['password'], $q->row()->password)) {
				$result = $this->db->get_where('users', Array('id' => $q->row()->id))->row();
				$response->status = 1;
				$response->message = 'Login successful.';
				$token = $this->generateToken($q->row()->id);
				$response->token = $token;
				$response->user = $result;
				$response->user->clockIn = $this->getClockData($response->user->profile_id);
				//$response->user->settings = $this->getCompanySettings();
			} else {
				$response->status = 0;
				$response->message = 'Your password doesn\'t match our records, please try again';
			}
		}
		return $response;


	}

	public function getUserIdFromToken($token = null) {
		if($token == null) {
			$headers = apache_request_headers();
			if (isset($headers['Authorization'])) {
				$matches = array();
				preg_match('/Token (.*)/', $headers['Authorization'], $matches);
				if (isset($matches[1])) {
					$token = $matches[1];
				}
			}
		}

		$data = null;
		try {
			$data = $this->jwt->decode($token, $this->CONSUMER_SECRET);
		} catch(exception $e) {
			//print_r($e);
		}

		return $data && $data->userid ? $data->userid : false;
	}

	public function getUserInfo($uid) {
		$q = $this->db->select('email, first_name, last_name, username, id, phone, profile_id')->get_where('users', Array('id' => $uid));
		$response = new stdClass();
		if($q->num_rows() == 0) {
			$response->status = 0;
			$response->message = 'User not found.';
		} else {
			$response->status = 1;
			$response->user = $q->row();
			$response->user->clockIn = $this->getClockData($q->row()->profile_id);
		}
		return $response;
	}

	public function getTenantInfo($uid) {
		$q = $this->db->select('email, first_name, last_name, id, phone')->get_where('profiles', Array('id' => $uid));
		return $q->row();
	}

	public function getTickets($uid, $filters) {
		foreach($filters as $field => $filter) {
			switch ($field) {
				case 'property_id':
					if(count($filter)) $this->db->where_in('m.property', $filter);
					break;
				case 'tenant': case 'priority': case 'status': case 'assigned_to': case 'type':
					if(count($filter)) $this->db->where_in('m.'.$field, $filter);
					break;
				case 'due_date_calc':
					if(count($filter)) $this->db->having('due_date_calc in ('.implode(',', $filter).')');
					break;
			}
		}
		$this->db->select('m.*,p.name as propname, if(CURDATE() = m.due_date,1
		 ,if(CURDATE() = date_add(m.due_date, INTERVAL 1 DAY),2
		 ,if(CURDATE() = date_add(m.due_date, INTERVAL -1 DAY),3,
		 ,if((YEARWEEK(CURDATE(), 1) = YEARWEEK(m.due_date, 1)) and (CURDATE() > m.due_date),4,
		 ,if((YEARWEEK(CURDATE(), 1) = YEARWEEK(m.due_date, 1)) and (CURDATE() < m.due_date),5,
		 ,if((month(CURDATE()) = month(m.due_date)) and (year(CURDATE()) = year(m.due_date)) and (CURDATE() < m.due_date),7,
		 ,if((month(CURDATE()) = month(m.due_date)) and (year(CURDATE()) = year(m.due_date)) and (CURDATE() > m.due_date),6,
		 ,if((CURDATE() > m.due_date),8
		 ,if(CURDATE() < m.due_date,9,
		 ,10))))))))) as due_date_calc');
		 $this->db->from('maintenance m');
		 $this->db->join('properties p','m.property = p.id', 'LEFT');
		 $q = $this->db->get();
		$response = new stdClass();
		$response->status = 1;
		$response->tickets = $q->result();
		$response->filters = $this->maintenance2_model->generateFilters();
		$tenantfilter = (Object)["column_name" => "tenant", "field" => "tenant", "data" => []];
		$tenantfilter->data = $this->db->order_by('name ASC')->select('CONCAT_WS(" ",first_name,last_name) as name, id as value')->get_where('profiles', Array('profile_type_id' => 3))->result();
		$response->filters[] = $tenantfilter;
		$statuses = $this->maintenance2_model->statuses;
		foreach($response->tickets as $i => &$ticket) {
			$ticket->statustext = $statuses[$ticket->status];
			$date=date_create($ticket->create_date);
			$ticket->create_date = date_format($date,"m/d/Y");
			if(count($filters['tags']) && !array_intersect(explode(',', $ticket->tags), $filters['tags']))
				unset($response->tickets[$i]);
		}
		$response->tickets = array_values($response->tickets);
		return $response;
	}

	public function getTicket($uid, $id) {

		$this->db->join('profiles p1', 'p1.id = m.assigned_to', 'LEFT')->join('profiles p2', 'p2.id = m.created_by', 'LEFT')->join('properties p', 'p.id = m.property', 'LEFT')->join('units u', 'u.id = m.unit', 'LEFT')->select('tags, description, tenant, assigned_to, CONCAT_WS(" ",p1.first_name,p1.last_name) as assigned_name, CONCAT_WS(" ",p2.first_name,p2.last_name) as created_name, priority, created_by, property as property_id, category as type, m.id, m.title, p.name as property, u.name as unit, due_date, m.status');
		$q = $this->db->get_where('maintenance m', Array('m.id' => $id));
		$response = new stdClass();
		$response->status = 1;
		$ticket = $q->row();

		$ticket->statusid = $ticket->status;
		$ticket->status = $this->maintenance2_model->statuses[$ticket->status];
		$ticket->statusList = [];
		$ticket->priorityList = [];
		$ticket->employeeList = $this->getEmployeeList();
		foreach($this->maintenance2_model->statuses as $i => $s) {
			if($i) {
				$ticket->statusList[] = (Object)Array("label" => $s, "value" => (string)$i);
			}
		}
		foreach($this->maintenance2_model->priorities as $i => $s) {
				$ticket->priorityList[] = (Object)Array("label" => $s, "value" => (string)$i);
		}
		$ticket->type = $this->maintenance2_model->types[$ticket->type];
		$ticket->priorityid = $ticket->priority;
		$ticket->priority = ['Low', 'Normal', 'High'][$ticket->priority];
		$ticket->create_date = date_format(date_create($ticket->create_date),"m/d/Y");
		$ticket->due_date = date_format(date_create($ticket->due_date),"Y-m-d");
		$ticket->tenant_info = $this->getTenantInfo($ticket->tenant);
		$response->ticket = $ticket;
		$response->messages = $this->maintenance2_model->getMessages($ticket->id);
		foreach($response->messages as $i => &$message) {
			$message->date = date_format(date_create($message->date),"m/d/Y");
		}
		return $response;
	}

	public function getTenant($uid, $id) {

		$q = $this->db->get_where('profiles', Array('id' => $id));
		$response = new stdClass();
		$response->status = 1;
		$tenant = $q->row();
		$tenant->ticketnum = $this->db->get_where('maintenance', Array('tenant' => $id, 'status<=' => 5))->num_rows();
		$tenant->img = "https://i.insider.com/5d66d21e6f24eb396b6c8192?width=700";
		$lid = $this->getTenantLease($id)->id;
		if($lid) {
			$q = $this->db->query("SELECT sum(debit - credit) as totalbalance
			FROM transactions
			INNER JOIN transaction_header on transaction_header.id = transactions.trans_id
			WHERE `account_id` = '{$this->site->settings->accounts_receivable}'  AND lease_id = {$lid} and (profile_id = {$id} OR profile_id is null OR profile_id = 0)");
			$tenant->balance = ($q->row()->totalbalance < 0 ? '-' : '').'$'.abs(number_format($q->row()->totalbalance,2,'.',','));

		} else {
			$tenant->balance = 'No Lease Found';
		}
		$response->tenant = $tenant;
		return $response;
	}

	public function getTenantLease($uid) {
		$q = $this->db->query("SELECT  leases.id, leases.end, leases.amount, property_id, leases.unit_id FROM `leases`Inner Join leases_profiles on leases_profiles.lease_id = leases.id  LEFT JOIN units on units.id = leases.unit_id where leases_profiles.profile_id = {$uid} ORDER BY end DESC");
		if($q->num_rows() > 0) {
			//print_r($q->row());
			return $q->row();
		} else {
			return false;
		}
	}


	public function getMessages($uid, $id, $last) {
		$response = new stdClass();
		$response->status = 1;
		$response->messages = $this->maintenance2_model->getMessages($id);
		if($response->messages[count($response->messages)-1]->id > $last) {
			$response->newMessages = $response->messages[count($response->messages)-1]->id;
		} else {
			$response->newMessages = 0;
			$response->messages = [];
		}
		foreach($response->messages as $i => &$message) {
			$message->date = date_format(date_create($message->date),"m/d/Y");
		}
		return $response;
	}

	public function getFiles($uid, $id) {
		$response = new stdClass();
		$response->status = 1;
		$response->files = $this->db->get_where('maintenance_files', Array('ticket_id' => $id))->result();
		$images = ['png', 'jpg', 'jpeg', 'gif'];
		$videos = ['mov', 'avi', 'mp4'];
		foreach($response->files as $i => &$file) {
			$ext = pathinfo($file->url, PATHINFO_EXTENSION);
			if(in_array($ext, $images)) $file->format = 'image'; else
			if(in_array($ext, $videos)) $file->format = 'video'; else
			if($ext == 'pdf') $file->format = 3; else
			$file->format = 4;
			$file->url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].base_url().$file->url;
		}
		return $response;
	}

	public function getSelectData($data) {
		$response = new stdClass();
		switch($data['field']) {
			case 'assigned':
				$response->data = $this->db->select('CONCAT_WS("",first_name," ",last_name) as label, id as value')->get_where('profiles', Array('profile_type_id' => 4))->result();
				break;
			case 'tags':
				$response->data = $this->db->select('text as label, id as value')->get('maintenance_tags')->result();
				break;
			case 'property':
				$response->data = $this->db->select('id as value, name as label')->from('properties')->get()->result();
				break;
			case 'unit':
				if($data['filters']['property']) {
					$this->db->where('property_id', $data['filters']['property']['value']);
				}
				$response->data = $this->db->select('units.id as value, units.name as label, property_id as pid, properties.name as pname')
					->JOIN('properties', 'units.property_id = properties.id', 'LEFT')->from('units')->get()->result();
				break;
			case 'tenant':
				if($data['filters']['property']) {
					$this->db->where('properties.id', $data['filters']['property']['value']);
				}
				if($data['filters']['unit']) {
					$this->db->where('units.id', $data['filters']['unit']['value']);
				}
				$response->data = $this->db->select('profiles.`id` as value, properties.id as pid, leases.id as lid, properties.name as pname, concat_ws(" ", `first_name`, last_name) as label, units.id as unit_id, units.name as unit_name')
				->from('leases_profiles')
				->JOIN('profiles', 'leases_profiles.profile_id = `profiles`.id', 'LEFT')
				->JOIN('leases', 'leases_profiles.lease_id = leases.id', 'LEFT')
				->JOIN('units', 'leases.unit_id = units.id', 'LEFT')
				->JOIN('properties', 'units.property_id = properties.id', 'LEFT')
				->get()->result();

				break;
			default:
				$response->statuses = $this->generateSelectData($this->maintenance2_model->statuses);
				$response->priorities = $this->generateSelectData($this->maintenance2_model->priorities);
				$response->categories = $this->generateSelectData($this->maintenance2_model->types);
				break;
		}
		return $response;
	}

	public function generateSelectData($data) {
		$result = Array();
		foreach($data as $i => $d) {
			if($d) {
				$result[] = (Object)Array("value" => $i, "label" => $d);
			}
		}
		return $result;
	}

	public function addMessage($tid, $uid, $message) {
		$userInfo = $this->getUserInfo($uid);
		$name = $userInfo->user->first_name .' '. $userInfo->user->last_name;
		$this->db->insert('maintenance_messages', Array('text'=>$message, 'name'=>$name, 'profile_id'=>$userInfo->user->profile_id, 'ticket_id' => $tid));

		$response = new stdClass();
		$response->status = 1;
		$response->message = "Message added.";
		return $response;
	}

	public function getClockData($pid) {
		$q = $this->db->get_where('timesheet', Array('profile_id' => $pid, 'date' => date("Y-m-d"), 'end_time' => null, 'project' => 'Maintenance'));
		//echo $this->db->last_query();
		if($q->num_rows()) {
			return (Object)Array('ticket' => $q->row()->task, 'start' => $q->row()->date ." ". $q->row()->start_time);
		} else {
			return null;
		}
	}

	public function toggleClock($uid, $data) {
		$userInfo = $this->getUserInfo($uid);
		$response = new stdClass();
		$response->status = 1;
		$q = $this->db->get_where('timesheet', Array('profile_id' => $userInfo->user->profile_id, 'date' => date_format(date_create($data['date']), 'Y-m-d'), 'end_time' => null, 'project' => 'Maintenance', 'task !=' => $data['task']));
		if($q->num_rows()) {
			$response->status = 1;
			$response->result = 'Clock out of your current task before starting a new task.';
			return $response;
		}
		$q = $this->db->get_where('timesheet', Array('profile_id' => $userInfo->user->profile_id, 'date' => date_format(date_create($data['date']), 'Y-m-d'), 'end_time' => null, 'project' => 'Maintenance', 'task' => $data['task']));
		if($q->num_rows()) {
			$this->db->update('timesheet', Array('end_time' => $data['time']), Array('profile_id' => $userInfo->user->profile_id, 'date' => date_format(date_create($data['date']), 'Y-m-d'), 'project' => 'Maintenance', 'task' => $data['task']));
			//echo $this->db->last_query();
			$response->result = "end";
		} else {
			$this->db->insert('timesheet', Array('profile_id' => $userInfo->user->profile_id, 'date' => date_format(date_create($data['date']), 'Y-m-d'), 'end_time' => null, 'start_time' => $data['time'], 'project' => 'Maintenance', 'task' => $data['task']));
			$response->result = "start";
			$response->data = (Object)Array("ticket" => $data['task'], "start" => $data['date'] . " " . $data['time']);
		}
		$response->message = "Clock Updated";
		return $response;
	}

	function getEmployeeList() {
		return $this->db->select('CONCAT_WS("",first_name," ",last_name) as label, id as value')->get_where('profiles', Array('profile_type_id' => 4))->result();
	}

	public function addTicket($uid, $data) {
		$user = $this->getUserInfo($uid);
		if(strlen($data['message']) > 0)
			$message = Array("name" => $user->user->first_name ? $user->user->first_name." ".$user->user->last_name : 'Admin', "text" => $data['message'], "internal" => $data['internal'] ? 1 : 0);
		unset($data['message']);
		unset($data['internal']);
		$tids = Array();
		foreach($data['tags'] as $tag) {
			if($tag["__isNew__"]) {
				$q = $this->db->get_where('maintenance_tags', Array('text' => $tag['label']));
				if($q->num_rows() > 0) {
					$id = $q->row()->id;
				} else {
					$this->db->insert('maintenance_tags', Array('text' => $tag['label']));
					$id =  $this->db->insert_id();
				}
			} else {
				$id = $tag['value'];
			}
			$tids[] = $id;
		}
		//print_r($tids);
		$data['tags'] = implode(',', $tids);
		$data['due_date'] = date_format(date_create($data['dueDate']),"Y-m-d");
		$data['created_by'] = $user->user->profile_id;
		unset($data['dueDate']);
		//echo $data['tags'];
		$this->db->insert('maintenance', $data);
		$tid = $this->db->insert_id();
		if(isset($message)) {
			$message['ticket_id'] = $tid;
			$this->db->insert('maintenance_messages', $message);
		}
		return true;
	}

}
