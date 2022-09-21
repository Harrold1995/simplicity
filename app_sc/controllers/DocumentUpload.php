<?php defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentUpload extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->meta['title'] = "Document Upload";
        $this->meta['h2'] = "Document Upload";
        $this->page_construct('properties/index', null, $this->meta);
       
    }

    function attach_document($id){
        //$data = $this->input->post();
        $this->load->model('documents_model');
        $result = $this->documents_model->attach_document($id);
        echo json_encode($result);
    }

    function tooltipster($id){
        // $a = 'I am a tooltipster!'. $id;
        // echo $a;
        $this->load->model('documents_model');
        $result = $this->documents_model->get_documents($id);
        echo $result;
    }

    function attach_rec_document($id){
        $this->load->model('documents_model');
        $result = $this->documents_model->attach_rec_document($id);
        echo json_encode($result);
    }

}
