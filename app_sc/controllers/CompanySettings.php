<?php defined('BASEPATH') OR exit('No direct script access allowed');

class CompanySettings extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
    }

    function index()
    {
        $this->meta['title'] = "Company Settings";
        $this->meta['h2'] = "Company Settings";
        //$this->page_construct('Employees/index', null, $this->meta);
    }

    function getModal(){
        $this->load->model('leases_model');
        //$this->load->model('ion_auth');
        switch ($this->input->post('mode')) {
        case 'companySettings' :
                $this->data['target'] = base_url(). 'companySettings/editcompanySettings';
                $this->data['title'] = 'Company Settings';
                $this->data['companySettings'] = $this->getCompanySettings();
                $this->data['lcsetups'] = $this->leases_model->getLateChargeSetups();
                $this->data['users'] = $this->ion_auth->users()->result();
            break;
        }
        $this->load->view('forms/companySettings/main2', $this->data);
    }

        public function getCompanySettings()
    {
        $this->load->model('encryption_model');
        //$this->db->where('active', 1);
        $q = $this->db->get('company_settings');
        //$q = $this->db->get_where('company_settings', array('id' => 1), 1);
        if ($q->num_rows() > 0) {
            $data = $q->row();
            $data = $this->encryption_model->decryptThis($data);
            return $data;
        }
        return false;
    }

    function editcompanySettings(){
        $this->load->model('encryption_model');
        $companySettings = $this->input->post('companySettings');
        $companySettings = $this->encryption_model->encryptThis($companySettings);

        //for logo 

        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['upload_path'] = 'uploads/images';
        $this->load->library('upload', $config);
        $this->upload->do_upload('image');
        if($this->upload->data('file_name')){
            $companySettings['company_logo'] = $this->upload->data('file_name');
        }
        


        if(array_key_exists('id', $companySettings)){
            if($this->db->update('company_settings', $companySettings, array('id' => $companySettings['id']))){
                echo json_encode(array('type' => 'success', 'message' => 'Company settings successfully updated.'));
            }else{
                echo json_encode(array('type' => 'danger', 'message' => 'Company settings failed to  update.'));
            }
        }else{
            if($this->db->insert('company_settings', $companySettings)){
                echo json_encode(array('type' => 'success', 'message' => 'Company settings successfully submitted.'));
            }else{
                echo json_encode(array('type' => 'danger', 'message' => 'Company settings failed to  insert.'));
            }
        }

            // $csid = $companySettings['id'];
            // unset($companySettings['id']);
            //$this->db->update('company_settings', $companySettings, array('id' => $aid));
    }
}
