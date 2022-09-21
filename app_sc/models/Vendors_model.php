<?php defined('BASEPATH') OR exit('No direct script access allowed');
//include_once('Profiles_model.php');

class Vendors_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('accounts_model');
    }

    public function getVendor($id)
    {
        $q = $this->db->get_where('profiles', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            $data = $q->row();
            $data = $this->encryption_model->decryptThis($data);
            return $data;
        }
        return false;
    }

    public function getVendorsList($addtype = false)
    {
        $ap = $this->site->settings->accounts_payable;
        $this->db->select('p.*, LTRIM(CONCAT_WS(" ",p.first_name,p.last_name)) as name, p.id, totalbalance');
        $this->db->from('profiles p');
        $this->db->join('(SELECT sum(credit - debit) as totalbalance, profile_id

        FROM transactions
        WHERE `account_id` = '. $ap .' 
        Group by profile_id) as b', 'p.id = b.profile_id','left');
        $this->db->where('profile_type_id', 1);
        $this->db->ORDER_BY('name');

       // $this->db->join('leases l', 'lp.lease_id = l.id');
        $q = $this->db->get();
        //echo $this->db->last_query();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                   //$row->totalBalance = $this->getOpenBalance($row->id);
                if ($addtype == true) {
                    $row->type = 'vendors';
                    $row->info = '$' . (int)$row->totalbalance;
                }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }


    

 
    public function getPortalLogin($id)
    {
        $q = $this->db->get_where('users', array('profile_id' => $id), 1);
        if ($q->num_rows() > 0) {
            $data = $q->row();
            $data = $this->encryption_model->decryptThis($data);
            return $data;
        }
        return false;
    }
    

    public function getContacts($id){

        $this->db->select('c.*');
        $this->db->from('contacts c');
        $this->db->join('profile_contact pc', 'pc.contact_id = c.id');
        $this->db->join('profiles p', 'p.id = pc.profile_id');
        $this->db->where('p.id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getAddresses($id){

        $this->db->select('a.*');
        $this->db->from('profile_address a');
        $this->db->where('a.profile_id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function buildTree($tenants, $leases)
    {
        $childs = array();

        foreach ($leases as &$item)
        if ($item->profile_id != null)
            $childs[$item->profile_id][] = &$item;
        unset($item);
        /*foreach ($tenants as &$item) if (isset($childs[$item->id]))
            $item->children = $childs[$item->id];
        unset($item);*/
        foreach ($tenants as &$item) if (isset($childs[$item->id])) {
            $item->children = $childs[$item->id];
            $item->tree = $this->site->renderTree($item);
        }
        return $tenants;
    }

    public function getOpenBalance($profile_id)
    {   
        $sum = '(SELECT trans_id, SUM(amount) AS amounts
         FROM(SELECT transaction_id_a AS trans_id, (0- amount) AS amount
         FROM applied_payments
         UNION ALL
         SELECT transaction_id_b AS trans_id, amount 
         FROM applied_payments) trans
         GROUP BY trans_id) transum';

         $this->db->select('t.profile_id, (t.credit - t.debit) AS amount, ((t.credit - t.debit) - IFNULL(transum.amounts,0)) AS open_balance');
         $this->db->from('transactions t');
         $this->db->join($sum, 't.id = transum.trans_id','left');
         //$this->db->where('t.lease_id', $leaseId);
         $this->db->where('t.profile_id', $profile_id);
         $this->db->where('t.account_id', $this->site->settings->accounts_payable);
         //$this->db->where_in('t.item_id', $rule->typesOfCharges);
         $balances = $this->db->get_compiled_select();
         $balances = '(' . $balances . ')balances';
         $this->db->reset_query();

         $this->db->select('balances.profile_id, SUM(balances.open_balance) AS open_balance');
         $this->db->from($balances);
         $this->db->group_by('balances.profile_id');
         //$this->db->having('open_balance > 0');

         $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                //$data[] = $row;
                $data = $row->open_balance;
            }
        }
        return $data;
    }

            public function getAllAccounts()
        {
            $this->db->select('id, name');
            $this->db->from('accounts');
            $this->db->order_by('name');
            $this->db->where('active', 1);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
                return $data;
            }
            return array();
        }
}
