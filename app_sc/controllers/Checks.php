<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Checks extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    function index()
    {
        $this->meta['title'] = "Checks";
        $this->meta['h2'] = "Checks";
        $this->page_construct('journalIndex/index', null, $this->meta);
    }

    function sec()
    {
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
    );
    print_r($csrf);
    }

   


    function addCheck()
    {
        $this->load->model('checks_model');
        $errors = "";
        $header = $this->input->post('header');
        $headerTransaction = $this->input->post('headerTransaction');
        $transactions = $this->input->post('transactions');
        $special = $this->input->post('checks');

        $data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions, 'special' => $special);
        $validate = $this->validate_model->validate("checks", $data);

        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/$validate['bool'] &&  $this->checks_model->addCheck($header, $headerTransaction, $transactions, $special))
            echo json_encode(array('type' => 'success', 'message' => 'Check successfully added.'));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function editCheck($tid = 0)
    {
        $this->load->model('checks_model');

        $header = $this->input->post('header');
        $headerTransaction = $this->input->post('headerTransaction');
        $transactions = $this->input->post('transactions');
        $special = $this->input->post('checks');
        $deletes = $this->input->post('delete');

        $data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions, 'special' => $special);
        $validate = $this->validate_model->validate("checks", $data);
       
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/$validate['bool'] &&   $this->checks_model->editCheck($header, $headerTransaction, $transactions, $tid, $deletes))
            echo json_encode(array('type' => 'success', 'message' => 'Check successfully edited.'));
        else {
            $errors = $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    public function onPrint()
    {   
        //$accounts1 = $this->input->get('accounts');
        $accounts = $this->input->get('accounts');
        $this->load->model('checks_model');
        //$data = json_encode($this->checks_model->onPrintMany($accounts));
        $data = json_encode($this->checks_model->onPrintMany($accounts));
        echo $data;

    }
        public function getDefaultBank($id)
        {   
            $this->load->model('checks_model');
            $bank = $this->checks_model->getDefaultBank($id);
            echo json_encode($bank);
        }

    function getModal()
    {
        $this->load->model('checks_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'checks/addCheck';
                $this->data['title'] = 'Add Check';
                $this->data['classes'] = $this->checks_model->getClasses();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['properties'] = $this->checks_model->getProperties();
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['banks'] = $this->checks_model->getAllBanks();
                $this->data['accounts'] = $this->checks_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);//not used anymore
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                //$this->data['units'] = $this->checks_model->getUnits();
                //$this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                //$this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['names'] = $this->checks_model->getNames();
                $this->data['jNames'] =  json_encode($this->data['names']);
                $this->data['jTransactions'] =  json_encode($transactions);
                $this->data['jPropertyAccounts'] =  json_encode($this->checks_model->getPropertyAccounts2());

                //return $this->checks_model->getAccounts($pid);
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                break;
            case 'edit' :
                $this->data['target'] = 'checks/editCheck/' . $this->input->post('id');
                $this->data['title'] = 'Edit Check';
                $this->data['classes'] = $this->checks_model->getClasses();
                $this->data['properties'] = $this->checks_model->getProperties();
                $this->data['banks'] = $this->checks_model->getAllBanks();
                $this->data['accounts'] = $this->checks_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['units'] = $this->checks_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['header'] = $this->checks_model->getHeader($this->input->post('id'));
                $transactions = $this->checks_model->getTransactions($this->input->post('id'));
                $headerTransaction = array_shift($transactions);
                $this->data['balance'] = $this->checks_model->getBalance($headerTransaction->account_id);
                $this->data['transactions'] =   $transactions;    
                $this->data['headerTransaction'] =  $headerTransaction; 
                $this->data['address'] = $this->checks_model->getAddress1($this->input->post('id'));
                $this->data['checks'] = $this->checks_model->getCheck($this->input->post('id'));
                $this->data['names'] = $this->checks_model->getNames();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);//not used anymore
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['jNames'] =  json_encode($this->data['names']);
                $this->data['jTransactions'] =  json_encode($transactions);
                $this->data['jPropertyAccounts'] =  json_encode($this->checks_model->getPropertyAccounts2());

               
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                break;
        }
        $this->load->view('forms/check/main', $this->data);
    }




    
}
