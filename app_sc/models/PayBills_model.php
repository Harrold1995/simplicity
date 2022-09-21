<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PayBills_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ap = $this->site->settings->accounts_payable;
        $this->load->model('encryption_model');
    }


    function getVendor()
    {
        $this->db->select('id, CONCAT_WS(" ",first_name, last_name) AS vendor');
        $this->db->from('profiles');
        $this->db->where('profile_type_id',1);// type id number for vendor
        $this->db->where('active', 1);
        $this->db->ORDER_BY('vendor');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    public function getTransactionType()
    {
        $q = $this->db->get('transaction_type');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }
    public function getBalance($aid)
    {
        $this->db->select('account_id, SUM(debit - credit) AS balance');
        $this->db->from('transactions');
        $this->db->where('account_id', $aid);
        $this->db->group_by('account_id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->balance ? $q->row()->balance : 0;
        }
        

    }
    

    
    public function getBanks()
    {
        $this->db->select('id, name, accno');
        $this->db->from('accounts');
        $this->db->where('account_types_id', 1);
        $this->db->where('active', 1);
        $this->db->ORDER_BY('name');
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;

    }

    public function getCC()
    {
        $this->db->select('id, name, accno');
        $this->db->from('accounts');
        $this->db->where('account_types_id', 6);
        $this->db->where('active', 1);
        $this->db->ORDER_BY('name');
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;

    }

    public function getPaymentMethods()
    {
        $this->db->select('id, name');
        $this->db->from('payment_methods');
        $this->db->ORDER_BY('name');
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }
    
    public function getAccounts()
    {
        $this->db->select('id, name, accno');
        $this->db->from('accounts');
        $this->db->where_in('account_types_id',[1,6]);
        $this->db->where('active', 1);
        $this->db->ORDER_BY('name');
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;

    }

    public function getProperties()
    {
        $this->db->select('id, name');
        $this->db->from('properties');
        $this->db->where('active', 1);
        $this->db->ORDER_BY('name');
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;

    }

    public function getAddress($id)
    {   
        $this->db->select('id,address_line_1,address_line_2, CONCAT_WS("",city," ", state," ",area_code) AS city , CONCAT_WS(" ",first_name, last_name) AS vendor');
        $this->db->from('profiles');
        $this->db->where(array('id'=> $id));        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }



    

    
    public function getTransactions($due_date , $profile_id , $property_id )
    {
        $this->db->select('t.id, th.id AS th_id, th.transaction_type AS type, t.profile_id , CONCAT_WS(" ",p.first_name,p.last_name) AS vendor, t.property_id, props.name, props.default_bank, th.transaction_ref, th.transaction_date , b.due_date, (t.credit - t.debit) AS amount, ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ap);
        $this->db->join('bills b', 't.trans_id = b.trans_id','left');
        $this->db->join('profiles p', 't.profile_id = p.id', 'left');
        $this->db->join('properties props', 't.property_id = props.id');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments
        UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0');

        if($due_date){
        $this->db->where('b.due_date <=', $due_date);
        }
        if($profile_id){
        $this->db->where('t.profile_id', $profile_id);
        }
        if($property_id){
        $this->db->where('t.property_id', $property_id);
        }
        
         $this->db->ORDER_BY('th.transaction_date ASC');
         //$this->db->limit(300);
        // $sql = $this->db->get_compiled_select();
        // echo $sql;
        // return;
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }                   
        return null;
       
    }

    public function getTransactionsSlick()
    {
        $this->db->select('t.id, th.id AS th_id, th.transaction_type AS type, t.profile_id, concat_ws(" ", a.accno, a.name) as bank_name, CONCAT_WS(" ",p.first_name,p.last_name) AS vendor, t.property_id, props.name, props.default_bank as account_id, th.transaction_ref, th.transaction_date , b.due_date, (t.credit - t.debit) AS bill_amount, ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance, 0 as amount');
        $this->db->from('transactions t');
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ap);
        $this->db->join('bills b', 't.trans_id = b.trans_id','left');
        $this->db->join('profiles p', 't.profile_id = p.id', 'left');
        $this->db->join('properties props', 't.property_id = props.id');
        $this->db->join('accounts a', 'props.default_bank = a.id','left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments
        UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0');

        $this->db->ORDER_BY('th.transaction_date ASC');

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getHeaderEdit($th_id)
    {
        $this->db->select('th.id, th.memo, CONCAT(DATE_FORMAT(th.last_mod_date, "%m/%d/%Y")," ",TIME_FORMAT(th.last_mod_date, "%r")) AS modified, th.last_mod_by, th.transaction_date AS date, th.transaction_ref, CONCAT_WS("",pr.first_name," ",pr.last_name) AS user,  t.profile_id, CONCAT_WS("",p.first_name," ",p.last_name) AS vendor,t.property_id, props.name, t.account_id, a.name As account_name, t.credit,
        t.class_id, p.address_line_1,p.address_line_2, CONCAT_WS("",p.city,", ", p.state," ",p.area_code) AS city');
        $this->db->from('transaction_header th'); 
        $this->db->join('transactions t', 'th.id = t.trans_id AND t.trans_id ='. $th_id);
        $this->db->join('profiles p', 't.profile_id = p.id');
        $this->db->join('properties props', 't.property_id = props.id');
        $this->db->join('accounts a', 't.account_id = a.id');
        $this->db->join('users u', 'th.last_mod_by = u.id',"left"); 
        $this->db->join('profiles pr', 'u.profile_id = pr.id',"left"); 
        $this->db->where('t.account_id !=', $this->ap);

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $row = $q->row();
        $this->db->select('account_id, SUM(debit - credit) AS balance');
        $this->db->from('transactions');
        $this->db->where('account_id', $row->account_id);
        $this->db->group_by('account_id');
        $b = $this->db->get();
        $row->balance = $b->row()->balance ? $b->row()->balance : 0;
        return $row;
        }
        return false;

    }


    // public function getTransactionsEdit($th_id)
    // {   
    //     $this->db->select('id, profile_id');
    //     $this->db->from('transactions'); 
    //     $this->db->where(array('trans_id'=>$th_id, 'account_id' => $this->ap));
    //     $transactionInfo = $this->db->get();
    //     //if ($q->num_rows() > 0) {
    //         $tid = $transactionInfo->row()->id;
    //         $pid = $transactionInfo->row()->profile_id;
    //         $this->db->reset_query();
    //     //}
    //         //return null;
    //      // get all transactions that don't have this payment in applied payments table (a or b)
    //     $this->db->select('t.id, t.description, th.transaction_date AS date, b.due_date, (t.credit - t.debit) AS amount, ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance, 0 AS payment');
    //     $this->db->from('transactions t'); 
    //     $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id = $this->ap  AND t.profile_id ='. $pid); 
    //     $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
    //     $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
    //     FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
    //     FROM applied_payments WHERE transaction_id_a !=' . $tid .
    //     ' UNION ALL
    //     SELECT transaction_id_b AS trans_id, amount 
    //     FROM applied_payments WHERE transaction_id_a !=' . $tid . ') trans
    //     GROUP BY trans_id) transum','t.id = transum.trans_id','left');
    //     $this->db->where('t.id !=',$tid);
    //     $this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0 AND t.id NOT IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $tid . ')');
    //     //$this->db->where(array('((t.debit - t.credit) - transum.amounts)!='=> 0));
    //     //$this->db->or_where('((t.debit - t.credit) - transum.amounts) IS NULL');
    //     //$this->db->where('t.id NOT IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $tid . ')'); 
        
        
    //      $other_payments = $this->db->get_compiled_select();
    //      $this->db->reset_query();

    //     // //get all transactions that have this payment in applied payments table (a) open balance not including this payment
    //     $this->db->select('t.id, t.description, th.transaction_date AS date, b.due_date, (t.credit - t.debit) AS amount, ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance, IFNULL(ap.amount,0) AS payment');
    //     $this->db->from('transactions t'); 
    //     $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id = $this->ap  AND t.profile_id ='. $pid); 
    //     $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
    //     $this->db->join('(SELECT transaction_id_b AS trans_id, amount
    //     FROM applied_payments
    //     WHERE transaction_id_a =' .  $tid . ') ap','t.id = ap.trans_id','left');
    //     $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
    //     FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
    //     FROM applied_payments
    //     WHERE transaction_id_a !='. $tid .
    //     ' UNION ALL
    //     SELECT transaction_id_b AS trans_id, amount 
    //     FROM applied_payments WHERE transaction_id_a !='. $tid .') trans GROUP BY trans_id) transum','t.id = transum.trans_id','left');
    //     $this->db->where('t.id IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $tid . ')');         
    //     // $sql = $this->db->get_compiled_select();
    //     // echo $sql;
    //     // return;
    //     $this_payment = $this->db->get_compiled_select();
    //     $this->db->reset_query();
    //     //$q = $this->db->get();
    //     $q = $this->db->query("$other_payments UNION $this_payment");
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return null;
    // }
    public function getTransactionsInEdit($profile_id)
    {
        $this->db->select('t.id, th.id AS th_id, th.transaction_type AS type, props.name AS property_name,  th.transaction_ref, a.name AS account_name, th.memo AS description, ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance, 0 AS payment');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ap);
        $this->db->join('bills b', 't.trans_id = b.trans_id','left');
        $this->db->join('profiles p', 't.profile_id = p.id');
        $this->db->join('properties props', 't.property_id = props.id');
        $this->db->join('accounts a', 't.account_id = a.id');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments
        UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0');
        $this->db->where('t.profile_id', $profile_id);
        
         $this->db->ORDER_BY('th.transaction_date ASC');
        // $sql = $this->db->get_compiled_select();
        // echo $sql;
        // return;
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
       
    }

    public function getTransactionsInEditNew($profile_id, $th_id, $property_id)
    {

        $this->db->select('id, profile_id, account_id, property_id');
        $this->db->from('transactions'); 
        $this->db->where(array('trans_id'=>$th_id, 'account_id' => $this->ap));
        $q = $this->db->get();
        //if ($q->num_rows() > 0) {
            $tid = $q->row()->id;
            //$pid = $q->row()->profile_id;
            //$account_id = $q->row()->account_id;
            //$property_id = $q->row()->property_id;
            $this->db->reset_query();
            
        $this->db->select('t.id, th.id AS th_id, th.transaction_type AS type, props.name AS property_name,  th.transaction_ref, a.name AS account_name, th.memo AS description, ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance, IFNULL(ap.amount,0) AS payment');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ap);
        $this->db->join('bills b', 't.trans_id = b.trans_id','left');
        $this->db->join('profiles p', 't.profile_id = p.id');
        $this->db->join('properties props', 't.property_id = props.id');
        $this->db->join('accounts a', 't.account_id = a.id');
        $this->db->join('applied_payments ap','t.id = ap.transaction_id_b AND transaction_id_a =' .  $tid,  ' left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments WHERE transaction_id_a !=' . $this->db->escape($tid) .
        ' UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments WHERE transaction_id_a !=' . $this->db->escape($tid) . ') trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0');
        $this->db->where('t.profile_id', $profile_id);
        $this->db->where('t.property_id', $property_id);
        $this->db->where('t.id !=',$tid);
        //if($profile_id == $pid)$this->db->where('t.id IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $this->db->escape($tid) . ')');
         $this->db->ORDER_BY('th.transaction_date ASC');
        // $sql = $this->db->get_compiled_select();
        // echo $sql;
        // return;
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
       
    }
    public function getTransactionsEdit($th_id)
    {   
        $this->db->select('id, profile_id, account_id, property_id');
        $this->db->from('transactions'); 
        $this->db->where(array('trans_id'=>$th_id, 'account_id' => $this->ap));
        $q = $this->db->get();
        //if ($q->num_rows() > 0) {
            $tid = $q->row()->id;
            $pid = $q->row()->profile_id;
            //$account_id = $q->row()->account_id;
            $property_id = $q->row()->property_id;
            $this->db->reset_query();
        //}
        
         // get all transactions that don't have this payment in applied payments table (a or b)
        $this->db->select('t.id, th.id AS th_id, th.transaction_type AS type, t.account_id, b.due_date, p.name AS property_name, a.name AS account_name, th.memo AS description, th.transaction_ref, (t.credit - t.debit) AS amount, ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance, 0 AS payment');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ap  .' AND t.profile_id ='. $pid.' AND t.property_id ='. $property_id);
        $this->db->join('properties p', 't.property_id = p.id','left');
        $this->db->join('accounts a', 't.account_id = a.id','left'); 
        $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments WHERE transaction_id_a !=' . $this->db->escape($tid) .
        ' UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments WHERE transaction_id_a !=' . $this->db->escape($tid) . ') trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('t.id !=',$tid);
        $this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0 AND t.id NOT IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $this->db->escape($tid) . ')');
        //$this->db->where(array('((t.debit - t.credit) - transum.amounts)!='=> 0));
        //$this->db->or_where('((t.debit - t.credit) - transum.amounts) IS NULL');
        //$this->db->where('t.id NOT IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $tid . ')'); 
        
        
         $other_payments = $this->db->get_compiled_select();
         $this->db->reset_query();

        // //get all transactions that have this payment in applied payments table (a) open balance not including this payment
        $this->db->select('t.id, th.id AS th_id, th.transaction_type AS type, t.account_id, b.due_date, p.name AS property_name, a.name AS account_name, th.memo AS description, th.transaction_ref, (t.credit - t.debit) AS amount, ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance, IFNULL(ap.amount,0) AS payment');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ap . ' AND t.profile_id ='. $pid);
        $this->db->join('properties p', 't.property_id = p.id','left');
        $this->db->join('accounts a', 't.account_id = a.id','left');  
        $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
        $this->db->join('applied_payments ap','t.id = ap.transaction_id_b AND transaction_id_a =' .  $tid);
        // $this->db->join('(SELECT transaction_id_b AS trans_id, amount
        //  FROM applied_payments
        //  WHERE transaction_id_a =' .  $tid . ') ap','t.id = ap.trans_id','left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0 - amount AS amount
        FROM applied_payments
        WHERE transaction_id_a !='. $this->db->escape($tid) .
        ' UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments WHERE transaction_id_a !='. $this->db->escape($tid) .') trans GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('t.id IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $this->db->escape($tid) . ')');
        $this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0');//make sure this filter right         
        // $sql = $this->db->get_compiled_select();
        // echo $sql;
        // return;
        $this_payment = $this->db->get_compiled_select();
        $this->db->reset_query();
        //$q = $this->db->get();
        $q = $this->db->query("$other_payments UNION $this_payment");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }
