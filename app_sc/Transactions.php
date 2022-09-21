<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->ar = $this->site->settings->accounts_receivable;
        $this->ap = $this->site->settings->accounts_payable;
        $this->sd = $this->site->settings->security_deposits;
        $this->uf = $this->site->settings->undeposited_funds;
        $this->lmr = $this->site->settings->lmr;
        //$this->nsf = $this->site->settings->default_nsf;
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

    // function addTransaction()
    // {
    //     $this->load->model('transactions_model');

    //     $header = $this->input->post('header');
    //     $transactions = $this->input->post('transactions');

    //     //$this->form_validation->set_rules($this->settings->accountFormValidation);
    //     if (/*$this->form_validation->run() &&*/
    //     $this->transactions_model->addTransaction($header, $transactions))
    //         echo json_encode(array('type' => 'success', 'message' => 'Transaction successfully added.'));
    //     /*else {
    //         echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('transaction')));
    //     }*/
    // }

    // function getModal()
    // {
    //     $this->load->model('accounts_model');
    //     $this->load->model('transactions_model');

    //     //$params = json_decode($this -> input -> post('params'));
    //     //$this -> data['profiles'] = $this -> tenants_model -> getTenants();
    //     switch ($this->input->post('mode')) {
    //         case 'add' :
    //             $this->data['target'] = 'accounts/addAccount';
    //             $this->data['title'] = 'Add Account';
    //             // $this->data['account_types'] = $this->accounts_model->getAccountTypes();
    //             // $this->data['parents'] = $this->accounts_model->getParents();
    //             // $this->data['classes'] = $this->accounts_model->getClasses();
    //             // if (isset($params->es_key)) {
    //             //     $key = explode('.', $params->es_key)[1];
    //             //     $this->data['account'] = new stdClass();
    //             //     $this->data['account']->$key = $params->es_value;
    //             // }
    //             $this->data['transactions'] = $this->transactions_model->getTransactions();
    //             $this->data['singleTransaction'] = $this->transactions_model->getTransaction();
    //             $this->data['jtransactions'] = json_encode($this->data['singleTransaction']);
    //             $this->data['getAllTransactions'] = $this->transactions_model->getAllTransactions();
    //             $this->data['jgetAllTransactions'] = json_encode($this->data['getAllTransactions']);
    //             $this->data['getAccountsTotal'] = $this->transactions_model->getAccountsTotal();
    //             $this->data['jgetAccountsTotal'] = json_encode($this->data['getAccountsTotal']);
    //             break;
    //         case 'edit' :
    //             // $this->data['target'] = 'accounts/editAccount/' . $this->input->post('id');
    //             // $this->data['title'] = 'Edit Account';
    //             // $this->data['account'] = $this->accounts_model->getAccount($this->input->post('id'));
    //             // //$said = $this->data['account']->id;
    //             // $this->data['account_types'] = $this->accounts_model->getAccountTypes();
    //             // $this->data['parents'] = $this->accounts_model->getParents();
    //             // $this->data['classes'] = $this->accounts_model->getClasses();
    //             // $this->data['table'] = $this->accounts_model->getSpecialAccountName($this->data['account']->account_types_id);
    //             // $this->data['specialAccount'] = $this->accounts_model->getSpecialAccount($this->data['account']->id);
    //             // if (isset($params->es_key)) {
    //             //     $key = explode('.', $params->es_key)[1];
    //             //     $this->data['account'] = new stdClass();
    //             //     $this->data['account']->$key = $params->es_value;
    //             // }
    //             break;
    //     }
    //     $this->load->view('forms/transactions/main4', $this->data);
    // }

