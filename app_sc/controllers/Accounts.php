<?php defined('BASEPATH') OR exit('No direct script access allowed');
include 'app_sc/helpers/logs/logs.php';

class Accounts extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->icons = Array("property" => "fas fa-building", "unit" => "far fa-building");
        $this->load->model('validate_model');
        $this->load->library('encryption');
        $this->load->model('accounts_model');
    }

    function index()
    {

      
        $this->meta['title'] = "Accounts";
        $this->meta['h2'] = "Accounts";
        $this->page_construct('accounts/index', null, $this->meta);


    }

    function addAccount() 
    {
        // $this->load->model('validations_model');
        // $this->data['validate'] = $this->validations_model->validate("accounts", $this->input->post());
        // if($this->data['validate']){
        //     echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('account')));
        // }
        $errors = "";

        $account = $this->input->post('account');
        $table = $this->input->post('table');
        //$table = $this->accounts_model->getSpecialAccount($account['account_types_id']);
        $specialAccount = $this->input->post('specialAccount');
        $propertyAccounts = $this->input->post('propertyAccounts');
        $data = array('account' => $account, 'table' => $table, 'specialAccount' => $specialAccount);
        $validate = $this->validate_model->validate("account", $data);
        $this->form_validation->set_rules($this->settings->accountFormValidation);
        if ($this->form_validation->run() && $validate['bool'] && $this->accounts_model->addAccount($account, $specialAccount, $table, $propertyAccounts))
            echo json_encode(array('type' => 'success', 'message' => 'Account successfully added.'));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('account')));

        }
    }

    function editAccount($aid = 0)
    {
        if(($aid == $this->site->settings->accounts_receivable) ||($aid == $this->site->settings->accounts_payable) || ($aid == $this->site->settings->undeposited_funds)){
            echo json_encode(array('type' => 'danger', 'message' => 'No modification allowed', 'errors' => $this->parse_errors('account')));
            return;
        }
        // $this->load->model('validations_model');
        // $this->data['validate'] = $this->validations_model->validate("accounts", $this->input->post());
        // if(!$this->data['validate']){
        //     echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('account')));
        // }else{
        //     $this->load->model('validations_model');
            $errors = "";
            $account = $this->input->post('account');
            $table = $this->input->post('table');
            $specialAccount = $this->input->post('specialAccount');
            $propertyAccounts = $this->input->post('propertyAccounts');
            
            if(!$account['active']) $account['active'] = 0;
            if(!$account['all_props']) $account['all_props'] = 0;

            $data = array('id' => $aid, 'account' => $account, 'table' => $table, 'specialAccount' => $specialAccount);
            $validate = $this->validate_model->validate("account", $data);
            if($validate['msg'] == "Account name is already used.</br>"){
                echo json_encode(array('type' => 'other', 'message' => 'An account with the same name, parent and account type already exists, do you want to merge the two?', 'confirm_a' =>$validate['params']['account_a'], 'confirm_b'=>$validate['params']['account_b']));
                return;
            }
            $this->form_validation->set_rules($this->settings->accountFormValidation);
            if ($this->form_validation->run() && $validate['bool'] && $this->accounts_model->editAccount($account, $specialAccount, $aid,  $table, $propertyAccounts)){
                echo json_encode(array('type' => 'success', 'message' => 'Account successfully updated.'));
            }else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('account')));

        }
    }

    function editSpecialAccount($aid){
            $errors = "";
            $account = $this->input->post('account');
            $table = $this->input->post('table');
            $specialAccount = $this->input->post('specialAccount');
            $data = array('id' => $aid, 'table' => $table, 'specialAccount' => $specialAccount);
            // $validate = $this->validate_model->validate("account", $data);
            // $this->form_validation->set_rules($this->settings->accountFormValidation);
            if (/*$this->form_validation->run() && $validate['bool'] &&*/ $this->accounts_model->editSpecialAccount($specialAccount, $aid,  $table)){
                echo json_encode(array('type' => 'success', 'message' => 'Account successfully updated.'));
            }else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('account')));
            }
    }
    public function mergegetModal()
    {   
        $data = json_decode($this->input->post('params'));
        $this->load->view('forms/merge', $data);
    }
    
    public function getModal()
    {
        $this->load->model('profiles_model');
        $this->load->model('encryption_model');

        //$params = json_decode($this -> input -> post('params'));
        //$this -> data['profiles'] = $this -> tenants_model -> getTenants();
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'accounts/addAccount';
                $this->data['title'] = 'Add Account';
                $this->data['classes'] = $this->accounts_model->getClasses();
                $this->data['account_types'] = $this->accounts_model->getAccountTypes();
                $parent = $this->data['parents'] = $this->accounts_model->getAllParents();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['parents']);
                $this->data['properties'] = $this->accounts_model->getProperties();
                $this->data['allProperties'] = $this->data['properties'];
                $this->data['vendors'] = $this->accounts_model->getVendorsList();
                $this->data['profiles'] = $this->accounts_model->getProfiles();
                $this->data['jvendors'] = json_encode($this->data['vendors']);
                $this->data['jallProperties'] = json_encode($this->data['allProperties']);
                $this->data['jallProfiles'] = json_encode($this->data['profiles']);
                $this->data['accounts'] = $this->accounts_model->getAccounts();
                $this->data['jaccounts'] = json_encode($this->data['accounts']);
                $this->data['maxId'] = $this->accounts_model->maxId();
                $this->data['plaid_env'] = 'development';
                $this->data['plaid_client_id'] = '5e5d829fd6e09e0012bc175c';
                $this->data['plaid_public'] = 'a55921b437e68bd01c9d8da602f2ce';
                $this->data['plaid_secret'] = '4c95a91e4e1b0a393b7ed4d0389089';
                $this->data['plaid_url'] = 'https://development.plaid.com';


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
                // $this->data['parents'] = $this->accounts_model->getAllParents();
                // $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['parents']);
                $this->data['parents'] = $this->accounts_model->getParents($this->data['account']->account_types_id);
                $this->data['classes'] = $this->accounts_model->getClasses();
                $this->data['table'] = $this->accounts_model->getSpecialAccountName($this->data['account']->account_types_id);
                $this->data['properties'] = $this->accounts_model->getProperties($this->input->post('id'));
                $this->data['allProperties'] = $this->accounts_model->getProperties();
                $this->data['vendors'] = $this->accounts_model->getVendorsList();
                $this->data['profiles'] = $this->accounts_model->getProfiles();
                $this->data['jvendors'] = json_encode($this->data['vendors']);
                $this->data['jallProperties'] = json_encode($this->data['allProperties']);
                $this->data['jallProfiles'] = json_encode($this->data['profiles']);
                $this->data['accounts'] = $this->accounts_model->getAccounts();
                $this->data['jaccounts'] = json_encode($this->data['accounts']);
                //$this->data['property_accounts'] = $this->accounts_model->getPropertyAccounts($this->input->post('id'));
                if($this->data['table']->special_table !== null){
                    $this->data['specialAccount'] = $this->accounts_model->getSpecialAccount($this->data['table']->special_table,$this->data['account']->id);
                    $this->data['plaid_env'] = 'development';
                    $this->data['plaid_client_id'] = '5e5d829fd6e09e0012bc175c';
                    $this->data['plaid_public'] = 'a55921b437e68bd01c9d8da602f2ce';
                    $this->data['plaid_secret'] = '4c95a91e4e1b0a393b7ed4d0389089';
                    $this->data['plaid_url'] = 'https://development.plaid.com';
                    //$this->data['specialAccount'] = $this->encryption_model->decryptThis($this->data['specialAccount']);
                } 
                //$specialAccount =  $this->data['specialAccount'];
                
                if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['account'] = new stdClass();
                    $this->data['account']->$key = $params->es_value;
                }
                break;
        }
        if(($this->input->post('id') == 451) || ($this->input->post('id') == 454)){
            $this->data['locked'] = "locked";
        }

        $this->load->view('forms/account/main', $this->data);
        
    }

    public function date_check($date1, $date2)
    {
            if ( new Date($date1) >= new Date($date2))
            {
                    //$this->form_validation->set_message('date_check', 'The start date is after the end date');
                    return FALSE;
            }
            else
            {
                    return TRUE;
            }
    }

    function getBankName($routingNumber){
        $result = $this->accounts_model->getBankName($routingNumber);
        echo $result ? $result : '';
    }

    function save_plaid_token($access_token, $id, $name, $ins_info){

        $token = json_decode($access_token);
        
        $data = array(
            'ins_id' => $id,
            'bank_name' => $name,
            'access_token' => $token->access_token,
            'custom' => $ins_info
         );
        


        $this->db->insert('fin_ins', $data);
        //echo json_encode(array('type' => 'success', 'message' => 'Account successfully linked to your bank.'));


    }


    function test($account_id){
        $months =  'select 
                            DATE_FORMAT(m1, "%b") AS month

                            from
                            (
                            select 
                            DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),"%Y-%m-01")
                            +INTERVAL m MONTH as m1
                            from
                            (
                            select @rownum:=@rownum+1 as m from
                            (select 1 union select 2 union select 3 union select 4) t1,
                            (select 1 union select 2 union select 3 union select 4) t2,
                            (select @rownum:=-1) t0
                            ) d1
                            ) d2 
                            where m1<=LAST_DAY(DATE_SUB(CURDATE(),INTERVAL 1 MONTH))
                            order by m1';
        $this->db->select('DATE_FORMAT(th.transaction_date,"%b") AS month,YEAR(th.transaction_date),
            CASE 
                WHEN (ac.debit_credit = "credit") THEN  SUM(t.credit - t.debit)
                WHEN (ac.debit_credit = "debit") THEN SUM(t.debit - t.credit)
            END AS balance ');
        $this->db->from('transaction_header th');
        $this->db->join('transactions t', 'th.id = t.trans_id');
        $this->db->join('accounts a', 't.account_id = a.id AND a.id =' . $account_id);
        $this->db->join('account_types at', 'a.account_types_id = at.id');
        $this->db->join('account_category ac', 'at.account_category_id = ac.id');
        //$this->db->join('(' . $months . ')months', 'DATE_FORMAT(th.transaction_date,"%b") = months.month','left');
        $this->db->where('th.transaction_date BETWEEN DATE_FORMAT(DATE_SUB(CURDATE(),INTERVAL 6 MONTH),"%Y-%m-01") AND LAST_DAY(DATE_SUB(CURDATE(),INTERVAL 1 MONTH))');
        $this->db->group_by('YEAR(th.transaction_date), DATE_FORMAT(transaction_date,"%b"), DATE_FORMAT(transaction_date,"%m"), t.account_id');
        $this->db->order_by('YEAR(th.transaction_date) DESC, DATE_FORMAT(transaction_date,"%m") DESC');
        $q = $this->db->get_compiled_select();
        echo $q;

    }

      

    public function process_plaid_token(){
        $plaid_env = 'development';
        $plaid_client_id = "5e5d829fd6e09e0012bc175c";
        $plaid_public = "a55921b437e68bd01c9d8da602f2ce";
        $plaid_secret = "4c95a91e4e1b0a393b7ed4d0389089";
        $plaid_url = "https://development.plaid.com";
        $pt = $this->input->post('pt');
        $bank = $this->input->post('bank');
        $ins_id = $this->input->post('md')['institution']['institution_id'];
        $ins_name = $this->input->post('md')['institution']['name'];
        $plaid_acct = $this->input->post('md')['account']['id'];
        $bankId = $this->input->post('bank');
        $ins_info;
        $plaid_token='';

        $result =  $this->accounts_model->checkFinIns($ins_id);
        if($result == false) {
            $plaid_token = $this->get_plaid_token($pt);

            $ins_info = $this->get_ins_info($ins_id);
     
            $this->save_plaid_token($plaid_token, $ins_id, $ins_name, $ins_info);
            $token = json_decode($plaid_token);
            $plaid_token = $token->access_token;
        } else {
            $plaid_token = $result->access_token;
            //echo "duplicate!";
        };
        
       
        //add bank if it isnt yet in the db
        if($bankId == null){
            $acctid = $this->input->post('id');                       
            $this->db->insert('banks', Array("account_id"=>$acctid, "bank_name" => $ins_name));
            $bankId = $this->db->insert_id();
            
        }
        $banks = json_decode($this->get_bank_accts_plaid($ins_id));
        $data = ['accounts' => $banks, 'ins_info' => json_decode($ins_info)->institution ];

        echo json_encode($data);
        //$this->save_plaid_acct($plaid_acct, $ins_id, $bankId);
        //$this->get_plaid_accounts($plaid_token);

    
    }


    function save_plaid_acct($plaid_acct = null, $ins_id = null, $bankId = null){
        
        //if it's from a new bank that was added the values are passed in from previous function
        if(isset($plaid_acct)){
            $data = array(
                'ins_id' => $ins_id,
                'plaid_acct' => $plaid_acct
             );
        }
        
        //if it's from an old bank the values are passed in from the form ($post)
        if(null !== $this->input->post('plaid_acct')){
            $ins_id = $this->input->post('ins_id');
            $data = array(
                'ins_id' => $this->input->post('ins_id'),
                'plaid_acct' => $this->input->post('plaid_acct')
             );
             
             $bankId = $this->input->post('bank_id');
             if ($bankId == 0){
                $acctid = $this->input->post('acct_id');                       
                $this->db->insert('banks', Array("account_id"=>$acctid, "bank_name" => $ins_name));
                $bankId = $this->db->insert_id();
             }
             $plaid_acct = $this->input->post('plaid_acct');
        }
        
        
        $cust = json_encode($data);

        $q1 = $this->db->update('banks', array('custom' => $cust), array('id' => $bankId));
        $q2 = $this->db->update('plaid_banks', array('bank_id' => $bankId), array('plaid_id' => $plaid_acct));
        $this->accounts_model->get_trans_plaid($ins_id);
        //echo "success";

    }

    function get_trans_plaid($ins_id){
        $this->accounts_model->get_trans_plaid($ins_id);
    }
    

    function get_bank_accts_plaid($ins_id){
        $access_token = $this->accounts_model->getToken($ins_id);
        $accounts = $this->get_plaid_accounts($access_token);
        if ($accounts == 'ITEM_LOGIN_REQUIRED'){
            //$plaid_info = $this->accounts_model->getInsInfo($ins_id);
            $link_token = json_decode($this->get_link_token_update($access_token))->link_token;
            $info = array(
                'ins_id' => $ins_id,
                'link_token' => $link_token,
                'error' => 'ITEM_LOGIN_REQUIRED',
            );

            $info = json_encode($info);
            return $info;
        } else {

            $accounts = json_decode($accounts);
            $data = array();
            $update='';
            $separator='';
            $separator1='';
            foreach ($accounts->accounts as $account){
                $update.=$separator1."(";

                $info = array(
                    'balances' => $account->balances,
                    'mask' => $account->mask,
                    'name' => $account->name,
                    'official_name' => $account->official_name,
                    'subtype' => $account->subtype,
                    'type' => $account->type
                );

                $info = json_encode($info);

                
                
                $account1 = array (
                    'plaid_id' => $account->account_id,
                    'ins_id' => $ins_id,
                    'info' => $info

                );
                
                foreach ($account1 as $key => $value) {
                    $update.=$separator." '$value' ";
                    $separator=','; 
                    
                
                }

                $update.=")";
                $separator=' '; 
                $separator1=','; 

                
            }
            
            $update = 'insert into plaid_banks (plaid_id, ins_id, info) values'.$update.' ON DUPLICATE KEY UPDATE info = VALUES(info)';
            $q = $this->db->query($update);

            $this->db->select('*');
            $this->db->from('plaid_banks');
            $this->db->join('banks', 'banks.id = plaid_banks.bank_id', 'left');
            $this->db->join('accounts', 'accounts.id = banks.account_id','left');
            $this->db->where('ins_id', $ins_id);
            //$this->db->where('bank_id', null);
            

            $q2 = $this->db->get();
            if ($q2->num_rows() > 0) {
                
                foreach (($q2->result()) as &$row) {
                    $row->info = json_decode($row->info);
                }
                return json_encode($q2->result());
            } else {
                return 'There are no more accounts to link to from this bank';
            }
            

           
        

        }
        
    }


    
    
    
    
    function get_bank_accts_list(){
        $result = $this->get_bank_accts_plaid($this->input->post('ins_id'));


        echo $result;
        
        
        
        


    }

    function update_plaid_accts_list(){
        $ins_id = $this->input->post('ins_id');
        $access_token = $this->accounts_model->getToken($ins_id);
        $accounts = $this->get_plaid_accounts($access_token);

        echo $accounts;


    }

    function get_plaid_accounts($access_Token)
       {
        $plaid_env = 'development';
        $plaid_client_id = "5e5d829fd6e09e0012bc175c";
        $plaid_public = "a55921b437e68bd01c9d8da602f2ce";
        $plaid_secret = "4c95a91e4e1b0a393b7ed4d0389089";
        $plaid_url = "https://development.plaid.com";

           //global $plaid_client_id, $plaid_secret, $plaid_url;
           $data = array(
               "client_id" => $plaid_client_id,
               "secret" => $plaid_secret,
               "access_token" => $access_Token, 
               //"start_date" => '2020-10-10',
               //"end_date" => '2020-12-10',
               //"options" => array( "account_ids" => array('bnK3QbRoooFoBwQBx71MCM5VgQv9nWiVZ6pxM')) 
               
           );

          
   
           $data_fields = json_encode($data); 
   
           //initialize session
           $ch=curl_init($plaid_url . "/accounts/get");
   
           //set options
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fields);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
             'Content-Type: application/json',                                                                                
             'Content-Length: ' . strlen($data_fields))                                                                       
           );   
   
           //execute session
           $accts_json = curl_exec($ch);
           $error = json_decode($accts_json);
           $error_code = $error->error_code;
           
          
          
           //$exchange_token = json_decode($token_json,true);          
           //close session
           curl_close($ch);  
           
           if ($error_code == 'ITEM_LOGIN_REQUIRED'){
            //$this->process_plaid_token;


           
            return  $error_code;
           } else {
            return  $accts_json;
           }
   
           
       }

    


       function get_plaid_token($public_token)
       {

        $plaid_env = 'development';
        $plaid_client_id = "5e5d829fd6e09e0012bc175c";
        $plaid_public = "a55921b437e68bd01c9d8da602f2ce";
        $plaid_secret = "4c95a91e4e1b0a393b7ed4d0389089";
        $plaid_url = "https://development.plaid.com";

           //global $plaid_client_id, $plaid_secret, $plaid_url;
           $data = array(
               "client_id" => $plaid_client_id,
               "secret" => $plaid_secret,
               "public_token" => $public_token
           );
   
           $data_fields = json_encode($data);        
   
           //initialize session
           $ch=curl_init($plaid_url . "/item/public_token/exchange");
   
           //set options
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fields);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
             'Content-Type: application/json',                                                                                
             'Content-Length: ' . strlen($data_fields))                                                                       
           );   
   
           //execute session
           $token_json = curl_exec($ch);
           //$exchange_token = json_decode($token_json,true);          
           //close session
           curl_close($ch);        
   
           return  $token_json;
       }
       
       //buildium deposits-payments relationship
       function iterate_deposits(){
          //select deposits
          $query = $this->db->get_where('transaction_header', array('transaction_type' => 8));
          

            foreach ($query->result() as $row)
            {
                    echo $row->id;
                    echo '<br>';
                    echo $row->transaction_ref;
                    $this->get_buildium_deposit_payments($row->id);
            }

            //$sql1 = "UPDATE `transactions` AS t1 JOIN transactions t2  ON t1.deposit_id = t2.trans_id SET t1.deposit_id = t2.id WHERE t2.account_id = ".$this->site->settings->undeposited_funds;
            $this->db->query($sql1);
       }
       function get_buildium_deposit_payments($deposit){
          $url="https://blueelminc.managebuilding.com/Manager/api/deposits/".$deposit."/payments";
          $ch = curl_init();
            $headers = array(
                'Accept-Encoding: gzip, deflate, br',
                'Connection: keep-alive',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36e',
                'Host: blueelminc.managebuilding.com',
                'Accept: application/json, text/plain, */*',
                'cookie: _ga=GA1.2.592001624.1637609533; rememberEmailCookie=blueelmny@gmail.com; _at_id.buildium.publicsite_tenantportal.cee0=a3454c00f85ce163.1642106109.1.1642106109.1642106109.19.19.; _evga_c878=f080641730873a20.07k; optimizelyEndUserId=oeu1644944783368r0.693025149538882; _gid=GA1.2.686779568.1645384985; TwoFactorCookie-237208_511998=1E714A53FBB1182661816C5187D45870C4401291; properties_511998=-2; XSRF-TOKEN=5af6d15b77a4373af0c23045552ad52b; Manager.AspNet.ApplicationCookie=P8VLVnPG9IgLj7-9ITqez3ZOdpuEc8mzLFrn_VfWh_R3xRPTzxemAXSykAaD9hGwYamIvOlPHrEm5xwEXZjfvtdmAShJacTmfg-N3ng_8xCTbQYMgknmkCGIB_pNe_W4OFTTiU1ZA25y7tFI27PxY6iUE1ljGkB1Aue66e-lk0y_khyEmlHUllDNqkB8E2v87tUqKfWNSUDhljF3PHhTQzjBbdMA-PnXvPC3FJE5n-QVxWWD3yZfmbAjy5LTjgPuzK0tNJgRdnHKkM9KwZIvpsVnLXmRBElCqQxcC0FtIll00rGfTVaNJrjgkfK9wTPFOuHmth7xvmcRKslIEMCa_oX7se7xAf_KPHJkyaVR2J7EFPgTSjs6SzSlV3TdoM7ttLyM0izgLk1UNxpp7SmrcArCTXSBlM-7Rc0u1UgwOz8G_eAhcdarMwfA7Tr4obE0-OLGp4CfPJFAh-p-Xcj7wBvVqIz5j3vslJpDVsDURmJDSh_VEDvdrFDfdvQ93o8BdMkftw6BZseYfidTBXpCNbf9PhggkioH4GmrzDy3QI0jf0MUv41f0cPU2RWu8nKnU8aksdEy03RgbM5AmLDqor9KczMfPektHkf_05ndUznqXCP3pVBu134AuV6u65McmtGJckgb9R4wlChJO0NVQouu7KZ89XUZWCnx9C-M1tEmzLwHP9eirwWMg0wUIsLZiqmQAovB1gQkqskLZPGKamgeKqE; _hp2_ses_props.1205160675=%7B%22r%22%3A%22https%3A%2F%2Fblueelminc.managebuilding.com%2FManager%2Fapp%2Fhomepage%2Fdashboard%3Finitpage%3D1%22%2C%22ts%22%3A1645720803561%2C%22d%22%3A%22blueelminc.managebuilding.com%22%2C%22h%22%3A%22%2FManager%2Fapp%2Fbanking%2Fbank-account%2F246193%2Fregister%22%7D; _evgn_c878=%7B%22puid%22%3A%22zzTQj2xDuqYBdq90tWmL1zQni1OpJbMdGQtLXeiu-zw8n8Kn3FqhxQLoiyX4DSA7kBd8eqP6S3tCyOqGov9S9Q%22%2C%22paid%22%3A%22vKw2x40JOMkcQXrHqdSGEuIBAl5i3MHy8bFPU_kTz1Q%22%7D; _hp2_id.1205160675=%7B%22userId%22%3A%228261768640819380%22%2C%22pageviewId%22%3A%226118035948193230%22%2C%22sessionId%22%3A%223448499069610995%22%2C%22identity%22%3A%22a98e8d2a-933b-4f3e-bc36-d4c7d2f7625e%22%2C%22trackerVersion%22%3A%224.0%22%2C%22identityField%22%3Anull%2C%22isIdentified%22%3A1%7D'
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

        echo '<br><br>';
        echo $json;
        $newjson = json_decode($json);
        echo '<br><br>';

        foreach ($newjson as $payment) {
            //$q1 = $this->db->update('transactions', array('deposit_id' => $deposit), array('trans_id' => $payment->Id));
            echo $payment->BuildingName;
            echo '<br><br>';
            echo $payment->Id;
            echo '<br><br>';
            echo $payment->TotalAmount;
          }

       }
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
        $url="https://blueelminc.managebuilding.com/Manager/api/deposits/13575856/payments"; 
        $postinfo = "email=".$username."&password=".$password;
        
        $cookie_file_path = base_url() . "uploads/cookie.txt";
        
        $ch = curl_init();
        $headers = array(
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36e',
            'Host: blueelminc.managebuilding.com',
            'Accept: application/json, text/plain, */*',
            'cookie: _ga=GA1.2.841654277.1645563892;%_gid=GA1.2.832334772.1645563892;%rememberEmailCookie=blueelmny@gmail.com;%properties_511998=-2; _hp2_ses_props.1205160675=%7B%22r%22%3A%22https%3A%2F%2Fblueelminc.managebuilding.com%2FManager%2Fapp%2Fhomepage%2Fdashboard%3Finitpage%3D1%22%2C%22ts%22%3A1645634857064%2C%22d%22%3A%22blueelminc.managebuilding.com%22%2C%22h%22%3A%22%2FManager%2Fpublic%2Fauthentication%2Flogin%22%2C%22q%22%3A%22%3FReturnUrl%3D%252FManager%252Fapp%252Fbanking%252Fbank-account%252F246234%252Fregister%22%7D; TwoFactorCookie-237208_511998=1E714A53FBB1182661816C5187D45870C4401291; XSRF-TOKEN=f723744dcd0d5cc80f7c32407869904b; Manager.AspNet.ApplicationCookie=MunDAdR08TtN6IxOie4Zy4HpN-0rGQIuqE3T0TDs1TrKmWvIBvbIYB2r0FeWveEaNLjeH0iFXU8Q3LdGSfPNMYMhznVAbqy4iadvogafUZHYOu1Q5fD5OVb76UpQMcwa4z6RU-Cw6QTe9Du09WXvbzeVCJshBbOLdUUVROfYx30PH29aSR0_yuBsC-rP4Ckaa9nnlqK-VHISUrei7AWY_oAeYQ3XT84Gke2G0WZiKjw_ftVnn6o9PqjVm71DQtu2XcWWXwURtwsAJFYObt7yVuhv3B6uRou84h0mVjJB-K0V3RgKYMRZ8TsvOUrCJpltPoZ4bSWretoR_2TEABIbICj3BQzodm2ytL_IMx7M-VsIEaKEZZeLtUDuhRM55DoZ-i6DIfsVOfyp6U2ZT6Hua4_FMCW9YNDumt_jy-a-VP-2LHQOmt7rKK0AuVwFEARLUZpANKngnOabZZjXhlVu2ewoQNktUorIbXaZDAl9W1PvaYR2gaimhnRDUKW_idaGZ-QFbJExo5XTOgWCyAv7hBH-1wmMdd-hfsItcp8QmIkFwG1knBFFd4dQ0anr_cxv0ia0BaWQFpKcwOYxEmbYLdvHH68a9Lo1auYiNq1-bqB2A8KysyDjFnj2z2r85ptoEPVDlnw7Vex-wmF-rRES_eM7MA3A5o2qFbkFG1F-FqbTCHuu0WcVSjGzAptXcnTdEGwl3e2vCBoEXGG0YVspvLgjYuU; _hp2_id.1205160675=%7B%22userId%22%3A%225322993951702415%22%2C%22pageviewId%22%3A%227027136504351810%22%2C%22sessionId%22%3A%22381321480397941%22%2C%22identity%22%3A%22a98e8d2a-933b-4f3e-bc36-d4c7d2f7625e%22%2C%22trackerVersion%22%3A%224.0%22%2C%22identityField%22%3Anull%2C%22isIdentified%22%3A1%7D; _evgn_c878=%7B%22puid%22%3A%22SCmIouVGs5BlFmXB2rYARnCre9drEt_gOuD63kgwPbCWYKMEGQuwTf4k7m4F5G9vrZ25KMDv9OC45nVNYlatkQ%22%2C%22paid%22%3A%22dhAK_MWK648soNtBUdYTsZ3QueB_zaapKdhkzRYS7cw%22%7D; _evga_c878=a90c0b6eb7123503.07k'
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

       function get_ins_info($insid){

        $plaid_env = 'development';
        $plaid_client_id = "5e5d829fd6e09e0012bc175c";
        $plaid_public = "a55921b437e68bd01c9d8da602f2ce";
        $plaid_secret = "4c95a91e4e1b0a393b7ed4d0389089";
        $plaid_url = "https://development.plaid.com";

           //global $plaid_client_id, $plaid_secret, $plaid_url;
           $data = array(
               "client_id" => $plaid_client_id,
               "secret" => $plaid_secret,
               "institution_id" => $insid,
               "options" => array( "include_optional_metadata" => true)

           );
   
           $data_fields = json_encode($data);        
   
           //initialize session
           $ch=curl_init($plaid_url . "/institutions/get_by_id");
   
           //set options
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fields);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
             'Content-Type: application/json',                                                                                
             'Content-Length: ' . strlen($data_fields))                                                                       
           );   
   
           //execute session
           $ins_info = curl_exec($ch);
           //$exchange_token = json_decode($token_json,true);          
           //close session
           curl_close($ch);        
   
           return  $ins_info;



       }


       function get_link_token_update($access_token){

        $plaid_env = 'development';
        $plaid_client_id = "5e5d829fd6e09e0012bc175c";
        $plaid_public = "a55921b437e68bd01c9d8da602f2ce";
        $plaid_secret = "4c95a91e4e1b0a393b7ed4d0389089";
        $plaid_url = "https://development.plaid.com";

           //global $plaid_client_id, $plaid_secret, $plaid_url;
           $data = array(
               "client_id" => $plaid_client_id,
               "secret" => $plaid_secret,  
               "user" => array( "client_user_id" => 'test'),
               "client_name" => "Simpli-city",
               "country_codes" => array('US'),
               "language"=> "en",
               "access_token"=> $access_token
           );
   
           $data_fields = json_encode($data);        
   
           //initialize session
           $ch=curl_init($plaid_url . "/link/token/create");
   
           //set options
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fields);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
             'Content-Type: application/json',                                                                                
             'Content-Length: ' . strlen($data_fields))                                                                       
           );   
   
           //execute session
           $link_token = curl_exec($ch);
           //$exchange_token = json_decode($token_json,true);          
           //close session
           curl_close($ch);        
   
           return  $link_token;
           

       }

       
       function get_link_token(){

        $plaid_env = 'development';
        $plaid_client_id = "5e5d829fd6e09e0012bc175c";
        $plaid_public = "a55921b437e68bd01c9d8da602f2ce";
        $plaid_secret = "4c95a91e4e1b0a393b7ed4d0389089";
        $plaid_url = "https://development.plaid.com";

           //global $plaid_client_id, $plaid_secret, $plaid_url;
           $data = array(
               "client_id" => $plaid_client_id,
               "secret" => $plaid_secret,  
               "products" => array( "auth"),
               "user" => array( "client_user_id" => 'test'),
               "client_name" => "Simpli-city",
               "country_codes" => array('US'),
               "language"=> "en"
           );
   
           $data_fields = json_encode($data);        
   
           //initialize session
           $ch=curl_init($plaid_url . "/link/token/create");
   
           //set options
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fields);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
             'Content-Type: application/json',                                                                                
             'Content-Length: ' . strlen($data_fields))                                                                       
           );   
   
           //execute session
           $link_token = curl_exec($ch);
           //$exchange_token = json_decode($token_json,true);          
           //close session
           curl_close($ch);        
   
           return  $link_token;
           



       }

       function chooseBankgetModal() {
        
        $this->data['target'] = 'accounts/getPlaidAccounts';
        $this->data['title'] = 'Connect A Bank';
        $this->data['banks'] = $this->accounts_model->getPlaidBanks();
        $this->data['bank_id'] = $this->input->post('id');
        $this->data['acct_id'] = json_decode($this->input->post('params'))->acct_id;
        $this->data['plaid_env'] = 'development';
        $this->data['plaid_client_id'] = '5e5d829fd6e09e0012bc175c';
        $this->data['plaid_public'] = 'a55921b437e68bd01c9d8da602f2ce';
        $this->data['plaid_secret'] = '4c95a91e4e1b0a393b7ed4d0389089';
        $this->data['plaid_url'] = 'https://development.plaid.com';


       

        $this->load->view('forms/account/plaid/banks', $this->data);
        
       }

       function updateAutoCharges($a, $b, $type) {
           //update a to b
        //$a = 3836;
        //$b = 3789;
        $memtrans = $this->db->get('memorized_transactions')->result();
        foreach ($memtrans as $memtran){
            $data = json_decode($memtran->data);
            $transactions = $data->transactions;
            if(is_array($transactions)){
                //for multiline memorized transactions
                foreach($transactions as &$trans){
                    if($trans->$type == $a){
                        $trans->$type = $b;
                    
                    } 
                }
            } else {
                //for singleline memorized transactions
                if($data->transactions->$type == $a){
                    $data->transactions->$type = $b;
                
                }
            }
           

            $memtran->data = json_encode($data);
        }

        //todo make mass update for all memorized transactions
        $this->db->update_batch('memorized_transactions',$memtrans, 'id'); 
    
       }

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