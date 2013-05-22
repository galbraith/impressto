<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Asset loader - Loads javascript and css assets following specified rules 
* main features: 
* prevents duplicated loading of assets
* ensures assets exits before loading
* allows smarty style variables to be parsed within assets 
* can be used to load all assets in head for W3C validity
* provides positioning of loaded assets
* auto compression of assets
* autoloading for mobile versions of assets
* autoloading for docket versions of assets (client specific versions)
* autoloading of extended versions of assets (overrrides that do not require changes to core asset files)
*
* @package		asset_loader
* @author		Galbraith Desmond <galbraithdesmond@gmail.com>
* @description Loads javascript and css assets following specified rules
*
* @version		1.0.1 (2012-03-18)
*/

class asset_loader {
	
	public $loaded_scripts = array();
	public $loaded_styles = array();
	public $module_asset_versions = array();
	
	
	private $vardata = array();

	public $header_top_css = null;
	public $header_top_js = null;
	
	public $misc_header_top_assets = null; //  this allows us to load custom third party header data
	public $misc_header_assets = null; //  this allows us to load custom third party header data
	
	
	
	public $header_css = null;
	public $header_js = null;
	
	public $header_top_js_strings = null;
	public $header_top_css_strings = null;	
	
	public $header_js_strings = null;
	public $header_css_strings = null;

	public $blocked_js = null;
	public $blocked_css = null;
	
	public $block_all_assets = FALSE;
	
	
	
		
	
	public function __construct(){

	}

	
	public function set_module_asset_version($module_name,$version_num){
	
		if($module_name != "" && !is_numeric($module_name) && is_numeric($version_num)){
			
			$this->module_asset_versions[$module_name] = $version_num;
			
		}

	
	}
	
	
	/**
	* Copied over from template_loader. Used to find the optimum assets based on docket, lang, device
	* @todo - setup
	*/
	function find_assets($data, $fullkeys = FALSE){ 
	
	
	}
	
	
	/**
	* Copied over from template_loader. Used to find the optimum assets based on docket, lang, device
	* @todo - setup
	*/
	function find_asset($data){ 
	
	
	}
	
	/**
	* Copied over from template_loader. Used to find the optimum assets based on docket, lang, device
	* @todo - setup
	*/
	function get_fallback_folders($data){
	
	
	}
	
	
	

	/**
	* This adds a labeled third party section of asset calls to the head
	*
	*/
	public function add_misc_header_assets($label, $string){
	
		$this->misc_header_assets[$label] = $string;
		
		
	}

	
	/**
	* This adds a labeled third party section of asset calls to the head
	*
	*/
	public function add_misc_header_top_assets($label, $string){
	
		$this->misc_header_top_assets[$label] = $string;
		
	}
	
	
	
	
	
	
	/**
	* This simply adds the js at the top of the array
	*
	*/
	public function add_header_css_top($uncompressed_css, $data = null, $media = 'screen', $version = null){
		$this->add_header_css($uncompressed_css, $data, $media, "TOP");
	}
	
	/**
	* this does not return anything but simply stores the css
	* in memory to be loadd into the page header.
	* Similar to Wordpress wp_register_style
	*
	*/
	public function add_header_css($uncompressed_css, $data = null, $media = 'screen', $position = null, $version = null){
		

		$uncompressed_css = str_replace(ASSETURL  . PROJECTNAME . "/","",$uncompressed_css);
		
		//$uncompressed_css = str_replace(".min.css",".css",$uncompressed_css); // fix invalid direct calls for minifed assets
		
		
		// we will not load anything that has been blocked
		if(is_array($this->blocked_css) && in_array($uncompressed_css,$this->blocked_css)){
			return;
		}
		
		
		if($this->header_css == null) $this->header_css = array();
		if($this->header_top_css == null) $this->header_top_css = array();
		
		$preparedasset = $this->insert_css($uncompressed_css, $data, $media);
		
		if($preparedasset){
			
			if($position == "TOP"){
				$this->header_top_css[$uncompressed_css] = $preparedasset;
			}else{
				$this->header_css[$uncompressed_css] = $preparedasset;
			}

		}
		
	}
	
	
	/**
	* This simply adds the js at the top of the array
	*
	*/
	public function add_header_css_string_top($css, $data = null){
		
		$this->add_header_css_string($css, $data, "TOP");
	}
	
	/**
	* this does not return anything but simply stores the css string
	* in memory to be loadd into the page header
	*
	*/
	public function add_header_css_string($css, $data = null, $position = null){
		
		if($this->header_css_strings == null) $this->header_css_strings = array();
		if($this->header_top_css_strings == null) $this->header_top_css_strings = array();

		if(is_array($data)){
			foreach($data as $key=>$val){
				$this->vardata[$key] = $val;
			}
		}
		
		if($position == "TOP"){
		
			$this->header_top_css_strings[] = $this->varparse($css);		
		
		}else{
					
				
			$this->header_css_strings[] = $this->varparse($css);
		}

	}
	

