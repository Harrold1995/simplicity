<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Charges extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
    }

    function index()
    {
        $this->meta['title'] = "Charges";
        $this->meta['h2'] = "Charges";
        $this->page_construct('journalIndex/index', null, $this->meta);
    }

    function newCharge()
    {
        $errors = "";
        $this->load->model('charges_model');
        $header = $this->input->post('header');
        $transaction = $this->input->post('transactions');
        
        $data = array('header' => $header, 'transactions' => $transaction);
        //$validate = $this->validate_model->validate("charges", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&$validate['bool'] &&*/ $this->charges_model->addCharge($header, $transaction))
            echo json_encode(array('type' => 'success', 'message' => 'Charge successfully added.'));
            else {
                $errors = $errors . validation_errors() ."</br>". $validate['msg'];
                echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
            }
    }

    function editCharge($id)
    {
        $errors = "";
        $this->load->model('charges_model');
        $header = $this->input->post('header');
        $transaction = $this->input->post('newCharge');
        $data = array('header' => $header, 'transactions' => $transaction);
        //$validate = $this->validate_model->validate("charges", $data);
        //$this->form_validation->set_rules($this->settings->accountFormValidation);
        if (/*$this->form_validation->run() &&$validate['bool'] &&*/ $this->charges_model->editCharge($header, $transaction, $id))
            echo json_encode(array('type' => 'success', 'message' => 'Charge successfully added.'));
            else {
                $errors = $errors . validation_errors() ."</br>". $validate['msg'];
                echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('transaction')));
            }
    }

    function getModal()
    {
        $this->load->model('leases_model');
        $this->load->model('units_model');
        $this->load->model('tenants_model');
        $this->load->model('charges_model');

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'Charges/newCharge';
                $this->data['title'] = 'Add charge';
                $this->data['properties'] = $this->charges_model->getAllProperties();
                $this->data['units'] = $this->units_model->getUnits();
                $this->data['tenants'] = $this->tenants_model->getAllTenants2();
                $this->data['leases'] = $this->charges_model->getAllLeases();
                $this->data['items'] = $this->leases_model->getAllItems();

                break;
            case 'edit' :
            //$this->load->model('journalEntry_model');
            $this->load->model('charges_model');
                $this->data['target'] = 'Charges/editCharge/' . $this->input->post('id');
                $this->data['title'] = 'Edit charge';
                $this->data['properties'] = $this->charges_model->getAllProperties();
                $this->data['units'] = $this->units_model->getUnits();
                $this->data['tenants'] = $this->tenants_model->getAllTenants2();
                $this->data['leases'] = $this->charges_model->getAllLeases();
                $this->data['items'] = $this->leases_model->getAllItems();
                $this->data['header'] = $this->charges_model->getHeader($this->input->post('id'));
                $this->data['transaction'] = $this->charges_model->getTransaction($this->input->post('id'));

                break;
        }
        $this->load->view('forms/charge/addChargeModel', $this->data);
    }

    public function t()
{
//     //(select * from your_table order by id desc limit 20) order by id; 
//     $this->db->select('(t.debit - t.credit) AS amount');
//     $this->db->from('transactions t');
//     $this->db->where('t.account_id', 451);
//     $this->db->where('t.lease_id', 1);
//     $this->db->order_by('id desc');
//     $this->db->limit(20);

//     $sql = $this->db->get_compiled_select();
//     $this->db->reset_query();

//     // select * from (
//     //     select * from your_table order by id desc limit 20
//     // ) tmp order by tmp.id asc
    

//     $this->db->select('*');
//     $this->db->from('(' . $sql . ')lt');
//     $this->db->order_by('lt.id desc');
//     $sql2 = $this->db->get_compiled_select();
//     echo $sql2;
        $lease_id = 6;
        $profile_id = 7;
        // $this->db->select('t.lease_id, SUM(debit - credit) AS totalBalance');
        // $this->db->from('transactions t');
        // $this->db->join('leases l', 't.lease_id = l.id');
        // $this->db->join('leases_profiles lp', 'lp.lease_id = l.id');
        // $this->db->where('t.account_id', 451);
        // //$this->db->where('t.lease_id', $lease_id);
        // $this->db->where('(IF(l.bill_collectively = 0, t.profile_id = lp.profile_id AND t.lease_id =' . $lease_id .', t.lease_id = ' . $lease_id .'))'); 


        

        
            
 
            $this->db->select('l.id, lp.profile_id, CONCAT_WS(" ",p.first_name,p.last_name) AS name, p.address_line_1, p.address_line_2, CONCAT(p.city,", ",p.state) AS cs, p.area_code AS zip, l.bill_collectively');
            $this->db->from('leases l');
            $this->db->join('leases_profiles lp', 'l.id = lp.lease_id');
            $this->db->join('profiles p', 'lp.profile_id = p.id AND p.active = 1');
            
            $this->db->where('l.id', $lease_id);

            $sql = $this->db->get_compiled_select();
            $this->db->reset_query();


        $this->db->select('t.lease_id, sq.profile_id,  SUM(t.debit - t.credit) AS totalBalance');
        $this->db->from('transactions t');
        $this->db->join('(' . $sql . ')sq', 't.lease_id = sq.id');
        $this->db->where('t.account_id', $this->site->settings->accounts_receivable);
        $this->db->where('t.lease_id', $lease_id);
        $this->db->where('(IF(sq.bill_collectively = 0, t.profile_id = sq.profile_id AND t.lease_id =' . $lease_id .', t.lease_id = ' . $lease_id .'))'); 
        $this->db->group_by('t.lease_id'); // won't work need to group by profile also but then won't give total for leases with all profiles in sum unless group_concat but then they are on the same line  
        $q = $this->db->get_compiled_select();
            echo $q;


    
 }
