<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MemorizedTransactions_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->sd = $this->site->settings->security_deposits;
        $this->sdi = $this->site->settings->default_sd_item;
        $this->lmr = $this->site->settings->lmr;
        $this->lmri = $this->site->settings->default_lmr_item;
        $this->s8 = $this->site->settings->section_eight;
        $this->ins_prepaid = $this->site->settings->ins_prepaid;
        $this->tax_prepaid = $this->site->settings->tax_prepaid;
        $this->tax_expense = $this->site->settings->tax_expense;
        $this->mm = $this->site->settings->mid_month;
        //$this->load->model('validate_model');
    }

    public function addTransaction($specs, $transactionData)
    {   
        $transactionData = json_encode($transactionData);
        if(empty(trim($specs['next_trans_date'])) || (strpos($specs['next_trans_date'] , 'NaN') !== false)){
            $specs['next_trans_date'] = $specs['start_date'];
        }
        if(empty(trim($specs['end_date'])) || (strpos($specs['end_date'] , 'NaN') !== false)){//
            $specs['end_date'] = '9999/12/31';
        }
        
        $memorized_transaction = ['data' => $transactionData] + $specs;
        //$header['last_mod_by'] = $this->ion_auth->get_user_id();
        //$header['last_mod_date'] = date('Y-m-d H:i:s');
        //$header['transaction_type'] = 4;
    
        $this->db->insert('memorized_transactions', $memorized_transaction);
        
         return true;
    }

    //not done//don't know if ever used
    public function editTransaction($specs, $transactionData, $type_id, $type_item_id)
    {   
        $transactionData = json_encode($transactionData);
        if(empty(trim($specs['next_trans_date']))){$specs['next_trans_date'] = $specs['start_date'];}
        $memorized_transaction = ['data' => $transactionData] + $specs;
        $this->db->update('memorized_transactions', $memorized_transaction, array('type_id' => $type_id, 'type_item_id' => $type_item_id));
        
         return true;
    }

    public function editTransactionNew($specs, $transactionData, $mt_id)
    {
        $transactionData = json_encode($transactionData);
        if(empty(trim($specs['next_trans_date'])) || (strpos($specs['next_trans_date'] , 'NaN') !== false)){
            $specs['next_trans_date'] = $specs['start_date'];
        }
        if(empty(trim($specs['end_date'])) || (strpos($specs['end_date'] , 'NaN') !== false)){//
            $specs['end_date'] = '9999/12/31';
        }
        $memorized_transaction = ['data' => $transactionData] + $specs;
        $this->db->update('memorized_transactions', $memorized_transaction, array('id' => $mt_id));

         return true;
    }


    public function deleteTransaction($transactions)
    {   
        $transactionData = json_encode($transactionData);
        if(empty(trim($specs['next_trans_date'])) || (strpos($specs['next_trans_date'] , 'NaN') !== false)){
            $specs['next_trans_date'] = $specs['start_date'];
        }
        if(empty(trim($specs['end_date'])) || (strpos($specs['end_date'] , 'NaN') !== false)){//
            $specs['end_date'] = '9999/12/31';
        }
        $memorized_transaction = ['data' => $transactionData] + $specs;
        $this->db->update('memorized_transactions', $memorized_transaction, array('id' => $mt_id));
        
         return true;
    }

    public function getName($id)
    {
        $this->db->select('CONCAT_WS(" ",first_name, last_name) AS name');
        $this->db->from('profiles'); 
        $this->db->where('id', $id);
        $q = $this->db->get(); 
        if ($q->num_rows() > 0) {
            return $q->row()->name;
        } 
    }
    
// no property id ,item_id?, start and end date by ttl - is it the same as lease or is it move_in, name? what about active?

public function addSection8($data, $ttls, $lid, $sect8, $account_id,  $property_id, $item_id, $params, &$end)
{  //need to work out if date is empty because by default will become today
    $secData = $data;
    $dataStart = new DateTimeImmutable($data['start']);
    $dataEnd = new DateTimeImmutable($data['end']);
    $secStart = new DateTimeImmutable($sect8['start_date']);
    $secEnd = !empty((trim($sect8['end_date']))) ? new DateTimeImmutable($sect8['end_date']) : new DateTimeImmutable('9999/12/30');

    $end = $end ? $end : '';

   if(!empty($end)){
        $start = $end->modify('+1 day');

       if($start == $secStart){
            $end = ($secEnd > $dataEnd) ? $dataEnd : $secEnd;//means do section 8
       }else{
        $end = ($sect8['start_date'] && ($secStart <= $dataEnd)) ? $secStart->modify('-1 day') : $dataEnd;// means not section8 this period
        $data['start'] = $start->format('Y-m-d');
        $data['end'] = $end->format('Y-m-d');

        //do regular rent charge
        $this->addMonthlyLeaseCharge($data, $ttls, $lid, $account_id, $property_id, $item_id, $params);

        // if section 8  do section 8 after this
        if(!empty(trim($sect8['start_date'])) && ($secStart <= $dataEnd)){
            $params['index'] = $params['index'] + 1;
            $this->addSection8($secData, $ttls, $lid, $sect8, $account_id,  $property_id, $item_id, $params, $end);
            return;
        }else{
            return;
        }
       }
   }else{
        $start = $dataStart;
       if(!empty(trim($sect8['start_date'])) && ($secStart <= $dataStart)){
        $end = ($secEnd > $dataEnd) ? $dataEnd : $secEnd;//means do section 8 - will go to line 
       }else{
        $end = (!empty(trim($sect8['start_date'])) && ($secStart <= $dataEnd)) ? $secStart->modify('-1 day') : $dataEnd; // means not section8 this period
        $data1 = $data;
        $data1['start'] = $start->format('Y-m-d');
        $data1['end'] = $end->format('Y-m-d');
        //do regular rent charge
        $this->addMonthlyLeaseCharge($data1, $ttls, $lid, $account_id, $property_id, $item_id, $params);

        //do section 8 now if applicable
        if(!empty(trim($sect8['start_date'])) && ($secStart <= $dataEnd)){
            $params['index'] = $params['index'] + 1;
            $this->addSection8($secData, $ttls, $lid, $sect8, $account_id,  $property_id, $item_id, $params, $end);
            return;
        }else{
            return;
        }
        }

   }

    $move_in = $start->format('Y-m-d');
    $move_out = $end->format('Y-m-d');
    $data['start'] = $move_in;
    $data['end'] = $move_out;
    $sect8['voucher_amount'] = removeComma($sect8['voucher_amount']);
    $sect8Info = ['lease_id' => $lid, 'profile_id' => $this->s8, 'unit_id' => $data['unit_id'], 'amount' => $sect8['voucher_amount'], 'move_in' => $move_in, 'move_out' => $move_out, 'active' => 1];
    $this->db->insert('leases_profiles', $sect8Info);
    // foreach($ttls as &$ttl){
    //     $ttl['amount'] == $ttl['amount'] - $sect8['voucher_amount'];
    // }
    $ttls[] = $sect8Info;
    $ttls[0]['amount'] = removeComma($ttls[0]['amount']);
    $ttls[0]['amount'] = $ttls[0]['amount'] - $sect8['voucher_amount'];
    $this->addMonthlyLeaseCharge($data, $ttls, $lid, $account_id, $property_id, $item_id, $params);

    //if $data[end] < s8[end] break //needs to be in leases model

}

