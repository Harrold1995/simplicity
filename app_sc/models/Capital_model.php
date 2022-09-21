<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Capital_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function submitCapitals($capitals){
        $printBody = '';
        foreach($capitals as $capital){
            //$this->data['capital'] = $capital;
             $printBody = $printBody . $this->load->view('forms/capital/print_template.php', $capital, TRUE);
          }
          return $printBody;
    }

}
