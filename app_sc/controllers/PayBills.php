<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PayBills extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    function index()
    {

      
        $this->meta['title'] = "Pay Bills";
        $this->meta['h2'] = "Pay Bills";
       // $this->page_construct('reconciliations/index', null, $this->meta);


    }

    

   


    function applyPayments() 
    {
        $this->load->model('payBills_model');
        $transactions = $this->input->post('transactions');
       
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        /*if ($this->form_validation->run() &&*/
        $date = $this->input->post('pay_bill_date');
        if($date){
        $date = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
        }
       //$data = array('header' => $header, 'transactions' => $transactions);
       $validate = $this->validate_model->validate("payBills", $transactions); 
       if ($validate['bool'] && $this->payBills_model->applyPayments( $date , $transactions)){
        //($this->form_validation->run() && $validate['bool'] && $this->payBills_model->applyPayments( $sqlDate , $transactions)){
            echo json_encode(array('type' => 'success', 'message' => 'Transaction successfully added.'));
       }
        else {
            $errors = $validate['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors));
            //echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
      
    }

    function editAppliedPayments($aid = 0)
    {
        $this->load->model('payBills_model');
        $header = $this->input->post('header');
        $amount = $this->input->post('amount');
        $payedFrom = $this->input->post('bank');
        $property_id = $this->input->post('property');
        $profile_id = $this->input->post('vendor');
        $applied_payments = $this->input->post('applied_payments');

        
        if ($this->payBills_model->editAppliedPayments($profile_id, $amount, $payedFrom, $property_id, $header, $applied_payments)){
            echo json_encode(array('type' => 'success', 'message' => 'Applied payment successfully updated.'));
        }
    }

    
    public function getModal()
    {
        $this->load->model('payBills_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'payBills/applyPayments';
                $this->data['title'] = 'Pay Bills';
                $this->data['vendors'] = $this->payBills_model->getVendor();
                $this->data['paymentMethods'] = $this->payBills_model->getPaymentMethods();
                $this->data['bankAccounts'] = $this->payBills_model->getBanks();
                $this->data['CcAccounts'] = $this->payBills_model->getCC();
                $this->data['transactions'] = $this->payBills_model->getTransactions(null, null, null);
                $this->data['accounts'] = $this->payBills_model->getAccounts();
                $this->data['properties'] = $this->payBills_model->getProperties();
               
                
                $this->load->view('forms/vendor/main', $this->data);
                break;
            case 'edit' ://which values are we passing into the edit 
                $this->data['target'] = 'payBills/editAppliedPayments/' . $this->input->post('id');
                $this->data['title'] = 'Edit Payed Bills';
                $this->data['bankAccounts'] = $this->payBills_model->getBanks();
                $this->data['accounts'] = $this->payBills_model->getAccounts();
                $this->data['vendors'] = $this->payBills_model->getVendor();
                $this->data['properties'] = $this->payBills_model->getProperties();
                $this->data['header'] = $this->payBills_model->getHeaderEdit($this->input->post('id'));
                $this->data['transactions'] = $this->payBills_model->getTransactionsEdit($this->input->post('id'));
               
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                // }
                        $this->load->view('forms/vendor/editBill', $this->data);
                break;
        }
        //$this->load->view('forms/receive_payment', $this->data);
       
        
    }
}