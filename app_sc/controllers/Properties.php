<?php defined('BASEPATH') OR exit('No direct script access allowed');
include 'app_sc/helpers/logs/logs.php';
class Properties extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('validate_model');
        $this->load->library('encryption');
        $this->load->model('logs_model');
        $this->icons = Array("property" => "fas fa-building", "unit" => "far fa-building");
    }

    function index()
    {
        $this->meta['title'] = "Properties";
        $this->meta['h2'] = "Properties";
        $this->page_construct('properties/index', null, $this->meta);
       if (!$this->permissions->checkPermissions('properties_general')) return;

    }

    function attach_document(){
        $data = $this->input->post();
        $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $this->upload->do_upload('attach_document');
        $data['attach_document'] = $this->upload->data('file_name');
        $this->db->insert('documents', Array("name" => $data['data'], "reference_id" => '6', "type" => "1"));
        $this->db->insert('documents', Array("name" => $data['attach_document'], "reference_id" => '6', "type" => "1"));
        $a = 2;
        $b = 3;
    }
    Function instantUpdateSettings(){
        //used for capitalcalls
        $val = $this->input->post('value');
        $field = $this->input->post('type');
        $property = $this->input->post('id');


        
            $val = removeComma($val);
        
        
        $this->db->where('id', $property);
        $this->db->update('properties', array($field => $val));
        
    }

    Function clrAllCcSettings(){
        //used for capitalcalls to clear all data
        $data = array(
            'reserves' => 0,
            'additional_expense' => 0,
            'included_in_payables' => 0,
            'cc_notes' => 0,
            'cc_amt' => 0
         );
        
        $this->db->update('properties', $data);
        $this->db->update('company_settings', array('cur_capital_call' => null));
        
    }

    function addProperty()
    {
        $this->load->model('properties_model');
        $data = $this->input->post('property');
        $units = $this->input->post('unit');
        $owners = $this->input->post('owners');
        $taxes = $this->input->post('taxes');
        $utilities = $this->input->post('utilities');
        $insurance = $this->input->post('insurance');
        $managements = $this->input->post('managements');
        $managementsAccounts = $this->input->post('managementsAccounts');
        $key_codes = $this->input->post('key_codes');
        
        $this->form_validation->set_rules($this->settings->propertyFormValidation);
        $validate = $this->validate_model->validate("properties", $data);
        if ($this->form_validation->run() && $validate['bool']) {
            $pid = $this->properties_model->addProperty($data, $units, $owners, $taxes, $utilities, $insurance, $managements, $key_codes);

            if ($pid) {
                $add_property_log = new Log_Property_Added($this->ion_auth->get_user_id(), $pid, $data['name']);

                $this->logs_model->add_log($add_property_log);

                echo json_encode(array('type' => 'success', 'message' => 'Property successfully added.'));
                return;
            }
        }

        // if db request failed
        $errors = $errors . validation_errors() ."</br>". $validate['msg'];
        echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('property')));
        
    }

    function editProperty($pid = 0)
    {
        $this->load->model('properties_model');
        $data = $this->input->post('property');
        $units = $this->input->post('unit');
        $owners = $this->input->post('owners');
        $taxes = $this->input->post('taxes');
        $utilities = $this->input->post('utilities');
        $insurance = $this->input->post('insurance');
        $managements = $this->input->post('managements');
        $managementsAccounts = $this->input->post('managementsAccounts');
        $key_codes = $this->input->post('key_codes');

        $deletes = $this->input->post('delete');
       
        $delete = $this->input->post('confirm');
        if($deletes && ($delete == NULL)){
            $response = $this->properties_model->editProperty($data, $units, $owners, $taxes, $utilities, $insurance, $pid, $managements, $key_codes, $deletes, $delete);
            echo json_encode(array('type' => 'warning', 'message' => $response));
            return;
        }
        $this->form_validation->set_rules($this->settings->propertyFormValidation);
        $validate = $this->validate_model->validate("properties", $data);

        $property_info = $this->properties_model->getProperty($pid);

        if ($this->form_validation->run() && $validate['bool'] && $this->properties_model->editProperty($data, $units, $owners, $taxes, $utilities, $insurance, $pid, $managements, $key_codes, $deletes, $delete)){//$deletes, $delete
            if ($property_info->name != $data['name']) {
                $update_title_log = new Log_Property_Title_Update($this->ion_auth->get_user_id(), $pid, $property_info->name, $data['name']);
                $this->logs_model->add_log($update_title_log);
            }

            echo json_encode(array('type' => 'success', 'message' => 'Property successfully updated.'));
        }else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('property')));
            }
            
            /*$url = "https://smsgateway.me/api/v4/message/send";
            $headers = array(
                'Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhZG1pbiIsImlhdCI6MTUzMDYwMzczNCwiZXhwIjo0MTAyNDQ0ODAwLCJ1aWQiOjU2MDA5LCJyb2xlcyI6WyJST0xFX1VTRVIiXX0.MbhOpdBjwqaSVd9tsVNnY4dTDyD8JIkPIETBxNNEaQQ'
                );

            $request = array(array(
                'phone_number' => '+13479256456',
                'message' => 'Property '.$data['name'].' has been updated!',
                'device_id' => '95348'
            ));

            $ch = curl_init($url);
            $options = array(
            CURLOPT_HEADER         => false,        // don't return headers
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,         // follow redirects
            // CURLOPT_ENCODING       => "utf-8",           // handle all encodings
            CURLOPT_AUTOREFERER    => true,         // set referer on redirect
            CURLOPT_POST            => 1,            // i am sending post data
            CURLOPT_POSTFIELDS     => json_encode($request),    // this are my post vars
            CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
            CURLOPT_SSL_VERIFYPEER => false,        //
            CURLOPT_VERBOSE        => 0,
            CURLOPT_HTTPHEADER     => $headers
        );

        curl_setopt_array($ch,$options);
        $data = curl_exec($ch);
        curl_close($ch);*/

    }

    function deleteProperty($pid = 0)
    {
        $this->load->model('properties_model');
        $validate = $this->input->post('validate');
        $result = $this->validateDelete($pid);
        if ($result['status'] == 1) {
            if ($validate!='yes') {
                $property_info = $this->properties_model->getProperty($pid);
                $this->properties_model->deleteProperty($pid);

                $delete_property_log = new Log_Property_Deleted($this->ion_auth->get_user_id(), $pid, $property_info->name);
                $this->logs_model->add_log($delete_property_log);
            }
            echo json_encode(array('type' => 'success', 'message' => 'Property successfully deleted.'));
        }else
            echo json_encode(array('type' => 'danger', 'message' => $result['error']));
    }

        function getUtilities()
    {   
        $id = $this->input->get('id');
        $this->load->model('properties_model');
        $utilities = json_encode($this->properties_model->getPropertyUtilities($this->input->get('id')));
        echo $utilities;

    }

    function getPropertyUtilitiesForBill()
    {   
        $id = $this->input->get('id');
        $this->load->model('properties_model');
        $utilities = json_encode($this->properties_model->getPropertyUtilitiesForBill($this->input->get('id')));
        echo $utilities;

    }

    function getPropertyUtilitiesForBillSlick()
    {
        $this->load->model('properties_model');
        $data = $this->properties_model->getPropertyUtilitiesForBill();
        $columns = Array();
        $columns[] = Array("id" => "plus", "name" => '+', "field" => "plus", "width" => 55, "cssClass" => "slick-plus", "sortable" => false, "resizable" => false, "formatter" => "PlusFormatter");
        $columns[] = Array("id" => "check", "name" => '<label for="pay_bill_select_all" class="custom-checkbox"><input type="checkbox" class="hidden" id="pay_bill_select_all"><div class="input"></div></label>', "field" => "check", "width" => 55, "sortable" => false, "resizable" => false, "formatter" => "CheckFormatter");
        $columns[] = Array("id" => "vendor", "name" => "Vendor", "field" => "profileName", "sortable" => true, "resizable" => true);
        $columns[] = Array("id" => "property", "name" => "Property", "field" => "propertyName", "sortable" => true, "resizable" => true);
        $columns[] = Array("id" => "unit", "name" => "Unit", "field" => "unitName", "sortable" => true, "resizable" => true);
        $columns[] = Array("id" => "description", "name" => "Description", "field" => "description", "sortable" => false, "resizable" => true, "formatter" => "InputFormatter", "format" => "text", "asyncPostRender" => "renderInput", "valuefield" => "description", "instantUpdate" => true);
        $columns[] = Array("id" => "account", "name" => "Account", "field" => "account", "sortable" => true, "resizable" => true);
        $columns[] = Array("id" => "type", "name" => "Utility Type", "field" => "utility_type", "sortable" => true, "resizable" => true);
        $columns[] = Array("id" => "last_date", "name" => "Last Payment Date", "field" => "old_last_paid_date", "sortable" => true, "resizable" => true, "formatter" => "DateFormatter", "datatype" => "date");
        $columns[] = Array("id" => "direct", "name" => 'Direct Payment', "field" => "direct_payment", "width" => 55, "sortable" => false, "resizable" => false, "formatter" => "CheckFormatter", "asyncPostRender" => "renderCheckbox");
        $columns[] = Array("id" => "account_id", "name" => "Expense Acct", "field" => "account_id", "sortable" => false, "resizable" => true, "formatter" => "SelectFormatter", "asyncPostRender" => "renderSelect", "source" => "account", "namefield" => "bank_name");
        $columns[] = Array("id" => "billable", "name" => 'Billable', "field" => "billable", "width" => 55, "sortable" => false, "resizable" => false, "formatter" => "CheckFormatter", "asyncPostRender" => "renderCheckbox");
        $columns[] = Array("id" => "date", "name" => "Date", "field" => "date", "sortable" => false, "resizable" => true, "formatter" => "InputFormatter", "format" => "date", "asyncPostRender" => "renderInput", "valuefield" => "date");
        $columns[] = Array("id" => "amount", "name" => "Amount", "field" => "amount", "sortable" => false, "resizable" => true, "formatter" => "InputFormatter", "format" => "usd", "asyncPostRender" => "renderInput", "valuefield" => "amount", "total" => true);
        $columns[] = Array("id" => "usage", "name" => "Usage", "field" => "usage", "sortable" => false, "resizable" => true, "formatter" => "InputFormatter", "format" => "text", "asyncPostRender" => "renderInput", "valuefield" => "usage");
        $columns[] = Array("id" => "estimate", "name" => 'Estimate', "field" => "estimate", "width" => 55, "sortable" => false, "resizable" => false, "formatter" => "CheckFormatter", "asyncPostRender" => "renderCheckbox");
        $columns[] = Array("id" => "memo", "name" => "Memo", "field" => "memo", "sortable" => false, "resizable" => true, "formatter" => "InputFormatter", "format" => "text", "asyncPostRender" => "renderInput", "valuefield" => "memo", "instantUpdate" => true);
        echo json_encode(Array("data" => $data, "columns" => $columns));

    }

    function getModal()
    {   
        if (!$this->permissions->checkPermissions('properties_properties_view', TRUE)) return;
        $this->load->model('properties_model');
        $this->load->model('units_model');
        $this->load->model('tenants_model');
        $this->load->model('leases_model');
        $this->load->model('notes_model');
        $this->load->model('documents_model');
        $this->load->model('entities_model');

        $params = json_decode($this->input->post('params'));
        $this->data['property_status'] = $this->settings->property_status;
        $this->data['profiles'] = $this->tenants_model->getTenants();
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'properties/addProperty';
                $this->data['title'] = 'Add Property';
                $this->data['bankAccounts'] = $this->properties_model->getBankAccounts();
                $this->data['allManagers'] = $this->properties_model->getAllManagers();
                $this->data['accounts'] = $this->properties_model->getAllAccounts();
                $this->data['items'] = $this->properties_model->getItems();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['expenseAccounts'] = $this->properties_model->getAllAccounts('expense account');
                $this->data['subexpenseAccounts'] = $this->site->getNestedSelect($this->data['expenseAccounts']);
                $this->data['vendors'] = $this->properties_model->getVendors();
                $this->data['entities'] = $this->entities_model->getAllEntities();
                $this->data['lateCharges'] = $this->properties_model->getAlllateCharges();
                $this->data['ltemplates'] = $this->leases_model->getLeaseTemplates();
                $this->data['paid_by_types'] = $this->properties_model->getPaid_by_types();
                if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['property'] = new stdClass();
                    $this->data['property']->$key = $params->es_value;
                }
                break;
            case 'edit' :
                $this->data['target'] = 'properties/editProperty/' . $this->input->post('id');
                $this->data['title'] = 'Edit Property';
                $this->data['property'] = $this->properties_model->getProperty($this->input->post('id'));
                $this->data['units'] = $this->units_model->getPropertyUnits($this->input->post('id'), 'property');
                $this->data['property']->units = $this->units_model->getPropertyUnits($this->input->post('id'), 'property');
                $this->data['owners'] = $this->properties_model->getPropertyOwners($this->input->post('id'));
                $this->data['taxes'] = $this->properties_model->getPropertyTaxes($this->input->post('id'));
                $this->data['utilities'] = $this->properties_model->getPropertyUtilities($this->input->post('id'));
                $this->data['insurance'] = $this->properties_model->getPropertyInsurance($this->input->post('id'));
                $this->data['managements'] = $this->properties_model->getManagement($this->input->post('id'));
                $this->data['documents'] = $this->properties_model->getPropertyDocuments($this->input->post('id'));
                $this->data['notes'] = $this->notes_model->getNotes($this->input->post('id'),1);//number corresponds to database
                $this->data['documents'] = $this->documents_model->getDocuments($this->input->post('id'),1);//number corresponds to database
                $this->data['bankAccounts'] = $this->properties_model->getBankAccounts();
                $this->data['manager'] = $this->properties_model->getPropertyManager($this->input->post('id'));
                $this->data['allManagers'] = $this->properties_model->getAllManagers();
                $this->data['accounts'] = $this->properties_model->getAllAccounts();
                $this->data['items'] = $this->properties_model->getItems();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['expenseAccounts'] = $this->properties_model->getAllAccounts('expense account');
                $this->data['subexpenseAccounts'] = $this->site->getNestedSelect($this->data['expenseAccounts']);
                $this->data['vendors'] = $this->properties_model->getVendors();
                $this->data['frequencies'] = $this->properties_model->getFrequencies();
                $this->data['utilityTypes'] = $this->properties_model->getutilityTypes();
                $this->data['entities'] = $this->entities_model->getAllEntities();
                $this->data['lateCharges'] = $this->properties_model->getAlllateCharges();
                $this->data['ltemplates'] = $this->leases_model->getLeaseTemplates();
                $this->data['paid_by_types'] = $this->properties_model->getPaid_by_types();
                $this->data['key_codes'] = $this->properties_model->getKey_codes($this->input->post('id'));
                break;
        }
        $this->load->view('forms/property/main2', $this->data);
    }

    function validateDelete($id)
    {
        $this->load->model('units_model');
        $result = Array("status" => 1);
        $units = $this->units_model->getPropertyUnits($id);
        if(count($units) > 0) {
            $result['status'] = 0;
            $result['error'] = 'There are units linked to this property, cannot delete!';
        }
        return $result;
    }

    function sendEmail()
    {
        $this->load->library('email');

            $string = $this->load->view('email_template.php', '', TRUE);

            $subject = 'This is a test';
            $message = '<p>This message has been sent for testing purposes.</p>';

            // Get full html:
            $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=' . strtolower(config_item('charset')) . '" />
                <title>' . html_escape($subject) . '</title>
                <style type="text/css">
                    body {
                        font-family: Arial, Verdana, Helvetica, sans-serif;
                        font-size: 16px;
                    }
                </style>
            </head>
            <body>
            ' . $message . '
            </body>
            </html>';
            // Also, for getting full html you may use the following internal method:
            //$body = $this->email->full_html($subject, $message);

            $result = $this->email
                ->from('rafael@simpli-city.com')
                ->reply_to('rafael@simpli-city.com')    // Optional, an account where a human being reads.
                ->to('info@thevertexlabs.com')
                ->subject($subject)
                ->message($body)
                ->send();

            var_dump($result);
            echo '<br />';
            echo $this->email->print_debugger();

            exit;
    }
}
