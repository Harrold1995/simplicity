<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tenantstolease extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('tenants_model');
    }

    function getModal()
    {
        $this->load->model('units_model');

        $params = json_decode($this->input->post('params'));
        $this->data['lateCharges'] = $this->units_model->getAlllateCharges();
        if (isset($params->unit)){
            $this->data['units'] = $this->units_model->getUnitandsubs($params->unit);
        }
        if (isset($params->lease) && $params->lease > 0){
            $this->data['lease_id'] = $params->lease;
        }
        if (isset($params->main_id) && (int)$params->main_id > 0){
            $this->data['lease_id'] = $params->main_id;
        }
        $this->data['pet_types'] = $this->settings->pet_types;
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'tenantstolease/addTenantToLease';
                $this->data['title'] = 'Add People To Lease';
                break;
            case 'edit' :
                $this->data['target'] = 'tenantstolease/editTenantToLease';
                $this->data['title'] = 'Edit People To Lease';
                if ($params->serialized != '') {
                    $value = array();
                    parse_str($params->serialized, $value);
                    $this->data['ttl'] = (object)$value;
                } elseif ($this->input->post('id') != '')
                    $this->data['ttl'] = $this->tenants_model->getTtl($this->input->post('id'));
                    $this->data['units'] = $this->units_model->getUnitandsubs($this->data['ttl']->unit_id);
                break;
        }
        $this->data['tenants'] = $this->tenants_model->getAllTenants2();
        $this->load->view('forms/tenant-lease', $this->data);
    }
}
