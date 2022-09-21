<?php defined('BASEPATH') OR exit('No direct script access allowed');

class JournalEntry extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    function index()
    {
        $this->meta['title'] = "Journal Entry";
        $this->meta['h2'] = "Journal Entry";
        $this->page_construct('journalIndex/index', null, $this->meta);
    }

    // function getTransactionsDTData()
    // {
    //     $data = Array("data" => Array());
    //     $data["data"][0] = (object)Array("type" => "Payment1", "date" => "1/10/2018", "amount" => "-$1,200.00",
    //         "name" => "ABC Contracting5", "reference" => "RC1564", "description" => "Rent Charge for January",
    //         "balance" => "$1,200.00",
    //         "details" => Array(
    //             (object)Array("type" => "type example", "amount" => "$444", "name" => "John Doe"),
    //             (object)Array("type" => "type example2", "amount" => "$564", "name" => "Jane Doe"),
    //         )
    //     );
    //     $data["data"][1] = (object)Array("type" => "Payment2", "date" => "1/5/2015", "amount" => "$1,800.00",
    //         "name" => "ABC Contracting1", "reference" => "RC1565", "description" => "Rent Charge for February",
    //         "balance" => "$2,300.00",
    //         "details" => Array(
    //             (object)Array("type" => "type example3", "amount" => "$784", "name" => "John Doe"),
    //             (object)Array("type" => "type example4", "amount" => "$984", "name" => "Jane Doe"),
    //         )
    //     );
    //     echo json_encode($data);
    // }

    function sec()
    {
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
    );
    print_r($csrf);
    }


    function addJournalEntry()
    {
        $errors = "";
        $this->load->model('journalEntry_model');

        $header = $this->input->post('header');
        $transactions = $this->input->post('transactions');
        //$validate = $this->validate_model->calcTotal($transactions);
        $data = array('header' => $header, 'transactions' => $transactions);
        $validate = $this->validate_model->validate("journalEntry", $data);
        //if(!$validate){$errors = "Totals don't match </br>";}
        $this->form_validation->set_rules($this->settings->journalEntryFormValidation);
        if ($this->form_validation->run() && $validate['bool'] && $this->journalEntry_model->addJournalEntry($header, $transactions))
            echo json_encode(array('type' => 'success', 'message' => 'Transaction successfully added.'));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function editJournalEntry($tid = 0)
    {
        $errors = "";
        $this->load->model('journalEntry_model');

        $header = $this->input->post('header');
        $transactions = $this->input->post('transactions');
        // $validate = $this->validate_model->calcTotal($transactions);
        // if($validate == false){$errors = "Totals don't match";}
        $data = array('header' => $header, 'transactions' => $transactions);
        $validate = $this->validate_model->validate("journalEntry", $data);
        $this->form_validation->set_rules($this->settings->journalEntryFormValidation);
        if ($this->form_validation->run()&& $validate['bool'] && $this->journalEntry_model->editJournalEntry($header, $transactions, $tid))
            echo json_encode(array('type' => 'success', 'message' => 'Transaction successfully edited.'));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function getRow(){

        $this->load->model('journalEntry_model');
        $this->data['classes'] = $this->journalEntry_model->getClasses();
        $this->data['properties'] = $this->journalEntry_model->getProperties();
        $this->data['accounts'] = $this->journalEntry_model->getAllAccounts();
        $this->data['units'] = $this->journalEntry_model->getUnits();
        $this->data['names'] = $this->journalEntry_model->getNames();

        $this->load->view('forms/journalEntry/transactionDetails', $this->data);

    }

    function getModal()
    {
        $this->load->model('journalEntry_model');
        $this->load->model('checks_model');

        //$params = json_decode($this -> input -> post('params'));
        //$this -> data['profiles'] = $this -> tenants_model -> getTenants();
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'journalEntry/addJournalEntry';
                $this->data['title'] = 'Add Journal Entry';
                $this->data['classes'] = $this->journalEntry_model->getClasses();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['properties'] = $this->journalEntry_model->getProperties();
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['accounts'] = $this->journalEntry_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);//not used anymore, using jsubaccounts
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->journalEntry_model->getUnits();
                $this->data['jUnits'] =  json_encode($this->data['units']);//not used anymore, using jsubunits
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['names'] = $this->journalEntry_model->getNames();
                $this->data['jNames'] =  json_encode($this->data['names']);
                $this->data['jPropertyAccounts'] =  json_encode($this->checks_model->getPropertyAccounts2());
              $trans =  $this->data['jTransactions'] =  json_encode($this->data['transactions']);

                //return $this->journalEntry_model->getAccounts($pid);
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                break;
            case 'edit' :
                $this->data['target'] = 'journalEntry/editJournalEntry/' . $this->input->post('id');
                $this->data['title'] = 'Edit Journal Entry';
                $this->data['classes'] = $this->journalEntry_model->getClasses();
                $this->data['properties'] = $this->journalEntry_model->getProperties();
                $this->data['accounts'] = $this->journalEntry_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jsubaccounts'] = json_encode($this->data['subaccounts']);
                $this->data['units'] = $this->journalEntry_model->getUnits();
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['units']);
                $this->data['jsubunits'] = json_encode($this->data['subunits']);
                $this->data['header'] = $this->journalEntry_model->getHeader($this->input->post('id'));
                $this->data['transactions'] = $this->journalEntry_model->getTransactions($this->input->post('id'));
                $this->data['names'] = $this->journalEntry_model->getNames();
                $this->data['jClasses'] = json_encode($this->data['classes']);
                $this->data['jProperties'] = json_encode($this->data['properties']);
                $this->data['jAccounts'] = json_encode($this->data['accounts']);
                $this->data['jUnits'] =  json_encode($this->data['units']);
                $this->data['jNames'] =  json_encode($this->data['names']);
                $trans = $this->data['jTransactions'] =  json_encode($this->data['transactions']);
                $this->data['jPropertyAccounts'] =  json_encode($this->checks_model->getPropertyAccounts2());

                $test = $this->journalEntry_model->getTransactions($this->input->post('id'));

              

               
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }*/
                break;
        }
        $this->load->view('forms/journalEntry/main', $this->data);
    }




    
}
