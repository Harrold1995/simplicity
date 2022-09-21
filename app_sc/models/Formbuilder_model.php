<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Formbuilder_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getFields($type) {
        $result = [];
        switch($type){
            case 'property':
                $result = ['property.name', 'property.address', 'units.name', 'units.bedrooms'];
                break;
            case 'lease':
                $result = ['property.name', 'property.address', 'property.city', 'property.state', 'property.zip', 'units.name', 'units.bedrooms', 'lease.start', 'lease.end', 'lease.amount', 'lease.memo', 'tenants.first_name', 'tenants.last_name'];
                break;
            default:
				$result = ['property.name', 'property.address', 'units.name', 'units.bedrooms', 'lease.start', 'lease.end', 'lease.amount', 'lease.memo', 'tenants.first_name', 'tenants.last_name'];
            	break;
           
        }
        return $result;
    }

    public function getTemplateFromLeaseId($id) {
		$q = $this->db->select('lt.*')->join('lease_templates lt', 'lt.id = l.lease_template' ,'LEFT')->get_where('leases l', Array('l.id' => $id));
		if ($q->num_rows() > 0) {
			return $q->row();
		}
		return array();
	}

    public function getFillData($lease_id) {
        $result = new stdClass();
		$q = $this->db->get_where('leases', Array('id' => $lease_id), 1);
		$result->lease = $q->row();
		$q = $this->db->join('profiles p', 'lp.profile_id = p.id')->get_where('leases_profiles lp', Array('lease_id' => $lease_id));
		$result->tenants = $q->result();
		$q = $this->db->select('p.*')->join('properties p', 'p.id = u.property_id', 'LEFT')->get_where('units u', Array('u.id' => $result->lease->unit_id), 1);
		$result->property = $q->row();
		$q = $this->db->get_where('units', Array('id' => $result->lease->unit_id));
		$result->units = $q->row();
        return $result;
    }

    public function getPropertyData($params) {
        $result = new stdClass();
        $q = $this->db->get_where('properties', Array('id' => $params['property_id']), 1);
        $result->property = $q->row();
        $q = $this->db->get_where('units', Array('property_id' => $params['property_id']));
        $result->units = $q->result();
        return $result;
    }

    public function getLeaseData($params) {
        $result = new stdClass();
        $q = $this->db->get_where('leases', Array('id' => $params['lease_id']), 1);
        $result->lease = $q->row();
        $q = $this->db->join('profiles p', 'lp.profile_id = p.id')->get_where('leases_profiles lp', Array('lease_id' => $params['lease_id']));
        $result->tenants = $q->result();
        return $result;
    }


}
