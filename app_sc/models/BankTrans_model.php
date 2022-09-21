<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BankTrans_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function addBankTrans($header,$headerTransaction, $transactions)
    {   
        $this->db->trans_start();
        
        $trans_id = $this->addHeader($header, 15);
        $special['trans_id'] = $trans_id;
        $account_id;
        foreach($headerTransaction as &$transaction){
                $special['paid_to'] = $transaction['profile_id'];
                $transaction['trans_id'] = $trans_id; 
                $account_id = $transaction['account_id'];
        }
        $this->db->insert_batch('transactions', $headerTransaction);
        $filled = $this->removeEmpty($transactions, $trans_id);
        
        $this->addDetails($filled);


                         
        $config['allowed_types'] = 'gif|jpg|jpeg|png|xls';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $this->upload->do_upload('attach_document');
        if($this->upload->data('file_name') != ""){
            $data['document'] = $this->upload->data('file_name');
            $this->db->insert('documents', Array("name" =>  $data['document'], "reference_id" => $trans_id, "type" => "21"));
        }
        
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
              return false;
        } else { return $trans_id; }
        
    }


    public function editBankTrans($header, $headerTransaction, $headerTransactionsAdd, $transactions, $id,  $deletes)
    {   
        //$this->speach = true;
        $this->db->trans_start();

        $this->load->model('transaction_snapshot_model');
        $this->transaction_snapshot_model->transaction_snapshot($id);
        $this->updateHeader($header, $id);

        $this->db->update_batch('transactions',$headerTransaction, 'id'); 
        //$this->db->update('transactions', $headerTransaction, array('id' => $headerTransaction['id']));
        // need to do insert for new header lines $this->db->insert('transactions', $headerTransaction, array('id' => $headerTransaction['id']));
        if($deletes){
            $this->deleteLines($deletes);
        }
        $filled = $this->removeEmptyEdit($transactions, $id);
        $this->editDetails($filled);
        if($headerTransactionsAdd){
            $this->db->insert_batch('transactions', $headerTransactionsAdd);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            //$this->$warning = new stdClass();
            // $this->speach = false;
            // $error = $this->db->error();
            // $a ='ERROR Transaction did not Update. Contact Mr. Craven';
            // $errNo   = $error['code'];
            // $errMess = $error['message'];
            //$this->msg = $a; 
            return false;
        }else{
            return true;
        }
         
    }

    
    




}
    
    

