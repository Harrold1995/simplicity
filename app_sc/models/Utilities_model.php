<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Utilities_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDefaultBank($property_id)
    {   
        $this->db->select('default_bank');
        $this->db->from('properties'); 
        $this->db->where('id', $property_id);
       
        $q = $this->db->get(); 
        if ($q->num_rows() > 0) {
            return $q->row()->default_bank;
        }
       
    }

    //accounts payable or if check default account
    //expense account

    // public function recordUtilityBillOld($rows)
    // {   
    //     $insert = 0;
    //     $accountsPayable = $this->site->settings->accounts_payable;
    //     foreach($rows as $key => &$row){
    //         $utility_id = $key;
    //         $utilities = $row['utilities'];
    //         $transactions = $row['transactions'];
    //         $transaction_date = $utilities['last_paid_date'];
           
    //         $transaction_type =$row['utilities']['direct_payment'] == 1 ? 4 : 2;

    //         $transAmount = array_sum(array_column($row['transactions'], 'amount'));
    //         $account_id = $transactions[key($transactions)]['account_id'];
    //         $property_id = $transactions[key($transactions)]['property_id'];
    //         $profile_id = $transactions[key($transactions)]['profile_id'];
    //         $unit_id = $transactions[key($transactions)]['unit_id'];
    //         $account = $transactions[key($transactions)]['account'];
    //         $billable = $transactions[key($transactions)]['billable'];
            
    //         $object = $this->getDefaultBank($property_id);
    //         $default_bank = $object->default_bank;
    //         if(empty($default_bank)){
    //             $noBank[] =  $object->name;  
    //             //continue;
    //         }
    //         if($transaction_type == 4 && $transAmount <= 0){
    //             $negative[] =  $account;  
    //             //continue;
    //         }

    //         if(empty($default_bank) || ($transaction_type == 4 && $transAmount <= 0)){
    //             continue;
    //         }
            

    //         $this->db->trans_start();
            
    //         $header = ['type_id' => 9, 'type_item_id' =>  $utility_id,'transaction_type' => $transaction_type , 'transaction_date' => $transaction_date, /*'transaction_ref' => $transaction_ref,*/ 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
    //         $this->db->insert('transaction_header', $header);    
    //         $trans_id = $this->db->insert_id();
    //         // do you need to check for negative amount
    //         $payableOrchecking = ['type_id' => 9, 'type_item_id' =>  $utility_id,'account_id' => $transaction_type == 4 ? $default_bank : $accountsPayable,  'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => $ $account, 'trans_id' => $trans_id, 'credit' => $transAmount];
    //         $this->db->insert('transactions', $payableOrchecking);
    //         $i = 0;   
    //         foreach($transactions as &$transaction){
    //         $i++;
    //         if(!empty(trim($transaction['amount']))){
    //             $utility_trans = $transaction['utility_trans'];
                
    //             if($transaction['amount'] > 0) { $amount = 'debit'; }
    //             else{ $amount = 'credit';  
    //                 $transaction['amount'] = $transaction['amount'] * -1;
    //         }
           
    //             $transactionDetail = ['type_id' => 9, 'type_item_id' =>  $utility_id,  'account_id' => $transaction['account_id'],  'property_id' => $transaction['property_id'], 'unit_id' => $transaction['unit_id'], 'description' => $account, 'trans_id' => $trans_id, $amount => $transaction['amount'], 'billable' => $transaction['billable'], 'line_number' => $i];
    //             //$this->db->insert('transactions', $transactionDetail);
    //             $transaction_id = $this->db->insert_id();
    //             $utiity_trans['transaction_id'] = $transaction_id;
    //             $this->db->insert('utility_trans', $utility_trans);
    //         }
                
    //         }

    //         $this->db->update('utilities', $utilities, 'id =' . $utility_id);

    //         $this->db->trans_complete();
    //         $insert++;
    //     }
        
    //     if(!empty($noBank)){
    //         $noBank = array_unique($noBank);
    //         $bankMessage = 'The following properties don\'t have default bank accounts: ' . implode(", ", $noBank); 
    //     }

    //     if(!empty($negative)){
    //         $negative = array_unique($negative);
    //         $negMessage = 'The following utility accounts have a zero or negative total: ' . implode(", ", $negative); 
    //     }
    //         $warning = new stdClass();
    //         $warning->noBank = $bankMessage;
    //         $warning->negative = $negMessage;
    //         $warning->statusMessage = $insert . ' transactions inserted';
    //         $warning->status = $insert;  
    //         return $warning;
            
    //         // if($insert > 0) return true;
    // }
       
    public function recordUtilityBill($row, $utility_id)
    {   
     
        $accountsPayable = $this->site->settings->accounts_payable;
       
            //$utility_id = $key;
            $utilities = $row['utilities'];
            $transactions = $row['transactions'];
            $transaction_date = $utilities['last_paid_date'];
           
            $transaction_type =$row['utilities']['direct_payment'] == 1 ? 4 : 2;

            $transAmount = array_sum(array_column($row['transactions'], 'amount'));
            $account_id = $transactions[key($transactions)]['account_id'];
            $property_id = $transactions[key($transactions)]['property_id'];
            $profile_id = $transactions[key($transactions)]['profile_id'];
            $unit_id = $transactions[key($transactions)]['unit_id'];
            $account = $transactions[key($transactions)]['account'];
            $billable = $transactions[key($transactions)]['billable'];
            
            $default_bank = $this->getDefaultBank($property_id);
           

            $this->db->trans_start();
            
            $header = ['type_id' => 9, 'type_item_id' =>  $utility_id,'transaction_type' => $transaction_type , 'transaction_date' => $transaction_date, 'memo' => 'Account- ' . $account, 'transaction_ref' => (($transaction_type == 4) ? 'ACH' : $account), 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
            $this->db->insert('transaction_header', $header);    
            $trans_id = $this->db->insert_id();
            // do you need to check for negative amount
            $payableOrchecking = ['profile_id' => $profile_id, 'type_id' => 9, 'type_item_id' =>  $utility_id,'account_id' => (($transaction_type == 4) ? $default_bank : $accountsPayable),  'property_id' => $property_id, 'unit_id' => $unit_id, 'description' => 'Account- ' .  $account, 'trans_id' => $trans_id, 'credit' => $transAmount];
            $this->db->insert('transactions', $payableOrchecking);
            $i = 0;   
            foreach($transactions as &$transaction){
            $i++;
            if(!empty(trim($transaction['amount']))){
                $utility_trans = $transaction['utility_trans'];
                
                if($transaction['amount'] > 0) { $amount = 'debit'; }
                else{ $amount = 'credit';  
                    $transaction['amount'] = $transaction['amount'] * -1;
            }
           
                $transactionDetail = ['profile_id' => $transaction['profile_id'],'type_id' => 9, 'type_item_id' =>  $utility_id,  'account_id' => $transaction['account_id'],  'property_id' => $transaction['property_id'], 'unit_id' => $transaction['unit_id'], 'description' => 'Account- ' .  $account . ' ' . 'Usage- ' . $utility_trans['util_usage'], 'trans_id' => $trans_id, $amount => $transaction['amount'], 'billable' => $transaction['billable'], 'line_number' => $i];
                $this->db->insert('transactions', $transactionDetail);
                $transaction_id = $this->db->insert_id();
                $utiity_trans['transaction_id'] = $transaction_id;
                $this->db->insert('utility_trans', $utility_trans);
            }
                
            }

            $this->db->update('utilities', $utilities, 'id =' . $utility_id);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {
               return false;
            }
           
            return true;
            
            
    }

    //accounts payable or if check default account
    //expense account

//     public function recordUtilityBill2($property_id, $transactions, $details)
//     {   
//         $this->db->trans_start();
//         $accountsPayable = $this->site->settings->accounts_payable;
//         $default_bank = $this->getDefaultBank($property_id);
//         if(empty($default_bank)){return 'No default bank account for this property';}
//         foreach($transactions as &$transaction){
//             $transaction_date = $transaction['transacion_date'];
//             //$transaction_ref = $transaction['transaction_ref'];
//             //$property_id = $transaction['property_id'];
//             $property_id = $property_id;
//             //$description = $transaction['description'];
//             $unit_id = $transaction['unit_id'];
//             $expenseAcc = $transaction['default_expense_acct'];//default expense
//             $utility_id = $transaction['id'];
//             $usage = $transaction['usage'];
//             $transaction_type =$transaction['direct_payment'] == 1 ? 4 : 2;
//             //$total = array_sum(array_column($transaction, 'amount'));
//             $transAmount = $transaction['amount'];
//             // $total > 0 ? $amount = 'credit' : $amount = 'credit'; 
//             // $amount = $total > 0 ?  'credit' :  'debit'; 

//             if($transAmount > 0) { $amount = 'credit'; }
//             else{ $amount = 'debit';  
//                 $transAmount = $transAmount * -1;
//             }
            
//             $header = ['transaction_type' => $transaction_type , 'transaction_date' => $transaction_date, /*'transaction_ref' => $transaction_ref,*/ 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
//             $this->db->insert('transaction_header', $header);
//             $trans_id = $this->db->insert_id();
//             $payableOrchecking = ['account_id' => $transaction_type == 4 ? $default_bank : $accountsPayable,  'property_id' => $property_id, 'unit_id' => $unit_id, /*'description' => $description,*/ 'trans_id' => $trans_id, $amount => $transAmount];
//             $this->db->insert('transactions', $payableOrchecking);
//             $transacionDetails = [];
//             if(array_key_exists($utility_id, $details)){
//                 foreach($details[$utility_id] as &$detail){
//                     //unset($detail["ofx_id"]); 
//                     $detail['trans_id'] = $trans_id;
//                     if($detail['amount'] > 0) {
//                         $detail['debit'] = $detail['amount'];
//                         $detail['credit'] = 0;
//                         unset($detail["amount"]); 
//                      }
//                     elseif($detail['amount'] < 0) { 
//                         $detail['credit'] = $detail['amount'] * -1;
//                         $detail['debit'] = 0;
//                         unset($detail["amount"]); 
                      
//                     }
//                         $this->db->insert('transactions', $detail);
//                         $transaction_id = $this->db->insert_id();
//                         $utiity_trans['util_usage'] = $usage;
//                         $utiity_trans['utility_id'] = $utility_id;
//                         $utiity_trans['transaction_id'] = $transaction_id;
//                         $this->db->insert('utility_trans', $utility_trans);
//                 }
        
//             }else{
//                 if($transAmount > 0) { $amount = 'debit'; }
//             else{ $amount = 'credit';  
//                 $transAmount = $transAmount * -1;
//             }
//             $transaction = ['account_id' => $expenseAcc,  'property_id' => $property_id, 'unit_id' => $unit_id, /*'description' => $description,*/ 'trans_id' => $trans_id, $amount => $transAmount];
//             $this->db->insert('transactions', $transaction);
//             $transaction_id = $this->db->insert_id();
//             $utiity_trans['util_usage'] = $usage;
//             $utiity_trans['utility_id'] = $utility_id;
//             $utiity_trans['transaction_id'] = $transaction_id;
//             $this->db->insert('utility_trans', $utility_trans);
//             }

//             $utilities['last_paid_date'] = $transaction_date;
//             $this->db->update('utilities', $utilities, 'id =' . $utility_id);

//             $this->db->trans_complete();
//         }

       
//         return true;
// }

//     public function recordUtilityBill1($property_id, $transactions, $details)
//     {   
//         $this->db->trans_start();
//         $accountsPayable = $this->site->settings->accounts_payable;
//         $default_bank = $this->getDefaultBank($property_id);
//         if(empty($default_bank)){return 'No default bank account for this property';}
//         foreach($transactions as &$transaction){
//             $transaction_date = $transaction['transacion_date'];
//             //$transaction_ref = $transaction['transaction_ref'];
//             //$property_id = $transaction['property_id'];
//             $property_id = $property_id;
//             //$description = $transaction['description'];
//             $unit_id = $transaction['unit_id'];
//             $expenseAcc = $transaction['default_expense_acct'];//default expense
//             $utility_id = $transaction['id'];
//             $usage = $transaction['usage'];
//             $transaction_type =$transaction['direct_payment'] == 1 ? 4 : 2;
//             //$total = array_sum(array_column($transaction, 'amount'));
//             $transAmount = $transaction['amount'];
//             // $total > 0 ? $amount = 'credit' : $amount = 'credit'; 
//             // $amount = $total > 0 ?  'credit' :  'debit'; 

//             if($transAmount > 0) { $amount = 'credit'; }
//             else{ $amount = 'debit';  
//                 $transAmount = $transAmount * -1;
//             }
            
//             $header = ['transaction_type' => $transaction_type , 'transaction_date' => $transaction_date, /*'transaction_ref' => $transaction_ref,*/ 'last_mod_date' => date("Y-m-d H:i:s"),'last_mod_by' => $this->ion_auth->get_user_id()];
//             $this->db->insert('transaction_header', $header);
//             $trans_id = $this->db->insert_id();
//             $payableOrchecking = ['account_id' => $transaction_type == 4 ? $default_bank : $accountsPayable,  'property_id' => $property_id, 'unit_id' => $unit_id, /*'description' => $description,*/ 'trans_id' => $trans_id, $amount => $transAmount];
//             $this->db->insert('transactions', $payableOrchecking);
//             $transacionDetails = [];
//             if(array_key_exists($utility_id, $details)){
//                 foreach($details[$utility_id] as &$detail){
//                     //unset($detail["ofx_id"]); 
//                     $detail['trans_id'] = $trans_id;
//                     if($detail['amount'] > 0) {
//                         $detail['debit'] = $detail['amount'];
//                         $detail['credit'] = 0;
//                         unset($detail["amount"]); 
//                         $transacionDetails[] = $detail;
//                      }
//                     elseif($detail['amount'] < 0) { 
//                         $detail['credit'] = $detail['amount'] * -1;
//                         $detail['debit'] = 0;
//                         unset($detail["amount"]); 
//                         $transacionDetails[] = $detail;
//                     }
                    
//                 }
        
//                 if(!empty($transacionDetails)){
//                     $this->db->insert_batch('transactions', $transacionDetails);
//                 }
        
//             }else{
//                 if($transAmount > 0) { $amount = 'debit'; }
//             else{ $amount = 'credit';  
//                 $transAmount = $transAmount * -1;
//             }
//             $transaction = ['account_id' => $expenseAcc,  'property_id' => $property_id, 'unit_id' => $unit_id, /*'description' => $description,*/ 'trans_id' => $trans_id, $amount => $transAmount];
//             $this->db->insert('transactions', $transaction);
//             }
//             $utiity_trans['util_usage'] = $usage;
//             $utiity_trans['utility_id'] = $utility_id;
//             $utiity_trans['transaction_id'] = $transaction_id;
//             $this->db->insert('utility_trans', $utility_trans);
        
//         }

//         $this->db->trans_complete();
//         return true;
    
//     }

    function deleteUtilities($utilities, $delete){
        if($utilities){
            $response = $this->checkItems($utilities, $delete);
               if($delete == NULL) {
                   return $response;
                }else{
                    return true;
                }
           }
    }

}
