<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ApplyRefund extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    // function index()
    // {
    //     $this->meta['title'] = "Bills";
    //     $this->meta['h2'] = "Bills";
    //     $this->page_construct('journalIndex/index', null, $this->meta);
    // }

    function applyPayments()
    {
        $this->load->model('applyRefundSecurity_model');
        $header = $this->input->post('header');
        $$leaseInfo = $this->input->post('$leaseInfo');
        $applyAmount = $this->input->post('applyAmount');
        $refundAmount = $this->input->post('refundAmount');
        $checkingAccount = $this->input->post('checkingAccount');
        $applied_payments = $this->input->post('applied_payments');
        
       if (/*$validate['bool'] &&*/ $this->applyRefundSecurity_model->applyPayments($header, $leaseInfo, $applyAmount, $refundAmount, $checkingAccount, $applied_payments)){
        //($this->form_validation->run() && $validate['bool'] && $this->payBills_model->applyPayments( $sqlDate , $transactions)){
            echo json_encode(array('type' => 'success', 'message' => 'Transactions successfully added.'));
       }
        else {
            $errors = $validate['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors));
            //echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
      
    } 
    
    function editAppliedPayments() 
    {
        $this->load->model('applyRefundSecurity_model');
        $header = $this->input->post('header');
        $$leaseInfo = $this->input->post('$leaseInfo');
        $applyAmount = $this->input->post('applyAmount');
        $refundAmount = $this->input->post('refundAmount');
        $checkingAccount = $this->input->post('checkingAccount');
        $applied_payments = $this->input->post('applied_payments');
       if (/*$validate['bool'] &&*/ $this->applyRefundSecurity_model->editAppliedPayments($header, $leaseInfo, $applyAmount, $refundAmount, $checkingAccount, $applied_payments)){
        //($this->form_validation->run() && $validate['bool'] && $this->payBills_model->applyPayments( $sqlDate , $transactions)){
            echo json_encode(array('type' => 'success', 'message' => 'Transactions successfully edited.'));
       }
        else {
            $errors = $validate['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors));
            //echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
      
    }        

    public function getModal()
    {
        $this->load->model('applyRefundSecurity_model');
        $params = json_decode($this->input->post('params'));

        switch ($this->input->post('mode')) {

                case 'add' :
                $this->data['target'] = 'ApplyRefund/applyPayments';
                $this->data['title'] = 'APPLY/REFUND SECURITY/LMR';
                $this->data['tenants'] = $this->applyRefundSecurity_model->getTenants();
                $this->data['banks'] = $this->applyRefundSecurity_model->getBanks();
                $this->data['property_id'] = $params->property;
                $this->data['unit_id'] = $params->unit;
                $this->data['sdBalance'] = $this->applyRefundSecurity_model->getBalance($params->lease);
                $this->data['lease_id'] = $params->lease;
                $this->data['leases'] = $this->applyRefundSecurity_model->getLeases();
                $this->data['properties'] = $this->applyRefundSecurity_model->getProperties();
                $this->data['units'] = $this->applyRefundSecurity_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jTransactions'] = json_encode($this->applyRefundSecurity_model->getTransactions($params->lease)); 
               
                break;
            case 'edit' :
                $this->data['target'] = 'ApplyRefund/editAppliedPayments/' . $this->input->post('id');
                $this->data['title'] = 'Edit APPLY/REFUND SECURITY/LMR';
                $this->data['tenants'] = $this->applyRefundSecurity_model->getTenants();
                $this->data['banks'] = $this->applyRefundSecurity_model->getBanks();
                $this->data['property_id'] = $params->property;
                $this->data['unit_id'] = $params->unit;
                //$this->data['lease'] = $params->lease;
                $this->data['lease_id'] = $params->lease;
                $this->data['leases'] = $this->applyRefundSecurity_model->getLeases();
                $this->data['properties'] = $this->applyRefundSecurity_model->getProperties();
                $this->data['units'] = $this->applyRefundSecurity_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jTransactions'] = json_encode($this->applyRefundSecurity_model->getTransactionsEdit($this->input->post('id'))); 
                $this->data['header'] = $this->applyRefundSecurity_model->getHeaderEdit($this->input->post('id')); 

                break;
                } 
                $this->load->view('forms/apply_refund/main', $this->data);       
    }
}

