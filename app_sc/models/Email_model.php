<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Email_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->getUser();
    }

    function getUser(){
            //gets the users email and app password
            $this->load->library('email');
            //$userId = $this->ion_auth->get_user_id();
            //$userInfo = $this->getUserInfo($userId);
            $config['smtp_user'] = $this->session->userdata('email');
            $config['smtp_pass']    = $this->session->userdata('email_pass');
            $this->email->initialize($config);
    }

    /* function getUserInfo($id){
        $this->db->select('email, email_password');
        $this->db->from('users');
        $this->db->where('id ', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;
    } */

    function emailNotifications($subject, $body, $profile = 'joshua@simpli-city.com')
    {
        $this->load->library('email');
        //$this->load->model('email_model');
            $result = $this->email
                ->from('rafael@simpli-city.com')
                //->reply_to($this->session->userdata('email'))    // Optional, an account where a human being reads.
                //->to('debbie@simpli-city.com')
                ->to($profile)
                ->subject($subject)
                ->message($body)
                ->send();

                return $result;
    }

    function emailActiveTenants($subject, $body, $profile, $template = 'basic')
    {
        $this->load->library('email');
        $email_user = $this->db->get_where('users', array('id' => $this->site->settings->tenant_notification_user))->row();
        $config['smtp_user'] = $email_user->email;
        $config['smtp_pass'] = $email_user->email_password;
        $this->email->initialize($config);
        $data = array(
            'company_name' => $this->site->settings->company_name,
            'company_phone' => $this->site->settings->company_phone,
            'company_email' => $this->site->settings->company_email,
            'company_logo' => $this->site->settings->company_logo,
            'message' => $body,
            'title' => $subject
        );
        if($template != 'plain'){
            $parsedEmail = $this->load->view('email_templates/'.$template, $data, TRUE);
        } else {
            $parsedEmail = $body;
        }
        //$this->load->model('email_model');
            //$result = $this->email
            $result = $this->email
						->from($this->session->userdata('email'))   
						//->from($companySettings->company_email)             
						//->reply_to($companySettings->company_email)    // Optional, an account where a human being reads.
						//->to('debbie@simpli-city.com')
						->to($profile->email) 
						->subject($subject)
						->message($parsedEmail)
                        ->send();
                        

                if($result == false)
                {
                    //show_error($this->email->print_debugger());
                    return false;
                } else {
                    $data = array(
                        'profile_id' => $profile->id,
                        'type' => 'email',
                        'subject' => $subject,
                        'body' => $body,
                        'sent' => date("Y-m-d H:i:s"),
                        'options' => json_encode(array(
                            'from' => $this->session->userdata('email'),
                            'template' => 'basic'
                        ))
                     );
                    
            
            
                    $this->db->insert('communications', $data);
                    return true;
                }
    }
}