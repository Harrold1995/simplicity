<?php defined('BASEPATH') or exit('No direct script access allowed');

class Communications_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addCommunication($data)
    {
        $this->db->insert('notes', $data);
        return true;
    }

    public function getCommunication($id)
    {
        $q = $this->db->get_where('notes', array('id' => $id));
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    function addNoteForm($id, $type){
        $data;
        $data->objectId = $id;
        $data->target = $type;
        $data->type = 'AddNoteform';
        return $data;
    }

    public function getCommunications($id,$tableId)
    {
        $this->db->select('c.*, CONCAT_WS(" ",p.first_name,p.last_name) as name');
        $this->db->from('communications c');
        $this->db->join('document_types dt', ' c.type_item_id = dt.id', 'left');
        //$this->db->join('users u', ' n.profile_id = u.id', 'left');
        $this->db->join('profiles p', ' c.profile_id = p.id', 'left');
        $this->db->where('c.profile_id', $id);
        //for use later when adding to property page
        //$this->db->where('c.object_id', $id);
        //$this->db->where('n.object_type_id', $tableId);
        $this->db->ORDER_BY('c.sent DESC');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
                return $data;
            }
        return false;
    }

    public function getNote2($id)
    {
        $this->db->select('n.name');
        $this->db->from('notes n');
        $this->db->where('n.id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    }

}
