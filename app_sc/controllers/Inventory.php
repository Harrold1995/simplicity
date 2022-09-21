<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Inventory extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('inventory_model');
        $this->icons = Array("property" => "fas fa-building", "unit" => "far fa-building");
    }

    function index()
    {
        $this->meta['title'] = "Inventory";
        $this->meta['h2'] = "Inventory";
        $this->page_construct('inventory/index', null, $this->meta);
    }

    function addInventory()
    {
        $data = $this->input->post('inventory');
        if ($this->inventory_model->addInventory($data))
            echo json_encode(array('type' => 'success', 'message' => 'Vendor successfully added.'));
    }

    function editInventory()
    {
        //$this->load->model('inventory_model');
        $data = $this->input->post('inventory');
        if ($this->inventory_model->editInventory($data))
            echo json_encode(array('type' => 'success', 'message' => 'Item successfully changed.'));
    }

    function getModal()
    {
        $this->load->model('inventory_model');

        $params = json_decode($this->input->post('params'));
        $this->data['item_types'] = $this->settings->item_types;
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'inventory/addInventory';
                $this->data['title'] = 'Add Inventory';
                $this->data['parents'] = $this->inventory_model->getParentsList();
                $this->data['accounts'] = $this->inventory_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                $this->data['jParents'] =  json_encode($this->data['parents']);
                /*if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['inventory'] = new stdClass();
                    $this->data['inventory']->$key = $params->es_value;
                }*/
                break;
            case 'edit' :
                $this->data['target'] = 'inventory/editInventory/' . $this->input->post('id');
                $this->data['title'] = 'Edit Inventory';
                $this->data['inventory'] = $this->inventory_model->getSingleInventory($this->input->post('id'));
                $this->data['parents'] = $this->inventory_model->getParentsList($this->data['inventory']);
                $this->data['allParents'] = $this->inventory_model->getParentsList();
                $this->data['jParents'] =  json_encode($this->data['allParents']);
                $this->data['accounts'] = $this->inventory_model->getAllAccounts();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                break;
        }
        $this->load->view('forms/inventory/main', $this->data);
    }
}
