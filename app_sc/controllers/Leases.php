<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Leases extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('leases_model');
        $this->load->model('validate_model');
    }

    function addLease()
    {
        $errors = "";
        $data = $this->input->post('lease');
        $ttls = $this->input->post('tenanttoleases');
        // if($data['start'] >= $data['end']){$error = "Start date is after the end date. </br>";}
        $autoCharges = $this->input->post('autoCharges');
        $renewal = $this->input->post('renewal');
        $rs = $this->input->post('rs');
        $sect_8 = $this->input->post('sect_8');
        $ic = $this->input->post('in_court');
        $vdata = array('data' => $data, 'ttls' => $ttls);
        $validate = $this->validate_model->validate("lease", $vdata);
        $this->form_validation->set_rules($this->settings->leaseFormValidation);
        if ($this->form_validation->run() && $validate['bool'] && $this->leases_model->addLease($data, $ttls, $renewal, $autoCharges, $rs, $sect_8, $ic))
            echo json_encode(array('type' => 'success', 'message' => 'Lease successfully added.'));
        else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('lease')));
        }
    }

    function addLateChargeSetup()
    {
        $data = $this->input->post('latecharge');
        $rules = $this->input->post('rules');
        if ($id = $this->leases_model->addLateChargeSetup($data, $rules))
            echo json_encode(array('id' => $id, 'es_text' => $data['name'], 'type' => 'success', 'message' => 'Late charge setup successfully added.'));
        else {
            echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors('lease')));
        }
    }

    function editLease($lid = 0)
    {
        $errors = "";
        $data = $this->input->post('lease');
        $ttls = $this->input->post('tenanttoleases');
        $deletes = $this->input->post('delete');
        $delete = $this->input->post('confirm');
        $autoCharges = $this->input->post('autoCharges');
        $renewal = $this->input->post('renewal');
        $rs = $this->input->post('rs');
        $sect_8 = $this->input->post('sect_8');
        $ic = $this->input->post('in_court');

        if($deletes && ($delete == NULL)){
            $response = $this->leases_model->editLease($data, $ttls, $lid, $renewal, $autoCharges, $rs, $sect_8, $ic, $deletes, $delete);
            echo json_encode(array('type' => 'warning', 'message' => $response));
            return;
        }
        // if($data['start'] >= $data['end']){$error = "Start date is after the end date. </br>";}
        $vdata = array('data' => $data, 'ttls' => $ttls, 'edit' => true);
        $validate = $this->validate_model->validate("lease", $vdata);
        $this->form_validation->set_rules($this->settings->leaseFormValidation);
        if ($this->form_validation->run() && $validate['bool'] && $this->leases_model->editLease($data, $ttls, $lid, $renewal, $autoCharges, $rs, $sect_8, $ic, $deletes, $delete)){
            echo json_encode(array('type' => 'success', 'message' => 'Lease successfully updated.'));
        }else {
            $errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('lease')));
        }
    }

    function editLateChargeSetup($lcid = 0)
    {
        $data = $this->input->post('latecharge');
        $rules = $this->input->post('rules');
        if ($this->leases_model->editLateChargeSetup($data, $rules, $lcid))
            echo json_encode(array('type' => 'success', 'message' => 'Late charge setup successfully updated.'));
    }

    function getModal()
    {
        $this->load->model('properties_model');
        $this->load->model('units_model');
        $this->load->model('tenants_model');
        $this->load->model('notes_model');
        $this->load->model('documents_model');
        $this->data['ltemplates'] = $this->leases_model->getLeaseTemplates();
        $params = json_decode($this->input->post('params'));
        $this->data['properties'] = $this->properties_model->getAllProperties();
        $this->data['lcsetups'] = $this->leases_model->getLateChargeSetups();
        $this->data['profiles'] = $this->tenants_model->getTenants();
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'leases/addLease';
                $this->data['title'] = 'Add Lease';
                $this->data['tenants_on_lease'] = $this->leases_model->getTenants();//gets all tenants
                $this->data['lease_templates'] = $this->leases_model->getLease_templates();
                $this->data['items'] = $this->leases_model->getAllItems();
                $this->data['frequencies'] = $this->leases_model->getFrequencies();
                 if (isset($params->unit)) {
                     $this->data['lease']->unit_id = $params->unit;
                     $this->data['lease']->property_id = $this->units_model->getProperty($params->unit);
                     $this->data['units'] = $this->units_model->getPropertyUnits($this->data['lease']->property_id);
                 }
                break;
            case 'edit' :
                $this->data['target'] = 'leases/editLease/' . $this->input->post('id');
                $this->data['title'] = 'Edit Lease';
                $this->data['notes'] = $this->notes_model->getNotes($this->input->post('id'),2);//number corresponds to database
                $this->data['addNoteForm'] = $this->notes_model->addNoteForm($this->input->post('id'),"lease");
                $this->data['lease'] = $this->leases_model->getLease($this->input->post('id'));
                $this->data['lease']->ttls = $this->leases_model->getLeaseTtls($this->input->post('id'));
                $this->data['lease_templates'] = $this->leases_model->getLease_templates();
                $this->data['section_8'] = $this->leases_model->getSection8($this->input->post('id'));
                $this->data['rent_stabilized'] = $this->leases_model->getrent_stabilized($this->input->post('id'));
                $this->data['in_court'] = $this->leases_model->getIn_court($this->input->post('id'));
                $this->data['tenants_on_lease'] = $this->leases_model->getTenants_on_lease($this->input->post('id'));
                $this->data['jtenants_on_lease'] = json_encode($this->leases_model->getTenants_on_lease($this->input->post('id')));
                $this->data['renewal'] = $this->leases_model->getrenewal($this->input->post('id'));
                $this->data['notes'] = $this->notes_model->getNotes($this->input->post('id'),2);//number corresponds to database
                $this->data['documents'] = $this->documents_model->getDocuments($this->input->post('id'),2);//number corresponds to database
                $this->data['auto_charges'] = $this->leases_model->getmemorizedTransactions($this->input->post('id'),2);//number corresponds to database
                $this->data['frequencies'] = $this->leases_model->getFrequencies();
                $this->data['items'] = $this->leases_model->getAllItems();
                if (isset($this->data['lease']->property_id))
                    $this->data['units'] = $this->units_model->getPropertyUnits($this->data['lease']->property_id);
                break;
        }
        $this->load->view('forms/lease/main', $this->data);
    }

    function getLateChargeModal()
    {
        $params = json_decode($this->input->post('params'));
        $this->data['ctypes'] = $this->leases_model->getAllItems();
        $this->data['amount_types'] = $this->settings->late_charge_amount_types;
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'leases/addLateChargeSetup';
                $this->data['title'] = 'Add Late Charge Setup';
                if (isset($params->es_key)) {
                    $key = explode('.', $params->es_key)[1];
                    $this->data['latecharge'] = new stdClass();
                    $this->data['latecharge']->$key = $params->es_value;
                }
                break;
            case 'edit' :
                $this->data['target'] = 'leases/editLateChargeSetup/' . $this->input->post('id');
                $this->data['title'] = 'Edit Late Charge Setup';
                $this->data['latecharge'] = $this->leases_model->getLateChargeSetup($this->input->post('id'));
                break;
        }
        $this->load->view('forms/lease/latecharge', $this->data);
    }

    function printPdf($lid)
    {
        $lease = $this->leases_model->getLease($lid);
        //redirect('formbuilder/printForm/'.$lease->lease_template.'/lease_id/'.$lid);
        redirect('formbuilder/printForm/'.$lid);
    }

    public function changeTagName($node, $name)
    {
        $childnodes = array();
        foreach ($node->childNodes as $child) {
            $childnodes[] = $child;
        }
        $newnode = $node->ownerDocument->createElement($name);
        foreach ($childnodes as $child) {
            $child2 = $node->ownerDocument->importNode($child, true);
            $newnode->appendChild($child2);
        }
        foreach ($node->attributes as $attrName => $attrNode) {
            $attrName = $attrNode->nodeName;
            $attrValue = $attrNode->nodeValue;
            $newnode->setAttribute($attrName, $attrValue);
        }
        $node->parentNode->replaceChild($newnode, $node);
        return $newnode;
    }

     function getBrainFrequencies()
    {
        $frequencies = $this->leases_model->getFrequencies();
        echo json_encode($frequencies);
    }

    function leaseDates()
    {

        if(empty($this->input->Post('start')) ) { $start = NULL; } else  {$start= $this->input->Post('start');} 
        if(empty($this->input->Post('end')) ) { $end = NULL; } else  {$end= $this->input->Post('end');} 
        if(empty($this->input->Post('in')) ) { $in = NULL; } else  {$in= $this->input->Post('start');} 
        if(empty($this->input->Post('out')) ) { $out = NULL; } else  {$out= $this->input->Post('out');} 
        $lid= $this->input->Post('lid');



        if ( $this->leases_model->changeLeaseDates($lid,$start,$end,$in,$out))
            echo json_encode(array('type' => 'success', 'message' => 'Lease successfully Updated.'));
        else {
            
            echo json_encode(array('type' => 'danger', 'message' => "error"));
        }

        
    }
    
    function validateDelete($id)
    {
       
        $result = Array("status" => 1);
        $transactions = $this->checkLease($id);
        if($transactions !== NULL){
            $result['status'] = 0;
            $result['error'] = 'There are transactions for this lease, cannot delete!';
        }
        return $result;
    }

    function deleteLease($lid = 0)
    {
        $this->load->model('leases_model');
        $validate = $this->input->post('validate');
        $result = $this->validateDelete($lid);
        if ($result['status'] == 1) {
            if($validate!='yes') $this->leases_model->deleteLease($lid);
            echo json_encode(array('type' => 'success', 'message' => 'Lease successfully deleted.'));
        }else
            echo json_encode(array('type' => 'danger', 'message' => $result['error']));
    }

    public function checkLease($lease_id)
    {   
       $this->db->select('t.id');
       $this->db->from('transactions t');
       $this->db->where('t.lease_id', $lease_id);
       $q = $this->db->get();

       if ($q->num_rows() > 0) {
            return $q->row()->id;
        } return null;
    }
}


