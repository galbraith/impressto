<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* edit tools - functions to assist in editing and saving content within pages and widgets. 
*
* @package		ps_edittools
*
* @todo - add autosuggest to allow editors to select pre-existing tags and prevent duplicates in the system
*
* @author		Galbraith Desmond <galbraithdesmond@gmail.com>
* @description Assists in validating and setting user sessions.
*
* @version		1.0.5 (2012-03-02)
*/
class ps_edittools {
	


	function __construct(){
		


	}

	/**
	* Used to universally set the WYSIWYG editor from within rich content editing module views. 
	* WYSIWYG asset calls are registered here and WYSIWYG placement code is simply echoed out.
	*
	* @param array - configuration values
	* 
	*/
	public function insert_wysiwyg($config){
		
		
		$CI = & get_instance();
		
		// set some basic defaults
		if(!isset($config['content'])) $config['content'] = "";
		if(!isset($config['width'])) $config['width'] = 800;
		if(!isset($config['height'])) $config['height'] = 400;
		if(!isset($config['toolbar'])) $config['toolbar'] = 'Full';
		if(!isset($config['lang'])) $config['lang'] = 'en';
		
		if(!isset($config['name'])) return; // do not load if we have no field name for the editor
		
		

		
		
		
		
		
		/////////////////////////
		// Establish what WYSIWYG editor we will be using
		//
		
		// This block wysiwyg editing cookie is been polled already in /application/core_modules/site_settings/site_settings.php, 
		// BUT we check again in case there have been overrides 
		$block_wysiwyg_editing = $CI->input->cookie('block_wysiwyg_editing', FALSE);
		
		
		if($block_wysiwyg_editing == "true"){
			
			$wysiwyg_editor = "";
			
			
		}else{

			
			if(isset($config['wysiwyg_editor']) && $config['wysiwyg_editor'] != ""){
				
				$wysiwyg_editor = $config['wysiwyg_editor'];
				
			}else{
				
				if(!$CI->config->item('wysiwyg_editor')){
					
					
					$CI->load->model("site_settings/site_settings_model");
					$site_options = $CI->site_settings_model->get_settings();
					
					if(!isset($site_options['wysiwyg_editor']) || $site_options['wysiwyg_editor'] == "") $wysiwyg_editor = "none";
					else $wysiwyg_editor = $site_options['wysiwyg_editor'];
					
					// store it so we don't have to call this routine again
					$CI->config->set_item('wysiwyg_editor',$site_options['wysiwyg_editor']);
					
					
					
				}else{
					
					$wysiwyg_editor = $CI->config->item('wysiwyg_editor');
					
				}
				
			}
			
		}
		
		//
		///////////////////
		
		
		if($wysiwyg_editor == "tiny_mce"){ // this is the preferred choice
			
			$CI->load->library('asset_loader');
			
			$CI->asset_loader->add_header_js(ASSETURL  . "third_party/tiny_mce/tiny_mce.js");
			
			if($config['toolbar'] == "Full") $tinyclass = "mceAdvanced";
			else $tinyclass = "mceSimple";
			
			
			if(!$CI->config->item('wysiwyg_initialized')){
				
				$header_js_string = $CI->load->view('tiny_mce_init', $config, TRUE);	
				
				$CI->asset_loader->add_header_js_string($header_js_string);
				
				$CI->config->set_item('wysiwyg_initialized',TRUE);
				
			}
			
			
			echo "<textarea class=\"{$tinyclass}\" style=\"height:{$config['height']}px; width: 100%\" name=\"{$config['name']}\" id=\"{$config['name']}\">{$config['content']}</textarea>";
			
			
			
			
		}else if($wysiwyg_editor == "ckeditor"){ // this is for legacy users who are simply too lazy to learn something new
			
			if(!$CI->config->item('ckeditor_object')){

				// need to make sure we only declare this object once
				require_once getenv("DOCUMENT_ROOT") .'/'.PROJECTNAME.'/third_party/ckeditor/ckeditor.php' ;
				$ckeditor = new CKEditor( );
				
				$CI->config->set_item('ckeditor_object',$ckeditor);
				
			}
			
			$ckeditor = & $CI->config->item('ckeditor_object');
			
			$ckeditor->basePath	= '/'.PROJECTNAME.'/third_party/ckeditor/';
			$ckeditor->config['filebrowserBrowseUrl'] = '/file_browser/';
			$ckeditor->config['filebrowserImageBrowseUrl'] = '/file_browser/';
			$ckeditor->config['filebrowserFlashBrowseUrl'] = '/file_browser/';
			$ckeditor->config['filebrowserWindowWidth'] = '800';
			$ckeditor->config['filebrowserWindowHeight'] = '550';
			$ckeditor->config['height'] = $config['height'];
			$ckeditor->config['toolbar'] = $config['toolbar'];
			
			$ckeditor->config['uiColor'] = '#58C4E4'; // This value should be coming from a theme config file
			
			$ckeditor->editor($config['name'], $config['content']);
			
			
		}else{ // fall back to NERD MODE
			
			echo "<textarea style=\"height:{$config['height']}px; width: 100%\" name=\"{$config['name']}\" id=\"{$config['name']}\">{$config['content']}</textarea>";
			
			
			
		}
		
	}
	
	
	/**
	* Returns an array of all the pages in the content section. These are the wrappers of front end views.
	* @return array
	*/
	public function get_content_page_list($data){
		
		
		$CI =& get_instance();
		
		$language = (isset($data['language']) ? $data['language']  : "en" );
		
		$use_ids = ( (isset($data['use_ids']) && $data['use_ids']) ? TRUE  : FALSE);
		
		
		if(!isset($data['usewrapper'])) $data['usewrapper']  = FALSE;
		

		$CI->load->library('adjacencytree');
		
		$content_table = "{$CI->db->dbprefix}content_" . $language;

		$CI->adjacencytree->init();
		$CI->adjacencytree->setdebug(FALSE);
		
		$CI->adjacencytree->setidfield('node_id');
		$CI->adjacencytree->setparentidfield('node_parent');
		$CI->adjacencytree->setpositionfield('node_position');
		$CI->adjacencytree->setdbtable("{$CI->db->dbprefix}content_nodes");
		$CI->adjacencytree->setDBConnectionID($CI->db->conn_id);
		
		$CI->adjacencytree->setjointable($content_table,"CO_node", array("CO_Node","CO_MenuTitle","CO_seoTitle","CO_Url","CO_Color"));
		
		$node = $CI->adjacencytree->getFullNodesArray();
		
		$baserootid = 1; // this is the ROOT node
		
		$groups = $CI->adjacencytree->getChildNodes($baserootid);
		
		// load this so save a little processing in the pagelinkselectorlist function
		$CI->load->library("image_color");

		return $this->pagelinkselectorlist($groups, $language, $use_ids, TRUE);
		
	}
	
	
	
