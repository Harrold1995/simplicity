<?php defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');
header("HTTP/1.1 200 OK");
error_reporting(E_ALL);
ini_set('display_errors', '1');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	die;
}

class Tenantapi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('tenantportal_model');
		$this->site->initSettings();

    }

    public function login() {
    	$data = json_decode(file_get_contents("php://input"),true);
    	$response = $this->tenantportal_model->checkLogin($data);
    	if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function user() {
		$uid = $this->tenantportal_model->getUserIdFromToken();
		if(!$uid) {
			header('HTTP/1.1 401 Forbidden');
			return;
		}
		$response = $this->tenantportal_model->getUserInfo($uid);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function balance() {
		$uid = $this->tenantportal_model->getUserIdFromToken();
		$lease_id = $this->tenantportal_model->getLeaseIdFromToken();
		if(!$uid) {
			header('HTTP/1.1 401 Forbidden');
			return;
		}
		$response = new stdClass();
		$response->lease_id = $lease_id;
		$q = $this->db->query("SELECT sum(debit - credit) as totalbalance
			FROM transactions
			INNER JOIN transaction_header on transaction_header.id = transactions.trans_id
			WHERE `account_id` = '{$this->site->settings->accounts_receivable}'  AND lease_id = {$lease_id} and (profile_id = {$uid} OR profile_id is null OR profile_id = 0)");
		$response->balance = '$'.number_format($q->row()->totalbalance,2,'.',',');
		$q = $this->db->query("SELECT  max(transaction_date) as date
			FROM transactions
			INNER JOIN transaction_header on transaction_header.id = transactions.trans_id
			WHERE `account_id` = {$this->site->settings->accounts_receivable} AND lease_id = {$lease_id} and (profile_id = {$uid} OR profile_id is null OR profile_id = 0) AND transaction_type = 6 AND debit > 0");
		$date = $q->row()->date;
		$response->duedate = $date;
		$now = time(); // or your date as well
		$your_date = strtotime($date);
		$datediff = $your_date - $now;
		$response->days = ceil($datediff / (60 * 60 * 24));
		$q = $this->db->query("SELECT  leases.end, leases.amount FROM `leases`Inner Join leases_profiles on leases_profiles.lease_id = leases.id where leases.id = {$lease_id}");
		$response->enddate = $q->row()->end;
		$response->rent = '$'.number_format($q->row()->amount,2,'.',',');;
		echo json_encode($response);
    }

    public function transactions() {
		$uid = $this->tenantportal_model->getUserIdFromToken();
		$lease_id = $this->tenantportal_model->getLeaseIdFromToken();
		if(!$uid) {
			header('HTTP/1.1 401 Forbidden');
			return;
		}
		$response = new stdClass();
		$q = $this->db->query("SELECT transactions.id,  debit - credit as amount, transaction_type.name as memo, transaction_header.transaction_date AS date FROM transactions INNER JOIN transaction_header on transaction_header.id = transactions.trans_id INNER Join transaction_type on transaction_type.id = transaction_header.transaction_type WHERE `account_id` = {$this->site->settings->accounts_receivable} AND lease_id = {$lease_id} and (profile_id = {$uid} OR profile_id is null OR profile_id = 0) ORDER BY id DESC");
		$result = $q->result();
		foreach($result as &$r) {
			$r->amount = ($r->amount < 0 ?'-':'').'$'.number_format(abs($r->amount),2,'.',',');
		}
		$response->transactions = $q->result();

		echo json_encode($response);
	}

	public function addTicket() {
		$uid = $this->verifyId();
    	$data = $_POST;
    	$files = $_FILES;
		$filedata = $this->tenantportal_model->uploadFiles($files['images'], $files['videos']);
		$response = $this->tenantportal_model->addTicket($uid, $data, $filedata);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function getTickets() {
		$uid = $this->verifyId();
		$response = $this->tenantportal_model->getTickets($uid);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function getTicket($tid) {
		$data = json_decode(file_get_contents("php://input"),true);
		$uid = $this->verifyId();
		if($data['onlyMessages'])
			$response = $this->tenantportal_model->getMessages($tid, $uid, $data['lmid']);
		else
			$response = $this->tenantportal_model->getTicket($tid, $uid);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function addMessage() {
		$data = json_decode(file_get_contents("php://input"),true);
		$tid = $data['tid'];
		$message = $data['message'];
		$uid = $this->verifyId();
		$response = $this->tenantportal_model->addMessage($tid, $uid, $message);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function verifyId() {
		$uid = $this->tenantportal_model->getUserIdFromToken();
		if(!$uid) {
			header('HTTP/1.1 401 Forbidden');
			die();
		}
		return $uid;
	}

	public function regUser() {
		$data = json_decode(file_get_contents("php://input"),true);
		$uid = $this->tenantportal_model->getUserIdFromToken($data['user']['token']);
		if(!$uid) {
			header('HTTP/1.1 401 Forbidden');
			return;
		}
		$response = $this->tenantportal_model->getUserInfo($uid, true);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function register() {
		$data = json_decode(file_get_contents("php://input"),true);
		$uid = $this->tenantportal_model->getUserIdFromToken($data['token']);
		if(!$uid) {
			header('HTTP/1.1 401 Forbidden');
			return;
		}
		$response = $this->tenantportal_model->register($uid, $data);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function resetpassword() {
		$data = json_decode(file_get_contents("php://input"),true);
		$uid = $this->tenantportal_model->getUserIdFromToken($data['token']);
		if(!$uid) {
			header('HTTP/1.1 401 Forbidden');
			return;
		}
		$response = $this->tenantportal_model->resetpassword($uid, $data);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');
		echo json_encode($response);
	}

	public function forgot() {
		$data = json_decode(file_get_contents("php://input"),true);
		$response = $this->tenantportal_model->getUserIdFromEmail($data['email']);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function loadSettings() {
		$uid = $this->verifyId();
		if(!$uid) {
			header('HTTP/1.1 401 Forbidden');
			return;
		}
		$response = $this->tenantportal_model->loadSettings($uid);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function saveSettings() {
		$uid = $this->verifyId();
		$data = json_decode(file_get_contents("php://input"),true);
		if(!$uid) {
			header('HTTP/1.1 401 Forbidden');
			return;
		}
		$response = $this->tenantportal_model->saveSettings($uid, $data);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');
		echo json_encode($response);
	}
	
	public function addBankAccount() {
		$uid;
		$lid;
		$data = json_decode(file_get_contents("php://input"),true);
		if($data['profile_id']){
			$uid = $data['profile_id'];
			$lid = $data['lid'];
		} else {
			$uid = $this->verifyId();
			$lid = $this->tenantportal_model->getLeaseIdFromToken();
		}
		
		$response = $this->tenantportal_model->addBankAccount($uid, $data, $lid);
		if ($response->status == 0) header('HTTP/1.1 403 Unauthorized');
		echo json_encode($response);
	}

	public function addCCAccount() {
		$uid;
		$lid;
		$data = json_decode(file_get_contents("php://input"),true);
		if($data['profile_id']){
			$uid = $data['profile_id'];
			$lid = $data['lid'];
		} else {
			$uid = $this->verifyId();
			$lid = $this->tenantportal_model->getLeaseIdFromToken();
		}
		$data['exp']=str_replace('/', '', $data['exp']);
		$data['routing']=$data['exp'];
		$data['type_checking']=1;
		$response = $this->tenantportal_model->addBankAccount($uid, $data, $lid);
		if ($response->status == 0) header('HTTP/1.1 403 Unauthorized');
		echo json_encode($response);
	}

	public function getBankAccounts() {
		$uid = $this->verifyId();
		$response = $this->tenantportal_model->getBankAccounts($uid);
		if ($response->status == 0) header('HTTP/1.1 403 Unauthorized');
		echo json_encode($response);
	}

	public function sendPayment() {
		$this->load->model('tenantpayment_model');
		$data = json_decode(file_get_contents("php://input"),true);
		$uid;
		$lease_id;
		if($data['uid']){
			$uid = $data['uid'];
			$lease_id = $data['lease_id'];
		} else {
			$uid = $this->verifyId();
			$lease_id = $this->tenantportal_model->getLeaseIdFromToken();
		}	
		
		$amount = $data['amount'] + ($data['fee'] ? $data['fee'] : 0);
		$result = $this->tenantpayment_model->doTransaction($uid, $lease_id, $amount, $data['bank_account']);
		echo json_encode($result);
		$data = array(
			'profile_id' => $uid,
			'lease_id' => $lease_id,
			'amount' => $amount
		);
		if ($result->GatewayStatus == 'Approved'){
			$this->load->model('transactionsImport_model');
			$this->transactionsImport_model->enterPortalPayments($data, $result);	
		}
	}

	public function initAch($token, $routing, $amount, $name, $isSut) {
	 	$ach_data = $isSut ?
			Array("xAccount" => "$token", "xRouting" => $routing) :
		 	Array("xToken" => "$token");

		$cardknoxOptions = Array(
			"xKey" => "simplicitydevd335b9d66a384299b7569f8301a27edc",
			"xVersion" => "4.5.9",
			"xSoftwareName" => "Tenant Portal",
			"xSoftwareVersion" => "1.0",
			"xCommand" => 'check:sale',
			"xAmount" => $amount,
			"xName" => $name
		);

		$postData = array_merge($ach_data, $cardknoxOptions);
  
		$handler = curl_init();
  
		curl_setopt($handler, CURLOPT_URL, "https://x1.cardknox.com/gatewayjson");
		curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($handler, CURLOPT_POST, true);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
  
		$response = curl_exec($handler);
		return $response;
	}

	public function updateToken() {
		$data = json_decode(file_get_contents("php://input"),true);
		$token = $data['token'];
		$bank_account_id = $data['bank_account'];
		$uid = $this->verifyId();

		$response = $this->tenantportal_model->updateToken($token, $bank_account_id);
		if ($response->status == 0) header('HTTP/1.1 403 Unauthorized');
		echo json_encode($response);
	}

	public function removeAccount() {
		$data = json_decode(file_get_contents("php://input"),true);
		$bank_account_id = $data['bank_account'];
		//$uid = $this->verifyId();

		$response = $this->tenantportal_model->removeAccount($bank_account_id);
		if ($response->status == 0) header('HTTP/1.1 403 Unauthorized');
		echo json_encode($response);
	}

	public function enableRecurring() {
		$post = json_decode(file_get_contents("php://input"),true);
		$uid;
		$lease_id;
		if($post['uid']){
			$uid = $post['uid'];
			$lease_id = $post['lease_id'];
		} else {
			$uid = $this->verifyId();
			$lease_id = $this->tenantportal_model->getLeaseIdFromToken();
		}	
		$this->load->model('tenantpayment_model');
		$data = Array("enable" => (bool)($post['enable']), "bank_account" => (int)$post['bank_account'], "type" => "month", "interval" => 1, "amount" => $post['amount'], "start" => $post['start_date'], "end" => $post['end_date']);
		if($post['schedule_id']){$data['schedule_id'] = $post['schedule_id'];}
		$response = $this->tenantpayment_model->enableRecurring($data, $uid, $lease_id);
		if ($response->status == 0) header('HTTP/1.1 403 Unauthorized');
		echo json_encode($response);
	}

	public function getRecurring() {
		$uid = $this->verifyId();
		$lid = $this->tenantportal_model->getLeaseIdFromToken();
		$this->load->model('tenantpayment_model');
		$response = $this->tenantpayment_model->getRecurring($uid, $lid);
		echo json_encode($response);
	}

	public function changeLease() {
		$data = json_decode(file_get_contents("php://input"),true);
		$lid = $data['lease_id'];
		$uid = $this->verifyId();
		$response = $this->tenantportal_model->changeLease($uid, $lid);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function transactionhook() {
		                $data = $_POST;
						$profile = $this->db->get_where('profiles', array('id' => $data['xCustom02']), 1)->row();
						
						$this->load->library('email');
						$email_user = $this->db->get_where('users', array('id' => $this->site->settings->tenant_notification_user))->row();
						$config['smtp_user'] = $email_user->email;
						$config['smtp_pass'] = $email_user->email_password;
						$this->email->initialize($config);

				
						$data['tenant'] = $profile;
						
						$data['message'] = 'Reference Number: '.$data["xRefNum"].'<br>Amount: $'.$data["xAmount"].'<br>Account Number: '.$data["xMaskedAccountNumber"].'<br>Tenant: '.$data["xName"].'<br>Status: '.$data['xResponseResult'];
						$subject = '$'.$data["xAmount"].' portal payment for '.$data["xName"].' was successfull';
						$body = $this->load->view('email_templates/payment_received.php', $data, TRUE);
						if ($data['xResponseResult'] != 'Approved'){
							$subject = '$'.$data["xAmount"].' portal payment for '.$data["xName"].' failed';
							$body = $this->load->view('email_templates/payment_failed.php', $data, TRUE);
						}
						
						
						
						

						$result = $this->email
						->from($email_user->email)   
						//->from($companySettings->company_email)             
						//->reply_to($companySettings->company_email)    // Optional, an account where a human being reads.
						//->to('debbie@simpli-city.com')
						->to('debbieklar37@gmail.com') 
						->subject($subject)
						->message($body)
						->send();

						if ($data['xResponseResult'] == 'Approved' && $data['xSourceKey'] == 'Recurring'){
							//enter transaction
							$tenantCust = $this->db->get_where('tenant_bank_accounts', array('paymentId' => $data['xPaymentMethodID']), 1)->row();
							$tenantCustData = array(
								'profile_id' => $tenantCust->profile_id,
								'lease_id' => $tenantCust->lease_id,
								'amount' => $data['xAmount'] + ($data['fee'] ? $data['fee'] : 0)
							);
							$data1->GatewayRefNum = $data['xRefNum'];
							
							//$amount = $data['xamount'] + ($data['fee'] ? $data['fee'] : 0);
							$this->load->model('transactionsImport_model');
			                $this->transactionsImport_model->enterPortalPayments($tenantCustData, $data1);
						}
		file_put_contents(__DIR__.'/../../uploads/hooklog.txt',
	    json_encode($_POST). PHP_EOL, FILE_APPEND);
		
	 }
}
