<?php defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(E_ALL);
ini_set('display_errors', '1');

class Paymentapi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function init_ach() {
    	//$data = $_POST;
    	$data = Array("routing" => '021000021', "account" => '1111111', "name" => 'John Doe', "amount" => 2);
		$postData = array(
			"xKey" => "simplicitydevd335b9d66a384299b7569f8301a27edc",
			"xVersion" => "4.5.9",
			"xSoftwareName" => "Tenant Portal",
			"xSoftwareVersion" => "1.0",
			"xCommand" => 'check:sale',
			"xAmount" => $data['amount'],
			"xRouting" => $data['routing'],
			"xAccount" => $data['account'],
			"xName" => $data->name,
		);

		$handler = curl_init();

		curl_setopt($handler, CURLOPT_URL, "https://x1.cardknox.com/gatewayjson");
		curl_setopt($handler, CURLOPT_POSTFIELDS, json_encode($postData));
		curl_setopt($handler, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($handler, CURLOPT_POST, true);

		$response = curl_exec($handler);

	}


}
