<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * form elements - Provides generic functions for the generation of reports within client specific modules. 
 *
 * @package		ps_reportutils
 * @author		Galbraith Desmond <galbraithdesmond@gmail.com>
 * @description Provides generic functions for the generation of reports.
 *
 * @version		1.0.5 (2012-01-02)
 */
 
 
 

/*//////////////////////////////////



////////////////////////////////////*/



class ps_reportutils{



	function __construct(){
		
		
	}
	
	
	///////////////////////////////////////////////////
	// this generic function saves the effort of 
	// rety
	//
	function load_ordericons(&$disp,$sortfields,$args,$jsfunc,$additionaljsars=''){
	
		global $pf;
			
		if($args[sort_direction] == "") $args[sort_direction] = "ASC";
		if($args[sort_field] == "") $args[sort_field] = $sortfields[0];
		$currentsortfieldindex = array_search($args[sort_field],$sortfields);

		if($additionaljsars != "") $additionaljsars = "," . $additionaljsars;
					
		for($i=0; $i < count($sortfields); $i++){
		
			$disp['orderascimg' . $i] = "<IMG SRC=\"".ASSETURL ."img/button-order-asc-inactive.gif\" WIDTH=\"10\" HEIGHT=\"8\" BORDER=\"0\">";
			$disp['orderdescimg' . $i] = "<IMG SRC=\"".ASSETURL ."img/button-order-desc-inactive.gif\" WIDTH=\"10\" HEIGHT=\"8\" BORDER=\"0\">";

			if($currentsortfieldindex == $i){
			
				if($args[sort_direction] == "DESC"){

					$disp["orderascimg". $i] = "<IMG SRC=\"".ASSETURL ."img/button-order-asc-inactive.gif\" WIDTH=\"10\" HEIGHT=\"8\" BORDER=\"0\">";
					$disp["orderdescimg". $i] = "<IMG SRC=\"".ASSETURL ."img/button-order-desc.gif\" WIDTH=\"10\" HEIGHT=\"8\" BORDER=\"0\">";
		
				}else{
		
					$disp["orderascimg". $i] = "<IMG SRC=\"".ASSETURL ."img/button-order-asc.gif\" WIDTH=\"10\" HEIGHT=\"8\" BORDER=\"0\">";
					$disp["orderdescimg". $i] = "<IMG SRC=\"".ASSETURL ."img/button-order-desc-inactive.gif\" WIDTH=\"10\" HEIGHT=\"8\" BORDER=\"0\">";
	
				}
			
			}
			

			
			$disp["pairedsorticons". $i] = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n"
			. "<tr>\n"
			. "<td><a href=\"javascript:$jsfunc('".$sortfields[$i]."','ASC'".$additionaljsars.");\">".$disp['orderascimg'. $i]."</a></td>\n"
			. "<td><a href=\"javascript:$jsfunc('".$sortfields[$i]."','DESC'".$additionaljsars.");\">".$disp['orderdescimg'. $i]."</a></td>\n"
			. "</tr>\n"
			. "</table>\n";
		
		}
		
		
		
	}/////////////////////
	
	
	////////////////////////////////////////////
	function monthPullDown($month, $fieldname,$onchange='',$leadinzeros=false){

		global $pf, $lang_text;
		
		if($month == "") $month = date("m");
		
		$month = ($month * 1); //remove leading zerp
		
		
		if(!is_array($lang_text[months])){
			$lang_text = $pf->op_language->loadlangtext("common","","");
		}
		
		
		$montharray = $lang_text[months];
		

		$outbuf = "";
		
		$outbuf .= "\n<select id=\"$fieldname\" name=\"$fieldname\"";
		
		if($onchange != "")$outbuf .= " onchange=\"".$onchange."\" ";
		
		$outbuf .= ">\n";

		for($i=0; $i < 12; $i++) {
		
			if($leadinzeros && $i < 9) $leadzero = "0";
			else $leadzero = "";
		
		
			if ($i != ($month - 1)) {
				$outbuf .= "<option value=\"" . $leadzero . ($i + 1) . "\">".$montharray[$i]."</option>\n";
			} else {
				$outbuf .= "<option value=\"" . $leadzero . ($i + 1) . "\" selected>".$montharray[$i]."</option>\n";
			}
			
		}

		$outbuf .= "</select>\n\n";
		
		return $outbuf;
		
	}


