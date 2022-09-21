<?php defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Authorization');
error_reporting(0);
ini_set('display_errors', false);
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	die;
}

class Formbuilderapi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('encryption_model');
		$this->load->model('tenantportal_model');
		$this->load->library('JWT');
		$this->CONSUMER_SECRET = JWT_SECRET_KEY;
		$this->CONSUMER_TTL = 86400;

    }

	function fetchdata($id){
		header("content-type: application/json");
		$uid = $this->tenantportal_model->getUserIdFromToken();
		$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'https://';
		$thisurl = $protocol. $_SERVER['HTTP_HOST'];
		$result=(Object)Array("saveUrl" => $thisurl.'/formbuilderapi/savedata/'.$id, "error" => "");
		if($uid) {
			$q = $this->db->get_where('lease_templates', array('id' => $id), 1);
			if ($q->num_rows() > 0){
				if ($q->row()->data) $result->data = $q->row()->data;
				else $result->data = '[{ "type": "paragraph", "children" : [{"text": "" }]}]';
			}
		} else {
			$result->error = "Not authorised";
		}
		echo json_encode($result);
	}

	function fetchoptions($id){
		header("content-type: application/json");
		$this->load->model('formbuilder_model');
		$type = $this->input->get('option');
		$result = $this->formbuilder_model->getFields($type);
		echo json_encode($result);
	}

	function savedata($id){
		$uid = $this->tenantportal_model->getUserIdFromToken();

		if($uid) {
			$data = json_decode(file_get_contents('php://input'), true);
			$insert = array('html' => $data['html'], 'data' => json_encode($data['bfdata']), 'recordset' => $data['recordset']);
			$this->db->update('lease_templates', $insert, array('id' => $id));
			$result = (object)array("status" => 1, "message" => "Lease template saved.");
		} else {
			$result = (object)array("status" => 0, "message" => "Not authorised");
		}
		echo json_encode($result);
	}


}