	/**
*
* loop throught the adjacency array to return an ordered 2d array
* 
* @param items array
* @param language string
* @return array
*/
	private function pagelinkselectorlist($items, $language, $use_ids = FALSE, $init = FALSE) {

		$CI =& get_instance();
		
		static $page_color, $text_color, $color_switch_nestlevel, $nestlevel, $bgcolors, $bgtxtcolors;

		
		if(!$nestlevel) $nestlevel = 0;	
		if(!$bgcolors) $bgcolors = array();
		if(!$bgtxtcolors) $bgtxtcolors = array();	
		
		
		static $return_array;
		
		if($init) $return_array = null;
		
		if(!$return_array) $return_array = array();
		
		
		
		if (count($items) && is_array($items)) {
			
			
			foreach ($items as $page_id=>$page_vals) {
				
				
				if($nestlevel <= $color_switch_nestlevel){
					
					if( isset($bgcolors[$page_vals['node_parent']])){ // we are back to where we were before we set a color	
						$page_color = $bgcolors[$page_vals['node_parent']];
						$text_color = $bgtxtcolors[$page_vals['node_parent']];
						
					}else{
						
						$page_color = null;
						$text_color = null;
						
					}
				}
				
				if($page_vals['CO_Color'] != ""){
					
					$page_color = $page_vals['CO_Color']; // this is the color that carries thru
					$bgcolors[$page_id] = $page_color;
					
					$text_color = "#" . $CI->image_color->getTextColor($page_color);
					$bgtxtcolors[$page_id] = $text_color;
					
					$color_switch_nestlevel = $nestlevel;
				}
				
				
				$has_childen = false;
				
				if(isset($page_vals['children']) && count($page_vals['children'])) { 
					$has_childen = true;
				}

				$code_indent = str_repeat("---",($nestlevel));
				
				$label = "";
				
				if($page_vals['CO_MenuTitle'] != "") $label = $page_vals['CO_MenuTitle'];
				else $label = $page_vals['CO_seoTitle'];
				
				
				$return_array[] = array(
				
				"id" => $page_id,
				"label" => $label,
				"code_indent" => $code_indent,				
				"link"  => "/" . $language . "/" . $page_vals['CO_Url'],
				"color" => $page_color,
				"text_color" => $text_color,
				
				);
				
				
				if ($has_childen){
					
					$nestlevel ++;
					
					$this->pagelinkselectorlist($page_vals['children'], $language, $use_ids);
					
					$nestlevel --;
					
				}
				
			}
			

		}

		return $return_array;
		

	}///////


	
	/**
	* Used to iso decode all the input vars prior to
	* SQL insert statements
	*/
	public function utfdecodeallvars($argsarray){
		
		$returargsarray = array();
		
		foreach($argsarray as $key=>$val){
			
			
			if(is_array($val)){
				
				foreach($val as $subkey=>$subval){
					
					if($key != "" && $subkey != "" && $subval != ""){

						if(is_numeric($subval)){
							
							$returargsarray[$key][$subkey] = $subval;
							
						}else{
							
							
							$returargsarray[$key][$subkey] = $this->charset_decode_utf_8($subval);
						}
						
						
						
						
					}else{
						
						
					}
					
				}
				
			}else{
				
				if($key != "" && $val != ""){

					if(is_numeric($val)){
						$returargsarray[$key] = $val;
					}else{
						$returargsarray[$key] = $this->charset_decode_utf_8($val);
					}
				}
				
			}
		}
		
		
		return $returargsarray;
		
		
		
	}//////////////
	
	
	///////////////////////////////////////////////////////////
	// COPIED FROM SQUIRREL MAIL
	//	
	function charset_decode_utf_8($string) {
		
		/* Only do the slow convert if there are 8-bit characters */
		/* avoid using 0xA0 (\240) in ereg ranges. RH73 does not like that */
		if (! preg_match("/[\200-\237]/", $string) and ! preg_match("/[\241-\377]/", $string))
		return $string;

		// decode three byte unicode characters
		$string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e",       
		"'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",   
		$string);