	/////////////////////////////
	function yearPullDown($year,$fieldname,$onchange=''){

		$outbuf = "";
		
		if($year == "") $year = date("Y");
		
		
		$outbuf .= "<select id=\"$fieldname\" name=\"$fieldname\"";
		
		if($onchange != "")$outbuf .= " onchange=\"".$onchange."\" ";
		
		$outbuf .= ">\n";

		$z = 3;
		for($i=1;$i < 8; $i++) {
			if ($z == 0) {
				$outbuf .= "<option value=\"" . ($year - $z) . "\" selected>" . ($year - $z) . "</option>\n";
			} else {
				$outbuf .= "<option value=\"" . ($year - $z) . "\">" . ($year - $z) . "</option>\n";
			}
			
			$z--;
		}

		$outbuf .= "</select>\n\n";
		
		return $outbuf;
		
	}
	
	////////////////////////////////
	//
	//
	//
	function leadingzero($val){

		$val = $val . ""; // recast to string
		if(strlen($val) == 1) $val = "0" . $val;
		
		return $val;
		
	}//////////////////
	
	
	/**
	* @uses same a regular paginator except it adds a javascript link instead of a static link
	* @author Galbraith Desmond
	 * @return html 
	 */
	function ajaxPaginator( $max, $maxPerPage, $page, $script, $params, $seperator = '|', $maxPagesPerPage = 10, $page_id=''){

		global $pf, $PageManager,  $disp;
		
		$disp[full_asset_url] = $pf->server_type . $pf->subdomain_name . "." . $pf->domain_name . $pf->asset_url;
		
		
		
		$prevtemplatedir = $pf->tengine->getDir();

		$pf->tengine->setDir($pf->app_dir . "/templates/");
	

		
		if($maxPerPage == 0 || $maxPerPage == "") $maxPerPage = 20;		
		
		if(is_array($params)){
			
			$previcon = "<td><a href=\"javascript:".$script."('" . implode("','",$params) . "','" 
			. ($page-1) 
			. "')\"><img src=\"".$disp[full_asset_url] . "img/pageprev.gif\" border=0></a></td>";
			$nexticon = "<td><a href=\"javascript:".$script."('" . implode("','",$params) . "','" 
			. ($page+1) 
			. "')\"><img src=\"".$disp[full_asset_url] . "img/pagenext.gif\" border=0></a></td>";
			
			
		}else if($params != ""){
			
			$previcon = "<td><a href=\"javascript:$script('$params&amp;page=" . ($page-1) . "')\"><img src=\"".$disp[full_asset_url] . "img/pageprev.gif\" border=0></a></td>";
			$nexticon = "<td><a href=\"javascript:$script('$params&amp;page=" . ($page+1) . "')\"><img src=\"".$disp[full_asset_url] . "img/pagenext.gif\" border=0></a></td>";
			
		}else{
			
			$previcon = "<td><a href=\"javascript:$script('" . ($page-1) . "')\"><img src=\"".$disp[full_asset_url] . "img/pageprev.gif\" border=0></a></td>";
			$nexticon = "<td><a href=\"javascript:$script('" . ($page+1) . "')\"><img src=\"".$disp[full_asset_url] . "img/pagenext.gif\" border=0></a></td>";
			
		}

		$podstron=  ceil( $max / $maxPerPage ); 

		$outbuf .=   "<table border=0 cellpadding=1>\n<tr>\n";

		if($podstron > $page) 
		$next = 1; 
		else  
		$next = 0; 

		$max = ceil( $page + ( $maxPagesPerPage / 2 ) );
		$min = ceil( $page - ( $maxPagesPerPage / 2 ) );

		if($min<0)
		$max += -( $min );
		if( $max > $podstron )
		$min -= $max - $podstron;

		$l['min'] = 0;
		$l['max'] = 0;


		if($page > 1) $disp[previcon] = $previcon;


		$disp[pagenums] = "";

		for ( $i = 1; $i <= $podstron; $i++ ) { 

			if( $i >= $min && $i <= $max ) {

				if ( $i == $page ){
					$disp[pagenums] .= "<td>$seperator</td><td><font style=\"font-weight: bold;\">".$i."</font></td>\n"; 
				}else{			
					
					if(is_array($params)){
						$disp[pagenums] .= "<td>$seperator</td><td><a href=\"javascript:".$script."('" . implode("','",$params) . "','" . $i . "')\">$i</a></td>\n";

					}else if($params != ""){
						$disp[pagenums] .= "<td>$seperator</td><td><a href=\"javascript:$script('$params&amp;page=$i')\">$i</a></td>\n"; 

					}else{
						$disp[pagenums] .= "<td>$seperator</td><td><a href=\"javascript:$script('$i')\">$i</a></td>\n"; 
					}
										
					
				}
			}elseif( $i < $min ) {

				if( $i == 1 ){
					
				
					if(is_array($params)){
						$disp[pagenums] .= "<td>$seperator</td><td><a href=\"javascript:".$script."('" . implode("','",$params) . "','" . $i . "')\">$i</a></td>\n"; 

					}else if($params != ""){
						$disp[pagenums] .= "<td>$seperator</td><td><a href=\"javascript:$script('$params&amp;page=$i')\">$i</a></td>\n"; 

					}else{
						$disp[pagenums] .= "<td>$seperator</td><td><a href=\"javascript:$script('$i')\">$i</a></td>\n"; 
					}
					
					
					
				}else{
					if( $l['min'] == 0 ){
						$disp[pagenums] .= "<td>$seperator ... </td>\n"; 
						$l['min'] = 1;
					}
				}

			}elseif( $i > $min ) {

				if( $i == $podstron ){
					
					
					if(is_array($params)){
						$disp[pagenums] .= "<td>$seperator</td><td><a href=\"javascript:".$script."('" . implode("','",$params) . "','" . $i . "')\">$i</a></td>\n"; 

					}else if($params != ""){
						$disp[pagenums] .= "<td>$seperator</td><td><a href=\"javascript:".$script."('$params&amp;page=$i')\">$i</a></td>\n"; 

					}else{
						$disp[pagenums] .= "<td>$seperator</td><td><a href=\"javascript:".$script."('$i')\">$i</a></td>\n"; 
					}
					
				}else{
					if( $l['max'] == 0 ){
						$disp[pagenums] .= "<td>$seperator ... </td>\n"; 
						$l['max'] = 1;
					}
				}
			}
		} // end for

		$disp[pagenums] .= "<td>$seperator</td>\n";

		if($page < $podstron) $disp[nexticon] = $nexticon;

		//echo " $script <br>";
		
		if($page_id != "") $disp[currentpagefield] = "<input type=\"hidden\" id=\"".$page_id."current_page\" name=\"".$page_id."current_page\" value=\"$page\">";
		


		if($podstron > 10){
		
			$pf->load->library('formlist');
			
	
			//$pf->load->library("formlist",array("cnc_formlist"));

			
			$formlist = new FormList('paginator');
		
		
			$splitlimit = 10;
		
			if($podstron > 100) $splitlimit = 20;
			if($podstron > 500) $splitlimit = 50;
			if($podstron > 1000) $splitlimit = 100;
			if($podstron > 5000) $splitlimit = 500;		
			if($podstron > 10000) $splitlimit = 1000;	
		
			$splitrange = round($podstron / 10);
		
		
			$pagehops_array = array($pf->lang_text[gotopage]=>"",1=>1);
				
			//$outbuf .= "$maxPagesPerPage, $page, $maxPerPage, $max ";
		
			for($i=10; $i < $podstron; $i+= 10){
				$pagehops_array[$i] = $i;
			}
				
			$pagehops_array[$podstron] = $podstron;
	
		
			$disp[paginator_select] = $formlist->Add('paginator_select','select');
			$disp[paginator_select]->setOptions($pagehops_array);
		
			if(is_array($params)){	
				$disp[paginator_select]->setOnChange("paginatorhopper('$script','','" . implode("','",$params) . "',this)");
			}else if($params != ""){
				$disp[paginator_select]->setOnChange("paginatorhopper('$script','$params&amp;page=','',this)");
			}else{
				$disp[paginator_select]->setOnChange("paginatorhopper('$script','','', this)");
			}
		
			$disp[paginator_select] = $disp[paginator_select]->gethtml();
		
		}
		

		$outbuf = $pf->tengine->showpartial('common.tpl','AJAXPAGINATOR');
			
		$pf->tengine->setDir($prevtemplatedir);
		
		return $outbuf;

	}////////////////////////////////////


