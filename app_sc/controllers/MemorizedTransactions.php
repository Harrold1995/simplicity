<?php defined('BASEPATH') OR exit('No direct script access allowed');

//include_once 'checks.php';
class MemorizedTransactions extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        //$this->load->model('validate_model');
        
    }

    // function index()
    // {
    //     $this->meta['title'] = "Checks";
    //     $this->meta['h2'] = "Checks";
    //     $this->page_construct('journalIndex/index', null, $this->meta);
    // }

    

    // function test()
    // {
        
// require_once (dirname(__FILE__) . "/checks.php");            
// //$this->check = new Checks();
// redirect("Checks/AddCheck");
// $_POST =  [
//     'one' =>  [
       
//         'profile_id' => 6, 'lease_id' => 7, 'property_id' => 8, 'unit_id' => 94, 'trans_id' => 15
//      ],
//     'two' =>  [
//         ['id' => 1, 'profile_id' => 2, 'property_id' => 4, 'unit_id' => 4, 'trans_id' => 9],
//         ['id' => 1, 'profile_id' => 2, 'property_id' => 3, 'unit_id' => 4, 'trans_id' => 5]
//      ],
//     'three' =>  [
//         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
//         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
//         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5]]
//     ];
    
//     $this->load->library('memorized');
//     $this->memorized->editCheck1();
    
// }

function test()
{
    $this->load->model('memorizedTransactions_model');
    $this->memorizedTransactions_model->checkTransactions();
    $transactionData = $this->input->post();
}

    function addMemorizedTransaction()
    {
        $this->load->model('memorizedTransactions_model');
        $errors = "";
        $specs = $this->input->post('brain');
        if(!$specs['auto']){
            $specs['auto'] = 0;
        }
        
        unset($_POST['brain']);
        $transactionData = $this->input->post();
        $property_id = $transactionData['headerTransaction']['property_id'] ? $transactionData['headerTransaction']['property_id'] : ($transactionData['transactions']['property_id'] ? $transactionData['transactions']['property_id'] : $transactionData['transactions'][0]['property_id']);
        $specs['property_id'] = $property_id;
        $transaction_type = $specs['transaction_type'];
        switch ($transaction_type) {
            case '2' :
                 $specs['type_id'] = 6;
                 $specs['type_item_id'] = $transactionData['headerTransaction']['profile_id'];
                 $specs['amount'] = $transactionData['headerTransaction']['credit'];
                 break;
            case 'journalEntry' :
                $this->journalEntry($data);
                return $this->validation;
            
        }
        foreach($transactionData as $key => &$value){
          foreach($value as &$val){
            if(is_array($val)){
            unset($val['id']);
            }else{unset($value['id']);}
          }
        }
    
       
        //$data = array('header' => $header, 'headerTransaction' => $headerTransaction, 'transactions' => $transactions, 'special' => $special);
        //$validate = $this->validate_model->validate("checks", $data);

        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/$this->memorizedTransactions_model->addTransaction($specs, $transactionData))
            echo json_encode(array('type' => 'success', 'message' => 'Memorized transaction successfully added.'));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    // function editCheck($tid = 0)
    // {
    //     $this->load->model('checks_model');
        // // $_POST =  [
        // //     'one' =>  [
               
        // //         'profile_id' => 6, 'lease_id' => 7, 'property_id' => 8, 'unit_id' => 94, 'trans_id' => 15
        // //      ],
        // //     'two' =>  [
        // //         ['id' => 1, 'profile_id' => 2, 'property_id' => 4, 'unit_id' => 4, 'trans_id' => 9],
        // //         ['id' => 1, 'profile_id' => 2, 'property_id' => 3, 'unit_id' => 4, 'trans_id' => 5]
        // //      ],
        // //     'three' =>  [
        // //         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
        // //         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
        // //         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5]]
        // //     ];
        // // $post =$this->input->post();
        // //var_dump($post);
        // $data = [
        //     'one' =>  [
               
        //         'profile_id' => 6, 'lease_id' => 7, 'property_id' => 8, 'unit_id' => 94, 'trans_id' => 15
        //      ],
        //     'two' =>  [
        //         ['id' => 1, 'profile_id' => 2, 'property_id' => 4, 'unit_id' => 4, 'trans_id' => 9],
        //         ['id' => 1, 'profile_id' => 2, 'property_id' => 3, 'unit_id' => 4, 'trans_id' => 5]
        //      ],
        //     'three' =>  [
        //         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
        //         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
        //         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5]]
        //     ];
            // foreach($post as $key => &$value){
            //     foreach($value as &$val){
            //     if(is_array($val)){
            //         unset($val['id']);
            //     }else{unset($value['id']);}
                
                
            // }
            // //unset($value['say']);
            // $result[$key] = $value;
            // }

//         foreach($post as $key=>$value)
// {
//         $$key = $value;
// }
        
 // $header = $this->input->post('header');
        // $headerTransaction = $this->input->post('headerTransaction');
        // $transactions = $this->input->post('transactions');
        // $special = $this->input->post('checks');
        // $deletes = $this->input->post('delete');
       
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
       // if (/*$this->form_validation->run() &&*/ $this->checks_model->editCheck($header, $headerTransaction, $transactions, $checks, $tid, $deletes))
         //   echo json_encode(array('type' => 'success', 'message' => 'Check successfully edited.'));
        /*else {
            echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('transaction')));
        }*/
   // }

    

    
}
