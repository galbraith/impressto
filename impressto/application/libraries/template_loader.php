<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
* locate load and parse templates based on docket 
* number, language, device and template type
*/
class template_loader {
	
	
	
	public function __construct(){

	}
	
	/**
	* Replacement for the Widget render function
	* reads the template config and renders it as PHP or Smarty based on what the type is
	* determoines the optinal view based on nalguge, device and docket.
	* in use but there is a much better way of handling templates
	* types are smarty, php, pageshaped
	* For pageshaped templates use the 'partial_section' parameter to specify partial views
	*/
	function render_template($data){
	

		$outbuf = "";
		
		$CI =& get_instance();
		
		$CI->load->helper('ps_helper');
				
		
		if(!isset($data['is_page'])) $data['is_page'] = FALSE;
		
		$template_file = "";
		
		if(isset($data['template_file'])){
			
			// bypass all the finding crap and just load the thing here.
			$template_file = $data['template_file'];
			
		}
		
			
		//add a little hack here for now to work around the smarty pages
		if(!$data['is_page']){
		
					
		
			$template_file = $this->find_template($data); // data contains the module, widgettype, tempate name
			
			
			if($template_file == ""){
				
				return;
				
			}else{
				
				$template_data = $this->template_data($template_file);
				
				
			}
			
				
		}else{
		
			//if($data['is_page']) $template_data['type'] = "smarty";
			$template_file = $this->find_template($data); // data contains the module, widgettype, tempate name
						
			//echo $template_file;
						
			if($template_file == ""){
				
				return;
				
			}else{
			
				$template_data = $this->template_data($template_file);
				
				//print_r($template_data);
				
			}
			
			
		}
		
		
		
		if(isset($template_data['Type'])){
			$template_type = trim(strtolower($template_data['Type']));
		}else if(isset($template_data['type'])){
			$template_type = trim(strtolower($template_data['type']));
		}else{
			$template_type = "php";
		}
		
		// Only show template debug info for widget, module and sparks templates 
		// because IE chokes to death on anyhing beore the <doctype> tag.
		if( !$data['is_page']){

			// if we are in development or testing mode add debug into for template work
				
			if($CI->config->item('debugmode') && $template_file != "")
				$outbuf .= "<!-- PS TEMPLATE: " . str_replace(getenv("DOCUMENT_ROOT"),"",$template_file ) . " -->";
			
		}
								
		
		switch($template_type){
			
		case "smarty" :
		
		
			if(!file_exists($CI->config->item('smarty_compile_dir'))){
			
				$CI->load->library("ps_files");
				$CI->ps_files->create_dirpath( $CI->config->item('smarty_compile_dir') );
			
			}
			
			if ($CI->config->item('debugmode') == TRUE){ //if in debug mode such as when user is logged into system admin

				if ($CI->mysmarty->isCached($data['template'],$data['my_cache_id'])) { 
			
					$CI->mysmarty->clear_cache($data['template'],$data['my_cache_id']);
					$CI->mysmarty->clear_compiled_tpl($data['template']);
				}
			}
				
		
			
			if(!$data['is_page']){
			
				// this looks to be incomplete ... 
				// Probably the result of being interrupted -
				// Probably went like this ...
				// "Can you take a look at this for a minute ..."
				// Good thing we aren't fixing f-ing airplanes eh! 

				$prev_template_dirs = $CI->mysmarty->getTemplateDir();
				$CI->mysmarty->setTemplateDir($template_dir);
				
			}else{
				
				// peterdrinnan - october 31, 2012
				/*
		          ,
                 _))._
               /`'---'`\
              |  <\ />  |
              | \  ^  / |
              \  '-'-'  /
               '--'----'
				*/
				
				$CI->mysmarty->set_fallback_templates();
				
				// assign all page attributes to smart vars
				if(isset($data['page_data']) && is_array($data['page_data'])){
			
					foreach ($data['page_data'] as $key=>$value) {
						$CI->mysmarty->assign($key,$value);
					}
				}
				
				// assign all asset calls to smarty vars
				if(isset($CI->asset_loader->header_js) || isset($CI->asset_loader->header_js_strings)){
					$CI->mysmarty->assign('output_header_js', $CI->asset_loader->output_header_js());
				}else{
					// so we don't get smarty errors
					$CI->mysmarty->assign('output_header_js', ""); 	
				}
				
				if(isset($CI->asset_loader->header_css) || isset($CI->asset_loader->header_css_strings)){
					$CI->mysmarty->assign('output_header_css', $CI->asset_loader->output_header_css());
				}else{
					// so we don't get smarty errors
					$CI->mysmarty->assign('output_header_css', ""); 			
				}
				
				$CI->mysmarty->assign('output_misc_header_top_assets', $CI->asset_loader->output_misc_header_top_assets());
				$CI->mysmarty->assign('output_header_misc_assets', $CI->asset_loader->output_header_misc_assets());
		
				
				// this is where it gets wierd... we need to find a language match if possible
				$CI->config->set_item("smartyrender",TRUE);
							
				$outbuf .= $CI->mysmarty->fetch($data['template'], $data['my_cache_id']);
				

			}
			
			
			if(!$data['is_page']){
			
				// This reverts the templates back to the standard smarty fallback forders.
				// this is a safety measure for smarty includes and plugin calls in case a widget 
				// that uses smarty gets called in the middle of the main smarty page template.  
				$CI->mysmarty->set_fallback_templates();
			}
			
			
			break;
			
			
		case "php" :
						
			
			// assign all asset calls to smarty vars
			if(isset($CI->asset_loader->header_js) || isset($CI->asset_loader->header_js_strings)){
				$data['output_header_js'] = $CI->asset_loader->output_header_js();
			}
			if(isset($CI->asset_loader->header_css) || isset($CI->asset_loader->header_css_strings)){
				$data['output_header_css'] = $CI->asset_loader->output_header_css();
			}
			
			$data['output_misc_header_top_assets'] = $CI->asset_loader->output_misc_header_top_assets();
			$data['output_header_misc_assets'] = $CI->asset_loader->output_header_misc_assets();
							
			// globalize everything in $data
			extract($data);
			
			
			ob_start();

			include $template_file;
						
			$outbuf .= ob_get_contents();

			ob_end_clean();
						
			break;
			


		case "impressto" :
				
							
			ob_start();
						
			$CI->load->library('impressto');
			
			//$CI->load->library('CssMin');
				
			$tfile = basename($template_file); 
			$tdir = dirname($template_file); 
			
			$prev_tdir = $CI->impressto->getDir($tdir);
			$CI->impressto->setDir($tdir);
			
			if(isset($data['partial_section'])){
				echo $CI->impressto->showpartial($tfile, $data['partial_section'], $data);
			}else{
				echo $CI->impressto->show($tfile, $data);
			}
			
			$CI->impressto->setDir($prev_tdir);
			
	
			$outbuf .= ob_get_contents();

			ob_end_clean();
			
			
			break;
			
		}
		
		
		return $outbuf;
		

		
		
	}

	
	/**
	* 
	* Seeks the best template based on cliet DOCKET, user language and device
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	* @param $data array ( template, module, is_widget, widgettype )
	* @return string 
	*/
	function get_fallback_folders($data){
	
		$CI =& get_instance();
		
		$projectnum = $CI->config->item('projectnum');
		$modules_locations = $CI->config->item('modules_locations');
		
		if(!isset($data['lang'])) $data['lang'] = $CI->config->item('lang_selected');
		
		// failsafe
		if($data['lang'] == "") $data['lang'] = "en";
				
	
		
		
		$default_headers = array(
		
		'Name' => 'Name',
		'Type' => '',
		'Filename' => 'Filename',
		'Author' => 'Author',
		'Docket' => 'Docket',
		'Version' => 'Docket',
		'Status' => 'Status',
		'Date' => 'Date'
		);

		$is_widget = FALSE;
		$widgettype = "";
		$module = "";


		
		$CI->load->helper('mobile');
		
		if(isset($data['device']) && $data['device'] != '') $device = $data['device'];
		else $device = ps_domobile() ? "mobile" : "standard"; // potential flaws here...
	

	
		
		if(isset($data['module']) && $data['module'] != '') $module = $data['module'];
		if(isset($data['is_widget']) && $data['is_widget'] != '') $is_widget = $data['is_widget'];
		if(isset($data['widgettype']) && $data['widgettype'] != '') $widgettype = $data['widgettype'];

		
		$return_array = array();
		

		
		$template_locations = array();
		$view_locations = array();
		

		/////////////////////////////////
		// SETUP THE FALLBACKS
		
		if($module == "" && $widgettype != ""){ // this is an adhoc module with no parent module
			
			if($device != "standard") $template_locations[] = "widgets/{$projectnum}/{$device}_{$data['lang']}/{$widgettype}";
			if($device != "standard") $template_locations[] = "widgets/{$projectnum}/{$device}/{$widgettype}";
			$template_locations[] = "widgets/{$projectnum}/standard_{$data['lang']}/{$widgettype}";
			$template_locations[] = "widgets/{$projectnum}/standard/{$widgettype}";
			
			if($device != "standard") $template_locations[] = "widgets/default/{$device}/{$widgettype}";
			$template_locations[] = "widgets/default/standard/{$widgettype}";
			

			$view_locations[] = APPPATH. "{$projectnum}/widgets/views/{$widgettype}/standard_{$data['lang']}";
			$view_locations[] = APPPATH. "{$projectnum}/widgets/views/{$widgettype}/standard";
			$view_locations[] = APPPATH. "{$projectnum}/widgets/views/{$widgettype}";
	
			$view_locations[] = APPPATH. "widgets/views/{$widgettype}/standard";
			$view_locations[] = APPPATH ."widgets/views/{$widgettype}";
			
		

			
		}else if($is_widget && $module != "" && $widgettype == ""){ // this is a module widget but no specific type
			
					 
			if($device != "standard") $template_locations[] = "modules/{$projectnum}/{$module}/{$device}_{$data['lang']}/widgets";
			if($device != "standard") $template_locations[] = "modules/{$projectnum}/{$module}/{$device}/widgets";
			
			$template_locations[] = "modules/{$projectnum}/{$module}/standard_{$data['lang']}/widgets";
			$template_locations[] = "modules/{$projectnum}/{$module}/standard/widgets";
			$template_locations[] = "modules/{$projectnum}/{$module}/widgets"; // this is a disencouraged legacy fallback
			
			
			if($device != "standard") $template_locations[] = "modules/default/{$module}/{$device}/widgets";
					
			$template_locations[] = "modules/default/{$module}/standard_{$data['lang']}/widgets";
			$template_locations[] = "modules/default/{$module}/standard/widgets";
			$template_locations[] = "modules/default/{$module}/widgets"; // this is a legacy fallback
			
			
			
			foreach ($modules_locations as $ml_fullpath => $ml_rel) {	
				
				if($device != "standard") $view_locations[] = "{$ml_fullpath}{$module}/widgets/views/{$device}_{$data['lang']}";
				$view_locations[] = "{$ml_fullpath}{$module}/widgets/views/standard_{$data['lang']}";
				if($device != "standard") $view_locations[] = "{$ml_fullpath}{$module}/widgets/views/{$device}";
				$view_locations[] = "{$ml_fullpath}{$module}/widgets/views/standard";
				$view_locations[] = "{$ml_fullpath}{$module}/widgets/views"; // this is a discouraged legacy fallback
				
			}
			
			
		}else if($module != "" && $widgettype != ""){ // these are different types of widgets that can be defined for a module
			
			if($device != "standard") $template_locations[] = "modules/{$projectnum}/{$module}/{$device}_{$data['lang']}/widgets/{$widgettype}";
			if($device != "standard") $template_locations[] = "modules/{$projectnum}/{$module}/{$device}/widgets/{$widgettype}";
			
			$template_locations[] = "modules/{$projectnum}/{$module}/standard_{$data['lang']}/widgets/{$widgettype}";
			$template_locations[] = "modules/{$projectnum}/{$module}/standard/widgets/{$widgettype}";
			$template_locations[] = "modules/{$projectnum}/{$module}/widgets/{$widgettype}";
			
			
			if($device != "standard") $template_locations[] = "modules/default/{$module}/{$device}/widgets/{$widgettype}";
			
			
			$template_locations[] = "modules/default/{$module}/standard_{$data['lang']}/widgets/{$widgettype}";
			$template_locations[] = "modules/default/{$module}/standard/widgets/{$widgettype}";
			$template_locations[] = "modules/default/{$module}/widgets/{$widgettype}";
			
			
			
			foreach ($modules_locations as $ml_fullpath => $ml_rel) {	
				
				if($device != "standard") $view_locations[] = "{$ml_fullpath}{$module}/widgets/views/{$device}_{$data['lang']}/{$widgettype}";
				if($device != "standard") $view_locations[] = "{$ml_fullpath}{$module}/widgets/views/{$device}/{$widgettype}";
				$view_locations[] = "{$ml_fullpath}{$module}/widgets/views/standard_{$data['lang']}/{$widgettype}";
				$view_locations[] = "{$ml_fullpath}{$module}/widgets/views/standard/{$widgettype}";
				$view_locations[] = "{$ml_fullpath}{$module}/widgets/views/{$widgettype}"; // this is a disencouraged legacy fallback
				
			}
			

			
			
		}else if($module != ""){ // this is not a widget
			
			
			if($device != "standard") $template_locations[] = "modules/{$projectnum}/{$module}/{$device}_{$data['lang']}";
			$template_locations[] = "modules/{$projectnum}/{$module}/standard_{$data['lang']}";
			if($device != "standard") $template_locations[] = "modules/{$projectnum}/{$module}/{$device}";
			$template_locations[] = "modules/{$projectnum}/{$module}/standard";
			
			if($device != "standard") $template_locations[] = "modules/default/{$module}/{$device}";
			
			$template_locations[] = "modules/default/{$module}/standard_{$data['lang']}";	
			$template_locations[] = "modules/default/{$module}/standard";	
			
			
			foreach ($modules_locations as $ml_fullpath => $ml_rel) {	
				
				if($device != "standard") $view_locations[] = "{$ml_fullpath}{$module}/views/{$device}_{$data['lang']}";
				if($device != "standard") $view_locations[] = "{$ml_fullpath}{$module}/views/{$device}";

				$view_locations[] = "{$ml_fullpath}{$module}/views/standard_{$data['lang']}";
				$view_locations[] = "{$ml_fullpath}{$module}/views/standard";
				$view_locations[] = "{$ml_fullpath}{$module}/views";
				
			}
			

			
		}else{ // now we will assume we are looking in the smarty folder for a page template
			
		
			if($device != "standard") $template_locations[] = "pages/{$projectnum}/{$device}_{$data['lang']}";
			$template_locations[] = "pages/{$projectnum}/standard_{$data['lang']}";
			if($device != "standard") $template_locations[] = "pages/{$projectnum}/{$device}";
			$template_locations[] = "pages/{$projectnum}/standard";
						
			if($device != "standard") $template_locations[] = "pages/default/{$device}";
			
			$template_locations[] = "pages/default/standard_{$data['lang']}";	
			$template_locations[] = "pages/default/standard";	

			/////////////////////////
			// phase out the stuff below
			if($device != "standard") $template_locations[] = "smarty/{$projectnum}/{$device}_{$data['lang']}";
			$template_locations[] = "smarty/{$projectnum}/standard_{$data['lang']}";
			if($device != "standard") $template_locations[] = "smarty/{$projectnum}/{$device}";
			$template_locations[] = "smarty/{$projectnum}/standard";
						
			if($device != "standard") $template_locations[] = "smarty/default/{$device}";
			
			$template_locations[] = "smarty/default/standard_{$data['lang']}";	
			$template_locations[] = "smarty/default/standard";	
			
			// end phase out
			////////////////////
						
		}
		
		return array('template_locations'=>$template_locations,'view_locations'=>$view_locations);
		
	
	
	}
	
	
	