	/**
	* @uses creates a static paginator
	* @author Galbraith Desmond
	* @param max int number of total records
	* @param maxPerPage int max to show on any page
	* @page current page number
	* @address string url of requesting page
	* @separator string  used to separate items
	* @maxPagesPerPage into number of pages to show in paginator nav bar
	* @page_id string  used to identify paginator for page with more than one paginator instance
	* @anchor string anchor element to sroll to
	*
	* @return html 
	*/
	function Paginator( $max, $maxPerPage, $page, $address, $seperator = '|', $maxPagesPerPage = 10, $page_id='', $anchor='' ){

		$previcon = "<td><a href=\"" . $address . "&amp;page=" .($page-1) . $anchor ."\"><img src=\"".ASSETURL . "public/default/images/left_arrow.gif\" border=0></a></td>";
		$nexticon = "<td><a href=\"" . $address . "&amp;page=" .($page+1) . $anchor  ."\"><img src=\"".ASSETURL . "public/default/images/right_arrow.gif\" border=0></a></td>";

		if($maxPerPage == "") $maxPerPage = 10;


		$podstron=  ceil( $max / $maxPerPage ); 

		$outbuf = "<table border=0 cellpadding=1>\n<tr>\n";

		if($podstron > $page) 
		$next = 1; 
		else  
		$next = 0; 

		$max = ceil( $page + ( $maxPagesPerPage / 2 ) );
		$min = ceil( $page - ( $maxPagesPerPage / 2 ) );

		if($min<0)
		$max += -( $min );
		if( $max > $podstron )
		$min -= $max - $podstron;

		$l['min'] = 0;
		$l['max'] = 0;

		if($page > 1) $outbuf .= $previcon;


		for ( $i = 1; $i <= $podstron; $i++ ) { 

			if( $i >= $min && $i <= $max ) {

				if ( $i == $page ) 
				$outbuf .= "<td>$seperator</td><td><font style=\"font-weight: bold;\">".$i."</font></td>\n"; 
				else 
				$outbuf .= "<td>$seperator</td><td><a href=\"" . $address . "&amp;page=" .$i .  $anchor . "\">" . $i ."</a></td>\n"; 

			}elseif( $i < $min ) {

				if( $i == 1 )
				$outbuf .= "<td>$seperator</td><td><a href=\"".$address."&amp;page=".$i. $anchor . "\">".$i."</a></td>\n"; 
				else{
					if( $l['min'] == 0 ){
						$outbuf .= "<td>$seperator ... </td>"; 
						$l['min'] = 1;
					}
				}

			}elseif( $i > $min ) {

				if( $i == $podstron ){
					$outbuf .= "<td>$seperator</td><td><a href=\"" . $address . "&amp;page=".$i. $anchor . "\">".$i."</a></td>\n"; 
				}else{
					if( $l['max'] == 0 ){
						$outbuf .= "<td>$seperator ... </td>\n"; 
						$l['max'] = 1;
					}
				}
			}
		} // end for


		$outbuf .= "<td>$seperator</td>\n";

		if($page < $podstron) $outbuf .= $nexticon;


		
		
		
		if($podstron > 10){
		
			$pf->load->library("formlist");
			$formlist = new FormList('paginator');
		
		
			$splitlimit = 10;
		
			if($podstron > 100) $splitlimit = 20;
			if($podstron > 500) $splitlimit = 50;
			if($podstron > 1000) $splitlimit = 100;
			if($podstron > 5000) $splitlimit = 500;		
			if($podstron > 10000) $splitlimit = 1000;	
		
			$splitrange = round($podstron / 10);
		
		
			$pagehops_array = array($pf->lang_text[gotopage]=>"",1=>1);
				
		
			for($i=10; $i < $podstron; $i+= 10){
				$pagehops_array[$i] = $i;
			}
				
			$pagehops_array[$podstron] = $podstron;
	
		
			$disp[paginator_select] = $formlist->Add('paginator_select','select');
			$disp[paginator_select]->setOptions($pagehops_array);
			$disp[paginator_select]->setOnChange("document.location='$address&amp;page=' + this.value + '$anchor';");
			$disp[paginator_select] = $disp[paginator_select]->gethtml();
			
			
			$outbuf .= "<td>" . $disp[paginator_select] . "</td>\n";
		
		}
		
		
		
		$outbuf .= "</tr>\n</table>";

		$outbuf .= "<input type=\"hidden\" id=\"".$page_id."current_page\" name=\"".$page_id."current_page\" value=\"$page\">";
		
		//$pf->tengine->setDir($prevtemplatedir);
		
		return $outbuf;

	} // end function countPages
	
	
	

