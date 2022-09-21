<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Recordsets_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getRecordsets()
    {
        $result = Array();
        $q = $this->db->get('report_types');
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $type)
                $result[$type->id] = $type;
        }
        return $result;
    }

    public function getRecordset($id)
    {
        $result = array();
        $q = $this->db->get_where('report_types', array('id' => $id), 1);
        if ($q->num_rows() > 0){
            $result['set'] = $q->row();
        }
        $q = $this->db->get_where('report_columns', array('type_id' => $id));
        if ($q->num_rows() > 0){
            $result['columns'] = $q->result();
        }
        return $result;
    }


    public function addRecordset($data, $fields)
    {
        $this->db->insert('report_types', $data);
        $id = $this->db->insert_id();
        foreach($fields as &$field) {
            $field['type_id'] = $id;
            $this->db->insert('report_columns', $field);
        }
        return true;
    }

    public function editRecordset($data, $fields, $delete, $id)
    {
        $this->db->update('report_types', $data, array('id' => $id));
        foreach($fields as &$field) {
            $field['type_id'] = $id;
            $fid = $field['id'];
            unset($field['id']);
            if($fid)
                $this->db->update('report_columns', $field, array('id' => $fid));
            else
                $this->db->insert('report_columns', $field);
        }
        foreach($delete as $id) {
            $this->db->delete('report_columns', array('id' => $id));
        }
        return true;
    }

    public function deleteRecordset($id)
    {
        $this->db->delete('report_types', Array("id" => $id));
        return true;
    }
}
