<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ReceivePayments_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ar = $this->site->settings->accounts_receivable;

    }

    function getTenants()
    {
        $this->db->select('id, CONCAT_WS(" ",first_name, last_name) AS tenant');
        $this->db->from('profiles');
        $this->db->where('profile_type_id',3);
        $this->db->where('active', 1);
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
        $q = $this->db->get('payment_methods');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    
    public function getDepositTo()
    {
        $this->db->select('id, name, accno');
        $this->db->from('accounts');
        $this->db->where('account_types_id', 1);
        $this->db->or_where('id', $this->site->settings->undeposited_funds);
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

    public function getProfileId($th_id)
    {
        $this->db->select('profile_id');
        $this->db->from('transactions');
        $this->db->where('trans_id', $th_id);
        $this->db->limit(1);
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->profile_id;
        }
        return null;
    }


    public function getProperties($profile_id = null)
    {
        if($profile_id){
            $this->db->distinct();
            $this->db->select('p.id, p.name');
            $this->db->from('leases_profiles lp');
            $this->db->join('units u', 'lp.unit_id = u.id AND lp.profile_id=' . $profile_id);
            $this->db->join('properties p', 'u.property_id = p.id');
            $this->db->where('p.active', 1);
            $this->db->ORDER_BY('p.id ASC');
        }else{
            $this->db->select('id, name');
            $this->db->from('properties');
        }
        
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;

    }

    public function getLeases()
    {
        $this->db->select('l.id, l.unit_id, CONCAT(l.start," - ", l.end) AS name, u.property_id as property_id, p.name as property, u.name as unit, lp.profile_id');
        $this->db->from('leases l');
        $this->db->join('units u', 'l.unit_id = u.id');
        $this->db->join('properties p', 'u.property_id = p.id');
        $this->db->join('leases_profiles lp', 'l.id = lp.lease_id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }
    
    public function getTransactions($lease_id, $profile_id)
    {   
        // if($property_id == null){
        //     $this->db->select('p.id');
        //     $this->db->from('leases_profiles lp');
        //     $this->db->join('units u', 'lp.unit_id = u.id AND lp.profile_id=' . $profile_id);
        //     $this->db->join('properties p', 'u.property_id = p.id');
        //     $this->db->where('p.active', 1);
        //     $this->db->ORDER_BY('p.id ASC');
        //     $this->db->limit(1);
            
        //     $q = $this->db->get();
        //     if ($q->num_rows() > 0) {
        //         $property_id = $q->row()->id;
        //     }
        // }

        //$property = $property_id ?  ' AND t.property_id ='. $property_id : '';
       
        $this->db->select('t.id, t.profile_id, t.lease_id, t.property_id, ifnull(t.description," ") as description, th.id as th_id, th.transaction_type, th.transaction_date AS date, ifnull(b.due_date," ") as due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, 0 AS received_payment');
        $this->db->from('transactions t'); 
        //$this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar . ' AND t.profile_id ='. $profile_id . $property);
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar . ' AND t.lease_id ='. $lease_id . ' AND (t.profile_id ='. $profile_id . ' OR t.profile_id IS NULL OR t.profile_id = 0)'
        //. ' 
        );
        $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments
        UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        //$this->db->where('((t.debit - t.credit) - transum.amounts)', '! = 0');
        $this->db->where('((t.debit - t.credit) - IFNULL(transum.amounts,0)) !=',  0);
        $this->db->where('th.transaction_type !=',  5);
        // $where = '(t.profile_id ='. $profile_id . ' OR t.profile_id IS NULL OR t.profile_id = 0)';
        // $this->db->where($where);
        //$this->db->or_where('t.profile_id',$pid);
        //(array('archived' => NULL));
        $this->db->ORDER_BY('th.transaction_date ASC');
        // $sql = $this->db->get_compiled_select();
        // echo $sql;
        // return;
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            //$data['properties'] = $this->getProperties($profile_id);
            return $data;
        }
        return null;
       
    }
    // public function getHeaderEdit($th_id)
    // {
    //     $this->db->select('th.id, t.id AS transaction_id_a, th.memo, th.transaction_date AS date, th.transaction_ref, CONCAT(DATE_FORMAT(th.last_mod_date, "%m/%d/%Y")," ",TIME_FORMAT(th.last_mod_date, "%r")) AS modified, th.last_mod_by, t.profile_id, CONCAT_WS(" ",p.first_name,p.last_name) AS user, t.credit, cp.trans_id AS cp_tid, cp.payment_method, cp.deposit_on, dt.account_id AS deposit_to, t.deposit_id AS tdeposit_id, dt.deposit_id AS deposit_id');
    //     $this->db->from('transaction_header th'); 
    //     $this->db->join('transactions t', 'th.id = t.trans_id AND t.trans_id ='. $th_id . ' AND t.account_id ='. $this->ar);
    //     $this->db->join('transactions dt', 'th.id = dt.trans_id AND dt.trans_id ='. $th_id . ' AND dt.account_id !='. $this->ar);
    //     //$this->db->join('transactions dp', 't.deposit_id = dp.trans_id', 'right');
    //     //$this->db->join('transaction_header dph', 'dp.trans_id = dph.id');
    //     $this->db->join('customer_payments cp', 'cp.trans_id = t.id','left');
    //     $this->db->join('users u', 'th.last_mod_by = u.id','left'); 
    //     $this->db->join('profiles p', 'u.profile_id = p.id','left'); 
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         $test = $q->row();
    //         return $q->row();
    //     }
    //     return false;

    // }

    public function getHeaderEdit($th_id)
    {
        $this->db->select('th.id, t.id AS transaction_id_a, th.memo, th.transaction_date AS date, th.transaction_ref
        , CONCAT(DATE_FORMAT(th.last_mod_date, "%m/%d/%Y")," ",TIME_FORMAT(th.last_mod_date, "%r")) AS modified
        , th.last_mod_by, t.profile_id, CONCAT_WS(" ",p.first_name,p.last_name) AS user, t.credit, cp.trans_id AS cp_tid
        , cp.payment_method, cp.deposit_on, cp.nsf, dt.account_id AS deposit_to, dt.id AS transaction_id_b, a.name AS deposit_to_name
        , t.deposit_id AS tdeposit_id, dt.deposit_id AS deposit_id, dt.rec_id, dt.clr, dudf.trans_id AS deposit_id2, IFNULL(ddt.account_id, dt.account_id) AS deposit_bank_id,  a.name AS deposit_bank_name, a.id AS deposit_bank_id
        , IFNULL(dth.transaction_date, th.transaction_date) AS deposit_date, dt.rec_id as rec_id, t.profile_id as profile_id, t.lease_id as lease_id, t.property_id as property_id ');
        $this->db->from('transaction_header th'); 
        $this->db->join('transactions t', 'th.id = t.trans_id AND t.trans_id ='. $th_id . ' AND t.account_id ='. $this->ar);
        $this->db->join('transactions dt', 'th.id = dt.trans_id AND dt.trans_id ='. $th_id . ' AND dt.account_id !='. $this->ar);
        //$this->db->join('accounts a', 'dt.account_id = a.id');
        $this->db->join('customer_payments cp', 'cp.trans_id = t.id','left');
        $this->db->join('users u', 'th.last_mod_by = u.id','left'); 
        $this->db->join('profiles p', 'u.profile_id = p.id','left'); 
        $this->db->join('transactions dudf', 'dudf.id = dt.deposit_id', 'left');
        $this->db->join('transactions ddt', 'dudf.trans_id = ddt.trans_id AND ddt.account_id !=' . $this->uf, 'left');
        $this->db->join('accounts a', 'ddt.account_id = a.id', 'left');
        $this->db->join('transaction_header dth', 'ddt.trans_id = dth.id', 'left'); 
        $this->db->order_by('ddt.id ASC'); 
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $test = $q->row();
            return $q->row();
        }
        return false;

    }

    //
    // $this->db->select('rpu.profile_id, rpu.property_id, rpu.unit_id, rpu.lease_id, rpu.debit, ddt.account_id');
    //     $this->db->from('transactions rpu');
    //     $this->db->join('transactions dudf', 'dudf.id = rpu.deposit_id AND rpu.trans_id = ' . $post['trans_id'] . ' AND rpu.account_id != ' . $this->ar);
    //     $this->db->join('transactions ddt', 'dudf.trans_id = ddt.trans_id AND ddt.account_id !=' . $this->uf);
    //     //$this->db->where('rpu.trans_id = ' . $trans_id . ' AND rpu.account_id != ' . $this->ar);
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //          $profile_id = $q->row()->profile_id;
    //          $property_id = $q->row()->property_id;
    //          $unit_id = $q->row()->unit_id;
    //          $lease_id = $q->row()->lease_id;
    //          $amount = $q->row()->debit;
    //          $account_id = $q->row()->account_id;//deposit to in deposits
    //     }


    public function getTransactionsEditNew($th_id, $lease_id, $pid)
    {   
        $this->db->select('id');
        $this->db->from('transactions'); 
        $this->db->where(array('trans_id'=>$th_id, 'account_id' => $this->ar));
        $transactionInfo = $this->db->get();
        //if ($q->num_rows() > 0) {
            $tid = $transactionInfo->row()->id;
            $this->db->reset_query();
        //}
            //return null;
         // get all transactions that don't have this payment in applied payments table (a or b)
        $this->db->select('t.id, t.profile_id, t.lease_id, t.property_id, t.description, th.id as th_id, th.transaction_type, th.transaction_date AS date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, IFNULL(ap.amount,0) AS received_payment');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar .  ' AND t.lease_id ='. $lease_id. ' AND (t.profile_id ='. $pid . ' OR t.profile_id IS NULL OR t.profile_id = 0)'
        //' AND t.profile_id ='. $pid 
        
        ); 
        $this->db->join('(select sum(amount) as amount, transaction_id_a, transaction_id_b from (select transaction_id_a, transaction_id_b, amount from `applied_payments` 
        union select transaction_id_b ,transaction_id_a, amount  from `applied_payments`) ap1  group by transaction_id_a, transaction_id_b) ap','t.id = ap.transaction_id_b AND transaction_id_a =' .  $tid,  ' left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments WHERE transaction_id_b !=' . $this->db->escape($tid) .
        ' UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments WHERE transaction_id_a !=' . $this->db->escape($tid) . ') trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('t.id !=',$tid);
        $this->db->where('((t.debit - t.credit) - IFNULL(transum.amounts,0))!= 0');
        $this->db->where('th.transaction_type !=',  5);
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

    public function getTransactionsEdit($th_id, $lease_id, $pid)
    {   
        $this->db->select('id');
        $this->db->from('transactions'); 
        $this->db->where(array('trans_id'=>$th_id, 'account_id' => $this->ar));
        $transactionInfo = $this->db->get();
        //if ($q->num_rows() > 0) {
            $tid = $transactionInfo->row()->id;
            $this->db->reset_query();
        //}
            //return null;
         // get all transactions that don't have this payment in applied payments table (a or b)
        $this->db->select('t.id, t.profile_id, t.lease_id, t.property_id, t.description, th.id as th_id, th.transaction_type, th.transaction_date AS date, b.due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, 0 AS received_payment');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar .  ' AND t.lease_id ='. $lease_id . ' AND (t.profile_id ='. $pid . ' OR t.profile_id IS NULL OR t.profile_id = 0)'
        //' AND t.profile_id ='. $p_id 
        ); 
        $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments WHERE transaction_id_a !=' . $this->db->escape($tid) .
        ' UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments WHERE transaction_id_a !=' . $this->db->escape($tid) . ') trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('t.id !=',$tid);
        $this->db->where('((t.debit - t.credit) - IFNULL(transum.amounts,0))!= 0 AND t.id NOT IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $this->db->escape($tid) . ')');
        
        
         $other_payments = $this->db->get_compiled_select();
         $this->db->reset_query();

        // //get all transactions that have this payment in applied payments table (a) open balance not including this payment
        $this->db->select('t.id, t.profile_id, t.lease_id, t.property_id, t.description, th.id as th_id, th.transaction_type, th.transaction_date AS date, b.due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, IFNULL(ap.amount,0) AS received_payment');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar) ; // AND (t.profile_id ='. $pid . ' OR t.profile_id IS NULL OR t.profile_id = 0)'); 
        $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
        $this->db->join('(SELECT transaction_id_b AS trans_id, amount
        FROM applied_payments
        WHERE transaction_id_a =' .  $this->db->escape($tid) . ') ap','t.id = ap.trans_id','left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments
        WHERE transaction_id_a !='. $this->db->escape($tid) .
        ' UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments WHERE transaction_id_a !='. $this->db->escape($tid) .') trans GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('t.id IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $this->db->escape($tid) . ')');         
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

    public function applyPayments1($profile_id, $amount, $header, $customerPayments, $applied_payments)
    {   
        $this->db->trans_start();

        $trans_id = $this->addHeader($header, 5);
        foreach($applied_payments as &$applied_payment){
            $total = array_sum(array_column($applied_payment, 'amount'));
            $accounts_receivable = ['account_id' => $this->ar,  'profile_id' => $profile_id, 'trans_id' => $trans_id, 'credit' => $total];
            
            $this->db->insert('transactions',$accounts_receivable);
            $transaction_id_a = $this->db->insert_id();
            $this->db->insert('transactions',$deposit_to);

            $customerPayments['trans_id'] = $transaction_id_a;
            $this->db->insert('customer_payments', $customerPayments);
            
        // code for ones that are checked go into applied_payments array to be inserted
            $applied_payments = array_filter($applied_payments, function($v) {
                return  $v['applied'] == 1;
            });
            
            $this->applyPaymentsAdd($applied_payments, $transaction_id_a);
        }
        $deposit_to =  ['account_id' => $customerPayments['deposit_to'],'profile_id' => $profile_id, 'trans_id' => $trans_id, 'debit' => $amount];

        $this->db->trans_complete();

        return true;
        
    }
    
  
    public function applyPayments($header, $customerPayments, $applied_payments, $accounts_receivable, $deposit_to)
    {   
        $this->db->trans_start();

        $trans_id = $this->addHeader($header, 5);
        $accounts_receivable['trans_id'] =  $trans_id;
        $deposit_to['trans_id'] =  $trans_id;
        $this->db->insert('transactions',$accounts_receivable);
        $transaction_id_a = $this->db->insert_id();
        $this->db->insert('transactions',$deposit_to);

        $customerPayments['trans_id'] = $transaction_id_a;
        $this->db->insert('customer_payments', $customerPayments);

        $this->applyPaymentsAdd($applied_payments, $transaction_id_a);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
             return false;
        }

        return true;
        
    }

    public function editAppliedPayments($header, $customerPayments, $applied_payments, $accounts_receivable, $deposit_to, $id)
    {   
        $this->db->trans_start();

        // $this->db->select('id');
        // $this->db->from('transactions'); 
        // $this->db->where(array('trans_id'=> $header['id'], 'account_id !=' => $this->ar));
        // $q = $this->db->get();
        // //if ($q->num_rows() > 0) {
        // $dt_id = $q->row()->id;
        // $this->db->reset_query();

        $this->load->model('transaction_snapshot_model');
        $this->transaction_snapshot_model->transaction_snapshot($header['id']);

        $this->updateHeader($header, $header['id']);
        
        $this->db->update('transactions',$accounts_receivable, array('id' => $accounts_receivable['id']));
        $this->db->update('transactions',$deposit_to, array('id' => $id));
        $this->db->update('customer_payments', $customerPayments, array('trans_id' => $customerPayments['trans_id']));

        $this->db->where('transaction_id_a = '. $accounts_receivable['id'].' or transaction_id_b = '.$accounts_receivable['id']);
        $this->db->delete('applied_payments');

        $this->applyPaymentsAdd($applied_payments,  $accounts_receivable['id']);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
             return false;
        }

        return true;
        
        }

        public function getProfile($id)
        {
            $q = $this->db->select('id')->get_where('profiles', array('id' => $id), 1);
            if ($q->num_rows() > 0) {
                $data = $q->row();
                return $data;
            }
            return null;
        }
}