	///////////////////////////////////////////
	//
	//
	function timeMinutes($tstamp){
	
		if($tstamp == "") return "";
		
		list($hour,$minute) = explode(":",$tstamp);

		$hour = ($hour * 1);		
		$minute = ($minute * 1);
				
		$minutes = ($hour * 60) + $minute;
	
		return $minutes;
	
	}/////////////////////////////////
	
	
	
	///////////////////////////////////////////////////////////
	//
	//
	function timeDifference($tstamp1,$tstamp2){
		
		if($tstamp1 == "" || $tstamp2 == "") return "";
		
		
		$timetominutes1 = $this->timeMinutes($tstamp1);
		$timetominutes2 = $this->timeMinutes($tstamp2);
		
		$timedifference = ($timetominutes2 - $timetominutes1);


		
		return $timedifference;
	
	}/////////////////////////////
	

	///////////////////////////////////////////////////////////
	//
	//
	function minutesToHours($minutes){
			
		if($minutes == "") return "";
		
		
		$leftoverminutes = ($minutes % 60);
		
		$hours = (($minutes - $leftoverminutes) / 60);
		
		$returnstamp = "$hours:$leftoverminutes";
		

		return $this->reformatTime($returnstamp);
		
		
		//return returnstamp;
		
	
	}/////////////////////////////
	
	
	
	

	
	
	
	///////////////////////////////////////////
	//
	function switchTimeFormat($tstamp,$format='military'){
	
		$tstamp = trim($tstamp);
			
		if($tstamp == "") return "";
		
		if(preg_match("/pm/i",$tstamp) || preg_match("/am/i",$tstamp)){
		
			if(preg_match("/pm/i",$tstamp)){
			
				$tstamp = trim(eregi_replace("pm","",$tstamp));

				list($hour,$minute) = explode(":",$tstamp);
				
				$hour = ($hour*1) + 12;
													
			}else{
			
				$tstamp = trim(eregi_replace("am","",$tstamp));
				list($hour,$minute) = explode(":",$tstamp);
							
			}
			
			
			if(strlen($hour) < 2) $hour = "0" . $hour;
			if(strlen($minute) < 2) $minute = "0" . $minute;
						
			$tstamp = "$hour:$minute";
			
		
		}
				
		if($format == 'ampm'){
		
			list($hour,$minute) = explode(":",$tstamp);
									
			if(($hour * 1) > 11){
			
				if(($hour * 1) != 12) $hour = ($hour % 12);
								
				if(strlen($hour) < 2) $hour = "0" . $hour;
				if(strlen($minute) < 2) $minute = "0" . $minute;	
				
				return "$hour:$minute pm";
							
			
			}else{
			
				if(strlen($hour) < 2) $hour = "0" . $hour;
				if(strlen($minute) < 2) $minute = "0" . $minute;	
				
				return "$hour:$minute am";
							
			}
			
		}else{
				
			return $tstamp;
			
		
		}
		
		

	
	}////////////////////////////
	
	
	
