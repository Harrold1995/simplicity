<?php defined('BASEPATH') OR exit('No direct script access allowed');

class TransactionsImport extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        //$this->icons = Array("property" => "fas fa-building", "unit" => "far fa-building");
        $this->load->model('validate_model');
        $this->load->model('transactionsImport_model');
    }

    function transactionsImport()
        {
            $transactions = $this->input->post('import'); 
            $date = $this->input->post('transaction_date');  
            $importType = $this->input->post('importType');  
    
            $data = array('transactions' => $transactions);
            //$validate = $this->validate_model->validate("transactions_import", $data); 
            $status = $this->transactionsImport_model->processPayments($transactions, $date, $importType);
            echo json_encode($status);
        //     //($this->form_validation->run() && $validate['bool'] && $this->payBills_model->applyPayments( $sqlDate , $transactions)){
        //         echo json_encode(array('type' => 'success', 'message' => 'Transactions successfully imported.'));
        //    }
        //     else {
        //         $errors = $validate['msg'];
        //         //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
        //         //echo json_encode(array('type' => 'danger', 'message' => $errors));
        //         echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        //     }
    }
    
    public function getModal()
    {

        switch ($this->input->post('mode')) {
            case 'upload' :
                $this->data['target'] = 'transactionsImport/transactionsImport/'. $this->input->post('id');
                $this->data['title'] = 'Transactions Import';
                $this->load->view('forms/import/transactionsImportapts', $this->data);
                break;
            // case 'edit' :
            //     $this->data['target'] = 'timesheet/editTimesheet/' . $this->input->post('id');
            //     $this->data['title'] = 'Edit Account';
            //     $this->load->view('forms/account/main', $this->data);
            //     break;
            // case 'addProject':
            // $this->load->view('forms/timesheet/main', $this->data);
            //     break;
        }

        
    }



        
}






