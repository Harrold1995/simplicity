<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->quickAddTypes = Array(
            "class" => Array("type" => "table", "key" => "classes.description"),
        );
    }

    public function renderTree($item)
    {
        if (!isset($item->children) or count(isset($item->children)) == 0) return '';
        $tree = '';
        foreach ($item->children as $child) {
            // if(isset($child->name)) { $child->name = $child->name; }else{$child->name = $child->start;}
            $tree .= '<tr tabindex="0" class="sub" style="display:none;" data-type="' . $child->type . '" data-tt-parent-id="' . $item->type . "-" . $item->id . '" data-id="' . $child->id . '" data-tt-id="' . $child->type . "-" . $child->id . '">
                    <td style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap; max-width: 180px"><i class="' . $this->settings->tree_icons[$child->type] . '"></i> ' . $child->name . '</td>
                    <td class="overlay-f">' . $child->info . '</td>
                  </tr>';

            $tree .= $this->renderTree($child);
        }
        return $tree;
    }

    public function getNestedSelect($nodes){
        $childs = array();
        foreach ($nodes as &$item)
            $childs[$item->parent_id][] = &$item;
        unset($item);
        foreach ($nodes as &$item) if (isset($childs[$item->id]))
            $item->children = $childs[$item->id];
        unset($item);
        $nodes = $childs[0];
        $result = array();
        foreach($nodes as &$item){
            $result = $this -> nestedSelectStep($item, $result, 0);
        }
        return $result;
    }


    public function nestedSelectStep($item, $result, $step){
        $item->step = $step;
        $result[] = $item;
        if(isset($item->children))
            foreach($item->children as &$child){
                $result = $this->nestedSelectStep($child, $result, $step+1);
            }
        return $result;
    }

    public function getNestedSelectSlick($nodes){
        $childs = array();
        foreach ($nodes as &$item)
            $childs[$item->parent_id][] = &$item;
        unset($item);
        foreach ($nodes as &$item) if (isset($childs[$item->id]))
            $item->children = $childs[$item->id];
        unset($item);
        $nodes = $childs[0];
        $result = array();
        foreach($nodes as &$item){
            $result = $this -> nestedSelectStepSlick($item, $result, 0);
        }
        return $result;
    }


    public function nestedSelectStepSlick($item, $result, $step, $parent = null){
        $item->indent = $step;
        $item->step = $step;
        $item->parent = $parent;
        $result[] = $item;
        $item_id = count($result) - 1;
        $item->slickid = $item_id;
        if(isset($item->children))
            foreach($item->children as &$child){
                $result = $this->nestedSelectStepSlick($child, $result, $step+1, $item_id);
            }
        return $result;
    }

    public function initSettings(){
        $this->db->select('*');
        $this->db->from('company_settings');
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            $data = $q->row();
            $this->load->model('encryption_model');
            $data = $this->encryption_model->decryptThis($data);
            //return $data;
            $this->settings = $data;
        }
        
    }
    
    public function renderTree2($item)
    {
        if (!isset($item->children) or count(isset($item->children)) == 0) return '';
        $tree = '';
        foreach ($item->children as $child) {
            $tree .= '<tr tabindex="0" class="sub" style="display:none;" data-type="account" data-tt-parent-id="'. $item->type . "-" . $item->id. '" data-id="' . $child->id . '" data-tt-id="' . $child->type . "-" . $child->id . '">
                            <td style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap; max-width: 180px">' . $child->name . '</td>
                            <td>' . $child->shortname . '</td>
                            <td>$' . number_format($child->balance, 2) . '</td>
                     </tr>';
                        

            $tree .= $this->renderTree2($child);
        }
        return $tree;
    }// data-tt-id="' . $child->name  . '">
    function getInstantSearchData($type = null){
        if(!$type || $type == 'vendors') {
            $q = $this->db->select('id, concat_ws(" ", first_name, last_name) as name, email as email, phone as phone, address_line_1 as address, concat_ws(" ", first_name, last_name, email, phone, address_line_1) as searchText, "vendors" as type, profile_type_id')->where('profile_type_id', 1)->get('profiles');
            $vendors = Array();
            if ($q->num_rows() > 0) {
                $vendors = $q->result();
            }
            if($type == 'vendors') return $vendors;
        }

        if(!$type || $type == 'owners') {
            $q = $this->db->select('id, concat_ws(" ", first_name, last_name) as name, email as email, phone as phone, address_line_1 as address, concat_ws(" ", first_name, last_name, email, phone, address_line_1) as searchText, "owners" as type, profile_type_id')->where('profile_type_id', 5)->get('profiles');
            $owners = Array();
            if ($q->num_rows() > 0) {
                $owners = $q->result();
            }
            if($type == 'owners') return $owners;
        }

        if(!$type || $type == 'properties') {
            $pfilter = PFLAG==00 ?" where properties.id IN".PROPERTIES:"";
            $q = $this->db->query(
                'select concat_ws(" ", properties.name, entities.name, accounts.name) as searchText,"properties" as type, properties.id, properties.name as name, properties.address as address, entities.name as entity, entity_id, accounts.name as def_account, accounts.id as accid
                from properties
                left join entities on entities.id = properties.entity_id
                left join accounts on properties.default_bank = accounts.id'.$pfilter
            );
            $properties = Array();
            if ($q->num_rows() > 0) {
                $properties = $q->result();
            }
            if($type == 'properties') return $properties;
        }

        if(!$type || $type == 'accounts') {
            $pfilter = PFLAG==00 ?"where (banks.id is null) or (banks.property IN".PROPERTIES.")":"";
            $q = $this->db->query(
                'select concat_ws(" ", accno, accounts.name, account_types.name, account_category.name) as searchText,"accounts" as type, accounts.id as id, accounts.name as name, account_types.name as account_type, account_category.name as account_category
                from accounts
                join account_types on account_types.id = accounts.account_types_id
                join account_category on account_category.id = account_types.account_category_id
                left join banks on accounts.id = banks.account_id '.$pfilter
            );
            $accounts = Array();
            if ($q->num_rows() > 0) {
                $accounts = $q->result();
            }
            if($type == 'accounts') return $accounts;
        }

        if(!$type || $type == 'tenants') {
            $pfilter = PFLAG==00 ?" where properties.id IN".PROPERTIES:"";
            $q = $this->db->query('
            SELECT "tenants" as type, profiles.`id` as id, properties.id as pid, leases.id as lid, properties.name as propname, concat_ws(" ", properties.name, trim(units.name), `first_name`, last_name, email, phone) as searchText, concat_ws(" ", `first_name`, last_name) as name, trim(units.name) as unitname , concat_ws(" ", `first_name`, last_name, properties.short_name, trim(units.name), concat_ws("-",year(leases.start),year(leases.end))) as fullName, `profile_type_id`, units.id as unit_id, email, phone 
            FROM `leases_profiles` 
            join profiles on leases_profiles.profile_id = `profiles`.id
            join leases on leases_profiles.lease_id = leases.id
            join units on leases.unit_id = units.id
            join properties on units.property_id = properties.id'.$pfilter
            //where profiles.active = 1'
            );
            $names = $q -> result_array();
            if ($q->num_rows() > 0) {
                $tenants = $q->result();
            }
            if($type == 'tenants') return $tenants;
        }

        return Array( "tenants" => $tenants,"vendors" => $vendors ,"properties" => $properties, "owners" => $owners, "accounts" => $accounts, 'entities' => $entities);

    }

    function getAllSelectsData($type = null){
        if(!$type || $type == 'property') {
            $this->db->select('id, name');
            $this->db->from('properties');
            if(PFLAG==00) {$this->db->where_in('properties.id',explode( ',', trim(PROPERTIES, '()')));} 
            $q = $this->db->get();
            $properties = Array();
            if ($q->num_rows() > 0) {
                $properties = $q->result();
            }
            if($type == 'property') return $properties;
        }

        if(!$type || $type == 'propertiese') {
            $pfilter = PFLAG==00 ?" where properties.id IN".PROPERTIES:"";
            $q = $this->db->query(
                'select properties.id, concat_ws(" ",properties.name, concat("(",entities.name,")")) as name
                from properties
                left join entities on entities.id = properties.entity_id'. $pfilter
            );
            $propertiese = Array();
            if ($q->num_rows() > 0) {
                $propertiese = $q->result();
            }
            if($type == 'eproperty') return $propertiese;
        }
        if(!$type || $type == 'unit') {
            $q = $this->db->select('id, parent_id, name, property_id')->get('units');
            $units = Array();
            if ($q->num_rows() > 0) {
                $units = $q->result();
            }
            if($type == 'unit') return $this->getNestedSelectSlick($units);
        }
        if(!$type || $type == 'account') {
            $this->db->select('a.id, a.parent_id,  concat_ws(" ", a.accno, a.name) as name, at.shortname as details, all_props, GROUP_CONCAT(p.property_id SEPARATOR "|") as property_id, if(account_types_id = 1 or a.id = '.$this->site->settings->undeposited_funds.', 1, 0) as depositable');
            $this->db->from('accounts a');
            $this->db->join('property_accounts p','p.account_id = a.id', 'left');
            $this->db->join('account_types at','a.account_types_id = at.id', 'left');
            $this->db->join('banks b','a.id = b.account_id', 'left');
            if(PFLAG==00) {
                $this->db->where_in('b.property',explode( ',', trim(PROPERTIES, '()')));
                $this->db->or_where('b.id', null);
            } 
            $this->db->group_by('a.id');
            $q = $this->db->get();
            $accounts = Array();
            if ($q->num_rows() > 0) {
                $accounts = $q->result();
            }
            if($type == 'account') return $this->getNestedSelectSlick($accounts);
        }
        if(!$type || $type == 'class') {
            $q = $this->db->select('id, description as name')->get('classes');
            $classes = Array();
            if ($q->num_rows() > 0) {
                $classes = $q->result();
            }
            if($type == 'class') return $classes;
        }
        if(!$type || $type == 'profile') {
            $q = $this->db->query('SELECT `id`, " " as propname, " " as tenantname,"" as unitname, concat_ws(" ", `first_name`, last_name) as name, `profile_type_id`, `def_expense_acc` as `defaccount`, null as lease, null as unit_id, null as prop_id  FROM `profiles` WHERE profile_type_id <>3
            UNION ALL
            SELECT concat_ws("-",profiles.`id`,leases.id), properties.name as propname, concat_ws(" ", `first_name`, last_name) as tenantname, trim(units.name) as unitname , concat_ws(" ", `first_name`, last_name, properties.short_name, trim(units.name), concat_ws("-",year(leases.start),year(leases.end))) as name, `profile_type_id`, `def_expense_acc` as `defaccount`, leases.id as lease, units.id as unit_id, properties.id as prop_id 
            FROM `leases_profiles` 
            join profiles on leases_profiles.profile_id = `profiles`.id
            join leases on leases_profiles.lease_id = leases.id
            join units on leases.unit_id = units.id
            join properties on units.property_id = properties.id'
            //where profiles.active = 1'
            );
            $names = $q -> result_array();
            if ($q->num_rows() > 0) {
                $names = $q->result();
            }
            if($type == 'profile') return $names;
        }
		if(!$type || $type == 'tenant') {
			$q = $this->db->query('SELECT profiles.`id` as id, concat_ws(" ", `first_name`, last_name) as name, `profile_type_id`, leases.id as lease, units.id as unit_id, properties.id as prop_id 
            FROM `leases_profiles`             
            join profiles on leases_profiles.profile_id = `profiles`.id
            join leases on leases_profiles.lease_id = leases.id
            join units on leases.unit_id = units.id
            join properties on units.property_id = properties.id
            WHERE profile_type_id = 3'
			);
			$tenants = Array();
			if ($q->num_rows() > 0) {
				$tenants = $q->result();
			}
			if($type == 'tenant') return $tenants;
        }
        
        if(!$type || $type == 'profile2') {
			$q = $this->db->query('SELECT profiles.`id` as id, concat_ws(" ", `first_name`, last_name) as name, `profile_type_id`
            FROM  profiles 
            WHERE profile_type_id = 3'
			);
			$profile2 = Array();
			if ($q->num_rows() > 0) {
				$profile2 = $q->result();
			}
			if($type == 'profile2') return $profile2;
		}
        if(!$type || $type == 'account_category') {
            $q = $this->db->select('id, name')->get('account_category');
            $ac = Array();
            if ($q->num_rows() > 0) {
                $ac = $q->result();
            }
            if($type == 'account_category') return $ac;
        }
        if(!$type || $type == 'account_type') {
            $q = $this->db->select('id, name')->get('account_types');
            $at = Array();
            if ($q->num_rows() > 0) {
                $at = $q->result();
            }
            if($type == 'account_type') return $at;
        }
        if(!$type || $type == 'vendors') {
            $q = $this->db->select('id, concat_ws(" ", first_name, last_name) as name, profile_type_id')->where('profile_type_id', 1)->get('profiles');
            $vendors = Array();
            if ($q->num_rows() > 0) {
                $vendors = $q->result();
            }
            if($type == 'vendors') return $vendors;
        }
        if(!$type || $type == 'items') {
            $q = $this->db->select('id, item_name as name')->get('items');
            $items = Array();
            if ($q->num_rows() > 0) {
                $items = $q->result();
            }
            if($type == 'items') return $items;
        }
		if(!$type || $type == 'mtags') {
			$q = $this->db->select('id, text as name')->get('maintenance_tags');
			$tags = Array();
			if ($q->num_rows() > 0) {
				$tags = $q->result();
			}
			if($type == 'mtags') return $tags;
        }
        
        if(!$type || $type == 'entities') {
            $q = $this->db->select('id, name')->get('entities');
            $entities = Array();
            if ($q->num_rows() > 0) {
                $entities = $q->result();
            }
            if($type == 'entities') return $entities;
        }

        return (Object)Array("sitesettings" => $this->site->settings, "property" => $properties, "eproperty" => $propertiese, "profile2" => $profile2, "unit" => $this->getNestedSelectSlick($units), "account" => $this->getNestedSelectSlick($accounts), "class" => $classes, "profile" => $names, "tenant" => $tenants, "account_category" => $ac, "account_type" => $at, "vendors" => $vendors,  "mtags" => $tags,  "item" => $items, 'entity' => $entities);
    }

    public function getLeaseStatus($start, $end, $out) {
        if($out == null) return '2';
        if(strtotime(date("Y-m-d")) > strtotime($out)) return '1';

        if(strtotime(date("Y-m-d")) < strtotime($start)) return '3';
        return '2';
    }
}
