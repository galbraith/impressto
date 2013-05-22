<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * widget utilities - Makes working with widgets easy with reusable functions 
 *
 * @package		widget_utils
 * @author		Galbraith Desmond
 * @author		David Gorman
 * @version		1.0.2 (2012-03-10)
 */
 

class widget_utils{

	
	// Class Constructor. 	
	public function __construct(){
	
	

					
	

	}
	
	
	
	/**
	* returns an array of all widget attributes
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	* @param widget id
	* @return array containing requested widget attributes
	*/ 
	public function get_widget_options($widget_id){
	
		$return_array = array();
		
		$CI = &get_instance();
		
	
		$CI->db->select('widget_options.name, widget_options.value, widgets.module AS widget_module, widgets.widget AS widget_type, widgets.instance AS widget_name');
		$CI->db->from('widget_options');
		$CI->db->join('widgets', 'widget_options.widget_id = widgets.widget_id');
		$CI->db->where('widget_options.widget_id', $widget_id);
		$query = $CI->db->get();

				
		if ($query->num_rows() > 0){
			
			foreach ($query->result() as $row){
				
				$return_array[$row->name] = $row->value;
				
				$return_array['widget_module'] = $row->widget_module;
				$return_array['widget_type'] = $row->widget_type;
				$return_array['widget_name'] = $row->widget_name;
				
				$return_array['widget_id'] = $widget_id;
			}
				
		}
		
		return $return_array;
		

	}
	
	/**
	* 
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	*/ 
	public function	getwidgetcollections()
	{
	
		$CI = &get_instance();
			
		$query = $CI->db->get("{$CI->db->dbprefix}widget_collections");
		
		$return_array = array("Select"=>"");
		

		if ($query->num_rows() > 0){
			
			foreach ($query->result() as $row){
				
				$return_array[$row->name] = $row->id;
				
			}
				
		}
		
		return $return_array;

	}
	
	
	
	/**
	* 
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	*/ 
	public function get_widget_name($widget_id){
	
		$CI = &get_instance();
			
		//$sql = "SELECT instance FROM {$CI->db->dbprefix}widgets WHERE widget_id = '{$widget_id}'";
		
		$CI->db->select('instance');
		
		$query = $CI->db->get_where('widgets', array('widget_id' => $widget_id ));

		$rowdata = $CI->db->get_where('widgets', array('widget_id'=> $widget_id))->row();
			
					
		if ( isset($rowdata->instance)){
		
			return $rowdata->instance;
		
		}
		
		return false;
				

	}
	
	/**
	* simply retun the widget id matching the parameters
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	*/
	public function getwidgetid($widget, $module = '', $instance_name = ''){
		
		
		$CI = &get_instance();
				
		$query = $CI->db->get_where('widgets', array('module' => $module,'widget' => $widget, 'instance' => $instance_name  ));
					
		
		if ($query->num_rows() == 1) {
			
			$row = $query->row();
			return $row->widget_id;
			
		}
		
		return false;
			
	}
	
	
	/**
	* This is called when a widget is deleted. It will remove the widget and any attributres
	* associated with it. 
	*
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	*/ 
	public function delete_widget($widget_id){
			
		$CI = &get_instance();
			
	
		$CI->db->delete('widgets', array('widget_id' => $widget_id)); 
		
		$CI->db->delete('widget_options', array('widget_id' => $widget_id));
		
		$CI->db->delete('widget_placements', array('widget_id' => $widget_id));
			
		
				
	}
	
	
	
	/**
	* This is called when deleting a widget and we o not have the id of the widget. It will remove the widget and any attributres
	* associated with it. 
	*
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	*/ 
	public function un_register_widget($widget, $module = '', $instance_name = ''){
		
		$widgetid = $this->getwidgetid($widget, $module, $instance_name);
		
		$this->delete_widget($widgetid);
				
				
	}
	
	


	/**
	* This is called when a new widget is installed or a widget instance is created
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	*/ 
	public function register_widget($widget, $module = '', $instance_name = '', $new_instance_name = ''){
		
		$CI = &get_instance();
				
		// check if the widget is in the table. if it is get the id otherwise insert it and get the id
		$sql = "SELECT widget_id FROM {$CI->db->dbprefix}widgets WHERE module = '{$module}' AND widget = '{$widget}' AND instance = '{$instance_name}' ";
		$query = $CI->db->query($sql);
		

		if ($query->num_rows() > 0) {
			
			$row = $query->row();
			
			$widget_id = $row->widget_id;
			
			if($new_instance_name != "" && $new_instance_name != $instance_name){
			
				$sql = "UPDATE {$CI->db->dbprefix}widgets SET module = '{$module}', widget = '{$widget}', instance = '{$new_instance_name}' ";
				$sql .= "WHERE widget_id = '{$widget_id}' ";
			
				$CI->db->query($sql);
				
						
			}
			
			
		} else {
			
			if($new_instance_name != "" && $new_instance_name != $instance_name) $instance_name = $new_instance_name;
		
			$sql = "INSERT INTO {$CI->db->dbprefix}widgets SET module = '{$module}', widget = '{$widget}', instance = '{$instance_name}'";
			$CI->db->query($sql);
			
			$widget_id =  $CI->db->insert_id() ; 
			
		}	
		
				
		return $widget_id;
		
	}

