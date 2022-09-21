<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Htmlapi extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function getLateChargeRow()
    {
        $ind = $this->input->post('count');
        echo '<section>
                <h3>Rule #' . ($ind - 1) . '</h3>
                <p>
                    <label>On the</label>
                    <input type="text" size="3" value=""  name="rules[' . $ind . '][day]">
                    <label>th of the month, charge</label>
                    <input type="text" size="3" value="" name="rules[' . $ind . '][amount]">
                    <span class="select">
                        <select  name="rules[' . $ind . '][type]">
                                        ';
                                $amount_types = $this->settings->late_charge_amount_types;
                                foreach ($amount_types as $id => $at)
                                        echo'<option value="'.$id.'" '.($rule->type == $id ? 'selected' : '').'>'.$at.'</option>';

                                echo'   
                        </select>
                    </span>
                    <label>On</label>
                    <span class="check-a">
                            <label for="rule' . $ind . '-all" class="checkbox">All Charge Types
                                <input type="checkbox" value="0" name="rules[' . $ind . '][all_types]"  id="rule' . $ind . '-all" name="rules[' . $ind . '][all_types]" onchange="$(this).parent().next().prop(\'disabled\', function(i, v) { return !v; });">
                                <span class="input"></span>
                            </label>
                    
                    </span>
                    <label>or</label>
                    <a class="btn cpopup-trigger" data-target="#rulepopup' . $ind . '" href="#">Choose Charge Types</a>
                </p>
                <div class="cpopup c-top c-right" id="rulepopup' . $ind . '">
                    <h4>Charge types:</h4> <ul class="check-a">';
        $ctypes = $this->db->get('items');
        if ($ctypes->num_rows() > 0) {
            foreach (($ctypes->result()) as &$row) {
                echo '<li class="custom-control custom-checkbox form-group mb-0">

            <label for="rules' . $ind . 'ctypes' . $row->id . '" class="checkbox"><input type="checkbox" ' . (in_array($row->id, $rule->ctypes) ? "checked" : '') .  'value="1" class="hidden" aria-hidden="true"
            name="rules[' . $ind . '][ctypes][' . $row->id . ']" id="rules' . $ind . 'ctypes' . $row->id . '"><span class="input"></span>' . $row->item_name .'</label>

                </li>';
            }
           
        }
        /* $ctypes = $this->settings->charge_types;
        foreach ($ctypes as $cid => $cname) {
            echo '<li class="custom-control custom-checkbox form-group mb-0">

            <label for="rules' . $ind . 'ctypes' . $cid . '" class="checkbox"><input type="checkbox" ' . (in_array($cid, $rule->ctypes) ? "checked" : '') .  'value="1" class="hidden" aria-hidden="true"
            name="rules[' . $ind . '][ctypes][' . $cid . ']" id="rules' . $ind . 'ctypes' . $cid . '"><span class="input"></span>' . $cname .'</label>

                </li>';
        } */
        echo '    </ul> 
                  <p>  <a href="#" class="btn cpopup-trigger" data-target="#rulepopup' . $ind . '">done</a> </p>
                </div>
                <br/><br/>
            </section>';
    }

    function getSettingRow()
    {
        $cols = explode(',',$this->input->post('columns'));
        $id = $this->input->post('id') + 1;
        $ind = $id;
        echo '<div class="row">
                <div class="form-group col-1">
                    <div class="field-input">
                        <input type="text" value="' . $id . '" class="form-control setting-id" name="values[' . $ind . '][id]" readonly>
                    </div>
                </div>
                <div class="form-group col-11 row" row-id="'.$ind.'">';

                for ($ii=0;$ii<count($cols);$ii++) {
                    echo'<div class="field-input col" column="'.$cols[$ii].'">
                        <input type="text" value="" class="form-control" name="values['.$ind.']['.$cols[$ii].'][value]">
                    </div>';
               }
        echo'<a href="#" class="delete-option" onclick="$(this).parent().parent().remove();"><i class="fas fa-times-circle"></i></a>
                                                   
                </div>
            </div>';
    }
}
