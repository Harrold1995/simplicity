<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tenantportal_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('encryption_model');
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
		$uname = $data['user']['username'];
		//echo password_hash($data['password'], PASSWORD_DEFAULT);
		$q = $this->db->get_where('tenant_data', Array('username' => $uname));
		$response = new stdClass();
		if($q->num_rows() == 0) {
			$response->status = 0;
			$response->message = 'User not found.';
		} else {
			if(password_verify($data['user']['password'], $q->row()->password)) {
				$result = $this->db->get_where('profiles', Array('id' => $q->row()->profile_id))->row();
				$response->status = 1;
				$response->message = 'Login successful.';
				$lid = $this->getTenantLease($q->row()->profile_id)->id;
				$token = $this->generateToken($q->row()->profile_id, $lid);
				$response->token = $token;
				$response->user = $result;
				$q = $this->db->query("SELECT  leases.id, leases.end, leases.amount, p.name as property, units.name as unit, leases.unit_id FROM `leases`Inner Join leases_profiles on leases_profiles.lease_id = leases.id  LEFT JOIN units on units.id = leases.unit_id LEFT JOIN properties p on p.id = units.property_id where leases_profiles.profile_id = {$q->row()->profile_id}  ORDER BY end DESC");
				$result = $q->result();
				$response->user->leases = Array();
				foreach($result as $l) {
					$response->user->leases[] = (Object)Array("id" => $l->id, "name" => $l->property." Unit ".$l->unit."(".$l->end.")");
				}
				$response->user->activelease =  $this->getLease($lid);
				$response->user->ikey =  $this->getIkey($lid);
				$response->user->settings = $this->getCompanySettings();
				$_SESSION['tenantdata'] = $result;
			} else {
				$response->status = 0;
				$response->message = 'Your password doesn\'t match our records, please try again';
			}
		}
		return $response;


	}

	public function getIkey($lid) {
		$pid = $this->db->select('u.property_id as pid')->join('units u', 'u.id = l.unit_id', 'left')->get_where('leases l', Array('l.id' => $lid))->row()->pid;
		$ids = $this->db->order_by('property_id DESC')->where_in('property_id', Array(0, $pid))->get('property_keys')->result();
		if($ids[0]->ikey)
			$key = $ids[0]->ikey;
		else if($ids[1] && $ids[1]->property_id == 0)
			$key = $ids[1]->ikey;
		return (Object)Array("xKey" => $key, "xSoftwareName" => "cardknox-ifields", "xSoftwareVersion" => "1.0.0");
	}

	public function changeLease($uid, $lid) {
		$q = $this->db->get_where('leases_profiles', Array('lease_id' => $lid, 'profile_id' => $uid));
		$response = new stdClass();
		if($q->num_rows() == 0) {
			$response->status = 0;
			$response->message = 'User not found.';
		} else {
			$response->status = 1;
			$response->message = 'Login successful.';
			$token = $this->generateToken($uid, $lid);
			$response->token = $token;
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

	public function getLeaseIdFromToken($uid = null) {

		$headers = apache_request_headers();
		if (isset($headers['Authorization'])) {
			$matches = array();
			preg_match('/Token (.*)/', $headers['Authorization'], $matches);
			if (isset($matches[1])) {
				$token = $matches[1];
			}
		}

		$data = null;
		if($token) {
			try {
				$data = $this->jwt->decode($token, $this->CONSUMER_SECRET);
			} catch(exception $e) {
				print_r($e);
			}
		}


		return $data && $data->leaseid ? $data->leaseid : $this->getTenantLease($uid ? $uid : $data->userid)->id;
	}

	public function getUserIdFromEmail($email = null) {
		$q = $this->db->get_where('tenant_data', Array('username' => $email));
		$response = new stdClass();
		if($q->num_rows() == 0) {
			$response->status = 0;
			$response->message = 'User with this email is not found.';
		} else {
			$response->status = 1;
			$response->message = 'Thank you, we sent you an email with instructions on how to restore your password.';
			$this->sendForgetEmail($q->row()->profile_id);
		}
		return $response;
	}

	public function sendForgetEmail($uid) {
		$user = $this->db->get_where('profiles', Array('id' => $uid))->row();
		$token = $this->tenantportal_model->generateToken($uid);
		$this->load->library('email');
		$this->email->from('debbie@simpli-city.com', 'Joshua');
		//$this->email->to('dyba.vit@gmail.com');
		$this->email->to($user->email);
		$this->email->set_newline("\r\n");
		$this->email->subject('Simli-City Password Recovery');
		$body = '<!DOCTYPE html >
                          <html >
                          <head>
                              <meta http-equiv="Content-Type" content="text/html; " />
                              <title>Simli-City Password Recovery</title>
                              <style type="text/css">
                                  body {
                                      font-family: Arial, Verdana, Helvetica, sans-serif;                                      
                                  }
                              </style>
                          </head>
                          <body>
                          To reset your password please follow the link below: <br/>
                          <a href="'.FRONTEND_LINK.'forgot/'.$token.'">Reset password</a> 
                          </body>
                          </html>';
		$this->email->message($body);

		if(!$this->email->send()) {
			echo $this->email->print_debugger();
		}
	}

	public function sendRegisterEmail($uid) {
		$user = $this->db->get_where('profiles', Array('id' => $uid))->row();
		$response = new stdClass();
		$tenantnum = $this->db->get_where('tenant_data', Array('profile_id' => $uid))->num_rows();
		if($tenantnum > 0) {
			$response->status = 'danger';
			$response->message = 'This user is already registered.';
			return $response;
		} else if(strlen($user->email) < 4 ) {
			$response->status = 'danger';
			$response->message = 'This user has no email on file.';
			return $response;
		} else{
			$response->status = 'success';
			$response->message = 'Invite link was sent to '.$user->email.'.';
		}

		$token = $this->tenantportal_model->generateToken($uid);
		$company_name = $this->site->settings->company_name ? $this->site->settings->company_name: 'Your Property Manager';
		$company_phone = $this->site->settings->company_phone;
        $company_email = $this->site->settings->company_email;
        $company_logo = $this->site->settings->company_logo ? $this->site->settings->company_logo : 'logo.png';
		$this->load->library('email');
		$email_user = $this->db->get_where('users', array('id' => $this->site->settings->tenant_notification_user))->row();
		$config['smtp_user'] = $email_user->email;
		$config['smtp_pass'] = $email_user->email_password;
		$this->email->initialize($config);

		$this->email->from($email_user->email, $company_name);
		//$this->email->to('sales@simpli-city.com');
		$this->email->to($user->email);
		$this->email->set_newline("\r\n");
		$this->email->subject($company_name.' - Make Payments Online');
		$message ='Hello '.$user->first_name.', <br><br>
		'.$company_name.' has invited you to activate your Online Portal, where you can: <br><br>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tbody><tr>
				<td valign="middle" align="left" width="40" height="50"><img width="30" height="30" alt="Online Payments" src="https://ci5.googleusercontent.com/proxy/u2xkMzIitZodzcRdF8FakbSdYWrr0NZa6ao2qTfWS17U2wfGwh_iaCJWQQRZWdtULz3JIrcFMI0vAX26lG-UnBhNCVMCd0cW5Fv6A5JPMdgGzevX2Qjdhg=s0-d-e1-ft#https://pa.cdn.appfolio.com/appfolio/images/tportal_payments_icon.png" style="font-size:6px;line-height:1em;text-align:center" class="CToWUd"></td>
				<td valign="middle" align="left">Make payments online.</td>
			</tr>
			<tr>
				<td valign="middle" align="left" width="40" height="50"><img width="30" height="30" alt="Auto Payments" src="https://ci3.googleusercontent.com/proxy/BS9P5q4ulfFNTyHIAGqW1vfP2ZIxstEqtJR934rN2R5r2Qjx6nLnS67Uy63WQtsf3evIizYDgbspxzsr8YLKI-ouGXGJYxtNw_klZyywq7ilx8SIRchq6w=s0-d-e1-ft#https://pa.cdn.appfolio.com/appfolio/images/tportal_calendar_icon.png" style="font-size:6px;line-height:1em;text-align:center" class="CToWUd"></td>
				<td valign="middle" align="left">Set up automatic payments.</td>
			</tr>

					<tr>
				<td valign="middle" align="left" width="40" height="50"><img width="30" height="30" alt="Maintenance Requests" src="https://ci6.googleusercontent.com/proxy/IIkHhNMQmllpNw3t4yVt7n0GvA8lD6YcWklwzmmGgUIjaTdoWD6D8u1oO-0LcucbbuRQtrAUaZTG-kG9YGLF7nV20CIihi-MMuPNjMfLvNxjA2khIsuqGOFzoQ=s0-d-e1-ft#https://pa.cdn.appfolio.com/appfolio/images/tportal_maintenance_icon.png" style="font-size:6px;line-height:1em;text-align:center" class="CToWUd"></td>
				<td valign="middle" align="left">Submit maintenance requests from any device.</td>
			</tr>



			<tr>
			<td height="20"><br></td>
			</tr>
			</tbody></table>
					<a href="'.FRONTEND_LINK.'register/'.$token.'" style ="text-decoration: none;"><span style=" padding: 20px; background-color: #32a98e;border-radius: 27px;color: white; font-weight: bold; ">Activate Your Account</span></a> <br>';

		$body = '
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=' . strtolower(config_item('charset')) . '" />
            <title>' . html_escape($company_name.' - Make Payments Online') . '</title>
            <style type="text/css">
            
            html { -webkit-text-size-adjust: none; -ms-text-size-adjust: none;}

                @media only screen and (min-device-width: 750px) {
                    .table750 {width: 750px !important;}
                }
                @media only screen and (max-device-width: 750px), only screen and (max-width: 750px){
                  table[class="table750"] {width: 100% !important;}
                  .mob_b {width: 93% !important; max-width: 93% !important; min-width: 93% !important;}
                  .mob_b1 {width: 100% !important; max-width: 100% !important; min-width: 100% !important;}
                  .mob_left {text-align: left !important;}
                  .mob_center {text-align: center !important;}
                  .mob_soc {width: 50% !important; max-width: 50% !important; min-width: 50% !important;}
                  .mob_menu {width: 50% !important; max-width: 50% !important; min-width: 50% !important; box-shadow: inset -1px -1px 0 0 rgba(255, 255, 255, 0.2); }
                  .mob_btn {width: 100% !important; max-width: 100% !important; min-width: 100% !important;}
                  .mob_pad {width: 15px !important; max-width: 15px !important; min-width: 15px !important;}
                  .top_pad {height: 15px !important; max-height: 15px !important; min-height: 15px !important;}
                  .top_pad2 {height: 50px !important; max-height: 50px !important; min-height: 50px !important;}
                  .mob_title1 {font-size: 16px !important; line-height: 40px !important;}
                  .mob_title2 {font-size: 26px !important; line-height: 33px !important;}
                  .mob_txt {font-size: 20px !important; line-height: 25px !important;}
                }
               @media only screen and (max-device-width: 550px), only screen and (max-width: 550px){
                  .mod_div {display: block !important;}
               }
                .table750 {width: 750px;}
            </style>
            </head>
            <body style="margin: 0; padding: 0;">

            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background: #f5f8fa; min-width: 340px; font-size: 1px; line-height: normal;">
                <tr>
                <td align="center" valign="top">            
                    <!--[if (gte mso 9)|(IE)]>
                     <table border="0" cellspacing="0" cellpadding="0">
                     <tr><td align="left" valign="top" width="750"><![endif]-->
                    <table cellpadding="0" cellspacing="0" border="0" width="750" class="table750" style="width: 100%; max-width: 750px; min-width: 340px; background: #f5f8fa;">
                        <tr>
                           <td class="mob_pad" width="25" style="width: 25px; max-width: 25px; min-width: 25px;">&nbsp;</td>
                            <td align="center" valign="top" style="background: #ffffff;">

                              <table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100% !important; min-width: 100%; max-width: 100%; background: #f5f8fa;">
                                 <tr>
                                    <td align="right" valign="top">
                                       <div class="top_pad" style="height: 25px; line-height: 25px; font-size: 23px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>

                              <table cellpadding="0" cellspacing="0" border="0" width="88%" style="width: 88% !important; min-width: 88%; max-width: 88%;">
                                 <tr>
                                    <td class="mob_left" align="center" valign="top">
                                       <div style="height: 40px; line-height: 40px; font-size: 38px;">&nbsp;</div>
                                       <a href="#" target="_blank" style="display: block; max-width: 128px;">
                                       
                                          <img src="'.base_url() . "uploads/images/".$company_logo.'" alt="img" width="128" border="0" style="display: block; width: 128px;" />
                                       </a>
                                       <div class="top_pad2" style="height: 78px; line-height: 78px; font-size: 76px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>

                              <table cellpadding="0" cellspacing="0" border="0" width="88%" style="width: 88% !important; min-width: 88%; max-width: 88%;">
                                 <tr>
                                    <td class="mob_left" align="left" valign="top">
                                       <font class="mob_title1" face="\'Source Sans Pro\', sans-serif" color="#1a1a1a" style="font-size: 16px; line-height: 35px; font-weight: 300; ">
                                          
                                          <br>

                                               '.$message.'
                                       </font>
                                       <div style="height: 25px; line-height: 25px; font-size: 23px;">&nbsp;</div>
                                       <font class="mob_title2" face="\'Source Sans Pro\', sans-serif" color="#5e5e5e" style="font-size: 16px; line-height: 45px; font-weight: 300; ">
                                          <span class="mob_title2" style="font-family: \'Source Sans Pro\', Arial, Tahoma, Geneva, sans-serif; color: #5e5e5e; font-size: 16px; line-height: 35px; font-weight: 300;">
										  Let us know if you have any questions. <br><br>

                                            '.$company_name.' <br>

                                            '.$company_phone.'.</span>
                                       </font>
                                       <div style="height: 38px; line-height: 38px; font-size: 16px;">&nbsp;</div>
                                       <table class="mob_btn" cellpadding="0" cellspacing="0" border="0" width="250" style="width: 250px !important; max-width: 250px; min-width: 250px; background: #27cbcc; border-radius: 4px;">
                                          <tr>
                                          </tr>
                                       </table>
                                       <div class="top_pad2" style="height: 78px; line-height: 78px; font-size: 76px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>

                              <table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100% !important;min-width: 100%;max-width: 100%;background-color: #302b4d;color: white;">
                              <tbody><tr>
                                 <td class="mob_left" align="left" valign="top" style="text-align: right; padding: 15px;">
                                    
                                    
                                 <a href="https://simpli-city.com/"style="text-decoration: none;">
                                    <font class="mob_title2" face="\'Source Sans Pro\', sans-serif" color="#5e5e5e" style="font-size: 16px;line-height: 45px;font-weight: 300;color: white;">
                                       <span class="mob_title2" style="font-family: \'Source Sans Pro\', Arial, Tahoma, Geneva, sans-serif; color: #5e5e5e; font-size: 16px; line-height: 35px; font-weight: 300;;;;;color: #d61156;;;;;">
                                         Sent with   </span>
                                    </font>
                                   <img style="width: 70px;" src="'.base_url().'uploads/images/logo.png"></a>
                                    
                                    
                                    
                                 </td>
                              </tr>
                           </tbody></table>

                              

                           </td>
                           <td class="mob_pad" width="25" style="width: 25px; max-width: 25px; min-width: 25px;">&nbsp;</td>
                        </tr>
                     </table>
                     <!--[if (gte mso 9)|(IE)]>
                     </td></tr>
                     </table><![endif]-->
                  </td>
               </tr>
               
            </table>
            </body>
            </html>';

		$this->email->message($body);

		if(!$this->email->send()) {
			echo $this->email->print_debugger();
		}
		return $response;
	}

	public function getUserInfo($uid, $checkunique = false) {
		$q = $this->db->get_where('profiles', Array('id' => $uid));
		$response = new stdClass();
		if($q->num_rows() == 0) {
			$response->status = 0;
			$response->message = 'User not found.';
		} else {
			$response->status = 1;
			$response->user = $q->row();
			$q = $this->db->query("SELECT  leases.id, leases.end, leases.amount, p.name as property, units.name as unit, leases.unit_id FROM `leases`Inner Join leases_profiles on leases_profiles.lease_id = leases.id  LEFT JOIN units on units.id = leases.unit_id LEFT JOIN properties p on p.id = units.property_id where leases_profiles.profile_id = {$uid}  ORDER BY end DESC");
			$result = $q->result();
			$response->user->leases = Array();
			foreach($result as $l) {
				$response->user->leases[] = (Object)Array("id" => $l->id, "name" => $l->property." Unit ".$l->unit."(".$l->end.")");
			}
			$response->user->activelease =  $this->getLease($this->tenantportal_model->getLeaseIdFromToken($uid));
			$response->user->ikey =  $this->getIkey($this->tenantportal_model->getLeaseIdFromToken($uid));
			$response->user->settings = $this->getCompanySettings();
		}
		if($checkunique) {
			$q = $this->db->get_where('tenant_data', Array('profile_id' => $uid));
			if($q->num_rows() > 0) {
				$response->status = 0;
				$response->message = 'You are already registered.';
			}
		}
		return $response;
	}

	public function getCompanySettings() {
		return $this->db->select('accept_ach, accept_cc, default_ach_fee, default_cc_fee')->get('company_settings')->row();
	}

	public function getTenantLease($uid) {
		$q = $this->db->query("SELECT  leases.id, leases.end, leases.amount, property_id, leases.unit_id FROM `leases`Inner Join leases_profiles on leases_profiles.lease_id = leases.id  LEFT JOIN units on units.id = leases.unit_id where leases_profiles.profile_id = {$uid} AND start < NOW() ORDER BY end DESC");
		if($q->num_rows() > 0) {
			return $q->row();
		} else {
			return false;
		}
	}

	public function getLease($lid) {
		$q = $this->db->query("SELECT  leases.id, leases.restrict_payments, leases.active, leases.end, leases.amount, property_id, leases.unit_id FROM `leases`Inner Join leases_profiles on leases_profiles.lease_id = leases.id  LEFT JOIN units on units.id = leases.unit_id where leases.id = {$lid}");
		if($q->num_rows() > 0) {
			return $q->row();
		} else {
			return false;
		}
	}

	public function uploadFiles($images, $videos) {
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
			if(move_uploaded_file($fileTmpPath, $dest_path))
			{
				$response->images[] = $dest_path;
			}
		}
		$length = count($videos['name']);
		for($i = 0; $i < $length; $i++ ) {
			$fileName = $videos['name'][$i];
			$fileTmpPath = $videos['tmp_name'][$i];
			$fileNameCmps = explode(".", $fileName);
			$fileExtension = strtolower(end($fileNameCmps));
			$newFileName = md5(time() . $fileName) . '.' . $fileExtension;
			$dest_path = $uploadFileDir . $newFileName;
			if(move_uploaded_file($fileTmpPath, $dest_path))
			{
				$response->images[] = $dest_path;
			}
		}
		return $response;
	}

	public function addTicket($uid, $data, $files) {
		$lease = $this->getLease($this->getLeaseIdFromToken());
		$this->db->insert('maintenance', Array('category'=>$data['type'], 'title'=>$data['title'], 'description'=>$data['description'], 'property' => $lease->property_id, 'unit' => $lease->unit_id, 'tenant' => $uid, 'lease_id' => $lease->id));
		$ticket_id = $this->db->insert_id();
		foreach($files->images as $file) {
			$this->db->insert('maintenance_files', Array('url'=>$file, 'type'=>0, 'ticket_id'=>$ticket_id));
		}
		foreach($files->videos as $file) {
			$this->db->insert('maintenance_files', Array('url'=>$file, 'type'=>1, 'ticket_id'=>$ticket_id));
		}
		$response = new stdClass();
		$response->status = 1;
		$response->id = $ticket_id;
		$response->message = "Ticket added.";
		return $response;
	}

	public function addMessage($tid, $uid, $message) {
		$userInfo = $this->getUserInfo($uid);
		$name = $userInfo->user->first_name .' '. $userInfo->user->last_name;
		$this->db->insert('maintenance_messages', Array('text'=>$message, 'name'=>$name, 'profile_id'=>$uid, 'ticket_id' => $tid));

		$response = new stdClass();
		$response->status = 1;
		$response->message = "Message added.";
		return $response;
	}

	public function getTickets($uid) {
		$lid = $this->getLeaseIdFromToken();
		$statuses = ['received', 'waitingForUser', 'completed'];
		$q = $this->db->select('title, description, create_date as date, status, id as number')->get_where('maintenance', Array('tenant' => $uid, 'lease_id' => $lid));
		$response = new stdClass();
		if($q->num_rows() >= 0) {
			$response->status = 1;
			$response->count = 0;
			$result = $q->result();
			foreach($result as &$ticket) {
				$ticket->status = $statuses[$ticket->status];
				if($ticket->status != 2)
					$response->count++;
			}
			$response->tickets = $result;
		} else {
			$response->status = 1;
			$response->count = 0;
			$response->tickets = Array();
		}
		return $response;
	}

	public function getTicket($tid, $uid) {
		$statuses = ['received', 'waitingForUser', 'completed'];
		$types = ['', 'Plumbing', 'Electric', 'Appliances', 'Heat/Air', 'Locks & Key', 'Pest', 'Other'];
		$q = $this->db->select('title, description, create_date as date, status, id as number, category as type')->get_where('maintenance', Array('tenant' => $uid, 'id' => $tid));
		$response = new stdClass();
		if($q->num_rows() >= 0) {
			$response->status = 1;
			$response->count = 0;
			$result = $q->row();
			$result->status = $statuses[$result->status];
			$result->type = $types[$result->type];
			$response->ticket = $result;
			$q = $this->db->select('text, name, date, profile_id as user_id, internal')->order_by('id DESC')->get_where('maintenance_messages', Array('ticket_id' => $tid));
			$response->messages = $q->result();
		} else {
			$response->status = 0;
			$response->message = 'Error retrieving ticket.';
		}
		return $response;
	}

	public function getMessages($tid, $uid, $lmid) {
		$q = $this->db->select('m.id')->JOIN('maintenance t', 't.id = m.ticket_id', 'left')->get_where('maintenance_messages m', Array('ticket_id' => $tid, 'internal' =>'0', 'tenant' => $uid, 'm.id >' => (int)$lmid));
		//print_r($this->db->last_query());

		$response = new stdClass();
		$response->status = 1;
		if($q->num_rows() >= 0) {
			$result = $q->row();
			$response->messages = $q->result();
			$q = $this->db->select('text, name, date, profile_id as user_id, id, internal')->order_by('id DESC')->get_where('maintenance_messages', Array('ticket_id' => $tid, 'internal' =>'0'));
			$response->messages = $q->result();
			$response->lastMessageId = $response->messages[0]->id;
		}
		return $response;
	}

	public function register($uid, $data) {
		$userInfo = $this->getUserInfo($uid);
		$response = new stdClass();
		if($data['password1'] != $data['password2']) {
			$response->status = 0;
			$response->message = 'Your passwords don\'t match.';
		} else if(strlen($data['password1']) < 8) {
			$response->status = 0;
			$response->message = 'Your password has to be at least 8 characters long.';
		} else {
			$this->db->insert('tenant_data', Array('profile_id' => $uid, 'username' => $userInfo->user->email, 'password' => password_hash($data['password1'], PASSWORD_DEFAULT)));
			$response->status = 1;
			$response->message = 'Your account was successfully created, you can log in now using your email and password.';
			$this->db->update('profiles', Array('invite_status' => 2), Array('id' => $uid));
		}
		return $response;
	}

	public function resetpassword($uid, $data) {
		$userInfo = $this->getUserInfo($uid);
		$response = new stdClass();
		if($data['password1'] != $data['password2']) {
			$response->status = 0;
			$response->message = 'Your passwords don\'t match.';
		} else if(strlen($data['password1']) < 8) {
			$response->status = 0;
			$response->message = 'Your password has to be at least 8 characters long.';
		} else {
			$this->db->update('tenant_data', Array('password' => password_hash($data['password1'], PASSWORD_DEFAULT)), Array('profile_id' => $uid));
			$response->status = 1;
			$response->message = 'Your password was successfully reset.';
		}
		return $response;
	}

	public function deletetenant($uid) {
		$response = new stdClass();
		$this->db->delete('tenant_data', Array('profile_id' => $uid));
		$response->status = 1;
		$response->message = 'User was removed from tenant portal.';

		return $response;
	}

	public function loadSettings($uid) {
		$q = $this->db->select('first_name, last_name, email, phone, newsletter')->get_where('profiles', Array('id' => $uid));
		$response = new stdClass();
		if($q->num_rows() >= 0) {
			$response->status = 1;

			$response->user = $q->row();
			$response->user->newsletter = (bool)$response->user->newsletter;
		} else {
			$response->status = 0;
			$response->message = 'User not found!';
		}
		return $response;
	}

	public function saveSettings($uid, $data) {
		$response = new stdClass();
		$newdata = Array('newsletter' => (int)$data['newsletter'], 'phone' => $data['phone']);
		if($this->db->update('profiles', $newdata, Array('id' => $uid))) {
			$response->status = 1;
			$response->message = 'Settings saved.';
		}
		return $response;
	}

	public function addBankAccount($uid, $data, $lid) {
		$settings = $this->getCompanySettings();
		$cc = isset($data['exp'])?1:0;
		$response = new stdClass();
		if(!$settings->accept_ach && $cc == 0 || $cc == 1 && !$settings->accept_cc) {
			$response->status = 0;
			$response->message = "We don't accept this payment method at the moment.";
			return $response;
		}
		$this->db->insert('tenant_bank_accounts', Array('name'=>$data['name'], 'nickname'=>$data['nickname'], 'profile_id'=>$uid, 'account'=>$data['account'], 'routing'=>$data['routing'], 'type_checking'=>$data['type_checking'], 'token'=>$data['token'], 'lease_id'=>$lid, 'cc'=>$cc));
		$bank_account_id = $this->db->insert_id();

		$response->status = 1;
		$response->message = "Bank account added.";
		$response->id = $bank_account_id;

		$this->load->model('tenantpayment_model');
		$customerId = $this->tenantpayment_model->addCustomer($uid, $lid);
		$account = (Object)Array('name' => $data['name'], 'cc' => $cc, 'id' => $bank_account_id, 'routing' => $data['routing'], 'token' => $data['token']);
		$pid = $this->tenantpayment_model->addPayment($account, $customerId);
		if(!$pid) {
			$this->db->delete('tenant_bank_accounts', Array('id' => $bank_account_id));
			$response->status = 0;
			$response->message = "There was an error, please make sure all fields contain correct information and fill them out again.";
		}
		return $response;
	}

	public function getBankAccount($bid) {
		$q = $this->db->select('nickname, name, routing, token')->get_where('tenant_bank_accounts', Array('id' => $bid));
		$response = new stdClass();
		if($q->num_rows() >= 0) {
			$response->status = 1;
			$response->account = $q->row();
		} else {
			$response->status = 0;
			$response->message = 'Account not found.';
		}
		return $response;
	}

	public function getBankAccounts($uid) {
		$statuses = ['received', 'waitingForUser', 'completed'];
		$lid = $this->getLeaseIdFromToken();
		$q = $this->db->select('id, nickname, account, cc')->get_where('tenant_bank_accounts', Array('profile_id' => $uid, 'lease_id' => $lid));
		$response = new stdClass();
		if($q->num_rows() >= 0) {
			$response->status = 1;
			$response->count = 0;
			$result = $q->result();
			foreach($result as &$account) $response->count++;
			$response->accounts = $result;
		} else {
			$response->status = 1;
			$response->count = 0;
			$response->accounts = Array();
		}
		return $response;
	}

	public function updateToken($token, $bid) {
		$response = new stdClass();
		if ($this->db->update('tenant_bank_accounts', Array('token' => $token), Array('id' => $bid))) {
			$response->status = 1;
			$response->message = 'Token updated';
			$response->bank_accound_id =  $bid;
		}
		return $response;
	}

	public function removeAccount($bid) {
		$response = new stdClass();
		$this->db->delete('tenant_bank_accounts', Array('id' => $bid));
		$response->status = 1;
		$response->message = 'Bank account was removed';

		return $response;
	}
}