	/**
* this function is a little lost. It is currently
* in use but there is a much better way of handling templates
*/
	function template_selector($data){

		$CI =& get_instance();
		
			
		// now load the form elemetn thingy
		$CI->load->library("formelement");
		
		$selector_name = "";
		$selector_label = "";
		$value = "";
		$onchange = "";

		
		
		if(isset($data['selector_name']) && $data['selector_name'] != '') $selector_name = $data['selector_name'];
		if(isset($data['selector_label']) && $data['selector_label'] != '') $selector_label = $data['selector_label'];
		if(isset($data['value']) && $data['value'] != '') $value = $data['value'];
		if(isset($data['onchange']) && $data['onchange'] != '') $onchange = $data['onchange'];

		
		
		
		$usewrapper = isset($data['usewrapper']) ? $data['usewrapper'] : TRUE;
		$showlabels = isset($data['showlabels']) ? $data['showlabels'] : TRUE;

		
		
		$data = array(
		'name'        => $selector_name,
		'type'          => 'select',
		'id'          => strtolower(str_replace(" ","_",$selector_name)),
		'label'          => $selector_label,
		'options' =>  array_merge(array("Select"=>""),$this->find_templates($data)),
		'value'       => $value,
		'usewrapper'       => $usewrapper,
		'onchange'       => $onchange,
		'showlabels'       => $showlabels,
		);
		
			

		
		$outbuf = $CI->formelement->generate($data);

				
		return $outbuf;
		
		
	}

	
	
	
	/**
* get a list of all the widget tempates available for the defined
* module. This will only search the templates folder, not the views
* folders
* @param controllerpath just points to the current controller location
*/
	function find_templates($data, $fullkeys = FALSE){ 

		$CI = & get_instance();

		if(!isset($data['lang'])) $data['lang'] = "en";
		
		$fallback_folders = $this->get_fallback_folders($data);
		
	
		$templatedir = "";
		
		//echo "\n\n\n";
		
		
		// loop through the various module locations
		foreach ($fallback_folders['template_locations'] as $location) {	

			
			// try to find the docket version first
			$testdir = PSTEMPLATEPATH . $location;
			
			//echo " $testdir <br />\n";
		
			if(file_exists($testdir)){
				
				
				// one last check see that the folder is not empty
				$dir_empty = $this->is_dir_empty($testdir);
				
				if(!$dir_empty){
					
					//echo "GOT  $testdir <br />";
					
					$templatedir = $testdir;
					break;
				}
				
			}


		}
		
		// if nothing yet found, we will start looking in the CI view folders
		
		if($templatedir == ""){
			
			
			// loop through the various module locations
			foreach ($fallback_folders['view_locations'] as $location) {	
				
				
				
				$testdir = $location;
				
				//echo "<br /> " . $testdir . "<br />";
				
				if(file_exists($testdir)){
				
					//echo "<br /> " . $testdir . "<br />";
				
				
					$dir_empty = $this->is_dir_empty($testdir);
					
					if(!$dir_empty){
						$templatedir = $testdir;
						break;
					}
					
				}


			}
			
		}
		

		$return_array = array();
		
		
		$lang_avail = $CI->config->item('lang_avail');
		

		// get the template meta data
		if(file_exists($templatedir)){
			
			if ($files_handle = opendir($templatedir)) {

				while (false !== ($file = readdir($files_handle))) {
					
					if(!is_dir($templatedir . "/". $file)){
						
						if ($file != "." && $file != ".." && $file != "index.htm" && $file != "index.html") {
							
							// look for a language version first
							$template_file = $templatedir . "/" . $file;
													
							$template_filename = basename($template_file);
														
							$template_data = $this->template_data($template_file);
							
							$process = FALSE;
							
							if( ( isset($template_data['Name']) && $template_data['Name'] != "") && !array_key_exists($template_data['Name'],$return_array)) $process = TRUE;	
		
							if($data['lang'] != ""){
							
								foreach($lang_avail AS $key => $val){
															
									if($data['lang'] != $key && strpos($file, "_" . $key . ".") !== false){
															
										$process = FALSE;
									}
								
								}
																				
							}
								
							if($data['lang'] != "" && $template_data['Lang'] != "" && ( $template_data['Lang'] != $data['lang']) ){
								$process = FALSE;
							}
							
							if( $process ){
																			
								if($fullkeys) $return_array[$template_data['Name']] = $template_data;
								else $return_array[$template_data['Name']] = $template_filename; 
							}
							
						}
					}
				}
			}
			
		}
		
		
		//print_r($return_array);
		
		
		return $return_array;
		


	}


	
	/**
	* 
	* Seeks the best template based on cliet DOCKET, user language and device
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	* @param $data array ( template, module, is_widget, widgettype )
	* @return string 
	*/
	public function find_template($data){

		$CI =& get_instance();
		
		
		$fallbackfiles = array();
		
	
		if(!isset($data['template']) || $data['template'] == ''){
			return FALSE;
		}else{
		
			// do a little filename purge first
			$data['template'] = str_replace( "_" . $CI->config->item('lang_default'),"",$data['template'] );
				
			
			// now we need to replace the ideal file with the correct language and then setup the falbacks
			
			$filenamewithoutext = substr($data['template'],0,strpos($data['template'],".")); 
			$filenameext = substr($data['template'],strpos($data['template'],"."), strlen($data['template'])); 
			
			$fallbackfiles[] =  $filenamewithoutext . "_" . $CI->config->item('lang_selected') . $filenameext;
			$fallbackfiles[] =  $filenamewithoutext . $filenameext;
			
			if($CI->config->item('lang_selected') != $CI->config->item('lang_default'))
			$fallbackfiles[] =  $filenamewithoutext . "_" . $CI->config->item('lang_default') . $filenameext;			
			
		}
		
		
		$fallback_folders = $this->get_fallback_folders($data);
		
		//print_r($fallback_folders);
	


		
		// loop through the various module locations
		foreach ($fallback_folders['template_locations'] as $location) {	
						
			
			foreach($fallbackfiles AS $file){
				
				// try to find the docket version first
				$testfile = PSTEMPLATEPATH . $location . "/" . $file;
				
				//echo $testfile . "<br /><br />";
						
				if(file_exists($testfile)) return $testfile;
				
				
			}

		}
		
		
		
		// loop through the various module locations
		foreach ($fallback_folders['view_locations'] as $location) {	
			
			foreach($fallbackfiles AS $file){
				
				$testfile = $location . "/" . $file;
		
				//echo "<br />" . $testfile . "<br /><br />";
		
				if(file_exists($testfile)){
				
					//echo "<br /> TADA $testfile <br />";
				
					return $testfile;
				}
				
				
			}

		}
		
		

		return FALSE;
		
		

	}
	
	
	
