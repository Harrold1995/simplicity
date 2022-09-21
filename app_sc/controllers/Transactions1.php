<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions1 extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->meta['title'] = "Transactions";
        $this->meta['h2'] = "Transactions";
        $this->page_construct('transactions/index', null, $this->meta);
    }

    function getTransactionsDTData()
    {
        $data = Array("data" => Array());
        $data["data"][0] = (object)Array("type" => "Payment1", "date" => "1/10/2018", "amount" => "-$1,200.00",
            "name" => "ABC Contracting5", "reference" => "RC1564", "description" => "Rent Charge for January",
            "balance" => "$1,200.00",
            "details" => Array(
                (object)Array("type" => "type example", "amount" => "$444", "name" => "John Doe"),
                (object)Array("type" => "type example2", "amount" => "$564", "name" => "Jane Doe"),
            )
        );
        $data["data"][1] = (object)Array("type" => "Payment2", "date" => "1/5/2015", "amount" => "$1,800.00",
            "name" => "ABC Contracting1", "reference" => "RC1565", "description" => "Rent Charge for February",
            "balance" => "$2,300.00",
            "details" => Array(
                (object)Array("type" => "type example3", "amount" => "$784", "name" => "John Doe"),
                (object)Array("type" => "type example4", "amount" => "$984", "name" => "Jane Doe"),
            )
        );
        echo json_encode($data);
    }

    public function getTransactionsDTDataDetails($th_id, $tid)
    {
        $this->db->select('t.id, tt.name AS type, th.transaction_date AS date, CASE 
        WHEN (ac.debit_credit = "credit") THEN t.credit - t.debit 
        WHEN (ac.debit_credit = "debit") THEN t.debit - t.credit 
        END AS amount, CONCAT_WS("",p.first_name," ", p.last_name) AS name, IF(TRIM(th.transaction_ref)!="",th.transaction_ref,th.memo) AS reference, t.description');
        $this->db->from('transactions t');
        $this->db->join('accounts a', 'a.id = t.account_id');
        $this->db->join('transaction_header th', 'th.id = t.trans_id AND th.id =' . $th_id);
        $this->db->join('account_types at', 'at.id = a.account_types_id');
        $this->db->join('account_category ac', 'ac.id = at.account_category_id');
        $this->db->join('transaction_type tt', 'tt.id = th.transaction_type');
        $this->db->join('profiles p', 'p.id = t.profile_id','left');
        //$this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0');
        $this->db->where('th.id', $th_id);
        $this->db->where('t.id !=', $tid);
        $this->db->order_by('th.transaction_date DESC, t.trans_id DESC, t.id ASC');
        $q = $this->db->get();
        //print_r($this->db->last_query());
        if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }              
                 return $data;
        }
    }
    
    public function getTransactionsDTData2($itemId, $filter, $type = null)
    {
        $filters = $this->input->post('filters');
        //add to filter by min
    //     $this->db->select_min('t.id');
    //     $this->db->from('transactions t');
    //     $this->db->group_by('t.trans_id');
    //     $q = $this->db->get();

    //     if ($q->num_rows() > 0) {
    //         foreach (($q->result()) as &$row) {
    //             $min[] = $row->id;
    //         }              
    // }
        $sd = [$this->site->settings->security_deposits,$this->site->settings->lmr];
        $ap = $this->site->settings->accounts_payable;
        $ar = $this->site->settings->accounts_receivable;
        $bank = 1;
        $cc = 6;
        $this->db->select('t.id, th.id as did, pr.id as pid, t.profile_id as dprofile, th.id AS th_id, t.lease_id as dlease,  th.transaction_type as dtype, tt.name AS type, th.transaction_date AS date, pr.name AS Property, it.sales_description as item, CASE 
        WHEN (ac.debit_credit = "credit") THEN t.credit - t.debit 
        WHEN (ac.debit_credit = "debit") THEN t.debit - t.credit 
        END AS amount, CONCAT_WS("",p.first_name," ", p.last_name) AS name, IF(TRIM(th.transaction_ref)!="",th.transaction_ref,th.memo) AS reference, t.clr, ifnull(t.description,th.memo) as description, 
        CASE 
        WHEN (ac.debit_credit = "credit") THEN  ((t.credit - t.debit) - IFNULL(transum.amounts1,0))
        WHEN (ac.debit_credit = "debit") THEN ((t.debit - t.credit) - IFNULL(transum.amounts1,0))
        END AS balance2, 
        0 as balance, 
        t.account_id as aid, debit as debit, credit as credit,
        CASE WHEN (ac.debit_credit = "credit") THEN t.credit - t.debit 
        WHEN (ac.debit_credit = "debit") THEN t.debit - t.credit 
        END AS balance1, if(t.account_id IN('.$ap.','.$ar.'), if(ac.debit_credit = "credit",((t.credit-t.debit)-ifnull(transum.amounts1,0)),((t.debit-t.credit)-ifnull(transum.amounts1,0))) ,0  ) as amounts, if(recs.closed =1,1, if(isnull(t.rec_id), 0,2)) as clr');
        $this->db->from('transactions t');
        $this->db->join('accounts a', 'a.id = t.account_id');
        $this->db->join('reconciliations recs', 't.rec_id = recs.id','left');
        $this->db->join('transaction_header th', 'th.id = t.trans_id');
        $this->db->join('account_types at', 'at.id = a.account_types_id');
        $this->db->join('account_category ac', 'ac.id = at.account_category_id');
        $this->db->join('transaction_type tt', 'tt.id = th.transaction_type','left');
        $this->db->join('profiles p', 'p.id = t.profile_id','left');
        $this->db->join('properties pr', 'pr.id = t.property_id', 'left'); 
        $this->db->join('items it', 't.item_id = it.id', 'left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts1
                    FROM(
                        SELECT transaction_id_a AS trans_id, 0 - amount AS amount
                        FROM applied_payments
                        UNION ALL
                        SELECT transaction_id_b AS trans_id, amount 
                        FROM applied_payments
                        ) trans
                        GROUP BY trans_id
                        ) transum','t.id = transum.trans_id','left');
        //$this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0');
        $this->db->where($filter, $itemId);
        if(PFLAG==00) {$this->db->where_in('pr.id',explode( ',', trim(PROPERTIES, '()')));} 
        switch($type) {
            case 'tenant': $this->db->where('a.id', $this->site->settings->accounts_receivable); break;
            case 'sd': $this->db->where_in('a.id',$sd); break;
            case 'bills': $this->db->where('th.transaction_type', 2); break;
            //case 'vendor': $this->db->where('a.id', $this->site->settings->accounts_payable);
        }
        //can get rid of other filters now
        foreach ($filters as $f){
            $this->db->where($f['filter'], $f['ItemId']);
        }
        //$this->db->where('(CASE WHEN (ac.debit_credit = "credit") THEN (t.credit - t.debit) !=0 WHEN (ac.debit_credit = "debit") THEN (t.debit - t.credit) !=0 END)');
        // add to take out doubles but won't show all info unless you use the dropdown
        //$this->db->where_in('t.id',$min);
        $this->db->order_by('th.transaction_date DESC, t.trans_id DESC, t.id ASC');
        $q = $this->db->get();
        //echo $this->db->last_query();
        //echo "<script>console.log('$amount this is the amount');</script>";
        // if ($q->num_rows() > 0) {
        //     foreach (($q->result()) as &$row) {
        //     //    $amount= $this->getSingleAccountAmount($row->id);
        //     //         $row->balance = $amount;
        //          $info[] = $row;
        //     }
        //     //$data = new stdClass();
        //     $one = array_shift($info);
        //     $data['data'][0] = $one;
        //     //$object->data = $one;
        //     //$data[] = $one;
        //     $one->details = $info;
        //     //$array[] = $object;
        //     echo json_encode($data);
        // }

        // if ($q->num_rows() > 0) {
        //     foreach (($q->result()) as &$row) {
        //     //    $amount= $this->getSingleAccountAmount($row->id);
        //     //         $row->balance = $amount;
        //          $row->details = Array(
        //             (object)Array("type" => "type example3", "amount" => "$784", "name" => "John Doe"),
        //             (object)Array("type" => "type example4", "amount" => "$984", "name" => "Jane Doe"),
        //         );
        //          $info['data'][] = $row;
        //     }
            
        //     echo json_encode($info);
        // }
        $data = Array();
        $columns = Array();
        $columns[] = (Object)Array("id" => "type", "field" => "type", "name" => "Type", "formatter" => "NameFormatter", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "date", "field" => "date", "name" => "Date", "formatter" => "DateFormatter", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "name", "field" => "name", "name" => "Name", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "Property", "field" => "Property", "name" => "Property", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "Item", "field" => "item", "name" => "Item", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "reference", "field" => "reference", "name" => "Reference", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "description", "field" => "description", "name" => "Description", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "amounts", "field" => "amounts", "name" => "Open", "formatter" => "UsdFormatter", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "debit", "field" => "debit", "name" => "Debit", "formatter" => "UsdFormatter", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "credit", "field" => "credit", "name" => "Credit", "formatter" => "UsdFormatter", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "clr", "field" => "clr", "name" => "clr", "formatter" => "checkFormatter", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "amount", "field" => "amount", "name" => "Amount", "formatter" => "ColorUsdFormatter", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "balance", "field" => "balance", "name" => "Balance", "formatter" => "ColorUsdFormatter", "resizable" => true);
        

        if ($q->num_rows() > 0) {
            $data = $this->group_transactions($q->result(),$type);

        } else {
            $data[0] = (object)Array("id" => 0, "type" => " ", "date" => null, "amount" => null,
                "name" => "No Transactions for this account", "reference" => " ", "description" => " ",
                "balance" => null
            );
        }
        echo json_encode(Array("data"=> $data, "columns"=>$columns));
    }

    function ignoreBefore($bank, $date){
        $this->load->model('reconciliations_model');

        if($this->reconciliations_model->ignoreBefore($bank, $date)){
            // get updated trans data
            $data = $this->bankTransData($bank, 't.account_id');
            echo json_encode(array('type' => 'success', 'message' => 'Transactions before <b>'.$date.'<b> are now ignored. You can add/remove individual ignores on the grid to finetune your selection', 'transdata' => $data));
        }else{
            $errors = $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
        }
    }

    public function getBankTransData($itemId, $filter, $type = null)
    {   
        $filters = $this->input->post('filters');

        echo json_encode($this->bankTransData($itemId, $filter, $type));
    }
    public function bankTransData($itemId, $filter, $type = null)
    {   
        $ins_id = $this->db->get_where('plaid_banks', array('plaid_id' => $itemId))->row()->ins_id;
        $this->load->model('accounts_model');
        $this->accounts_model->get_trans_plaid($ins_id);
        $this->db->select('*, banks.property as property_id, banks.account_id as bank_account');
        $this->db->from('plaid_trans');
        $this->db->join('banks', ' JSON_EXTRACT(custom, "$.plaid_acct")=plaid_trans.account_id');
        $this->db->where('plaid_trans.account_id ="'.$itemId.'"');
        $this->db->where('plaid_trans.removed < 1');
        $q = $this->db->get();
       
        
        $data = Array();
        $columns = Array();
        //$columns[] = (Object)Array("id" => "type", "field" => "type", "name" => "Type", "formatter" => "NameFormatter", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "date", "field" => "date", "name" => "Date", "formatter" => "DateFormatter", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "amount", "field" => "amount", "name" => "Amount", "formatter" => "UsdFormatter", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "name", "field" => "name", "name" => "Name", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "reference", "field" => "reference", "name" => "Reference", "resizable" => true, "sortable" => true);
        $columns[] = (Object)Array("id" => "description", "field" => "description", "name" => "Description", "resizable" => true, "sortable" => true);
        //$columns[] = (Object)Array("id" => "balance", "field" => "balance", "name" => "Balance", "formatter" => "UsdFormatter", "resizable" => true);
        $columns[] = (Object)Array("id" => "trans_match", "field" => "trans_match", "name" => "Matched", "formatter" => "matchFormatter", "resizable" => true, "sortable" => true);
        

        if ($q->num_rows() > 0) {
            $data = $this->group_transactions($q->result(),$type);

        } else {
            $data[0] = (object)Array("id" => 0, "type" => " ", "date" => null, "amount" => null,
                "name" => "No Transactions for this account", "reference" => " ", "description" => " ",
                "balance" => null
            );
        }
        return Array("data"=> $data, "columns"=>$columns);
    }


    public function group_transactions($data, $type = null) {
        $result = array();
        $last_id = null;
        $last_i = 0;
        $i = 0;
        $lastbalance = 0;
        foreach($data as $t) {

                $last_id = $t->th_id;
                $result[] = $t;
                $result[$i]->id = $i;
                $result[$i]->_collapsed = true;
                if(($type=='vendor') && ($result[$i]->aid != $this->site->settings->accounts_payable)){
                    $result[$i]->balance1 = 0;
                }
                //$result[$i]->amount = 0;
                $result[$i]->header = true;
                //$lastbalance = $result[$i]->balance;
                $last_i = $i;
                $i++;



        }
        return $result;
    }

    public function getSingleAccountTransactions($id)
    {
        $theId = 1;
        if($id){
            $theId = $id;
        }
        $this->db->select('t.id, t.item_id, th.transaction_type AS type, t.debit,t.description, th.id AS th_id , t.clr, a.name, a.accno, th.transaction_date, th.transaction_ref,tt.name AS name2, CONCAT(p.first_name," ", p.last_name) AS vendor, 
        CASE 
        WHEN (ac.debit_credit = "credit") THEN t.credit - t.debit 
        WHEN (ac.debit_credit = "debit") THEN t.debit - t.credit 
        END AS balance,
        CASE 
        WHEN (ac.debit_credit = "credit") THEN  ((t.credit - t.debit) - IFNULL(transum.amounts1,0))
        WHEN (ac.debit_credit = "debit") THEN ((t.debit - t.credit) - IFNULL(transum.amounts1,0))
        END AS open_balance');
        $this->db->from('transactions t');
        $this->db->join('accounts a', 'a.id = t.account_id');
        $this->db->join('transaction_header th', 'th.id = t.trans_id');
        $this->db->join('account_types at', 'at.id = a.account_types_id');
        $this->db->join('account_category ac', 'ac.id = at.account_category_id');
        $this->db->join('transaction_type tt', 'tt.id = th.transaction_type');
        $this->db->join('profiles p', 'p.id = t.profile_id','left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0 - amount AS amount
        FROM applied_payments
        UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        //$this->db->where('((t.credit - t.debit) - IFNULL(transum.amounts,0))!= 0');
        $this->db->where('t.account_id', $theId);
        $this->db->order_by('th.transaction_date DESC, t.trans_id DESC, t.id ASC');
        $q = $this->db->get();
        //echo "<script>console.log('$amount this is the amount');</script>";
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                // $amount= $this->tryout($row->id);
                // $row->balance = $amount;
                $data[] = $row;
            }
            return $data;
        }
    }
    

    function sec()
    {
        $csrf = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
    );
    print_r($csrf);
    }


    function addTransaction()
    {
        $this->load->model('transactions_model');

        $header = $this->input->post('header');
        $transactions = $this->input->post('transactions');

        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&*/ $this->transactions_model->addTransaction($header, $transactions))
            echo json_encode(array('type' => 'success', 'message' => 'Transaction successfully added.'));
        /*else {
            echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('transaction')));
        }*/
    }

    function getModal()
    {
        $this->load->model('accounts_model');

        //$params = json_decode($this -> input -> post('params'));
        //$this -> data['profiles'] = $this -> tenants_model -> getTenants();
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'accounts/addAccount';
                $this->data['title'] = 'Add Account';
                $this->data['account_types'] = $this->accounts_model->getAccountTypes();
                $this->data['parents'] = $this->accounts_model->getParents();
                $this->data['classes'] = $this->accounts_model->getClasses();
                if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }
                break;
            case 'edit' :
                $this->data['target'] = 'accounts/editAccount/' . $this->input->post('id');
                $this->data['title'] = 'Edit Account';
                $this->data['account'] = $this->accounts_model->getAccount($this->input->post('id'));
                //$said = $this->data['account']->id;
                $this->data['account_types'] = $this->accounts_model->getAccountTypes();
                $this->data['parents'] = $this->accounts_model->getParents();
                $this->data['classes'] = $this->accounts_model->getClasses();
                $this->data['table'] = $this->accounts_model->getSpecialAccountName($this->data['account']->account_types_id);
                $this->data['specialAccount'] = $this->accounts_model->getSpecialAccount($this->data['account']->id);
                if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }
                break;
        }
        $this->load->view('forms/account/main', $this->data);
    }
}
