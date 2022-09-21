<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->meta['title'] = "Dashboard";
        $this->meta['h2'] = "Dashboard";
        $this->page_construct('dashboard/index', null, $this->meta);
    }

    function outStandingRent(){
        $data = array('num' => '$12,456.90');
        echo json_encode($data);
    }

    function upcomingVacancies(){
        $data = array('num' => '45');
        echo json_encode($data);
    }

    function openMaintenance(){
        $data = array('num' => '75');
        echo json_encode($data);
    }

    function newBankTrans(){
        $data = array('num' => '75');
        echo json_encode($data);
    }

    function transByMonth($id){
        $this->load->model('accounts_model');
        echo json_encode($this->accounts_model->getSumTransactions($id));
    }

}