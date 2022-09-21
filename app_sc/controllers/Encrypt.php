<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Encrypt extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
    }

    function index()
    {
        $this->meta['title'] = "Encrypt";
        $this->meta['h2'] = "Encrypt";
        //$this->page_construct('Employees/index', null, $this->meta);
    }
    
    function encrypt($table, $column)
    {
            $this->db->select('id,'. $column);
            $this->db->from($table);
            // $this->db->limit(5);
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

        function getModal(){
            switch ($this->input->post('mode')) {
                case 'banks' :
                     $this->encrypt('banks', 'account_number');
                    $this->encrypt('banks', 'routing');
                    break;
                case 'cc' :
                   
                   
                    break;
            }
    
            //$this->load->view('forms/account/main', $this->data);
            
        }
}
