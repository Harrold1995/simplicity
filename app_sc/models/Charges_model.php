<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Charges_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ar = $this->site->settings->accounts_receivable;
    }

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

    public function addCharge($header, $transaction)
    {   
        $this->db->trans_start();

        $trans_id = $this->addHeader($header, 6);
        $transaction['account_id'] = $transaction['account_id'] ? $transaction['account_id'] : $this->getAccountId($transaction['item_id']);
        $transaction['trans_id'] = $trans_id;
        $this->db->insert('transactions', $transaction);
        $dc = $transaction['credit'] ? 'debit' : 'credit';
        $dcv = $transaction['credit'] ? $transaction['credit'] : $transaction['debit'];
        //$accounts_receivable = ['account_id' => $this->ar, 'profile_id' => $transaction['profile_id'], 'lease_id' => $transaction['lease_id'], 'property_id' => $transaction['property_id'], 'unit_id' => $transaction['unit_id'], 'trans_id' => $trans_id, 'description' => $transaction['description'], 'debit' => $transaction['credit'], 'item_id' => $transaction['item_id']];
        $accounts_receivable = ['account_id' => $this->ar, 'profile_id' => $transaction['profile_id'], 'lease_id' => $transaction['lease_id'], 'property_id' => $transaction['property_id'], 'unit_id' => $transaction['unit_id'], 'trans_id' => $trans_id, 'description' => $transaction['description'], $dc => $dcv == null ? 0 : $dcv, 'item_id' => $transaction['item_id']];
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

    public function getTransaction1($id)
    {   

        $q = $this->db->order_by('id', 'asc')->get_where('transactions', array('trans_id' => $id, 'account_id !=' => $this->ar));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTransaction($id)
    {   
        $this->db->select('id, trans_id, property_id, lease_id, unit_id, profile_id, (credit - debit) AS credit, item_id, description');
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
        
        $dc1 = $transaction['credit'] ? 'credit' : 'debit';
        $dc = $transaction['credit'] ? 'debit' : 'credit';
        $dcv = $transaction['credit'] ? $transaction['credit'] : $transaction['debit'];

        $transaction['account_id'] = $this->getAccountId($transaction['item_id']);
        $transaction['credit'] = $transaction['credit'] ? $transaction['credit'] : '0.00';
        $transaction['debit'] = $transaction['debit'] ? $transaction['debit'] : '0.00';


        $this->db->update('transactions', $transaction, array('trans_id' => $id, 'account_id !=' =>$this->ar));



        //$accounts_receivable = ['profile_id' => $transaction['profile_id'], 'lease_id' => $transaction['lease_id'], 'property_id' => $transaction['property_id'], 'unit_id' => $transaction['unit_id'], 'description' => $transaction['description'], 'debit' => $transaction['credit'], 'item_id' => $transaction['item_id']];
        $accounts_receivable = ['profile_id' => $transaction['profile_id'], 'lease_id' => $transaction['lease_id'], 'property_id' => $transaction['property_id'], 'unit_id' => $transaction['unit_id'], 'description' => $transaction['description'], $dc => $dcv, $dc1 => '0.00', 'item_id' => $transaction['item_id']];
      
        $this->db->update('transactions', $accounts_receivable, array('trans_id' => $id, 'account_id' =>$this->ar));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;// generate an error... or use the log_message() function to log your error
        }


        return true;
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

    public function getAllLeases()
    {
        $this->db->select('l.*, substring_index(GROUP_CONCAT(p.name SEPARATOR "#") , "#",1) as property, substring_index(GROUP_CONCAT(u.name SEPARATOR "#") , "#",1) as unit, substring_index(GROUP_CONCAT(p.id SEPARATOR "#")  , "#",1) as property_id, GROUP_CONCAT(lp.profile_id) AS profile_leases');
        $this->db->from('leases l');
        $this->db->join('leases_profiles lp', 'l.id = lp.lease_id');
        $this->db->join('units u', 'l.unit_id = u.id');
        $this->db->join('properties p', 'u.property_id = p.id');
        //$this->db->where('lp.profile_id', $pid);
        $this->db->group_by('l.id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $row->type = 'lease';
                $row->name = $row->start . ' - ' . $row->end;
            }
            return $q->result();
        }
        return null;
    }

    
    public function getLeases($id, $type, $date = null, $startDate = null)
    {
        $bill_collectively = $this->db->get_where('leases', array('id' => $id))->row('bill_collectively');
        //SELECT sum(debit - credit) as balance, lease_id, profile_id FROM transactions WHERE account_id = '$this->ar' GROUP BY lease_id, profile_id
        $this->db->select('SUM(t.debit - t.credit) as balance, t.lease_id, t.profile_id');
        $this->db->from('transactions t');        
        if($date){
            $sqlDate = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
            $this->db->join('transaction_header th', 'th.id = t.trans_id');
            $this->db->where('th.transaction_date <=', $sqlDate);
            
        }

        $this->db->where('t.account_id', $this->ar);
        $this->db->group_by('t.lease_id, t.profile_id');

        $indivbal = $this->db->get_compiled_select();
        $this->db->reset_query();

        //SELECT sum(debit - credit) as balance, lease_id FROM transactions WHERE account_id = '$this->ar' GROUP BY lease_id
        $this->db->select('SUM(t.debit - t.credit) as balance, t.lease_id');
        $this->db->from('transactions t');
        if($date){
            $sqlDate = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
            $this->db->join('transaction_header th', 'th.id = t.trans_id');
            $this->db->where('th.transaction_date <=', $sqlDate);
            
        }
        $this->db->where('t.account_id', $this->ar);
        $this->db->group_by('t.lease_id');

        $colbal = $this->db->get_compiled_select();
        $this->db->reset_query();

       

        if($bill_collectively == 1 && $type == 'lease'){

            $this->db->select('l.id, group_concat(lp.profile_id) as profile_id, group_concat(CONCAT_WS(" ",p.first_name,p.last_name) SEPARATOR ", ") AS name, max(p.address_line_1) as address_line_1, max(p.address_line_2) as address_line_2, max(CONCAT(p.city,", ",p.state)) AS cs, max(p.area_code) AS zip, l.bill_collectively, max(colbal.balance) AS Tbalance, IFNULL(max(e.name), max(pr.name)) as eName, IFNULL(max(e.address), max(pr.address)) as eAddress, IFNULL(max(e.city), max(pr.city)) as eCity, IFNULL(max(e.state), max(pr.state)) as eState, IFNULL(max(e.zip), max(pr.zip)) as Ezip, IFNULL(max(e.email), "") as eEmail, IFNULL(max(e.phone), "") as ePhone, max(pr.name) as property, max(u.name) as unit');
            $this->db->from('leases l');
            $this->db->join('leases_profiles lp', 'lp.lease_id = l.id');
            $this->db->join('units u', 'u.id = l.unit_id');
            $this->db->join('properties pr', 'pr.id = u.property_id');
            $this->db->join('entities e', 'e.id = pr.entity_id', 'left');
            $this->db->join('profiles p', 'lp.profile_id = p.id AND p.active = 1');
            $this->db->join('(' . $colbal . ')colbal', 'l.id = colbal.lease_id');
            $this->db->group_by('l.id'); 
            $this->db->where('l.id', $id);
        } else {
            $this->db->select('l.id, lp.profile_id, CONCAT_WS(" ",p.first_name,p.last_name) AS name, p.address_line_1, p.address_line_2, CONCAT(p.city,", ",p.state) AS cs, p.area_code AS zip, l.bill_collectively, IF(l.bill_collectively IS NULL , indivbal.balance, colbal.balance) AS Tbalance, IFNULL(e.name, pr.name) as eName, IFNULL(e.address, pr.address) as eAddress, IFNULL(e.city, pr.city) as eCity, IFNULL(e.state, pr.state) as eState, IFNULL(e.zip, pr.zip) as Ezip, IFNULL(e.email, "") as eEmail, IFNULL(e.phone, "") as ePhone, pr.name as property, u.name as unit');
            $this->db->from('leases l');
            $this->db->join('leases_profiles lp', 'lp.lease_id = l.id');
            $this->db->join('units u', 'u.id = l.unit_id', 'left');
            $this->db->join('properties pr', 'pr.id = u.property_id', 'left');
            $this->db->join('entities e', 'e.id = pr.entity_id', 'left');
            $this->db->join('profiles p', 'lp.profile_id = p.id AND p.active = 1');
            $this->db->join('(' . $colbal . ')colbal', 'l.id = colbal.lease_id');
            $this->db->join('(' . $indivbal . ')indivbal', 'lp.profile_id = indivbal.profile_id AND l.id = indivbal.lease_id','left');
            
            if($type == 'lease'){
                $this->db->where('l.id', $id); 
            }else if($type == 'tenant'){
                $this->db->where('p.id', $id); 
            }else{
                $this->db->where('(l.start <= CURDATE() AND l.end > CURDATE())');
                $this->db->or_where('(IF(l.bill_collectively IS NULL, indivbal.balance > 0, colbal.balance > 0))'); 
            }
        }
        
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $row->data = $this->getLastTrans($row->id, $row->profile_id, $row->bill_collectively, $row->Tbalance, $date, $startDate);
                // if(!$row->bill_collectively && $row->data->total == 0 ){
                //     continue;
                // }
                $allData[] = $row;
            }
            return $allData;
        }
        return null;
    }

    public function getProfiles($lease, $profile)
    {
        //SELECT sum(debit - credit) as balance, lease_id, profile_id FROM transactions WHERE account_id = '$this->ar' GROUP BY lease_id, profile_id
        $this->db->select('SUM(debit - credit) as balance, lease_id, profile_id');
        $this->db->from('transactions');
        $this->db->where('account_id', $this->ar);
        $this->db->group_by('lease_id, profile_id');

        $indivbal = $this->db->get_compiled_select();
        $this->db->reset_query();

        //SELECT sum(debit - credit) as balance, lease_id FROM transactions WHERE account_id = '$this->ar' GROUP BY lease_id
        $this->db->select('SUM(debit - credit) as balance, lease_id');
        $this->db->from('transactions');
        $this->db->where('account_id', $this->ar);
        $this->db->group_by('lease_id');

        $colbal = $this->db->get_compiled_select();
        $this->db->reset_query();

        $this->db->select('l.id, lp.profile_id, CONCAT_WS(" ",p.first_name,p.last_name) AS name, p.address_line_1, p.address_line_2, CONCAT(p.city,", ",p.state) AS cs, p.area_code AS zip, l.bill_collectively, IF (l.bill_collectively IS NULL , indivbal.balance, colbal.balance) AS Tbalance, IFNULL(e.name, pr.name) as eName, IFNULL(e.address, pr.address) as eAddress, IFNULL(e.city, pr.city) as eCity, IFNULL(e.state, pr.state) as eState, IFNULL(e.zip, pr.zip) as Ezip, IFNULL(e.email, "") as eEmail, IFNULL(e.phone, "") as ePhone, pr.name as property, u.name as unit');
        $this->db->from('leases l');
        $this->db->join('leases_profiles lp', 'lp.lease_id = l.id');
        $this->db->join('units u', 'u.id = l.unit_id', 'left');
        $this->db->join('properties pr', 'pr.id = u.property_id', 'left');
        $this->db->join('entities e', 'e.id = pr.entity_id', 'left');
        $this->db->join('profiles p', 'lp.profile_id = p.id AND p.active = 1');
        $this->db->join('(' . $indivbal . ')indivbal', 'lp.profile_id = indivbal.profile_id AND l.id = indivbal.lease_id','left');
        $this->db->join('(' . $colbal . ')colbal', 'l.id = colbal.lease_id','left');

        $this->db->where('lp.profile_id', $profile); 
        $this->db->where('l.id', $lease);
      
        $this->db->where('((l.start <= CURDATE() AND l.end > CURDATE()) OR (IF(l.bill_collectively IS NULL, indivbal.balance > 0, colbal.balance > 0)))');
            
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $row->data = $this->getLastTrans($row->id, $row->profile_id, $row->bill_collectively, $row->Tbalance);
                $allData[] = $row;
            }
            return $allData;
        }
        return null;
    }

    public function getAllProfiles($profileId  = null)
    {

        //SELECT sum(debit - credit) as balance, lease_id, profile_id FROM transactions WHERE account_id = '$this->ar' GROUP BY lease_id, profile_id
        $this->db->select('SUM(debit - credit) as balance, lease_id, profile_id');
        $this->db->from('transactions');
        $this->db->where('account_id', $this->ar);
        $this->db->group_by('lease_id, profile_id');

        $indivbal = $this->db->get_compiled_select();
        $this->db->reset_query();

        //SELECT sum(debit - credit) as balance, lease_id FROM transactions WHERE account_id = '$this->ar' GROUP BY lease_id
        $this->db->select('SUM(debit - credit) as balance, lease_id');
        $this->db->from('transactions');
        $this->db->where('account_id', $this->ar);
        $this->db->group_by('lease_id');

        $colbal = $this->db->get_compiled_select();
        $this->db->reset_query();




        $this->db->select('l.id as lease_id, l.in_court, p.email_statements, p.mail_statements, CONCAT_WS(" ",p.first_name,p.last_name) AS name, CONCAT(l.start," ",l.end) as leaseName, lp.profile_id, pr.name as propertyName, u.name as unitName, IF (l.bill_collectively IS NULL , indivbal.balance, colbal.balance) AS balance ');
        $this->db->from('leases l');
        $this->db->join('leases_profiles lp', 'lp.lease_id = l.id');
        $this->db->join('units u', 'u.id = l.unit_id', 'left');
        $this->db->join('properties pr', 'pr.id = u.property_id', 'left');
        $this->db->join('entities e', 'e.id = pr.entity_id', 'left');
        $this->db->join('profiles p', 'lp.profile_id = p.id AND p.active = 1');
        $this->db->join('(' . $indivbal . ')indivbal', 'lp.profile_id = indivbal.profile_id AND l.id = indivbal.lease_id');
        $this->db->join('(' . $colbal . ')colbal', 'l.id = colbal.lease_id');

            $this->db->where('(l.start <= CURDATE() AND l.end > CURDATE())');
            $this->db->or_where('(IF(l.bill_collectively IS NULL, indivbal.balance > 0, colbal.balance > 0))');
           if($profileId){
            $this->db->where('(p.id ='. $profileId);
           }   
      
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $allData[] = $row;
            }
            return $allData;
        }
        return null;
    }

    public function getTenant($tid)
    {
        $this->db->select('pr.*, lp.lease_id as lease_id, CONCAT(pr.first_name," ",pr.last_name) as name, p.name as property, u.name as unit, u.id as unit_id, p.id as property_id, lp.lease_id');
        $this->db->from('profiles pr');
        $this->db->join('leases_profiles lp', 'lp.profile_id = pr.id', 'left');
        $this->db->join('units u', 'lp.unit_id = u.id', 'left');
        $this->db->join('properties p', 'u.property_id = p.id', 'left');
        $this->db->where("pr.id=".$tid)->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data;
        }
        return null;
    }

    ///need to do for charges this is a copy from checks
    // public function printInvoice($accounts)
    // {   
    //     $this->db->trans_start();
    //     foreach($accounts as $account){
            
    //         $this->db->select('th.id AS th_id, th.transaction_date, t.debit AS amount, t.description, CONCAT(p.first_name, " ", p.last_name), props.name, u.name');
    //         $this->db->from('transaction_header th');
    //         $this->db->join('transactions t', 'th.id = t.trans_id AND t.trans_id =' . $account['th_id']);
    //         $this->db->join('accounts a', 't.account_id = a.id AND a.id =$this->ar');
    //         $this->db->join('profiles p', 't.profile_id = p.id');
    //         $this->db->join('properties props', 't.property_id = props.id');
    //         $this->db->join('units u', 't.unit_id = u.id');
    //         $q = $this->db->get();
    //         if ($q->num_rows() > 0) {
    //             $data[] = $q->row();
    //         } 
            // if ($q->num_rows() > 0) {
            //     $this->db->where('id', $account['th_id']);
            //     $this->db->update('transaction_header', array('transaction_ref' => $q->row()->next_check_num));
            // }

            
   //     }

        
        // $aIds = array_column($accounts, 'id');
        // $th_ids = array_column($accounts, 'th_id');

        // $this->db->where_in('id', $th_ids);
        // $this->db->update('transaction_header', array('to_print' => 0));

        // $this->db->set('next_check_num', 'next_check_num + 1', FALSE);
        // $this->db->where_in('account_id', $aIds);
        // $this->db->update('banks'); // gives UPDATE `mytable` SET `field` = 'field+1' WHERE `id` = 2
        // $this->db->where_in('account_id', $aIds);
        // $this->db->update('banks', array('next_check_num' => 'next_check_num + 1'));
        //$this->db->trans_complete();
        //return $data;
        
  //  }

    // public function test()
    // {
    //     $this->db->select('lease_id, SUM(debit - credit) AS totalBalance');
    //     $this->db->from('transactions t');
    //     $this->db->join('leases t', 't.lease_id = l.id');
    //     $this->db->where('t.account_id', $this->ar);
    //     $this->db->where('t.lease_id', $lease_id);
    //     $this->db->where('(IF(l.bill_collectively = 0, t.profile_id = lp.profile_id AND t.lease_id = $lease_id, t.lease_id = $lease_id))'); 
        
    //     $sql = $this->db->get_compiled_select();

    //     foreach($leases as $lease_id){
    //         $this->db->select('t.lease_id, lp.profile_id, CONCAT_WS(" ",p.first_name,p.last_name) AS name, p.address_line_1, p.address_line_2, CONCAT(p.city,", "p.state) AS cs, p.area_code AS zip, l.bill_collectively, SUM(t.debit - t.credit) AS totalBalance');
    //         $this->db->from('leases l');
    //         $this->db->join('leases_profiles lp', 'l.id = lp.lease_id');
    //         $this->db->join('profiles p', 'lp.profile_id = p.id AND p.active = 1');
    //         $this->db->where('lease_id', $lease_id);
            
    //         $q = $this->db->get();
    //         if ($q->num_rows() > 0) {
    //             foreach (($q->result()) as &$row) {
    //                 $row->data = $this->getBalance($lease_id, $row->profile_id, $row->bill_collectively);
    //                 $allData[] = $row;
    //             }
    //         }
    //     } 

       
    // }

    // public function getTenants($leases)
    // {

    //     foreach($leases as $lease_id){
    //         $this->db->select('lp.profile_id, CONCAT_WS(" ",p.first_name,p.last_name) AS name, p.address_line_1, p.address_line_2, CONCAT(p.city,", ",p.state) AS cs, p.area_code AS zip, l.bill_collectively');
    //         $this->db->from('leases l');
    //         $this->db->join('leases_profiles lp', 'l.id = lp.lease_id');
    //         $this->db->join('profiles p', 'lp.profile_id = p.id AND p.active = 1');
    //         $this->db->where('lease_id', $lease_id);
            
    //         $q = $this->db->get();
    //         if ($q->num_rows() > 0) {
    //             foreach (($q->result()) as &$row) {
    //                 $row->data = $this->getBalance($lease_id, $row->profile_id, $row->bill_collectively);
    //                 $allData[] = $row;
    //             }
    //         }
    //     }
    //     return $allData;
    // }

    public function getBalance($lease_id, $profile_id, $bill_collectively)
    {
        $this->db->select('lease_id, SUM(debit - credit) AS totalBalance');
        $this->db->from('transactions t');
        $this->db->where('t.account_id', $this->ar);
        $this->db->where('t.lease_id', $lease_id);
        if(!$bill_collectively){$this->db->where('t.profile_id', $profile_id);}
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
                return $this->getLastTrans($lease_id, $profile_id, $bill_collectively, $q->row()->totalBalance);
        } 
    }

    public function getLastTrans($lease_id, $profile_id, $bill_collectively, $totalBalance, $date = null, $startDate = null)
    {
        //(select * from your_table order by id desc limit 20) order by id; 
        if(!$bill_collectively) {$this->db->select('t.id, t.description, th.transaction_date, if(tt.name ="customer payments", "Payment-Thanks", if(tt.name ="charge", concat_ws("-","Charge",i.item_name), tt.name)) AS type, t.item_id, (t.debit - t.credit) AS amount');}
        if($bill_collectively) {$this->db->select('MAX(t.id) AS id, MAX(t.description) AS description, th.transaction_date, MAX(if(tt.name ="customer payments", concat_ws(" ","Payment-", first_name, last_name), if(tt.name ="charge", concat_ws("-","Charge",i.item_name), tt.name))) AS type, t.item_id, SUM(t.debit - t.credit) AS amount');}
        $this->db->from('transactions t');
        $this->db->join('transaction_header th', 't.trans_id = th.id');
        $this->db->join('transaction_type tt', 'th.transaction_type = tt.id');
        $this->db->join('profiles p', 't.profile_id = p.id','left');
        $this->db->join('items i', 't.item_id = i.id','left');
        $this->db->where('t.account_id', $this->ar);
        $this->db->where('t.lease_id', $lease_id);
        if($date){
            //$sqlDate = sqlDate($date);
            $sqlDate = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
            $this->db->where('th.transaction_date <=', $sqlDate);
            if($startDate){
                $sqlDate2 = date_create_from_format('m-d-Y', str_replace('/', '-', $startDate))->format('Y-m-d');
                $this->db->where('th.transaction_date >=', $sqlDate2);
            } 
        }
        if(!$bill_collectively){$this->db->where('t.profile_id', $profile_id);}
        if($bill_collectively){$this->db->group_by('t.item_id, th.transaction_date, if(th.transaction_type = 5, t.profile_id, 1)');}
        $this->db->order_by('th.transaction_date ASC');
        
        //need to fix front end formatting in order to allow more than 15
        //if(!$startDate){
            //$this->db->limit(15);
        //}

        $sql = $this->db->get_compiled_select();
        $this->db->reset_query();

        // select * from (
        //     select * from your_table order by id desc limit 20
        // ) tmp order by tmp.id asc
    //SELECT * FROM (
    //     SELECT (t.debit - t.credit) AS amount FROM `transactions` `t` WHERE `t`.`account_id` = 451 AND `t`.`lease_id` = 1 ORDER BY `id` desc LIMIT 20
    //     )lt ORDER BY `lt`.`id` desc  

        $this->db->select('*');
        $this->db->from('(' . $sql . ')lt');
        $this->db->order_by('lt.transaction_date ASC');
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $data = $q->result_array();
        }
            return $this->getBalanceInfo($totalBalance, $data);
        } 
    

    public function getBalanceInfo($totalBalance, $data)
    {
        $totalLast = array_sum(array_column($data, 'amount'));
        $beginningBalance = $totalBalance - $totalLast;
        $balance = $beginningBalance;
        foreach($data as &$datum){
            $balance += $datum['amount'];
            $datum['balance'] = $balance;
        }
        $info = new stdClass();
        $info->trans = $data;
        $info->total = $totalBalance;
        $info->beginningBalance = $beginningBalance;
        return $info;
    }

    public function getAllTenants()
    {
        $this->db->select('p.*, CONCAT_WS("",p.first_name," ",p.last_name) as name, GROUP_CONCAT(lp.lease_id) AS profile_leases');
        $this->db->from('profiles p');
        $this->db->join('leases_profiles lp', 'p.id = lp.profile_id');
        $this->db->join('profile_types pt', 'pt.id = p.profile_type_id');
        $this->db->where("p.profile_type_id",3);
        $this->db->group_by('p.id');
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
