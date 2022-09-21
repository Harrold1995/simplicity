<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bank_transfer extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('bank_transfer_model');
    }

    function index()
    {
        $this->meta['title'] = "Investor";
        $this->meta['h2'] = "Investor";
        $this->page_construct('Investor/index', $this->data, $this->meta);
    }

    function addBank_transfer()
    {
        $data = $this->input->post('bank_transfer');
        $this->load->model('validate_model');
        $validate = $this->validate_model->validate("bank_transfer", $data);
        $header = ['transaction_date' => $data['date'], 'memo' => $data['memo']];
        $from = ['account_id' => $data['from_account_id'], 'property_id' => $data['property_id'], 'credit' => $data['amount']];
        $to = ['account_id' => $data['to_account_id'], 'property_id' => $data['property_id'], 'debit' => $data['amount']];
        if ($validate['bool'] && $this->bank_transfer_model->addBank_transfer($header, $from, $to)) {
            echo json_encode(array('type' => 'success', 'message' => 'Transfer completed successfully.'));
            return true;
        } else {
            $errors = $errors . validation_errors() . "</br>" . $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors));
            return false;
        }
    }

    // function editInvestor($Iid = 0)
    // {
    //     $data = $this->input->post('tenant');
    //     $contact = $this->input->post('contact');
    //     $address = $this->input->post('address');
    //     $deletes = $this->input->post('delete');
       
    //     $delete = $this->input->post('confirm');
    //     if($deletes && $delete == NULL){
    //         $response = $this->investors_model->editInvestor($data, $contact, $address, $Iid, $deletes, $delete);
    //         echo json_encode(array('type' => 'warning', 'message' => $response));
    //         return;
    //     }
    //     if ($this->investors_model->editInvestor($data, $contact, $address, $Iid, $deletes, $delete))
    //         echo json_encode(array('type' => 'success', 'message' => 'Investor successfully updated.'));
    // }

    function getModal()
    {
        $params = json_decode($this->input->post('params'));
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'bank_transfer/addBank_transfer';
                $this->data['title'] = 'Bank transfer';
                $this->data['properties'] = $this->bank_transfer_model->getAllProperties();
                $this->data['banks'] = $this->bank_transfer_model->getBanks();
                $this->load->view('forms/bank_transfer/main', $this->data);
                break;
        }
    }
}
