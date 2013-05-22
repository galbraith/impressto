<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* form elements - an alternative to the CodeIgniter form class
*
* @package		formelement
* @author		Galbraith Desmond <galbraithdesmond@gmail.com>
* @description This is alternative to the CodeIgniter form class
*
* @version		1.8 (2012-10-05)
*
* IMPORTANT NOTE: As of version 1.8 it is assumed that on the client side you
* will be using jquery bvalidator for your clietn side validation. 
* See: http://code.google.com/p/bvalidator/
*

*/




class formelement{

	var $name = "";
	var $id = "";
	var $type;
	var $label = "";
	var $labelwidth = "";
	
	var $columns = 50;

	var $rows = 3;
	var $size = 10;
	var $options = "";
	var $disabledoptions = array();
	
	var $colors = null;
	var $textcolors = null;
	
	var $value = "";

	var $width = 0;
	var $height = 0;
	
	var $maxlength = '';
	var $tabindex = '';

	var $usewrapper = true;
	
	
	
	var $autocomplete = true;
	
	
	
	var $required = false;

	var $disabled = false;
	var $disabled_class_flag = '';
	

	
	var $forward = false;
	var $onclick = "";
	var $onfocus = "";
	var $onblur = "";
	var $onchange = "";
	var $onkeyup = "";
	var $onkeypress = "";
	
	
	// this is for french 
	var $semicolon = ":";
	
	
	
	var $showlabels = false;

	var $orientation = "vertical";
	var $elementid;

	var $xtras;
	var $isarray = false;
	
	
	var $misc;
	
	var $cssclass = "";
	var $style = "";

	var $uppercaps = "";
	
	var $gradient_begin = "";
	var $gradient_end = "";
	
	var $guidelines = "";

	var $required_marker = "*&nbsp;";
	
	var $hintcolors = array("#FFFFFF","#FF7A0F","#FFEB8F","#FF7A0F");
	
	
	var $opfvalid = "true";
	var $visible = true;	
	
	
	
	
	
	function __construct(){
		
		$this->CI = & get_instance();
		
		$this->value = "";
		$this->label = "";

	}


	
	function init(){
		
		$this->name = "";
		
		$this->label = "";
		$this->labelwidth = "";
		
		$this->columns = 50;

		$this->rows = 3;
		$this->size = 10;
		$this->options = "";
		$this->disabledoptions = array();
		
		$this->colors = "";
		$this->value = "";

		$this->width = 0;
		$this->height = 0;
		
		$this->maxlength = '';
		$this->tabindex = '';
		
		
		$this->autocomplete = true;
		
		
		
		$this->required = false;

		$this->disabled = false;
		$this->disabled_class_flag = '';
		

		
		$this->forward = false;
		$this->onclick = "";
		$this->onfocus = "";
		$this->onblur = "";
		$this->onchange = "";
		$this->onkeyup = "";
		$this->onkeypress = "";
		
		
		$this->semicolon = ":";
		
		$this->showlabels = true;

		$this->isarray = false;

		
		$this->cssclass = "";
		$this->style = "";

		$this->uppercaps = "";
		
		$this->gradient_begin = "";
		$this->gradient_end = "";
		
		$this->guidelines = "";

		$this->required_marker = "*&nbsp;";
		
		$this->hintcolors = array("#FFFFFF","#FF7A0F","#FFEB8F","#FF7A0F");
		
		
		$this->opfvalid = "true";
		$this->visible = true;	
		
	}

	
	function setName($name){
		
		$name = $this->FixFieldName($name);
		
		$this->name = $name;
		
		
	}



	
	function setType($type){
		$this->type = strtolower($type);
		if($this->type == "check") $this->type = "checkbox";
		
	}
	

	function setRequiredMarker($value){
		$this->required_marker = $value;
	}
	
	


	function setLabel($label){

		$label = trim($label);
		
		if($label != "") $label = html_entity_decode($label);
		
		$this->label = $label;

	}

	function setLabelWidth($width){

		if(is_numeric($width)) $this->labelwidth = $width;


		
	}
	
	function setXtras($value){
		
		$this->xtras = $value;
		
	}



	
	
	
	
	function setTabIndex($value){
		$this->tabindex = $value;
	}
	
	function setMisc($value){
		$this->misc = $value;
	}
	

	function setGradient($begin,$end){

		global $qcolors;

		$this->gradient_begin = $begin;
		$this->gradient_end = $end;

	}

	function setShowLabels($value = true){
		
		$this->showlabels = $value;

	}
	
	
	function setIsArray($value){
		
		$this->isarray = $value;
		
	}
	

	function setColumns($value){
		$this->width = ($value * 6);
	}

	function setWidth($value){
		$this->width = $value;
		$this->columns = $value;
		$this->size = $value;
	}
	
	function setHeight($value){
		$this->height = $value;
		$this->rows = $value;
	}
	
	function setSize($value){
		
		if($value == "") $value = 2;
		$this->width = ($value * 6);
	}

	function setMaxLength($value){
		$this->maxlength = $value;
	}


	
	
	
	function setRowWidth($value){
		$this->rowwidth = ($value);
	}
	
	function setRows($value){
		
		$this->rows = $value;
		
	}

	////////////////////////////////////////
	// used to control selector option colors
	// ALL keys in the array are assumed to be lowercase
	function setColors($value){
		
		$lowercasekeys = array();
		
		foreach($value as $key=>$val){
			$lowercasekeys[strtolower($key)] = $val;
		}
		
		
		$this->colors = $lowercasekeys;
		
	}
	
	function setTextColors($value){
		
		$lowercasekeys = array();
		
		foreach($value as $key=>$val){
			$lowercasekeys[strtolower($key)] = $val;
		}
		
		$this->textcolors = $lowercasekeys;
		
	}
	
	
	function setAutoComplete($value){
		
		$this->autocomplete = $value;
		
	}



	
	
	function setOptions($value){
		$this->options = $value;
	}

	function setDisabledOptions($value){
		
		if(is_array($value)) $this->disabledoptions = $value;
	}
	



	function setValue($value){
		$this->value = $value;
	}

	function setRequired($value){

		if(!is_numeric($value)) $value = strtolower($value);

		if($value == 'required' || $value == '1' || $value == 1 || $value == 'true'){ 
			$value = true; 
		}else{ 
			$value = false; 
		}

		$this->required = $value;


	}

	function setForward($value){

		if(!is_numeric($value)) $value = strtolower($value);
		if($value == 'forward' || $value == '1' || $value == 'true' || $value == true){ $value = 1; }
		else{ $value = false; }

		$this->forward = $value;
	}

	function setOnClick($value){
		$this->onclick = $value;
	}

	
	function setDisabled($value){
		
		if($value < 1) $value = false;
		if($value > 0) $value = true;
		
		$this->disabled = $value;
		
		if($this->disabled) $this->disabled_class_flag = "_disabled";
		else $this->disabled_class_flag = "";
		
		
	}
	
	function setOnKeyUp($value){
		$this->onkeyup = $value;
	}

	function setOnKeyPress($value){
		$this->onkeypress = $value;
	}
	
	function setOnFocus($value){
		$this->onfocus = $value;
	}

	function setOnChange($value){
		$this->onchange = $value;
	}

	function setOnBlur($value){
		$this->onblur = $value;
	}

	function setClass($value){
		$this->cssclass = $value;
	}

	function setStyle($value){
		$this->style = $value;
	}
	

