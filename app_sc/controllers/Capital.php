<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Capital extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    function index()
    {
        $this->meta['title'] = "Capital";
        $this->meta['h2'] = "Capital";
        //$this->page_construct('Capital/index', null, $this->meta);
    }

    function getCapital($date){
        //echo 'capital controller';
        $q = $this->db->get('capital');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            echo json_encode($data);
        }else{
            echo 'false..';
        }
    }

    function submitCapitals(){
        $this->load->model('capital_model');
        $capitals = $this->input->post('capital');
        $result = $this->capital_model->submitCapitals($capitals);
        echo json_encode($result);
    }

    function getModal()
    {

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'capital/submitCapitals';
                $this->data['title'] = 'Add Capital';

                break;
            case 'edit' :
                $this->data['target'] = 'Capital/editCharge/' . $this->input->post('id');
                $this->data['title'] = 'Edit Capital';

                break;
        }
        $this->load->view('forms/Capital/main', $this->data);
    }

    function capitalFunction($date){
        $sqlDate = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
        $this->ap = $this->site->settings->accounts_payable;
        $q = $this->db->query( "
        SELECT max(properties.id) as id, properties.name, sum(bank_balance) as bank_balance, sum(payables) as payables, 0 AS mortgage, 0 AS additional_expense, sum(income) as income,  sum(expense)/3 as expense from 

            (SELECT property_id,  
                if(default_bank = account_id, debit-credit,0) as bank_balance, 
                if(account_id = '$this->ap', credit - debit,0) as payables, 
                if(account_category_id = 4 and transaction_date <= DATE_SUB(NOW(), INTERVAL 1 MONTH), credit - debit,0) as income, 
                if(account_category_id = 5 and transaction_date <= DATE_SUB(NOW(), INTERVAL 3 MONTH), debit - credit,0) as expense
            from transactions 
            INNER JOIN transaction_header on transaction_header.id = transactions.trans_id
            INNER JOIN properties on transactions.property_id = properties.id
            INNER JOIN accounts on transactions.account_id = accounts.id
            INNER Join account_types on accounts.account_types_id = account_types.id
            Where transaction_date <= ' $sqlDate'

            ) cc 


            INNER JOIN properties on cc.property_id = properties.id

            group BY properties.name");
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                echo json_encode($data);
            }else{
                echo "capitalFunction";
            }
        }

    
}
