<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function getUnitsList()
    {
        $this->load->model('units_model');
        $data = $this->units_model->getPropertyUnits($this->input->get('value'));
        echo "<option value='0'></option>";
        foreach ($data as $unit) {
            echo "<option value='" . $unit->id . "'>" . $unit->name . "</option>";
        }
    }

    function getParentsList()
    {
        $this->load->model('accounts_model');
        $data = $this->accounts_model->getParents($this->input->get('value'));
        echo "<option value='0'>None</option>";
        foreach ($data as $parent) {
            echo "<option value='" . $parent->id . "'>" . $parent->name . "</option>";
        }
    }

    function getAccountsList()
    {
        $this->load->model('journalEntry_model');
        $data = $this->journalEntry_model->getAccounts($this->input->get('value'));
        echo "<option value='0'>None</option>";
        foreach ($data as $account) {
            echo "<option value='" . $account->id . "'>" . $account->name . "</option>";
        }
    }

    function getAddress()
    {
        $this->load->model('checks_model');
        $data = $this->checks_model->getAddress($this->input->get('value'));
        echo"<p >". $data->address_line_1 ."</p><p>". $data->city ."</p>";
    }

    function getBankBalance()
    {
        $this->load->model('checks_model');
        $data = $this->checks_model->getBalance($this->input->get('value'));
        echo  $data ;
    }
    


    function getVendorAddress()
    {
        $this->load->model('payBills_model');
        $data = $this->payBills_model->getAddress($this->input->get('value'));
        echo  $data->address_line_1 . "<br>" . $data->city ;
    }

    function getAccountsProperty()
    {
        $this->load->model('checks_model');
        $accounts = $this->checks_model->getPropertyAccounts($this->input->get('value'));
        echo "<option value='0'>None</option>";
        foreach ($accounts as $account) {
            echo "<option value='" . $account->id . "'>" . $account->name . "</option>";
        }
    }

    function getUnitsProperty()
    {
        $this->load->model('checks_model');
        $units = $this->checks_model->getPropertyUnits($this->input->get('value'));
        echo "<option value='0'>None</option>";
        foreach ($units as $unit) {
            echo "<option value='" . $unit->id . "'>" . $unit->name . "</option>";
        }
    }

    function getProperties()
    {
        $select = `<select id="rpProperties" name="property" onchange="JS.loadList(api/getTransactions', {profile: $(this).closest('#receive_paymentModel').find('#profile_id').closest('.select').find('input[type=hidden]').val() ,property:$(this).closest('.select').find('input[type=hidden]').val()} , '#received_payment_table' ,  $(this).closest('#receive_paymentModel'))`;
        $this->load->model('receivePayments_model');
        $value = $this->input->get('value');
        $properties = $this->receivePayments_model->getProperties($this->input->get('value'));
        echo '<label for="rpProperties">Property:</label>';
        echo '<span class="select">';
        echo   '<select id="rpProperties" name="property" onchange="JS.loadList(';
        echo "'api/getTransactions', {profile: $(this).closest('#receive_paymentModel').find('#profile_id').closest('.select').find('input[type=hidden]').val() ,property:$(this).closest('.select').find('input[type=hidden]').val()} , '#received_payment_table' ,  $(this).closest('#receive_paymentModel'))";
        echo '" class="editable-select">';
            echo "<option value='0'>None</option>";
        foreach ($properties as $property) {
            echo "<option value='" . $property->id . "'" . (isset($properties) && $properties[0]->id == $property->id ? ' selected' : '') .">" . $property->name . "</option>";
        }
         echo '</select></span>';
    }

    function getNames()
    {
        $this->load->model('checks_model');
        $names = $this->checks_model->getNames($this->input->get('value'));
        echo "<option value='0'>None</option>";
        foreach ($names as $name) {
            echo "<option value='" . $name->id . "'>" . $name->vendor . "</option>";
        }
    }

    

    function getAllTenantsList()
    {
        $this->load->model('tenants_model');
        $data = $this->tenants_model->getTenants();
        echo "<option value='0'>None</option>";
        foreach ($data as $tenant) {
            echo "<option value='" . $tenant->id . "'>" . $tenant->first_name . " " . $tenant->last_name . "</option>";
        }
    }

    function quickAdd()
    {
        $key = $this->input->post('key');
        $value = $this->input->post('value');
        $type = $this->input->post('type');
        switch ($type) {
            case 'setting' :
                $this->addToSettings($key, $value);
                break;
            case 'table' :
                $this->addToTable($key, $value);
                break;
        }
    }

    function quickAddFast()
    {
        $type = $this->input->post('type');
        $value = $this->input->post('value');
        $data = $this->site->quickAddTypes[$type];
        switch ($data['type']) {
            case 'setting' :
                $this->addToSettings($data['key'], $value);
                break;
            case 'table' :
                $this->addToTable($data['key'], $value);
                break;
        }
    }

    function addToTable($key, $value)
    {
        $table = explode('.', $key);
        $q = $this->db->get_where($table[0], array($table[1] => $value), 1);
        if ($q->num_rows() == 0) {
            $this->db->insert($table[0], Array($table[1] => $value));
            $pid = $this->db->insert_id();
            echo json_encode(Array("text" => $value, "value" => $pid));
        }
    }

    function addToSettings($key, $value)
    {
        if (!in_array($value, $this->settings->$key) && $value != '') {
            $settings = $this->settings->$key;
            $settings[max(array_keys($settings)) + 1] = $value;
            $this->settings->$key = $settings;
            echo json_encode(Array("text" => $value, "value" => max(array_keys($this->settings->$key))));
        }
    }

    function getTransactions()
    {
        $this->load->model('receivePayments_model');
        $tt = $this->input->get();
        $profile = $this->input->get('profile');
        //$property = $this->input->get('property');
        //$property = $property ? $property : NULL;
        $lease = $this->input->get('lease');
        $transactions = $this->receivePayments_model->getTransactions($profile, $lease);
           foreach($transactions as $transaction) {

          echo  '<tr role="row"  id="received_payament_row" class="received_payament_row">'.
            '<td  id="received_payament_check" >'. 
            '<input  class="received_payament_input" type="hidden"  name="applied_payments[' .  $transaction->id . '][applied]" value="'.  (($transaction->received_payment != 0 )  ? '1' : '0')  .'">
            <input type="hidden" name="applied_payments['. $transaction->id .'][transaction_id_b]" value="'.  $transaction->id . '"> 
            <i id="received_payament_icon_check" style="visibility:'. ($transaction->received_payment != 0  ? 'visible' : 'hidden') .'" class="icon-check" aria-hidden="true"></i><div class="shadow" style="width: 1200px;"></div>
            </td>
            <td>' . $transaction->description . '</td>
            <td>' . $transaction->date . '</td>
            <td>' . $transaction->due_date .'</td>
            <td><span class="wrapper"><span class="text-left">$</span>  <span class="payment_amount">' .  $transaction->amount .'</span></span></td>
            <td><span class="wrapper"><span class="text-left">$</span><span class="open_balance" >'. $transaction->open_balance .' </span> </span></td>
            <td id="received_payament_row_input_amount">
                <span class="input-amount">
                    <label for="tcaa">$</label>
                    <input type="text" id="received_payament_input_amount" name="applied_payments[' .  $transaction->id .'][amount]" value="'.  $transaction->received_payment .'"  '.  ( $transaction->received_payment != 0 ? "" : "disabled" )  . ' >
                </span>
            </td>
      </tr>';

           }
        echo '<style type="text/css" onload="firstUpdateUnapliedAmount($(this))"></style>';   
	
    }

    function getBillTransactions()
    {
        $this->load->model('PayBills_model');
        $date = $this->input->get('date');
        if($date){
            $sqlDate = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
        }
        $vendor = $this->input->get('vendor');
        $property = $this->input->get('property');
        $transactions = $this->PayBills_model->getTransactions($sqlDate, $this->input->get('vendor'), $this->input->get('property'));
        $accounts = $this->PayBills_model->getAccounts();
        foreach( $transactions as $transaction ){
            echo '<tr class="checkRow edit_bill_row" onclick="setAccountName($(this))">
            <td id="edit_bill_check">
                <input  class="editBill_input" type="hidden" name="applied_payments['.$transaction->property_id.']['. $transaction->id .'][applied]" value="0">
                <input type="hidden" name="applied_payments['.$transaction->property_id.']['. $transaction->id .'][transaction_id_b]" value="'.  $transaction->id .'"> 
                <i class="icon-check" id="edit_bill_icon_check" style="visibility :hidden" ></i>
                <input  class="pay_bill_input" type="hidden"   value="0">
            </td>

            <td> 
            <input type="hidden" id="transactionId" value="' .  $transaction->id  . '">
            <span>'.$transaction->transaction_ref .'</span>	 
            
            </td>
            <td>'. $transaction->default_bank.'</td>
            <td>
                <input type="hidden" class="paybill_property" value="'. $transaction->property_id  .'">
                <span>'. $transaction->name .'</span>	
            </td>
            <td id="description">'. $transaction->description .'</td>
            
            <td id="due_date">'. $transaction->due_date .'</td>
            <td class="text-center"><span class="text-left">$</span><span id="bill_amount">$' . number_format($transaction->amount, 2) .'</span></td>
            <td class="text-center"><span class="text-left">$</span> <span id="open_balance" class ="edit_bill_open">$' . number_format($transaction->open_balance, 2).' </span> </td>'.
            '<td class="edit_bill_row_input_amount">
            <span class="input-amount">
                 <label >$</label>
                    <input type="text" placeholder="0.00" id="edit_bill_input_amount" class="decimal checkAmount total"  name="applied_payments['.$transaction->property_id.']['. $transaction->id .'][amount]" >
            </td>
        </tr>';
        }
        echo '<style type="text/css" onload="payBill.pbcalcTotal($(this))"></style>';


    }

    public function getBalance()
    {
        $this->load->model('PayBills_model');
        $balance = $this->PayBills_model->getBalance($this->input->get('value'));       
        echo $balance;
    
    }

    function getTransactionsEdit()
    {
        $this->load->model('PayBills_model');
        $th_id = $this->input->get('th_id');
        $vendor = $this->input->get('vendor');
        $property_id = $this->input->get('property_id');
        $transactions = $this->PayBills_model->getTransactionsInEditNew($vendor, $th_id, $property_id);
        //$transactions = $this->PayBills_model->getTransactionsInEdit($th_id, $vendor);//,169870 fix front-end api and change function to new one
       // var_dump($transactions);
        foreach($transactions as $transaction){
            echo '<tr class="edit_bill_row">
            <td  id="edit_bill_check" >
            <input  class="editBill_input" type="hidden" name="applied_payments['. $transaction->id .'][applied]" value="'. (( $transaction->payment > 0   )  ? '1' : '0')  .'">
            <input type="hidden" name="applied_payments['. $transaction->id .'][transaction_id_b]" value="'.  $transaction->id .'"> 
            <i id="edit_bill_icon_check" style="visibility :'.  ((   $transaction->payment > 0   ) ? 'visible' : 'hidden')  .'" class="icon-check" aria-hidden="true"></i>
                <div class="shadow" style="width: 1215px;"></div>
            </td>
            <td>'.  $transaction->transaction_ref .'</td>
            <td>'. $transaction->account_name .'</td>
            <td>'. $transaction->property_name .'</td>
            <td>'. (( $transaction->description  )  ? $transaction->description : 'No Description')    .'</td>
            <td><span class="wrapper"><span class="text-left">$</span> <span class="edit_bill_open"> '. $transaction->open_balance .'  </span>   </span></td>

            <td class="edit_bill_row_input_amount">
               <span class="input-amount">
                    <label >$</label>
                    <input type="text" id="edit_bill_input_amount" 
                    
                        name="applied_payments['. $transaction->id .'][amount]"  
                        value="'. $transaction->payment .'" 
                        '.  (( $transaction->payment != 0  ) ? '' : 'disabled')  
                        .' > 
                </span>
            </td>
    </tr>';

        }
    }

    function getAllLCList() {
        $this->load->model('leases_model');
        $data = $this->leases_model->getLateChargeSetups();
        echo "<option value='0'></option>";
        foreach ($data as $lc) {
            echo "<option value='" . $lc->id . "'>" . $lc->name . "</option>";
        }
    }

    function getAllSelectsData(){
        $type = $_POST['type'];
        echo json_encode($this->site->getAllSelectsData($type));
    }

    function getInstantSearchData(){
        $type = $_POST['type'];
        echo json_encode($this->site->getInstantSearchData($type));
    }

    function saveSlickSettings($key = '') {
        $data = json_encode($this->input->post());
        $user_id = $this->session->userdata('user_id');
        $q = $this->db->get_where('slick_settings', Array('key'=>$key, "user_id" => $user_id));
        if($q->num_rows() > 0)
            $this->db->update('slick_settings',Array("settings" => $data), Array('key'=>$key, "user_id" => $user_id));
        else
            $this->db->insert('slick_settings',Array("key"=>$key, "settings" => $data, "user_id" => $user_id));
    }

    function getSlickSettings($key = '') {
        $user_id = $this->session->userdata('user_id');
        $q = $this->db->get_where('slick_settings', Array('key'=>$key, "user_id" => $user_id));
        if($q->num_rows() > 0)
            echo $q->row()->settings;
        else
            echo 'null';

    }
    
    function inviteusers() {
        $users = $this->input->post('users');
        $statuses = array();
        foreach($users as $user){
            $statuses[$user] = $this->inviteuser($user, 1);
        }
        echo json_encode($statuses);
    }

    function inviteuser($id, $multiple = null) {
		$this->load->model('tenantportal_model');
        $status = $this->tenantportal_model->sendRegisterEmail($id);
        $this->db->update('profiles', Array('invite_status' => 1), Array('id' => $id));
        if($multiple == 1){
            return $status;
        } else {
            echo json_encode($status);
        }
    	
	}

	function deleteuser($id) {
		$this->load->model('tenantportal_model');
		$status = $this->tenantportal_model->deleteTenant($id);
		$this->db->update('profiles', Array('invite_status' => 0), Array('id' => $id));
		echo json_encode($status);
	}
}
