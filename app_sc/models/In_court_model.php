<?php defined('BASEPATH') OR exit('No direct script access allowed');

class in_court_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notes_model');
    }

    public function getIn_court()
    {
        $custNames = '(SELECT GROUP_CONCAT( CONCAT_WS(" ", first_name, last_name)) as Name, leases.id as lease FROM
        leases JOIN leases_profiles on leases_profiles.lease_id = leases.id JOIN profiles on leases_profiles.profile_id =profiles.id group by leases.id)';

        $this->db->select('in_court.id, cn.name as Name, p.name as Property, u.name as unit, case_num, attorney, follow_up_date AS "Follow up date", follow_up_reason AS "Follow up reason", warrant_requested AS "Warrant requested", warrant_issued AS "Warrant issued" ');
        $this->db->from('in_court');
        $this->db->join('leases l', 'in_court.lease_id = l.id');
        $this->db->join('units u', 'l.unit_id = u.id');
        $this->db->join('properties p', 'u.property_id = p.id');
        $this->db->join($custNames.' cn ', 'cn.lease = l.id');

        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $row->notes = $this->notes_model->getNotes($row->id,16);//number corresponds to database
                $data[] = $row;
            }
            return $data;
        }
        return null;


    }

    function editIn_court($data){
        $passed = true;
        foreach ($data as $singleData){
            $inCourtId = $singleData['id'];
            unset($singleData['id']);
            //unset($singleData['undefined']);
            $result = $this->db->update('in_court', $singleData, array('id' => $inCourtId));
            if(!$result){$passed = false;}     
        }
        return $passed;
    }
}
