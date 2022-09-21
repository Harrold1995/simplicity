<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Properties_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addProperty($data, $units, $owners, $taxes, $utilities, $insurance, $managements, $key_codes)
    {
        $this->db->trans_start();

        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['upload_path'] = 'uploads/images';
        $this->load->library('upload', $config);
        $this->upload->do_upload('image');
        $data['image'] = $this->upload->data('file_name');

        $this->db->insert('properties', $data);
        $pid = $this->db->insert_id();
        // if($data['image'] != ""){
        //     $this->db->insert('documents', Array("name" => $data['image'], "reference_id" => $pid, "type" => "1"));
        // }
       

        $profileInfo = ['first_name' => $data['name'], 'address_line_1' => $data['address'], 'city' => $data['city'], 'state' => $data['state'], 'area_code' => $data['zip'], 'active' => $data['active']];
        
        $vendor = ['profile_type_id' => 1] + $profileInfo;
        $this->db->insert('profiles', $vendor);
        $vendor_profile_id = $this->db->insert_id();

        $tenant = ['profile_type_id' => 3] + $profileInfo;
        $this->db->insert('profiles', $tenant);
        $customer = $this->db->insert_id();
        $propertyName = $data['name'];

        $addProfiles = ['customer_profile_id' => $customer, 'vendor_profile_id' => $vendor_profile_id];
        $this->db->update('properties', $addProfiles, array('id' => $pid));

        $this->load->model('memorizedTransactions_model');


        foreach ($owners as &$owner) {
            $owner['property_id'] = $pid;
            $this->db->insert('property_owners', $owner);
        }

        foreach ($taxes as &$tax) {
            $tax['property_id'] = $pid;
            $this->db->insert('property_tax', $tax);
             $ptid = $this->db->insert_id();
             //$this->memorizedTransactions_model->taxes($tax, $ptid);
        }

        foreach ($utilities as &$utility) {
            $utility['property_id'] = $pid;
            $this->db->insert('utilities', $utility);
        }

        foreach ($insurance as &$policy) {
            $policy['property_id'] = $pid;
            $this->db->insert('insurance_policies', $policy);
            $ipid = $this->db->insert_id();
            $this->memorizedTransactions_model->payInsCharge($policy, $ipid);
        }

        foreach ($managements as &$management) {
            $management['property_id'] = $pid;
            $this->db->insert('management_fees', $management);
            $mfid = $this->db->insert_id();
            $this->memorizedTransactions_model->managementFees($management, $mfid, $pid, $customer, $propertyName);
        }

        foreach ($key_codes as &$key_code) {
            $key_code['property_id'] = $pid;
            $this->db->insert('property_key_codes', $key_code);
        }

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

            $unit['property_id'] = $pid;
            $this->db->insert('units', $unit);
            $uid = $this->db->insert_id();

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

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
            return false;
        }

        return $pid;
    }

    public function editProperty($data, $units, $owners, $taxes, $utilities, $insurance, $pid, $managements, $key_codes, $deletes, $delete)
    {
        $this->db->trans_start();

        if($deletes){
         $response = $this->checkItems($deletes, $delete);
            if($delete == NULL) return $response;
        }
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['upload_path'] = 'uploads/images';
        $this->load->library('upload', $config);
        $this->upload->do_upload('image');
        if($this->upload->data('file_name') != ""){
            $data['image'] = $this->upload->data('file_name');
            //$this->db->insert('documents', Array("name" => $data['image'], "reference_id" => $pid, "type" => "1"));
        }
        
        $this->db->update('properties', $data, array('id' => $pid));
        //used for attachment
        // $this->load->model('documents_model');
        // $this->documents_model->uploadAttachment($pid, '1');

        $profileInfo = ['first_name' => $data['name'], 'address_line_1' => $data['address'], 'city' => $data['city'], 'state' => $data['state'], 'area_code' => $data['zip'], 'active' => $data['active']];

        $this->db->select('customer_profile_id, vendor_profile_id');
        $this->db->from('properties');
        $this->db->where('id', $pid);
        $q = $this->db->get();
        $customer = $q->row()->customer_profile_id;
        $profile = $q->row()->vendor_profile_id;
        $propertyName = $data['name'];

        $this->db->where_in('id', [$customer, $profile]);
        $this->db->update('profiles', $profileInfo);

        $this->load->model('memorizedTransactions_model');

        foreach ($owners as &$owner) {
            if(array_key_exists('id', $owner)){
                $this->db->update('property_owners', $owner, array('id' => $owner['id']));
            }else{
                $owner['property_id'] = $pid;
                $this->db->insert('property_owners', $owner);
            }
           
        }

        foreach ($taxes as &$tax) {
            if(array_key_exists('id', $tax)){
                $this->db->update('property_tax', $tax, array('id' => $tax['id']));
            }else{
                $tax['property_id'] = $pid;
                $this->db->insert('property_tax', $tax);
                $ptid = $this->db->insert_id();
                //$this->memorizedTransactions_model->taxes($tax, $ptid);
            }
        }

        foreach ($utilities as &$utility) {
            if(array_key_exists('id', $utility)){
                $this->db->update('utilities', $utility, array('id' => $utility['id']));
            }else{
                $utility['property_id'] = $pid;
                $this->db->insert('utilities', $utility);
            }
        }

        foreach ($insurance as &$policy) {
            if(array_key_exists('id', $policy)){
                $this->db->update('insurance_policies', $policy, array('id' => $policy['id']));
            }else{
                $policy['property_id'] = $pid;
                $this->db->insert('insurance_policies', $policy);
                $ipid = $this->db->insert_id();
                $this->memorizedTransactions_model->payInsCharge($policy, $ipid);
            }
        }

        foreach ($managements as &$management) {
            if(array_key_exists('id', $management)){
                $this->db->update('management_fees', $management, array('id' => $management['id']));
                $this->memorizedTransactions_model->managementFees($management, $management['id'], $pid, $customer, $propertyName,1);
            }else{
                $management['property_id'] = $pid;
                $this->db->insert('management_fees', $management);
                $mfid = $this->db->insert_id();
                $this->memorizedTransactions_model->managementFees($management, $mfid, $pid, $customer, $propertyName);
            }
        }

        foreach ($key_codes as &$key_code) {
            if(array_key_exists('id', $key_code)){
                $this->db->update('property_key_codes', $key_code, array('id' => $key_code['id']));
            }else{
                $key_code['property_id'] = $pid;
                $this->db->insert('property_key_codes', $key_code);
            }
        }

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

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {
                return false;
            }
            return true;
    }

    public function getManagement($pid)
    {
        $this->db->select('mf.id, mf.frequency, f.name AS fname, mf.vendor, CONCAT_WS(" ",p.first_name,p.last_name) AS name, mf.amount, mf.start_date, mf.end_date, mf.item_id, accounts.name AS iname, mf.percentage_fixed, mf.account_id, a.name AS aname, mf.unit_id, u.name AS uname'); 
        $this->db->from('management_fees mf');
        $this->db->join('frequencies f','mf.frequency = f.id');
        $this->db->join('profiles p', 'mf.vendor = p.id' );
        $this->db->join('accounts', 'mf.item_id = accounts.id');
        $this->db->join('accounts a', 'mf.account_id = a.id');
        $this->db->join('units u', 'mf.unit_id = u.id','left');
        $this->db->where('mf.property_id', $pid);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }




    public function deleteProperty($id)
    {
        $q = $this->db->delete('properties', array('id' => $id));
        return true;
    }

    public function getProperty($id)
    {
        $q = $this->db->get_where('properties', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            $a = $q->row();
            $manager = $this->getManager($a->manager);
            $q->row()->manager = $manager;
            $entity = $this->getEntity($a->entity_id);
            $q->row()->entityName = $entity;
            return $q->row();
        }
        return false;
    }

    public function getAllProperties($addtype = false, $check = true)
    {
        if($check) $this->db->where('('.PFLAG.' OR properties.id IN '.PROPERTIES.')');
        $q = $this->db->select('*')->order_by('name', 'ASC')->get('properties');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'property';
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getPropertiesIdNames()
    {
        $this->db->where('active', 1);
        $q = $this->db->get('properties');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[$row->id] = $row->name;
            }
            return $data;
        }
        return null;
    }

    public function getAllUnits($addtype = false)
    {
        $q = $this->db->select('*')->order_by('id', 'ASC')->get('units');
        //$q = $this->db->get('units')->order_by('id', 'ASC');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) {
                    $row->type = 'unit';
                    $row->info = $this->settings->unit_types[$row->unit_type_id];
                }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getAllTenants($addtype = false)
    {
        $this->db->select('p.*, l.start, CONCAT_WS(" ",p.first_name,p.last_name) as name, lp.unit_id, lp.lease_id, lp.amount');
        $this->db->from('leases_profiles lp');
        $this->db->join('leases l', 'l.id = lp.lease_id');
        $this->db->join('profiles p', 'p.id = lp.profile_id');
        $this->db->where('lp.active','1');
        $this->db->order_by('name', 'ASC');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) {
                    $row->type = 'tenant';
                    $row->info = '$' . (int)$row->amount;
                    $row->lid = $row->lease_id;
                }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getAllTenants2($addtype = false)
    {
        $pfilter =  "";
        $pfilter2 =  PFLAG==00 ?"AND property_id IN".PROPERTIES:"";
        if (PFLAG==00){
           

            $this->db->select('group_concat(lp.profile_id) as property_profiles');
            $this->db->from('leases_profiles lp');
            $this->db->join('leases l', 'l.id = lp.lease_id');
            $this->db->join('units u', 'l.unit_id = u.id');
            $this->db->where('`property_id` in '.PROPERTIES);
            $property_profiles = $this->db->get()->row('property_profiles');
            $pfilter = "AND pr.id IN(".$property_profiles.")";
        } 
        $ar = $this->site->settings->accounts_receivable;
        $this->db->select('pr.*, LTRIM(CONCAT_WS(" ",pr.first_name, pr.last_name)) as name, b.profile_id, totalbalance, lp.lease_id as lease_id ');
        $this->db->from('profiles pr');
        $this->db->join('leases_profiles lp', 'pr.id = lp.profile_id');
        $this->db->join('(SELECT sum(debit - credit) as totalbalance, profile_id, lease_id

        FROM transactions
        WHERE `account_id` = '. $ar .' '.$pfilter2.' 
        Group by profile_id, lease_id) as b', 'pr.id = b.profile_id and lp.lease_id = b.lease_id','left');

        $this->db->where('profile_type_id = 3 '.$pfilter);

        $this->db->ORDER_BY('name');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                //$row->totalBalance = $this->getOpenBalance($row->id);
                if ($addtype == true) {
                    $row->type = 'tenant';
                    $row->info = '$' . number_format($row->totalbalance,2);
                    $row->lid = $row->lease_id;
                }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getAllLeases($addtype = false)
    {
        $ar = $this->site->settings->accounts_receivable;
        $this->db->select('l.*, totalbalance');
        
        $this->db->from('leases l');
        $this->db->join('(SELECT sum(debit - credit) as totalbalance, lease_id

        FROM transactions
        WHERE `account_id` = '. $ar .' '.$pfilter2.' 
        Group by lease_id) as b', 'l.id = b.lease_id','left');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) {
                    $row->type = 'lease';
                    $row->info = '$' . number_format($row->totalbalance,2);
                    $row->name = $row->start." To ".$row->end;
                    $row->lstatus = $this->site->getLeaseStatus($row->start, $row->end, $row->move_out);
                }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getPropertiesList()
    {
        $properties = $this->getAllProperties(true);
        $units = $this->getAllUnits(true);
        $tenants = $this->getAllTenants2(true);
        $leases = $this->getAllLeases(true);
        if (isset($properties) && isset($units))
            return $this->buildTree($properties, $units, $tenants, $leases);
        else {
            return $properties;
        }
    }

    public function buildTree($properties, $units, $tenants, $leases)
    {
        $childs = array();

        foreach ($units as &$item)
            if ($item->parent_id != 0) $childs[$item->parent_id][] = &$item;
            else $childs['p' . $item->property_id][] = &$item;
        unset($item);
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
        foreach ($units as &$item) if (isset($childs[$item->id])) {
            $item->children = $childs[$item->id];
        }
        unset($item);
        foreach ($properties as &$item) if (isset($childs['p' . $item->id])) {
            $item->children = $childs['p' . $item->id];
            $item->info = count($item->children);
            //$item->tree = $this->site->renderTree($item);
        }
        return $properties;
    }

        //old code
       /* public function getSinglePropertyTransactions2($id)
        {
            $theId = 1;
            if($id){
                $theId = $id;
            }
            $this->db->select('t.*, th.transaction_type AS type, th.transaction_date, th.transaction_ref,tt.name, 1 AS balance');
            
            $this->db->from('transactions t');
            $this->db->join('property_accounts pa', 'pa.account_id = t.account_id');
            $this->db->join('properties p', 'pa.property_id = p.id');
            $this->db->join('transaction_header th', 'th.id = t.trans_id');
            $this->db->join('transaction_type tt', 'tt.id = th.transaction_type');
            $this->db->where('p.id', $theId);
    
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $amount= $this->getSingleAccountAmount($row->id);
                    $row->balance = $amount;
                    $data[] = $row;
                    echo "<script>console.log('got to properties');</script>";
                }
                return $data;
            }
        }*/
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

        //populate right side
        public function getSinglePropertyTransactions($id)
        {
            $theId = 1;
            if($id){
                $theId = $id;
            }
            $this->db->select('t.*, th.transaction_type AS type, th.transaction_date, th.transaction_ref,tt.name AS transactionName, 1 AS balance');
            
            $this->db->from('transactions t');
            $this->db->join('properties p', 'p.id = t.property_id');
            //$this->db->join('properties p', 'pa.property_id = p.id');
            $this->db->join('transaction_header th', 'th.id = t.trans_id');
            $this->db->join('transaction_type tt', 'tt.id = th.transaction_type');
            $this->db->where('t.property_id', $theId);
    
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $amount= $this->getSingleAccountAmount($row->id);
                    $row->balance = $amount;
                    $data[] = $row;
                    //echo "<script>console.log('got to properties');</script>";
                }
                return $data;
            }
        }

        public function getUnitsCount($pid)
        {
            if(!isset($pid)){return;}
            $q =$this->db->where('property_id',$pid)->from("units")->count_all_results();
            return $q;
        }

        public function getVacancyCount($pid)
        {
            if(!isset($pid)){return;}

            $date = date('Y-m-d');
            $date = date('Y-m-d', strtotime($date));

            /*$this->load->model('units_model');
            $pUnits = $this->units_model->getPropertyUnits($pid);
            foreach($pUnits as $unit){
                $unitIds[] = $unit->id;
            }
            if(isset($unitIds)){*/
                    $total =0;
                    $this->db->select_max('l.end'); 
                    $this->db->from('leases l');
                    $this->db->join('units u', 'u.id = l.unit_id');
                    $this->db->join('properties p', 'p.id = u.property_id');
                    $this->db->where('u.property_id', $pid);
                    $this->db->group_by('unit_id');
                    $q = $this->db->get();
                    $test =$this->getUnitsWithOutLeaseCount($pid);
                    $total += $test;
                    foreach (($q->result()) as &$row) {
                        if($row->end < $date )
                        ++$total;
                }return $total;
            //}
            return false;
        }
        public function getUnitsWithOutLeaseCount($pid)
        {
            $total = 0;
            if(!isset($pid)){return;}

            $this->db->select('u.id'); 
            $this->db->from('units u');
            $this->db->join('leases l', 'l.unit_id = u.id','left');
            $this->db->where('l.unit_id ', null);
            $this->db->where('u.property_id', $pid);
            $q = $this->db->get();
            foreach (($q->result()) as &$row) {
                $total++;
            }
            return $total;
        }

        public function getFutureVacancyCount($pid)
        {
            if(!isset($pid)){return;}

            $todaysDate = date('Y-m-d');
            $date =  date('Y-m-d', strtotime('+2 months'));

           /* $this->load->model('units_model');
            $pUnits = $this->units_model->getPropertyUnits($pid);
            foreach($pUnits as $unit){
                $unitIds[] = $unit->id;
            }
            if(isset($unitIds)){*/
                    $total =0;
                    $this->db->select_max('l.end'); 
                    $this->db->from('leases l');
                    $this->db->join('units u', 'u.id = l.unit_id');
                    $this->db->join('properties p', 'p.id = u.property_id');
                    $this->db->where('u.property_id', $pid);
                    $this->db->group_by('unit_id');
                    $q = $this->db->get();
                    $total += $test;
                    foreach (($q->result()) as &$row) {
                        if(($row->end < $date) and ($row->end > $todaysDate ))
                        ++$total;
                }return $total;
           // }
            return false;
        }

        public function getManager($id)
        {
            $q = $this->db->get_where('profiles', array('id' => $id), 1);
            if ($q->num_rows() > 0) {
                $manager = $q->row()->first_name . " " . $q->row()->last_name;
                //$q->first_name = $manager;
                return $manager;
                //return $q->first_name;
            }
            return false;
        }
        public function getEntity($id)
        {
            $q = $this->db->get_where('entities', array('id' => $id), 1);
            if ($q->num_rows() > 0) {
                $entity = $q->row()->name;
               return $entity;
            }
            return false;
        }

        public function getPropertyRentTotal($pid)
        {
            if(!isset($pid)){return;}

            $date = date('Y-m-d');
            $date = date('Y-m-d', strtotime($date));

                        $total =0;
                        $this->db->select('l.amount'); 
                        $this->db->from('leases l');
                        $this->db->join('units u', 'l.unit_id = u.id');
                        $this->db->where("(l.end >'".$date."' OR l.end = '0000-00-00')");
                        $this->db->where('l.start <', $date);
                        $this->db->where('u.property_id', $pid);
                        $this->db->where('u.active', 1);
                        $q = $this->db->get();

                    foreach (($q->result()) as &$row) {
                        //if($row->end > $date and $row->start < $date){
                            $total += $row->amount;
                        //}
                }return $total;
            return false;
        }
        public function ytdProfit($pid)
        {
            $q = $this->db->query( "SELECT sum(credit - debit) AS amount
            FROM transactions  JOIN accounts ON accounts.id = transactions.account_id  
            JOIN transaction_header ON transaction_header.id = transactions.trans_id   
            JOIN account_types ON account_types.id =accounts.account_types_id   
            JOIN account_category ON account_category.id = account_types.account_category_id   
            JOIN properties ON properties.id = transactions.property_id
            WHERE account_category.id >3 AND account_category.id <6 
            AND (transaction_header.transaction_date BETWEEN '". date('Y') ."-01-01' AND '" . date('Y-m-d') ."') 
            AND (properties.id = '$pid')");
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $total = $row->amount;
                }
                return $total;
            }
            return "0.00";
        }

        public function allIn($pid)
        {
            $q = $this->db->query( "SELECT sum(Debit -Credit) as Amount
                                    FROM(
                                        
                                        SELECT  transactions.id as id, transactions.credit AS Credit, transactions.debit AS Debit, account_category.name AS 'Account_Category', account_types.name AS 'Account_Type', accounts.name as Account, accounts.accno as 'Account_Number', transactions.Date as Date, transactions.description as Description,
                                            CASE 
                                                    WHEN accounts.account_types_id = 4 or accounts.id = 114 THEN  'BY EXPENSE'
                                                    WHEN accounts.id = company_settings.net_income THEN  'BY EXPENSE'
                                                    WHEN tare.id IS NOT NULL THEN  'BY EXPENSE'
                                                    WHEN accounts.account_types_id IN(1,2,3,5) THEN  'BY EQUITY'
                                                    WHEN account_category.id = 3 AND tare.id IS NULL AND accounts.id != company_settings.net_income AND accounts.id != 114 THEN 'BY EQUITY'
                                                    WHEN account_category.id = 2 THEN 'BY EQUITY'
                                                    ELSE 0
                                            END AS ExEq , property_id 
                                            FROM
                                            (
                                                SELECT  t.id as id, t.trans_id, credit, debit, IF (ag.id < '4', a.id, company_settings.net_income) as account, transaction_date as Date, t.description, property_id FROM `transactions` t 
                                                INNER JOIN transaction_header th ON th.id = t.trans_id
                                                LEFT JOIN accounts a on t.account_id = a.id
                                                LEFT JOIN account_types act on act.id = a.account_types_id
                                                INNER JOIN account_category ag on ag.id = act.account_category_id
                                                INNER JOIN properties p on p.id = t.property_id
                                                cross join company_settings

                                            ) as transactions
                                        cross join company_settings
                                        LEFT JOIN(
                                        SELECT t.id  as id FROM `transactions` t 
                                            JOIN accounts a on t.account_id = a.id AND a.name like '%tare%'
                                            JOIN account_types act on act.id = a.account_types_id 
                                            INNER JOIN account_category ag on ag.id = act.account_category_id and ag.id = 3
                                            )tare on transactions.id = tare.id
                                            LEFT JOIN accounts on transactions.account = accounts.id
                                    LEFT JOIN account_types on account_types.id = accounts.account_types_id
                                    LEFT JOIN account_category on account_category.id = account_types.account_category_id
                                    WHERE property_id = '$pid'  AND  transactions.Date <= '".date('Y-m-d')."'
                                    )  all_in

                                    Where ExEq = 'BY EQUITY'");
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $total = 0- $row->Amount;
                }
                return $total;
            }
            return "0.00";
        }


        public function investorIn($pid)
        {
            $q = $this->db->query( "SELECT  SUM(transactions.credit -transactions.debit) as Amount
            FROM transactions  JOIN accounts ON accounts.id = transactions.account_id  
            JOIN transaction_header ON transaction_header.id = transactions.trans_id   
            JOIN account_types ON account_types.id =accounts.account_types_id   JOIN account_category ON account_category.id = account_types.account_category_id   
            JOIN properties ON properties.id = transactions.property_id  
            WHERE  account_category.id = 3 AND accounts.name not like '%tare%' AND  accounts.name not like '%net income%' AND accounts.name not like '%retained earnings%' AND property_id = '$pid'  AND  transaction_header.transaction_date <= '".date('Y-m-d')."'
                                   ");
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $total = $row->Amount;
                }
                return $total;
            }
            return "0.00";
        }


        public function MortgagesTot($pid)
        {
            $q = $this->db->query( "SELECT  SUM(transactions.credit -transactions.debit) as Amount
            FROM transactions  JOIN accounts ON accounts.id = transactions.account_id  
            JOIN transaction_header ON transaction_header.id = transactions.trans_id   
            JOIN account_types ON account_types.id =accounts.account_types_id   JOIN account_category ON account_category.id = account_types.account_category_id   
            JOIN properties ON properties.id = transactions.property_id  
            WHERE  account_types.id in (8,9) AND property_id = '$pid'  AND  transaction_header.transaction_date <= '".date('Y-m-d')."'
                                   ");
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $total = $row->Amount;
                }
                return $total;
            }
            return "0.00";
        }

        public function getPropertyBank($pid)
        {
            if(!isset($pid)){return;}

                        $this->db->select('a.name'); 
                        $this->db->from('accounts a');
                        $this->db->join('properties p', 'p.default_bank = a.id');
                        $this->db->where('p.id', $pid);
                        $q = $this->db->get();
                        foreach (($q->result()) as &$row) {
                        $bank = $row->name;
                    }
                return $bank;
        }

        public function getPropertyBankTotal($pid){

            if(!isset($pid)){return;}
            $this->db->select('t.account_id, SUM(t.credit) AS credit, SUM(t.debit) AS debit, ac.debit_credit,1 AS balance');
            
            $this->db->from('transactions t');
            $this->db->join('accounts a', 'a.id = t.account_id');
            $this->db->join('properties p', 'p.default_bank = a.id');
            $this->db->join('account_types at', 'at.id = a.account_types_id');
            $this->db->join('account_category ac', 'ac.id = at.account_category_id');
            $this->db->where('p.id', $pid);
            $this->db->group_by('t.account_id');
    
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

        public function getPropertyOwners($id)
        {
            $this->db->select('property_owners.id AS owner_id, property_owners.property_id, property_owners.percentage, property_owners.profile_id,profiles.first_name, profiles.last_name, profiles.email, profiles.phone ,profiles.address_line_1,  profiles.address_line_2');
            $this->db->from('property_owners');
            $this->db->join('profiles', 'property_owners.profile_id = profiles.id');
            $this->db->where('property_owners.property_id', $id);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

        public function getPropertyTaxes($id)
        {
            $this->db->select('pt.*, f.name AS fname,');
            $this->db->from('property_tax pt');
            $this->db->join('frequencies f','pt.frequency = f.id');
            $this->db->where('pt.property_id', $id);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

        public function getPropertyUtilities($id)
        {
            $this->db->select('u.*, a.name as aname, a.id as aid, un.name as unname, CONCAT_WS(" ",payee.first_name,payee.last_name) as payeeName, ut.name as utName, pt.name as paidByName');
            $this->db->from('utilities u');
            $this->db->join('utility_types ut', 'u.utility_type = ut.id','left');
            $this->db->join('accounts a', 'u.default_expense_acct = a.id', 'left');
            $this->db->join('units un', 'u.unit_id = un.id','left');
            $this->db->join('profiles payee', 'u.payee = payee.id', 'left');
            $this->db->join('profile_types pt', 'u.paid_by = pt.id', 'left');
            $this->db->where('u.property_id', $id);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

        public function getPropertyUtilitiesForBill($id = null)
        {
            $this->db->select('u.id, u.payee AS profile_id, CONCAT_WS(" ",prof.first_name,prof.last_name) as profileName, u.property_id, p.name as propertyName, u.unit_id, un.name as unitName, u.description, u.account, ut.name as utility_type, ut.id as utility_type_id, u.last_paid_date AS old_last_paid_date, u.direct_payment, u.default_expense_acct AS account_id, u.billable, u.memo');
            $this->db->from('utilities u');
            $this->db->join('utility_types ut', 'u.utility_type = ut.id', 'left');
            $this->db->join('properties p', 'u.property_id = p.id', 'left');
            $this->db->join('units un', 'u.unit_id = un.id', 'left');
            $this->db->join('profiles prof', 'u.payee = prof.id', 'left');
            if($id){
                $this->db->where('u.property_id', $id);
            }
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

    public function getPropertyDocuments($id)
    {
        $this->db->select('*');
        $this->db->from('documents');
        $this->db->where('documents.reference_id', $id);
        $this->db->where('documents.type', '1');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $row->href = base_url().'uploads/documents/'.$row->name;
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

        public function getPropertyInsurance($id)
        {
            $this->db->select('ip.*, a.name as bankName, CONCAT_WS(" ",p.first_name, p.last_name) as brokerName');
            $this->db->from('insurance_policies ip');
            $this->db->join('profiles p', 'ip.broker = p.id', 'left');
            $this->db->join('accounts a', 'ip.payment_acct = a.id', 'left');
            $this->db->where('ip.property_id', $id);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

        public function getBankAccounts()
        {
            $this->db->select('id, name');
            $this->db->from('accounts');
            $this->db->where('account_types_id', 1);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

        public function getAllManagers()
        {
            $this->db->select('id,  CONCAT_WS(" ",first_name, last_name) as name,');
            $this->db->from('profiles');
            $this->db->where('profile_type_id', 4);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

        public function getPropertyManager($id)
        {
            $q = $this->db->get_where('properties', array('id' => $id), 1);
            if ($q->num_rows() > 0) {
                $manager = $q->row()->manager;
                return $manager;
            }
            return false;
        }

        public function getItems()
        {
            $this->db->select('id, item_name as name');
            $this->db->from('items');
            $this->db->order_by('item_name');
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

        public function getAllAccounts($type = null)
        {
            $this->db->select('id, name, accno, all_props, parent_id');
            $this->db->from('accounts');
            $this->db->order_by('name');
            $this->db->where('active', 1);
            if($type){
                $this->db->where('account_types_id', 13);//income accounts
            }
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
                return $data;
            }
            return array();
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

        function getFrequencies(){
            $this->db->select('id, name');
            $this->db->from('frequencies');
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return array();

        }

        public function getutilityTypes()
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

        public function getPaid_by_types()
        {
            $this->db->select('id, name');
            $this->db->from('profile_types');
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

        function getKey_codes($id){
            $this->db->select('property_key_codes.id as id, property_key_codes.property_id as property_id, key_code, area, property_key_codes.active as active, u.id as unit, u.name as uname');
            $this->db->from('property_key_codes');
            $this->db->join('units u', 'property_key_codes.unit = u.id', 'left');
            $this->db->where('property_key_codes.property_id', $id);
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