// public function addSection8($data, $ttls, $lid, $sect8, $account_id,  $property_id, $item_id, $params)
// {
//     $move_in = $sect8['start_date'] >= $data['start'] ? $sect8['start_date'] : $data['start'];
//     $move_out = (empty($sect8['end_date']) || $sect8['end_date'] > $data['end']) ? $data['end'] : $sect8['end_date'];
//     $data['start'] = $move_in;
//     $data['end'] = $move_out;
//     $sect8Info = ['lease_id' => $lid, 'profile_id' => $this->s8, 'unit_id' => $data['unit_id'], 'amount' => $sect8['voucher_amount'], 'move_in' => $move_in, 'move_out' => $move_out, 'active' => 1];
//     $this->db->insert('leases_profiles', $sect8Info);
//     // foreach($ttls as &$ttl){
//     //     $ttl['amount'] == $ttl['amount'] - $sect8['voucher_amount'];
//     // }
//     $ttls[] = $sect8Info;
//     $ttls[0]['amount'] = $ttls[0]['amount'] - $sect8['voucher_amount'];
//     $this->addMonthlyLeaseCharge($data, $ttls, $lid, $account_id, $property_id, $item_id, $params);

// }

public function memLeaseCharge($tenant, $lid, $transaction_date, $endDate, $next_trans_date, $account_id, $property_id, $item_id, $amount = null)
{
    $name = $this->getName($tenant['profile_id']);
                ///memorized trans for whole months
                $tenant['amount'] = removeComma($tenant['amount']);
                $transactionData = [];
                $transactionData = ['header' => ['transaction_date' => $transaction_date]];
                $transactionData +=  ['transactions' => ['account_id'=> $account_id, 'profile_id' => $tenant['profile_id'], 'property_id' => $property_id, 'unit_id' => $tenant['unit_id'], 'lease_id' => $lid, 'item_id' => $item_id, 'credit' => ($amount ? $amount : $tenant['amount']), 'description' => 'lease charge']];
                $specs = ['name' => 'Lease charge for' . ' '. $name, 'transaction_type' => 6, 'type_id' => 2, 'type_item_id' => $lid, 'frequency' => 1, 'start_date' =>$transaction_date, 'end_date' => $endDate, 'amount' => ($amount ? $amount : $tenant['amount']), 'next_trans_date' => ($next_trans_date ? $next_trans_date : ''), 'property_id' => $property_id, 'auto' => 1];
                $this->addTransaction($specs, $transactionData);
}

public function autoCharge($specs, $lid, $account_id, $property_id, $unit_id, $mt_id=NULL)
{
    $profile_id = $specs['profile_id'];
    $item = $specs['item_type_id'];
    $amount = $specs['amount'];
    unset($specs['profile_id']);
    unset($specs['item_type_id']);

    $transactionData = [];
    $transactionData = ['header' => ['transaction_date' => $specs['start_date']]];
    $transactionData +=  ['transactions' => ['account_id'=> $account_id, 'profile_id' => $profile_id, 'property_id' => $property_id, 'unit_id' => $unit_id, 'lease_id' => $lid, 'item_id' => $item, 'credit' => $amount, 'description' => 'Auto charge']];
    if($mt_id){
        $this->editTransactionNew($specs, $transactionData, $mt_id);
    }else{
        $this->addTransaction($specs, $transactionData);

    }
}
//     public function addMonthlyLeaseCharge($data, $ttls, $lid, $account_id, $property_id, $item_id,$add = null)
//     {

//         if(substr($data['start'], -2) != '01'){
//             if($add) $this->addMidMonthlyLeaseCharge($data, $ttls, $lid, $account_id, $property_id, $item_id);
//             $startDate = new DateTime($data['start']);
//             $firstDayNextMonth = $startDate->modify('first day of 1 month');
//             $next_trans_date = $firstDayNextMonth->format('Y-m-d');
//         }

//             $endDate = new DateTime($data['end']);
//             //get first day of second to last month
//             $first_day = $endDate->modify('first day of -1 month');
//             $lastWhole = $first_day->format('Y-m-d');
//             //$this->finalMonthLeaseCharge($data, $ttls, $lid);

//             if(substr($data['start'], -2) == '01' || $add === null){
//                 if (isset($ttls)){
//                     foreach ($ttls as &$ttl) {
//                         $this->finalMonthLeaseCharge($data, $ttl, $lid, $account_id, $property_id, $item_id); 
//                     }   
//                 }
//             }

//         if (isset($ttls)){
//                 foreach ($ttls as &$ttl) {

//                 $this->memLeaseCharge($ttl, $lid, $data['start'], $lastWhole, $next_trans_date, $account_id, $property_id, $item_id);

