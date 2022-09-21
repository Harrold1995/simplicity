<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Entities_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getEntity($id)
    {
        //$this->db->where('active', 1);
        $q = $this->db->get_where('entities', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }



    public function getAllEntities($addtype = false)
    {
        //$this->db->where('active', 1);
        //$q = $this->db->get('entities');
        $this->db->select('e.id, e.name, CONCAT_WS(" ", e.address, e.address2) AS address, e.city, e.state, e.zip, e.email, e.phone, e.tax_id, e.city, e.description, e.closing_date');
        $this->db->from('entities e'); 
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'entities';
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function editEntity($data)
    {
        $eid = $data['id'];
        unset($data['id']);
        if ($this->db->update('entities', $data, array('id' => $eid)))
            return true;
    }

    public function addEntity($data)
    {
        $this->db->insert('entities', $data);
        return true;
    }
    
}
