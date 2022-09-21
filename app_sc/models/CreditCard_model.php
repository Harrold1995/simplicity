<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CreditCard_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function getCC()
    {
        $this->db->select('id, name');
        $this->db->from('accounts');
        $this->db->where('account_types_id',6);
        $this->db->ORDER_BY('id','DESC');
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;

    }

    public function getCC1()
    {
        $this->db->select('id, name');
        $this->db->from('accounts');
        $this->db->where('account_types_id',6);
        $this->db->ORDER_BY('id','DESC');
        $this->db->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return null;

    }

    public function getProperties($id=0)
    {
        $this->db->select('p.id, p.name, p.active');//, pa.property_id
        $this->db->from('properties p');
        //$this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id,'left');

        $this->db->where('p.active', 1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getAllAccounts()
    {
        $this->db->select('id, name, accno, all_props, parent_id, active');
        $this->db->from('accounts');
        $this->db->where('active', 1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return array();
    }

    function getPropertyAccounts($pid)
    {  
        $this->db->select('id, name, parent_id');
        $this->db->from('accounts');
        $this->db->where('(all_props = 1 AND active = 1) OR (id IN (SELECT account_id FROM property_accounts WHERE property_id =' . $this->db->escape($pid) . ') AND active = 1)');
        $this->db->distinct();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
          $data =  $this->site->getNestedSelect($data);
          
          return $data;
        }
        return array();
    }

    public function getPropertyUnits($pid, $addtype = false)
    {   
        $this->db->select('id, name, parent_id');
        $this->db->from('units');
        $this->db->where('property_id', $pid);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'account_type';
                $data[] = $row;
            }
             $data =  $this->site->getNestedSelect($data);
             return $data;

        }
        return null;
    }

    public function getOfxImports($aid)
    {
        $this->db->select('id, date, (0 - amount) AS amount, IFNULL(CONCAT(name," ",memo),"No Description") AS description, uniqueId AS ref, card_member');
        $this->db->from('ofx_imports');
        // if($startDate AND $endDate){
        //     $this->db->where('date BETWEEN' . $startDate . 'AND' . $endDate);
        // }
        // if($startDate AND $endDate == NULL){
        //     $this->db->where('date >=', $startDate);
        // }
        $this->db->where('account_id', $aid);
        $this->db->where('trans_id', NULL);
        $this->db->ORDER_BY('date','DESC');
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;

    }

    
//     public function recordTransactionsOld($ccAccount_id,  $transactions, $details)
//     {   
//         $ar = $this->site->settings->accounts_receivable;
//         $ap =$this->site->settings->accounts_payable;

//         $this->db->trans_start();
//         $this->db->select('cc.property_id, cc.vendor, p.vendor_profile_id');
//         $this->db->from('credit_cards cc');
//         $this->db->join('properties p', 'cc.property_id = p.id');
//         $this->db->where('cc.account_id', $ccAccount_id);
//         $q = $this->db->get();
//         $Ccproperty_id = $q->row()->property_id;
//         $vendor = $q->row()->vendor;
//         $vp_id = $q->row()->vendor_profile_id;
//         $insert = 0;
//         foreach($transactions as &$transaction){
//             $cp_id = NULL;
//             $ofx_id = $transaction['ofxId'];    
//             $transaction_date = $transaction['transaction_date'];
//             $transaction_ref = $transaction['transaction_ref'];
//             $property_id = $transaction['property_id'];
//             $description = $transaction['description'];
//             $unit_id = $transaction['unit_id'];
//             $account_id = $transaction['account_id'];

//             //$total = array_sum(array_column($transaction, 'amount'));
//             $transAmount = $transaction['amount'];
//             // $total > 0 ? $amount = 'credit' : $amount = 'credit'; 
//             // $amount = $total > 0 ?  'credit' :  'debit'; 
//             //if (array_search(1 > 0, array_column($details, 'amount')) !== FALSE)
//             $error = [];

//             if(array_key_exists($ofx_id, $details)  && (array_sum(array_column($details[$ofx_id], 'amount')) > 0 || array_sum(array_column($details[$ofx_id], 'amount')) < 0)){
//                 $total = array_sum(array_column($details[$ofx_id], 'amount'));
//                 if($transAmount != $total){
//                     $error[] = $transaction_ref; 
//                     continue;
//                 }
//             }    
//             if($transAmount > 0) { $amount = 'credit'; }
//             else{ $amount = 'debit';  
//                 $transAmount = $transAmount * -1;
//             }
            
//             $header = ['transaction_type' => 9,'transaction_ref' => $transaction_ref, 'transaction_date' => $transaction_date, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
//             $this->db->insert('transaction_header', $header);
//             $trans_id = $this->db->insert_id();
//             $this->db->where('id', $ofx_id);
//             $this->db->update('ofx_imports', array('trans_id' => $trans_id));
//             $ccAcc = ['account_id' => $ccAccount_id,  'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>$vendor];
//             $this->db->insert('transactions', $ccAcc);

//             //$transacionDetails = [];
//             $i = 0;
//             if(array_key_exists($ofx_id, $details)  && (array_sum(array_column($details[$ofx_id], 'amount')) > 0 || array_sum(array_column($details[$ofx_id], 'amount')) < 0)){
//                 foreach($details[$ofx_id] as &$detail){
                    
//                     $i++;
//                     //unset($detail["ofx_id"]); 
//                     $detail['trans_id'] = $trans_id;
//                     $detail['line_number'] = $i;
//                     if($detail['amount'] > 0) {
//                         $detail['debit'] = $detail['amount'];
//                         $detail['credit'] = 0;
//                         unset($detail["amount"]); 
//                         if($Ccproperty_id != $detail['property_id']){
//                             $this->db->select('p.customer_profile_id');
//                             $this->db->from('properties p');
//                             $this->db->where('p.id', $detail['property_id']);
//                             $q = $this->db->get();
//                             $cp_id = $q->row()->customer_profile_id;
//                             $accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, 'credit' => $detail['credit'], 'debit' => $detail['debit'], 'line_number' => ++$i];
//                             $accountsPayable = ['account_id' => $ap, 'property_id' => $detail['property_id'], 'unit_id' => $detail['unit_id'], 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $vp_id,'credit' => $detail['debit'], 'debit' => $detail['credit'], 'line_number' => ++$i];
//                             $detail['profile_id'] = $vp_id;
//                             $this->db->insert('transactions', $accountsReceivable);
//                             $this->db->insert('transactions', $accountsPayable);
//                         }

