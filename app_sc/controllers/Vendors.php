<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Vendors extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('vendors_model');
        $this->load->model('tenants_model');//same as tenants
        $this->load->model('profiles_model');
        $this->load->model('documents_model');
    }

    function index()
    {
        $this->meta['title'] = "Vendors";
        $this->meta['h2'] = "Vendors";
        $this->page_construct('vendors/index', null, $this->meta);
    }
    
    function addVendor()
    {
        $data = $this->input->post('vendor');
        $data['profile_type_id'] = 1;
        if ($this->profiles_model->addProfile($data))
            echo json_encode(array(/*'id' => $id, 'es_text' => $data['first_name'] . " " . $data['last_name'],*/ 'type' => 'success', 'message' => 'Vendor successfully added.'));
    }

    function editVendor($tid = 0)
    {
        $data = $this->input->post('vendor');
        $contact = $this->input->post('contact');
        $address = $this->input->post('address');
        $deletes = $this->input->post('delete');
       
        $delete = $this->input->post('confirm');
        if($deletes && ($delete == NULL)){
            $response = $this->profiles_model->editProfile($data, $contact, $address, $tid, $deletes, $delete);
            echo json_encode(array('type' => 'warning', 'message' => $response));
            return;
        }
        if ($this->profiles_model->editProfile($data, $contact, $address, $tid, $deletes, $delete))
            echo json_encode(array('type' => 'success', 'message' => 'Vendor successfully updated.'));
    }

    function getModal()
    {
        $params = json_decode($this->input->post('params'));
        $this->data['contact_method_types'] = $this->settings->contact_method;
        $this->data['default_expense_accounts'] = $this->vendors_model->getAllAccounts();
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'vendors/addVendor';
                $this->data['title'] = 'Add Vendor';
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['vendor'] = new stdClass();
                //     $this->data['vendor']->$key = $params->es_value;
                // }
                break;
            case 'edit' :
                $this->data['target'] = 'vendors/editVendor/'. $this->input->post('id');
                $this->data['title'] = 'Edit Vendor';
                $this->data['vendor'] = $this->vendors_model->getVendor($this->input->post('id'));
                $this->data['contacts'] = $this->vendors_model->getContacts($this->input->post('id'));
                $this->data['addresses'] = $this->vendors_model->getAddresses($this->input->post('id'));
                $this->data['documents'] = $this->documents_model->getDocuments($this->input->post('id'),6);//number corresponds to database
                //$this->data['documentsInfo'] = $this->documents_model->getDocumentsProperty($this->input->get('id'));
                break;
        }
        $this->load->view('forms/vendors/main', $this->data);
    }
}