	function setUpperCaps($value){
		
		if(!is_numeric($value)) $value = strtolower($value);

		if($value == 'yes' || $value == '1' || $value == 1 || $value == 'true'){ 
			$value = true; 
		}else{ 
			$value = false; 
		}
		
		$this->uppercaps = $value;
		
		
	}


	
	
	
	//////////////////////////////
	// options
	// HORIZONTALNOLABELS, HORIZONTAL, VERTICAL
	//
	function setOrientation($value){
		
		if(strtolower($value) == "h") $value = "horizontal";
		if(strtolower($value) == "v") $value = "vertical";
		
		// hack for reverse compatibility
		if($value == "VERTICAL2COLS"){
			$value = "vertical";
			$this->setWidth(2);
		}
		
		$this->orientation = strtolower($value);

		$this->orientation = str_replace(" ","",$this->orientation);
		

		if($this->orientation == "horizontalnolabels"){
			$this->showlabels = false;
			$this->orientation = "horizontal";
		}

		if($this->orientation == "verticalnolabels"){
			$this->showlabels = false;
			$this->orientation = "vertical";

		}

	}

	function setVisible($value){
		
		if(strtolower($value) == "none") $value = false;
		if(strtolower($value) == "visible") $value = true;		
		
		$this->visible = $value;
	}
	

	function setHint($value){
		
		$this->guidelines = $value;
		
	}
	
	function setID($id){
		
		$this->id = $id;
		
	}
	
	function setGuidelines($value){
		$this->setHint($value);
	}
	
	
	function SetElementID($value){
		
		$this->elementid = $value;
		
	}
	
	
	/**
	* spits out the form element directly
	* 
	*/
	function display($data = null){

		echo $this->generate($data);

	}


	/**
	* legacy call to generate()
	*
	*/
	public function gethtml($data = null){

		return $this->generate($data);


	}
	
	
	function cleanText($text) {
		
		$text = htmlentities($text, ENT_COMPAT, "UTF-8");
		$text = preg_replace('/&([a-zA-Z])(uml&#166;acute&#166;grave&#166;circ&#166;tilde&#166;cedil&#166;ring);/', '$1', $text); // remove accents
		$text = html_entity_decode($text);
		$text = strip_tags($text);
		$text = preg_replace('#[\n\r]#is', ' ', $text); // new line to space
		$text = preg_replace('#\b&[a-z]+;\b#', ' ', $text); // remove html entities

		// filter out strange characters like ^, $, &, change "it's" to "its"
		//$chars_match = array('^', '$', '&', '(', ')', '<', '>', '`', '\'', '"', '&#166;', ',', '?', '%', '~', '+', 'www.', 'http://', '[', ']', '{', '}', ':', '\\', '/', '=', '#', '\'', ';', '!', '*', '.', '');
		//$chars_replace = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', '', '', ' ', ' ', ' ', ' ', '', ' ', ' ', '', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ' , ' ', ' ', ' ', ' ', ' ', ' ', '', '');
		//for ($i = 0; $i < count($chars_match); $i++) {
		//	$text = str_replace($chars_match[$i], $chars_replace[$i], $text);
		//}

		return $text;
	}	 

	/**
	* legacy call to generate()
	*
	*/
	public function GenerateElement($data = null){
		
		return $this->generate($data );
		
		
	}
	
	
	/**
	*
	*
	*/
	public function generate($data = null){
		
		global $_POST, $formrecordrow;
		
		if(isset($data) && is_array($data)){
			
			$this->init();
			
			// setup everything here..
			foreach($data as $key => $val){
				
				$this->$key = $val;
				
			}
			
		}
		
		if(isset($this->width)) $this->width = str_replace("px","",$this->width);
		if(isset($this->height)) $this->height = str_replace("px","",$this->height);
				
				
		
		
		
		///////////////////////
		// this is an override from the form report manager
		global $SHOWALLBCFORMELEMENTS;
		if($SHOWALLBCFORMELEMENTS == true) $this->visible = true;
		//
		///////////////////////
		
		if(is_object($this->CI) && $this->CI->lang == "fr") $this->semicolon = " :";		

		if($this->onclick == "onfocus") $this->onclick ='';
		if($this->onclick == "onchange") $this->onclick='';

		$this->label = addslashes($this->label);
		$this->label = str_replace("~","",$this->label);


		$this->name = str_replace(" ","_",$this->name);
		$this->name = str_replace("/","_",$this->name);
		if($this->name == "") return "Missing Field Name";
		
		
		if(!$this->required) $this->required_marker = "";
		
		
		if($this->guidelines != ""){
			
			$this->guidelines = $this->cleanText($this->guidelines);
			
			$guidelines = "<div style=\"background: url('".ASSETURL ."img/info.gif'); width:16px; height:16px;\" onmouseover=\"Tip('".$this->guidelines."'";

			if($this->label != "") $tiptitle = $this->label;
			else $tiptitle = $this->name;
			
			$tiptitle = $this->cleanText($tiptitle);
			
			
			if( strlen($tiptitle) > 20) $tiptitle = substr($tiptitle,0,20) . "...";
			
			$guidelines .= ", TITLE, '".$tiptitle."'";
			
			if($this->hintcolors[0] == "") $this->hintcolors[0] = "#FFFFFF";
			if($this->hintcolors[1] == "") $this->hintcolors[1] = "#FFFF00";
			if($this->hintcolors[2] == "") $this->hintcolors[2] = "#FFFFEE";
			if($this->hintcolors[3] == "") $this->hintcolors[3] = "#222222";
			
			
			$guidelines .= ", TITLEFONTCOLOR, '".$this->hintcolors[0]."'";
			$guidelines .= ", TITLEBGCOLOR, '".$this->hintcolors[1]."'"; 		
			$guidelines .= ", BGCOLOR, '".$this->hintcolors[2]."'";
			$guidelines .= ", BORDERCOLOR, '".$this->hintcolors[3]."'";			
			$guidelines .= ", DELAY, 1200";			
			$guidelines .= ", PADDING, 9";
			$guidelines .= ", WIDTH, 150";
			

			
			$guidelines .= ");\"></div>";
			
			$this->guidelines = $guidelines;
			
		}
		
		
		// get global presets and load them		
		if($this->value == ""){
			
			global ${$this->name};
			if(${$this->name} != "") $this->value = ${$this->name} ;
			
		}
		
		
		if($this->required) $this->opfvalid = "false"; 
		
		

		switch($this->type){

		case "select":
		case "dropdown":
							

			$output = $this->Form_Select();
			break;
			


		case "text":
			$output = $this->Form_Text();
			break;

		case "date":
			$output = $this->Form_Date();
			break;


		case "province":
			
			
			if(strtolower($this->misc) == "canada"){
				include(APPPATH."libraries/iso_canada.php");
				$this->options = $iso_provinces;
			}else if(strtolower($this->misc) == "canada-us"){
				include(APPPATH."libraries/iso_provinces.php");
				$this->options = $iso_provinces;
			}else if(strtolower($this->misc) == "international"){
				include(APPPATH."libraries/iso_countries.php");
				$this->options = $iso_countries;
			}
			
			
			
			$output = $this->Form_Select();
			break;
			
		case "email":
			$output = $this->Form_Email();
			break;


		case "numeric":
			$output = $this->Form_Numeric();
			break;


		case "radio":
			$output = $this->Form_RadioBox();
			break;


		case "checkbox":
			$output = $this->Form_CheckBox();
			break;

		case "check":
			
			
			$output = $this->Form_CheckBox();
			break;


		case "multiselect":
			
			if(!is_array($this->value)) $this->value = explode("~",$this->value . "~");
			$output = $this->Form_SelectCombo();
			break;
			
			
		case "multicheck":
		case "multicheckbox":
		
			if(!is_array($this->value) && preg_match("/~/",$this->value)) $this->value = explode("~",$this->value);
			
			$output = $this->Form_CheckBoxMultiple();
			break;
			

		case "hidden":
			$output = $this->Form_Hidden();
			break;

		case "captcha":
			
			$this->required = true;
			$output = $this->Form_Captcha();
			break;

		case "password":
			$output = $this->Form_Password();
			break;

		case "phone":
			$output = $this->Form_Phone();
			break;

		case "textarea":
			$output = $this->Form_TextArea();
			break;

		case "submit":
			$output = $this->Form_Submit();
			break;

		case "reset":
			$output = $this->Form_Reset();
			break;

		case "button":
			$output = $this->Form_Button();
			break;

		case "sectionbreak":
			$output = $this->Form_SectionBreak();
			break;
			
		}

		if(isset($output)) return $output;

	}



