<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Employees extends MY_Controller
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
        $this->meta['title'] = "Employees";
        $this->meta['h2'] = "Employees";
        $this->page_construct('Employees/index', null, $this->meta);
    }
    
    function addEmployee()
    {
        $data = $this->input->post('employee');
        $data['profile_type_id'] = 4;
        if ($this->profiles_model->addProfile($data))
            echo json_encode(array(/*'id' => $id, 'es_text' => $data['first_name'] . " " . $data['last_name'],*/ 'type' => 'success', 'message' => 'employee successfully added.'));
    }

    function editEmployee($tid = 0)
    {
        $data = $this->input->post('employee');
        $contact = $this->input->post('contact');
        $address = $this->input->post('address');
        $deletes = $this->input->post('delete');
        $delete = $this->input->post('confirm');
        $user = $this->input->post('user');

        $user2 = array(
            'profile_id' => $user->profile_id,
            'company' => $user->company,
            'phone' => $user->phone,
            'email' => $user->email,
            'username' => $user->username,
            'email_password' => $user->email_password,
        );

        // update the password if it was posted
        if ($this->input->post('password')) {
            $user2['password'] = $this->input->post('password');
        }

        if ($this->profiles_model->editProfile($data, $contact, $address, $tid, $deletes, $delete))
            echo json_encode(array('type' => 'success', 'message' => 'Employee successfully updated.'));
    }

    function getModal()
    {
        $params = json_decode($this->input->post('params'));
        $this->data['contact_method_types'] = $this->settings->contact_method;
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'employees/addEmployee';
                $this->data['title'] = 'Add Employee';
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['vendor'] = new stdClass();
                //     $this->data['vendor']->$key = $params->es_value;
                // }
                break;
            case 'edit' :
                $this->data['target'] = 'employees/editEmployee/'. $this->input->post('id');
                $this->data['title'] = 'Edit Employee';
                $this->data['employee'] = $this->tenants_model->getTenant($this->input->post('id'));
                $this->data['contacts'] = $this->vendors_model->getContacts($this->input->post('id'));
                $this->data['user'] = $this->vendors_model->getPortalLogin($this->input->post('id'));
                $this->data['addresses'] = $this->vendors_model->getAddresses($this->input->post('id'));
                $this->data['documents'] = $this->documents_model->getDocuments($this->input->post('id'),6);//number corresponds to database
                //$this->data['documentsInfo'] = $this->documents_model->getDocumentsProperty($this->input->get('id'));
                break;
        }
        $this->load->view('forms/employees/main', $this->data);
    }
}
