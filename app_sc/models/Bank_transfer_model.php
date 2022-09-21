<?php defined('BASEPATH') OR exit('No direct script access allowed');
include('Profiles_model.php');

class Bank_transfer_model extends MY_Model
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

    public function addBank_transfer($header, $from, $to){
        $this->db->trans_start();

        $trans_id = $this->addHeader($header, 11);//need new transaction type for this?
        $from['trans_id'] = $trans_id;
        $to['trans_id'] = $trans_id;

        $this->db->insert('transactions', $from);
        $this->db->insert('transactions', $to);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        return true;
    }

}
