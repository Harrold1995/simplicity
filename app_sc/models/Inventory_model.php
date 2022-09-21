<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    function getInventoryList($itemTypes)
    {
        $child =0;
        $topItems = $this->getAllItems($itemTypes);
        $items = $this->getAllItems($itemTypes, $child);
        if (isset($topItems) && isset($items))
            return $this->buildTree($topItems, $items);
        else
            return $topItems;
    } 
    function getAllItems($itemTypes, $params = null){
        $this->db->select('i.*, i.item_name AS name');
        $this->db->from('items i');
        if(isset($params)){
            $this->db->where('i.parent_id !=', 0);
        }else{
            $this->db->where('i.parent_id', 0);
        }
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                foreach($itemTypes  as $k => $itype){
                    
                    $row->info = $row->sales_description;
                }
                $row->type = 'inventory';
                $data[] = $row;
            }
            return $data;
        }
    }
    //*/

    /*function getSingleInventory($id)
    {
        $theId = 1;
        if($id){
            $theId = $id;
        }
        echo "<script>console.log('$theId');</script>";
         $this->db->select('i.id, i.item_name AS name, i.type');
        
        $this->db->from('items i');
        $this->db->where('i.id', $theId);
        $q = $this->db->get();
        $data  = $q->result();
            echo "<script>console.log('the data is $data');</script>";
            return $data;
        
    }*/

        public function getSingleInventory($id)
    {
        $q = $this->db->get_where('items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            $result = $q->row();
            $parent = $this->getParent($result->parent_id);
            $result->parent = $parent->item_name;
            return $result; //$q->row();
        }
        return false;
    }

    public function editInventory($data)
    {
        $Iid = $data['id'];
        unset($data['id']);
        if ($this->db->update('items', $data, array('id' => $Iid)))
            return true;
    }

    public function addInventory($data)
    {
        $this->db->insert('items', $data);
        return true;
    }

    public function getSingleInventoryTransactions($id)
    {
        $theId = 1;
        if($id){
            $theId = $id;
        }//june 7 modified at.name to th.transaction_type
        $this->db->select('t.id, t.item_id, th.transaction_type AS type, t.debit,t.description, th.id AS th_id , t.clr, a.name, a.accno, th.transaction_date, th.transaction_ref,tt.name AS name2, CONCAT(p.first_name," ", p.last_name) AS vendor, 1 AS balance');
        
        $this->db->from('transactions t');
        $this->db->join('accounts a', 'a.id = t.account_id');
        $this->db->join('transaction_header th', 'th.id = t.trans_id');
        $this->db->join('account_types at', 'at.id = a.account_types_id');
        $this->db->join('transaction_type tt', 'tt.id = th.transaction_type');
        $this->db->join('profiles p', 'p.id = t.profile_id');
        $this->db->where('t.item_id', $theId);
        $this->db->order_by('th.transaction_date DESC, t.trans_id DESC, t.id ASC');
        $q = $this->db->get();
       
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $amount=0; //$this->getSingleInventoryAmount($row->id);
                $row->balance = $amount;
                //$amount= $this->getSingleInventoryAmount($row->id);
                //$row->balance = $amount;
                $data[] = $row;
            }
            return $data;
        }
    }
    public function getSingleInventoryAmount($id){
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

    public function getParent($id)
    {
        $q = $this->db->get_where('items', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    function getParentsList($params = null)
    {
        $this->db->select('i.*, i.item_name AS name');
        $this->db->from('items i');
        if(isset($params)){
            $this->db->where('i.type ', $params->type);
            $this->db->where('i.id !=', $params->id);
        }
        //$this->db->where('i.parent_id', 0);

        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function buildTree($items, $itemChild)
    {
        $childs = array();
        foreach ($itemChild as &$item)
            $childs[$item->parent_id][] = &$item;
        unset($item);
        foreach ($itemChild as &$item) if (isset($childs[$item->id]))
        $item->children = $childs[$item->id];
        unset($item);
        foreach ($items as &$item) if (isset($childs[$item->id])){
            $item->children = $childs[ $item->id];
            $item->tree = $this->site->renderTree($item);
        }
        return $items;
    }

        function getAllAccounts()
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
}