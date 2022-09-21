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
    {   $pfilter =  PFLAG==00 ?"AND (b.id is null) or (b.property IN".PROPERTIES.")":"";
        $q = $this->db->query("SELECT a.id, a.parent_id, a.active, a.name, at.shortname, at.name AS type, IFNULL(credit, 0) AS credit, IFNULL(debit, 0) as debit, ac.debit_credit, ifnull(if(debit_credit = 'credit', (credit - debit), debit - credit), 0) AS balance, a.accno 
            FROM accounts a 
            LEFT JOIN (SELECT sum(t.debit) as debit, sum(t.credit) as credit, account_id FROM transactions t GROUP BY t.account_id) t ON t.account_id = a.id 
            LEFT JOIN account_types at ON at.id = a.account_types_id 
            LEFT JOIN banks b ON a.id = b.account_id 
            LEFT JOIN account_category ac ON ac.id = at.account_category_id 
            WHERE a.parent_id ".(isset($params)?'!':'')."= 0 ".$pfilter.  
            "ORDER BY at.name ASC");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                //if ($row->debit_credit === "credit") {
                    //$row->balance = $row->credit - $row->debit;
                //} elseif ($row->debit_credit === "debit") {
                    //$row->balance = $row->debit - $row->credit;
                //} else {
                    //$row->balance = 0;
                //}
                $row->info2 = '$'.number_format($row->balance, 2);
                $row->info = $row->shortname;
                $row->type = 'account';
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
            //add bank name for fast select (clumsy.. maybe ther's a better way)
            if($table == 'banks'){
                $q2 = $this->db->get_where('properties', array('id' => $data->property), 1);
                if ($q2->num_rows() > 0) {
                    $data2 = $q2->row();
                    $data->prop_name = $data2->name;
                }

            }
            if(isset($data->custom)){
               $finins = json_decode($data->custom);
               if(isset( $finins->ins_id)){
                $q2 = $this->db->get_where('fin_ins', array('ins_id' =>$finins->ins_id), 1);
                if ($q2->num_rows() > 0) {
                    $data->finins = json_decode($q2->row()->custom)->institution;
                    $data->finins->account_id = $finins->plaid_acct;
                }

                /* $q3 = $this->db->get_where('plaid_trans', array('account_id' =>  'Jz6wQ5gBRbFRJvEr95WjHyy8BqRqMpHB94ZKb'));
                if ($q3->num_rows() > 0) {
                    if ($q3->num_rows() > 0) {
                        
                        foreach (($q3->result()) as &$row) {
                            // $amount= $this->tryout($row->id);
                            // $row->balance = $amount;
                            $data2[] = $row;
                        }
                        $object = (object) $data2;
                        $data->bankTrans = $object;
                    }
                   
                } */
               }
            }
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

    public function searchAccounts($search_string)
    {   
        $where = "(b.id is null) or (b.property IN".PROPERTIES.")";

        $this->db->select('a.*');
        $this->db->from('accounts a');
        $this->db->join('banks b', 'a.id = b.account_id', 'left');
        $this->db->where('active', 1);
        $this->db->group_start();
            $this->db->or_like([
                'a.name' => $search_string,
                'a.description' => $search_string,
                'a.accno' => $search_string
            ]);
        $this->db->group_end();
        if (PFLAG==00){
            $this->db->where($where, NULL, FALSE);
        }

        $this->db->order_by('a.id');

        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) {
                    $row->type = 'tenant';
                }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

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
                //echo $error;
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
                            $specialAccountArray[] = ''. $key .'= VALUES('.$key.')';
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

        if ($this->db->trans_status() === FALSE)
        {
                $error = 'error occurred';
                //echo $error;
                return false;
        }
    
    return true;
    }


    public function getVendorsList($addtype = false)
    {
        $this->db->select('p.*, LTRIM(CONCAT_WS(" ",p.first_name,p.last_name)) as name');
        $this->db->from('profiles p');
        $this->db->where('profile_type_id', 1);
        $this->db->ORDER_BY('name');
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
        $this->db->select('id, LTRIM(CONCAT_WS(" ",first_name, last_name)) AS name');
        $this->db->from('profiles');
        $this->db->ORDER_BY('name');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }
    //for bar graph
    public function getSumTransactions($account_id)
    {
            // 'SELECT DATE_FORMAT(th.transaction_date,"%b") AS Month,YEAR(th.transaction_date),  CASE 
            // WHEN (ac.debit_credit = "credit") THEN  SUM(t.credit - t.debit)
            // WHEN (ac.debit_credit = "debit") THEN SUM(t.debit - t.credit) 
            // END AS balance 
            // FROM transaction_header th
            // JOIN transactions t ON  th.id = t.trans_id 
            // JOIN accounts a ON t.account_id = a.id
            // JOIN account_types at ON a.account_types_id = at.id
            // JOIN account_category ac ON at.account_category_id = ac.id
            // WHERE th.transaction_date BETWEEN DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),"%Y-%m-01") AND LAST_DAY(DATE_SUB(CURDATE(),INTERVAL 1 MONTH)) 
            // GROUP BY YEAR(th.transaction_date), DATE_FORMAT(transaction_date,"%b"), DATE_FORMAT(transaction_date,"%m"),t.account_id
            // ORDER BY YEAR(th.transaction_date) DESC,  DATE_FORMAT(transaction_date,"%m")  DESC';

           $months =  '(SELECT 
                            DATE_FORMAT(m1, "%b") AS month
                            FROM
                            (
                            SELECT 
                            DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),"%Y-%m-01")
                            +INTERVAL m MONTH as m1
                            FROM
                            (
                            SELECT @rownum:=@rownum+1 as m FROM
                            (SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4) t1,
                            (SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4) t2,
                            (SELECT @rownum:=-1) t0
                            ) d1
                            ) d2 
                            where m1<=LAST_DAY(DATE_SUB(CURDATE(),INTERVAL 1 MONTH))
                            order by m1)m';
        $this->db->select('DATE_FORMAT(th.transaction_date,"%b") AS month,YEAR(th.transaction_date),
            CASE 
                WHEN (ac.debit_credit = "credit") THEN  SUM(t.credit - t.debit)
                WHEN (ac.debit_credit = "debit") THEN SUM(t.debit - t.credit)
            END AS balance ');
        $this->db->from('transaction_header th');
        $this->db->join('transactions t', 'th.id = t.trans_id');
        $this->db->join('accounts a', 't.account_id = a.id AND a.id =' . $account_id);
        $this->db->join('account_types at', 'a.account_types_id = at.id');
        $this->db->join('account_category ac', 'at.account_category_id = ac.id');
        // $this->db->join('(' . $months . ')months', 'th.DATE_FORMAT(th.transaction_date,"%b") = months.month','left');
        $this->db->where('th.transaction_date BETWEEN DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),"%Y-%m-01") AND LAST_DAY(DATE_SUB(CURDATE(),INTERVAL 1 MONTH))');
        $this->db->group_by('YEAR(th.transaction_date), DATE_FORMAT(transaction_date,"%b"), DATE_FORMAT(transaction_date,"%m"), t.account_id');
        $this->db->order_by('YEAR(th.transaction_date) DESC, DATE_FORMAT(transaction_date,"%m") DESC');
        $totals = $this->db->get_compiled_select();
        $totals = '('.$totals.')t';
        $this->db->reset_query();

        $this->db->select('m.month, IF(t.month IS NOT NULL, t.balance, 0) AS balance');
        $this->db->from($months);
        $this->db->join($totals,'m.month = t.month','left');
        $q = $this->db->get();


        if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }              
                 return $data;
        }
    }

    function getBankName($routingNumber){

        $q = $this->db->select('bank_name')->from('bank_routing_numbers')->where('routing_number', $routingNumber)->get();
        if ($q->num_rows() > 0) {
            $data = $q->result();
            return $data[0]->bank_name;
        }
        return false;
    }

    public function getAccounts()
    {
        $this->db->select('id, name, accno');
        $this->db->from('accounts');
        //$this->db->where('account_types_id', 1);
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

    function maxId(){
        $this->db->select_max('accno');
        $query = $this->db->get('accounts');
        if ($query->num_rows() > 0) {
            $data = $query->result();
            $data = $data[0]->accno + 1;
            return $data;
        }
        return null;
    }

    public function checkFinIns($id)
    {
        //$this->db->where('active', 1);
        $q = $this->db->get_where('fin_ins', array('ins_id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPlaidBanks()
    {
        $this->db->select('fi.*');
        $this->db->from('fin_ins fi');
        //$this->db->where('profile_type_id', 1);
        //$this->db->ORDER_BY('name');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->custom = json_decode($row->custom);
                unset($row->access_token);
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    function getToken($ins_id){
        $this->db->select('access_token');
        $this->db->from('fin_ins');
        $this->db->where('ins_id', $ins_id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data = $row->access_token;
            }
            return $data;
        }
        return null;

    }

    function getCursor($ins_id){
        return $this->db->get_where('fin_ins', array('ins_id' => $ins_id))->row()->next_cursor;

    }

    function getInsInfo($ins_id){
        $this->db->select('*');
        $this->db->from('fin_ins');
        $this->db->where('ins_id', $ins_id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $row->custom = json_decode($row->custom);
                $data = $row;
            }
            return $data;
        }
        return null;

    }


    function get_trans_plaid($ins_id){

        $access_token = $this->getToken($ins_id);
        $next_cursor = $this->getCursor($ins_id);
        $response = $this->get_plaid_transactions($access_token, $next_cursor);
        
        $response = json_decode($response);

        
        if (!empty($response->added)) { $this->process_plaid_added_trans($response->added);}       
        if (!empty($response->removed)) {  $this->process_plaid_removed_trans($response->removed);}
        if (!empty($response->modified)) {$this->process_plaid_updated_trans($response->modified);}
        
        
        $q1 = $this->db->update('fin_ins', array('next_cursor' => $response->next_cursor), array('ins_id' => $ins_id));
    
        //if there was more than 500 new transaction pull again
        if($response->has_more == true){
            $this->get_trans_plaid($ins_id);
        } 
        
    }

    function get_plaid_transactions($access_token, $cursor)
       {

        $plaid_env = 'development';
        $plaid_client_id = "5e5d829fd6e09e0012bc175c";
        $plaid_public = "a55921b437e68bd01c9d8da602f2ce";
        $plaid_secret = "4c95a91e4e1b0a393b7ed4d0389089";
        $plaid_url = "https://development.plaid.com";

           //global $plaid_client_id, $plaid_secret, $plaid_url;
           $data = array(
               "client_id" => $plaid_client_id,
               "secret" => $plaid_secret,
               "access_token" => $access_token,
               "count" => 500 
               //"start_date" => '2020-10-10',
               //"end_date" => '2022-12-10',
               //"options" => array( "account_ids" => array('bnK3QbRoooFoBwQBx71MCM5VgQv9nWiVZ6pxM')) 
               
           );
           if($cursor != null){$data['cursor'] = $cursor;}
   
           $data_fields = json_encode($data);        
   
           //initialize session
           $ch=curl_init($plaid_url . "/transactions/sync");
   
           //set options
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fields);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
             'Content-Type: application/json',                                                                                
             'Content-Length: ' . strlen($data_fields))                                                                       
           );   
   
           //execute session
           $trans_json = curl_exec($ch);
           //$exchange_token = json_decode($token_json,true);          
           //close session
           curl_close($ch); 

           return  $trans_json;
       }


       function process_plaid_added_trans($transactions){
        $data = array();
            $update='';
            $separator='';
            $separator1='';
            foreach ($transactions as $transaction){
                $update.=$separator1."(";

                //$info = array(
                    //'balances' => $transaction->balances,
                    //'mask' => $transaction->mask,
                    
               // );

                //$info = json_encode($info);

                
                
                $trans1 = array (
                    'account_id' => addslashes($transaction->account_id) ,
                    'account_owner' => addslashes($transaction->account_owner),
                    'amount' => addslashes($transaction->amount),
                    'authorized_date' => addslashes($transaction->authorized_date),
                    'check_number' => addslashes($transaction->check_number),
                    'date' => addslashes($transaction->date),
                    //'location' => $transaction->location,
                    'merchant_name' => addslashes($transaction->merchant_name),
                    'name' => addslashes($transaction->name),
                    //'payment_meta' => $transaction->payment_meta,
                    'pending' => addslashes($transaction->pending),
                    'pending_transaction_id' => addslashes($transaction->pending_transaction_id),
                    'transaction_id' => addslashes($transaction->transaction_id)

                   

                );
                
                foreach ($trans1 as $key => $value) {
                    $update.=$separator." '$value' ";
                    $separator=','; 
                    
                
                }

                $update.=")";
                $separator=' '; 
                $separator1=','; 

                
            }
            
            $update = 'insert into plaid_trans (account_id, account_owner, amount, authorized_date, check_number,date, merchant_name, name, pending, pending_transaction_id, transaction_id) values'.$update.' ON DUPLICATE KEY UPDATE pending = VALUES(pending)';
            $q = $this->db->query($update);
    }

    function process_plaid_removed_trans($removed){
        $removed  = (array) $removed;
        $list =  array_map(function($remove) {
            return $remove->transaction_id;
        }, $removed);

        $this->db->where_in('transaction_id', $list);
        $this->db->update('plaid_trans', array('removed' => 1));


    }
    
    function process_plaid_updated_trans($modified){

        //todo need to add additional info this is just the basics
        foreach ($modified as $trans) {
            $this->db->update('plaid_trans', array('amount' => $trans->amount, 'date' => $trans->date, 'name' => $trans->name), array('transaction_id' => $trans->transaction_id));  
        }
    }


    /* function get_trans_plaid($ins_id){
        //$ins_id = 'ins_3';
        $access_token = $this->getToken($ins_id);
        $transactions = $this->get_plaid_transactions($access_token);
        
        

            $transactions = json_decode($transactions);
            $transactions = $transactions ->transactions;
            $data = array();
            $update='';
            $separator='';
            $separator1='';
            foreach ($transactions as $transaction){
                $update.=$separator1."(";

                //$info = array(
                    //'balances' => $transaction->balances,
                    //'mask' => $transaction->mask,
                    
               // );

                //$info = json_encode($info);

                
                
                $trans1 = array (
                    'account_id' => addslashes($transaction->account_id) ,
                    'account_owner' => addslashes($transaction->account_owner),
                    'amount' => addslashes($transaction->amount),
                    'authorized_date' => addslashes($transaction->authorized_date),
                    'check_number' => addslashes($transaction->check_number),
                    'date' => addslashes($transaction->date),
                    //'location' => $transaction->location,
                    'merchant_name' => addslashes($transaction->merchant_name),
                    'name' => addslashes($transaction->name),
                    //'payment_meta' => $transaction->payment_meta,
                    'pending' => addslashes($transaction->pending),
                    'pending_transaction_id' => addslashes($transaction->pending_transaction_id),
                    'transaction_id' => addslashes($transaction->transaction_id)

                   

                );
                
                foreach ($trans1 as $key => $value) {
                    $update.=$separator." '$value' ";
                    $separator=','; 
                    
                
                }

                $update.=")";
                $separator=' '; 
                $separator1=','; 

                
            }
            
            $update = 'insert into plaid_trans (account_id, account_owner, amount, authorized_date, check_number,date, merchant_name, name, pending, pending_transaction_id, transaction_id) values'.$update.' ON DUPLICATE KEY UPDATE pending = VALUES(pending)';
            $q = $this->db->query($update);

            $this->db->select('*');
            $this->db->from('plaid_trans');
           
            //$this->db->where('ins_id', $ins_id);
            

            $q2 = $this->db->get();
            if ($q2->num_rows() > 0) {
                
                foreach (($q2->result()) as &$row) {
                    $row->info = json_decode($row->info);
                }
                echo json_encode($q2->result());
            }
            

           
        

        
        
    }

    function get_plaid_transactions($access_token)
       {

        $plaid_env = 'development';
        $plaid_client_id = "5e5d829fd6e09e0012bc175c";
        $plaid_public = "a55921b437e68bd01c9d8da602f2ce";
        $plaid_secret = "4c95a91e4e1b0a393b7ed4d0389089";
        $plaid_url = "https://development.plaid.com";

           //global $plaid_client_id, $plaid_secret, $plaid_url;
           $data = array(
               "client_id" => $plaid_client_id,
               "secret" => $plaid_secret,
               "access_token" => $access_token, 
               "start_date" => '2020-10-10',
               "end_date" => '2021-12-10',
               //"options" => array( "account_ids" => array('bnK3QbRoooFoBwQBx71MCM5VgQv9nWiVZ6pxM')) 
               
           );
   
           $data_fields = json_encode($data);        
   
           //initialize session
           $ch=curl_init($plaid_url . "/transactions/get");
   
           //set options
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fields);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
             'Content-Type: application/json',                                                                                
             'Content-Length: ' . strlen($data_fields))                                                                       
           );   
   
           //execute session
           $trans_json = curl_exec($ch);
           //$exchange_token = json_decode($token_json,true);          
           //close session
           curl_close($ch); 

           return  $trans_json;
       } */

    


    
}
