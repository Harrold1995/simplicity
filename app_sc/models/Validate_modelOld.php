<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Validate_modelOld extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        //$this->validation = array('bool' => true, 'msg' => "", 'auth' => 'pass');
        $this->ap = $this->site->settings->accounts_payable;
        $this->password = $this->site->settings->password;
    }

    function failMessage($data){
            $this->validation['msg'] = $this->validation['msg'] . $data ."</br>";
            $this->validation['bool'] = false;
    }
      
    function validate($form, $data){
        $this->validation = array('bool' => true, 'msg' => "", 'auth' => 'pass');
        switch ($form) {
            case 'applyRefunds' :
                 $this->applyRefunds($data);
                 return $this->validation;
            case 'journalEntry' :
                $this->journalEntry($data);
                return $this->validation;
            case 'lease' :
                 $this->lease($data);
                 return $this->validation;
            case 'account' :
                 $this->account($data);
                 return $this->validation;
            case 'bills' :
                 $this->bills($data);
                 return $this->validation;
            case 'transactions' :
                 $this->checkPropertyEntityDate($data, 1);
                 return $this->validation;
            case 'payBills' :
                 $this->payBills($data);
                 return $this->validation;
            case 'charges' :
                 $this->charges($data);
                 return $this->validation;
            case 'checks' :
                 $this->checks($data);
                 return $this->validation;
            case 'cc_charge' :
                 $this->cc_charge($data);
                 return $this->validation;
            case 'cc_grid_charge' :
                $this->cc_grid_charge($data);
                 return $this->validation;
            case 'utilities' :
                $this->utilities($data);
                return $this->validation;
            case 'receivePayments' :
                 $this->receivePayments($data);
                 return $this->validation;
            case 'restricted' :
                 $this->restricted($data);
                 return $this->validation;
            case 'properties' :
                 $this->properties($data);
                 return $this->validation;
            case 'deposits' :
                 $this->deposits($data);
                 return $this->validation;
            case 'date' :
                 $this->vDate($data);
                 return $this->validation;
            case 'ccAccount' :
                 $this->ccAccount($data);
                 return $this->validation;
            // case 'otherDeposits' :
            //      $this->otherDeposits($data);
            //      return $this->validation;
            case 'propertyTaxes' :
                $this->propertyTaxes($data);
                return $this->validation;
            case 'reconciliation' :
                $this->vDate($data);
                return $this->validation;
            case 'bank_transfer' :
                $this->bank_transfer($data);
                return $this->validation;
            case 'addUtility' :
                $this->addUtility($data);
                return $this->validation;
            case 'delopenReconciliation' :
                $this->getLastReconciliation($data);
                return $this->validation;
        }
    }

    function vDate($date)
    {
        //if(empty($date) || $date == 'NaN/NaN/NaN') $this->failMessage("Date Required");
       
        ///if (count($test_arr) == 3) {
            if(stripos($date, '-') !== false) $test_arr  = explode('-', $date);
            if(stripos($date, '/') !== false) $test_arr  = explode('/', $date);
            if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) {
                return true;
            } else {
                $this->failMessage("Valid Date Required");
                return false;
            }
           
    }
    //for journal entry form
    function calcTotal($data){
        $transactions = $data['transactions'];
        if(!$transactions){$this->failMessage("No transactions.");}
        $debitTotal = 0;
        $creditTotal = 0;
        foreach($transactions as $singleTransaction){
            $debitTotal = $debitTotal + $singleTransaction['debit'];
            $creditTotal = $creditTotal + $singleTransaction['credit'];
        }
        if(round($creditTotal,2) == round($debitTotal,2)){
            return;
        }else{
            $this->failMessage("Totals don't match!");
        }
    }
    function ref($data){
        $header = $data['header'];
        if($header['transaction_ref']){
        }else{
            $this->failMessage("Reference is required.");
        }
    }

    function date_check($data){
        $this->checkDate($data['end'], $data['start']);
        // if($data['start'] >= $data['end']){
        //     $this->failMessage("Date start is after end date!");
        // }
    }

    function move_in_check($data){
        if($data['move_out']){
            $this->checkDate($data['move_out'], $data['move_in']);
        }
        // if($data['move_in'] >= $data['move_out']){
        //     $this->failMessage("Move in is after move out!");
        // }
    }

    function account($data){
        $table = $data['table'];
        $specialAccount = $data['specialAccount'];
        $account = $data['account'];
        $aid = $data['id'];

        $this->accountName($account, $aid);

        if($table == "banks"){
            // if(strlen($specialAccount['routing']) !== 9){
            //     $this->failMessage("Invalid routing number!");
            // }
        }if($table == "credit_cards"){
           // if(strlen($specialAccount['cc_num']) !== 16){
               // $this->failMessage("Invalid credit card number!");
            //}if(strlen($specialAccount['security_code']) > 4 || strlen($specialAccount['security_code']) < 3){
                //$this->failMessage("Invalid security code!");               
             //}
        }
    }

    function checkPropertyEntityDate($data, $closing){
        //$header = $data['header'];
        $transactions = $data['transactions'];
        $trans_date = $data['header']['transaction_date'];
        $trans_date = str_replace('/', '-', $trans_date);
        $password = $data['header']['password'] ? $data['header']['password'] : null;
        if($this->vDate($trans_date) != true )  return;
        $this->load->model('transactions_model');
        foreach($transactions as $singleTransactions){
            if(array_key_exists('credit', $singleTransactions) ? ($singleTransactions['credit'] > 0 or $singleTransactions['credit'] < 0) : (array_key_exists('debit', $singleTransactions) ? ($singleTransactions['debit'] > 0 or $singleTransactions['debit'] < 0) : ($singleTransactions['amount'] > 0 or $singleTransactions['amount'] < 0))){
                $pid = $singleTransactions['property_id'];
                $propertyClosingDate = $this->transactions_model->checkPropertyEntityDate($pid);
                $this->checkDate($trans_date, $propertyClosingDate->closing_date, $closing, $password);
            }
        }
    }
    //check any 2 dates.
    function checkDate($date1, $date2, $closing = null, $password = null){
        //$this->validation['auth'] = 'pass';
        if(strtotime($date1) < strtotime($date2)){
        //query for password
        // if ($q->num_rows() > 0) {
        //     $data = $q->row();
        //     $data = $this->encryption_model->decryptThis($data);
        // }
        // if($data->password == $password) return;
                if($closing == null){
                    $this->failMessage("start date must be before end date!");
                    return;
                }else{
                    if($password == null){$this->validation['auth'] = 'get';}   
                    else if($password == $this->password){$this->validation['auth'] = 'pass';}
                    else{$this->validation['auth'] = 'fail';}
                    //$this->validation['auth'] = false;
                    //$this->failMessage("transaction date is before closing date!");
                    return;
                }
                
            }else{
                return;
            }
        
    }

    

    public function openBalanceOnAdd($amount,$transaction_id_b)
    {   
        $this->db->select('t.id, th.id AS th_id, ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ap);
       
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments
        UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id AND transum.trans_id=' . $transaction_id_b ,'left');
        $this->db->where('t.id', $transaction_id_b);

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $overPayment = [];
                $ob = $q->row()->open_balance;
                if($amount > $ob){
                    $overPayment[] = $transaction_id_b;
                }
           
                if(!empty($overPayment)) {
                    $this->failMessage('Paying more than the open balance'); 
                }
       
       
        }
    }
    public function openBalanceOnEdit($amount, $transaction_id_a, $transaction_id_b)
    {
        $this->db->select('t.id, th.id AS th_id,  ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ap);
        
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments WHERE transaction_id_a !=' . $transaction_id_a .
        ' UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments WHERE transaction_id_a !=' . $transaction_id_a . ') trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('t.id', $transaction_id_b);
       
        // $sql = $this->db->get_compiled_select();
        // echo $sql;
        // return;
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $overPayment = [];
                $ob = $q->row()->open_balance;
                if($amount > $ob){
                    $overPayment[] = $transaction_id_b;
                }
           
                if(!empty($overPayment)) {
                    $this->failMessage('Paying more than the open balance'); 
                }
        }

    }
    function payBills($data)
    {   
        foreach($data['transaction'] as &$trans){
        
            $trans['transaction_id_b'];
            $trans['amount'];
            if($data['mode'] === 'add') $this->openBalanceOnAdd($trans['amount'], $trans['transaction_id_b']);
            if($data['mode'] === 'edit') $this->openBalanceOnedit($trans['amount'], $data['transaction_id_a'], $trans['transaction_id_b']);
        }
        $this->checkPropertyEntityDate($data, 1);
        $this->checkAmount($data['transactions'][0]);
        if(empty($data['transactions'][0]['account_id']) || $data['transactions'][0]['account_id'] == -1){
            $this->failMessage('account required');
        }
        if(empty($data['transactions'][0]['profile_id']) || $data['transactions'][0]['profile_id'] == -1){
            $this->failMessage('payee required');
        }
        if(empty($data['transactions'][0]['property_id']) || $data['transactions'][0]['property_id'] == -1){
            $this->failMessage('property required');
        }
        $total = $data['transactions'][0]['credit']; 
        $appliedPayments = $data['applied_payments'];
        $appliedAmount = array_sum(array_column($appliedPayments, 'amount')); 
        if(round($total,2) < round($appliedAmount,2)){
            $this->failMessage('Amount can\'t be less than amount applying'); 
        }

    }


    function checkAmount($transaction)
    {
            $account_id = $transaction['account_id'];
            $property_id = $transaction['property_id'];
            $profile_id = $transaction['profile_id'];
            
            if($transaction['credit'] < 0){
                $this->db->select('a.name AS account, properties.name AS property, CONCAT_WS(" ",p.first_name,p.last_name) AS profile');
                $this->db->from('accounts a'); 
                $this->db->from('properties');
                $this->db->from('profiles p');
                $this->db->where('properties.id', $property_id); 
                $this->db->where('a.id', $account_id);
                $this->db->where('p.id', $profile_id);

                $q = $this->db->get();

                $profile = $q->row()->profile;
                $property = $q->row()->property;
                $account = $q->row()->account;

                $this->failMessage("Payment to vendor $profile  for property $property  using account $account  has a total equal or less than zero");
            }
    }

    function valid()
    {
        $this->vDate($date);
    }

    //applyRefunds validation
    function applyRefunds($data){

        if(array_key_exists('checking account', $data)){
            if(empty($data['checking account']) || $data['checking account'] == -1){
                $this->failMessage('account required');
            }
        }
        if(array_key_exists('checking account', $data)){   
            $applyTotal = $data['applyTotal']; 
            $appliedPayments = $data['applied_payments'];
            $total = array_sum(array_column($appliedPayments, 'amount')); 
            if(round($applyTotal,2) < round($total,2)){
                $this->failMessage('Amount can\'t be less than amount applying'); 
            }
        }
        
        $fields = [];
        $fields = $data['leaseInfo'] + $data['header'] + $data['total'];
        $this->requiredFields('applyRefunds', $fields);
        $this->load->model('applyRefundSecurity_model');
        if($data['leaseInfo']['lease_id'] && $data['leaseInfo']['profile_id'] && $data['mode'] == 'add'){
            $sdBalance = $this->applyRefundSecurity_model->getSdBalance($data['leaseInfo']['lease_id'], $data['leaseInfo']['profile_id']);
            $lmrBalance = $this->applyRefundSecurity_model->getLmrBalance($data['leaseInfo']['lease_id'], $data['leaseInfo']['profile_id']);
            if(round($data['totalSd'],2) > round($sdBalance,2)) $this->failMessage('Insufficient funds in SD Account'); 
            if(round($data['totalLmr'],2) > round($lmrBalance,2)) $this->failMessage('Insufficient funds in LMR Account'); 
        }
        if($data['leaseInfo']['lease_id'] && $data['leaseInfo']['profile_id'] && $data['mode'] == 'edit'){
            $sdBalance = $this->applyRefundSecurity_model->getSdBalanceEdit($data['th_id'], $data['leaseInfo']['lease_id'], $data['leaseInfo']['profile_id']);
            $lmrBalance = $this->applyRefundSecurity_model->getLmrBalanceEdit($data['th_id'], $data['leaseInfo']['lease_id'], $data['leaseInfo']['profile_id']);
            if(round($data['total)Sd'],2) > round($sdBalance,2)) $this->failMessage('Insufficient funds in SD Account'); 
            if(round($data['totalLmr'],2) > round($lmrBalance,2)) $this->failMessage('Insufficient funds in LMR Account');

        }
        if(array_key_exists('total', $data)){   
            $checkPropertyEntity['header'] = $data['header'];
            $checkPropertyEntity['transactions'][0] = ['amount' => $data['total']['Totals'], 'property_id' => $data['leaseInfo']['property_id']];
            $this->checkPropertyEntityDate($checkPropertyEntity, 1);
        }
    }
    
    //lease validation
    function lease($data){
        $lease = $data['data'];
        $ttls = $data['ttls'];
        $edit = $data['edit'];
        if(!$edit){
            if(($ttls == null) || ($ttls[0]['profile_id'] < 1)){
                $this->failMessage('There must be a tenant on the lease!');
            }
        }
        $this->date_check($lease);
        $this->move_in_check($lease);
        if($ttls){
            $this->checkTtlUnit($lease['unit_id'], $ttls);
        }
    }
    //check ttl unit
    function checkTtlUnit($uid, $ttls){
        $this->load->model('units_model');
        $units = $this->getUnitandsubs($uid);
        foreach ($ttls as $ttl){
            if(!in_array($ttl['unit_id'], $units)){
                 $this->failMessage('Unit does not match lease');
            }
        }
    }
    public function getUnitandsubs($uid)
    {
        $this->db->select('u.id');
        $this->db->from('units u');
        $this->db->where('u.id', $uid);
        $this->db->or_where('u.parent_id', $uid);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row->id;
            }
            return $data;
        }
        return null;
    }

    public function billConfirm($data)
    {
        $this->db->select('th.transaction_ref, t.profile_id, t.property_id');
        $this->db->from('transaction_header th');
        $this->db->join('transactions t','th.id = t.trans_id');
        $this->db->where('th.transaction_type', 2);
        $this->db->where('th.transaction_ref', trim($data['header']['transaction_ref']));
        $this->db->where('t.property_id', $data['headerTransaction']['property_id']);
        $this->db->where('t.profile_id', $data['headerTransaction']['profile_id']);
        //$this->db->where('t.profile_id', $data['headerTransaction']['credit']);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $this->failMessage('A bill for this property with this reference and vendor already exists');
        }
    }

    //bills validation
    function bills($data){
        $header = $data['header'];
        $headerTransaction = $data['headerTransaction'];
        $transactions = $data['transactions'];
        $special = $data['special'];
        $headerTransaction = $headerTransaction + $special;
        $headerTransactionForCheck['transactions'] = $headerTransaction;
        $checkPropertyEntity['header'] = $header;
        $checkPropertyEntity['transactions'] = $data['transactions'] + $headerTransactionForCheck;
        $this->calcTotal($checkPropertyEntity);
        $this->PropertyTotal($checkPropertyEntity['transactions']);
        $this->checkPropertyEntityDate($checkPropertyEntity, 1);
        //$this->checkPropertyEntityDate($headerTransaction);
        foreach($transactions as &$transaction){
            if($transaction['debit']){$transaction['amount'] =  (double)$transaction['debit'];}
            if($transaction['credit']){$transaction['amount'] =  (double)$transaction['credit'];}
            $this->checkAccountAndProperty($transaction['amount'], $transaction);
        }
        //$this->requiredFields( 'bills', $transactions['h']);
        $this->requiredFields( 'bills', $header, $headerTransaction);
        //if(trim($data['header']['transaction_ref']))$this->billConfirm($data);
    }
    //charges validation
    function charges($data){
        $transactions = $data['transactions'];
        $header = $data['header'];
        $checkPropertyEntityDate['transactions'] = $data;
        $checkPropertyEntityDate['header'] = $header;
        $this->checkProfileLease($transactions);
        //unset($checkPropertyEntityDate['transactions']['header']);
        //this is used to make it a multi dimentional array to be like the other forms for the property entity check;
        $checkPropertyEntityDate['transactions'] = $checkPropertyEntityDate['transactions'];
        $this->checkPropertyEntityDate($checkPropertyEntityDate, 1);
        $this->requiredFields( 'charges', $transactions, $header);
    }
    
    //checks validation
    function checks($data){
        $header = $data['header'];
        $transactions = $data['transactions'];
        $headerTransaction = $data['headerTransaction'];
        //$special = $data['special'];
        $headerTransaction = $headerTransaction;
        $headerTransactionForCheck['transactions'] = $headerTransaction;
        $checkPropertyEntity['transactions'] = $data['transactions'] + $headerTransactionForCheck;
        $checkPropertyEntity['header'] = $header;
        $this->checkPropertyEntityDate($checkPropertyEntity, 1);

        foreach($transactions as &$transaction){
            if($transaction['debit']){$transaction['amount'] =  (double)$transaction['debit'];}
            if($transaction['credit']){$transaction['amount'] =  (double)$transaction['credit'];}
            $this->checkAccountAndProperty($transaction['amount'], $transaction);
            $this->transactionClosed($transaction['id'], $transaction);
            //$this->requiredFields( 'checkstransactions',  $transaction);
        }
            //headerTransaction was not included in transactions 
            $this->transactionClosed($headerTransaction['id'], $headerTransaction );
            $propertyTransaction = array($headerTransaction['id'] => $headerTransaction);
            $propertyTransaction = $propertyTransaction + $transactions;
            $this->PropertyTotal($propertyTransaction);
            $this->checkTotal($transactions, $headerTransaction['credit']);
            $this->requiredFields( 'checks', $header, $headerTransaction);
            
    }
    //cc_grid_charge validation
    function ccAccount($data)
    {
        if(empty($data) || ($data == -1)){
            $this->failMessage('account required');
        }
    }

    function cc_grid_charge($data){
        //$account_id = $data['ccAccount_id'];
        $transaction = $data['transaction'];
        $details = $data['details'];
        $ofx_id = $transaction['ofxId'];
        $transAmount = $transaction['amount'];

        if(array_key_exists($ofx_id, $details)  && ((round(array_sum(array_column($details[$ofx_id], 'amount')),2) > 0) || (round(array_sum(array_column($details[$ofx_id], 'amount')),2) < 0))){
            $total = array_sum(array_column($details[$ofx_id], 'amount'));
            if(round($transAmount,2) != round($total,2)){
                $this->failMessage('This transaction doesn\'t equal');
               
            }
        }    

        // if(empty($account_id) || $account_id == -1){
        //     $this->failMessage('account required');
        //     }
        
            $ofx_id = $transaction['ofxId'];
            $header['transaction_date'] = $transaction['transaction_date'];
            $checkPropertyEntity['header'] = $header;
            if(!array_key_exists($ofx_id, $details)){ 
                $this->checkAccountAndProperty($transaction['amount'], $transaction);
                $checkPropertyEntity['transactions'] = $transactions;
                $this->checkPropertyEntityDate($checkPropertyEntity, 1);
            }
            if(array_key_exists($ofx_id, $details)){ 
                $checkPropertyEntity['transactions'] = $details[$ofx_id];
                
            foreach($details[$ofx_id] as &$detail){
                    $this->checkAccountAndProperty($detail['amount'], $detail);
                    $this->checkPropertyEntityDate($checkPropertyEntity, 1);
                }

            }
                
            }
        
    
    //cc_charge validation
    function cc_charge($data){
        $header = $data['header'];
        $transactions = $data['transactions'];
        $creditCards = $data['creditCard'];

        $headerTransactionForCheck['transactions'] = $creditCards;
        $checkPropertyEntity['header'] = $header;
        $checkPropertyEntity['transactions'] = $data['transactions'] + $headerTransactionForCheck;
        $this->checkPropertyEntityDate($checkPropertyEntity, 1);
        foreach($transactions as &$transaction){
            $this->checkAccountAndProperty($transaction['debit'], $transaction);
        }
        $this->PropertyTotal($checkPropertyEntity['transactions']);
        $this->checkTotal($transactions, $creditCards['credit']);
        $this->requiredFields('cc_charge', $creditCards );
    }

    //deposits validation
    function deposits($data){
        if($data['ids']){
            $this->checkFordeleteOld($data['ids']);
            return;
        }
        $this->requiredFields('deposits', $data['headerTransaction'] );
        $this->requiredFields('deposits', $data['header']);
        foreach($data['transactions'] as $datum){
            $this->checkAccountAndProperty($datum['credit'], $datum );
        }
        array_push($data['transactions'], $data['headerTransaction']);
        if($data['mode'] == 'edit'){
            foreach($data['transactions'] as $transaction){
                $this->transactionClosed($transaction['id'], $transaction);
            }
        }
        $this->checkPropertyEntityDate($data, 1);
        array_push($data['transactions'], $data['buf']);
        $this->PropertyTotal($data['transactions']);

        // if($data['mode'] === 'edit'){
        //     $this->checkFordeleteOld($data['trans_id'], $data['property']);
        // }

        

        // if($data['mode'] === 'edit'){
        //     $properties = array_unique(array_column($data['transactions'], 'property_id'));
        //     foreach($properties as $property_id){
        //         if(trim($property_id) != ""){
        //             if($property_id != $data['property_id']){
        //                 $this->failMessage('Sorry you can\'t make a new deposit to another property in edit');
        //             }
        //         }   
        //     }
        // }
    }

    public function checkFordeleteOld($deletes){

        $this->db->trans_start();

        $this->db->select('deposit_id,clr');
        $this->db->from('transactions');
        //$this->db->where(array('trans_id' => $trans_id, 'property_id !=' => $property_id));
        $this->db->where_in('id',$deletes);
        $q = $this->db->get();
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if($row->clr == 1){
                    $cleared =  "OOPS transactions that were cleared can't be deleted";
                }

                // if($row->transaction_date <  $row->closing_date){
                //     $beforeClosingDate =  "OOPS transactions that are before closing date can't be deleted";
                // }

                if(($row->deposit_id != null) && ($row->deposit_id != 0)){
                    $deposited =  "OOPS transactions that were deposited can't be deleted";
                }

                // if(($row->apa_id != null) || ($row->apb_id != null)){
                //     $applied = "Transaction/s  is/are applied to a payment";
                // }
            }
        }
            if(!empty($cleared) || !empty($deposited)) 
            {
                $this->failMessage($cleared . ' ' . ' '. $deposited);
            }
            
           
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
   
            return true;
    }
    //journal entry validation
    function journalEntry($data){
        $this->calcTotal($data);
        $this->checkPropertyEntityDate($data, 1);
        $header = $data['header'];
        $transactions = $data['transactions'];
        
        foreach($transactions as &$transaction){
            if(round($transaction['credit'],2) > 0 or round($transaction['credit'],2) < 0){$transaction['amount'] = (double)$transaction['credit'];}
            if(round($transaction['debit'],2) > 0 or round($transaction['debit'],2) < 0){$transaction['amount'] = (double)$transaction['debit'];}
            $this->checkAccountAndProperty($transaction['amount'], $transaction);
            $this->transactionClosed($transaction['id'], $transaction);
        }
             //$this->journalEntryPropertyTotal($transactions);
             $this->PropertyTotal($transactions);
             $this->requiredFields( 'journalEntry', $header);
    }

    function properties($data){
        $header['property'] = $data['name'];
        //$this->requiredFields('properties', $header );
    }

    //receivePayments validation
    function receivePayments($data){
        $amount = $data['transactions'][0]['debit']; 
        $date = $data['header']['transaction_date'];
        if($data['deposit_id'] != NULL && $data['deposit_id'] != 0){
            $msg = '';
            if(round($amount,2) != round($data['curAmnt'],2)){$msg .= ' deposit amount';};
            if(str_replace('/', '-', $date) != $data['curDate']){$msg .= ' deposit date';};
            if($data['transactions'][0]['account_id'] != $data['curAcct'] )
            {$msg .= ' deposit to';};
            if(!empty($msg))
            {return $this->failMessage('Sorry you can\'t edit' . $msg .' on a deposited payment');}
        } 
        $this->vDate($date);
        $this->requiredFields('receivePayments', $data['transactions'][0]);
        $appliedPayments = $data['applied_payments'];
        $applyAmount = array_sum(array_column($appliedPayments, 'amount')); 
        if(round($amount,2) < round($applyAmount,2)){
            $this->failMessage('Amount can\'t be less than amount applying'); 
        }
        $this->checkPropertyEntityDate($data, 1);
    }

    function propertyTaxes($data){
        //foreach($data as $singleData){
            foreach($data as $single){
                $this->requiredFields('propertyTaxes', $single);
            }
       // }
    }
    //bank_transfer
    function bank_transfer($data){
        $from = $data['from_account_id'];
        if($data['to_account_id'] == $from){
            $this->failMessage('You are transfering to the same account!');
        }
        $this->sufficientFunds($from, $data['amount']);
        $this->requiredFields('bank_transfer', $data);
    }

    public function sufficientFunds($from, $amount)
    {    
        $this->db->select('SUM(debit - credit) AS balance');
        $this->db->from('transactions');
        $this->db->where('account_id', $from);   
        $q = $this->db->get();  
        
        //if ($q->num_rows() > 0) {
            $balance = $q->row()->balance;
            if(round(($balance - $amount),2) < 0){
                $this->failMessage('Insufficient funds for transfer');
            //}
        }
    }

    function restricted($data)
    {   
        if($data['lease_id'] && $data['profile_id']){
            $this->db->select('l.restrict_payments AS lease, lp.restrict_payments AS profile');
            $this->db->from('leases l');
            $this->db->join('leases_profiles lp', 'l.id = lp.lease_id AND lp.lease_id =' . $data['lease_id'] . ' AND lp.profile_id ='. $data['profile_id']); 
            //$this->db->where(array('trans_id'=>$th_id, 'account_id' => $this->ap));
            $q = $this->db->get();
            //if ($q->num_rows() > 0) {
            $lease = $q->row()->lease;
            $profile = $q->row()->profile;
            if($lease || $profile){
                $message = $lease ? 'Payment restricted on this lease' : 'Payment restricted on this tenant';
                $this->failMessage($message); 
            }
        }
        
            
    }

    public function getLastReconciliation($data){
        $this->db->select('account_id');
        $this->db->from('reconciliations');
        $this->db->where('id', $data['rec_id']);

        $account_id = $this->db->get_compiled_select();
        $this->db->reset_query();

        $this->db->select('MAX(id) AS rec_id');
        $this->db->from('reconciliations');
        $this->db->where("account_id = ($account_id)", NULL, FALSE);
        //$this->db->where('account_id = (SELECT account_id FROM reconciliations WHERE id = ' . $data['rec_id'] . ')', NULL, FALSE);
        $q = $this->db->get(); 
        if ($q->num_rows() > 0) {
            $rec_id = $q->row()->rec_id;
            $row = $q->row();
        }

        // $this->db->select('MAX(rec_id) AS rec_id');
        // $this->db->from('transactions');
        // $this->db->where("account_id = ($account_id)", NULL, FALSE);
        // //$this->db->where('account_id = (SELECT account_id FROM reconciliations WHERE id = ' . $data['rec_id'] . ')', NULL, FALSE);
        // $q = $this->db->get(); 
        // if ($q->num_rows() > 0) {
        //     $rec_id = $q->row()->rec_id;
        //     $row = $q->row();
        // }
      //  if($rec_id != NULL){
            if($data['rec_id'] != $rec_id){
                $this->failMessage("Sorry you can only {$data['mode']} the last reconciliation for this account"); 
            }
      //  }
            
    }

    //utilities validation

    public function getDefaultBank($property_id)
    {   
        $this->db->select('name, default_bank');
        $this->db->from('properties'); 
        $this->db->where('id', $property_id);
       
        $q = $this->db->get(); 
        if ($q->num_rows() > 0) {
            return $q->row();
        }
       
    }

    function addUtility($data){
        $this->requiredFields( 'addUtility', $data);
        $this->checkIfUnitOnProperty( $data['property_id'], $data['unit_id']);
    }
   
    function checkIfUnitOnProperty($property_id, $unit_id){
        $result = $this->db->select('property_id')->from('units')->where('id', $unit_id)->get()->row()->property_id;
        if($property_id != $result){
            $this->failMessage( "Unit is not on the property.");
        }
    }

    function utilities($data){
       
         $transactions = $data['transactions'];
         $date = $data['utilities']['last_paid_date'];
         //$this->vDate($date);
         $checkPropertyEntity['header']['password'] = $data['password'];
         $checkPropertyEntity['header']['transaction_date'] = $date;
         $checkPropertyEntity['transactions'][0] = $transactions[0];
         $this->checkPropertyEntityDate($checkPropertyEntity, 1);

         $transaction_type =$row['utilities']['direct_payment'] == 1 ? 4 : 2;
         $transAmount = array_sum(array_column($row['transactions'], 'amount'));
         $property_id = $transactions[key($transactions)]['property_id'];
         $property = $this->getDefaultBank($property_id);
         $default_bank = $property->default_bank;
         if(empty($default_bank)){
             $this->failMessage( "Property $property->name doesn\'t have a default bank account");
         }
         if($transaction_type == 4 && round($transAmount,2) <= 0){
            $this->failMessage( "Utility account $account has a zero or negative total");
         }

        foreach($transactions as $key => &$transaction){
            //$utility_trans = $transaction['utility_trans'];
            if(($key == 0) || !empty(trim($transaction['amount']))) $this->checkAccountAndProperty($transaction['amount'], $transaction);
            //$this->transactionClosed($transaction['account_id'], $transaction['id'], $transaction);
            if(($key == 0) || !empty(trim($transaction['amount']))) $this->requiredFields( 'utilities', $transaction);
            //$this->requiredFields( 'utility_trans', $utility_trans);
        }
    
            
    }

   
     //Can't have 2 accounts with same name and account type
     function accountName($account, $aid){
        $q = $this->db->select('name')->from('accounts')->where('account_types_id', $account['account_types_id'])->where('name', $account['name'])->where('id !=', $aid)->get();
        if ($q->num_rows() > 0) {
                    $this->failMessage("Account name is already used.");
                }
     }
        //single property debits and credits must equal
        function PropertyTotal($transactions){
            $properties = array_unique(array_column($transactions, 'property_id'));
            $properties = array_diff( $properties, [-1] );

            foreach($properties as $property){
                $propertyTotal = 0;

            foreach($transactions as &$transaction){

                $transaction['debit'] = round(floatval(str_replace(',', '', $transaction['debit'])),2);
                $transaction['credit'] = round(floatval(str_replace(',', '', $transaction['credit'])),2);
                
                if($transaction['property_id'] == $property){
                    //(double)$transaction['debit'] =  trim($transaction['debit'],"-");
                    $propertyTotal = round($propertyTotal,2) - round($transaction['debit'],2);
                    $propertyTotal = round($propertyTotal,2) + round($transaction['credit'],2);
                    //$this->failMessage( $transaction['credit'] . " doesn't equal.".$transaction['debit']."total".$propertyTotal);
                }
            }
            //(double)$propertyTotal =  trim($propertyTotal,"-");
            if(round($propertyTotal,2) != 0){
                $q = $this->db->select('name')->from('properties')->where('id', $property)->get();
                if ($q->num_rows() > 0) {
                    $propertyName = $q->row();
                }
                $this->failMessage($propertyName->name . " doesn't equal.". $propertyTotal);
            }
            }
        }
        //not udes any more, instead using PropertyTotal
    //single property debits and credits must equal
    function journalEntryPropertyTotal($transactions){
        $properties = array_unique(array_column($transactions, 'property_id'));
        $properties = array_diff( $properties, [-1] );

        foreach($properties as $property){
            $totalDebit = 0;
            $totalCredit = 0;
           foreach($transactions as &$transaction){
               if($transaction['property_id'] == $property){
                   $totalDebit += $transaction['debit'];
                   $totalCredit += $transaction['credit'];
               }
           }
           (double)$totalDebit =  trim($totalDebit,"-");
           if(round($totalDebit,2) != round($totalCredit,2)){
            $q = $this->db->select('name')->from('properties')->where('id', $property)->get();
            if ($q->num_rows() > 0) {
                $propertyName = $q->row();
            }
               $this->failMessage($propertyName->name . " amounts don't equal.");
           }
        }
    }
    //accounts receivable must be tenant accounts payable must be vendor
    function checkTenantVendor($name, $number){
        if($name > 0){
            $q = $this->db->select('profile_type_id')->get_where('profiles', array('id' => $name), 1);
            if ($q->num_rows() > 0) {
            $data = $q->row();
            }
            if($number ==  $this->site->settings->accounts_receivable){
                if($data->profile_type_id  != 3){
                    $this->failMessage("Profile must be a tenant.");
                }
            }
            if($number ==  $this->site->settings->accounts_payable){
                if($data->profile_type_id != 1){
                    $this->failMessage("Profile must be a vendor.");
                }
            }
        }else{
            $this->failMessage("No name chosen.");
        }      
    }
    //if there is an amount there must be account and property
    function checkAccountAndProperty($amount, $transaction){
        if($amount > 0 or $amount < 0){
            if(!$transaction['property_id'] OR $transaction['property_id'] == -1 OR $transaction['property_id'] == ""){
                $this->failMessage("Property is required.");
            }
            if(!$transaction['account_id'] OR $transaction['account_id'] == -1 OR $transaction['account_id'] == ""){
                $this->failMessage("Account is required.");
            }
            if($transaction['account_id'] ==  $this->site->settings->accounts_receivable OR $transaction['account_id'] == $this->site->settings->accounts_payable){
                    $this->checkTenantVendor($transaction['profile_id'],$transaction['account_id']);
            }
        }
    }
    //total on top must equal the whole bottom
    function checkTotal($transaction, $topTotal){
        $total = array_sum(array_column($transaction, 'amount'));

        if(round($topTotal,2) <= 0){
            $this->failMessage("Amount is required.");
        }
        // if($topTotal != $total){
        //     $this->failMessage("Amounts don't equal.");
        // }
    }
    //for charges check if profile is in this lease
    function checkProfileLease($transaction){
        $profile_id = $transaction['profile_id'];
        //new- doesn't need profile but if there is a profile it must be on the lease
        if($profile_id == ""){return;}
        $lease_id = $transaction['lease_id'];
        $unit_id = $transaction['unit_id'];

        $this->db->select('id');
        $this->db->from('leases_profiles');
        $this->db->where('profile_id', $profile_id);
        $this->db->where('lease_id', $lease_id);
        $this->db->where('unit_id', $unit_id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            
            }
            else{
            $this->failMessage("Profile is not on lease");
        }
    }
    //checks for required fields
    function requiredFields( $type, $header, $topData = null){
        unset($header['memo']);
        //unset($header['basis']);
        unset($header['transaction_type']);
        unset($topData['class_id']);
        unset($header['class_id']);
        unset($header['description']);
        unset($header['to_print']);
        unset($header['old_last_paid_date']);
        unset($header['unit_id']);// lease validation needs unit
        unset($header['utility_type']);
        unset($header['account']);//for utilities make sure not neede elsewhere
        unset($header['billable']);
        unset($header['trans_id']);
        unset($topData['trans_id']);
        unset($header['transaction_ref']);
        unset($topData['description']);
        unset($topData['terms']);

        if($topData){
            $data = $topData + $header;
        }else{
            $data = $header;
        }
        $ChangedFields = $this->changeFieldNames($data, $type);
        foreach($ChangedFields as $k =>$value){
            if(!is_numeric($value) && !empty($value)) continue;
            if($value < .01){
                //this if is for bills to take out under score
                if($k === 'due_date'){
                    $this->failMessage( "Due Date is required.");
                    continue;
                }
                //this if is for property taxes to take out under score
                if($k === 'last_pay_date'){
                    $this->failMessage( "Pay Date is required.");
                    continue;
                }
                $this->failMessage($k . " is required.");
            }
        }
    }
    //change field names to take out underscore for error msg
    function changeFieldNames($data, $type){
        if($type != 'journalEntry'  && $type != 'properties' && $type != 'utility_trans' && $type != 'deposits' && $type != 'otherDeposits' && $type != 'receivePayments'){
            if(array_key_exists('profile_id', $data)) $data['profile'] = $data['profile_id'];
            unset($data['profile_id']);
            if(array_key_exists('property_id', $data))$data['property'] = $data['property_id'];
            unset($data['property_id']);
            if(array_key_exists('account_id', $data)) $data['account'] = $data['account_id'];
            unset($data['account_id']);
        } 
        if( $type != 'properties' && $type != 'utility_trans' && $type != 'utilities' && $type != 'deposits'){
            if(array_key_exists('transaction_date', $data)) $data['date'] = $data['transaction_date'];
            unset($data['transaction_date']);
            //$data['reference'] = $data['transaction_ref'];
            unset($data['transaction_ref']);
        }
        if($type == 'charges'){
                $data['item'] = $data['item_id'];
                unset($data['item_id']);
                $data['lease'] = $data['lease_id'];
                unset($data['lease_id']);
                unset($data['reference']);
                unset($data['account']);
                unset($data['profile']);
        }
        if($type == 'properties'){
            $property['property'] = $data['property'];
            unset($data);
            $data['property'] = $property['property'];
    }
    if($type == 'checks'){
        unset($data['reference']);
        unset($data['debit']);
    }

    if($type == 'checkstransactions'){
        unset($data['reference']);
        unset($data['date']);
        unset($data['credit']);
    }

    if($type == 'bank_transfer'){
        $data['From account'] = $data['from_account_id'];
        unset($data['from_account_id']);
        $data['To account'] = $data['to_account_id'];
        unset($data['to_account_id']);
    }

    if($type == 'addUtility'){
        unset($data['unit_id']);
    }

    if($type == 'deposits'){
        if(array_key_exists('property_id', $data)) $data['property'] = $data['property_id'];
        if(array_key_exists('account_id', $data)) $data['account'] = $data['account_id'];
        if(array_key_exists('debit', $data)) $data['Deposit Totals'] = $data['debit'];
        if(array_key_exists('transaction_date', $data)) $data['date'] = $data['transaction_date'];
        unset($data['transaction_date']);
        unset($data['property_id']);
        unset($data['account_id']);
        unset($data['debit']);
    }

    if($type === 'receivePayments'){
        if(array_key_exists('property_id', $data)) $data['property'] = $data['property_id'];
        if(array_key_exists('account_id', $data)) $data['Deposit To'] = $data['account_id'];
        if(array_key_exists('debit', $data)) $data['Amount'] = $data['debit'];
        if(array_key_exists('transaction_date', $data)) $data['date'] = $data['transaction_date'];
        if(array_key_exists('lease_id', $data)) $data['lease_id'] = $data['lease_id'];
        if(array_key_exists('profile_id', $data)) $data['profile'] = $data['profile_id'];
        unset($data['transaction_date']);
        unset($data['property_id']);
        unset($data['account_id']);
        unset($data['debit']);
        unset($data['lease_id']);
        unset($data['unit_id']);
        unset($data['profile_id']);
    }

    if($type == 'utilities'){
        unset($data['account_id']);
    }
            return $data;
    }
    //can't change name or amount if transaction has a rec id that closed = 1
    function transactionClosed($tid, $transaction){
         //$this->load->model('reconciliations_model');
         //$lastRec = $this->reconciliations_model->getLastReconciliation($aid);
         $this->load->model('transactions_model');
         $oldTransaction = $this->transactions_model->getSingleTransaction($tid);
           if($oldTransaction->rec_id != Null && $oldTransaction->clr == 1 ){
                //$this->load->model('transactions_model');
                //$oldTransaction = $this->transactions_model->getSingleTransaction($tid);
                    if($transaction['credit']){
                        if(round($oldTransaction->credit,2) != round($transaction['credit'],2)) {
                            $this->failMessage("Amount can't be modified.");
                        }
                    }

                    if($transaction['debit']){
                        if(round($oldTransaction->debit,2) != round($transaction['debit'],2)) {
                            $this->failMessage("Amount can't be modified.");
                        }
                    }
                    
                // $q = $this->db->select('name')->from('accounts')->where('id', $aid)->get();
                // if ($q->num_rows() > 0) {
                //     $accountName = $q->row();
                // }
                    if(trim($oldTransaction->account_id) != trim($transaction['account_id'])){
                        $this->failMessage("Account name can't be modified.");
                    }
           }
    }

    function checkEmptyString($data){
        foreach($data as $k =>$value){
            if($value == ""){
                $this->failMessage($k . " is required.");
            }
        }
    }
}