//                 }
//             }
//         else{
//             $transactionData = ['header' => ['transaction_date' => $data['start']]];
//             $transactionData +=  ['transactions' => ['account_id' => $account_id, 'property_id' => $property_id, 'unit_id' => $data['unit_id'], 'lease_id' => $lid, 'item_id' => $item_id, 'credit' => $data['amount'], 'description' => 'description']];
//         $specs = ['name' => $lid, 'transaction_type' => 6, 'type_id' => 2, 'type_item_id' => $lid, 'frequency' => 1, 'start_date' =>$data['start'], 'end_date' => $lastWhole , 'amount' => $data['amount'], 'next_trans_date' => $next_trans_date ? $next_trans_date : '', 'auto' => 1];
//         $this->addTransaction($specs, $transactionData);
//     }

// }
public function addMonthlyLeaseCharge($data, $ttls, $lid, $account_id, $property_id, $item_id, $params)
{   

    // $endYear =  substr($data['end'], 0, 4);
    // $endMonth = substr($data['end'], 5, 2);

    $startDate = new DateTime($data['start']);
    $firstDayNextMonth = $startDate->modify('first day of 1 month');
    $next_trans_date = $firstDayNextMonth->format('Y-m-d');

    // $scndYear =  substr($next_trans_date, 0, 4);
    // $scndMonth = substr($next_trans_date, 5, 2);

    $lastMonth = new DateTime($data['end']);
    $lastMonth = $lastMonth->modify('first day of 0 month');
    $endDate = new DateTime($data['end']);
    //get first day of second to last month
    $first_day = $endDate->modify('first day of -1 month');
    $lastWhole = $first_day->format('Y-m-d');

    if(!$this->mm){
        if(substr($data['start'], -2) != '01'){
            if($params['index'] === 0)  $this->addFirstMidMonthLeaseChargeNow($data, $ttls, $lid, $account_id, $property_id, $item_id, $params);
            if($params['index'] !== 0) $this->addFirstMidMonthlyLeaseChargeMem($data, $ttls, $lid, $account_id, $property_id, $item_id);
        
        }else{
            if($params['index'] === 0) $this->addFirstMonthlLeaseChargeNow($data, $ttls, $lid, $account_id, $property_id, $item_id, $params);
            if($params['index'] !== 0) $this->addFirstMonthLeaseChargeMem($data, $ttls, $lid, $account_id, $property_id, $item_id);
        }
    }


    if($this->mm){
        //want mid month charges
        if($params['index'] === 0) $this->addFirstMonthlLeaseChargeNow($data, $ttls, $lid, $account_id, $property_id, $item_id, $params);
        if($params['index'] !== 0) $this->addFirstMonthLeaseChargeMem($data, $ttls, $lid, $account_id, $property_id, $item_id);
        //new DateTimeImmutable
        $endYear =  substr($data['end'], 0, 4);
        $endMonth = substr($data['end'], 5, 2);
        $endDay = substr($data['end'], -2);
        $stYear =  substr($data['start'], 0, 4);
        $stMonth = substr($data['start'], 5, 2);
        $stDate = new DateTimeImmutable($data['start']);
        $nextMonth = $stDate->modify('1 month');
        $trans_date = $nextMonth->format('Y-m-d');
        $ndDate = new DateTimeImmutable($data['end']);
        $potentialLastDayOfMonth = $stDate->modify('1 month -1 day');
        $pld = $potentialLastDayOfMonth->format('d');

        //working on detecting less than 2 month lease 
        // $date1 = new DateTime('2019-07-03');
        // $date2 = new DateTime('2019-06-04');
        // $date1->modify('+1 day');
        // $diff = $date1->diff($date2);
        // echo $diff->m;

        //will not be right if lease is more than 1 month and less than 2 whole month

            if($endDay == $pld && (($startMonth != $endMonth) || ($startYear != $endYear))){
                if (isset($ttls)){
                    foreach ($ttls as &$ttl) {
                        $this->memLeaseCharge($ttl, $lid, $trans_date, $data['end'], $trans_date, $account_id, $property_id, $item_id); 
                    } return; //is whole months albeit starting from mid month so done
                }
            }elseif($endDay == $pld && (($startMonth == $endMonth) && ($startYear == $endYear))){
               return; //nothing to do exactly one month lease
            }
            elseif($endDay != $pld /*&& (($startMonth == $endMonth) || ($startYear == $endYear))*/){
                $difference = abs($endDay - $pld);
                if($endDay < $pld){
                    $lmd = 30 - $difference;// last whole month is $endMonth - 1,$endYear = $endMonth - 1 == 12 ? $endYear -1 : $endYear;$endYear .'-'.$endMonth - 1 .'-'.$pld
                    // last month start is substr_replace(ast whole month is $endMonth, $pld + 1, -2) end is $data['end']
                    $lastWholeMonth = $ndDate->modify('-1 month');
                    $lastWholeMonthEndDate = $lastWholeMonth->format('Y-m-' . $pld);
                    foreach ($ttls as &$ttl) {
                        $this->memLeaseCharge($ttl, $lid, $trans_date, $lastWholeMonthEndDate, $trans_date, $account_id, $property_id, $item_id); 
                         //do last month lease charge
                         $lastTrans = substr_replace($lastWholeMonthEndDate, ($pld + 1), -2);
                         $ttl['amount'] = removeComma($ttl['amount']);
                         $amount = round((($ttl['amount'] / 30) * $lmd),2);
                        $this->memLeaseCharge($ttl, $lid, $lastTrans, $data['end'], $lastTrans, $account_id, $property_id, $item_id, $amount);
                    } 

                }else{
                    $lmd = $difference;// last whole month is substr_replace($data['end'], $pld, -2), last month start is substr_replace($data['end'], $pld +1, -2) end is $data['end']
                    $lastWholeMonthEndDate = substr_replace($data['end'], $pld, -2);
                    foreach ($ttls as &$ttl) {
                        $this->memLeaseCharge($ttl, $lid, $trans_date, $lastWholeMonthEndDate, $trans_date, $account_id, $property_id, $item_id); 
                        //do last month lease charge
                        $lastTrans = substr_replace($lastWholeMonthEndDate, ($pld + 1), -2);
                        $ttl['amount'] = removeComma($ttl['amount']);
                        $amount = round((($ttl['amount'] / 30) * $lmd),2);
                        $this->memLeaseCharge($ttl, $lid, $lastTrans, $data['end'], $lastTrans, $account_id, $property_id, $item_id, $amount);
                    } 
                }
            }
                
    }
    
    else{
        if($firstDayNextMonth < $lastMonth){
            $amount = 0;
            if (isset($ttls)){
                    foreach ($ttls as &$ttl) {
                        if($ttl['amount']>0){
                            $amount += $ttl['amount'];
                            $this->memLeaseCharge($ttl, $lid, $next_trans_date, $lastWhole, $next_trans_date, $account_id, $property_id, $item_id); //all months besides the first ans last
                        }
                        
                    }
            }
            if (!isset($ttls) or  $amount == 0){
                $transactionData = ['header' => ['transaction_date' => $data['start']]];
                $transactionData +=  ['transactions' => ['account_id' => $account_id, 'property_id' => $property_id, 'unit_id' => $data['unit_id'], 'lease_id' => $lid, 'item_id' => $item_id, 'credit' => $data['amount'], 'description' => 'description']];
                $specs = ['name' => 'Lease Charge', 'transaction_type' => 6, 'type_id' => 2, 'type_item_id' => $lid, 'frequency' => 1, 'start_date' =>$data['start'], 'end_date' => $lastWhole , 'amount' => $data['amount'], 'next_trans_date' => ($next_trans_date ? $next_trans_date : ''), 'auto' => 1];
                $this->addTransaction($specs, $transactionData);
            }
        }
    }

}



