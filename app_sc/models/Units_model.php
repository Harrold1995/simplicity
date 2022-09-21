<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Units_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addUnit($data)
    {
        $this->db->insert('units', $data);
        return $this->db->insert_id();
    }

    public function editUnit($unit, $utilities, $deletes)
    {   

        $this->db->trans_start();

        if($deletes){
            $response = $this->checkItems($deletes, $delete);
               if($delete == NULL) return $response;
           }


        $uid = $unit['id'];
        unset($unit['id']);

        $this->db->update('units', $unit, array('id' => $uid));

        foreach ($utilities as &$utility) {
            if(array_key_exists('id', $utility)){
                $this->db->update('utilities', $utility, array('id' => $utility['id']));
            }else{
                $utility['property_id'] = $unit['property_id'];
                $utility['unit_id'] = $uid;
                $this->db->insert('utilities', $utility);
            }
        }



        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }
        return true;

    }



    public function getUnit($id)
    {
        $q = $this->db->get_where('units', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPropertyUnits($pid, $property = null)
    {
        $q = $this->db->get_where('units', array('property_id' => $pid));
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if($property){
                    $row->unit_type_name = $this->getUnitTypes($row->unit_type_id);
                }
                $data[] = $row;
            }
            return $data;
        }
        return Array();
    }

     public function getUnitTypes($type_id)
    {
        $unit_types = $this->settings->unit_types;

        foreach ($unit_types as $k => $utype) {
            if($type_id == $k){
                return $utype;
            }
        } 
    } 

    public function getUtilityTypes()
        {
            $this->db->select('*');
            $this->db->from('utility_types');
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

    public function getUnitandsubs($uid)
    {
        $this->db->select('u.id, u.name');
        $this->db->from('units u');
        $this->db->where('u.id', $uid);
        $this->db->or_where('u.parent_id', $uid);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getVendors()
        {
            $this->db->select('id, first_name, last_name, CONCAT_WS(" ",first_name, last_name) AS vendor, CONCAT_WS(" ",first_name, last_name) AS name');
            $this->db->from('profiles');
            $this->db->where('profile_type_id', 1);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return array();
        }

    public function getAllTenants($addtype = false)
    {
        $this->db->select('p.*, l.start, CONCAT_WS(" ",p.first_name,p.last_name) as name, lp.unit_id, lp.lease_id, lp.amount');
        $this->db->from('leases_profiles lp');
        $this->db->join('leases l', 'l.id = lp.lease_id');
        $this->db->join('profiles p', 'p.id = lp.profile_id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) {
                    $row->type = 'tenant';
                    $row->info = '$' . (int)$row->amount;
                }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getUnits($addtype = false, $params = null)
    {
        
        $this->load->model('properties_model');
        $properties = $this->properties_model->getPropertiesIdNames();
        $this->db->select('*');
        $this->db->from('units u');
        if(isset($params)){
            $this->db->where($params);
        }
        if (PFLAG==00){
            $this->db->where('`u.property_id` in '.PROPERTIES);
        }
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) {
                    $row->type = 'unit';
                    $row->info = $properties[$row->property_id];
                }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getAllLeases($addtype = false)
    {
        $q = $this->db->get('leases');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) {
                    $row->type = 'lease';
                    $row->info = '$' . (int)$row->amount;
                    $row->name = $row->start;
                    $row->lstatus = $this->site->getLeaseStatus($row->start, $row->end, $row->move_out);
                }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getUnitsList()
    {
        $topunits = $this->getUnits(true, array('parent_id' => 0));
        $units = $this->getUnits(true, array('parent_id!=' => 0));
        $leases = $this->getAllLeases(true);
        $tenants = $this->getAllTenants(true);
        if (isset($topunits) && isset($units))
            return $this->buildTree($topunits, $units, $leases, $tenants);
        else
            return $this->buildTree($topunits, null, $leases, $tenants);
    }

    public function getUnitType($id)
    {
        $this->db->select('ut.name');
        $this->db->from('unit_types ut');
        $this->db->join('units u', 'u.unit_type_id = ut.id');
        $this->db->where('u.id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }

    public function getLeaseAmount($id){

        $this->db->select('l.start,l.end, l.amount, l.unit_id');
        $this->db->from('leases l');
        $this->db->join('units u', 'u.id = l.unit_id');
        $this->db->where('l.unit_id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $date = date('Y-m-d');
                $date = date('Y-m-d', strtotime($date));
                $start = date('Y-m-d', strtotime($row->start));
                $end = date('Y-m-d', strtotime($row->end));
                //echo "<script>console.log(' got here');</script>";
                if (($date >= $start) && ($date <= $end)) {
                        // do this and that
                        //echo "<script>console.log(' got to lease');</script>";
                        $data[] = $row;
                        return $data;
                }
            }
        }
    }

    public function buildTree($topunits, $units = null, $leases, $tenants)
    {
        $childs = array();

        foreach ($units as &$item)
            $childs[$item->parent_id][] = &$item;
        unset($item);
        /*foreach ($tenants as &$item)
            if ($item->unit_id != null)
                $childs[$item->unit_id][] = &$item;
        unset($item);*/
        foreach ($leases as &$item)
        if ($item->unit_id != null)
            $childs[$item->unit_id][] = &$item;
        unset($item);
        foreach ($tenants as &$item)
        if ($item->lease_id != null)
            $childs['l' . $item->lease_id][] = &$item;
        unset($item);
        foreach ($leases as &$item) if (isset($childs ['l' . $item->id]))
            $item->children = $childs ['l' . $item->id];
        unset($item);
        foreach ($units as &$item) if (isset($childs[$item->id]))
            $item->children = $childs[$item->id];
        unset($item);
        foreach ($topunits as &$item) if (isset($childs[$item->id])) {
            $item->children = $childs[$item->id];
            $item->tree = $this->site->renderTree($item);
        }
        return $topunits;
    }

    public function getSingleUnitTransactions($id)
    {
        $theId = 1;
        if($id){
            $theId = $id;
        }//june 7 modified at.name to th.transaction_type
        $this->db->select('t.*, th.transaction_type AS type, a.name, a.accno, th.transaction_date, th.transaction_ref,tt.name AS transactionName,1 AS amount');

        $this->db->from('transactions t');
        $this->db->join('accounts a', 'a.id = t.account_id');
        $this->db->join('transaction_header th', 'th.id = t.trans_id');
        $this->db->join('account_types at', 'at.id = a.account_types_id');
        $this->db->join('transaction_type tt', 'tt.id = th.transaction_type');
        $this->db->where('t.unit_id', $theId);

        $q = $this->db->get();
        echo "<script>console.log('$amount this is the amount');</script>";
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $amount= $this->getSingleUnitAmount($row->id);
                $row->amount = $amount;
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getSingleUnitAmount($id){
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

            public function getUnitUtilities($id)
        {
            $this->db->select('u.*, a.name as aname, a.id as aid, un.name as unname, CONCAT(payee.first_name," ",payee.last_name) as payeeName, ut.name as utName');
            $this->db->from('utilities u');
            $this->db->join('utility_types ut', 'u.utility_type = ut.id', 'left');
            $this->db->join('accounts a', 'u.default_expense_acct = a.id', 'left');
            $this->db->join('units un', 'u.unit_id = un.id', 'left');
            $this->db->join('profiles payee', 'u.payee = payee.id', 'left');
            $this->db->where('u.unit_id', $id);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

        function addEditUtilities($utilities, $unit_id){ 
            $q = $this->db->select('property_id')->get_where('units', array('id' => $unit_id), 1);
            if ($q->num_rows() > 0) {
                $property_id = $q->row();
            }
            foreach ($utilities as &$utility) {
                $utility['unit_id'] = $unit_id;
                if(array_key_exists('id', $utility)){
                    $utility['property_id'] = $property_id->property_id;
                    $this->db->update('utilities', $utility, array('id' => $utility['id']));
                }else{
                    $utility['property_id'] = $property_id->property_id;
                    $this->db->insert('utilities', $utility);
                }
            }
            return true;
        }

        public function getAlllateCharges()
        {
            $this->db->select('id, name');
            $this->db->from('late_charge_setups');
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

        function getProperty($unit_id){ 
            $q = $this->db->select('property_id')->get_where('units', array('id' => $unit_id), 1);
            if ($q->num_rows() > 0) {
                $property_id = $q->row()->property_id;
            }
            return $property_id;
        }

        public function deleteUnit($id)
        {
            $this->db->trans_start();

                //doing it in regular sql because we might add more tables to delete query       
                $sql ='DELETE u
                    FROM units u
                    WHERE u.id = ?';
                    $this->db->query($sql, array($id));

            $this->db->trans_complete();

                if ($this->db->trans_status() === FALSE)
                {
                    return false;
                }
        
                return true;
      }
}
