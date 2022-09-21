<?php defined('BASEPATH') OR exit('No direct script access allowed');
include('Profiles_model.php');

class Transfer_bal_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
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

    public function getAllProperties($addtype = false)
    {
        $this->db->where('active', 1);
        $q = $this->db->get('properties');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'property';
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function addBal_transfer($header, $details){
        $this->db->trans_start();

        $trans_id = $this->addHeader($header, 14);

        foreach ($details as &$detail) {
            $detail['trans_id'] = $trans_id;
        }
        
        $this->addDetails($details);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        return true;
    }

    public function editBal_transfer($id, $header, $details, $deletes){

        $this->db->trans_start();

        $this->load->model('transaction_snapshot_model');
        $this->transaction_snapshot_model->transaction_snapshot($id);
        $this->updateHeader($header, $id);
        $this->editDetails($details);
        if($deletes){
            $this->deleteLines($deletes);
        } 
        $this->db->trans_complete();


        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        return true;
    }

    public function getHeaderEdit($id){

        $this->db->select('th.id, th.transaction_type, CONCAT(DATE_FORMAT(th.last_mod_date, "%m/%d/%Y")," ",TIME_FORMAT(th.last_mod_date, "%r")) AS modified, th.last_mod_by, CONCAT_WS(" ",p.first_name,p.last_name) AS user,  th.transaction_ref, th.transaction_date AS date, th.memo');
        $this->db->from('transaction_header th'); 
        $this->db->join('users u', 'th.last_mod_by = u.id','left'); 
        $this->db->join('profiles p', 'u.profile_id = p.id','left'); 
        $this->db->where('th.id', $id);
        $this->db->limit(1); 
        
        $q = $this->db->get();  
        //$q = $this->db->get_where('transaction_header', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;

    }

    public function getTransactionsEdit($id){

        $this->db->select('id, account_id, property_id, profile_id, lease_id, unit_id, description, class_id, debit, credit, rec_id, clr');
        $this->db->from('transactions');
        $this->db->where(array('trans_id' => $id));
        $this->db->order_by('line_number ASC', 'id ASC');
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getBalances($lease, $profile){

        $this->db->select('account_id, sum(debit), sum(credit)');
        $this->db->from('transactions');
        $this->db->where(array('profile_id' => $profile, 'lease_id' => $id));
        $this->db->where_in('account_id',array( $this->site->settings->accounts_receivable,$this->site->settings->security_deposits, $this->site->settings->lmr) );
        $this->db->group_by('account_id');
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($row->account_id = $this->site->settings->accounts_receivable ){
                $data['ar'] = $row->debit - $row->credit;
                } else if ($row->account_id = $this->site->settings->security_deposits ){
                    $data['sd'] =  $row->credit - $row->debit;
                } else {
                    $data['lmr'] = $row->credit - $row->debit;
                }
            }
            return $data;
        }
        return null;
    }


}