public function addFirstMidMonthLeaseChargeNow($data, $ttls, $lid, $account_id, $property_id, $item_id, $params)
{

    $endYear =  substr($data['end'], 0, 4);
    $endMonth = substr($data['end'], 5, 2);
    $header = ['transaction_date' => $data['start']];
    $year =  substr($data['start'], 0, 4);
    $month = substr($data['start'], 5, 2);
    $day = substr($data['start'], -2);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $daysPayingFor = ($daysInMonth - $day) + 1;//need to check that last day is end of month
    $amount = 0;

    if (isset($ttls)){


        foreach ($ttls as &$ttl) {
            if($ttl['amount']>0){
                    $amount += $ttl['amount'];
                    if($params['add'] === 1){
                        $ttl['amount'] = removeComma($ttl['amount']);
                        $charge = round(($ttl['amount'] / $daysInMonth) * $daysPayingFor, 2);
                        $transaction = ['profile_id' => $ttl['profile_id'], 'account_id' => $account_id, 'property_id' => $property_id, 'unit_id' => $ttl['unit_id'], 'lease_id' => $lid, 'item_id' => $item_id, 'credit' => $charge, 'description' => 'lease charge for part of month'];
                        $this->load->model('charges_model');
                        $this->charges_model->addCharge($header, $transaction);
                    }
                    if(($month != $endMonth) || ($year != $endYear)){
                        $this->finalMonthLeaseCharge($data, $ttl, $lid, $account_id, $property_id, $item_id, $charge);
                    }
            }
        }
    }
    if (!isset($ttls) or  $amount == 0){
        if($params['add'] === 1){
            $charge = ($data['amount'] / $daysInMonth) * $daysPayingFor;
            $transaction = ['account_id' => $account_id, 'property_id' => $property_id, 'unit_id' => $data['unit_id'], 'lease_id' => $lid, 'item_id' => $item_id, 'credit' => $charge, 'description' => 'lease charge for part of month'];
            $this->load->model('charges_model');
            $this->charges_model->addCharge($header, $transaction);
        }
        if(($month != $endMonth) || ($year != $endYear)){
            $this->finalMonthLeaseCharge($data, $ttls, $lid, $account_id, $property_id, $item_id, $charge);
        }
    }

}

    public function addFirstMonthlLeaseChargeNow($data, $ttls, $lid, $account_id, $property_id, $item_id, $params)
    {
        $header = ['transaction_date' => $data['start']];
        $startYear =  substr($data['start'], 0, 4);
        $startMonth = substr($data['start'], 5, 2);
        $endYear =  substr($data['end'], 0, 4);
        $endMonth = substr($data['end'], 5, 2);
        $amount = 0;
       //need to check that last day is last day in month
        if (isset($ttls)){
            foreach ($ttls as &$ttl) {
                if($ttl['amount'] > 0){
                    $amount += $ttl['amount'];
                    if($params['add'] === 1){
                        $ttl['amount'] = removeComma($ttl['amount']);
                        $transaction = ['profile_id' => $ttl['profile_id'], 'account_id' => $account_id, 'property_id' => $property_id, 'unit_id' => $ttl['unit_id'], 'lease_id' => $lid, 'item_id' => $item_id, 'credit' => $ttl['amount'], 'description' => 'lease charge for first month'];
                        $this->load->model('charges_model');
                        $this->charges_model->addCharge($header, $transaction);
                    }
                    if(!$this->mm){
                        if(($startMonth != $endMonth) || ($startYear != $endYear)){
                            $this->finalMonthLeaseCharge($data, $ttl, $lid, $account_id, $property_id, $item_id);
                        }
                    }
                }
                
            }
        }
    if (!isset($ttls) or  $amount == 0){
        if($params['add'] === 1){
            $transaction = ['account_id' => $account_id, 'property_id' => $property_id, 'unit_id' => $data['unit_id'], 'lease_id' => $lid, 'item_id' => $item_id, 'credit' => $data['amount'], 'description' => 'lease charge for first month'];
            $this->load->model('charges_model');
            $this->charges_model->addCharge($header, $transaction);
        }
        if(($startMonth != $endMonth) || ($startYear != $endYear)){
            $this->finalMonthLeaseCharge($data, $ttls, $lid, $account_id, $property_id, $item_id);
        }
        }
    }