	function Form_Button() {

		
		if($pf->FVARS['print'] == 1) return "";
		
		
		
		if($this->label == "") $this->label = $this->name;
		
		$output = "<div id=\"" . $this->name. "_div\">";
		

		$output .= "<input type='button'";
		$output .= " value='" . $this->label . "'";

		if($this->cssclass != "") $output .= " class=\"$this->cssclass\"";
		if($this->style != "") $output .= " style=\"$this->style\"";
		if($this->onclick!= "") $output .= " onclick=\"$this->onclick\"";

		$output .= ">\n";
		
		$output .= "</div>";
		

		return $output;


	}


	function Form_Reset() {

		
		if($pf->FVARS['print'] == 1) return "";
		

		if($this->label == "") $this->label = $this->name; 
		

		$output = "<input type='reset'";
		$output .= " name='$this->name'";
		
		if($this->id != "") $output .= " id='$this->id'";
		else $output .= " id='$this->name'";
		
		$output .= " value='$this->label'";


		if($this->cssclass != "") $output .= " class=\"$this->cssclass\"";
		
		if($this->style != ""){
			
			$output .= " style=\"$this->style\"";
			
		}else if($this->gradient_begin != "" || $this->gradient_end != ""){

			$output .= " style=\"";
			if($this->gradient_begin != "") $output .= "background-color:".$this->gradient_begin.";";
			if($this->gradient_end != "") $output .= "color:".$this->gradient_end."; border-bottom-color:".$this->gradient_end.";";
			$output .= "\"";
			
		}
		

		$output .= ">";


		return $output;
	}


	function Form_SectionBreak() {


		$output .= "<div class=\"ps_form_sectionbreak\"";
		
		if($this->gradient_begin != "" || $this->gradient_end != ""){

			$output .= " style=\"";
			
			if($this->gradient_begin != "") $output .= "background-color:".$this->gradient_begin.";";
			if($this->gradient_end != "") $output .= "color:".$this->gradient_end."; border-bottom-color:".$this->gradient_end.";";
			if($this->width > 50) $output .= "width:".$this->width."px;";
			if($this->height > 10) $output .= "height:".$this->height."px;";
			
			$output .= "\"";
			
		}
		$output .= ">\n";

		if($this->label != "") $output .= stripslashes($this->label);
		
		$output .= "</div>";

		return $output;
	}

	
	
	function Form_Submit() {
		
		
		if($pf->FVARS['print'] == 1) return "";
		

		if($this->label == "") $this->label = $this->name; 
		

		$output = "<input type='submit'";
		$output .= " name='$this->name'";
		
		if($this->id != "") $output .= " id='$this->id'";
		else $output .= " id='$this->name'";
		
		$output .= " value='$this->label'";


		if($this->cssclass != "") $output .= " class=\"$this->cssclass\"";
		
		if($this->style != ""){
			
			$output .= " style=\"$this->style\"";
			
		}else if($this->gradient_begin != "" || $this->gradient_end != ""){

			$output .= " style=\"";
			if($this->gradient_begin != "") $output .= "background-color:".$this->gradient_begin.";";
			if($this->gradient_end != "") $output .= "color:".$this->gradient_end."; border-bottom-color:".$this->gradient_end.";";
			$output .= "\"";
			
		}
		
		if($this->onclick!= "") $output .= " onclick=\"$this->onclick\"";
		if($this->onfocus != "") $output .= " onFocus=\"$this->onfocus\"";
		if($this->onblur != "") $output .= " onBlur=\"$this->onblur\"";
		if($this->onchange != "") $output .= " onchange=\"$this->onchange\"";
		$output .= ">";


		return $output;


	}////////////////////////



	/////////////////////////////////////
	//
	//
	//
	function Form_Password() {

		
		if($this->width < 25) $this->width = 25;
		
		$output = "<div ";
		
		if($this->visible == false) $output .= "style=\"display:none;\"";
		
		if($this->label != "") $output .= " opflabel=\"" . $this->label . "\" ";
		else $output .= " opflabel=\"" . $this->name . "\" ";
		
		$output .= " opfrequired=\"" . $this->required . "\" ";
		
		$output .= " opftype=\"password\" ";
		
		$output .= " opfvalid=\"".$this->opfvalid."\" ";
		
		$output .= " opfignorvalid=\"false\" ";
		
		$output .= " class=\"formelement" . $this->disabled_class_flag . "\" ";
		
		$output .= " opfldname=\"".$this->name."\" ";
		
		//$output .= " onclick=\"ps_form.divhighlight('".$this->name."')\" ";
		
		$output .= " id=\"" . $this->name. "_div\"";
		
		$output .= " >\n";
		
		$labelinfo = "";
		
		if($this->showlabels && $this->label != ""){
			
			if($this->orientation == "horizontal"){
				
				if($this->required_marker != "") $labelinfo .= "<td valign=\"top\">" . $this->required_marker . "</td>";
				$labelinfo .= "<td valign=\"top\"";
				
				if(is_numeric($this->labelwidth)){
					$labelinfo .= "width=".$this->labelwidth;
					
				}				
				$labelinfo .= " style=\"text-align:right;\">";
				
				$labelinfo .= "<label class=\"qfield password label ".$this->orientation."\" for=\"" . $this->name . "\">";
				
				$labelinfo .= str_replace(" ","&nbsp;",$this->label);
				if( substr(  $this->label, strlen($this->label)-1, 1) != "?") $labelinfo .= $this->semicolon;
				
				$labelinfo .= "</label>";
				
				$labelinfo .= "&nbsp;</td>\n";
				
			}else{
				
				$output .= "<div style=\"width:".$this->width."px;display:block; text-align:left;\">";
				
				$labelinfo .= "<label class=\"qfield password label ".$this->orientation."\" for=\"" . $this->name . "\">";
				
				$output .= $this->required_marker;
				$output .= str_replace(" ","&nbsp;",$this->label);
				
				$labelinfo .= "</label>";
				
				$output .= "</div>\n";
			}
			
		}
		
		
		$output .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr>".$labelinfo."<td valign=\"top\"><input type='password'";
		
		
		$output .= " name='$this->name'";
		
		if($this->id != "") $output .= " id='$this->id'";
		else $output .= " id='$this->name'";
		
		
		if($this->tabindex != '') $output .= " tabindex='$this->tabindex'";
		
		
		$output .= " value=\"".str_replace("\"","'",$this->value)."\"";

		
		if($this->style == "") $output .= " style=\"width:".$this->width."px;\"";
		else $output .= " style=\"$this->style\"";
		

		if($this->cssclass != "") $output .= " class=\"$this->cssclass\"";

		$output .= " onclick=\"this.style.background='#FFFFFF';";
		
		if($this->onclick!= "") $output .= " " . $this->onclick;
		
		$output .= "\" ";
		
		
		
		if($this->onfocus != "") $output .= " onFocus=\"$this->onfocus\"";
		
		
		if($this->onblur != "") $output .= " onBlur=\"$this->onblur\"";
		if($this->onchange != "") $output .= " onchange=\"$this->onchange\"";
		if($this->onkeyup != "") $output .= " onKeyUp=\"$this->onkeyup\"";
		
		$output .= " data-required=\"{$this->required}\" ";
		
		$output .= "></td>";
		
		//}
		
		
		if($this->xtras != "" || $this->guidelines != ""){

			if($this->xtras != ""){
				$output .= "<td>";
				$output .= $this->xtras;
				$output .= "</td>";
			}

			if($this->guidelines != ""){
				$output .= "<td>";
				$output .= $this->guidelines;
				$output .= "</td>";
			}
			

		}

		$output .= "</tr></table>\n";
		
		
		$output .= "</div>\n";
		
		return $output;


	}////////////////////////////////
	
	
	////////////////////////////////////
	//
	//
	//
	function Form_Captcha() {

		
		if($pf->FVARS['print'] == 1) return "";
		
		
		$output = "<div ";
		
		if($this->visible == false) $output .= "style=\"display:none;\"";
		
		if($this->label != "") $output .= " opflabel=\"" . $this->label . "\" ";
		else $output .= " opflabel=\"" . $this->name . "\" ";
		
		$output .= " opfrequired=\"" . $this->required . "\" ";
		
		$output .= " opftype=\"captcha\" ";
		
		$output .= " opfvalid=\"".$this->opfvalid."\" ";
		
		$output .= " opfignorvalid=\"false\" ";
		
		$output .= " class=\"formelement" . $this->disabled_class_flag . "\" ";
		
		$output .= " opfldname=\"".$this->name."\" ";
		
		//$output .= " onclick=\"ps_form.divhighlight('".$this->name."')\" ";
		
		$output .= " id=\"" . $this->name. "_div\"";
		
		$output .= " >\n";

		
		
		
		$output .= "<img id=\"".$this->name."_captcha_img\" src=\"/admin.captcha/?id=";

		if($this->elementid != "") $output .= $this->elementid;
		else if($this->name != "") $output .= $this->name;
		
		
		$output .= "\">";

		$output .= "<br>";
		
		
		
		if($this->showlabels && $this->label != ""){
			$output .= "<div style=\"clear:both; display:block; font-weight:bold\">".$this->label."</div>\n";
		}
		
		$output .= "<input type=\"text\" name=\"".$this->name."\" ";
		
		if($this->id != "") $output .= " id='$this->id'";
		else $output .= " id='$this->name'";
		
		$output .= " style=\"width:".$this->width."px;\" value=\"\">";
		
		$output .= "</div>";
		
		return $output;


	}////////////////////////////////

