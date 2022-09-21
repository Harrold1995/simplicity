
<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback extends MY_Controller {
  public function __construct()
  {
      parent::__construct();
      $this->load->database();
      $this->load->library(array('ion_auth', 'form_validation'));
      $this->load->helper(array('url', 'language'));
      $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

      $this->lang->load('users');
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

  function sendEmail() {
      $this->load->library('email');
      $result = json_decode($this->input->post('data'),true);

      $user_id = $result[2]['userId'];
      $user_support = $this->ion_auth->user($user_id)->row();

      if (!isset($user_support) || empty($user_support->email)) {
        return;
      }
      $support_email = $user_support->email;

      $user = $this->ion_auth->user()->row();

      $message = $result[0]['Issue'];
      $img = $result[1];
      $img = $result[1];
      $img = str_replace('data:image/png;base64,', '', $img);
      $img = str_replace(' ', '+', $img);
      $fileData = base64_decode($img);
      $fileName = dirname(__FILE__).'/../../temp/image_' . date('Y-m-d-H-i-s') . '_' . uniqid() . '.png';
      file_put_contents($fileName, $fileData);

      $email = $this->email
        ->from($user->email)
        ->to($support_email)
        ->subject('Simpli-city: feedback from '.$user->first_name.' ('.$user->email.')')
        ->message($message)
        ->attach($fileName);

      if(!$this->email->send())
      {
          show_error($this->email->print_debugger());
      }

      unlink($fileName);
  }
}