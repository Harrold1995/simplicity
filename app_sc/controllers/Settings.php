<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('settings_model');
    }

    function index()
    {
        $this->meta['title'] = "Settings";
        $this->meta['h2'] = "Settings";
        redirect('settings/seditor');
    }

    function seditor()
    {
        $this->meta['title'] = "Settings Editor";
        $this->meta['h2'] = "Settings Editor";
        $data['settings'] = $this->settings->get_all();
        $data['page'] = 'seditor';
        $this->page_construct('settings/settingseditor', $data, $this->meta);
    }

    function ltemplates()
    {
		$this->load->model('tenantportal_model');
        $this->meta['title'] = "Lease Templates";
        $this->meta['h2'] = "Lease Templates";
        $data['page'] = 'ltemplates';
        $data['templates'] = $this->settings_model->getLTemplates();
        $data['token'] = $this->tenantportal_model->generateToken($_SESSION['user_id']);
        $this->page_construct('settings/leasetemplates', $data, $this->meta);
    }

	function pkeys()
	{
		$this->load->model('tenantportal_model');
		$this->meta['title'] = "Property Keys";
		$this->meta['h2'] = "Property Keys";
		$data['page'] = 'pkeys';
		$data['defkey'] = $this->settings_model->getDefKey();
		$data['properties'] = $this->settings_model->getProperties();
		$this->page_construct('settings/pkeys', $data, $this->meta);

	}

	function savepkeys() {
		$this->settings_model->savePkeys($_POST);
		$this->session->set_flashdata('msg', 'Successfuly Saved');
		redirect('settings/pkeys');
	}

    function getModal()
    {
        $key = $this->input->post('id');
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = base_url().'settings/addSetting';
                $this->data['title'] = 'Add Setting';
                break;
            case 'edit' :
                $this->data['target'] = base_url().'settings/editSetting/' . $key;
                $this->data['title'] = 'Edit Setting';
                $this->data['setting'] = $this->settings->$key;
                $this->data['setting_details'] = $this->settings_model->getSettingDetails($key);
                break;
        }
        $this->load->view('forms/setting', $this->data);
    }

    function getLTemplateModal()
    {
        $id = $this->input->post('id');
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = base_url().'settings/addLTemplate';
                $this->data['title'] = 'Add Lease Template';
                break;
            case 'edit' :
                $this->load->model('leases_model');
                $this->data['target'] = base_url().'settings/editLTemplate/' . $id;
                $this->data['title'] = 'Edit Lease Template';
                $this->data['template'] = $this->leases_model->getLeaseTemplate($id);
                break;
        }
        $this->load->view('forms/ltemplate', $this->data);
    }

    function addLTemplate()
    {
        $data = $this->input->post();
        if ($this->settings_model->addLTemplate($data))
            echo json_encode(array('type' => 'success', 'message' => 'Template successfully added.'));
    }

    function editLTemplate($id)
    {
        $data = $this->input->post();
        if ($this->settings_model->editLTemplate($data, $id))
            echo json_encode(array('type' => 'success', 'message' => 'Template successfully updated.'));
    }

    function deleteLTemplate($id)
    {
       if ($this->settings_model->deleteLTemplate($id))
            echo json_encode(array('type' => 'success', 'message' => 'Template successfully deleted.'));
    }

    function editSetting($key)
    {
        $data = $this->input->post('details');
        $values = $this->input->post('values');
        $fields = $this->input->post('fields');
        $setting = array();
        foreach($values as $value){
            if(count($fields) > 1) {
                $setting[$value['id']] = array();
                foreach ($fields as $find => $field) {
                    $setting[$value['id']][$field['name']] = $value[$find]['value'];
                }
            }else{
                $setting[$value['id']] = $value[0]['value'];
            }
        }

        $this->settings->$key = $setting;
        if ($this->settings_model->editSettingDetails($data, $key))
            echo json_encode(array('type' => 'success', 'message' => 'Setting successfully updated.'));
    }

    function addSetting()
    {
        $data = $this->input->post('details');
        $values = $this->input->post('values');
        $fields = $this->input->post('fields');
        $setting = array();
        $key = $data['key'];
        foreach($values as $value){
            if(count($fields) > 1) {
                $setting[$value['id']] = array();
                foreach ($fields as $find => $field) {
                    $setting[$value['id']][$field['name']] = $value[$find]['value'];
                }
            }else{
                $setting[$value['id']] = $value[0]['value'];
            }
        }

        $this->settings->$key = $setting;
        if ($this->settings_model->addSettingDetails($data))
            echo json_encode(array('type' => 'success', 'message' => 'Setting successfully added.'));
    }

    function deleteSetting($key)
    {
        $this->settings->delete($key);
        if ($this->settings_model->deleteSettingDetails($key))
            echo json_encode(array('type' => 'success', 'message' => 'Setting successfully deleted.'));
    }

    public function date_check($date1, $date2)
        {
                if ( new Date($date1) >= new Date($date2))
                {
                        //$this->form_validation->set_message('date_check', 'The start date is after the end date');
                        return FALSE;
                }
                else
                {
                        return TRUE;
                }
        }
}
