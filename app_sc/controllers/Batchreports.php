<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Batchreports extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('reports_model');
        $this->load->model('reportsTable_model');
        $this->load->model('batchreports_model');
        $_SESSION['allTypes'] = $this->reports_model->getAllTypes();
        session_write_close();
    }

    function index()
    {
        $this->load->model('properties_model');
        $this->meta['title'] = "Batch Reports";
        $this->meta['h2'] = "Batch Reports";
        $this->data['properties'] = $this->properties_model->getAllProperties();
        $this->page_construct('batchreports/index', $this->data, $this->meta);
    }

    function edit($id)
    {
        $this->meta['title'] = "Batch Report Editor";
        $this->meta['h2'] = "Batch Report Editor";
        $report = $this->batchreports_model->getBatchReport($id)->settings;
        $this->data['batch'] = $report;
        $this->data['id'] = $id;
        $this->page_construct('batchreports/edit', $this->data, $this->meta);
    }

    function new()
    {
        $this->meta['title'] = "Batch Report Editor";
        $this->meta['h2'] = "Batch Report Editor";
        $this->page_construct('batchreports/edit', $this->data, $this->meta);
    }


    function getBatchReportAjax($id = null) {
        echo json_encode($this->batchreports_model->getBatchReport($id)->settings);
    }

    function saveBatch($id) {
        $settings = json_encode($this->input->post('data'));
        $name = $this->input->post('data')['name'];
        //print_r($this->input->post('data')['name']);
        $redirect = null;
        if ($this->batchreports_model->saveBatch($id, $settings, $name));
            echo json_encode(array('type' => 'success', 'message' => 'Batch successfully saved.', 'redirect' => $redirect));
    }

    function getEditRightHtmlAjax($id = null) {
        $data = $this->input->post('data');
        foreach($data['filters'] as $filter) {
            $filters[$filter['column']] = (Object)$filter;
        }
        foreach($data['params'] as $param) {
            $params[$param['key']] = (Object)$param;
        }
        $this->data['filters'] = $filters;
        $this->data['params'] = $params;
        $this->data['settings'] = (Object)$data;
        $this->data['report'] = $this->reports_model->getReport($id);
        $this->data['report']->settings = json_decode($this->data['report']->settings);
        $temp = $this->reports_model->getAllColumnsArray($this->data['report']->settings->type);
        foreach ($temp as $c) {
            $newc[$c['id']] = $c;
        }
        $this->data['columns'] = $newc;
        $this->load->view('batchreports/edit-filters', $this->data, $this->meta);
    }

    function loadRight() {
        $id = $this->input->post('id');
        $batch = $this->batchreports_model->getBatchReport($id);
        $this->data['signs'] = Array("equals", "not Equals", "like", "between", "greater than", "less than", "is in");
        $this->data['fields'] = $batch->settings->fields;
        $this->load->view('batchreports/right', $this->data, $this->meta);
    }

    function loadBodyWrapper() {
        $this->load->view('batchreports/bodywrapper', $this->data, $this->meta);
    }

    function loadList() {
        $data = $this->batchreports_model->getBatchList();
        $columns = Array();
        $columns[] = (object)Array("id" => "check", "field" => "check", "name" => "", "formatter" => "checkformatter", "width" => 10);
        $columns[] = (object)Array("id" => "name", "field" => "name", "name" => "Name");
        $columns[] = (object)Array("id" => "link", "field" => "links", "name" => "Links");
        echo json_encode(Array('columns' => $columns, 'data' => $data));
    }

    function loadReportsList() {
        $data = $this->batchreports_model->getReportList();
        $columns = Array();
        $columns[] = (object)Array("id" => "check", "field" => "check", "name" => "", "formatter" => "checkformatter", "width" => 5);
        $columns[] = (object)Array("id" => "name", "field" => "name", "name" => "Name", "width" => 300);
        echo json_encode(Array('columns' => $columns, 'data' => $data));
    }

    function test() {
        for($i = 0; $i< 20000000; $i++) {
            echo $i;
        }
    }
}