//                         $this->db->insert('transactions', $detail);
//                      }
//                     elseif($detail['amount'] < 0) { 
//                         $detail['credit'] = $detail['amount'] * -1;
//                         $detail['debit'] = 0;
//                         unset($detail["amount"]); 
//                         if($Ccproperty_id != $detail['property_id']){
//                             $this->db->select('p.customer_profile_id');
//                             $this->db->from('properties p');
//                             $this->db->where('p.id', $detail['property_id']);
//                             $q = $this->db->get();
//                             $cp_id = $q->row()->customer_profile_id;
//                             $accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, 'credit' => $detail['credit'], 'debit' => $detail['debit'], 'line_number' => ++$i];
//                             $accountsPayable = ['account_id' => $ap, 'property_id' => $detail['property_id'], 'unit_id' => $detail['unit_id'], 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $vp_id,'credit' => $detail['debit'], 'debit' => $detail['credit'], 'line_number' => ++$i];
//                             $detail['profile_id'] = $vp_id;
//                             $this->db->insert('transactions', $accountsReceivable);
//                             $this->db->insert('transactions', $accountsPayable);
//                         }
//                         $this->db->insert('transactions', $detail);
//                     }
//                 }
//             }else{
//                 $i = 0;
//                 if($transAmount > 0) { $amount = 'debit'; }
//             else{ $amount = 'credit';  
//                 $transAmount = $transAmount * -1;
//             }

//             if($Ccproperty_id != $property_id){
//                 $this->db->select('p.customer_profile_id');
//                 $this->db->from('properties p');
//                 $this->db->where('p.id', $property_id);
//                 $q = $this->db->get();
//                 $cp_id = $q->row()->customer_profile_id;
//                 $accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, $amount => $transAmount, 'line_number' => ++$i];
//                 $accountsPayable = ['account_id' => $ap, 'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $vp_id, (($amount == 'debit') ? 'credit' : 'debit') => $transAmount, 'line_number' => ++$i];
//                 $this->db->insert('transactions', $accountsReceivable);
//                 $this->db->insert('transactions', $accountsPayable);
//                 }

//             $transaction = ['account_id' => $account_id,  'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => $description, 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>  ($cp_id ? $vp_id : ''), 'line_number' => ++$i];
//             $this->db->insert('transactions', $transaction);
//             }
//             $insert++;
//         }
        
//         $this->db->trans_complete();
//         if(!empty($error)){
//             $message = 'The following transaction amounts don\'t equal: ' . implode(", ", $error); 
//         }
//             $warning = new stdClass();
//             $warning->message = $message;
//             $warning->statusMessage = $insert . ' transactions inserted';
//             $warning->status = $insert;  
//             return $warning;
// }


// public function recordTransactionsLastWorking($ccAccount_id , $Ccproperty_id, $vendor, $vp_id, $transaction, $details)
//     {   
//         $ar = $this->site->settings->accounts_receivable;
//         $ap =$this->site->settings->accounts_payable;

//         //foreach($transactions as &$transaction){
//             $cp_id = NULL;
//             $ofx_id = $transaction['ofxId'];    
//             $transaction_date = $transaction['transaction_date'];
//             $transaction_ref = $transaction['transaction_ref'];
//             $property_id = $transaction['property_id'];
//             $description = $transaction['description'];
//             $unit_id = $transaction['unit_id'];
//             $account_id = $transaction['account_id'];

//             //$total = array_sum(array_column($transaction, 'amount'));
//             $transAmount = $transaction['amount'];
//             // $total > 0 ? $amount = 'credit' : $amount = 'credit'; 
//             // $amount = $total > 0 ?  'credit' :  'debit'; 
//             //if (array_search(1 > 0, array_column($details, 'amount')) !== FALSE)
//             //$error = [];

//             // if(array_key_exists($ofx_id, $details)  && (array_sum(array_column($details[$ofx_id], 'amount')) > 0 || array_sum(array_column($details[$ofx_id], 'amount')) < 0)){
//             //     $total = array_sum(array_column($details[$ofx_id], 'amount'));
//             //     if($transAmount != $total){
//             //         $error[] = $transaction_ref; 
//             //         continue;
//             //     }
//             // }    
//             if($transAmount > 0) { $amount = 'credit'; }
//             else{ $amount = 'debit';  
//                 $transAmount = $transAmount * -1;
//             }
//             $this->db->trans_start();
            
//             $header = ['transaction_type' => 9,'transaction_ref' => $transaction_ref, 'transaction_date' => $transaction_date, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
//             $this->db->insert('transaction_header', $header);
//             $trans_id = $this->db->insert_id();
//             $this->db->where('id', $ofx_id);
//             $this->db->update('ofx_imports', array('trans_id' => $trans_id));
//             $ccAcc = ['account_id' => $ccAccount_id,  'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>$vendor];
//             $this->db->insert('transactions', $ccAcc);

//             //$transacionDetails = [];
//             $i = 0;
//             if(array_key_exists($ofx_id, $details)  && ((array_sum(array_column($details[$ofx_id], 'amount')) > 0) || (array_sum(array_column($details[$ofx_id], 'amount')) < 0))){

//                 foreach($details[$ofx_id] as &$detail){
                    
//                     $i++;
//                     //unset($detail["ofx_id"]); 
//                     $detail['trans_id'] = $trans_id;
//                     $detail['line_number'] = $i;
//                     if($detail['amount'] > 0) {
//                         $detail['debit'] = $detail['amount'];
//                         $detail['credit'] = 0;
//                         unset($detail["amount"]); 
//                         if($Ccproperty_id != $detail['property_id']){
//                             $this->db->select('p.customer_profile_id');
//                             $this->db->from('properties p');
//                             $this->db->where('p.id', $detail['property_id']);
//                             $q = $this->db->get();
//                             $cp_id = $q->row()->customer_profile_id;
//                             $accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, 'credit' => $detail['credit'], 'debit' => $detail['debit'], 'line_number' => ++$i];
//                             $accountsPayable = ['account_id' => $ap, 'property_id' => $detail['property_id'], 'unit_id' => $detail['unit_id'], 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $vp_id,'credit' => $detail['debit'], 'debit' => $detail['credit'], 'line_number' => ++$i];
//                             $detail['profile_id'] = $vp_id;
//                             $this->db->insert('transactions', $accountsReceivable);
//                             $this->db->insert('transactions', $accountsPayable);
//                         }

//                         $this->db->insert('transactions', $detail);
//                      }
//                     elseif($detail['amount'] < 0) { 
//                         $detail['credit'] = $detail['amount'] * -1;
//                         $detail['debit'] = 0;
//                         unset($detail["amount"]); 
//                         if($Ccproperty_id != $detail['property_id']){
//                             $this->db->select('p.customer_profile_id');
//                             $this->db->from('properties p');
//                             $this->db->where('p.id', $detail['property_id']);
//                             $q = $this->db->get();
//                             $cp_id = $q->row()->customer_profile_id;
//                             $accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, 'credit' => $detail['credit'], 'debit' => $detail['debit'], 'line_number' => ++$i];
//                             $accountsPayable = ['account_id' => $ap, 'property_id' => $detail['property_id'], 'unit_id' => $detail['unit_id'], 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $vp_id,'credit' => $detail['debit'], 'debit' => $detail['credit'], 'line_number' => ++$i];
//                             $detail['profile_id'] = $vp_id;
//                             $this->db->insert('transactions', $accountsReceivable);
//                             $this->db->insert('transactions', $accountsPayable);
//                         }
//                         $this->db->insert('transactions', $detail);
//                     }
//                 }
//             }else{
//                 $i = 0;
//                 if($transAmount > 0) { $amount = 'debit'; }
//             else{ $amount = 'credit';  
//                 $transAmount = $transAmount * -1;
//             }

