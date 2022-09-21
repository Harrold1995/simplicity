<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bills extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    function index()
    {
        $this->meta['title'] = "Bills";
        $this->meta['h2'] = "Bills";
        $this->page_construct('journalIndex/index', null, $this->meta);
    }

    function test()
    {
        //echo 5;
        //echo $GLOBALS['AR'];
        echo $this->AR;
       // defined('AR')                  OR define('AR',451);


//$GLOBALS['variable'] = 'my stuff';
// $this->AR = 'my test'; in my_controller

    }

    function sec()
    {
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
    );
    print_r($csrf);
    }


    function addBill()
    {   
        $this->load->model('bills_model');
        
        $errors = "";
        $header = $this->input->post('header');
        $headerTransaction = $this->input->post('headerTransaction');
        $transactions = $this->input->post('transactions');
        $special = $this->input->post('bills');
        $data = array('header' => $header, 'transactions' => $transactions);
        $validate = $this->validate_model->validate("transactions", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/$validate['bool'] && $this->bills_model->addbill($header, $headerTransaction, $transactions, $special))
            echo json_encode(array('type' => 'success', 'message' => 'Bill successfully added.'));
        else {
            $errors = $errors . /*validation_errors() .*/"</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function editBill($tid = 0)
    {
        $this->load->model('bills_model');

        $header = $this->input->post('header');
        $headerTransaction = $this->input->post('headerTransaction');
        $transactions = $this->input->post('transactions');
        $special = $this->input->post('bills');
        $data = array('header' => $header, 'transactions' => $transactions, 'special' => $special, 'headerTransaction' => $headerTransaction);
        //$validate = $this->validate_model->validate("transactions", $data);
        $validate = $this->validate_model->validate("bills", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/$validate['bool'] && $this->bills_model->editBill($header, $headerTransaction, $transactions, $special, $tid))
            echo json_encode(array('type' => 'success', 'message' => 'Bill successfully edited.'));
        else {
            $errors = $errors . /*validation_errors() .*/"</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function getModal()
    {
        $this->load->model('bills_model');
        $this->load->model('checks_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'bills/addBill';
                $this->data['title'] = 'Add Bill';
                $this->data['classes'] = $this->bills_model->getClasses();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['properties'] = $this->bills_model->getProperties();
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['accounts'] = $this->bills_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->bills_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['jUnits'] =  json_encode($this->data['units']);
                $this->data['names'] = $this->bills_model->getNames();
                $this->data['jNames'] =  json_encode($this->data['names']);
                $this->data['jTransactions']= json_encode($this->data['transactions']);
                $this->data['jPropertyAccounts'] =  json_encode($this->checks_model->getPropertyAccounts2());


                //return $this->bills_model->getAccounts($pid);
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                break;
            case 'edit' :
                $this->data['target'] = 'bills/editBill/' . $this->input->post('id');
                $this->data['title'] = 'Edit Bill';
                $this->data['classes'] = $this->bills_model->getClasses();
                $this->data['properties'] = $this->bills_model->getProperties();
                $this->data['accounts'] = $this->bills_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['units'] = $this->bills_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['header'] = $this->bills_model->getHeader($this->input->post('id'));
                $transactions = $this->bills_model->getTransactions($this->input->post('id'));
                //$transactions =  $this->data['allTransactions'];
                $headerTransaction = array_shift($transactions);
                $this->data['transactions'] =   $transactions;    
                $this->data['headerTransaction'] =   $headerTransaction;  
                
                $this->data['bills'] = $this->bills_model->getBill($this->input->post('id'));
                $this->data['names'] = $this->bills_model->getNames();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                //$this->data['accounts'] = $this->bills_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['jUnits'] =  json_encode($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['jNames'] =  json_encode($this->data['names']);
                $this->data['jTransactions']= json_encode($transactions);
                $this->data['jPropertyAccounts'] =  json_encode($this->checks_model->getPropertyAccounts2());

               
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                break;
        }
        $this->load->view('forms/bill/main', $this->data);
    }

    function sendEmail(){
               
              
               $this->load->library('email');
       
       
       
               $this->email->from('joshua@simpli-city.com', 'Joshua');
               $this->email->to('joshua@simpli-city.com');
       
               $header = $this->input->post('header');
               $transactions = $this->input->post('transactions');
               $special = $this->input->post('bills');
       
             
               
              
       
               $subject = 'Simli-City Balance';
                      $message = '<p>This message has been sent for testing purposes.</p>';
                     
              
                      $this->email->subject('Simli-City Balance');
                      $body = '<!DOCTYPE html >
                          <html >
                          <head>
                              <meta http-equiv="Content-Type" content="text/html; " />
                              <title>' . html_escape($subject) . '</title>
                              <style type="text/css">
                                  body {
                                      font-family: Arial, Verdana, Helvetica, sans-serif;
                                      font-size: 45px;
                                      color:red;
                                  }
                              </style>
                          </head>
                          <body>
                           <div style="color:blue; border:1px solid red; padding:20px">
                           <h1 style="color:red;" > You Balance is Overdue</h1> 
                          ' . $message . '
                          </div>
                          <div>
                                  <table style="border: 1px solid black;">
                                  <tr style="border: 1px solid black;">
                                      <th>Account</th>
                                      <th>Property</th> 
                                      <th>Description</th>
                                      <th>Amount</th>
                                 </tr>
                                <tr style="border: 1px solid black;color:red;">
                                  <td>'. $transactions[1][account_id] . '</td>
                                  <td>'. $transactions[1][unit_id] . '</td> 
                                  <td>'. $transactions[1][description] . '</td>
                                  <td>'. $transactions[1][debit] . '</td>
                                </tr>
                                <tr style="border: 1px solid black; color:red;">
                                  <td>'. $transactions[2][account_id] . '</td>
                                  <td>'. $transactions[2][unit_id] . '</td> 
                                  <td>'. $transactions[2][description] . '</td>
                                  <td>'. $transactions[2][debit] . '</td>
                                </tr>
              
                                  </table>
                          
                          </div>
                          </body>
                          </html>';
                      $this->email->message($body);
       
               if($this->email->send())
               {
                   echo ' email was sent !!!!!!!!1';
               }else {
                   show_error($this->email->print_debugger());
               }
           }




    
}