//     public function applyPaymentsOld($date, $transactions)
//     {   
       
//         foreach($transactions as &$transaction){
//             $account_id = $transaction[key($transaction)]['account_id'];
//             $property_id = $transaction[key($transaction)]['property_id'];
//             $profile_id = $transaction[key($transaction)]['profile_id'];
//             $total = array_sum(array_column($transaction, 'amount'));

//             if($total > 0){

//             $this->db->trans_start();
                
//             $header = ['transaction_type' => 7,'transaction_ref' => 'not done!', 'transaction_date' => $date,'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
//             $total = array_sum(array_column($transaction, 'amount'));
//             $this->db->insert('transaction_header', $header);
//             $trans_id = $this->db->insert_id();
//             $pmt_account =  ['account_id' => $account_id,'profile_id' => $profile_id, 'property_id' => $property_id, 'trans_id' => $trans_id, 'credit' => $total];
//             $accounts_payable = ['account_id' => $this->ap,'profile_id' => $profile_id, 'property_id' => $property_id, 'trans_id' => $trans_id, 'debit' => $total];
//             $this->db->insert('transactions',$pmt_account);
//             $this->db->insert('transactions',$accounts_payable);
//             $transaction_id_a = $this->db->insert_id();

//             $payments = [];
//             $payment = [];
//             foreach($transaction as &$trans){
//                 $payment['transaction_id_a'] = $transaction_id_a;
//                 $payment['transaction_id_b'] = $trans['transaction_id_b'];
//                 $payment['amount'] = $trans['amount'];
//                 $payment['date_modified'] = date("Y-m-d H:i:s");

