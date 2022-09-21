<?php defined('BASEPATH') OR exit('No direct script access allowed');
include('Profiles_model.php');

class Investors_model extends Profiles_model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function addInvestor($data)
    {
        $data['profile_type_id'] = 5;
        return parent::addProfile($data);
    }

    public function editInvestor($data, $contact, $address, $tid, $deletes, $delete)
    {
        return parent::editProfile($data, $contact, $address, $tid, $deletes, $delete);
    }


    public function getAllInvestors()
    {
        $this->db->select('p.*, CONCAT_WS("",p.first_name," ",p.last_name) as name');
        $this->db->from('profiles p');
        $this->db->join('profile_types pt', 'pt.id = p.profile_type_id');
        $this->db->where("p.profile_type_id",5);
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getInvestor($Iid)
    {
        $this->db->select('pr.*, CONCAT(pr.first_name," ",pr.last_name) as name');
        $this->db->from('profiles pr');
        $this->db->where("pr.id=".$Iid)->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $data = $q->row();
            $data = $this->encryption_model->decryptThis($data);
            return $data;
        }
        return array();
    }

    public function getContacts($id){

        $this->db->select('c.*');
        $this->db->from('contacts c');
        $this->db->join('profile_contact pc', 'pc.contact_id = c.id');
        $this->db->join('profiles p', 'p.id = pc.profile_id');
        $this->db->where('p.id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getAddresses($id){

        $this->db->select('a.*');
        $this->db->from('profile_address a');
        $this->db->where('a.profile_id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }
}
