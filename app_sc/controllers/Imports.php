<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Imports extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->icons = Array("property" => "fas fa-building", "unit" => "far fa-building");
        $this->load->model('validate_model');
        $this->load->library('encryption');
        $this->load->model('accounts_model');
        $this->load->model('MY_model');
        $this->ap = $this->site->settings->accounts_payable;
                                  
        $this->buildium_cookie = '_ga=GA1.2.1118987630.1660853443; _evga_c878=0c3f72988d0b8a92.0Lj; _gid=GA1.2.1684552742.1662487482; properties_1123677=All; _gat=1; _hp2_id.1205160675=%7B%22userId%22%3A%224754099293938360%22%2C%22pageviewId%22%3A%227125671423874647%22%2C%22sessionId%22%3A%22235445134007509%22%2C%22identity%22%3A%228e3eb306-effc-4652-a66d-8989eba26f34%22%2C%22trackerVersion%22%3A%224.0%22%2C%22identityField%22%3Anull%2C%22isIdentified%22%3A1%7D; _hp2_ses_props.1205160675=%7B%22r%22%3A%22https%3A%2F%2Frstarmanagement.managebuilding.com%2FManager%2Fapp%2Frentalowners%2F1049227%2Fproperties%22%2C%22ts%22%3A1662652884583%2C%22d%22%3A%22rstarmanagement.managebuilding.com%22%2C%22h%22%3A%22%2FManager%2Fpublic%2Fauthentication%2Flogin%22%2C%22q%22%3A%22%3FReturnUrl%3D%252FManager%252Fapp%252Fproperties%252Fproperty%252F89733%252Fsummary%22%7D; AKA_A2=A; XSRF-TOKEN=d5b99edbfab8a72c54d72f39f79ad90d; Manager.AspNet.ApplicationCookie=2HcOnCCIkZ1SSPUXmUmIrTwtPJoXREA9ri9vo2HqH-YvFH25YBF2vcyHIJbOs_LZOuwwfbapKz7VatlJ_3mwn-Gd0vSnRFD1XEv2bXo0_KrDoa0CtWVVJtef4-cOWCrrLPDYVkZtKDUPrvcLVgIEus57u8Ck9erHa39gFfGDE1GkE6XtFZU_9dlVb4sJGs-XKsr1y35E4J6AYKApN7Lb9K0jZvV1VWL7LG3H0V-2gLZFQ179S7idzMs1erMTBKfJduE_MFpWW6hJzetZc-cwqKJsXxmhPix5zxsehpoJoWA10Iz7n2mVHHmDczoVTP7ZIhmgeVc_6A3L2L2soEwbvXd6ZmPz_CnV0bMjZ8vx2iXOCcIEI65PqduPw4AypRvIGgjJ75R3zKzEFtopxJH3RUDD7QHWgrH7WmVJ-CP8jaPszQDDMLcor7f02DPjxeBmtLX1jMDfOq9yv1QHjDbXtIvz8i8_b1XP07Xi5aEtDL4U_wQtPe6oIm6WhwJtZWMPafLqkGTN0GvZ9C7RViaJqW8I_-Ph6RCXNrPi7w1l4AQ_sXb3VruN_0m4AA0nMfMgidyYZiXseo5Xe98waj-IcnDf2ILBS5lye5HcjfnZkydrRw94SVo--kXd8E_9qc49w_HmsvZFm667NGlKbRHo9nZi3h39pICRfyzP6iMdYPyqYqvU8jPMCwL3NcajsRTdyGw1PDl2XJyg6zM9adwUAN27pGE';
    }

    function index()
    {

      
        $this->meta['title'] = "Accounts";
        $this->meta['h2'] = "Accounts";
        $this->page_construct('accounts/index', null, $this->meta);
        


    }

     function importBuildium(){
      $this->iterate_banks();
      $this->iterate_payment();
      $this->iterate_deposits();
      $this->iterate_leases();
      $this->iterate_properties();
      $this->bills_toPmts();
      //$this->get_all_buildium_tenants();
    } 
       //auto apply old balances(not just for buildium)
       function iterate_payment(){
        $this->load->model('receivePayments_model');


            //get all open customer payments asc
            $this->db->select('t.id, t.profile_id, t.lease_id, t.property_id, t.description, th.id as th_id, th.transaction_type, th.transaction_date AS date, (t.debit - t.credit) AS amount, ((t.credit - t.debit) - IFNULL(ap.amount,0)) AS open_balance, IFNULL(ap.amount,0) AS received_payment');
            $this->db->from('transactions t'); 
            $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->site->settings->accounts_receivable); 
            $this->db->join('(select sum(amount) as amount, transaction_id_a from (
                  select transaction_id_a, transaction_id_b, amount from `applied_payments` 
            union select transaction_id_b ,transaction_id_a, amount  from `applied_payments`) ap1  group by transaction_id_a) ap','t.id = ap.transaction_id_a ',  ' left');
            $this->db->where('th.transaction_type',  5);
            $this->db->where('(t.credit - t.debit) - IFNULL(ap.amount,0) >',  0);
            //$this->db->where('t.lease_id',  335786);
            
            
            $this->db->ORDER_BY('th.transaction_date ASC');
            $q = $this->db->get();
            $num = 0;
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    echo '<br>'.$num.'out of'.$q->num_rows();
                    echo '<br>tid: '.$row->id.' thid: '.$row->th_id.' date: '.$row->date.' amount: '.$row->amount.' open_balance' .$row->open_balance;
                    $num = $num + 1;
                  $trans_id_a = $row->id;
                  $trans_amount = $row->open_balance;
                  //pull all open charges asc by date
                  $transactions = $this->receivePayments_model->getTransactionsEditNew($row->th_id, $row->lease_id, $row->profile_id);
                  $applied_payments=array();
                  
                  foreach ($transactions as $trans){
                      if ($trans_amount>0){
                        //echo '<br>Description: '.$trans->description.' id: '.$trans->id.' amount: '.$trans->amount.' open: '.$trans->open_balance.' recieved payments: '.$trans->received_payment.' thid: '.$trans->th_id.' applied: '.$apply_amount;
                        if($trans->open_balance > 0 ){
                            $apply_amount = $trans->open_balance > $trans_amount ? $trans_amount:$trans->open_balance; 
                            $trans_amount -= $apply_amount;
                            echo '<br>Description: '.$trans->description.' id: '.$trans->id.' amount: '.$trans->amount.' open: '.$trans->open_balance.' recieved payments: '.$trans->received_payment.' thid: '.$trans->th_id.' applied: '.$apply_amount;
                            $applied_payments[$trans->id]=array("transaction_id_b"=>$trans->id, "amount"=>$apply_amount);
                        }
                      }else{

                      break;
                      }
                    }
                    if(count($applied_payments)>0){
                        $this->MY_model->applyPaymentsAdd($applied_payments, $row->id);
                    }

                }
            }

       }
 //buildium deposits-payments relationship
       function iterate_deposits(){
          //select deposits
          $this->db->select('transactions.id, transactions.trans_id');
            $this->db->from('transactions');
            $this->db->join('transaction_header', 'transaction_header.id = transactions.trans_id');
            $this->db->where('transaction_type', 8);
            $this->db->where('account_id', $this->site->settings->undeposited_funds);
            //$this->db->where('line_number', null);
            $query = $this->db->get();
          //$query = $this->db->get_where('transaction_header', array('transaction_type' => 8));
          $TotalRows = $query->num_rows();
          $num = 1;

          

            foreach ($query->result() as $row)
            {
                    echo '<br>'.$num.' of '.$TotalRows.' rows ';
                    $num ++;
                    echo '<br>';
                    echo $row->id;
                    $this->get_buildium_deposit_payments($row->trans_id, $row->id);
            }

            //$sql1 = "UPDATE `transactions` AS t1 JOIN transactions t2  ON t1.deposit_id = t2.trans_id SET t1.deposit_id = t2.id WHERE t2.account_id = ".$this->site->settings->undeposited_funds;
            //$this->db->query($sql1);
       }



       function get_buildium_deposit_payments($deposit, $trans_id){
          $url="https://rstarmanagement.managebuilding.com/Manager/api/deposits/".$deposit;

        $newjson = $this->get_curl_buildium($url);
        echo '<br><br>';

        foreach ($newjson->DepositedPaymentIds as $payment) {
            $q1 = $this->db->update('transactions', array('deposit_id' => $trans_id), array('trans_id' => $payment));
            //echo $payment->BuildingName.'<br><br>'.$payment->Id.'<br><br>'.$payment->TotalAmount;
            echo '<br> <b>payment id: </b>'.$payment.'<br>';
          }

       }


       // buildium bills/payments relationship
       function bills_toPmts(){
         $query = $this->db->get_where('transaction_header', array('transaction_type' => 2));
         foreach ($query->result() as $row){
           $this->get_buildium_billPayments($row->id);
         }

       }
       function get_buildium_billPayments($billid){
            $url="https://rstarmanagement.managebuilding.com/Manager/api/bills/".$billid;
            $newjson = $this->get_curl_buildium($url);
            $properties = array();
            
            
            // get amount for each property
            // loop thru postings
            foreach ($newjson->PaymentChecks as $PaymentCheck) {
              $paymentMainId = $PaymentCheck->Id;
              
              
              // get all ap trans for this check
              $this->db->select('*');
              $this->db->from('transactions');
              $this->db->where('account_id', $this->ap);
              $this->db->where('trans_id', $paymentMainId);
              $query = $this->db->get();

              
              foreach ($query->result() as $row){
                $applied_amount = 0;
                $transaction_a_id = $row->id;
                // get all ap trans for this bill with this property id
                  $this->db->select('*');
                  $this->db->from('transactions');
                  $this->db->where('account_id', $this->ap);
                  $this->db->where('property_id', $row->property_id);
                  $this->db->where('trans_id', $billid);
                  $billquery = $this->db->get();
                  foreach ($billquery->result() as $billrow){
                    $applied=0;
                    $transaction_b_id = $billrow->id;
                    if($billrow->credit >= ($row->debit - $applied_amount)){
                       $applied = $row->debit - $applied_amount;
                       
                    } else {
                      $applied = $billrow->credit;
                    }
                    $applied_amount = $applied_amount + $applied;
                    $appliedPmt = array('transaction_id_a'=>$transaction_a_id, 'transaction_id_b'=>$transaction_b_id, 'amount'=>$applied);
                    $this->db->insert('applied_payments', $appliedPmt); 
                  }

              }



            }
           /*  foreach ($newjson->Postings as $Posting) {
              $properties[$Posting->BuildingId]['amount'] += $Posting->Amount;
            }
           

            foreach ($newjson->BillOrVendorCreditJournalIds as $bill) {

              $this->db->select('*');
              $this->db->from('transactions');
              $this->db->where('account_id', $this->ap);
              $this->db->where('property_id', $Posting->BuildingId);
              $this->db->where('trans_id', $bill);
              $query = $this->db->get();


              
  
            
  
              foreach ($query->result() as $row)
              {
                echo '<br/> bill detail'.$row->id;
                echo '<br/> bill amount'.$row->credit;
                echo '<br/> property amount $'.$properties[$row->property_id]['amount'].'<br/>';

                if(($row->credit - $row->debit) <= $properties[$row->property_id]['amount']){
                  $applied_amount = ($row->credit - $row->debit);
                  $properties[$row->property_id]['amount'] = $properties[$row->property_id]['amount'] - ($row->credit - $row->debit);
                } else {
                  $applied_amount = $properties[$row->property_id]['amount'];
                  $properties[$row->property_id]['amount'] = 0;
                  
                }
                
                echo '<br/> applied amount $'.$applied_amount.'<br/>';
              }
                 echo '<br/> bill main'.$bill;
                 //for each accounts payable line

                 //get open balance

                 // if property amount = > open balance apply open balance

                 //else apply amount
              }
   */
              
          

       }
    

       // todo make whole import for now just update
       function iterate_maintenance(){
         echo 'iteratemaintenance';

         
        
       // $query = $this->db->get('maintenance');
         

        $url="https://rstarmanagement.managebuilding.com/Manager/api/v3/tasks?%24inlinecount=allpages&%24top=600&%24orderby=LastModifiedInstant%20desc&statusIds=1&statusIds=2&statusIds=3&statusIds=4&statusIds=5&priorityIds=1&priorityIds=2&priorityIds=3";
  
  
        $newjson = $this->get_curl_buildium($url);
        $tickets = $newjson->Items;
        $ticketArray = array();
        $this->db->select('GROUP_CONCAT(id)');
        $this->db->from('properties');
        $this->db->where('active','1');
        $qp = $this->db->get()->row('GROUP_CONCAT(id)');
        
        $properties = explode(",", $qp);

        //$categories = array('1684' => 1);
        //$priorities = ['Low', 'Normal', 'High'];
        //$statuses = ['','New', 'In Progress', 'Completed', 'Deffered', 'Closed'];
        //echo json_encode($newjson);

        
        
          foreach ($tickets as $ticket)
          {

            echo '<br>'.$ticket->Id;

                   //echo 'found '.$ticket->Id.' unitid: '.$ticket->UnitId.'  Requesting entity_id'.$ticket->RequestingEntityId.' <br>';
                  //echo $row->id;
                  //$buildiumRow = array_search($row->id, array_column($tickets, 'Id'));
                  if(in_array($ticket->PropertyId, $properties) &&  $ticket->TaskStatusId != 5  &&  $ticket->TaskStatusId != 3){
                    $url2="https://rstarmanagement.managebuilding.com/Manager/api/v3/tasks/".$ticket->Id;
  
              
                    $singleticket = $this->get_curl_buildium($url2);
                    echo $singleticket->RequestedBy;
                    $ticketArray[$ticket->Id] = array('id' => $ticket->Id, 'category' => 1, 'title' => $ticket->ShortDescription, 'description' => $ticket->Comments, 'created_by' => $ticket->CreatedByUserId, 'create_date' => $ticket->CreatedInstant, 'status' => $ticket->TaskStatusId, 'attachments' => null, 'messages' => null, 'checklist' => null, 'property' => $ticket->PropertyId, 'unit' => $ticket->UnitId, 'tenant' => $singleticket->RequestedBy->Id, 'entry_granted' => null
                    , 'last_updated' => $ticket->LastModifiedInstant
                    , 'dependencies' => null
                    , 'type_id' => null
                    , 'type_item_id' => null
                    , 'assigned_to' => $ticket->AssignedTo->Id
                    , 'due_date' => $ticket->DueDate
                    , 'tags' => ''
                    , 'attention' => 0
                    , 'priority' => ($ticket->PriorityId -1) );

                    //$tickets = json_decode(json_encode($newjson->Items),TRUE);
                    
                  }

                  echo '<br> Requested by'.isset($singleticket->RequestedBy) ? $singleticket->RequestedBy->Id : "".' <br>';
                  

                  
          } 

          if(count($ticketArray) > 0){
            $this->db->insert_batch('maintenance', $ticketArray); 
        }

      }



       // Get Banks from system 
       function iterate_banks(){
        
        $query = $this->db->get_where('accounts', array('account_types_id' => 1));

        
        $counter = 1;
          foreach ($query->result() as $row)
          {
                  echo $counter;
                  $counter++;
                  echo $row->id;
                  echo '<br>';
                  echo $row->name;
                  echo '<br>';
                  echo $row->accno;
                  $this->get_buildium_recs($row->id, $row->accno);
                  $this->get_buildium_bank_info($row->id, $row->accno);

                  
          }

     }
       
    // Get buildium Bank recs
    function get_buildium_recs($id, $accno){
    $url="https://rstarmanagement.managebuilding.com/Manager/api/bankAccounts/".$accno."/reconciliations";


        $newjson = $this->get_curl_buildium($url);
        $recs = array();

        foreach ($newjson->FinishedReconciliations as $rec) {
            $rec1 = array(
                'id' => $rec->Id ,
                'statement_end_date' => $rec->StatementEndingDate ,
                'beginning_bal' => $rec->StatementBeginningBalance ,
                'ending_bal' => $rec->StatementEndingBalance ,
                'account_id' => $id ,
                'closed' => 1
            );

            array_push($recs, $rec1);
            $this->get_buildium_rec_trans($rec->Id, $rec->BankGLAccountId);
            }

            if(count($recs) > 0){
                $this->db->insert_batch('reconciliations', $recs); 
            }
        }


         // Get buildium transactions
    function get_buildium_trans(){
      $url="https://rstarmanagement.managebuilding.com/Manager/api/reports/Financials/GeneralLedger.csv/download?isQuickExport=true";

          $accounts = $this->get_buildium_accounts();
          $transToEnter = array();
          $file = $this->get_curl_file_buildium($url);
          //echo $file;
          $file2 = explode(PHP_EOL, $file);
          foreach ($file2  as $key => $trans) {
            $file2[$key] = str_getcsv($trans);
            $property = $file2[$key][24];
            if($file2[$key][0]== 18251392){
              echo '<br>'.$trans.'<br> trans_id: '.$file2[$key][0].' GLAccountName: '.$file2[$key][11].' type: '.$file2[$key][14].' amount:'.(float)$file2[$key][6];
            }
            if($file2[$key][14] == 51){ 
              $prop_id = $this->db->get_where('properties', array('name' => $property))->row()->id;
              echo '<br>Missing';
              $trans1 = array(
                'trans_id' => $file2[$key][0],
                'account_id' => $file2[$key][8],
                'description' => $file2[$key][5].' newbuildiumimport',
                'debit' => (float)$file2[$key][6]>0 ? (float)$file2[$key][6]:0,
                'credit' => (float)$file2[$key][6]<0 ?0 - (float)$file2[$key][6]: 0,
                'line_number' => $file2[$key][0],
                'property_id' => $prop_id
            );


            $transToEnter[$key] = $trans1;
            //array_push($transToEnter, $trans1);
            //echo '<br>'.$trans.'<br> trans_id: '.$file2[$key][0].' GLAccountName: '.$file2[$key][11].' type: '.$file2[$key][14].' amount:'.(float)$file2[$key][8];

            }
            //echo '<br> trans_id: '.$file2[$key][0].' GLAccountName: '.$file2[$key][11];
          }

          if(count($transToEnter) > 0){
            $this->db->insert_batch('transactions', $transToEnter); 
         }
          //echo $file;

      }

    // Get buildium accounts
    function get_buildium_accounts(){
      $url="https://rstarmanagement.managebuilding.com/Manager/api/glAccounts/all?%24top=500&%24orderby=SortName%20asc";

          $json = $this->get_curl_buildium($url);
          $accounts = array();

          foreach ($json->Items  as $account) {
            $accounts[$account->Id] = (array) $account;
          }
          return $accounts;
   
      }

      // enter buildium accounts
    function enter_buildium_accounts(){
         $accounts = $this->get_buildium_accounts();
         $accountsToEnter = array();


         foreach ($accounts  as $account) {
           if ($account['SubType'] == 51){
            $account1 = array(
              'id' => $account['Id'],
              'name' => $account['Name'],
              'account_types_id' => 16,
              'accno' => $account['Id'],
              'description' => $account['Description'],
              'all_props' =>1,
              'parent_id' =>0,
              'active' => 1
            );
             $accountsToEnter[$account['Id']] = $account1;
             echo '<br>'.$account['Name'];
           }
          
        }


        if(count($accountsToEnter) > 0){
          $this->db->insert_batch('accounts', $accountsToEnter); 
       }
   
      }


    function get_buildium_bank_info($id, $accno){
        $url="https://rstarmanagement.managebuilding.com/Manager/api/bankAccounts/".$accno;
        
    
            $newjson = $this->get_curl_buildium($url);
            $bank_routing = $newjson->ExtendedBankAccountInfo->RoutingNumber;
            $bank_account = $newjson->ExtendedBankAccountInfo->AccountNumberUnformatted;
            $bank_name = $newjson->CheckPrintingInfo->BankInformationLine1;

            echo 'name: '. $newjson->CheckPrintingInfo->CompanyInformationLine1.'address:'.$newjson->CheckPrintingInfo->CompanyInformationLine2.'bank Name: '.$bank_name;
            $bankData = array(
                "account_id"=>$id, 
                "bank_name" => $newjson->CheckPrintingInfo->BankInformationLine1, 
                "routing" => $newjson->ExtendedBankAccountInfo->RoutingNumber,
                "account_number" => $newjson->ExtendedBankAccountInfo->AccountNumberUnformatted);
            $bankData = $this->encryption_model->encryptThis($bankData);
            $this->db->insert('banks', $bankData);
            $bank_id =$this->db->insert_id();

            $entity_data = array(
                "name" => $newjson->CheckPrintingInfo->CompanyInformationLine1, 
                "address" => $newjson->CheckPrintingInfo->CompanyInformationLine2,
                "address2" => $newjson->CheckPrintingInfo->CompanyInformationLine3
            );


            $bankPropsUrl='https://rstarmanagement.managebuilding.com/Manager/api/bankAccounts/'.$accno.'/properties?%24inlinecount=allpages&includeLeaseOverrideSettings=true';    
            $bankProps = $this->get_curl_buildium($bankPropsUrl);
            if (count($bankProps->Items)>0){
                $this->db->insert('entities', $entity_data);
                $entity_id =$this->db->insert_id();
                $this->db->update('properties', array('entity_id' => $entity_id), array('default_bank' => $id));
                foreach ($bankProps->Items as $prop) {
                    $property_q = $this->db->get_where('properties', array('name' => $prop->BuildingName));
                    $property_id = $property_q->row()->id;;
                    $this->db->update('banks', array('property' => $property_id), array('id' => $bank_id));
                    echo '<br> building Name: '.$prop->BuildingName;
                }
            }




    }
    
    // global curl fuction
    function get_curl_buildium($address){
        $ch = curl_init();
            $headers = array(
                
                'Connection: keep-alive',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36e',
                'Host: rstarmanagement.managebuilding.com',
                'Accept: application/json, text/plain, */*',
                'cookie: _ga=GA1.2.1118987630.1660853443; _evga_c878=0c3f72988d0b8a92.0Lj; _gid=GA1.2.1684552742.1662487482; properties_1123677=All; _gat=1; _hp2_id.1205160675=%7B%22userId%22%3A%224754099293938360%22%2C%22pageviewId%22%3A%227125671423874647%22%2C%22sessionId%22%3A%22235445134007509%22%2C%22identity%22%3A%228e3eb306-effc-4652-a66d-8989eba26f34%22%2C%22trackerVersion%22%3A%224.0%22%2C%22identityField%22%3Anull%2C%22isIdentified%22%3A1%7D; _hp2_ses_props.1205160675=%7B%22r%22%3A%22https%3A%2F%2Frstarmanagement.managebuilding.com%2FManager%2Fapp%2Frentalowners%2F1049227%2Fproperties%22%2C%22ts%22%3A1662652884583%2C%22d%22%3A%22rstarmanagement.managebuilding.com%22%2C%22h%22%3A%22%2FManager%2Fpublic%2Fauthentication%2Flogin%22%2C%22q%22%3A%22%3FReturnUrl%3D%252FManager%252Fapp%252Fproperties%252Fproperty%252F89733%252Fsummary%22%7D; AKA_A2=A; XSRF-TOKEN=d5b99edbfab8a72c54d72f39f79ad90d; Manager.AspNet.ApplicationCookie=2HcOnCCIkZ1SSPUXmUmIrTwtPJoXREA9ri9vo2HqH-YvFH25YBF2vcyHIJbOs_LZOuwwfbapKz7VatlJ_3mwn-Gd0vSnRFD1XEv2bXo0_KrDoa0CtWVVJtef4-cOWCrrLPDYVkZtKDUPrvcLVgIEus57u8Ck9erHa39gFfGDE1GkE6XtFZU_9dlVb4sJGs-XKsr1y35E4J6AYKApN7Lb9K0jZvV1VWL7LG3H0V-2gLZFQ179S7idzMs1erMTBKfJduE_MFpWW6hJzetZc-cwqKJsXxmhPix5zxsehpoJoWA10Iz7n2mVHHmDczoVTP7ZIhmgeVc_6A3L2L2soEwbvXd6ZmPz_CnV0bMjZ8vx2iXOCcIEI65PqduPw4AypRvIGgjJ75R3zKzEFtopxJH3RUDD7QHWgrH7WmVJ-CP8jaPszQDDMLcor7f02DPjxeBmtLX1jMDfOq9yv1QHjDbXtIvz8i8_b1XP07Xi5aEtDL4U_wQtPe6oIm6WhwJtZWMPafLqkGTN0GvZ9C7RViaJqW8I_-Ph6RCXNrPi7w1l4AQ_sXb3VruN_0m4AA0nMfMgidyYZiXseo5Xe98waj-IcnDf2ILBS5lye5HcjfnZkydrRw94SVo--kXd8E_9qc49w_HmsvZFm667NGlKbRHo9nZi3h39pICRfyzP6iMdYPyqYqvU8jPMCwL3NcajsRTdyGw1PDl2XJyg6zM9adwUAN27pGE'
                );
            curl_setopt($ch, CURLOPT_URL, $address);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $body = '{}';
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_ENCODING, 'identity');
            $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $json = curl_exec($ch);
            curl_close($ch);
    
            $newjson = json_decode($json);
            return $newjson;

    }

     // file fuction (using to get transaction report)
     function get_curl_file_buildium($address){
      $ch = curl_init();
          $headers = array(
              
              'Connection: keep-alive',
              'Content-Type: application/json',
              'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36e',
              'Host: rstarmanagement.managebuilding.com',
              'Accept: application/json, text/plain, */*',
              'X-Correlation-Id: a929015b51a748f2ac84b25ae3afa090',
              'X-XSRF-TOKEN: 5b0978ca973e95862d4b5ad30fc9b755',
              'cookie: _ga=GA1.2.592001624.1637609533; rememberEmailCookie=blueelmny@gmail.com; _at_id.buildium.publicsite_tenantportal.cee0=a3454c00f85ce163.1642106109.1.1642106109.1642106109.19.19.; _evga_c878=f080641730873a20.07k; optimizelyEndUserId=oeu1644944783368r0.693025149538882; TwoFactorCookie-237208_511998=1E714A53FBB1182661816C5187D45870C4401291; _gid=GA1.2.2076709847.1650899284; _hp2_ses_props.1205160675=%7B%22r%22%3A%22https%3A%2F%2Frstarmanagement.managebuilding.com%2FManager%2Fapp%2Faccounting%2FchartOfAccounts%22%2C%22ts%22%3A1651074409332%2C%22d%22%3A%22rstarmanagement.managebuilding.com%22%2C%22h%22%3A%22%2FManager%2Fapp%2Fbanking%2Fbank-account%2F400853%2Fregister%22%7D; XSRF-TOKEN=5b0978ca973e95862d4b5ad30fc9b755; Manager.AspNet.ApplicationCookie=nXBl-5Wc5W3XKeUgNny4ZkiQeMmOap8JeOc-VAGWBZwDtiBVg7ozA-R-h5vBnkjYtH63cgeutpRK6NAe9HZQfleXmOTcjfkgVTx7s-IsHY2R1cxW8yTs2YrBxxghW3J5Ffq58DiYhQiqWeH06HeCDqjMYsYETnj3RcHbJhNmCRLCOiv6K6z6a788NZlHFft6QcS1wglHnoGXHYlY1CKj1eX5MduWva3l3HW2GYqXcN2ihkfM4xCv9lPBKPWVDkjp85NuQ2I9PEGvhw1un67a_PDfr0Dai-I6NxOeRTmiGd1lQEBD19d0ZqRCJHdJMEtkknrxz416gLZ5cC40JLCAB8JIC-hxJ9jOt0vCI6Wd3ajFiF6cuPdzL_rEs2cwvc9A2Lz5o0koh48DlbTcnTYVLASCFY6jrLt1HTYHOuet4G5yqrCh2k5-FBFhcGeDY88JvmsQGCRj6n2MU96OYa_HxWWu56_DD07OxFc6lPaORq2fXO_qXPCd8kBSIGgQTwSCv8eBwXqCr_fqslZkNtMih-ks8T8MEwpMYRbuQeYOMXmmuQz_Yx_oGf2VsX1C9zAc7nmkeyDihNLqtrL9vKTsplyakUv5NUDXIoBtctzpTptIVSfCWfvHEfxPXBEYOIcF8KNVDbwnSEGerN_D_mn7V9k907aX7nmmr1zG7u151W7iLfQLfIzzuMO_nR2BDUgi4QCWeSZrkGsNoAgTHvCODfcTLpA; _evgn_c878=%7B%22puid%22%3A%22wa3mT3AzNxG8XJoVSU8OOIMe2HxqBbN3cZoau2Cr5LtE8ujRC_PSTSBk12a1Cyq2vyX3BORWRUXOp1dpdWwp5A%22%2C%22paid%22%3A%220OF-usR6PTN3vZFQjkCK-tq7_kq4C1fUo_ItfDO-MBc%22%7D; _hp2_id.1205160675=%7B%22userId%22%3A%228261768640819380%22%2C%22pageviewId%22%3A%223177833028706023%22%2C%22sessionId%22%3A%223972667606145027%22%2C%22identity%22%3A%22a98e8d2a-933b-4f3e-bc36-d4c7d2f7625e%22%2C%22trackerVersion%22%3A%224.0%22%2C%22identityField%22%3Anull%2C%22isIdentified%22%3A1%7D; properties_511998=-2; reportDownload_Financials_GeneralLedger_237208=%7b%22MaxNumberOfRowsLimitExceeded%22%3afalse%2c%22TotalNumberOfRows%22%3a37403%2c%22MaxNumberOfRowsLimit%22%3a0%2c%22LastRecordDate%22%3a%22%22%7d'
              );
          curl_setopt($ch, CURLOPT_URL, $address);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt($ch, CURLOPT_HEADER, 0);
          $body = '{"reportCategory":"Financials","reportName":"GeneralLedger","fileFormat":"csv","inlineQueryString":"?isQuickExport=true","ReportQueryJson":"{\"Criteria\":{\"ReportTitle\":\"General Ledger\",\"ShowCompany\":false,\"EditionId\":2,\"currentDate\":\"2022-04-25\",\"reportDatabaseId\":11,\"fromDate\":\"2008-01-01\",\"toDate\":\"2026-04-25\",\"accountingbasis\":\"0\",\"buildingid\":\"-2\",\"building\":\"All\",\"type\":\"special\",\"filter\":{\"Type\":\"AllPropertiesAndCompany\",\"EntityId\":2},\"isAllPropertiesSelected\":false,\"daterange\":\"1:YTD\",\"skipDateRangeLimit\":\"true\",\"splitUpLongDateSpan\":\"true\"},\"ReportCustomization\":{\"VisibleColumnNames\":[\"Date\",\"JournalCodeDescription\",\"UnitNumber\",\"PayeeName\",\"Description\",\"DebitAmount\",\"CreditAmount\",\"Balance\"],\"GroupColumnNames\":[\"BuildingName\",\"GLAccountTypeName\",\"GLAccount\"],\"NaturalSortColumnNames\":[\"UnitNumber\"],\"SortByColumnName\":\"Date\",\"SortOrder\":0,\"IsExpanded\":false},\"PageOrientation\":1,\"PrintScale\":1,\"CustomPrintScale\":null,\"Offset\":0,\"IsDetailsTab\":false,\"IsSummaryTab\":false,\"ShowGridLines\":false}","CsrfToken":"b8ac11656be167c61d9f9eec07fbfcc0"}';
          curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
          curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_ENCODING, 'identity');
          $st = curl_exec($ch);
          //$fd = fopen($tmp_name, 'w');
          //fwrite($fd, $st);
          //fclose($fd);
          curl_close($ch);
  
          //$newjson = json_decode($json);
          return $st;

  }

     // get list of transactions for each rec from buildium and update rec_id in simpli-city
     function get_buildium_rec_trans($rec_id, $account_id){
        $url="https://rstarmanagement.managebuilding.com/Manager/api/bankAccounts/$account_id/reconciliations/".$rec_id;


        $newjson = $this->get_curl_buildium($url);

        $trans1 = '1';

        foreach ($newjson->JournalEntries as $trans) {
            $trans1 = $trans1.','.$trans->Id;
        }
        
        if(count($trans1) > 0){
            $this->db->query('UPDATE transactions JOIN accounts ON accounts.id= transactions.account_id SET `rec_id` = '.$rec_id.', clr = 1  WHERE `trans_id` IN('.$trans1.') AND `accno` = '.$account_id);  
        }
     }


   
      //buildium autocharges
      function iterate_leases(){
        //select leases
        $query = $this->db->get('leases');
        

          foreach ($query->result() as $row)
          {
                  echo $row->id;
                  echo '<br>';
                  echo $row->start.'-'.$row->end.' Amount:'.$row->amount;
                  $this->get_buildium_lease_autocharges($row->id, $row->memo);
                  //$this->get_buildium_lease_tenants($row->id, $row->memo);
          }

          //$sql1 = "UPDATE `transactions` AS t1 JOIN transactions t2  ON t1.deposit_id = t2.trans_id SET t1.deposit_id = t2.id WHERE t2.account_id = ".$this->site->settings->undeposited_funds;
          //$this->db->query($sql1);
     }

     function get_all_buildium_tenants(){
        $url="https://rstarmanagement.managebuilding.com/Manager/api/users/tenants?%24inlinecount=allpages&%24top=2000&%24orderby=LastName%20asc&ResidentStatuses=2&ResidentStatuses=4&ResidentStatuses=16";

        $newjson = $this->get_curl_buildium($url);
        //return $newjson;
        foreach ($newjson->Items as $user) {
            echo '<br><br>home: '.$user->Home.' work: '.$user->Work.' mobile: '.$user->Mobile.' '.$user->FirstName.' '.$user->LastName.'<br>'; 
            $primary_phone = $user->Work;
            $num=0;
            if ($user->Work != null){$num += 1;}
            if ($user->Mobile != null){$primary_phone = $user->Mobile; $num += 1;} 
            if ($user->Home !=null){$primary_phone =$user->Home;  $num += 1;}
            if($num>1){
                $contact_info = array(
                    "first_name"=> $user->FirstName, 
                    "last_name" => $user->LastName, 
                    "cell" => $user->Mobile,
                    "home" => $user->Home,
                    "work" => $user->Work);
                  $this->db->insert('contacts', $contact_info);
        
                  $contact_id =$this->db->insert_id();
                  $tenant_id = $this->db->get_where('profiles', array('tax_id' => $user->Id))->row()->id;
                  $contact = array(
                    "profile_id"=> $tenant_id, 
                    "contact_id" => $contact_id);
                  $this->db->insert('profile_contact', $contact);
            }
            if($num>0){
                $this->db->update('profiles', array('phone' => $primary_phone), array('tax_id' => $user->Id));
            }

  
  
          }
     }

     function get_all_buildium_leases(){
        $url="https://rstarmanagement.managebuilding.com/Manager/api/leases?%24inlinecount=allpages&%24top=100&%24orderby=LeaseDescription%20asc&LeaseStatuses=1&LeaseStatuses=3";

        $newjson = $this->get_curl_buildium($url);
        return $newjson;

     }

     function get_buildium_lease_autocharges($lease_id, $blease_id){
        $url="https://rstarmanagement.managebuilding.com/Manager/api/leases/".$blease_id."/recurringTransactionsByLeaseId?%24inlinecount=allpages&%24top=100&%24orderby=NextChargeDate%20asc";

        $newjson = $this->get_curl_buildium($url);
        echo '<br><br>';

      foreach ($newjson->Items as $autocharge) {
        echo '<br><br>ID: '.$autocharge->Id.'NextChargeDate: '.$autocharge->NextChargeDate.' TransactionType: '.$autocharge->TransactionType.' Amount: '.$autocharge->Amount.' glaccount:'.$autocharge->GLAccountName;
          if ($autocharge->TransactionType == 'Charge' or $autocharge->TransactionType == 'Credit') {
            $account_id = $this->db->get_where('accounts', array('accno' => $autocharge->GLAccountId))->row()->id;
            $item_id = $this->db->get_where('items', array('acct_income' => $account_id))->row()->id;
            $unit_id = $this->db->get_where('leases', array('id' => $blease_id))->row()->unit_id;
            $property_id = $this->db->get_where('units', array('id' => $unit_id))->row()->property_id;
            $profile_id = $this->db->get_where('leases_profiles', array('lease_id' => $blease_id))->row(0)->profile_id;
            $lease_start = $this->db->get_where('leases', array('id' => $blease_id))->row(0)->start;
            $amount = $autocharge->TransactionType == 'Credit' ? 0-$autocharge->Amount:$autocharge->Amount;
  
            
            $autochargenew = array(
              //"id"=> $autocharge->Id,
              "name"=> $autocharge->Memo, 
              "transaction_type" => 6, 
              "type_id" => 2,
              "type_item_id" => $blease_id,
              "frequency" => 1,
              "start_date" => $lease_start,
              "end_date" => $autocharge->LeaseEndDate,
              "next_trans_date" => $autocharge->NextChargeDate,
              "amount" => $amount,
              "data" => '{"header":{"transaction_date":"2019\/02\/13"},"transactions":{"account_id":"'.$account_id.'","profile_id":"'.$profile_id.'","property_id":"'.$property_id.'","unit_id":"'.$unit_id.'","lease_id":"'.$blease_id.'","item_id":"'.$item_id.'","credit":"'.$amount.'","description":"'.$autocharge->Memo.'"}}',
              "auto" => 1,
              "property_id" => $profile_id);
              echo $autochargenew['data'].'<br>';
              $this->db->insert('memorized_transactions', $autochargenew);
          }
         
        }



     }

     function get_buildium_lease_tenants($lease_id, $blease_id){
        $url="https://rstarmanagement.managebuilding.com/Manager/api/leases/".$blease_id."/residents";

        $newjson = $this->get_curl_buildium($url);
        echo '<br><br>';

      foreach ($newjson as $user) {
          //$q1 = $this->db->update('transactions', array('deposit_id' => $deposit), array('trans_id' => $payment->Id));
          echo '<br><br>home: '.$user->UserDetails->Home.' work: '.$user->UserDetails->Work.' mobile: '.$user->UserDetails->Mobile.' '.$user->UserDetails->FirstName.' '.$user->UserDetails->LastName.'<br>'; 
          $contact_info = array(
            "first_name"=> $user->UserDetails->FirstName, 
            "last_name" => $user->UserDetails->LastName, 
            "cell" => $user->UserDetails->Mobile,
            "home" => $user->UserDetails->Home,
            "work" => $user->UserDetails->Work);
          $this->db->insert('contacts', $contact_info);

          $contact_id =$this->db->insert_id();
          $tenant_id = $this->db->get_where('profiles', array('tax_id' => $user->UserDetails->Id))->row()->id;
          $contact = array(
            "profile_id"=> $tenant_id, 
            "contact_id" => $contact_id);
          $this->db->insert('profile_contact', $contact);


        }

     }


     function get_buildium_properties(){
        $url="https://rstarmanagement.managebuilding.com/Manager/api/properties?%24inlinecount=allpages&%24top=100&%24orderby=Name%20asc&propertySelectionType=AllRentals&SubTypeIds=1&SubTypeIds=2&SubTypeIds=3&SubTypeIds=10&SubTypeIds=11&SubTypeIds=12&SubTypeIds=13&SubTypeIds=14&SubTypeIds=15&status=-1&RentalOwnerId=null";


        $newjson = $this->get_curl_buildium($url);


      foreach ($newjson->Items as $property) {
          //$q1 = $this->db->update('transactions', array('deposit_id' => $deposit), array('trans_id' => $payment->Id));
          //echo '<br><br>name: '.$property->Name.'status: '.$property->Status.'id: '.$property->Id.' Bank '.$property->OperatingBankAccount;
        }

        return $newjson->Items;

     }

     function iterate_properties(){

        $properties = $this->get_buildium_properties();
        echo 'units';

        foreach ($properties as $property) {
          //$q1 = $this->db->update('transactions', array('deposit_id' => $deposit), array('trans_id' => $payment->Id));
          echo '<br><br>name: '.$property->Name.'status: '.$property->Status.'id: '.$property->Id;
          $this->get_buildium_units($property->Id);
        }
        

     }

     function iterate_units(){
         $properties = $this->get_buildium_properties();
         foreach ($properties as $property){
             echo '<br>'.$property->Name;
            $this->get_buildium_units($property->Id);
         }
     }


     function get_buildium_units ($property){
         echo $property;
         $url="https://rstarmanagement.managebuilding.com/Manager/api/properties/".$property."/unitsList";


        $newjson = $this->get_curl_buildium($url);
        $unitArray = array();

        foreach ($newjson as $unit) {
            //$q1 = $this->db->update('transactions', array('deposit_id' => $deposit), array('trans_id' => $payment->Id));
            echo '<br><br>name: '.$unit->Name.'id: '.$unit->Id;
            $unit->property_id = $property;
            $unitArray[$unit->Id] = array('property_id' => $property, 'name' => $unit->Name, 'id' => $unit->Id, 'active'=> 1, 'parent_id'=> 0);
            

            $this->get_buildium_leases($property, $unit->Id);
          }
          $this->db->insert_batch('units', $unitArray);

          return $newjson;
     }


     function get_buildium_leases ($property,$unit){
        echo $property;
        $url="https://rstarmanagement.managebuilding.com/Manager/api/properties/".$property.'/units/'.$unit."/leases";


       $newjson = $this->get_curl_buildium($url);

       foreach ($newjson as $lease) {
         $lease1=array();
           //$q1 = $this->db->update('transactions', array('deposit_id' => $deposit), array('trans_id' => $payment->Id));
           echo '<br><br>id: '.$lease->LeaseId.' name: '.$lease->ResidentNames.' LeaseStatus: '.$lease->LeaseStatus.' Move Out: '.$lease->MoveoutDate;
           $lease->property_id = $property;
           $lease->unit_id = $unit;

           
           $lease1['unit_id'] = $unit;
           $lease1['id'] = $lease->LeaseId;
           $lease1['start'] = $lease->StartDate;
           $lease1['end'] = $lease->EndDate;
           $lease1['amount'] = $lease->Rent;
           $lease1['move_out'] = $lease->MoveoutDate;
           $lease1['memo'] = $lease->LeaseId;
           //$this->db->update('leases', array('move_out' => 'end'), array('id' => $lease->LeaseId));
           $this->db->insert('leases', $lease1);

          $this->get_lease_tenants($lease->LeaseId );
         }

         return $newjson;
    }
    function get_lease_tenants($LeaseId ){
      $url="https://rstarmanagement.managebuilding.com/Manager/api/leases/".$LeaseId."/tenants";


      $newjson = $this->get_curl_buildium($url);

      foreach ($newjson as $lease_profile) {
        $leases_profiles = array('profile_id' => $lease_profile->Id, 'lease_id' => $LeaseId, 'unit_id' => $lease_profile->UnitId, 'active'=> 1);
        $this->db->insert('leases_profiles', $leases_profiles);
      }

    }












     ///test function

     function get_buildium_deposit_payments_test(){
        $url="https://rstarmanagement.managebuilding.com/Manager/api/deposits/26848230";
        $ch = curl_init();
          $headers = array(
              'Accept-Encoding: gzip, deflate, br',
              'Connection: keep-alive',
              'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36e',
              'Host: rstarmanagement.managebuilding.com',
              'Accept: application/json, text/plain, */*',
              'cookie: _ga=GA1.2.592001624.1637609533; rememberEmailCookie=blueelmny@gmail.com; _at_id.buildium.publicsite_tenantportal.cee0=a3454c00f85ce163.1642106109.1.1642106109.1642106109.19.19.; _evga_c878=f080641730873a20.07k; optimizelyEndUserId=oeu1644944783368r0.693025149538882; TwoFactorCookie-237208_511998=1E714A53FBB1182661816C5187D45870C4401291; properties_511998=-2; reportDownload_Financials_GeneralLedger_237208=%7b%22MaxNumberOfRowsLimitExceeded%22%3afalse%2c%22TotalNumberOfRows%22%3a46141%2c%22MaxNumberOfRowsLimit%22%3a0%2c%22LastRecordDate%22%3a%22%22%7d; _gid=GA1.2.2107623187.1647882736; XSRF-TOKEN=f5c1ab285845ae78686234139f5b6c19; Manager.AspNet.ApplicationCookie=J67OQdpHsA47TVjUvQDVAhvchCXRiX7b0htMcnKr4m5m74Cr0aazwKRX24d5PcpBwVPuLTRg1e6JBWUk-WkQeNYIVZzy6ylzobQUWfOjM3T2tetZ0lmuGQO8aJQG3f7n_hkMEWeg2CUYWvqE4eOCopPKd_zaA1HRVGtrfLA9mN1fxUf4SXiMgAJQGehoXRuaIdo7PFsAllmsKO7CIgBI8zTfggJ5xb8Sx1psN0lNapoUjO4A9okwutziTw0yWkFLDyiWScOrhwyB07eglPT21OxxHV-M77lFsPMZX_1C2uPY92326fYo0DBRWPXzyzahxmBRh9eIxo9JUDFtFj23si4rcNtPCY-f0OtgQgwB3DLNDN0F2-UIxLY-dfycIGNgBvK924N0n5El938R9uLCWASZ1_ksZsoP8s2C3HfW6cKbG_yHe62yQTQfk9wk9hluDGTta7wZmXBEo7bWwmzjWDvxmI6cnKeT00V6BZAMWCaaUhRtaHCzUW1dpOMcVb7gDUuHKaVW_hV89abgksZcNzdoW1gp3iB3fu9qnevG5vN58ywtN95SvHywoNjz-jHK791cZYe9uIrBakS8W56LjWpaJLvhTx1-GC8SCouamG0G8Xe5GHmLhc5g1o3YXzzk7EqN82tUjhN8tQdG3oRY5jhTfXJjx5Y8-_0zg9pkaQ_uR-gF_wOpngHaxFmXpUmbXWUAyRHLiMJhiqwusvZkwD1h1bA; _hp2_ses_props.1205160675=%7B%22r%22%3A%22https%3A%2F%2Frstarmanagement.managebuilding.com%2FManager%2Fapp%2Faccounting%2Fbills%3Finitpage%3D1%22%2C%22ts%22%3A1647891732793%2C%22d%22%3A%22rstarmanagement.managebuilding.com%22%2C%22h%22%3A%22%2FManager%2Fapp%2Frentals%2Ftenants%2F526485%2Fsummary%22%7D; _gat=1; _hp2_id.1205160675=%7B%22userId%22%3A%228261768640819380%22%2C%22pageviewId%22%3A%221411830802588835%22%2C%22sessionId%22%3A%228081246441885229%22%2C%22identity%22%3A%22a98e8d2a-933b-4f3e-bc36-d4c7d2f7625e%22%2C%22trackerVersion%22%3A%224.0%22%2C%22identityField%22%3Anull%2C%22isIdentified%22%3A1%7D; _evgn_c878=%7B%22puid%22%3A%22FL8B6aJcQ8J7a-9ir76nNCYzgAOm3j6JM99AL33z4ngpygD2PQqu0IqgqZTK9UWxXsNDww7lKdUu5_HLra4S-A%22%2C%22paid%22%3A%22NoaC_OvEAtiidtwBEPN4191N34VXTw8jOM0L7cq9CNc%22%7D'
              );
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
          curl_setopt($ch, CURLOPT_HEADER, 0);
          $body = '{}';
          curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
          curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

          $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

          $json = curl_exec($ch);
          curl_close($ch);

      $newjson = json_decode($json);
      echo $response;

      foreach ($newjson->DepositedPaymentIds as $payment) {
          //$q1 = $this->db->update('transactions', array('deposit_id' => $deposit), array('trans_id' => $payment->Id));
          echo $payment->BuildingName.'<br><br>'.$payment->Id.'<br><br>'.$payment->TotalAmount;
        }

     }
