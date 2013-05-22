<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class im_pagination{

	public $max = null;
	
	public $maxperpage = 10; // maximum number or results to show pre page
	public $maxpagesperpage = 10; // maximum number of pages to show in the paginator
	
	public $page = 1;
	
	public $anchor = '';
	public $current_url = '';
	
	public $script = '';
	public $params = null;
	
	
	public $pager_id = '';	
	
	public $previmg = "";
	public $nextimg = "";

	
	public $asset_url = '';
	
	public $page_varname = 'page';	

	public $css_ul_class = '';	
	
	public $css_ul_id = '';	
	public $css_li_class = '';	
	
	public $css_ul_active_class = '';	
	public $css_li_disabled_class = '';	
	
	public $doajax = FALSE;	
	
	
	/**
	*
	*
	*/
	function __construct(){
	
		$this->asset_url = ASSETURL . "public/default/images/";
				
		
	}
	
	
	/**
	*
	*
	*/
	public function init($config){
		

		$this->max = (isset($config['max']) ? $config['max'] : "");
		$this->maxperpage = (isset($config['maxperpage']) ? $config['maxperpage'] : 10);
		$this->page = (isset($config['page']) ? $config['page'] : 1);
		$this->seperator = (isset($config['seperator']) ? $config['seperator'] : "");
		$this->maxpagesperpage = (isset($config['maxpagesperpage']) ? $config['maxpagesperpage'] : 10);
		$this->pager_id = (isset($config['pager_id']) ? $config['pager_id'] : "");
		$this->anchor = (isset($config['anchor']) ? $config['anchor'] : "");
		$this->script = (isset($config['script']) ? $config['script'] : "");
		$this->params = (isset($config['params']) ? $config['params'] : "");
		$this->page_varname = (isset($config['page_varname']) ? $config['page_varname'] : "page");
		$this->previmg = (isset($config['previmg']) ? $config['previmg'] : "");
		$this->nextimg = (isset($config['nextimg']) ? $config['nextimg'] : "");
				
		
		$this->asset_url = (isset($config['asset_url']) ? $config['asset_url'] : ASSETURL . "public/default/images/");
							

		$this->css_ul_id = (isset($config['css_ul_id']) ? $config['css_ul_id'] : "");
		
		$this->css_ul_class = (isset($config['css_ul_class']) ? $config['css_ul_class'] : "pagination");

		$this->css_li_class = (isset($config['css_li_class']) ? $config['css_li_class'] : "");

		$this->css_ul_active_class = (isset($config['css_ul_active_class']) ? $config['css_ul_active_class'] : "active");
		$this->css_li_disabled_class = (isset($config['css_li_disabled_class']) ? $config['css_li_disabled_class'] : "disabled");
		
	
		$this->current_url = (isset($config['current_url']) ? $config['current_url'] : $this->current_url());

	
		
	}

	
	/**
	* legacy call
	*
	*/
	public function initialize($config){
		
		$this->init($config);
				
	}

	
	

	/**
	* @uses creates a static paginator
	* @author Galbraith Desmond
	* @param max int number of total records
	* @param maxperpage int max to show on any page
	* @page current page number
	* @current_url string url of requesting page
	* @separator string  used to separate items
	* @maxPagesPerPage into number of pages to show in paginator nav bar
	* @pager_id string  used to identify paginator for page with more than one paginator instance
	* @anchor string anchor element to sroll to
	*
	* @return html 
	*/
	function create_links( ){

	
		$ajax_params = "";
			

		if($this->script != FALSE && $this->script != ""){
				
			if(is_array($this->params)){
			
				$ajax_params = ",'" . implode("','",$this->params) . "'";
				
			}
					
		
			$previcon = "<li class=\"previous\"><a href=\"javascript:{$this->script}('" . ($this->page-1) . "'{$ajax_params})\"><img src=\"".$this->asset_url . $this->previmg . "\"></a></li>";
			$nexticon = "<li class=\"next\"><a href=\"javascript:{$this->script}('" . ($this->page+1) . "'{$ajax_params})\"><img src=\"".$this->asset_url . $this->nextimg . "\"></a></li>";
			
					

		}else{
		
			$previcon = "<li class=\"previous\"><a class=\"prev_img\" href=\"" . $this->current_url . $this->leadquery() . "{$this->page_varname}=" .($this->page-1) . $this->anchor ."\" title=\"Previous Page\"><img src=\"".$this->asset_url .$this->previmg ."\"></a></li>";
			$nexticon = "<li class=\"next\"><a class=\"next_img\" href=\"" . $this->current_url . $this->leadquery() . "{$this->page_varname}=" .($this->page+1) . $this->anchor  ."\" title=\"Next Page\"><img src=\"".$this->asset_url . $this->nextimg ."\" ></a></li>";
		}

		if($this->maxperpage == "") $this->maxperpage = 10;


		$podstron =  ceil( $this->max / $this->maxperpage ); 

		
		// if there is only one page of results, not any point in having a paginator
		if($podstron < 2) return "";
		
		
		
		
		$outbuf = "<ul ";
		
		if($this->css_ul_id != "") $outbuf .= " id=\"{$this->css_ul_id}\" ";
		if($this->css_ul_class != "") $outbuf .= " class=\"{$this->css_ul_class}\" ";
		
		$outbuf .= ">\n";

		if($podstron > $this->page) 
		$next = 1; 
		else  
		$next = 0; 

		$this->max = ceil( $this->page + ( $this->maxpagesperpage / 2 ) );
		$min = ceil( $this->page - ( $this->maxpagesperpage / 2 ) );

		if( $min < 0 )
		$this->max += -( $min );
		if( $this->max > $podstron )
		$min -= $this->max - $podstron;

		$l['min'] = 0;
		$l['max'] = 0;

		if($this->page > 1) $outbuf .= $previcon;

		

		for ( $i = 1; $i <= $podstron; $i++ ) { 

			if( $i >= $min && $i <= $this->max ) {

				if ( $i == $this->page ){
					$outbuf .= "<li class=\"{$this->css_ul_active_class}\"><a href=\"#\">{$i}</a></li>\n"; 
				}
				else{
				
					if($this->script != ""){
					
						$outbuf .= "<li><a href=\"javascript:{$this->script}('{$i}'{$ajax_params});\">" . $i ."</a></li>\n"; 
					
					}else{
						$outbuf .= "<li><a href=\"" . $this->current_url . $this->leadquery() . "{$this->page_varname}=" .$i .  $this->anchor . "\">" . $i ."</a></li>\n"; 
					}
				}

			}elseif( $i < $min ) {

				if( $i == 1 ){
				
					if($this->script != ""){
					
						$outbuf .= "<li><a href=\"javascript:{$this->script}('{$i}'{$ajax_params});\">".$i."</a></li>\n"; 

					}else{
					
						$outbuf .= "<li><a href=\"".$this->current_url. $this->leadquery() . "{$this->page_varname}=".$i. $this->anchor . "\">".$i."</a></li>\n"; 
					
					}
				}else{
				
					if( $l['min'] == 0 ){
		
						$outbuf .= "<li class=\"{$this->css_li_disabled_class}\">...</li>\n"; 
						$l['min'] = 1;
					}
				}

			}elseif( $i > $min ) {

				if( $i == $podstron ){
					
					if($this->script != ""){

						$outbuf .= "<li><a href=\"javascript:{$this->script}('{$i}'{$ajax_params});\">".$i."</a></li>\n"; 

					}else{
					
						$outbuf .= "<li><a href=\"" . $this->current_url  . $this->leadquery() . "{$this->page_varname}=".$i. $this->anchor . "\">".$i."</a></li>\n"; 
					
					}
					
				}else{
					if( $l['max'] == 0 ){
						$outbuf .= "<li class=\"{$this->css_li_disabled_class}\">...</li>\n"; 
						$l['max'] = 1;
					}
				}
			}
		} // end for

		if($this->page < $podstron) $outbuf .= $nexticon;

		
		
		
		if($podstron > 10){
			
			$CI =& get_instance(); 
			
			$CI->load->library("formelement");
			
			
			$splitlimit = 10;
			
			if($podstron > 100) $splitlimit = 20;
			if($podstron > 500) $splitlimit = 50;
			if($podstron > 1000) $splitlimit = 100;
			if($podstron > 5000) $splitlimit = 500;		
			if($podstron > 10000) $splitlimit = 1000;	
			
			$splitrange = round($podstron / 10);
			
			
			$pagehops_array = array(lang('gotopage')=>"",1=>1);
			
			
			for($i=10; $i < $podstron; $i+= 10){
				$pagehops_array[$i] = $i;
			}
			
			$pagehops_array[$podstron] = $podstron;
			
			
			$fielddata = array();
			
			
			$fielddata['name'] = "paginator_select";
			$fielddata['type'] = 'select';
			$fielddata['id'] = "paginator_select";
			$fielddata['label'] = "Category";
			
			if($this->script != ""){

				$fielddata['onchange'] = "{$this->script}('{$i}'{$ajax_params});\">".$i."</a></li>\n"; 

			}else{
			
				$fielddata['onchange'] = "document.location='{$this->current_url}" . $this->leadquery() . "page=' + this.value + '{$this->anchor}';";
				
			}
			
			$fielddata['options'] = $pagehops_array;
			$fielddata['value'] = $this->page;
			
			$outbuf .= "<li>" . $CI->formelement->generate($fielddata) . "</li>\n";
			
		}
		
		
		$outbuf .= "</ul>";

		$outbuf .= "<input type=\"hidden\" id=\"".$this->pager_id."current_page\" name=\"".$this->pager_id."current_page\" value=\"{$this->page}\">";

		
		return $outbuf;

	} // end function countPages
	
	
	/**
	*
	*
	*/
	private function current_url() {
		
		$pageURL = 'http';
		$pageURL .= "://";
		
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		
		
		$pageURL = preg_replace('/([?&])'.$this->page_varname.'=[^&]+(&|$)/','$1',$pageURL);
		
		return $pageURL;
	}
	

	
	/**
	* Cleans up the argument list so we can swap out our paginator calls 
	* while maintinaing any other arguments that may be in the url
	*/
	private function leadquery(){
		
		if(strpos($this->current_url, "?") === FALSE) return "?";
		if ( substr($this->current_url, -1) == "?") return "";
		if(strpos($this->current_url, "?") !== FALSE){
			if ( substr($this->current_url, -1) != "&") return "&";
		}
						
		return "";
				
	}
	

	

	
}