//             if($Ccproperty_id != $property_id){
//                 $this->db->select('p.customer_profile_id');
//                 $this->db->from('properties p');
//                 $this->db->where('p.id', $property_id);
//                 $q = $this->db->get();
//                 $cp_id = $q->row()->customer_profile_id;
//                 $accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, $amount => $transAmount, 'line_number' => ++$i];
//                 $accountsPayable = ['account_id' => $ap, 'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $vp_id, (($amount == 'debit') ? 'credit' : 'debit') => $transAmount, 'line_number' => ++$i];
//                 $this->db->insert('transactions', $accountsReceivable);
//                 $this->db->insert('transactions', $accountsPayable);
//                 }

//             $transaction = ['account_id' => $account_id,  'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => $description, 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>  ($cp_id ? $vp_id : ''), 'line_number' => ++$i];
//             $this->db->insert('transactions', $transaction);
//             }
//             $this->db->trans_complete();
//             return true;
//        // }
        
        
       
        
// }
function getAccount($id){
    $q = $this->db->select('name')->from('accounts')->where('id', $id)->get();
    if ($q->num_rows() > 0) {
        return $q->row()->name;
    }
    return null;
}

function getProperty($id){
    $q = $this->db->select('name')->from('properties')->where('id', $id)->get();
    if ($q->num_rows() > 0) {
        return $q->row()->name;
    }
    return null;
}

function addQuotes($elem){
    return '"'.$elem.'"';
 }

function createFile(){
    $time = date('YmdHis');
    $file = $time .'.IIF';
    $cont = 
    "!TRNS,TRNSTYPE,DATE,ACCNT,NAME,AMOUNT,DOCNUM,MEMO,CLASS\r\n\r\n".

    "!SPL,TRNSTYPE,DATE,ACCNT,NAME,AMOUNT,DOCNUM,MEMO,CLASS\r\n\r\n".

    "!ENDTRNS\r\n\r\n";

    file_put_contents($file, $cont);
    return $file;
}

function appendFile($transaction, $file){
$b = array_map(function($elem){
    return '"'.$elem.'"';
 }, $transaction);
$a = implode(",",$b);
$a .= "\r\n\r\n";
file_put_contents($file, $a ,FILE_APPEND);
// $ar = 'account Receivable';$Ccproperty_id = 'cc';$description = 'description';
// //$accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, 'credit' => $detail['credit'], 'debit' => $detail['debit']]; //, 'line_number' => ++$i]
// $accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description];

//$a .= "\r\n\r\n\"ENDTRNS\"\r\n\r\n";
}

function endtrns($file){
    $endTransaction = "ENDTRNS\r\n\r\n";
    file_put_contents($file, $endTransaction ,FILE_APPEND);
}