	///////////////////////////////////////////
	//
	function reformatTime($tstamp){
	
		if($tstamp == "") return "";
	
		list($hour,$minute) = explode(":",$tstamp);
		
		if(strlen($hour) < 2) $hour = "0" . $hour;
		if(strlen($minute) < 2) $minute = "0" . $minute;
		

		return "$hour:$minute";
	
	}////////////////////////////
	
	
	/////////////////////////////////////////////////
	// convert 745 to 07:45, 1315 to 13:15
	//
	function timeIntToClock($timeint){
	
		if(strlen($timeint) == 3){ // AM
					
			$timeint_hour = "0" . substr($timeint,0,1);
			$timeint_min = substr($timeint,1,2);	
						
		}else{
					
			$timeint_hour = substr($timeint,0,2);					
			$timeint_min = substr($timeint,2,2);						
		}
		
		return "$timeint_hour:$timeint_min";
		
	}////////////////////
					
	

	/*
	* is you want a int return, specify the length
	* if you want a dtring return, use the PHP date format (i.e. Ymd).
	*/
	function timeStampConvert($tstamp,$toformat){
	
		if($tstamp == "" || $toformat == "") return "";
	
			
		if(is_numeric($tstamp)){
		
		
			$year = substr($tstamp,0,4);
			$month = substr($tstamp,4,2);
			$day = substr($tstamp,6,2);
			$hour = substr($tstamp,8,2);
			$minute = substr($tstamp,10,2);
			$second = substr($tstamp,12,2);
			
			if($year == "") return "";
			if($month == "")  $day = 1;
			if($day == "") $day = 1;
			if($hour == "") $hour = 0;			
			if($minute == "") $minute = 0;		
			if($second == "") $second = 0;
			
			return date($toformat, mktime($hour, $minute, $second , $month, $day, $year));
			
		
		}else{
		
			return date($toformat, strtotime($tstamp));
			
		}
		
		
	
	}////////////////////////////
	