//                 $payments[] = $payment;

//             }

//             if(!empty($payments))
//         {
//             $this->db->insert_batch('applied_payments', $payments);
//         }

//         $this->db->trans_complete();
        
//     }
//         }
//         return true;
        
// }



public function applyPayments($header, $transaction, $pmt_account, $accounts_payable, $print)
{   
    $this->db->trans_start();

    $trans_id = $this->addHeader($header, 7);    
    
    $pmt_account['trans_id'] = $trans_id;
    $accounts_payable['trans_id'] = $trans_id;
    $this->db->insert('transactions',$pmt_account);
    $this->db->insert('transactions',$accounts_payable);
    $transaction_id_a = $this->db->insert_id();

    $payments = [];
    $payment = [];
    foreach($transaction as &$trans){
        $payment['transaction_id_a'] = $transaction_id_a;
        $payment['transaction_id_b'] = $trans['transaction_id_b'];
        $payment['amount'] = $trans['amount'];
        $payment['date_modified'] = date("Y-m-d H:i:s");

        // I probably put into $payments array to satisfy the insert_batch
        $payments[] = $payment;

    }

if(!empty($payments))
{
    $this->db->insert_batch('applied_payments', $payments);
}


if($print) $data = $this->onPrintMany($trans_id, $pmt_account['account_id'], $transaction_id_a);
$data->memo = implode(",",array_column($data->details, 'transaction_ref'));

if($data->memo){
    $this->db->where('id', $trans_id);
    $this->db->update('transaction_header', array('memo' => $data->memo));
}

//}
$this->db->trans_complete();

if ($this->db->trans_status() === FALSE)
{
    return false;
}
if($print){
    return $data;
}else{
    return true;
} 
        
}


