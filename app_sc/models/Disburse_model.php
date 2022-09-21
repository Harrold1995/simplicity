<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Disburse_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function submitCapitals($capitals,$asOf, $type, $due){
        $printBody = '';
        $investors = [];
        $test = [];
        $q = $this->db->query("select group_concat(property_id) as properties, group_concat(percentage) as percentage, profiles.first_name, profiles.last_name from property_owners 
                               inner join profiles on profiles.id = property_owners.profile_id
                               group by profiles.first_name, profiles.last_name" );
        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $row->properties = explode(',', $row->properties);
                $row->percentage = explode(',', $row->percentage);
                $investors[] = $row;
            }
        }else{
            echo "capitalFunction";
        }

        foreach($investors as $investor){
            $propCount = 0;
            $properties = $investor->properties;
            foreach($investor->properties as $key => $property){
                
                if(isset($capitals[$property])){
                    $investor->capitals[$property] = $capitals[$property];
                    $investor->capitals[$property]['percent'] = $investor->percentage[$key];
                    $investor->as_of = $asOf;
                    $investor->due = $due;
                    $propCount = 1;
                }
                
            }
            if ($propCount == 1){
                if ($type == 'Disbursements'){
                    $printBody = $printBody . $this->load->view('forms/disburse/print_template.php', $investor, TRUE);
                } else {
                    $printBody = $printBody . $this->load->view('forms/capital/print_template.php', $investor, TRUE);
                }
               
            }
            
        }

          return $printBody;
    }

}
