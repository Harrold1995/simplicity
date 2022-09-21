<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ReportsApi extends MY_Controller
{
    function __construct()
    {
        $this->icons = Array("property" => "fas fa-building", "unit" => "far fa-building", "tenant" => "far fa-user");
        parent::__construct();
    }

    function getPropertiesList()
    {
        $data = array();
        $selected = $this->input->post('selected');
        $this->db->where('active', 1);
        $q = $this->db->get('properties');
        if ($q->num_rows() > 0) {
            $data = $q->result();
        }
        echo "<select class='editable-select'>";
        foreach ($data as $d) {
            echo "<option ".((trim(strtolower($d->name)) == trim(strtolower($selected))) ? "selected" : "") ." value='" . $d->id . "'>" . $d->name . "</option>";
        }
        echo"</select>";
    }

    function getAccountsList()
    {
        $data = array();
        $selected = $this->input->post('selected');
        $this->db->where('active', 1);
        $q = $this->db->get('accounts');
        if ($q->num_rows() > 0) {
            $data = $q->result();
        }
        echo "<select class='editable-select'>";
        foreach ($data as $d) {
            echo "<option ".((trim(strtolower($d->name)) == trim(strtolower($selected))) ? "selected" : "") ." value='" . $d->id . "'>" . $d->name . "</option>";
        }
        echo"</select>";
    }

}
