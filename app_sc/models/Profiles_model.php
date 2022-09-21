<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Profiles_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('encryption_model');
    }

    public function addProfile($data)
    {
                $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['upload_path'] = 'uploads/images';
        $this->load->library('upload', $config);
        $this->upload->do_upload('image');
        $data['image'] = $this->upload->data('file_name');

        $data['phone'] = preg_replace('/\D+/', '', $data['phone']);
        $data['tax_id'] = preg_replace('/\D+/', '', $data['tax_id']);
        $data['contact_methods'] = $this->contactMethod($data);
        $data = $this->encryption_model->encryptThis($data);
        $this->db->insert('profiles', $data);
        $pid = $this->db->insert_id();
        //$this->db->insert('documents', Array("name" => $data['image'], "reference_id" => $pid, "type" => "6"));
        return $pid;
    }

    public function editProfile($data, $contacts = null, $addresses = null, $pid, $deletes, $delete)
    {
        if($deletes){
            $response = $this->checkItems($deletes, $delete);
               if($delete == NULL) return $response;
           }
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['upload_path'] = 'uploads/images';
        $this->load->library('upload', $config);
        $this->upload->do_upload('image');
        if($this->upload->data('file_name') != ""){
            $data['image'] = $this->upload->data('file_name');
            //$this->db->insert('documents', Array("name" => $data['image'], "reference_id" => $pid, "type" => "6"));
        }

        $data['phone'] = preg_replace('/\D+/', '', $data['phone']);
        $data['tax_id'] = preg_replace('/\D+/', '', $data['tax_id']);
        $data['contact_methods'] = $this->contactMethod($data);
        unset($data['id']);

        foreach ($contacts as &$contact) {
            if(array_key_exists('id', $contact)){
                $this->db->update('contacts', $contact, array('id' => $contact['id']));
            }else{
                $this->db->insert('contacts', $contact);
                $cid = $this->db->insert_id();
                $profile_contacts["profile_id"] = $pid;
                $profile_contacts["contact_id"] = $cid;
                $this->db->insert('profile_contact', $profile_contacts);
            }
        }

        foreach ($addresses as &$address) {
            if(array_key_exists('id', $address)){
                $this->db->update('profile_address', $address, array('id' => $address['id']));
            }else{
                $address['profile_id'] = $pid;
                $this->db->insert('profile_address', $address);
            }
        }

        
        $data = $this->encryption_model->encryptThis($data);
        if ($this->db->update('profiles', $data, array('id' => $pid)))
            return true;
    }

    public function getProfiles($params = null)
    {
        if (!isset($params)) $q = $this->db->get('profiles');
        else
            $q = $this->db->get_where('profiles', $params);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getProfile($id)
    {
        $q = $this->db->select('*,CONCAT_WS(" ",first_name, last_name) as name')->get_where('profiles', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            $data = $q->row();
            $data = $this->encryption_model->decryptThis($data);
            return $data;
        }
        return null;
    }

    public function contactMethod($data)
    {
        $contactMethods = "";
        foreach($data['contact_methods'] as $cmethod){
                $contactMethods = $contactMethods . $cmethod . ',';
        }
        $contactMethods = rtrim($contactMethods, ',');
        return $contactMethods;
    }
}
