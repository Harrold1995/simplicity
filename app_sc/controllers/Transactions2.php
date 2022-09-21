<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions2 extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->ar = $this->site->settings->accounts_receivable;
        $this->ap = $this->site->settings->accounts_payable;
        $this->sd = $this->site->settings->security_deposits;
        $this->uf = $this->site->settings->undeposited_funds;
        $this->lmr = $this->site->settings->lmr;
        $this->load->model('validate_model');
    }

    function index()
    {
        $this->meta['title'] = "Transactions";
        $this->meta['h2'] = "Transactions";
        $this->page_construct('transactions/index', null, $this->meta);
    }

   

    function sec()
    {
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
    );
    print_r($csrf);
    }


    function addTransaction()
    {
        $this->load->model('transactions_model');

        $header = $this->input->post('header');
        $transactions = $this->input->post('transactions');

        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/ $this->transactions_model->addTransaction($header, $transactions))
            echo json_encode(array('type' => 'success', 'message' => 'Transaction successfully added.'));
        /*else {
            echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('transaction')));
        }*/
    }

    function getModal()
    {
        $this->load->model('accounts_model');
        $this->load->model('transactions_model');

        //$params = json_decode($this -> input -> post('params'));
        //$this -> data['profiles'] = $this -> tenants_model -> getTenants();
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'accounts/addAccount';
                $this->data['title'] = 'Add Account';
                // $this->data['account_types'] = $this->accounts_model->getAccountTypes();
                // $this->data['parents'] = $this->accounts_model->getParents();
                // $this->data['classes'] = $this->accounts_model->getClasses();
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                // }
                $this->data['transactions'] = $this->transactions_model->getTransactions();
                $this->data['singleTransaction'] = $this->transactions_model->getTransaction();
                $this->data['jtransactions'] = json_encode($this->data['singleTransaction']);
                $this->data['getAllTransactions'] = $this->transactions_model->getAllTransactions();
                $this->data['jgetAllTransactions'] = json_encode($this->data['getAllTransactions']);
                $this->data['getAccountsTotal'] = $this->transactions_model->getAccountsTotal();
                $this->data['jgetAccountsTotal'] = json_encode($this->data['getAccountsTotal']);
                break;
            case 'edit' :
                // $this->data['target'] = 'accounts/editAccount/' . $this->input->post('id');
                // $this->data['title'] = 'Edit Account';
                // $this->data['account'] = $this->accounts_model->getAccount($this->input->post('id'));
                // //$said = $this->data['account']->id;
                // $this->data['account_types'] = $this->accounts_model->getAccountTypes();
                // $this->data['parents'] = $this->accounts_model->getParents();
                // $this->data['classes'] = $this->accounts_model->getClasses();
                // $this->data['table'] = $this->accounts_model->getSpecialAccountName($this->data['account']->account_types_id);
                // $this->data['specialAccount'] = $this->accounts_model->getSpecialAccount($this->data['account']->id);
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                // }
                break;
        }
        $this->load->view('forms/transactions/main4', $this->data);
    }

 

