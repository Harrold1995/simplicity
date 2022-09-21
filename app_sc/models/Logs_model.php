<?php defined('BASEPATH') or exit('No direct script access allowed');
class Logs_model extends CI_Model
{
  private $log_classes = array(
    Log_Login, Log_Logout, Log_Property_Added,
    Log_Property_Title_Update, 
    Log_Rec, Log_general
  );

  public function __construct()
  {
      parent::__construct();
  }

  public function add_log(Log $log) {
    $action_type = $log::get_action_type();
    $action_type = is_null($action_type) ? $log->action_type : $action_type;
    $this->db->insert('users_log', Array(
      "time" => $log->time,
      "user_id" => $log->user_id,
      "action_type" => $action_type,
      "object_type" => $log->object_type,
      "object_id" => $log->object_id,
      "data" => $log->get_data_json()
    ));
  }

  public function get_logs($user_id = false)
  {
      if (!$user_id) {
          return null;
      }

      $this->db->select('ul.*');
      $this->db->from('users_log ul');
      $this->db->where('ul.user_id', $user_id);
      $this->db->order_by('ul.time DESC');
      $q = $this->db->get();

      if ($q->num_rows() > 0) {
          foreach (($q->result()) as &$row) {
              $action_type = $row->action_type;

              foreach ($this->log_classes as $log_class) {
                if ($log_class::get_action_type() == $action_type) {
                  $data[] = $log_class::fromDatabase(
                    $row->time,
                    $row->user_id, 
                    $row->object_type,
                    $row->object_id, 
                    $row->data
                  );
                  break;
                }
              }
          }
          return $data;
      }
      return null;
  }
}