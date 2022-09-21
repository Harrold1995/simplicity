<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ReceivePayments extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        
    }

    function index()
    {

      
        $this->meta['title'] = "Receive Payment";
        $this->meta['h2'] = "Receive Payment";
       // $this->page_construct('reconciliations/index', null, $this->meta);


    }

    public function getIdEdit($th_id)
    
    {   
        $this->db->select('t.id, t.profile_id');
        $this->db->from('transactions t'); 
        $this->db->where(array('t.trans_id'=>$th_id, 't.account_id' => $this->site->settings->accounts_receivable));
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $tid = $q->row()->id;
            $pid = $q->row()->profile_id;
            echo "id is $tid and profile_id is $pid";  
            }
            
            return null;
        
    }

    public function getTransactions($pid)
    {
        $this->db->select('t.id, t.description, th.transaction_date AS date, b.due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id = $this->site->settings->accounts_receivable  AND t.profile_id ='. $pid);
        $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments
        UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        //$this->db->where('((t.debit - t.credit) - transum.amounts)', '! = 0');
        $this->db->where('((t.debit - t.credit) - transum.amounts)!=',  0);
        $this->db->or_where('((t.debit - t.credit) - transum.amounts) IS NULL');
        //$this->db->or_where('t.profile_id',$pid);
        //(array('archived' => NULL));
        $this->db->ORDER_BY('th.transaction_date ASC');
        $sql = $this->db->get_compiled_select();
        echo $sql;
        return;
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
       
    }
    // public function getTransactionsEditTest()
    // {
    //     $this->getTransactionsEdit(4, 4);
    // }
    public function getTransactionsEdit()
    {
        $this->db->select('t.id, t.description, th.transaction_date AS date, b.due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, " " AS received_payment');
        $this->db->from('transactions t'); 
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id = $this->site->settings->accounts_receivable  AND t.profile_id =259'); 
        $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments WHERE transaction_id_a != 259
         UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments WHERE transaction_id_a !=259) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('(((t.debit - t.credit) - transum.amounts)!= 0 OR ((t.debit - t.credit) - transum.amounts) IS NULL))
        AND t.id NOT IN (SELECT transaction_id_b from applied_payments where transaction_id_a = 259)');
        //$this->db->where(array('((t.debit - t.credit) - transum.amounts)!='=> 0));
        //$this->db->or_where('((t.debit - t.credit) - transum.amounts) IS NULL');
        //$this->db->where('t.id NOT IN (SELECT transaction_id_b from applied_payments where transaction_id_a = ' . $tid . ')'); 
        
        
        $other_payments = $this->db->get_compiled_select();
        //$this->db->reset_query();
        
        //$sql = $this->db->get_compiled_select();
        echo $sql;
        //return;
        $q = $this->db->get();
        //$q = $this->db->query("$other_payments UNION $this_payment");
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            
            return $data;
        }
        return null;
       
    }

    function applyPayments() 
    {
        $this->load->model('receivePayments_model');
        $profile_id = $this->input->post('profile_id');
        $amount = $this->input->post('amount');
        $header = $this->input->post('header');
        $customerPayments = $this->input->post('customer_payments');
        $applied_payments = $this->input->post('applied_payments');
        
        
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        /*if ($this->form_validation->run() &&*/ $this->receivePayments_model->applyPayments($profile_id, $amount, $header, $customerPayments, $applied_payments);
            echo json_encode(array('type' => 'success', 'message' => 'Payment successfully applied.'));
        // else {
        //     echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('account')));
        // }
    }

    function editAppliedPayments($aid = 0)
    {
        $this->load->model('receivePayments_model');
        $profile_id = $this->input->post('profile_id');
        $amount = $this->input->post('amount');
        $header = $this->input->post('header');
        $customerPayments = $this->input->post('customer_payments');
        $applied_payments = $this->input->post('applied_payments');
        
        
        if ($this->receivePayments_model->editAppliedPayments($profile_id, $amount, $header, $customerPayments, $applied_payments));
            echo json_encode(array('type' => 'success', 'message' => 'Applied payment successfully updated.'));
    }

    
    public function getModal()// not complete see my tasks in asana
    {
        $this->load->model('receivePayments_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $idd = $this->input->post('id');
                $this->data['target'] = 'receivePayments/applyPayments';
                $this->data['title'] = 'Edit Received Payment';
                $this->data['tenants'] = $this->receivePayments_model->getTenants();
                $this->data['transaction_types'] = $this->receivePayments_model->getTransactionType();
                $this->data['accounts'] = $this->receivePayments_model->getDepositTo();
                
                //$this->data['transactions'] = $this->receivePayments_model->getTransactions($this->input->post('id'));//($this->input->post('id'));
                
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                // }
                break;
            case 'edit' ://which values are we passing into the edit 
                $this->data['target'] = 'receivePayments/editAppliedPayments/' . $this->input->post('id');
                $this->data['title'] = 'Edit Received Payment';
                $this->data['tenants'] = $this->receivePayments_model->getTenants();
                $this->data['transaction_types'] = $this->receivePayments_model->getTransactionType();
                $this->data['accounts'] = $this->receivePayments_model->getDepositTo();
                $this->data['header'] = $this->receivePayments_model->getHeaderEdit($this->input->post('id'));
                $this->data['transactions'] = $this->receivePayments_model->getTransactionsEdit($this->input->post('id'));
                // if (isset($params->es_key)) {
                //     $key = explode('.', $params->es_key)[1];
                //     $this->data['account'] = new stdClass();
                //     $this->data['account']->$key = $params->es_value;
                // }
                break;
        }
        //$this->load->view('forms/receive_payment', $this->data);
        $this->load->view('forms/receive_payment/main', $this->data);
        
    }
}