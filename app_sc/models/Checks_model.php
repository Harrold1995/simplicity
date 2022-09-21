<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Checks_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('encryption_model');
    }

//     public function test($data)
//     {
//         foreach($data AS $key => &$value){
//             if($key == 'header'){
//                 $key = header[]
//                 $trans_id = $this->db->insert_id();
//                 $key['last_mod_by'] = $this->ion_auth->get_user_id();
//                 $key['last_mod_date'] = date('Y-m-d H:i:s');
//                 $key['transaction_type'] = 4;
//             }
        
//             if($key == 'transactions'){//loop through values and place trans id in
//                 foreach($value as &$val){
//                     $val['trans_id']  = $trans_id;
//                 } 
//             }
//             if($key == 'checks'){
//                 $key['paid_to'] = 1;//$headerTransaction['profile_id'];
//                 $key['trans_id'] = $trans_id;
                
//             }
           
        
//      $this->db->insert_batch($key, $value);
    
 
//  }
 
//  for($i = 0; $i < 10; $i++){
//      if($i = 3){
//          $a = 'hello';
//      }
//      if($i = 7){
//          $b = $a;
//          echo $b;
//          continue;
//      }
//  }
//     }

    public function addCheck($header,$headerTransaction, $transactions, $print)
    {   
        $this->db->trans_start();
        
        $trans_id = $this->addHeader($header, 4);
        //$headerTransaction['credit'] = str_replace(',', '' , $headerTransaction['credit']); 
        //$headerTransaction['trans_id'] = $trans_id;
        $special['trans_id'] = $trans_id;
        $account_id;
        foreach($headerTransaction as &$transaction){
                $special['paid_to'] = $transaction['profile_id'];
                $transaction['trans_id'] = $trans_id; 
                $account_id = $transaction['account_id'];
        }
        $this->db->insert_batch('transactions', $headerTransaction);
        //$this->db->insert('transactions', $headerTransaction);
        $filled = $this->removeEmpty($transactions, $trans_id);
        
        $this->addDetails($filled);
       
        //$special['paid_to'] = $headerTransaction['profile_id'];
        //$special['trans_id'] = $trans_id;
        $this->db->insert('checks', $special);

        if($print === '1'){
            $account = [['id' => $account_id, 'th_id' => $trans_id]];
            $data = $this->onPrintMany($account);
        } 

                         
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
        }
        
        if($print === '1'){
            $checkinfo = new stdClass();
            $checkinfo->data = $data;
            return $checkinfo;
        }else{
            return true;
        }
    }

    public function getHeader($id)
    {   
        $this->db->select('th.id, th.transaction_type, CONCAT(DATE_FORMAT(th.last_mod_date, "%m/%d/%Y")," ",TIME_FORMAT(th.last_mod_date, "%r")) AS modified, th.last_mod_by, CONCAT_WS(" ",p.first_name,p.last_name) AS user,  th.transaction_ref, th.transaction_date AS date, th.memo, to_print');
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

    public function getTransactions($id)
    {   
        //$q = $this->db->order_by('line_number ASC', 'id ASC')->get_where('transactions', array('trans_id' => $id));
        $this->db->select('group_concat(id) as id, account_id as account_id, max(property_id) as property_id, max(profile_id) as profile_id, max(lease_id) as lease_id, max(unit_id) as unit_id, max(description) as description, max(class_id) as class_id, sum(debit) as debit, sum(credit) as credit, max(rec_id) as rec_id, max(clr) as clr');
        $this->db->from('transactions');
        $this->db->where(array('trans_id' => $id));
        $this->db->order_by('line_number ASC', 'id ASC');
        $this->db->group_by('line_number, account_id');
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getCheck($id)
    {
        $q = $this->db->get_where('checks', array('trans_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAddress($id)
    {   
        $this->db->select('address_line_1,address_line_2, CONCAT(city," ", state," ",area_code) AS city , CONCAT_WS(" ",first_name, last_name) AS vendor');
        $this->db->from('profiles');
        $this->db->where(array('id'=> $id));        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAddress1($id)
    {   
        $this->db->select('p.address_line_1, p.address_line_2, CONCAT(p.city," ", p.state," ",p.area_code) AS city');
        $this->db->from('transactions t');
        $this->db->join('profiles p','t.profile_id = p.id','left');
        $this->db->where(array('t.trans_id' => $id));
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getBalance($aid)
    {
        $this->db->select('account_id, SUM(debit - credit) AS balance');
        $this->db->from('transactions');
        $this->db->where('account_id', $aid);
        $this->db->group_by('account_id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->balance ? $q->row()->balance : 0;
        }
        

    }
    public function editCheck($header, $headerTransaction, $headerTransactionsAdd, $transactions, $id, $print, $deletes)
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
        $special['paid_to'] = $headerTransaction['profile_id'];
        $this->db->update('checks', $special, array('trans_id' => $id));
        if($print === '1'){
            $account = [['id' => current($headerTransaction)['account_id'], 'th_id' => $id]];
            $data = $this->onPrintMany($account);
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
        }
        if($print === '1'){
            $checkinfo = new stdClass();
            $checkinfo->data = $data;
            return $checkinfo;
        }else{
            return true;
        }
         
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

    public function getAllBanks()
    {
        $this->db->select('a.id, a.name, b.account_number, b.routing, a.all_props');
        $this->db->from('accounts a');
        $this->db->join('banks b','a.id = b.account_id','left');
        $this->db->where(array('account_types_id'=> 1));        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    // function getAccounts($pid)

    // {   // #1 SubQueries no.1 -------------------------------------------
    //     $this->db->select('id, name, accno, all_props');
    //     $this->db->from('accounts');
    //     $this->db->where(array('all_props'=> 1, 'active' => 1));
    //     //$query = $this->db->get();
    //     $all_props1 = $this->db->get_compiled_select();
    //     //echo $all_props1. 'UNION';
       
    //     $this->db->reset_query();
        
    //     // #2 SubQueries no.2 -------------------------------------------
        
    //     $this->db->select('a.id, a.name, a.accno, all_props');
    //     $this->db->from('accounts a');
    //     $this->db->join('property_accounts pa', 'a.id = pa.account_id AND pa.property_id ='. $pid);
    //     $this->db->where('a.active', 1);
    //     //$query = $this->db->get();
    //     $all_props0 = $this->db->get_compiled_select();
    //     //echo $all_props0;
    //     $this->db->reset_query();
        
    //     // #3 Union with Simple Manual Queries --------------------------
        
    //     $q = $this->db->query("$all_props1 UNION $all_props0");
        
        
        
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as $row) {
    //             $data[] = $row;
    //         }
    //         print_r($data);
    //     }
    //     return array();
    // }

    function getPropertyAccounts($pid)

    {  
        $this->db->select('id, name, parent_id');
        $this->db->from('accounts');
        $this->db->where('(all_props = 1 AND active = 1) OR (id IN (SELECT account_id FROM property_accounts WHERE property_id =' . $this->db->escape($pid) . ') AND active = 1)');
        $this->db->distinct();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
          $data =  $this->site->getNestedSelect($data);
          
          return $data;
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
        $this->db->select('p.id, p.name, p.active');//, pa.property_id
        $this->db->from('properties p');
        //$this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id,'left');

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

    // public function getProperties2($id, $all_props)
    // {   if($all_props==1)
    //     {
    //     $this->db->select('id, name, active');
    //     $this->db->from('properties');
    //     //$this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id,'left');
    //     $this->db->where('active', 1);
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return null;
    // }else
    // {
    //     $this->db->select('p.id, p.name, p.active');
    //     $this->db->from('properties p');
    //     $this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id);
    //     $this->db->where('active', 1);
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return null; 
    // }
    // }

    // public function getProperties3($id, $all_props)
    // {   
    //     $this->db->select('p.id, p.name, p.active');
    //     $this->db->from('properties p');
    //     $this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id);
    //     $this->db->where('active', 1);
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return null; 
    // }
    

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

    public function getPropertyUnits($pid, $addtype = false)
    {   
        $this->db->select('id, name, parent_id, property_id');
        $this->db->from('units');
        $this->db->where('property_id', $pid);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'account_type';
                $data[] = $row;
            }
             $data =  $this->site->getNestedSelect($data);
             return $data;

        }
        return null;
    }

    public function getNames($aid = 0)
    {
        $this->db->select('id, def_expense_acc, LTRIM(CONCAT_WS("",first_name," ", last_name)) AS vendor');
        $this->db->from('profiles');
        if($aid ==  $this->site->settings->accounts_receivable){
            $this->db->where('profile_type_id', 3);
        }
        if($aid == $this->site->settings->accounts_payable){
            $this->db->where('profile_type_id', 1);
        }
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

    // public function getVendorsTenants($aid)
    // {
    //     $this->db->select('id, CONCAT_WS(" ",first_name, last_name) AS vendor');
    //     $this->db->from('profiles');
    //     if($aid == 451){
    //         $this->db->where('profile_type_id', 3);
    //     }
    //     if($aid == 454){
    //         $this->db->where('profile_type_id', 1);
    //     }
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return array();
    // }

    

    // public function onPrint($aid, $th_id)
    // {
    //     $this->db->trans_start();
    //     $this->db->select('routing, account_number, next_check_num');
    //     $this->db->from('banks');
    //     $this->db->where('account_id', $aid);
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             $data[] = $row;
    //         }
    //     } 
    //     if ($q->num_rows() > 0) {
    //         $next_check_num = $q->row()->next_check_num;
    //     }

    //     $this->db->where('id', $th_id);
    //     $this->db->update('transaction_header', array('transaction_ref' => $next_check_num));

    //     $this->db->where('id', $th_id);
    //     $this->db->update('checks', array('to_print' => 0));

    //     $this->db->where('account_number', $aid);
    //     $this->db->update('banks', array('next_check_num' => $next_check_num + 1));

    //     $this->db->trans_complete();
    //     return $data;
        
    // }

    public function getTransDetails($th_id, $id)


    {
        $this->db->select('a.name AS account , props.name AS property, t.description, t.debit');
        $this->db->from('transactions t');
        $this->db->join('accounts a', 't.account_id = a.id');
        $this->db->join('properties props', 't.property_id = props.id');
        $this->db->where('trans_id', $th_id);
        $this->db->where('a.id !=', $id);
        $this->db->order_by('t.id  ASC');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
             //array_shift($data);
             return $data;
        } 
        
    }

    function printBlankCheck($accountId){
        $this->db->select(' b.routing, b.account_number, IFNULL(b.bank_name, "") as bank_name, IFNULL(b.bank_address, "") as bank_address, b.next_check_num, pr.name, pr.address, pr.city, pr.state, pr.zip, IFNULL(e.name, pr.name) as eName, IFNULL(e.address, pr.address) as eAddress, IFNULL(e.city, pr.city) as eCity, IFNULL(e.state, pr.state) as eState, IFNULL(e.zip, pr.zip) as Ezip');
        //$this->db->select(' b.routing, b.account_number, b.next_check_num, e.name, pr.address, CONCAT_WS( " ", pr.city, pr.state) as cs, pr.zip, IFNULL(e.name, pr.name) as eName, IFNULL(e.address, pr.address) as eAddress, IFNULL(e.city, pr.city) as eCity, IFNULL(e.state, pr.state) as eState, IFNULL(e.zip, pr.zip) as Ezip');
        $this->db->from('banks b');
        $this->db->join('properties pr', 'pr.id = b.property', 'left');
        $this->db->join('entities e', 'e.id = pr.entity_id', 'left');
        $this->db->where('b.account_id', $accountId);
        $this->db->order_by('pr.id');
        $this->db->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $data = $q->row();
            $data = $this->encryption_model->decryptThis($data);
                $this->db->set('next_check_num', 'next_check_num + 1', FALSE);
                $this->db->where('account_id', $accountId);
                $this->db->update('banks'); 
            return $data;
        }
        return false;
    }

    public function onPrintMany($accounts)
    {   
        $this->db->trans_start();
        foreach($accounts as $account){
            
            $this->db->select('max(th.id) AS th_id, max(th.transaction_date) as date, max(th.memo) as memo, sum(t.credit) as credit, IFNULL(max(b.bank_name), "") as bank_name, IFNULL(max(b.bank_address), "") as bank_address, max(b.routing) as routing, max(b.account_id) as account_id, max(b.account_number) as account_number, ifnull(max(b.next_check_num), 1) as next_check_num, CONCAT_WS( " ", max(p.first_name), max(p.last_name)) as profile, max(p.address_line_1) as address_line_1,  max(p.address_line_2) as address_line_2, ifnull(max(e.city), max(p.city)) as city, ifnull(max(e.state) ,max(p.state)) as state, ifnull(max(e.zip),max(p.area_code)) AS zip, IFNULL(max(e.name), max(pr.name)) as eName, IFNULL(max(e.address), max(pr.address)) as eAddress, IFNULL(max(e.city), max(pr.city)) as eCity, IFNULL(max(e.state), max(pr.state)) as eState, IFNULL(max(e.zip), max(pr.zip)) as Ezip, IFNULL(max(e.email), "") as eEmail, IFNULL(max(e.phone), "") as ePhone');
            $this->db->from('banks b');
            $this->db->join('transactions t', 'b.account_id = t.account_id');
            $this->db->join('transaction_header th', 't.trans_id = th.id AND th.id ='. $account['th_id']);
            $this->db->join('profiles p', 't.profile_id = p.id', 'left');
            $this->db->join('properties pr', 'pr.id = t.property_id', 'left');
            $this->db->join('properties epr', 'epr.id = b.property', 'left');
            $this->db->join('entities e', 'e.id = epr.entity_id', 'left');
            $this->db->where('b.account_id', $account['id']);
            $this->db->order_by('t.id ASC');
            $this->db->group_by('line_number');
            $this->db->limit(1);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                $q->row()->details = $this->getTransDetails($account['th_id'],$account['id']);
                //$data[] = $q->row();
                $data1 = $q->row();
                $data1 = $this->encryption_model->decryptThis($data1);
                //$data1->account_number = $this->encryption_model->decryptThis($data1->account_number);
                $data[] = $data1;
            } 
            
            if ($q->num_rows() > 0) {
                $this->db->where('id', $account['th_id']);
                $this->db->update('transaction_header', array('transaction_ref' => $q->row()->next_check_num));

                if( !isset($account['single']) ){
                    $this->db->set('next_check_num', 'next_check_num + 1', FALSE);
                    $this->db->where('account_id', $account['id']);
                    $this->db->update('banks'); 
                }



                // gives UPDATE `mytable` SET `field` = 'field+1' WHERE `id` = 2
                // $this->db->where_in('account_id', $aIds);
                // $this->db->update('banks', array('next_check_num' => 'next_check_num + 1'));

                //test - failed
                // $this->db->where('account_id', $account['id']);
                // $this->db->update('banks', array('next_check_num' => $next_check_num + 1));
            }

            
        }

        
        //$aIds = array_column($accounts, 'id');
        $th_ids = array_column($accounts, 'th_id');

        if($th_ids){
            $this->db->where_in('id', $th_ids);
            $this->db->update('transaction_header', array('to_print' => 0));
        }
        
        // if($aIds){
        //     $this->db->set('next_check_num', 'next_check_num + 1', FALSE);
        //     $this->db->where_in('account_id', $aIds);
        //     $this->db->update('banks'); // gives UPDATE `mytable` SET `field` = 'field+1' WHERE `id` = 2
        //     // $this->db->where_in('account_id', $aIds);
        //     // $this->db->update('banks', array('next_check_num' => 'next_check_num + 1'));
        // }
        $this->db->trans_complete();
        // foreach($data as $singleData){
        //     $singleData = $this->encryption_model->decryptThis($singleData);
        // }
        return $data;
        
    }

    public function confirmPrint($account)
    {   
        $this->db->trans_start();
        
        if($account['printResult'] === 'fail'){
            $this->db->where('id', $account['th_id']);
            $this->db->update('transaction_header', array('to_print' => 1));

            $this->db->set('next_check_num', 'next_check_num - 1', FALSE);
            $this->db->where('account_id', $account['account_id']);
            $this->db->update('banks'); 
        }
        
        
        if($account['printResult'] === 'pass'){
            $this->db->where('id', $account['th_id']);
            $this->db->update('transaction_header', array('transaction_ref' => $account['check_number'])); 

            $next_check_num = $account['next_check_num'];
            $check_number = $account['check_number'];

            if($account['next_check_number'] <= $account['check_number']){
                $this->db->set('next_check_num', $account['check_number'] + 1, FALSE);
                $this->db->where('account_id', $account['account_id']);
                $this->db->update('banks'); 
            }
            
            
        }
        $this->db->trans_complete();
        
       
        
    }

    // public function onPrintMany1($accounts)
    // {   
    //     $this->db->trans_start();
    //     foreach($accounts as $account){
            
    //         $this->db->select($account['th_id'] . ' AS th_id, routing, account_number, next_check_num');
    //         $this->db->from('banks');
    //         $this->db->where('account_id', $account['id']);
    //         $q = $this->db->get();
    //         if ($q->num_rows() > 0) {
    //             $data[] = $q->row();
    //         } 
    //         if ($q->num_rows() > 0) {
    //             $next_check_num = $q->row()->next_check_num;
    //         }

    //         $this->db->where('id', $account['th_id']);
    //         $this->db->update('transaction_header', array('transaction_ref' => $next_check_num));
    //     }

        
    //     $aIds = array_column($accounts, 'id');
    //     $th_ids = array_column($accounts, 'th_id');

    //     $this->db->where_in('id', $th_ids);
    //     $this->db->update('transaction_header', array('to_print' => 0));

    //     $this->db->set('next_check_num', 'next_check_num + 1', FALSE);
    //     $this->db->where_in('account_id', $aIds);
    //     $this->db->update('banks'); // gives UPDATE `mytable` SET `field` = 'field+1' WHERE `id` = 2
    //     // $this->db->where_in('account_id', $aIds);
    //     // $this->db->update('banks', array('next_check_num' => 'next_check_num + 1'));
    //     $this->db->trans_complete();
    //     return $data;
        
    // }

    function getPropertyAccounts2()
    {
        $this->db->select('p.*');
        $this->db->from('property_accounts p');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    function getChecksToPrint()
    {
        $this->db->select('t.trans_id as id, max(th.transaction_date) as date, max(CONCAT_WS("", p.first_name," ",p.last_name)) as profile, max(prop.name) as property, t.account_id, max(a.name) as account, max(b.next_check_num) AS check_num, sum(t.credit - t.debit) as amount');//b.next_check_num ch.check_num
        $this->db->from('transactions t');
        $this->db->join('transaction_header th', 't.trans_id = th.id');
        //$this->db->join('checks ch', 'th.id = ch.trans_id', 'left');
        $this->db->join('banks b', 't.account_id = b.account_id', 'left');
        $this->db->join('profiles p', 't.profile_id = p.id');
        $this->db->join('properties prop', 't.property_id = prop.id');
        $this->db->join('accounts a', 't.account_id = a.id');
        $this->db->where('th.transaction_type', 4);
        $this->db->where('th.to_print', 1);
        $this->db->where('a.account_types_id =', 1);
        $this->db->group_by('account_id, trans_id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                //  $q = $this->db->select('CONCAT_WS(" ",first_name, last_name) as name')->get_where('profiles', array('id' => $row->profile), 1);
                //  $row->profile = $q->row()->name;
                // $q = $this->db->select('name')->get_where('properties', array('id' => $row->property), 1);
                // $row->property = $q->row()->name;
                // $row->account_id = $row->account;
                // $q = $this->db->select('name')->get_where('accounts', array('id' => $row->account), 1);
                // $row->account = $q->row()->name; 
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

        function getDefaultBank($id){
        $this->db->select('a.id, a.name');
        $this->db->from('accounts a');
        $this->db->join('properties p', 'p.default_bank = a.id');
        $this->db->where('p.id =', $id);
        $q = $this->db->get();
            if ($q->num_rows() > 0) {
                $bank = $q->row();
                return $bank;
            }
            return false;
        }
}
