<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * authentication library - functions to assist in validating and setting user sessions 
 *
 * @package		ps_authlib
 * @author		Galbraith Desmond <galbraithdesmond@gmail.com>
 * @description Assists in validating and setting user sessions.
 *
 */
 

class ps_authlib {
		
	var $user_session_folder; 

	public function __construct()
	{
		
		$CI =& get_instance();
					
		$this->user_session_folder = $CI->config->item('user_sessions_dir');
		
		if(!file_exists($this->user_session_folder)){
		
			$CI->load->library("ps_files");
			$CI->ps_files->create_dirpath($this->user_session_folder);
							
		}
		
	}
	
	
	function cookie_session_validate()
	{
		
		global $_COOKIE;
		
		$CI =& get_instance();
			
		
		if(isset($_COOKIE['psakey']) && $_COOKIE['psakey'] != ""){
			

			$sessionfile = $this->user_session_folder. md5($_COOKIE['psakey']);
			
			
			// look for the session file in /appname/user sessions
			if(file_exists($sessionfile)){
				
				// read the user session file and set $usrername and $password
				
				$sessiondata = file_get_contents($sessionfile);
				
				$sessiondata  = unserialize($sessiondata);
								
								
				$CI->db->where('usr_username', $sessiondata['username']);
				$CI->db->where('usr_password', md5($sessiondata['password']));
				
				$query = $CI->db->get("{$CI->db->dbprefix}users");
						
				
				if($query->num_rows == 1)
				{
					$row = $query->row_array(); 
					return $row;
					
				}else{
				
					return false;
					
				}
							
			}			
		}

		
	}
	
	public function set_persistent_session_cookie($data){
	
		// now set the cookie with the random key
		$cookieval = $this->genRandomString();
		

		
		
		
		$session_file = $this->user_session_folder. md5($cookieval);
		
		
		file_put_contents($session_file, serialize($data));
		

		if(isset($data['persist']) && $data['persist'] == true){
		
			setcookie("psakey", $cookieval, time()+(60*60*24*365), "/"); // one year
			
		}else{
		
			setcookie("psakey", $cookieval, time()+(60*60*24), "/"); // 24 hours 

		}
		
	}
	
	function genRandomString() {
		
		$length = 20;
		
		$characters = "0123456789abcdefghijklmnopqrstuvwxyz!@#$%^&*()";
		
		$string = "";    

		for ($p = 0; $p < $length; $p++) {
			
			$char = mt_rand(0, strlen($characters)-1);
			
			$string .= $characters[$char];
			
		}

		return $string;
		
	}

	
	
}

