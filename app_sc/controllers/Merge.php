<?php defined('BASEPATH') OR exit('No direct script access allowed');
include 'app_sc/helpers/logs/logs.php';

class Merge extends MY_Controller
{
function mergeAccounts() {
        $data = $this->input->post();
        $a = $data['accounta'];
        $b = $data['accountb'];

        // check if they are both the same account type
        $ainfo =  $this->db->get_where('accounts', array('id' =>$a))->row();
        $binfo =  $this->db->get_where('accounts', array('id' =>$b))->row();


        // check if account is in company settings or system
        
        // change all transactions from a to b
        $q1 = $this->db->update('transactions', array('account_id' => $b), array('account_id' => $a));
        // change all transactions snapshots from a to b
        $q2 = $this->db->update('transactions_snapshot', array('account_id' => $b), array('account_id' => $a));

        // check if one of them has a bank/credit card/mortgages associated
                         //check for reconcilliations

                         //check for connected banks




        // check for connected items
        $q2 = $this->db->update('items', array('acct_income' => $b), array('acct_income' => $a));
        $q2 = $this->db->update('items', array('acct_expense' => $b), array('acct_expense' => $a));
        $q2 = $this->db->update('items', array('acct_asset' => $b), array('acct_asset' => $a));

        // check for notes

        $q2 = $this->db->update('notes', array('object_id' => $b), array('object_id' => $a, 'object_type_id' => 4));

        // check for documents

        $q2 = $this->db->update('documents', array('reference_id' => $b), array('reference_id' => $a, 'type' => 4));
        
        //// check insurance policies
        $q2 = $this->db->update('insurance_policies', array('prepaid_account' => $b), array('prepaid_account' => $a));
        $q2 = $this->db->update('insurance_policies', array('expense_account' => $b), array('expense_account' => $a));
        $q2 = $this->db->update('insurance_policies', array('payment_acct' => $b), array('payment_acct' => $a));

        //check memorized transactions
        $this->updateAutoCharges($a, $b, 'account_id');

        //check property tax

        // check utitlities

        //check if in report filters

        //check vendor default expense account

        //add log

         $this->load->model('logs_model');
         $update_title_log = new Log_General($this->ion_auth->get_user_id(), 'Merged Accounts', $a, "Merged Accounts", "Merged ".$a."(".$ainfo->name.") to ".$b."(".$ainfo->name.")");
		 $this->logs_model->add_log($update_title_log);

        // delete account b 

        $this->db->delete('accounts', array('id' => $a));

        echo json_encode(array('type' => 'success', 'message' => 'Accounts successfully merged.'));

       }   
}