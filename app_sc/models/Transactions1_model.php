<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions1_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addDetails($transactions, $trans_id)
    {
        foreach ($transactions as &$transaction)
        {
            foreach($transaction as $value)
            {
                if($value != "-1" AND $value !=""){
                    $myArray[] = $transaction;
                    continue 2;
            }
                
            }
        }
    
        foreach ( $myArray as &$transaction ) {//in bills didn't insert batch check why
            $transaction['trans_id'] = $trans_id;
            if($transaction['debit'] > 0) {
                $transaction['debit'] = str_replace(',', '' , $transaction['debit']); 
                $transaction['credit'] = 0;
             }
             elseif($transaction['debit'] < 0) {
                $transaction['credit'] = $transaction['debit'] * -1;
                $transaction['credit'] = str_replace(',', '' , $transaction['credit']); 
                $transaction['debit'] = 0;
             }
             else{
                $transaction['debit'] = 0;
                $transaction['credit'] = 0;
             }
        }
        if(!empty($myArray))
        {
            $this->db->insert_batch('transactions', $myArray);
        }
    }

    public function editDetails($transactions, $id)
    {

    $filled = [];
        $i = 0;
        foreach ($transactions as &$transaction)
        {   $i++;
            foreach($transaction as $value)
            {
                if($value != "-1" AND $value !=""){
                    $transaction['line_number'] = $i;
                    $transaction['trans_id'] = $id;
                    $filled[] = $transaction;
                    continue 2;
            }
                
            }
        }
        
        $updateArray = [];
        $insertrArray = [];

        foreach ( $filled as &$transaction ) {
            if(array_key_exists('id', $transaction)){
                $updateArray[] = $transaction;
            }else{
                $insertArray[] = $transaction;
            }
         }

         

         if(!empty($updateArray))
         {
            foreach ($updateArray as &$transaction ) {
                //$transaction['trans_id'] = $trans_id;
                
                    if($transaction['debit'] > 0) {
                        $transaction['debit'] = str_replace(',', '' , $transaction['debit']); 
                        $transaction['credit'] = 0;
                     }
                     elseif($transaction['debit'] < 0) {
                        $transaction['credit'] = $transaction['debit'] * -1;
                        $transaction['credit'] = str_replace(',', '' , $transaction['credit']); 
                        $transaction['debit'] = 0;
                     }
                     else{
                        $transaction['debit'] = 0;
                        $transaction['credit'] = 0;
                     }
                        
                     } 
            $this->db->update_batch('transactions', $updateArray, 'id');//in bills didn't insert batch check why
         }
         if(!empty($insertArray))
         {
            foreach ( $insertArray as &$transaction ) {
                //$transaction['trans_id'] = $trans_id;
                
                    if($transaction['debit'] > 0) {
                        $transaction['debit'] = str_replace(',', '' , $transaction['debit']); 
                        $transaction['credit'] = 0;
                     }
                     elseif($transaction['debit'] < 0) {
                        $transaction['credit'] = $transaction['debit'] * -1;
                        $transaction['credit'] = str_replace(',', '' , $transaction['credit']); 
                        $transaction['debit'] = 0;
                     }
                     else{
                        $transaction['debit'] = 0;
                        $transaction['credit'] = 0;
                     }
            } 
            $this->db->insert_batch('transactions', $insertArray);  //in bills didn't insert batch check why
         }
        }

    public function addTransCont(){
        $this->load->model('transactions_model');
        $errors = "";
        $header = $this->input->post('header');
        $transactions = $this->input->post('transactions');
        $checks = $this->input->post('checks');

        $data = array('header' => $header, 'transactions' => $transactions, 'checks' => $special);
        $validate = $this->validate_model->validate("checks", $data);

        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/$validate['bool'] &&  $this->checks_model->addCheck($header, $headerTransaction, $transactions, $special))
            echo json_encode(array('type' => 'success', 'message' => 'Check successfully added.'));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    public function addTrans($data){
        foreach($data AS $key =>$value){
            $this->db->insert($key, $value);
        }
    }

    public function addTransaction($header, $headerTransaction, $transactions, $special)///make sure arguments are in proper places
    {   
        $myArray = [];
        $header['last_mod_by'] = $this->ion_auth->get_user_id();
        $header['last_mod_date'] = date('Y-m-d H:i:s');
        $header['transaction_type'] = 4;//make dynamic
        $this->db->insert('transaction_header', $header);
        $trans_id = $this->db->insert_id();
        if(!empty($headerTransaction))
        {
        $headerTransaction['trans_id'] = $last_id;
        $this->db->insert('transactions', $headerTransaction);
        }
        foreach ($transactions as &$transaction)
        {
            foreach($transaction as $value)
            {
                if($value != "-1" AND $value !=""){
                    $myArray[] = $transaction;
                    continue 2;
            }
                
            }
        }
    
        foreach ( $myArray as &$transaction ) {//in bills didn't insert batch check why
            $transaction['trans_id'] = $trans_id;
            if($transaction['debit'] > 0) {
                $transaction['debit'] = str_replace(',', '' , $transaction['debit']); 
                $transaction['credit'] = 0;
             }
             elseif($transaction['debit'] < 0) {
                $transaction['credit'] = $transaction['debit'] * -1;
                $transaction['credit'] = str_replace(',', '' , $transaction['credit']); 
                $transaction['debit'] = 0;
             }
             else{
                $transaction['debit'] = 0;
                $transaction['credit'] = 0;
             }
        }
        if(!empty($myArray))
        {
            $this->db->insert_batch('transactions', $myArray);
        }
        if(!empty($special))
        {
         $special['paid_to'] = $headerTransaction['profile_id'];
         $special['trans_id'] = $trans_id;
         $this->db->insert($table, $special);//make dynamic
        }
         return true;
    }

    public function editTransaction($header, $headerTransaction, $transactions, $special, $id, $deletes)//make sure arguments are in proper places
    {   
        $this->load->model('transaction_snapshot_model');
        $header['last_mod_date'] = date('Y-m-d H:i:s');
        $this->transaction_snapshot_model->transaction_snapshot($id);
        $header['last_mod_by'] = $this->ion_auth->get_user_id();
        $header['transaction_type'] = 4;//make dynamic
        $this->db->update('transaction_header', $header, array('id' => $id));
        if(!empty($headerTransaction))
        {
        $this->db->update('transactions', $headerTransaction, array('id' => $headerTransaction['id']));
        }

        $filled = [];
        $i = 0;
        foreach ($transactions as &$transaction)
        {   $i++;
            foreach($transaction as $value)
            {
                if($value != "-1" AND $value !=""){
                    $transaction['line_number'] = $i;
                    $transaction['trans_id'] = $id;
                    $filled[] = $transaction;
                    continue 2;
            }
                
            }
        }
        
        $updateArray = [];
        $insertrArray = [];

        foreach ( $filled as &$transaction ) {
            if(array_key_exists('id', $transaction)){
                $updateArray[] = $transaction;
            }else{
                $insertArray[] = $transaction;
            }
         }

         

         if(!empty($updateArray))
         {
            foreach ($updateArray as &$transaction ) {
                //$transaction['trans_id'] = $trans_id;
                
                    if($transaction['debit'] > 0) {
                        $transaction['debit'] = str_replace(',', '' , $transaction['debit']); 
                        $transaction['credit'] = 0;
                     }
                     elseif($transaction['debit'] < 0) {
                        $transaction['credit'] = $transaction['debit'] * -1;
                        $transaction['credit'] = str_replace(',', '' , $transaction['credit']); 
                        $transaction['debit'] = 0;
                     }
                     else{
                        $transaction['debit'] = 0;
                        $transaction['credit'] = 0;
                     }
                        
                     } 
            $this->db->update_batch('transactions', $updateArray, 'id');//in bills didn't insert batch check why
         }
         if(!empty($insertArray))
         {
            foreach ( $insertArray as &$transaction ) {
                //$transaction['trans_id'] = $trans_id;
                
                    if($transaction['debit'] > 0) {
                        $transaction['debit'] = str_replace(',', '' , $transaction['debit']); 
                        $transaction['credit'] = 0;
                     }
                     elseif($transaction['debit'] < 0) {
                        $transaction['credit'] = $transaction['debit'] * -1;
                        $transaction['credit'] = str_replace(',', '' , $transaction['credit']); 
                        $transaction['debit'] = 0;
                     }
                     else{
                        $transaction['debit'] = 0;
                        $transaction['credit'] = 0;
                     }
            } 
            $this->db->insert_batch('transactions', $insertArray);  //in bills didn't insert batch check why
         }

         if(!empty($deletes)){
            $this->db->where_in('id', array_keys($deletes)); //array_values($deletes)probably don't need whole fancy function just pass array
            $this->db->delete('transactions'); 
         }

         if(!empty($special))
         {
         $special['paid_to'] = $headerTransaction['profile_id'];
         $this->db->update($table, $special, array('trans_id' => $id));///make dynamic
         }
         return true;
    }

    
   
}