	/**
	* Scans the admin thems folders to find any administration themes that are available.
	* This function is a little out of place here so it is just parking. 
	* @author Galbraith Desmond
	* @since 2.7
	* @version 1
	* @return array or themes
	*
	*/
	public function find_admin_themes(){
	
	
		$CI =& get_instance();
		
		$projectnum = $CI->config->item('projectnum');
		
	
		$return_array = array();
		
		$themedirs = array("{$projectnum}/views/themes","views/themes");
		
		
		foreach($themedirs AS $themedir){
					
			$themedir = APPPATH . $themedir;
			
			
			if(file_exists($themedir)){
			
				
				if ($files_handle = opendir($themedir)) {

					while (false !== ($sampled = readdir($files_handle))) {
					
						if(is_dir($themedir . "/". $sampled) && $sampled != "." && $sampled != ".."){
					
							$template_dir = $themedir . "/" . $sampled;
						
						
							$core_wrapper_file = $themedir . "/" . $sampled . "/admin/includes/core_wrapper.php";
																			
							$themedata = $this->template_data($core_wrapper_file);
							
							if(isset($themedata['Filename']) && $themedata['Filename'] != ""){
							
								if(!in_array($sampled,$return_array)){
							
									if(isset($themedata['Name']) && $themedata['Name'] != "") $label = $themedata['Name'];
									else $label = $sampled;
									
									$return_array[$label] = $sampled;
									
								}
								
							}
						
						}
								
					}
				}
			}
			
		}
		
		
		return $return_array;
		
		
	
	}