public function test(){

        $this->db->select('SUM(debit - credit) as balance, lease_id, profile_id');
        $this->db->from('transactions');
        $this->db->where('account_id', 451);
        $this->db->group_by('lease_id, profile_id');

        $indivbal = $this->db->get_compiled_select();
        $this->db->reset_query();

        //SELECT sum(debit - credit) as balance, lease_id FROM transactions WHERE account_id = '$this->ar' GROUP BY lease_id
        $this->db->select('SUM(debit - credit) as balance, lease_id');
        $this->db->from('transactions');
        $this->db->where('account_id', 451);
        $this->db->group_by('lease_id');

        $colbal = $this->db->get_compiled_select();
        $this->db->reset_query();




        $this->db->select('l.id, lp.profile_id, CONCAT_WS(" ",p.first_name,p.last_name) AS name, p.address_line_1, p.address_line_2, CONCAT(p.city,", ",p.state) AS cs, p.area_code AS zip, l.bill_collectively, IF (l.bill_collectively IS NULL , indivbal.balance, colbal.balance) AS Tbalance, IFNULL(e.name, pr.name) as eName, IFNULL(e.address, pr.address) as eAddress, IFNULL(e.city, pr.city) as eCity, IFNULL(e.state, pr.state) as eState, IFNULL(e.zip, pr.zip) as Ezip, IFNULL(e.email, "") as eEmail, IFNULL(e.phone, "") as ePhone, pr.name as property, u.name as unit');
        $this->db->from('leases l');
        $this->db->join('leases_profiles lp', 'lp.lease_id = l.id');
        $this->db->join('units u', 'u.id = l.unit_id', 'left');
        $this->db->join('properties pr', 'pr.id = u.property_id', 'left');
        $this->db->join('entities e', 'e.id = pr.entity_id', 'left');
        $this->db->join('profiles p', 'lp.profile_id = p.id AND p.active = 1');
        $this->db->join('(' . $indivbal . ')indivbal', 'lp.profile_id = indivbal.profile_id AND l.id = indivbal.lease_id');
        $this->db->join('(' . $colbal . ')colbal', 'l.id = colbal.lease_id');

        $this->db->where('lp.profile_id', 7); 
        $this->db->where('l.id', 2);
      
            //$this->db->where('(l.start <= CURDATE() AND l.end > CURDATE())');
            //$this->db->or_where('(IF(l.bill_collectively IS NULL, indivbal.balance > 0, colbal.balance > 0) (IF(l.bill_collectively IS NULL, indivbal.balance > 0, colbal.balance > 0)))'); 

            $this->db->where('((l.start <= CURDATE() AND l.end > CURDATE()) OR (IF(l.bill_collectively IS NULL, indivbal.balance > 0, colbal.balance > 0)))');
            //$this->db->or_where('(IF(l.bill_collectively IS NULL, indivbal.balance > 0, colbal.balance > 0))'); 
            $q = $this->db->get_compiled_select();
            echo $q;
}

 public function getLastTrans()
    {
        //(select * from your_table order by id desc limit 20) order by id; 
        $this->db->select('t.id, th.transaction_date, tt.name AS type, (t.debit - t.credit) AS amount');
        $this->db->from('transactions t');
        $this->db->join('transaction_header th', 't.trans_id = th.id');
        $this->db->join('transaction_type tt', 'th.transaction_type = tt.id');
        $this->db->where('t.account_id', $this->site->settings->accounts_receivable);
        $this->db->where('t.lease_id', 6);
        if(!$bill_collectively){$this->db->where('t.profile_id', 7);}
        $this->db->order_by('t.id DESC');
        $this->db->limit(20);

        $sql = $this->db->get_compiled_select();
        //$this->db->reset_query();

        // select * from (
        //     select * from your_table order by id desc limit 20
        // ) tmp order by tmp.id asc
    //SELECT * FROM (
    //     SELECT (t.debit - t.credit) AS amount FROM `transactions` `t` WHERE `t`.`account_id` = 451 AND `t`.`lease_id` = 1 ORDER BY `id` desc LIMIT 20
    //     )lt ORDER BY `lt`.`id` desc  

        $this->db->select('*');
        $this->db->from('(' . $sql . ')lt');
        $this->db->order_by('lt.id ASC');

        $q = $this->db->get_compiled_select();
        echo $q;return
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $q->row_array();
            }
            return $this->getBalanceInfo($totalBalance, $data);
        } 
    }

 public function getProfiles($lease, $profile)
    {
        //SELECT sum(debit - credit) as balance, lease_id, profile_id FROM transactions WHERE account_id = '$this->ar' GROUP BY lease_id, profile_id
        $this->db->select('SUM(debit - credit) as balance, lease_id, profile_id');
        $this->db->from('transactions');
        $this->db->where('account_id', $this->ar);
        $this->db->group_by('lease_id, profile_id');

        $indivbal = $this->db->get_compiled_select();
        $this->db->reset_query();

        //SELECT sum(debit - credit) as balance, lease_id FROM transactions WHERE account_id = '$this->ar' GROUP BY lease_id
        $this->db->select('SUM(debit - credit) as balance, lease_id');
        $this->db->from('transactions');
        $this->db->where('account_id', $this->ar);
        $this->db->group_by('lease_id');

        $colbal = $this->db->get_compiled_select();
        $this->db->reset_query();

        $this->db->select('l.id, lp.profile_id, CONCAT_WS(" ",p.first_name,p.last_name) AS name, p.address_line_1, p.address_line_2, CONCAT(p.city,", ",p.state) AS cs, p.area_code AS zip, l.bill_collectively, IF (l.bill_collectively IS NULL , indivbal.balance, colbal.balance) AS Tbalance, IFNULL(e.name, pr.name) as eName, IFNULL(e.address, pr.address) as eAddress, IFNULL(e.city, pr.city) as eCity, IFNULL(e.state, pr.state) as eState, IFNULL(e.zip, pr.zip) as Ezip, IFNULL(e.email, "") as eEmail, IFNULL(e.phone, "") as ePhone, pr.name as property, u.name as unit');
        $this->db->from('leases l');
        $this->db->join('leases_profiles lp', 'lp.lease_id = l.id');
        $this->db->join('units u', 'u.id = l.unit_id', 'left');
        $this->db->join('properties pr', 'pr.id = u.property_id', 'left');
        $this->db->join('entities e', 'e.id = pr.entity_id', 'left');
        $this->db->join('profiles p', 'lp.profile_id = p.id AND p.active = 1');
        $this->db->join('(' . $indivbal . ')indivbal', 'lp.profile_id = indivbal.profile_id AND l.id = indivbal.lease_id');
        $this->db->join('(' . $colbal . ')colbal', 'l.id = colbal.lease_id');

        $this->db->where('lp.profile_id', $profile); 
        $this->db->where('l.id', $lease);
      
        $this->db->where('((l.start <= CURDATE() AND l.end > CURDATE()) OR (IF(l.bill_collectively IS NULL, indivbal.balance > 0, colbal.balance > 0)))');
            
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $row->data = $this->getLastTrans($row->id, $row->profile_id, $row->bill_collectively, $row->Tbalance);
                $allData[] = $row;
            }
            return $allData;
        }
        return null;
    }

    
}