	////////////////////////////////////
	//
	//
	//
	function Form_CheckBox() {

		
		if(is_array($this->options)){
			$option = implode(",",$this->options);
		}else{
			$option = $this->options;
		}

		$output = "";
		
		
		
		if($this->usewrapper){
			
			$output = "<div ";
			
			if((!$this->showlabels || $this->label == "") && $this->width == 0){
				$this->width = 20;
				
				$output .= " style=\"width:".$this->width."px;";
				
				if($this->visible == false) $output .= "display:none;";
				
				$output .= "\" ";
				
			}else if($this->visible == false) $output .= "style=\"display:none;\"";
			
			
			if($this->label != "") $output .= " opflabel=\"" . $this->label . "\" ";
			else $output .= " opflabel=\"" . $this->name . "\" ";
			
			
			$output .= " opfrequired=\"" . $this->required . "\" ";
			$output .= " opfvalid=\"".$this->opfvalid."\" ";
			
			$output .= " opfignorvalid=\"false\" ";
			
			$output .= " opftype=\"checkbox\" ";
			
			$output .= " class=\"formelement" . $this->disabled_class_flag . "\" ";
			
			$output .= " opfldname=\"".$this->name."\" ";
			
			//$output .= " onclick=\"ps_form.divhighlight('".$this->name."')\" ";
			
			$output .= " id=\"" . $this->name. "_div\"";
			
			
			
			$output .= " >\n";
			

			if($this->guidelines != ""){
				$output .= "<div style=\"float:left;margin-top:2px;\">";
				$output .= $this->guidelines;
				$output .= "</div>\n";
			}
			
		}
		
		
		$output .= "<input type='checkbox'";
		$output .= " name='{$this->name}'";
		
		if($this->id != "") $output .= " id='$this->id'";
		else $output .= " id='$this->name'";
		
		$output .= " value='$option'";
		
		if($option != "" && $option == $this->value) $output .= " checked";

		if($this->cssclass != "") $output .= " class=\"$this->cssclass\"";
		if($this->style != "") $output .= " style=\"$this->style\"";
		
		if($this->tabindex != '') $output .= " tabindex='$this->tabindex'";
		
		
		$output .= " onclick=\"this.style.background='#FFFFFF';";
		
		if($this->onclick!= "") $output .= " " . $this->onclick;
		
		$output .= "\" ";
		
		
		if($this->onfocus != "") $output .= " onFocus=\"$this->onfocus\"";
		if($this->onblur != "") $output .= " onBlur=\"$this->onblur\"";
		if($this->onchange != "") $output .= " onchange=\"$this->onchange\"";

		if($this->disabled) $output .= " DISABLED ";

		if($this->required) $output .= " data-bvalidator=\"required,required\" ";
		
		
		$output .= ">";
		
		if($this->usewrapper){
			
			if($this->showlabels && $this->label != ""){
				$output .= "&nbsp;<label for=\"{$this->name}\" class=\"qfield checkbox label ".$this->orientation."\" style=\"display:inline;\">";
				$output .= $this->required_marker;
				$output .= str_replace(" ","&nbsp;",$this->label)."</label>\n";
			}


			

			$output .= "</div>";
			
		}
		
		

		return $output;

	}////////////////////////
	
	
	