	/**
	* Set all widget options in a single function call
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	* @since 2012/10/19
	* @var int widget id
	* @var array widget option data
	*/ 
	public function set_widget_options($widget_id, $data){
	
		$CI = &get_instance();
			
		if(!is_array($data)) return;
		
		foreach($data as $name => $value){
		
			$sql = "DELETE FROM {$CI->db->dbprefix}widget_options WHERE widget_id = '{$widget_id}' AND name = '{$name}'";
			$CI->db->query($sql);
			
			// just in case some crazy mf wants to store array, keeping in mind we only have 255 characters to work with
			if(is_array($value)) $value = serialize($value); 
			
			$sql = "INSERT INTO {$CI->db->dbprefix}widget_options SET widget_id = '{$widget_id}', name = '{$name}', value = '{$value}'";
			$CI->db->query($sql);
		
		}
		
	}
	
	
	
	/**
	* This is called when a new widget is installed or a widget instance is created
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	*/ 
	public function set_widget_option($widget_id, $name, $value = ''){
	
		$CI = &get_instance();
				
		$sql = "DELETE FROM {$CI->db->dbprefix}widget_options WHERE widget_id = '{$widget_id}' AND name = '{$name}'";
		$CI->db->query($sql);
		
			
		$sql = "INSERT INTO {$CI->db->dbprefix}widget_options SET widget_id = '{$widget_id}', name = '{$name}', value = '{$value}'";
		$CI->db->query($sql);
		
		
	}
	
	
	
	
	/**
	* load a widgets config file.
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>	
	* @return array
	*/
	public function load_widget_config($widget_name, $module = ''){
		
		$CI = &get_instance();
				
		$projectnum = $CI->config->item('projectnum');
		
		if($module == ""){
			$locations = array($projectnum . "/widgets",	"widgets" );
		}else{
		
			$locations = array(
				$projectnum . "/modules/" . $module . "/widgets",
				"custom_modules/" . $module . "/widgets",
				"core_modules/" . $module . "/widgets"
			);
		
		}
				
		
		foreach($locations as $location){
			
			$widgetconfigfile = APPPATH . $location . "/config/" . $widget_name . ".php";
			
			if(file_exists($widgetconfigfile)){
				
				// initialize the array in case the new entry is bogus
				$config['widget_config'] = array();
							
				include($widgetconfigfile);
								
				if(isset($config['widget_config']['name'])){
				
					return $config['widget_config'];
									
				}
									

			}
			
		}
		
		return false;
		
		

	}
	
