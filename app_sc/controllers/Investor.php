<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Investor extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('investors_model');
    }

    function index()
    {
        $this->meta['title'] = "Investor";
        $this->meta['h2'] = "Investor";
        $this->page_construct('Investor/index', $this->data, $this->meta);
    }

    function addInvestor()
    {
        $data = $this->input->post('investor');
        if ($id = $this->investors_model->addInvestor($data))
            echo json_encode(array('id' => $id, 'es_text' => $data['first_name'] . " " . $data['last_name'], 'type' => 'success', 'message' => 'Investor successfully added.'));
    }

    function editInvestor($Iid = 0)
    {
        $data = $this->input->post('tenant');
        $contact = $this->input->post('contact');
        $address = $this->input->post('address');
        $deletes = $this->input->post('delete');
       
        $delete = $this->input->post('confirm');
        if($deletes && $delete == NULL){
            $response = $this->investors_model->editInvestor($data, $contact, $address, $Iid, $deletes, $delete);
            echo json_encode(array('type' => 'warning', 'message' => $response));
            return;
        }
        if ($this->investors_model->editInvestor($data, $contact, $address, $Iid, $deletes, $delete))
            echo json_encode(array('type' => 'success', 'message' => 'Investor successfully updated.'));
    }

    function getModal()
    {
        $params = json_decode($this->input->post('params'));
        $this->data['contact_method_types'] = $this->settings->contact_method;
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'investor/addInvestor';
                $this->data['title'] = 'Add Investor';
                $this->load->view('forms/investors/main', $this->data);
                break;
            case 'edit' :
                $this->data['target'] = 'investor/editInvestor/'. $this->input->post('id');
                $this->data['title'] = 'Edit Investor';
                $this->data['investor'] = $this->investors_model->getInvestor($this->input->post('id'));
                $this->data['contacts'] = $this->investors_model->getContacts($this->input->post('id'));
                $this->data['addresses'] = $this->investors_model->getAddresses($this->input->post('id'));
                //$this->data['documents'] = $this->documents_model->getDocuments($this->input->post('id'),5);//number corresponds to database
                $this->load->view('forms/investors/main', $this->data);
                break;
            case 'get' :
                $this->data['title'] = 'Investors';
                $this->data['investors'] = $this->investors_model->getAllInvestors();
                $this->load->view('forms/investors/allInvestors', $this->data);
                break;
        }
    }
}
