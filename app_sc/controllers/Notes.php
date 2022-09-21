<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notes extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->icons = Array("property" => "fas fa-building", "unit" => "far fa-building");
        $this->load->model('validate_model');
        $this->load->model('notes_model');
    }

    function index()
    {   
        $this->meta['title'] = "Notes";
        $this->meta['h2'] = "Notes";
        //?$this->page_construct('notes/index', null, $this->meta);
    }

    function addNote($nid = null) 
    {
        $errors = "";
        $this->load->model('notes_model');
        $data = $this->input->post();
        switch($nid){
            case 'property':
                $data['object_type_id'] = 1;
            break;
            case 'unit' :
                $data['object_type_id'] = 3;
            break;
            case 'account' :
                $data['object_type_id'] = 4;
            break;
            case 'tenant' :
                $data['object_type_id'] = 5;
            break;
            case 'lease' :
                $data['object_type_id'] = 2;
            break;
            case 'vendors' :
                $data['object_type_id'] = 6;
            break;
            case 'inventory' :
                $data['object_type_id'] = 7;
            break;
            case 'in_court' :
                $data['object_type_id'] = 16;
            break;
            case 'utilities' :
                $data['object_type_id'] = 9;
            break;
        }
        $date = date('Y-m-d H:i:s');
        $data['note_date'] = $date;
        //$validate = $this->validate_model->validate("account", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() && $validate['bool'] && */$this->notes_model->addNote($data))
            echo json_encode(array('type' => 'success', 'message' => 'New note successfully added.', 'date' => $date));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('account')));

        }
    }

    function editAccount($aid = 0)
    {
        // $this->load->model('validations_model');
        // $this->data['validate'] = $this->validations_model->validate("accounts", $this->input->post());
        // if(!$this->data['validate']){
        //     echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('account')));
        // }else{
        //     $this->load->model('validations_model');
            $errors = "";
            $this->load->model('accounts_model');
            $account = $this->input->post('account');
            $table = $this->input->post('table');
            $specialAccount = $this->input->post('specialAccount');
            $propertyAccounts = $this->input->post('propertyAccounts');
            $data = array('account' => $account, 'table' => $table, 'specialAccount' => $specialAccount);
            $validate = $this->validate_model->validate("account", $data);
            $this->form_validation->set_rules($this->settings->accountFormValidation);
            if ($this->form_validation->run() && $validate['bool'] && $this->accounts_model->editAccount($account, $specialAccount, $aid,  $table, $propertyAccounts)){
                echo json_encode(array('type' => 'success', 'message' => 'Account successfully updated.'));
            }else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('account')));

        }
    }

    
    public function getModal()
    {
        $this->load->model('notes_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'notes/addNote';
                $this->data['title'] = 'Add Note';

                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                // }
                break;
            case 'edit' :
                $this->data['target'] = 'accounts/editAccount/' . $this->input->post('id');
                $this->data['title'] = 'Edit Account';
                if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }
                break;
        }

        $this->load->view('forms/note.php', $this->data);
        
    }

    function getNotes($id,$type){
        switch ($type) {
            case 'in_court' :
                $type = 16;
                break;
            case 'utilities' :
                $type = 9;
                break;
        }
        $in_courtNotes = $this->notes_model->getNotes($id,$type);//number corresponds to database
        echo json_encode($in_courtNotes);
    }

}