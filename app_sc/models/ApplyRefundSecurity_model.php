<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ApplyRefundSecurity_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ar = $this->site->settings->accounts_receivable;
        $this->sd = $this->site->settings->security_deposits;
        $this->lmr = $this->site->settings->lmr;
    }

    // public function getBalance($lease_id, $profile_id = null)//$prop_id, $unit_id
    // {
    //     $this->db->select('SUM(credit - debit) AS balance');
    //     $this->db->from('transactions');
    //     $this->db->where('account_id', 244);//244 is security deposits
    //     //$this->db->where('profile_id', $prof_id);
    //     $this->db->where('lease_id', $lease_id);
    //     if($profile_id){
    //         $this->db->where('profile_id', $profile_id);
    //         $this->db->group_by('profile_id');
    //     }
    //     // $this->db->where('property_id', $prop_id);
    //     // $this->db->where('unit_id', $unit_id);
    //     //$this->db->group_by($prof_id, $prop_id, $unit_id);
    //     $this->db->group_by('lease_id');
        
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         return $q->row()->balance ? $q->row()->balance : 0;
    //     }
        

    // }

    public function getBalance($lease_id, $profile_id = null)//$prop_id, $unit_id
    {
        $this->db->select('SUM(credit - debit) AS balance, account_id');
        $this->db->from('transactions');
        $this->db->where('lease_id', $lease_id);
        $this->db->where('(account_id =' . $this->sd . ' OR account_id = ' . $this->lmr . ')');
        
       // if($profile_id){
            $this->db->where('profile_id', $profile_id);
            $this->db->group_by('profile_id');
      //  }
        
        $this->db->group_by('lease_id, account_id');
       
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            $data = $q->result_array();
            return $data;
        }
        return array();
    }

    public function getSdBalance($lease_id, $profile_id)//$prop_id, $unit_id
    {
        $this->db->select('SUM(credit - debit) AS sdBalance');
        $this->db->from('transactions');
        $this->db->where('lease_id', $lease_id);
        $this->db->where('account_id',$this->sd);
        $this->db->where('profile_id', $profile_id);
        
     
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            $data = $q->row()->sdBalance;
            return $data;
        }
        return array();
    }

    public function getLmrBalance($lease_id, $profile_id = null)
    {
        $this->db->select('SUM(credit - debit) AS lmrBalance');
        $this->db->from('transactions');
        $this->db->where('lease_id', $lease_id);
        $this->db->where('account_id',$this->lmr);
        $this->db->where('profile_id', $profile_id);

        
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            $data = $q->row()->lmrBalance;
            return $data;
        }
        return array();
    }

    public function getSdBalanceEdit($th_id, $lease_id, $profile_id)//$prop_id, $unit_id
    {
        $this->db->select('SUM(credit - debit) AS sdBalance');
        $this->db->from('transactions');
        $this->db->where('lease_id', $lease_id);
        $this->db->where('trans_id !=', $th_id);
        $this->db->where('account_id',$this->sd);
        $this->db->where('profile_id', $profile_id);
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            $data = $q->row()->sdBalance;
            return $data;
        }
        return array();
    }

    public function getLmrBalanceEdit($th_id, $lease_id, $profile_id)//$prop_id, $unit_id
    {
        $this->db->select('SUM(credit - debit) AS lmrBalance');
        $this->db->from('transactions');
        $this->db->where('lease_id', $lease_id);
        $this->db->where('trans_id !=', $th_id);
        $this->db->where('account_id',$this->lmr);
        $this->db->where('profile_id', $profile_id);
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            $data = $q->row()->lmrBalance;
            return $data;
        }
        return array();
    }

    public function getBalanceEdit($th_id, $lease_id, $profile_id = null)//$prop_id, $unit_id
    {
        $this->db->select('SUM(credit - debit) AS balance, account_id');
        $this->db->from('transactions');
        $this->db->where('lease_id', $lease_id);
        $this->db->where('trans_id !=', $th_id);
        $this->db->where('(account_id =' . $this->sd . ' OR account_id = ' . $this->lmr . ')');
        
        
        if($profile_id){
            $this->db->where('profile_id', $profile_id);
            $this->db->group_by('profile_id');
        }
        
        $this->db->group_by('lease_id, account_id');
       
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            $data = $q->result_array();
            return $data;
        }
        return array();
    }
    
    public function getArBalance($lease_id, $pofile_id)
    {
        $this->db->select('SUM(debit - credit) AS balance');
        $this->db->from('transactions');
        $this->db->where('account_id', $this->ar);
        $this->db->where('lease_id', $lease_id);
        $this->db->where('profile_id', $pofile_id);

        
        //$this->db->group_by('lease_id, account_id');
        
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            
            return $q->row()->balance;
        }
        return array();
    }

    public function getArBalanceEdit($th_id, $lease_id, $pofile_id)
    {
        $this->db->select('SUM(debit - credit) AS balance');
        $this->db->from('transactions');
        $this->db->where('account_id', $this->ar);
        $this->db->where('lease_id', $lease_id);
        $this->db->where('profile_id', $pofile_id);
        $this->db->where('trans_id !=', $th_id);

        
        //$this->db->group_by('lease_id, account_id');
        
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            
            return $q->row()->balance;
        }
        return array();
    }
    function checkApplyRefund($id, $type){
        $lmr = $this->getRefundBalance($id, $type, $this->lmr);
        $sd = $this->getRefundBalance($id, $type, $this->sd);
        if(($lmr <> 0) || ($sd <> 0)){
            return true;
        }else{
            return false;
        }
    }
    public function getRefundBalance($id, $type, $account)
    {
        $this->db->select('SUM(credit - debit) AS balance');
        $this->db->from('transactions');
        $this->db->where($type, $id);
        $this->db->where('account_id',$account);

        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            $data = $q->row()->balance;
            return $data;
        }
        return array();
    }

    function getTenants()
    {
        $this->db->select('id, LTRIM(CONCAT_WS(" ",first_name, last_name)) AS tenant');
        $this->db->from('profiles');
        $this->db->where('profile_type_id',3);
        $this->db->ORDER_BY('tenant');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    
    public function getBanks()
    {
        $this->db->select('id, name, accno');
        $this->db->from('accounts');
        $this->db->where('account_types_id', 1);
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

    public function getProperties($id=0)
    {
        $this->db->select('p.id, p.name, p.active');//, pa.property_id
        $this->db->from('properties p');
        //$this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id,'left');

        $this->db->where('p.active', 1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getUnits($addtype = false)
{   
    $this->db->select('id, name, parent_id');
    $this->db->from('units');
    $q = $this->db->get();
    if ($q->num_rows() > 0) {
        foreach (($q->result()) as &$row) {
            if ($addtype == true) $row->type = 'account_type';
            $data[] = $row;
        }
        return $data;
    }
    return null;
}

// public function getLease($id)
//     {
//         $this->db->select('l.id, l.start, l.end');//, u.property_id as property_id, p.address as address, u.name as unit
//         $this->db->from('leases l');
//         //$this->db->join('units u', 'l.unit_id = u.id');
//         //$this->db->join('properties p', 'u.property_id = p.id');
//         $this->db->where('l.id', $id);
//         $this->db->limit(1);
//         $q = $this->db->get();
//         if ($q->num_rows() > 0) {
//             return $q->row();
//         }
//         return null;
//     }

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
    
    public function getTransactions($lease_id, $profile_id = null)
    {   
        $profile_filter = $profile_id ? ' AND (t.profile_id =' . $profile_id . ' OR t.profile_id IS NULL OR t.profile_id = 0)' : "";
        //$profile_filter = $profile_id ? "( t.profile_id ='" . $profile_id . "' OR t.profile_id ='null')" : '';

        $this->db->select('t.id, t.description, th.transaction_date AS date, b.due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, 0 AS received_payment');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar .  ' AND t.lease_id ='. $lease_id . $profile_filter);
        //$this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id = 451' .  $profile_id ? 'AND t.property_id =' . $profile_id : "".  $property_id ? 'AND t.property_id =' . $property_id : "" .  $unit_id ? 'AND t.unit_id =' . $unit_id : "" .  $lease_id ? 'AND t.lease_id =' . $lease_id : "");
        $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments
        UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        //$this->db->where('((t.debit - t.credit) - transum.amounts)', '! = 0');
        $this->db->where('((t.debit - t.credit) - IFNULL(transum.amounts,0)) !=',  0);//check tomorrow
        //$this->db->where('t.profile_id',$profile_filter); //or_where ??
        //$this->db->or_where('t.profile_id',$pid);
        //(array('archived' => NULL));
        $this->db->ORDER_BY('th.transaction_date ASC');
        //$sql = $this->db->get_compiled_select();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
       
    }

    // public function getTransactions2($profile_id)
    // {   
    //     //$profile_filter = $profile_id ? ' AND t.profile_id =' . $profile_id : "";

    //     $this->db->select('t.id, t.description, th.transaction_date AS date, b.due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, 0 AS received_payment');
    //     $this->db->from('transactions t'); 
    //     $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar .  ' AND t.profile_id ='. $profile_id);
    //     //$this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id = 451' .  $profile_id ? 'AND t.property_id =' . $profile_id : "".  $property_id ? 'AND t.property_id =' . $property_id : "" .  $unit_id ? 'AND t.unit_id =' . $unit_id : "" .  $lease_id ? 'AND t.lease_id =' . $lease_id : "");
    //     $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
    //     $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
    //     FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
    //     FROM applied_payments
    //     UNION ALL
    //     SELECT transaction_id_b AS trans_id, amount 
    //     FROM applied_payments) trans
    //     GROUP BY trans_id) transum','t.id = transum.trans_id','left');
    //     //$this->db->where('((t.debit - t.credit) - transum.amounts)', '! = 0');
    //     $this->db->where('((t.debit - t.credit) - IFNULL(transum.amounts,0)) !=',  0);//check tomorrow
    //     //$this->db->or_where('t.profile_id',$pid);
    //     //(array('archived' => NULL));
    //     $this->db->ORDER_BY('th.transaction_date ASC');
    //     //$sql = $this->db->get_compiled_select();
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return null;
       
    // }
    //SELECT *, TIME_FORMAT(th.last_mod_date, "%r") FROM `transaction_header`
    //SELECT DATE_FORMAT(th.last_mod_date, '%d/m/Y') FROM `transaction_header` WHERE 1
    public function getHeaderEdit($th_id)
    {
        $this->db->select('th.id, th.memo, th.transaction_date AS date, CONCAT(DATE_FORMAT(th.last_mod_date, "%m/%d/%Y")," ",TIME_FORMAT(th.last_mod_date, "%r")) AS modified, th.last_mod_by, CONCAT_WS(" ",p.first_name,p.last_name) AS user, IFNULL(sd.profile_id,lmr.profile_id) AS profile_id, IFNULL(sd.property_id,lmr.property_id) AS property_id, IFNULL(sd.unit_id,lmr.unit_id) AS unit_id, IFNULL(sd.lease_id,lmr.lease_id) AS lease_id, sd.debit AS sdApplyAmount, lmr.debit AS lmrApplyAmount');
        $this->db->from('transaction_header th');
        $this->db->join('users u', 'th.last_mod_by = u.id', 'left'); 
        $this->db->join('profiles p', 'u.profile_id = p.id','left'); 
        $this->db->join('transactions sd', 'th.id = sd.trans_id AND sd.trans_id ='. $th_id . ' AND sd.account_id =' . $this->sd, 'left');
        $this->db->join('transactions lmr', 'th.id = lmr.trans_id AND lmr.trans_id ='. $th_id . ' AND lmr.account_id =' . $this->lmr, 'left');
        $this->db->where('th.id', $th_id);

        
        //$this->db->join('profiles p', 't.profile_id = p.id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;

    }

    public function getTransactionsEdit($th_id, $lease_id, $pid)
    {   
        $this->db->select('id');
        $this->db->from('transactions'); 
        $this->db->where(array('trans_id'=>$th_id, 'account_id' => $this->ar));
        //if ($q->num_rows() > 0) {
            $q = $this->db->get();
            $tid = $q->row()->id;
            $this->db->reset_query();
        //}
            //return null;

        $profile_filter = $pid ? ' AND (t.profile_id = ' . $pid . ' OR t.profile_id IS NULL OR t.profile_id = 0)'  : "";
        
         // get all transactions that don't have this payment in applied payments table (a or b)
        $this->db->select('t.id, t.description, th.transaction_date AS date, b.due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, 0 AS received_payment');
        $this->db->from('transactions t'); 
        //$this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id = 451  AND t.profile_id ='. $pid);
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar . ' AND t.lease_id ='. $lease_id . $profile_filter);
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
        $this->db->select('t.id, t.description, th.transaction_date AS date, b.due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, IFNULL(ap.amount,0) AS received_payment');
        $this->db->from('transactions t'); 
        //$this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id = 451  AND t.profile_id ='. $pid);
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar . ' AND t.lease_id ='. $lease_id . $profile_filter);
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

    // public function getTransactionsEdit2($th_id, $pid)
    // {   
    //     $this->db->select('id');
    //     $this->db->from('transactions'); 
    //     $this->db->where(array('trans_id'=>$th_id, 'account_id' => $this->ar));
    //     //if ($q->num_rows() > 0) {
    //         $q = $this->db->get();
    //         $tid = $q->row()->id;
    //         $this->db->reset_query();
    //     //}
    //         //return null;

    //     $profile_filter = $pid ? ' AND t.profile_id = ' . $pid : "";
        
    //      // get all transactions that don't have this payment in applied payments table (a or b)
    //     $this->db->select('t.id, t.description, th.transaction_date AS date, b.due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, 0 AS received_payment');
    //     $this->db->from('transactions t'); 
    //     //$this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id = 451  AND t.profile_id ='. $pid);
    //     $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar . ' AND t.profile_id ='. $pid);
    //     $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
    //     $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
    //     FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
    //     FROM applied_payments WHERE transaction_id_a !=' . $this->db->escape($tid) .
    //     ' UNION ALL
    //     SELECT transaction_id_b AS trans_id, amount 
    //     FROM applied_payments WHERE transaction_id_a !=' . $this->db->escape($tid) . ') trans
    //     GROUP BY trans_id) transum','t.id = transum.trans_id','left');
    //     $this->db->where('t.id !=',$tid);
    //     $this->db->where('((t.debit - t.credit) - IFNULL(transum.amounts,0))!= 0 AND t.id NOT IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $this->db->escape($tid) . ')');
        
        
    //      $other_payments = $this->db->get_compiled_select();
    //      $this->db->reset_query();

    //     // //get all transactions that have this payment in applied payments table (a) open balance not including this payment
    //     $this->db->select('t.id, t.description, th.transaction_date AS date, b.due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, IFNULL(ap.amount,0) AS received_payment');
    //     $this->db->from('transactions t'); 
    //     //$this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id = 451  AND t.profile_id ='. $pid);
    //     $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar . ' AND t.profile_id ='. $pid);
    //     $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
    //     $this->db->join('(SELECT transaction_id_b AS trans_id, amount
    //     FROM applied_payments
    //     WHERE transaction_id_a =' .  $this->db->escape($tid) . ') ap','t.id = ap.trans_id','left');
    //     $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
    //     FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
    //     FROM applied_payments
    //     WHERE transaction_id_a !='. $this->db->escape($tid) .
    //     ' UNION ALL
    //     SELECT transaction_id_b AS trans_id, amount 
    //     FROM applied_payments WHERE transaction_id_a !='. $this->db->escape($tid) .') trans GROUP BY trans_id) transum','t.id = transum.trans_id','left');
    //     $this->db->where('t.id IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $this->db->escape($tid) . ')');         
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
    
                                    
    public function applyPayments($header, $accounts_receivable, $applyTransactions,  $refundTransactions, $leaseInfo, $sdApplyAmount, $sdRefundAmount, $lmrApplyAmount, $lmrRefundAmount, $checkingAccount, $applied_payments, $check)
    {   
        $this->db->trans_start();
        
        if(($sdApplyAmount > 0) || ($lmrApplyAmount > 0)){
            $trans_id = $this->addHeader($header, 10);
            $accounts_receivable['trans_id'] = $trans_id;
            $this->db->insert('transactions',$accounts_receivable);
            $transaction_id_a = $this->db->insert_id();
            $transactions = $this->insertTransId($applyTransactions, $trans_id);
            $this->db->insert_batch('transactions', $transactions);

            $this->applyPaymentsAdd($applied_payments, $transaction_id_a);
        }   
        
        ////////////////refund  $header,$headerTransaction is $refund, $transactions is $security_deposits?, $special is check

        if(($sdRefundAmount > 0) || ($lmrRefundAmount > 0)){  
            $header['memo']="Security deposit refund";
            $trans_id = $this->addHeader($header, 4);
            $transactions = $this->insertTransId($refundTransactions, $trans_id);
            $this->db->insert_batch('transactions', $transactions);

            $check['trans_id'] =$trans_id;
            $this->db->insert('checks', $check);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        //really just need trans id of check for print or true if not printing
        return $trans_id;

    }

    public function editAppliedPayments($header, $accounts_receivable, $applyTransactions,  $refundTransactions, $leaseInfo, $sdApplyAmount, $sdRefundAmount, $lmrApplyAmount, $lmrRefundAmount, $checkingAccount, $applied_payments, $check)
    {   
        $this->db->trans_start();

        $this->db->select('id');
        $this->db->from('transactions'); 
        $this->db->where(array('trans_id'=> $header['id'], 'account_id' => $this->ar));
        $q = $this->db->get();
        if($q->num_rows() > 0) {
            $ar = $q->row()->id;
        }
            
            $this->updateHeader($header, $header['id']);
            $accounts_receivable = $leaseInfo +  ['credit' => $sdApplyAmount + $lmrApplyAmount];
            
            $this->db->update('transactions',$accounts_receivable, array('trans_id' => $header['id'],'account_id' => $this->ar));
            
           ////check if this works
            $this->db->where('trans_id', $header['id']);
            $this->db->update_batch('transactions', $applyTransactions, 'account_id');
            $this->db->delete('applied_payments', array('transaction_id_a' => $ar));
            $this->applyPaymentsAdd($applied_payments, $ar);
        

            if(($sdRefundAmount > 0) || ($lmrRefundAmount > 0)){  
                unset($header['id']);
                $trans_id = $this->addHeader($header, 4);
                $transactions = $this->insertTransId($refundTransactions, $trans_id);
                $this->db->insert_batch('transactions', $transactions);
    
                $check['trans_id'] =$trans_id;
                $this->db->insert('checks', $check);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {
                return false;
            }
            $trans_id = $trans_id ? $trans_id : $header['id'];//just to make like add really need trans id of check for print or true if not printing
            return $trans_id;
        }

        // function getDefaultAccount($pid){
        //     $this->db->select('IF(p.sd_refund_account !=0, p.sd_refund_account,p.default_bank) AS account_id');
            
        //     $this->db->from('properties p');
        //     $this->db->where('p.id', $pid);
    
        //     $q = $this->db->get();
        //     if ($q->num_rows() > 0) {
        //         $account_id = $q->row()->account_id;
        //         return $account_id;
        //    }
        //    return null;
        // }
        function getDefaultAccount($lid){

            $this->db->select('IF(p.sd_refund_account !=0, p.sd_refund_account,p.default_bank) AS account_id');     
            $this->db->from('properties p');
            $this->db->join('units u', 'p.id = u.property_id');
            $this->db->join('leases l', 'l.unit_id = u.id');
            $this->db->where('l.id', $lid);
    
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                $account_id = $q->row()->account_id;
                return $account_id;
           }
           return null;
        }
}
