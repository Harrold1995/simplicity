<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CreditCard extends MY_Controller
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


    function addTransaction() 
    {
        $this->load->model('creditCard_model');
        $header = $this->input->post('header');
        $creditCard = $this->input->post('credit_card');
        $transactions = $this->input->post('transactions');
        
    //     //$this->form_validation->set_rules($this->settings->accountFormValidation);
    //     /*if ($this->form_validation->run() &&*/
    //     $date = $this->input->post('pay_bill_date');
    //     if($date){
    //     $date = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
    //     }
        $data = array('header' => $header, 'transactions' => $transactions, 'creditCard' => $creditCard);
        $validate = $this->validate_model->validate("cc_charge", $data); 
       if ($validate['bool'] && $this->creditCard_model->addTransaction( $header , $creditCard, $transactions)){
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
    
    function editTransaction() 
    {
        $this->load->model('creditCard_model');
        $header = $this->input->post('header');
        $transactions = $this->input->post('transactions');
        $creditCard = $this->input->post('credit_card');
        $deletes = $this->input->post('delete');
    //     //$this->form_validation->set_rules($this->settings->accountFormValidation);
    //     /*if ($this->form_validation->run() &&*/
    //     $date = $this->input->post('pay_bill_date');
    //     if($date){
    //     $date = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
    //     }
        $data = array('header' => $header, 'transactions' => $transactions, 'creditCard' => $creditCard);
        $validate = $this->validate_model->validate("cc_charge", $data); 
       if ($validate['bool'] && $this->creditCard_model->editTransaction($header, $creditCard, $transactions, $deletes)){
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

    function recordTransactions() 
    {
        $this->load->model('creditCard_model');
        $account_id = $this->input->post('ccAccount_id');
        $transactions = $this->input->post('transactions');
        $details = $this->input->post('details');


    //     //$this->form_validation->set_rules($this->settings->accountFormValidation);
    //     /*if ($this->form_validation->run() &&*/
    //     $date = $this->input->post('pay_bill_date');
    //     if($date){
    //     $date = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
    //     }
        $data = array('account_id' => $account_id, 'transactions' => $transactions, 'details' => $details);
        $validate = $this->validate_model->validate("cc_grid_charge", $data); 
       if ($validate['bool'] && $this->creditCard_model->recordTransactions($account_id , $transactions, $details)){
        //($this->form_validation->run() && $validate['bool'] && $this->payBills_model->applyPayments( $sqlDate , $transactions)){
            echo json_encode(array('type' => 'success', 'message' => 'Transactions successfully added.'));
       }
        else {
            $errors = $validate['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            //echo json_encode(array('type' => 'danger', 'message' => $errors));
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
      
    }
        
    
    function getAccount()
    {   
        $id = $this->input->get('id');
        $this->load->model('ofx_model');
        $this->load->model('creditCard_model');
        //$this->ofx_model->getOfx($this->input->get('id'));
        $test = json_encode($this->creditCard_model->getOfxImports($this->input->get('id')));
        echo $test;
        //return json_encode($this->creditCard_model->getOfxImports($this->input->get('id')));
        //$this->parse($result);

    }   

    public function getModal()
    {
        $this->load->model('ofx_model');
        $this->load->model('creditCard_model');

        switch ($this->input->post('mode')) {
            case 'record' :
                $this->data['target'] = 'creditCard/recordTransactions';
                $this->data['title'] = 'CC Transactions';
                $this->data['creditCards'] = json_encode($this->creditCard_model->getCC());
                $this->data['njfirstAccount'] = $this->creditCard_model->getCC1();
                $this->data['firstAccount'] = json_encode($this->data['njfirstAccount']);
                $firstAccountId = $this->data['njfirstAccount']->id;
                //$this->ofx_model->getOfx($firstAccountId);
                //$this->ofx_model->getOfx(975);
                $this->data['ofxImports'] = json_encode($this->creditCard_model->getOfxImports($firstAccountId));
               
                //$this->data['target'] = 'creditCard/addCcCharge';
                $this->data['title'] = 'Add Credit Card Charge';
                 $this->data['properties'] = $this->creditCard_model->getProperties();
                // $this->data['names'] = $this->creditCard_model->getNames();
                // $this->data['classes'] = $this->creditCard_model->getClasses();
                 $this->data['accounts'] = $this->creditCard_model->getAllAccounts();
                // $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                 $this->data['units'] = $this->creditCard_model->getUnits();
                // $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                $this->data['jUnits'] =  json_encode($this->data['units']);
                $this->data['jPropertyAccounts'] =  json_encode($this->creditCard_model->getPropertyAccounts2());
                $this->data['names'] = $this->creditCard_model->getProfiles();
                $this->data['jNames'] =  json_encode($this->data['names']);
                $this->load->view('forms/cc/cc_grid_charge', $this->data);
                break;
                case 'add' :
                $this->data['target'] = 'creditCard/addTransaction';
                $this->data['title'] = 'Add Credit Card Charge';
                $this->data['properties'] = $this->creditCard_model->getProperties();
                $this->data['vendors'] = $this->creditCard_model->getVendors();
                $this->data['classes'] = $this->creditCard_model->getClasses();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['creditCard'] = $this->creditCard_model->getCC();
                $this->data['accounts'] = $this->creditCard_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['units'] = $this->creditCard_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                $this->data['jUnits'] =  json_encode($this->data['units']);
                $this->data['names'] = $this->creditCard_model->getProfiles();
                $this->data['jNames'] =  json_encode($this->data['names']);
               
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                $this->load->view('forms/cc/cc_charge', $this->data);
                break;
            case 'edit' :
                $this->data['header'] = $this->creditCard_model->getHeaderEdit($this->input->post('id'));
                $this->data['transactions'] = $this->creditCard_model->getTransactions($this->input->post('id'));
                $this->data['accountName'] = $this->creditCard_model->getAccountName($this->data['transactions'][0]->account_id);
                $this->data['jHeader'] = json_encode($this->creditCard_model->getHeaderEdit($this->input->post('id')));
                $this->data['jTransactions'] = json_encode($this->creditCard_model->getTransactions($this->input->post('id')));
                $this->data['target'] = 'creditCard/editTransaction/' . $this->input->post('id');
                $this->data['title'] = 'Edit Credit Card Charge';
                $this->data['properties'] = $this->creditCard_model->getProperties();
                $this->data['creditCard'] = $this->creditCard_model->getCC();
                $this->data['vendors'] = $this->creditCard_model->getVendors();
                $this->data['classes'] = $this->creditCard_model->getClasses();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['accounts'] = $this->creditCard_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['units'] = $this->creditCard_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                $this->data['jUnits'] =  json_encode($this->data['units']);
                $this->data['names'] = $this->creditCard_model->getProfiles();
                $this->data['jNames'] =  json_encode($this->data['names']);
               
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                $this->load->view('forms/cc/cc_charge', $this->data);
                break;
                }
                //break;

           
    }
function t(){
    $this->load->model('encryption_model');
    // $cinfo = ['cc_num' => 'x', 'user_id' => 'x', 'password' => 'x'];
    //     $cinfo = $this->encryption_model->encryptThis($cinfo);
    //     var_dump($cinfo);
    $this->db->select('cc_num, user_id AS userId, password');
        $this->db->from('credit_cards');
        $this->db->where('account_id', 975);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $data = $q->row();
            $data = $this->encryption_model->decryptThis($data);
            $cc_num = $data->cc_num;
            $userId = $data->userId;
            $password = $data->password;
            // $cc_num = $q->row()->cc_num;
            // $userId = $q->row()->userId;
            // $password = $q->row()->password;
            var_dump($data);
        }
}
       
        
    //}
}

