<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

	public function getDefKey()
	{
		$q = $this->db->get_where('property_keys', Array('property_id' => 0));
		if ($q->num_rows() > 0){
			return $q->row();
		}
		return array();
	}

	public function savePkeys($data)
	{
		$data = $data['pkeys'];
		$ids = array_map (function($value){
			return $value->property_id;
		} , $this->db->get('property_keys')->result());
		foreach($data as $i => $key) {
			echo $key;
			if(in_array($i, $ids)) {
				$this->db->update('property_keys', Array('key' => trim($key['key']), 'ikey' => trim($key['ikey'])), Array('property_id' => $i));
			} else if (trim($key['key']) !='' || trim($key['ikey']) !='') {
				$this->db->insert('property_keys', Array('property_id' => $i, 'key' => trim($key['key']), 'ikey' => trim($key['ikey'])));
			}
		}
	}

	public function getProperties()
	{
		$q = $this->db->join('property_keys pk', 'p.id = pk.property_id', 'left')->get('properties p');
		if ($q->num_rows() > 0){
			return $q->result();
		}
		return array();
	}

    public function getLTemplates($data = null)
    {
        if (!isset($data))
            $q = $this->db->get('lease_templates');
        else
            $q = $this->db->get_where('lease_templates', $data);
        if ($q->num_rows() > 0){
            return $q->result();
        }
        return array();
    }

    public function addLTemplate($data)
    {
        $this->db->insert('lease_templates', $data);
        return true;
    }

    public function editLTemplate($data, $id)
    {
        $this->db->update('lease_templates', $data, array('id' => $id));
        return true;
    }

    public function deleteLTemplate($id)
    {
        $this->db->delete('lease_templates', Array("id" => $id));
        return true;
    }

    public function getSettingDetails($key)
    {
        $q = $this->db->get_where('settings_details', array('key' => $key), 1);
        if ($q->num_rows() > 0){
            return $q->row();
        }
        return array();
    }
    
    public function addSettingDetails($data)
    {
        $this->db->insert('settings_details', $data);
        return true;
    }

    public function deleteSettingDetails($key)
    {
        $this->db->delete('settings_details', Array("key" => $key));
        return true;
    }

    public function editSettingDetails($data, $key)
    {
        $q = $this->getSettingDetails($key);
        if(count($q) == 0) $this->addSettingDetails($data);
        else $this->db->update('settings_details', $data, array('key' => $key));
        return true;
    }
}
