<?php defined('BASEPATH') OR exit('No direct script access allowed');
include 'app_sc/helpers/logs/logs.php';

class Maintenance extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->icons = Array("property" => "fas fa-building", "unit" => "far fa-building");
		$this->load->model('maintenance2_model');
		$this->load->model('logs_model');
    }

    function index()
    {   
        $this->meta['title'] = "Maintenance";
        $this->meta['h2'] = "Maintenance";
		$this->data['data'] = $this->maintenance2_model->generateFilters();
        $this->page_construct('maintenance/index', $this->data, $this->meta);
    }

    function getTickets() {
		$data = Array();
		$columns = Array();
		$columns[] = (Object)Array("id" => "type", "field" => "type", "name" => "Type", "formatter" => "TypeFormatter", "resizable" => true, "sortable" => true, "width" => 40);
		$columns[] = (Object)Array("id" => "title", "field" => "title", "name" => "Title", "formatter" => "TitleFormatter", "resizable" => true, "sortable" => true, "width" => 200);
		$columns[] = (Object)Array("id" => "users", "field" => "assigned_name", "name" => "Users", "formatter" => "UsersFormatter", "resizable" => true, "sortable" => true, "width" => 130);
		$columns[] = (Object)Array("id" => "property", "field" => "property", "name" => "Property", "formatter" => "PropertyFormatter", "resizable" => true, "sortable" => true, "width" => 130);
		$columns[] = (Object)Array("id" => "status", "field" => "status", "name" => "Status", "formatter" => "StatusFormatter", "resizable" => true, "sortable" => true,"width" => 110);
		$columns[] = (Object)Array("id" => "dueDate", "field" => "due_date", "name" => "Date", "formatter" => "DateFormatter", "resizable" => true, "sortable" => true, "width" => 110);
		$columns[] = (Object)Array("id" => "link", "field" => "id", "name" => "Link", "formatter" => "LinkFormatter", "resizable" => true, "sortable" => true, "width" => 10);

		$this->db->join('profiles p1', 'p1.id = m.assigned_to', 'LEFT')->join('profiles p2', 'p2.id = m.created_by', 'LEFT')->join('properties p', 'p.id = m.property', 'LEFT')->join('units u', 'u.id = m.unit', 'LEFT')->select('tags, assigned_to, CONCAT_WS(" ",p1.first_name,p1.last_name) as assigned_name, CONCAT_WS(" ",p2.first_name,p2.last_name) as created_name, priority, created_by, property as property_id, category as type, m.id, m.title, p.name as property, u.name as unit, due_date, m.status,
		 if(CURDATE() = due_date,1
		 ,if(CURDATE() = date_add(due_date, INTERVAL 1 DAY),2
		 ,if(CURDATE() = date_add(due_date, INTERVAL -1 DAY),3,
		 ,if((YEARWEEK(CURDATE(), 1) = YEARWEEK(due_date, 1)) and (CURDATE() > due_date),4,
		 ,if((YEARWEEK(CURDATE(), 1) = YEARWEEK(due_date, 1)) and (CURDATE() < due_date),5,
		 ,if((month(CURDATE()) = month(due_date)) and (year(CURDATE()) = year(due_date)) and (CURDATE() < due_date),7,
		 ,if((month(CURDATE()) = month(due_date)) and (year(CURDATE()) = year(due_date)) and (CURDATE() > due_date),6,
		 ,if((CURDATE() > due_date),8
		 ,if(CURDATE() < due_date,9,
		 ,10))))))))) as due_date_calc');
		if($_GET['property_id']) $this->db->where('property', $_GET['property_id']);
		if($_GET['unit_id']) $this->db->where('unit', $_GET['unit_id']);
		if($_GET['tenant_id']) $this->db->where('tenant', $_GET['tenant_id']);
		$this->db->order_by('due_date', 'desc');
		$q = $this->db->get('maintenance m');
		if ($q->num_rows() > 0) {
			$data = $q->result();

		} else {
			$data[0] = (object)Array("type" => " ", "dueDate" => null, "assigned_name" => null,
				"name" => "No Tickets found", "property" => " ", "link" => " ",
				"status" => null
			);
		}
		echo json_encode(Array("data"=> $data, "columns"=>$columns));
	}

	function getModal()
	{
		$this->data['categories'] = $this->maintenance2_model->categoriesList();
		$this->data['statuses'] = $this->maintenance2_model->statusList();
		$this->data['employees'] = $this->maintenance2_model->getEmployeeList();
		switch ($this->input->post('mode')) {
			case 'add' :
				$this->data['target'] = 'maintenance/addTicket';
				$this->data['property_id'] = $_GET['property_id'];
				$this->data['unit_id'] = $_GET['unit_id'];
				$this->data['tenant_id'] = $_GET['tenant_id'];
				break;
			case 'edit' :
				$this->data['target'] = 'maintenance/editTicket/'.$this->input->post('id');
				$this->data['ticket'] = $this->maintenance2_model->getTicket($this->input->post('id'));
				$this->data['attachments'] = $this->maintenance2_model->getAttachments($this->input->post('id'));
				break;
		}
		$this->load->view('maintenance/maintenance-modal', $this->data);
	}

	function addTag() {
    	$tag = $this->input->post('text');
    	$q = $this->db->get_where('maintenance_tags', Array('text' => $tag));
    	if($q->num_rows() > 0) {
    		echo $q->row()->id;
		} else {
    		$this->db->insert('maintenance_tags', Array('text' => $tag));
    		echo $this->db->insert_id();
		}
	}

	function addTicket() {
		$data = $this->input->post();
		$data['created_by'] = $this->session->userdata('profileId');

		$files = $_FILES['files'];
		$this->maintenance2_model->addTicket($data, $files);
		$ticket = $this->db->insert_id();
		echo json_encode(array('type' => 'success', 'message' => 'Ticket successfully added.', 'id' => $ticket));

		//logs
		$update_title_log = new Log_General($this->ion_auth->get_user_id(), 'Maintenance Ticket', $ticket, "Created", $data);
		$this->logs_model->add_log($update_title_log);
		
		//email
		$data['action'] = 'A new ticket was added and assigned to you.';
		$this->notificationEmail($data);
	}
	
	function updateTicketEmail($tid, $action = null) {
		$statuses = (array)$this->maintenance2_model->statusList();
		$postdata = $this->input->post();
		$sendOptions = json_decode($postdata['result']);
		if(isset($postdata['action'])) {$action = $postdata['action'];}
		$data = $this->db->get_where('maintenance', array('id' => $tid), 1)->row_array();
		$data['statustext']=$statuses[$data['status']-1]->name;
		$data['action'] = $action;
		$data['created_by'] = $this->session->userdata('first_name').' '.$this->session->userdata('last_name');
		foreach ($sendOptions as $sendOption){
			switch ($sendOption) {
				//sending to staff
				case 1:
								//if($this->session->userdata('profileId') !== $data['assigned_to']){
							$profile = $this->db->get_where('profiles', array('id' => $data['assigned_to']), 1)->row();
							$this->setUpdateTicketEmailData($data, $profile);
						//};  

						//if($this->session->userdata('profileId') !== $data['created_by']){
							$profile = $this->db->get_where('profiles', array('id' => $this->session->userdata('profileId')), 1)->row();
							$profile->email = $profile->email != null ? $profile->email : $this->session->userdata('email');
							$this->setUpdateTicketEmailData($data, $profile);
						//}; 
					break;

				//sending to tenants
				case 2:
						$profile = $this->db->get_where('profiles', array('id' => $data['tenant']), 1)->row();
						
						$this->load->library('email');
						$email_user = $this->db->get_where('users', array('id' => $this->site->settings->tenant_notification_user))->row();
						$config['smtp_user'] = $email_user->email;
						$config['smtp_pass'] = $email_user->email_password;
						$this->email->initialize($config);

				
				        $data['tenant'] = $profile;
						$subject = 'There is an update to the maintenance request you submitted on '.$data['create_date'];
						$data['message'] = 'There is an update to the maintenance request you submitted on '.$data['create_date'].':<br><br>'.$action;
						$body = $this->load->view('email_templates/tenant-notification.php', $data, TRUE);
						
						

						$result = $this->email
						->from($this->session->userdata('email'))   
						//->from($companySettings->company_email)             
						->reply_to($companySettings->company_email)    // Optional, an account where a human being reads.
						//->to('debbie@simpli-city.com')
						->to($profile->email) 
						->subject($subject)
						->message($body)
						->send();
							
						//$this->TenantUpdateTicketEmail($data, $profile);
					break;

				//sending to owners
				case 3:
					echo "i equals 2";
					break;
			}
		}
		

       
	} 

	function TenantUpdateTicketEmail($data, $toProfile){
		$data['Tenant_name'] = $toprofile->first_name.' '.$toprofile->last_name;
		$data['Tenant_email'] = $toProfile->email;
		$body = $this->load->view('email_templates/task-notification.php', $data, TRUE);
		//$this->sendEmail($body, $data);
	}

	function setUpdateTicketEmailData($data, $toProfile){
		$profile = $this->db->get_where('profiles', array('id' => $data['assigned_to']), 1)->row();
		$data['assigned_to_name'] = $profile->first_name.' '.$profile->last_name;
		$data['initials'] = $this->session->userdata('first_name')[0].$this->session->userdata('last_name')[0];
		$data['assigned_to_email'] = $toProfile->email;
		$data['property_name'] = $this->db->get_where('properties', array('id' => $data['property']), 1)->row()->name;
		$data['category_name'] = $this->maintenance2_model->types['category'];
		$body = $this->load->view('email_templates/task-notification.php', $data, TRUE);
		$this->sendEmail($body, $data);
	}

	function notificationEmail($data) {
		$profile = $this->db->get_where('profiles', array('id' => $data['assigned_to']), 1)->row();
		$data['assigned_to_name'] = $profile->first_name.' '.$profile->last_name;
		$data['initials'] = $this->session->userdata('first_name')[0].$this->session->userdata('last_name')[0];
		$data['assigned_to_email'] = $profile->email != null ? $profile->email : $this->db->get_where('users', array('profile_id' => $data['assigned_to']), 1)->row()->email;
		$data['property_name'] = $this->db->get_where('properties', array('id' => $data['property']), 1)->row()->name;
		$data['category_name'] = $this->maintenance2_model->types['category'];
		$body = $this->load->view('email_templates/task-notification.php', $data, TRUE);
		$this->sendEmail($body, $data);
       
	}

	function addMessage($tid) {
		$data = $this->input->post();
		$this->maintenance2_model->addMessage($data, $tid);
		$update_title_log = new Log_General($this->ion_auth->get_user_id(), 'Maintenance Ticket', $tid, "message", $data);
		$this->logs_model->add_log($update_title_log);
	}

	
	function instantUpdate($tid) {
		$data = $this->input->post();
		$this->maintenance2_model->instantUpdate($data, $tid);
		$update_title_log = new Log_General($this->ion_auth->get_user_id(), 'Maintenance Ticket', $tid, $data['changedField'], $data['newValue']);
		$this->logs_model->add_log($update_title_log);
		echo "successfully changed ".$data['changedField'].'!';
	}


	function getMessages($tid) {
		$this->data['messages'] = $this->maintenance2_model->getMessages($tid);
		$this->data['ticket'] = $this->maintenance2_model->getTicket($tid);
		$this->load->view('maintenance/messages', $this->data);
	}

	function editTicket($id) {
		$data = $this->input->post();
		$files = $_FILES['files'];
		$this->maintenance2_model->editTicket($data, $id, $files);
		echo json_encode(array('type' => 'success', 'message' => 'Ticket successfully updated.', 'id' => $id));

		$update_title_log = new Log_General($this->ion_auth->get_user_id(), 'Maintenance Ticket', $id, "edit", $data);
		$this->logs_model->add_log($update_title_log);
	}

	function deleteTicket($id) {
		$this->db->delete('maintenance', array('id' => $id));
		echo json_encode(array('type' => 'success', 'message' => 'Ticket successfully deleted.', 'id' => $id));

		$update_title_log = new Log_General($this->ion_auth->get_user_id(), 'Maintenance Ticket', $id, "delete", null);
		$this->logs_model->add_log($update_title_log);
	}

	function sendEmail($body, $data)
    {
        $this->load->library('email');
        $config['smtp_user'] = $this->session->userdata('email');
        $config['smtp_pass']    = $this->session->userdata('email_pass');
        $this->email->initialize($config);

        //$query = $this->db->get_where('profiles', array('id' => $row->profile_id));
        //$tenant=$query->row();
        //$lquery = $this->db->get_where('leases', array('id' => $row->lease_id));
        //$lease=$lquery->row();
        $company_name = $this->site->settings->company_name;
        $company_phone = $this->site->settings->company_phone;
        $company_email = $this->site->settings->company_email;
        $company_logo = $this->site->settings->company_logo;



            $subject = 'task notification' ;


            
            
    
            // Also, for getting full html you may use the following internal method:
            //$body = $this->email->full_html($subject, $message);

            $result = $this->email
                ->from($this->session->userdata('email'))   
                //->from($companySettings->company_email)             
                ->reply_to($companySettings->company_email)    // Optional, an account where a human being reads.
                ->to($data['assigned_to_email'])
                //->to('debbie@simpli-city.com') 
                ->subject($subject)
                ->message($body)
                ->send();

            //var_dump($result);
            //echo '<br />';
            //echo $this->email->print_debugger();
            //echo $this->session->userdata('email');

            //exit; 
    }


}
