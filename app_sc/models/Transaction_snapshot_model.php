<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_snapshot_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

function transaction_snapshot($headerId){

    $header = $this->db->get_where('transaction_header', array('id' => $headerId));
    $transactions = $this->db->get_where('transactions', array('trans_id' => $headerId));
    //inserting into transaction_header_snapshot
    if ($header->num_rows() > 0) {
             $transactionData = $header->row();
             $transactionData->transaction_header_id = $headerId;
             unset($transactionData->id);
             $this->db->insert('transaction_header_snapshot', $transactionData);
             $last_id = $this->db->insert_id();
    }
    

    //inserting into transactions_snapshot
    if ($transactions->num_rows() > 0) {
        foreach (($transactions->result()) as &$row) {
            $row->transaction_id = $row->id;
            $row->trans_snapshot_id = $last_id;
            unset($row->id);
            $headerData[] = $row; 
        }
        $this->db->insert_batch('transactions_snapshot', $headerData);
    }
    
     
     
}

}
