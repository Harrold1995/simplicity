<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Encryption_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
    }

    public function decryptThis($data)
    {
        if($data->routing){
            $data->routing = $this->encryption->decrypt($data->routing);
        }
        if($data->account_number){
            $data->account_number = $this->encryption->decrypt($data->account_number);
        }
        if($data->cc_num){
            $data->cc_num = $this->encryption->decrypt($data->cc_num);
        }
        if($data->password){
            $data->password = $this->encryption->decrypt($data->password);
        }
        if($data->tax_id){
            $data->tax_id = $this->encryption->decrypt($data->tax_id);
        }
        if($data->userId){
            $data->userId = $this->encryption->decrypt($data->userId);
        }
        //$data = $this->encryption->decrypt($data);
        return $data;
    }

    public function encryptThis($data)
    {
        if($data['routing']){
            $data['routing'] = $this->encryption->encrypt($data['routing']);
        }
        if($data['account_number']){
            $data['account_number'] = $this->encryption->encrypt($data['account_number']);
        }
        if($data['cc_num']){
            $data['cc_num'] = $this->encryption->encrypt($data['cc_num']);
        }
        if($data['password']){
            $data['password'] = $this->encryption->encrypt($data['password']);
        }
        if($data['tax_id']){
            $data['tax_id'] = $this->encryption->encrypt($data['tax_id']);
        }
        if($data['user_id']){
            $data['user_id'] = $this->encryption->encrypt($data['user_id']);
        }
        //$data = $this->encryption->decrypt($data);
        return $data;
    }

    function encrypt($table, $column)
    {
            $this->db->select('id,'. $column);
            $this->db->from($table);
            $this->db->limit(5);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
                foreach($data as $account){
                    $plain_text = $account->$column;
                    $account->$column = $this->encryption->encrypt($plain_text);
                    $this->db->update($table, $account, array('id' => $account->id));
                }
                
            }
    }


    
}