	///////////////////////////////////////
	//
	//
	//
	function generatePDF($title,$html,$debug=false){
		
		global $pf;
		
		if($debug){
			echo $html;
			return;
		}
		
		ini_set("memory_limit", "80M");

		require_once(APPPATH."/libraries/dompdf/dompdf_config.inc.php");
		
	
		
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->render();
		$dompdf->stream($title . "_" . date("Y_m_d") . ".pdf");
		
		
		
	}//////////////////
	
	
	
	///////////////////////////////////////
	//
	//
	//
	function generateCSV($form_name,$columns,$form_data,$debug=false){

		global $pf;
		
		if($debug){
			$row_num = 1;
			foreach ($form_data as $row_data){
				if($row_data != ""){
					foreach ($row_data as $data){
						echo "$data , ";
					}
					echo "<br>";
				}
			}
			return;
		}
		
		
		require(APPPATH."libraries/pear/Compat/Function/fputcsv.php");
		
		//$clean_form_name = ereg_replace("[^a-zA-Z0-9_-]", "",$form_name);
		$clean_form_name = preg_replace("#[^a-zA-Z0-9_-]#", "",$form_name);
		
		//Prepare headers
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public", false);
		header("Content-Description: File Transfer");
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=\"{$clean_form_name}.csv\"");
		
		$out = fopen('php://output', 'w');
		
		fputcsv($out, $columns);
		
		
		
