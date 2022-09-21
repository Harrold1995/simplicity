<?php
  abstract class Log {
    public $time = null;
    public $user_id = null;
    public $object_type = null;
    public $object_id = null;
    public $data = null;

    function __construct($user_id) {
      $this->user_id = $user_id;
      $this->time = date("Y-m-d H:i:s");
    }

    public static function fromDatabase($time, $user_id, $object_type, $object_id, $dataJSON) {
      $class = get_called_class();

      $instance = new $class($user_id);
      $instance->time = $time;
      $instance->object_type = $object_type;
      $instance->object_id = $object_id;
      $instance->data = json_decode($dataJSON);

      return $instance;
    }

    abstract public static function get_action_type();

    abstract public function get_data_text();

    public function get_data_json() {
      if ($this->data) { 
        return json_encode($this->data);
      };
      
      return null;
    }
  }

  class Log_Login extends Log {
    public static function get_action_type() {
      return 'login';
    }

    public function get_data_text() {
      return 'authorised';
    }
  }

  class Log_Logout extends Log {
    public static function get_action_type() {
      return 'logout';
    }

    public function get_data_text() {
      return 'logged out';
    }
  }

  /*
    DATA
    property_title: string
  */
  class Log_Property_Added extends Log {
    function __construct($user_id,  $property_id=null, $property_title=null) {
      parent::__construct($user_id);

      $this->user_id = $user_id;
      $this->object_type = "property";
      $this->object_id = $property_id;

      $this->data = array("property_title" => $property_title);
    }

    public static function get_action_type() {
      return 'property_added';
    }

    public function get_data_text() {
      return "created property <a href=\"#\" data-type=\"property\" \
              data-id=\"". $this->object_id."\" data-mode=\"edit\">". $this->data->property_title ."</a>";
    }
  }

  /*
    DATA
    property_title: string
  */
  class Log_Property_Deleted extends Log {
    function __construct($user_id,  $property_id=null, $property_title=null) {
      parent::__construct($user_id);

      $this->user_id = $user_id;
      $this->object_type = "property";
      $this->object_id = $property_id;

      $this->data = array("property_title" => $property_title);
    }

    public static function get_action_type() {
      return 'property_deleted';
    }

    public function get_data_text() {
      return "deleted property <b>". $this->data->property_title ."</b>";
    }
  }

  /*
    Data
    old_title: string
    new_title: string
  */
  class Log_Property_Title_Update extends Log {
    function __construct($user_id,  $property_id=null, $old_title=null, $new_title=null) {
      parent::__construct($user_id);

      $this->user_id = $user_id;
      $this->object_type = "property";
      $this->object_id = $property_id;

      $this->data = array("old_title" => $old_title, "new_title" => $new_title);
    }

    public static function get_action_type() {
      return 'property_changed_title';
    }

    public function get_data_text() {
      return "changed property title from ". $this->data->old_title ." to
        <a href=\"#\" data-type=\"property\" data-id=\"". $this->object_id."\" data-mode=\"edit\">". $this->data->new_title ."</a>";
    }
  }

  class Log_General extends Log {
    static $action_type;
    function __construct($user_id,  $object_type, $object_id = null, $action_type, $data=null ) {
      parent::__construct($user_id);
      self::$action_type = $action_type;
      $this->user_id = $user_id;
      $this->object_type = $object_type;
      $this->object_id = $object_id;
      $this->data = $data;

    }

    public static function get_action_type() {
      return self::$action_type;
    }

    public function get_data_text() { 
      return $action_type." ".$this->object_type." #
        <a href=\"#\"  data-id='50' class='reportLink' rtype='report' defaults='". $this->object_id."' >". $this->object_id."</a>";
    }

  }

  class Log_Rec extends Log {
    function __construct($user_id,  $reconciliation = null, $transactions = null,$statement_end_date = null , $closed = null , $property = null, $mode = null) {
      parent::__construct($user_id);


      $this->user_id = $user_id;
      $this->object_type = "rec";
      $this->object_id = $reconciliation['id'];
      $transactions1 = array_filter($transactions, function($v) {
        return $v == 1;
        });

      $transactions0 = array_filter($transactions, function($v) {
        return $v == 0;
      });

      $this->data = array("transactions_cl" => $transactions1, "transactions_uc" => $transactions0, "reconciliation" => $reconciliation);
    }

    public static function get_action_type() {
      return 'reconcilliation '.$mode;
    }

    public function get_data_text() { 
      return "created rec #
        <a href=\"#\"  data-id='50' class='reportLink' rtype='report' defaults='". $this->object_id."' >". $this->object_id."</a> for Account # <a href=\"#\"  data-id='50' class='reportLink' rtype='report' defaults='". $this->object_id."' >". $this->data->reconciliation->account_id ."</a>";
    }
  }
?>