	/**
	* Query the widgets db table and return an array of ids, instance names and all widget_options for matches
	* @param string module
	* @param string widget
	* @return array containing all widget attributes and option values
	*/
	public function get_widgets_attributes_by_module( $module, $widget = null){
	
		$return_array = array();
		
		$CI =& get_instance();
			
		$CI->db->where('module', $module);
		if($widget) $CI->db->where('widget', $widget);
		
		$query = $CI->db->get('widgets');
		
		//echo $CI->db->last_query();
		
		if ($query->num_rows() > 0){
			
			foreach ($query->result() as $row){
			
				$options = array();
				
				$CI->db->where('widget_id', $row->widget_id);
				
				$query2 = $CI->db->get('widget_options');
				
				if ($query2->num_rows() > 0){
				
					foreach ($query2->result() as $optrow){
					
						$options[$optrow->name] = $optrow->value;
					}
					
					
				}
						
				$record_array = array(
				
					'widget_id'=>$row->widget_id,
					'instance'=>$row->instance,
					'options'=>$options,
					
				);
				
				$return_array["id_" . $row->widget_id] = $record_array;
								
			}
		}
		

		return $return_array;
				
	
	}
		
	
	/**
	* Read all the app folders and return a list of all widgets in the system
	*
	*/
	public function get_widgets( $module = null){
		
				
		$CI =& get_instance();
		
		$CI->load->helper('directory');
		
		$projectnum = $CI->config->item('projectnum');
		
	
		$widgets_array = array();

		if($module){
		
			$locations = array(

				$projectnum . "/modules/" . $module . "/widgets",
				"custom_modules/" . $module . "/widgets",
				"core_modules/" . $module ."/widgets"
		
			);
		
			
		}else{
		
			$locations = array(

				$projectnum . "/widgets",
				"widgets"
		
			);
			
		}
		
			

		foreach($locations as $location){
			
			$widgetdir = APPPATH . $location;
			
			
			
			if(file_exists($widgetdir)){
				
				if ($files_handle = opendir($widgetdir)) {

					while (false !== ($file = readdir($files_handle))) {
						

						if($file != "." && $file != ".." && $file != "views" && $file != "index.html" && $file != ".htaccess" &&  $file != "config"){
						
							$widget_desc_name = str_replace(".php","",$file);
						
							$config_file = $widgetdir . "/config/" . basename($file);
														
							if(file_exists($config_file)){
								
								// initialize the array in case the new entry is bogus
								$config['module_config'] = array();
								$module_data = array();
							
								//echo " GOT CONFIG ";
								include($config_file);
								
								
								if(isset($config['widget_config']['name'])){
								
									$widget_desc_name = $config['widget_config']['name'];
																	
								
								}
								
								
							}
							
							// if would be nice to know if this module is active but that will have to wait for another day...
							
							$widget_name = str_replace(".php","",$file);
							
							if(!in_array($file,$widgets_array))
								$widgets_array[] = array("desc_name"=>$widget_desc_name, "name"=>$widget_name);
					
							
						}

					}
				}
			}
			
		}
		


		return $widgets_array;
		
		
	}
	
	
	/**
	* This function can be called by template files or module code to execute template like function calls
	* Sample string: 
	* $text = "He is a simple demo of  [widget type='bg_pos_slider/bg_widget' othername='seomething else' relative=wawaewa] OR [widget='bg_pos_slider/bg_widget' ] 
	* return processed html
	*/	
	public function process_widgets($string){
	
		$CI = & get_instance();
			
		
		//return $string;
		
		if($string == "") return "";
		
		// replace BBCode style widget call for actual widget plugin call
		$string = preg_replace_callback(
		'/\[WIDGET=([^\]]+)?\]/i',
		create_function(
		'$matches',
		'
				return \'{=Widget::run(\'.$matches[1].\')=}\';
				
				'
		),
		$string);
		


		// replace wordpress style widget call for actual widget plugin call
		$string = preg_replace_callback(
		'/\[WIDGET ([^\]]+)?\]/i',
		create_function(
		'$matches',
		'
				$widget_attributes = Widget::extract_tag_attributes($matches[1]);
				
				$addon_args = array();
					
				foreach($widget_attributes as $key => $val){
				
					if(isset($widget_attributes[$key])){
							
						$widget_attributes[$key] = str_replace("\'","",trim($widget_attributes[$key]));
										
						if($key != "type"){
							$addon_args[]  = "\'$key\'=>\'$widget_attributes[$key]\'";
						}
					}
				}
						
				$addon_args_string = "array(";
				$addon_args_string .= implode(",",$addon_args);
				$addon_args_string .= ")";
		
				$returnstring = "{=Widget::run(\'" . $widget_attributes[\'type\'] . "\'," . $addon_args_string . ")=}";
					
				return $returnstring;		
				'
		),
		$string);
		

		// run widgets that are embedded as shortcodes in pages
		$string = preg_replace_callback(
		'/\[widget(.*?)\]/is',
		create_function(
		'$matches',
		'
				$slug = $matches[1];
				
				$returnstring = "{=Widget::run_slug(\'" . $slug . "\')=}";
				
				return $returnstring;	
		'
		),
		$string);

		
		//echo $string;
		
		$pattern = '/\{=(.*?)\=}/is';
		$outbuf = preg_replace_callback($pattern, array(&$this, '_func_eval'), $string) . "\n";
		
		
		return $outbuf; //$outbuf;
		
	}////////////////////
	
	
	///////////////////////////////////////
	//
	// watch out for function calls with ' characters converted to &#39;
	// took me hours to figure that $#%@ out.
	//
	protected function _func_eval($matches){
		

		ob_start();
		
		$outbuf = "";
		
		if(trim($matches[1]) != ""){
			
			if(preg_match("/\(/",$matches[1])){
				
				if(preg_match("/::/",$matches[1])){
					
					$str = trim($matches[1]); 
					
					// quick little fix for corrupted tags
					$str = str_replace("&#39;","'", $str);
						
					eval("\$outbuf = " . $str . ";");
					
				}					
			}
		}
		

		$outbuf = ob_get_contents();

		ob_end_clean();
		
		return $outbuf;
		
	}///////////////////////////

	
	
	
}

