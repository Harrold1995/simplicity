<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    function index()
    {
        $this->meta['title'] = "Invoice";
        $this->meta['h2'] = "Invoice";
        //$this->page_construct('journalIndex/index', null, $this->meta);
    }
    
    public function printInvoice($lease, $profile)
    {   
        //$accounts1 = $this->input->get('accounts');
        //$leases = $this->input->post('leases');
        //$leaseId = $lease;
        $this->load->model('charges_model');
        //$data = $this->charges_model->getLeases($leaseId);
        $data = $this->charges_model->getProfiles($lease, $profile);
        //$data = json_encode($this->charges_model->getTenants([2]));
        //echo $data;
        return $data;

    }

    function getModal()
    {
        $params = json_decode($this->input->post('params'));
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'invoice/addInvoice';
                $this->data['title'] = 'Add Invoice';
                //$this->data['invoices'] = $this->printInvoice($this->input->post('id'));
                $this->data['invoices'] = $this->printInvoice();
                $this->load->view('forms/invoice/main', $this->data);

                break;
            case 'get' :
                $this->data['target'] = 'invoice/addInvoice' . $params;
                $this->data['title'] = 'Add Invoice';
                $this->data['invoices'] = $this->printInvoice($params);
                $this->load->view('forms/invoice/main', $this->data);

                break;
            case 'choose' :
                $this->load->model('charges_model');
                $this->data['target'] = 'invoice/sendInvoices' . $params;
                $this->data['title'] = 'Add Invoice';
                $this->load->model('leases_model');
                $this->data['leases'] = $this->getAllLeases();
                $this->data['profiles'] = $this->charges_model->getAllProfiles();
                //$this->data['leases'] = $this->leases_model->getLeasesList();
                $this->load->view('forms/invoice/email_invoice', $this->data);
                break;
            case 'statement' :
                $this->load->model('charges_model');
                $this->data['target'] = 'invoice/sendInvoices' . $params;
                $this->data['title'] = 'Add Invoice';
                //$this->data['invoices'] = $this->printInvoice($params->tenant, $params->lease);
                $this->data['invoices'] = $this->charges_model->getLeases($params->id, $params->type);
                $this->data['info']->id = $params->id;
                $this->data['info']->type = $params->type;
                if ($this->site->settings->use_management_for_statement == 1){
                    $this->data['invoices']['eName'] = $this->site->settings->company_name;
                    $this->data['invoices']['eAddress'] = $this->site->settings->company_address;
                    $this->data['invoices']['eCity'] = $this->site->settings->company_city;
                    $this->data['invoices']['Ezip'] = $this->site->settings->company_state;
                    $this->data['invoices']['eName'] = $this->site->settings->company_zip;
                    $this->data['invoices']['eemail'] = $this->site->settings->company_email;
                    $this->data['invoices']['ePhone'] = $this->site->settings->company_phone;
                }
                // $this->data['leases'] = $this->getAllLeases();
                // $this->data['profiles'] = $this->charges_model->getAllProfiles();
                // //$this->data['leases'] = $this->leases_model->getLeasesList();
                //$printBody = $this->load->view('forms/invoice/print_template.php', $this->data, TRUE);
                 $this->load->view('forms/invoice/main3', $this->data);
                break;
        }
    }

    function getFilteredStatements(){
        $this->load->model('charges_model');
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $date = $this->input->post('date');
        $startDate = $this->input->post('date2');
        $params = json_decode($this->input->post());
        $this->data['date'] = $date;
        $this->data['startDate'] = $startDate;
        $this->data['invoices'] = $this->charges_model->getLeases($id, $type, $date, $startDate);
        $invoice_statement = $this->load->view('forms/invoice/invoice_statement.php', $this->data, TRUE);
        echo json_encode($invoice_statement);
    }

    function sendInvoices(){
        $trs = [];
        $invoices = [];
        //$message['court'] = 'In court successfully updated!';
        $courtMessage = ['type' => 'success', 'message' => "In court successfully updated!"];
        $params = $this->input->post('params');
        $params2 = json_decode($params);
        foreach($params2 as $param){
            $this->data['invoices'] = $this->printInvoice($param->lease_id, $param->profile_id);
            $invoice = $this->data['invoices'];
            if($param->email == 1){
                $result = $this->sendEmail($invoice, $param->profile_id);
                if($result){
                    array_push($trs, array('type' => 'success', 'message' => "", 'id' => $param->profile_id. '-'.  $param->lease_id));
                }else{
                    array_push($trs, array('type' => 'danger', 'message' => "No email address!", 'id' => $param->profile_id. '-'.  $param->lease_id));
                }
            }
            if($param->print == 1){
                array_push($invoices, $invoice);
            }
            //if($param->court){
                $in_court = ['in_court' => $param->court];
                $this->db->update('leases', $in_court, array('id' => $param->lease_id));
                $query = $this->db->select(1)
                ->where('lease_id', $param->lease_id)
                ->get('in_court')->result();
                if($query[0] > 0 ){
                    // $ic['lease_id'] = $lid;
                    // $this->db->update('in_court', $ic, array('lease_id' => $lid));
                }else{
                    $courtResult = $this->db->insert('in_court',  array('lease_id' => $param->lease_id));
                    if($courtResult == false){
                        array_push($courtMessage, array('type' => 'danger', 'message' => "In court failed to update!"));
                    }
                }
                
            //}
        }
        if(!empty($invoices)){
            $printBody = '';
            foreach($invoices as $invoice){
                $this->data['invoice'] = $invoice[0];
                 $printBody = $printBody . $this->load->view('forms/invoice/print_template.php', $this->data, TRUE);
              }
              $message['print'] = $printBody;
            //   echo $aa;
        }
        $message['emailTrs'] = $trs;
        $message['court'] = $courtMessage;
        echo json_encode($message);
        //$this->load->view('forms/invoice/main2', $this->data);

    }

    function sendEmail($data, $profile)
    {
        $this->load->library('email');
            //used to get users email
            $this->load->model('email_model');
            $this->data['invoice'] = $data[0];
            $body = $this->load->view('forms/invoice/email_template.php', $this->data, TRUE);
            $profile = $this->getTenantEmail($profile);
            $subject = 'This is a test';
            //used to get users email
            $this->load->model('email_model');
            //$message = '<p>This message has been sent for testing purposes.</p>';

            // Get full html:
           
            // Also, for getting full html you may use the following internal method:
            //$body = $this->email->full_html($subject, $message);

            $result = $this->email
                ->from('rafael@simpli-city.com')
                ->reply_to($this->session->userdata('email'))    // Optional, an account where a human being reads.
                //->to('debbie@simpli-city.com')
                ->to('ycraven@simpli-city.com')
                //->to($profile)
                ->subject($subject)
                ->message($body)
                ->send();

                return $result;

            // var_dump($result);
            // echo '<br />';
            // echo $this->email->print_debugger();

            //exit;
    }
        //used to get tenants and leases emails
        function sendInvoice()
    {       $attachment = $this->input->post('attachment');
            $returnMessage['type'] = 'success';
            $returnMessage['message'] = 'Email succesfully sent!';
            //$this->load->library('email');
            //used to get users email
            $this->load->model('email_model');
            //$this->data['invoice'] = $data;
            $params = $this->input->post('params');
            $id = $this->input->post('id');
            $type = $this->input->post('type');
            
            if($type == 'tenant'){
                $profile = new stdClass();
                if (strpos($id, ',')){
                    $id = $str_arr = explode (",", $id);
                    $profile = $this->getEmailsByProfile($id);
                    $profile = explode (";", $profile);
                } else {
                    $profile = $this->getTenantEmail($id);
                }               
                $result = $this->sendInvoiceEmail($params, $profile, $attachment);
                if($result == false){
                    $returnMessage['type'] = 'danger';
                    $returnMessage['message'] =  $profile ;
                }
            }
            if($type == 'lease'){

                
                    $count = 0;
                    $profiles = $this->getAllTenantsEmail($id);
                    foreach($profiles as $profile){
                        $profile = $this->getTenantEmail($profile->profile_id);
                        $result = $this->sendInvoiceEmail($params, $profile,$attachment);
                        if($result == false){$count++;}
                    
                    if($count > 0){
                        $returnMessage['type'] = 'danger';
                        $returnMessage['message'] = $count . ' email not sent!';
                    }
                }

            }
            echo json_encode($returnMessage);
            
    }
    //used to send statements for tenants and leases
    function sendInvoiceEmail($params, $profile, $attachment)
    {
            $this->load->library('email');
            $config['smtp_user'] = $this->session->userdata('email');
            $config['smtp_pass'] = $this->session->userdata('email_pass');
            $this->email->initialize($config);

            $body = json_decode($params);
            $body ='<table style="background-color: aliceblue; width:100%" ><tr><td style ="width: 10%;"></td><td><div style ="background-color: white; width:775px; box-shadow: #e4e9ed 5px 10px 10px; padding:25px; margin:25px;">'.json_decode($params).'</div></td></tr></table>';

            $subject = 'Rent Statement';
            //if($profile != ""){
                $result = $this->email
                    ->from($this->session->userdata('email'))
                    //->reply_to('rafael@simpli-city.com')    // Optional, an account where a human being reads.
                    //->to('debbie@simpli-city.com')
                    ->to($profile)
                    ->subject($subject)
                    ->message($body)
                    ->attach($attachment)
                    ->send();
            //}
            return $result;

    }

    public function getAllLeases()
    {
        $this->db->select('l.id, CONCAT(l.start," ",l.end) as name, p.name as property, u.name as unit, SUM(t.debit - t.credit) as balance');
        $this->db->from('leases l');
        $this->db->join('units u', 'l.unit_id = u.id');
        $this->db->join('properties p', 'u.property_id = p.id');
        $this->db->join('transactions t', 'l.id = t.lease_id AND t.account_id ='. $this->site->settings->accounts_receivable);
        $this->db->group_by('l.id');
        $this->db->having('balance != 0');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            // foreach (($q->result()) as &$row) {
            //     //$row->type = 'lease';
            //     // $row->name = $row->start . ' - ' . $row->end;
            // }
            return $q->result();
        }
        return null;
    }

    function getTenantEmail($profile){
        $query = $this->db->select('email')->where('id', $profile)->get('profiles');
        return $query->row()->email; 
    }

    function getEmailsByProfile($profileIdArray){
        $this->db->select('group_concat(email SEPARATOR ";") as email');
        $this->db->from('profiles');
        $this->db->where_in('id', $profileIdArray);
        $q = $this->db->get();
        return $q->row()->email; 
    }

    function getAllTenantsEmail($lease){
        $query = $this->db->select('profile_id')->where('lease_id', $lease)->get('leases_profiles');
        return  $query->result();
    }

    // function getUserInfo($id){
    //     $this->db->select('email, email_password');
    //     $this->db->from('users');
    //     $this->db->where('id ', $id);
    //     $q = $this->db->get();
    //     if ($q->num_rows() > 0) {
    //         return $q->row();
    //     }
    //     return null;
    // }
    
}