public function getBillDetails($transaction_id_a){
        $this->db->select('t.id, th.id AS th_id, th.transaction_date AS bill_date, tt.name AS type,  th.transaction_ref, (t.credit - t.debit) AS original_amount,  ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance, IFNULL(ap.amount,0) AS payment');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ap);
        $this->db->join('transaction_type tt', 'th.transaction_type = tt.id','left');
        $this->db->join('bills b', 't.trans_id = b.trans_id','left');
        
        $this->db->join('applied_payments ap','t.id = ap.transaction_id_b AND transaction_id_a =' .  $transaction_id_a);
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments WHERE transaction_id_a !=' . $transaction_id_a .
        ' UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments WHERE transaction_id_a !=' . $transaction_id_a . ') trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        //$this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0');
        //$this->db->where('t.profile_id', $profile_id);
        //$this->db->where('t.property_id', $property_id);
        $this->db->where('t.id !=',$transaction_id_a);
        //if($profile_id == $pid)$this->db->where('t.id IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $this->db->escape($tid) . ')');
         $this->db->ORDER_BY('th.transaction_date ASC');
        // $sql = $this->db->get_compiled_select();
        // echo $sql;
        // return;
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
       
    }

    public function onPrintMany($trans_id, $account_id, $transaction_id_a=null,  $single = null )
    {   
        if($transaction_id_a == null){
            $this->db->select('id');
            $this->db->from('transactions'); 
            $this->db->where(array('trans_id'=>$trans_id, 'account_id' => $this->ap));
            $q = $this->db->get();
            $transaction_id_a = $q->row()->id;
            $this->db->reset_query();
        }
        $this->db->trans_start();
       // foreach($accounts as $account){
            //$pmt_account =  ['account_id' => $account_id,'profile_id' => $profile_id, 'property_id' => $property_id, 'credit' => $total];
            $this->db->select('th.id AS th_id, b.account_id as account_id, th.transaction_date as date, th.memo as memo, t.credit, b.routing, IFNULL(b.bank_name, "") as bank_name, IFNULL(b.bank_address, "") as bank_address, b.account_number, ifnull(b.next_check_num ,1) as next_check_num, CONCAT_WS( " ", p.first_name, p.last_name) as profile, p.address_line_1, p.address_line_2, p.city, p.state, p.area_code AS zip, IFNULL(e.name, pr.name) as eName, IFNULL(e.address, pr.address) as eAddress, IFNULL(e.city, pr.city) as eCity, IFNULL(e.state, pr.state) as eState, IFNULL(e.zip, pr.zip) as Ezip, IFNULL(e.email, "") as eEmail, IFNULL(e.phone, "") as ePhone');
            $this->db->from('banks b');
            $this->db->join('transactions t', 'b.account_id = t.account_id');
            $this->db->join('transaction_header th', 't.trans_id = th.id AND th.id ='. $trans_id);
            $this->db->join('profiles p', 't.profile_id = p.id');
            $this->db->join('properties pr', 'pr.id = t.property_id', 'left');
            $this->db->join('entities e', 'e.id = pr.entity_id', 'left');
            $this->db->where('b.account_id', $account_id);
            $this->db->order_by('t.id ASC');
            $this->db->limit(1);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                $q->row()->details = $this->getBillDetails($transaction_id_a);
               
                $data1 = $q->row();
                $data1 = $this->encryption_model->decryptThis($data1);
                $data[] = $data1;
            } 
            if ($q->num_rows() > 0) {
                $this->db->where('id', $trans_id);
                $this->db->update('transaction_header', array('transaction_ref' => $q->row()->next_check_num));
            }

      
        $this->db->where('id', $trans_id);
        $this->db->update('transaction_header', array('to_print' => 0));
        
        if( !isset($single) ){

            $this->db->set('next_check_num', 'next_check_num + 1', FALSE);
            $this->db->where('account_id', $account_id);
            $this->db->update('banks'); // gives UPDATE `mytable` SET `field` = 'field+1' WHERE `id` = 2
        }

        
        $this->db->trans_complete();
        
        return $data;
        
    }

