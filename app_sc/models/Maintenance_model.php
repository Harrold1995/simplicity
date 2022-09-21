<?php defined('BASEPATH') or exit('No direct script access allowed');

class Maintenance_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addNote($data)
    {
        $this->db->insert('notes', $data);
        return true;
    }

    public function getNote($id)
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

    public function getNotes($id,$tableId)
    {
        $this->db->select('n.*, CONCAT_WS(" ",p.first_name,p.last_name) as name');
        $this->db->from('notes n');
        $this->db->join('document_types dt', ' n.object_type_id = dt.id');
        $this->db->join('users u', ' n.profile_id = u.id', 'left');
        $this->db->join('profiles p', ' u.profile_id = p.id', 'left');
        $this->db->where('n.object_id', $id);
        $this->db->where('n.object_type_id', $tableId);
        $this->db->ORDER_BY('n.note_date DESC');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
                return $data;
            }
        return false;
    }

    public function getMaintenance($id)
    {
        $this->db->select('m.*, CONCAT_WS(" ",p.first_name,p.last_name) as name');
        $this->db->from('maintenance m');
        //$this->db->join('document_types dt', ' n.object_type_id = dt.id');
        $this->db->join('profiles p', 'created_by = p.id', 'left');
        $this->db->where('m.property', $id);
        //$this->db->where('n.object_type_id', $tableId);
        $this->db->ORDER_BY('create_date DESC');
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
