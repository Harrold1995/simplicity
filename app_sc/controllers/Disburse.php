<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Disburse extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    function index()
    {
        $this->meta['title'] = "Disbursements";
        $this->meta['h2'] = "Disbursements";
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

    function submitDisburse(){
        $this->load->model('disburse_model');
        $capitals = $this->input->post('row');
        $asOf = $this->input->post('as_of_date');
        $due = $this->input->post('due_date');
        $type = $this->input->post('type');
        $result = $this->disburse_model->submitCapitals($capitals,  $asOf,$type,$due);
        echo json_encode($result);
    }

    function getModal()
    {
        switch ($this->input->post('id')) {
            case 'disburse' :
                $this->data['target'] = 'Disburse/submitDisburse';
                $this->data['title'] = 'Disbursements';

                break;
            case 'capital' :
                $this->data['target'] = 'Disburse/submitDisburse';
                $this->data['title'] = 'Capital Calls';

                break;
        }

        //getting current cc date
        $this->db->select('cur_capital_call');
        $q = $this->db->get('company_settings');
        $date = $q->row('cur_capital_call');
        if ($date === null){
            $date = date();
        }
        $this->data['date'] = $date;


        
/*         switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'Disburse/submitDisburse';
                $this->data['title'] = 'Disbursements';

                break;
            case 'edit' :
                $this->data['target'] = 'Disburse/editCharge/' . $this->input->post('id');
                $this->data['title'] = 'Edit Capital';

                break;
        } */

        $this->load->view('forms/disburse/main', $this->data);
    }

    function capitalFunction($date){
        $sqlDate = date_create_from_format('Y-m-d', str_replace('/', '-', $date))->format('Y-m-d');
        $this->ap = $this->site->settings->accounts_payable;
        $this->sd = $this->site->settings->security_deposits;
        $this->lmr = $this->site->settings->lmr;
        $q = $this->db->query( "
        SELECT max(properties.id) as id, properties.name, sum(bank_balance) as bank_balance, sum(payables) as payables,  ifnull(reserves,0) as reserves, sum(sdd) as sd, sum(lmrr) as lmr, ifnull(additional_expense , 0) AS additional_expense, ifnull(included_in_payables, 0) AS included_in_payables, sum(income) as income,  sum(expense)/3 as expense, cc_notes as notes, cc_amt from 

            (SELECT property_id,  
                if(account_id in(default_bank,sd_refund_account), debit-credit,0) as bank_balance, 
                if(account_id = '$this->ap', credit - debit,0) as payables, 
                if(account_id = '$this->sd', credit - debit,0) as sdd, 
                if(account_id = '$this->lmr', credit - debit,0) as lmrr, 
                if(account_category_id = 4 and transaction_date >= DATE_SUB(' $sqlDate', INTERVAL 1 MONTH), credit - debit,0) as income, 
                if(account_category_id = 5 and transaction_date >= DATE_SUB(' $sqlDate', INTERVAL 3 MONTH), debit - credit,0) as expense
            from transactions 
            INNER JOIN transaction_header on transaction_header.id = transactions.trans_id
            INNER JOIN properties on transactions.property_id = properties.id
            INNER JOIN accounts on transactions.account_id = accounts.id
            INNER Join account_types on accounts.account_types_id = account_types.id
            Where transaction_date <= ' $sqlDate' and transaction_date > '2007-12-30' and (basis in(0,1) or isnull(basis))

            ) cc 


            INNER JOIN properties on cc.property_id = properties.id

            group BY properties.name, reserves, additional_expense, included_in_payables, cc_notes, cc_amt");
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                $columns = Array();
                $columns[] = Array("id" => "check", "name" => '<label for="pay_bill_select_all" class="custom-checkbox"><input type="checkbox" class="hidden" id="pay_bill_select_all"><div class="input"></div></label>', "field" => "check", "width" => 55, "sortable" => false, "resizable" => false, "formatter" => "CheckFormatter");
                $columns[] = Array("id" => "property", "name" => "Property", "field" => "name", "sortable" => true, "resizable" => true);
                $columns[] = Array("id" => "bank_balance", "name" => "Bank Balance", "field" => "bank_balance", "sortable" => true, "formatter" => "UsdFormatter", "resizable" => true);
                $columns[] = Array("id" => "payables", "name" => "Open AP", "field" => "payables", "sortable" => true,  "formatter" => "UsdFormatter", "resizable" => true);
                $columns[] = Array("id" => "reserves", "name" => "Mortgage", "field" => "reserves", "sortable" => false, "resizable" => true, "formatter" => "InputFormatter", "format" => "usd", "asyncPostRender" => "renderInput", "valuefield" => "reserves", "instantUpdate" => true);
                $columns[] = Array("id" => "additional_expense", "name" => "Additional Expenses", "field" => "additional_expense", "sortable" => true, "resizable" => true, "formatter" => "InputFormatter", "format" => "usd", "asyncPostRender" => "renderInput", "valuefield" => "additional_expense", "instantUpdate" => true);
                $columns[] = Array("id" => "included_in_payables", "name" => "included in payables", "field" => "included_in_payables", "sortable" => true, "resizable" => true, "formatter" => "InputFormatter", "format" => "usd", "asyncPostRender" => "renderInput", "valuefield" => "included", "instantUpdate" => true);
                $columns[] = Array("id" => "cc_notes", "name" => "notes", "field" => "cc_notes", "sortable" => true, "resizable" => true, "formatter" => "InputFormatter", "format" => "text", "instantUpdate" => true);
                $columns[] = Array("id" => "SD", "name" => 'sd', "field" => "sd", "sortable" => true, "resizable" => true, "formatter" => "UsdFormatter");
                $columns[] = Array("id" => "lmr", "name" => "LMR", "field" => "lmr", "sortable" => false, "resizable" => true, "formatter" => "UsdFormatter");
                $columns[] = Array("id" => "income", "name" => 'Income', "field" => "income", "width" => 55, "sortable" => false, "resizable" => true, "formatter" => "UsdFormatter");
                $columns[] = Array("id" => "expense", "name" => 'Expense', "field" => "expense", "width" => 55, "sortable" => false, "resizable" => true, "formatter" => "UsdFormatter");
                $columns[] = Array("id" => "capital call amt", "name" => "Suggested Amount", "field" => "amount", "sortable" => false, "resizable" => true, "formatter" => "UsdFormatter", "format" => "usd", "asyncPostRender" => "renderFormula", "formula" => "parseFloat(dataContext.bank_balance) - (parseFloat(dataContext.payables) + parseFloat(dataContext.reserves) + parseFloat(dataContext.additional_expense) + parseFloat(dataContext.sd) + parseFloat(dataContext.lmr) + parseFloat(dataContext.expense)) + (parseFloat(dataContext.income) + parseFloat(dataContext.included_in_payables))", "total" => true);
                $columns[] = Array("id" => "cc_amt", "name" => "final Amount", "field" => "cc_amt", "sortable" => false, "resizable" => true, "formatter" => "InputFormatter", "format" => "usd", "asyncPostRender" => "renderInput", "valuefield" => "cc_amt", "instantUpdate" => true);
                echo json_encode(Array("data" => $data, "columns" => $columns));

                $this->db->update('company_settings', array('cur_capital_call' => $sqlDate));
            }else{
                echo "capitalFunction";
            }
    }



    
}