//include_once 'checks.php';


    public function checkTransactions()//run everyday maybe crontab? set on linux server or task scheduler on windows if using windows server?
    {
        $today = date('Y-m-d');//date as yyyy-mm-dd
        $this->db->select('mt.id, mt.transaction_type, f.interval_unit, f.number, mt.end_date, mt.next_trans_date, mt.data');
        $this->db->from('memorized_transactions mt');
        $this->db->join('frequencies f', 'mt.frequency = f.id'); 
        $this->db->where('mt.next_trans_date', $today);
        $this->db->where('mt.auto', 1);

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $this->applyTransaction($row);
            }
        }
       

    }

    public function checkManagementFees()//run everyday maybe crontab? set on linux server or task scheduler on windows if using windows server?
    {

        // pull all fees that have to run today
        $today = date('Y-m-d');//date as yyyy-mm-dd
        $nextDate = date( "Y-m-d", strtotime( $today." +".$row->number." ".$row->interval_unit) );
        $this->db->select('f.interval_unit, f.number, management_fees.id, management_fees.item_id, p.default_bank, management_fees.frequency, management_fees.account_id, management_fees.percentage_fixed, management_fees.vendor, management_fees.amount, management_fees.property_id');
        $this->db->from('management_fees');
        $this->db->join('frequencies f', 'management_fees.frequency = f.id'); 
        $this->db->join('properties p', 'management_fees.property_id = p.id');
        $this->db->where('management_fees.start_date', $today);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                
                $nextDate = date( "Y-m-d", strtotime( $today." +".$row->number." ".$row->interval_unit) );
                $headerAcct = $this->site->settings->management_check == 1 ? $row->default_bank : $this->site->settings->accounts_payable;
                
                // if it is based on percentage replace amount with calculation below:
                if($row->percentage_fixed == 1){
                    echo 'percentage';
                    $lastDate = date( "Y-m-d", strtotime( $today." -".$row->number." ".$row->interval_unit) );
                    

                    $this->db->select('sum(credit-debit) as amount');
                    $this->db->from('transactions');
                    $this->db->join('transaction_header', 'transaction_header.id = transactions.trans_id'); 
                    $this->db->where('transaction_header.transaction_date >', $lastDate);
                    $this->db->where('transaction_header.transaction_date <=', $today);
                    $this->db->where('account_id !=', $this->site->settings->accounts_receivable);
                    $this->db->where('property_id', $row->property_id);
                    $this->db->where_in('item_id', explode(',', $row->item_id));

                    $this->db->group_by('property_id');
                    $q2 = $this->db->get();
                        if ($q2->num_rows() > 0) {
                            foreach (($q2->result()) as &$mfee) {
                                echo $mfee->amount.'<br>';
                                if ($mfee->amount > 0 ){
                                    $row->memo = $row->amount.' percent of charged income totaling $'.$mfee->amount.' from '.$lastDate.' to '.$today.'.';
                                    $row->amount = $mfee->amount * ($row->amount/100);
                                   
                                } else{
                                    //remove transaction
                                    continue;
                                };
                            }
                        } else {
                            //remove transaction from array since there isnt any income
                            continue;
                        }

                }

                //compose the transaction
                $_POST = json_decode('{"headerTransaction":{"account_id":"'.$headerAcct.'","credit":"'.$row->amount.'","profile_id":"'.$row->vendor.'","class_id":"", "property_id":"'.$row->property_id.'"},"header":{"transaction_ref":"","transaction_date":"'.$today.'","memo":"'.$row->memo.'","to_print":"1"},"transactions":[{"account_id":"'.$row->account_id.'","property_id":"'.$row->property_id.'","unit_id":"'.$row->unit_id.'","description":"","debit":"'.$row->amount.'","class_id":"","profile_id":"'.$row->vendor.'"}],"radioButton":"normal", "bills":{"terms":"30 days","due_date":"2022\/04\/28","request_approval_from":"20270"}}', true);
                $_POST['header']['transaction_date'] = $today;
                 //enter management

                 $success = $this->site->settings->management_check == 1 ? $this->addCheck(true): $this->addBill(true);


                 if ($success) {
                    //$this->db->query('UPDATE management_fees SET start_date ="'.$nextDate.'" WHERE id =' . $row->id);
                     if($this->site->settings->email_autocharge_notices == 1){
                         //$this->sendEmail($row,'lc');
                     }

                 }else{
                     $subject = 'Notification from server';
                     $body = "Management fee for  failed"; 
                     $this->email_model->emailNotifications($subject, $body);
                 }
                //$transactionData['transactions'][1] = ['account_id' => $this->site->settings->accounts_receivable, 'property_id' => $mPropId, 'description' => '', 'profile_id' => $customer, 'unit_id' => '', 'debit' => $management['amount']];
                //$transactionData['transactions'][2] = ['account_id' => $incomeAcct, 'property_id' => $mPropId, 'unit_id' => $detail['unit_id'], 'description' => '', 'profile_id' => $customer, 'debit' => $management['amount'] * -1];

                // enter the transaction
               //$this->applyTransaction($trans);
            }
        }

        //update Next Transaction date
       

    }

    public function manualTransactions()
    {
        $memorizedTransaction_ids = $this->input->post('transactions');
        $this->db->select('mt.id, mt.transaction_type, f.interval_unit, f.number, mt.end_date, m.next_trans_date, mt.data');
        $this->db->from('memorized_transactions mt');
        $this->db->join('frequencies f', 'mt.frequency = f.id'); 
        $this->db->where_in('mt.id', $memorizedTransaction_ids);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $this->applyTransaction($row);
            }
        }
       

    }


    public function applyTransaction($transaction)
    {   
        // $_POST =  [
        //         'one' =>  [
                   
        //             'profile_id' => 6, 'lease_id' => 7, 'property_id' => 8, 'unit_id' => 94, 'trans_id' => 15
        //          ],
        //         'two' =>  [
        //             ['id' => 1, 'profile_id' => 2, 'property_id' => 4, 'unit_id' => 4, 'trans_id' => 9],
        //             ['id' => 1, 'profile_id' => 2, 'property_id' => 3, 'unit_id' => 4, 'trans_id' => 5]
        //          ],
        //         'three' =>  [
        //             ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
        //             ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
        //             ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5]]
        //         ];
        $_POST = json_decode($transaction->data, TRUE);
        $_POST['header']['transaction_date'] = $transaction->next_trans_date;//date('Y-m-d');
        switch ($transaction->transaction_type) {
            case 1 ://journal
                $this->addJournalEntry();
                break;
            case 2 ://bill
                $this->addBill();
                break;
            case 3://selling
                $this->addCheck();
                break;
            case 4 ://check
                $this->addCheck();
                break;   
            case 5 ://customer payments
                $this->applyReceivedPayments();
                break;
            case 6 ://charge
                $this->newCharge();
                break;   
            case 7://bill payment
                $this->applyBillPayments();
                break;
            case 8 ://deposit
                $this->depositPayments();
                break;   
            case 9 ://credit card
                $this->addCcTransaction();
                break;
            case 10 ://SD Refund
                $this->applyPayments();
                break;   
                   
        }
         //code to update next_trans_date according to interval_type ,interval_amount, end_date, (start_date?)
         //also see about updating within the json in mysql to see if can set transaction_date now, also check if other info in data needs to be changed
        

        //$this->db->query('UPDATE memorized_transactions SET next_trans_date =
         //(DATE_ADD(next_trans_date, INTERVAL ' . $transaction->number .' ' . $transaction->interval_unit. ') WHERE id =' . $transaction->id);
    }



    function addMemorizedTransaction()
    {
        $this->load->model('memorizedTransactions_model');
        $errors = "";
        $specs = $this->input->post('conditions');
        unset($_POST['conditions']);
        $transactionData = $this->input->post();
        foreach($transactionData as $key => &$value){
          foreach($value as &$val){
            if(is_array($val)){
            unset($val['id']);
            }else{unset($value['id']);}
          }
    }
    
       
        //$data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions, 'special' => $special);
        //$validate = $this->validate_model->validate("checks", $data);

        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/$validate['bool'] &&  $this->memorizedTransactions_model->addTransaction($specs, $transactionData))
            echo json_encode(array('type' => 'success', 'message' => 'Check successfully added.'));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function memorizedTransactionsgetModal(){
        $this->load->model('memorizedTransactions_model');
        switch ($this->input->post('mode')) {

            case 'add' :
                $this->data['target'] = 'transactions/manualTransactions';
                $this->data['title'] = 'Memorized Transactions';
                $this->data['jMemorizedTransactions'] = json_encode($this->memorizedTransactions_model->getmemorizedTransactions());
        }
        $this->load->view('forms/memorized_transactions/main', $this->data);
    }
    ////accounts
    function addAccount() 
    {
        // $this->load->model('validations_model');
        // $this->data['validate'] = $this->validations_model->validate("accounts", $this->input->post());
        // if($this->data['validate']){
        //     echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('account')));
        // }
        $errors = "";
        $this->load->model('accounts_model');

        $account = $this->input->post('account');
        $table = $this->input->post('table');
        //$table = $this->accounts_model->getSpecialAccount($account['account_types_id']);
        $specialAccount = $this->input->post('specialAccount');
        $propertyAccounts = $this->input->post('propertyAccounts');
        $data = array('account' => $account, 'table' => $table, 'specialAccount' => $specialAccount);
        $validate = $this->validate_model->validate("account", $data);
        $this->form_validation->set_rules($this->settings->accountFormValidation);
        if ($this->form_validation->run() && $validate['bool'] && $this->accounts_model->addAccount($account, $specialAccount, $table, $propertyAccounts))
        
            echo json_encode(array('type' => 'success', 'message' => 'Account successfully added.'));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('account')));

        }
    }

    function editAccount($aid = 0)
    {
        if($aid == $this->ar || $aid == $this->ap){
            echo json_encode(array('type' => 'danger', 'message' => 'No modification allowed', 'errors' => $this->parse_errors('account')));
            return;
        }
        // $this->load->model('validations_model');
        // $this->data['validate'] = $this->validations_model->validate("accounts", $this->input->post());
        // if(!$this->data['validate']){
        //     echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('account')));
        // }else{
        //     $this->load->model('validations_model');
            $errors = "";
            $this->load->model('accounts_model');
            $account = $this->input->post('account');
            $table = $this->input->post('table');
            $specialAccount = $this->input->post('specialAccount');
            $propertyAccounts = $this->input->post('propertyAccounts');
            $data = array('account' => $account, 'table' => $table, 'specialAccount' => $specialAccount);
            $validate = $this->validate_model->validate("account", $data);
            $this->form_validation->set_rules($this->settings->accountFormValidation);
            if ($this->form_validation->run() && $validate['bool'] && $this->accounts_model->editAccount($account, $specialAccount, $aid,  $table, $propertyAccounts)){
                echo json_encode(array('type' => 'success', 'message' => 'Account successfully updated.'));
            }else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('account')));

        }
    }

    /////////apply refund

    function applyPayments()
    {
        $this->load->model('applyRefundSecurity_model');
        $header = $this->input->post('header');
        $leaseInfo = $this->input->post('leaseInfo');
        $sdApplyAmount = $this->input->post('sdApplyAmount');
        $sdRefundAmount = $this->input->post('sdRefundAmount');
        $lmrApplyAmount = $this->input->post('lmrApplyAmount');
        $lmrRefundAmount = $this->input->post('lmrRefundAmount');
        $checkingAccount = $this->input->post('checkingAccount');
        $applied_payments = $this->input->post('applied_payments');








        $sdApplyAmount = is_numeric($sdApplyAmount) ? $sdApplyAmount : 0;
        $lmrApplyAmount = is_numeric($lmrApplyAmount) ? $lmrApplyAmount : 0;
        
        // if($sdApplyAmount > 0 || $lmrApplyAmount > 0){
        //     //$trans_id = $this->addHeader($header, 10);
        //     $header;
        //     $accounts_receivable = ['account_id' => $this->ar, 'trans_id' => $trans_id, 'credit' => $sdApplyAmount + $lmrApplyAmount, 'debit' => 0] + $leaseInfo;
        //     $security_deposit =  ['account_id' => $this->sd, 'trans_id' => $trans_id, 'debit' => $sdApplyAmount, 'credit' => 0] + $leaseInfo;
        //     $lmr =  ['account_id' => $this->lmr, 'trans_id' => $trans_id, 'debit' => $lmrApplyAmount, 'credit' => 0] + $leaseInfo;
        //     // $this->db->insert('transactions',$accounts_receivable);
        //     // $transaction_id_a = $this->db->insert_id();
        //     //$this->db->insert('transactions',$security_deposit);
        //     //$this->db->insert('transactions',$lmr);

        //     //$this->applyPaymentsAdd($applied_payments, $transaction_id_a);
        // }
        
        
        ////////////////refund  $header,$headerTransaction is $refund, $transactions is $security_deposits?, $special is check

        if($sdRefundAmount > 0 || $lmrRefundAmount > 0){
            $header;
            $refundFrom = ['account_id' => $checkingAccount, 'trans_id' => $trans_id, 'description' =>'SD/LMR Refund', 'credit' => $sdRefundAmount + $lmrRefundAmount] + $leaseInfo;

            $security_deposit =  ['account_id' => $this->sd, 'trans_id' => $trans_id, 'debit' => $sdRefundAmount] + $leaseInfo;
            $lmr =  ['account_id' => $this->lmr, 'trans_id' => $trans_id, 'debit' => $lmrRefundAmount] + $leaseInfo;
            //$this->db->insert('transactions', $refundFrom);
            if($sdRefundAmount > 0)$transactions[] = $security_deposit;
            if($lmrRefundAmount > 0)$transactions[] = $lmr;

            $check = ['paid_to' => $leaseInfo['profile_id'], 'trans_id' => $trans_id];
            $this->db->insert('checks', $check);

            $data = array('header' => $header, 'headerTransaction' => $refundFrom, 'transactions' => $transactions);
            $validate = $this->validate_model->validate("checks", $data);
        }









        
       if ($validate['bool'] && $this->applyRefundSecurity_model->applyPayments($header, $leaseInfo, $sdApplyAmount, $sdRefundAmount, $lmrApplyAmount, $lmrRefundAmount, $checkingAccount, $applied_payments)){
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
        $leaseInfo = $this->input->post('leaseInfo');
        $sdApplyAmount = $this->input->post('sdApplyAmount');
        $sdRefundAmount = $this->input->post('sdRefundAmount');
        $lmrApplyAmount = $this->input->post('lmrApplyAmount');
        $lmrRefundAmount = $this->input->post('lmrRefundAmount');
        $checkingAccount = $this->input->post('checkingAccount');
        $applied_payments = $this->input->post('applied_payments');
       if (/*$validate['bool'] &&*/ $this->applyRefundSecurity_model->editAppliedPayments($header, $leaseInfo, $sdApplyAmount, $sdRefundAmount, $lmrApplyAmount, $lmrRefundAmount, $checkingAccount, $applied_payments)){
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

    ////apply refund
    public function applyRefundgetModal()
    {
        $this->load->model('applyRefundSecurity_model');
        $params = json_decode($this->input->post('params'));

        switch ($this->input->post('mode')) {

                case 'add' :
                $this->data['target'] = 'transactions/applyPayments';
                $this->data['title'] = 'APPLY/REFUND SECURITY/LMR';
                $this->data['tenants'] = $this->applyRefundSecurity_model->getTenants();
                $this->data['banks'] = $this->applyRefundSecurity_model->getBanks();
                $this->data['property_id'] = $params->property;
                $this->data['unit_id'] = $params->unit;
                //$this->data['sdBalance'] = $this->applyRefundSecurity_model->getSdBalance($params->lease);
                //$this->data['lmBalance'] = $this->applyRefundSecurity_model->getLmBalance($params->lease);
                $balances = $this->applyRefundSecurity_model->getBalance($params->lease, $params->profile);
                $lmr = array_values(array_filter($balances, function($v) {
                    return  $v['account_id'] == $this->lmr;
                }));
                $sd = array_values(array_filter($balances, function($v) {
                    return  $v['account_id'] == $this->sd;
                }));
                $this->data['lmBalance'] = $lmr[0]['balance'];
                $this->data['sdBalance'] = $sd[0]['balance'];
                $this->data['arBalance'] = $this->applyRefundSecurity_model->getArBalance($params->lease, $params->profile);
                $this->data['lease_id'] = $params->lease;
                $this->data['leases'] = $this->applyRefundSecurity_model->getLeases();
                $this->data['properties'] = $this->applyRefundSecurity_model->getProperties();
                $this->data['units'] = $this->applyRefundSecurity_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jTransactions'] = json_encode($this->applyRefundSecurity_model->getTransactions($params->lease, $params->profile));
                //$this->data['jTransactions'] = json_encode($this->applyRefundSecurity_model->getTransactions2($params->profile));
                $this->data['header']->profile_id = $params->profile; 
               
                break;
            case 'edit' :
                $this->data['target'] = 'transactions/editAppliedPayments/' . $this->input->post('id');
                $this->data['title'] = 'Edit APPLY/REFUND SECURITY/LMR';
                $this->data['tenants'] = $this->applyRefundSecurity_model->getTenants();
                $this->data['banks'] = $this->applyRefundSecurity_model->getBanks();
                $this->data['property_id'] = $params->property;
                $this->data['unit_id'] = $params->unit;
                
                $balances = $this->applyRefundSecurity_model->getBalanceEdit($this->input->post('id'), $params->lease, $params->profile);
                $lmr = array_values(array_filter($balances, function($v) {
                    return  $v['account_id'] == $this->lmr;
                }));
                $sd = array_values(array_filter($balances, function($v) {
                    return  $v['account_id'] == $this->sd;
                }));
                $this->data['lmBalance'] = $lmr[0]['balance'];
                $this->data['sdBalance'] = $sd[0]['balance'];
                $this->data['arBalance'] = $this->applyRefundSecurity_model->getArBalanceEdit($this->input->post('id'), $params->lease, $params->profile);
                $this->data['lease_id'] = $params->lease;
                $this->data['leases'] = $this->applyRefundSecurity_model->getLeases();
                $this->data['properties'] = $this->applyRefundSecurity_model->getProperties();
                $this->data['units'] = $this->applyRefundSecurity_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jTransactions'] = json_encode($this->applyRefundSecurity_model->getTransactionsEdit($this->input->post('id'), $params->lease, $params->profile));
                //$this->data['jTransactions'] = json_encode($this->applyRefundSecurity_model->getTransactionsEdit2($this->input->post('id'), $params->profile));  
                $this->data['header'] = $this->applyRefundSecurity_model->getHeaderEdit($this->input->post('id')); 

                break;
                } 
                $this->load->view('forms/apply_refund/main', $this->data);       
    }

        function getApplyRefundTransactions(){
            $this->load->model('applyRefundSecurity_model');
            if($this->input->get('id')){
                $transactions = $this->applyRefundSecurity_model->getTransactionsEdit($this->input->get('id'), $this->input->get('lease'),$this->input->get('profile'));
                $arBalance = $this->applyRefundSecurity_model->getArBalanceEdit($this->input->get('id'),$this->input->get('lease'),$this->input->get('profile'));
                $sdBalance = $this->applyRefundSecurity_model->getSdBalanceEdit($this->input->get('id'),$this->input->get('lease'),$this->input->get('profile'));
                $lmrBalance = $this->applyRefundSecurity_model->getLmrBalanceEdit($this->input->get('id'),$this->input->get('lease'),$this->input->get('profile'));
            }else{
                $transactions = $this->applyRefundSecurity_model->getTransactions($this->input->get('lease'),$this->input->get('profile'));
                $arBalance = $this->applyRefundSecurity_model->getArBalance($this->input->get('lease'),$this->input->get('profile'));
                $sdBalance = $this->applyRefundSecurity_model->getSdBalance($this->input->get('lease'),$this->input->get('profile'));
                $lmrBalance = $this->applyRefundSecurity_model->getLmrBalance($this->input->get('lease'),$this->input->get('profile'));
            }

            //echo $transactions;
            echo json_encode(array('transactions' => $transactions,'arBalance' => $arBalance, 'sdBalance' => $sdBalance,'lmrBalance' => $lmrBalance));
        }
    
    
    ////////// bill

    function addBill($memorized = null)
    {   
        $this->load->model('bills_model');
        
        $errors = "";
        $header = $this->input->post('header');
        $headerTransaction = $this->input->post('headerTransaction');
        $transactions = $this->input->post('transactions');
        $special = $this->input->post('bills');
        $type = $this->input->post('radioButton');
        $data = array('header' => $header, 'transactions' => $transactions);
        $validate = $this->validate_model->validate("transactions", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);

        if (/*$this->form_validation->run() &&*/$validate['bool'] && $this->bills_model->addbill($type, $header, $headerTransaction, $transactions, $special))
            echo json_encode(array('type' => 'success', 'message' => 'Bill successfully added.'));
        else {
            $errors = $errors . /*validation_errors() .*/"</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function editBill($tid = 0)
    {
        $this->load->model('bills_model');

        $header = $this->input->post('header');
        $headerTransaction = $this->input->post('headerTransaction');
        $transactions = $this->input->post('transactions');
        $special = $this->input->post('bills');
        $data = array('header' => $header, 'transactions' => $transactions, 'special' => $special, 'headerTransaction' => $headerTransaction);
        //$validate = $this->validate_model->validate("transactions", $data);
        $validate = $this->validate_model->validate("bills", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/$validate['bool'] && $this->bills_model->editBill($header, $headerTransaction, $transactions, $special, $tid))
            echo json_encode(array('type' => 'success', 'message' => 'Bill successfully edited.'));
        else {
            $errors = $errors . /*validation_errors() .*/"</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    //////bills
    function billsgetModal()
    {
        $this->load->model('bills_model');
        $this->load->model('checks_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'transactions/addBill';
                $this->data['title'] = 'Add Bill';
                $this->data['classes'] = $this->bills_model->getClasses();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['properties'] = $this->bills_model->getProperties();
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['accounts'] = $this->bills_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->bills_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['jUnits'] =  json_encode($this->data['units']);
                $this->data['names'] = $this->bills_model->getNames();
                $this->data['jNames'] =  json_encode($this->data['names']);
                $this->data['jTransactions']= json_encode($this->data['transactions']);
                $this->data['jPropertyAccounts'] =  json_encode($this->checks_model->getPropertyAccounts2());


                //return $this->bills_model->getAccounts($pid);
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                break;
            case 'edit' :
                $this->data['target'] = 'transactions/editBill/' . $this->input->post('id');
                $this->data['title'] = 'Edit Bill';
                $this->data['classes'] = $this->bills_model->getClasses();
                $this->data['properties'] = $this->bills_model->getProperties();
                $this->data['accounts'] = $this->bills_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['units'] = $this->bills_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['header'] = $this->bills_model->getHeader($this->input->post('id'));
                $transactions = $this->bills_model->getTransactions($this->input->post('id'));
                //$transactions =  $this->data['allTransactions'];
                $headerTransaction = array_shift($transactions);
                $this->data['transactions'] =   $transactions;    
                $this->data['headerTransaction'] =   $headerTransaction;  
                
                $this->data['bills'] = $this->bills_model->getBill($this->input->post('id'));
                $this->data['names'] = $this->bills_model->getNames();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                //$this->data['accounts'] = $this->bills_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['jUnits'] =  json_encode($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['jNames'] =  json_encode($this->data['names']);
                $this->data['jTransactions']= json_encode($transactions);
                $this->data['jPropertyAccounts'] =  json_encode($this->checks_model->getPropertyAccounts2());

               
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                break;
        }
        $this->load->view('forms/bill/main', $this->data);
    }
     ////charges

     function newCharge()
    {
        $errors = "";
        $this->load->model('charges_model');
        $header = $this->input->post('header');
        $transaction = $this->input->post('transactions');
        $data = array('header' => $header, 'transactions' => $transaction);
        $validate = $this->validate_model->validate("charges", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/$validate['bool'] && $this->charges_model->addCharge($header, $transaction))
            echo json_encode(array('type' => 'success', 'message' => 'Charge successfully added.'));
            else {
                $errors = $errors . validation_errors() ."</br>". $validate['msg'];
                echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
            }
    }

    function editCharge($id)
    {
        $errors = "";
        $this->load->model('charges_model');
        $header = $this->input->post('header');
        $transaction = $this->input->post('transactions');
        $data = array('header' => $header, 'transactions' => $transaction);
        $validate = $this->validate_model->validate("charges", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/$validate['bool'] && $this->charges_model->editCharge($header, $transaction, $id))
            echo json_encode(array('type' => 'success', 'message' => 'Charge successfully edited.'));
            else {
                $errors = $errors . validation_errors() ."</br>". $validate['msg'];
                echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
            }
    }

    public function printInvoice()
    {   
        //$accounts1 = $this->input->get('accounts');
        $leases = $this->input->post('leases');
        $this->load->model('charges_model');
        $data = json_encode($this->charges_model->getLeases($leases));
        //$data = json_encode($this->charges_model->getTenants([2]));
        echo $data;
       

    }

    ///charges
    function chargesgetModal()
    {
        $this->load->model('leases_model');
        $this->load->model('units_model');
        $this->load->model('tenants_model');
        $this->load->model('charges_model');
        $params = json_decode($this -> input -> post('params'));
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'transactions/newCharge';
                $this->data['title'] = 'Add charge';
                $this->data['properties'] = $this->charges_model->getAllProperties();
                $this->data['units'] = $this->units_model->getUnits();
                $this->data['tenants'] = $this->tenants_model->getAllTenants2();
                $this->data['leases'] = $this->charges_model->getAllLeases();
                $this->data['items'] = $this->leases_model->getAllItems();
                $this->data['jleases'] = json_encode($this->data['leases']);
                $this->data['jproperties'] = json_encode($this->data['properties']);
                $this->data['junits'] = json_encode($this->data['units']);
                $this->data['jtenants'] = json_encode($this->data['tenants']);
                $this->data['newChargeInfo'] = $params;

                break;
            case 'edit' :
                $this->load->model('charges_model');
                $this->data['target'] = 'transactions/editCharge/' . $this->input->post('id');
                $this->data['title'] = 'Edit charge';
                $this->data['properties'] = $this->charges_model->getAllProperties();
                $this->data['units'] = $this->units_model->getUnits();
                $this->data['tenants'] = $this->tenants_model->getAllTenants2();
                $this->data['leases'] = $this->charges_model->getAllLeases();
                $this->data['items'] = $this->leases_model->getAllItems();
                $this->data['header'] = $this->charges_model->getHeader($this->input->post('id'));
                $this->data['transaction'] = $this->charges_model->getTransaction($this->input->post('id'));
                $this->data['jtransaction'] = json_encode($this->data['transaction']);
                $this->data['jleases'] = json_encode($this->data['leases']);
                $this->data['jproperties'] = json_encode($this->data['properties']);
                $this->data['junits'] = json_encode($this->data['units']);
                $this->data['jtenants'] = json_encode($this->data['tenants']);
                break;
        }
        $this->load->view('forms/charge/addChargeModel', $this->data);
    }


    ////////checks

    function addCheck($memorized = null)
    {
        $this->load->model('checks_model');

        $errors = "";
        $header = $this->input->post('header');
        $headerTransaction = $this->input->post('headerTransaction');
        $transactions = $this->input->post('transactions');
        $print = $this->input->post('saveAndPrint');
        //$special = $this->input->post('checks');

        $data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions);
        $validate = $this->validate_model->validate("checks", $data);

        if ($validate['bool'] &&  $this->checks_model->addCheck($header, $headerTransaction, $transactions, $print))
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
        //$special = $this->input->post('checks');
        $deletes = $this->input->post('delete');

        $data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions);
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
        //$accounts1 = $this->input->get('accounts1');
        $accounts = $this->input->post('transactions');
        $accounts2 = $this->input->get();
        $accounts3 = $this->input->post();
        $accounts4 = json_decode($accounts3['params'], true);
        $this->load->model('checks_model');
        //$data = json_encode($this->checks_model->onPrintMany($accounts));
        $data = $this->checks_model->onPrintMany($accounts4);
        //$data->type = "";
        echo json_encode($data);

    }

    //////checks
    function checksgetModal()
    {
        $this->load->model('checks_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'transactions/addCheck';
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
                $this->data['units'] = $this->checks_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
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
                $this->load->view('forms/check/main', $this->data);
                break;
            case 'edit' :
                $this->data['target'] = 'transactions/editCheck/' . $this->input->post('id');
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
                $this->load->view('forms/check/main', $this->data);
                break;
                case 'checkToPrint':
                $this->load->model('creditCard_model');
                $this->data['target'] = 'transactions/onPrint' ;
                $this->data['title'] = 'Edit Check';

                $this->data['transactions'] = $this->checks_model->getChecksToPrint();
                $this->data['jtransactions'] =  json_encode($this->data['transactions']);
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
                $this->load->view('forms/check/check_to_print', $this->data);
                break;
        }
        
    }


    /////////credit cards

    function addCcTransaction() 
    {
        $this->load->model('creditCard_model');
        $header = $this->input->post('header');
        $creditCard = $this->input->post('credit_card');
        $transactions = $this->input->post('transactions');
        $charge = $this->input->post('charge');
        $credit = $this->input->post('credit');
        
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
        $ccAccount_id = $this->input->post('ccAccount_id');
        $transactions = $this->input->post('transactions');
        $details = $this->input->post('details');

        //$data = array('details' => $details, 'transactions' => $transactions);
        $data = $this->input->post();
        $validate = $this->validate_model->validate("cc_grid_charge", $data);
    if ($validate['bool']){ 
        $warnings = $this->creditCard_model->recordTransactions($ccAccount_id , $transactions, $details);
        if(empty($warnings->message) && $warnings->status > 0){
            echo json_encode(array('type' => 'success', 'message' => 'All Transactions successfully added.'));
        }
        else{
            echo json_encode(array('type' => 'danger', 'message' => $warnings->message .  '</br>' . $warnings->statusMessage));
        }
    }else {
            $errors = $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors));
    }
      
    }

    ///credit card
    public function creditCardgetModal()
    {
        $this->load->model('ofx_model');
        $this->load->model('creditCard_model');
        $params = json_decode($this->input->post('params'));

        switch ($this->input->post('mode')) {
            case 'record' :
                $this->data['target'] = 'transactions/recordTransactions';
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
                $this->data['target'] = 'transactions/addCcTransaction';
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
                $this->data['jPropertyAccounts'] =  json_encode($this->creditCard_model->getPropertyAccounts2());
                $this->data['header']->account_id = $params->account;
                $this->data['header']->profile_id = $params->profile;
                $this->data['header']->property_id = $params->property;
               
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                $this->load->view('forms/cc/cc_charge2', $this->data);
                break;
            case 'edit' :
                $this->data['header'] = $this->creditCard_model->getHeaderEdit($this->input->post('id'));
                $this->data['transactions'] = $this->creditCard_model->getTransactions($this->input->post('id'));
                $this->data['accountName'] = $this->creditCard_model->getAccountName($this->data['transactions'][0]->account_id);
                $this->data['jHeader'] = json_encode($this->creditCard_model->getHeaderEdit($this->input->post('id')));
                $this->data['jTransactions'] = json_encode($this->creditCard_model->getTransactions($this->input->post('id')));
                $this->data['target'] = 'transactions/editTransaction/' . $this->input->post('id');
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
                $this->data['jPropertyAccounts'] =  json_encode($this->creditCard_model->getPropertyAccounts2());
               
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                $this->load->view('forms/cc/cc_charge2', $this->data);
                break;
                }
                //break;

            }

    /////// delete

    function deleteTransaction()
    {
        $this->load->model('delete_model');
        $message = json_encode($this->delete_model->checkTransaction($th_id, $delete = NULL));
        echo $message;
    }

    function deleteAccount()
    {
        $this->load->model('delete_model');
        $message = json_encode($this->delete_model->checkAccount($acc_id, $delete = NULL));
        echo $message;
    }

    function deleteName()
    {
        $this->load->model('delete_model');
        $message = json_encode($this->delete_model->checkName($profile_id, $delete = NULL));
        echo $message;
    }

    ////////deposits

    function depositPayments() 
    {
        $this->load->model('deposits_model');
        $account_id = $this->input->post('account_id');
        $property_id = $this->input->post('property_id');
        $header = $this->input->post('header');
        // if($header['date']){
        //     $header['date'] = sqlDate($header['date']);
        // }
        $amount = $this->input->post('amount');
        $totalAmount = $this->input->post('totalAmount');
        $undeposited = $this->input->post('undeposited');
        $otherDeposits = $this->input->post('transactions');
        
        $data = $this->input->post();
        $validate = $this->validate_model->validate("deposits", $data);
        
        if ($validate['bool'] && $this->deposits_model->depositPayments($account_id, $property_id,  $header, $amount, $totalAmount, $undeposited, $otherDeposits)){
            echo json_encode(array('type' => 'success', 'message' => 'Payments successfully deposited.'));
        }else {
            $errors =  $validate['msg'];
          echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $errors));
    }
}

    function editDeposits($aid = 0)
    {
        $this->load->model('deposits_model');
        $account_id = $this->input->post('account_id');
        $header = $this->input->post('header');
        $property_id = $this->input->post('property_id');
        // if($header['date']){
        //     $header['date'] = sqlDate($header['date']);
        // }
        $amount = $this->input->post('amount');
        $totalAmount = $this->input->post('totalAmount');
        $undeposited = $this->input->post('undeposited');
        $otherDeposits = $this->input->post('transactions');
        $deletes = $this->input->post('delete');

        
        if ($this->deposits_model->editDeposits($account_id, $property_id, $header, $amount, $totalAmount, $undeposited, $otherDeposits, $deletes)){
            echo json_encode(array('type' => 'success', 'message' => 'Deposits successfully updated.'));
        }
    }

    public function depositsgetModal()
    {
        $this->load->model('deposits_model');
        $this->load->model('creditCard_model');
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'transactions/depositPayments';
                $this->data['title'] = 'Deposits';
                $this->data['undepositedChecks'] = $this->deposits_model->getUndepositedFunds(null);
                $this->data['properties'] = $this->deposits_model->getProperties();
                $this->data['names'] = $this->deposits_model->getNames();
                $this->data['banks'] = $this->deposits_model->getDepositTo();
                $this->data['classes'] = $this->deposits_model->getClasses();
                $this->data['accounts'] = $this->deposits_model->getAllAccounts();
                $this->data['jPropertyAccounts'] =  json_encode($this->creditCard_model->getPropertyAccounts2());


                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);//not used anymore, using jsubaccounts
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->deposits_model->getUnits();
                $this->data['jUnits'] =  json_encode($this->data['units']);//not used anymore, using jsubunits
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['jNames'] =  json_encode($this->data['names']);


               
          
                break;
            case 'edit' ://which values are we passing into the edit 
                $this->data['target'] = 'transactions/editDeposits/' . $this->input->post('id');
                $this->data['title'] = 'Edit Deposits';
                $this->data['header'] = $this->deposits_model->getHeaderEdit($this->input->post('id'));
                $this->data['account_id'] = $this->deposits_model->getAccount($this->input->post('id'));
                $this->data['undepositedChecks'] = $this->deposits_model->getDepositedEdit($this->input->post('id'));
                $this->data['transactions'] = $this->deposits_model->getOtherDepositsEdit($this->input->post('id'));
                $this->data['property_id'] = $this->deposits_model->getProperty($this->input->post('id'));
                $this->data['properties'] = $this->deposits_model->getProperties();
                $this->data['names'] = $this->deposits_model->getNames();
                $this->data['accounts'] = $this->deposits_model->getAllAccounts();
                $this->data['banks'] = $this->deposits_model->getDepositTo();
                $this->data['classes'] = $this->deposits_model->getClasses();
                $this->data['jPropertyAccounts'] =  json_encode($this->creditCard_model->getPropertyAccounts2());

                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);//not used anymore, using jsubaccounts
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->deposits_model->getUnits();
                $this->data['jUnits'] =  json_encode($this->data['units']);//not used anymore, using jsubunits
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['jNames'] =  json_encode($this->data['names']);

                break;
        }

        $this->load->view('forms/deposit/main', $this->data);
        
    }

    ////////journal entry

    function addJournalEntry()
    {
        $errors = "";
        $this->load->model('journalEntry_model');

        $header = $this->input->post('header');
        $transactions = $this->input->post('transactions');
        //$validate = $this->validate_model->calcTotal($transactions);
        $data = array('header' => $header, 'transactions' => $transactions);
        $validate = $this->validate_model->validate("journalEntry", $data);
        //if(!$validate){$errors = "Totals don't match </br>";}
        $this->form_validation->set_rules($this->settings->journalEntryFormValidation);
        if ($this->form_validation->run() && $validate['bool'] && $this->journalEntry_model->addJournalEntry($header, $transactions))
            echo json_encode(array('type' => 'success', 'message' => 'Transaction successfully added.'));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function editJournalEntry($tid = 0)
    {
        $errors = "";
        $this->load->model('journalEntry_model');

        $header = $this->input->post('header');
        $transactions = $this->input->post('transactions');
        // $validate = $this->validate_model->calcTotal($transactions);
        // if($validate == false){$errors = "Totals don't match";}
        $data = array('header' => $header, 'transactions' => $transactions);
        $validate = $this->validate_model->validate("journalEntry", $data);
        $this->form_validation->set_rules($this->settings->journalEntryFormValidation);
        if ($this->form_validation->run()&& $validate['bool'] && $this->journalEntry_model->editJournalEntry($header, $transactions, $tid))
            echo json_encode(array('type' => 'success', 'message' => 'Transaction successfully edited.'));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }


    function journalEntrygetModal()
    {
        $this->load->model('journalEntry_model');
        $this->load->model('checks_model');

        //$params = json_decode($this -> input -> post('params'));
        //$this -> data['profiles'] = $this -> tenants_model -> getTenants();
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'transactions/addJournalEntry';
                $this->data['title'] = 'Add Journal Entry';
                $this->data['classes'] = $this->journalEntry_model->getClasses();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['properties'] = $this->journalEntry_model->getProperties();
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['accounts'] = $this->journalEntry_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);//not used anymore, using jsubaccounts
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->journalEntry_model->getUnits();
                $this->data['jUnits'] =  json_encode($this->data['units']);//not used anymore, using jsubunits
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['names'] = $this->journalEntry_model->getNames();
                $this->data['jNames'] =  json_encode($this->data['names']);
                $this->data['jPropertyAccounts'] =  json_encode($this->checks_model->getPropertyAccounts2());
              $trans =  $this->data['jTransactions'] =  json_encode($this->data['transactions']);

                //return $this->journalEntry_model->getAccounts($pid);
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                break;
            case 'edit' :
                $this->data['target'] = 'transactions/editJournalEntry/' . $this->input->post('id');
                $this->data['title'] = 'Edit Journal Entry';
                $this->data['classes'] = $this->journalEntry_model->getClasses();
                $this->data['properties'] = $this->journalEntry_model->getProperties();
                $this->data['accounts'] = $this->journalEntry_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->journalEntry_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['header'] = $this->journalEntry_model->getHeader($this->input->post('id'));
                $this->data['transactions'] = $this->journalEntry_model->getTransactions($this->input->post('id'));
                $this->data['names'] = $this->journalEntry_model->getNames();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                $this->data['jUnits'] =  json_encode($this->data['units']);
                $this->data['jNames'] =  json_encode($this->data['names']);
                $trans = $this->data['jTransactions'] =  json_encode($this->data['transactions']);
                $this->data['jPropertyAccounts'] =  json_encode($this->checks_model->getPropertyAccounts2());

                $test = $this->journalEntry_model->getTransactions($this->input->post('id'));

              

               
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                break;
        }
        $this->load->view('forms/journalEntry/main', $this->data);
    }
    

    
        //paybills

        function applyBillPayments() 
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

    function editBillPayments($aid = 0)
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

    public function payBillsgetModal()
    {
        $this->load->model('payBills_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'transactions/applyBillPayments';
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
                $this->data['target'] = 'transactions/editBillPayments/' . $this->input->post('id');
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
        //////receive payment

        function applyReceivedPayments() 
    {
        $this->load->model('receivePayments_model');
        $profile_id = $this->input->post('profile_id');
        $lease_id = $this->input->post('lease_id');
        $property_id = $this->input->post('property_id');
        $unit_id = $this->input->post('unit_id');
        $amount = $this->input->post('amount');
        $header = $this->input->post('header');
        $customerPayments = $this->input->post('customer_payments');
        $applied_payments = $this->input->post('applied_payments');
        
        
        
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        $validate = $this->validate_model->validate("receivePayments", $header);
        if ($validate['bool'] && /*$this->form_validation->run() &&*/ $this->receivePayments_model->applyPayments($profile_id, $lease_id, $property_id, $unit_id,  $amount, $header, $customerPayments, $applied_payments)){
            echo json_encode(array('type' => 'success', 'message' => 'Payment successfully applied.'));
        }else {
            $errors = $validate['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors));
             //echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('account')));
         }
    }

    function editReceivedPayments($aid = 0)
    {
        $this->load->model('receivePayments_model');
        $profile_id = $this->input->post('profile_id');
        $lease_id = $this->input->post('lease_id');
        $property_id = $this->input->post('property_id');
        $unit_id = $this->input->post('unit_id');
        $amount = $this->input->post('amount');
        $header = $this->input->post('header');
        $customerPayments = $this->input->post('customer_payments');
        $applied_payments = $this->input->post('applied_payments');
        
        $validate = $this->validate_model->validate("receivePayments", $header);
        if ($validate['bool'] &&  $this->receivePayments_model->editAppliedPayments($profile_id, $lease_id, $property_id, $unit_id,  $amount, $header, $customerPayments, $applied_payments)){
            echo json_encode(array('type' => 'success', 'message' => 'Applied payment successfully updated.'));
        }else {
            $errors = $validate['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors));
             //echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('account')));
         }
    }

    public function receivePaymentsgetModal()// not complete see my tasks in asana
    {
        $this->load->model('receivePayments_model');
        $params = json_decode($this->input->post('params'));

        switch ($this->input->post('mode')) {
            case 'add' :
                $idd = $this->input->post('id');
                $this->data['target'] = 'transactions/applyReceivedPayments';
                $this->data['title'] = 'Edit Received Payment';
                $this->data['leases'] = $this->receivePayments_model->getLeases();
                $this->data['tenants'] = $this->receivePayments_model->getTenants();
                $this->data['transaction_types'] = $this->receivePayments_model->getTransactionType();
                $this->data['accounts'] = $this->receivePayments_model->getDepositTo();
                $this->data['properties'] = $this->receivePayments_model->getProperties($params->profile);
                $this->data['profile'] = $this->receivePayments_model->getProfile($params->profile);
                $this->data['profileI'] = $params->profile;
                $this->data['lease'] = $params->lease;
                $this->data['lease_id'] = $params->lease;
                $this->data['header']->deposit_to = $this->uf;
                
                //$this->data['transactions'] = $this->receivePayments_model->getTransactions($this->input->post('id'));//($this->input->post('id'));
                
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                // }
                break;
            case 'edit' ://which values are we passing into the edit 
                $this->data['target'] = 'transactions/editReceivedPayments/' . $this->input->post('id');
                $this->data['title'] = 'Edit Received Payment';
                $this->data['leases'] = $this->receivePayments_model->getLeases();
                $this->data['tenants'] = $this->receivePayments_model->getTenants();
                $this->data['transaction_types'] = $this->receivePayments_model->getTransactionType();
                $this->data['accounts'] = $this->receivePayments_model->getDepositTo();
                $this->data['header'] = $this->receivePayments_model->getHeaderEdit($this->input->post('id'));
                $this->data['transactions'] = $this->receivePayments_model->getTransactionsEdit($this->input->post('id'),$params->lease, $params->profile);
                $this->data['profile'] = $this->receivePayments_model->getProfile($params->profile);
                $this->data['lease'] = $params->lease;
                $this->data['lease_id'] = $params->lease;
                //$this->data['profileI'] = $params->profile;
                //$profile_id = $params->profile ? $params->profile : $this->receivePayments_model->getProfileId($this->input->post('id'));
                $this->data['properties'] = $this->receivePayments_model->getProperties($profile_id);
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                // }
                break;
        }
        //$this->load->view('forms/receive_payment', $this->data);
        $this->load->view('forms/receive_payment/main', $this->data);
        
    }


/////utilities
    function recordUtilities() 
    {
        $this->load->model('utilities_model');
        //$post = $this->input->post();
        $property_id = $this->input->post('property');
        $rows = $this->input->post('row');
        


    //     //$this->form_validation->set_rules($this->settings->accountFormValidation);
    //     /*if ($this->form_validation->run() &&*/
    //     $date = $this->input->post('pay_bill_date');
    //     if($date){
    //     $date = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
    //     }
    //    //$data = array('header' => $header, 'transactions' => $transactions);
        $validate = $this->validate_model->validate("utilities", $rows); 
       if ($validate['bool']){
            $warnings = $this->utilities_model->recordUtilityBill($rows);
            if(empty($warnings->noBank) && empty($warnings->negative) && $warnings->status > 0){
                echo json_encode(array('type' => 'success', 'message' => 'All Transactions successfully added.'));
            }
            else{
                echo json_encode(array('type' => 'danger', 'message' => $warnings->noBank . '</br>' . $warnings->negative . '</br>' . $warnings->statusMessage));
            }
       }
        else {
            $errors = $validate['msg'];
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            //echo json_encode(array('type' => 'danger', 'message' => $errors));
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
      
    }

        ////utilities grid
    public function utilitiesGridgetModal()
    {
        $this->load->model('creditCard_model');
        $params = json_decode($this->input->post('params'));

        switch ($this->input->post('mode')) {

                case 'add' :
                $this->data['target'] = 'transactions/recordUtilities';
                $this->data['title'] = 'Utilities Form';
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
                break;
            case 'edit' :
                $this->data['target'] = 'transactions/utilitiesForm/' . $this->input->post('id');
                $this->data['title'] = 'Utilities Form';
                break;
                } 
                $this->load->view('forms/utilities/main', $this->data);       
    }


    }