public function recordTransactions($ccAccount_id , $Ccproperty_id, $vendor, $vp_id, $transaction, $details, $file)
    {   
        // $file = $this->createFile();
        
        $ar = $this->site->settings->accounts_receivable;
        $ap =$this->site->settings->accounts_payable;

        //foreach($transactions as &$transaction){
            $cp_id = NULL;
            $ofx_id = $transaction['ofxId'];    
            $transaction_date = $transaction['transaction_date'];
            $card_member = $transaction['card_member'];
            $iifDate = humanDate($transaction_date);
            $transaction_ref = $transaction['transaction_ref'];
            $property_id = $transaction['property_id'];
            $transPropName = $this->getProperty($property_id);
            $description = $transaction['description'];
            $unit_id = $transaction['unit_id'];
            $account_id = $transaction['account_id'];
            $transAccntName = $this->getAccount($account_id);

           $transAmount = $transaction['amount'];
           
            if($transAmount > 0) { 
                $amount = 'credit'; 
                $ccExportAmount = $transaction['amount'] * -1;
            }
            else{ $amount = 'debit';  
                $transAmount = $transAmount * -1;
                $ccExportAmount = $transaction['amount'];
            }
            $this->db->trans_start();
            
            
            //export 
            // $ccAcc = ['account_id' => $ccAccount_id,  'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>$vendor];
            $ccAcc = ['t' => 'TRNS','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => $this->getAccount($ccAccount_id), 'NAME' =>$this->getProperty($Ccproperty_id), 'AMOUNT' => $ccExportAmount,  'DOCNUM' => $transaction_ref, 'MEMO' => trim($description), 'CLASS' => 'AMEX:' . $card_member];
            $this->appendFile($ccAcc, $file);

            //$transacionDetails = [];
            $i = 0;
            if(array_key_exists($ofx_id, $details)  && ((array_sum(array_column($details[$ofx_id], 'amount')) > 0) || (array_sum(array_column($details[$ofx_id], 'amount')) < 0))){
                foreach($details[$ofx_id] as &$expDetail){
                    if(trim($expDetail['amount']) == 0){
                        continue;
                    }
                    $accnt = $Ccproperty_id == $expDetail['property_id'] ? $this->getAccount($expDetail['account_id']) : 'MP amex';
                    $propertyName = $this->getProperty($expDetail['property_id']);
                    $expAmount = $expDetail['amount'];
                    //what to do if one is same property do we want it to be mp Amex also? if so we need another trans for it
                    $det = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => $accnt, 'NAME' =>$propertyName, 'AMOUNT' => $expAmount,  'DOCNUM' => $transaction_ref , 'MEMO' => trim($detail['description']), 'CLASS' => 'AMEX:' . $card_member];
                    $this->appendFile($det, $file);
                }
                $this->endtrns($file);

                $properties = array_unique(array_column($details[$ofx_id], 'property_id'));
                foreach($properties as $property){
                    if(trim($property) == 0 || $property == -1) {
                        continue;
                    }
                    $propertyName = $this->getProperty($property);
                    $detailsPerProp = array_filter($details[$ofx_id], function($v) use($property){
                        return  $v['property_id'] == $property;
                        });
                        if($Ccproperty_id != $property){
                            $this->db->select('p.customer_profile_id');
                            $this->db->from('properties p');
                            $this->db->where('p.id', $detail['property_id']);
                            $q = $this->db->get();
                            $cp_id = $q->row()->customer_profile_id;
                             
                            //   $detail['profile_id'] = $vp_id;
                            //$this->db->insert('transactions', $accountsReceivable);
                            
                            //$this->db->insert('transactions', $accountsPayable);
                            $totalPerProp = array_sum(array_column($detailsPerProp, 'amount')); 
                            if($totalPerProp > 0){
                                $apCredit  =   $totalPerProp;
                                $apDebit  =   0;
                            }else{
                                $apCredit  =   0;
                                $apDebit  =   $totalPerProp;
                            }
                        
                                                                                                                                                                            //$detail['debit']
                            $accountsReceivable = ['t' => 'TRNS','TRNSTYPE' => 'invoice', 'DATE' => $iifDate, 'ACCNT' => 'Accounts Receivable', 'NAME' =>$propertyName, 'AMOUNT' => $totalPerProp,  'DOCNUM' => $transaction_ref, 'MEMO' => trim($detail['description']), 'CLASS' => 'AMEX:' . $card_member];
                            $counterAccountsReceivable = ['t' => 'SPL','TRNSTYPE' => 'invoice', 'DATE' => $iifDate, 'ACCNT' => 'MP amex', 'NAME' =>$propertyName, 'AMOUNT' => $totalPerProp * -1,  'DOCNUM' => $transaction_ref, 'MEMO' => trim($detail['description']), 'CLASS' => 'AMEX:' . $card_member];
                            $this->appendFile($accountsReceivable, $file);
                            $this->appendFile($counterAccountsReceivable, $file);
                            $this->endtrns($file);

                            $header = ['transaction_type' => 2,'transaction_ref' => $transaction_ref, 'transaction_date' => $transaction_date, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
                            $this->db->insert('transaction_header', $header);
                            $trans_id = $this->db->insert_id();

                            $this->db->where('id', $ofx_id);
                            $this->db->update('ofx_imports', array('trans_id' => $trans_id));

                            $accountsPayable = ['account_id' => $ap, 'property_id' => $property, 'unit_id' => '', 'description' => trim($description), 'trans_id' => $trans_id, 'profile_id' => $vp_id,'credit' => $apCredit, 'debit' => $apDebit, 'line_number' => 0];
                            $this->db->insert('transactions', $accountsPayable);

                            foreach($detailsPerProp as &$detail){
                                $i++;
                                if(trim($detail['amount']) == 0){
                                    continue;
                                }
                                //unset($detail["ofx_id"]); 
                                $detail['profile_id'] = $vp_id;
                                $detail['trans_id'] = $trans_id;
                                $detail['line_number'] = $i;
                                $expAmount = $detail['amount'];
                                if($detail['amount'] > 0) {
                                    $detail['debit'] = $detail['amount'];
                                    $detail['credit'] = 0;
                                }elseif($detail['amount'] < 0) { 
                                    $detail['credit'] = $detail['amount'] * -1;
                                    $detail['debit'] = 0;
                                }
                                    unset($detail["amount"]); 
                                    $this->db->insert('transactions', $detail);
                            }
                        }
                    
                }
            }else{
                $i = 0;
                if($transAmount > 0) { $amount = 'debit'; }
            else{ $amount = 'credit';  
                $transAmount = $transAmount * -1;
            }

            if($Ccproperty_id != $property_id){
                $propertyName = 
                $this->db->select('p.customer_profile_id');
                $this->db->from('properties p');
                $this->db->where('p.id', $property_id);
                $q = $this->db->get();
                $cp_id = $q->row()->customer_profile_id;
                //                                                                         should this be MP amex
                //$transaction = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => $transAccntName, 'NAME' =>$transPropName, 'AMOUNT' => $ccExportAmount * -1,  'DOCNUM' => $transaction_ref , 'MEMO' => trim($description), 'CLASS' => 'AMEX:' . $card_member];
                $transaction = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => 'Accounts Receivable', 'NAME' =>$transPropName, 'AMOUNT' => $ccExportAmount * -1,  'DOCNUM' => $transaction_ref , 'MEMO' => trim($description), 'CLASS' => 'AMEX:' . $card_member];
                $this->appendFile($transaction, $file);
                $this->endtrns($file);
                //export
                if($amount == 'debit'){$negAmount = $transAmount;}
                if($amount == 'credit'){$negAmount = $transAmount * -1;}
                //$accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, $amount => $transAmount];// , 'line_number' => ++$i
                // $accountsReceivable = ['t' => 'TRNS','TRNSTYPE' => 'invoice', 'DATE' => $iifDate, 'ACCNT' => 'Accounts Receivable', 'NAME' =>$transPropName, 'AMOUNT' => $negAmount,  'DOCNUM' => $transaction_ref, 'MEMO' => trim($description), 'CLASS' => 'AMEX:' . $card_member];
                // //                                                                          or should this be $transAccntName
                // $counterAccountsReceivable = ['t' => 'SPL','TRNSTYPE' => 'invoice', 'DATE' => $iifDate, 'ACCNT' => 'MP amex', 'NAME' =>$transPropName, 'AMOUNT' => $negAmount * -1,  'DOCNUM' => $transaction_ref, 'MEMO' => trim($description), 'CLASS' => 'AMEX:' . $card_member];
                // $this->appendFile($accountsReceivable, $file);
                // $this->appendFile($counterAccountsReceivable, $file);
                // $this->endtrns($file);

                $header = ['transaction_type' => 2,'transaction_ref' => $transaction_ref, 'transaction_date' => $transaction_date, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
                $this->db->insert('transaction_header', $header);
                $trans_id = $this->db->insert_id();

                $this->db->where('id', $ofx_id);
                $this->db->update('ofx_imports', array('trans_id' => $trans_id));

                $accountsPayable = ['account_id' => $ap, 'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => trim($description), 'trans_id' => $trans_id, 'profile_id' => $vp_id, (($amount == 'debit') ? 'credit' : 'debit') => $transAmount, 'line_number' => 0];
                $this->db->insert('transactions', $accountsPayable);

                $transaction = ['account_id' => $account_id,  'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => trim($description), 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>  ($cp_id ? $vp_id : ''), 'line_number' => ++$i];
                $this->db->insert('transactions', $transaction);

                }else{
                    //export
                    //$transaction = ['account_id' => $account_id,  'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => $description, 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>  ($cp_id ? $vp_id : ''), 'line_number' => ++$i];
                    $transaction = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => $transAccntName, 'NAME' =>$transPropName, 'AMOUNT' => $ccExportAmount * -1,  'DOCNUM' => $transaction_ref , 'MEMO' => trim($description), 'CLASS' => ''];
                    $this->appendFile($transaction, $file);
                    $this->endtrns($file);

                    //$this->db->insert('transactions', $transaction);//export
                }

            }

            
            $this->db->trans_complete();
            return true;
       // }
        
        
       
        
}

public function recordTransactionsExOrNoEx($ccAccount_id , $Ccproperty_id, $vendor, $vp_id, $transaction, $details, $file)
    {   
        $ar = $this->site->settings->accounts_receivable;
        $ap =$this->site->settings->accounts_payable;

            $cp_id = NULL;
            $ofx_id = $transaction['ofxId'];    
            $transaction_date = $transaction['transaction_date'];
            $card_member = $transaction['card_member'];
            $iifDate = humanDate($transaction_date);
            $transaction_ref = $transaction['transaction_ref'];
            $property_id = $transaction['property_id'];
            $transPropName = $this->getProperty($property_id);
            $description = $transaction['description'];
            $unit_id = $transaction['unit_id'];
            $account_id = $transaction['account_id'];
            $transAccntName = $this->getAccount($account_id);

           $transAmount = $transaction['amount'];
           
            if($transAmount > 0) { 
                $amount = 'credit'; 
                $ccExportAmount = $transaction['amount'] * -1;
            }
            else{ $amount = 'debit';  
                $transAmount = $transAmount * -1;
                $ccExportAmount = $transaction['amount'];
            }
            $this->db->trans_start();

//           if(!$exporting){
//             $header = ['transaction_type' => 9,'transaction_ref' => $transaction_ref, 'transaction_date' => $transaction_date, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
//             $this->db->insert('transaction_header', $header);
//             $trans_id = $this->db->insert_id();
//             $this->db->where('id', $ofx_id);
//             $this->db->update('ofx_imports', array('trans_id' => $trans_id));
//             $ccAcc = ['account_id' => $ccAccount_id,  'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>$vendor];
//             $this->db->insert('transactions', $ccAcc);
//            }

            //if($exporting){
                $ccAcc = ['t' => 'TRNS','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => $this->getAccount($ccAccount_id), 'NAME' =>$this->getProperty($Ccproperty_id), 'AMOUNT' => $ccExportAmount,  'DOCNUM' => $transaction_ref, 'MEMO' => trim($description), 'CLASS' => 'AMEX:' . $card_member];
                $this->appendFile($ccAcc, $file);
            //} 
            
            //
            //$transacionDetails = [];
            $i = 0;
            if(array_key_exists($ofx_id, $details)  && ((array_sum(array_column($details[$ofx_id], 'amount')) > 0) || (array_sum(array_column($details[$ofx_id], 'amount')) < 0))){
                //if($exporting){
                foreach($details[$ofx_id] as &$expDetail){
                    if(trim($expDetail['amount']) == 0){
                        continue;
                    }
                    $accnt = $Ccproperty_id == $expDetail['property_id'] ? $this->getAccount($expDetail['account_id']) : 'MP amex';
                    $propertyName = $this->getProperty($expDetail['property_id']);
                    $expAmount = $expDetail['amount'];
                    //what to do if one is same property do we want it to be mp Amex also? if so we need another trans for it
                    $det = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => $accnt, 'NAME' =>$propertyName, 'AMOUNT' => $expAmount,  'DOCNUM' => $transaction_ref , 'MEMO' => trim($detail['description']), 'CLASS' => 'AMEX:' . $card_member];
                    $this->appendFile($det, $file);
                }
                $this->endtrns($file);
                //}
                $properties = array_unique(array_column($details[$ofx_id], 'property_id'));
                foreach($properties as $property){
                    if(trim($property) == 0 || $property == -1) {
                        continue;
                    }
                    //if($exporting){
                    $propertyName = $this->getProperty($property);
                    //}
                    $detailsPerProp = array_filter($details[$ofx_id], function($v) use($property){
                        return  $v['property_id'] == $property;
                        });
                        if($Ccproperty_id != $property){
                            $this->db->select('p.customer_profile_id');
                            $this->db->from('properties p');
                            $this->db->where('p.id', $detail['property_id']);
                            $q = $this->db->get();
                            $cp_id = $q->row()->customer_profile_id;
                             
                            //   $detail['profile_id'] = $vp_id;
                            //$this->db->insert('transactions', $accountsReceivable);
                            
                            //$this->db->insert('transactions', $accountsPayable);
                            $totalPerProp = array_sum(array_column($detailsPerProp, 'amount')); 
                            if($totalPerProp > 0){
                                $apCredit  =   $totalPerProp;
                                $apDebit  =   0;
                            }else{
                                $apCredit  =   0;
                                $apDebit  =   $totalPerProp;
                            }
                        
                            //if($exporting){                                                                                                                                                //$detail['debit']
                            $accountsReceivable = ['t' => 'TRNS','TRNSTYPE' => 'invoice', 'DATE' => $iifDate, 'ACCNT' => 'Accounts Receivable', 'NAME' =>$propertyName, 'AMOUNT' => $totalPerProp,  'DOCNUM' => $transaction_ref, 'MEMO' => trim($detail['description']), 'CLASS' => 'AMEX:' . $card_member];
                            $counterAccountsReceivable = ['t' => 'SPL','TRNSTYPE' => 'invoice', 'DATE' => $iifDate, 'ACCNT' => 'MP amex', 'NAME' =>$propertyName, 'AMOUNT' => $totalPerProp * -1,  'DOCNUM' => $transaction_ref, 'MEMO' => trim($detail['description']), 'CLASS' => 'AMEX:' . $card_member];
                            $this->appendFile($accountsReceivable, $file);
                            $this->appendFile($counterAccountsReceivable, $file);
                            $this->endtrns($file);

                            $header = ['transaction_type' => 2,'transaction_ref' => $transaction_ref, 'transaction_date' => $transaction_date, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
                            $this->db->insert('transaction_header', $header);
                            $trans_id = $this->db->insert_id();

                            $this->db->where('id', $ofx_id);
                            $this->db->update('ofx_imports', array('trans_id' => $trans_id));
                            //}

                            //if(!$exporting){
                            //$accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, 'credit' => $apDebit, 'debit' => $apCredit, 'line_number' => ++$i];
                            //$this->db->insert('transactions', $accountsReceivable);
                            //}

                            $accountsPayable = ['account_id' => $ap, 'property_id' => $property, 'unit_id' => '', 'description' => trim($description), 'trans_id' => $trans_id, 'profile_id' => $vp_id,'credit' => $apCredit, 'debit' => $apDebit, 'line_number' => 0];
                            $this->db->insert('transactions', $accountsPayable);

                            foreach($detailsPerProp as &$detail){
                                $i++;
                                if(trim($detail['amount']) == 0){
                                    continue;
                                }
                                //unset($detail["ofx_id"]); 
                                $detail['profile_id'] = $vp_id;
                                $detail['trans_id'] = $trans_id;
                                $detail['line_number'] = $i;
                                $expAmount = $detail['amount'];
                                if($detail['amount'] > 0) {
                                    $detail['debit'] = $detail['amount'];
                                    $detail['credit'] = 0;
                                }elseif($detail['amount'] < 0) { 
                                    $detail['credit'] = $detail['amount'] * -1;
                                    $detail['debit'] = 0;
                                }
                                    unset($detail["amount"]); 
                                    $this->db->insert('transactions', $detail);
                            }
                        }
                    
                }
            }else{
                $i = 0;
                if($transAmount > 0) { $amount = 'debit'; }
            else{ $amount = 'credit';  
                $transAmount = $transAmount * -1;
            }

            if($Ccproperty_id != $property_id){
                $propertyName = 
                $this->db->select('p.customer_profile_id');
                $this->db->from('properties p');
                $this->db->where('p.id', $property_id);
                $q = $this->db->get();
                $cp_id = $q->row()->customer_profile_id;
                //if($exporting){
                //                                                                         should this be MP amex
                //$transaction = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => $transAccntName, 'NAME' =>$transPropName, 'AMOUNT' => $ccExportAmount * -1,  'DOCNUM' => $transaction_ref , 'MEMO' => trim($description), 'CLASS' => 'AMEX:' . $card_member];
                $transaction = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => 'Accounts Receivable', 'NAME' =>$transPropName, 'AMOUNT' => $ccExportAmount * -1,  'DOCNUM' => $transaction_ref , 'MEMO' => trim($description), 'CLASS' => 'AMEX:' . $card_member];
                $this->appendFile($transaction, $file);
                $this->endtrns($file);
                //}
                if($amount == 'debit'){$negAmount = $transAmount;}
                if($amount == 'credit'){$negAmount = $transAmount * -1;}
                //if($exporting){
                $header = ['transaction_type' => 2,'transaction_ref' => $transaction_ref, 'transaction_date' => $transaction_date, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
                $this->db->insert('transaction_header', $header);
                $trans_id = $this->db->insert_id();

                $this->db->where('id', $ofx_id);
                $this->db->update('ofx_imports', array('trans_id' => $trans_id));
                //}

                //if(!$exporting){
                //$accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, $amount => $transAmount, 'line_number' => ++$i];
                //$this->db->insert('transactions', $accountsReceivable);
                //}

                $accountsPayable = ['account_id' => $ap, 'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => trim($description), 'trans_id' => $trans_id, 'profile_id' => $vp_id, (($amount == 'debit') ? 'credit' : 'debit') => $transAmount, 'line_number' => 0];
                $this->db->insert('transactions', $accountsPayable);

                $transaction = ['account_id' => $account_id,  'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => trim($description), 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>  ($cp_id ? $vp_id : ''), 'line_number' => ++$i];
                $this->db->insert('transactions', $transaction);

                }else{
                    //if($exporting){
                    $transaction = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => $transAccntName, 'NAME' =>$transPropName, 'AMOUNT' => $ccExportAmount * -1,  'DOCNUM' => $transaction_ref , 'MEMO' => trim($description), 'CLASS' => ''];
                    $this->appendFile($transaction, $file);
                    $this->endtrns($file);
                    //}

                    //if(!$exporting){
                    //$transaction = ['account_id' => $account_id,  'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => $description, 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>  ($cp_id ? $vp_id : ''), 'line_number' => ++$i];
                    //$this->db->insert('transactions', $transaction);
                    //}
                }

            }

            
            $this->db->trans_complete();
            return true;
       // }
        
        
       
        
}

// public function recordTransactions($ccAccount_id , $Ccproperty_id, $vendor, $vp_id, $transaction, $details)
//     {   
//         $file = $this->createFile();
//         $ar = $this->site->settings->accounts_receivable;
//         $ap =$this->site->settings->accounts_payable;

//         //foreach($transactions as &$transaction){
//             $cp_id = NULL;
//             $ofx_id = $transaction['ofxId'];    
//             $transaction_date = $transaction['transaction_date'];
//             $iifDate = humanDate($transaction_date);
//             $transaction_ref = $transaction['transaction_ref'];
//             $property_id = $transaction['property_id'];
//             $description = $transaction['description'];
//             $unit_id = $transaction['unit_id'];
//             $account_id = $transaction['account_id'];

//             //$total = array_sum(array_column($transaction, 'amount'));
//             $transAmount = $transaction['amount'];
//             // $total > 0 ? $amount = 'credit' : $amount = 'credit'; 
//             // $amount = $total > 0 ?  'credit' :  'debit'; 
//             //if (array_search(1 > 0, array_column($details, 'amount')) !== FALSE)
//             //$error = [];

//             // if(array_key_exists($ofx_id, $details)  && (array_sum(array_column($details[$ofx_id], 'amount')) > 0 || array_sum(array_column($details[$ofx_id], 'amount')) < 0)){
//             //     $total = array_sum(array_column($details[$ofx_id], 'amount'));
//             //     if($transAmount != $total){
//             //         $error[] = $transaction_ref; 
//             //         continue;
//             //     }
//             // }    
//             if($transAmount > 0) { $amount = 'credit'; }
//             else{ $amount = 'debit';  
//                 $transAmount = $transAmount * -1;
//             }
//             $this->db->trans_start();
            
//             $header = ['transaction_type' => 2,'transaction_ref' => $transaction_ref, 'transaction_date' => $transaction_date, 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
//             $this->db->insert('transaction_header', $header);
//             $trans_id = $this->db->insert_id();
//             $this->db->where('id', $ofx_id);
//             $this->db->update('ofx_imports', array('trans_id' => $trans_id));
//             //export maybe need new transaction_header
//             // $ccAcc = ['account_id' => $ccAccount_id,  'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>$vendor];
//             $ccAcc = ['t' => 'TRNS','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => $ccAccount_id, 'NAME' =>'', 'AMOUNT' => $transAmount,  'DOCNUM' => $transaction_ref, 'MEMO' => trim($description), 'CLASS' => $trans_id];
//             //$this->db->insert('transactions', $ccAcc);
//             $this->appendFile($ccAcc, $file);

//             //$transacionDetails = [];
//             $i = 0;
//             if(array_key_exists($ofx_id, $details)  && ((array_sum(array_column($details[$ofx_id], 'amount')) > 0) || (array_sum(array_column($details[$ofx_id], 'amount')) < 0))){

//                 foreach($details[$ofx_id] as &$detail){
                    
//                     $i++;
//                     //unset($detail["ofx_id"]); 
//                     $detail['trans_id'] = $trans_id;
//                     $detail['line_number'] = $i;
//                     if($detail['amount'] > 0) {
//                         $detail['debit'] = $detail['amount'];
//                         $detail['credit'] = 0;
//                         unset($detail["amount"]); 
//                         if($Ccproperty_id != $detail['property_id']){
//                             $this->db->select('p.customer_profile_id');
//                             $this->db->from('properties p');
//                             $this->db->where('p.id', $detail['property_id']);
//                             $q = $this->db->get();
//                             $cp_id = $q->row()->customer_profile_id;
//                             ////export
//                             //$accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, 'credit' => $detail['credit'], 'debit' => $detail['debit']]; //, 'line_number' => ++$i]
//                             $accountsReceivable = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => 'Accounts Receivable', 'NAME' =>'', 'AMOUNT' => $detail['debit'],  'DOCNUM' => $transaction_ref, 'MEMO' => trim($detail['description']), 'CLASS' => ''];
//                             $accountsPayable = ['account_id' => $ap, 'property_id' => $detail['property_id'], 'unit_id' => $detail['unit_id'], 'description' => trim($description), 'trans_id' => $trans_id, 'profile_id' => $vp_id,'credit' => $detail['debit'], 'debit' => $detail['credit'], 'line_number' => ++$i];
//                             $detail['profile_id'] = $vp_id;
//                             //$this->db->insert('transactions', $accountsReceivable);
//                             $this->appendFile($accountsReceivable, $file);
//                             $this->db->insert('transactions', $accountsPayable);
//                             $this->db->insert('transactions', $detail);
//                         }else{
//                             //$this->db->insert('transactions', $detail); export
//                             $det = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => 'Account', 'NAME' =>'', 'AMOUNT' => $detail['debit'],  'DOCNUM' => $transaction_ref , 'MEMO' => trim($detail['description']), 'CLASS' => ''];
//                             $this->appendFile($det, $file);
//                         }

//                      }
//                     elseif($detail['amount'] < 0) { 
//                         $negAmount = $detail['amount'];
//                         $detail['credit'] = $detail['amount'] * -1;
//                         $detail['debit'] = 0;
//                         unset($detail["amount"]); 
//                         if($Ccproperty_id != $detail['property_id']){
//                             $this->db->select('p.customer_profile_id');
//                             $this->db->from('properties p');
//                             $this->db->where('p.id', $detail['property_id']);
//                             $q = $this->db->get();
//                             $cp_id = $q->row()->customer_profile_id;
//                             //export
//                             //$accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, 'credit' => $detail['credit'], 'debit' => $detail['debit']];//, 'line_number' => ++$i
//                             $accountsReceivable = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => 'Accounts Receivable', 'NAME' =>'', 'AMOUNT' => $negAmount,  'DOCNUM' => $transaction_ref, 'MEMO' => $detail['description'], 'CLASS' => ''];
//                             $accountsPayable = ['account_id' => $ap, 'property_id' => $detail['property_id'], 'unit_id' => $detail['unit_id'], 'description' => trim($detail['description']), 'trans_id' => $trans_id, 'profile_id' => $vp_id,'credit' => $detail['debit'], 'debit' => $detail['credit'], 'line_number' => ++$i];
//                             $detail['profile_id'] = $vp_id;
//                             //$this->db->insert('transactions', $accountsReceivable);
//                             $this->appendFile($accountsReceivable, $file);
//                             $this->db->insert('transactions', $accountsPayable);
//                             $this->db->insert('transactions', $detail);
//                         }else{
//                             //$this->db->insert('transactions', $detail);export
//                             $det = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => 'Account', 'NAME' =>'', 'AMOUNT' => $negAmount,  'DOCNUM' => $transaction_ref , 'MEMO' => trim($detail['description']), 'CLASS' => ''];
//                             $this->appendFile($det, $file);
//                         }
                        
//                     }
//                 }
//             }else{
//                 $i = 0;
//                 if($transAmount > 0) { $amount = 'debit'; }
//             else{ $amount = 'credit';  
//                 $transAmount = $transAmount * -1;
//             }

//             if($Ccproperty_id != $property_id){
//                 $this->db->select('p.customer_profile_id');
//                 $this->db->from('properties p');
//                 $this->db->where('p.id', $property_id);
//                 $q = $this->db->get();
//                 $cp_id = $q->row()->customer_profile_id;
//                 //export
//                 if($amount == 'debit'){$negAmount = $transAmount;}
//                 if($amount == 'credit'){$negAmount = $transAmount * -1;}
//                 //$accountsReceivable = ['account_id' => $ar, 'property_id' => $Ccproperty_id, 'description' => $description, 'trans_id' => $trans_id, 'profile_id' => $cp_id, $amount => $transAmount];// , 'line_number' => ++$i
//                 $accountsReceivable = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => 'Accounts Receivable', 'NAME' =>'', 'AMOUNT' => $negAmount,  'DOCNUM' => $transaction_ref, 'MEMO' => trim($description), 'CLASS' => ''];
//                 $accountsPayable = ['account_id' => $ap, 'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => trim($description), 'trans_id' => $trans_id, 'profile_id' => $vp_id, (($amount == 'debit') ? 'credit' : 'debit') => $transAmount, 'line_number' => ++$i];
//                 //$this->db->insert('transactions', $accountsReceivable);
//                 $this->appendFile($accountsReceivable, $file);
//                 $this->db->insert('transactions', $accountsPayable);
//                 $transaction = ['account_id' => $account_id,  'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => trim($description), 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>  ($cp_id ? $vp_id : ''), 'line_number' => ++$i];
//                 $this->db->insert('transactions', $transaction);
//                 }else{
//                     //export
//                     //$transaction = ['account_id' => $account_id,  'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => $description, 'trans_id' => $trans_id, $amount => $transAmount, 'profile_id' =>  ($cp_id ? $vp_id : ''), 'line_number' => ++$i];
//                     $transaction = ['t' => 'SPL','TRNSTYPE' => 'CREDIT CARD', 'DATE' => $iifDate, 'ACCNT' => 'Account', 'NAME' =>'', 'AMOUNT' => $negAmount,  'DOCNUM' => $transaction_ref , 'MEMO' => trim($description), 'CLASS' => ''];
//                     $this->appendFile($transaction, $file);
//                     //$this->db->insert('transactions', $transaction);//export
//                 }

//             }
//             $endTransaction = "ENDTRNS\r\n\r\n";
//             file_put_contents($file, $endTransaction ,FILE_APPEND);
//             //$this->appendFile($endTransaction, $file);
//             $this->db->trans_complete();
//             return true;
//        // }
        
        
       
        
// }

public function getHeaderEdit($th_id)
{
   
    $this->db->select('t.id AS ccTransaction_id, th.id AS th_id, CONCAT(DATE_FORMAT(th.last_mod_date, "%m/%d/%Y")," ",TIME_FORMAT(th.last_mod_date, "%r")) AS modified, th.last_mod_by, CONCAT_WS(" ",p.first_name,p.last_name) AS user, t.account_id, t.property_id, t.profile_id, th.transaction_ref, th.transaction_date AS date, th.memo,  (t.credit - t.debit)  AS amount, t.class_id');
    $this->db->from('transactions t'); 
    $this->db->join('transaction_header th', 't.trans_id = th.id AND th.id =' . $th_id);
    $this->db->join('users u', 'th.last_mod_by = u.id','left'); 
    $this->db->join('profiles p', 'u.profile_id = p.id','left'); 
    $this->db->order_by('t.id', 'ASC');
    $this->db->limit(1); 
    
    $q = $this->db->get();   
    if ($q->num_rows() > 0) {
        return $q->row();
    }
    return false;
}

public function getTransactions($th_id)
{
        $this->db->select('id, account_id, property_id, description, debit AS debit, credit as credit, profile_id, class_id');
        $this->db->from('transactions');
        $this->db->where('trans_id', $th_id);
        $this->db->order_by('line_number ASC', 'id ASC');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            $data = array_slice($data, 1);
            return $data;
        }
        return null;
}
public function AddTransaction($type, $header, $creditCard,  $transactions)
{   
    $this->db->trans_start();

    $trans_id = $this->addHeader($header, 9);
    if($type === "credit"){ 
        $creditCard['debit'] = $creditCard['credit']; 
        $creditCard['credit'] = 0;
    }
    $creditCard['trans_id'] = $trans_id; 
    $this->db->insert('transactions', $creditCard);
    $filled = $this->removeEmpty($transactions, $trans_id);
    $this->addDetailsPerType($filled, $type);
    
    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
    {
        return false;
    }
    return true;
     
}

public function EditTransaction($type, $header, $creditCard, $transactions, $deletes)
{   
    $this->db->trans_start();

    $this->load->model('transaction_snapshot_model');
    $this->transaction_snapshot_model->transaction_snapshot($header['id']);
    $this->updateHeader($header, $header['id']);
    $trans_id = $header['id'];
    if($type === "credit"){ 
        $creditCard['debit'] = $creditCard['credit']; 
        $creditCard['credit'] = 0;
    }

    if($type === "normal"){ 
        $creditCard['debit'] = 0;
    }
    $this->db->update('transactions', $creditCard, 'id =' . $creditCard['id']);
    
    $filled = $this->removeEmptyEdit($transactions, $header['id']);
    $this->editDetailsPerType($filled, $type);
    $this->deleteLines($deletes);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE)
    {
        return false;
    }

    return true;
}


public function getAccountTypes($addtype = false)
{
    $q = $this->db->get('account_types');
    if ($q->num_rows() > 0) {
        foreach (($q->result()) as &$row) {
            if ($addtype == true) $row->type = 'account_type';
            $data[] = $row;
        }
        return $data;
    }
    return null;
}


function getAccounts($pid)

{   // #1 SubQueries no.1 -------------------------------------------
    $this->db->select('id, name, accno, all_props');
    $this->db->from('accounts');
    $this->db->where(array('all_props'=> 1, 'active' => 1));
    //$query = $this->db->get();
    $all_props1 = $this->db->get_compiled_select();
    //echo $all_props1. 'UNION';
   
    $this->db->reset_query();
    
    // #2 SubQueries no.2 -------------------------------------------
    
    $this->db->select('a.id, a.name, a.accno, all_props');
    $this->db->from('accounts a');
    $this->db->join('property_accounts pa', 'a.id = pa.account_id AND pa.property_id ='. $pid);
    $this->db->where('a.active', 1);
    //$query = $this->db->get();
    $all_props0 = $this->db->get_compiled_select();
    //echo $all_props0;
    $this->db->reset_query();
    
    // #3 Union with Simple Manual Queries --------------------------
    
    $q = $this->db->query("$all_props1 UNION $all_props0");
    
    
    
    if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
            $data[] = $row;
        }
        print_r($data);
    }
    return array();
}

