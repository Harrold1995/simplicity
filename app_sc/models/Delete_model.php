<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Delete_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
   
    public function checkTransaction($th_id, $delete = NULL)
    {   
       $cleared = ''; 
       $beforeClosingDate = '';
       $applied = '';
       $deposited = '';
       $this->db->select('th.transaction_date, t.id AS t_id, r.closed, t.deposit_id, e.closing_date, apa.id AS apa_id, apb.id AS apb_id');
       $this->db->from('transaction_header th');
       $this->db->join('transactions t', 'th.id = t.trans_id');
       $this->db->join('properties p', 't.property_id = p.id');
       $this->db->join('applied_payments apa', 't.id = apa.transaction_id_a', 'left');
       $this->db->join('applied_payments apb', 't.id = apb.transaction_id_b', 'left');
       $this->db->join('entities e', 'p.entity_id = e.id', 'left');
       $this->db->join('reconciliations r', 't.rec_id = r.id', 'left');
       $this->db->where('th.id', $th_id);

       $q = $this->db->get();
        if($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if($row->closed == 1){
                    $cleared =  "OOPS transactions that were cleared can't be deleted";
                }

                if($row->transaction_date <  $row->closing_date){
                    $beforeClosingDate =  "OOPS transactions that are before closing date can't be deleted";
                }

                if(($row->deposit_id != null) && ($row->deposit_id != 0)){
                    $deposited =  "OOPS transactions that were deposited can't be deleted";
                }

                if(($row->apa_id != null) || ($row->apb_id != null)){
                    $applied = "Transaction/s  is/are applied to a payment";
                }
            }
        }
            if(!empty($cleared) || !empty($beforeClosingDate) || !empty($deposited)) 
            {
                $error = new stdClass();
                $error->message = $cleared .' '.  $beforeClosingDate . ' '. $deposited;
                $error->status = 1;  
                return $error;
            }
            
            if(empty($cleared) && empty($beforeClosingDate)){
               
                if(!empty($applied) && ($delete == NULL)){
                $warning = new stdClass();
                $warning->messages = 'A transaction/s has been applied to a payment are you sure you want to delete this transaction?';
                $warning->status = 0;  
                return $warning;
                    
                }
                if($delete == NULL){
                    $warning = new stdClass();
                    $warning->messages = 'Are you sure you want to delete this transaction?';
                    $warning->status = 0;  
                    return $warning;
                }
                $t_ids = array_map(function($t) { return $t->t_id; }, $q->result());
                $success = $this->deleteTransaction($th_id, $t_ids);
                return $success;  
            }
    }

    public function DeleteTransaction($th_id, $t_ids)// left join all special tables
    {   
        $this->db->trans_start();
        //transaction snapshot needed for special table? applied payments?
        $this->load->model('transaction_snapshot_model');
        $this->transaction_snapshot_model->transaction_snapshot($th_id);
        

    $header = $this->db->get_where('transaction_header', array('id' => $th_id));
    if ($header->num_rows() > 0) {
        $headerData = $header->row();
        $headerData->transaction_header_id = $th_id;
        $headerData->memo = 'DELETED';
        unset($headerData->id);
        $this->db->insert('transaction_header_snapshot', $headerData);
        $last_id = $this->db->insert_id();
        $this->db->reset_query();
}
        $this->db->select('*');
        $this->db->from('transactions');
        $this->db->where('trans_id', $th_id);
        $this->db->limit(1);
        $transaction = $this->db->get();
    

    // inserting into transactions_snapshot
    if ($transaction->num_rows() > 0) {
            $transaction = $transaction->row();
            $transaction->transaction_id = $transaction->id;
            $transaction->trans_snapshot_id = $last_id;
            $transaction->description = 'DELETED';
            unset($transaction->id);
            
            $this->db->insert('transactions_snapshot', $transaction);
            
    }

        //$this->db->delete('th, t, b, c, ap
        $sql ='DELETE th, t, b, c, ap
        FROM transaction_header th
        INNER JOIN transactions t ON th.id = t.trans_id
        LEFT JOIN bills b ON th.id = b.trans_id
        LEFT JOIN checks c ON th.id = c.trans_id
        LEFT JOIN applied_payments ap ON t.id = ap.transaction_id_a OR t.id = ap.transaction_id_b
        WHERE th.id = ?'; //for sql injection
        $this->db->query($sql, array($th_id)); //for sql injection
        // $this->db->from('transaction_header th');
        // $this->db->join('transactions t', 'th.id = t.trans_id');
        // $this->db->join('bills b', 'th.id = b.trans_id', 'left');
        // $this->db->join('checks c', 'th.id = c.trans_id', 'left');
        //$this->db->join('applied_payments ap', 't.id = ap.transaction_id_a OR t.id = ap.transaction_id_b', 'left');
        
        //$sql = $this->db->get_compiled_delete();
        //echo $sql;


        //erase deposit ids from transactions
        if($t_ids){
            $this->db->where_in('deposit_id', $t_ids);
            $this->db->update('transactions', array('deposit_id' => NULL));
        }

        $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE){
                $fail = new stdClass();
                    $fail->message = 'Transaction not deleted something went wrong';
                    $fail->status = 1;  
                    return $fail;
            }

        $success = new stdClass();
                    $success->message = 'Transaction deleted successfully';
                    $success->status = 0;  
                    return $success;

        //do we need to erase row in reconc table ---OF COURSE NOT!!! because can have other transactions in it

        

        //is paid field in transactions table being used

    }

    public function checkAccount($acc_id, $delete)
    {   
       $transaction = ''; 
       $parent = '';
       $default = '';
       $this->db->select('t.id AS t_id, a.parent_id, p.default_bank, i.acct_income');
       $this->db->from('accounts a');
       $this->db->join('transactions t', 'a.id = t.account_id', 'left');
       $this->db->join('properties p', 'a.id = p.default_bank', 'left');
       $this->db->join('items i', 'a.id = i.acct_income', 'left');
       $this->db->where('a.id', $acc_id);
       $this->db->or_where('a.parent_id', $acc_id);
       //$this->db->or_where('p.default_bank', $acc_id);
       $q = $this->db->get();

       if ($q->num_rows() > 0) {

            foreach (($q->result()) as $row) {
                if($row->t_id != NULL){
                    $transaction =  "this account is in a transaction";
                }

                if($row->parent_id == $acc_id){
                    $parent =  "this account is a parent of another account";
                }

                if(($row->default_bank != null) || ($row->acct_income != null)){
                    $default = "this account is a default account";
                }
            }
        }
            if(!empty($transaction) || !empty($parent) || !empty($default)) 
            {
                $error = new stdClass();
                $error->message = 'Can not delete ' . $transaction .' '.  $parent .' '. $default;
                $error->status = 1;  
                return $error;
            }
            
            if(empty($transaction) && empty($parent) && empty($default) && ($delete == NULL)) 
            {
                    $warning = new stdClass();
                    $warning->messages = 'Are you sure you want to delete account?';
                    $warning->status = 0;  
                    return $warning;
            }
            
               //return; 
                //$t_ids = array_map(function($t) { return $t->t_id; }, $q->result());
                $success = $this->deleteAccount($acc_id);  
                return $success;
                  
       
            
        }

    public function deleteAccount($acc_id)// left join all special tables
    {   
        $this->db->trans_start();
        $this->db->where('id', $acc_id);
        $this->db->delete('accounts');
        // $this->db->delete();
        // $this->db->from('accounts');
        // $this->db->where('id', $acc_id);
        // $sql = $this->db->get_compiled_delete();
        // echo $sql;
        //return;
        $this->db->trans_complete();
            
        if ($this->db->trans_status() === FALSE)
        {
            $fail = new stdClass();
                $fail->message = 'Account not deleted something went wrong';
                $fail->status = 1;  
                return $fail;
        }

        $success = new stdClass();
        $success->message = 'Account deleted successfully';
        $success->status = 0;  
        return $success;

    }

    public function checkName($profile_id, $delete = NULL)
    {   
       $transaction = ''; 
       $lease = '';
      
       $this->db->select('t.id AS t_id, lp.profile_id');
       $this->db->from('profiles p');
       $this->db->join('transactions t', 'p.id = t.profile_id', 'left');
       $this->db->join('leases_profiles lp', 'p.id = lp.profile_id', 'left');
       $this->db->where('p.id', $profile_id);
       $q = $this->db->get();

       if ($q->num_rows() > 0) {

            foreach (($q->result()) as $row) {
                if($row->t_id != NULL){
                    $transaction =  "this name is in a transaction";
                }

                if($row->profile_id != NULL){
                    $lease =  "this name is in a lease";
                }
            }
        }
            if(!empty($transaction) || !empty($lease)) 
            {
                $error = new stdClass();
                $error->message = 'Can not delete ' . $transaction .' '.  $lease;
                $error->status = 1;  
                return $error;
            }
            
            if(empty($transaction) && empty($lease) && ($delete == NULL)) 
            {
                    $warning = new stdClass();
                    $warning->messages = 'Are you sure you want to delete name?';
                    $warning->status = 0;  
                    return $warning;
            }
            
            
            $success = $this->deleteName($profile_id);  
            return $success;
        }

    public function DeleteName($profile_id)// left join all special tables
    {   
        $this->db->trans_start();
        
        $this->db->where('id', $profile_id);
        $this->db->delete('profiles');
        
        $this->db->trans_complete();
            
        if ($this->db->trans_status() === FALSE)
        {
            $fail = new stdClass();
                $fail->message = 'Name not deleted something went wrong';
                $fail->status = 1;  
                return $fail;
        }
        $success = new stdClass();
        $success->message = 'Name deleted successfully';
        $success->status = 0;  
        return $success;

    }

    // public function checkItems($type_ids, $type_item_ids, $delete = NULL)//array of arrays or objects with $type_ids and $type_item_ids
    // {   
    //    $inTransaction = []; 
    //    $memorizedTransactions = [];
     
    //    foreach($arrays as $array){
    //         $this->db->select('t.type_item_id, t.type_id');
    //         $this->db->from('transactions t');
    //         $this->db->where('t.type_id', $array['type_id']);//$object->type_id
    //         $this->db->where('t.type_item_id', $array['type_item_id']);
    //         $q = $this->db->get();
    
    //         if ($q->num_rows() > 0) {
    //             $inTransaction[] =  $q->row();
    //         }
    //         else{
    //             $memorizedTransactions[] = $this->checkMemorizedTransactions($array['type_id'], $array['type_item_id'], $delete);
    //             if($delete)$this->deleteItems($type_id, $type_item_id);
    //         }
    //    }

    //     $response = new stdClass();
        
    //    if($delete = null && empty($inTransaction) && empty($memorizedTransactions) ){
    //     $response->message = 'Are you sure you want to delete';
    //     $response->status = 0;
    //     return $response;  
    //    }

    //    if($delete = null && !empty($inTransaction) && !empty($memorizedTransactions) ){
    //     $response->message = 'Some item(s) that you are trying to delete are linked to transactions and can\'t be deleted, other(s) are linked to memorized transactions. Would you like to delete those?';
    //     $response->status = 0;
    //     return $response;  
    //    }

    //    if($delete = null && empty($inTransaction) && !empty($memorizedTransactions) ){
    //     $response->message = 'Items are linked to memorized transactions. Would you like to delete them?';
    //     $response->status = 0;
    //     return $response;  
    //    }

    //    if($delete = null && !empty($inTransaction) && empty($memorizedTransactions) ){
    //     $response->message = 'Items you are trying to delete are linked to transactions and can\'t be deleted';
    //     $response->status = 1;
    //     return $response;  
    //    }
      
           
    //     }  

    // public function checkMemorizedTransactions($type_id, $type_item_id, $delete)
    // {
    //     //$memorizedTransaction; 
        
    //     $this->db->select('mt.id AS mt_id');
    //     $this->db->from('memorized_transactions mt');
    //     //$this->db->join('memorized_transactions mt', 'p.id = t.profile_id', 'left');
    //     //$this->db->join('leases_profiles lp', 'p.id = lp.profile_id', 'left');
    //     $this->db->where('mt.type_id', $type_id);
    //     $this->db->where('mt.type_item_id', $type_item_id);
    //     $q = $this->db->get();

    //     if ($q->num_rows() > 0) {
    //         if($delete == NULL) {return  $q->row();}
    //         //else{$memorizedTransactions[] = [];}
       

    //         if($delete){
    //             //$this->deletePropertyDetails($type_id, $type_item_id); 
    //             $this->deleteMemorized($q->row()->mt_id);
    //         } 
    //     }
    // }

    // public function deleteItems($type_id, $type_item_Id )
    // {
    //     $this->db->select('table');
    //     $this->db->from('document_types');
    //     $this->db->where('type_id', $type_id);
    //     $q = $this->db->get();

    //     if ($q->num_rows() > 0) {
    //             $table = $q->row()->table;
    //     }
    //     $this->db->where('id', $type_item_Id);
    //     $this->db->delete($table);
    // }

    // public function deleteMemorized($mt_id)
    // {
    //     $this->db->where('id', $mt_id);
    //     $this->db->delete('memorized_transactions');
    // }
}






