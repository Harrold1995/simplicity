
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Email extends MY_Controller {
  public function __construct()
  {
      parent::__construct();
      $this->load->model('email_model');
      $this->load->database();
      $this->load->library(array('ion_auth', 'form_validation'));
      $this->load->helper(array('url', 'language'));
      $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

      $this->lang->load('users');
  }
  

  function getModal()
    {
        $params = json_decode($this->input->post('params'));
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'email/sendTenantEmail2';
                $this->data['title'] = 'Send Email';
                $this->load->view('forms/email/tenants', $this->data);

                break;
            case 'get' :

                break;

        }
    }

  function sendTenantEmail2() {
    $this->load->model('email_model');
    $subject = $this->input->post('subject');
    $body = $this->input->post('message');
    $template = $this->input->post('template_id') !== null ? $this->input->post('template_id'): 'basic';
    $tenants = new stdClass();
     if($this->input->post('recipients') == 'profile'){
        $tenantlist  = explode(',', $this->input->post('tenants'));
       $profiles = array(); 
        foreach($tenantlist as $p){
          $profiles[explode('-', $p)[0]] = explode('-', $p)[0];
       }

       $this->db->select('profiles.*');
       $this->db->from('profiles');
       $this->db->where_in('id', $profiles);
       $tenants = $this->db->get()->result(); 
    } 

    if($this->input->post('recipients') == 'property'){
      $tenants  = $this->getActiveTenants(explode(',', $this->input->post('properties')));
    }
    
    $success = array();
    $failure = array();
      foreach($tenants as $p){
  
        if ( $this->email_model->emailActiveTenants( $subject, $body, $p, $template)){
          $success[$p->id] = $p->email;
        } else {
          $failure[$p->id] = $p->email;
        }}
      
       if (!$failure){
          echo json_encode(array('type' => 'success', 'message' => count($success).' emails succesfully sent.'));
      } else {
              echo json_encode(array('type' => 'danger', 'message' => count($failure).' emails not sent<br>'. count($success).' emails succesfully sent.'));
      }  
    

    
  }

   function getActiveTenants($properties = null){
    $today = date('Y-m-d');
    $this->db->select('profiles.*');
    $this->db->from('profiles');
    $this->db->join('leases_profiles lp', 'profiles.id = lp.profile_id');
    $this->db->join('leases', 'leases.id = lp.lease_id');
    $this->db->join('units u', 'u.id = leases.unit_id');
    $this->db->where('(leases.start < "'.$today.'" OR leases.start is null) AND (leases.move_out >=  "'.$today.'" OR  isnull(leases.move_out))');

    if(isset($properties)){
      $this->db->where_in('u.property_id', $properties);
    }

    return $this->db->get()->result();

     
  } 

  function getSupportEmails() {
    $users_all = $this->ion_auth->users()->result();
    $users_with_email = array_filter(
                          $users_all, 
                          function($v, $k) {
                            return (isset($v->email) && !empty($v->email));
                          }, 
                          ARRAY_FILTER_USE_BOTH
                        );
    $support_emails = array_values(array_map(function($v) {
      return array(
        "userId" => $v->id,
        "name" => $v->first_name." ".$v->last_name,
        "email"=> $v->email
      );
    }, $users_with_email));
  
    echo json_encode(array("supportEmails" => $support_emails));
  }

  function sendTenantEmail() {
      $this->load->library('email');
      $subject = $this->input->post('subject');
      $body = $this->input->post('message');

      $result = json_decode($this->input->post('data'),true);

      $user_id =  $this->ion_auth->get_user_id();
      //$user_support = $this->ion_auth->user($user_id)->row();

      //if (!isset($user_support) || empty($user_support->email)) {
        //return;
      //}
      $email_to = 'debbieklar26@gmail.com, debbie@simpli-city.com';//$user_support->email;

      $user = $this->ion_auth->user()->row();

      $data = array(
        'body' => $body,
        'subject' => $subject,
        'from' => $user->email,
        'company' => 'Transition Aquisitins Management',
        'nameto' => 'peter',
        'namefrom' => $user->first_name.' '.$user->last_name,
      );


      $message = $message.$this->load->view('email_templates/basic.php', $data, TRUE);

      //$message = $result[0]['Issue'];


      $email = $this->email
        ->from($user->email)
        ->to($email_to)
        ->subject($subject)
        ->message($message); 
        //->attach($fileName);

      if(!$this->email->send())
      {
          show_error($this->email->print_debugger());
      }

      unlink($fileName);
  }
}