public function getClasses($addtype = false)
{
    $q = $this->db->get('classes');
    if ($q->num_rows() > 0) {
        foreach (($q->result()) as &$row) {
            if ($addtype == true) $row->type = 'account_type';
            $data[] = $row;
        }
        return $data;
    }
    return null;
}

public function getProperties2($id, $all_props)
{   if($all_props==1)
    {
    $this->db->select('id, name, active');
    $this->db->from('properties');
    //$this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id,'left');
    $this->db->where('active', 1);
    $q = $this->db->get();
    if ($q->num_rows() > 0) {
        foreach (($q->result()) as &$row) {
            $data[] = $row;
        }
        return $data;
    }
    return null;
}else
{
    $this->db->select('p.id, p.name, p.active');
    $this->db->from('properties p');
    $this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id);
    $this->db->where('active', 1);
    $q = $this->db->get();
    if ($q->num_rows() > 0) {
        foreach (($q->result()) as &$row) {
            $data[] = $row;
        }
        return $data;
    }
    return null; 
}
}

public function getProperties3($id, $all_props)
{   
    $this->db->select('p.id, p.name, p.active');
    $this->db->from('properties p');
    $this->db->join('property_accounts pa', 'p.id = pa.property_id AND pa.account_id ='. $id);
    $this->db->where('active', 1);
    $q = $this->db->get();
    if ($q->num_rows() > 0) {
        foreach (($q->result()) as &$row) {
            $data[] = $row;
        }
        return $data;
    }
    return null; 
}


