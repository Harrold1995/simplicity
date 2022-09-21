<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Nsf_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ar = $this->site->settings->accounts_receivable;
        $this->uf = $this->site->settings->undeposited_funds;
        // $this->nsf = $this->site->settings->default_nsf;
        $this->nsfi = $this->site->settings->default_nsf_item;
    }

    //same function in charges
    public function getAccountId($id)
    {
        $this->db->select('i.acct_income');           
        $this->db->from('items i');
        $this->db->where('i.id', $id); 
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->acct_income;
        }
        return null;
    }

    

    public function balanceBounce($header, $accounts_receivable, $deposit_to, $info, $post)
    {   
        $this->db->trans_start();

        // $this->db->select('rpu.profile_id, rpu.property_id, rpu.unit_id, rpu.lease_id, rpu.debit, ddt.account_id');
        // $this->db->from('transactions rpu');
        // $this->db->join('transactions dudf', 'dudf.id = rpu.deposit_id AND rpu.trans_id = ' . $trans_id . ' AND rpu.account_id != ' . $this->ar);
        // $this->db->join('transactions ddt', 'dudf.trans_id = ddt.trans_id AND ddt.account_id !=' . $this->uf);
        // //$this->db->where('rpu.trans_id = ' . $trans_id . ' AND rpu.account_id != ' . $this->ar);
        // $q = $this->db->get();
        // if ($q->num_rows() > 0) {
        //      $profile_id = $q->row()->profile_id;
        //      $property_id = $q->row()->property_id;
        //      $unit_id = $q->row()->unit_id;
        //      $lease_id = $q->row()->lease_id;
        //      $amount = $q->row()->debit;
        //      $account_id = $q->row()->account_id;//deposit to in deposits
        // }


        $trans_id = $this->addHeader($header, 12);//need new transaction type for this?
        $transaction_id_a = $post['transaction_id_a'];
        
        $deposit_to['trans_id'] = $trans_id;
        $accounts_receivable['trans_id'] = $trans_id;

        
        //$transaction_id_a = $this->db->insert_id();
        
        $this->db->insert('transactions', $deposit_to);
        $this->db->insert('transactions', $accounts_receivable);
        $transaction_id_b = $this->db->insert_id();
        $this->db->delete('applied_payments', array('transaction_id_a' => $transaction_id_a));
        //erase applied payment make new payment transaction id a is payment this accounts receivable is b 
        $applied_payment = ['amount' => $accounts_receivable['debit'], 'transaction_id_a' => $transaction_id_a, 'transaction_id_b' => $transaction_id_b, 'created_by' => $this->ion_auth->get_user_id(), 'date_modified' => date("Y-m-d H:i:s")];  
        $this->db->insert('applied_payments', $applied_payment);

        $this->db->where('trans_id', $transaction_id_a);
        $this->db->update('customer_payments', array('nsf' => $post['transaction_date']));

        $this->addNsf($header, $info, $post);
      
    

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
             return false;
        }
         
        return true;

        
    }

    public function balanceBounceEdit($header, $description, $transaction_id_a, $lease_id, $profile, $prop_id , $unit, $bank)
    {   
        $this->db->trans_start();




        $this->load->model('transaction_snapshot_model');
        $this->transaction_snapshot_model->transaction_snapshot($header_id);

        $this->updateHeader($header, $header['id']);

        
        //$transaction_id_a = $this->db->insert_id();
        //$this->db->update('transactions', $transaction, array('trans_id' => $id, 'account_id !=' =>$this->ar));
        $this->db->where(array('trans_id' => $header['id'], 'account_id !=' =>$this->ar));
        $this->db->update('transactions', 
               array(
                     'description' => $description
                    , 'unit_id' => $unit
                    , 'lease_id'=> $lease_id
                    , 'profile_id'=> $profile
                    , 'property_id'=> $prop_id
                    , 'account_id'=> $bank 
                )
            );

        $this->db->where(array('trans_id' => $header['id'], 'account_id' =>$this->ar));
        $this->db->update('transactions', array('description' => $description, 'unit_id' => $unit, 'lease_id'=> $lease_id , 'profile_id'=> $profile, 'property_id'=> $prop_id));
        //$this->db->insert('transactions', $deposit_to);
        // $accounts_receivable = ['profile_id' => $transaction['profile_id'], 'lease_id' => $transaction['lease_id'], 'property_id' => $transaction['property_id'], 'unit_id' => $transaction['unit_id'], 'description' => $transaction['description'], 'debit' => $transaction['credit'], 'item_id' => $transaction['item_id']];
        // $this->db->update('transactions', $accounts_receivable, array('trans_id' => $id, 'account_id' =>$this->ar));
        // $this->db->insert('transactions', $accounts_receivable);
        // $transaction_id_b = $this->db->insert_id();
        //$this->db->delete('applied_payments', array('transaction_id_a' => $transaction_id_a));
        //erase applied payment make new payment transaction id a is payment this accounts receivable is b 
        //$applied_payment = ['amount' => $accounts_receivable['debit'], 'transaction_id_a' => $transaction_id_a, 'transaction_id_b' => $transaction_id_b, 'created_by' => $this->ion_auth->get_user_id(), 'date_modified' => date("Y-m-d H:i:s")];  
        //$this->db->insert('applied_payments', $applied_payment);

        $this->db->where('trans_id', $transaction_id_a);
        $this->db->update('customer_payments', array('nsf' => $header['transaction_date']));

        //$this->addNsf($header, $info, $post); //not on edit
      
    

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
             return false;
        }
         
        return true;

        
    }


    public function addNsf($header, $info, $post)
    {   
        $this->db->trans_start();
        $fee = str_replace(',', '', $post['fee']); 
        $trans_id = $this->addHeader($header, 13);//which account id
        $transaction = ['trans_id' => $trans_id, 'account_id'=> $this->getAccountId($this->nsfi), 'credit' => $fee, 'item_id' => $this->nsfi, 'description' => $post['description']] + $info;
        
        $this->db->insert('transactions', $transaction);

        $accounts_receivable = ['trans_id' => $trans_id, 'account_id' => $this->ar, 'debit' => $fee, 'item_id' => $this->nsfi,'description' => $post['description']] + $info;
        $this->db->insert('transactions', $accounts_receivable);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
             return false;
        }
         
        return true;

        
    }

    public function getHeader($id)
    {
        $this->db->select('th.id, th.transaction_type, CONCAT(DATE_FORMAT(th.last_mod_date, "%m/%d/%Y")," ",TIME_FORMAT(th.last_mod_date, "%r")) AS modified,
        th.last_mod_by, CONCAT_WS(" ",p.first_name,p.last_name) AS user,  th.transaction_ref, th.transaction_date AS date, (credit - debit) AS amount, t.description,
        t.type_item_id AS transaction_id_a, tp.id AS profile, t.lease_id AS lease, a.id AS deposit_bank_name');
        $this->db->from('transaction_header th'); 
        $this->db->join('transactions t', 'th.id = t.trans_id AND th.id =' . $id . ' AND t.account_id !=' . $this->ar); 
        $this->db->join('leases l', 't.lease_id = l.id'); 
        $this->db->join('units un', 'l.unit_id = un.id'); 
        $this->db->join('properties props', 'un.property_id = props.id'); 
        $this->db->join('accounts a', 't.account_id = a.id');
        $this->db->join('profiles tp', 't.profile_id = tp.id'); 
        $this->db->join('users u', 'th.last_mod_by = u.id','left'); 
        $this->db->join('profiles p', 'u.profile_id = p.id','left');
        //$this->db->where('th.id', $id);
        $this->db->limit(1); 
        
        $q = $this->db->get();  
        //$q = $this->db->get_where('transaction_header', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            $test =  $q->row();
            return $q->row();
        }
        return false;
    }


    public function getTransaction($id)
    {   
        $this->db->select('id, trans_id, property_id, account_id, lease_id, unit_id, profile_id, (credit - debit) AS credit, description');
        $this->db->from('transactions');
        $this->db->where(array('trans_id' => $id, 'account_id !=' => $this->ar));
        $this->db->order_by('id', 'asc');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    

    public function editCharge($header, $transaction, $id)
    {   
        $this->db->trans_start();

        $this->load->model('transaction_snapshot_model');
        $this->transaction_snapshot_model->transaction_snapshot($id);

        $this->updateHeader($header, $id);
       
        //$transactions["debit"] = preg_replace("/[^0-9\.]/", "",$transactions["debit"]);
        $transaction['account_id'] = $this->getAccountId($transaction['item_id']);
        $this->db->update('transactions', $transaction, array('trans_id' => $id, 'account_id !=' =>$this->ar));

        $accounts_receivable = ['profile_id' => $transaction['profile_id'], 'lease_id' => $transaction['lease_id'], 'property_id' => $transaction['property_id'], 'unit_id' => $transaction['unit_id'], 'description' => $transaction['description'], 'debit' => $transaction['credit'], 'item_id' => $transaction['item_id']];
      
        $this->db->update('transactions', $accounts_receivable, array('trans_id' => $id, 'account_id' =>$this->ar));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;// generate an error... or use the log_message() function to log your error
        }


        return true;
    }

    
}
