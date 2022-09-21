<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer_bal extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('transfer_bal_model');
    }

    function index()
    {
        $this->meta['title'] = "Investor";
        $this->meta['h2'] = "Investor";
        $this->page_construct('Investor/index', $this->data, $this->meta);
    }

    function getBalances(){
        $profile = $this->input->post('profile');
        $lease = $this->input->post('lease');
        $balances = $this->transfer_bal_model->getBalances($profile, $lease);
        echo $balances;
    }

    function addBal_transfer()
    { 
        $data = $this->input->post();
        $this->load->model('validate_model');
        $validate = $this->validate_model->validate("transfer_bal", $data);
        $header = ['transaction_date' => $data['date'], 'memo' => $data['memo']];
        $details = array();

        if ($data['rent'] <> 0 ) {
            $details[] =  ['account_id' => $this->site->settings->accounts_receivable, 'profile_id' => $data['profile1'], 'lease_id' => $data['transfer1']['lease_id'], 'credit' => $data['rent'], 'property_id' => $data['transfer1']['prop_id'],'unit_id' => $data['transfer1']['unit_id'], 'line_number' => 1];
            $details[] =  ['account_id' => $this->site->settings->accounts_receivable, 'profile_id' => $data['profile2'], 'lease_id' => $data['transfer2']['lease_id'], 'debit' => $data['rent'],'property_id' => $data['transfer2']['prop_id'],'unit_id' => $data['transfer2']['unit_id'], 'line_number' => 4];
        }

        if ($data['sd'] <> 0 ) {
            $details[] =  ['account_id' => $this->site->settings->security_deposits, 'profile_id' => $data['profile1'], 'lease_id' => $data['transfer1']['lease_id'], 'debit' => $data['sd'],'property_id' => $data['transfer1']['prop_id'],'unit_id' => $data['transfer1']['unit_id'], 'line_number' => 2];
            $details[] =  ['account_id' => $this->site->settings->security_deposits, 'profile_id' => $data['profile2'], 'lease_id' => $data['transfer2']['lease_id'], 'credit' => $data['sd'],'property_id' => $data['transfer2']['prop_id'],'unit_id' => $data['transfer2']['unit_id'], 'line_number' => 5];
        }

        if ($data['lmr'] <> 0 ) {
            $details[] =  ['account_id' => $this->site->settings->lmr, 'profile_id' => $data['profile1'], 'lease_id' => $data['transfer1']['lease_id'], 'debit' => $data['lmr'],'property_id' => $data['transfer1']['prop_id'],'unit_id' => $data['transfer1']['unit_id'], 'line_number' => 3];
            $details[] =  ['account_id' => $this->site->settings->lmr, 'profile_id' => $data['profile2'], 'lease_id' => $data['transfer2']['lease_id'], 'credit' => $data['lmr'],'property_id' => $data['transfer2']['prop_id'],'unit_id' => $data['transfer2']['unit_id'], 'line_number' => 6];
        }
        

        
        

        if ($validate['bool'] && $this->transfer_bal_model->addBal_transfer($header, $details)) {
            echo json_encode(array('type' => 'success', 'message' => 'Transfer completed successfully.'));
            return true;
        } else {
            $errors = $errors . validation_errors() . "</br>" . $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors));
            return false;
        }
    }

    function editBal_transfer()
    { 
        $data = $this->input->post();
        $this->load->model('validate_model');
        $validate = $this->validate_model->validate("transfer_bal", $data);
        $header = ['transaction_date' => $data['date'], 'memo' => $data['memo']];
        $id = $this->input->post('id');
        $details = array();
        $deletes = array();

        if ($data['rent'] <> 0 ) {
            if (isset($data['ar1'])){
                $details[] =  ['account_id' => $this->site->settings->accounts_receivable, 'id' => $data['ar1'], 'profile_id' => $data['profile1'], 'lease_id' => $data['transfer1']['lease_id'], 'credit' => $data['rent'], 'property_id' => $data['transfer1']['prop_id'],'unit_id' => $data['transfer1']['unit_id'], 'line_number' => 1];
                $details[] =  ['account_id' => $this->site->settings->accounts_receivable, 'id' => $data['ar2'], 'profile_id' => $data['profile2'], 'lease_id' => $data['transfer2']['lease_id'], 'debit' => $data['rent'],'property_id' => $data['transfer2']['prop_id'],'unit_id' => $data['transfer2']['unit_id'], 'line_number' => 4];
            } else {
                $details[] =  ['trans_id' => $id,'account_id' => $this->site->settings->accounts_receivable, 'profile_id' => $data['profile1'], 'lease_id' => $data['transfer1']['lease_id'], 'credit' => $data['rent'], 'property_id' => $data['transfer1']['prop_id'],'unit_id' => $data['transfer1']['unit_id'], 'line_number' => 1];
                $details[] =  ['trans_id' => $id,'account_id' => $this->site->settings->accounts_receivable, 'profile_id' => $data['profile2'], 'lease_id' => $data['transfer2']['lease_id'], 'debit' => $data['rent'],'property_id' => $data['transfer2']['prop_id'],'unit_id' => $data['transfer2']['unit_id'], 'line_number' => 4];
            }

        } else if (isset($data['ar1'])){
            $deletes[] = $data['ar1'];
            $deletes[] = $data['ar2'];
        }

        if ($data['sd'] <> 0 ) {
            if (isset($data['sd1'])){
                $details[] =  ['account_id' => $this->site->settings->security_deposits, 'id' => $data['sd1'], 'profile_id' => $data['profile1'], 'lease_id' => $data['transfer1']['lease_id'], 'debit' => $data['sd'],'property_id' => $data['transfer1']['prop_id'],'unit_id' => $data['transfer1']['unit_id'], 'line_number' => 2];
                $details[] =  ['account_id' => $this->site->settings->security_deposits, 'id' => $data['sd2'], 'profile_id' => $data['profile2'], 'lease_id' => $data['transfer2']['lease_id'], 'credit' => $data['sd'],'property_id' => $data['transfer2']['prop_id'],'unit_id' => $data['transfer2']['unit_id'], 'line_number' => 5];
            } else {
                $details[] =  ['trans_id' => $id,'account_id' => $this->site->settings->security_deposits, 'profile_id' => $data['profile1'], 'lease_id' => $data['transfer1']['lease_id'], 'debit' => $data['sd'],'property_id' => $data['transfer1']['prop_id'],'unit_id' => $data['transfer1']['unit_id'], 'line_number' => 2];
                $details[] =  ['trans_id' => $id,'account_id' => $this->site->settings->security_deposits, 'profile_id' => $data['profile2'], 'lease_id' => $data['transfer2']['lease_id'], 'credit' => $data['sd'],'property_id' => $data['transfer2']['prop_id'],'unit_id' => $data['transfer2']['unit_id'], 'line_number' => 5];
            }

        }  else if (isset($data['sd1'])){
            $deletes[] = $data['sd1'];
            $deletes[] = $data['sd2'];
        }

        if ($data['lmr'] <> 0 ) {
            if (isset($data['lmr1'])){
                $details[] =  ['account_id' => $this->site->settings->lmr, 'id' => $data['lmr1'],'profile_id' => $data['profile1'], 'lease_id' => $data['transfer1']['lease_id'], 'debit' => $data['lmr'],'property_id' => $data['transfer1']['prop_id'],'unit_id' => $data['transfer1']['unit_id'], 'line_number' => 3];
                $details[] =  ['account_id' => $this->site->settings->lmr, 'id' => $data['lmr2'], 'profile_id' => $data['profile2'], 'lease_id' => $data['transfer2']['lease_id'], 'credit' => $data['lmr'],'property_id' => $data['transfer2']['prop_id'],'unit_id' => $data['transfer2']['unit_id'], 'line_number' => 6];
            } else {
                $details[] =  ['trans_id' => $id,'account_id' => $this->site->settings->lmr, 'profile_id' => $data['profile1'], 'lease_id' => $data['transfer1']['lease_id'], 'debit' => $data['lmr'],'property_id' => $data['transfer1']['prop_id'],'unit_id' => $data['transfer1']['unit_id'], 'line_number' => 3];
                $details[] =  ['trans_id' => $id,'account_id' => $this->site->settings->lmr, 'profile_id' => $data['profile2'], 'lease_id' => $data['transfer2']['lease_id'], 'credit' => $data['lmr'],'property_id' => $data['transfer2']['prop_id'],'unit_id' => $data['transfer2']['unit_id'], 'line_number' => 6];
            }

        } else if (isset($data['lmr1'])){
            $deletes[] = $data['lmr1'];
            $deletes[] = $data['lmr2'];
        }

        if ($validate['bool'] && $this->transfer_bal_model->editBal_transfer($id, $header, $details, $deletes)) {
            echo json_encode(array('type' => 'success', 'message' => 'Transfer edited successfully.'));
            return true;
        } else {
            $errors = $errors . validation_errors() . "</br>" . $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors));
            return false;
        }
    }

    // function editInvestor($Iid = 0)
    // {
    //     $data = $this->input->post('tenant');
    //     $contact = $this->input->post('contact');
    //     $address = $this->input->post('address');
    //     $deletes = $this->input->post('delete');
       
    //     $delete = $this->input->post('confirm');
    //     if($deletes && $delete == NULL){
    //         $response = $this->investors_model->editInvestor($data, $contact, $address, $Iid, $deletes, $delete);
    //         echo json_encode(array('type' => 'warning', 'message' => $response));
    //         return;
    //     }
    //     if ($this->investors_model->editInvestor($data, $contact, $address, $Iid, $deletes, $delete))
    //         echo json_encode(array('type' => 'success', 'message' => 'Investor successfully updated.'));
    // }

    function getModal()
    {
        $params = json_decode($this->input->post('params'));
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'transfer_bal/addBal_transfer';
                $this->data['title'] = 'Balance transfer';
                $this->load->view('forms/transfer_bal', $this->data);
                break;

            case 'edit' :
                $this->data['target'] = 'transfer_bal/editBal_transfer/'. $this->input->post('id');;
                $this->data['title'] = 'Balance transfer';
                $this->data['edit'] = 'edit';
                $this->data['header'] = $this->transfer_bal_model->getHeaderEdit($this->input->post('id'));
                $this->data['transactions'] = $this->transfer_bal_model->getTransactionsEdit($this->input->post('id'));
                $this->data['from'] =  $this->data['transactions'][0]->profile_id."-".$this->data['transactions'][0]->lease_id;
                $this->data['to'] =  end($this->data['transactions'])->profile_id."-".end($this->data['transactions'])->lease_id;

                foreach ($this->data['transactions'] as &$transaction) {
                    if ($transaction->account_id == $this->site->settings->accounts_receivable) {
                        $this->data['arAmt'] = $transaction->debit - $transaction->credit;
                        $this->data['ar'][] = $transaction->id;
                    } else if($transaction->account_id == $this->site->settings->security_deposits){
                        $this->data['sdAmt'] = $transaction->credit - $transaction->debit;
                        $this->data['sd'][] = $transaction->id;
                    } else {
                        $this->data['lmrAmt'] = $transaction->credit - $transaction->debit;
                        $this->data['lmr'][] = $transaction->id;
                    }
                }


                $this->load->view('forms/transfer_bal', $this->data);
                break;
        }
    }
}
