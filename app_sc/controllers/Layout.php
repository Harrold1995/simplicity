<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Layout extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('notes_model');
        $this->load->model('maintenance_model');
        $this->sd = $this->site->settings->security_deposits;
        $this->lmr = $this->site->settings->lmr;
    }

    function getLeftData($type=""){
        $this->result = array();
        switch($type){
            case 'property' :
                $this->load->model('properties_model');
                $result = $this->properties_model->getPropertiesList();
                $cols = Array(Array("title"=>"Property","width" => 400), Array("title"=>"Units","width" => 200));
                break;
            case 'unit' :
                $this->load->model('units_model');
                $result = $this->units_model->getUnitsList();
                $cols = Array(Array("title"=>"Unit","width" => 300), Array("title"=>"Properties","width" => 300));
                break;
            case 'tenant' :
                $this->load->model('tenants_model');
                $result = $this->tenants_model->getTenantsList();
                $cols = Array(Array("title"=>"Tenant","width" => 400), Array("title"=>"Balance","width" => 200));
                break;
            case 'lease' :
                $this->load->model('leases_model');
                $result = $this->leases_model->getLeasesList();
                $cols = Array(Array("title"=>"Property","width" => 400),Array("title"=>"Unit","width" => 100), Array("title"=>"Lease Dates","width" => 400,"class"=>"greenTd"));
                break;
            case 'account' :
                $this->load->model('accounts_model');
                $result = $this->accounts_model->getAccountsList();
                $cols = Array(Array("title"=>"Account","width" => 400),Array("title"=>"Type","width" => 100), Array("title"=>"Balance","width" => 300));
                break;
            case 'inventory' :
                $this->load->model('inventory_model');
                $item_types = $this->settings->item_types;
                $result = $this->inventory_model->getinventoryList($item_types);
                $cols = Array(Array("title"=>"Name","width" => 400),Array("title"=>"Type","width" => 400));
                break;
            case 'vendors' :
                $this->load->model('vendors_model');
                $result = $this->vendors_model->getVendorsList(true);
                $cols = Array(Array("title"=>"Name","width" => 400),Array("title"=>"Balance","width" => 200));
                break;
            case 'timesheet' :
                $this->load->model('timesheet_model');
                $result = $this->timesheet_model->getEmployeesList();
                //print_r($result);
                $cols = Array(Array("title"=>"Employee","width" => 400));
                break;
        }
        $this->colnumber = count($cols);
        $this->recursive($result, null, 0, 0);
        echo json_encode((Object)Array('data'=>$this->result, 'cols' => $cols));
    }

    public function recursive($data, $parent, $id, $indent)
    {
        foreach($data as $item){
            if($item->name == null) continue;
            $icon='';
            if($this->icons[$item->type]) $icon = '<i class="'.$this->icons[$item->type].'"></i>';
            $temp = Array("col0" => $item->name, "col1" => $item->info, "parent" => $parent, "id" => "id_".$id, "indent" => $indent, "_collapsed" => true, "dmode" => "edit", "dtype" => $item->type, "did" => $item->id, "active" => $item->active, "icon" => $icon);
            if($item->type == 'lease')
                $temp['lstatus'] = $item->lstatus;
            if($item->type == 'tenant')
                $temp['lid'] = $item->lid;
            if($item->info2)
                $temp['col2'] = $item->info2;
            //$temp['active'] = mt_rand(0, 1);
            $this->result[] = (Object)$temp;
            if(isset($item->children) && (count($item->children) > 0)) $id = $this->recursive($item->children, $id, $id+1, $indent + 1); else
                $id++;
        }
        return $id;
    }

    function getLeftColumn()
    {
        $this->data['icons'] = $this->settings->tree_icons;
        switch ($this->input->get('type')) {
            case 'property' :
                $this->getPropertiesLeft();
                break;
            case 'unit' :
                $this->getUnitsLeft();
                break;
            case 'tenant' :
                $this->getTenantsLeft();
                break;
            case 'lease' :
                $this->getLeasesLeft();
                break;
            case 'account' :
                $this->getAccountsLeft();
                break;
            case 'inventory' :
                $this->getInventoryLeft();
                break;
            case 'settings' :
                $this->getSettingsLeft();
                break;
            case 'vendors' :
                $this->getVendorsLeft();
                break;
            case 'timesheet' :
                $this->getTimesheetLeft();
                break;
			case 'maintenance' :
				$this->getMaintenanceLeft();
				break;
        }
    }

    function getRightColumn()
    {
        $this->load->model('documents_model');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        switch ($this->input->get('type')) {
            case 'property' :
                $this->getPropertiesRight();
                break;
            case 'unit' :
                $this->getUnitsRight();
                break;
            case 'tenant' :
                $this->getTenantsRight();
                break;
            case 'lease' :
                $this->getLeasesRight();
                break;
            case 'account' :
                $this->getAccountsRight($id);
                break;
            case 'inventory' :
                $this->getInventoryRight($id);
                break;
            case 'vendors' :
                $this->getVendorsRight($id);
                break;
            case 'employees' :
                $this->getTimesheetRight($id);
                break;
			case 'recordsets' :
				$this->getRecordsetsRight();
				break;
			case 'maintenance' :
				$this->getMaintenanceRight($id);
				break;
        }
    }

    function getPropertiesLeft()
    {
        //$this->load->model('properties_model');
        //$this->data['properties'] = $this->properties_model->getPropertiesList();
        $this->content_construct('properties/properties-left', $this->data);
    }

    function getPropertiesRight()
    {   
        if (!$this->permissions->checkPermissions('properties_properties_view', TRUE)) return;
        $this->load->model('properties_model');
        $this->data['property'] = $this->properties_model->getProperty($this->input->get('id'));
        $this->data['notes'] = $this->notes_model->getNotes($this->input->get('id'),1);//number corresponds to database
        $this->data['maintenance'] = $this->maintenance_model->getMaintenance($this->input->get('id'),1);//number corresponds to database
        $this->data['addNoteForm'] = $this->notes_model->addNoteForm($this->input->get('id'),$this->input->get('type'));
        $this->data['propertyBank'] = $this->properties_model->getPropertyBank($this->input->get('id'));
        $this->data['bankBalance'] = $this->properties_model->getPropertyBankTotal($this->input->get('id'));
        $this->data['unitsCount'] = $this->properties_model->getUnitsCount($this->input->get('id'));
        $this->data['getPropertyRentTotal'] = $this->properties_model->getPropertyRentTotal($this->input->get('id'));
        $this->data['ytdProfit'] = $this->properties_model->ytdProfit($this->input->get('id'));
        $this->data['allIn'] = $this->properties_model->allIn($this->input->get('id'));
        $this->data['investorIn'] = $this->properties_model->investorIn($this->input->get('id'));
        $this->data['MortgagesTot'] = $this->properties_model->MortgagesTot($this->input->get('id'));
        $this->data['vacancyCount'] = $this->properties_model->getVacancyCount($this->input->get('id'));
        $this->data['getFutureVacancyCount'] = $this->properties_model->getFutureVacancyCount($this->input->get('id'));
        //$this->data['singlePropertyTransactions'] = $this->properties_model->getSinglePropertyTransactions($this->input->get('id'));
        $this->data['documents'] = $this->documents_model->getDocuments($this->input->get('id'),1);//number corresponds to database
        //$this->data['documentsInfo'] = $this->documents_model->getTenant($this->input->get('id'));
        $this->data['documentsInfo'] = $this->documents_model->getDocumentsProperty($this->input->get('id'));
        $this->content_construct('properties/properties-right', $this->data);
        
    }

    function getUnitsLeft()
    {
        //$this->load->model('units_model');
        //$this->load->model('properties_model');
        //$this->data['units'] = $this->units_model->getUnitsList();
        $this->content_construct('properties/units-left', $this->data);
    }

    function getUnitsRight()
    {
        $this->load->model('units_model');
        $this->load->model('properties_model');
        $this->data['unit'] = $this->units_model->getUnit($this->input->get('id'));
        $this->data['unitType'] = $this->units_model->getUnitType($this->input->get('id'));
        $this->data['lease'] = $this->units_model->getLeaseAmount($this->input->get('id'));
        $this->data['property'] = $this->properties_model->getProperty($this->data['unit']->property_id);
        //$this->data['getSingleUnitTransactions'] = $this->units_model->getSingleUnitTransactions($this->input->get('id'));
        //$this->data['utilities'] = $this->units_model->getUnitUtilities($this->input->get('id'));
        //$this->data['accounts'] = $this->properties_model->getAllAccounts();
        //$this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
        //$this->data['vendors'] = $this->properties_model->getVendors();
        //$this->data['utilityTypes'] = $this->properties_model->getutilityTypes();
        $this->data['notes'] = $this->notes_model->getNotes($this->input->get('id'),3);//number corresponds to database
        $this->data['addNoteForm'] = $this->notes_model->addNoteForm($this->input->get('id'),$this->input->get('type'));
        $this->data['documents'] = $this->documents_model->getDocuments($this->input->get('id'),3);//number corresponds to database
        //$this->data['documentsProperty'] = $this->documents_model->getDocumentsProperty($this->data['unit']->property_id);
        $this->data['documentsInfo'] = $this->documents_model->getDocumentsUnit($this->input->get('id'));
        //$this->data['documentsUnit'] = $this->documents_model->getDocumentsProperty($this->input->get('id'));
        $this->content_construct('properties/units-right', $this->data);
    }

    function getTenantsLeft()
    {
        //$this->load->model('tenants_model');
        //$this->data['tenants'] = $this->tenants_model->getTenantsList();
        $this->content_construct('properties/tenants-left', $this->data);
    }

    function getTenantsRight()
    {
        $this->load->model('tenants_model');
        $this->load->model('applyRefundSecurity_model');
        $this->data['tenant'] = $this->tenants_model->getTenant($this->input->get('id'));
        $this->data['sdBalance'] = $this->applyRefundSecurity_model->getRefundBalance($this->input->get('id'), 'profile_id', $this->sd);
        $this->data['lmrBalance'] = $this->applyRefundSecurity_model->getRefundBalance($this->input->get('id'), 'profile_id', $this->lmr);
        $this->data['lease'] = $this->tenants_model->getTenantsLease($this->input->get('pid'));
        $this->data['lid'] = $this->input->get('lid');
        //$this->data['tenantTransactions'] = $this->tenants_model->getTenantTransactions($this->input->get('id'));
        //$this->data['checkApplyRefund'] = $this->applyRefundSecurity_model->checkApplyRefund($this->input->get('id'), 'profile_id');
        $this->data['checkApplyRefund'] = false; if(($this->data['lmrBalance'] + $this->data['sdBalance']) != 0){
            $this->data['checkApplyRefund'] = true;
        }
        $this->data['notes'] = $this->notes_model->getNotes($this->input->get('id'),5);//number corresponds to database
        $this->data['addNoteForm'] = $this->notes_model->addNoteForm($this->input->get('id'),$this->input->get('type'));
        $this->data['documents'] = $this->documents_model->getDocuments($this->input->get('id'),5);//number corresponds to database
        //$this->data['documentsProperty'] = $this->documents_model->getDocumentsProperty($this->data['tenant']->property_id);
        $this->data['documentsInfo'] = $this->documents_model->getDocumentsTenant($this->input->get('id'));
        $this->content_construct('properties/tenants-right', $this->data);
    }

    function getLeasesLeft()
    {
        //$this->load->model('leases_model');
        //$this->data['leases'] = $this->leases_model->getLeasesList();
        $this->content_construct('properties/leases-left', $this->data);
    }

    function getLeasesRight()
    {
        $this->load->model('leases_model');
        $this->load->model('units_model');
        $this->load->model('properties_model');
        $this->load->model('tenants_model');
        $this->load->model('applyRefundSecurity_model');
        $this->data['lease'] = $this->leases_model->getLease($this->input->get('id'));
        $this->data['unit'] = $this->units_model->getUnit($this->data['lease']->unit_id);
        $this->data['property'] = $this->properties_model->getProperty($this->data['unit']->property_id);
        $this->data['sdBalance'] = $this->applyRefundSecurity_model->getRefundBalance($this->input->get('id'), 'lease_id', $this->sd);
        $this->data['lmrBalance'] = $this->applyRefundSecurity_model->getRefundBalance($this->input->get('id'), 'lease_id', $this->lmr);
        $this->data['leaseTenants'] = $this->leases_model->getAllLeaseTenants($this->input->get('id'));
        $this->data['current'] = $this->leases_model->getLeaseAmount($this->input->get('id'));
        //$this->data['leaseTransactions'] = $this->leases_model->getLeaseTransactions($this->data['leaseTenants']);
        $this->data['checkApplyRefund'] = false; if(($this->data['lmrBalance'] + $this->data['sdBalance']) != 0){
            $this->data['checkApplyRefund'] = true;
        }
        $this->data['notes'] = $this->notes_model->getNotes($this->input->get('id'),2);//number corresponds to database
        $this->data['addNoteForm'] = $this->notes_model->addNoteForm($this->input->get('id'),$this->input->get('type'));
        $this->data['units'] = $this->units_model->getUnits();
        $this->data['tenants'] = $this->tenants_model->getAllTenants2();
        $this->data['items'] = $this->leases_model->getAllItems();
        $this->data['documents'] = $this->documents_model->getDocuments($this->input->get('id'),2);//number corresponds to database
        $this->data['documentsInfo'] = $this->documents_model->getDocumentsLease($this->input->get('id'));
        $this->content_construct('properties/leases-right', $this->data);
    }

    function getAccountsLeft()
    {
        //$this->load->model('accounts_model');
        //$this->data['accounts'] = $this->accounts_model->getAccountsList();
        $this->content_construct('accounts/accounts-left', $this->data);
    }

    function getAccountsRight($id)
    {
        if(($id == $this->site->settings->accounts_payable) || ($id ==  $this->site->settings->accounts_receivable)){
            $this->data['locked'] = "locked";
        }
        $this->load->model('accounts_model');
        $this->load->model('reconciliations_model');
        $this->load->model('properties_model');
        $this->data['properties'] = $this->properties_model->getAllProperties();
        $this->data['notes'] = $this->notes_model->getNotes($this->input->get('id'),4);//number corresponds to database
        $this->data['addNoteForm'] = $this->notes_model->addNoteForm($this->input->get('id'),$this->input->get('type'));
        $this->data['getSingleAccount'] = $this->accounts_model->getSingleAccount($id);
        $this->data['account_types'] = $this->accounts_model->getAccountTypes();
        $this->data['classes'] = $this->accounts_model->getClasses();
        $this->data['parents'] = $this->accounts_model->getParents($this->data['getSingleAccount']->account_types_id);
        $this->data['transactionsGraph'] = $this->accounts_model->getSumTransactions($this->input->get('id'));
        //$this->data['totalAmounts']->amounts = [100,5000,2900,9890,1234];
        //$this->data['totalAmounts']->months = ['june','july','august','may','april'];
        //$this->data['singleAccountTransactions'] = $this->accounts_model->getSingleAccountTransactions($id);
        //$this->data['getCreditCard'] = $this->accounts_model->getAccountCC($id); not used anymore(as of 8/3/18)
        $this->data['note'] = $this->notes_model->getNote($id);
        if($this->data['getSingleAccount']->type == "Bank" OR $this->data['getSingleAccount']->type == "Credit Card" OR $this->data['getSingleAccount']->type == "Mortgages"){
            $this->data['getSingleAccount']->image = $this->data['getSingleAccount']->type;

        }

        if(isset($specialAccount->finins)){
            $this->data['bank_id'] = $specialAccount->finins;
          } else {
            $this->data['allReconciliation'] = $this->reconciliations_model->getAllReconciliation($id);
            $this->data['reconciliation'] = $this->reconciliations_model->getLastReconciliation2($id);
            //$closed = intval($this->data['reconciliation']->closed);
            $closed = $this->data['reconciliation']->closed;
          }

        if($this->data['getSingleAccount']->type == "Bank" OR $this->data['getSingleAccount']->type == "Credit Card"  OR $this->data['getSingleAccount']->type == "Mortgages"){
            if($this->data['getSingleAccount']->type == "Mortgages"){
                $this->data['accounts'] = $this->accounts_model->getAccounts();
                $this->data['vendors'] = $this->accounts_model->getVendorsList();
            }
            $this->data['specialAccount'] = $this->accounts_model->getSpecialAccount($this->data['getSingleAccount']->special_table,$id);
            $specialacct = $this->data['specialAccount'];
            $this->data['target'] = 'accounts/editAccount/' . $this->input->get('id');
            // $this->data['balance'] = $this->reconciliations_model->getLastReconciliation($id);
            // $this->data['cleared'] = $this->reconciliations_model->clearedTransactions($id);//for testing
            // $this->content_construct('accounts/accounts-right2', $this->data);//for testing
            if($closed === '0' ){
                $this->data['cleared'] = $this->reconciliations_model->clearedTransactions($id);
                $this->data['count'] = $this->reconciliations_model->countClearedTransactions($id);
                $this->data['diff'] = ($this->data['cleared'][0]->amount - $this->data['reconciliation']->statement_bal); 
                $this->data['recDisplay'] = "continue";
                $this->content_construct('accounts/accounts-right', $this->data);
            }else{
                $this->data['recDisplay'] = "start";
                $this->content_construct('accounts/accounts-right', $this->data);}
        }else{
            $this->data['recDisplay'] = "other";
            //echo "real: ".(memory_get_peak_usage()/1024/1024)." MiB\n\n";

            $this->content_construct('accounts/accounts-right', $this->data);
        }
        
    }

    //new added on 5/28
    function getInventoryLeft()
    {
        //$this->load->model('inventory_model');
        //$this->data['item_types'] = $this->settings->item_types;
        //$this->data['inventory'] = $this->inventory_model->getinventoryList($this->data['item_types']);
        $this->content_construct('inventory/inventory-left', $this->data);
    }

    function getSettingsLeft()
    {
        $this->content_construct('settings/settings-left', null);
    }

    //new added on 5/29
    function getInventoryRight($id)
    {
        $this->load->model('inventory_model');
        $this->data['singleInventory'] = $this->inventory_model->getSingleInventory($id);
        //$this->data['singleInventoryTransactions'] = $this->inventory_model->getSingleInventoryTransactions($id);
        $this->data['notes'] = $this->notes_model->getNotes($this->input->get('id'),7);//number corresponds to database
        $this->data['addNoteForm'] = $this->notes_model->addNoteForm($this->input->get('id'),$this->input->get('type'));
        $this->content_construct('inventory/inventory-right', $this->data);
    }

    function getRecordsetsRight()
    {
        $this->load->model('recordsets_model');
        $this->data['recordsets'] = $this->recordsets_model->getRecordsets();
        $this->content_construct('recordsets/recordsets_right', $this->data);
    }

    function getVendorsLeft()
    {
        //$this->load->model('vendors_model');
        //$this->data['vendors'] = $this->vendors_model->getVendorsList();
        
        $this->content_construct('vendors/vendors-left', $this->data);
    }

    function getVendorsRight($id)
    {
        $this->load->model('vendors_model');
        $this->load->model('properties_model');
        $this->load->model('memorizedTransactions_model');
        $this->data['singeVendor'] = $this->vendors_model->getVendor($id);
        //the following line doesn't make sense- its passing in the profile id but matching it with account id, but we don't use this anymore anyways
        $this->data['singleVendorTransactions'] = $this->accounts_model->getSingleAccountTransactions($id);
        $this->data['notes'] = $this->notes_model->getNotes($this->input->get('id'),6);//number corresponds to database
        $this->data['properties'] = $this->properties_model->getAllProperties();
        $this->data['autobills'] = $this->memorizedTransactions_model->getmemorizedTransactions(6,$id);

        $this->data['addNoteForm'] = $this->notes_model->addNoteForm($this->input->get('id'),$this->input->get('type'));
        $this->content_construct('vendors/vendors-right', $this->data);
    }

        function getTimesheetLeft()
    {
        $this->load->model('timesheet_model');
        //$this->content_construct('timesheet.php', null);
        $this->data['employees'] = $this->timesheet_model->getEmployeesList();
        $this->content_construct('timesheet/timesheet-left', $this->data);
    }

        function getTimesheetRight($id)
    {
        $this->load->model('timesheet_model');
        
        $this->data['employee'] = $this->timesheet_model->getEmployee($id);
        $this->data['SingleEmployeeTimesheet'] = $this->timesheet_model->getSingleEmployeeTimesheet($id);
        //$this->data['totalHours'] = $this->timesheet_model->getTotals($this->data['SingleEmployeeTimesheet'][0]->time_arr);
        //$this->data['totalPay'] = $this->data['SingleEmployeeTimesheet'][0]->totalPay;
        $this->content_construct('timesheet/timesheet-right2', $this->data);
    }

	function getMaintenanceRight()
	{
		$this->load->model('maintenance2_model');

		$this->content_construct('maintenance/maintenance-right', $this->data);
	}

	function getMaintenanceLeft()
	{
		$this->load->model('maintenance2_model');
		$this->data['data'] = $this->maintenance2_model->generateFilters();
		$this->content_construct('maintenance/maintenance-left', $this->data);
	}

    function getId()
    {
        $this->load->model('inventory_model');
        $this->data['inventory'] = $this->inventory_model->getinventoryList();
        $this->content_construct('inventory/inventory-right', $this->data);
    }
}