public function getUnits($addtype = false)
{   
    $this->db->select('id, name, parent_id, property_id');
    $this->db->from('units');
    $q = $this->db->get();
    if ($q->num_rows() > 0) {
        foreach (($q->result()) as &$row) {
            if ($addtype == true) $row->type = 'account_type';
            $data[] = $row;
        }
        return $data;
    }
    return null;
}

//filtering profiles by vendor
function getVendors()
{
    $this->db->select('id, LTRIM(CONCAT_WS(" ",first_name, last_name)) AS vendor');
    $this->db->from('profiles');
    $this->db->where('profile_type_id',1);
    $this->db->ORDER_BY('vendor');
    $q = $this->db->get();
    if ($q->num_rows() > 0) {
        foreach (($q->result()) as &$row) {
            $data[] = $row;
        }
        return $data;
    }
    return array();
}

// function getProfiles($aid = 0)
// {
//     $this->db->select('id, CONCAT_WS(" ",first_name, last_name) AS tenant');
//     $this->db->from('profiles');
//     if($aid = 451){$this->db->where('profile_type_id',3);}//receivable, tenants
//     if($aid = 454){$this->db->where('profile_type_id',1);}//payable, vendors
//     $q = $this->db->get();
//     if ($q->num_rows() > 0) {
//         foreach (($q->result()) as &$row) {
//             $data[] = $row;
//         }
//         return $data;
//     }
//     return array();
// }

function getProfiles()
{
    $this->db->select('id, CONCAT_WS(" ",first_name, last_name) AS name, LTRIM(CONCAT_WS(" ",first_name, last_name)) AS vendor, profile_type_id');
    $this->db->from('profiles');
    $this->db->ORDER_BY('vendor');
    $q = $this->db->get();
    if ($q->num_rows() > 0) {
        foreach (($q->result()) as &$row) {
            $data[] = $row;
        }
        return $data;
    }
    return array();
}

function getAccountName($id)
{
    $this->db->select('id, name');
    $this->db->from('accounts');
    $this->db->where('id', $id);
    $q = $this->db->get();
    if ($q->num_rows() > 0) {
       return $q->row();
    }
    return false;
}

    function getPropertyAccounts2()
    {
        $this->db->select('p.*');
        $this->db->from('property_accounts p');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

        
    }
    
    