public function addFirstMidMonthlyLeaseChargeMem($data, $ttls, $lid, $account_id, $property_id, $item_id)
{
    //$header = ['transaction_date' => $data['start']];
    $endYear =  substr($data['end'], 0, 4);
    $endMonth = substr($data['end'], 5, 2);
    $year =  substr($data['start'], 0, 4);
    $month = substr($data['start'], 5, 2);
    $day = substr($data['start'], -2);
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);//need to check if not till end of month
    $daysPayingFor = ($daysInMonth - $day) + 1;
    $amount = 0;

    if (isset($ttls)){


        foreach ($ttls as &$ttl) {
            if($ttl['amount']>0){
                $amount += $ttl['amount'];
                $ttl['amount'] = removeComma($ttl['amount']);
                $charge = round(($ttl['amount'] / $daysInMonth) * $daysPayingFor, 2);
                $this->memLeaseCharge($ttl, $lid, $data['start'], $data['start'], $next_trans_date, $account_id, $property_id, $item_id, $charge);

                // $transaction = ['profile_id' => $ttl['profile_id'], 'account_id' => $account_id, 'property_id' => $property_id, 'unit_id' => $ttl['unit_id'], 'lease_id' => $lid, 'item_id' => $item_id, 'credit' => $charge, 'description' => 'lease charge for part of month'];
                // $this->load->model('charges_model');
                // $this->charges_model->addCharge($header, $transaction);
                if(($month != $endMonth) || ($year != $endYear)){
                    $this->finalMonthLeaseCharge($data, $ttl, $lid, $account_id, $property_id, $item_id, $charge);
                }
            }
        }
    }
// else{
//     $charge = ($data['amount'] / $daysInMonth) * $daysPayingFor;
//             $transaction = ['account_id' => $account_id, 'property_id' => $property_id, 'unit_id' => xxxxxxx, 'lease_id' => $lid, 'item_id' => $item_id, 'credit' => $charge, 'description' => 'lease charge for part of month'];
//             $this->load->model('charges_model');
//             $this->charges_model->addCharge($header, $transaction);

//             $this->finalMonthLeaseCharge($data, $ttls, $lid, $account_id, $property_id, $item_id, $charge); // added charge now
//     }

}

public function addFirstMonthLeaseChargeMem($data, $ttls, $lid, $account_id, $property_id, $item_id)
{
    //$header = ['transaction_date' => $data['start']];
    $startYear =  substr($data['start'], 0, 4);
    $startMonth = substr($data['start'], 5, 2);
    $endYear =  substr($data['end'], 0, 4);
    $endMonth = substr($data['end'], 5, 2);
    $amount = 0;
    //need to check if not till end of month
    if (isset($ttls)){


        foreach ($ttls as &$ttl) {
            if($ttl['amount']>0){
                $amount += $ttl['amount'];
                $ttl['amount'] = removeComma($ttl['amount']);
                $this->memLeaseCharge($ttl, $lid, $data['start'], $data['start'], $next_trans_date, $account_id, $property_id, $item_id);
                if(!$this->mm){
                    if(($startMonth != $endMonth) || ($startYear != $endYear)){
                        $this->finalMonthLeaseCharge($data, $ttl, $lid, $account_id, $property_id, $item_id, $charge);
                    }
                }
            }
        }
    }
// else{
//     $charge = ($data['amount'] / $daysInMonth) * $daysPayingFor;
//             $transaction = ['account_id' => $account_id, 'property_id' => $property_id, 'unit_id' => xxxxxxx, 'lease_id' => $lid, 'item_id' => $item_id, 'credit' => $charge, 'description' => 'lease charge for part of month'];
//             $this->load->model('charges_model');
//             $this->charges_model->addCharge($header, $transaction);

//             $this->finalMonthLeaseCharge($data, $ttls, $lid, $account_id, $property_id, $item_id, $charge); // added charge now
//     }

}


public function finalMonthLeaseCharge($data, $ttl, $lid, $account_id, $property_id, $item_id, $charge=null)
{
        //get first day of last  month for last month memorized trans
        $endDate = new DateTime($data['end']);
        $firstDayFinalMonth = $endDate->format('Y-m-01');
        ///getting info on final month to check if is whole month
        $finalYear =  substr($data['end'], 0, 4);
        $finalMonth = substr($data['end'], 5, 2);
        $finalDay = substr($data['end'], -2);
        $lengthOfFinalMonth = cal_days_in_month(CAL_GREGORIAN, $finalMonth, $finalYear);//need to check if final day is last day of month - checking
        $daysPayingFor = $finalDay;// - 1;
        $amount = 0;


        if (isset($ttl)  && $ttl['amount']>0){
            $amount += $ttl['amount'];

            $ttl['amount'] = removeComma($ttl['amount']);
                ////memorized trans for last (partial) month
                //$finalMonthCharge = $lengthOfFinalMonth == $finalDay ? $ttl['amount'] : ($ttl['amount'] / $lengthOfFinalMonth) * $daysPayingFor;
            $finalMonthCharge = ($lengthOfFinalMonth == $finalDay) ? $ttl['amount'] : ($charge ? ($ttl['amount'] - $charge) : round(($ttl['amount'] / $lengthOfFinalMonth) * $daysPayingFor,2));
                // if($lengthOfFinalMonth == $finalDay){
                //     $finalMonthCharge = $ttl['amount'];
                // }elseif($charge){
                //     $finalMonthCharge = $ttl['amount'] - $charge;
                // }else{
                //     $finalMonthCharge = ($ttl['amount'] / $lengthOfFinalMonth) * $daysPayingFor;
                // }

            $this->memLeaseCharge($ttl, $lid, $firstDayFinalMonth, $data['end'], $firstDayFinalMonth, $account_id, $property_id, $item_id, $finalMonthCharge);
        }

        if (!isset($ttl) or  $amount == 0){
                    $finalMonthCharge = ($lengthOfFinalMonth == $finalDay) ? $data['amount'] : ($charge ? ($data['amount'] - $charge) : round(($data['amount'] / $lengthOfFinalMonth) * $daysPayingFor,2));
                    

                    $transactionData = [];
                    $transactionData = ['header' => ['transaction_date' => $firstDayFinalMonth]];
                    $transactionData +=  ['transactions' => ['account_id' => $account_id, 'property_id' => $property_id, 'unit_id' => $ttl['unit_id'], 'lease_id' => $lid, 'item_id' => $item_id, 'credit' => $finalMonthCharge, 'description' => 'last month\'s charge']];
                    $specs = ['name' => 'Last Month\'s Lease Charge', 'transaction_type' => 6, 'type_id' => 2, 'type_item_id' => $lid, 'frequency' => 1, 'start_date' =>$firstDayFinalMonth, 'end_date' =>$data['end'], 'amount' => $finalMonthCharge, 'next_trans_date' => $firstDayFinalMonth, 'property_id' => $property_id, 'auto' => 1];
                    $this->addTransaction($specs, $transactionData);
        }
        

}