//include_once 'checks.php';

    function addMemorizedTransaction()
    {
        $this->load->model('memorizedTransactions_model');
        $errors = "";
        $specs = $this->input->post('brain');
        if (!$specs['auto']) {
            $specs['auto'] = 0;
        }
        unset($_POST['brain']);
        $transactionData = $this->input->post();
        $property_id = $transactionData['headerTransaction']['property_id'] ? $transactionData['headerTransaction']['property_id'] : ($transactionData['transactions']['property_id'] ? $transactionData['transactions']['property_id'] : $transactionData['transactions'][0]['property_id']);
        $specs['property_id'] = $property_id;
        $transaction_type = $specs['transaction_type'];

        $mode = "add"; 
        
        if(isset($transactionData['header']['id'])){
            $mode = "edit";
        }

        switch ($transaction_type) {
            case '1' :
            if($mode == 'add'){
                $validate = $this->addJournalEntry(1);
              } else {
                $validate = $this->editJournalEntry($transactionData['header']['id'],1);
              }
              break;
            case '2' :
                $validate = $this->addBill(2);
                $specs['type_id'] = 6;
                $specs['type_item_id'] = $transactionData['headerTransaction']['profile_id'];
                $specs['amount'] = $transactionData['headerTransaction']['credit'];
                break;
            case '4' :
                $validate = $this->addCheck(3);
            case '6' :
                $validate = $this->newCharge(6);
            case '9' :
                $validate = $this->addCcTransaction(9);
        }
        foreach ($transactionData as $key => &$value) {
            foreach ($value as &$val) {
                if (is_array($val)) {
                    unset($val['id']);
                } else {
                    unset($value['id']);
                }
            }
        }

        //$data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions, 'special' => $special);
        //$validate = $this->validate_model->validate("checks", $data);

        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if ($validate['bool'] && $this->memorizedTransactions_model->addTransaction($specs, $transactionData))
            echo json_encode(array('type' => 'success', 'message' => 'Memorized transaction successfully added.'));
        else {
            $errors = $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => 'Sorry Memorized transaction can\'t be added ' . $errors));
        }
    }

    public function checkTransactions()//run everyday maybe crontab? set on linux server or task scheduler on windows if using windows server?
    {
        $today = date('Y-m-d');//date as yyyy-mm-dd
        $advance = '+'.$this->site->settings->memorized_transaction_entry." days";
        $entryDate = date('Y-m-d', strtotime($today. $advance));
        $this->db->select('mt.id, mt.transaction_type, f.interval_unit, f.number, mt.end_date, mt.next_trans_date, mt.data');
        $this->db->from('memorized_transactions mt');
        $this->db->join('frequencies f', 'mt.frequency = f.id');
        $this->db->join('leases l', 'l.id = mt.type_item_id','left');
        //$this->db->join('leases l', 'l.id = REPLACE(JSON_EXTRACT(mt.data, "$.transactions.lease_id"),'"','')','left');
        $this->db->where('mt.next_trans_date', $entryDate);
        $this->db->where('(mt.end_date is null or `mt`.`end_date` ="0000-00-00" or mt.end_date >= next_trans_date)');
        $this->db->where('l.move_out', null);
        $this->db->where('mt.auto', 1);

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $this->applyTransaction($row);
                
                
            }
        }
    }

    public function checkLateCharges()//run everyday maybe crontab? set on linux server or task scheduler on windows if using windows server?
    {
        $today = date('Y-m-d');
        $dayOfMonth = date('d');
        $ar = $this->site->settings->accounts_receivable;
        $lc_item = $this->site->settings->Default_LC_item;


        //pull list of rules that should run this date
        $rulesQuery = $this->db->get_where('late_charge_rules', array('day' => $dayOfMonth));

        if ($rulesQuery->num_rows() > 0) {
            $itemAccount = $this->db->get_where('items', array('id' => $lc_item));
            $itemAccount3;
            //$itemAccount2 = $this->db->limit(1)->get_where('items', array('id' => $lc_item)->row();
            if ($itemAccount->num_rows() > 0) {
                foreach (($itemAccount->result()) as &$row) {
                    $itemAccount3 = $row->acct_income;
    
                }
    
            }
            foreach (($rulesQuery->result()) as &$row) {

                $all_types = $row->all_types;
                $ctype = $row->type;
                $amount = $row->amount;
                $lc = $row->late_charge_setup_id;
                $typelist;
                if($all_types <1){
                    
                    $this->db->select('GROUP_CONCAT(late_charge_type_id) AS type_ids');
                    $this->db->from('late_charge_types');
                    $this->db->where('late_charge_rule_id', $row->id);
                    $typesQuery = $this->db->get();
                    if ($typesQuery->num_rows() > 0) {
                        foreach (($typesQuery->result()) as &$row) {
                            $typelist = $row->type_ids;

                        }
                        
                    }
                }
                
                
                $this->db->select('sum(debit - credit) as totalbalance, u.id as unit_id, p.id as property_id, lease_id,  max(IF(`l`.`late_charge_setup_id` is null, IF(`U`.`late_charge` is null, IF(`p`.`late_charge_setup_id` is null,'. $this->site->settings->Default_LC_setup.', `p`.`late_charge_setup_id`), `U`.`late_charge`), `l`.`late_charge_setup_id`)) as lc');
                $this->db->from('leases l');
                $this->db->join('units u', 'u.id = l.unit_id');
                $this->db->join('properties p', 'p.id = u.property_id');
                $this->db->join('transactions t', 'l.id = t.lease_id');
                $this->db->join('transaction_header th', 'th.id = t.trans_id');
                $this->db->where('transaction_date <=', $today);
                $this->db->where('account_id ', $ar);
                if($all_types <1 && $typelist <> null){$this->db->where('(item_id in('.$typelist.') or item_id is null)');}
                if($all_types <1 && $typelist == null){$this->db->where('item_id is null');}
                $this->db->where('IF(`l`.`late_charge_setup_id` is null, IF(`U`.`late_charge` is null, IF(`p`.`late_charge_setup_id` is null,'. $this->site->settings->Default_LC_setup.', `p`.`late_charge_setup_id`), `U`.`late_charge`), `l`.`late_charge_setup_id`)=',$lc );
                $this->db->group_by('lease_id'); 

                $this->db->having('sum(debit - credit) >', 0);

                $q = $this->db->get();
                if ($q->num_rows() > 0) {
                    foreach (($q->result()) as &$row) {
                        $lateCharge = $ctype ==1 ? $amount : $row->totalbalance * ($amount/100);

                            //enter Late charge
                            $lateCharge = $ctype ==1 ? $amount : $row->totalbalance * ($amount/100);
                            $_POST = json_decode('{"header":{"transaction_date":"2018\/11\/01"},"transactions":{"account_id":"'.$itemAccount3.'","property_id":"'.$row->property_id.'","unit_id":"'.$row->unit_id.'","lease_id":"'.$row->lease_id.'","item_id":"'. $this->site->settings->Default_LC_item.'","credit":"'.$lateCharge.'","description":"late charge"}}', true);
                            $_POST['header']['transaction_date'] = $today;//date('Y-m-d');
                            $success = $this->newCharge();

                            if ($success) {
                                
                                if($this->site->settings->email_autocharge_notices == 1){
                                    //$this->sendEmail($row,'lc');
                                }

                            }else{
                                $subject = 'Notification from server';
                                $body = "Late fee for  $row->lease_id failed"; 
                                $this->email_model->emailNotifications($subject, $body);
                            }



                    }
                    
                }


            }
        }
       
       
    }

    public function manualTransactions()
    {
        $memorizedTransaction_ids = $this->input->post('transactions');
        $this->db->select('mt.id, mt.name, mt.transaction_type, f.interval_unit, f.number, mt.end_date, mt.next_trans_date, mt.data');
        $this->db->from('memorized_transactions mt');
        $this->db->join('frequencies f', 'mt.frequency = f.id');
        $this->db->where_in('mt.id', $memorizedTransaction_ids);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $messages = [];

            foreach (($q->result()) as &$row) {
                ob_start();
                $this->applyTransaction($row);
                $output = ob_get_contents();
                ob_end_clean();
                $output = json_decode($output);
                $output->msgInfo = $row->id;
                $messages[] = $output;
            }
            echo json_encode($messages);
        }
    }

    public function deleteMemorizedTransactions()
    {
        $memorizedTransaction_ids = $this->input->post('transactions');
        if (!empty($memorizedTransaction_ids)) {
            $this->db->where_in('id', $memorizedTransaction_ids);
            $result = $this->db->delete('memorized_transactions');
            if ($result) {
                echo json_encode(array('type' => 'success', 'message' => 'Transactions succefully deleted!'));
            } else {
                echo json_encode(array('type' => 'danger', 'message' => 'Transactions Could not be deleted!'));
            }
        }
    }

    public function applyTransaction($transaction)
    {
        $this->load->model('email_model');
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

        $_POST = json_decode($transaction->data, true);
        $_POST['header']['transaction_date'] = $transaction->next_trans_date;//date('Y-m-d');
        switch ($transaction->transaction_type) {
            case 1 ://journal
                $success = $this->addJournalEntry();
                break;
            case 2 ://bill
                unset($_POST['header']['transaction_ref']);
                $success = $this->addBill();
                break;
            case 3://selling
                $success = $this->addCheck();
                break;
            case 4 ://check
                $success = $this->addCheck();
                break;
            case 5 ://customer payments
                $success = $this->applyReceivedPayments();
                break;
            case 6 ://charge
                $success = $this->newCharge();
                break;
            case 7://bill payment
                $success = $this->applyBillPayments();
                break;
            case 8 ://deposit
                $success = $this->depositPayments();
                break;
            case 9 ://credit card
                $success = $this->addCcTransaction();
                break;
            case 10 ://SD Refund
                $success = $this->applyPayments();
                break;
        }
        //code to update next_trans_date according to interval_type ,interval_amount, end_date, (start_date?)
        //also see about updating within the json in mysql to see if can set transaction_date now, also check if other info in data needs to be changed

        if ($success) {
            $this->db->query('UPDATE memorized_transactions SET next_trans_date =
                DATE_ADD(next_trans_date, INTERVAL ' . $transaction->number . ' ' . $transaction->interval_unit . ') WHERE id =' . $transaction->id);
            //$subject = 'Notification from server';
            //$body = "Memorized transaction $transaction->name with id $transaction->id was processed"; 
            //$this->email_model->emailNotifications($subject, $body);
        }else{
            $subject = 'Notification from server';
            $body = "Memorized transaction $transaction->name with id $transaction->id failed"; 
            $this->email_model->emailNotifications($subject, $body);
        }
    }



    // function addMemorizedTransaction()
    // {
    //     $this->load->model('memorizedTransactions_model');
    //     $errors = "";
    //     $specs = $this->input->post('conditions');
    //     unset($_POST['conditions']);
    //     $transactionData = $this->input->post();
    //     foreach($transactionData as $key => &$value){
    //       foreach($value as &$val){
    //         if(is_array($val)){
    //         unset($val['id']);
    //         }else{unset($value['id']);}
    //       }
    // }

    //     //$data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions, 'special' => $special);
    //     //$validate = $this->validate_model->validate("checks", $data);

    //     //$this->form_validation->set_rules($this->settings->accountFormValidation);
    //     if (/*$this->form_validation->run() &&*/$validate['bool'] &&  $this->memorizedTransactions_model->addTransaction($specs, $transactionData))
    //         echo json_encode(array('type' => 'success', 'message' => 'Check successfully added.'));
    //     else {
    //         $errors = $errors . validation_errors() ."</br>". $validate['msg'];
    //         echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
    //     }
    // }

    function memorizedTransactionsgetModal()
    {
        $this->load->model('memorizedTransactions_model');
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'transactions/manualTransactions';
                $this->data['title'] = 'Memorized Transactions';
                $this->data['jMemorizedTransactions'] = json_encode($this->memorizedTransactions_model->getmemorizedTransactions());
        }
        $this->load->view('forms/memorized_transactions/main', $this->data);
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
        $refund = $this->input->post('refund');

        $sdApplyAmount = is_numeric($sdApplyAmount) ? $sdApplyAmount : 0;
        $lmrApplyAmount = is_numeric($lmrApplyAmount) ? $lmrApplyAmount : 0;
        $sdRefundAmount = is_numeric($sdRefundAmount) ? $sdRefundAmount : 0;
        $lmrRefundAmount = is_numeric($lmrRefundAmount) ? $lmrRefundAmount : 0;

        if (($sdApplyAmount > 0) || ($lmrApplyAmount > 0)) {
            $accounts_receivable = ['account_id' => $this->ar, 'credit' => $sdApplyAmount + $lmrApplyAmount] + $leaseInfo;
            $security_deposit = ['account_id' => $this->sd, 'debit' => $sdApplyAmount] + $leaseInfo;
            $lmr = ['account_id' => $this->lmr, 'debit' => $lmrApplyAmount] + $leaseInfo;
            $applyTransactions[] = $security_deposit;
            $applyTransactions[] = $lmr;
            $data = array('applyTotal' => $sdApplyAmount + $lmrApplyAmount, 'applied_payments' => $applied_payments);
            $validate1 = $this->validate_model->validate("applyRefunds", $data);
            //$this->applyPaymentsAdd($applied_payments, $transaction_id_a);
        }

        ////////////////refund  $header,$headerTransaction is $refund, $transactions is $security_deposits?, $special is check

        if (($sdRefundAmount > 0) || ($lmrRefundAmount > 0)) {
            $refundFrom = ['account_id' => $checkingAccount, 'description' => 'SD/LMR Refund', 'credit' => $sdRefundAmount + $lmrRefundAmount, 'debit' => 0, 'line_number' => 0] + $leaseInfo;
            $security_deposit = ['account_id' => $this->sd, 'description' => 'SD/LMR Refund', 'credit' => 0, 'debit' => $sdRefundAmount, 'line_number' => 1] + $leaseInfo;
            $lmr = ['account_id' => $this->lmr, 'description' => 'SD/LMR Refund', 'credit' => 0, 'debit' => $lmrRefundAmount, 'line_number' => 1] + $leaseInfo;

            if ($sdRefundAmount > 0) $refundTransactions[] = $security_deposit;
            if ($lmrRefundAmount > 0) $refundTransactions[] = $lmr;
            $refundTransactions[] = $refundFrom;

            $check = ['paid_to' => $leaseInfo['profile_id']];
            $data['checking account'] = $checkingAccount;
            //$data = array('header' => $header, 'headerTransaction' => $refundFrom, 'transactions' => $refundTransactions);
            $validate2 = $this->validate_model->validate("applyRefunds", $data);
        }

        $data = array('totalSd' => $sdApplyAmount + $sdRefundAmount, 'totalLmr' => $lmrApplyAmount + $lmrRefundAmount, 'leaseInfo' => $leaseInfo, 'total' => ['Totals' => $sdApplyAmount + $sdRefundAmount + $lmrApplyAmount + $lmrRefundAmount], 'header' => $header, 'mode' => 'add');
        $validate3 = $this->validate_model->validate("applyRefunds", $data);

        if (($validate1 ? $validate1['bool'] : 1) && ($validate2 ? $validate2['bool'] : 1) && ($validate3 ? $validate3['bool'] : 1) && $validate3['auth'] =='pass'){
            $th_id = $this->applyRefundSecurity_model->applyPayments($header, $accounts_receivable, $applyTransactions,  $refundTransactions, $leaseInfo, $sdApplyAmount, $sdRefundAmount, $lmrApplyAmount, $lmrRefundAmount, $checkingAccount, $applied_payments, $check);
            if($th_id){
                if($refund['to_print'] == 1){
                    $this->load->model('checks_model');
                    $account[0] = ['th_id' => $th_id, 'id' => $checkingAccount];
                    $data = $this->checks_model->onPrintMany($account);
                    $array = ['type' => 'success','auth' => $validate['auth'], 'message' => "Transactions successfully added!"];
                    $array['checks'] = $data;
                    echo json_encode($array);
                } else {
                    echo json_encode(array('type' => 'success', 'message' => 'Transactions successfully added.'));
                }
            } else {
                echo json_encode(array('type' => 'danger','auth' => $validate3['auth'], 'message' => 'something went wrong'));
            }
        } else {
            $errors = $validate1['msg'] . " " . $validate2['msg'] . " " . $validate3['msg'];;
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger','auth' => $validate3['auth'], 'message' => $errors));
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
        $refund = $this->input->post('refund');

        $sdApplyAmount = is_numeric($sdApplyAmount) ? $sdApplyAmount : 0;
        $lmrApplyAmount = is_numeric($lmrApplyAmount) ? $lmrApplyAmount : 0;
        $sdRefundAmount = is_numeric($sdRefundAmount) ? $sdRefundAmount : 0;
        $lmrRefundAmount = is_numeric($lmrRefundAmount) ? $lmrRefundAmount : 0;

        $accounts_receivable = $leaseInfo + ['account_id' => $this->ar, 'credit' => $sdApplyAmount + $lmrApplyAmount];
        $security_deposit = $leaseInfo + ['account_id' => $this->sd, 'debit' => $sdApplyAmount];
        $lmr = $leaseInfo + ['account_id' => $this->lmr, 'debit' => $lmrApplyAmount];

        $applyTransactions[] = $security_deposit;
        $applyTransactions[] = $lmr;

        // $data = array('header' => $header, 'applyTotal' => $sdApplyAmount + $lmrApplyAmount, 'applied_payments' => $applied_payments);
        // $validate = $this->validate_model->validate("applyRefunds", $data);

        if (($sdRefundAmount > 0) || ($lmrRefundAmount > 0)) {
            $refundFrom = ['account_id' => $checkingAccount, 'description' => 'SD/LMR Refund', 'credit' => $sdRefundAmount + $lmrRefundAmount, 'debit' => 0, 'line_number' => 0] + $leaseInfo;
            $security_deposit = ['account_id' => $this->sd, 'description' => 'SD/LMR Refund', 'credit' => 0, 'debit' => $sdRefundAmount, 'line_number' => 1] + $leaseInfo;
            $lmr = ['account_id' => $this->lmr, 'description' => 'SD/LMR Refund', 'credit' => 0, 'debit' => $lmrRefundAmount, 'line_number' => 1] + $leaseInfo;

            if ($sdRefundAmount > 0) $refundTransactions[] = $security_deposit;
            if ($lmrRefundAmount > 0) $refundTransactions[] = $lmr;
            $refundTransactions[] = $refundFrom;
            $check = ['paid_to' => $leaseInfo['profile_id']];

            $data['checking account'] = $checkingAccount;
            $validate2 = $this->validate_model->validate("applyRefunds", $data);
        }

        $data = array('applyTotal' => $sdApplyAmount + $lmrApplyAmount, 'applied_payments' => $applied_payments, 'totalSd' => $sdApplyAmount + $sdRefundAmount, 'totalLmr' => $lmrApplyAmount + $lmrRefundAmount, 'leaseInfo' => $leaseInfo, 'total' => ['Totals' => $sdApplyAmount + $sdRefundAmount + $lmrApplyAmount + $lmrRefundAmount], 'header' => $header, 'th_id' => $header['id'], 'mode' => 'edit');
        $validate3 = $this->validate_model->validate("applyRefunds", $data);

        if (($validate2 ? $validate2['bool'] : true) == true && ($validate3 ? $validate3['bool'] : true) == true && ($validate3 ? $validate3['auth'] : 'pass') == 'pass' && ($validate2 ? $validate2['auth'] : 'pass') == 'pass' ){
            $th_id = $this->applyRefundSecurity_model->editAppliedPayments($header, $accounts_receivable, $applyTransactions,  $refundTransactions, $leaseInfo, $sdApplyAmount, $sdRefundAmount, $lmrApplyAmount, $lmrRefundAmount, $checkingAccount, $applied_payments, $check);
            if($th_id){
                if($refund['to_print'] == 1){
                    $this->load->model('checks_model');
                    $account[0] = ['th_id' => $th_id, 'id' => $checkingAccount];
                    $data = $this->checks_model->onPrintMany($account);
                    $array = ['type' => 'success','auth' => $validate['auth'], 'message' => "Transactions successfully edited!"];
                    $array['checks'] = $data;
                    echo json_encode($array);
                } else {
                    echo json_encode(array('type' => 'success','auth' => $validate['auth'], 'message' => 'Transactions successfully edited.'));
                }
            } else {
                echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => 'something went wrong'));
            }
        } else {
            $auth = "fail";
            if ($validate3['auth'] == 'get' or $validate3['auth'] == 'get'){$auth ='get';}
            $errors = $validate2['msg'] . " " . $validate3['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger','auth' => $auth, 'message' => $errors));
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
                $lmr = array_values(array_filter($balances, function ($v) {
                    return $v['account_id'] == $this->lmr;
                }));
                $sd = array_values(array_filter($balances, function ($v) {
                    return $v['account_id'] == $this->sd;
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
                $this->data['edit'] = 'edit';
                $this->data['tenants'] = $this->applyRefundSecurity_model->getTenants();
                $this->data['banks'] = $this->applyRefundSecurity_model->getBanks();
                $this->data['property_id'] = $params->property;
                $this->data['unit_id'] = $params->unit;

                $balances = $this->applyRefundSecurity_model->getBalanceEdit($this->input->post('id'), $params->lease, $params->profile);
                $lmr = array_values(array_filter($balances, function ($v) {
                    return $v['account_id'] == $this->lmr;
                }));
                $sd = array_values(array_filter($balances, function ($v) {
                    return $v['account_id'] == $this->sd;
                }));
                $this->data['lmBalance'] = $lmr[0]['balance'];
                $this->data['sdBalance'] = $sd[0]['balance'];
                $this->data['arBalance'] = $this->applyRefundSecurity_model->getArBalanceEdit($this->input->post('id'), $params->lease, $params->profile);
                $this->data['lease_id'] = $params->lease;
                $this->data['leases'] = $this->applyRefundSecurity_model->getLeases();
                $this->data['properties'] = $this->applyRefundSecurity_model->getProperties();
                $this->data['units'] = $this->applyRefundSecurity_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                //$this->data['jTransactions'] = json_encode($this->applyRefundSecurity_model->getTransactionsEdit($this->input->post('id'), $params->lease, $params->profile));
                //$this->data['jTransactions'] = json_encode($this->applyRefundSecurity_model->getTransactionsEdit2($this->input->post('id'), $params->profile));  
                $this->data['header'] = $this->applyRefundSecurity_model->getHeaderEdit($this->input->post('id')); 
                //$this->data['account_id'] = $this->applyRefundSecurity_model->getDefaultAccount($this->data['header']->property_id);

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
                $default_bank = $this->applyRefundSecurity_model->getDefaultAccount($this->input->get('lease'));
            }else{
                $transactions = $this->applyRefundSecurity_model->getTransactions($this->input->get('lease'),$this->input->get('profile'));
                $arBalance = $this->applyRefundSecurity_model->getArBalance($this->input->get('lease'),$this->input->get('profile'));
                $sdBalance = $this->applyRefundSecurity_model->getSdBalance($this->input->get('lease'),$this->input->get('profile'));
                $lmrBalance = $this->applyRefundSecurity_model->getLmrBalance($this->input->get('lease'),$this->input->get('profile'));
                $default_bank = $this->applyRefundSecurity_model->getDefaultAccount($this->input->get('lease'));
            }

            //echo $transactions;
            echo json_encode(array('transactions' => $transactions,'arBalance' => $arBalance, 'sdBalance' => $sdBalance,'lmrBalance' => $lmrBalance, 'default_bank' => $default_bank));
        }


   

    ////////// bill

    function addBill($memorized = null)
    {
        $this->load->model('bills_model');
        $confirm = $this->input->post('confirm');
        $errors = "";
        $saveAndPay = $this->input->post('saveAndPay');
        $header = $this->input->post('header');
        if($saveAndPay == 1){$header['transaction_date'] = date("Y/m/d");}
        $headerTransaction = $this->removeHeaderComma($this->input->post('headerTransaction'));
        $headerTransaction['account_id'] = $this->ap;
        $transactions = $this->removeDetailCommas($this->input->post('transactions'));
        $special = $this->input->post('bills');
        $type = $this->input->post('radioButton');
        $data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions, 'special' => $special, 'confirm' => $confirm);
        $validate = $this->validate_model->validate("bills", $data);
        if ($memorized) {
            return $validate;
        }
        //$this->form_validation->set_rules($this->settings->accountFormValidation);

        if (/*$this->form_validation->run() &&*/
        $validate['bool'] && $validate['auth'] =='pass') {
            $transaction_id_b = $this->bills_model->addbill($type, $header, $headerTransaction, $transactions, $special);
            if ($transaction_id_b) {
                if ($saveAndPay == 1) {
                    $this->db->select('default_bank');
                    $this->db->from('properties');
                    $this->db->where('id', $headerTransaction['property_id']);
                    $q = $this->db->get();
                    if ($q->num_rows() > 0) {
                        $defaul_bank = $q->row()->default_bank;
                    }
                    $pmt_account = ['account_id' => $defaul_bank, 'profile_id' => $headerTransaction['profile_id'], 'property_id' => $headerTransaction['property_id'], 'credit' => $headerTransaction['credit']];
                    $accounts_payable = ['account_id' => $this->ap, 'profile_id' => $headerTransaction['profile_id'], 'property_id' => $headerTransaction['property_id'], 'debit' => $headerTransaction['credit']];
                    //should header stay the same as the bill? or just have bill date
                    $transaction[0] = ['transaction_id_b' => $transaction_id_b, 'amount' => $headerTransaction['credit']];
                    $this->load->model('payBills_model');
                    $this->payBills_model->applyPayments($header, $transaction, $pmt_account, $accounts_payable, 0);
                }
                echo json_encode(array('type' => $validate['type'], 'auth' => $validate['auth'], 'message' => 'Bill successfully added.'));
                return true;
            } else {
                echo json_encode(array('type' => 'danger', 'auth' => $validate['auth'], 'message' => 'Something went wrong'));
            }
    } else {
        // if($validate['auth'] == 'get' || $validate['auth'] == 'fail'){
        //     $auth['auth'] = $validate['auth'];
        //     //echo json_encode($auth);
        // }
            $errors = $errors . /*validation_errors() .*/
                "</br>" . $validate['msg'];
            echo json_encode(array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
            return false;
    }
        }

    function editBill($tid = 0)
    {
        $this->load->model('bills_model');

        $saveAndPay = $this->input->post('saveAndPay');
        $header = $this->input->post('header');
        $headerTransaction = $this->removeHeaderComma($this->input->post('headerTransaction'));
        $transactions = $this->removeDetailCommas($this->input->post('transactions'));
        $special = $this->input->post('bills');
        $deletes = $this->input->post('delete');
        $confirm = $this->input->post('confirm');
        $type = $this->input->post('radioButton');
        $applied = $this->input->post('applied');
        $origAmt = $this->input->post('origAmt');
        $data = array('header' => $header, 'transactions' => $transactions, 'special' => $special, 'headerTransaction' => $headerTransaction, 'trans_id' => $tid, 'confirm' => $confirm, 'deletes' => $deletes);
        //$validate = $this->validate_model->validate("transactions", $data);
        $validate = $this->validate_model->validate("bills", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);

        if (/*$this->form_validation->run() &&*/
        $validate['bool'] && $validate['auth'] =='pass') {
            $transaction_id_b = $this->bills_model->editBill($type, $header, $headerTransaction, $transactions, $special, $tid, $deletes, $applied, $origAmt);
            if ($transaction_id_b) {
                if ($saveAndPay == 1) {
                    $this->db->select('default_bank');
                    $this->db->from('properties');
                    $this->db->where('id', $headerTransaction['property_id']);
                    $q = $this->db->get();
                    if ($q->num_rows() > 0) {
                        $defaul_bank = $q->row()->default_bank;
                    }
                    $pmt_account = ['account_id' => $defaul_bank, 'profile_id' => $headerTransaction['profile_id'], 'property_id' => $headerTransaction['property_id'], 'credit' => $headerTransaction['credit']];
                    $accounts_payable = ['account_id' => $this->ap, 'profile_id' => $headerTransaction['profile_id'], 'property_id' => $headerTransaction['property_id'], 'debit' => $headerTransaction['credit']];
                    //should header stay the same as the bill? or just have bill date
                    $transaction[0] = ['transaction_id_b' => $transaction_id_b, 'amount' => $headerTransaction['credit']];
                    $this->load->model('payBills_model');
                    $this->payBills_model->applyPayments($header, $transaction, $pmt_account, $accounts_payable, 0);
                }
                echo json_encode(array('type' => $validate['type'], 'auth' => $validate['auth'], 'message' => 'Bill successfully edited.'));
            } else {
                echo json_encode(array('type' => 'danger', 'auth' => $validate['auth'], 'message' => 'Something went wrong'));
            }
        } else {
            // if($validate['auth'] == 'get' || $validate['auth'] == 'fail'){
            //     $auth['auth'] = $validate['auth'];
            //     //echo json_encode($auth);
            // }
                $errors = $errors . /*validation_errors() .*/
                    "</br>" . $validate['msg'];
                echo json_encode(array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
                return false;
        }
            }

    //////bills
    function billsgetModal()
    {
        $this->load->model('bills_model');
        $this->load->model('checks_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $params = json_decode($this->input->post('params'));
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
                $this->data['jUnits'] = json_encode($this->data['units']);
                $this->data['names'] = $this->bills_model->getNames();
                $this->data['jNames'] = json_encode($this->data['names']);
                $this->data['employees'] = $this->bills_model->getAllEmployees();
                $this->data['jTransactions'] = json_encode($this->data['transactions']);
                $this->data['jPropertyAccounts'] = json_encode($this->checks_model->getPropertyAccounts2());
                $this->data['paidStatusHtml'] = "";
                $this->data['headerTransaction']->profile_id = $params->profile;
                $this->data['headerTransaction']->property_id = $params->property;


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
                $this->data['edit'] = 'edit';
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
                $this->data['transactions'] = $transactions;
                $this->data['headerTransaction'] = $headerTransaction;
                $this->data['paid'] = $this->bills_model->getAmountPaid($this->input->post('id'));
                $this->data['bills'] = $this->bills_model->getBill($this->input->post('id'));
                $this->data['names'] = $this->bills_model->getNames();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                //$this->data['accounts'] = $this->bills_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['jUnits'] = json_encode($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['jNames'] = json_encode($this->data['names']);
                $this->data['employees'] = $this->bills_model->getAllEmployees();
                $this->data['jTransactions'] = json_encode($transactions);
                $this->data['jPropertyAccounts'] = json_encode($this->checks_model->getPropertyAccounts2());
                if ($this->data['paid'] > 0) {
                    $this->data['paidStatus'] = ($this->data['headerTransaction']->credit - $this->data['paid']) > 0 ? 'paidInPartial' : 'paidInFull';
                    $this->data['paidStatusHtml'] = ($this->data['paidStatus'] == 'paidInPartial') ? "Partially Paid" : 'Paid';
                } else {
                    $this->data['paidStatus'] = "";
                    $this->data['paidStatusHtml'] = "";
                }
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                break;
        }
        $this->load->view('forms/bill/main2', $this->data);
    }

    ////charges

    function newCharge($validate = null)
    {
        $errors = "";
        $this->load->model('charges_model');
        $header = $this->input->post('header');
        $transaction = $this->removeHeaderComma($this->input->post('transactions'));
        if($transaction['credit'] < 0 ){
            $transaction['debit'] =  $transaction['credit'] * -1;
            unset($transaction['credit']);
        }
        $data = array('header' => $header, 'transactions' => $transaction);
        $validate = $this->validate_model->validate("charges", $data);
        if ($memorized) {
            return $validate;
        }
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/
            $validate['bool'] && $validate['auth'] =='pass' && $this->charges_model->addCharge($header, $transaction)) {
                echo json_encode(array('type' => 'success','auth' => $validate['auth'], 'message' => 'Charge successfully added.'));
                if($this->site->settings->email_autocharge_notices == 1){
                    $this->sendEmail($row,'c');
                }

            return true;
        } else {
            $errors = $errors . validation_errors() . "</br>" . $validate['msg'];
            echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
            return false;
        }
    }

    function editCharge($id)
    {
        $errors = "";
        $this->load->model('charges_model');
        $header = $this->input->post('header');
        $transaction = $this->removeHeaderComma($this->input->post('transactions'));
        if($transaction['credit'] < 0 ){
            $transaction['debit'] =  $transaction['credit'] * -1;
            unset($transaction['credit']);
        }
        $data = array('header' => $header, 'transactions' => $transaction);
        $validate = $this->validate_model->validate("charges", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/
            $validate['bool'] && $validate['auth'] =='pass'  && $this->charges_model->editCharge($header, $transaction, $id)){
                echo json_encode(array('type' => 'success','auth' => $validate['auth'], 'message' => 'Charge successfully edited.'));
            }
        else {
            $errors = $errors . validation_errors() . "</br>" . $validate['msg'];
            echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    public function printInvoice()
    {
        //$accounts1 = $this->input->get('accounts');
        //$leases = $this->input->post('leases');
        $this->load->model('charges_model');
        //$data = json_encode($this->charges_model->getLeases($leases));
        //$data = json_encode($this->charges_model->getTenants([2]));
        $data = json_encode($this->charges_model->getprofile($lease, $profile));
        echo $data;
    }

    ///charges
    function chargesgetModal()
    {
        $this->load->model('leases_model');
        $this->load->model('units_model');
        $this->load->model('tenants_model');
        $this->load->model('charges_model');
        $params = json_decode($this->input->post('params'));
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'transactions/newCharge';
                $this->data['title'] = 'Add charge';
                $this->data['properties'] = $this->charges_model->getAllProperties();
                $this->data['units'] = $this->units_model->getUnits();
                $this->data['tenants'] = $this->charges_model->getAllTenants();
                $this->data['leases'] = $this->charges_model->getAllLeases();
                $this->data['items'] = $this->leases_model->getAllItems();
                $this->data['jItems'] = json_encode($this->leases_model->getAllItems());
                $this->data['jleases'] = json_encode($this->data['leases']);
                $this->data['jproperties'] = json_encode($this->data['properties']);
                $this->data['junits'] = json_encode($this->data['units']);
                $this->data['jtenants'] = json_encode($this->data['tenants']);
                $this->data['newChargeInfo'] = $params;
                if ($params->lease) {
                    $this->data['transaction']->lease_id = $params->lease;
                }
                if ($params->profile) {
                    $this->data['transaction']->profile_id = $params->profile;
                }

                break;
            case 'edit' :
                $this->load->model('charges_model');
                $this->data['target'] = 'transactions/editCharge/' . $this->input->post('id');
                $this->data['title'] = 'Edit charge';
                $this->data['edit'] = 'edit';
                $this->data['properties'] = $this->charges_model->getAllProperties();
                $this->data['units'] = $this->units_model->getUnits();
                $this->data['tenants'] = $this->charges_model->getAllTenants();
                $this->data['leases'] = $this->charges_model->getAllLeases();
                $this->data['items'] = $this->leases_model->getAllItems();
                $this->data['jItems'] = json_encode($this->leases_model->getAllItems());
                $this->data['header'] = $this->charges_model->getHeader($this->input->post('id'));
                $this->data['transaction'] = $this->charges_model->getTransaction($this->input->post('id'));
                $this->data['jtransaction'] = json_encode($this->data['transaction']);
                $this->data['jleases'] = json_encode($this->data['leases']);
                $this->data['jproperties'] = json_encode($this->data['properties']);
                $this->data['junits'] = json_encode($this->data['units']);
                $this->data['jtenants'] = json_encode($this->data['tenants']);
                break;
        }
        $this->load->view('forms/charge/addChargeModel4', $this->data);
    }
    

    ////////checks
   
    function addCheck($memorized = null)
    {
        $this->load->model('checks_model');

        $errors = "";
        $header = $this->input->post('header');
        $headerTransaction = $this->removeHeaderComma($this->input->post('headerTransaction'));
        $transactions = $this->removeDetailCommas($this->input->post('transactions'));
        $print = $this->input->post('saveAndPrint');
        //$special = $this->input->post('checks');

        $data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions);
        $validate = $this->validate_model->validate("checks", $data);
        if ($memorized) {
            return $validate;
        }

        // if ($validate['bool'] &&  $this->checks_model->addCheck($header, $headerTransaction, $transactions, $print))
        //     echo json_encode(array('type' => 'success', 'message' => 'Check successfully added.'));
        if ($validate['bool'] && $validate['auth'] =='pass') {
            $checksInfo = $this->checks_model->addCheck($header, $headerTransaction, $transactions, $print);
            if ($checksInfo) {
                $array = ['type' => $validate['type'],'auth' => $validate['auth'], 'message' => "Check successfully added"];
                if ($print === '1'){
                    $array['message'] = $checksInfo->data ? $array['message'] : 'Check added Insufficient Bank Info';
                    $array['checks'] = $checksInfo->data;
                }
                echo json_encode($array);
                return true;
            } else {
                echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => "Check not added Something went wrong"));
                return false;
            }
        } else {
            $errors = $errors . validation_errors() . "</br>" . $validate['msg'];
            echo json_encode(array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
            return false;
        }
    }

    function editCheck($tid = 0)
    {
        $this->load->model('checks_model');

        $header = $this->input->post('header');
        $headerTransaction = $this->removeHeaderComma($this->input->post('headerTransaction'));
        $transactions = $this->removeDetailCommas($this->input->post('transactions'));
        //$special = $this->input->post('checks');
        $print = $this->input->post('saveAndPrint');
        $deletes = $this->input->post('delete');
        //$m = $this->checks_model->msg;
        $data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions, 'deletes' => $deletes);
        $validate = $this->validate_model->validate("checks", $data);
        //$warning = $this->checks_model->editCheck($header, $headerTransaction, $transactions, $tid, $deletes);
        //if($warning->speach)echo json_encode(array('type' => 'danger', 'message' => $warning->speach, 'errors' => $this->parse_errors('transaction'))); return;
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        // if (/*$this->form_validation->run() &&*/$validate['bool'] &&  $this->checks_model->editCheck($header, $headerTransaction, $transactions, $tid, $print, $deletes))
        //     echo json_encode(array('type' => 'success', 'message' => 'Check successfully edited.'));
        if ($validate['bool'] && $validate['auth'] =='pass') {
            $checksInfo = $this->checks_model->editCheck($header, $headerTransaction, $transactions, $tid, $print, $deletes);
            if ($checksInfo) {
                $array = ['type' => $validate['type'],'auth' => $validate['auth'], 'message' => "Check successfully edited"];
                if ($print === '1'){
                    $array['message'] = $checksInfo->data ? $array['message'] : 'Check edited Insufficient Bank Info';
                    $array['checks'] = $checksInfo->data;
                }
                echo json_encode($array);
            } else {
                echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => "Check not edited Something went wrong"));
            }
        } else {
            $errors = $validate['msg'] . $this->checks_model->msg;
            echo json_encode(array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function voidCheck($tid = 0)
    {
        $this->load->model('checks_model');
        $delete = 1;
        $validate1 = $this->input->post('validate');
        if($validate1 ==='yes') $delete = NULL;
        
        $header = $this->input->post('header');
        $headerTransaction = $this->removeHeaderComma($this->input->post('headerTransaction'));
        $transactions = $this->removeDetailCommas($this->input->post('transactions'));
        $headerTransaction['credit'] = '0.00';
        $headerTransaction['debit'] = '0.00';
        $header['memo'] = 'VOID: '.$header['memo'];

        foreach($transactions as &$transaction){
            $transaction['debit'] = '0.00';
            $transaction['credit'] = '0.00';
        }

        //$special = $this->input->post('checks');
        $print = $this->input->post('saveAndPrint');
        $deletes = $this->input->post('delete');
        //$m = $this->checks_model->msg;
        $data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions, 'deletes' => $deletes);
        $validate = $this->validate_model->validate("vchecks", $data);
        //$warning = $this->checks_model->editCheck($header, $headerTransaction, $transactions, $tid, $deletes);
        //if($warning->speach)echo json_encode(array('type' => 'danger', 'message' => $warning->speach, 'errors' => $this->parse_errors('transaction'))); return;
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        // if (/*$this->form_validation->run() &&*/$validate['bool'] &&  $this->checks_model->editCheck($header, $headerTransaction, $transactions, $tid, $print, $deletes))
        //     echo json_encode(array('type' => 'success', 'message' => 'Check successfully edited.'));
        
        
        
        if ($validate['bool'] && $validate['auth'] =='pass') {
            if($delete == NULL){
                $warning = new stdClass();
                $warning->messages = 'Are you sure you want to void this transaction?';
                $warning->status = 0;  
                $array = ['type' => 'success'];
                echo json_encode($array);
            } else {
                $checksInfo = $this->checks_model->editCheck($header, $headerTransaction, $transactions, $tid, $print, $deletes);
                if ($checksInfo) {
                    $array = ['type' => $validate['type'],'auth' => $validate['auth'], 'message' => "Check is voided! "];
                    if ($print === '1'){
                        $array['message'] = $checksInfo->data ? $array['message'] : 'Check edited Insufficient Bank Info';
                        $array['checks'] = $checksInfo->data;
                    }
                    echo json_encode($array);
                } else {
                    echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => "Check not edited Something went wrong"));
                }
            }

        } else {
            $errors = $validate['msg'] . $this->checks_model->msg;
            echo json_encode(array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
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

    public function printBlankCheck()
    {
        $accountId = json_decode($this->input->post('params'), true);
        $this->load->model('checks_model');
        $data = $this->checks_model->printBlankCheck($accountId);
        echo json_encode($data);
    }

    public function verifyPrint()
    {
        $account = $this->input->post();
        $this->load->model('checks_model');
        $this->checks_model->confirmPrint($account);
    }

//nsf
    public function nsf(){
        $post = $this->input->post('nsf');
        $this->load->model('nsf_model');

        //$this->db->trans_start();
        //need transaction id a need deposit to account no need to query
        $this->db->select('rpu.profile_id, rpu.property_id, rpu.unit_id, rpu.lease_id, rpu.debit'); //ddt.account_id will not be needed
        $this->db->from('transactions rpu');
        //$this->db->join('transactions dudf', 'dudf.id = rpu.deposit_id AND rpu.trans_id = ' . $post['trans_id'] . ' AND rpu.account_id != ' . $this->ar);
        //line below will not be needed
        //$this->db->join('transactions ddt', 'dudf.trans_id = ddt.trans_id AND ddt.account_id !=' . $this->uf);
        $this->db->where('rpu.trans_id = ' . $post['trans_id'] . ' AND rpu.account_id != ' . $this->ar);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
             $profile_id = $q->row()->profile_id;
             $property_id = $q->row()->property_id;
             $unit_id = $q->row()->unit_id;
             $lease_id = $q->row()->lease_id;
             $amount = $q->row()->debit;
             //line below will not be needed
             //$account_id = $q->row()->account_id;//deposit to in deposits
             //line below will be needed
             
             

        }
        $account_id = $post['deposit_bank_id'];
        $transaction_id_a = $post['transaction_id_a'];
        $info = ['type_id' => 21, 'type_item_id' => $transaction_id_a, 'lease_id' =>  $lease_id, 'property_id' => $property_id, 'profile_id' => $profile_id, 'unit_id' => $unit_id];
        $header = ['transaction_date' => $post['transaction_date'], 'transaction_ref' => $post['check_number']];
        $accounts_receivable = $info + ['account_id' => $this->ar, 'debit' => $amount, 'description' => $post['description']];
        $deposit_to = $info + ['account_id' => $account_id, 'credit' => $amount, 'description' => $post['description']];

        
        if (/*$this->form_validation->run() && $validate['bool'] &&*/ $this->nsf_model->balanceBounce($header, $accounts_receivable, $deposit_to, $info, $post)){
            echo json_encode(array('type' => 'success', 'message' => 'Nsf successfully added.'));
        }
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => 'Nsf failed.', 'errors' => $this->parse_errors('nsf')));
        }
    }

    public function editNsf(){
        $post = $this->input->post();
        $this->load->model('nsf_model');

        //$this->db->trans_start();
        //need transaction id a need deposit to account no need to query
        // $this->db->select('rpu.profile_id, rpu.property_id, rpu.unit_id, rpu.lease_id, rpu.debit'); //ddt.account_id will not be needed
        // $this->db->from('transactions rpu');
        //$this->db->join('transactions dudf', 'dudf.id = rpu.deposit_id AND rpu.trans_id = ' . $post['trans_id'] . ' AND rpu.account_id != ' . $this->ar);
        //line below will not be needed
        //$this->db->join('transactions ddt', 'dudf.trans_id = ddt.trans_id AND ddt.account_id !=' . $this->uf);
        // $this->db->where('rpu.trans_id = ' . $post['trans_id'] . ' AND rpu.account_id != ' . $this->ar);
        // $q = $this->db->get();
        // if ($q->num_rows() > 0) {
        //      $profile_id = $q->row()->profile_id;
        //      $property_id = $q->row()->property_id;
        //      $unit_id = $q->row()->unit_id;
        //      $lease_id = $q->row()->lease_id;
        //      $amount = $q->row()->debit;
             //line below will not be needed
             //$account_id = $q->row()->account_id;//deposit to in deposits
             //line below will be needed
             
             

        //}
        //$account_id = $post['deposit_bank_id'];
        //$info = ['lease_id' => $lease_id, 'property_id' => $property_id, 'profile_id' => $profile_id, 'unit_id' => $unit_id];
        $header = ['id' =>  $post['nsf']['header_id'], 'transaction_date' =>  $post['nsf']['transaction_date']];
        //$accounts_receivable = $info + ['account_id' => $this->ar, 'debit' => $amount, 'description' => $post['description']];
        //$deposit_to = $info + ['account_id' => $account_id, 'credit' => $amount, 'description' => $post['description']];
        $description = $post['nsf']['description'];
        $transaction_id_a = $post['nsf']['transaction_id_a'];
        $lease_id = $post['profile']['lease_id'];
        $profile = $post['nsf']['profile'];
        $prop_id = $post['profile']['prop_id'];
        $unit = $post['profile']['unit_id'];
        $bank =  $post['nsf']['deposit_bank_id'];
        
        if (/*$this->form_validation->run() && $validate['bool'] &&*/ $this->nsf_model->balanceBounceEdit($header, $description, $transaction_id_a, $lease_id, $profile, $prop_id , $unit, $bank)){
            echo json_encode(array('type' => 'success', 'message' => 'Nsf successfully edited.'));
        }
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => 'Nsf edit failed.', 'errors' => $this->parse_errors('nsf')));
        }
    }

    function nsfgetModal()
    {
       
        $this->load->model('nsf_model');
        $params = json_decode($this->input->post('params'));
        switch ($this->input->post('mode')) {
            case 'add':
                $this->data['target'] = 'transactions/nsf/';
                $this->data['title'] = 'Nsf';
                //$this->data['id'] = $this->input->post('id');
                $this->data['id'] = $params->{'header[id]'};
                $this->data['transaction_id_a'] = $params->transaction_id_a;
                $this->data['ref'] = $params->{'header[transaction_ref]'};
                $this->data['profile'] = $params->profile_id;
                $this->data['lease'] = $params->lease_id;
                $this->data['account_id2'] = $params->account_id2;
                $this->data['deposit_date'] = $params->{'header[transaction_date]'};
                
                break;
            case 'edit' :
            $this->data['target'] = 'transactions/editNsf/';
            $this->data['title'] = 'Nsf';
            //$this->data['id'] = $this->input->post('id');
            $this->data['header'] = $this->nsf_model->getHeader($this->input->post('id'));
            $header = $this->data['header'];
            //$this->data['transaction'] = $this->nsf_model->getTransaction($this->input->post('id'));
            // $this->data['id'] = $params->trans_id;
             $this->data['transaction_id_a'] = $header->transaction_id_a;
             $this->data['ref'] = $header->transaction_ref;
             $this->data['profile'] = $header->profile;
             $this->data['lease'] = $header->lease;
            // $this->data['deposit_bank_id'] = $params->deposit_bank_id;
            $this->data['account_id2'] = $header->deposit_bank_name;
            //$this->data['deposit_date'] = $header->deposit_date;
                break;
        }
        $this->load->view('forms/check/nsf', $this->data);
    }

    //////checks
    function checksgetModal()
    {
        $this->load->model('checks_model');
        $params = json_decode($this->input->post('params'));
        switch ($this->input->post('mode')) {
            case 'add' :
                $accountId = json_decode($this->input->post('params'), true);
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
                $this->data['jNames'] = json_encode($this->data['names']);
                $this->data['jTransactions'] = json_encode($transactions);
                $this->data['jPropertyAccounts'] = json_encode($this->checks_model->getPropertyAccounts2());
                $this->data['headerTransaction']->account_id = $params->account;
                $this->data['headerTransaction']->profile_id = $params->profile;
                $this->data['headerTransaction']->property_id = $params->property;

                //return $this->checks_model->getAccounts($pid);
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                $this->load->view('forms/check/main6', $this->data);
                break;
            case 'edit' :
                $this->data['target'] = 'transactions/editCheck/' . $this->input->post('id');
                $this->data['title'] = 'Edit Check';
                $this->data['edit'] = 'edit';
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
                $this->data['transactions'] = $transactions;
                $this->data['headerTransaction'] = $headerTransaction;
                $this->data['address'] = $this->checks_model->getAddress1($this->input->post('id'));
                $this->data['checks'] = $this->checks_model->getCheck($this->input->post('id'));
                $this->data['names'] = $this->checks_model->getNames();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);//not used anymore
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['jNames'] = json_encode($this->data['names']);
                $this->data['jTransactions'] = json_encode($transactions);
                $this->data['jPropertyAccounts'] = json_encode($this->checks_model->getPropertyAccounts2());
                $clrd = $this->data['headerTransaction']->clr == 1 ? 'clrdrecinfo' : 'unclrdrecinfo';
                $this->data['hasRecId'] = $this->data['headerTransaction']->rec_id != null && $this->data['headerTransaction']->clr == 1 ? 'hasRecId' : '';
                $this->data['hasRecIdHtml'] = $this->data['headerTransaction']->rec_id != null && $this->data['headerTransaction']->clr == 1 ? 'Cleared' : '';
                $this->data['RecIdHtml'] = $this->data['headerTransaction']->rec_id != null  ? ' <span data-id="50" class ="'.$clrd.' reportLink" rtype="report" defaults="'.$this->data['headerTransaction']->rec_id.'"> Cleared on Rec#'.$this->data['headerTransaction']->rec_id.'</span>'  : '';
                

                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                $this->load->view('forms/check/main6', $this->data);
                break;
            case 'checkToPrint':
                $this->load->model('creditCard_model');
                $this->data['target'] = 'transactions/onPrint';
                $this->data['title'] = 'Edit Check';

                $this->data['transactions'] = $this->checks_model->getChecksToPrint();
                $this->data['jtransactions'] = json_encode($this->data['transactions']);
                $this->data['properties'] = $this->creditCard_model->getProperties();
                // $this->data['names'] = $this->creditCard_model->getNames();
                // $this->data['classes'] = $this->creditCard_model->getClasses();
                $this->data['accounts'] = $this->creditCard_model->getAllAccounts();
                // $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['units'] = $this->creditCard_model->getUnits();
                // $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                $this->data['jUnits'] = json_encode($this->data['units']);
                $this->data['jPropertyAccounts'] = json_encode($this->creditCard_model->getPropertyAccounts2());
                $this->data['names'] = $this->creditCard_model->getProfiles();
                $this->data['jNames'] = json_encode($this->data['names']);
                $this->load->view('forms/check/check_to_print', $this->data);
                break;
            case 'nsf':
                $this->data['target'] = 'transactions/nsf/';
                $this->data['title'] = 'Nsf';
                //$this->data['id'] = $this->input->post('id');
                $this->data['id'] = $params->trans_id;
                $this->data['transaction_id_a'] = $params->transaction_id_a;
                $this->data['ref'] = $params->ref;
                $this->data['profile'] = $params->profile;
                $this->data['lease'] = $params->lease;
                $this->data['deposit_bank_id'] = $params->deposit_bank_id;
                $this->data['deposit_bank_name'] = $params->deposit_bank_name;
                $this->data['deposit_date'] = $params->deposit_date;
                $this->load->view('forms/check/nsf', $this->data);
        }
    }

    /////////credit cards

    function addCcTransaction($memorized = null)
    {
        $this->load->model('creditCard_model');
        $header = $this->input->post('header');
        $creditCard = $this->removeHeaderComma($this->input->post('credit_card'));
        $transactions = $this->removeDetailCommas($this->input->post('transactions'));
        $charge = $this->input->post('charge');
        $credit = $this->input->post('credit');
        $type = $this->input->post('radioButton');

        //     //$this->form_validation->set_rules($this->settings->accountFormValidation);
        //     /*if ($this->form_validation->run() &&*/
        //     $date = $this->input->post('pay_bill_date');
        //     if($date){
        //     $date = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
        //     }
        $data = array('header' => $header, 'transactions' => $transactions, 'creditCard' => $creditCard);
        $validate = $this->validate_model->validate("cc_charge", $data);
        if ($memorized) {
            return $validate;
        }

        if ($validate['bool'] && $validate['auth'] =='pass' && $this->creditCard_model->addTransaction($type, $header, $creditCard, $transactions)) {
            //($this->form_validation->run() && $validate['bool'] && $this->payBills_model->applyPayments( $sqlDate , $transactions)){
            echo json_encode(array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => 'Transactions successfully added.'));
            return true;
        } else {
            $errors = $validate['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => $errors));
            return false;
            //echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function editTransaction()
    {
        $this->load->model('creditCard_model');
        $header = $this->input->post('header');
        $transactions = $this->removeDetailCommas($this->input->post('transactions'));
        $creditCard = $this->removeHeaderComma($this->input->post('credit_card'));
        $deletes = $this->input->post('delete');
        $type = $this->input->post('radioButton');
        //     //$this->form_validation->set_rules($this->settings->accountFormValidation);
        //     /*if ($this->form_validation->run() &&*/
        //     $date = $this->input->post('pay_bill_date');
        //     if($date){
        //     $date = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
        //     }
        $data = array('header' => $header, 'transactions' => $transactions, 'creditCard' => $creditCard, 'deletes' => $deletes);
        $validate = $this->validate_model->validate("cc_charge", $data);
        if ($validate['bool'] && $validate['auth'] =='pass' && $this->creditCard_model->editTransaction($type, $header, $creditCard, $transactions, $deletes)) {
            //($this->form_validation->run() && $validate['bool'] && $this->payBills_model->applyPayments( $sqlDate , $transactions)){
            echo json_encode(array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => 'Transactions successfully edited.'));
        } else {
            $errors = $validate['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => $errors));
            //echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    // function recordTransactionsOld() 
    // {
    //     $this->load->model('creditCard_model');
    //     $ccAccount_id = $this->input->post('ccAccount_id');
    //     $transactions = $this->input->post('transactions');
    //     $details = $this->input->post('details');

    //     //$data = array('details' => $details, 'transactions' => $transactions);
    //     $data = $this->input->post();
    //     $validate = $this->validate_model->validate("cc_grid_charge", $data);
    // if ($validate['bool']){ 
    //     $warnings = $this->creditCard_model->recordTransactions($ccAccount_id , $transactions, $details);
    //     if(empty($warnings->message) && $warnings->status > 0){
    //         echo json_encode(array('type' => 'success', 'message' => 'All Transactions successfully added.'));
    //     }
    //     else{
    //         echo json_encode(array('type' => 'danger', 'message' => $warnings->message .  '</br>' . $warnings->statusMessage));
    //     }
    // }else {
    //         $errors = $validate['msg'];
    //         echo json_encode(array('type' => 'danger', 'message' => $errors));
    // }

    // }

    function recordTransactions()
    {
        
        $this->load->model('creditCard_model');
        $ccAccount_id = $this->input->post('ccAccount_id');
        $transactions = $this->input->post('transactions');
        $details = $this->input->post('details');
        $file = $this->creditCard_model->createFile();
        rename(FCPATH . $file,FCPATH . "uploads/documents/" . $file);
        $url = base_url() . "uploads/documents/" . $file;
        //$path = str_replace("\\", '/', FCPATH);
        //$path = "C:/xampp/htdocs/simplicity/";
        $validate = $this->validate_model->validate("ccAccount", $ccAccount_id);
        if ($validate['bool'] == false) {
            echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => $validate['msg']));
            $validate['bool'] = true;
            $validate['msg'] = '';
            return;
        }

        $this->db->select('cc.property_id, cc.vendor, p.vendor_profile_id');
        $this->db->from('credit_cards cc');
        $this->db->join('properties p', 'cc.property_id = p.id');
        $this->db->where('cc.account_id', $ccAccount_id);
        $q = $this->db->get();
        $Ccproperty_id = $q->row()->property_id;
        $vendor = $q->row()->vendor;
        $vp_id = $q->row()->vendor_profile_id;

        $array = [];

        foreach ($transactions as $transaction) {
            $data = ['transaction' => $transaction, 'details' => $details, 'ccAccount_id' => $ccAccount_id];
            $validate = $this->validate_model->validate("cc_grid_charge", $data);

            if ($validate['bool'] && $validate['auth'] =='pass' && $this->creditCard_model->recordTransactions($ccAccount_id, $Ccproperty_id, $vendor, $vp_id, $transaction, $details, $file)) {
                array_push($array, array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => 'Transaction successfully added.', 'msgInfo' => $transaction['ofxId']));
            } else {
                $errors = $validate['msg'];
                $array['auth'] = $validate['auth'];
                $errors = str_replace('</br>', ' ', $errors);
                array_push($array, array('type' => 'danger','auth' => $validate['auth'], 'message' => $errors, 'msgInfo' => $transaction['ofxId']));
                //json_encode(array('type' => 'danger', 'message' =>$errors, 'utility' => $utility_id));
                //$fail++;
                $validate['bool'] = true;
                $this->validate_model->validation['bool'] = true;
                $this->validate_model->validation['msg'] = '';                 
                //continue;
            }
        }
        $array['file'] = $url;
        echo json_encode($array);
    }


    

        ///Bank Trans
        public function bankTransgetModal()
        {
            $this->load->model('BankTrans_model');
    
            switch ($this->input->post('mode')) {
                case 'record' :
                    $this->data['target'] = 'transactions/recordTransactions';
                    $this->data['title'] = 'CC Transactions';
                    $this->data['creditCards'] = json_encode($this->creditCard_model->getCC());
                    $this->data['njfirstAccount'] = $this->creditCard_model->getCC1();
                    $this->data['firstAccount'] = json_encode($this->data['njfirstAccount']);
                    $firstAccountId = $this->data['njfirstAccount']->id;
                    $this->data['error'] = $this->ofx_model->getOfx($firstAccountId);
    
                    $this->data['ofxImports'] = json_encode($this->creditCard_model->getOfxImports($firstAccountId));
                    $this->data['title'] = 'Add Credit Card Charge';
                    $this->data['properties'] = $this->creditCard_model->getProperties();
                    $this->data['accounts'] = $this->creditCard_model->getAllAccounts();
                    $this->data['units'] = $this->creditCard_model->getUnits();
                    $this->data['jProperties'] = json_encode($this->data['properties']);
                    $this->data['jAccounts'] = json_encode($this->data['accounts']);
                    $this->data['jUnits'] = json_encode($this->data['units']);
                    $this->data['jPropertyAccounts'] = json_encode($this->creditCard_model->getPropertyAccounts2());
                    $this->data['names'] = $this->creditCard_model->getProfiles();
                    $this->data['jNames'] = json_encode($this->data['names']);
                    $this->load->view('forms/cc/cc_grid_charge', $this->data);
                    break;
                case 'add' :
                    $this->data['target'] = 'transactions/addBankTrans';
                    $this->data['title'] = 'New Bank Transaction';
                    //$this->data['classes'] = $this->creditCard_model->getClasses();
                    $this->data['jClasses'] = json_encode($this->data['classes']);

                    $this->load->view('forms/bank_trans/bank_trans', $this->data);
                    break;
                case 'edit' :
                $this->data['edit'] = 'edit';
                    $this->data['header'] = $this->BankTrans_model->getHeaderEdit($this->input->post('id'));
                    $this->data['transactions'] = $this->creditCard_model->getTransactions($this->input->post('id'));
                    $this->data['accountName'] = $this->BankTrans_model->getAccountName($this->data['transactions'][0]->account_id);
                    $this->data['jHeader'] = json_encode($this->cBankTrans_model->getHeaderEdit($this->input->post('id')));
                    $this->data['jTransactions'] = json_encode($this->cBankTrans_model->getTransactions($this->input->post('id')));
                    $this->data['target'] = 'transactions/editTransaction/' . $this->input->post('id');
                    $this->data['title'] = 'Edit Bank Transaction';
                    $this->data['creditCard'] = $this->creditCard_model->getCC();
    
                    // if (isset($params->es_key)) {
                    //     $key = explode('.', $params->es_key)[1];
                    //     $this->data['account'] = new stdClass();
                    //     $this->data['account']->$key = $params->es_value;
                    $this->load->view('forms/bank_trans/bank_trans', $this->data);
                    break;
            }
            //break;
    
        }

    /////// delete

    function deleteTransaction()
    {
        $this->load->model('delete_model');
        $message = json_encode($this->delete_model->checkTransaction($th_id, $delete = null));
        echo $message;
    }

    function deleteAccount()
    {
        $this->load->model('delete_model');
        $message = json_encode($this->delete_model->checkAccount($acc_id, $delete = null));
        echo $message;
    }

    function deleteName()
    {
        $this->load->model('delete_model');
        $message = json_encode($this->delete_model->checkName($profile_id, $delete = null));
        echo $message;
    }

    ////////deposits

    public function getDefaultBankAccount($property_id)
    {
        $this->db->select('default_bank, accounts.name as name');
        $this->db->from('properties');
        $this->db->join('accounts', 'accounts.id = properties.default_bank');
        $this->db->where('properties.id', $property_id);
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $qresult = $q->row();
            return $qresult;
        } return $this->site->settings->default_bank;
    }

    // public function removeEmpty($transactions)
    // {
    //     $filled = [];
    //     //$i = 0;
    //     foreach ($transactions as &$transaction) {
    //         //$i++;
    //         foreach ($transaction as $value) {
    //             if ($value != "-1" AND $value != "" AND $value != '0.00') {
    //                 //$transaction['line_number'] = $i;
    //                 //$transaction['trans_id'] = $trans_id;
    //                 $filled[] = $transaction;
    //                 continue 2;
    //             }
    //         }
    //     }
    //     return $filled;
    // }

    function depositPayments($process = false)
    {
        $this->load->model('deposits_model');
        $header = $this->input->post('header');
        $account = $this->input->post('account_id');
        $deposits = $this->input->post('row');
        $od = $this->removeDetailCommas($this->input->post('transactions'));
        //$od = $this->removeEmpty($od);
        $ufProperties = array_keys($deposits);
        $otherProperties = array_filter($od, function($v) use($ufProperties){
            return  !in_array($v['property_id'], $ufProperties);
            });
        $properties = array_unique(array_column($otherProperties, 'property_id'));
        //$i = $process ? 1 : 0;
        $array = [];
        $fail = [];
        $error = false;
        $successes ='';
        
        $this->db->trans_start();

            if ($this->db->trans_status() === FALSE)
            {
                return false;
            }
        foreach($deposits as $key => $deposit){
            $property_id = $key;
            //$default_acc = $this->getDefaultBankAccount($property_id)->default_bank;
            $account_id = $account != -1  ? $account : $this->getDefaultBankAccount($property_id)->default_bank;
            $account_name = $account != -1  ? $account_name : $this->getDefaultBankAccount($property_id)->name;
            $undeposited = $deposit['undeposited'];
            $udAmount = array_sum($undeposited); 
            $udCount = count($undeposited);
            $otherDeposits = array_filter($od, function($v) use($property_id){
                return $v['property_id'] == $property_id;
                });
            $odAmount = array_sum(array_column($otherDeposits, 'credit'));
            $totalAmount = $udAmount + $odAmount;
            $balance_undeposited_funds = ['account_id' => $this->uf, 'property_id' => $property_id, 'credit' => $udAmount];
            $deposit_to = ['account_id' => $account_id, 'property_id' => $property_id, 'debit' => $totalAmount];
            if($process === false){
                $data = ['headerTransaction' => $deposit_to, 'header' => $header, 'transactions' => $otherDeposits, 'buf' => $balance_undeposited_funds, 'mode' => 'add'];
                $validate = $this->validate_model->validate("deposits", $data);
                if($validate['bool'] === false){//&& $validate['auth'] != 'pass',
                    $error = true;
                    $errors = str_replace('</br>', ' ', $validate['msg']);//'auth' => $validate['auth'],
                    array_push($array, $errors);
                } 
            }
            
            if($process){
                //$i =1;
                if($this->deposits_model->depositPayments($header, $balance_undeposited_funds, $deposit_to, $undeposited, $otherDeposits)) {
                        $smsg ="";
                        if ($udCount == 1){
                            $smsg =" ".$udCount." <small>payment for</small> ".number_format($udAmount,2);
                        } elseif ($udCount > 1) {
                            $smsg =" ".$udCount." <small>payments totaling</small> ".number_format($udAmount,2);
                        }

                        if ($udCount >0 and $odAmount <>0){
                            $smsg =$smsg." and " .number_format($odAmount,2). "<small>of other deposits were</small>";
                        } elseif ($odAmount <>0){
                            $smsg = number_format($odAmount,2)." <small>was";
                        }
                        $successes = $successes.$smsg." <small>succesfully deposited to</small> ".$account_name."<br/>";
 

                    //array_push($array, array('type' => 'success', 'message' => "Payments successfully deposited", 'msgInfo' => $property_id));
                    //echo json_encode(array('type' => 'success', 'message' => 'Payments successfully deposited.'));
                } else {
                    //$errors = str_replace('</br>', ' ', $validate['msg']);
                    //array_push($array, array('type' => 'danger', 'message' => 'Something went wrong', 'msgInfo' => $property_id));
                    array_push($fail,$property_id);
                }
            }
            
        }
        // need to do work if there is a property on bottom not on top
        // $ufProperties = array_keys($deposits);
        // $otherProperties = array_filter($od, function($v) use($ufProperties){
        //     return  !in_array($v['property_id'], $ufProperties);
        //     });
        // $properties = array_unique(array_column($otherProperties, 'property_id'));
        foreach($properties as $property_id){
            if(trim($property_id) != ""){
                $sepOtherDeposits = array_filter($otherProperties, function($v) use($property_id){
                    return $v['property_id'] == $property_id;
                });
                $account_id = $account != -1  ? $account : $this->getDefaultBankAccount($property_id)->default_bank;
                $sepOtherDepositsAmount = array_sum(array_column($sepOtherDeposits, 'credit'));
                $balance_undeposited_funds = ['account_id' => $this->uf, 'property_id' => $property_id, 'credit' => 0];
                $undeposited = NULL;
                $deposit_to = ['account_id' => $account_id, 'property_id' => $property_id, 'debit' => $sepOtherDepositsAmount];
                if($process === false){
                    $data = ['headerTransaction' => $deposit_to, 'header' => $header, 'transactions' => $sepOtherDeposits, 'buf' => $balance_undeposited_funds, 'mode' => 'add'];
                    $validate = $this->validate_model->validate("deposits", $data);
                    if($validate['bool'] === false){//&& $validate['auth'] != 'pass',
                        $error = true;
                    $errors = str_replace('</br>', ' ', $validate['msg']);//'auth' => $validate['auth'],
                    array_push($array, $errors);
                    } else {
                        $successes = $successes."Deposited ". $totalAmount ." to ".$account_id." <br/>";
                        
                    }
                }
                if($process){
                    //$i = 1;
                    if($this->deposits_model->depositPayments($header, $balance_undeposited_funds, $deposit_to, $undeposited, $sepOtherDeposits)) {
                        //array_push($array, array('type' => 'success', 'message' => "Payments successfully deposited", 'msgInfo' => $property_id));
                        //echo json_encode(array('type' => 'success', 'message' => 'Payments successfully deposited.'));
                    } else {
                        // $errors = str_replace('</br>', ' ', $validate['msg']);
                        // array_push($array, array('type' => 'danger', 'message' => $errors, 'msgInfo' => $property_id));
                        array_push($fail,$property_id);
                    }
                }
            }
        }

        $this->db->trans_complete();

        if($process){
            if(count($fail) < 1){
                echo json_encode(array('type' => 'success','auth' => $validate['auth'], 'message' => $successes));
            }else{
                $failed = implode(" ",$fail); 
                echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => "payments not deposited Something went wrong"));
            }
            return;
        }
        if($error === false){
            $process = true;
            //if($i < 1){
                $this->depositPayments($process);
            
            //}

        }else{
            $err = implode(" ",$array); 
            echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' =>'No payments deposited ' . $err));
        }
    }
    // public function getDefaultBankAccount()
    // {
    //     $this->db->select('default_bank');
    //     $this->db->from('properties');
    //     $this->db->where('id', $property_id);
        
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         return $q->row()->default_bank;
    //     } return $this->site->settings->default_bank;
    // }

    // function depositPaymentsNew()
    // {
    //     $this->load->model('deposits_model');
    //     $header = $this->input->post('header');
    //     $account_id = $this->input->post('account_id');
    //     $deposits = $this->input->post('deposits');

    //     foreach($deposits as $key => $deposit){
    //         $account_id = $account_id === 'not default' ? $account_id : getDefaultBankAccount();
    //         $property_id = $key;
    //         $amount = removeComma($this->input->post('amount'));
    //         //get total amount
    //         $appliedAmount = array_sum(array_column($appliedPayments, 'amount')); 
    //         $totalAmount = removeComma($this->input->post('totalAmount'));
    //         //get total totalAmount
    //         $undeposited = $deposit['undeposited'];
    //         $otherDeposits = $this->removeDetailCommas($deposit['transactions']);
    //         $balance_undeposited_funds = ['account_id' => $this->uf, 'property_id' => $property_id, 'credit' => $amount];
    //         $deposit_to = ['account_id' => $account_id, 'property_id' => $property_id, 'debit' => $totalAmount];
    //         $data = ['headerTransaction' => $deposit_to, 'header' => $header, 'transactions' => $otherDeposits, 'buf' => $balance_undeposited_funds, 'mode' => 'add'];
    //         $validate = $this->validate_model->validate("deposits", $data);
    //         // $this->validate_model->validation['bool'] = true;
    //         // $this->validate_model->validation['msg'] = '';

    //         //if(!empty($otherDeposits))$validate = $this->validate_model->validate("otherDeposits", $otherDeposits);
    //         if (($validate ? $validate['bool'] : 1) && $this->deposits_model->depositPayments($header, $balance_undeposited_funds, $deposit_to, $undeposited, $otherDeposits)) {
    //             array_push($array, array('type' => 'success', 'message' => "Payments successfully deposited", 'msgInfo' => $property_id));
    //             //echo json_encode(array('type' => 'success', 'message' => 'Payments successfully deposited.'));
    //         } else {
    //             $errors = str_replace('</br>', ' ', $validate['msg']);
    //             array_push($array, array('type' => 'danger', 'message' => $errors, 'msgInfo' => $property_id));
    //         }
    //     }
        
    //     echo json_encode($array);
    // }


    // function editDeposits($aid = 0)
    // {
    //     $this->load->model('deposits_model');
    //     $account_id = $this->input->post('account_id');
    //     $header = $this->input->post('header');
    //     //$property_id = $this->input->post('property_id');
    //     $deposits = $this->input->post('row');
    //     $otherDeposits = $this->removeDetailCommas($this->input->post('transactions'));
    //     //$check = $this->input->post('checked_id');
    //     $deletes = $this->input->post('delete');

    //     $this->db->select('id');
    //         $this->db->from('transactions');
    //         $this->db->where('trans_id', $header['id']);
    //         $this->db->order_by('id', 'ASC');
    //         $this->db->limit(2);
    //         $q = $this->db->get();
    //         if ($q->num_rows() > 0) {
    //             foreach (($q->result()) as &$row) {
    //                 $data[] = $row->id;
    //             }
    //         }
    //         $deposit_id = $data[0];
    //         $deposit_to_id = $data[1];

    //     foreach($deposits as $key => $deposit){
    //         $account_id = $account_id != -1  ? $account_id : $this->getDefaultBankAccount($property_id)->default_bank;
    //         $property_id = $key;
    //         $undeposited = $deposit['undeposited'];
    //         $udAmount = array_sum($undeposited); 
            
    //         $odAmount = array_sum(array_column($otherDeposits, 'credit'));
    //         $totalAmount = $udAmount + $odAmount;

            
    //         //just added id cause deposit to needs it for validation
    //         $balance_undeposited_funds = ['id' => $deposit_id, 'account_id' => $this->uf, 'property_id' => $property_id, 'credit' => $udAmount];
    //         $deposit_to = ['id' => $deposit_to_id, 'account_id' => $account_id, 'property_id' => $property_id, 'debit' => $totalAmount];
    //         $data = ['headerTransaction' => $deposit_to, 'header' => $header, 'transactions' => $otherDeposits, 'buf' => $balance_undeposited_funds, 'property_id' => $property_id, 'mode' => 'edit'];
    //         $validate = $this->validate_model->validate("deposits", $data);

    //         if (($validate ? $validate['bool'] : 1) && $this->deposits_model->editDeposits($header, $balance_undeposited_funds, $deposit_to, $undeposited, $otherDeposits, $deposit_id, $deposit_to_id, $deletes)) {//, $check
    //             echo json_encode(array('type' => 'success', 'message' => 'Deposits successfully updated.'));
    //         } else {
    //             $errors = $validate['msg'];
    //             echo json_encode(array('type' => 'danger', 'message' => $errors));
    //         }
    //     }

    //     // need to do work if there is a property on bottom not on top
    //     $ufProperties = array_keys($deposits);
    //     $otherProperties = array_filter($otherDeposits, function($v) use($ufProperties){
    //         return  !in_array($v['property_id'], $ufProperties);
    //         });
    //     $properties = array_unique(array_column($otherProperties, 'property_id'));
    //     foreach($properties as $property_id){
    //         if(trim($property_id) != ""){
    //             $sepOtherDeposits = array_filter($otherProperties, function($v) use($property_id){
    //                 return $v['property_id'] == $property_id;
    //             });
    //             $account_id = $account_id != -1  ? $account_id : $this->getDefaultBankAccount($property_id)->default_bank;
    //             $sepOtherDepositsAmount = array_sum(array_column($sepOtherDeposits, 'credit'));
    //             $balance_undeposited_funds = ['account_id' => $this->uf, 'property_id' => $property_id, 'credit' => 0];
    //             $undeposited = NULL;
    //             $deposit_to = ['account_id' => $account_id, 'property_id' => $property_id, 'debit' => $sepOtherDepositsAmount];
    //             $data = ['headerTransaction' => $deposit_to, 'header' => $header, 'transactions' => $sepOtherDeposits, 'buf' => $balance_undeposited_funds, 'property_id' => $property_id, 'mode' => 'edit'];
    //             $validate = $this->validate_model->validate("deposits", $data);
    //             if (($validate ? $validate['bool'] : 1) && $this->deposits_model->editDeposits($header, $balance_undeposited_funds, $deposit_to, $undeposited, $sepOtherDeposits, $deposit_id, $deposit_to_id, $deletes)) {//, $check
    //                 echo json_encode(array('type' => 'success', 'message' => 'Deposits successfully updated.'));
    //             } else {
    //                 $errors = $validate['msg'];
    //                 echo json_encode(array('type' => 'danger', 'message' => $errors));
    //             }
    //         }
            
    //     }
    // }

    function editDeposits($id, $process = false)
    {   
        $this->load->model('deposits_model');
        $header = $this->input->post('header');
        $account = $this->input->post('account_id');
        $deposits = $this->input->post('row');
        $deletes = $this->input->post('delete');
        if(!$deletes){
            $deletes = []; 
        }

        $od = $this->removeDetailCommas($this->input->post('transactions'));
        //$od = $this->removeEmpty($od);
        $ufProperties = array_keys($deposits);
        $otherProperties = array_filter($od, function($v) use($ufProperties){
            return  !in_array($v['property_id'], $ufProperties);
            });
        $properties = array_unique(array_column($otherProperties, 'property_id'));

        $this->db->trans_start();

        $this->db->select('id');
            $this->db->from('transactions');
            $this->db->where('trans_id', $header['id']);
            $this->db->order_by('id', 'ASC');
            $this->db->limit(2);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row->id;
                }
            }
            $deposit_id = $data[0];
            $deposit_to_id = $data[1];

            $fail = [];
            $error = false;
            $edit = true;
        
        foreach($deposits as $key => $deposit){
            $property_id = $key;
            $account_id = $account != -1  ? $account : $this->getDefaultBankAccount($property_id)->default_bank;
            $undeposited = $deposit['undeposited'];
            $udAmount = array_sum($undeposited); 
            $otherDeposits = array_filter($od, function($v) use($property_id){
                return $v['property_id'] == $property_id;
                });
            if(!$edit){
                foreach($otherDeposits as &$otherDeposit){
                    if($otherDeposit['id']){
                        array_push($deletes, $otherDeposit['id']);
                    }
                    unset($otherDeposit['id']);
                }
                
            }
            $odAmount = array_sum(array_column($otherDeposits, 'credit'));
            $totalAmount = $udAmount + $odAmount;
            $edit_balance_undeposited_funds = ['id' => $deposit_id, 'account_id' => $this->uf, 'property_id' => $property_id, 'credit' => $udAmount];
            $balance_undeposited_funds = ['account_id' => $this->uf, 'property_id' => $property_id, 'credit' => $udAmount];
            $edit_deposit_to = ['id' => $deposit_to_id, 'account_id' => $account_id, 'property_id' => $property_id, 'debit' => $totalAmount];
            $deposit_to = ['account_id' => $account_id, 'property_id' => $property_id, 'debit' => $totalAmount];
            if($process === false){
                if($edit){
                    $data = ['trans_id' => $header['id'], 'property' => $property_id, 'headerTransaction' => $edit_deposit_to, 'header' => $header, 'transactions' => $otherDeposits, 'buf' => $edit_balance_undeposited_funds, 'mode' => 'add'];
                }else{
                    $data = ['headerTransaction' => $deposit_to, 'header' => $header, 'transactions' => $otherDeposits, 'buf' => $balance_undeposited_funds, 'mode' => 'edit'];
                }
                $validate = $this->validate_model->validate("deposits", $data);
                if($validate['bool'] === false){//&& $validate['auth'] != 'pass',
                    $error = true;
                    $errors = str_replace('</br>', ' ', $validate['msg']);//'auth' => $validate['auth'],
                    array_push($array, $errors);
                }
            }
            
            if($edit){
                // $ids = $this->deposits_model->findDeletes($header['id'], $property_id, $deposit_id, $deposit_to_id);
                // if($ids){array_push($deletes,$ids);}
            }

            if($process){
                    if($edit){
                        //$this->deposits_model->deleteOld($header['id'], $property_id, $deposit_id, $deposit_to_id);
                        
                        $passed = $this->deposits_model->editDeposits($header, $edit_balance_undeposited_funds, $edit_deposit_to, $undeposited, $otherDeposits, $deposit_id, $deposit_to_id, $deletes);
                    }else{
                        $passed = $this->deposits_model->depositPayments($header, $balance_undeposited_funds, $deposit_to, $undeposited, $otherDeposits);
                    }
                   if($passed)  {
                    //array_push($array, array('type' => 'success', 'message' => "Payments successfully deposited", 'msgInfo' => $property_id));
                    //echo json_encode(array('type' => 'success', 'message' => 'Payments successfully deposited.'));
                } else {
                    //$errors = str_replace('</br>', ' ', $validate['msg']);
                    //array_push($array, array('type' => 'danger', 'message' => 'Something went wrong', 'msgInfo' => $property_id));
                    array_push($fail,$property_id);
                }
            }
            $edit = false;
        }
        // need to do work if there is a property on bottom not on top
        // $ufProperties = array_keys($deposits);
        // $otherProperties = array_filter($od, function($v) use($ufProperties){
        //     return  !in_array($v['property_id'], $ufProperties);
        //     });
        // $properties = array_unique(array_column($otherProperties, 'property_id'));
        foreach($properties as $property_id){
            if(trim($property_id) != ""){
                $sepOtherDeposits = array_filter($otherProperties, function($v) use($property_id){
                    return $v['property_id'] == $property_id;
                });
                if(!$edit){
                    foreach($sepOtherDeposits as &$otherDeposit){
                        if($otherDeposit['id']){
                            array_push($deletes, $otherDeposit['id']);
                        }
                        unset($otherDeposit['id']);
                        
                    }
                    
                }
                
                $account_id = $account != -1  ? $account : $this->getDefaultBankAccount($property_id)->default_bank;
                $sepOtherDepositsAmount = array_sum(array_column($sepOtherDeposits, 'credit'));
                $edit_balance_undeposited_funds = ['id' => $deposit_id, 'account_id' => $this->uf, 'property_id' => $property_id, 'credit' => 0];
                $balance_undeposited_funds = ['account_id' => $this->uf, 'property_id' => $property_id, 'credit' => 0];
                $undeposited = NULL;
                $edit_deposit_to = ['id' => $deposit_to_id, 'account_id' => $account_id, 'property_id' => $property_id, 'debit' => $sepOtherDepositsAmount];
                $deposit_to = ['account_id' => $account_id, 'property_id' => $property_id, 'debit' => $sepOtherDepositsAmount];
                if($process === false){
                    if($edit){
                        $data = ['trans_id' => $header['id'], 'property' => $property_id, 'headerTransaction' => $edit_deposit_to, 'header' => $header, 'transactions' => $sepOtherDeposits, 'buf' => $edit_balance_undeposited_funds, 'mode' => 'edit'];
                    }else{
                        $data = ['headerTransaction' => $deposit_to, 'header' => $header, 'transactions' => $sepOtherDeposits, 'buf' => $balance_undeposited_funds, 'mode' => 'add'];
                    }
                    
                    $validate = $this->validate_model->validate("deposits", $data);
                    if($validate['bool'] === false){//&& $validate['auth'] != 'pass',
                        $error = true;
                    $errors = str_replace('</br>', ' ', $validate['msg']);//'auth' => $validate['auth'],
                    array_push($array, $errors);
                    }
                }
                if($edit){
                    // $ids = $this->deposits_model->findDeletes($header['id'], $property_id, $deposit_id, $deposit_to_id);
                    // if($ids){array_push($deletes,$ids);}
                }
               
                if($process){
                    if($edit){
                        //$this->deposits_model->deleteOld($header['id'], $property_id, $deposit_id, $deposit_to_id);
                        
                        $passed = $this->deposits_model->editDeposits($header, $edit_balance_undeposited_funds, $edit_deposit_to, $undeposited, $sepOtherDeposits, $deposit_id, $deposit_to_id, $deletes);
                    }else{
                        $passed = $this->deposits_model->depositPayments($header, $balance_undeposited_funds, $deposit_to, $undeposited, $sepOtherDeposits);
                    }
                    if($passed) {
                        //array_push($array, array('type' => 'success', 'message' => "Payments successfully deposited", 'msgInfo' => $property_id));
                        //echo json_encode(array('type' => 'success', 'message' => 'Payments successfully deposited.'));
                    } else {
                        // $errors = str_replace('</br>', ' ', $validate['msg']);
                        // array_push($array, array('type' => 'danger', 'message' => $errors, 'msgInfo' => $property_id));
                            array_push($fail,$property_id);
                    }
                }
                $edit = false;
            }
            
        }


        if($deletes && $process === false){
            $data = ['deletes' => $deletes];
            $validate = $this->validate_model->validate("deposits", $data);
            if($validate['bool'] === false){//&& $validate['auth'] != 'pass',
                $error = true;
                $errors = str_replace('</br>', ' ', $validate['msg']);//'auth' => $validate['auth'],
                array_push($array, $errors);
            }
        }

        if($deletes && $process){
            //$this->deleteLines($deletes);
        }
        $this->db->trans_complete();

        if($process){
            if(count($fail) < 1){
                echo json_encode(array('type' => 'success','auth' => $validate['auth'], 'message' => 'Payments successfully updated.'));
            }else{
                $failed = implode(" ",$fail); 
                echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => "payments not deposited something went wrong"));
            }
            return;
        }
        if($error === false){
            $process = true;
            //if($i < 1){
                $this->editDeposits($id, $process);
            
            //}

        }else{
            $err = implode(" ",$array); 
            echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' =>'No payments deposited ' . $err.$validate['msg'].$errors));
        }
    }

    public function depositsgetModal()
    {
        $this->load->model('deposits_model');
        $this->load->model('creditCard_model');
        $params = json_decode($this->input->post('params'));
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
                $this->data['jPropertyAccounts'] = json_encode($this->creditCard_model->getPropertyAccounts2());
                $this->data['account_id'] = $params->account_id ? $params->account_id : '';
                $this->data['property_id'] = $params->property_id ? $params->property_id : $this->data['properties'][0]->id;
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);//not used anymore, using jsubaccounts
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->deposits_model->getUnits();
                $this->data['jUnits'] = json_encode($this->data['units']);//not used anymore, using jsubunits
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['jNames'] = json_encode($this->data['names']);
                $this->data['hasRecIdHtml'] = "";

                break;
            case 'edit' ://which values are we passing into the edit 
                $this->data['target'] = 'transactions/editDeposits/' . $this->input->post('id');
                $this->data['title'] = 'Edit Deposits';
                $this->data['edit'] = 'edit';
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
                $this->data['jPropertyAccounts'] = json_encode($this->creditCard_model->getPropertyAccounts2());

                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);//not used anymore, using jsubaccounts
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->deposits_model->getUnits();
                $this->data['jUnits'] = json_encode($this->data['units']);//not used anymore, using jsubunits
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['jNames'] = json_encode($this->data['names']);
                $this->data['jTransactions'] = json_encode($this->data['transactions']);
                $this->data['hasRecId'] = $this->data['transactions'][0]->deposit_id != null ? 'hasRecId' : '';
                $this->data['hasRecIdHtml'] = $this->data['transactions'][0]->deposit_id != null ? 'Cleared' : '';
                

                break;
        }

        $this->load->view('forms/deposit/main2', $this->data);
    }

    ////////journal entry

    function addJournalEntry($memorized = null)
    {
        $errors = "";
        $this->load->model('journalEntry_model');

        $header = $this->input->post('header');
        $transactions = $this->removeDetailCommas($this->input->post('transactions'));
        //$validate = $this->validate_model->calcTotal($transactions);
        $data = array('header' => $header, 'transactions' => $transactions);
        $validate = $this->validate_model->validate("journalEntry", $data);
        $this->form_validation->set_rules($this->settings->journalEntryFormValidation);
       
        if ($this->form_validation->run() && $validate['bool'] && $validate['auth'] =='pass') {
            $response = $this->journalEntry_model->addJournalEntry($header, $transactions);
            if ($response){
                if ($memorized) {
                   return $validate;
                } else {
                    echo json_encode(array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => 'Transaction  successfully added.', 'id' => $response));
                    return true; 
                }
            } 

        } else {
            $errors = $errors . validation_errors() . "</br>" . $validate['msg'];
            echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
            return false;
        }
    }

    function editJournalEntry($tid = 0, $memorized = null)
    {
        $errors = "";
        $this->load->model('journalEntry_model');
        $deletes = $this->input->post('delete');
        $header = $this->input->post('header');
        $transactions = $this->removeDetailCommas($this->input->post('transactions'));
        // $validate = $this->validate_model->calcTotal($transactions);
        // if($validate == false){$errors = "Totals don't match";}
        $data = array('header' => $header, 'transactions' => $transactions, 'deletes' => $deletes);
        $validate = $this->validate_model->validate("journalEntry", $data);
        $this->form_validation->set_rules($this->settings->journalEntryFormValidation);
        if ($this->form_validation->run() && $validate['bool'] && $validate['auth'] =='pass'  && $this->journalEntry_model->editJournalEntry($header, $transactions, $tid, $deletes)){
            if ($memorized) {
               return $validate;
            } else{echo json_encode(array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => 'Transaction successfully edited.'));}}
        else {
            $errors = $errors . validation_errors() . "</br>" . $validate['msg'];
            echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
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
                /*$this->data['properties'] = $this->journalEntry_model->getProperties();
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['accounts'] = $this->journalEntry_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);//not used anymore, using jsubaccounts
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->journalEntry_model->getUnits();
                $this->data['jUnits'] = json_encode($this->data['units']);//not used anymore, using jsubunits
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['names'] = $this->journalEntry_model->getNames();
                $this->data['jNames'] = json_encode($this->data['names']);
                $this->data['jPropertyAccounts'] = json_encode($this->checks_model->getPropertyAccounts2());*/
                $trans = $this->data['jTransactions'] = json_encode($this->data['transactions']);

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
                $this->data['edit'] = 'edit';
                $this->data['classes'] = $this->journalEntry_model->getClasses();
                /* $this->data['properties'] = $this->journalEntry_model->getProperties();
                $this->data['accounts'] = $this->journalEntry_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->journalEntry_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']); */
                $this->data['header'] = $this->journalEntry_model->getHeader($this->input->post('id'));
                $this->data['transactions'] = $this->journalEntry_model->getTransactions($this->input->post('id'));
               /*  $this->data['names'] = $this->journalEntry_model->getNames();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                $this->data['jUnits'] = json_encode($this->data['units']);
                $this->data['jNames'] = json_encode($this->data['names']); */
                $trans = $this->data['jTransactions'] = json_encode($this->data['transactions']);
                //$this->data['jPropertyAccounts'] = json_encode($this->checks_model->getPropertyAccounts2());

                //$test = $this->journalEntry_model->getTransactions($this->input->post('id'));

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

//     function applyBillPaymentsOld() 
//     {
//         $this->load->model('payBills_model');
//         $transactions = $this->input->post('transactions');

//         //$this->form_validation->set_rules($this->settings->accountFormValidation);
//         /*if ($this->form_validation->run() &&*/
//         $header = $this->input->post('header');

//         $i = 0;
//         $j = 0;
//         $validate = $this->validate_model->validate("date", $header['transaction_date']); 
//         if($validate['bool'] == false){
//             echo json_encode(array('type' => 'danger', 'message' => $validate['msg']));
//             $validate['bool'] = true;
//             $validate['msg'] = '';
//             return;
//         }

//         foreach($transactions as $key => &$transaction){
//             $key;
//             $account_id = $transaction[key($transaction)]['account_id'];
//             $property_id = $transaction[key($transaction)]['property_id'];
//             $profile_id = $transaction[key($transaction)]['profile_id'];

//             foreach($transaction as &$trans){
//                 $trans['amount'] = str_replace(',', '' , $trans['amount']); 
//             }

//             $total = array_sum(array_column($transaction, 'amount'));

//             //if($total > 0){

//             //$this->db->trans_start();

//             //$header = ['transaction_type' => 7,'transaction_ref' => 'not done!', 'transaction_date' => $date,'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];

//             $pmt_account =  ['account_id' => $account_id,'profile_id' => $profile_id, 'property_id' => $property_id, 'credit' => $total];
//             $accounts_payable = ['account_id' => $this->ap,'profile_id' => $profile_id, 'property_id' => $property_id, 'debit' => $total];
//     //['Totals' => $sdApplyAmount + $sdRefundAmount + $lmrApplyAmount + $lmrRefundAmount]
//             $data = ['header' => $header, 'transactions' =>  [0 => $pmt_account]];
//             $validate = $this->validate_model->validate("payBills", $data); 
//             if($validate['bool'] == false){
//                 $errors .= $validate['msg'];
//                 $j++;
//                 $validate['bool'] = true;
//                 $this->validate_model->validation['bool'] = true;
//                 $this->validate_model->validation['msg'] = '';
//                 continue;
//             } 
//             if($validate['bool'] == true){
//                 $this->payBills_model->applyPayments($header, $transaction, $pmt_account, $accounts_payable);
//                 $i++;
//             }
//             //continue;

//    // }
//         }

//     if ($i > 0){
//         //($this->form_validation->run() && $validate['bool'] && $this->payBills_model->applyPayments( $sqlDate , $transactions)){
//             $msg = $j ? ", $j not added <br>$errors" : '';
//             echo json_encode(array('type' => 'success', 'message' => "$i Transactions successfully added" . $msg));
//     }
//         else {
//             $errors = $validate['msg'];
//             //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
//             echo json_encode(array('type' => 'danger', 'message' => "No transactions added <br> $errors"));
//         }
//     }

    //  function test(){
    //     $this->db->select(' *,CASE 
    //     WHEN (transactions.account_id ='. $this->site->settings->accounts_receivable .')  THEN  ((transactions.debit - transactions.credit) - ap.amount)
    //     WHEN (transactions.account_id =' . $this->site->settings->accounts_payable . ') THEN ((transactions.credit - transactions.debit) - ap.amount)
    //     END AS difference');
    //     $this->db->from('transactions');
    //     $this->db->join('(select tid, sum(amount) as amount from (select transaction_id_a as tid, 0-amount as amount from applied_payments 
    //     UNION select transaction_id_b as tid, amount as amount from applied_payments) as ap1 group by tid
    //     ) ap','transactions.id = ap.tid','left');
    //     $this->db->where('(CASE WHEN (transactions.account_id = 85) THEN ap.amount != (transactions.debit - transactions.credit)
    //     WHEN (transactions.account_id = 86) THEN ap.amount != (transactions.credit - transactions.debit) END)');
    //     $q = $this->db->get_compiled_select();
    //     echo $q;
    //  }

    function applyBillPayments()
    {
        $this->load->model('payBills_model');
        $transactions = $this->input->post('transactions1');
        $print = $this->input->post('printPayBillChecks');
        $header = $this->input->post('header');

        $validate = $this->validate_model->validate("date", $header['transaction_date']);
        if ($validate['bool'] == false) {
            echo json_encode(array('type' => 'danger', 'message' => $validate['msg']));
            $validate['bool'] = true;
            $validate['msg'] = '';
            return;
        }
        $checks = [];
        $array = [];
        foreach ($transactions as $key => &$transaction) {
            $account_id = $transaction[key($transaction)]['account_id'];
            $property_id = $transaction[key($transaction)]['property_id'];
            $profile_id = $transaction[key($transaction)]['profile_id'];

            foreach ($transaction as &$trans) {
                $trans['amount'] = str_replace(',', '', $trans['amount']);
            }

            $total = array_sum(array_column($transaction, 'amount'));

            $pmt_account = ['account_id' => $account_id, 'profile_id' => $profile_id, 'property_id' => $property_id, 'credit' => $total];
            $accounts_payable = ['account_id' => $this->ap, 'profile_id' => $profile_id, 'property_id' => $property_id, 'debit' => $total];

            $data = ['header' => $header, 'transactions' => [0 => $pmt_account], 'transaction' => $transaction, 'mode' => 'add', 'print' => $print];
            $validate = $this->validate_model->validate("payBills", $data);

            if ($validate['bool'] && $validate['auth'] =='pass' ) {
                $checksInfo = $this->payBills_model->applyPayments($header, $transaction, $pmt_account, $accounts_payable, $print);
                if ($checksInfo) {
                    array_push($array, array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => "Transactions successfully added", 'app' => $key));
                    if ($print) array_push($checks, $checksInfo);
                } else {
                    array_push($array, array('type' => 'danger','auth' => $validate['auth'], 'message' => "Transaction not added Something went wrong", 'app' => $key));
                }
            } else {
                $errors = str_replace('</br>', ' ', $validate['msg']);
                array_push($array, array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => "Transaction not added <br> $errors", 'app' => $key));
                $validate['bool'] = true;
                $this->validate_model->validation['bool'] = true;
                $this->validate_model->validation['msg'] = '';
            }
        }
        $array['checks'] = $checks;
        echo json_encode($array);
        //echo json_encode($checks);

    }

    function printBillPayment()
    {
        $this->load->model('payBills_model');
        $info = $this->input->post();
        $info = json_decode($info['params'], true);
        $a = $info[0];
        $th_id = $a['th_id'];
        $account_id = $a['account_id'];
        $single = $a['single'];

        $checksInfo = $this->payBills_model->onPrintMany($th_id, $account_id, null, $single );
        echo json_encode($checksInfo);
    }

    function editBillPayments($aid = 0)
    {
        $this->load->model('payBills_model');
        $header = $this->input->post('header');
        $amount = $this->input->post('amount');
        $payedFrom = $this->input->post('bank');
        $property_id = $this->input->post('property');
        $profile_id = $this->input->post('vendor');
        $class = $this->input->post('class');
        $applied_payments = $this->input->post('applied_payments');
        $printEditBill = $this->input->post('saveAndPrint');

        $applied_payments = array_filter($applied_payments, function ($v) {
            return $v['applied'] == 1;
        });

        foreach ($applied_payments as &$applied_payment) {
            $applied_payment['amount'] = str_replace(',', '', $applied_payment['amount']);
        }

        $this->db->select('id');
        $this->db->from('transactions');
        $this->db->where(array('trans_id' => $header['id'], 'account_id !=' => $this->ap));
        $q = $this->db->get();
        //if ($q->num_rows() > 0) {
        $pmt_acct_id = $q->row()->id;
        $this->db->reset_query();

        $this->db->select('id');
        $this->db->from('transactions');
        $this->db->where(array('trans_id' => $header['id'], 'account_id' => $this->ap));
        $q = $this->db->get();
        //if ($q->num_rows() > 0) {
        $acct_p_id = $q->row()->id;
        $this->db->reset_query();

        $accounts_payable = ['profile_id' => $profile_id, 'property_id' => $property_id, 'debit' => $amount, 'class_id' => $class];
        $pmt_account = ['account_id' => $payedFrom, 'profile_id' => $profile_id, 'property_id' => $property_id, 'credit' => $amount, 'class_id' => $class];

        $validate1 = $this->validate_model->validate("date", $header['transaction_date']);
        $data = ['header' => $header, 'transactions' => [0 => $pmt_account], 'applied_payments' => $applied_payments, 'transaction' => $applied_payments, 'mode' => 'edit', 'transaction_id_a' => $acct_p_id];
        $validate2 = $this->validate_model->validate("payBills", $data);

        if ($validate1['bool'] && $validate2['bool'] && $validate2['auth'] =='pass' ) {
            $checksInfo = $this->payBills_model->editAppliedPayments($header, $pmt_account, $accounts_payable, $pmt_acct_id, $acct_p_id, $applied_payments, $printEditBill);
            if ($checksInfo) {
                $array = ['type' => 'success', 'message' => "Transaction successfully added"];
                if ($printEditBill) $array['checks'] = $checksInfo;
                echo json_encode($array);
            } else {
                echo json_encode(array('type' => 'danger','auth' => $validate2['auth'], 'message' => "Transaction not added Something went wrong"));
            }
        } else {
            $errors = $validate1['msg'] . " " . $validate2['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger','auth' => $validate2['auth'], 'message' => $errors));
            //echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    public function payBillsgetModal()
    {
        $this->load->model('payBills_model');
        $vendorId = $this->input->post('id');
        if($vendorId != ""){
            $vendorId = $vendorId;
            $propertyId = null;
        }else{
            $vendorId = null;
            $propertyId = 1;
        }
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'transactions/applyBillPayments';
                $this->data['title'] = 'Pay Bills';
                $this->data['vendors'] = $this->payBills_model->getVendor();
                $this->data['paymentMethods'] = $this->payBills_model->getPaymentMethods();
                $this->data['bankAccounts'] = $this->payBills_model->getBanks();
                $this->data['CcAccounts'] = $this->payBills_model->getCC();
                $this->data['transactions'] = $this->payBills_model->getTransactions(null, $vendorId, $propertyId);
                $this->data['jTransactions'] = json_encode($this->data['transactions']);
                $this->data['accounts'] = $this->payBills_model->getAccounts();
                $this->data['properties'] = $this->payBills_model->getProperties();

                $this->load->view('forms/vendor/main5', $this->data);
                break;
            case 'edit' ://which values are we passing into the edit 
                $this->data['target'] = 'transactions/editBillPayments/' . $this->input->post('id');
                $this->data['title'] = 'Edit Payed Bills';
                $this->data['edit'] = 'edit';
                $this->data['bankAccounts'] = $this->payBills_model->getBanks();
                $this->data['accounts'] = $this->payBills_model->getAccounts();
                $this->data['vendors'] = $this->payBills_model->getVendor();
                $this->data['properties'] = $this->payBills_model->getProperties();
                $this->data['header'] = $this->payBills_model->getHeaderEdit($this->input->post('id'));
                $this->data['transactions'] = $this->payBills_model->getTransactionsEdit($this->input->post('id'));
                $recInfo = $this->payBills_model->getRecInfo($this->input->post('id'));
                //$headerTransaction = array_shift($recInfo);
                $this->data['classes'] = $this->payBills_model->getClasses();
                // $this->data['hasRecId'] = $headerTransaction->rec_id != null ? 'hasRecId' : '';
                // $this->data['hasRecIdHtml'] = $headerTransaction->rec_id != null ? 'Cleared' : '';
                $this->data['hasRecId'] = $recInfo->rec_id != null && $recInfo->clr == 1 ? 'hasRecId' : '';
                $this->data['hasRecIdHtml'] = $recInfo->rec_id != null && $recInfo->clr == 1 ? 'Cleared' : '';
                $this->data['RecIdHtml'] = $recInfo->rec_id != null && $recInfo->clr == 1 ? 'Cleared on Rec# <span data-id="50" class="reportLink" rtype="report" defaults="'.$recInfo->rec_id.'"><a href="#" >'.$recInfo->rec_id.'</a></span>' : '';

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

    public function payBillsgetData() {
        $this->load->model('payBills_model');
        $data = $this->payBills_model->getTransactionsSlick();
        $columns = Array();
        $columns[] = Array("id" => "check", "name" => '<label for="pay_bill_select_all" class="custom-checkbox"><input type="checkbox" class="hidden" id="pay_bill_select_all"><div class="input"></div></label>', "field" => "check", "width" => 55, "sortable" => false, "resizable" => false, "formatter" => "CheckFormatter");
        $columns[] = Array("id" => "vendor", "name" => "Vendor", "field" => "vendor", "sortable" => true, "resizable" => true);
        $columns[] = Array("id" => "account_id", "name" => "Pmt Account", "field" => "account_id", "sortable" => false, "resizable" => true, "formatter" => "SelectFormatter", "asyncPostRender" => "renderSelect", "source" => "account", "namefield" => "bank_name");
        $columns[] = Array("id" => "property", "name" => "Property", "field" => "name", "sortable" => true, "resizable" => true);
        $columns[] = Array("id" => "reference", "name" => "Reference", "field" => "transaction_ref", "sortable" => true, "resizable" => true);
        $columns[] = Array("id" => "transaction_date", "name" => "Bill Date", "field" => "transaction_date", "sortable" => true, "resizable" => true, "formatter" => "DateFormatter", "datatype" => "date");
        $columns[] = Array("id" => "due_date", "name" => "Due Date", "field" => "due_date", "sortable" => true, "resizable" => true, "formatter" => "DateFormatter", "datatype" => "date");
        $columns[] = Array("id" => "bill_amount", "name" => "Bill Amount", "field" => "bill_amount", "sortable" => true, "resizable" => true, "total" => true, "formatter" => "UsdFormatter", "datatype" => "num");
        $columns[] = Array("id" => "open_balance", "name" => "Open Balance", "field" => "open_balance", "sortable" => true, "resizable" => true, "total" => true, "formatter" => "UsdFormatter", "datatype" => "num");
        $columns[] = Array("id" => "amount", "name" => "Pmt Amount", "field" => "amount", "sortable" => false, "resizable" => true, "formatter" => "InputFormatter", "format" => "usd", "asyncPostRender" => "renderInput", "valuefield" => "open_balance", "total" => true);

        echo json_encode(Array("data" => $data, "columns" => $columns));
    }

    //////receive payment

    function applyReceivedPayments()
    {
        $this->load->model('receivePayments_model');
        $lease_info =$this->input->post('profile');

        $account_id = $this->input->post('account_id');
        $profile_id = $this->input->post('profile_id');
        $property_id = null !== $this->input->post('property_id') ? $this->input->post('property_id'): $lease_info['prop_id'];
        $unit_id = null !==  $this->input->post('unit_id') ? $this->input->post('unit_id'): $lease_info['unit_id'];
        $lease_id = null !==  $this->input->post('lease_id') ? $this->input->post('lease_id'): $lease_info['lease_id'] ;
        $amount = removeComma($this->input->post('amount'));
        $header = $this->input->post('header');
        $customerPayments = $this->input->post('customer_payments');
        $applied_payments = $this->input->post('applied_payments');

        $validate = $this->validate_model->validate("restricted", ['lease_id' => $lease_id, 'profile_id' => $profile_id]);
        if ($validate['bool'] == false) {
            echo json_encode(array('type' => 'danger', 'message' => $validate['msg']));
            $validate['bool'] = true;
            $validate['msg'] = '';
            return;
        }

        $accounts_receivable = ['account_id' => $this->ar, 'lease_id' => $lease_id, 'property_id' => $property_id, 'profile_id' => $profile_id, 'unit_id' => $unit_id, 'credit' => $amount];
        $deposit_to = ['account_id' => $account_id, 'lease_id' => $lease_id, 'property_id' => $property_id, 'profile_id' => $profile_id, 'unit_id' => $unit_id, 'debit' => $amount];

        $data = ['header' => $header, 'transactions' => [0 => $deposit_to], 'applied_payments' => $applied_payments];
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        $validate = $this->validate_model->validate("receivePayments", $data);
        if ($validate['bool'] && $validate['auth'] =='pass' && /*$this->form_validation->run() &&*/
            $this->receivePayments_model->applyPayments($header, $customerPayments, $applied_payments, $accounts_receivable, $deposit_to)) {
            echo json_encode(array('type' => $validate['type'], 'message' => 'Payment successfully applied.'));
            if($this->site->settings->email_payment_notices == 1){
                $data = (object)$header;
                $data->data = (object)$accounts_receivable;
                    $this->sendEmail($data, 'p');               
            }
        } else {
            $errors = $validate['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors));
            //echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('account')));
        }
    }

    function editReceivedPayments($aid = 0)
    {
        $this->load->model('receivePayments_model');
        $lease_info = $this->input->post('profile');
        $account_id = $this->input->post('account_id');
        $profile_id = $this->input->post('profile_id');
        $lease_id = $lease_info['lease_id'];
        $property_id = $lease_info['prop_id'];
        $unit_id = $lease_info['unit_id'];
        $amount = $this->input->post('amount');
        $header = $this->input->post('header');
        $customerPayments = $this->input->post('customer_payments');
        $applied_payments = $this->input->post('applied_payments');
        $transaction_id_a = $this->input->post('transaction_id_a');

        $accounts_receivable = ['id' => $transaction_id_a, 'profile_id' => $profile_id, 'lease_id' => $lease_id, 'property_id' => $property_id, 'unit_id' => $unit_id, 'credit' => $amount];
        $deposit_to = ['account_id' => $account_id, 'profile_id' => $profile_id, 'lease_id' => $lease_id, 'property_id' => $property_id, 'unit_id' => $unit_id, 'debit' => $amount];

        $this->db->select('t.id, t.account_id, t.deposit_id, t.debit, t.rec_id, th.transaction_date, t.profile_id, t.lease_id');
        $this->db->from('transactions t');
        $this->db->join('transaction_header th', 't.trans_id = th.id');
        $this->db->join('customer_payments cp', 't.trans_id = th.id');
        $this->db->where(array('t.trans_id' => $header['id'], 't.account_id !=' => $this->ar));
        $q = $this->db->get();
        //if ($q->num_rows() > 0) {
        $id = $q->row()->id;
        $dt_id = $q->row()->deposit_id;
        $curAcct = $q->row()->account_id;
        $curAmnt = $q->row()->debit;
        $curDate = $q->row()->transaction_date;
        $recId = $q->row()->rec_id;
        $this->db->reset_query();

        $data = ['header' => $header, 'transactions' => [0 => $deposit_to], 'applied_payments' => $applied_payments, 'deposit_id' => $dt_id, 'curAcct' => $curAcct, 'curAmnt' => $curAmnt, 'curDate' => $curDate, 'recId' => $recId, 't2'=> $id];
        $validate = $this->validate_model->validate("receivePayments", $data);
        if ($validate['bool'] && $validate['auth'] =='pass' && $this->receivePayments_model->editAppliedPayments($header, $customerPayments, $applied_payments, $accounts_receivable, $deposit_to, $id)) {
            echo json_encode(array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => 'Applied payment successfully updated.'));
        } else {
            $errors = $validate['msg'];
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => $errors));
            //echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('account')));
        }
    }

    public function receivePaymentsgetModal()// not complete see my tasks in asana
    {
        $this->load->model('receivePayments_model');
        $params = json_decode($this->input->post('params'));
        $this->data['payment_methods'] = $this->settings->payment_types;

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
                $this->data['header']->profile_id = $params->profile;

                if ($params->lease) {
                    $this->data['lease_id'] = $params->lease;
                }
                if ($params->profile) {
                    $this->data['header']->profile_id = $params->profile;
                }


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
                $this->data['edit'] = 'edit';
                //$this->data['leases'] = $this->receivePayments_model->getLeases();
                //$this->data['tenants'] = $this->receivePayments_model->getTenants();
                $this->data['transaction_types'] = $this->receivePayments_model->getTransactionType();
                $this->data['accounts'] = $this->receivePayments_model->getDepositTo();
                $this->data['header'] = $this->receivePayments_model->getHeaderEdit($this->input->post('id'));
                //$this->data['transactions'] = $this->receivePayments_model->getTransactionsEdit($this->input->post('id'),$params->lease, $params->profile);
                // $this->data['header']->id = $this->input->post('id');
                // $this->data['header']->profile_id =  $params->profile;
                //$this->data['profile'] = $this->receivePayments_model->getProfile($params->profile);
                //$this->data['lease'] = $params->lease;
                $dataDebug =  $this->data['header'];
                $this->data['lease_id'] = $dataDebug->lease_id;
                //$this->data['profileI'] = $params->profile;
                //$profile_id = $params->profile ? $params->profile : $this->receivePayments_model->getProfileId($this->input->post('id'));
                //$this->data['properties'] = $this->receivePayments_model->getProperties($profile_id);
                $this->data['hasDepositId'] = (($this->data['header']->tdeposit_id != null) || ($this->data['header']->deposit_id != null)) ? 'hasDepositId' : '';
                $this->data['hasDepositIdHtml'] = (($this->data['header']->tdeposit_id != null) || ($this->data['header']->deposit_id != null)) ? 'Deposited' : '';
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

    function getReceivePaymentsTransactions()
    {
        $this->load->model('receivePayments_model');
        if ($this->input->get('id')) {
            $transactions = $this->receivePayments_model->getTransactionsEditNew($this->input->get('id'), $this->input->get('lease'), $this->input->get('profile'));
        } else {
            $transactions = $this->receivePayments_model->getTransactions($this->input->get('lease'), $this->input->get('profile'));
        }

        //echo $transactions;
        echo json_encode(array('transactions' => $transactions));
    }

/////utilities
    // function recordUtilitiesOld()
    // {
    //     $this->load->model('utilities_model');

    //     $rows = $this->input->post('row');

    //     $insert = 0;
    //     $fail = 0;

    //     foreach ($rows as $key => &$row) {
    //         $utility_id = $key;
    //         $validate = $this->validate_model->validate("utilities", $row);
    //         if ($validate['bool'] == false) {
    //             $errors .= $validate['msg'];
    //             $fail++;
    //             $validate['bool'] = true;
    //             $this->validate_model->validation['bool'] = true;
    //             $this->validate_model->validation['msg'] = '';
    //             continue;
    //         }
    //         if ($validate['bool'] == true) {
    //             $this->utilities_model->recordUtilityBill($row, $utility_id);
    //             $insert++;
    //         }
    //     }
    //     if ($insert > 0) {
    //         //($this->form_validation->run() && $validate['bool'] && $this->payBills_model->applyPayments( $sqlDate , $transactions)){
    //         $msg = $fail ? ", $fail not added <br>$errors" : '';
    //         echo json_encode(array('type' => 'success', 'message' => "$insert Transactions successfully added" . $msg));
    //     } else {
    //         //$errors = $validate['msg'];
    //         //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
    //         echo json_encode(array('type' => 'danger', 'message' => "No transactions added <br> $errors"));
    //     }
    // }

    function recordUtilities()
    {
        $this->load->model('utilities_model');
        $rows = $this->input->post('row');
        $header = $this->input->post('header');

        $array = [];

        foreach ($rows as $key => &$row) {
            $utility_id = $key;
            if($header){$row = $row + $header;}
            $validate = $this->validate_model->validate("utilities", $row);

            if ($validate['bool'] && $validate['auth'] =='pass' && $this->utilities_model->recordUtilityBill($row, $utility_id)) {
                array_push($array, array('type' => $validate['type'],'auth' => $validate['auth'], 'message' => "Transaction successfully added", 'msgInfo' => $utility_id));
                //$array['auth'] = $validate['auth'];
            } else {
                $errors = str_replace('</br>', ' ', $validate['msg']);
                array_push($array, array('type' => 'danger','auth' => $validate['auth'], 'message' => $errors, 'msgInfo' => $utility_id));
                //$array['auth'] = $validate['auth'];
                $validate['bool'] = true;
                $this->validate_model->validation['bool'] = true;
                $this->validate_model->validation['msg'] = '';
            }
        }
        
        echo json_encode($array);
            
        }

        function deleteUtilities(){
            $this->load->model('utilities_model');
            $utilities = $this->input->post();
            //$arr = array();
            $arr['9'] = array();
            foreach($utilities['row'] as $row => $value){
                //$arr = array_merge($arr['9'], array($row => $row));
                //array_push($arr['9'], $row => $row);
                $arr[9] += array($row => $row);
            }
            
            $delete = $this->input->post('confirm');
            if($arr && $delete == NULL){
                $response = $this->utilities_model->deleteUtilities($arr, $delete);
                echo json_encode(array('type' => 'warning','auth' => $validate['auth'], 'message' => $response));
                return;
            }
            if ($this->utilities_model->deleteUtilities($arr, $delete)){//$deletes, $delete
                echo json_encode(array('type' => 'success','auth' => $validate['auth'], 'message' => 'Utilities successfully deleted.'));
            }else {
                    echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('utilities')));
                }
            
        }

        function addUtility(){
            $this->load->model('utilities_model');
            $utility = $this->input->post('utility');
            $validate = $this->validate_model->validate("addUtility", $utility);
            if ($validate['bool'] && $this->db->insert('utilities', $utility)){
                $uid = $this->db->insert_id();
                echo json_encode(array('type' => $validate['type'],'auth' => $validate['auth'], 'nutil' => $uid, 'message' => 'Utility successfully added.'));
            }else {
                $errors = validation_errors() ."</br>". $validate['msg'];
                echo json_encode(array('type' => 'danger','auth' => $validate['auth'], 'message' => $errors, 'errors' => $this->parse_errors('utilities')));
            }
        }
        function updateUtility(){
            //$this->db->update('utilities', $utility)
            //$this->db->update('utilities',$type, array('id' => $id));
            $value = $this->input->post('value');
            $type = $this->input->post('type');
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $this->db->update('utilities', array($type => $value));
        }
        

    ////utilities grid
    public function utilitiesGridgetModal()
    {
        $this->load->model('creditCard_model');
        $this->load->model('properties_model');
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
                $this->data['jUnits'] = json_encode($this->data['units']);
                $this->data['jPropertyAccounts'] = json_encode($this->creditCard_model->getPropertyAccounts2());
                $this->data['names'] = $this->creditCard_model->getProfiles();
                $this->data['jNames'] = json_encode($this->data['names']);
                $this->data['utilityTypes'] = $this->properties_model->getutilityTypes();
                $this->data['accounts'] = $this->properties_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->load->view('forms/utilities/main2', $this->data);
                break;
            case 'edit' :
                $this->data['target'] = 'transactions/utilitiesForm/' . $this->input->post('id');
                $this->data['title'] = 'Utilities Form';
                $this->data['edit'] = 'edit';
                break;
            case 'addNew' :
                $this->data['target'] = 'transactions/addUtility/';
                $this->data['utilityTypes'] = $this->properties_model->getutilityTypes();
                $this->data['accounts'] = $this->properties_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['units'] = $this->creditCard_model->getUnits();
                $this->data['vendors'] = $this->properties_model->getVendors();
                $this->load->view('forms/utilities/new', $this->data);
                break;
        }
        
    }

    public function getTrDetailsPopup($type, $id)
    {
        $query = "SELECT  transaction_header.id AS THIDOLD, b.oldid AS THID, b.type_id, b.transaction_id_a AS payment_id, b.transaction_id_b AS bill_id, b.type AS TTYPE, b.applied AS applied, b.Bill AS Bill, b.transaction_date AS PDATE, b.account AS ACCT, b.transaction_ref AS REF 
            FROM
            (SELECT  ta.trans_id, t.trans_id as oldid, tt.id AS type_id, tt.name AS type, ap.transaction_id_a, ap.transaction_id_b, th.transaction_date, ap.amount AS Applied, (t.debit + t.credit) AS Bill, IF(a.ct > 1, 'Split',accounts.name) AS account, th.transaction_ref
            FROM applied_payments ap 
            JOIN transactions t ON ap.transaction_id_b = t.id
            JOIN(
            SELECT trans_id, COUNT(*) AS ct , SUBSTRING_INDEX(GROUP_CONCAT(account_id SEPARATOR '#'),'#',1) AS account_id
            FROM
            (SELECT account_id,trans_id FROM transactions CROSS JOIN company_settings
            WHERE account_id NOT IN (company_settings.accounts_payable, company_settings.accounts_receivable)
            )b GROUP BY trans_id)a ON a.trans_id = t.trans_id
            JOIN transaction_header th ON t.trans_id = th.id
            JOIN accounts ON a.account_id = accounts.id
            JOIN transaction_type tt ON th.transaction_type = tt.id
            JOIN transactions ta ON ap.transaction_id_a = ta.id) b
            JOIN transaction_header ON b.trans_id = transaction_header.id WHERE b.trans_id = ".$id;
        $q = $this->db->query($query);
        $result = $q->result();
        $query = "SELECT transaction_header.id AS THIDOLD, b.oldid AS THID, b.type_id, b.transaction_id_a AS payment_id, b.transaction_id_b AS bill_id, b.type AS TTYPE, b.applied AS applied, b.Bill AS Bill, b.transaction_date AS PDATE, b.account AS ACCT, b.transaction_ref AS REF 
            FROM
            (SELECT  ta.trans_id, t.trans_id as oldid, tt.id AS type_id, tt.name AS type, ap.transaction_id_a, ap.transaction_id_b, th.transaction_date, ap.amount AS Applied, (t.debit + t.credit) AS Bill, IF(a.ct > 1, 'Split',accounts.name) AS account, th.transaction_ref
            FROM applied_payments ap 
            JOIN transactions t ON ap.transaction_id_a = t.id
            JOIN(
            SELECT trans_id, COUNT(*) AS ct , SUBSTRING_INDEX(GROUP_CONCAT(account_id SEPARATOR '#'),'#',1) AS account_id
            FROM
            (SELECT account_id,trans_id FROM transactions CROSS JOIN company_settings
            WHERE account_id NOT IN (company_settings.accounts_payable, company_settings.accounts_receivable)
            )b GROUP BY trans_id)a ON a.trans_id = t.trans_id
            JOIN transaction_header th ON t.trans_id = th.id
            JOIN accounts ON a.account_id = accounts.id
            JOIN transaction_type tt ON th.transaction_type = tt.id
            JOIN transactions ta ON ap.transaction_id_b = ta.id) b
            JOIN transaction_header ON b.trans_id = transaction_header.id WHERE b.trans_id = ".$id;
        $q = $this->db->query($query);
        $result = array_merge($result, $q->result());
        if(count($result) == 0) {
            echo 'No connected transactions';
            return;
        }
        echo'<style>.ttt td{text-align:center;cursor:pointer;} .ttt tr{padding:5px;}</style>';
        echo "<table width='100%' cellspacing='15' class='ttt'>";
        echo"<tr><th>Type</th><th>Applied</th><th>Amount</th><th>Date</th><th>Account</th><th>Reference</th></tr>";
        foreach($result as $t) {
            echo"<tr data-mode='edit' data-type='{$t->type_id}' data-id='{$t->THID}'>";
            echo"<td>{$t->TTYPE}</td><td>{$t->applied}</td><td>{$t->Bill}</td><td>{$t->PDATE}</td><td>{$t->ACCT}</td><td>{$t->REF}</td>";
            echo"</tr>";
        }
        echo "</table>";
    }

    public function quickAdd(){
        $alldata = $this->input->post();
        $bankTransId = $this->input->post('bank_trans_id');
        echo $bankTransId;
    }

    public function getAuditTrailPopup($type, $id)
    {
        $trans = "SELECT transactions_snapshot.trans_snapshot_id as hsi,transactions_snapshot.id as tsi, transactions_snapshot.trans_id,transactions_snapshot.transaction_id, transactions_snapshot.debit as debit, transactions_snapshot.credit as credit, transactions_snapshot.description as description, accounts.name AS account, properties.name as property 
        ,  transactions_snapshot.class_id, transactions_snapshot.description, CONCAT_WS(' ',profiles.first_name, profiles.last_name) AS profile
        from transactions_snapshot 
        join accounts on transactions_snapshot.account_id = accounts .id
        join properties on transactions_snapshot.property_id = properties.id
        left join profiles on transactions_snapshot.profile_id = profiles.id
        where transactions_snapshot.trans_id = ".$id;

        $header ="SELECT transaction_header_snapshot.id as hsi,transaction_header_snapshot.transaction_header_id,  transaction_header_snapshot.transaction_date as date, CONCAT(DATE_FORMAT(transaction_header_snapshot.last_mod_date, '%m/%d/%Y'),' ',TIME_FORMAT(transaction_header_snapshot.last_mod_date, '%r')) AS modified, transaction_header_snapshot.last_mod_date as lm, CONCAT_WS(' ',profiles.first_name, profiles.last_name) AS user, transaction_type.name AS TTYP
        , transaction_header_snapshot.transaction_ref as ref, transaction_header_snapshot.memo  as memo
        from  transaction_header_snapshot  
        join transaction_type on transaction_header_snapshot.transaction_type = transaction_type.id
        left join users on transaction_header_snapshot.last_mod_by = users.id
        left join profiles on  users.profile_id = profiles.id where transaction_header_snapshot.transaction_header_id = ".$id." order by transaction_header_snapshot.last_mod_date DESC" ;

        $q = $this->db->query($header);
        $result =  $q->result();
        if(count($result) == 0) {
            echo 'Nothing was changed';
            return;
        }
        $q2 = $this->db->query($trans);
        $trns =  $q2->result();


        echo "<style> .atlist{padding-left:0px} .ttt td{text-align:center;cursor:pointer;} .ttt2{border-top:none} .ttt2 th{font-size:1.1em; } .ttt2 td{text-align:center;cursor:pointer; border-none} .ttt tr{padding:5px;} .atList li{padding:10px;} .atList{list-style-type:none; max-height:calc(60vh); overflow:auto;}</style><h4 style = 'text-align: center;  line-height: 2;border-radius: 6px}'>Audit Trail</h4><ul class = 'atList'>";
        
        foreach($result as $t) {
            echo"<li>
            <div style ='margin: 7px; box-shadow: 0 3px 6px rgb(226, 220, 226); padding: 5px; border-radius: 7px; border: 1px solid #f595e9; '>
            <div style ='box-shadow: 0 3px 6px rgb(226, 220, 226); background: #f37ce4cf; padding-right: 6px; padding-left: 6px; border-radius: 10px; color:white; font-size:small; display: inline; margin: 7px; float:right'>Modified on :{$t->modified} by {$t->user}</div>
            <p><b>   Transaction Date: </b>".date('m/d/Y', strtotime($t->date))." </p><p><b>   Transaction Ref: </b>{$t->ref}</p><p><b>   Memo: </b>{$t->memo}</p>";
            echo "<table width='100%' style ='margin-bottom: 3px; background: white; border-radius: 7px;' class='ttt ttt2'>";
            echo"<tr><th>Account</th><th>Property</th><th>Debit</th><th>Credit</th><th>Description</th><th>Name</th></tr>";
            foreach($trns as $trn) {
                if ($trn->hsi == $t->hsi){
                    echo"<tr><td>{$trn->account}</td><td>{$trn->property}</td><td>{$trn->debit}</td><td>{$trn->credit}</td><td>{$trn->description}</td><td>{$trn->profile}</td></tr>";
                } 
               
            }
            echo "</table>";
            echo "</div>";
            echo "</li>";
        }
        
    }

    public function sendEmail($row, $type)
    {
        $this->load->library('email');
        $config['smtp_user'] = $this->session->userdata('email');
        $config['smtp_pass']    = $this->session->userdata('email_pass');
        $this->email->initialize($config);
        $trans_date = $type == 'c'? $row->next_trans_date : $row->transaction_date ;
        $row = $type == 'c'?json_decode($row->transactions->data):$row->data;
        $lease_id = $row->lease_id;
        //echo $row->transactions->lease_id;
        //echo $row->transactions->profile_id;
        //echo $row->transactions->property_id;
        //echo $row->transactions->unit_id;
        //echo $row->transactions->credit;
        //echo $row->transactions->description;
        //echo $row->transactions->Item_id;

        $query = $this->db->get_where('profiles', array('id' => $row->profile_id));
        $tenant=$query->row();
        $lquery = $this->db->get_where('leases', array('id' => $row->lease_id));
        $lease=$lquery->row();
        $company_name = $this->site->settings->company_name;
        $company_phone = $this->site->settings->company_phone;
        $company_email = $this->site->settings->company_email;
        $company_logo = $this->site->settings->company_logo;

        //echo $tenant->first_name;
        //echo $tenant->last_name;
        //echo $tenant->email;

        

            //$string = $this->load->view('email_template.php', '', TRUE);


            $subject = $type == 'c'? 'Rent Reminder From '.$company_name: 'Thank You for your payment!' ;
            $message = $type == 'c'? 'This is a reminder that your rent payment of $'.number_format($row->credit, 2, '.', ',').' is due on '.$trans_date.'.': 'Your payment of $'.number_format($row->credit, 2, '.', ',').' was posted to your account on '.$trans_date.'.';

            // Get full html:
            $body = '
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=' . strtolower(config_item('charset')) . '" />
            <title>' . html_escape($subject) . '</title>
            <style type="text/css">
            
            html { -webkit-text-size-adjust: none; -ms-text-size-adjust: none;}

                @media only screen and (min-device-width: 750px) {
                    .table750 {width: 750px !important;}
                }
                @media only screen and (max-device-width: 750px), only screen and (max-width: 750px){
                  table[class="table750"] {width: 100% !important;}
                  .mob_b {width: 93% !important; max-width: 93% !important; min-width: 93% !important;}
                  .mob_b1 {width: 100% !important; max-width: 100% !important; min-width: 100% !important;}
                  .mob_left {text-align: left !important;}
                  .mob_center {text-align: center !important;}
                  .mob_soc {width: 50% !important; max-width: 50% !important; min-width: 50% !important;}
                  .mob_menu {width: 50% !important; max-width: 50% !important; min-width: 50% !important; box-shadow: inset -1px -1px 0 0 rgba(255, 255, 255, 0.2); }
                  .mob_btn {width: 100% !important; max-width: 100% !important; min-width: 100% !important;}
                  .mob_pad {width: 15px !important; max-width: 15px !important; min-width: 15px !important;}
                  .top_pad {height: 15px !important; max-height: 15px !important; min-height: 15px !important;}
                  .top_pad2 {height: 50px !important; max-height: 50px !important; min-height: 50px !important;}
                  .mob_title1 {font-size: 18px !important; line-height: 40px !important;}
                  .mob_title2 {font-size: 26px !important; line-height: 33px !important;}
                  .mob_txt {font-size: 20px !important; line-height: 25px !important;}
                }
               @media only screen and (max-device-width: 550px), only screen and (max-width: 550px){
                  .mod_div {display: block !important;}
               }
                .table750 {width: 750px;}
            </style>
            </head>
            <body style="margin: 0; padding: 0;">

            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background: #f5f8fa; min-width: 340px; font-size: 1px; line-height: normal;">
                <tr>
                <td align="center" valign="top">            
                    <!--[if (gte mso 9)|(IE)]>
                     <table border="0" cellspacing="0" cellpadding="0">
                     <tr><td align="left" valign="top" width="750"><![endif]-->
                    <table cellpadding="0" cellspacing="0" border="0" width="750" class="table750" style="width: 100%; max-width: 750px; min-width: 340px; background: #f5f8fa;">
                        <tr>
                           <td class="mob_pad" width="25" style="width: 25px; max-width: 25px; min-width: 25px;">&nbsp;</td>
                            <td align="center" valign="top" style="background: #ffffff;">

                              <table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100% !important; min-width: 100%; max-width: 100%; background: #f5f8fa;">
                                 <tr>
                                    <td align="right" valign="top">
                                       <div class="top_pad" style="height: 25px; line-height: 25px; font-size: 23px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>

                              <table cellpadding="0" cellspacing="0" border="0" width="88%" style="width: 88% !important; min-width: 88%; max-width: 88%;">
                                 <tr>
                                    <td class="mob_left" align="center" valign="top">
                                       <div style="height: 40px; line-height: 40px; font-size: 38px;">&nbsp;</div>
                                       <a href="#" target="_blank" style="display: block; max-width: 128px;">
                                       
                                          <img src="'.base_url() . "uploads/images/" . $company_logo.'" alt="img" width="128" border="0" style="display: block; width: 128px;" />
                                       </a>
                                       <div class="top_pad2" style="height: 78px; line-height: 78px; font-size: 76px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>

                              <table cellpadding="0" cellspacing="0" border="0" width="88%" style="width: 88% !important; min-width: 88%; max-width: 88%;">
                                 <tr>
                                    <td class="mob_left" align="left" valign="top">
                                       <font class="mob_title1" face="\'Source Sans Pro\', sans-serif" color="#1a1a1a" style="font-size: 52px; line-height: 55px; font-weight: 300; ">
                                          <span class="mob_title1" style="font-family: \'Source Sans Pro\', Arial, Tahoma, Geneva, sans-serif; color: #1a1a1a; font-size: 22px; line-height: 32px; font-weight: 300;">Hello '.$tenant->first_name.'
                                          <br>

                                               '.$message.'</span>
                                       </font>
                                       <div style="height: 25px; line-height: 25px; font-size: 23px;">&nbsp;</div>
                                       <font class="mob_title2" face="\'Source Sans Pro\', sans-serif" color="#5e5e5e" style="font-size: 18px; line-height: 45px; font-weight: 300; ">
                                          <span class="mob_title2" style="font-family: \'Source Sans Pro\', Arial, Tahoma, Geneva, sans-serif; color: #5e5e5e; font-size: 18px; line-height: 35px; font-weight: 300;">
                                            Thank you for choosing '.$company_name.'. <br><br>

                                            '.$company_name.' <br><br>

                                            '.$company_phone.'.</span>
                                       </font>
                                       <div style="height: 38px; line-height: 38px; font-size: 18px;">&nbsp;</div>
                                       <table class="mob_btn" cellpadding="0" cellspacing="0" border="0" width="250" style="width: 250px !important; max-width: 250px; min-width: 250px; background: #27cbcc; border-radius: 4px;">
                                          <tr>
                                          </tr>
                                       </table>
                                       <div class="top_pad2" style="height: 78px; line-height: 78px; font-size: 76px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>

                              

                           </td>
                           <td class="mob_pad" width="25" style="width: 25px; max-width: 25px; min-width: 25px;">&nbsp;</td>
                        </tr>
                     </table>
                     <!--[if (gte mso 9)|(IE)]>
                     </td></tr>
                     </table><![endif]-->
                  </td>
               </tr>
            </table>
            </body>
            </html>';
            
            
    
            // Also, for getting full html you may use the following internal method:
            //$body = $this->email->full_html($subject, $message);

            $result = $this->email
                ->from($this->session->userdata('email'))   
                //->from($companySettings->company_email)             
                ->reply_to($companySettings->company_email)    // Optional, an account where a human being reads.
                ->to('debbie@simpli-city.com')
                //->to($tenant->email) 
                ->subject($subject)
                ->message($body)
                ->send();

            //var_dump($result);
            //echo '<br />';
            //echo $this->email->print_debugger();
            //echo $this->session->userdata('email');

            exit; 
    }
}










    