	/**
	* returns the plepared asset for inclusion in a page
	* @ returns valid  asset resource string
	*/
	public function insert_css($asset, $data = null, $media = 'screen' )
	{
		return $this->insert_asset($asset, $data, $media);
		
	}	
	
	
	/**
	* returns the plepared asset for inclusion in a page
	* Note that if the non-minified version is newer than minified version, the non-minified version will load
	* If no non-minified version exists, the minified version will load if it exists.
	* @ returns valid  asset resource string
	*/
	private function insert_asset($asset, $data = null, $media = 'screen' ){
		
		$CI = & get_instance();
		
		$CI->load->library('lessc');
		
		$site_lang = $CI->config->item('site_lang') != "" ? $CI->config->item('site_lang') : 'en';					
		
		$returnstring = "";
		
		$filetype = $this->get_extention($asset);
		
		if($filetype == "css") $loadedholder = &$this->loaded_styles;
		else  $loadedholder = &$this->loaded_scripts;
		
		$original_uncompressed_asset = $asset;
				
		$asset = $this->seek_asset_version($asset);
		
		if(!$asset || $asset == ""){ // no non compresed version found.
			
			// look for a compressed version
			
			if($filetype == "css"){
				$asset = $this->replace_extension(str_replace(".min.css",".css",$original_uncompressed_asset),"min.css");
			}else{
				$asset = $this->replace_extension(str_replace(".min.js",".js",$original_uncompressed_asset),"min.js");
			}

			$asset = $this->seek_asset_version($asset);
			
			if(!$asset || $asset == ""){
		
				$returnstring .=  "\n<!-- Missing $original_uncompressed_asset -->\n";
				return $returnstring;
			}
			
		}
		
			
		
		if( strpos($asset, "http://") === false 
				&&
				strpos($asset, "https://") === false
				&&
				strpos($asset, getenv("DOCUMENT_ROOT")) === false
				){
			$asset = getenv("DOCUMENT_ROOT") . $asset;
		}
		
		
		if(!in_array($asset,$loadedholder)){

			$loadedholder[] = $asset;

			
			if(strpos($asset, "http://") !== false || strpos($asset, "https://") !== false ){
				
				if($filetype == "css"){
					
					$returnstring .= "\n	<link rel=\"stylesheet\" href=\"{$asset}\" media=\"{$media}\" />";
					
					
				}else{
					
					$returnstring .= "\n	<script type=\"text/javascript\" src=\"{$asset}\"></script>";	
					
				}
				
				return $returnstring;
				
			}			
			
			

					
			
			$assetdir = dirname($asset);
			
			$assetfile = $this->getfilename($asset);
			
			
			if($filetype == "css"){
				
				if($this->domobile()){ // look for mobile versions of this asset first if this applies
					
					$less_version =  $this->seek_docket_version($assetdir . "/" . $this->remove_extention($assetfile) . ".mobi.less");	
					
					if(!$less_version) $less_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . ".mobi.less");
					
					if(!$less_version) $less_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . ".less");
					
					
				}else{
					
					$less_version =  $this->seek_docket_version($assetdir . "/" . $this->remove_extention($assetfile) . ".less");	
					
					if(!$less_version) $less_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . ".less");
					
				}
				