	////////////////////////////////////////////
	//
	//
	//
	function Form_CheckBoxMultiple($usetype = 'checkbox') {

		
		$output = "";
		
		if($this->width < 1) $this->width = 1;
		
		if($this->gradient_begin != "" && $this->gradient_end != "" && $this->width > 2){
			
			require_once(APPPATH."libraries/gradient.php");
			$gradient = new gradient($this->gradient_begin,$this->gradient_end,$this->width);
			$cellcolors = $gradient->createArray();
			

		}
		
		$divwidth = "100%";
		
		if(isset($this->rowwidth) && $this->rowwidth < 1) $divwidth = "100%";
		else if(isset($this->rowwidth)) $divwidth = $this->rowwidth . "px";
		
		
		if($this->usewrapper){
			

			$output = "<div ";
			
			if($this->visible == false) $output .= "style=\"display:none;\"";
			
			if($this->label != "") $output .= " opflabel=\"" . $this->label . "\" ";
			else $output .= " opflabel=\"" . $this->name . "\" ";
			
			$output .= " opfrequired=\"" . $this->required . "\" ";
			
			$output .= " opftype=\"multicheckbox\" ";
			
			$output .= " opfvalid=\"".$this->opfvalid."\" ";
			
			$output .= " opfignorvalid=\"false\" ";
			
			$output .= " style=\"width:$divwidth\" ";
			
			$output .= " class=\"formelement" . $this->disabled_class_flag . "\" ";
			
			$output .= " opfldname=\"".$this->name."\" ";
			
			//$output .= " onclick=\"ps_form.divhighlight('".$this->name."')\" ";
			
			$output .= " id=\"" . $this->name. "_div\"";
			
			$output .= " >\n";
			

			
			

			if($this->showlabels && $this->label != ""){
				
				if($this->orientation == "horizontal"){
					
					$output .= "<div style=\"display:inline;text-align:right;\">";
					
					$output .= "<label class=\"qfield checkboxmultiple label ".$this->orientation."\" for=\"" . $this->name . "\">";
					
					$output .= $this->required_marker;
					$output .= stripslashes($this->label);
					if( substr(  $this->label, strlen($this->label)-1, 1) != "?") $output .= $this->semicolon;
					$output .= "&nbsp;";
					
					$output .= "</label>";
					
					$output .= "</div>\n";
					
				}else{
				
					$output .= "<div class=\"qfield radio label\" style=\"display:block;text-align:left;\">";

					$output .= "<label class=\"qfield checkboxmultiple label ".$this->orientation."\" for=\"" . $this->name . "\">";
					
					$output .= $this->required_marker;
					$output .= stripslashes($this->label);
					
					$output .= "</label>";
					
					$output .= "</div>\n";
				}
				
				$this->required_marker = "";
				
			}

			
		}
		

		

		if($this->guidelines != ""){
			$output .= "<div style=\"float:right;margin-top:10px;\">";
			$output .= $this->guidelines;
			$output .= "</div>\n";
		}
		

		if($this->orientation == "horizontal"){
			$output .= "<div style=\"padding:1px; display:inline;\">";
			if($this->showlabels) $output .= $this->required_marker;
		}else{
			$output .= "<div style=\"padding:1px;\">";
			if($this->showlabels) $output .= $this->required_marker;
		}
		
		
		if($this->width > 0 && $this->orientation == "horizontal"){
			
			
			if($this->required_marker != ""){
				
				$output .= "<div style=\"float:left\">" . $this->required_marker."</div><div style=\"float:left\">\n";
				
			}
			
			
			if(!isset($this->rowwidth)) $this->rowwidth = 0;
			
			$output .= "\n<table class=\"qfield radio table ".$this->orientation."\" ";
			if($this->rowwidth > 0) $output .= " style=\"width:".$this->rowwidth."px;\" ";
			$output .= " border=0 cellpadding=3 cellspacing=0>\n";
			
		}else{
			$output .= "\n<table class=\"qfield radio table ".$this->orientation."\" border=0 cellpadding=3 cellspacing=0>\n";
		}

		if($this->options != "" && !is_array($this->options)){
			$this->options .= ",";
			$temparrelements =  explode(",",$this->options);
			$this->options = array();
			foreach($temparrelements as $arrelement){
				if($arrelement != "") $this->options[$arrelement] = $arrelement;
			}
		}


		if(isset($_POST[$this->name])){
			$this->value = $_POST[$this->name];
		}

		
		

		
		

		if(is_array($this->options)){
			
			$checkindex = 0;

			foreach($this->options as $key => $val){
				
				$checkindex ++;
				
				
				if($this->width > 0 && $this->orientation == "horizontal"){

					if($checkindex == 1) $output .= "<tr>\n";
					
					if(isset($cellcolors)){
						$output .= "<td style=\"background-color:".$cellcolors[($checkindex - 1)]."\" nowrap>";
					}else{
						$output .= "<td nowrap>";
					}
					
				}else{
					$output .= "<tr>\n<td valign=\"center\" nowrap>";
				}
				
				$output .= "<input id='" . $this->name . "_$checkindex' type='$usetype' name='" . $this->name;

				if($usetype != "radio"){
					$output .= "[{$checkindex}]";
				}
				$output .= "' value='$val'";

				
				if(is_array($this->value)){
					if(in_array($val,$this->value)) $output .= " checked ";
				}else if($this->value == $val){
					$output .= " checked ";
				}else if($this->value == "CHECKALL"){
					$output .= " checked ";
				}
				

				if($this->cssclass != "") $output .= " class=\"$this->cssclass\"";	
				if($this->style != "") $output .= " style=\"$this->style\"";
				if($this->onclick!= "") $output .= " onclick=\"$this->onclick\"";
				if($this->onfocus != "") $output .= " onFocus=\"$this->onfocus\"";
				if($this->onblur != "") $output .= " onBlur=\"$this->onblur\"";
				if($this->onchange != "") $output .= " onchange=\"$this->onchange\"";

				$disabled_checkfield_flag = "";
				
				if($this->disabled || in_array($val,$this->disabledoptions)){
					$output .= " DISABLED ";
					$disabled_checkfield_flag = "_disabled";
					
				}
				
				
				
				if($usetype == "radio" && $this->required && $checkindex == count($this->options)){
								
					$output .= " data-bvalidator=\"required\" data-bvalidator-msg=\"Select one radio button\" ";
				}
				

				$output .= "></td>\n<td valign=\"center\" nowrap><label for=\"{$this->name}_{$checkindex}\" id=\"{$this->name}_{$checkindex}_label\" class=\"ps_fieldlabel" . $disabled_checkfield_flag . "\">\n";
				
				if(is_numeric($key)){ 
					$output .= $val; 
				}else{
					$output .= str_replace("FOK_","",$key);
				}
				
				
				if($this->width > 0 && $this->orientation == "horizontal"){

					
					$output .=  "</label></td>\n"; 
					
					if(($checkindex % $this->width) == 0){
						
						if($checkindex < count($this->options)){

							$output .= "</tr>\n<tr>\n";
						}
					}



				}else{ 

					$output .= "</label></td></tr>"; 
					
				}

			}
		}

		if($this->width > 0 && $this->orientation == "horizontal"){

			$output .= "</tr>\n</table>\n";
			
			if($this->required_marker != ""){
				
				$output .= "</div>\n";
				
			}
			
			
		}else{
			
			$output .= "</table>\n";
			
		}
		
		$output .= "</div>\n";
		
		if($this->usewrapper){
			
			
			$output .= "</div>\n";
			
		}
		

		return $output;


	}////////////////////////


	//////////////////////////////////
	//
	function Form_Radio() {
		
		return $this->Form_CheckBoxMultiple('radio');
		
	}////////////////////////
	

	///////////////////////////////////
	//
	//
	//
	function Form_RadioBox(){
		
		return $this->Form_CheckBoxMultiple('radio');
		
	}////////////////////

	



