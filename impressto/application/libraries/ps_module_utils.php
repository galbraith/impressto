<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* widget utilities - Makes working with widgets easy with reusable functions 
*
* @package		widget_utils
* @author		Galbraith Desmond
* @author		Gabriel Catellier
*
* @version		1.0.2 (2012-03-10)
*/


class ps_module_utils{

	private $CI;
	
	
	// Class Constructor. 	
	public function __construct(){
		
		
		$this->CI =&get_instance();
		
		

	}
	

	/**
	* Reads through all the app folders and returns the found modules.
	*
	*/
	public function get_modules(){
		
		
		$CI =& get_instance();
		
		$CI->load->helper('directory');
		
		$projectnum = $CI->config->item('projectnum');
		
		
		$module_states = array();
		$module_ids = array();
		
		//$CI->db->order_by("name", "desc");
		
		$query = $CI->db->get('modules');
		
		
		foreach ($query->result() as $row)
		{
			
			$module_states[$row->name] = $row->active;
			$module_ids[$row->name] = $row->id;
			
		}


		
		$modules_array = array();

		$locations = array(

		$projectnum . "/modules",
		"custom_modules",
		"core_modules"
		
		);
		
		
		$module_types_array = array("core"=>array(),"custom"=>array());
		
		

		foreach($locations as $location){
			
			$moduledir = APPPATH . $location;
			
			if(file_exists($moduledir)){
				
				if ($files_handle = opendir($moduledir)) {

					while (false !== ($file = readdir($files_handle))) {
						

						if(is_dir($moduledir . "/". $file) && $file != "." && $file != ".."){
							
							$config_file = $moduledir . "/". $file . "/config/config.php";
							
							if(file_exists($config_file)){
								
								// initialize the array in case the new entry is bogus
								$config['module_config'] = array();
								$module_data = array();
								
								include($config_file);
								
								if(isset($config['module_config']['name'])){
									
									
									$module_data['description'] = $config['module_config']['description'];
									$module_data['author'] = $config['module_config']['author'];
									$module_data['path'] = $moduledir . "/". $file;
									$module_data['name'] = $config['module_config']['name'];
									$module_data['admin_menu_section'] = $config['module_config']['admin_menu_section'];
									
									if(isset($config['module_config']['url'])) $module_data['url'] = $config['module_config']['url'];
									else $module_data['url'] = $file;
									
									if(isset($config['module_config']['version'])) $module_data['version'] = $config['module_config']['version'];
									else $module_data['version'] = "N/A";
									
									if(isset($config['module_config']['last_updated'])) $module_data['last_updated'] = $config['module_config']['last_updated'];
									else $module_data['last_updated'] ="N/A";
									
									
									
									
									if(!array_key_exists($file, $module_states)){ 
										
										// add the new;y found module but do not activate it.
										$data = array('name' => $file,'active' => 'N');
										$CI->db->insert('modules', $data); 
										$module_ids[$file] = $CI->db->insert_id();
										$module_states[$file] = 'N';
										
									}
									
									
									if(isset($module_states[$file]) && $module_states[$file] == 'Y'){
										$module_data['active'] = TRUE;
									}else{
										$module_data['active'] = FALSE;
									}
									

									if(isset($module_ids[$file]) && $module_ids[$file] != ''){
										$module_data['id'] = $module_ids[$file];
									}else{
										$module_data['id'] = null;
									}
									
									if(isset($config['module_config']['module_type'])){
										$module_data['module_type'] = $config['module_config']['module_type'];
									}else{
										$module_data['module_type'] = "custom";
									}	
									
									
									if($module_data['module_type'] == "core"){
										
										$module_types_array['core'][$file] = $module_data;
										
									}else{
										
										$module_types_array['custom'][$file] = $module_data;
									}
									
									
								}
								
							}	
							
						}

					}
				}
			}
			
		}
		
		foreach($module_types_array['custom'] AS $file => $module_data){
			
			if (!array_key_exists($file, $modules_array)) {
				$modules_array[$file] = $module_data;
			}
		}
		
		foreach($module_types_array['core'] AS $file => $module_data){
			
			if (!array_key_exists($file, $modules_array)) {
				$modules_array[$file] = $module_data;
			}
		}
		

		return $modules_array;
		
		
	}
	
	
	

	/**
	* This is called when a new module is found
	*
	*/ 
	public function register_module($module){

		
		// check if the widget is in the table. if it is get the id otherwise insert it and get the id
		$sql = "SELECT widget_id FROM ps_modules WHERE module = '{$module}' AND widget = '{$widget}' AND instance = '{$instance_name}' ";
		
		$query = $this->CI->db->query($sql);
		

		if ($query->num_rows() > 0) {
			
			$row = $query->row();
			
			$widget_id = $row->widget_id;
			
			if($new_instance_name != "" && $new_instance_name != $instance_name){
				
				$sql = "UPDATE {$this->CI->db->dbprefix}widgets SET module = '{$module}', widget = '{$widget}', instance = '{$new_instance_name}' ";
				$sql .= "WHERE widget_id = '{$widget_id}' ";
				
				$this->CI->db->query($sql);
				
				
			}
			
			
		} else {
			
			if($new_instance_name != "" && $new_instance_name != $instance_name) $instance_name = $new_instance_name;
			
			$sql = "INSERT INTO {$CI->db->dbprefix}widgets SET module = '{$module}', widget = '{$widget}', instance = '{$instance_name}'";
			$this->CI->db->query($sql);
			
			$widget_id =  $this->CI->db->insert_id() ; 
			
		}	
		
		
		return $widget_id;
		
	}


	

	
	
}