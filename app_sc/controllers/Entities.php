<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Entities extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    function index()
    {

      
        $this->meta['title'] = "Entities";
        $this->meta['h2'] = "Entities";
        $this->page_construct('Entities/index', null, $this->meta);


    }

    function addEntity() 
    {

        $errors = "";
        $this->load->model('entities_model');

        $entity = $this->input->post('entities');
        //$data = array('id' => $eid, 'entity' => $entity);
        //$validate = $this->validate_model->validate("entity", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() && $validate['bool'] &&*/ $this->entities_model->addEntity($entity)){
            echo json_encode(array('type' => 'success', 'message' => 'Entity successfully added.'));
        }
        else {
            $errors = $errors . $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('entity')));

        }
    }

    function editEntity($eid = 0)
    {
      
            $errors = "";
            $this->load->model('entities_model');
            $entity = $this->input->post('entities');
            $entity['id'] = $eid;
            
            $data = array('id' => $eid, 'entity' => $entity);
            //$validate = $this->validate_model->validate("entity", $data);
            //$this->form_validation->set_rules($this->settings->accountFormValidation);
            if (/*$this->form_validation->run() && $validate['bool'] &&*/ $this->entities_model->editEntity($entity)){
                echo json_encode(array('type' => 'success', 'message' => 'Entity successfully updated.'));
            }else {
            $errors = $errors . $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('entity')));

        }
    }

    
    public function getModal()
    {
        $this->load->model('entities_model');
        $this->load->model('accounts_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'entities/addEntity';
                $this->data['title'] = 'Add Entities';
                $this->load->view('forms/entities/main', $this->data);
                break;
            case 'edit' :
                $this->data['target'] = 'entities/editEntity/' . $this->input->post('id');
                $this->data['title'] = 'Edit Entity';
                $this->data['entities'] = $this->entities_model->getEntity($this->input->post('id'));
            
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                // }
                $this->load->view('forms/entities/main', $this->data);
                break;
            case 'allEntities' :
                $this->data['target'] = 'entities/allEntities/';
                $this->data['title'] = 'All Entities';
                $this->data['entities'] = $this->entities_model->getAllEntities();
                $this->data['jentities'] = json_encode($this->data['entities']);
                $this->load->view('forms/entities/allEntities', $this->data);
                break;
        }
        
    }

}