	////////////////////////////////////////////
	//
	//
	//
	function Form_Select() {

		
		$output = "";

		if($this->visible == false) $this->usewrapper == true;
		
		
		if($this->usewrapper){
			
			$output .= "<div ";
			
			if($this->visible == false) $output .= "style=\"display:none;\"";
			
			if($this->label != "") $output .= " opflabel=\"" . $this->label . "\" ";
			else $output .= " opflabel=\"" . $this->name . "\" ";
			
			$output .= " opfrequired=\"" . $this->required . "\" ";
			
			$output .= " opftype=\"select\" ";
			
			$output .= " opfvalid=\"".$this->opfvalid."\" ";
			
			$output .= " class=\"formelement" . $this->disabled_class_flag . "\" ";
			
			$output .= " opfldname=\"".$this->name."\" ";
			
			//$output .= " onclick=\"ps_form.divhighlight('".$this->name."')\" ";
			
			$output .= " id=\"" . $this->name. "_div\"";
			
			$output .= " >\n";
			
		}

		
		
		if($this->showlabels && $this->label != ""){
			
			if($this->orientation == "vertical"){
				

				$output .= "<label class=\"qfield select label ".$this->orientation."\" for=\"" . $this->name . "\"";
				
				$output .= " style=\"display:block;text-align:left;\" ";
				
				$output .= ">";
				
				$output .= $this->required_marker;
				$output .= stripslashes($this->label);
				
				$output .= "</label>";

			}
			
			$this->required_marker = "";
			
		}
		
		
		

		if($this->options != "" && !is_array($this->options)){
			$this->options .= ",";
			$temparrelements =  explode(",",$this->options);
			$this->options = array();
			
			foreach($temparrelements as $arrelement){
				if($arrelement != "") $this->options[$arrelement] = $arrelement;
			}
		}

		

		
		if($this->orientation == "horizontal" && $this->showlabels){
			

			
			$output .= "<table>\n<tr>\n";
			
			$output .= "<td nowrap class=\"qfield select label ".$this->orientation."\" valign=\"top\">";
			
			$output .= "<label class=\"qfield select label ".$this->orientation."\" for=\"" . $this->name . "\">";
			
			$output .= $this->required_marker;
			$output .= stripslashes($this->label);
			
			if( substr(  $this->label, strlen($this->label)-1, 1) != "?"){
				$output .= $this->semicolon;
			}
			
			$output .= "&nbsp;";
			
			$output .= "</label>";
			
			$output .= "</td>\n";

			$output .= "<td valign=\"top\">";
			
			
		}
		
		
		$output .= "<select ";
		$output .= " name=\"$this->name";
		if($this->isarray) $output .= "[]";
		$output .= "\"";
		
		if($this->id != "") $output .= " id='$this->id'";
		else $output .= " id='$this->name'";
		


		if($this->cssclass != "") $output .= " class=\"$this->cssclass\"";
		
		if($this->style != ""){ 
			$output .= " style=\"$this->style\"";
		}else if($this->width > 50){
			$output .= " style=\"width:".$this->width."px;\"";
		}
		
		if($this->height != "") $output .= " size=\"$this->height\"";		
		
		
		if($this->onfocus != "") $output .= " onFocus=\"$this->onfocus\"";
		if($this->onblur != "") $output .= " onBlur=\"$this->onblur\"";


		if($this->tabindex != '') $output .= " tabindex='$this->tabindex'";
		

		$output .= " onchange=\"this.style.background='#FFFFFF'; ";
		
		if($this->onchange != ""){
			$output .= $this->onchange;
		}else if($this->onclick != ""){
			$output .= $this->onclick;
		}
		
		$output .= "\" ";
		
		$output .= " data-required=\"{$this->required}\" ";

		
		$output .= ">\n";

		if($this->options != "" && !is_array($this->options)){
			$foptions .= ",";
			$temparrelements =  explode(",",$this->options);
			$this->options = array();
			foreach($temparrelements as $arrelement){
				if($arrelement != "") $this->options[$arrelement] = $arrelement;
			}
		}



		Console::log($this->options);
		

		if(is_array($this->options)){
			
			// for colors array
			$ci = 0;
			
			foreach($this->options as $key => $val){

				if(strtoupper($val) == "SELECT"){
					$key = $val;
					$val = "";
				}
				
				
				$output .= "<OPTION ";
				
				if( is_array($this->colors) || is_array($this->textcolors) ){	

					$output .= " style=\"";
							
					
					if( is_array($this->colors) && isset($this->colors[strtolower($key)])){
					
						$output .= "background-color:".$this->colors[strtolower($key)].";";
					
					}
					if( is_array($this->textcolors) && isset($this->textcolors[strtolower($key)])){
						$output .= "color:".$this->textcolors[strtolower($key)].";";
					}
					
					$output .= "\"";
					
				}

				$output .= " value='$val' ";

				if($val == $this->value) $output .= " selected=\"selected\" ";

				
				$output .= ">";

				if(is_numeric($key)) $output .= $val;
				else $output .=  str_replace("FOK_","",$key);

				$output .= "</option>\n";
				
				$ci++;
				
			}
			
			
			
		}


		$output .= "</select>";
		
		if($this->orientation == "horizontal" && $this->showlabels){
			$output .= "</td>";
			if($this->guidelines != "") $output .= "<td>".$this->guidelines . "</td>";
			$output .= "</tr></table>";
		}
		
		

		if($this->usewrapper){
			
			$output .= "</div>";
			
		}

		
		
		return $output;


	}
	////////////////////////




	////////////////////////////////////////////
	//
	//
	function Form_SelectCombo() {

		
		if($this->rows == "") $this->rows = 2;

		if($this->onclick == "onchange") $this->onclick='';

		$this->label = addslashes($this->label);
		$this->label = str_replace("~","",$this->label);



		if($this->options != "" && !is_array($this->options)){

			$this->options =  explode(",",$this->options .",");
			array_pop($this->options);
		}


		if($this->value != "" && !is_array($this->value)){

			$this->value = explode("~",$this->value."~");
			array_pop($this->value);

		}

		$output = "<div ";
		
		if($this->visible == false) $output .= "style=\"display:none;\"";
		
		if($this->label != "") $output .= " opflabel=\"" . $this->label . "\" ";
		else $output .= " opflabel=\"" . $this->name . "\" ";
		
		$output .= " opfrequired=\"" . $this->required . "\" ";
		
		$output .= " opftype=\"multiselect\" ";
		
		$output .= " opfvalid=\"".$this->opfvalid."\" ";
		
		$output .= " opfignorvalid=\"false\" ";
		
		$output .= " class=\"formelement" . $this->disabled_class_flag . "\" ";
		
		$output .= " opfldname=\"".$this->name."\" ";
		
		//$output .= " onclick=\"ps_form.divhighlight('".$this->name."')\" ";
		
		$output .= " id=\"" . $this->name. "_div\"";
		
		$output .= " >\n";

		if($this->showlabels && $this->label != ""){
			
			if($this->orientation == "horizontal"){
				$output .= "<div style=\"display:block;float:left;margin:0pt 0pt 5px;padding:3px 5px;text-align:right;\">";
				
				$output .= "<label class=\"qfield selectcombo label ".$this->orientation."\" for=\"" . $this->name . "\">";
				
				$output .= $this->required_marker;
				
				$output .= stripslashes($this->label);
				if( substr(  $this->label, strlen($this->label)-1, 1) != "?") $output .= $this->semicolon;
				
				$output .= "</label>";
				
				$output .= "&nbsp;</div>\n";
			}else{
				$output .= "<div class=\"qfield label\" style=\"clear:left; display:block; text-algn:left;\">";
				
				$output .= "<label class=\"qfield selectcombo label ".$this->orientation."\" for=\"" . $this->name . "\">";
				
				$output .= $this->required_marker;
				
				
				$output .= stripslashes($this->label);
				
				$output .= "</label>";
				
				$output .= "</div>\n";
				
			}
		}

		
		$output .= "<select ";
		$output .= " name=\"$this->name"."[]\"";
		
		if($this->id != "") $output .= " id='$this->id'";
		else $output .= " id='$this->name'";
		
		$output .= " MULTIPLE SIZE=\"$this->rows\"";
		

		if($this->tabindex != '') $output .= " tabindex='$this->tabindex'";
		
		$output .= " onchange=\"this.style.background='#FFFFFF'; ";
		
		if($this->onchange != ""){
			$output .= $this->onchange;
		}else if($this->onclick != ""){
			$output .= $this->onclick;
		}
		
		$output .= "\" ";
		
		
		if($this->cssclass != "") $output .= " class=\"$this->cssclass\"";
		
		if($this->style != ""){ 
			$output .= " style=\"$this->style\"";
		}else if($this->width > 50){
			$output .= " style=\"width:".$this->width."px;\"";
		}
		
		if($this->onfocus != "") $output .= " onFocus=\"$this->onfocus\"";
		if($this->onblur != "") $output .= " onBlur=\"$this->onblur\"";
		if($this->onchange != "") $output .= " onchange=\"$this->onchange\"";

		$output .= " data-required=\"{$this->required}\" ";
		
		$output .= ">\n";

		if(is_array($this->options)){

			foreach($this->options as $key => $val){

				$output .= "<OPTION ";
				
				if( is_array($this->colors) || is_array($this->textcolors) ){	

					$output .= " style=\"";
					
					if( is_array($this->colors) && isset($this->colors[$key])){
						$output .= " background-color:".$this->colors[$key]."; ";
					}
					if( is_array($this->textcolors) && isset($this->textcolors[$key])){
						$output .= " color:".$this->textcolors[$key]."; ";
					}
					
					$output .= "\"";
					
				}
				

				if(is_array($this->value) && in_array($val,$this->value)) $output .= " selected=\"selected\" ";

				$output .= "value='$val'>";

				if(is_numeric($key)) $output .= $val;
				else $output .=  str_replace("FOK_","",$key);

				$output .= "</option>\n";
			}
		}

		$output .= "</select>";

		$output .= "</div>";
		
		
		return $output;


	}////////////////////////

	
	
	
	
	
	
	
	
	////////////////////////////////////////////
	//
	//
	//
	function Form_Email() {

		return $this->Form_Text('email');
		

	}////////////////////////
	
	
	
	
	
