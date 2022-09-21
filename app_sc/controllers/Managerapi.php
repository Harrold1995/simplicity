<?php defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');
header("HTTP/1.1 200 OK");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	die;
}

class Managerapi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('managerportal_model');
		$this->site->initSettings();

    }

    public function login() {
    	$data = json_decode(file_get_contents("php://input"),true);
    	$response = $this->managerportal_model->checkLogin($data);
    	if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function user() {
		$uid = $this->verifyId();
		$response = $this->managerportal_model->getUserInfo($uid);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function tickets() {
		$uid = $this->verifyId();
		$data = json_decode(file_get_contents("php://input"),true);
		$response = $this->managerportal_model->getTickets($uid, $data['filters']);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function ticket($id) {
		$uid = $this->verifyId();
		$response = $this->managerportal_model->getTicket($uid, $id);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function tenant($id) {
		$uid = $this->verifyId();
		$response = $this->managerportal_model->getTenant($uid, $id);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function getMessages($id) {
		$data = json_decode(file_get_contents("php://input"),true);
		$uid = $this->verifyId();
		$response = $this->managerportal_model->getMessages($uid, $id, $data['last']);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function add() {
		$data = json_decode(file_get_contents("php://input"),true);
		$uid = $this->verifyId();
		$response = $this->managerportal_model->addTicket($uid, $data['data']);
		//if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function getSelectData() {
		$data = json_decode(file_get_contents("php://input"),true);
		$uid = $this->verifyId();
		$response = $this->managerportal_model->getSelectData($data);
		echo json_encode($response);
	}

	public function updateField() {
		$data = json_decode(file_get_contents("php://input"),true);
		$uid = $this->verifyId();
		$this->db->update("maintenance", Array($data['field'] => $data['data']), Array('id' => $data['tid']));
	}

	public function getFiles($id) {
		$uid = $this->verifyId();
		$response = $this->managerportal_model->getFiles($uid, $id);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function addMessage() {
		$data = json_decode(file_get_contents("php://input"),true);
		$tid = $data['tid'];
		$message = $data['message'];
		$uid = $this->verifyId();
		$response = $this->managerportal_model->addMessage($tid, $uid, $message);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function toggleClock() {
		$data = json_decode(file_get_contents("php://input"),true);
		$uid = $this->verifyId();
		$response = $this->managerportal_model->toggleClock($uid, $data);
		if($response->status == 0) header('HTTP/1.1 403 Unauthorized');;
		echo json_encode($response);
	}

	public function verifyId() {
		$uid = $this->managerportal_model->getUserIdFromToken();
		if(!$uid) {
			header('HTTP/1.1 401 Forbidden');
			die();
		}
		return $uid;
	}
}
