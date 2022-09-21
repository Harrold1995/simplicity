<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Memorized {

        
            protected $CI;

            // We'll use a constructor, as you can't directly call a function
            // from a property definition.
            public function __construct()
            {
                    // Assign the CodeIgniter super-object
                    $this->CI =& get_instance();
            }
            function editCheck1($tid = 0)
            {
                $this->CI->load->model('checks_model');
        
                $header = $this->CI->input->post('header');
                $headerTransaction = $this->CI->input->post('headerTransaction');
                $transactions = $this->CI->input->post('transactions');
                $special = $this->CI->input->post('checks');
                $deletes = $this->CI->input->post('delete');
               
                //$this->form_validation->set_rules($this->settings->accountFormValidation);
                if (/*$this->form_validation->run() &&*/ $this->CI->checks_model->editCheck($header, $headerTransaction, $transactions, $special, $tid, $deletes))
                    echo json_encode(array('type' => 'success', 'message' => 'Check successfully edited.'));
                /*else {
                    echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('transaction')));
                }*/
            }
            
        
}