public function addPetDeposit($data, $ttls, $lid, $property_id)
{
    $header = ['transaction_date' => $data['start']];

    if (isset($ttls)){

        foreach ($ttls as &$ttl) {
            if($ttl['pets'] && $ttl['pet_deposit']){
                $transaction = ['profile_id' => $ttl['profile_id'], 'account_id' => $this->sd, 'property_id' => $property_id, 'unit_id' => $ttl['unit_id'], 'lease_id' => $lid, 'item_id' => $this->sdi, 'credit' => $ttl['pet_deposit'], 'description' => 'Pet Deposit'];
                $this->load->model('charges_model');
                $this->charges_model->addCharge($header, $transaction);
            }
        }
    }

}

public function addSecurityDeposit($data, $ttls, $lid, $property_id)
{
    $header = ['transaction_date' => $data['start']];
    $amount = 0;
    if (isset($ttls)){
        foreach ($ttls as &$ttl) {
            if($ttl['deposit'] > 0){
                $amount += $ttl['deposit'];
                $transaction = ['profile_id' => $ttl['profile_id'], 'account_id' => $this->sd, 'property_id' => $property_id, 'unit_id' => $ttl['unit_id'], 'lease_id' => $lid, 'item_id' => $this->sdi, 'credit' => $ttl['deposit'], 'description' => 'Security Deposit'];
                $this->load->model('charges_model');
                $this->charges_model->addCharge($header, $transaction);
            }
        }
    }
    if (!isset($ttls) or  $amount == 0){
        if($data['deposit'] > 0){
            $transaction = ['account_id' => $this->sd, 'property_id' => $property_id, 'unit_id' => $data['unit_id'], 'lease_id' => $lid, 'item_id' => $this->sdi, 'credit' => $data['deposit'], 'description' => 'Security Deposit'];
            $this->load->model('charges_model');
            $this->charges_model->addCharge($header, $transaction);
        }
    }

}