	////////////////////////////////////////////
	//
	//
	//
	function Form_Date() {


		$output = "";
		
		if($this->misc == "timeonly") $this->misc = 3;
		
		if($this->misc == 3) $icon = "clock";
		else $icon = "calender";
		
		
		if($this->misc == 1)  $this->misc = "datetime";

		
		$output .= "<div style=\"display:none;position:absolute;\" id=\"popupcalendar_".$this->name."_div\"></div>";
		
		$output .= "<div ";
		
		if($this->visible == false) $output .= "style=\"display:none;\"";
		
		if($this->label != "") $output .= " opflabel=\"" . $this->label . "\" ";
		else $output .= " opflabel=\"" . $this->name . "\" ";
		
		$output .= " opfrequired=\"" . $this->required . "\" ";
		
		$output .= " opftype=\"date\" ";
		
		$output .= " opfvalid=\"".$this->opfvalid."\" ";
		
		$output .= " opfignorvalid=\"false\" ";
		
		$output .= " class=\"formelement" . $this->disabled_class_flag . "\" ";
		
		$output .= " opfldname=\"".$this->name."\" ";
		
		//$output .= " onclick=\"ps_form.divhighlight('".$this->name."')\" ";
		
		$output .= " id=\"" . $this->name. "_div\"";
		
		$output .= " >\n";
		
		
		$labelinfo = "";
		
		if($this->showlabels && $this->label != ""){
			
			if($this->orientation == "horizontal"){
				
				if($this->required_marker != "") $labelinfo .= "<td valign=\"top\">" . $this->required_marker . "</td>";
				$labelinfo .= "<td valign=\"top\" style=\"text-align:right;\">";
				
				$labelinfo .= "<label class=\"qfield date label ".$this->orientation."\" for=\"" . $this->name . "\">";
				
				$labelinfo .= str_replace(" ","&nbsp;",$this->label);
				if( substr(  $this->label, strlen($this->label)-1, 1) != "?") $labelinfo .= $this->semicolon;
				
				$labelinfo .= "</label>";
				
				$labelinfo .= "&nbsp;</td>\n";
				
			}else{
				
				$output .= "<div style=\"width:".$this->width."px;display:block; text-align:left;\">";
				
				$output .= "<label class=\"qfield date label ".$this->orientation."\" for=\"" . $this->name . "\">";
				
				$output .= $this->required_marker;
				$output .= str_replace(" ","&nbsp;",$this->label);
				
				$output .= "</label>";
				
				$output .= "</div>\n";
			}
			
		}
		
		
		
		$output .= "<table class=\"qfield date table ".$this->orientation."\" border=0 cellpadding=0 cellspacing=0><tr>".$labelinfo."<td valign=\"top\"><input type='text'";
		$output .= " name='$this->name'";
		
		if($this->id != "") $output .= " id='$this->id'";
		else $output .= " id='$this->name'";
		
		
		$output .= " value=\"".str_replace("\"","'",$this->value)."\"";

		
		if($this->style == "") $output .= " style=\"width:".$this->width."px;\"";
		else $output .= " style=\"$this->style\"";
		
		if($this->tabindex != '') $output .= " tabindex='$this->tabindex'";
		
		if($this->cssclass != "") $output .= " class=\"$this->cssclass\"";

		$output .= " onclick=\"this.style.background='#FFFFFF';";
		
		if($this->onclick != "") $output .= " " . $this->onclick;
		
		$output .= "\" ";
		
		
		if($this->onfocus != "") $output .= " onFocus=\"$this->onfocus\"";
		if($this->onblur != "") $output .= " onBlur=\"$this->onblur\"";
		if($this->onchange != "") $output .= " onchange=\"$this->onchange\"";
		if($this->onkeyup != "") $output .= " onKeyUp=\"$this->onkeyup\"";
		if($this->onkeypress != "") $output .= " onKeyPress=\"$this->onkeypress\"";			
		
		
		
		$output .= "></td>";
		
		$output .= "<td>";
		
		//$output .= "<div onclick=\"\$('#$this->name').focus();\" style=\"background: url('".$this->asset_url ."img/$icon.gif') no-repeat right; width:20px; height:16px;\"></div>";

		$output .= "</td>";
		
		if($this->xtras != "" || $this->guidelines != ""){

			if($this->xtras != ""){
				$output .= "<td>";
				$output .= $this->xtras;
				$output .= "</td>";
			}

			if($this->guidelines != ""){
				$output .= "<td>";
				$output .= $this->guidelines;
				$output .= "</td>";
			}
			

		}

		$output .= "</tr></table>\n";
		
		
		$output .= "</div>\n";
		
		return $output;


	}////////////////////////
	
	
	
	
	
	
	
	

	////////////////////////////////////////////
	//
	//
	//
	function Form_Text($ftype='text') {

		
		if($this->width < 25) $this->width = 25;
		
		$output = "";
		
		if($this->usewrapper){
			
			
			$output = "<div ";
			
			if($this->visible == false) $output .= "style=\"display:none;\"";
			
			if($this->label != "") $output .= " opflabel=\"" . $this->label . "\" ";
			else $output .= " opflabel=\"" . $this->name . "\" ";
			
			$output .= " opfrequired=\"" . $this->required . "\" ";
			
			$output .= " opftype=\"".$ftype."\" ";
			
			$output .= " opfvalid=\"".$this->opfvalid."\" ";

			$output .= " opfignorvalid=\"false\" ";
			
			$output .= " class=\"formelement" . $this->disabled_class_flag . "\" ";
			
			$output .= " opfldname=\"".$this->name."\" ";
			
			//$output .= " onclick=\"ps_form.divhighlight('".$this->name."')\" ";
			
			$output .= " id=\"" . $this->name. "_div\"";
			
			$output .= " >\n";
			
		}
		
		
		$labelinfo = "";
		
		if($this->showlabels && $this->label != ""){
		
			
			
			if($this->orientation == "horizontal"){
			
				
				$labelinfo .= "<label class=\"qfield text label ".$this->orientation."\" for=\"" . $this->name . " ";
				
							
				if($this->required_marker != "") $labelinfo .=  $this->required_marker;
				
				if(is_numeric($this->labelwidth)){
					$labelinfo .= "width=".$this->labelwidth;
					
				}
				
				$labelinfo .= " style=\"display:inline; text-align:right;\">";
				

				
				
				$labelinfo .= str_replace(" ","&nbsp;",stripslashes($this->label));
				if( substr(  $this->label, strlen($this->label)-1, 1) != "?") $labelinfo .= $this->semicolon;
				
				
				$labelinfo .= "</label>&nbsp;";
				
				
				//echo 
				
				
			}else{
						
				
				$output .= "<label ";
				
				$output .= " style=\"width:".$this->width."px;display:block; text-align:left;\" ";

				$output .= " class=\"qfield text label ".$this->orientation."\" for=\"" . $this->name . "\">";
				
				$output .= $this->required_marker;
				$output .= str_replace(" ","&nbsp;",stripslashes($this->label));

				$output .= "</label>";
				
		
			}
			
		}
		
		
		
		
		if($this->CI->input->get_post('print') != ""){
			
			if($this->value == ""){
				
				$output .= "<div style=\"height:20px; width:".$this->width."px; padding:3px;border-width:1px;border-style:solid;\">";		
				$output .= "</div>";
				
			}else{
				
				$output .= "<div style=\"height:20px; padding:3px;border-width:1px;border-style:solid;\">";		
				$output .= $this->value;
				$output .= "</div>";
				
			}
			
			
		}else{
			
			$output .= $labelinfo;
			
			$output .= "<input type='text'";
			
			if($this->disabled) $output .= " DISABLED ";
			
			
			$output .= " name=\"$this->name";
			if($this->isarray) $output .= "[]";
			$output .= "\"";
			
			if($this->id != "") $output .= " id='$this->id'";
			else $output .= " id='$this->name'";
			
			
			
			if($this->maxlength != '') $output .= " maxlength='".$this->maxlength."'";
			if($this->tabindex != '') $output .= " tabindex='".$this->tabindex."'";

			
			if($this->autocomplete == false) $output .= " autocomplete='off' ";
			
			
			// this is for cluetips
			if($this->label != "") $output .= " title='".$this->label."'";
			
			$output .= " value=\"".str_replace("\"","'",$this->value)."\"";

			$output .= " style=\"";
			
			$output .= "width:".$this->width."px; ";
			
			if($this->uppercaps != "") $output .= " text-transform: uppercase; ";
			
			if($this->style != "") $output .= " $this->style ";
			
			$output .= "\"";
			

			if($this->cssclass != "") $output .= " class=\"$this->cssclass\"";

			
			if($this->onclick!= "") $output .= " " . $this->onclick;
			
			
			
			
			if($this->onfocus != "") $output .= " onFocus=\"$this->onfocus\"";
			if($this->onblur != "") $output .= " onBlur=\"$this->onblur\"";
			if($this->onchange != "") $output .= " onchange=\"$this->onchange\"";
			if($this->onkeyup != "") $output .= " onKeyUp=\"$this->onkeyup\"";
			if($this->onkeypress != "") $output .= " onKeyPress=\"$this->onkeypress\"";			
			
			if($this->required) $output .= " data-bvalidator=\"required\" ";
			
			
			$output .= ">";
			
		}

		if($this->usewrapper){
			
			
			if($this->xtras != "" || $this->guidelines != ""){

				if($this->xtras != ""){

					$output .= $this->xtras;

				}

				if($this->guidelines != ""){
					$output .= $this->guidelines;
					
				}
				

			}
			
			
			
			$output .= "</div>\n";
			
		}
		
		
		return $output;


	}////////////////////////





