<?php defined('BASEPATH') OR exit('No direct script access allowed');

class JournalEntry_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addJournalEntry($header, $transactions)
    {   
        $this->db->trans_start();

        $trans_id = $this->addHeader($header, 1);

        $filled = $this->removeEmpty($transactions, $trans_id);
    
        
        if(!empty($filled))
        {
            $this->db->insert_batch('transactions', $filled);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }

        return $trans_id;
    }

    public function getHeader($id)
    {
        $this->db->select('th.id, th.transaction_type, CONCAT(DATE_FORMAT(th.last_mod_date, "%m/%d/%Y")," ",TIME_FORMAT(th.last_mod_date, "%r")) AS modified, th.last_mod_by, CONCAT_WS(" ",p.first_name,p.last_name) AS user,  th.transaction_ref, th.transaction_date AS date, th.memo, th.basis');
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
        // $this->db->select('*');
        // $this->db->from('transactions');
        // $this->db->where(array('trans_id' => $id));
        // $this->db->order_by("id", "asc");
        // $q = $this->db->get();
        $q = $this->db->order_by('line_number ASC', 'id ASC')->get_where('transactions', array('trans_id' => $id));
        //$q = $this->db->get_where('transactions', array('trans_id' => $id));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'transaction_type';
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }
    public function editJournalEntry($header, $transactions, $id, $deletes)
    {   
        $this->db->trans_start();

        $this->load->model('transaction_snapshot_model');
        $this->transaction_snapshot_model->transaction_snapshot($id);
       
        $this->updateHeader($header, $id);

        $filled = $this->removeEmptyEdit($transactions, $id);

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
            $this->db->update_batch('transactions', $updateArray, 'id');
         }
         if(!empty($insertArray))
         {
            $this->db->insert_batch('transactions', $insertArray);
         }

         $this->deleteLines($deletes);

         $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
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

    public function getAllAccounts()
    {
        $this->db->select('id, name, accno, all_props, parent_id');
        $this->db->from('accounts');
        $this->db->order_by('name');
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

    public function getNames()
    {
        $this->db->select('id, first_name, last_name, CONCAT_WS(" ",first_name, last_name) AS vendor');
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

    public function editProperty($data, $units, $pid)
    {
        $this->db->update('properties', $data, array('id' => $pid));
        if (isset($units) && (count($units) > 0))
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
}
