<?php defined('BASEPATH') OR exit('No direct script access allowed');
include 'app_sc/helpers/logs/logs.php';

class Reconciliations extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
        $this->load->model('logs_model');
        $this->load->model('reconciliations_model');
        
    }

    function index()
    {

      
        $this->meta['title'] = "Reconciliations";
        $this->meta['h2'] = "Reconciliation";
       // $this->page_construct('reconciliations/index', null, $this->meta);
      

    }
    

    
    function startReconciliation() 
    {

        $reconciliation = $this->input->post('reconciliation');
        $transactions = $this->input->post('transactions');
        $closed = $this->input->post('closed');
        $property_set = $this->input->post('property');
        $property = $this->input->post('property_id');
        $statement_end_date = $reconciliation['statement_end_date'];
        $acct = $reconciliation['account_id'];

        //adding bank account property if it wasn't set before 
        if (empty($property_set) && !empty($property) )  {
            $prop = array('property' => $property);
            $this->db->update('banks',$prop,'account_id='.$acct);
        }
        
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        $data = $statement_end_date;
        $validate = $this->validate_model->validate("reconciliation", $data);
        if($validate['bool']){
            $status = $this->reconciliations_model->startReconciliation($reconciliation, $transactions,$statement_end_date, $closed, $property);
            $update_title_log = new Log_Rec($this->ion_auth->get_user_id(), $reconciliation, $transactions,$statement_end_date, $closed, $property, "Created");
            $this->logs_model->add_log($update_title_log);
            if($status){
                if($status === 'reconciled'){
                    echo json_encode(array('type' => 'success', 'message' => 'Account successfully reconciled.'));

                }else{
                    echo json_encode(array('type' => 'success', 'message' => 'Reconciliation saved for later.'));
                }
            }else{
                echo json_encode(array('type' => 'danger', 'message' => 'Something went wrong'));
            }
        }else{
            echo json_encode(array('type' => 'danger', 'message' => $validate['msg']));
        }
        
            
    }
  
    function autoReconciliation()
    {   
        $this->load->model('logs_model');
        $reconciliation = $this->input->post('reconciliation');
        $transactions = $this->input->post('transactions');
        $banktrans = $this->input->post('banktrans');

        //$validate = $this->validate_model->validate("reconciliation-auto", $data);

        $status = $this->reconciliations_model->autoReconciliation($reconciliation, $transactions, $banktrans);
        $update_title_log = new Log_Rec($this->ion_auth->get_user_id(), $reconciliation, $transactions,$banktrans, "updated");
        $this->logs_model->add_log($update_title_log);  

        if($status){
            echo json_encode(array('type' => 'success', 'message' => 'Match Successfully Saved.'));
        }
    }
    function refresh(){
        $type = $this->input->post('type'); 
        $reconciliation = $this->input->post('reconciliation');
        $transactions = $this->input->post('transactions');
        $banktrans = $this->input->post('banktrans');
        $newData = $this->reconciliations_model->refresh($reconciliation, $transactions, $banktrans, $type);
        echo json_encode($newData);
    }

    function editReconciliation()
    {   
        $this->load->model('logs_model');
        $reconciliation = $this->input->post('reconciliation');
        $transactions = $this->input->post('transactions');
        $closed = $this->input->post('closed');
        $property_set = $this->input->post('property');
        $property = $this->input->post('property_id');
        $acct = $reconciliation['account_id'];
        $statement_end_date = $reconciliation['statement_end_date'];
        $data = $statement_end_date;

                //adding bank account property if it wasn't set before 
                if (empty($property_set) && !empty($property) )  {
                    $prop = array('property' => $property);
                    $this->db->update('banks',$prop,'account_id='.$acct);
                } 

        $validate = $this->validate_model->validate("reconciliation", $data);
        if($validate['bool']){
            $status = $this->reconciliations_model->editReconciliation($reconciliation, $transactions,$statement_end_date, $closed);
            $update_title_log = new Log_Rec($this->ion_auth->get_user_id(), $reconciliation, $transactions,$statement_end_date, $closed, $property, "updated");
            $this->logs_model->add_log($update_title_log);            
            if($status){
                if ($this->input->post('refresh')){
                    $this->getNewRec($reconciliation['account_id'],$reconciliation['id']);
                } elseif ($status === 'reconciled'){
                    echo json_encode(array('type' => 'success', 'message' => 'Account successfully reconciled.'));
                }else{
                    echo json_encode(array('type' => 'success', 'message' => 'Reconciliation saved for later.'));
                }
            }else{
                echo json_encode(array('type' => 'danger', 'message' => 'Something went wrong'));
            }
        }else{
            echo json_encode(array('type' => 'danger', 'message' => $validate['msg']));
        }
        
    }

    function delete($rec_id, $type){
        $data = ['rec_id' => $rec_id, 'mode' => 'delete'];
        $validate = $this->validate_model->validate("delopenReconciliation", $data);
        //select max(rec_id) from transactions where account_id = 5

        if(($validate['bool'] || $type =='auto')&& $this->reconciliations_model->deleteRec($rec_id, $type)){
            echo json_encode(array('type' => 'success', 'message' => 'Reconciliation Deleted successfully'));
        }else{
            $errors = $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function ignoreTrans($trans_id){

        if($this->reconciliations_model->ignoreTrans($trans_id)){
            echo json_encode(array('type' => 'success', 'message' => 'This transaction is now being ignored and wont show up on the reconcilliations page'));
        }else{
            $errors = $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function unIgnoreTrans($trans){

        if($this->reconciliations_model->unIgnoreTrans($trans)){
            echo json_encode(array('type' => 'success', 'message' => 'Transaction ignore removed!'));
        }else{
            $errors = $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }


    function reopen($rec_id){
        $data = ['rec_id' => $rec_id, 'mode' => 'reopen'];
        $validate = $this->validate_model->validate("delopenReconciliation", $data);
        //select max(rec_id) from transactions where account_id = 5
        if($validate['bool'] && $this->reconciliations_model->reopenRec($rec_id)){
            echo json_encode(array('type' => 'success', 'message' => 'Reconciliation reopen successfully'));
        }else{
            $errors = $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    function getNewRec($id = null, $r_id = null){
        $refresh = true;
        $getNewRec['reconciliation'] = $this->reconciliations_model->getLastReconciliation($id);
        $closed = $getNewRec['reconciliation']->closed;
                if (is_null($id)){
                    $id = $this->input->get('id');
                    $r_id = $getNewRec['reconciliation']->r_id;
                    $refresh = false;
                }

                if($closed === '0'){
                    $getNewRec['target'] = 'reconciliations/editReconciliation';
                }else{
                    $getNewRec['target'] = 'reconciliations/startReconciliation';
                }
                $this->data['account'] = $this->reconciliations_model->getAccount($id);

                $this->data['credits'] = $this->reconciliations_model->getCredits($id,$r_id);
                $this->data['debits']  = $this->reconciliations_model->getDebits($id,$r_id);
                $this->data['cleared'] = $this->reconciliations_model->clearedTransactionsDebitCredit($id);
                //$getNewRec['accounts'] = $this->reconciliations_model->getAllAccounts();
                if($refresh == true){
                    $this->data['reconciliation'] = $getNewRec;
                    echo json_encode($this->data);
                    //echo json_encode($getNewRec);
                }else {
                    echo json_encode($getNewRec);
                }
                
    }

    public function getMatched(){
        $type = $this->input->post('type');
        $rec_id = $this->input->post('rec_id');

        if($type == 'banktrans'){
            $this->db->select('*');
            $this->db->from('transactions');
            $this->db->join('transaction_header th', 'th.id = transactions.trans_id');
            $this->db->where('rec_id', $rec_id);
        } else {
            $this->db->select('*');
            $this->db->from('plaid_trans');
            $this->db->where('trans_match', $rec_id);
        }
        
        $q = $this->db->get(); 
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $row->name = str_replace("'", '*', $row->name);
                $row->merchant_name = str_replace("'", '*', $row->merchant_name);
                $data[] = $row;
            }
            echo json_encode($data);
        }
    }

    public function pullReconciliation($rec_id){
        $this->db->select('account_id');
        $this->db->from('reconciliations');
        $this->db->where('id', $rec_id);
        $q = $this->db->get(); 
        if ($q->num_rows() > 0) {
            $account_id = $q->row()->account_id;
        }

        // $account_id = $this->db->get_compiled_select();//will probably get account id to pull up rec
        // $this->db->reset_query();

        $this->db->select('MAX(rec_id) AS rec_id');
        $this->db->from('transactions');
        //$this->db->where("account_id = ($account_id)", NULL, FALSE);
        $this->db->where('account_id', $account_id);
        //$this->db->where('account_id = (SELECT account_id FROM reconciliations WHERE id = ' . $data['rec_id'] . ')', NULL, FALSE);
        $q = $this->db->get(); 
        if ($q->num_rows() > 0) {
            $lastRec = $q->row()->rec_id;
        }
        if($rec_id != $lastRec){
            $msg = 'Sorry you can only pull the last reconciliation for this account'; 
        }
        //get modal passing account id and rec id
    }


    
    public function getModal()// not complete see my tasks in asana
    {
        $params = json_decode($this->input->post('params'));

        switch ($this->input->post('mode')) {
            case 'start' :
                //$this->data['target'] = 'reconciliations/startReconciliation';
                $this->data['title'] = 'Start Reconciliation';
                $this->data['accounts'] = $this->reconciliations_model->getAccounts();
                $this->data['reconciliation'] = $this->reconciliations_model->getLastReconciliation($this->input->post('id'));
                $closed = $this->data['reconciliation']->closed;
                if($params->type == 'auto'){
                    $this->data['target'] = 'reconciliations/autoReconciliation';
                }
                else{
                    if($closed === '0'){
                        $this->data['target'] = 'reconciliations/editReconciliation';
                    }else{
                        $this->data['target'] = 'reconciliations/startReconciliation';
                    }     
                }
                
                $this->data['account'] = $this->reconciliations_model->getAccount($this->input->post('id'));
                $this->data['credits'] = $this->reconciliations_model->getCredits($this->input->post('id'),$this->data['reconciliation']->r_id);
                $this->data['debits'] = $this->reconciliations_model->getDebits($this->input->post('id'),$this->data['reconciliation']->r_id);
                $this->data['cleared'] = $this->reconciliations_model->clearedTransactionsDebitCredit($this->input->post('id'));
                $this->data['rectype'] = $params->rectype;
                //$this->data['accounts'] = $this->reconciliations_model->getAllAccounts();


                // $this->data['ending_bal'] = $this->reconciliations_model->getEndingBalance($aid);
                // $this->data['credits'] = $this->reconciliations_model->getCredits();
                // $this->data['debits'] = $this->reconciliations_model->getDebits();

                

                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                // }
                break;
            // case 'edit' ://which values are we passing into the edit 
            //     $this->data['target'] = 'reconciliations/editReconciliation/' . $this->input->post('id');
            //     $this->data['title'] = 'Edit Reconciliation';
            //     $this->data['accounts'] = $this->reconciliations_model->getAccounts();
            //     $this->data['credits'] = $this->reconciliations_model->getCreditsEdit($aid, $rec_id);
            //     $this->data['debits'] = $this->reconciliations_model->getDebitsEdit($aid, $rec_id);
               
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                // }
                //break;
        }
        if($params->type == 'auto'){
            $this->load->view('forms/reconciliation/auto', $this->data);
        }else{
            $this->load->view('forms/reconciliation/main2', $this->data);
        }
        
    }

    function getCreditsAjax($aid) {
        $r = $this->reconciliations_model->getLastReconciliation($aid);
        $data = $this->reconciliations_model->getCredits($aid, $r->r_id);
        $columns = Array();
        $columns[] = (Object)Array("id" => "check", "field" => "check", "name" => "<div id='checkall'><i id='rec-icon-check' class='icon-check' style='display:none'></i></div>", "formatter" => "CheckFormatter", "width"=> 10);
        $columns[] = (Object)Array("id" => "date", "field" => "date", "name" => "Date", "width"=> 80, "sortable" => true);
        $columns[] = (Object)Array("id" => "type", "field" => "type", "name" => "Type", "width"=> 80, "sortable" => true);
        $columns[] = (Object)Array("id" => "num", "field" => "num", "name" => "Num", "width"=> 60, "sortable" => true, "datatype" => "num");
        $columns[] = (Object)Array("id" => "vendor", "field" => "vendor", "name" => "Name", "sortable" => true);
        $columns[] = (Object)Array("id" => "amount", "field" => "amount", "name" => "Amount", "width"=> 80, "sortable" => true, "datatype" => "num");
        echo json_encode(Array("data"=> $data, "columns"=>$columns));
    }
    function getDebitsAjax($aid) {
        $r = $this->reconciliations_model->getLastReconciliation($aid);
        $data = $this->reconciliations_model->getDebits($aid, $r->r_id);
        $columns = Array();
        $columns[] = (Object)Array("id" => "check", "field" => "check", "name" => "<div id='checkall'><i id='rec-icon-check' class='icon-check' style='display:none'></i></div>", "formatter" => "CheckFormatter", "width"=> 10);
        $columns[] = (Object)Array("id" => "date", "field" => "date", "name" => "Date", "width"=> 80, "sortable" => true);
        $columns[] = (Object)Array("id" => "type", "field" => "type", "name" => "Type", "width"=> 80, "sortable" => true);
        $columns[] = (Object)Array("id" => "num", "field" => "num", "name" => "Num", "width"=> 60, "sortable" => true, "datatype" => "num");
        $columns[] = (Object)Array("id" => "vendor", "field" => "vendor", "name" => "Name", "sortable" => true);
        $columns[] = (Object)Array("id" => "amount", "field" => "amount", "name" => "Amount", "width"=> 80, "sortable" => true, "datatype" => "num");
        echo json_encode(Array("data"=> $data, "columns"=>$columns));
    }
    function getAllAjax($aid) {
        $r = $this->reconciliations_model->getLastReconciliation($aid);
        $data = $this->reconciliations_model->getAllTrans($aid, $r->r_id);
        $columns = Array();
        $columns[] = (Object)Array("id" => "check", "field" => "check", "name" => "<div id='checkall'><i id='rec-icon-check' class='icon-check' style='display:none'></i></div>", "formatter" => "CheckFormatter", "width"=> 10);
        $columns[] = (Object)Array("id" => "date", "field" => "date", "name" => "Date", "width"=> 80, "sortable" => true);
        $columns[] = (Object)Array("id" => "type", "field" => "type", "name" => "Type", "width"=> 80, "sortable" => true);
        $columns[] = (Object)Array("id" => "num", "field" => "num", "name" => "Num", "width"=> 60, "sortable" => true, "datatype" => "num");
        $columns[] = (Object)Array("id" => "vendor", "field" => "vendor", "name" => "Name", "sortable" => true);
        $columns[] = (Object)Array("id" => "amount", "field" => "amount", "name" => "Amount", "width"=> 80, "sortable" => true, "datatype" => "num");
        echo json_encode(Array("data"=> $data, "columns"=>$columns));
    }
    function getBankAjax($aid) {      
        $data = $this->reconciliations_model->getBankTrans($aid);
        $columns = Array();
        $columns[] = (Object)Array("id" => "check", "field" => "check", "name" => "<div id='checkall'><i id='rec-icon-check' class='icon-check' style='display:none'></i></div>", "formatter" => "CheckFormatter", "width"=> 10);
        $columns[] = (Object)Array("id" => "date", "field" => "date", "name" => "Date", "width"=> 80, "sortable" => true);
        $columns[] = (Object)Array("id" => "num", "field" => "num", "name" => "Num", "width"=> 60, "sortable" => true, "datatype" => "num");
        $columns[] = (Object)Array("id" => "amount", "field" => "amount", "name" => "Amount", "width"=> 80, "sortable" => true, "datatype" => "num");
        $columns[] = Array("id" => "account_id", "name" => "Expense Acct", "field" => "account_id", "sortable" => false, "resizable" => true, "formatter" => "SelectFormatter", "asyncPostRender" => "renderSelect", "source" => "account", "namefield" => "bank_name");
        $columns[] = Array("id" => "Name", "name" => "Name", "field" => "Name", "sortable" => true, "resizable" => true, "formatter" => "SelectFormatter", "asyncPostRender" => "renderSelect", "source" => "profile", "namefield" => "profile_name");
        echo json_encode(Array("data"=> $data, "columns"=>$columns));
    }
    function getBankAddAjax($aid) {      
        $data = $this->reconciliations_model->getBankTrans($aid);
        $columns = Array();
        $columns[] = (Object)Array("id" => "date", "field" => "date", "name" => "Date", "width"=> 80, "sortable" => true);
        $columns[] = (Object)Array("id" => "num", "field" => "num", "name" => "Num", "width"=> 60, "sortable" => true, "datatype" => "num");
        $columns[] = (Object)Array("id" => "amount", "field" => "amount", "name" => "Amount", "width"=> 80, "sortable" => true, "datatype" => "num");
        $columns[] = (Object)Array("id" => "account_id", "name" => "Expense Acct", "field" => "account_id", "sortable" => false, "resizable" => true, "formatter" => "SelectFormatter", "asyncPostRender" => "renderSelect", "source" => "account", "namefield" => "bank_name");
        $columns[] = (Object)Array("id" => "profile_id", "name" => "Name", "field" => "profile_id", "sortable" => true, "resizable" => true, "formatter" => "SelectFormatter", "asyncPostRender" => "renderSelect", "source" => "profile", "namefield" => "profile_name");
        $columns[] = (Object)Array("id" => "property_id", "name" => "Property", "field" => "property_id", "sortable" => true, "resizable" => true, "formatter" => "SelectFormatter", "asyncPostRender" => "renderSelect", "source" => "property", "namefield" => "property_name");
        $columns[] = (Object)Array("id" => "quickAdd", "field" => "quickAdd", "name" => "quick add", "formatter" => "ButtonFormatter", "width"=> 12);
        $columns[] = (Object)Array("id" => "addAs", "field" => "addAs", "name" => "add As", "formatter" => "AddAsButtonFormatter", "width"=> 12);
        echo json_encode(Array("data"=> $data, "columns"=>$columns));
    }
}