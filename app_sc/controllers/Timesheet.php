<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Timesheet extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->icons = Array("property" => "fas fa-building", "unit" => "far fa-building");
        $this->load->model('validate_model');
    }

    function index()
    {

        if (!$this->permissions->checkPermissions('timesheet_general')) return;
        $this->meta['title'] = "Timesheet";
        $this->meta['h2'] = "Timesheet";
        $this->page_construct('timesheet/index', null, $this->meta);


    }

    function addTimesheet($id) 
    {
        $errors = "";
        $this->load->model('timesheet_model');
        $data = $this->input->post();
        $data['profile_id'] = $id;

        if (/*$this->form_validation->run() && $validate['bool'] && */$this->timesheet_model->addTimesheet($data))
            echo json_encode(array('type' => 'success', 'message' => 'New timesheet successfully added.'));
        else {
            //$errors = $errors . validation_errors() ."</br>". $validate['msg'];
            echo json_encode(array('type' => 'danger', 'message' => $errors, 'errors' => $this->parse_errors('account')));
        }
    }

    
    public function getModal()
    {

        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = 'timesheet/addTimesheet/'. $this->input->post('id');
                $this->data['title'] = 'Add Timesheet';
                $this->load->view('forms/timesheet/main', $this->data);
                break;
            case 'edit' :
                $this->data['target'] = 'timesheet/editTimesheet/' . $this->input->post('id');
                $this->data['title'] = 'Edit Account';
                $this->load->view('forms/account/main', $this->data);
                break;
            case 'addProject':
            $this->load->view('forms/timesheet/main', $this->data);
                break;
        }

        
    }



    public function startEndTime()
        {
        $query = $this->db->query('SELECT id, profile_id, date, start_time, end_time, project FROM timesheet WHERE profile_id = 1 ORDER BY id DESC LIMIT 1');
        $row =  $query->row();
        $timer = '';
        if ($row->end_time ==! null) {
            $timer = 'Start';
        } else {
            $timer = 'End';
        }




        $timerInfo = array(
        'id' => $row->id,
        'date' => $row->date,
        'startTime' => $row->start_time,
        'endTime' => $row->end_time,
        'project' => $row->project,
        'timer' => $timer
        );
        echo json_encode( $timerInfo);
        }

        public function startTime()
        {
        $start_date = $this->input->post('start_date');
        $start_time = $this->input->post('start_time');
        $project = $this->input->post('project');
        $profile = $this->input->post('profile');
        echo $start_time;
        $query = $this->db->query("INSERT INTO `timesheet` (`id`, `profile_id`, `date`, `start_time`, `end_time`, `project`, `task`, `rate`, `bill`)
            VALUES (NULL, '$profile', '$start_date', '$start_time', null, '$project', task, 100, 0)");
        }

        public function endTime()
        {
        $end_time = $this->input->post('end_time');
        $profile = $this->input->post('profile');
        echo $end_time;
        $query = $this->db->query("UPDATE timesheet SET end_time= '$end_time' WHERE profile_id = '$profile' ORDER BY id DESC LIMIT 1");
        }



        
}