		// decode two byte unicode characters
		$string = preg_replace("/([\300-\337])([\200-\277])/e",
		"'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'",
		$string);

		return $string;
	} 

	


	/**
	*
	*
	*/
	function isDirEmpty($dirname){

		
		$CI =& get_instance();
		
		$CI->load->library("ps_files");
		

		return $CI->ps_files->isDirEmpty($dirname);


	}/////////////////




	/**
	* SWAPS ONE ARRAY ELEMENT POSITION WITH ANOTHERS
	*
	*/
	function change_array_position($target_array, $current_position, $new_position){

		if($current_position != $new_position 
				&& $new_position > -1 && $current_position > -1
				&& $new_position < count($target_array) 
				&& $current_position < count($target_array)){

			$displacedrec = $target_array[$new_position];

			$target_array[$new_position] = $target_array[$current_position];
			$target_array[$current_position] = $displacedrec;
		}

		return $target_array;

	}



	/**
	*
	*
	*/
	function deldir($dir){

		$CI =& get_instance();
		
		$CI->load->library("ps_files");

		$CI->ps_files->deldir($dir);

	}


	/**
	*
	*
	*/
	function copydir($source, $dest){

		$CI =& get_instance();
		
		$CI->load->library("ps_files");

		return $CI->ps_files->copydir($source, $dest);

		

	}


	

	/**
	* Removes an entry to the search index table
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	* @param array $data  (content_id, content_module)
	*
	*/
	function deletesearchindex($searchdata){
		
		$CI =& get_instance();
		
		$CI->db->delete("searchindex_" . $searchdata['lang'], array('content_id' =>  $searchdata['content_id'] ,'content_module' => $searchdata['content_module'])); 
		
		
		

	}///////////////////////////////////////////////////////////////////////////////
	



	
	/**
	* Adds an entry to the search index table
	* @author Galbraith Desmond <galbraithdesmond@gmail.com>
	* @param array $data  (id, type, content)
	* @todo ADD option for tagging content
	*/
	function addsearchindex($searchdata){

		$CI =& get_instance();
		
		if(!isset($searchdata['expiration'])) $searchdata['expiration'] = date('Ymd', strtotime('+5 years'));
		if(!isset($searchdata['updated'])) $searchdata['updated'] = date('Ymdhi');

		if(!isset($searchdata['priority'])) $searchdata['priority'] = 8;
		if(!isset($searchdata['change_frequency'])) $searchdata['change_frequency'] = 3;
		
		
		
		// first lets check to see if this item exists so we don't keep raking up incremented ids and making life generally
		// miserable for some poor sod trying to debug this thing.
		
		$searchindex_id = null;
		
		$CI->db->select('id');
		
		$query = $CI->db->get_where("searchindex_" . $searchdata['lang'], array('content_id' =>  $searchdata['content_id'] ,'content_module' => $searchdata['content_module'])); 
		
		if ($query->num_rows() > 0){
			
			$row = $query->row();
			
			$searchindex_id = $row->id;		
			
		}
		
		
		$searchdata['content'] = $this->stripContent($searchdata['content']);
		$searchdata['content'] = preg_replace("/{[^{]*}/", "", $searchdata['content']);
		
		$contentlength = strlen($searchdata['content']);
		

		$data = array(
		'title' => $searchdata['title'],
		'content' => $searchdata['content'],
		'content_id' => $searchdata['content_id'],
		'content_module' => $searchdata['content_module'],

		'priority' => $searchdata['priority'],
		'change_frequency' => $searchdata['change_frequency'],
		'slug' => $searchdata['slug'],
		
		'expiration' => $searchdata['expiration'],
		'updated' => $searchdata['updated'],
		'contentlength' => $contentlength,		
		);
		

		
		
		if($searchindex_id){
			
			$CI->db->where('id', $searchindex_id);
			$CI->db->update("searchindex_" . $searchdata['lang'], $data); 
			
		}else{
			
			$CI->db->insert("searchindex_" . $searchdata['lang'], $data); 

		}
		
		//echo $CI->db->last_query();

		
		
	}//////////////////////////////////
	



	/**
	* remove javascript and other illegal html chars
	*
	*/	
	function stripContent($contents){
		
		
		
		$pattern = array (	"'<script[^>]*?>.*?</script>'si",
		"'<div[^>]*?class=\"hide\"[^>]*?>.*?</div>'si",
		"'<a[^>]*?class=\"hide\"[^>]*?>.*?</a>'si",
		"'<div[^>]*?class=\"varselect\"[^>]*?>.*?</div>'si",
		"'\t'si",

		"'&(quot|#34);'i", // replacing HTML entities
		"'&(amp|#38);'i",
		"'&(lt|#60);'i",
		"'&(gt|#62);'i",
		"'&(nbsp|#160);'i",
		"'&(iexcl|#161);'i",
		"'&(cent|#162);'i",
		"'&(pound|#163);'i",
		"'&(copy|#169);'i",
		"'&#(\d+);'e");                    

		$replacement = array (	"",
		"",
		"",
		"",
		" ",

		"\"",
		"&",
		"<",
		">",
		" ",
		chr(161),
		chr(162),
		chr(163),
		chr(169),
		"chr(\\1)");

		$stripped = strip_tags(preg_replace($pattern, $replacement, $contents));
		
		//$stripped  = $pf->op_public->RemoveIllegalFileChars($stripped);
		
		$stripped = str_replace("\n","",$stripped);
		$stripped = str_replace("\r","",$stripped);
		
		$stripped = str_replace("[PAGEBREAK]","\n[PAGEBREAK]\n",$stripped);
		
		
		return $stripped;
		
	}//////////////////////////////////
	
	

	
	
	
	/**
	*
	* strips out HTML header and footer and just returns
	* body content
	*/
	function getHTMLContent($html){
		
		$html=preg_replace("/<!DOCTYPE((.|\n)*?)>/ims","",$html);
		preg_match("/<head>((.|\n)*?)<\/head>/ims",$html,$matches);
		preg_match("/<title>((.|\n)*?)<\/title>/ims",$head,$matches);
		$html=preg_replace("/<head>((.|\n)*?)<\/head>/ims","",$html);
		$html=preg_replace("/<\/?body((.|\n)*?)>/ims","",$html);
		$html = preg_replace("/<\/?html((.|\n)*?)>/ims","",$html);
		
		return $html;
		
	}/////////
	
	
	
}


