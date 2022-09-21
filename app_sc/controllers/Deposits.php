<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Deposits extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        
    }

    function index()
    {

      
        $this->meta['title'] = "Deposits";
        $this->meta['h2'] = "Deposit Payments";
       // $this->page_construct('reconciliations/index', null, $this->meta);


    }

   
    

    function depositPayments() 
    {
        $this->load->model('deposits_model');
        $account_id = $this->input->post('account_id');
        $header = $this->input->post('header');
        if($header['date']){
            $header['date'] = sqlDate($header['date']);
        }
        $amount = $this->input->post('amount');
        $totalAmount = $this->input->post('totalAmount');
        $undeposited = $this->input->post('undeposited');
        $otherDeposits = $this->input->post('otherDeposits'); 
        
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        /*if ($this->form_validation->run() &&*/ $this->deposits_model->depositPayments($account_id,  $header, $amount, $totalAmount, $undeposited, $otherDeposits);
            echo json_encode(array('type' => 'success', 'message' => 'Payments successfully deposited.'));
        // else {
        //     echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('account')));
        // }
    }

    function editDeposits($aid = 0)
    {
        $this->load->model('deposits_model');
        $account_id = $this->input->post('account_id');
        $header = $this->input->post('header');
        if($header['date']){
            $header['date'] = sqlDate($header['date']);
        }
        $amount = $this->input->post('amount');
        $totalAmount = $this->input->post('totalAmount');
        $undeposited = $this->input->post('undeposited');
        $otherDeposits = $this->input->post('otherDeposits');
        $deletes = $this->input->post('delete');

        
        if ($this->deposits_model->editDeposits($account_id, $header, $amount, $totalAmount, $undeposited, $otherDeposits, $deletes));
            echo json_encode(array('type' => 'success', 'message' => 'Deposits successfully updated.'));
    }

    
    public function getModal()
    {
        $this->load->model('deposits_model');
        $this->load->model('checks_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'deposits/depositPayments';
                $this->data['title'] = 'Deposits';
                $this->data['undepositedChecks'] = $this->deposits_model->getUndepositedFunds(null);
                $this->data['properties'] = $this->deposits_model->getProperties();
                $this->data['names'] = $this->deposits_model->getNames();
                $this->data['banks'] = $this->deposits_model->getDepositTo();
                $this->data['classes'] = $this->deposits_model->getClasses();
                $this->data['accounts'] = $this->deposits_model->getAllAccounts();

                $this->data['jProperties'] = json_encode($this->data['properties']);

                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->deposits_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
               // $this->data['jUnits'] =  json_encode($this->data['units']);
                $this->data['jPropertyAccounts'] =  json_encode($this->checks_model->getPropertyAccounts2());


               
          
                break;
            case 'edit' ://which values are we passing into the edit 
                $this->data['target'] = 'deposits/editDeposits/' . $this->input->post('id');
                $this->data['title'] = 'Edit Deposits';
                $this->data['header'] = $this->deposits_model->getHeaderEdit($this->input->post('id'));
                $this->data['undepositedChecks'] = $this->deposits_model->getDepositedEdit($this->input->post('id'));
                $this->data['otherDeposits'] = $this->deposits_model->getOtherDepositsEdit($this->input->post('id'));
                $this->data['properties'] = $this->deposits_model->getProperties();
                $this->data['names'] = $this->deposits_model->getNames();
                $this->data['accounts'] = $this->deposits_model->getAllAccounts();
                $this->data['banks'] = $this->deposits_model->getDepositTo();
                $this->data['classes'] = $this->deposits_model->getClasses();

                break;
        }

        $this->load->view('forms/deposit/main', $this->data);
        
    }
}