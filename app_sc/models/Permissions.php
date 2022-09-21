<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Permissions extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllPermissions() {
        $array = Array();
        $array[] = Array("key" => "properties", "options" => Array("view", "edit", "add", "delete"), "name" => "Properties", "data" => Array(
            Array('key' => "properties_owners", "options" => Array("view", "edit", "add", "delete"), "name" => "Owners"),
            Array('key' => "properties_taxes", "options" => Array("view", "edit", "add", "delete"), "name" => "Taxes"),
        ));
        $array[] = Array("key" => "units", "options" => Array("enabled", "view", "edit", "add", "delete"), "name" => "Units");
        $array[] = Array("key" => "leases", "options" => Array("enabled", "view", "edit", "add", "delete"), "name" => "Leases", "data" => Array(
            Array('key' => "tenants", "options" => Array("view", "edit", "add", "delete"), "name" => "Tenants"),
            Array('key' => "some_feature", "options" => Array("enabled"), "name" => "Some feature"),
        ));
        return $array;
    }

    public function getPermissions() {
        $groups = $this->ion_auth->get_users_groups_ids($this->session->userdata('user_id'));
        if(count($groups) == 0) return null;
        $this->db->select('*');
        $this->db->where_in('group_id', $groups);
        $q = $this->db->get('permissions');
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }

    public function getGroupPermissions($id) {

        $q = $this->db->select('permission')->get_where('user_groups_permissions', array('group_id'=>$id));
        if ($q->num_rows() > 0) {
            return array_column($q->result(), 'permission');
        }
        return array();
    }

    public function editGroupPermissions($id, $data) {

        $this->db->delete('user_groups_permissions', array('group_id'=>$id));
        $perms = Array();
        foreach($data as $key => $p) {
            $perms[] = Array("permission" => $key, "group_id" => $id);
        }
        if(count($perms) == 0) return true;
        $q = $this->db->insert_batch('user_groups_permissions', $perms);
        if ($q) return true;        
        return false;
    }

    public function getUserPermissions($id) {

        $q = $this->db->select('permission')->get_where('users_permissions', array('user_id'=>$id));
        if ($q->num_rows() > 0) {
            return array_column($q->result(), 'permission');
        }
        return array();
    }

    public function getUserProperties($id) {

        $q = $this->db->select('property_id')->get_where('users_properties', array('user_id'=>$id));
        if ($q->num_rows() > 0) {
            return array_column($q->result(), 'property_id');
        }
        return array();
    }

    public function editUserPermissions($id, $data, $properties) {

        $this->db->delete('users_permissions', array('user_id'=>$id));
        $this->db->delete('users_properties', array('user_id'=>$id));
        $perms = Array();
        foreach($data as $key => $p) {
            $perms[] = Array("permission" => $key, "user_id" => $id);
        }
        $ps = Array();
        foreach($properties as $key => $p) {
            $ps[] = Array("property_id" => $key, "user_id" => $id);
        }
        if(count($perms) > 0) 
            $q = $this->db->insert_batch('users_permissions', $perms);
        if(count($ps) > 0) 
            $q = $this->db->insert_batch('users_properties', $ps);
        
        return true;
    }

    public function checkPermissions($type = null, $modal = FALSE, $redirect = TRUE)
    {
        $permissions = $this->getPermissions();
        $field = $type;
        if ($permissions == null || (int)$permissions->$field == 0) {
            $this->session->set_flashdata('error', 'Access denied!');
            if ($modal) {
                return false;
            } else {
                if($redirect)redirect('dashboard');
            }
        } else return true;
    }

}
