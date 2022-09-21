 <?php
class MY_Exceptions extends CI_Exceptions {

function __construct()
{
    parent::__construct();
}

function log_exception($severity, $message, $filepath, $line)

{   
    //if (ENVIRONMENT === 'production') {
        
    //}

     $mailTo = ['joshua@simpli-city.com','ycraven@simpli-city.com','debbie@simpli-city.com'];
		
			$CI =& get_instance();
		
		//this page loads before codeigniter instance is instantiated therefore at times will be null
		// if ($CI === null) {
		// 	new CI_Controller();
		// 	$CI =& get_instance();
		// } 
		
		
		$CI->load->library('ion_auth');
		$user_id = $CI->ion_auth->get_user_id();
		
		$CI->db->select('CONCAT_WS(" ",p.first_name,p.last_name) AS user');
        $CI->db->from('profiles p'); 
        $CI->db->join('users u', 'p.id = u.profile_id'); 
        $CI->db->where('u.id', $user_id);
        
        $q = $CI->db->get(); 
       
        if ($q->num_rows() > 0) {
            $user =  $q->row()->user;
        }
        $CI->load->library('email');
        //$trace =  debug_backtrace();
			$body = 'Severity: '.$severity.'  --> '.$message. ' '.$filepath.' '.$line;
            $subject = $user . ' got error from server';
            $myEmail = $CI->email;
            
			$myEmail->from('joshua@simpli-city.com')
                    ->reply_to('joshua@simpli-city.com')    // Optional, an account where a human being reads.
				    ->to($mailTo)
                    ->subject($subject)
                    ->message($body)
                    ->send(); 


    parent::log_exception($severity, $message, $filepath, $line);
}

} 