public function addLmr($data, $ttls, $lid, $property_id)
{
    $header = ['transaction_date' => $data['start']];
    $amount = 0;

    if (isset($ttls)){
        foreach ($ttls as &$ttl) {
            if($ttl['last_month'] >0){
                $amount += $ttl['last_month'];
                $transaction = ['profile_id' => $ttl['profile_id'], 'account_id' => $this->lmr, 'property_id' => $property_id, 'unit_id' => $ttl['unit_id'], 'lease_id' => $lid, 'item_id' => $this->lmri, 'credit' => $ttl['last_month'], 'description' => 'Last Month\'s Rent'];
                $this->load->model('charges_model');
                $this->charges_model->addCharge($header, $transaction);
            }
        }
    }
    if (!isset($ttls) or  $amount == 0){
        if($data['last_month'] > 0){
            $transaction = ['account_id' => $this->lmr, 'property_id' => $property_id, 'unit_id' => $data['unit_id'], 'lease_id' => $lid, 'item_id' => $this->lmri, 'credit' => $data['last_month'], 'description' => 'Last Month\'s Rent'];
            $this->load->model('charges_model');
            $this->charges_model->addCharge($header, $transaction);
        }
    }
}

    public function memInsTrans($policy, $ipid, $startDate, $transaction_date, $amount)
    {
        $transactionData = [];
        $transactionData = ['header' => ['transaction_date' => $transaction_date, 'transaction_ref' => "insurance adjustment"]];
        $asset_account = $this->site->settings->ins_prepaid;
        $expense_account = $this->site->settings->ins_exp;
        $transactionData['transactions'][0] = ['account_id' => $asset_account , 'property_id' => $policy['property_id'], 'unit_id' => $policy['unit'], 'credit' => $amount, 'debit' => 0];
        $transactionData['transactions'][1] =  ['account_id' => $expense_account, 'property_id' => $policy['property_id'], 'unit_id' => $policy['unit'], 'debit' => $amount, 'credit' => 0];
        $specs = ['property_id' => $policy['property_id'], 'name' => 'Insurance disbursement', 'transaction_type' => 1, 'type_id' => 8, 'type_item_id' => $ipid, 'frequency' => 1, 'start_date' =>$startDate, 'end_date' =>$transaction_date, 'amount' => $amount, 'next_trans_date' => $transaction_date,  'auto' => 1];
             $this->addTransaction($specs, $transactionData);
    }

    public function payInsCharge($policy, $ipid)
    {
        ///first month
        $startDate = new DateTime($policy['start_date']);
        $fstartDate = $startDate->format('Y-m-d');
        
        $endDate = new DateTime($policy['end_date']);
        $immutableEndDate = new DateTimeImmutable($policy['end_date']);
        
        $totalDays = $endDate->diff($startDate)->format("%a") + 1;
        $perDay = round($policy['price'] / $totalDays, 2);

        $year =  substr($fstartDate, 0, 4);
        $month = substr($fstartDate, 5, 2);
        $day = substr($fstartDate, -2);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $endOfMonth = substr_replace($fstartDate, $daysInMonth, -2);
        $daysPayingFor = ($daysInMonth - $day) + 1;
        $amount = $perDay * $daysPayingFor;

        $this->memInsTrans($policy, $ipid, $fstartDate, $endOfMonth, $amount);

        $this->midInsCharge($policy, $ipid, $startDate, $endDate, $perDay, $amount, $immutableEndDate);
    }

    public function midInsCharge($policy, $ipid, $startDate, $endDate, $perDay, $charge, $immutableEndDate)
    {   
        //middle whole months
        $charge = $charge;
        $firstWholeMonth = $startDate->modify('first day of 1 month');
        $fFirstWholeMonth = $firstWholeMonth->format('Y-m-d');
        $lastWholeMonth = $endDate->modify('first day of -1 month');
        $flastWholeMonth = $lastWholeMonth->format('Y-m-d');
        $totalWholeMonths = $lastWholeMonth->diff($firstWholeMonth)->format("%m") + 1;
            
        $monthsArray = [$fFirstWholeMonth];
        for($i=0;$i<$totalWholeMonths-1;$i++){
            $fmonth = $firstWholeMonth->modify('+1 month')->format('Y-m-d');
            //$month = $start_date->format('Y-m-d');
            $monthsArray[] = $fmonth;
        }
           
       //could really do all this in first foreach don't know if it's a major optimization
       foreach($monthsArray as $date){
            $year =  substr($date, 0, 4);
            $month = substr($date, 5, 2);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $endOfMonth = substr_replace($date, $daysInMonth, -2);
            $amount = $perDay * $daysInMonth;

            $this->memInsTrans($policy, $ipid, $date, $endOfMonth, $amount);

            $charge += $amount;
      }
      
       $this->finalInsCharge($policy, $ipid,  $immutableEndDate, $charge);
    }

    public function finalInsCharge($policy, $ipid, $endDate, $charge)
    {   
        //final month
        $lastDayFinalMonth = $endDate->format('Y-m-d');
        $firstDayFinalMonth = $endDate->format('Y-m-01');
        $finalYear =  substr($lastDayFinalMonth, 0, 4);
        $finalMonth = substr($lastDayFinalMonth, 5, 2);
        $finalDay = substr($lastDayFinalMonth, -2);
        
        $amount = $policy['price'] - $charge;

        $this->memInsTrans($policy, $ipid, $firstDayFinalMonth, $lastDayFinalMonth, $amount);
    }

    public function taxes($tax)
    {
        $startDate = new DateTime($tax['start_date']);
        $fstartDate = $startDate->format('Y-m-d');
        $this->db->select('f.interval_unit, f.number, pt.property_id, pt.payee, CONCAT_WS("/",pt.borough,pt.block,pt.lot) AS bbl');
        $this->db->from('frequencies f');
        $this->db->join('property_tax pt','f.id = pt.frequency');
        $this->db->where('pt.id',$tax['id']);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $interval_unit = $q->row()->interval_unit;
            $number = $q->row()->number;
            $property = $q->row()->property_id;
            $payee = $q->row()->payee;
            $BBL = $q->row()->bbl;
        }

        $endDate = new DateTime($tax['start_date']);
        $endDate->modify('+' . $number . $interval_unit . '-1 day');
        $fendDate = $endDate->format('Y-m-d');

        $totalDays = $endDate->diff($startDate)->format("%a") + 1;
        $perDay = round($tax['amount'] / $totalDays, 2);


        $secondTolastMonth = $endDate->modify('first day of -1 month');
        //$fsecondTolastMonth = $secondTolastMonth->format('Y-m-d');
        $totalMonths = $secondTolastMonth->diff($startDate)->format("%m") + 1;
            
        $monthsArray = [$fstartDate];
        for($i=0;$i<$totalMonths-1;$i++){
            $fmonth = $startDate->modify('+1 month')->format('Y-m-d');
            //$month = $start_date->format('Y-m-d');
            $monthsArray[] = $fmonth;
        }

        //could really do all this in first foreach don't know if it's a major optimization
       foreach($monthsArray as $date){
            $year =  substr($date, 0, 4);
            $month = substr($date, 5, 2);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $endOfMonth = substr_replace($date, $daysInMonth, -2);
            $amount = $perDay * $daysInMonth;

            //$this->memInsTrans($tax, $ptid, $date, $endOfMonth, $amount);

            $transactionData = [];
             $transactionData = ['header' => ['transaction_date' => $date]];
             /*$asset_account*/
             $transactionData['transactions'][0] = ['account_id' => $this->tax_prepaid , 'property_id' => $property, 'credit' => $amount, 'debit' => 0, 'description' => 'To Allocate Taxes by Month'];
             /*$expense_account*/
             $transactionData['transactions'][1] =  ['account_id' => $this->tax_expense, 'property_id' => $property, 'debit' => $amount, 'credit' => 0, 'description' => 'To Allocate Taxes by Month'];
             $specs = ['name' => 'Tax Adj', 'transaction_type' => 1, 'type_id' => 10, 'type_item_id' => $tax['id'], 'frequency' => 1, 'start_date' =>$date, 'end_date' =>$endOfMonth, 'amount' => $amount, 'next_trans_date' => $date,  'auto' => 1, 'property_id' => $property];
             $this->addTransaction($specs, $transactionData);
             $charge += $amount;
       }
             ///last month
            $year =  substr($fendDate, 0, 4);
            $month = substr($fendDate, 5, 2);
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            //$endOfMonth = substr_replace($fendDate, $daysInMonth, -2);
            $begOfMonth = substr_replace($fendDate, '01', -2);
            $amount = $tax['amount'] - $charge;

            //$this->memInsTrans($tax, $ptid, $begOfMonth, $fendDate, $amount);

             $transactionData = [];
             $transactionData = ['header' => ['transaction_date' => $begOfMonth]];
             /*$asset_account*/
             $transactionData['transactions'][0] = ['account_id' => $this->tax_prepaid , 'property_id' => $property, 'unit_id' => $tax['unit'], 'credit' => $amount, 'debit' => 0];
             /*$expense_account*/
             $transactionData['transactions'][1] =  ['account_id' => $this->tax_expense, 'property_id' => $property, 'unit_id' => $tax['unit'], 'debit' => $amount, 'credit' => 0];
             $specs = ['name' => 'Tax Adj', 'transaction_type' => 1, 'type_id' => 10, 'type_item_id' => $tax['id'], 'frequency' => 1, 'start_date' =>$begOfMonth, 'end_date' =>$fendDate, 'amount' => $amount, 'next_trans_date' => $begOfMonth,  'auto' => 1, 'property_id' => $property];
             $this->addTransaction($specs, $transactionData);
             $this->load->model('checks_model');
             $header = ['transaction_date' => $tax['last_pay_date'], 'memo' => 'BBL ' . $BBL];// 'transaction_ref' => 'next_check_number
             $headerTransaction = ['account_id' => $tax['payment_acct'], 'property_id' => $property, 'profile_id' => $payee, 'credit' => $tax['amount'], 'description' => 'BBL ' . $BBL];
             $transactions[0] = ['account_id' => $this->tax_prepaid, 'property_id' => $property, 'debit' => $tax['amount'], 'description' => 'BBL ' . $BBL];
             $this->checks_model->addCheck($header,$headerTransaction, $transactions, 0);
    }

    public function managementFees($management, $mfid, $pid, $customer, $propertyName, $update=null)
    {
        $this->db->select('id');
        $this->db->from('properties'); 
        $this->db->where('vendor_profile_id', $management['vendor']);
        $q = $this->db->get(); 
        if ($q->num_rows() > 0) {
            $mPropId = $q->row()->id;
        }

        // $this->db->select('customer_profile_id');
        // $this->db->from('properties'); 
        // $this->db->where('id', $pid);
        // $q = $this->db->get(); 
        // if ($q->num_rows() > 0) {
        //     $customer = $q->row()->customer_profile_id;
        // }

        $this->db->select('acct_income, acct_expense');
            $this->db->from('items');
            $this->db->where('id', $management['item_id']);
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                $incomeAcct = $q->row()->acct_income;
                $expenseAcct = $q->row()->acct_expense;
            }

        $transactionData = [];
        $transactionData = ['header' => ['transaction_date' => $management['start_date']]];
        $transactionData +=  ['headerTransaction' => ['account_id' => $this->site->settings->accounts_payable, 'profile_id' => $management['vendor'], 'property_id' => $pid, 'unit_id' => $management['unit_id'], 'credit' => $management['amount'], 'description' => 'description']];
        $transactionData['transactions'][0] = ['account_id' => $expenseAcct, 'property_id' => $pid,'profile_id' => $management['vendor'],  'unit_id' => $management['unit_id'], 'debit' => $management['amount'], 'description' => ''];
        $transactionData['transactions'][1] = ['account_id' => $this->site->settings->accounts_receivable, 'property_id' => $mPropId, 'description' => '', 'profile_id' => $customer, 'unit_id' => '', 'debit' => $management['amount']];
        $transactionData['transactions'][2] = ['account_id' => $incomeAcct, 'property_id' => $mPropId, 'unit_id' => $detail['unit_id'], 'description' => '', 'profile_id' => $customer, 'debit' => $management['amount'] * -1];
        
        $specs = ['name' => 'Management fees for' . $propertyName, 'transaction_type' => 2, 'type_id' => 15, 'type_item_id' => $mfid, 'frequency' => $management['frequency'], 'start_date' =>$management['start_date'], 'end_date' => $management['end_date'], 'amount' => $management['amount'], 'next_trans_date' => '',  'auto' => 1, 'property_id' => $pid];
        if($update == NULL){$this->addTransaction($specs, $transactionData);} 
        else {$this->editTransaction($specs, $transactionData, 15, $mfid);}
        
    }

    


    public function getHeader($id)
    {   
        $this->db->select('th.id, th.transaction_type, th.last_mod_date, th.last_mod_by, CONCAT_WS(" ",p.first_name,p.last_name) AS user,  th.transaction_ref, th.transaction_date AS date, th.memo');
        $this->db->from('transaction_header th'); 
        $this->db->join('profiles p', 'th.last_mod_by = p.id', 'left');
        $this->db->where('th.id', $id);
        $this->db->limit(1); 
        
        $q = $this->db->get(); 
        //$q = $this->db->get_where('transaction_header', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getTransactions($id)
    {   
        $q = $this->db->order_by('id', 'asc')->get_where('transactions', array('trans_id' => $id));
        // $this->db->select('account_id,property_id,profile_id, unit_id," ",area_code) AS city');
        // $this->db->from('transactions');
        // $this->db->join('profiles p','transactions.profile_id = p.id','left');
        // $this->db->where(array('trans_id' => $id));
        // $this->db->order_by("id", "asc");
        //$q = $this->db->get();

        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if ($addtype == true) $row->type = 'check_type';
                $row->units = $this->getPropertyUnits($row->property_id);
                $data[] = $row;
            }
            return $data;
        }
        return null;
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

    public function getAllAccounts()
    {
        $this->db->select('id, name, accno, all_props, parent_id');
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

    public function getAllBanks()
    {
        $this->db->select('a.id, a.name, b.account_number, b.routing, a.all_props');
        $this->db->from('accounts a');
        $this->db->join('banks b','a.id = b.account_id','left');
        $this->db->where(array('account_types_id'=> 1));        
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

    function getmemorizedTransactions($type=null,$typeId=null){
        $this->db->select('mt.id, mt.name,  tt.name as Type, mt.next_trans_date, f.name as frequency, if(mt.auto=1,"yes", "no") as auto, p.name as property, mt.amount as amount ');//, pa.property_id
        $this->db->from('memorized_transactions mt');
        $this->db->join('properties p', 'mt.property_id = p.id', 'left');
        $this->db->join('transaction_type tt', 'mt.transaction_type = tt.id', 'left');
        $this->db->join('frequencies f','f.id = mt.frequency');
        $this->db->where('mt.next_trans_date > "2007-01-01"');
        $this->db->where('mt.end_date >=', date());
        $this->db->where('mt.transaction_type !=6 or mt.auto = 0');

        if($type && $typeId){
            $this->db->where('mt.type_id', $type);
            $this->db->where('mt.type_item_id', $typeId);
        }


        

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