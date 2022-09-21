<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->settings->unit_types = Array(1 => "2 Beds", 2 => "Studio");
        $this->settings->late_charge_amount_types = Array(0 => "%", 1 => "$");
        $this->settings->pet_types = Array(
            0 => Array("name" => "None", "deposit" => 0),
            1 => Array("name" => "Cats", "deposit" => 200),
            2 => Array("name" => "Dogs", "deposit" => 300)
            );
        $this->settings->profile_types = Array(1 => "Tenant", 2 => "Broker");
        $this->settings->tree_icons = Array("property" => "fas fa-building", "unit" => "far fa-building", "tenant" => "far fa-user");
        $this->settings->charge_types = Array(1 => "Rent Charge", 2 => "Security Deposit", 3 => "Maintenance", 4 => "Repairs", 5 => "Last Month's Rent", 6 => "Refund", 7 => "Misc.");
        $this->initValidation();
    }

    function initValidation()
    {
        $this->settings->propertyFormValidation = array(
            array(
                'field' => 'property[name]',
                'label' => 'Property name',
                'rules' => 'trim|required|min_length[5]'
            ),
            array(
                'field' => 'property[address]',
                'label' => 'Address',
                'rules' => 'trim|required|min_length[5]',
            ),
            array(
                'field' => 'image',
                'label' => 'File',
                'rules' => 'file_allowed_type[image]',
            )
        );
        $this->settings->leaseFormValidation = array(
            array(
                'field' => 'original',
                'label' => 'File',
                'rules' => 'file_allowed_type[document]'
            )
        );
        $this->settings->accountFormValidation = array(
            array(
                'field' => 'account[name]',
                'label' => 'Account name',
                'rules' => 'trim|required|min_length[5]'
            )
        );
    }
}
