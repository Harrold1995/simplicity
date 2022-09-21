<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Units extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('units_model');
    }

    function addUnit()
    {
        $data = $this->input->post('unit');
        if ($id = $this->units_model->addUnit($data))
            echo json_encode(array('es_text' => $data['name'], 'id' => $id, 'type' => 'success', 'message' => 'Unit successfully added.'));
    }
    function addUnit2()
    {
        $data = $this->input->post();
        if ($id = $this->units_model->addUnit($data))
            echo json_encode(array('es_text' => $data['name'], 'id' => $id, 'type' => 'success', 'message' => 'Unit successfully added.'));
    }

    function editUnit($uid)
    {
        $data = $this->input->post();
        $unit = $this->input->post('unit');
        $uilities = $this->input->post('utilities');
        $deletes = $this->input->post('delete');

        $delete = $this->input->post('confirm');
        if($deletes && ($delete == NULL)){
            $response = $this->units_model->editUnit($data, $unit, $uilities, $deletes, $delete);
            echo json_encode(array('type' => 'warning', 'message' => $response));
            return;
        }

        if ($this->units_model->editUnit($unit, $uilities, $deletes, $delete))
            echo json_encode(array('type' => 'success', 'message' => 'Unit successfully changed.'));

           
    }

    function addEditUtilities(){
        $test = 0;
        $utilities = $this->input->post('utilities');
        $unit_id = $this->input->post('unit_id');
        if ($this->units_model->addEditUtilities($utilities, $unit_id)){
            echo json_encode(array('type' => 'success', 'message' => 'Utility successfully updated.'));
        }else {
                echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('property')));
            }
    }

    function getModal()
    {
        $this->load->model('properties_model');
        $params = json_decode($this->input->post('params'));
        $this->data['unit_types'] = $this->settings->unit_types;
        $this->data['unit_status'] = $this->settings->unit_status;
        $this->data['lateCharges'] = $this->units_model->getAlllateCharges();
        
        
        if (isset($params->property_name))
            $this->data['property_name'] = $params->property_name;
        else
            $this->data['property_name'] = '';
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'units/addUnit2';
                $this->data['title'] = 'Add Unit';
                $this->data['properties'] = $this->properties_model->getAllProperties();
                $this->data['unit'] = new stdClass();
                if (isset($params->property_id))
                    $this->data['unit']->property_id = $params->property_id;
                else
                    $this->data['unit']->property_id = '';
                if (isset($params->parent_id)) {
                    $this->data['unit']->property_id = $params->parent_id;
                    $this->data['subunits'] = $this->units_model->getPropertyUnits($params->parent_id);
                }
                if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['unit']->$key = $params->es_value;
                }
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['subunits']);
                $this->load->view('forms/units/unitadd', $this->data);
                break;
            case 'edit' :
                $this->data['target'] = 'units/editUnit/' . $this->input->post('id');
                $this->data['title'] = 'Edit Unit';
                if ($params->serialized != '') {
                    $value = array();
                    parse_str($params->serialized, $value);
                    $this->data['unit'] = (object)$value;
                } else {
                    if ($this->input->post('id') != '')
                        $this->data['unit'] = $this->units_model->getUnit($this->input->post('id'));
                }
                
                if (isset($params->tableGrid) && $params->tableGrid==true) {
                    $this->data['fieldFormat'] = 'field';
                } else {
                    $this->data['fieldFormat'] = 'unit[field]';
                    
                }
                $this->data['properties'] = $this->properties_model->getAllProperties();
                $this->data['utilities'] = $this->units_model->getUnitUtilities($this->input->post('id'));
                $this->data['vendors'] = $this->units_model->getVendors();
                $this->data['subaccounts'] = $this->site->getNestedSelect($this->data['accounts']);
                
                $this->data['utilityTypes'] = $this->units_model->getutilityTypes();
                if (isset($this->data['unit']->property_id))
                    $this->data['subunits'] = $this->units_model->getPropertyUnits($this->data['unit']->property_id);
                elseif (isset($params->parent_id)) {
                    $this->data['unit']->property_id = $params->parent_id;
                    $this->data['subunits'] = $this->units_model->getPropertyUnits($params->parent_id);
                }
                $this->data['subunits'] = $this->site->getNestedSelect($this->data['subunits']);
                $this->load->view('forms/units/unit2', $this->data);
                break;
        }
        //$this->data['subunits'] = array_merge($this->data['subunits'], $params->newitems);
        
    }

    function validateDelete($id)
    {
       
        $result = Array("status" => 1);
        $outCome = $this->checkUnit($id);
        if($outCome->status == 0){
            $result['status'] = 0;
            $result['error'] = $outCome->message;
        }
        return $result;
    }

    function deleteUnit($lid = 0)
    {
        $this->load->model('units_model');
        $validate = $this->input->post('validate');
        $result = $this->validateDelete($lid);
        if ($result['status'] == 1) {
            if($validate!='yes') $this->units_model->deleteUnit($lid);
            echo json_encode(array('type' => 'success', 'message' => 'Unit successfully deleted.'));
        }else
            echo json_encode(array('type' => 'danger', 'message' => $result['error']));
    }

    public function checkUnit($unit_id)
    {  
       $transaction = '';
       $lease = '';
       $this->db->select('t.id AS tran, l.id AS lease');
       $this->db->from('units u');
       $this->db->join('transactions t','u.id = t.unit_id', 'left');
       $this->db->join('leases l', 'u.id = l.unit_id','left');
       $this->db->where('u.id', $unit_id);
       $q = $this->db->get();

       if ($q->num_rows() > 0) {
        foreach (($q->result()) as $row) {
            if($row->tran != NULL){
                $transaction =  'this unit is in a transaction';
            }

            if($row->lease != NULL){
                $lease =  'this unit is on a lease';
            }
        }
    }   
        $outCome= new stdClass();
        if(!empty($transaction) || !empty($lease)) 
        {
            $outCome->message = 'Can not delete ' . $transaction .' '.  $lease;
            $outCome->status = 0;  
            return $outCome;
        }else{
            $outCome->status = 1;
            return $outCome;
        }
    }
}
