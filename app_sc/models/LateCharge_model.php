<?php defined('BASEPATH') OR exit('No direct script access allowed');

class LateCharge_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function checkRules()//run everyday maybe crontab? set on linux server or task scheduler on windows if using windows server?
    {
        $dayOfMonth = date('d');//day of month with leading zero
        $this->db->select('amount, type, all_types, late_charge_setup_id');
        $this->db->from('late_charge_rules'); 
        $this->db->where('day', $dayOfMonth);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                    $row->typesOfCharges = $this->getTypes($row->all_types, $row->id);
                $this->getLeases($row);
            }
        }
        // if($rules){
        //     $this->getLeases($rules);
        // }

    }

    public function getTypes($allTypes, $rule_id)
    {   
        if($allTypes == 1){
        $this->db->select('id');
        $this->db->from('items'); 
        //$this->db->where('late_charge_rule_id', $rule_id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $types[] = $row;
            }
        } return $types;
        } else{
            $this->db->select('late_charge_type_id');
            $this->db->from('late_charge_types'); 
            $this->db->where('late_charge_rule_id', $rule_id);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $types[] = $row;
                }
            } return $types;
        }
        
        // if($rules){
        //     $this->getLeases($rules);
        // }

    }

    public function getLeases($rule)
    {
        $late_charge_setup_id = $rule->late_charge_setup_id;
        $this->db->select('id');
        $this->db->from('leases'); 
        $this->db->where('late_charge_setup_id', $late_charge_setup_id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $this->getOpenBalance($row->id, $rule);
            }
        }
    }

    

    public function getOpenBalance($leaseId, $rule)
    {   
        $sum = '(SELECT trans_id, SUM(amount) AS amounts
         FROM(SELECT transaction_id_a AS trans_id, (0- amount) AS amount
         FROM applied_payments
         UNION ALL
         SELECT transaction_id_b AS trans_id, amount 
         FROM applied_payments) trans
         GROUP BY trans_id) transum';

         $this->db->select('lease_id, t.profile_id, property_id, unit_id, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance');//account_id, property_id, unit_id
         $this->db->from('transactions t');
         $this->db->join($sum, 't.id = transum.trans_id','left');
         $this->db->where('t.lease_id', $leaseId);
         $this->db->where('t.account_id', $this->site->settings->accounts_receivable);
         $this->db->where_in('t.item_id', $rule->typesOfCharges);
         $balances = $this->db->get_compiled_select();
         $balances = '(' . $balances . ')balances';
         $this->db->reset_query();

         $this->db->select('balances.profile_id, balances.lease_id, balances.property_id, balances.unit_id, balances.default_RC_item, SUM(balances.open_balance) AS open_balance');//balances.account_id, balances.property_id, balances.unit_id
         $this->db->from($balances);
         $this->db->group_by('balances.profile_id');
         $this->db->having('open_balance > 0');

         $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
        }
        $this->addLateCharge($data, $rule);
    }
        
     
    public function addLateCharge($openBalances, $rule)
    {
       
        foreach($openBalances as $openBalance){
            $profile_id = $openBalance->profile_id;
            $lease_id = $openBalance->lease_id;
            $property_id = $openBalance->property_id; 
            $unit_id = $openBalance->unit_id;
            
            $this->db->select('p.default_RC_item, i.acct_income');
            $this->db->from('properties p');
            $this->db->join('items i', 'p.default_LC_item = i.id'); 
            $this->db->where('p.id', $property_id);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                $account_id = $q->row()->acct_income;
                $item_id = $q->row()->item_id;
            }
            $openBalance = $openBalance->open_balance;
            $ruleAmount = $rule->amount;
            $percentOrDollar = $rule->type;//0 is % 1 is $
            $ruleAmount = ($percentOrDollar == 1) ? $ruleAmount : (($ruleAmount/100) * $openBalance);
            // if($percentOrDollar == 0){
            //     $ruleAmount = ($ruleAmount/100) * $openBalance;
            // }
            $this->db->trans_start();

            $header = ['transaction_type' => 6, 'transaction_date' => date("Y-m-d"), 'last_mod_by' => $this->ion_auth->get_user_id(), 'last_mod_date' => date('Y-m-d H:i:s')];
            $this->db->insert('transaction_header', $header);
            $trans_id = $this->db->insert_id();

            $accounts_receivable = ['account_id' => $this->site->settings->accounts_receivable,'profile_id' => $profile_id, 'lease_id' => $lease_id, 'property_id' => $property_id,  'unit_id' => $unit_id, 'trans_id' => $trans_id, 'debit' => $ruleAmount];
            $this->db->insert('transactions', $accounts_receivable);
            
            $IncomeAccount = ['account_id' => $account_id, 'item_id' => $item_id,'profile_id' => $profile_id, 'lease_id' => $lease_id, 'property_id' => $property_id,  'unit_id' => $unit_id, 'trans_id' => $trans_id, 'credit' => $ruleAmount];
            $this->db->insert('transactions', $IncomeAccount);

            $this->db->trans_complete();
        }
    }


}
