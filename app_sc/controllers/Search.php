<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
        $this->load->library('encryption');
        $this->icons = Array("property" => "fas fa-building", "unit" => "far fa-building");
    }

    function sanitize($s) {
        return preg_replace("/[^a-zA-Z0-9. -]+/", "", $s);
    }

    function search()
    {
        $params = $this->input->post('searchString');
        $search_string = $this->sanitize($params);

        $this->load->model('tenants_model');
        $this->load->model('accounts_model');
        $this->load->model('transactions_model');

        $response = ["results" => []];
        $response["results"]["accounts"] = $this->accounts_model->searchAccounts($search_string);
        $response["results"]["tenants"] = $this->tenants_model->searchTenants($search_string);
        $response["results"]["transactions"] = $this->transactions_model->searchTransactions($search_string);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    function getModal()
    {   
        $params = json_decode($this->input->post('params'));
        $this->data['search_string'] = $this->sanitize($params->searchString);

        $this->load->view('forms/search/search', $this->data);
    }

    public function generate() {
        $this->db->select('transactions.id, transactions.description, transactions.credit, transactions.debit, 
        accounts.name as aname, transaction_type.name as tname, bills.terms, DATE(transaction_header.transaction_date) as date');
        $this->db->from('transactions ');
        $this->db->join('accounts', 'accounts.id = transactions.account_id', 'left');
        $this->db->join('transaction_header', 'transactions.trans_id = transaction_header.id', 'left');
        $this->db->join('transaction_type', 'transaction_header.transaction_type = transaction_type.id', 'left');
        $this->db->join('bills', 'transaction_header.id = bills.trans_id', 'left');
        $q = $this->db->limit('0, 50000')->get();
        echo $this->db->last_query();
        foreach($q->result() as $t) {
            $str = Array();
            $str[] = $t->description;
            $str[] = $t->credit;
            $str[] = $t->debit;
            $str[] = $t->aname;
            $str[] = $t->tname;
            $str[] = $t->terms;
            $str[] = $t->date;
            $this->db->insert('tsearch', Array('id' => $t->id, 'search' => implode('||', $str)));
        }


    }
}
