<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tenantpayment_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('tenantportal_model');
	}

	public function xkey($lid) {
		$pid = $this->db->select('u.property_id as pid')->join('units u', 'u.id = l.unit_id', 'left')->get_where('leases l', Array('l.id' => $lid))->row()->pid;
		$ids = $this->db->order_by('property_id DESC')->where_in('property_id', Array(0, $pid))->get('property_keys')->result();
		if($ids[0]-> key)
			return $ids[0]->key;
		else if($ids[1] && $ids[1]->property_id == 0)
			return $ids[1]->key;
	}

	public function addCustomer($uid, $lid = null) {
		//$lid = $this->tenantportal_model->getLeaseIdFromToken();
		$q = $this->db->get_where('tenant_customers', Array('lease_id' => $lid, 'profile_id' => $uid));
		$response = $q->row();
		if($response->customer_id) return $response->customer_id;
		$q = $this->db->get_where('profiles', Array('id' => $uid));
		$response = $q->row();

		$cardknoxOptions = Array(
			"SoftwareName" => "Tenant Portal",
			"SoftwareVersion" => "1.0",
			"BillFirstName" => $response->first_name,
			"BillLastName" => $response->last_name,
			"CustomerNumber" => $uid,
			"Email" => $response->email,
			"CustomerCustom01" => $lid
		);

		$postData = $cardknoxOptions;

		$handler = curl_init();

		curl_setopt($handler, CURLOPT_URL, "https://api.cardknox.com/v2/CreateCustomer");
		curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: '.$this->xkey($lid), 'X-Recurring-Api-Version: 2.1'));
		curl_setopt($handler, CURLOPT_POST, true);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($handler));
		//print_r($response);
		if($response->CustomerId) {
			$this->db->insert('tenant_customers', Array('customer_id' => $response->CustomerId, 'profile_id' => $uid, 'lease_id' => $lid));
		}
		return $response->CustomerId;
	}

	public function addPayment($account, $customerId) {
		$lid = $this->getLidfromCid($customerId);
		if($account->paymentId) return $account->paymentId;
		$tokenType = $account->cc == 1 ? 'cc':'check';
		$additional = $account->cc == 1 ? 'Exp':'Routing';
		$cardknoxOptions = Array(
			"SoftwareName" => "Tenant Portal",
			"SoftwareVersion" => "1.0",
			"Token" => $account->token,
			"CustomerId" => $customerId,
			"TokenType" => $tokenType,
			$additional => str_replace('/','',$account->routing),
			"Name" => $account->name
		);

		$postData = $cardknoxOptions;

		$handler = curl_init();
		//echo $this->xkey($lid);
		curl_setopt($handler, CURLOPT_URL, "https://api.cardknox.com/v2/CreatePaymentMethod");
		curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: '.$this->xkey($lid), 'X-Recurring-Api-Version: 2.1'));
		curl_setopt($handler, CURLOPT_POST, true);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($handler));
		//print_r($response);
		if($response->PaymentMethodId) {
			$this->db->update('tenant_bank_accounts', Array('paymentId' => $response->PaymentMethodId), Array('id' => $account->id));
			return $response->PaymentMethodId;
		}
		return false;
	}

	public function getPaymentMethods() {
		$cardknoxOptions = Array(
			"SoftwareName" => "Tenant Portal",
			"SoftwareVersion" => "1.0",
			"NextToken" => "",
			"PageSize" => 500
		);

		$postData = $cardknoxOptions;

		$handler = curl_init();

		curl_setopt($handler, CURLOPT_URL, "https://api.cardknox.com/v2/ListPaymentMethods");
		curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: '.$this->xkey, 'X-Recurring-Api-Version: 2.1'));
		curl_setopt($handler, CURLOPT_POST, true);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($handler));
		//print_r($response);
		return $response;
	}



	public function getLidfromCid($cid) {
		return $this->db->get_where('tenant_customers', Array('customer_id' => $cid))->row()->lease_id;
	}

	public function setDefaultPayment($account, $customerId, $paymentId) {
		$cardknoxOptions = Array(
			"SoftwareName" => "Tenant Portal",
			"SoftwareVersion" => "1.0",
			"PaymentMethodId" => $paymentId,
			"Name" => $account->name,
			"SetAsDefault" => true,
			"Revision" => $account->revision
		);
		$lid = $this->getLidfromCid($customerId);
		$postData = $cardknoxOptions;

		$handler = curl_init();

		curl_setopt($handler, CURLOPT_URL, "https://api.cardknox.com/v2/UpdatePaymentMethod");
		curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: '.$this->xkey($lid), 'X-Recurring-Api-Version: 2.1'));
		curl_setopt($handler, CURLOPT_POST, true);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($handler));
		if($response->Result == 'S') {
			$this->db->update('tenant_bank_accounts', Array('isDefault' => 0), Array('profile_id' => $account->profile_id));
			$this->db->update('tenant_bank_accounts', Array('isDefault' => 1, 'revision' => $account->revision + 1), Array('id' => $account->id));
		}
		return $response->PaymentMethodId;
	}

	public function addRecurring($account, $customerId, $data) {
		$pid = $this->addPayment($account, $customerId);
		$lid = $this->getLidfromCid($customerId);
		if(!$pid) return false;
		$this->setDefaultPayment($account, $customerId, $pid);
		$cardknoxOptions = Array(
			"SoftwareName" => "Tenant Portal",
			"SoftwareVersion" => "1.0",
			"CustomerId" => $customerId,
			"IntervalType" => $data['type'],
			"Amount" => (float)$data['amount'],
			"IntervalCount" => $data['interval'],
			"StartDate" => $data['start'],
			"EndDate" => $data['end'],
			"UseDefaultPaymentMethodOnly" => true
		);
		if($data['end'] == 'cancel') {
			unset($cardknoxOptions["EndDate"]);
			$cardknoxOptions["TotalPayments"] = '';
		} else if($data['end'] == 'lease_end') {
			$lease =  $this->tenantportal_model->getLease($this->tenantportal_model->getLeaseIdFromToken());
			//$cardknoxOptions["EndDate"] = date('Y-m-d', strtotime('next year'));
			$cardknoxOptions["EndDate"] = date('Y-m-d', strtotime($lease->end));
		}
		$postData = $cardknoxOptions;
		$handler = curl_init();

		curl_setopt($handler, CURLOPT_URL, "https://api.cardknox.com/v2/CreateSchedule");
		curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: '.$this->xkey($lid), 'X-Recurring-Api-Version: 2.1'));
		curl_setopt($handler, CURLOPT_POST, true);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($handler));
		//print_r($postData);
		//print_r($response);
		$result1 = new stdClass();
		if($response->Result == 'S') {
			$this->db->insert('tenant_autopay_data', Array('customer_id' => $customerId, 'payment_id' => $pid, 'schedule_id' => $response->ScheduleId));
			$result1->status = 1;
			$result1->message = 'Success.';
			$result1->ScheduleId = $response->ScheduleId;
			$result1->Amount = $data['amount'];
			$result1->start_date = $cardknoxOptions['StartDate'];
		} else {
			$result1->status = 0;
			$result1->message = $response->Error;
		}
		return $result1;
	}

	public function getRecurring($uid, $lid) {
		$autopay = $this->db->join('tenant_customers tc', 'tad.customer_id = tc.customer_id', 'LEFT')->get_where('tenant_autopay_data tad', Array('tc.profile_id' => $uid, 'tc.lease_id' => $lid))->row();
		if(!$autopay) return false;
		$cardknoxOptions = Array(
			"SoftwareName" => "Tenant Portal",
			"SoftwareVersion" => "1.0",
			"ScheduleId" => $autopay->schedule_id,
		);

		$postData = $cardknoxOptions;
		$handler = curl_init();

		curl_setopt($handler, CURLOPT_URL, "https://api.cardknox.com/v2/GetSchedule");
		curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: '.$this->xkey($lid), 'X-Recurring-Api-Version: 2.1'));
		curl_setopt($handler, CURLOPT_POST, true);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($handler));
		//print_r($postData);
		//print_r($response);
		$baccount = $this->db->get_where('tenant_bank_accounts', Array('paymentId' => $autopay->payment_id))->row();
		$response->baccount = $baccount->nickname.' ***'.$baccount->account;
		return $response;
	}

	public function doTransaction($uid, $lid, $amount, $bank_account) {
		$account = $this->db->get_where('tenant_bank_accounts b', Array('id' => $bank_account, 'b.profile_id' => $uid))->row();
		$customerId = $this->addCustomer($account->profile_id, $lid);
		$paymentId = $this->addPayment($account, $customerId);
		if(!$paymentId || !$customerId) {
			if(!$paymentId) {
				return (Object) Array('Result' => 'E', 'sut'=>true);
			}
		}

		$cardknoxOptions = Array(
			"SoftwareName" => "Tenant Portal",
			"SoftwareVersion" => "1.0",
			"PaymentMethodId" => $paymentId,
			"Amount" => $amount
		);

		$postData = $cardknoxOptions;
		$handler = curl_init();

		curl_setopt($handler, CURLOPT_URL, "https://api.cardknox.com/v2/ProcessTransaction");
		curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: '.$this->xkey($lid), 'X-Recurring-Api-Version: 2.1'));
		curl_setopt($handler, CURLOPT_POST, true);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($handler));
		//print_r($postData);
		//print_r(curl_exec($handler));

		return $response;
	}

	public function deleteRecurring($uid, $lid, $schedule_id = null) {
		if($schedule_id){
			$autopay->schedule_id = $schedule_id;
		} else {
            $autopay = $this->db->join('tenant_customers tc', 'tad.customer_id = tc.customer_id', 'LEFT')->get_where('tenant_autopay_data tad', Array('tc.profile_id' => $uid, 'tc.lease_id' => $lid))->row();
		}
		

		$cardknoxOptions = Array(
			"SoftwareName" => "Tenant Portal",
			"SoftwareVersion" => "1.0",
			"ScheduleId" => $autopay->schedule_id,
		);
		$postData = $cardknoxOptions;
		$handler = curl_init();

		curl_setopt($handler, CURLOPT_URL, "https://api.cardknox.com/v2/DeleteSchedule");
		curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: '.$this->xkey($lid), 'X-Recurring-Api-Version: 2.1'));
		curl_setopt($handler, CURLOPT_POST, true);
		curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);

		$response = json_decode(curl_exec($handler));
		$result1 = new stdClass();
		if($response->Result == 'S') {
			$this->db->delete('tenant_autopay_data', Array('schedule_id' => $autopay->schedule_id));
			$result1->status = 1;
			$result1->message = 'Success.';
		} else {
			$result1->status = 0;
			$result1->message = 'There was an error processing your request.';
		}

		return $result1;
	}

	public function enableRecurring($data, $uid, $lid) {
		//$lid = $this->tenantportal_model->getLeaseIdFromToken();
		if($data['enable']) {
			$account = $this->db->join('tenant_data td', 'b.profile_id = td.profile_id', 'LEFT')->get_where('tenant_bank_accounts b', Array('id' => $data['bank_account'], 'b.profile_id' => $uid))->row();
			$response = new stdClass();
			if(!isset($account)) {
				$response->status = 0;
				$response->message = 'You are not authorised to use this payment method.';
				return $response;
			}

			$customerId = $this->addCustomer($account->profile_id, $lid);
		}
		$autopay = $this->db->join('tenant_customers tc', 'tad.customer_id = tc.customer_id', 'LEFT')->get_where('tenant_autopay_data tad', Array('tc.profile_id' => $uid, 'tc.lease_id' => $lid))->row();


		if($data['enable'] && !$autopay) {
			return $this->addRecurring($account, $customerId, $data);
		} else if($data['schedule_id']) {
			return $this->deleteRecurring(null, null, $data['schedule_id']);
		} else if(!$data['enable'] && $autopay) {
			return $this->deleteRecurring($uid, $lid);
		} else {
			$response->status = 0;
			$response->message = 'There was an error processing your request.';
			return $response;
		}
	}
}
