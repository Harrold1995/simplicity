<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PropertyTaxes extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {

      
        $this->meta['title'] = "Property Taxes";
        $this->meta['h2'] = "Property Taxes";
        //$this->page_construct('Entities/index', null, $this->meta);


    }

    function editPropertyTaxes()
    {
        $array = [];
            $errors = "";
            $this->load->model('propertyTaxes_model');
            $this->load->model('validate_model');
            $propertyTaxes = $this->input->post('propertyTaxes');
            foreach($propertyTaxes as $key => $propertyTax){
                $data = array('propertyTaxes' => $propertyTax);
            $validate = $this->validate_model->validate("propertyTaxes", $data);
            //$this->form_validation->set_rules($this->settings->accountFormValidation);
            if (/*$this->form_validation->run() &&*/ $validate['bool'] && $this->propertyTaxes_model->editPropertyTaxes($propertyTax)){
                array_push($array, array('type' => 'success', 'message' => 'Property Tax successfully updated.', 'msgInfo' => $key));
            }else {
                $errors = $errors . $validate['msg'];
                array_push($array, array('type' => 'danger', 'message' => $errors, 'msgInfo' => $key));
            }
            }
            echo json_encode($array);
    }

    
    public function getModal()
    {
        $this->load->model('propertyTaxes_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                // $this->data['target'] = 'entities/addEntity';
                // $this->data['title'] = 'Add Entities';
                break;
            case 'get' :
                $this->data['target'] = 'propertyTaxes/editPropertyTaxes';
                $this->data['title'] = 'Edit Property Taxes';
                $this->data['propertyTaxes'] = $this->propertyTaxes_model->getAllPropertyTaxes();
                $this->data['jPropertyTaxes'] = json_encode($this->propertyTaxes_model->getAllPropertyTaxes());
                $this->data['accounts'] = json_encode($this->propertyTaxes_model->getAccounts());
            
                $this->load->view('forms/propertyTaxes/main', $this->data);
                break;
        }
        
    }

}