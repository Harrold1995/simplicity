<?php defined('BASEPATH') OR exit('No direct script access allowed');

class TransactionsImport_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ar = $this->site->settings->accounts_receivable;
        $this->uf = $this->site->settings->undeposited_funds;
        $this->scd = $this->site->settings->sort_charge_date;
        $this->da = $this->site->settings->date_asc;

    }

    
    
    public function getDepositTo()
    {
        $this->db->select('id, name, accno');
        $this->db->from('accounts');
        $this->db->where('account_types_id', 1);
        $this->db->or_where('id', $this->site->settings->undeposited_funds);
        $this->db->where('active', 1);
        $this->db->ORDER_BY('name');
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;

    }

    public function getProfileId($th_id)
    {
        $this->db->select('profile_id');
        $this->db->from('transactions');
        $this->db->where('trans_id', $th_id);
        $this->db->limit(1);
        
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->profile_id;
        }
        return null;
    }


    public function getProperties($profile_id = null)
    {
        if($profile_id){
            $this->db->distinct();
            $this->db->select('p.id, p.name');
            $this->db->from('leases_profiles lp');
            $this->db->join('units u', 'lp.unit_id = u.id AND lp.profile_id=' . $profile_id);
            $this->db->join('properties p', 'u.property_id = p.id');
            $this->db->where('p.active', 1);
            $this->db->ORDER_BY('p.id ASC');
        }else{
            $this->db->select('id, name');
            $this->db->from('properties');
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

    public function getLeases()
    {
        $this->db->select('l.id, l.unit_id, CONCAT(l.start," - ", l.end) AS name, u.property_id as property_id, p.name as property, u.name as unit, lp.profile_id');
        $this->db->from('leases l');
        $this->db->join('units u', 'l.unit_id = u.id');
        $this->db->join('properties p', 'u.property_id = p.id');
        $this->db->join('leases_profiles lp', 'l.id = lp.lease_id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }
    
    public function getTransactions($lease_id, $profile_id)//took out  $profile_id
    {   
        // if($property_id == null){
        //     $this->db->select('p.id');
        //     $this->db->from('leases_profiles lp');
        //     $this->db->join('units u', 'lp.unit_id = u.id AND lp.profile_id=' . $profile_id);
        //     $this->db->join('properties p', 'u.property_id = p.id');
        //     $this->db->where('p.active', 1);
        //     $this->db->ORDER_BY('p.id ASC');
        //     $this->db->limit(1);
            
        //     $q = $this->db->get();
        //     if ($q->num_rows() > 0) {
        //         $property_id = $q->row()->id;
        //     }
        // }

        //$property = $property_id ?  ' AND t.property_id ='. $property_id : '';
       
        $this->db->select('t.id, t.profile_id, t.lease_id, t.property_id, t.description, th.id as th_id, th.transaction_type, th.transaction_date AS date, b.due_date, (t.debit - t.credit) AS amount, ((t.debit - t.credit) - IFNULL(transum.amounts,0)) AS open_balance, 0 AS received_payment');
        $this->db->from('transactions t'); 
        //$this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar . ' AND t.lease_id ='. $lease_id . ' AND (t.profile_id ='. $profile_id . ' OR t.profile_id IS NULL OR t.profile_id = 0)');
        $this->db->join('transaction_header th', 't.trans_id = th.id AND t.account_id =' . $this->ar . ' AND t.lease_id ='. $lease_id . ' AND t.profile_id ='. $profile_id);
        $this->db->join('bills b', 't.trans_id = b.trans_id', 'left');
        $this->db->join('(SELECT trans_id, SUM(amount) AS amounts
        FROM(SELECT transaction_id_a AS trans_id, 0- amount AS amount
        FROM applied_payments
        UNION ALL
        SELECT transaction_id_b AS trans_id, amount 
        FROM applied_payments) trans
        GROUP BY trans_id) transum','t.id = transum.trans_id','left');
        $this->db->where('((t.debit - t.credit) - IFNULL(transum.amounts,0)) !=',  0);
        
        $dateASC = $this->da ? ' ASC' : ' DESC';
        $sort_by = $this->scd ? 't.item_id ASC, th.transaction_date' . $dateASC : 'th.transaction_date' . $dateASC . ', t.item_id ASC';
        $this->db->ORDER_BY($sort_by);
        // $sql = $this->db->get_compiled_select();
        // echo $sql;
        // return;
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $data = $q->result_array();
            return $data;
        }
        return null;
       
    }
    public function getHeaderEdit($th_id)
    {
        $this->db->select('th.id, th.memo, th.transaction_date AS date, th.transaction_ref, CONCAT(DATE_FORMAT(th.last_mod_date, "%m/%d/%Y")," ",TIME_FORMAT(th.last_mod_date, "%r")) AS modified, th.last_mod_by, t.profile_id, CONCAT_WS(" ",p.first_name,p.last_name) AS user, t.credit, cp.trans_id AS cp_tid, cp.payment_method, cp.deposit_on, dt.account_id AS deposit_to, t.deposit_id');
        $this->db->from('transaction_header th'); 
        $this->db->join('transactions t', 'th.id = t.trans_id AND t.trans_id ='. $th_id . ' AND t.account_id ='. $this->ar);
        $this->db->join('transactions dt', 'th.id = dt.trans_id AND dt.trans_id ='. $th_id . ' AND dt.account_id !='. $this->ar);
        $this->db->join('customer_payments cp', 'cp.trans_id = t.id','left');
        $this->db->join('users u', 'th.last_mod_by = u.id','left'); 
        $this->db->join('profiles p', 'u.profile_id = p.id','left'); 
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;

    }


    public function applyPayments($header, $customerPayments, $applied_payments, $accounts_receivable, $deposit_to, $system = null)
    {  
        $this->db->trans_start();

        $trans_id = isset($system) ? $this->addHeader($header, 5, 1):$this->addHeader($header, 5);
        $accounts_receivable['trans_id'] =  $trans_id;
        $deposit_to['trans_id'] =  $trans_id;
        $this->db->insert('transactions',$accounts_receivable);
        $transaction_id_a = $this->db->insert_id();
        $this->db->insert('transactions',$deposit_to);

        $customerPayments['trans_id'] = $transaction_id_a;
        $this->db->insert('customer_payments', $customerPayments);
        
        if($applied_payments) {if(isset($system)){$this->applyPaymentsAdd($applied_payments, $transaction_id_a,1);}else{$this->applyPaymentsAdd($applied_payments, $transaction_id_a);}}

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
        {
             return false;
        }

        return true;
        
    }


    public function enterPortalPayments($data, $ach_response){
        $openBalances = $this->getTransactions($data['lease_id'], $data['profile_id']);
                $applied_payments = [];
                //clone amount for transactions 

                $amount = $data['amount'];
                $date = date("Y-m-d");
                $balances = array_column($openBalances, 'open_balance');
                $key = array_search($amount, $balances);
                if($key){
                    $applied_payments[] = ['transaction_id_b' => $openBalances[$key]['id'], 'amount' => $amount];
                }else{
                    foreach($openBalances as $ob){
                        if($amount == 0){
                            break;
                        }
                        //if has credit don't need to pay
                        if($ob['open_balance'] > 0){
                            $apply = $amount > $ob['open_balance'] ? $ob['open_balance'] : $amount;
                           // do payment
            
                            $applied_payments[] = ['transaction_id_b' => $ob['id'], 'amount' => $apply];
                            $amount = (($amount - $ob['open_balance']) > 0) ? ($amount - $ob['open_balance']) : 0;
    
                        }else{
                            continue;
                        }
    
                    }//end of open balance for each
                }
                $propInfo = $this->getPropBylease($data['lease_id']);
                $property = $propInfo->property_id;
                $depositTo = $propInfo->default_bank; 
                $unit = $propInfo->unit_id; 
                //$ach_response = json_decode($ach_response);
                $ref = 'PP-'.$ach_response->GatewayRefNum; 
                $amount = $data['amount'];
                $accounts_receivable = ['account_id' => $this->ar, 'lease_id' =>  $data['lease_id'], 'property_id' => $property, 'profile_id' => $data['profile_id'], 'unit_id' => $unit,  'credit' => $amount];
                $deposit_to =  ['account_id' => $depositTo, 'lease_id' =>  $data['lease_id'], 'property_id' => $property, 'profile_id' => $data['profile_id'], 'unit_id' => $unit, 'debit' => $amount];
                $header = ['transaction_date' => $date,'transaction_ref' => $ref, 'memo' => 'Tenant Portal Payment'];
                $customerPayments = ['payment_method' => 6];
                $applied_payments = $applied_payments ? $applied_payments : NULL;
                $entered = $this->applyPayments($header, $customerPayments, $applied_payments, $accounts_receivable, $deposit_to, 1);
                if($entered) {
                    // send email
                    if($this->site->settings->email_payment_notices == 1){
                        $data = (object)$header;
                        $data->data = (object)$accounts_receivable;
                            $this->sendEmail($data, 'p');               
                    }
                    array_push($array,array('msgInfo' => $t['id'],'type' => 'success', 'message' => 'Transaction successfully added.'));
                }else{
                    array_push($array,array('msgInfo' => $t['id'],'type' => 'danger', 'message' => 'something went wrong while data was being entered')); 
                }
    }

    public function getPropBylease($lease_id){

        $this->db->select('property_id, unit_id, default_bank');
        $this->db->from('leases l'); 
        $this->db->join('units u', 'l.unit_id = u.id');
        $this->db->join('properties p', 'u.property_id = p.id');
        $this->db->limit(1);

        $q = $this->db->get();
        $row = $q->row();
        return $row;
          
    }

    public function processPayments($transactions, $date, $importType =null)
    { 
        
        $array = [];
        
        foreach($transactions as $t){
            $str = substr_replace($t['profile'] ,"", -2);
            $this->db->select('l.id AS lease, p.id AS property, prof.id AS profile, u.id AS unit, default_bank');
            $this->db->from('profiles prof'); 
            $this->db->join('leases_profiles lp', 'prof.id = lp.profile_id');
            $this->db->join('leases l', 'lp.lease_id = l.id'); 
            $this->db->join('units u', 'l.unit_id = u.id');
            $this->db->join('properties p', 'u.property_id = p.id');

            if ( isset($t['property_id'])){
                $this->db->where('p.id', $t['property_id']); 
            }   else {
                if($importType == 'apts'){
                    $this->db->where("(TRIM(p.name) like '%".$t['property']."%' or p.mapping like '%".$t['property']."%' )");
                } else {
                    $this->db->where(['TRIM(p.short_name)' => $t['short_name'], 'TRIM(u.name)' => $t['unit']]);
                }
            }

            if ( isset($t['unit_id'])){
                $this->db->where('u.id', $t['unit_id_id']); 
            }   else {
                    $this->db->where("(TRIM(u.name) = '".$t['unit']."' or u.mapping = '".$t['unit']."')");
            }

            if ( isset($t['profile_id'])){
                $this->db->where(['lp.profile_id' => explode("-",$t['profile_id'])[0],'lp.lease_id' => explode("-",$t['profile_id'])[1]]); 
            } else {
                if($importType == 'apts'){
                    $this->db->where('(CONCAT_WS(" ",TRIM(prof.first_name),TRIM(prof.last_name))="'.$t['profile'].'" or prof.mapping = "'.$t['profile'].'")');
                } else {
                    $this->db->where('(CONCAT_WS(" ",TRIM(prof.first_name),TRIM(prof.last_name))="'.$str.'" or prof.mapping = "'.$t['profile'].'")');
                }
            }
           
            $this->db->order_by('l.start DESC');
            $this->db->limit(1);

            $q = $this->db->get();

            if ( isset($t['property_id']) && $t['property_id'] > 0 ){
                $updateText = 'concat_ws(",",mapping,"'.$t['property'].'")';
                $updateText2 = 'UPDATE properties SET mapping = concat_ws(",",mapping,"'.$t['property'].'") WHERE `id`= '.$t['property_id'];
                $qu = $this->db->query($updateText2);
                //$this->db->update('properties4',array('mapping' =>  $updateText),'id='.$t['property_id']);                        
            }

            if ( isset($t['unit_id']) && $t['unit_id'] > 0 ){
                $this->db->update('units',array('mapping' => $t['unit']),'id='.$t['unit_id']);                        
            }
            if ( isset($t['profile_id']) ){
                //$this->db->where('id', explode("-",$t['profile_id'])[0]);
                $this->db->update('profiles',array('mapping' =>$t['profile']),'id='.explode("-",$t['profile_id'])[0]);
                
            }

            if ($q->num_rows() > 0) {
                $data = $q->row();
                
                $openBalances = $this->getTransactions($data->lease, $data->profile); //took out $data->profile?
                $applied_payments = [];
                //clone amount for transactions 

                $amount = $t['amount'];
                $balances = array_column($openBalances, 'open_balance');
                $key = array_search($amount, $balances);
                if($key){
                    $applied_payments[] = ['transaction_id_b' => $openBalances[$key]['id'], 'amount' => $amount];
                }else{
                    foreach($openBalances as $ob){
                        if($amount == 0){
                            break;
                        }
                        //if has credit don't need to pay
                        if($ob['open_balance'] > 0){
                            $apply = $amount > $ob['open_balance'] ? $ob['open_balance'] : $amount;
                           // do payment
            
                            $applied_payments[] = ['transaction_id_b' => $ob['id'], 'amount' => $apply];
                            $amount = (($amount - $ob['open_balance']) > 0) ? ($amount - $ob['open_balance']) : 0;
    
                        }else{
                            continue;
                        }
    
                    }//end of open balance for each
                }
                $tdate = ($importType=='apts') ? date('Y-m-d', strtotime(str_replace('/', '-', $t['initiatedOn']))) : $date; 
                $depositTo = ($importType=='apts') ? $data->default_bank : $this->uf; 
                $ref = ($importType=='apts') ? $t['ReferenceID']: $t['description']; 
                $amount = ($importType =="apts") ? $t['credit'] : $t['amount'];
                $accounts_receivable = ['account_id' => $this->ar, 'lease_id' =>  $data->lease, 'property_id' => $data->property, 'profile_id' => $data->profile, 'unit_id' => $data->unit,  'credit' => $amount];
                $deposit_to =  ['account_id' => $depositTo, 'lease_id' =>  $data->lease, 'property_id' => $data->property, 'profile_id' => $data->profile, 'unit_id' => $data->unit, 'debit' => $amount];
                $header = ['transaction_date' => $tdate,'transaction_ref' => $ref, 'memo' => $t['description']];
                $customerPayments = ['payment_method' => 6];
                $applied_payments = $applied_payments ? $applied_payments : NULL;
                $entered = $this->applyPayments($header, $customerPayments, $applied_payments, $accounts_receivable, $deposit_to);
                if($entered) {
                    array_push($array,array('msgInfo' => $t['id'],'type' => 'success', 'message' => 'Transaction successfully added.'));
                }else{
                    array_push($array,array('msgInfo' => $t['id'],'type' => 'danger', 'message' => 'something went wrong while data was being entered')); 
                }
               

            }else{

            // $this->db->select('p.id AS property');
            // $this->db->from('properties p'); 
            // $this->db->where('TRIM(p.short_name)', $t['short_name']);
            // $q = $this->db->get();
            // $propertyName = $q->row()->property ? $q->row()->property : ''; 

            // $this->db->select('u.id AS unit');
            // $this->db->from('units u'); 
            // $this->db->where('TRIM(u.name)', $t['unit']);
            // $q = $this->db->get();
            // $unitNames = $q->row()->unit ? $q->row()->unit : '';  

            // $this->db->select('prof.id AS profile');
            // $this->db->from('profiles prof'); 
            // $this->db->where('CONCAT_WS(" ",TRIM(prof.first_name),TRIM(prof.last_name))=' , $str);
            // $q = $this->db->get();
            // $profileName = $q->row()->profile ? $q->row()->profile: '';  
            
            // if(!empty($propertyName) && !empty($unitNames) && !empty($profileName)){
            //     $this->db->select('u.id AS unit');
            //     $this->db->from('properties p');
            //     $this->db->join('units u', 'u.property_id = p.id AND TRIM(u.name) =' . $t['unit']);
            //     $this->db->where('p.id',  $propertyName);
            //     $q = $this->db->get();
            //     $unitName = $q->row()->unit ? $q->row()->unit : '';  

            //     if(!empty($unitName)){
            //         $this->db->select('lp.id');
            //         $this->db->from('leases_profiles lp');
            //         $this->db->where('unit_id',$unitName);
            //         $q = $this->db->get();
            //         $lp_ids = $q->row()->unit ? $q->row()->id : '';
                    
            //         if(!empty($lp_ids)){
            //             $this->db->select('lp.profile_id');
            //             $this->db->from('leases_profiles lp');
            //             $this->db->where_in(['id' ->$lp_ids]);
            //             $this->db->where_in(['profile_id' ->$profileName]);
            //             $q = $this->db->get();
            //             $lp_idsWithprofile = $q->row()->unit ? $q->row()->id : '';   
            //         }else{
            //             no such unit on a lease
            //         }
            //         if(empty($lp_idsWithprofile)){
            //             no profile on a lease with this unit
            //         }

                    

            //     }else{
            //         no such unit for this property
            //     }
                
            // }else{
            //     no such property ....
            // }


            $this->db->select('l.id AS lease, p.id AS property, prof.id AS profile, u.id AS unit, p.name AS property_name, u.name AS unit_name');
            $this->db->from('properties p');
            $this->db->join('units u', '(p.id = u.property_id AND TRIM(u.name) ="' . $t['unit'] . '") or (p.id = u.property_id AND u.mapping = '.'"'.$t['unit'].'")','left');
            $this->db->join('leases l', 'u.id = l.unit_id','left');
            $this->db->join('leases_profiles lp' ,'l.id = lp.lease_id','left');
            
            if($importType =="apts"){
                $this->db->join('profiles prof',' lp.profile_id = prof.id AND CONCAT_WS(" ",TRIM(prof.first_name),TRIM(prof.last_name)) ="' . $t['profile'] . '"','left');
                $this->db->where("TRIM(p.name)",$t['property'])->or_where("p.mapping like '%".$t['property']."%' ");
            }else{
                $this->db->join('profiles prof',' lp.profile_id = prof.id AND CONCAT_WS(" ",TRIM(prof.first_name),TRIM(prof.last_name)) ="' . $str . '"','left');
                $this->db->where('TRIM(p.short_name)',$t['short_name']);
            }
            
            
            $this->db->order_by('l.start DESC');
            $this->db->limit(1);

            $q = $this->db->get_compiled_select();
            $this->db->reset_query();

            $newq = str_replace("`", "", $q);
            $q = $this->db->query($newq);
            if ($q->num_rows() > 0) {
                $data = $q->row();
                if($data->unit == NULL){
                    $msg = 'We couldnt find the unit <b>'.$t['unit'].'</b> in <b>'.$data->property_name.'</b> please choose the correct unit <span class="select" style = "width:150px; display: inline-block; margin-bottom: 15px;" pxstype = "unit">
                    <label for="property" class="hidden">Label</label>
                    <select stype="unit" class="fastEditableSelect" key="unit.name" filter_key="property_id" filter_value ='.$data->property.' modal="unit"  default=" " id="unit_id1" name="import['.$t['id'].'][unit_id]">
                    </select>';
                }elseif(($data->unit != NULL) && ($data->lease != NULL)){
                    $msg = $t['profile'] .' does not live in unit ' . $data->unit_name.'  please choose a valid name for this unit <span class="select" style = "width:150px; display: inline-block; margin-bottom: 15px;" pxstype = "profile">
                    <label for="profile" class="hidden">Label</label>
                    <select stype="profile" class="fastEditableSelect" key="profiles.first_name" modal="tenant"  filter_key="unit_id" filter_value ='.$data->unit.' default=" " id="profile_id1" name="import['.$t['id'].'][profile_id]">
                    </select>
                </span>'; 
                }else{
                    $msg = 'No lease on unit ' . $t['unit'];
                }
            }else{
                $msg = 'We couldnt find a matching property to '.$t['property'].' please choose the correct property <span class="select" style = "width:150px; display: inline-block; margin-bottom: 15px;" pxstype = "property">
                <label for="property" class="hidden">Label</label>
                <select stype="property" class="fastEditableSelect" key="property.name" modal="property"  default=" " id="property_id1" name="import['.$t['id'].'][property_id]">
                </select>';
            }

            array_push($array,array('msgInfo' => $t['id'],'type' => 'danger', 'message' => $msg));
            }
            

        }//end of t foreach
        
        return $array;
       
    }

    public function sendEmail($row, $type)
    {
        $this->load->library('email');
        $email_user = $this->db->get_where('users', array('id' => $this->site->settings->tenant_notification_user))->row();
		$config['smtp_user'] = $email_user->email;
		$config['smtp_pass'] = $email_user->email_password;
        $this->email->initialize($config);
        $trans_date = $type == 'c'? $row->next_trans_date : $row->transaction_date ;
        $row = $type == 'c'?json_decode($row->transactions->data):$row->data;
        $lease_id = $row->lease_id;
        //echo $row->transactions->lease_id;
        //echo $row->transactions->profile_id;
        //echo $row->transactions->property_id;
        //echo $row->transactions->unit_id;
        //echo $row->transactions->credit;
        //echo $row->transactions->description;
        //echo $row->transactions->Item_id;

        $query = $this->db->get_where('profiles', array('id' => $row->profile_id));
        $tenant=$query->row();
        $lquery = $this->db->get_where('leases', array('id' => $row->lease_id));
        $lease=$lquery->row();
        if($tenant->email_pay_notifications != 1) {return;}
        $company_name = $this->site->settings->company_name;
        $company_phone = $this->site->settings->company_phone;
        $company_email = $this->site->settings->company_email;
        $company_logo = $this->site->settings->company_logo;

        //echo $tenant->first_name;
        //echo $tenant->last_name;
        //echo $tenant->email;

        

            //$string = $this->load->view('email_template.php', '', TRUE);


            $subject = $type == 'c'? 'Rent Reminder From '.$company_name: 'Thank You for your payment!' ;
            $message = $type == 'c'? 'This is a reminder that your rent payment of $'.number_format($row->credit, 2, '.', ',').' is due on '.$trans_date.'.': 'Your payment of $'.number_format($row->credit, 2, '.', ',').' was posted to your account on '.$trans_date.'.';

            // Get full html:
            $body = '
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=' . strtolower(config_item('charset')) . '" />
            <title>' . html_escape($subject) . '</title>
            <style type="text/css">
            
            html { -webkit-text-size-adjust: none; -ms-text-size-adjust: none;}

                @media only screen and (min-device-width: 750px) {
                    .table750 {width: 750px !important;}
                }
                @media only screen and (max-device-width: 750px), only screen and (max-width: 750px){
                  table[class="table750"] {width: 100% !important;}
                  .mob_b {width: 93% !important; max-width: 93% !important; min-width: 93% !important;}
                  .mob_b1 {width: 100% !important; max-width: 100% !important; min-width: 100% !important;}
                  .mob_left {text-align: left !important;}
                  .mob_center {text-align: center !important;}
                  .mob_soc {width: 50% !important; max-width: 50% !important; min-width: 50% !important;}
                  .mob_menu {width: 50% !important; max-width: 50% !important; min-width: 50% !important; box-shadow: inset -1px -1px 0 0 rgba(255, 255, 255, 0.2); }
                  .mob_btn {width: 100% !important; max-width: 100% !important; min-width: 100% !important;}
                  .mob_pad {width: 15px !important; max-width: 15px !important; min-width: 15px !important;}
                  .top_pad {height: 15px !important; max-height: 15px !important; min-height: 15px !important;}
                  .top_pad2 {height: 50px !important; max-height: 50px !important; min-height: 50px !important;}
                  .mob_title1 {font-size: 18px !important; line-height: 40px !important;}
                  .mob_title2 {font-size: 26px !important; line-height: 33px !important;}
                  .mob_txt {font-size: 20px !important; line-height: 25px !important;}
                }
               @media only screen and (max-device-width: 550px), only screen and (max-width: 550px){
                  .mod_div {display: block !important;}
               }
                .table750 {width: 750px;}
            </style>
            </head>
            <body style="margin: 0; padding: 0;">

            <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background: #f5f8fa; min-width: 340px; font-size: 1px; line-height: normal;">
                <tr>
                <td align="center" valign="top">            
                    <!--[if (gte mso 9)|(IE)]>
                     <table border="0" cellspacing="0" cellpadding="0">
                     <tr><td align="left" valign="top" width="750"><![endif]-->
                    <table cellpadding="0" cellspacing="0" border="0" width="750" class="table750" style="width: 100%; max-width: 750px; min-width: 340px; background: #f5f8fa;">
                        <tr>
                           <td class="mob_pad" width="25" style="width: 25px; max-width: 25px; min-width: 25px;">&nbsp;</td>
                            <td align="center" valign="top" style="background: #ffffff;">

                              <table cellpadding="0" cellspacing="0" border="0" width="100%" style="width: 100% !important; min-width: 100%; max-width: 100%; background: #f5f8fa;">
                                 <tr>
                                    <td align="right" valign="top">
                                       <div class="top_pad" style="height: 25px; line-height: 25px; font-size: 23px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>

                              <table cellpadding="0" cellspacing="0" border="0" width="88%" style="width: 88% !important; min-width: 88%; max-width: 88%;">
                                 <tr>
                                    <td class="mob_left" align="center" valign="top">
                                       <div style="height: 40px; line-height: 40px; font-size: 38px;">&nbsp;</div>
                                       <a href="#" target="_blank" style="display: block; max-width: 128px;">
                                       
                                          <img src="'.base_url() . "uploads/images/" . $company_logo.'" alt="img" width="128" border="0" style="display: block; width: 128px;" />
                                       </a>
                                       <div class="top_pad2" style="height: 78px; line-height: 78px; font-size: 76px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>

                              <table cellpadding="0" cellspacing="0" border="0" width="88%" style="width: 88% !important; min-width: 88%; max-width: 88%;">
                                 <tr>
                                    <td class="mob_left" align="left" valign="top">
                                       <font class="mob_title1" face="\'Source Sans Pro\', sans-serif" color="#1a1a1a" style="font-size: 52px; line-height: 55px; font-weight: 300; ">
                                          <span class="mob_title1" style="font-family: \'Source Sans Pro\', Arial, Tahoma, Geneva, sans-serif; color: #1a1a1a; font-size: 22px; line-height: 32px; font-weight: 300;">Hello '.$tenant->first_name.'
                                          <br>

                                               '.$message.'</span>
                                       </font>
                                       <div style="height: 25px; line-height: 25px; font-size: 23px;">&nbsp;</div>
                                       <font class="mob_title2" face="\'Source Sans Pro\', sans-serif" color="#5e5e5e" style="font-size: 18px; line-height: 45px; font-weight: 300; ">
                                          <span class="mob_title2" style="font-family: \'Source Sans Pro\', Arial, Tahoma, Geneva, sans-serif; color: #5e5e5e; font-size: 18px; line-height: 35px; font-weight: 300;">
                                            Thank you for choosing '.$company_name.'. <br><br>

                                            '.$company_name.' <br><br>

                                            '.$company_phone.'.</span>
                                       </font>
                                       <div style="height: 38px; line-height: 38px; font-size: 18px;">&nbsp;</div>
                                       <table class="mob_btn" cellpadding="0" cellspacing="0" border="0" width="250" style="width: 250px !important; max-width: 250px; min-width: 250px; background: #27cbcc; border-radius: 4px;">
                                          <tr>
                                          </tr>
                                       </table>
                                       <div class="top_pad2" style="height: 78px; line-height: 78px; font-size: 76px;">&nbsp;</div>
                                    </td>
                                 </tr>
                              </table>

                              

                           </td>
                           <td class="mob_pad" width="25" style="width: 25px; max-width: 25px; min-width: 25px;">&nbsp;</td>
                        </tr>
                     </table>
                     <!--[if (gte mso 9)|(IE)]>
                     </td></tr>
                     </table><![endif]-->
                  </td>
               </tr>
            </table>
            </body>
            </html>';
            
            
    
            // Also, for getting full html you may use the following internal method:
            //$body = $this->email->full_html($subject, $message);

            $result = $this->email
                ->from($email_user->email)   
                //->from($companySettings->company_email)             
                ->reply_to($companySettings->company_email)    // Optional, an account where a human being reads.
                ->bcc('debbie@simpli-city.com')
                ->to($tenant->email) 
                ->subject($subject)
                ->message($body)
                ->send();

            //var_dump($result);
            //echo '<br />';
            //echo $this->email->print_debugger();
            //echo $this->session->userdata('email');

            exit; 
    }
    

}
// $key = array_search(40489, array_column($userdb, 'uid'));
// select l.id AS lease, p.id AS property, prof.id AS profile, u.id AS unit
// from properties p
// left join units u on u.property_id = p.id AND p.short_name = ''

// join leases_profiles lp on prof.id = lp.profile_id
//             join units u on lp.unit_id = u.id
            
//            join leases l on lp.lease_id = l.id
//             where(['TRIM(p.short_name)' => $t['short_name'], 'TRIM(u.name)' => $t['unit'],'CONCAT_WS(" ",TRIM(prof.first_name),TRIM(prof.last_name))=' => $str]); 
           

//             select p.id AS pr, u.id AS un from properties p
// left join units u on  u.property_id = p.id AND u.name = 'STORE 2'
// where p.short_name = '462 36'


// select lp.id AS lps from leases_profiles lp
// join units u on  lp.unit_id = u.id 
// left join profiles prof on lp.profile_id = prof.id AND prof.name = 



// select l.id AS lease, p.id AS property, prof.id AS profile, u.id AS unit
//             from properties p
//             left join units u on p.id = u.property_id AND TRIM(u.name) = 'STORE 2'
//             left join leases_profiles lp on u.id = lp.unit_id
//             left join leases l on lp.lease_id = l.id
//             left join profiles prof on lp.profile_id = prof.id AND CONCAT_WS(" ",TRIM(prof.first_name),TRIM(prof.last_name)) = 'Alisa Ibragimova'
            
//             where TRIM(p.short_name)= '462 36'
//             order by l.start DESC
//             limit 1


//             select l.id AS lease, p.id AS property, prof.id AS profile, u.id AS unit
//             from properties p
//             left join units u on p.id = u.property_id AND TRIM(u.name) = 'ST2'
//             left join leases_profiles lp on u.id = lp.unit_id
//             left join profiles prof on lp.profile_id = prof.id AND CONCAT_WS(" ",TRIM(prof.first_name),TRIM(prof.last_name)) = 'Alisa Ibragimova'
//             left join leases l on lp.lease_id = l.id
            
//             where TRIM(p.short_name)= '462 36'
//             order by l.start DESC
//             limit 1