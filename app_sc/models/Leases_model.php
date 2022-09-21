<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Leases_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addLease($data, $ttls, $renewal, $autoCharges, $rs, $sect_8, $ic)
    {
        $this->db->trans_start();

        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $this->upload->do_upload('original');
        $data['original'] = $this->upload->data('file_name');
        $data['deposit'] = removeComma($data['deposit']);
        $data['amount'] = removeComma($data['amount']);
        $data['last_month'] = removeComma($data['last_month']);
        $data['holdover'] = removeComma($data['holdover']);
        if($data['move_out'] == ""){$data['move_out'] = null;}
        $this->db->insert('leases', $data);
        $lid = $this->db->insert_id();
        //used for attachment
        // $this->load->model('documents_model');
        // $this->documents_model->uploadAttachment($lid, '2');
        if($data['original'] != ""){
            $this->db->insert('documents', Array("name" => $data['original'], "reference_id" => $lid, "type" => "2"));
        }
        
        $this->db->select('i.acct_income, p.id, p.default_RC_item');
        $this->db->from('units u');
        $this->db->join('properties p', 'p.id = u.property_id AND u.id = ' . $data['unit_id']);
        $this->db->join('items i', 'p.default_RC_item = i.id', 'left');
        
        //$this->db->where('u.id', $data['unit_id']);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $account_id = $q->row()->acct_income;
            $property_id = $q->row()->id;
            $item_id = $q->row()->default_RC_item;
        }

        $account_id = $account_id  ? $account_id : $this->getDefault_RC_item();
        $item_id = $item_id ? $item_id : $this->site->settings->default_RC_item; 
            
        $tenants =  $ttls;//before $ttl is saved by reference will hav to change to remove commas from tenant
        if (isset($ttls)){
            foreach ($ttls as &$ttl) {
                $ttl['lease_id'] = $lid;
                $ttl['deposit'] = removeComma($ttl['deposit']);
                $ttl['amount'] = removeComma($ttl['amount']);
                $ttl['last_month'] = removeComma($ttl['last_month']);
                $ttl['pet_deposit'] = removeComma($ttl['pet_deposit']);
                $this->db->insert('leases_profiles', $ttl);
            }
            
        }
        //sort by date ascending
        if($data['section_8']){
            usort($sect_8, function($a, $b) {
                return new DateTime($a['start_date']) <=> new DateTime($b['start_date']);
            });
        }
        $this->load->model('memorizedTransactions_model');
        if($data['active'] == 1){
            if($data['section_8']){
                foreach ($sect_8 as $index => &$section_8) {
                    $section_8['lease_id'] = $lid;
                    $this->db->insert('sect_8', $section_8);
                    
                    $params = ['add' =>1, 's8' => 1, 'index' => $index];
                    $this->memorizedTransactions_model->addSection8($data, $tenants, $lid, $section_8, $account_id, $property_id, $item_id, $params,$end);
                    $voucherEnd = !empty(($section_8['end_date'])) ? new DateTime($section_8['end_date']) : new DateTime('9999/12/30');
                    if (new DateTime($data['end']) < $voucherEnd){
                        break;//
                    }

                    
                     
                }
            }else{
                $params = ['add' =>1,  's8' => 0, 'index' => 0];
                $this->memorizedTransactions_model->addMonthlyLeaseCharge($data, $ttls, $lid, $account_id, $property_id, $item_id, $params);
            }

            if ($data['section_8'] && (new DateTime($data['end']) > $voucherEnd)){
                $data1 = $data;
                $data1['start'] = $voucherEnd->modify('+1 day');
                $data1['start'] = $voucherEnd->format('Y-m-d');
                $params = ['add' =>1, 's8' => 0, 'index' => 1];
                $this->memorizedTransactions_model->addMonthlyLeaseCharge($data1, $ttls, $lid, $account_id, $property_id, $item_id, $params);
            }
            
            $this->memorizedTransactions_model->addSecurityDeposit($data, $ttls, $lid, $property_id);
            $this->memorizedTransactions_model->addlmr($data, $ttls, $lid, $property_id);
            $this->memorizedTransactions_model->addPetDeposit($data, $ttls, $lid, $property_id);
        }

            foreach ($renewal as &$rn) {
                    $rn['lease_id'] = $lid;
                    $this->db->insert('renewal', $rn);
            }
            // not finished
            foreach ($autoCharges as &$autoCharge) {
                    $account_id = $this->getAccount($autoCharge['item_type_id']);
                    $autoCharge['type_id'] = 2;
                    $autoCharge['type_item_id'] = $lid;
                    $autoCharge['transaction_type'] = 6;
                    $autoCharge['property_id'] = $property_id;
                    $this->memorizedTransactions_model->autoCharge($autoCharge, $lid, $account_id, $property_id, $data['unit_id']);//unit ? is account right we are not going with default
            }

            foreach ($rs as &$r) {
                    $r['lease_id'] = $lid;
                    $this->db->insert('rent_stabilized', $r);
            }
            
            foreach ($in_court as &$ic) {
                    $ic['lease_id'] = $lid;
                    $this->db->insert('in_court', $ic);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {
                return false;
            }
            return true;
    }

    public function addLateChargeSetup($data, $rules)
    {
        $this->db->insert('late_charge_setups', $data);
        $lid = $this->db->insert_id();
        if (isset($rules))
            foreach ($rules as &$rule) {
                $rule['late_charge_setup_id'] = $lid;
                $ctypes = $rule['ctypes'];
                unset($rule['ctypes']);
                $this->db->insert('late_charge_rules', $rule);
                $rid = $this->db->insert_id();
                if ($rule['all_types'] == 0)
                    foreach ($ctypes as $cid => $val) {
                        $this->db->insert('late_charge_types',
                            Array('late_charge_rule_id' => $rid, 'late_charge_type_id' => $cid, 'late_charge_setup_id' => $lid));
                    }
            }
        return $lid;
    }

    public function getDefault_RC_item()
    {
        $this->db->select('acct_income AS default_RC_item');
        $this->db->from('items');
        $this->db->where('id', $this->site->settings->default_RC_item);
        
        //$this->db->where('u.id', $data['unit_id']);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->default_RC_item;
        }
    }

    public function getAccount($item)
    {
        $this->db->select('acct_income AS account');
        $this->db->from('items');
        $this->db->where('id', $item);
        
        //$this->db->where('u.id', $data['unit_id']);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->account;
        }
    }

    public function editLease($data, $ttls, $lid, $renewal, $autoCharges, $rs, $sect_8, $in_court, $deletes, $delete)
    {
        $this->db->trans_start();

        if($deletes){
            $response = $this->checkItems($deletes, $delete);
               if($delete == NULL) return $response;
           }

        $config['allowed_types'] = 'pdf';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $this->upload->do_upload('original');
        if($this->upload->data('file_name') != ""){
            $data['original'] = $this->upload->data('file_name');
            $this->db->insert('documents', Array("name" => $data['original'], "reference_id" => $lid, "type" => "2"));
        }
        //used for attachment
        // $this->load->model('documents_model');
        // $this->documents_model->uploadAttachment($lid, '2');
        $data['deposit'] = removeComma($data['deposit']);
        $data['amount'] = removeComma($data['amount']);
        $data['last_month'] = removeComma($data['last_month']);
        $data['holdover'] = removeComma($data['holdover']);
        if($data['move_out'] == ""){$data['move_out'] = null;}
        $this->db->update('leases', $data, array('id' => $lid));

        $this->db->select('i.acct_income, p.id, p.default_RC_item');
        $this->db->from('units u');
        $this->db->join('properties p', 'p.id = u.property_id AND u.id = ' . $data['unit_id']);
        $this->db->join('items i', 'p.default_RC_item = i.id', 'left');
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $account_id = $q->row()->acct_income;
            $property_id = $q->row()->id;
            $item_id = $q->row()->default_RC_item;
        }

        $account_id = $account_id  ? $account_id : $this->getDefault_RC_item();
        $item_id = $item_id ? $item_id : $this->site->settings->default_RC_item;

        if (isset($ttls)){
            foreach ($ttls as &$ttl) {
                $ttl['deposit'] = removeComma($ttl['deposit']);
                $ttl['amount'] = removeComma($ttl['amount']);
                $ttl['last_month'] = removeComma($ttl['last_month']);
                $ttl['pet_deposit'] = removeComma($ttl['pet_deposit']);
                if (isset($ttl['id'])) {
                    $ttlid = $ttl['id'];
                    unset($ttl['id']);
                    $this->db->update('leases_profiles', $ttl, array('id' => $ttlid));
                } else {
                    $ttl['lease_id'] = $lid;
                    $this->db->insert('leases_profiles', $ttl);
                }
            }

            
        }

        foreach ($sect_8 as &$section_8) {
            if(array_key_exists('id', $section_8)){
                $this->db->update('sect_8', $section_8, array('id' => $section_8['id']));
            }else{
                $section_8['lease_id'] = $lid;
                $this->db->insert('sect_8', $section_8);
            }
        }
        $this->load->model('memorizedTransactions_model');
        //commented out memorized transaction code on edit
        // if($data['active'] == 1){
        //     ;

        //     $this->db->select('*');
        //     $this->db->from('leases_profiles');
        //     $this->db->where('lease_id', $lid);
            
        //     $q = $this->db->get();
            
        //     if ($q->num_rows() > 0) {
        //         $this->db->delete('memorized_transactions', array('type_id' => 2, 'type_item_id' => $lid));
        //         $ttls = $q->result_array();
        //         foreach($ttls as $ttl){
        //             $tenant[0] = $ttl;//need to pass in as array to addSection8 function and addMonthlyLeaseCharge function
        //             $ttl['sec_8'] = $this->getSec8PerProfile($lid, $ttl['profile_id']);
        //             if($ttl['sec_8'][0] != null) {
        //                 foreach($ttl['sec_8'] as $index => &$section_8){
        //                     $params = ['add' =>0, 's8' => 1, 'index' => 1];
        //                     $this->memorizedTransactions_model->addSection8($data, $tenant, $lid, $section_8, $account_id, $property_id, $item_id, $params, $end);
        //                     $voucherEnd = !empty(($section_8['end_date'])) ? new DateTime($section_8['end_date']) : new DateTime('9999/12/30');
        //                     if (new DateTime($data['end']) < $voucherEnd){
        //                         break;//
        //                     }
        //                     //need to do regular charge for after section 8 finishes also check if correct by add
        //                     // if ($data['section_8'] && new DateTime($data['end']) > $voucherEnd){
        //                     //     $data1 = $data;
        //                     //     $data1['start'] = $voucherEnd->modify('+1 day');
        //                     //     $data1['start'] = $voucherEnd->format('Y-m-d');
        //                     //     $params = ['add' =>1, 's8' => 0, 'index' => 1];
        //                     //     $this->memorizedTransactions_model->addMonthlyLeaseCharge($data1, $ttls, $lid, $account_id, $property_id, $item_id, $params);
        //                     // }
        //                 }
        //             }else{
        //                 $params = ['add' =>0, 's8' => 0, 'index' => 1];
        //                 $this->memorizedTransactions_model->addMonthlyLeaseCharge($data, $tenant, $lid, $account_id, $property_id, $item_id, $params);
        //             }
        //              if ($section_8 && new DateTime($data['end']) > $voucherEnd){
        //                         $data1 = $data;
        //                         $data1['start'] = $voucherEnd->modify('+1 day');
        //                         $data1['start'] = $voucherEnd->format('Y-m-d');
        //                         $params = ['add' =>1, 's8' => 0, 'index' => 1];
        //                         $this->memorizedTransactions_model->addMonthlyLeaseCharge($data1, $ttls, $lid, $account_id, $property_id, $item_id, $params);
        //                     }
        //         }
        //     }
        // }   
            foreach ($renewal as &$rn) {
                if(array_key_exists('id', $rn)){
                    $this->db->update('renewal', $rn, array('id' => $rn['id']));
                }else{
                    $rn['lease_id'] = $lid;
                    $this->db->insert('renewal', $rn);
                }
            }

        foreach ($autoCharges as &$autoCharge) {
            
                $account_id = $this->getAccount($autoCharge['item_type_id']);
                $autoCharge['type_id'] = 2;
                $autoCharge['type_item_id'] = $lid;
                $autoCharge['transaction_type'] = 6;
                $autoCharge['property_id'] = $property_id;
                $this->memorizedTransactions_model->autoCharge($autoCharge, $lid, $account_id, $property_id, $data['unit_id'], $autoCharge['id']);//unit ? is account right we are not going with default
        }
            

            foreach ($rs as &$r) {
                if(array_key_exists('id', $r)){
                    $this->db->update('rent_stabilized', $r, array('id' => $r['id']));
                }else{
                    $r['lease_id'] = $lid;
                    $this->db->insert('rent_stabilized', $r);
                }
            }
            
            foreach ($in_court as &$ic) {
                if(array_key_exists('id', $ic)){
                    $this->db->update('in_court', $ic, array('id' => $ic['id']));
                }else{
                    $ic['lease_id'] = $lid;
                    $this->db->insert('in_court', $ic);
                }
            }
            if(($data['in_court'] == 1) && ($in_court == null)){
                $query = $this->db->select(1)
                ->where('lease_id', $lid)
                ->get('in_court')->result();
                if($query[0] > 0 ){
                    // $ic['lease_id'] = $lid;
                    // $this->db->update('in_court', $ic, array('lease_id' => $lid));
                }else{
                    $ic['lease_id'] = $lid;
                    $this->db->insert('in_court', $ic);
                }
            }
            ///add functionality for sect_8
            ///add functionality for memorized transactions

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {
                return false;
            }
            return true;
    }

    public function getSec8PerProfile($lid, $pid){
        $this->db->select('*');
        $this->db->from('sect_8');
        $this->db->where(['lease_id'=> $lid, 'profile_id' =>$pid]);
        $this->db->order_by('start_date ASC');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->result_array();  
        }
    }

    public function editLateChargeSetup($data, $rules, $lid)
    {
        $this->db->update('late_charge_setups', $data, array('id' => $lid));
        $this->db->delete('late_charge_rules', array('late_charge_setup_id' => $lid));
        $this->db->delete('late_charge_types', array('late_charge_setup_id' => $lid));
        if (isset($rules))
            foreach ($rules as &$rule) {
                $rule['late_charge_setup_id'] = $lid;
                $ctypes = $rule['ctypes'];
                unset($rule['ctypes']);
                $this->db->insert('late_charge_rules', $rule);
                $rid = $this->db->insert_id();
                if ($rule['all_types'] == 0)
                    foreach ($ctypes as $cid => $val) {
                        $this->db->insert('late_charge_types',
                            Array('late_charge_rule_id' => $rid, 'late_charge_type_id' => $cid, 'late_charge_setup_id' => $lid));
                    }
            }
        return $lid;
    }

    public function getLateChargeSetup($id)
    {
        $q = $this->db->get_where('late_charge_setups', Array('id' => $id));
        if ($q->num_rows() > 0) {
            $data =  $q->row();
            $q = $this->db->get_where('late_charge_rules', Array('late_charge_setup_id' => $id));
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $row->ctypes = $this->getLateChargeTypes($row->id);
                    $data->rules[] = $row;
                }
            }
            return $data;
        }
        return array();
    }

    public function getLateChargeTypes($id)
    {
        $q = $this->db->get_where('late_charge_types', Array('late_charge_rule_id' => $id));
        $data = Array();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row->late_charge_type_id;
            }
        }
        return $data;
    }

    public function getLeaseTemplates()
    {
        $q = $this->db->get('lease_templates');
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return array();
    }

    public function getLeaseTemplate($id)
    {  
        $q = $this->db->get_where('lease_templates', Array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return array();
    }

    public function getLease($id)
    {
        $this->db->select('l.*, u.property_id as property_id, p.address as address, u.name as unit, commercial as commercial, commercial_policy_num, annual_Inspection_date, annual_Inspection_name, child_under6');
        $this->db->from('leases l');
        $this->db->join('units u', 'l.unit_id = u.id');
        $this->db->join('properties p', 'u.property_id = p.id');
        $this->db->where('l.id', $id);
        $this->db->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }

    public function getFillData($id)
    {
        $this->db->select('p.address as property_address, p.name as property_name, "some dude" as property_landlord,
            l.start as lease_startdate, l.end as lease_enddate');
        $this->db->from('leases l');
        $this->db->join('units u', 'l.unit_id = u.id');
        $this->db->join('properties p', 'u.property_id = p.id');
        $this->db->where('l.id', $id);
        $this->db->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $result = $q->row();
        }
        //getting tenants info
        $this->db->select('CONCAT(pr.first_name," ",pr.last_name) as tenants_name, pr.phone as tenants_phone, pr.address_line_1 as tenants_address');
        $this->db->from('leases_profiles lp');
        $this->db->join('units u', 'lp.unit_id = u.id', 'left');
        $this->db->join('profiles pr', 'lp.profile_id = pr.id', 'left');
        $this->db->where('lease_id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
           $result->tenants = $q->result();
        }
        return $result;
    }

    public function getLeaseTtls($id)
    {
        $this->db->select('pr.*, lp.*, CONCAT(pr.first_name," ",pr.last_name) as name, u.name as unit');
        $this->db->from('leases_profiles lp');
        $this->db->join('units u', 'lp.unit_id = u.id', 'left');
        $this->db->join('profiles pr', 'lp.profile_id = pr.id', 'left');
        $this->db->where('lease_id', $id);
        //$this->db->where('lp.active','1');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $row->move_in = isset($row->move_in) && $row->move_in != '0000-00-00' ? date("m/d/Y", strtotime($row->move_in)) : null;
                $row->move_out = isset($row->move_out) && $row->move_out != '0000-00-00' ? date("m/d/Y", strtotime($row->move_out)) : null;
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    public function getLateChargeSetups()
    {
        $q = $this->db->get('late_charge_setups');
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return array();
    }

    public function getAllLeases()
    {
        $this->db->select('l.*, p.name as property, u.name as unit');
        $this->db->from('leases l');
        $this->db->join('units u', 'l.unit_id = u.id');
        $this->db->join('properties p', 'u.property_id = p.id');
        if (PFLAG==00){
            $this->db->where('`u.property_id` in '.PROPERTIES);
        }
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $row->type = 'lease';
                $row->lstatus = $this->site->getLeaseStatus($row->start, $row->end, $row->move_out);
                $row->name = $row->property;
                $row->info = $row->unit;
                $row->info2 = date("M Y", strtotime($row->start))  . '-' . date("M Y", strtotime($row->end));
            }
            return $q->result();
        }
        return array();
    }

    public function getAllTenants($addtype = false)
    {
        $this->db->select('p.*, l.start, CONCAT_WS(" ",p.first_name,p.last_name) as name, lp.unit_id, lp.lease_id, lp.amount');
        $this->db->from('leases_profiles lp');
        $this->db->join('leases l', 'l.id = lp.lease_id');
        $this->db->join('profiles p', 'p.id = lp.profile_id');
        $this->db->order_by('name', 'ASC');
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

    public function getLeasesList()
    {
        $leases = $this->getAllLeases(true);
        $tenants = $this->getAllTenants(true);
        if (isset($leases) && isset($tenants))
            return $this->buildTree($leases, $tenants);
        else
            return $leases;
    }

    public function buildTree($leases, $tenants)
    {
        $childs = array();

    foreach ($tenants as &$item)
    if ($item->lease_id != null)
        $childs['l' . $item->lease_id][] = &$item;
    unset($item);
    foreach ($leases as &$item) if (isset($childs ['l' . $item->id])){
        $item->children = $childs ['l' . $item->id];
        $item->tree = $this->site->renderTree($item);
        }
        return $leases;
    }

    public function getLeaseAmount($id){
        $date = date('Y-m-d');
        $this->db->select('l.*');// * FROM `table` WHERE active=0 AND CURDATE() between dateStart and dateEnd
        //$this->db->select('l.start,l.end, l.amount, l.unit_id');
        $this->db->from('leases l');
        $this->db->where('l.id', $id);
        $this->db->where('l.start <=', $date );
        $this->db->where('l.end >=', $date);
        $q = $this->db->get();
        $a = $q->result();
        if($a[0]->start){
            return true;
        }
       return null;
    }

    public function getAllLeaseTenants($id)
    {
        $this->db->select(' CONCAT_WS(" ",p.first_name,p.last_name) as tenant, lp.lease_id as lid, p.id as id ');
        $this->db->from('profiles p');
        $this->db->join('leases_profiles lp', 'lp.profile_id = p.id');
        $this->db->where('lp.lease_id', $id);
        //$this->db->join('leases l', 'lp.lease_id = l.id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    function getLeaseTransactions($profiles = null){
        if($profiles){
            $this->load->model('tenants_model');
            foreach($profiles as $profile){
            $result = $this->tenants_model->getTenantTransactions($profile->id);
            $data[] = $result;
            }
            return $data;
        }
        return null;
    }

    function getAllItems(){
        $this->db->select('i.id, CONCAT_WS("-",i.item_name,i.sales_description) as name, i.sales_description');
        $this->db->from('items i');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    function getSection8($lid){
        $this->db->select('s.*');
        $this->db->from('sect_8 s');
        $this->db->where('s.lease_id', $lid);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    function getrent_stabilized($lid){
        $this->db->select('rs.*');
        $this->db->from('rent_stabilized rs');
        $this->db->where('rs.lease_id', $lid);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    function getIn_court($lid){
        $this->db->select('ic.*, CONCAT(p.first_name," ",p.last_name) AS profile_name');
        $this->db->from('in_court ic');
        $this->db->join('profiles p','ic.profile_id = p.id');
        $this->db->where('ic.lease_id', $lid);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    function getTenants_on_lease($lid = null){
        $this->db->select('p.id, CONCAT(p.first_name," ",p.last_name) AS name');
        $this->db->from('profiles p');
        $this->db->join('leases_profiles lp','p.id = lp.profile_id');
        //$this->db->join('leases l','lp.lease_id = l.id');
        if($lid){
            $this->db->where('lp.lease_id', $lid);
        }
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    function getTenants(){
        $this->db->select('p.id, CONCAT_WS(" ",p.first_name,p.last_name) AS name');
        $this->db->from('profiles p');
        $this->db->where('p.profile_type_id', 3);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    function getRenewal($lid){
        $this->db->select('r.*');
        $this->db->from('renewal r');
        $this->db->where('r.lease_id', $lid);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    function changeLeaseDates($lid,$start,$end, $in, $out){


        $value=array('start'=>$start,'end'=>$end,'move_in'=>$in,'move_out'=>$out);
        $this->db->where('id',$lid);
        if( $this->db->update('leases',$value))
            {
                return true;
            }
            else
            {
                return false;
            }
    }
// works but I don't think escapes fields from sql injection
    // function getmemorizedTransactions($id,$tableId){
    //     $this->db->select('mt.id, mt.name, mt.start_date, mt.end_date, mt.amount, mt.next_trans_date, mt.frequency, f.name as fname, mt.auto, mt.data->>"$.transactions.profile_id" AS profile_id, mt.data->>"$.transactions.item_id" AS item_type_id',FALSE);//, 
    //     $this->db->from('memorized_transactions mt');
    //     $this->db->join('frequencies f','f.id = mt.frequency');
    //     $this->db->where('mt.type_id',$tableId);
    //     $this->db->where('mt.type_item_id', $id);
    //     $this->db->order_by('mt.start_date ASC');
       
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return null;
    // }

    //escapes
    function getmemorizedTransactions($id,$tableId){
        $sql ='SELECT mt.id, mt.name, mt.start_date, mt.end_date, mt.amount, mt.next_trans_date, mt.frequency, f.name AS fname, mt.auto, mt.data->>"$.transactions.profile_id" AS profile_id, mt.data->>"$.transactions.item_id" AS item_type_id, CONCAT_WS(" ",p.first_name,p.last_name) AS pname, i.item_name AS iname
        FROM memorized_transactions mt
        JOIN frequencies f ON f.id = mt.frequency
        Left JOIN profiles p ON mt.data->>"$.transactions.profile_id" = p.id
        JOIN items i ON mt.data->>"$.transactions.item_id" = i.id
        WHERE mt.type_id = ? 
        AND mt.type_item_id = ? 
        ORDER BY mt.start_date ASC';
        $q =$this->db->query($sql, array($tableId,$id)); //for sql injection
      
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
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

    function getLease_templates(){
        $this->db->select('id, name');
        $this->db->from('lease_templates');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();

    }

    public function deleteLease($id)
    {
        $this->db->trans_start();

            //$q = $this->db->delete('leases', array('id' => $id));
    $sql ='DELETE l, mt, lp
        FROM leases l
        LEFT JOIN leases_profiles lp ON l.id = lp.lease_id
        LEFT JOIN memorized_transactions mt ON l.id = mt.type_item_id AND mt.type_id = 2
        WHERE l.id = ?'; //for sql injection
        $this->db->query($sql, array($id));

        $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {
                 return false;
            }
    
            return true;
    }
}
