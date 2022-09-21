<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    public function addTransaction($header, $transactions)
    {   
        $myArray = [];
        if($transactions["amount"] > 0){ $transactions["credit"] = $transactions["amount"]; }
        else{
            $transactions["debit"] = $transactions["amount"];
            $transactions["debit"] = preg_replace("/[^0-9\.]/", "",$transactions["debit"]);
         }
        unset($transactions["amount"]);
        unset($transactions["userId"]);
        unset($transactions["transaction_date"]);

        $this->db->insert('transaction_header', $header);
        $last_id = $this->db->insert_id();
        
         $transactions['trans_id'] = $last_id;
         $this->db->insert('transactions', $transactions);

         if($transactions["credit"]){ $transactions["debit"] = $transactions["credit"];unset($transactions["credit"]); }
         else{ $transactions["credit"] = $transactions["debit"];unset($transactions["debit"]); }

         $transactions['account_id'] = 451;//important- this is account receivable.
         $this->db->insert('transactions', $transactions);
         //array_push($myArray,$transactions);

        // foreach ( $transactions as &$transaction ) {
        //     $transaction['trans_id'] = $last_id;
        //  }
         //array_push($myArray,$transactions);
         //$this->db->insert_batch('transactions', $transactions);
        //$this->db->insert_batch('transactions', $myArray);
        //$sql = $this->db->set($transactions)->get_compiled_insert('transactions');
        //echo $sql;

        return true;
    }

    public function editTransaction($header, $transactions, $id)
    {   
        $myArray = [];
        $this->load->model('transaction_snapshot_model');
        $this->transaction_snapshot_model->transaction_snapshot($id);
        if($transactions["amount"] > 0){ $transactions["credit"] = $transactions["amount"]; }
        else{
            $transactions["debit"] = $transactions["amount"];
            $transactions["debit"] = preg_replace("/[^0-9\.]/", "",$transactions["debit"]);
         }
        unset($transactions["amount"]);
        unset($transactions["userId"]);
        unset($transactions["transaction_date"]);

        $transactionToUpdate = $this->getSingleTransactions($id);
        
         $transactions['trans_id'] = $id;
         $this->db->update('transactions', $transactions, array('id' => $transactionToUpdate[0]->id));

         if($transactions["credit"]){ $transactions["debit"] = $transactions["credit"];unset($transactions["credit"]); }
         else{ $transactions["credit"] = $transactions["debit"];unset($transactions["debit"]); }

         $transactions['account_id'] = 451;//important- this is account receivable.
         $this->db->update('transactions', $transactions, array('id' => $transactionToUpdate[1]->id));


        return true;
    }

    public function getSingleTransactions($id)
    {   
        $q = $this->db->order_by('id', 'asc')->get_where('transactions', array('trans_id' => $id));

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = '6';
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getAllAccounts($addtype = false)
    {
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

    function getParents()
    {
        $this->db->select('parent, id, name, accno');
        $this->db->from('accounts');
        //$this->db->join('leases_profiles lp', 'p.id = lp.profile_id', 'left');
        //$this->db->join('units u', 'lp.unit_id = u.id', 'left');
        //$this->db->join('properties pr', 'pr.id = u.property_id', 'left');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
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

    public function editProperty($data, $units, $pid)
    {
        $this->db->update('properties', $data, array('id' => $pid));
        if (isset($units) && count($units) > 0)
            foreach ($units as &$unit) {
                $tid = '';
                $parent = '';
                if ($unit['parent_id'][0] == 't') {
                    $parent = $unit['parent_id'];
                    $unit['parent_id'] = 0;
                }
                if (isset($unit['tid'])) {
                    $tid = $unit['tid'];
                    unset($unit['tid']);
                }
                if (isset($unit['id'])) {
                    $uid = $unit['id'];
                    unset($unit['id']);
                    $this->db->update('units', $unit, array('id' => $uid));
                } else {
                    $unit['property_id'] = $pid;
                    $this->db->insert('units', $unit);
                    $uid = $this->db->insert_id();
                }
                if ($tid != '')
                    $updates[$tid]['id'] = $uid;
                if ($parent != '')
                    $updates[$parent]['children'][] = $uid;
            }
        if (isset($updates))
            foreach ($updates as $u) {
                if (isset($u['children']))
                    foreach ($u['children'] as $c) {
                        $this->db->update('units', array('parent_id' => $u['id']), array('id' => $c));
                    }
            }
        return true;
    }

    function checkPropertyEntityDate($pid){

                $this->db->select('e.closing_date');
                $this->db->from('entities e');
                $this->db->join('properties p', 'e.id = p.entity_id');
                $this->db->where('p.id', $pid);
                $q = $this->db->get();
                if ($q->num_rows() > 0) { 
                    return $q->row();
                }
                return null;
    }

    public function getTransactions()
    {
        $q = $this->db->get('transactions');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                //if ($addtype == true) $row->type = 'account';
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getTransaction()
    {
        $this->db->select('t.*');           
        $this->db->from('transactions t');
        $this->db->limit(1); 
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                // if ($addtype == true) $row->type = 'property';
                 $data[] = $row;
            }
            return $data;
            echo "<script>console.log('got to transactions');</script>";
        }
        return null;
    }

    public function searchTransactions($search_string) {
        $this->db->select('transactions.id, transactions.trans_id, transactions.description, 
        CASE 
        WHEN (ac.debit_credit = "credit") THEN transactions.credit - transactions.debit 
        WHEN (ac.debit_credit = "debit") THEN transactions.debit - transactions.credit 
        END AS amount, 
        accounts.name as account_name,transaction_header.transaction_date, transaction_type.name as transaction_type,  transaction_header.transaction_ref as ref, concat_ws(" ",first_name, last_name) as tname');
        $this->db->from('transactions');
        $this->db->join('accounts', 'accounts.id = transactions.account_id');
        $this->db->join('transaction_header', 'transactions.trans_id = transaction_header.id');
        $this->db->join('transaction_type', 'transaction_header.transaction_type = transaction_type.id');
        $this->db->join('profiles', 'transactions.profile_id = profiles.id', 'left');
        $this->db->join('account_types', 'account_types.id = accounts.account_types_id');
        $this->db->join('account_category ac', 'ac.id = account_types.account_category_id');
        $this->db->join('bills', 'transaction_header.id = bills.trans_id', 'left');
        if(PFLAG==00) {$this->db->where_in('transactions.property_id',explode( ',', trim(PROPERTIES, '()')));} 

        $this->db->like('CONCAT_WS("|", transactions.description, accounts.name, profiles.first_name,  profiles.last_name, transactions.debit,  transactions.credit,  transaction_type.name, transaction_header.transaction_ref,  DATE(transaction_header.transaction_date))', $search_string, 'both');

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                    $data[] = $row;
            }
            return $data;
        }
    }

    public function getAllTransactions()
    {
        //$this->db->select('t.*, t.id as tid, a.*, a.id as aid, a.name as aname, at.*, at.name as atname, at.id as atid, ac.*, ac.name as acname, ac.id as acid,th.*');
        //$this->db->select('t.id as tid, a.id as aid, a.name as aname, at.name as atname, at.id as atid, ac.*, ac.name as acname, ac.id as acid');
          $this->db->select('t.id as tid, a.id as aid, a.name as aname, at.name as atname, at.id as atid, ac.*, ac.name as acname,
           ac.id as acid, at.shortname, t.description, a.account_types_id, th.transaction_date,t.debit, t.credit, t.description, t.property_id, t.account_id,  1 AS balance');
        $this->db->from('transactions t');
        $this->db->join('accounts a', 'a.id = t.account_id');
        $this->db->join('account_types at', 'at.id = a.account_types_id');
        $this->db->join('account_category ac', 'ac.id = at.account_category_id');
        $this->db->join('transaction_header th', 'th.id = t.trans_id');

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                    // $amount= $this->getSingleAccountAmount($row->tid);
                    // $row->balance = $amount;
                    $data[] = $row;
            }
            return $data;
        }
    }

    public function getSingleAccountAmount($id){
            $theId2 = 1;
            if($id){
                $theId2 = $id;
            }
            $this->db->select('t.id, t.credit, t.debit, ac.debit_credit,1 AS balance');
            
            $this->db->from('transactions t');
            $this->db->join('accounts a', 'a.id = t.account_id');
            $this->db->join('account_types at', 'at.id = a.account_types_id');
            $this->db->join('account_category ac', 'ac.id = at.account_category_id');
            $this->db->where('t.id', $theId2);
    
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    
                    //echo "<script>console.log('$row->debit this is a row');</script>";
                    if ($row->debit_credit === "credit") {
                        $row->balance = $row->credit - $row->debit;
                    } elseif ($row->debit_credit === "debit") {
                        $row->balance = $row->debit - $row->credit;
                    } else {
                        $row->balance = 0;
                    }
                    $data = $row->balance;
                }
                return $data;
           }
        } 

    public function getAccountsTotal()
    {
        $this->db->select('a.id,SUM(t.credit) AS credit, SUM(t.debit) AS debit, ac.debit_credit,1 AS balance,a.accno,  ac.name as acname, at.name as atname, a.name as aname');
        $this->db->from('accounts a');
        $this->db->join('transactions t', 't.account_id = a.id');
        $this->db->join('account_types at', 'at.id = a.account_types_id');
        $this->db->join('account_category ac', 'ac.id = at.account_category_id');
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

    public function getPropertyId($id)
    {
        $this->db->select('u.property_id');           
        $this->db->from('units u');
        $this->db->where('u.id', $id); 
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->property_id;
        }
        return null;
    }

    function getSingleTransaction($id){
        $this->db->select('t.debit, t.credit, a.name, t.account_id, rec_id, clr');
        $this->db->from('transactions t');
        $this->db->join('accounts a', 'a.id = t.account_id');
        $this->db->where('t.id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;



        $q = $this->db->select('name')->from('transctions')->where('id', $id)->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {

            }
        }
    }

    function getCheckHeaderTransaction($id){
        $this->db->select('sum(t.debit) as debit, sum(t.credit) as credit, max(a.name) as name, t.account_id, max(rec_id) as rec_id, max(clr) as clr');
        $this->db->from('transactions t');
        $this->db->join('accounts a', 'a.id = t.account_id');
        $this->db->where('t.trans_id', $id);
        $this->db->where('t.line_number', null);
        $this->db->group_by('t.account_id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;



        $q = $this->db->select('name')->from('transctions')->where('id', $id)->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {

            }
        }
    }

    public function getTransactionsDTData2($itemId, $filter)
    {
        $this->db->select('t.id, t.profile_id, th.id AS th_id, t.lease_id,  th.transaction_type, tt.name AS type, th.transaction_date AS date, pr.name AS Property, CASE 
        WHEN (ac.debit_credit = "credit") THEN t.credit - t.debit 
        WHEN (ac.debit_credit = "debit") THEN t.debit - t.credit 
        END AS amount, CONCAT_WS("",p.first_name," ", p.last_name) AS name, IF(TRIM(th.transaction_ref)!="",th.transaction_ref,th.memo) AS reference, t.clr, t.description,  
        CASE 
        WHEN (ac.debit_credit = "credit") THEN  ((t.credit - t.debit) - IFNULL(transum.amounts,0))
        WHEN (ac.debit_credit = "debit") THEN ((t.debit - t.credit) - IFNULL(transum.amounts,0))
        END AS balance');
        $this->db->from('transactions t');
        $this->db->join('accounts a', 'a.id = t.account_id');
        $this->db->join('transaction_header th', 'th.id = t.trans_id');
        $this->db->join('account_types at', 'at.id = a.account_types_id');
        $this->db->join('account_category ac', 'ac.id = at.account_category_id');
        $this->db->join('transaction_type tt', 'tt.id = th.transaction_type','left');
        $this->db->join('profiles p', 'p.id = t.profile_id','left');
        $this->db->join('properties pr', 'pr.id = t.property_id', 'left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0 - amount AS amount
        FROM applied_payments
        UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        //$this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0');
        $this->db->where($filter, $itemId);
        $this->db->where('(CASE WHEN (ac.debit_credit = "credit") THEN (t.credit - t.debit) !=0 
        WHEN (ac.debit_credit = "debit") THEN (t.debit - t.credit) !=0 END)');
        // add to take out doubles but won't show all info unless you use the dropdown
        //$this->db->where_in('t.id',$min);
        $this->db->order_by('th.transaction_date DESC, t.trans_id DESC, t.id ASC');
        $q = $this->db->get();
        //echo "<script>console.log('$amount this is the amount');</script>";
        // if ($q->num_rows() > 0) {
        //     foreach (($q->result()) as &$row) {
        //     //    $amount= $this->getSingleAccountAmount($row->id);
        //     //         $row->balance = $amount;
        //          $info[] = $row;
        //     }
        //     //$data = new stdClass();
        //     $one = array_shift($info);
        //     $data['data'][0] = $one;
        //     //$object->data = $one;
        //     //$data[] = $one;
        //     $one->details = $info;
        //     //$array[] = $object;
        //     echo json_encode($data);
        // }

        // if ($q->num_rows() > 0) {
        //     foreach (($q->result()) as &$row) {
        //     //    $amount= $this->getSingleAccountAmount($row->id);
        //     //         $row->balance = $amount;
        //          $row->details = Array(
        //             (object)Array("type" => "type example3", "amount" => "$784", "name" => "John Doe"),
        //             (object)Array("type" => "type example4", "amount" => "$984", "name" => "Jane Doe"),
        //         );
        //          $info['data'][] = $row;
        //     }

        //     echo json_encode($info);
        // }

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                //$row->details = $this->getTransactionsDTDataDetails($row->th_id, $row->id);
                $info['data'][] = $row;
            }

            echo json_encode($info);
        } else {
            $data = Array("data" => Array());
            $data["data"][0] = (object)Array("type" => " ", "date" => " ", "amount" => " ",
                "name" => "No Transactions for this account", "reference" => " ", "description" => " ",
                "balance" => "$00.00",
                "details" => Array(
                    (object)Array("type" => " ", "amount" => " ", "name" => " "),

                )
            );

            echo json_encode($data);
        }
    }
}
