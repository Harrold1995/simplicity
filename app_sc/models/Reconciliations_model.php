<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reconciliations_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->bf =$this->site->settings->bank_fees;
        $this->ii = $this->site->settings->interest_income;
        $this->uf = $this->site->settings->undeposited_funds;

    }

    function getAccounts()
    {
        $this->db->select('id, name, accno');
        $this->db->from('accounts');
        $this->db->where_in('account_types_id', array(1,6));
        $this->db->where('active', 1);
        // $this->db->where('account_types_id', 1);// bank
        // $this->db->or_where('account_types_id', 6);//Credit Card
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    function getAccount($id)
    {
        $this->db->select('accounts.id, accounts.name, accno, property, properties.name as propname');
        $this->db->from('accounts');
        $this->db-> join ('banks', 'banks.account_id = accounts.id', 'left');
        $this->db-> join ('properties', 'banks.property = properties.id', 'left');
        $this->db->where('accounts.id', $id);
        //$this->db->where('active', 1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return array();
    }

    
    public function getLastReconciliation($aid)
    {
        $this->db->select('if((closed = 0 OR closed IS NULL), id, null) AS r_id , IFNULL(closed, 0) AS closed, IFNULL(if((closed = 0 OR closed IS NULL), beginning_bal, ending_bal),0) AS beginning_bal, IFNULL(if((closed = 0 OR closed IS NULL), interest_earned, 0),0) AS interest_earned , IFNULL(if((closed = 0 OR closed IS NULL), service_charge, 0),0) AS service_charge, date_created, if((closed = 0 OR closed IS NULL), ending_bal, 0) AS statement_bal, if((closed = 0 OR closed IS NULL), statement_end_date, NULL) AS statement_end_date, statement_attachment');
        $this->db->from('reconciliations');
        $this->db->where('account_id', $aid);
        $this->db->ORDER_BY('id DESC');
        $this->db->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
        return $q->row();
            }

            $fields = (object) array(
                "r_id"=>null
                , "closed"=>"1"
                , "beginning_bal"=>"0"
                , "interest_earned"=>"0"
                , "date_created"=>null
                , "service_charge"=>"0"
                , "statement_bal"=>"0"
                , "date_created"=>null
                , "statement_end_date"=>null
                , "statement_attachement"=>null
            ); 
            return $fields;

    }

    public function getLastReconciliation2($aid)
    {
        $this->db->select('if((closed = 0 OR closed IS NULL), id, null) AS r_id , IFNULL(closed, 0) AS closed, IFNULL(if((closed = 0 OR closed IS NULL), beginning_bal, ending_bal),0) AS beginning_bal, IFNULL(if((closed = 0 OR closed IS NULL), interest_earned, 0),0) AS interest_earned , IFNULL(if((closed = 0 OR closed IS NULL), service_charge, 0),0) AS service_charge, date_created, if((closed = 0 OR closed IS NULL), ending_bal, 0) AS statement_bal, statement_end_date');
        $this->db->from('reconciliations');
        $this->db->where('account_id', $aid);
        $this->db->ORDER_BY('id DESC');
        $this->db->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
        return $q->row();
            }
        return array(
            "r_id"=>null
            , "closed"=>null
            , "beginning_bal"=>"0"
            , "interest_earned"=>"0"
            , "service_charge"=>"0"
            , "statement_bal"=>"0"
            
            
        ); 

    }

    public function getAllReconciliation($aid)
    {
        $this->db->select('r.id, r.ending_bal, r.statement_end_date, r.closed, r.account_id, r.statement_attachment, bal.run as bal, r.type');
        $this->db->from('reconciliations r');
        $this->db->join('(select amount, transaction_date, gen_date, @runtot :=@runtot + ifnull(amount,0) as run from (SELECT sum(debit - credit) as amount , transaction_date FROM `transactions` join transaction_header on transactions.trans_id = transaction_header.id where account_id = '.$aid.' group by transaction_date) t1 JOIN (SELECT @runtot:=0) r right join 
        (select gen_date from (select adddate("1970-01-01",t4*10000 + t3*1000 + t2*100 + t1*10 + t0) gen_date from (select 0 t0 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0, (select 0 t1 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1, (select 0 t2 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2, (select 0 t3 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3, (select 0 t4 union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v where gen_date between "1970-01-01" and "2070-01-01") datesn on datesn.gen_date = transaction_date) as bal','r.statement_end_date = bal.gen_date','left');
        $this->db->where('account_id', $aid);
        $this->db->where('account_id !=', 'auto');
        $this->db->ORDER_BY('id DESC');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $newDate = strtotime($row->statement_end_date); 
                //$bookBalance = $this->bookBalance($row->account_id, $row->statement_end_date);
                $row->bookBalance = $row->bal;
                $data[] = $row;
            }
        return $data;
            }
        return false;

    }

    public function bookBalance($aid, $date)  // not used anymore veryinneficient  added new join in getAllReconciliation function instead
    {
        $this->db->select('SUM(t.credit) AS credit, SUM(t.debit) AS debit, ac.debit_credit, 1 AS balance');
        $this->db->from('transactions t');
        $this->db->join('accounts a', 't.account_id = a.id');
        $this->db->join('account_types at', 'at.id = a.account_types_id');
        $this->db->join('account_category ac', 'ac.id = at.account_category_id');
        $this->db->join('transaction_header th', 'th.id = t.trans_id');
        $this->db->where('th.transaction_date <', $date);
        $this->db->where('t.account_id', $aid);
        //$this->db->where('a.active', 1);
        //$this->db->group_by('a.id');

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($row->debit_credit === "credit") {
                    $row->balance = $row->credit - $row->debit;
                } elseif ($row->debit_credit === "debit") {
                    $row->balance = $row->debit - $row->credit;
                } else {
                    $row->balance = 0;
                }
                $data[] = $row;
            }
            return $row->balance;
        }
    }
    
    public function getCredits($aid, $rec_id)
    {   
        $rec = $rec_id ? ' OR t.rec_id ='. $rec_id . ' AND (t.clr !=1 OR t.clr IS NULL))' : ')';
        $this->db->select('max(t.id) as id, max(t.rec_id) as rec_id, th.id AS th_id, max(th.transaction_date) AS date, max(tt.name) AS type, max(tt.id) as type_id, max(th.transaction_ref) AS num, max(CONCAT_WS(" ",p.first_name,p.last_name)) AS vendor, sum(t.credit) As amount');
        $this->db->from('transactions t');
        $this->db->join('transaction_header th', 't.trans_id = th.id');
        $this->db->join('transaction_type tt', 'th.transaction_type = tt.id');
        $this->db->join('profiles p', 'p.id = t.profile_id','left');
        //$this->db->where(array('t.account_id' => $aid,'t.credit !=' =>0, 't.clr' => 0));
        $this->db->where(array('t.account_id' => $aid,'t.credit !=' =>0));//,'t.rec_id' => NULL));
        $this->db->where('(t.rec_id IS NULL' . $rec);
        $this->db->ORDER_BY('th.transaction_date ASC');
        $this->db->GROUP_BY('th.id');

        //$this->db->distinct();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
       
    }

    public function getDebits($aid, $rec_id)
    {   
        $rec = $rec_id ? ' OR t.rec_id ='. $rec_id . ' AND (t.clr !=1 OR t.clr IS NULL))' : ')';
        $this->db->select('max(t.id) as id,max(t.rec_id) as rec_id, th.id AS th_id, max(th.transaction_date) AS date, max(tt.id) as type_id, max(tt.name) AS type,max(th.transaction_ref) AS num, max(CONCAT_WS(" ",p.first_name,p.last_name))AS vendor, sum(t.debit) AS amount');
        $this->db->from('transactions t');
        $this->db->join('transaction_header th', 't.trans_id = th.id');
        $this->db->join('transaction_type tt', 'th.transaction_type = tt.id');
        $this->db->join('profiles p', 'p.id = t.profile_id','left');
        //$this->db->where(array('t.account_id' => $aid,'t.debit !=' =>0, 't.clr' => 0));
        $this->db->where(array('t.account_id' => $aid,'t.debit !=' =>0));
        $this->db->where('(t.rec_id IS NULL' . $rec);
        $this->db->ORDER_BY('th.transaction_date ASC');
        $this->db->GROUP_BY('th.id');
       //$this->db->distinct();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
       
    }

    public function getAllTrans($aid, $rec_id)
    {   
        $rec = $rec_id ? ' OR t.rec_id ='. $rec_id . ' AND (t.clr !=1 OR t.clr IS NULL))' : ')';
        $this->db->select('max(t.id) as id,max(t.rec_id) as rec_id, max(th.id) AS th_id, max(th.transaction_date) AS date, max(tt.id) as type_id, max(tt.name) AS type,max(th.transaction_ref) AS num, max(CONCAT_WS(" ",p.first_name,p.last_name))AS vendor, sum((debit - credit)) AS amount');
        $this->db->from('transactions t');
        $this->db->join('transaction_header th', 't.trans_id = th.id');
        $this->db->join('transaction_type tt', 'th.transaction_type = tt.id');
        $this->db->join('profiles p', 'p.id = t.profile_id','left');
        //$this->db->where(array('t.account_id' => $aid,'t.debit !=' =>0, 't.clr' => 0));
        $this->db->where(array('t.account_id' => $aid));
        $this->db->where('(t.rec_id IS NULL' . $rec);
        $this->db->ORDER_BY('th.transaction_date ASC');
        $this->db->GROUP_BY('th.id');
       //$this->db->distinct();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
       
    }

    public function getBankTrans($aid)
    {
        $this->db->select('plaid_trans.amount as amount, plaid_trans.date as date, plaid_trans.name as num, plaid_trans.transaction_id as id, plaid_trans.transaction_id as transaction_id, plaid_trans.transaction_id as th_id, "auto" as type, banks.account_id as bank_account, banks.property as property, banks.property as property_id, properties.name as property, "name" as name');
        $this->db->from('plaid_trans');
        $this->db->join('plaid_banks pb', 'plaid_trans.account_id = pb.plaid_id');
        $this->db->join('banks', 'banks.id = pb.bank_id');
        $this->db->join('properties', 'properties.id = banks.property', 'left');
        $this->db->where(array('banks.account_id' => $aid));
        $this->db->where('plaid_trans.trans_match IS NULL');
        $this->db->where('plaid_trans.removed < 1');
        $this->db->where('plaid_trans.pending < 1');
        $this->db->ORDER_BY('plaid_trans.date ASC');
       //$this->db->distinct();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }
  
    public function startReconciliation($reconciliation, $transactions,  $statement_end_date, $closed=0, $property=0)
    { 
        $service_charge = $reconciliation['service_charge'];
        $interest_earned = $reconciliation['interest_earned'];

        $this->db->trans_start(); 
        
        if($closed === 'true'){
            $reconciliation['closed'] = 1;
            $reconciliation['service_charge']=0;
            $reconciliation['interest_earned']=0;
        }
        $reconciliation['type']='manual';
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $this->upload->do_upload('statement_attachment');
        $reconciliation['statement_attachment'] = $this->upload->data('file_name');

        $reconciliation['date_created'] = date("Y-m-d H:i:s");
        $this->db->insert('reconciliations', $reconciliation);
        $rec_id = $this->db->insert_id();
        if($reconciliation['statement_attachment'] != ""){
            $this->db->insert('documents', Array("name" => $reconciliation['statement_attachment'], "reference_id" => $rec_id, "type" => "18"));
        }
        $transactions1 = array_filter($transactions, function($v) {
            return $v == 1;
            });
    
        $transactions0 = array_filter($transactions, function($v) {
                return $v == 0;
                });
    
        //return;

         if($closed === 'true'){
            if (!empty($transactions1)) {
                $this->db->where_in('trans_id', array_keys($transactions1));
                $this->db->where('account_id',$reconciliation['account_id']);
                $this->db->update('transactions', array('rec_id' => $rec_id,'clr' => 1));
            }
            if($service_charge > 0){
                $transactionHeader = ['transaction_type' => 4, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id(), 'transaction_date' => $statement_end_date, 'memo' => 'Service Charge'];
                $this->db->insert('transaction_header',$transactionHeader);
                $trans_id = $this->db->insert_id();
                
                $creditTransaction = ['account_id' => $reconciliation['account_id'], 'trans_id' => $trans_id,'property_id' => $property, 'description' => 'Service Charge', 'debit' => 0, 'credit' => $service_charge, 'clr' => 1, 'rec_id' => $rec_id];
                $debitTransaction = ['account_id' => $this->bf, 'trans_id' => $trans_id, 'property_id' => $property, 'description' => 'Service Charge','debit' => $service_charge, 'credit' => 0,  'clr' => null, 'rec_id' => null];
                $bothTransactions[] = $creditTransaction;
                $bothTransactions[] = $debitTransaction;
                $this->db->insert_batch('transactions',$bothTransactions);
                $this->db->insert('checks',['trans_id' => $trans_id]);
         }
            if($interest_earned > 0){
                $transactionHeader = ['transaction_type' => 8, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id(), 'transaction_date' => $statement_end_date, 'memo' => 'Interest Earned'];
                $this->db->insert('transaction_header',$transactionHeader);
                $trans_id = $this->db->insert_id();
                $debitTransaction = ['account_id' => $reconciliation['account_id'], 'property_id' => $property, 'line_number' => 0, 'trans_id' => $trans_id, 'description' => 'Interest Earned','debit' => $interest_earned, 'credit' =>0, 'clr' => 1, 'rec_id' => $rec_id];
                $creditTransaction = ['account_id' => $this->ii, 'property_id' => $property, 'line_number' => 2, 'trans_id' => $trans_id, 'description' => 'Interest Earned', 'debit' => 0, 'credit' => $interest_earned, 'clr' => null, 'rec_id' => null];
                $ufTransaction =  ['account_id' => $this->uf, 'property_id' => $property, 'line_number' => null, 'trans_id' => $trans_id, 'description' => 'uf line', 'debit' => 0, 'credit' => 0, 'clr' => null, 'rec_id' => null];
                $twoTransactions[] = $debitTransaction;
                $twoTransactions[] = $creditTransaction;
                $twoTransactions[] = $ufTransaction;

                $this->db->insert_batch('transactions',$twoTransactions);
                
        }

             
         }else{
            if (!empty($transactions1)) {
                $this->db->where_in('trans_id', array_keys($transactions1));
                $this->db->where('account_id',$reconciliation['account_id']);
                $this->db->update('transactions', array('rec_id' => $rec_id)); 
            }
         }
         if (!empty($transactions0)) {
            $this->db->where_in('trans_id', array_keys($transactions0));
            $this->db->where('account_id',$reconciliation['account_id']);
            $this->db->update('transactions', array('rec_id' => NULL));
        }

        $this->db->trans_complete(); 

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }

        $msg = ($closed === 'true') ? 'reconciled' : 'saved';
        return $msg;
    }

    public function autoReconciliation($reconciliation, $transactions, $banktrans)
    { 


        $this->db->trans_start(); 
            
        $reconciliation['closed'] = 1;
        $reconciliation['type'] = 'auto';
        $reconciliation['beginning_bal'] = count($transactions);      
        $reconciliation['date_created'] = date("Y-m-d H:i:s");
        $this->db->insert('reconciliations', $reconciliation);
        $rec_id = $this->db->insert_id();

        $transactions1 = array_filter($transactions, function($v) {
            return $v == 1;
            });

        $bankTrans1 = array_filter($banktrans, function($v) {
            return $v == 1;
            });
    

        if (!empty($transactions1)) {
            $this->db->where_in('trans_id', array_keys($transactions1));
            $this->db->where('account_id',$reconciliation['account_id']);
            $this->db->update('transactions', array('rec_id' => $rec_id,'clr' => 1));
        }

        if (!empty($bankTrans1)) {
            $this->db->where_in('transaction_id', array_keys($bankTrans1));
            $this->db->update('plaid_trans', array('trans_match' => $rec_id));
        }



        $this->db->trans_complete(); 

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }

        $msg = ($closed === 'true') ? 'reconciled' : 'saved';
        return $msg;
    }
    
    public function ignoreTrans($trans){
        $this->db->update('plaid_trans', array('trans_match' => 1) , array('transaction_id' => $trans));
        return true;
    }

    public function unIgnoreTrans($trans){
        $this->db->update('plaid_trans', array('trans_match' => null) , array('transaction_id' => $trans));
        return true;
    }

    public function ignoreBefore($bank, $date){
        $this->db->update('plaid_trans', array('trans_match' => null) , array('trans_match' => 1));
        $this->db->update('plaid_trans', array('trans_match' => 1) , array('date <' => $date, 'trans_match' => null));
        $this->db->update('banks', array('auto_rec_start_date' => $date) , array('JSON_EXTRACT(custom, "$.plaid_acct") =' => $bank));
        return true;
    }

    public function editReconciliation($reconciliation, $transactions,  $statement_end_date, $closed, $property = 0)
    {  

        $serviceCharge = $reconciliation['service_charge'];
        $interest_earned = $reconciliation['interest_earned'];

        $this->db->trans_start();  

        if($closed === 'true'){
            
            $reconciliation['closed'] = 1;
            $reconciliation['service_charge']=0;
            $reconciliation['interest_earned']=0;
        }
        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $this->upload->do_upload('statement_attachment');
        $reconciliation['statement_attachment'] = $this->upload->data('file_name');
        if($this->upload->data('file_name') != ""){
            $reconciliation['statement_attachment'] = $this->upload->data('file_name');
            $this->db->insert('documents', Array("name" => $reconciliation['statement_attachment'], "reference_id" => $reconciliation['id'], "type" => "18"));
        }
        $this->db->update('reconciliations', $reconciliation , array('id' => $reconciliation['id']));
        
        $transactions1 = array_filter($transactions, function($v) {
            return $v == 1;
            });
    
        $transactions0 = array_filter($transactions, function($v) {
                return $v == 0;
                });
    
        
        if($closed === 'true')
        {
            if (!empty($transactions1)) {
            $this->db->where_in('trans_id', array_keys($transactions1));
            $this->db->where('account_id',$reconciliation['account_id']);
            $this->db->update('transactions', array('rec_id' => $reconciliation['id'],'clr' => 1));
        }
        if($service_charge> 0){
            $transactionHeader = ['transaction_type' => 4, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id(), 'transaction_date' => $statement_end_date, 'memo' => 'Service Charge'];
            $this->db->insert('transaction_header',$transactionHeader);
            $trans_id = $this->db->insert_id();
            $creditTransaction = ['account_id' => $reconciliation['account_id'], 'property_id' => $property, 'trans_id' => $trans_id, 'description' => 'Service Charge', 'debit' => 0, 'credit' => $serviceCharge, 'clr' => 1, 'rec_id' => $reconciliation['id']];
            $debitTransaction = ['account_id' => $this->bf, 'property_id' => $property, 'trans_id' => $trans_id, 'description' => 'Service Charge','debit' => $serviceCharge, 'credit' => 0, 'clr' => 0, 'rec_id' => Null];
            $bothTransactions[] = $creditTransaction;
            $bothTransactions[] = $debitTransaction;
            $this->db->insert_batch('transactions',$bothTransactions);
            $this->db->insert('checks',['trans_id' => $trans_id]);
     }
        if($interest_earned > 0){
            $transactionHeader = ['transaction_type' => 8, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id(), 'transaction_date' => $statement_end_date, 'memo' => 'Interest Earned'];
            $this->db->insert('transaction_header',$transactionHeader);
            $trans_id = $this->db->insert_id();
            $debitTransaction = ['account_id' => $reconciliation['account_id'], 'property_id' => $property, 'trans_id' => $trans_id, 'description' => 'Interest Earned','debit' => $interest_earned, 'credit' => 0, 'clr' => 1, 'rec_id' => $reconciliation['id']];
            $creditTransaction = ['account_id' => $this->ii, 'property_id' => $property, 'trans_id' => $trans_id, 'description' => 'Interest Earned', 'debit' => 0, 'credit' => $interest_earned, 'clr' => 1, 'rec_id' => $reconciliation['id']];
            $ufTransaction =  ['account_id' => $this->uf, 'property_id' => $property, 'line_number' => null, 'trans_id' => $trans_id, 'description' => 'uf line', 'debit' => 0, 'credit' => 0, 'clr' => null, 'rec_id' => null];
            $twoTransactions[] = $ufTransaction;
            $twoTransactions[] = $debitTransaction;
            $twoTransactions[] = $creditTransaction;
            $this->db->insert_batch('transactions',$twoTransactions);
            //$this->db->insert('xxxxx',['trans_id' => $trans_id]);
    }
        }else
        {   
            if (!empty($transactions1)) {
                $this->db->where_in('trans_id', array_keys($transactions1));
                $this->db->where('account_id',$reconciliation['account_id']);
                $this->db->update('transactions', array('rec_id' => $reconciliation['id'])); 
            }
        }
        if (!empty($transactions0)) {
            $this->db->where_in('trans_id', array_keys($transactions0));
            $this->db->where('account_id',$reconciliation['account_id']);
            $this->db->update('transactions', array('rec_id' => NULL));
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        
        $msg = ($closed === 'true') ? 'reconciled' : 'saved';
        return $msg;
    }

    function deleteRec($rec_id, $type){
        $this->db->trans_start();

        $this->db->where('id', $rec_id);
        $this->db->delete('reconciliations');
        
        if($type == 'auto'){
            $this->db->where('trans_match', $rec_id);
            $this->db->update('plaid_trans', array('trans_match' => NULL));
        }
        $this->db->where('rec_id', $rec_id);
        $this->db->update('transactions', array('rec_id' => NULL,'clr' => NULL));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        return true;
    }

    function reopenRec($rec_id){
        $this->db->trans_start();

        $this->db->where('id', $rec_id);
        $this->db->update('reconciliations', array('closed' => NULL));

        $this->db->where('rec_id', $rec_id);
        $this->db->update('transactions', array('clr' => NULL));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        return true;
    }

    function clearedTransactions($id){
        $this->db->select('SUM(t.debit - t.credit) AS amount');
        $this->db->from('transactions t');
        $this->db->where('t.account_id', $id );
        $this->db->where('t.rec_id !=', null );
        $this->db->where("(t.clr=0 OR t.clr is null)");
        $this->db->group_by('t.account_id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return null;
       
    }

    function clearedTransactionsDebitCredit($aid){
        $this->db->select('IFNULL(COUNT(td.debit),0) AS cd, IFNULL(SUM(td.debit),0) AS sd, IFNULL(COUNT(tc.credit),0) AS cc, IFNULL(SUM(tc.credit),0) AS sc');
        $this->db->from('transactions t');
        $this->db->join('transactions td', 't.id = td.id  AND td.debit !=0 AND td.clr = 0 ','left');
        $this->db->join('transactions tc', 't.id = tc.id  AND tc.credit !=0 AND tc.clr = 0 ','left');
        $this->db->where(array('t.rec_id !=' =>  null , 't.account_id' => $aid ));
        $this->db->group_by('t.account_id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
       
    }

    
    function countClearedTransactions($id){
        $this->db->select('COUNT(t.id) AS count');
        $this->db->from('transactions t');
        $this->db->where('t.account_id', $id );
        $this->db->where('t.rec_id !=', null );
        $this->db->where('t.clr', 0 );
        $this->db->group_by('t.account_id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return null;
       
    }

    public function getAllAccounts($addtype = false)
    {
        $this->db->where('active', 1);
        $q = $this->db->get('accounts');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'account';
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function update_cleared($transactions, $newdata, $update_field){
        foreach ($transactions as $key =>$transaction){
        
            if($transaction==1){
                $datatest = array_column($newdata, 'id');
                $key2 = array_search($key, $datatest);
                if($key2 !== false) {
                    $test = $newdata[$key2];
                    $newdata[$key2]->$update_field = '1';   
                }
                 
            }
        }

        return $newdata;
    }


    public function refresh($reconciliation, $transactions, $banktrans, $type){
        $leftColumn=[];
        $rightColumn=[];
        
        if ($type == 'manual'){
            
            $leftColumn = $this->update_cleared($transactions, $this->getDebits($reconciliation['account_id'], $reconciliation['id']), 'rec_id');
            $rightColumn = $this->update_cleared($transactions, $this->getCredits($reconciliation['account_id'], $reconciliation['id']), 'rec_id');
            
        } else {
            $newsystrans = $this->getAllTrans($reconciliation['account_id'], $reconciliation['id']);
            $newbanktrans = $this->getBankTrans($reconciliation['account_id']);
            
            $leftColumn = $this->update_cleared($transactions,  $newsystrans, 'rec_id');
            $rightColumn = $this->update_cleared($banktrans,  $newbanktrans, 'trans_match');

        }

        $data->leftColumn = $leftColumn;
        $data->rightColumn = $rightColumn;
        return $data;
 

    }


}
