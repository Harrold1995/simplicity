<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Deposits_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->uf = $this->site->settings->undeposited_funds;
        $this->ar = $this->site->settings->accounts_receivable;
        $this->ap = $this->site->settings->accounts_payable;
    }

    function getTenants()
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

    public function getNames($aid = 0)
    {
        $this->db->select('id, def_expense_acc, CONCAT_WS(" ",first_name, last_name) AS name, LTRIM(CONCAT_WS(" ",first_name, last_name)) AS vendor');
        $this->db->from('profiles');
        if($aid == $this->ar){
            $this->db->where('profile_type_id', 3);
        }
        if($aid == $this->ap){
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

    public function getProperties()
    {
        $this->db->select('id, name');
        $this->db->from('properties');
        //$this->db->where('account_id', $id);
        $this->db->where('active', 1);
        if(PFLAG==00) {
            $this->db->where_in('properties.id',explode( ',', trim(PROPERTIES, '()')));
           } 
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

    
    public function getDepositTo()
    {
        $this->db->select('id, name, accno');
        $this->db->from('accounts');
        $this->db->where('account_types_id', 1);
        $this->db->where('active', 1);
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

    public function getAllAccounts()
    {
        $this->db->select('id, name, accno , all_props, parent_id');
        $this->db->from('accounts');
        $this->db->where('active', 1);
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
    
    public function getUndepositedFunds($properties = null)
    {
        $this->db->select('t.id, th.transaction_type as type_id, th.id as tid, th.transaction_ref, props.name AS property, props.id AS property_id, th.transaction_date AS date, th.memo, CONCAT_WS(" ",p.first_name,p.last_name) AS tenant, (t.debit - t.credit) AS amount');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->uf);
        $this->db->join('profiles p', 't.profile_id = p.id');
        $this->db->join('properties props', 't.property_id = props.id AND props.active = 1');
        $this->db->where('t.deposit_id', NULL);
        $this->db->where('t.debit >', 0);

        //$this->db->where('active', 1);
        if($properties){
          $this->db->where_in('t.property_id',$properties);// array
        }
        if(PFLAG==00) {$this->db->where_in('t.property_id',explode( ',', trim(PROPERTIES, '()')));} 
        
        
        // //$this->db->or_where('t.profile_id',$pid);
        //(array('archived' => NULL));
        $this->db->ORDER_BY('th.transaction_date ASC');
        // $sql = $this->db->get_compiled_select();
        // echo $sql;
        // return;
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
       
    }
    public function getHeaderEdit($th_id)
    {
        $this->db->select('th.id, th.memo, th.transaction_date AS date, CONCAT(DATE_FORMAT(th.last_mod_date, "%m/%d/%Y")," ",TIME_FORMAT(th.last_mod_date, "%r")) AS modified, th.last_mod_by, CONCAT_WS(" ",p.first_name,p.last_name) AS user');
        $this->db->from('transaction_header th');
        $this->db->join('users u', 'th.last_mod_by = u.id','left'); 
        $this->db->join('profiles p', 'u.profile_id = p.id','left'); 
        $this->db->where('th.id', $th_id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;

    }

    public function getAccount($th_id)
    {   
        $this->db->select('account_id');
        $this->db->from('transactions'); 
        $this->db->where(array('trans_id'=>$th_id, 'account_id !=' => $this->uf ));
        $this->db->limit(1);
        $this->db->order_by('id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->account_id;
        }
    }
    public function getProperty($th_id)
    {   
        $this->db->select('property_id');
        $this->db->from('transactions'); 
        $this->db->where(array('trans_id'=>$th_id, 'account_id !=' => $this->uf));
        $this->db->limit(1);
        $this->db->order_by('id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->property_id;
        }
    }
    public function getDepositedEdit($th_id)
    {   
        $this->db->select('id, property_id');
        $this->db->from('transactions'); 
        $this->db->where(array('trans_id'=>$th_id, 'account_id' => $this->uf));
        $transactionInfo = $this->db->get();
        //if ($q->num_rows() > 0) {
            $tid = $transactionInfo->row()->id;
            //$pid = $transactionInfo->row()->property_id;
            $this->db->reset_query();
        //}
            //return null;
         // get all from undeposited funds that don't have deposit_id or have this deposit id
         $this->db->select('t.id, th.transaction_type as type_id, th.id as tid, t.deposit_id, th.transaction_ref, props.name AS property, props.id AS property_id, th.transaction_date AS date, th.memo, CONCAT_WS(" ",p.first_name,p.last_name) AS tenant, (t.debit - t.credit)  AS amount');
         $this->db->from('transactions t'); 
         $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->uf);
         $this->db->join('profiles p', 't.profile_id = p.id');
         $this->db->join('properties props', 't.property_id = props.id');// AND props.id =' . $pid
         $this->db->where('t.deposit_id', NULL); 
         $this->db->where('t.debit >', 0);
         if(PFLAG==00) {
            $this->db->where_in('props.id',explode( ',', trim(PROPERTIES, '()')));
           }     
         $this->db->or_where('t.deposit_id', $tid);
         if(PFLAG==00) {
             $this->db->where_in('props.id',explode( ',', trim(PROPERTIES, '()')));
            } 
        //  if($properties){
        //  $this->db->where_in('t.property_id',$properties);// array
        //  }
        
        //  $unaccounted = $this->db->get_compiled_select();
        //  $this->db->reset_query();

        // //get all undeposited funds that were allocated to accounts in this transaction(th.id = t.trans_id)
        //  $this->db->select('t.id, th.transaction_ref, props.name AS property th.transaction_date AS date, th.memo, CONCAT_WS(" ",p.first_name,p.last_name) AS tenant, t.debit  AS amount');
        //  $this->db->from('transactions t'); 
        //  $this->db->join('transaction_header th', 't.trans_id = th.id AND t.trans_id = ' . $th_id . 'AND t.account_id = $this->uf');
        //  $this->db->join('profiles p', 't.profile_id = p.id');
        //  $this->db->join('properties props', 't.property_id = props.id');
        // // $sql = $this->db->get_compiled_select();
        // // echo $sql;
        // // return;
        // $accountedThisTransaction = $this->db->get_compiled_select();
        // $this->db->reset_query();
        $q = $this->db->get();
        //$q = $this->db->query("$unaccounted UNION $accountedThisTransaction");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
       
    }

    public function getOtherDepositsEdit($th_id)
    {   
         $this->db->select('t.id, t.account_id,  t.property_id,  t.unit_id, t.description, t.profile_id, (t.credit-t.debit)  AS amount,t.class_id, t.deposit_id');
         $this->db->from('transactions t'); 
         $this->db->where('trans_id', $th_id);
         $this->db->where('line_number !=', 0); 
         //$this->db->join('transaction_header th', 't.trans_id = th.id AND t.trans_id = ' . $th_id);
        // $this->db->join('profiles p', 't.profile_id = p.id', "left");
         //$this->db->join('properties props', 't.property_id = props.id');
         $this->db->order_by('line_number ASC', 'id ASC');
         $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
           //$data = array_slice($data, 2);
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

    public function getPropertyUnits($pid, $addtype = false)
    {   
        $this->db->select('id, name, parent_id');
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
    public function depositPayments($header, $balance_undeposited_funds, $deposit_to, $undeposited, $otherDeposits)//$deletes will probably need
    {   
        //if($totalAmount > 0){
            $this->db->trans_start();
            unset($header['id']);
            $trans_id = $this->addHeader($header, 8);
            $balance_undeposited_funds['trans_id'] = $trans_id;
            $this->db->insert('transactions', $balance_undeposited_funds);
            $deposit_id = $this->db->insert_id();
            $deposit_to['trans_id'] = $trans_id;
            $this->db->insert('transactions', $deposit_to);
        
            //////decide how to receive undeposited funds WIP WIP

            // $depositing = array_filter($undeposited, function($v) {
            //     return $v == 1;
            //     });
        
            // $notDepositing = array_filter($undeposited, function($v) {
            //         return $v == 0;
            //         });
            // if (!empty($depositing)) {
            //     $this->db->where_in('id', array_keys($depositing));
            //     $this->db->update('transactions', array('deposit_id' => $deposit_id));
            // };

            if (!empty($undeposited)) {
                $this->db->where_in('trans_id', array_keys($undeposited));
                $this->db->where('account_id',  $this->uf);
                $this->db->update('transactions', array('deposit_id' => $deposit_id));
            };

            //////depends how to receiving data  WIP WIP don't know if need anymore remove empty
            $filled = $this->removeEmpty($otherDeposits, $trans_id);
            //will probably need editDetails
            //$this->editDetails($filled);
            $this->addDetails($filled);

            //will probably need this
            //if($deletes)$this->deleteLines($deletes);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {
                return false;
            }
       // }
       return true;
        
    }
//     public function deleteOld($ids){
//         $this->db->trans_start();

// //         $this->load->model('transaction_snapshot_model');
// //         $this->transaction_snapshot_model->transaction_snapshot($trans_id);
        

// //     $header = $this->db->get_where('transaction_header', array('id' => $trans_id));
// //     if ($header->num_rows() > 0) {
// //         $headerData = $header->row();
// //         $headerData->transaction_header_id = $trans_id;
// //         $headerData->memo = 'DELETED (AND REINSERTED  with other ID? ID ' . $trans_id . ' MAY HAVE BEEN ASSIGNED TO ANOTHER DEPOSIT)';
// //         unset($headerData->id);
// //         $this->db->insert('transaction_header_snapshot', $headerData);
// //         $last_id = $this->db->insert_id();
// //         $this->db->reset_query();
// // }
// //         $this->db->select('*');
// //         $this->db->from('transactions');
// //         $this->db->where('trans_id', $trans_id);
// //         $this->db->limit(1);
// //         $transaction = $this->db->get();
    

// //     // inserting into transactions_snapshot
// //     if ($transaction->num_rows() > 0) {
// //             $transaction = $transaction->row();
// //             $transaction->transaction_id = $transaction->id;
// //             $transaction->trans_snapshot_id = $last_id;
// //             $transaction->description = 'DELETED (AND REINSERTED  with other trans_id? trans_id ' . $trans_id . ' MAY HAVE BEEN ASSIGNED TO ANOTHER DEPOSIT)';
// //             unset($transaction->id);
            
// //             $this->db->insert('transactions_snapshot', $transaction);
            
// //     }

//         $ids = implode(",",$ids);
//         $sql ='DELETE t, ap
//         FROM transactions t
//         LEFT JOIN applied_payments ap ON t.id = ap.transaction_id_a OR t.id = ap.transaction_id_b
//         WHERE t.id IN(' . $ids . ') '; //for sql injection
//         $this->db->query($sql);

//         // $this->db->trans_start();

//         // $this->db->delete('transactions', array('trans_id' => $trans_id, 'property_id !=' => $property_id));
       
//         $this->db->trans_complete();

//         if ($this->db->trans_status() === FALSE)
//         {
//             return false;
//         }
//             return true;
//     }

    // public function findDeletes($trans_id, $property_id, $deposit_id, $deposit_to_id){

    //     $this->db->select('t.id AS id');
    //     $this->db->from('transactions t');
    //     //$this->db->join('applied_payments ap', 't.id = ap.transaction_id_a OR t.id = ap.transaction_id_b', 'left');
    //     $this->db->where('t.id NOT IN(' . $deposit_id .',' . $deposit_to_id . ') AND t.trans_id =' . $trans_id . ' AND t.property_id !=' . $property_id);
    //     // echo $this->db->get_compiled_select();
    //     // return;
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         $ids = array_column($q->result_array(), 'id'); 
    //         $ids = implode(",",$ids);
    //          return $ids;
    //     }
    //     return false;
    // }

    public function editDeposits($header, $balance_undeposited_funds, $deposit_to, $undeposited, $otherDeposits, $deposit_id, $deposit_to_id, $deletes)//, $check
    {   
        $this->db->trans_start();

        $this->load->model('transaction_snapshot_model');
        $this->transaction_snapshot_model->transaction_snapshot($header['id']);
        $this->updateHeader($header, $header['id']);
        $trans_id = $header['id'];


        $balance_undeposited_funds['trans_id'] = $trans_id;
        $this->db->update('transactions', $balance_undeposited_funds, 'id =' . $deposit_id);
        $deposit_to['trans_id'] = $trans_id;
        $this->db->update('transactions', $deposit_to, 'id =' . $deposit_to_id);
        

        
        //always set all deposit ids to null this way eliminating the issues with checked unchecked don't need some code after this
        $this->db->where('deposit_id', $deposit_id);
        $this->db->update('transactions', array('deposit_id' => NULL));
    
        // $notDepositing = array_filter($check, function($v) {
        //         return $v == 1;
        //         });
        
        
        if (!empty($undeposited)) {
            $this->db->where_in('trans_id', array_keys($undeposited));
            $this->db->where('account_id',  $this->uf);
            $this->db->update('transactions', array('deposit_id' => $deposit_id));
        };


        // if (!empty($notDepositing)) {
        //     $this->db->where_in('id', array_keys($notDepositing));
        //     $this->db->update('transactions', array('deposit_id' => NULL));
        // };

        
            
       

        $filled = $this->removeEmptyEdit($otherDeposits, $header['id']);
        $this->editDetails($filled);

        if($deletes)$this->deleteLines($deletes);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        
        return true;
}
}