		foreach ($form_data as $row_data){
			fputcsv($out, $row_data);
		}
		
		fclose($out);
		
		
	}//////////////////
	
	
	///////////////////////////////////////
	//
	//
	//
	function generateExcel($form_name,$columns,$form_data,$debug=false){
		
		global $pf;
		
		if($debug){
			
			$row_num = 1;
			foreach ($columns as $row_data){
				echo "$row_data , ";
			}
			echo "<br>";
			
			$row_num = 1;
			foreach ($form_data as $row_data){
				if($row_data != ""){
					foreach ($row_data as $data){
						echo "$data , ";
					}
					echo "<br>";
				}
			}
			return;
		}
		
		
		ini_set("include_path", APPPATH."/libraries/pear");
		
		
		
		require(APPPATH."/libraries/pear/Spreadsheet/Excel/Writer.php");
		
		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();
		
		// sending HTTP headers
		//$clean_form_name = ereg_replace("[^a-zA-Z0-9_-]", "",$form_name);
		$clean_form_name = preg_replace("#[^a-zA-Z0-9_-]#i", "",$form_name);
		
		$workbook->send("{$clean_form_name}.xls");
		
		if(function_exists('iconv')){
			$workbook->setVersion(8); 
		}
		
		// Creating a worksheet
		$worksheet =& $workbook->addWorksheet($clean_form_name);
		
		$format_bold =& $workbook->addFormat();
		$format_bold->setBold();
		$format_bold->setFgColor(22);
		$format_bold->setPattern(1);
		$format_bold->setBorder(1);
		
		if(function_exists('iconv')){
			$worksheet->setInputEncoding('ISO-8859-1');
		}
		
		//print column header
		
		$numrows = count($columns);
		

		
		$worksheet->freezePanes(array(1, 0));
		
		$i=0;
		foreach ($columns as $label){
			$worksheet->write(0, $i, $label,$format_bold);
			$i++;
		}
		
		

					
		
		//print column data
		$row_num = 1;
		foreach ($form_data as $row_data){
			$col_num = 0;
			
			if($row_data != ""){
				foreach ($row_data as $data){
					$worksheet->write($row_num, $col_num, $data);
					$col_num++;	
				}
				
				$row_num++;
				
			}
			
		}
		
		// Let's send the file
		$workbook->close();
		
		
		
	}//////////////////
	
	
	/**
	*@method 
	*@params
	*@return
	*/


	/**
	*@method  takes a simple array and returns table rows of defined columns
	*@params
	*@return
	*/
	function array2TableRows($inputarray,$maxcols = 3){
		
		$outbuf = "";		
		
		$outbuf .= "<tr>\n";
		
		for($i=0; $i < count($inputarray); $i++)
		{
			
			if($i > 0 && $i % $maxcols == 0){
				
				$outbuf .= "\n</tr>\n<tr>\n";
				
			}
			
			$outbuf .= "<td>" . $inputarray[$i] . "</td>";
			
			
		}
		
		if(($i % $maxcols) != 0){
			// now clean up the footer of the table so there are no missing cells. 
			
			$emptycells = ($maxcols - ($i % $maxcols));
			
			if($emptycells > 0){
				
				for($i=0; $i < $emptycells; $i++){
					
					$outbuf .= "<td></td>";
					
				}
				
				
			}
		}
		
		$outbuf .= "\n</tr>\n";


		return$outbuf;
		
	}///////////////

	/**
	*@method  takes a simple array and builds a table of defined columns
	*@params inputarray
	*@params maxcols
	*@params tablestyle
	*@return
	*/
	function array2Table($inputarray,$maxcols = 3, $tablestyle=""){
		
		$outbuf = "";		
		
		if($tablestyle == "") $tablestyle = "cellspacing='0' border='0'";
		
		
		$outbuf .= "<table " . $tablestyle . ">\n";
		
		$outbuf .= $this->array2TableRows($inputarray,$maxcols);
		
		$outbuf .= "\n</table>\n";

		return $outbuf;
		
	}///////////////
	
	
	
}
