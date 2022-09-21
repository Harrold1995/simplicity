<?php defined('BASEPATH') OR exit('No direct script access allowed');

class In_court extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        //$this->load->model('leases_model');
        //$this->load->model('validate_model');
        $this->load->model('notes_model');
    }

    function editIn_court($eic = 0)
    {
      
            $errors = "";
            $this->load->model('in_court_model');
            $in_court = $this->input->post('in_court');
            
            $data = array('id' => $eid, 'entity' => $in_court);
            //$validate = $this->validate_model->validate("entity", $data);
            //$this->form_validation->set_rules($this->settings->accountFormValidation);
            if (/*$this->form_validation->run() && $validate['bool'] &&*/ $this->in_court_model->editIn_court($in_court)){
                echo json_encode(array('type' => 'success', 'message' => 'In court successfully updated.'));
            }else {
            //$errors = $errors . $validate['msg'];
            $errors = 'failed';
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('in_court')));

        }
    }

    function getModal()
    {
        $this->load->model('in_court_model');
        switch ($this->input->post('mode')) {
            case 'in_court' :
                $this->data['target'] = 'in_court/editIn_court';
                $this->data['title'] = 'In Court';
                $this->data['jIn_court'] = json_encode($this->in_court_model->getIn_court());
                break;
        }
        $this->load->view('forms/in_court/main', $this->data);
    }

    function getIn_courtNotes($id){
        $in_courtNotes = $this->notes_model->getNotes($id,16);//number corresponds to database
        echo json_encode($in_courtNotes);
    }

}
