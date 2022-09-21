<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tenants extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('tenants_model');
        $this->load->model('documents_model');
    }

    function index()
    {
        // $this->data['tenants'] = $this->tenants_model->getTenantsList();
        // $this->data['mainform'] = $this->load->view('forms/form', null, true);
        // $this->data['add_unit_form'] = $this->load->view('forms/add_unit', null, true);
        $this->meta['title'] = "Tenants";
        $this->meta['h2'] = "Tenants";
        //$this->page_construct('tenants/index', $this->data, $this->meta);
    }

    function addTenant()
    {
        $data = $this->input->post('tenant');
        if ($id = $this->tenants_model->addTenant($data))
            echo json_encode(array('id' => $id, 'es_text' => $data['first_name'] . " " . $data['last_name'], 'type' => 'success', 'message' => 'Tenant successfully added.'));
    }

    function addTenant2()
    {
        $data = $this->input->post('tenant');
        if ($this->tenants_model->addTenant($data))
            echo json_encode(array('id' => $id, 'es_text' => $data['first_name'] . " " . $data['last_name'], 'type' => 'success', 'message' => 'Tenant successfully added.'));
    }

    function editTenant($tid = 0)
    {
        $data = $this->input->post('tenant');
        $contact = $this->input->post('contact');
        $address = $this->input->post('address');
        $deletes = $this->input->post('delete');
       
        $delete = $this->input->post('confirm');
        if($deletes && $delete == NULL){
            $response = $this->tenants_model->editTenant($data, $contact, $address, $tid, $deletes, $delete);
            echo json_encode(array('type' => 'warning', 'message' => $response));
            return;
        }
        if ($this->tenants_model->editTenant($data, $contact, $address, $tid, $deletes, $delete))
            echo json_encode(array('type' => 'success', 'message' => 'Tenant successfully updated.'));
    }

    

    function initPaymentgetModal()
    {
        $params = json_decode($this->input->post('params'));
        $tenant = $query = $this->db->get_where('profiles', array('id' => $params->profile_id))->row();

        $this->data['target'] = 'tenants/addTenant';
        $this->data['title'] = 'Process Payment';
        $this->data['leases'] = $this->tenants_model->getTenantLeases($params->profile_id);
        $this->data['payment_info'] = $params;
        $this->data['tenant'] =  $tenant->first_name.' '.$tenant->last_name ;
        $this->load->view('forms/init_payment', $this->data);
    }

    function addAutochargeGetModal()
    {
        $params = json_decode($this->input->post('params'));
        $tenant = $query = $this->db->get_where('profiles', array('id' => $params->profile_id))->row();

        $this->data['target'] = 'tenants/addTenant';
        $this->data['title'] = 'add autocharge';
        $this->data['leases'] = $this->tenants_model->getTenantLeases($params->profile_id);
        $this->data['payment_info'] = $params;
        $this->data['tenant'] =  $tenant->first_name.' '.$tenant->last_name ;
        $this->load->view('forms/init_recurring', $this->data);
    }

    function addPayMethodgetModal()
    {
        $params = json_decode($this->input->post('params'));
        $tenant = $query = $this->db->get_where('profiles', array('id' => $params->profile_id))->row();

        $this->data['title'] = 'Add Pay Method';
        $this->data['type'] = $params->type;
        $this->data['tenant'] =  $tenant->first_name.' '.$tenant->last_name ;
        $this->data['leases'] = $this->tenants_model->getTenantLeases($params->profile_id);
        $this->data['profile_id'] =  $params->profile_id ;
        $this->load->view('forms/add_pay_method', $this->data);
    }

    function getModal()
    {
        $params = json_decode($this->input->post('params'));
        $this->data['contact_method_types'] = $this->settings->contact_method;
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'tenants/addTenant';
                $this->data['title'] = 'Add Tenant';
                if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['tenant'] = new stdClass();
                    $this->data['tenant']->$key = $params->es_value;
                }
                break;
            case 'edit' :
                $this->data['target'] = 'tenants/editTenant/'. $this->input->post('id');
                $this->data['title'] = 'Edit Tenant';
                $this->data['tenant'] = $this->tenants_model->getTenant($this->input->post('id'));
                $this->data['contacts'] = $this->tenants_model->getContacts($this->input->post('id'));
                $this->data['addresses'] = $this->tenants_model->getAddresses($this->input->post('id'));
                $this->data['paymethods'] = $this->tenants_model->getPaymethods($this->input->post('id'));
                $this->data['documents'] = $this->documents_model->getDocuments($this->input->post('id'),5);//number corresponds to database
                break;
        }
        $this->load->view('forms/tenants/main', $this->data);
    }

    function inviteTenantgetModal(){
    

                $this->data['target'] = 'Api/inviteusers';
                $this->data['title'] = 'Invite Tenants to Portal';         

             $this->load->view('forms/tenants/invite', $this->data);
    }
    
    function inviteTenants(){
           
           $this->db->select('p.id as id , pr.name as property, concat_ws(" ", p.first_name, p.last_name) as tenant, u.name as unit, l.start as start, l.end as end, p.email as email, if(isnull(invite_status), "Not_Invited", if(invite_status = 1, "Invite_Sent", "Registered")) as invite_status');
           $this->db->from('profiles p');
           $this->db->join('leases_profiles lp', 'lp.profile_id = p.id');
           $this->db->join('leases l', 'lp.lease_id = l.id');
           $this->db->join('units u', 'l.unit_id = u.id');
           $this->db->join('properties pr', 'u.property_id = pr.id');
           $this->db->where('l.move_out', null);
           $q = $this->db->get();
        
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                $columns = Array();
                $columns[] = Array("id" => "check", "name" => '<label for="pay_bill_select_all" class="custom-checkbox"><input type="checkbox" class="hidden" id="pay_bill_select_all"><div class="input"></div></label>', "field" => "check", "width" => 55, "sortable" => false, "resizable" => false, "formatter" => "CheckFormatter");
                $columns[] = Array("id" => "property", "name" => "Property", "field" => "property", "sortable" => true, "resizable" => true);
                $columns[] = Array("id" => "unit", "name" => "Unit", "field" => "unit", "sortable" => true, "resizable" => true);
                $columns[] = Array("id" => "tenant", "name" => "Tenant", "field" => "tenant", "sortable" => true, "resizable" => true);
                $columns[] = Array("id" => "start", "name" => "Start", "field" => "start", "sortable" => true, "resizable" => true);
                $columns[] = Array("id" => "end", "name" => "End", "field" => "end", "sortable" => true, "resizable" => true);
                $columns[] = Array("id" => "invite_status", "name" => "invite_status", "field" => "invite_status", "sortable" => true, "resizable" => true);
                $columns[] = Array("id" => "email", "name" => "email", "field" => "email", "sortable" => true, "resizable" => true);

                echo json_encode(Array("data" => $data, "columns" => $columns));
            }
    }
}
