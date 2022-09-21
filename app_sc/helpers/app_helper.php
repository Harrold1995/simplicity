<?php

if(!function_exists('sqlDate'))
{
  function sqlDate($date)
  {
    if(stripos($date, '-') !== false) $test_arr  = explode('-', $date);
    if(stripos($date, '/') !== false) $test_arr  = explode('/', $date);
    if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) {
      $sqlDate = date_create_from_format('m-d-Y', str_replace('/', '-', $date))->format('Y-m-d');
      return $sqlDate;
    } else {
        return $date;
      }
  }
}

if(!function_exists('humanDate'))
{
  function humanDate($date)
  { 
    if(stripos($date, '-') !== false) $test_arr  = explode('-', $date);
    if(stripos($date, '/') !== false) $test_arr  = explode('/', $date);
    if (checkdate($test_arr[1], $test_arr[2], $test_arr[0])) {
      $humanDate = date_create_from_format('Y/m/d', str_replace('-', '/', $date))->format('m/d/Y');
      return $humanDate;
    } else {
        return $date;
    }
    
  }
}

if(!function_exists('removeComma'))
{
  function removeComma($value)
    {
         return str_replace(',', '', $value);
    }
}
 
//in config/autoload.php line 92 add helper file
//$autoload['helper'] = array('url', 'form', 'app');

// if(!function_exists('addCheck1'))
// {
//   function addCheck1()
//   {
//     $checks = require_once  "app_sc/controllers/checks.php";    
//      $CI = & get_instance();        
//      //$CI->check = new Checks();
//      $CI->checks->addChecks();
//  //$checks->addCheck();
// }
// }

// $_POST =  [
        //     'one' =>  [
               
        //         'profile_id' => 6, 'lease_id' => 7, 'property_id' => 8, 'unit_id' => 94, 'trans_id' => 15
        //      ],
        //     'two' =>  [
        //         ['id' => 1, 'profile_id' => 2, 'property_id' => 4, 'unit_id' => 4, 'trans_id' => 9],
        //         ['id' => 1, 'profile_id' => 2, 'property_id' => 3, 'unit_id' => 4, 'trans_id' => 5]
        //      ],
        //     'three' =>  [
        //         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
        //         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
        //         ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5]]
        //     ];
        // $post =$this->input->post();
        //var_dump($post);
        $data = [
          'one' =>  [
             
              'profile_id' => 6, 'lease_id' => 7, 'property_id' => 8, 'unit_id' => 94, 'trans_id' => 15
           ],
          'two' =>  [
              ['id' => 1, 'profile_id' => 2, 'property_id' => 4, 'unit_id' => 4, 'trans_id' => 9],
              ['id' => 1, 'profile_id' => 2, 'property_id' => 3, 'unit_id' => 4, 'trans_id' => 5]
           ],
          'three' =>  [
              ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
              ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5],
              ['hello' => 1, 'lease_id' => 2, 'good' => 3, 'unit_id' => 4, 'profile_id' => 5]]
          ];
          // foreach($post as $key => &$value){
          //     foreach($value as &$val){
          //     if(is_array($val)){
          //         unset($val['id']);
          //     }else{unset($value['id']);}
              
              
          // }
          // //unset($value['say']);
          // $result[$key] = $value;
          // }

//       foreach($post as $key=>$value)
// {
//       $$key = $value;
// }