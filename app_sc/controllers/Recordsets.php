<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Recordsets extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('recordsets_model');
    }

    function index()
    {
        $this->meta['title'] = "Report Recordsets";
        $this->meta['h2'] = "Recordsets";
        $this->data['recordsets'] = $this->recordsets_model->getRecordsets();
        $this->page_construct('recordsets/index', $this->data, $this->meta);
    }

    function getModal()
    {
        $id = $this->input->post('id');
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = base_url() . 'recordsets/addRecordSet';
                $this->data['title'] = 'Create New Record Set';
                break;
            case 'edit' :
                $this->data['target'] = base_url() . 'recordsets/editRecordSet/'.$id;
                $this->data['title'] = 'Edit Recordset';
                $set = $this->recordsets_model->getRecordset($id);
                $this->data['set'] = $set['set'];
                $this->data['columns'] = $set['columns'];
                break;
        }
        $this->load->view('forms/reports/recordset', $this->data);
    }

    function addRecordset()
    {
        $data = $this->input->post('r');
        $fields = $this->input->post('field');
        if ($this->recordsets_model->addRecordset($data, $fields))
            echo json_encode(array('type' => 'success', 'message' => 'Recordset successfully added.'));
    }

    function editRecordset($id)
    {
        $data = $this->input->post('r');
        $fields = $this->input->post('field');
        $delete = $this->input->post('delete');
        if ($this->recordsets_model->editRecordset($data, $fields, $delete, $id))
            echo json_encode(array('type' => 'success', 'message' => 'Recordset successfully updated.'));
    }

    function deleteRecordset($id)
    {
        if ($this->recordsets_model->deleteRecordset($id))
            echo json_encode(array('type' => 'success', 'message' => 'Recordset successfully deleted.'));
    }
}