	////////////////////////////////////////////

	function Form_Phone() {

		return $this->Form_Text('phone');

	}

	////////////////////////



	//////////////////////////////////
	//
	//
	//
	function Form_Numeric() {
		return $this->Form_Text('numeric'); 
		
	}



	function Form_TextArea() {

		
		if($this->columns == "") $this->columns = $this->size;

		if($this->width < 100) $this->width = 100;
		if($this->height < 10) $this->height = 10;
		
		$output = "<div ";
		
		if($this->visible == false) $output .= "style=\"display:none;\"";
		
		if($this->label != "") $output .= " opflabel=\"" . $this->label . "\" ";
		else $output .= " opflabel=\"" . $this->name . "\" ";
		
		$output .= " opfrequired=\"" . $this->required . "\" ";
		
		$output .= " opftype=\"textarea\" ";
		
		$output .= " opfvalid=\"".$this->opfvalid."\" ";
		
		$output .= " opfignorvalid=\"false\" ";
		
		$output .= " class=\"formelement" . $this->disabled_class_flag . "\" ";
		
		$output .= " opfldname=\"".$this->name."\" ";
		
		//$output .= " onclick=\"ps_form.divhighlight('".$this->name."')\" ";
		
		$output .= " id=\"" . $this->name. "_div\"";
		
		$output .= " >\n";
		

		if($this->showlabels && $this->label != ""){
			
			if($this->orientation == "horizontal"){
				
				$output .= "<div style=\"display:block;float:left;margin:0pt 0pt 5px;padding:3px 5px;text-align:right;\">";
				
				$output .= "<label class=\"qfield textarea label ".$this->orientation."\" for=\"" . $this->name . "\">";
				
				$output .= $this->required_marker;
				$output .= stripslashes($this->label);
				if( substr(  $this->label, strlen($this->label)-1, 1) != "?") $output .= $this->semicolon;
				
				$output .= "</label>";
				
				$output .= "&nbsp;</div>\n";
				
			}else{
				
				$output .= "<div style=\"clear:left; display:block; text-align:left;\">";
				
				$output .= "<label class=\"qfield textarea label ".$this->orientation."\" for=\"" . $this->name . "\">";
				
				$output .= $this->required_marker;
				$output .= stripslashes($this->label);
				
				$output .= "</label>";
				
				$output .= "</div>\n";
				
			}
		}
		
		

		if(isset($_GET['print']) && $_GET['print'] != ""){
			
			if($this->value == ""){
				
				$output .= "<div style=\"height:".$this->height."px; width:".$this->width."px; padding:3px;border-width:1px;border-style:solid;\">";		
				$output .= "</div>";
				
			}else{
				$output .= "<div style=\"padding:3px;border-width:1px;border-style:solid;\">";		
				$output .= $this->value;
				$output .= "</div>";
			}
			
			
		}else{
			

			$output .= "<textarea ";
			$output .= " name='$this->name'";
			
			if($this->id != "") $output .= " id='$this->id'";
			else $output .= " id='$this->name'";
			
			
			
			if($this->maxlength != '') $output .= " onkeypress=\"return ps_form.imposeMaxLength(event, this,".$this->maxlength.",'".$this->name."');\" ";
			if($this->tabindex != '') $output .= " tabindex='".$this->tabindex."'";
			
			
			if($this->disabled) $output .= " DISABLED ";

			
			if($this->style != ""){
				$output .= " style=\"$this->style\"";
			}else{
				$output .= " style=\"width:".$this->width."px; height:".$this->height."px;\"";
			}
			
			if($this->cssclass != "") $output .= " class=\"$this->cssclass\"";
			if($this->onclick!= "") $output .= " onchange=\"$this->onclick\"";
			if($this->onfocus != "") $output .= " onFocus=\"$this->onfocus\"";
			if($this->onblur != "") $output .= " onBlur=\"$this->onblur\"";
			if($this->onchange != "") $output .= " onchange=\"$this->onchange\"";
			if($this->onkeyup != "") $output .= " onKeyUp=\"$this->onkeyup\"";
			
			$output .= " onclick=\"this.style.background='#FFFFFF';";
			
			if($this->onclick!= "") $output .= " " . $this->onclick;
			
			$output .= "\" ";
			
			$output .= " data-required=\"{$this->required}\" ";
			
			$output .= " wrap='virtual'>";
			
			$output .= $this->value;
			$output .= "</textarea>\n";
			
			
			if($this->maxlength != ""){
				
				$output .= "<br><div id=\"" . $this->name. "_maxchars_div\"></div>\n";
				
				
				
			}
			
			
		}
		
		
		
		
		
		if($this->guidelines != ""){

			$output .= "<div style=\"float:left;margin-top:0px;\">";
			$output .= $this->guidelines;
			$output .= "</div>";
		}
		
		
		$output .= "<div style=\"clear:both;\"></div>\n";
		$output .= "</div>\n";
		
		
		
		return $output;


	}



	function Form_Hidden() {
	
		$CI = & get_instance();
				
	
		if($CI->config->item('formreview')){
			
			$output = "<input type='text'";
			$output .= " name='$this->name'";
			
			if($this->id != "") $output .= " id='$this->id'";
			else $output .= " id='$this->name'";
			
			$output .= " value='$this->value'";
			$output .= ">";
			
		}else{
			
			$output = "<input type='hidden'";
			$output .= " name='$this->name'";
			
			if($this->id != "") $output .= " id='$this->id'";
			else $output .= " id='$this->name'";
			
			$output .= " value='$this->value'";
			$output .= ">";
			
		}
		

		return $output;


	}

	////  END FORM SECTION 
	/////////////////////////////////////////////////
	
	
	
	///////////////////////////////
	// remove illegal characters that can screw up sql queries
	//
	function FixFieldName($string){
		
		$string = str_replace(" ","_",$string);
		$string = str_replace("-","_",$string);
		$string = str_replace("<","",$string);
		$string = str_replace(">","",$string);
		$string = str_replace("!","",$string);
		$string = str_replace("?","",$string);	
		
		//$string = ereg_replace("[^a-zA-Z0-9_-]", "",$string);
		
		$string = preg_replace("#[^a-zA-Z0-9_-]#i", "",$string);
		
		// remove accents
		$string= strtr($string,  "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ", "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn"); 
		
		
		return $string;
		
	}///////////////////////
	

} ///// END FormElement




