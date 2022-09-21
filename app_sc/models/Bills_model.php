<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bills_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addBill($type, $header, $headerTransactions, $transactions, $special)
    {   
        $this->db->trans_start();

        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $this->upload->do_upload('original');
        $special['original'] = $this->upload->data('file_name');


        $trans_id = $this->addHeader($header, 2);
        foreach ($headerTransactions as &$headerTransaction){
            $headerTransaction['trans_id'] = $trans_id;
            if($type === "credit"){ 
                $headerTransaction['debit'] = $headerTransaction['credit']; 
                $headerTransaction['credit'] = 0;
            }
        }

        $this->db->insert_batch('transactions', $headerTransactions);
        //$this->db->insert('transactions', $headerTransaction);
        $transaction_id_b = $this->db->insert_id();
        $filled = $this->removeEmpty($transactions, $trans_id);
        $this->addDetailsPerType($filled, $type);
        $special['trans_id'] = $trans_id;
        if($special)$this->db->insert('bills', $special);

        $config['allowed_types'] = 'gif|jpg|jpeg|png|xls';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $this->upload->do_upload('attach_document');
        if($this->upload->data('file_name') != ""){
            $data['document'] = $this->upload->data('file_name');
            $this->db->insert('documents', Array("name" =>  $data['document'], "reference_id" => $trans_id, "type" => "21"));
        }
        
        $this->db->trans_complete();
        $bid = $this->db->insert_id();
        //used for attachment
        // $this->load->model('documents_model');
        // $this->documents_model->uploadAttachment($bid, '2');
        if($special['original'] != ""){
            $this->db->insert('documents', Array("name" => $special['original'], "reference_id" => $bid, "type" => "17"));
        }
        if ($this->db->trans_status() === FALSE)
        {
         return false;
        }

         return $transaction_id_b;
    }

    public function getHeader($id)
    {   
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

    public function getAmountPaid($th_id)
    {
        $this->db->select('id');
        $this->db->from('transactions'); 
        $this->db->where(array('trans_id'=>$th_id, 'account_id' => $this->site->settings->accounts_payable));
        $q = $this->db->get();
        //if ($q->num_rows() > 0) {
        $tid = $q->row()->id;
        $this->db->reset_query();

        $this->db->select('IFNULL(SUM(amount),0) AS payed');
        $this->db->from('applied_payments');
        $this->db->where('transaction_id_b', $tid);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->payed;
        }
            return 0;
       
    }
    //// need to do
    public function getTransactions($id)
    {   
        $this->db->select('group_concat(id) as id, account_id as account_id, max(property_id) as property_id, max(profile_id) as profile_id, max(lease_id) as lease_id, max(unit_id) as unit_id, max(description) as description, max(class_id) as class_id, sum(debit) as debit, sum(credit) as credit, max(rec_id) as rec_id, max(clr) as clr');
        $this->db->from('transactions');
        $this->db->where(array('trans_id' => $id));
        $this->db->order_by('line_number ASC', 'id ASC');
        $this->db->group_by('line_number, account_id');
        $q = $this->db->get(); 

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'bill_type';
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getBill($id)
    {
        $q = $this->db->get_where('bills', array('trans_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
                return false;
    }
    public function editBill($type, $header, $headerTransactions, $transactions, $special, $id, $deletes, $applied, $origAmt, $headerTransactionsAdd)
    {   
        $this->db->trans_start();

        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $this->upload->do_upload('original');
        if($this->upload->data('file_name') != ""){
            $special['original'] = $this->upload->data('file_name');
            $this->db->insert('documents', Array("name" => $special['original'], "reference_id" => $id, "type" => "17"));
        }
        
        if (!empty($applied) && (( $type === "normal"  && $origAmt > $headerTransaction['credit']) or ($type === "credit"  && $origAmt != $headerTransaction['debit']) )) {
            $htid = $headerTransaction[id];
            $this->db->delete('applied_payments', array('transaction_id_b' => $htid));
          }

        $this->load->model('transaction_snapshot_model');
        $this->transaction_snapshot_model->transaction_snapshot($id);
        
        $this->updateHeader($header, $id);
        foreach ($headerTransactions as &$headerTransaction){
            if($type === "credit"){ 
                $headerTransaction['debit'] = $headerTransaction['credit']; 
                $headerTransaction['credit'] = 0;
            }
    
            if($type === "normal"){ 
                //$headerTransaction['debit'] = $headerTransaction['credit']; 
                $headerTransaction['debit'] = 0;
            }
        }

        $this->db->update_batch('transactions',$headerTransactions, 'id'); 
        //$this->db->update('transactions', $headerTransaction, array('id' => $headerTransaction['id']));
        $filled = $this->removeEmptyEdit($transactions, $id);
        $this->editDetailsPerType($filled, $type);
        if($headerTransactionsAdd){
            $this->db->insert_batch('transactions', $headerTransactionsAdd);
        }
        if($deletes){
            $this->deleteLines($deletes);
        }
         $this->db->update('bills', $special, array('trans_id' => $id));

         $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }

         return $headerTransaction['id']; //which is transaction_id_b in applied payment
    }

    

    public function getAccountTypes($addtype = false)
    {
        $q = $this->db->get('account_types');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'account_type';
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getAllAccounts()
    {
        $this->db->select('id, name, accno, all_props, parent_id');
        $this->db->from('accounts');
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


    function getAccounts($pid)

    {   // #1 SubQueries no.1 -------------------------------------------
        $this->db->select('id, name, accno, all_props');
        $this->db->from('accounts');
        $this->db->where(array('all_props'=> 1, 'active' => 1));
        //$query = $this->db->get();
        $all_props1 = $this->db->get_compiled_select();
        //echo $all_props1. 'UNION';
       
        $this->db->reset_query();
        
        // #2 SubQueries no.2 -------------------------------------------
        
        $this->db->select('a.id, a.name, a.accno, all_props');
        $this->db->from('accounts a');
        $this->db->join('property_accounts pa', 'a.id = pa.account_id AND pa.property_id ='. $pid);
        $this->db->where('a.active', 1);
        //$query = $this->db->get();
        $all_props0 = $this->db->get_compiled_select();
        //echo $all_props0;
        $this->db->reset_query();
        
        // #3 Union with Simple Manual Queries --------------------------
        
        $q = $this->db->query("$all_props1 UNION $all_props0");
        
        
        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            print_r($data);
        }
        return array();
    }

    public function getClasses($addtype = false)
    {
        $q = $this->db->get('classes');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'account_type';
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getProperties($id=0)
    {
        $this->db->select('p.id, p.name, p.active, pa.property_id');
        $this->db->from('properties p');
        $this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id,'left');

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

    public function getProperties2($id, $all_props)
    {   if($all_props==1)
        {
        $this->db->select('id, name, active');
        $this->db->from('properties');
        //$this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id,'left');
        $this->db->where('active', 1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }else
    {
        $this->db->select('p.id, p.name, p.active');
        $this->db->from('properties p');
        $this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id);
        $this->db->where('active', 1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null; 
    }
    }

    public function getProperties3($id, $all_props)
    {   
        $this->db->select('p.id, p.name, p.active');
        $this->db->from('properties p');
        $this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id);
        $this->db->where('active', 1);
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
        $this->db->select('id, name, parent_id, property_id');
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

    function getNames()
    {
        $this->db->select('id, LTRIM(CONCAT_WS("",first_name," ", last_name)) AS vendor');
        $this->db->from('profiles');
        $this->db->where('profile_type_id', 1);
        $this->db->ORDER_BY('vendor');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

        function getAllEmployees()
    {
        $this->db->select('id, CONCAT_WS("",first_name," ", last_name) AS employee');
        $this->db->from('profiles');
        $this->db->where('profile_type_id', 4);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

   
}
