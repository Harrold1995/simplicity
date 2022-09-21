<?php defined('BASEPATH') OR exit('No direct script access allowed');

class SendEmail extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    function index()
    {
        $this->meta['title'] = "Send Email";
        $this->meta['h2'] = "Send Email";
        //$this->page_construct('journalIndex/index', null, $this->meta);
    }

    function sendEmail()
    {
        $this->load->library('email');
        //used to get users email
        $this->load->model('email_model');
            //$emailw = $this->input->post();
            $email = $this->input->post('send_email');
            $to = $email['to'];
            $body = $email['message'];

            $subject = $email['subject'];
            //$message = '<p>This message has been sent for testing purposes.</p>';

            // Get full html:
           
            // Also, for getting full html you may use the following internal method:
            //$body = $this->email->full_html($subject, $message);

            $result = $this->email
                ->from('rafael@simpli-city.com')//not sending from here
                ->reply_to('rafael@simpli-city.com')    // Optional, an account where a human being reads.
                ->to($to)
                ->subject($subject)
                ->message($body)
                //->attach(base_url() . "uploads/images/bank.png")
                ->attach(base_url() . "uploads/documents/report2.pdf")
                ->send();

                if($result){
                    $returnMessage['type'] = 'success';
                    $returnMessage['message'] = 'Email succesfully sent!';
                }else{
                    $returnMessage['type'] = 'danger';
                    $returnMessage['message'] = 'Email not sent!';
                }
                echo json_encode($returnMessage);
            // var_dump($result);
            // echo '<br />';
            // echo $this->email->print_debugger();

            //exit;
    }


    
}


