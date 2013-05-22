<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mysmarty - Allows setup of prioritized template directories to scan 
 *
 * @package		Mysmarty
 * @author		Galbraith Desmond <galbraithdesmond@gmail.com>
 * @description Loads javascript and css assets following specified rules
 *
 * @version		1 (2012-02-22)
 */
 
	require "smarty/libs_3.1.6/Smarty.class.php";

	class Mysmarty extends Smarty
	{
		public function __construct ( )
		{
			//parent::Smarty( );
			parent::__construct();
			
			$CI =&get_instance();
	
			$config =& get_config( );			

			$this->left_delimiter	=  '{'; // this can also be {{
			$this->right_delimiter	=  '}';	// this can also be }}		

			$this->compile_dir  = $config['smarty_compile_dir']; 
				


		}

		
		public function set_fallback_templates ()
		{

  	
			$CI =&get_instance();
	
			$config =& get_config( );			
			
			$template_dirs = $config['template_dirs'];
			
		
			$CI->load->helper('cookie');
			$CI->load->helper('mobile');
			
			$ismobile = (ps_ismobile() == true) ? TRUE : FALSE;
			$domobile = (ps_domobile() == true) ? TRUE : FALSE;
			
			
			$updatedtemplate_dirs = $updatedplugin_dirs = array();
			
			// peterdrinnan - need to add the language var here.
			$lang = $CI->config->item('lang_selected');
						
			// peterdrinnan - the default  language comes from the lang_detect config file			
			$default_lang = $CI->config->item('lang_default');
			
										
			if ($domobile){		
			
				foreach($template_dirs as $val){
											
					$updatedtemplate_dirs[] = $val . "/mobile_" . $lang . "/";			
					$updatedtemplate_dirs[] = $val . "/mobile_" . $default_lang . "/";
					$updatedtemplate_dirs[] = $val . "/mobile/";
					$updatedtemplate_dirs[] = $val . "/standard_" . $lang . "/";
					$updatedtemplate_dirs[] = $val . "/standard_" . $default_lang . "/";
					$updatedtemplate_dirs[] = $val . "/standard/";
					
					$updatedplugin_dirs[] = $val . "/mobile_{$lang}/plugins/";
					$updatedplugin_dirs[] = $val . "/mobile_{$default_lang}/plugins/";
					$updatedplugin_dirs[] = $val . "/mobile/plugins/";										
					$updatedplugin_dirs[] = $val . "/standard_{$lang}/plugins/";	
					$updatedplugin_dirs[] = $val . "/standard_{$default_lang}/plugins/";
					$updatedplugin_dirs[] = $val . "/standard/plugins/";					
				}
												
			}else{
			
				foreach($template_dirs as $val){
				
					$updatedtemplate_dirs[] = $val . "/standard_{$lang}/";		
					$updatedtemplate_dirs[] = $val . "/standard_{$default_lang}/";	
					$updatedtemplate_dirs[] = $val . "/standard/";	
					
					$updatedplugin_dirs[] = $val . "/standard_{$lang}/plugins/";	
					$updatedplugin_dirs[] = $val . "/standard_{$default_lang}/plugins/";
					$updatedplugin_dirs[] = $val . "/standard/plugins/";						
				}
				
			}
			
					
			// load multiple locations so we have fallbacks
			$this->addTemplateDir($updatedtemplate_dirs);
			$this->addPluginsDir($updatedplugin_dirs);
			
			
		}
		
		
		public function view ( $resource_name, $cache_id = null )
		{
			if ( strpos($resource_name, '.') === false )
				$resource_name .= '.tpl.php';

			return parent::display( $resource_name, $cache_id );
		}
	}

