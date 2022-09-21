<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('encryption_model');
    }

    public function getAccountsList()
    {
        $child =0;
        $topAccounts = $this->getAllAccounts2();
        $accounts = $this->getAllAccounts2($child);
        if (isset($topAccounts) && isset($accounts))
            return $this->buildTree($topAccounts, $accounts);
        else
            return $topAccounts;
    }
    public function buildTree($accounts, $accountChild)
        {
            $childs = array();
            foreach ($accountChild as &$item)
                $childs[$item->parent_id][] = &$item;
            unset($item);
            foreach ($accountChild as &$item) if (isset($childs[$item->id]))
            $item->children = $childs[$item->id];
            unset($item);
            foreach ($accounts as &$item) if (isset($childs[$item->id])){
                $item->children = $childs[ $item->id];
                $item->tree = $this->site->renderTree2($item);
            }
            return $accounts;
        }
    public function getAllAccounts2($params = null)//there is a getAllAccounts already on this page
    {
        $this->db->select('a.id, a.parent_id, a.name,at.shortname, at.name AS type, SUM(t.credit) AS credit, SUM(t.debit) AS debit, ac.debit_credit,1 AS balance,a.accno');
        $this->db->from('accounts a');
        $this->db->join('transactions t', 't.account_id = a.id', 'left outer');
        $this->db->join('account_types at', 'at.id = a.account_types_id', 'left');
        $this->db->join('account_category ac', 'ac.id = at.account_category_id', 'left');
        $this->db->where('a.active', 1);
        $this->db->ORDER_BY('at.name ASC');
        if(isset($params)){
            $this->db->where('a.parent_id !=', 0);
        }else{
            $this->db->where('a.parent_id', 0);
        }
        $this->db->group_by('a.id');

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($row->debit_credit === "credit") {
                    $row->balance = $row->credit - $row->debit;
                } elseif ($row->debit_credit === "debit") {
                    $row->balance = $row->debit - $row->credit;
                } else {
                    $row->balance = 0;
                }
                $data[] = $row;
            }
            return $data;
        }
    }
    //populate right side
    // public function getSingleAccountTransactions($id)
    // {
    //     $theId = 1;
    //     if($id){
    //         $theId = $id;
    //     }//june 7 modified at.name to th.transaction_type
    //     $this->db->select('t.id, t.item_id, th.transaction_type AS type, t.debit,t.description, th.id AS th_id , t.clr, a.name, a.accno, th.transaction_date, th.transaction_ref,tt.name AS name2, CONCAT(p.first_name," ", p.last_name) AS vendor, 1 AS balance');
        
    //     $this->db->from('transactions t');
    //     $this->db->join('accounts a', 'a.id = t.account_id');
    //     $this->db->join('transaction_header th', 'th.id = t.trans_id');
    //     $this->db->join('account_types at', 'at.id = a.account_types_id');
    //     $this->db->join('transaction_type tt', 'tt.id = th.transaction_type');
    //     $this->db->join('profiles p', 'p.id = t.profile_id');
    //     $this->db->where('t.account_id', $theId);
    //     $this->db->order_by('th.transaction_date DESC, t.trans_id DESC, t.id ASC');
    //     $q = $this->db->get();
    //     echo "<script>console.log('$amount this is the amount');</script>";
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             $amount= $this->getSingleAccountAmount($row->id);
    //             $row->balance = $amount;
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    // }
    // public function getSingleAccountAmount($id){
    //     $theId2 = 1;
    //     if($id){
    //         $theId2 = $id;
    //     }
    //     $this->db->select('t.id, t.credit, t.debit, ac.debit_credit,1 AS balance');
        
    //     $this->db->from('transactions t');
    //     $this->db->join('accounts a', 'a.id = t.account_id');
    //     $this->db->join('account_types at', 'at.id = a.account_types_id');
    //     $this->db->join('account_category ac', 'ac.id = at.account_category_id');
    //     $this->db->where('t.id', $theId2);

    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
                
    //             echo "<script>console.log('$row->debit this is a row');</script>";
    //             if ($row->debit_credit === "credit") {
    //                 $row->balance = $row->credit - $row->debit;
    //             } elseif ($row->debit_credit === "debit") {
    //                 $row->balance = $row->debit - $row->credit;
    //             } else {
    //                 $row->balance = 0;
    //             }
    //             $data = $row->balance;
    //         }
    //         return $data;
    //    }
    // } 

    public function getSingleAccountTransactions($id)
    {
        $theId = 1;
        if($id){
            $theId = $id;
        }
        $this->db->select('t.id, t.item_id, th.transaction_type AS type, t.debit, IF(TRIM(t.description) !="",t.description,th.memo)AS description, th.id AS th_id , t.clr, a.name, a.accno, th.transaction_date, th.transaction_ref,tt.name AS name2, CONCAT(p.first_name," ", p.last_name) AS vendor, 
        CASE 
        WHEN (ac.debit_credit = "credit") THEN t.credit - t.debit 
        WHEN (ac.debit_credit = "debit") THEN t.debit - t.credit 
        END AS balance,
        CASE 
        WHEN (ac.debit_credit = "credit") THEN  ((t.credit - t.debit) - IFNULL(transum.amounts,0))
        WHEN (ac.debit_credit = "debit") THEN ((t.debit - t.credit) - IFNULL(transum.amounts,0))
        END AS open_balance');
        $this->db->from('transactions t');
        $this->db->join('accounts a', 'a.id = t.account_id');
        $this->db->join('transaction_header th', 'th.id = t.trans_id');
        $this->db->join('account_types at', 'at.id = a.account_types_id');
        $this->db->join('account_category ac', 'ac.id = at.account_category_id');
        $this->db->join('transaction_type tt', 'tt.id = th.transaction_type');
        $this->db->join('profiles p', 'p.id = t.profile_id');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0 - amount AS amount
        FROM applied_payments
        UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        //$this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0');
        $this->db->where('t.account_id', $theId);
        //$this->db->where('a.active', 1);
        $this->db->order_by('th.transaction_date DESC, t.trans_id DESC, t.id ASC');
        $q = $this->db->get();
        //echo "<script>console.log('$amount this is the amount');</script>";
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                // $amount= $this->tryout($row->id);
                // $row->balance = $amount;
                $data[] = $row;
            }
            return $data;
        }
    }
    
    
    

    public function getSingleAccount($id)
    {
        $this->db->select('a.*, at.name AS type, at.special_table');
        $this->db->from('accounts a');
        $this->db->join('account_types at', ' a.account_types_id = at.id');
        $this->db->where('a.id', $id);
        //$this->db->where('a.active', 1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
        public function getAccountCC($id)
    {
        $q = $this->db->get_where('credit_cards', array('account_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getAccount($id)
    {
        //$this->db->where('active', 1);
        $q = $this->db->get_where('accounts', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getSpecialAccount($table, $id)
    {   
        $q = $this->db->get_where($table, array('account_id' => $id), 1);
        if ($q->num_rows() > 0) {
            $data = $q->row();
            $data = $this->encryption_model->decryptThis($data);
            return $data;
        }
        return false;
    
}

    public function getSpecialAccountName($id)
    {
        $this->db->select('name, special_table');
        $this->db->from('account_types');
        $this->db->where('id', $id);
        $q = $this->db->get();
      //if ($q->num_rows() > 0) {
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
        /*$this->db->select('name');
        $this->db->from('account_types');
        $q = $this->db->get();
        //if ($q->num_rows() > 0) {
          foreach(q->result() as $row){
            return $row->name;
          }
         
      //  }
            
        
        return null;
}*/


    public function getAllAccounts($addtype = false)
    {
        $this->db->where('active', 1);
        $q = $this->db->get('accounts');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'account';
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function addAccount($account, $specialAccount, $table, $propertyAccounts)
    {   
        $this->db->trans_start(); 
        $this->db->insert('accounts', $account);
        $aid = $this->db->insert_id();
        if($propertyAccounts !== null)
        {
        foreach ($propertyAccounts as &$propertyAccount) {
            $propertyAccount['account_id'] = $aid;
         }
         $this->db->insert_batch('property_accounts', $propertyAccounts);
        }

        if($specialAccount !== null) {

            $specialAccount = $this->encryption_model->encryptThis($specialAccount);
            $specialAccount['account_id'] = $aid;
            $this->db->insert($table, $specialAccount);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
{
        $error = 'error occurred';
        echo $error;
        return false;
}
        return true;
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

       

    function getParents($id)
    {
        $this->db->select('id, name, accno, parent_id');
        $this->db->from('accounts');
        $this->db->where('account_types_id', $id);
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

    function getAllParents()
    {
        $this->db->select('id, name, accno, parent_id');
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

    public function getPropertyAccounts($id)
    {
        $this->db->select('property_id');
        $this->db->from('property_accounts');
        $this->db->where('account_id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function editAccount($account, $specialAccount, $aid,  $table, $propertyAccounts)
    {   $this->db->trans_start();
        //$propertyAccounts == null ? $account['all_props'] = 1 : $account['all_props'] = 0;

        $this->db->update('accounts', $account, array('id' => $aid));
        
        if ($specialAccount !== null) {
            /*$this->db->where('account_id',$aid);
            $q = $this->db->get($table);

            if ( $q->num_rows() > 0 ) 
            {
                $this->db->where('account_id',$aid);
                $this->db->update($table,$specialAccount);
            } else {
                $this->db->set('account_id', $aid);
                $this->db->insert($table, $specialAccount);
            }*/

                        $specialAccount = $this->encryption_model->encryptThis($specialAccount);

                        $specialAccount['account_id'] = $aid;
                        $specialAccountArray = [];
                        foreach ($specialAccount as $key => $value)
                        {
                            $specialAccountArray[] = '`'. $key .'`= VALUES(`'.$key.'`)';
                        }
                        //return $specialAccountArray;
                        //$this->db->insert($table, $specialAccount);
                        //$this->db->update($table, $specialAccount, array('id' => $said ));
                        $sql = $this->db->insert_string($table, $specialAccount) . ' ON DUPLICATE KEY UPDATE ' .
                        implode(', ', $specialAccountArray);
                        $this->db->query($sql);
        }

        $this->db->delete('property_accounts', array('account_id' => $aid));  
        if($propertyAccounts !== null)
        {
        foreach ($propertyAccounts as &$propertyAccount) {
            $propertyAccount['account_id'] = $aid;
         }
         $this->db->insert_batch('property_accounts', $propertyAccounts);
        }
        $this->db->trans_complete();
    
    return true;
    }

    public function getVendorsList($addtype = false)
    {
        $this->db->select('p.*, CONCAT_WS(" ",p.first_name,p.last_name) as name');
        $this->db->from('profiles p');
        $this->db->where('profile_type_id', 1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getProfiles($aid = 0)
    {
        $this->db->select('id, CONCAT_WS(" ",first_name, last_name) AS name');
        $this->db->from('profiles');
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
