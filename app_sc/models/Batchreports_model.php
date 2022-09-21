<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Batchreports_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getBatchReport($id) {
        $q = $this->db->get_where('batch_reports', 'id = '.$id, 1);
        $result = null;
        if($q->num_rows() > 0) {
            $result = $q->row();
            $result->settings = json_decode($result->settings);
            $result->settings->id = $id;
        }
        return $result;
    }

    public function getBatchList() {
        $q = $this->db->select('id, name')->get('batch_reports');
        $result = null;
        if($q->num_rows() > 0) {
            $result = $q->result();
        }
        return $result;
    }
    public function getReportList() {
        $q = $this->db->select('id, name, settings, id as report_id')->get('reports');
        $result = null;
        if($q->num_rows() > 0) {
            $result = $q->result();
        }
        return $result;
    }

    public function saveBatch($id, $settings, $name) {
        $this->db->set('settings', $settings)->set('name', $name)->where('id', $id)->update('batch_reports');
    }
}