	/**
* Retrieve metadata from a file.
*
* Searches for metadata in the first 8kiB of a file, such as a plugin or theme.
* Each piece of metadata must be on its own line. Fields can not span multiple
* lines, the value will get cut at the end of the first line.
*
* If the file data is not within that first 8kiB, then the author should correct
* their plugin file and move the data headers to the top.
*
* 
*/
	function template_data( $file, $default_headers = '') {
		
		
		if($default_headers == ""){
			
			// the leys and values have to be set or no go jojo
			$default_headers = array(
			'Name' => 'Name',
			'Type' => 'Type',
			'Filename' => 'Filename',
			'Lang' => 'Lang',
			'Description' => 'Description',		
			'Author' => 'Author',
			'Docket' => 'Docket',
			'Version' => 'Version',
			'Status' => 'Status',
			'Date' => 'Date'
			);
		}
		
	

		
		// We don't need to write to the file, so just open for reading.
		$fp = fopen( $file, 'r' );

		// Pull only the first 8kiB of the file in.
		$file_data = fread( $fp, 8192 );

		// PHP will close file handle, but we are good citizens.
		fclose( $fp );

		$all_headers = $default_headers;


		foreach ( $all_headers as $field => $regex ) {
			
			// this expression allows *, # and @ before the variable names
			preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field});
			
			if ( !empty( ${$field} ) )
			${$field} = ${$field}[1];
			else
			${$field} = '';
			
			${$field} = trim(${$field});
		}

		 
		$file_data = compact( array_keys( $all_headers ) );
		
		if($file_data['Filename'] == "") $file_data['Filename'] = basename($file);
		
		$file_data['fulltemplatepath'] = str_replace(getenv("DOCUMENT_ROOT"),"",$file);
		
		
		return $file_data;
		
	}
	
	
	
	
	private function is_dir_empty($path){
	
		// Scans the path for directories and if there are more than 2
		// directories i.e. "." and ".." then the directory is not empty
		
		$nullfiles = array(".","..","index.htm","index.html",".htaccess","includes","plugins"); // these are files that don't count as templates
		
		$files = @scandir($path);
		
				
		$templatefound = FALSE;
				
		foreach($files AS $file){
				
			if( !is_dir($path . "/" . $file)  && !in_array($file,$nullfiles) ){
			
				// yaay, the folder is not empty
				return FALSE;
			}
		
		}
		
		return TRUE;
				
		
		
	}
	
}
