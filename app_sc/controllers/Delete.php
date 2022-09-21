<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
        $this->load->model('delete_model');
    }

    // function index()
    // {
    //     $this->meta['title'] = "Charges";
    //     $this->meta['h2'] = "Charges";
    //     $this->page_construct('journalIndex/index', null, $this->meta);
    // }
    function checkItems()
    {
        //$this->load->model('delete_model');
        $response = json_encode($this->delete_model->checkItems($th_id, $delete = NULL));
        echo $response;
    }

    function deleteProperty($pid = 0)
    {
        $this->load->model('properties_model');
        $validate = $this->input->post('validate');
        $result = $this->validateDelete($pid);
        if ($result['status'] == 1) {
            if($validate!='yes') $this->properties_model->deleteProperty($pid);
            echo json_encode(array('type' => 'success', 'message' => 'Property successfully deleted.'));
        }else
            echo json_encode(array('type' => 'danger', 'message' => $result['error']));
    }

    function validateDelete($id)
    {
        $this->load->model('units_model');
        $result = Array("status" => 1);
        $units = $this->units_model->getPropertyUnits($id);
        if(count($units) > 0) {
            $result['status'] = 0;
            $result['error'] = 'There are units linked to this property, cannot delete!';
        }
        return $result;
    }

    function deleteTransaction($th_id = 0)
    {
        $delete = 1;
        $validate = $this->input->post('validate');
        if($validate ==='yes') $delete = NULL;
        $message = $this->delete_model->checkTransaction($th_id, $delete);
        if ($message->status === 0) {
            //if($validate!='yes') $this->properties_model->deleteProperty($pid);
            echo json_encode(array('type' => 'success', 'message' => 'Transaction successfully deleted.', 'messages' => $message->messages));
        }else{
            echo json_encode(array('type' => 'danger', 'message' => $message->message));
        }
    }

    function deleteAccount($account_id)
    {
        $delete = 1;
        $validate = $this->input->post('validate');
        if($validate ==='yes') $delete = NULL;
        $message = $this->delete_model->checkAccount($account_id, $delete);
        if ($message->status === 0) {
            echo json_encode(array('type' => 'success', 'message' => 'Account successfully deleted.', 'messages' => $message->messages));
        }else{
            echo json_encode(array('type' => 'danger', 'message' => $message->message));
        }
    }
    

    function deleteName($profile_id)
    {
        $delete = 1;
        $validate = $this->input->post('validate');
        if($validate ==='yes') $delete = NULL;
        $message = $this->delete_model->checkName($profile_id, $delete);
        if ($message->status === 0) {
            echo json_encode(array('type' => 'success', 'message' => 'Name successfully deleted.', 'messages' => $message->messages));
        }else{
            echo json_encode(array('type' => 'danger', 'message' => $message->message));
        }
    }

    // function test()
    // {
    //     $this->load->model('delete_model');
    //    $this->delete_model->DeleteAccount(3000);
       
    // }





    
}