/// not used just keeping for reference     
       function get_buildium()
       {
        //define("DOC_ROOT","/path/to/html");
        //$config['allowed_types'] = '*';
        //$config['upload_path'] = 'uploads/ctemp';
        //$this->load->library('upload', $config);
        //$this->upload->do_upload('file');
        //$data['name'] = $this->upload->data('file_name');
        //username and password of account
        $username = 'blueelmny@gmail.com';
        $password = 'Lazar@9335';
        
        //set the directory for the cookie using defined document root var
        //$path = DOC_ROOT."/ctemp";
        //build a unique path with every request to store. the info per user with custom func. I used this function to build unique paths based on member ID, that was for my use case. It can be a regular dir.
        //$path = build_unique_path($path); // this was for my use case
        
        //login form action url
        $url="https://rstarmanagement.managebuilding.com/Manager/api/deposits/13575856/payments"; 
        $postinfo = "email=".$username."&password=".$password;
        
        $cookie_file_path = base_url() . "uploads/cookie.txt";
        
        $ch = curl_init();
        $headers = array(
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36e',
            'Host: rstarmanagement.managebuilding.com',
            'Accept: application/json, text/plain, */*',
            'cookie: _ga=GA1.2.592001624.1637609533; rememberEmailCookie=blueelmny@gmail.com; _at_id.buildium.publicsite_tenantportal.cee0=a3454c00f85ce163.1642106109.1.1642106109.1642106109.19.19.; _evga_c878=f080641730873a20.07k; optimizelyEndUserId=oeu1644944783368r0.693025149538882; TwoFactorCookie-237208_511998=1E714A53FBB1182661816C5187D45870C4401291; _gid=GA1.2.797886145.1646765226; XSRF-TOKEN=c4d707156bf54575e6d210e5546a2b37; Manager.AspNet.ApplicationCookie=jFLdufcuJk0wHhKCG8xB3Gs4e_Xk1Cyj_CCO-XC6WFjcU6_Bbi63IF1fmYFyIrDSae3beZRixweSkRs_MxjHkTGBuuG1nfeNVeWMcTFICo28FYNHzN1V22aYOB33VGbyFPQIF85klN47CD8c4ZhRGE8dKslasCqLHPUHNTbTT7wLPGi2Zaq9CSh_3uFF00GfTD-lIhY8tDsTShebRb7lVdgNWlQStRwlagZMIRkbjz1PF-9FWzJgognhCxTsa3cS19YDZcmExONSXHPE7ujbHs0G7WMyPoQZhWQWZz2FO8Z_Wl0J_Xflsf0Svcs6JnsRnoTeqWjjEPe54Ua6UHtcwM2fBJkRC_3hNZpSIcfjufX3C9hibJIc60gL606EZSJDVvdv3BUcXanzrOo8vd4TaM6OJ13plosRkWmPz9c5L3TH7K-ynj1NuWxZNtnnYbz3Zh66PVZbsFeFTcngZ4mzsuN-9LVynBHk_nPqqPOl_KjYXe_jtXcaIl1ldDRcHxZKdTxeMnZ4uJ4TI4N3ruxN4kTWvmoSQEWICN8gczw1jpjsKVCvr0Xa-T3G4kgnbEmQcdivznvSb6Oh6vfNX1wC04kzpRaAOZp7PcFZKcIRIOPFV8XsFfF-p6PmY_kR955ytejKIqhr-pYaU_oxpJtiOMvRH5kBJnkH1MNAHP2qfn3ocfxXsZkxDHQ8RbYvMk7RH9s-d6F8pV1QiZg2cDK3yE2TTp8; properties_511998=-2; _gat=1; _hp2_ses_props.1205160675=%7B%22r%22%3A%22https%3A%2F%2Frstarmanagement.managebuilding.com%2FManager%2Fapp%2Faccounting%2Fbills%3Finitpage%3D1%22%2C%22ts%22%3A1646768510788%2C%22d%22%3A%22rstarmanagement.managebuilding.com%22%2C%22h%22%3A%22%2FManager%2Fapp%2Fbanking%2Fbank-account%2F246237%2Fpayment-settings%2Fepay%22%7D; _hp2_id.1205160675=%7B%22userId%22%3A%228261768640819380%22%2C%22pageviewId%22%3A%222594162829006384%22%2C%22sessionId%22%3A%226481722568866873%22%2C%22identity%22%3A%22a98e8d2a-933b-4f3e-bc36-d4c7d2f7625e%22%2C%22trackerVersion%22%3A%224.0%22%2C%22identityField%22%3Anull%2C%22isIdentified%22%3A1%7D'
            );
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            $body = '{}';

        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        //curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
        //set the cookie the site has for certain features, this is optional
        //curl_setopt($ch, CURLOPT_COOKIE, "cookiename=0");

        
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $body );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
        //curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        //page with the content I want to grab
        
        //do stuff with the info with DomDocument() etc
        
        //$info = curl_getinfo($ch);
        //var_dump($info);
        //$curl_error = curl_error($ch);

        $json = curl_exec($ch);
        curl_close($ch);

        echo '<br><br>';
        echo $json;
        $newjson = json_decode($json);
        echo '<br><br>';

        foreach ($newjson as $payment) {
            $q1 = $this->db->update('transactions', array('deposit_id' => '13575856'), array('trans_id' => $payment->Id));
            echo $payment->BuildingName;
            echo '<br><br>';
            echo $payment->Id;
            echo '<br><br>';
            echo $payment->TotalAmount;
          }

        //echo $curl_error;
        //echo $cookie_file_path;
       }

    }
