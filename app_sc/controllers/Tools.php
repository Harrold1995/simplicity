<?php
class Tools extends CI_Controller {

        public function message($to = 'World')
        {
                if(is_cli()){
                   echo "Hello {$to}!";
                }
                 
        }
}
//php say run php path with / controller and method just space?
//php index.php tools message shea