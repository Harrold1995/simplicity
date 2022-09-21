<?php defined('BASEPATH') OR exit('No direct script access allowed');
//include_once('Profiles_model.php');

class Timesheet_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('accounts_model');
    }

    public function addTimesheet($data)
    {
        $data['rate'] = 2.5;
        $this->db->insert('timesheet', $data);
        return true;
    }

    public function getEmployee($id)
    {
        $q = $this->db->get_where('profiles', array('id' => $id), 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getEmployeesList()
    {
        $this->db->select('p.*, CONCAT_WS(" ",p.first_name,p.last_name) as name');
        $this->db->from('profiles p');
        $this->db->where('profile_type_id', 4);
       // $this->db->join('leases l', 'lp.lease_id = l.id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                // if ($addtype == true) {
                     $row->type = 'employees';
                //     $row->info = '$' . (int)$row->amount;
                // }
                $data[] = $row;
            }
            return $data;
        }
        return null;
    }

    public function getSingleEmployeeTimesheet($id)
    {
        $totalDays = 0;
        $totalHours = 0;
        $time_arr = [];
        $totalPay = 0;
        $this->db->select('t.*');
        $this->db->from('timesheet t');
        $this->db->where('profile_id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                $one = new DateTime($row->start_time);
                $two = $row->end_time;
                $since_start = $one->diff(new DateTime($two));

                $decimal = $since_start->i/60;  //get minutes as decimal
                $hoursAsDecimal = $since_start->h+$decimal;
                $row->total = $hoursAsDecimal * $row->rate;
                if($since_start->d > 0){
                    $days = 24 * $since_start->d;
                    $row->total += $days * $row->rate;
                    $totalDays += $since_start->d;
                    $since_start->h += $since_start->h + $days;
                }
                $minutes = $since_start->i;
                if($since_start->i < 10){ 
                    $minutes = ("0" . $since_start->i);
                    }
                $row->duration = $since_start->h . ':' . $minutes;//for display to user
                if($row->project == 'Lunch'){
                    $lunch_time_arr[] = $row->duration;
                }
                $time_arr[] = $row->duration;

                //$time_arr[] = $row->duration;
                $totalPay += $row->total;
                $data[] = $row;
            }
            foreach ($time_arr as $time_val) {//function to add hours together
                $totalHours += $this->explode_time($time_val); // this fucntion will convert all hh:mm to seconds
            }
            foreach ($lunch_time_arr as $time_val) {//function to add hours together
                $totalLunchHours += $this->explode_time($time_val); // this fucntion will convert all hh:mm to seconds
            }
            $totalHours = $this->second_to_hhmm($totalHours); // this function will  convert all seconds to HH:MM.
            $totalLunchHours = $this->second_to_hhmm($totalLunchHours); // this function will  convert all seconds to HH:MM.
            $data['totalHours'] = $totalHours;
            $data['totalLunchHours'] = $totalLunchHours;
            $data['totalPay'] = $totalPay;
            return $data;
        }
        return null;
    }

    function explode_time($time) { //explode time and convert into seconds
        $time = explode(':', $time);
        $time = $time[0] * 3600 + $time[1] * 60;
        return $time;
    }

    function second_to_hhmm($time) { //convert seconds to hh:mm
            $hour = floor($time / 3600);
            $minute = strval(floor(($time % 3600) / 60));
            if ($minute == 0) {
                $minute = "00";
            } else {
                $minute = $minute;
            }
            $time = $hour . ":" . $minute;
            return $time;
    }


}
