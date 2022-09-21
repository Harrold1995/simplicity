<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Management extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    function index()
    {
        $this->meta['title'] = "Management fees";
        $this->meta['h2'] = "Management fees";
        //$this->page_construct('Capital/index', null, $this->meta);
    }


    function getModal()
    {


        $this->data['target'] = 'transactions/addManagementChecks';
        $this->data['title'] = 'Management Fees';
        $this->data['date'] = date("Y-m-d");

        $this->load->view('forms/management/main', $this->data);
    }

    function managementFunction($endDate){
        // pull all fees that have to run today
        $today = date('Y-m-d');//date as yyyy-mm-dd
        $nextDate = date( "Y-m-d", strtotime( $today." +".$row->number." ".$row->interval_unit) );
        $this->db->select('f.interval_unit, f.number, management_fees.id, management_fees.item_id, p.default_bank, management_fees.frequency, management_fees.account_id, management_fees.percentage_fixed, management_fees.vendor, management_fees.amount, management_fees.property_id, p.name as pname, concat(number," ", interval_unit) as "interval", management_fees.start_date');
        $this->db->from('management_fees');
        $this->db->join('frequencies f', 'management_fees.frequency = f.id'); 
        $this->db->join('properties p', 'management_fees.property_id = p.id');
        //$this->db->where('management_fees.start_date', $today);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            //generate cash basis table
            $this->load->model('ReportsTable_model');
            $cbtable = $this->ReportsTable_model->generateCashBasisTable();
            $default_income_acct = $this->db->get_where('items', array('id' => $this->site->settings->default_RC_item), 1)->row()->acct_income;

            foreach (($q->result()) as &$row) {
                
                $nextDate = date( "Y-m-d", strtotime( $today." +".$row->number." ".$row->interval_unit) );
                $headerAcct = $this->site->settings->management_check == 1 ? $row->default_bank : $this->site->settings->accounts_payable;

                
                // if it is based on percentage replace amount with calculation below:
                if($row->percentage_fixed == 1){
                    //$startDate = date( "Y-m-d", strtotime( $today." -".$row->number." ".$row->interval_unit) );
                    $startDate = $row->start_date;
                    //$endDate = date( "Y-m-d", strtotime( $today." - 1 day") );
                    

                    $this->db->select('sum(credit-debit) as amount');
                    $this->db->from($cbtable);
                    $this->db->where('property_id', $row->property_id);
                    $this->db->where_in('account_id', explode(',', $row->item_id));                   
                    //$this->db->where('account_id !=', $this->site->settings->accounts_receivable);
                    //$this->db->where('account_id =',$default_income_acct);
                    $this->db->where('transaction_date BETWEEN "'.$startDate.'" and "'.$endDate.'"');

                    $this->db->group_by('property_id');
                    $q2 = $this->db->get();
                        if ($q2->num_rows() > 0) {
                            foreach (($q2->result()) as &$mfee) {
                                if ($mfee->amount > 0 ){
                                    $row->memo = $startDate.' - '.$endDate.' '.$row->pname;
                                    $row->income = $mfee->amount;
                                    $row->calcAmount = $mfee->amount * ($row->amount/100);

                                   
                                } else{
                                    //remove transaction
                                    $row->calcAmount = 0;
                                    continue;
                                };
                            }
                        } else {

                            $row->calcAmount = 0;
                        }
                        
                        $row->amount = $row->amount.'%';
                        
                } else {
                    $row->calcAmount = $row->amount;
                    $row->amount = '$'.$row->amount;
                }
                $data[] = $row;
            }
            if($cbtable)
            $this->db->query('DROP TABLE IF EXISTS '.$cbtable);
        }


                

                $columns = Array();
                $columns[] = Array("id" => "check", "name" => '<label for="pay_bill_select_all" class="custom-checkbox"><input type="checkbox" class="hidden" id="pay_bill_select_all"><div class="input"></div></label>', "field" => "check", "width" => 55, "sortable" => false, "resizable" => false, "formatter" => "CheckFormatter");
                $columns[] = Array("id" => "property", "name" => "Property", "width" => 35, "field" => "pname", "sortable" => true, "resizable" => true);
                $columns[] = Array("id" => "amount", "name" => "Rate", "width" => 10,"field" => "amount", "sortable" => true, "resizable" => true);
                $columns[] = Array("id" => "income", "name" => "Income", "width" => 35, "field" => "income", "sortable" => true, "resizable" => true, "formatter" => "UsdFormatter");
                $columns[] = Array("id" => "account_id", "name" => "Pmt Account", "field" => "default_bank", "sortable" => false, "resizable" => true, "formatter" => "SelectFormatter", "asyncPostRender" => "renderSelect", "source" => "account", "namefield" => "bank_name");
                $columns[] = Array("id" => "account_id", "name" => "Expense Account", "field" => "account_id", "sortable" => false, "resizable" => true, "formatter" => "SelectFormatter", "asyncPostRender" => "renderSelect", "source" => "account", "namefield" => "bank_name");
                $columns[] = Array("id" => "vendor", "name" => "Payable To", "field" => "vendor", "sortable" => false, "resizable" => true, "formatter" => "SelectFormatter", "asyncPostRender" => "renderSelect", "source" => "profile", "namefield" => "vendor_name");
                $columns[] = Array("id" => "frequency", "name" => "Frequency", "width" => 35,"field" => "interval", "sortable" => false, "resizable" => true);
                $columns[] = Array("id" => "last_date", "name" => "Last Date", "width" => 35,"field" => "start_date", "sortable" => false, "resizable" => true);
                
                $columns[] = Array("id" => "additional_expense", "name" => "Management fee", "width" => 35,"field" => "calcAmount", "sortable" => true, "resizable" => true, "formatter" => "InputFormatter", "format" => "usd", "asyncPostRender" => "renderInput", "valuefield" => "additional_expense");

                echo json_encode(Array("data" => $data, "columns" => $columns));
    }


    

    
    
}