//     public function editAppliedPaymentsOld($profile_id, $amount, $payedFrom, $property_id, $header, $applied_payments)
//     {   
        
//         $this->db->select('id');
//         $this->db->from('transactions'); 
//         $this->db->where(array('trans_id'=> $header['id'], 'account_id !=' => $this->ap));
//         $transactionInfo = $this->db->get();
//         //if ($q->num_rows() > 0) {
//             $pmt_acct_id = $transactionInfo->row()->id;
//             $this->db->reset_query();

//         $this->db->select('id');
//         $this->db->from('transactions'); 
//         $this->db->where(array('trans_id'=> $header['id'], 'account_id' => $this->ap));
//         $transactionInfo = $this->db->get();
//         //if ($q->num_rows() > 0) {
//             $acct_p_id = $transactionInfo->row()->id;
//             $this->db->reset_query();

//         //$header['last_mod_date'] = date("Y-m-d H:i:s");
//         $header['last_mod_by'] = $this->ion_auth->get_user_id();
//         $header['last_mod_date'] = date('Y-m-d H:i:s');
//         //$header['transaction_date'] = sqlDate($header['transaction_date']);
//         $this->db->update('transaction_header', $header, array('id' => $header['id']));
        
//         $accounts_payable = ['profile_id' => $profile_id, 'property_id' => $property_id,  'debit' => $amount];
//         $pmt_account =  ['account_id' => $payedFrom, 'profile_id' => $profile_id, 'property_id' => $property_id, 'credit' => $amount];
//         $this->db->update('transactions',$accounts_payable, array('id' => $acct_p_id));
//         $this->db->update('transactions',$pmt_account, array('id' => $pmt_acct_id));