				if($less_version && file_exists($less_version)){
					$CI->lessc->ccompile($less_version, $asset);
				}

			}
			
			
			if($this->domobile()){ // look for mobile versions of this asset first if this applies
				
				$original_version = $this->seek_docket_version($assetdir . "/" . $this->remove_extention($assetfile) . ".mobi." . $this->get_extention($assetfile));
				
				if(!$original_version){
					$original_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . ".mobi." . $this->get_extention($assetfile));
				}
				
				if(!$original_version){
					// look for the docket standard version
					$original_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . "." . $this->get_extention($assetfile));
				}
						
				
			}else{
				
				// look for the docket standard version
				$original_version = $this->seek_docket_version($assetdir . "/" . $this->remove_extention($assetfile) . "." . $this->get_extention($assetfile));
			
				
				if(!$original_version){
					// look for the standard version
					$original_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . "." . $this->get_extention($assetfile));
				}
				
			}
			
			// now look for extended versions
			if($this->domobile()){ // look for mobile versions of this asset first if this applies
				
				// look for the docket version first
				$extended_original_version = $this->seek_docket_version($assetdir . "/" . $this->remove_extention($assetfile) . ".mobi.ext." . $this->get_extention($assetfile));
				
				if(!$extended_original_version){
					// look for the default mobi version
					$extended_original_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . ".mobi.ext." . $this->get_extention($assetfile));
				}
				
				if(!$extended_original_version){
					// look for the docket standard version
					$extended_original_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . ".ext." . $this->get_extention($assetfile));
				}

				// look for the localized docket standard version
					

				// look for the docket version first
				$localized_original_version = $this->seek_docket_version($assetdir . "/" . $this->remove_extention($assetfile) . ".mobi.".$site_lang."." . $this->get_extention($assetfile));
				
				if(!$localized_original_version){
					// look for the default mobi version
					$localized_original_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . ".mobi.".$site_lang."." . $this->get_extention($assetfile));
				}
				
				if(!$localized_original_version){
					// look for the docket standard version
					$localized_original_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . ".".$site_lang."." . $this->get_extention($assetfile));
				}
				
				
			}else{ // javascript
				
				// look for the docket standard version
				$extended_original_version = $this->seek_docket_version($assetdir . "/" . $this->remove_extention($assetfile) . ".ext." . $this->get_extention($assetfile));
				
				if(!$extended_original_version){
					// look for the standard version
					$extended_original_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . ".ext." . $this->get_extention($assetfile));
				}
				
						
				// look for the localized docket standard version
				$localized_original_version = $this->seek_docket_version($assetdir . "/" . $this->remove_extention($assetfile) . "." . $site_lang . "." . $this->get_extention($assetfile));
						
				if(!$localized_original_version){
					// look for the standard version
					$localized_original_version = $this->seek_default_version($assetdir . "/" . $this->remove_extention($assetfile) . "." . $site_lang . "." . $this->get_extention($assetfile));
				}

				
			}
			
			$devmode = false;
			if( $CI->config->item('debugmode') ) $devmode = true;
			
			
			$outname = FALSE; // default to assume not found
			$extended_outname = FALSE;
			$localized_outname = FALSE;
					
			//echo "LOOKING FOR $original_version <br />\n";
			
			
			if($devmode){
				
				$outname = trim($original_version);
				
				if($extended_original_version) $extended_outname = $extended_original_version;
				if($localized_original_version) $localized_outname = $localized_original_version;
			
			
			}else{
				
				// we will only compress css files.
				if($original_version && $filetype == "css"){
				
					$compressed_version = $this->replace_extension(str_replace(".min.css",".css",$original_version),"min.css");
					if($extended_original_version) $extended_compressed_version = $this->replace_extension(str_replace(".min.css",".css",$extended_original_version),"min.css");
					if($localized_original_version) $localized_compressed_version = $this->replace_extension(str_replace(".min.css",".css",$localized_original_version),"min.css");

					// if a compressed version exists use it
					if(file_exists($compressed_version)){

						// the uncompressed file has been changed so recompress it
						if(filemtime($compressed_version) > filemtime($original_version)){
							$outname = $compressed_version;
						}else{
							$outname = $original_version;
						}
					}else{
						$outname = $this->compresscss($original_version, $compressed_version);
					}
					
					// if a compressed version exists use it
					if(isset($extended_compressed_version) && file_exists($extended_compressed_version)){
						
						if($extended_original_version && filemtime($extended_compressed_version) > filemtime($extended_original_version)){
							$extended_outname =$extended_compressed_version;
						}else{
							$extended_outname = $extended_compressed_version;
						}
					}else if($extended_original_version){
						$extended_outname = $this->compresscss($extended_original_version, $extended_compressed_version);
					}
					
					// now do the same for the localized version
					if(isset($localized_compressed_version) && file_exists($localized_compressed_version)){
						
						if($localized_original_version){

							if(filemtime($localized_original_version) > filemtime($localized_compressed_version)){
								$localized_outname = $this->compresscss($localized_original_version, $localized_compressed_version);
							}else{
								$localized_outname = $localized_compressed_version;
							}
						}
						
					}else if($localized_original_version){
						$localized_outname = $this->compresscss($localized_original_version, $localized_compressed_version);
					}
					
					

				}else if($original_version){ // javascript
				
					$compressed_version = $this->replace_extension(str_replace(".min.js",".js",$original_version),"min.js");
					
					if(file_exists($compressed_version)){  // if javascript just look for the existence of a compressed version but do not make one
						
						if(filemtime($compressed_version) > filemtime($original_version)){
							$outname = $compressed_version;
						}else{
						
							$outname = $original_version;
							}
					}else{
						$outname = $original_version;
					}
					
										
					
					if($extended_original_version){ // no point loading an extension of no base loaded.
					
						$extended_compressed_version = $this->replace_extension(str_replace(".min.js",".js",$extended_original_version),"min.js");
					
						if(file_exists($extended_compressed_version)){
																
							if(filemtime($extended_compressed_version) > filemtime($extended_original_version)){
						
								$extended_outname = $extended_compressed_version;
						
							}else{
						
								$extended_outname = $extended_original_version;
							}
						}else{
					
							$extended_outname = $extended_original_version;
						}
					}else{
					
						$extended_outname = FALSE;
											
					}
					
					// do the localized version
					
					if($localized_original_version){
					
						$localized_compressed_version = $this->replace_extension(str_replace(".min.js",".js",$localized_original_version),"min.js");
					
						if(file_exists($localized_compressed_version)){
					
							if(filemtime($localized_compressed_version) > filemtime($localized_original_version)){
						
								$localized_outname = $localized_compressed_version;
					
							}else{
								$localized_outname = $localized_original_version;
							}
						}else{
							$localized_outname = $localized_original_version;
						}
					
					}else{
					
						$localized_outname = FALSE;
											
					
					}
					
				}
				
			}
			

							
			

			// peterdrinnan - Nov 20, 2012
			// IF YOU ARE READING THIS YOU ARE IN THE DEEPEST BOWELS OF CODE HELL
			// AND I HONESTLY APOLOGIZE FOR YOU HAVING TO BE HERE. PLEASE GO HAVE A BEER AFTER THIS SECTION.
			// If you make a bogus asset call it will trigger a hidden reload of the same page for 
			// each call which can wreak havoc on session variables. 
			// Sometimes these "." characters slip by the code above a result of my being a SHITTY CODER,
			// so here's a horrible, ugly, but totally working fix!!!!
			if(str_replace(getenv("DOCUMENT_ROOT"),"",$outname) == ".") $outname = FALSE;
						
			
			if($outname && isset($data) && is_array($data)){
				
				// WE ARE GOING TO NOW CHURN THROUGH THIS CSS AND DO VARIABLE SUBSTITUTION
				if($filetype == "css"){
					$returnstring .= "\n	<style type=\"text/css\">\n";
				}else{
					$returnstring .= "\n	<script>\n";
				}							
				
				foreach($data as $key=>$val){
					$this->vardata[$key] = $val;
				}
							
				
				$returnstring .= $this->varparse(file_get_contents($outname));
				
			
				if($filetype == "css"){
					$returnstring .= "\n	</style>\n";
				}else{
					$returnstring .= "\n	</script>\n";
				}
				
								
				
				if($extended_outname && file_exists($extended_outname)){
					
					if($filetype == "css"){
						$returnstring .= "\n	<style type=\"text/css\">\n";
					}else{
						$returnstring .= "\n	<script>\n";
					}	
					
					foreach($data as $key=>$val){
						$this->vardata[$key] = $val;
					}
					
							
					$returnstring .= $this->varparse(file_get_contents($extended_outname));
					
					if($filetype == "css"){
						$returnstring .= "\n	</style>\n";
					}else{
						$returnstring .= "\n	</script>\n";
					}

				}
				
				if($localized_outname && file_exists($localized_outname)){
					
					if($filetype == "css"){
						$returnstring .= "\n	<style type=\"text/css\">\n";
					}else{
						$returnstring .= "\n	<script>\n";
					}	
					
					//echo " HERE WE GO ";
					foreach($data as $key=>$val){
						$this->vardata[$key] = $val;
					}
					
					
					$returnstring .= $this->varparse(file_get_contents($localized_outname));
					
					if($filetype == "css"){
						$returnstring .= "\n	</style>\n";
					}else{
						$returnstring .= "\n	</script>\n";
					}

				}
				
				
				return $returnstring;
				
			}else if($outname && file_exists($outname)){
				
				$outname = str_replace(getenv("DOCUMENT_ROOT"),"",$outname);
				
				if($outname != ""){
				
					if($filetype == "css"){
						$returnstring .= "\n	<link rel=\"stylesheet\" href=\"{$outname}\" media=\"{$media}\" />";
					}else{
						$returnstring .= "\n	<script type=\"text/javascript\" src=\"{$outname}\"></script>";	
					}
				}
					
					
				if($extended_outname && file_exists($extended_outname)){
					
					$extended_outname = str_replace(getenv("DOCUMENT_ROOT"),"",$extended_outname);
					
					if($extended_outname != ""){
					
						if($filetype == "css"){
							$returnstring .= "\n	<link rel=\"stylesheet\" href=\"{$extended_outname}\" media=\"{$media}\" />";
						}else{
							$returnstring .= "\n	<script type=\"text/javascript\" src=\"{$extended_outname}\"></script>";	
						}
					
					}
					
				}
				
				if($localized_outname && file_exists($localized_outname)){
					
					$localized_outname = str_replace(getenv("DOCUMENT_ROOT"),"",$localized_outname);
					
					if($localized_outname != ""){
					
						if($filetype == "css"){
							$returnstring .= "\n	<link rel=\"stylesheet\" href=\"{$localized_outname}\" media=\"{$media}\" />";
						}else{
							$returnstring .= "\n	<script type=\"text/javascript\" src=\"{$localized_outname}\"></script>";	
						}
					}
					
				}
				
				return $returnstring;
				
			}
			
		}
		
		return false;
		
	}
	
	
	/**
	* simply run the file through a CSS compressor
	* @return usable css file name
	*/
	private function compresscss($uncompressed_css, $compressed_version){
		
		$CI = & get_instance();
		
		$CI->load->library("cssmin");
		
		$compresed_css = $CI->cssmin->minify(file_get_contents($uncompressed_css));
		
		$CI->load->helper('file');
		
		if (write_file($compressed_version, $compresed_css))
		{
			$outcssname = $compressed_version;
		}else{
			$outcssname = $uncompressed_css;
		}
		
		return $outcssname;
		
		
	}
	
	/**
	* This simply adds the js at the top of the arrayz
	*
	*/
	public function add_header_js_top($uncompressed_script, $data = null, $version = null)
	{
		$this->add_header_js($uncompressed_script, $data, "TOP");
	}

	
	
	
	/**
	* Rarely used but lets you shut off all asset loading. Useful for third party app views
	* @param bool TRUE FALSE
	*/
	public function block_all_assets($value = FALSE){
	
		$this->block_all_assets = $value;
		
	}
	
	/**
	* this shuts off a stylesheets previously queued for loading
	* Use this when you need to fix a library conflict
	*
	*/
	public function block_css($uncompressed_css){
	
		if($this->header_css == null) $this->header_css = array();
		if($this->header_top_css == null) $this->header_top_css = array();
		if($this->blocked_css == null) $this->blocked_css = array();

				
		$uncompressed_css = str_replace(ASSETURL . PROJECTNAME . "/","",$uncompressed_css);
		
		$this->blocked_css[] = $uncompressed_css;
				
		unset($this->header_top_css[$uncompressed_css]);
		unset($this->header_css[$uncompressed_css]);
						

	}

	
	/**
	* this shuts off a javascript previously queued for loading
	* Use this when you need to fix a library conflict
	*
	*/
	public function block_js($uncompressed_script){
	
		if($this->header_js == null) $this->header_js = array();
		if($this->header_top_js == null) $this->header_top_js = array();
		if($this->blocked_js == null) $this->blocked_js = array();

				
		$uncompressed_script = str_replace(ASSETURL . PROJECTNAME . "/","",$uncompressed_script);
		
		$this->blocked_js[] = $uncompressed_script;
				
		unset($this->header_top_js[$uncompressed_script]);
		unset($this->header_js[$uncompressed_script]);
						

	}
	
	
	/**
	* ALIAS CALL TO block_js()
	*
	*/
	public function nullify_js($uncompressed_script){
	
		$this->block_js($uncompressed_script);
								

	}
	
	
	/**
	* resets all javascript registrations
	* used for standalone modules exposed to the front end
	*
	*/
	public function reset_js_registy(){
	
		$this->loaded_scripts = array();
		$this->header_top_js = null;
		$this->misc_header_top_assets = null; //  this allows us to load custom third party header data
		$this->misc_header_assets = null; //  this allows us to load custom third party header data
		$this->header_js = null;
		$this->header_top_js_strings = null;
		$this->header_js_strings = null;
	
	
								

	}
	
	
	/**
	* resets all css registrations
	* used for standalone modules exposed to the front end
	*
	*/
	public function reset_css_registy(){
	
		$this->loaded_styles = array();
		
		$this->misc_header_top_assets = null; //  this allows us to load custom third party header data
		$this->misc_header_assets = null; //  this allows us to load custom third party header data
	
		$this->header_css = null;
	
		$this->header_top_css_strings = null;	
		$this->header_css_strings = null;	
				

	}
	
	
		
	/**
	* this does not return anything but simply stores the scripts
	* in memory to be loadd into the page header
	*
	*/
	public function add_header_js($uncompressed_script, $data = null, $position = null, $version = null)
	{

		$uncompressed_script = str_replace(ASSETURL . PROJECTNAME . "/","",$uncompressed_script);
		
				
		// we will not load anything that has been blocked
		if(is_array($this->blocked_js) && in_array($uncompressed_script,$this->blocked_js)){
			return;
		}
	
		if($this->header_js == null) $this->header_js = array();
		if($this->header_top_js == null) $this->header_top_js = array();
		
		$preparedasset = $this->insert_js($uncompressed_script, $data);
	
		if($preparedasset){
					
			if($position == "TOP"){
				$this->header_top_js[$uncompressed_script] = $preparedasset;
			}else{
				$this->header_js[$uncompressed_script] = $preparedasset;
				
				
			}
			
		}
		

	}
	

	
	/**
	* check to see if a preferred docket version of this asset exists
	* @return uri path of asset
	*/	
	private function seek_asset_version($asset){
		
		$CI = & get_instance();
		
		
		$projectnum = $CI->config->item('projectnum');
		
		$filetype = $this->get_extention($asset);
		
		$psfolders = array("public","core","themes","core_modules","custom_modules","widgets");
		

		if(strpos($asset, "http://") !== false || strpos($asset, "https://") !== false ){
			
			return $asset;
			
			
		}else{
			
			// do a cleanup first for sloppy coders
			$asset = str_replace(ASSETURL,"",$asset);
			$asset = ltrim($asset, '/');
			$asset = str_replace(PROJECTNAME . "/","",$asset);
			$asset = str_replace("default/","",$asset);
			$asset = str_replace("//","/",$asset);
			$asset = ltrim($asset, '/');
			
			if(strpos($asset, "third_party/") !== false){
				
				$asset = str_replace("third_party/","",$asset);
				$asset = str_replace("//","/",$asset);
				$asset = ltrim($asset, '/');
				
				if(file_exists(ASSET_ROOT . "third_party/" . $asset)){
					
					return ASSETURL . PROJECTNAME  . "/default/core/third_party/" . $asset;
				}
				
			}else if(strpos($asset, "sparks/") !== false){
				
				$asset = str_replace("sparks/","",$asset);
				$asset = str_replace("//","/",$asset);
				$asset = ltrim($asset, '/');
				
				if(file_exists(ASSET_ROOT . "sparks/" . $asset)){
					
					return ASSETURL . "sparks/" . $asset;
				}
				
				
			}else if(strpos($asset, "public/") !== false){
				
				
				$asset = str_replace("public/","",$asset);
				$asset = str_replace($projectnum,"",$asset);
				$asset = str_replace("//","/",$asset);
				$asset = ltrim($asset, '/');
				
				
				if(file_exists(ASSET_ROOT . "public/" . $projectnum . "/" . $asset)){
					
					return ASSETURL . "public/" . $projectnum . "/" . $asset;
					
				}else if(file_exists(ASSET_ROOT . "public/default/" . $asset)){
					
					return ASSETURL . "public/default/" . $asset;
					
				}else if($filetype == "css" && file_exists(ASSET_ROOT . "public/default/" . $this->replace_extension($asset,"less"))){
					
					return ASSETURL . "public/default/" . $asset;
				}
				
			}else{
			
				// we know it is a module call so lets check for the module version
				
			
				
				$trimmed_asset = str_replace("custom_modules/","",$asset);
				$trimmed_asset = str_replace("core_modules/","",$trimmed_asset);
				
				$pathinfo = pathinfo($trimmed_asset, PATHINFO_DIRNAME);
				$pathinfo = array_filter( explode('/', $pathinfo) );
			
				$module_name = $pathinfo[0];
			
				//echo "==$module_name== <br />";
								
				//print_r($this->module_asset_versions);
								
				if(isset($this->module_asset_versions[$module_name])){
				
					$version = $this->module_asset_versions[$module_name];
					
					//echo "version = $version ,  $asset <br />\n";

					if(strpos($asset,"min.js") !== false){
						$asset = str_replace("$version.min.js","min.js",$asset); // just strip it in case it was there already
						$asset = str_replace("min.js",".$version.min.js",$asset);
					}else{
					
						$asset = str_replace("$version.js",".js",$asset); // just strip it in case it was there already
						$asset = str_replace(".js",".$version.js",$asset);
									
					}
					
					if(strpos($asset,"min.css") !== false){
						$asset = str_replace("$version.min.js","min.css",$asset); // just strip it in case it was there already
						$asset = str_replace("min.js",".$version.min.css",$asset);
					}else{
						$asset = str_replace("$version.css",".css",$asset); // just strip it in case it was there already
						$asset = str_replace(".css",".$version.css",$asset);
					}
									
					//echo " $asset <br />\n";
				
				}
				
				
				
				
					
				
				
				
				
			
									
				foreach($psfolders  as $folder){
								
					
					if(strpos($asset, $folder. "/") !== false){
					
						$asset = str_replace($folder. "/","",$asset);
						$asset = str_replace("default/","",$asset);
						$asset = str_replace($projectnum."/","",$asset);
						
						$asset = str_replace("//","/",$asset);
									
						
						if(file_exists(ASSET_ROOT . PROJECTNAME . "/" . $projectnum . "/" . $folder. "/" . $asset)){
							
							//echo "LOOKING FOR " . ASSETURL . PROJECTNAME . "/" . $projectnum . "/" .$folder. "/" . $asset . "<br />";
							
							return ASSETURL . PROJECTNAME . "/" . $projectnum . "/" .$folder. "/" . $asset;
							
						}else if(file_exists(ASSET_ROOT . PROJECTNAME . "/default/" . $folder. "/" . $asset)){
							//echo "LOOKING FOR " . ASSETURL . PROJECTNAME . "/default/" . $folder. "/" . $asset . "<br />";
							return ASSETURL . PROJECTNAME . "/default/" . $folder. "/" . $asset;
							
						}else if($filetype = "css" && file_exists(ASSET_ROOT . PROJECTNAME . "/default/" . $folder. "/" . $this->replace_extension($asset,"less"))){
							
							//echo "LOOKING FOR " . ASSETURL . PROJECTNAME . "/default/" . $folder. "/" . $asset . "<br />";
							return ASSETURL . PROJECTNAME . "/default/" . $folder. "/" . $asset;
							
						}
					}
				}
				
			}
			
		}
		
		return false; // no asset found ... :(
		
		
	}

	
	/**
	* This simply adds the js at the top of the array
	*
	*/
	public function add_header_js_string_top($javascript, $data = null){
		$this->add_header_js_string($javascript, $data, "TOP");
	}
	
	
	/**
	* this does not return anything but simply stores the javascript string
	* in memory to be loadd into the page header
	* Similar to Wordpress wp_register_script
	*
	*/
	public function add_header_js_string($javascript, $data = null, $position = null)
	{
		
		if($this->header_js_strings == null) $this->header_js_strings = array();
		if($this->header_top_js_strings == null) $this->header_top_js_strings = array();
		
		if(is_array($data)){
			foreach($data as $key=>$val){
				$this->vardata[$key] = $val;
			}
		}
		
		if($position == "TOP"){
			$this->header_top_js_strings[] = $this->varparse($javascript);		
		}else{
			$this->header_js_strings[] = $this->varparse($javascript);	
		}
		
		
	}
	
	/**
	* Used to place third party component assets into very top of page heads
	*
	*/
	public function output_misc_header_top_assets()
	{
		
		if($this->block_all_assets) return "";	
				
		$outbuf = "";

		if($this->misc_header_top_assets && count($this->misc_header_top_assets) > 0){
		
			foreach($this->misc_header_top_assets AS $label => $asset_call_strings){
			
				$outbuf .= "\n	<!-- third party assets for {$label} --> \n";
				
				$outbuf .= $asset_call_strings;
				
				$outbuf .= "\n";
				
			
			}
			
		}
		
	
		return $outbuf;
		
		
	}


	
	
	/**
	* Used to place third party component assets into page heads
	*
	*/
	public function output_header_misc_assets()
	{
		
		if($this->block_all_assets) return "";	
				
		$outbuf = "";

		
		if($this->misc_header_assets && count($this->misc_header_assets) > 0){
		
			foreach($this->misc_header_assets AS $label => $asset_call_strings){
			
				$outbuf .= "\n	<!-- third party assets for {$label} --> \n";
				
				$outbuf .= $asset_call_strings;
				
				$outbuf .= "\n";
				
			
			}
				
			
		}

	
		return $outbuf;
		
		
	}
	
	
	
	/**
	* 
	*
	*
	*/
	public function output_header_js()
	{
		
		if($this->block_all_assets) return "";	
				
		$outbuf = "";
		
		if(count($this->header_top_js) > 0) $outbuf .= implode("\n",$this->header_top_js);
		
		if(count($this->header_js) > 0) $outbuf .= implode("\n",$this->header_js);
		
		
		if(count($this->header_top_js_strings) > 0){
			
			$outbuf .= "\n	<script type=\"text/javascript\">\n";
			
			foreach($this->header_top_js_strings as $strings){
				$outbuf .= "\n" . $strings . "\n";
			}
			
			$outbuf .= "\n	</script>\n";
		}
		
		if(count($this->header_js_strings) > 0){
			
			$outbuf .= "\n	<script type=\"text/javascript\">\n";
			
			foreach($this->header_js_strings as $strings){
				$outbuf .= "\n" . $strings . "\n";
			}
			
			$outbuf .= "\n	</script>\n";
		}
		
		
		return $outbuf;
		
		
		
	}
	
	
	/**
	* 
	*
	*
	*/
	public function output_header_css()
	{
		
		if($this->block_all_assets) return "";	
				
		$outbuf = "";

		if(count($this->header_top_css) > 0) $outbuf .= implode("\n",$this->header_top_css);
		if(count($this->header_css) > 0) $outbuf .= implode("\n",$this->header_css);

		if(count($this->header_top_css_strings) > 0){
			
			$outbuf .= "\n	<style type=\"text/css\">\n";
			
			foreach($this->header_top_css_strings as $strings){
				$outbuf .= "\n" . $strings . "\n";
			}
			
			$outbuf .= "\n	</style>";
		}
		
		if(count($this->header_css_strings) > 0){
			
			
			$outbuf .= "\n	<style type=\"text/css\">\n";
			
			foreach($this->header_css_strings as $strings){
				$outbuf .= "\n" . $strings . "\n";
			}
			
			$outbuf .= "\n	</style>";
		}
		
		return $outbuf;
		
		
	}
	
	
	/**
	* returns the plepared asset for inclusion in a page
	* @ returns valid  asset resource string
	*/
	public function insert_js($asset, $data = null)
	{
		return $this->insert_asset($asset, $data);
		
	}
	
	

	/**
	* Call the mobile helper and checks if this site is
	* inmobile mode. If so, look for mobile versions of js and css
	*/	
	private function domobile(){
		
		$CI = & get_instance();
		
		$CI->load->helper('mobile');
		
		return ps_domobile();
		
		
	}
	


	/**
	* This function parses the asset file for php variables and substititue them for actual values
	*
	* To add variables to a CSS file or JS file, use the following syntax:
	*
	*   {$varname} // regular variable
	* 	{$arrayname[varname]} // array element
	*
	*/
	private function varparse($string){
	
		$inputstring = $string;
		
		preg_match_all( '/(\{\$[a-zA-Z_]+[a-zA-Z0-9_]*)(([\[]+[\']*[a-zA-Z0-9_]+[\']*[\]]+)*\})/', $string, $tagmatches );
		
		$tags = array_unique($tagmatches[0]);
		
		
		foreach($tags as $tag){
		
			$isvararray = false;
			
			$varname = 	str_replace('{$','', $tag );
			$varname = str_replace('}','', $varname );
			
			if( preg_match( '/\]/', $varname ) ){

				if( preg_match( '/\[/', $varname ) ){
					$isvararray = true;
				}
			}
			
			if($isvararray){
				
				list($var,) = explode( '[', $varname );
				
				if( isset( $this->vardata[$var] ) ){

					$varstart = strpos( $varname, '[' )+1;
					$varend = strpos( $varname, ']' );
					$key = substr( $varname, $varstart, $varend-$varstart );

					
					if( isset( $this->vardata[$var][$key] ) ){
						
						$string = str_replace($tag,$this->vardata[$var][$key],$string);

					}else{
						
						$string = str_replace($tag,'',$string);
						
					}

				}else{
					$string = str_replace($tag,'',$string);
				}
				
			}else{
			
		
				$string = str_replace($tag,$this->vardata[$varname],$string);
				
			}
			
		}
			
		// if the string has been converted to a zero, just return the original string
		// Still trying to figure out why the string sometimes gets converted to a zero...
		if($string == "0" || $string == "") return $inputstring;
		else return $string;
		

		
		
	} // end function parse
	
	
	private function seek_default_version($asset){
		

		
		$CI = & get_instance();
		
		if($asset == "") return false;
		
		$projectnum = $CI->config->item('projectnum');
		
		$testasset = str_replace("/".$projectnum."/","/default/", $asset);
		
		//echo "testasset = $testasset \n";
		
		if(file_exists($testasset)){
			return $testasset;
		}else{
			return false;
		}
		
	}
	
	private function seek_docket_version($asset){
		
		$CI = & get_instance();
		
		if($asset == "") return false;
		
		$projectnum = $CI->config->item('projectnum');
		
		$testasset = str_replace("/default/","/" . $projectnum . "/", $asset);
		
		if(file_exists($testasset)){
			return $testasset;
		}else{
			return false;
		}
		
		
	}
	
	
	private function getfilename($file) {
		$filename = substr($file, strrpos($file,'/')+1,strlen($file)-strrpos($file,'/'));
		return $filename;
	}

	
	private function get_extention($filename) {
		
		return pathinfo($filename, PATHINFO_EXTENSION);
		
	}

	private function remove_extention($filename) {
		
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		
		return str_ireplace("." . $extension,"", $filename);
		
	}
	
	private function replace_extension($filename, $new_extension) {
		
		return $this->remove_extention($filename) . "." . $new_extension;
		
	}
	
	
}

