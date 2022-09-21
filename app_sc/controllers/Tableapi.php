<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tableapi extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function getProfileInfo($id){
        $this->load->model('tenants_model');
        $result = $this->tenants_model->getTenant($id);
        echo json_encode($result);
    }

    function getProfileRow($tab, $percentage, $owner_id, $spot)
    {
        $this->load->model('tenants_model');
        $result2 = $this->input->post();;
        $result = $this->tenants_model->getTenant($owner_id);
        $ind = 0;
        switch ($tab) {
            case 'owners' :
                // $data = ['<i class="icon-user"></i>
                //         <input name="owners[o'.$this->input->post('temp')['ind'].'][profile_id]" type="hidden" value="' . $this->input->post('temp')['profile_id'] . '">
                //         <input name="owners[o'.$this->input->post('temp')['ind'].'][percentage]" type="hidden" value="' . $this->input->post('temp')['percentage'] . '">',
                //         $this->input->post('temp')['percentage'], $result->first_name . ' ' . $result->last_name, $result->address_line_1, $result->email, $result->phone,
                //         '<a href="" class="delete-row"><i class="icon-x"></i> <span>Remove</span></a>'];
                 $data = ['<tr class="editOwner"><td><i class="icon-user"></i>
                          
                          <td class="dt-percentage"><input id="percentage" name="owners[o'.$spot.'][percentage]" type="hidden" value="' . $percentage . '">'.
                          $percentage . '</td><td style="overflow: visible"><input id="profile_id" name="owners[o'.$spot.'][profile_id]" type="hidden" value="' . $owner_id . '">'. $result->first_name . ' ' . $result->last_name . '</td><td class="ownerAddress">' . $result->address_line_1 . ' ' . $result->address_line_2 . '</td><td class="dt-email">' . $result->email . '</td><td class="dt-phone">' . $result->phone . '</td>
                          <td><a href="#" class="delete2"><i class="icon-x"></i></a></td></tr>'];
                $styles = ['', 'dt-percentage', '', '', 'dt-email', 'dt-phone', 'link-icon dt-delete'];
                break;
            case 'tenants' :
                $data = ['<i class="icon-user"></i>
                        <input name="owners[o'.$this->input->post('temp')['ind'].'][profile_id]" type="hidden" value="' . $this->input->post('temp')['profile_id'] . '">
                        <input name="owners[o'.$this->input->post('temp')['ind'].'][percentage]" type="hidden" value="' . $this->input->post('temp')['percentage'] . '">',
                    $this->input->post('temp')['percentage'], $result->first_name . ' ' . $result->last_name, $result->address_line_1, $result->email, $result->phone,
                    '<a href="" class="delete-row"><i class="icon-x"></i> <span>Remove</span></a>'];
                $styles = ['', 'dt-percentage', '', '', 'dt-email', 'dt-phone', 'link-icon dt-delete'];
                break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

    function getTaxesRow($tab)
    {
        $result = $this->input->post('temp');
        $humanDate = humanDate($result['last_pay_date']);
        $frequency =explode("+", $result['frequency']);
        $frequencyId=$frequency[0];
        $frequencyName=$frequency[1];
        $allocate_by =explode("+", $result['allocate_by']);
        $allocate_byId=$allocate_by[0];
        $allocate_byName=$allocate_by[1];
        switch ($tab) {
            case 'taxes' :
            $data = ['<input name="taxes[t'.$this->input->post('temp')['ind'].'][borough]" type="hidden" value="' . $result['borough'] . '">'. $result['borough'],
                    '<input name="taxes[t'.$this->input->post('temp')['ind'].'][block]" type="hidden" value="' . $result['block'] . '">'. $result['block'],
                    '<input name="taxes[t'.$this->input->post('temp')['ind'].'][lot]" type="hidden" value="' . $result['lot'] . '">'. $result['lot'],
                    '<input name="taxes[t'.$this->input->post('temp')['ind'].'][frequency]" type="hidden" value="' . $frequencyId . '">'. $frequencyName,
                    '<input name="taxes[t'.$this->input->post('temp')['ind'].'][payment_acct]" type="hidden" value="' . $result['payment_acct'] . '">'. $result['payment_acct'],
                    '<input name="taxes[t'.$this->input->post('temp')['ind'].'][assessment]" type="hidden" value="' . $result['assessment'] . '">'. $result['assessment'],
                    '<input name="taxes[t'.$this->input->post('temp')['ind'].'][allocate_by]" type="hidden" value="' . $allocate_byId . '">'. $allocate_byName,
                    '<input name="taxes[t'.$this->input->post('temp')['ind'].'][payee]" type="hidden" value="' . $result['payee'] . '">'. $result['payee'],
                    '<input name="taxes[t'.$this->input->post('temp')['ind'].'][last_pay_date]" type="hidden" value="' . $result['last_pay_date'] . '">'. $humanDate,
                    '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
                $styles = ['', '', '', '', '', '', '', '', '', 'dt-delete'];
                break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

    function getUtilitiesRow($tab)
    {
        $result = $this->input->post('temp');
        $utility_type =explode("+", $result['utility_type']);
        $utility_typeId=$utility_type[0];
        $utility_typeName=$utility_type[1];
        $account =explode("+", $result['default_expense_acct']);
        $accountId=$account[0];
        $accountName=$account[1];
        $unit =explode("+", $result['unit_id']);
        $unitId=$unit[0];
        $unitName=$unit[1];
        $payee =explode("+", $result['payee']);
        $payeeId=$payee[0];
        $payeeName=$payee[1];
        $paidBy =explode("+", $result['paid_by']);
        $paidById=$paidBy[0];
        $paidByName=$paidBy[1];
        $active = "";
        $checked = "";
        if($result['direct_payment'] == 1){$active = "active"; $checked = "checked";}
        switch ($tab) {
            case 'utilities' :
            $data = ['<input name="utilities[u'.$this->input->post('temp')['ind'].'][utility_type]" type="hidden" value="' . $utility_typeId . '">'. $utility_typeName,
                    '<input name="utilities[u'.$this->input->post('temp')['ind'].'][unit_id]" type="hidden" value="' . $unitId . '">'. $unitName,
                    '<input name="utilities[u'.$this->input->post('temp')['ind'].'][description]" type="hidden" value="' . $result['description'] . '">'. $result['description'],
                    '<input name="utilities[u'.$this->input->post('temp')['ind'].'][account]" type="hidden" value="' . $result['account'] . '">'. $result['account'],
                    '<input name="utilities[u'.$this->input->post('temp')['ind'].'][meter]" type="hidden" value="' . $result['meter'] . '">'. $result['meter'],
                    '<input name="utilities[u'.$this->input->post('temp')['ind'].'][default_expense_acct]" type="hidden" value="' . $accountId . '">'. $accountName,
                    '<input name="utilities[u'.$this->input->post('temp')['ind'].'][payee]" type="hidden" value="' . $payeeId . '">'. $payeeName,
                    '<input name="utilities[u'.$this->input->post('temp')['ind'].'][direct_payment]" type="hidden" value="' . (($result['direct_payment']) ? 1 : '') . '">
                    <ul class="check-a a">
                        <li><label for="direct_payment" class="checkbox '.$active.'"><input type="hidden" name="utilities[u'.$this->input->post('temp')['ind'].'][direct_payment]" value="0" /><input type="checkbox" value="1" '.$checked.' id="" name="utilities[u'.$this->input->post('temp')['ind'].'][direct_payment]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                    </ul>',
                    '<input name="utilities[u'.$this->input->post('temp')['ind'].'][paid_by]" type="hidden" value="' . $paidById . '">'. $paidByName,
                    '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
            $styles = ['','','', '', '', '', '', '','', 'dt-delete'];
            break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

    function getUnitUtilitiesRow($tab)
    {
        $result = $this->input->post('temp');
        $utility_type =explode("+", $result['utility_type']);
        $utility_typeId=$utility_type[0];
        $utility_typeName=$utility_type[1];
        $account =explode("+", $result['default_expense_acct']);
        $accountId=$account[0];
        $accountName=$account[1];
        $payee =explode("+", $result['payee']);
        $payeeId=$payee[0];
        $payeeName=$payee[1];
        $active = "";
        $checked = "";
        if($result['direct_payment'] == 1){$active = "active"; $checked = "checked";}
        switch ($tab) {
            case 'utilities' :
            $data = ['<input name="utilities[uu'.$this->input->post('temp')['ind'].'][utility_type]" type="hidden" value="' . $utility_typeId . '">'. $utility_typeName,
                    '<input name="utilities[uu'.$this->input->post('temp')['ind'].'][description]" type="hidden" value="' . $result['description'] . '">'. $result['description'],
                    '<input name="utilities[uu'.$this->input->post('temp')['ind'].'][account]" type="hidden" value="' . $result['account'] . '">'. $result['account'],
                    '<input name="utilities[uu'.$this->input->post('temp')['ind'].'][meter]" type="hidden" value="' . $result['meter'] . '">'. $result['meter'],
                    '<input name="utilities[uu'.$this->input->post('temp')['ind'].'][default_expense_acct]" type="hidden" value="' . $accountId . '">'. $accountName,
                    '<input name="utilities[uu'.$this->input->post('temp')['ind'].'][payee]" type="hidden" value="' . $payeeId . '">'. $payeeName,
                    '<input name="utilities[uu'.$this->input->post('temp')['ind'].'][direct_payment]" type="hidden" value="' . (($result['direct_payment']) ? 1 : '') . '">
                    <ul class="check-a a">
                        <li><label for="direct_payment" class="checkbox '.$active.'"><input type="hidden" name="utilities[uu'.$this->input->post('temp')['ind'].'][direct_payment]" value="0" /><input type="checkbox" value="1" '.$checked.' id="" name="utilities[uu'.$this->input->post('temp')['ind'].'][direct_payment]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                    </ul>',
                    '<input name="utilities[uu'.$this->input->post('temp')['ind'].'][paid_by]" type="hidden" value="' . $result['paid_by'] . '">'. $result['paid_by'],
                    '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
            $styles = ['','', '', '', '', '', '','', 'dt-delete'];
            break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

    function getInsuranceRow($tab)
    {
        $result = $this->input->post('temp');
        $humanDate1 = humanDate($result['start_date']);
        $humanDate2 = humanDate($result['end_date']);
        $payment_acct =explode("+", $result['payment_acct']);
        $payment_acctId=$payment_acct[0];
        $payment_acctName=$payment_acct[1];
        $broker =explode("+", $result['broker']);
        $brokerId=$broker[0];
        $brokerName=$broker[1];
        switch ($tab) {
            case 'insurance' :
            $data = ['<input name="insurance[i'.$this->input->post('temp')['ind'].'][policy]" type="hidden" value="' . $result['policy'] . '">'. $result['policy'],
                    '<input name="insurance[i'.$this->input->post('temp')['ind'].'][company]" type="hidden" value="' . $result['company'] . '">'. $result['company'],
                    '<input name="insurance[i'.$this->input->post('temp')['ind'].'][price]" type="hidden" value="' . $result['price'] . '">'. $result['price'],
                    '<input name="insurance[i'.$this->input->post('temp')['ind'].'][start_date]" type="hidden" value="' . $result['start_date'] . '">'. $humanDate1,
                    '<input name="insurance[i'.$this->input->post('temp')['ind'].'][end_date]" type="hidden" value="' . $result['end_date'] . '">'. $humanDate2,
                    '<input name="insurance[i'.$this->input->post('temp')['ind'].'][policy_type]" type="hidden" value="' . $result['policy_type'] . '">'. $result['policy_type'],
                    '<input name="insurance[i'.$this->input->post('temp')['ind'].'][coverage]" type="hidden" value="' . $result['coverage'] . '">'. $result['coverage'],
                    '<input name="insurance[i'.$this->input->post('temp')['ind'].'][payment_acct]" type="hidden" value="' . $payment_acctId . '">'. $payment_acctName,
                    '<input name="insurance[i'.$this->input->post('temp')['ind'].'][broker]" type="hidden" value="' . $brokerId . '">'. $brokerName,
                    '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
            $styles = ['', '', '', '', '', '', '', '', '', 'dt-delete'];
            break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

    function getPeoplesRow($tab)
    {
        $this->load->model('tenants_model');
        $result = $this->input->post('temp');
        switch ($tab) {
            case 'peoples' :
                $data = ["", $result['amount'], $result['name'], $result['address'], $result['unit'], $result['start_date'],$result['end_date'],$result['notes'], '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
                $styles = ['', '', '', '', '', '', '','', 'dt-delete'];
                break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

        function getVendorsRow($tab)
    {
        //$this->load->model('tenants_model');
        $result = $this->input->post('temp');
        switch ($tab) {
            case 'contact' :
                // $data = ["", $result['first_name'], $result['last_name'], $result['relationship'], $result['home_phone'], $result['cell'],$result['work_phone'],$result['ext'],$result['email'], '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
                // $styles = ['', '', '', '', '', '', '','','', 'dt-delete'];

                $data = ['<input name="contact[c'.$this->input->post('temp')['ind'].'][first_name]" type="hidden" value="' . $result['first_name'] . '">'. $result['first_name'],
                    '<input name="contact[c'.$this->input->post('temp')['ind'].'][last_name]" type="hidden" value="' . $result['last_name'] . '">'. $result['last_name'],
                    '<input name="contact[c'.$this->input->post('temp')['ind'].'][relation]" type="hidden" value="' . $result['relation'] . '">'. $result['relation'],
                    '<input name="contact[c'.$this->input->post('temp')['ind'].'][home]" type="hidden" value="' . $result['home'] . '">'. $result['home'],
                    '<input name="contact[c'.$this->input->post('temp')['ind'].'][cell]" type="hidden" value="' . $result['cell'] . '">'. $result['cell'],
                    '<input name="contact[c'.$this->input->post('temp')['ind'].'][work]" type="hidden" value="' . $result['work'] . '">'. $result['work'],
                    '<input name="contact[c'.$this->input->post('temp')['ind'].'][ext]" type="hidden" value="' . $result['ext'] . '">'. $result['ext'],
                    '<input name="contact[c'.$this->input->post('temp')['ind'].'][email]" type="hidden" value="' . $result['email'] . '">'. $result['email'],
                    '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
                    $styles = ['','','', '', '', '', '', '', 'dt-delete'];
                break;
            case 'address' :

                $data = ['<input name="address[a'.$this->input->post('temp')['ind'].'][profile_id]" type="hidden" value="' . $result['profile_id'] . '">
                    <input name="address[a'.$this->input->post('temp')['ind'].'][address_line_1]" type="hidden" value="' . $result['address_line_1'] . '">'. $result['address_line_1'],
                    '<input name="address[a'.$this->input->post('temp')['ind'].'][address_line_2]" type="hidden" value="' . $result['address_line_2'] . '">'. $result['address_line_2'],
                    '<input name="address[a'.$this->input->post('temp')['ind'].'][city]" type="hidden" value="' . $result['city'] . '">'. $result['city'],
                    '<input name="address[a'.$this->input->post('temp')['ind'].'][state]" type="hidden" value="' . $result['state'] . '">'. $result['state'],
                    '<input name="address[a'.$this->input->post('temp')['ind'].'][zip]" type="hidden" value="' . $result['zip'] . '">'. $result['zip'],
                    '<input name="address[a'.$this->input->post('temp')['ind'].'][apt]" type="hidden" value="' . $result['apt'] . '">'. $result['apt'],
                    '<input name="address[a'.$this->input->post('temp')['ind'].'][country]" type="hidden" value="' . $result['country'] . '">'. $result['country'],
                    '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
                    $styles = ['','', '', '', '', '', '', 'dt-delete'];
                break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

    function getmanagementsRow($tab){
        $humanDate1 = humanDate($result['start_date']);
        $humanDate2 = humanDate($result['end_date']);
        $result = $this->input->post('temp');
        $vendor =explode("+", $result['vendor']);
        $vendorId=$vendor[0];
        $vendorName=$vendor[1];
        //$account =explode("+", $result['account_id']);
        //$accountId=$account[0];
        //$accountName=$account[1];
        $unit =explode("+", $result['unit_id']);
        $unitId=$unit[0];
        $unitName=$unit[1];
        $frequency =explode("+", $result['frequency']);
        $frequencyId=$frequency[0];
        $frequencyName=$frequency[1];
        $item =explode("+", $result['item_id']);
        $itemId=$item[0];
        $itemName=$item[1];
        $active = "";
        $checked = "";
        if($result['percentage_fixed'] == 1){$active = "active"; $checked = "checked";}
        if($result['all_props'] == 1){$pactive = "active"; $pchecked = "checked";}
        $accountArray = [];
        foreach($result['account_id'] as $account){array_push($accountArray, $account);}
        switch ($tab) {
            case 'managements' :

            $data = ['<input name="managements[m'.$this->input->post('temp')['ind'].'][frequency]" type="hidden" value="' . $frequencyId . '">'. $frequencyName,
                    '<input name="managements[m'.$this->input->post('temp')['ind'].'][vendor]" type="hidden" value="' . $vendorId . '">'. $vendorName,
                    '<input name="managements[m'.$this->input->post('temp')['ind'].'][unit_id]" type="hidden" value="' . $unitId . '">'. $unitName,
                    '<input name="managements[m'.$this->input->post('temp')['ind'].'][amount]" type="hidden" value="' . $result['amount'] . '">'. $result['amount'],
                    '<input name="managements[m'.$this->input->post('temp')['ind'].'][start_date]" type="hidden" value="' . $result['start_date'] . '">'. $humanDate1,
                    '<input name="managements[m'.$this->input->post('temp')['ind'].'][end_date]" type="hidden" value="' . $result['end_date'] . '">'. $humanDate2,
                    '<input name="managements[m'.$this->input->post('temp')['ind'].'][item_id]" type="hidden" value="' . $itemId . '">'. $itemName,
                    
                    '<ul class="check-a a">
                        <li><label for="percentage_fixed" class="checkbox '.$active.'"><input type="hidden" name="managements[m'.$this->input->post('temp')['ind'].'][percentage_fixed]" value="0" /><input type="checkbox" value="1" '.$checked.' id="" name="managements[m'.$this->input->post('temp')['ind'].'][percentage_fixed]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                    </ul>',
    
                    '<ul class="check-a a">
                        <li><label for="all_accounts" class="checkbox '.$pactive.'"><input type="hidden" name="managements[m'.$this->input->post('temp')['ind'].'][all_accounts]" value="0" /><input type="checkbox" value="1" '.$pchecked.' id="" name="managements[m'.$this->input->post('temp')['ind'].'][all_accounts]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                    </ul><input name="managements[m'.$this->input->post('temp')['ind'].'][account_id]" type="hidden" value="' . json_encode($accountArray) . '"><button id="chooseAccounts">Multiple</button>',
                    '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
                    $styles = ['','','', '', '', '', '', '', 'dt-delete'];
            break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

    function getSection8Row($tab)
    {
        $result = $this->input->post('temp');
        $humanDate1 = humanDate($result['start_date']);
        $humanDate2 = humanDate($result['end_date']);
        switch ($tab) {
            case 'section8' :
            $data = ['<input name="sect_8[s'.$this->input->post('temp')['ind'].'][voucher_amount]" type="hidden" value="' . $result['voucher_amount'] . '">'. $result['voucher_amount'],
                    '<input name="sect_8[s'.$this->input->post('temp')['ind'].'][voucher_num]" type="hidden" value="' . $result['voucher_num'] . '">'. $result['voucher_num'],
                    '<input name="sect_8[s'.$this->input->post('temp')['ind'].'][start_date]" type="hidden" value="' . $result['start_date'] . '">'. $humanDate1,
                    '<input name="sect_8[s'.$this->input->post('temp')['ind'].'][end_date]" type="hidden" value="' . $result['end_date'] . '">'. $humanDate2,
                    '<input name="sect_8[s'.$this->input->post('temp')['ind'].'][notes]" type="hidden" value="' . $result['notes'] . '">'. $result['notes'],
                    '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
            $styles = ['', '', '', '', '', 'dt-delete'];
            break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

    function getRsRow($tab)
    {
        $result = $this->input->post('temp');
        $humanDate1 = humanDate($result['start_date']);
        $humanDate2 = humanDate($result['end_date']);
        switch ($tab) {
            case 'rs' :
            $data = ['<input name="rs[rs'.$this->input->post('temp')['ind'].'][legal_rent]" type="hidden" value="' . $result['legal_rent'] . '">'. $result['legal_rent'],
                    '<input name="rs[rs'.$this->input->post('temp')['ind'].'][preferencial_rent]" type="hidden" value="' . $result['preferencial_rent'] . '">'. $result['preferencial_rent'],
                    '<input name="rs[rs'.$this->input->post('temp')['ind'].'][start_date]" type="hidden" value="' . $result['start_date'] . '">'. $humanDate1,
                    '<input name="rs[rs'.$this->input->post('temp')['ind'].'][end_date]" type="hidden" value="' . $result['end_date'] . '">'. $humanDate2,
                    '<input name="rs[rs'.$this->input->post('temp')['ind'].'][notes]" type="hidden" value="' . $result['notes'] . '">'. $result['notes'],
                    '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
            $styles = ['', '', '', '', '', 'dt-delete'];
            break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

        function getRenewalRow($tab)
    {
        $result = $this->input->post('temp');
        $humanDate1 = humanDate($result['start_date']);
        $humanDate2 = humanDate($result['end_date']);
        switch ($tab) {
            case 'renewal' :
            $data = ['<input name="renewal[renewal'.$this->input->post('temp')['ind'].'][renewal_form]" type="hidden" value="' . $result['renewal_form'] . '">'. $result['renewal_form'],
                    '<input name="renewal[renewal'.$this->input->post('temp')['ind'].'][rent]" type="hidden" value="' . $result['rent'] . '">'. $result['rent'],
                    '<input name="renewal[renewal'.$this->input->post('temp')['ind'].'][sd]" type="hidden" value="' . $result['sd'] . '">'. $result['sd'],
                    '<input name="renewal[renewal'.$this->input->post('temp')['ind'].'][lmr]" type="hidden" value="' . $result['lmr'] . '">'. $result['lmr'],
                    '<input name="renewal[renewal'.$this->input->post('temp')['ind'].'][start_date]" type="hidden" value="' . $result['start_date'] . '">'. $humanDate1,
                    '<input name="renewal[renewal'.$this->input->post('temp')['ind'].'][end_date]" type="hidden" value="' . $result['end_date'] . '">'. $humanDate2,
                    '<input name="renewal[renewal'.$this->input->post('temp')['ind'].'][notes]" type="hidden" value="' . $result['notes'] . '">'. $result['notes'],
                    '<a href="" class="delete-row"><i class="icon-x"></i></a>'];
            $styles = ['', '', '', '', '', 'dt-delete'];
            break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

    function getIn_courtRow($tab)
    {
        $result = $this->input->post('temp');
        $name = explode("+", $result['tenants_on_lease_name']);
        $humanDate = humanDate($result['follow_up_date']);
        $tenantId = $name[0];
        $tenantName = $name[1];
        $active = "";
        $checked = "";
        if($result['warrant_requested'] == 1){$active = "active"; $checked = "checked";}
        $wactive = "";
        $wchecked = "";
        if($result['warrant_issued'] == 1){$wactive = "active"; $wchecked = "checked";}
        switch ($tab) {
            case 'in_court' :
            $data = ['<input name="in_court[ic'.$this->input->post('temp')['ind'].'][profile_id]" type="hidden" value="' . $tenantId . '">'. $tenantName,
                    '<input name="in_court[ic'.$this->input->post('temp')['ind'].'][case_num]" type="hidden" value="' . $result['case_num'] . '">'. $result['case_num'],
                    '<input name="in_court[ic'.$this->input->post('temp')['ind'].'][attorney]" type="hidden" value="' . $result['attorney'] . '">'. $result['attorney'],
                    '<input name="in_court[ic'.$this->input->post('temp')['ind'].'][follow_up_date]" type="hidden" value="' . $result['follow_up_date'] . '">'. $humanDate,
                    '<input name="in_court[ic'.$this->input->post('temp')['ind'].'][follow_up_reason]" type="hidden" value="' . $result['follow_up_reason'] . '">'. $result['follow_up_reason'],
                    '<ul class="check-a a">
                        <li><label for="warrant_requested" class="checkbox '.$active.'"><input type="hidden" name="in_court[ic'.$this->input->post('temp')['ind'].'][warrant_requested]" value="0" /><input type="checkbox" value="1" '.$checked.' id="" name="in_court[ic'.$this->input->post('temp')['ind'].'][warrant_requested]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                    </ul>',
                    '<ul class="check-a a">
                        <li><label for="warrant_issued" class="checkbox '.$wactive.'"><input type="hidden" name="in_court[ic'.$this->input->post('temp')['ind'].'][warrant_issued]" value="0" /><input type="checkbox" value="1" '.$wchecked.' id="" name="in_court[ic'.$this->input->post('temp')['ind'].'][warrant_issued]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                    </ul>',
                    '<a href="" class="delete-row" style="width: 2%;><i class="icon-x"></i></a>'];
            $styles = ['', '', '', '', '', '', '', 'dt-delete'];
            break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

    function getAutoChargesRow($tab)
    {
        $result = $this->input->post('temp');
        $humanDate = humanDate($result['next_trans_date']);
        $humanDate2 = humanDate($result['start_date']);
        $humanDate3 = humanDate($result['end_date']);
        $profile_id = explode("+", $result['profile_id']);
        $nameId = $profile_id[0];
        $nameName = $profile_id[1];
        $items = explode("+", $result['item_type_id']);
        $itemId = $items[0];
        $itemName = $items[1];
        $frequency = explode("+", $result['frequency']);
        $frequencyId = $frequency[0];
        $frequencyName = $frequency[1];
        $active = "";
        $checked = "";
        if($result['auto'] == 1){$active = "active"; $checked = "checked";}
        switch ($tab) {
            case 'autoCharges' :
            $data = ['<input name="autoCharges[ac'.$this->input->post('temp')['ind'].'][profile_id]" type="hidden" value="' . $nameId . '">'. $nameName,
                    '<input name="autoCharges[ac'.$this->input->post('temp')['ind'].'][name]" type="hidden" value="' . $result['name'] . '">'. $result['name'],
                    '<input name="autoCharges[ac'.$this->input->post('temp')['ind'].'][start_date]" type="hidden" value="' . $result['start_date'] . '">'. $humanDate2,
                    '<input name="autoCharges[ac'.$this->input->post('temp')['ind'].'][end_date]" type="hidden" value="' . $result['end_date'] . '">'. $humanDate3,
                    '<input name="autoCharges[ac'.$this->input->post('temp')['ind'].'][next_trans_date]" type="hidden" value="' . $result['next_trans_date'] . '">'. $humanDate,
                    '<input name="autoCharges[ac'.$this->input->post('temp')['ind'].'][amount]" type="hidden" value="' . $result['amount'] . '">'. $result['amount'],
                    '<input name="autoCharges[ac'.$this->input->post('temp')['ind'].'][item_type_id]" type="hidden" value="' . $itemId . '">'. $itemName,
                    '<input name="autoCharges[ac'.$this->input->post('temp')['ind'].'][frequency]" type="hidden" value="' . $frequencyId . '">'. $frequencyName,
                    '<ul class="check-a a">
                        <li><label for="auto" class="checkbox '.$active.'"><input type="hidden" name="autoCharges[ac'.$this->input->post('temp')['ind'].'][auto]" value="0" /><input type="checkbox" value="1" '.$checked.' id="" name="autoCharges[ac'.$this->input->post('temp')['ind'].'][auto]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                    </ul>',
                    '<a href="" class="delete-row" style="width: 2%;><i class="icon-x"></i></a>'];
            $styles = ['', '', '', '', '', '', '','','', 'dt-delete'];
            break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }

    function getkey_codes($tab)
    {
        $result = $this->input->post('temp');
        $ind = 0;
        switch ($tab) {
            case 'key_code' :
                $data = ['<input id="area" name="key_codes[kc'.$this->input->post('temp')['ind'].'][area]" type="hidden" value="' . $this->input->post('temp')['area'] . '">'. $result['area'],
                        '<input id="key_code" name="key_codes[kc'.$this->input->post('temp')['ind'].'][key_code]" type="hidden" value="' . $this->input->post('temp')['key_code'] . '">'. $result['key_code'],
                        '<a href="" class="delete-row"><i class="icon-x"></i> <span>Remove</span></a>'];
                $styles = ['', '', '', '', 'link-icon dt-delete'];
                break;
        }
        echo json_encode(Array('data' => $data, 'styles' => $styles));
    }
}