//         $this->db->delete('applied_payments', array('transaction_id_a' => $acct_p_id));

//         // code for ones that are checked go into applied_payments array to be inserted
//         $applied_payments = array_filter($applied_payments, function($v) {
//             return  $v['applied'] == 1;
//         });
//         // I think the code is missing for entering transaction_id_b, need to check over --  I think it's good getting from front end
//         $this->applyPaymentsAdd($applied_payments, $acct_p_id);
//         return true;
        
// }
                                       
public function editAppliedPayments($header, $pmt_account, $accounts_payable, $pmt_acct_id, $acct_p_id, $applied_payments, $printEditBill)
{   
        $this->db->trans_start();

        $this->updateHeader($header, $header['id']);    
        
        $this->db->update('transactions',$accounts_payable, array('id' => $acct_p_id));
        $this->db->update('transactions',$pmt_account, array('id' => $pmt_acct_id));

        $this->db->delete('applied_payments', array('transaction_id_a' => $acct_p_id));

        $this->applyPaymentsAdd($applied_payments, $acct_p_id);

        if($printEditBill) $data = $this->onPrintMany($header['id'], $pmt_account['account_id'], $acct_p_id);
        $data->memo = implode(",",array_column($data->details, 'transaction_ref'));

        if($data->memo){
            $this->db->where('id', $header['id']);
            $this->db->update('transaction_header', array('memo' => $data->memo));
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
             return false;
        }

        if($printEditBill){
            return $data;
        }else{
            return true;
        } 

        
}

    public function getClasses($addtype = false)
    {
        $q = $this->db->get('classes');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'account_type';
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

        public function getRecInfo($id)
    {   
        $this->db->select('id, rec_id, clr');
        $this->db->from('transactions');
        $this->db->where(array('trans_id' => $id, 'account_id !=' => $this->ap));//AND account id != accounts payable won't have to do array shift 
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data = $row;
            }
            return $data;
        }
        return null;
    }

}
