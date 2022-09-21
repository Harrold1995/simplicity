<?php defined('BASEPATH') OR exit('No direct script access allowed');
include('Profiles_model.php');

class Tenants_model extends Profiles_model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addTenant($data)
    {
        $data['profile_type_id'] = 3;
        return parent::addProfile($data);
    }

    public function editTenant($data, $contact, $address, $tid, $deletes, $delete)
    {
        return parent::editProfile($data, $contact, $address, $tid, $deletes, $delete);
    }

    public function getTenants($addtype = false, $params = null)
    {
        if (!isset($params)) $q = $this->db->get('profiles');
        else
            $q = $this->db->get_where('profiles', $params);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) {
                    $row->type = 'tenant';
                    //$row->info=$properties[$row->property_id];
                }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getAllTenants2()//get tenants gets all profiles even not tenents
    {
        $this->db->select('p.*, CONCAT_WS("",p.first_name," ",p.last_name) as name');
        $this->db->from('profiles p');
        $this->db->join('profile_types pt', 'pt.id = p.profile_type_id');
        $this->db->where("p.profile_type_id",3);
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getTtl($id)
    {
        $q = $this->db->get_where('leases_profiles', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data;
        }
        return null;
    }

    public function getTenant($tid)
    {
        $this->db->select('pr.*, lp.lease_id as lease_id, CONCAT(pr.first_name," ",pr.last_name) as name, p.name as property, u.name as unit, u.id as unit_id, p.id as property_id, lp.lease_id');
        $this->db->from('profiles pr');
        $this->db->join('leases_profiles lp', 'lp.profile_id = pr.id', 'left');
        $this->db->join('units u', 'lp.unit_id = u.id', 'left');
        $this->db->join('properties p', 'u.property_id = p.id', 'left');
        $this->db->where("pr.id=".$tid)->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $data = $q->row();
            $data = $this->encryption_model->decryptThis($data);
            return $data;
        }
        return array();
    }

    /*function getTenantsList()
    {
        $this->db->select('pr.*, lp.lease_id as lease_id, CONCAT(pr.first_name," ",pr.last_name) as name, p.name as property, u.name as unit');
        $this->db->from('profiles pr');
        $this->db->join('leases_profiles lp', 'lp.profile_id = pr.id', 'left');
        $this->db->join('units u', 'lp.unit_id = u.id', 'left');
        $this->db->join('properties p', 'u.property_id = p.id' , 'left');
        $q = $this->db->where('profile_type_id', '1')->get();
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return array();
    }*/

    function getTenantsLease($pid = null){
        $q = $this->db->get_where('leases', array('id' => $pid), 1);
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data;
        }
        return null;
    }

    public function getTenantsList()
    {
        $tenants = $this->getAllTenants(true);
        $leases = $this->getAllLeases(true);
        if (isset($tenants) && isset($leases))
            return $this->buildTree($tenants, $leases);
        else {
            return $tenants;
        }
    }

    public function searchTenants($search_string)
    {
        $this->db->select('p.*');
        $this->db->from('profiles p');
        $this->db->where('p.profile_type_id', 3);
        $this->db->group_start();
            $this->db->or_like([
                'p.first_name' => $search_string,
                'p.last_name' => $search_string,
                'p.email' => $search_string,
                'p.phone' => $search_string
            ]);
        $this->db->group_end();
 
        $this->db->order_by('p.id');

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

    public function getAllTenants($addtype = false)
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
        $this->db->select('pr.*, LTRIM(CONCAT_WS(" ",pr.first_name, pr.last_name)) as name, b.profile_id, totalbalance');
        $this->db->from('profiles pr');
        $this->db->join('(SELECT sum(debit - credit) as totalbalance, profile_id

        FROM transactions
        WHERE `account_id` = '. $ar .' '.$pfilter2.' 
        Group by profile_id) as b', 'pr.id = b.profile_id','left');

        $this->db->where('profile_type_id = 3 '.$pfilter);

        $this->db->ORDER_BY('name');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                //$row->totalBalance = $this->getOpenBalance($row->id);
                if ($addtype == true) {
                    $row->type = 'tenant';
                    $row->info = '$' . (int)$row->totalbalance;
                }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }
    //IMPORTANT - old does not work- gets id for lease profiles and not leases(in site.php)
    // public function getAllLeases($addtype = false)
    // {
    //     $this->db->select('lp.*, p.id AS pid, lp.amount, l.start');
    //     $this->db->from('leases_profiles lp');
    //     $this->db->join('leases l', 'l.id = lp.lease_id');
    //     $this->db->join('profiles p', 'lp.profile_id = p.id');
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             if ($addtype == true) {
    //                 $row->type = 'lease';
    //                 $row->info = '$' . (int)$row->amount;
    //                 $row->name = $row->start;
    //             }
    //             $data[] = $row;
    //         }
    //         return $data;
    //     }
    //     return null;
    // }
    //new works
    public function getAllLeases($addtype = false)
    {
        $this->db->select('l.*,lp.profile_id, p.id AS pid, lp.amount, l.start');
        $this->db->from('leases l');
        $this->db->join('leases_profiles lp', 'l.id = lp.lease_id');
        $this->db->join('profiles p', 'lp.profile_id = p.id');
        if (PFLAG==00){
            $this->db->join('units u', 'u.id = l.unit_id');
            $this->db->where('`u.property_id` in '.PROPERTIES);
        }
        $q = $this->db->get();
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

    public function buildTree($tenants, $leases)
    {
        $childs = array();

        foreach ($leases as &$item)
        if ($item->profile_id != null)
            $childs[$item->profile_id][] = &$item;
        unset($item);
        /*foreach ($tenants as &$item) if (isset($childs[$item->id]))
            $item->children = $childs[$item->id];
        unset($item);*/
        foreach ($tenants as &$item) if (isset($childs[$item->id])) {
            $item->children = $childs[$item->id];
            $item->tree = $this->site->renderTree($item);
        }
        return $tenants;
    }

    public function getContacts($id){

        $this->db->select('c.*');
        $this->db->from('contacts c');
        $this->db->join('profile_contact pc', 'pc.contact_id = c.id');
        $this->db->join('profiles p', 'p.id = pc.profile_id');
        $this->db->where('p.id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getPaymethods($id){

        $this->db->select('pm.*, tad.schedule_id');
        $this->db->from('tenant_bank_accounts pm');
        $this->db->join('tenant_autopay_data tad', 'pm.paymentId = tad.payment_id', 'left');
        $this->db->where('profile_id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getTenantLeases($id){

        $this->db->select(' leases.id as id, units.name as unit, properties.name as property, concat_ws(" ", profiles.first_name, profiles.last_name) as name' );
        $this->db->from('leases_profiles');
        $this->db->join('leases', 'leases.id = leases_profiles.lease_id');
        $this->db->join('units', 'units.id = leases.unit_id');
        $this->db->join('properties', 'properties.id = units.property_id');
        $this->db->join('profiles', 'profiles.id = leases_profiles.profile_id');
        $this->db->where('leases_profiles.profile_id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    

    public function getTenantTransactions($id)
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
        $this->db->where('t.profile_id', $theId);
        $this->db->order_by('th.transaction_date DESC, t.trans_id DESC, t.id ASC');
        $q = $this->db->get();
        //echo "<script>console.log('$amount this is the amount');</script>";
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $amount= $this->getSingletenantAmount($row->id);
                $row->balance = $amount;
                $data[] = $row;
            }
            return $data;
        }
    }
    public function getSingletenantAmount($id){
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

    // public function getOpenBalance($profile_id)
    // {   
    //     $sum = '(SELECT trans_id, SUM(amount) AS amounts
    //      FROM(SELECT transaction_id_a AS trans_id, (0- amount) AS amount
    //      FROM applied_payments
    //      UNION ALL
    //      SELECT transaction_id_b AS trans_id, amount 
    //      FROM applied_payments) trans
    //      GROUP BY trans_id) transum';

    //      $this->db->select('t.profile_id, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance');
    //      $this->db->from('transactions t');
    //      $this->db->join($sum, 't.id = transum.trans_id','left');
    //      //$this->db->where('t.lease_id', $leaseId);
    //      $this->db->where('t.profile_id', $profile_id);
    //      $this->db->where('t.account_id', 451);
    //      //$this->db->where_in('t.item_id', $rule->typesOfCharges);
    //      $balances = $this->db->get_compiled_select();
    //      $balances = '(' . $balances . ')balances';
    //      $this->db->reset_query();

    //      $this->db->select('balances.profile_id, SUM(balances.open_balance) AS open_balance');
    //      $this->db->from($balances);
    //      $this->db->group_by('balances.profile_id');
    //      //$this->db->having('open_balance > 0');

    //      $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             //$data[] = $row;
    //             $data = $row->open_balance;
    //         }
    //     }
    //     return $data;
    // }

    public function getAddresses($id){

        $this->db->select('a.*');
        $this->db->from('profile_address a');
        $this->db->where('a.profile_id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getAllLeaseTenantsEmail($lease){
        $this->db->select('group_concat(email SEPARATOR ";") as email');
        $this->db->from('leases_profiles');
        $this->db->join('profiles', 'profiles.id = leases_profiles.profile_id');
        $this->db->where('lease_id', $lease);
        $this->db->where('profiles.active', 1);
        $this->db->where('leases_profiles.move_out', null);
        $this->db->group_by('lease_id');
        $q = $this->db->get();
        return $q->row()->email; 
    }

    public function getAllLeaseTenantsNames($lease){
        $this->db->select('group_concat(first_name SEPARATOR ", ") as first_name');
        $this->db->from('leases_profiles');
        $this->db->join('profiles', 'profiles.id = leases_profiles.profile_id');
        $this->db->where('lease_id', $lease);
        $this->db->where('profiles.active', 1);
        $this->db->where('leases_profiles.move_out', null);
        $this->db->group_by('lease_id');
        $q = $this->db->get();
        return $q->row()->first_name; 
    }

    
}
