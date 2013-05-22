<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// LEGACY ONLY - PEPLACED BY IMPRESSTO

class ps_templates{

	var $content;
	var $TplFile;
	var $block;
	var $startBlock;
	var $endBlock;
	var $endBlockLine;
	var $fileContent;	
	var $blockContent;
	var $directory;
	var $altlang;
	
	var $ds = "/";  // directory separator
		
	
	private $swapdirs = array();
	
	
	var $mastlang;
	var $dofunceval = true;
	

	public function __construct(){	

		$this->content 		  = ' ';
		$this->TplFile 		  = '';
		$this->block			  = '';
		$this->startBlock   = '<!-- BEGIN ';
		$this->endBlock 	  = '<!-- END ';
		$this->endBlockLine = ' -->';
		$this->mastlang = $this->ds . 'en' . $this->ds;
		$this->altlang = $this->ds  . 'fr' . $this->ds;


	}

	
	
	
	/**
	* This allows us to separate common default templates
	* from highly customized ones
	*/
	function addSwapDir($dir){
		
		
		if($dir != "" && $dir != "/" && $dir != "\\"){
			
			if($dir[strlen($dir)-1] != $this->ds){
				$dir .= $this->ds;
			}
			
			if(!in_array($dir,$this->swapdirs)){
				
				$this->swapdirs[] = $dir;
			
			}
		}
		
		
		
	}//////////////

	///////////////////////////////////////////////////////
	// TEMPLATE PARSER
	///////////////////////////////////////////////////////

	/**
	* check to se if ths block exists in the template
	* return boolean
	*/
	function blockexists( $sFile, $sBlock ){
	
		$prevdir = "";
		
		if(strpos($sFile, $this->ds) === false){
			$this->setFile( $this->directory.$sFile );		

		}else{
			
			// this allows you to specify a path on each call so you don;t have to use setDir each time. 

			$prevdir = $this->getDir();
			
			$viewfile  = $sFile;
			
			// purge all the paths from the file handle before searching for alternatives
			for($i=0; $i < count($this->swapdirs); $i++){
				
				$viewfile = str_replace($this->swapdirs[$i],"",$viewfile);
				
			}
			
			$viewfile_path = str_replace($viewfile,"",$sFile);
			
			$this->setDir($viewfile_path);
			
			$this->setFile($viewfile );
		}

		
		$this->setBlock( $sBlock );

		
		$sFile = $this->getContent( true );

		$iStart = strpos( $sFile, $this->startBlock.$this->block.$this->endBlockLine );
		$iEnd = strpos( $sFile, $this->endBlock.$this->block.$this->endBlockLine );

		if( is_int( $iStart ) && is_int( $iEnd ) ){
			
			if($prevdir != "") $this->setDir($prevdir);
				
			return true;
		}else {
		
			if($prevdir != "") $this->setDir($prevdir);
			
			return false;
		}
		
	
	}////////////
	
	/**
	* take an array and make all keyed variables within global
	*
	*/
	private function globalize($data){
	
		if(is_array($data)){
		
			foreach($data as $key => $val){
			
				global ${$key};

				${$key} = $val;
			
			}
		
		}
	
	}/////
	
	
	
	/**
	* grab a block of html from a file and process it
	* 
	*/
	function showpartial( $sFile, $sBlock, $data = null ){
		
		if($data) $this->globalize($data);
		
		
		$prevdir = "";
		
		if(strpos($sFile, $this->ds) === false){
			$this->setFile( $this->directory.$sFile );		

		}else{
			
			// this allows you to specify a full path rather than using 
			// the SetDir function  

			$prevdir = $this->getDir();
			
			
			$viewfile  = $sFile;
			
			// purge all the paths from the file handle before searching for alternatives
			for($i=0; $i < count($this->swapdirs); $i++){
				
				$viewfile = str_replace($this->swapdirs[$i],"",$viewfile);
				
			}
			//echo " IN HERE ";
			
			$viewfile_path = str_replace($viewfile,"",$sFile);
			
			$this->setDir($viewfile_path);
						
			$this->setFile($viewfile );
		}

		
		$this->setBlock( $sBlock );

		$this->contentdisplay( true );
		
		if($prevdir != "") $this->setDir($prevdir);
		
		return $this->content;


	} // end function tplbshow
	
	
	/**
	*
	*
	*
	*/	
	function show( $sFile, $data = null ){
		
		if($data) $this->globalize($data);
		
		$prevdir = "";
		
		if(strpos($sFile, $this->ds) === false){
			$this->setFile( $this->directory.$sFile );		
		}else{
			
			// this allows you to specify a full path rather than using 
			// the SetDir function  
			
			
			$prevdir = $this->getDir();
			
			$viewfile  = $sFile;
			
			// purge all the paths from the file handle before searching for alternatives
			for($i=0; $i < count($this->swapdirs); $i++){
				
				$viewfile = str_replace($this->swapdirs[$i],"",$viewfile);
				
			}
			
			$viewfile_path = str_replace($viewfile,"",$sFile);
			
			$this->setDir($viewfile_path);
			
			$this->setFile($viewfile );
			
		}
		

		$this->contentdisplay( );
		
		//if($prevdir != "") $this->setDir($prevdir);
		
		
		return $this->content;

	} // end function tplshow
	

	
	
	
	/**
	*
	*
	*
	*/
	private function contentdisplay( $bBlock = null ){


		if( $this->checkFile( ) ){

			if( isset( $bBlock ) ){
				$this->contentblockParse( );
			}else{
				$this->parseAll( );
			}

			

		}

	} // end function contentdisplay
	


	

	/**
	*
	*
	*
	*/
	function checkFile( ){
		
		
		//	// get the basename form the tplfile...
		//	// extranct the
		if( is_file( $this->TplFile ) ){
			
			return true;
			
		}else {
		
			
			// look for the localized version in the default directory first
			
			$sampledir =  $this->directory;
						
			if($this->altlang != "" && $this->mastlang != ""){
				
				$samplefile  = str_replace($sampledir,"",$this->TplFile);
				

				$testdir = str_replace($this->altlang,$this->mastlang,$sampledir);
				$testfile = str_replace("_" . $this->altlang . ".tpl.php", "_" . $this->mastlang . ".tpl.php",$samplefile);

				
				
				if( is_file( $testdir.$testfile ) ){
					
					$this->TplFile = $testdir.$testfile;
					return true;
					
				}

				
				
			}else{
				
				
				$samplefile  = str_replace($sampledir,"",$this->TplFile);
				
				if( is_file( $sampledir.$samplefile ) ){
					
					$this->TplFile = $sampledir.$samplefile;
					return true;
					
				}
				
			}
			
			// so we didn't find the file yet, lets try in the swap dirs if they are setup
			
			if(count($this->swapdirs) > 0){
			
								
				// purge all the paths from the file handle before searching for alternatives
				for($i=0; $i < count($this->swapdirs); $i++){
					
					$this->TplFile = str_replace($this->swapdirs[$i],"",$this->TplFile);
					
				}
				
				
				
				
				for($i=0; $i < count($this->swapdirs); $i++){
					
					if($this->altlang != "" && $this->mastlang != ""){
						
						$testdir = str_replace($this->altlang,$this->mastlang,$this->swapdirs[$i]);
						$testfile = str_replace("_" . $this->altlang . ".tpl", "_" . $this->mastlang . ".tpl",$this->TplFile);
								
						
						//echo "<br />looking for " . $testdir.$testfile . "<br />";
						
						if( is_file( $testdir.$testfile ) ){
						
							//echo "<br />found " . $testdir.$testfile . "<br />";
							
							$this->TplFile = $testdir.$testfile;
							return true;
							
						}
						
						// now we do a hack to implement the new MVC view folders. This can be pahsed out once the MVC has been fully implemented
						$mvc_viewfile = basename($testfile); 
						$mvcviewfile_path = str_replace($mvc_viewfile,"",$testfile) . "views" . $this->ds;
						
						
						//	echo strtolower($testdir) . strtolower($mvcviewfile_path) . strtolower($mvc_viewfile) . " <br>";
						
						
						
						// now try a lowercase path with the views folder included....
						if( is_file( strtolower($testdir) . strtolower($mvcviewfile_path) . strtolower($mvc_viewfile) ) ){
							
							$this->TplFile = strtolower($testdir) . strtolower($mvcviewfile_path) . strtolower($mvc_viewfile);
							return true;
							
						}
						
						
					}else{


						$testdir = $this->swapdirs[$i];
						$testfile = $this->TplFile;
						
						
						if( is_file( $testdir.$testfile ) ){
							
							$this->TplFile = $testdir.$testfile;
							return true;
							
						}
						
						// now we do a hack to implement the new MVC view folders. This can be pahsed out once the MVC has been fully implemented
						$mvc_viewfile = basename($testfile); 
						$mvcviewfile_path = str_replace($mvc_viewfile,"",$testfile) . "views" . $this->ds;
						
						
						
						
						// now try a lowercase path with the views folder included....
						if( is_file( strtolower($testdir) . strtolower($mvcviewfile_path) . strtolower($mvc_viewfile) ) ){
							
							$this->TplFile = strtolower($testdir) . strtolower($mvcviewfile_path) . strtolower($mvc_viewfile);
							return true;
							
						}
						
						
					}
					
				}
				
				
			}
			
			
			$this->content = null;
			echo 'No template file: <i>'. $this->TplFile.'</i><br />';
			return false;
			
			
			
		}
		
	} // end function checkFile
	
	/**
	* Parse content with PHP
	* @return void
	*/
	function phpparse($string){
	
		// skip and hop over the extrat function
		$this_string = $string;
	
		extract( $GLOBALS ); // this is friggin GOLD
				
		$string = $this_string;
		
		
		ob_start();
		eval(" ?>" . $string . "<?php ");
		$this->content = ob_get_contents();
		ob_end_clean();

				
	} // end function parsePHP 

	
	/**
	* New funtion has been completely rewritten
	*
	*
	*/
	function varparse( ){
		
		if( preg_match( '/<?php/', $this->content ) )  $this->phpparse($this->content);
		
		//preg_match_all ('/\{\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\[\]x7f-\xff]*\}/', $this->content, $tagmatches);

		// this was copied from the new version...
		preg_match_all( '/(\{\$[a-zA-Z_]+[a-zA-Z0-9_]*)(([\[]+[\']*[a-zA-Z0-9_]+[\']*[\]]+)*\})/', $this->content, $tagmatches );
		
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
				
				if( isset( $GLOBALS[$var] ) ){

					global ${$var};

					$varstart = strpos( $varname, '[' )+1;
					$varend = strpos( $varname, ']' );
					$key = substr( $varname, $varstart, $varend-$varstart );

					if( isset( ${$var}[$key] ) ){
						
						$this->content = str_replace($tag,${$var}[$key],$this->content);

					}else{
						
						$this->content = str_replace($tag,'',$this->content);
						
					}

				}else{
					
					$this->content = str_replace($tag,'',$this->content);
					
				}
				
				
			}else{
				
				global ${$varname};
				
				$this->content = str_replace($tag,${$varname},$this->content);

				
			}
			
		}
		
		if($this->dofunceval){
			

			$this->content = $this->process_func_evals($this->content);
		}	

		
		return true;
		
	} // end function parse
	


	
	
	/**
	* 
	*
	*
	*/
	function contentblockParse(){

		
		if( isset( $this->blockContent[$this->TplFile][$this->block] ) ){
			
			$this->content = $this->blockContent[$this->TplFile][$this->block];
			
		}else{
			
			$this->content = $this->getFileBlock( );

			if( isset( $this->content ) ){

				$this->blockContent[$this->TplFile][$this->block] = $this->content;
				
			}
		}

		$this->varparse( );
		
		
	}/////////////////////
	

	/**
	* 
	*
	*
	*/
	function parseAll( ){

		if( isset( $this->fileContent[$this->TplFile] ) ){
			$this->content = $this->fileContent[$this->TplFile];
		}else{
			$this->content = $this->getContent( );
		}

		$this->varparse( );
		
	} // end function allParse
	
	
	
	/**
	* 
	*
	*
	*/
	function getContent( $bBlock = null ){

		if( isset( $this->fileContent[$this->TplFile] ) ){
			$mReturn = $this->fileContent[$this->TplFile];
		}else{
			if( isset( $bBlock ) ){
				$mReturn = $this->getFile( $this->TplFile );
			}else{
				$mReturn = $this->getFile( $this->TplFile );
			}

			$this->fileContent[$this->TplFile] = $mReturn;

		}

		return $mReturn;

	} // end function getContent


	/**
	* 
	*
	*
	*/	
	function getFile( $sFile ){

		$rFile =  fopen( $sFile, 'r' );
		$iSize =  filesize( $sFile );

		if( $iSize > 0 )
		$sContent = fread( $rFile, $iSize );
		else
		$sContent = null;

		fclose( $rFile );
		return ' '.$sContent;

	} // end function getFile


	/**
	* 
	*
	*
	*/
	function getFileBlock( $sFile = null, $sBlock = null ){

		if( isset( $sFile ) && isset( $sBlock ) ){
			$this->setFile( $sFile );
			$this->setBlock( $sBlock );
		}

		$sFile = $this->getContent( true );

		$iStart = strpos( $sFile, $this->startBlock.$this->block.$this->endBlockLine );
		$iEnd = strpos( $sFile, $this->endBlock.$this->block.$this->endBlockLine );

		if( is_int( $iStart ) && is_int( $iEnd ) ){
			$iStart += strlen( $this->startBlock.$this->block.$this->endBlockLine );
			return ' '.substr( $sFile, $iStart, $iEnd - $iStart );
		}else {
			echo 'No block: <i>'.$this->block.'</i> in file: '.$this->TplFile.' <br />';
			return null;
		}
	} // end function getFileBlock


	/**
	* 
	*
	*
	*/
	function getFileArray( $sFile ){
		return file( $sFile );
	} // end function getFileArray
	
	
	/**
	* 
	*
	*
	*/
	function setDoFuncEval($val){
		
		if($val == "1" || $val == "true") $val = true;
		
		if($val != true) $val = false;
		
		$this->dofunceval = $val;
		
		
	} // end function getDir

	
	/**
	* 
	*
	*
	*/
	function getDir( ){
		return $this->directory;
	} // end function getDir

	
	
	/**
	* 
	*
	*
	*/
	function setSwapDirs( $sDirs ){
	
		if(is_array($sDirs)){
		
			foreach($sDirs as $dir){
			
				$this->addSwapDir($dir);
				//echo "<br > <br />ADDING $dir <br /><br />";
								
			}
			
		}
		
		
	} // end function setDir
	
	
	/**
	* 
	*
	*
	*/
	function setDir( $sDir ){
	
		
			$sDir = str_replace("/",$this->ds,$sDir);
			$sDir = str_replace("\\",$this->ds,$sDir);		
			
			$this->directory = $sDir;
			
			
			if($sDir != "" && $sDir[strlen($sDir)-1] != $this->ds){
					$this->directory .= $this->ds;
			}

		
		
	} // end function setDir

	
	/**
	* 
	*
	*
	*/
	function setFile( $sFile ){

		$this->TplFile = $sFile;
		
	} // end function setFile


	/**
	* 
	*
	*
	*/
	function setBlock( $sBlock ){
		$this->block = $sBlock;
	} // end function setBlock

	
	/**
	* 
	*
	*
	*/
	function setMastLang($mastlang){
		$this->mastlang = $mastlang;
	} // 

	/**
	* 
	*
	*
	*/
	function setAltLang($altlang){
		$this->altlang = $altlang;
	} // 
	
	
	
	
	/**
	* This function can be called by template files or module code to execute template like function calls
	* Sample string: 
	* $text = "He is a simple demo of {some_stupid_class::sayhello('pete')}. Here is another sample:  {itworked('suckyall')} . Here is another: {fancyhi('one','two',array(1,2,3))}";
	* return processed html
	*/	
	function process_func_evals($string){
		
		$pattern = '/\{=(.*?)=\}/is';
		$string = preg_replace_callback($pattern, array($this, 'func_eval'), $string) . "\n";
		
		return $string;
		
	}////////////////////


	///////////////////////////////////////
	//
	// watch out for function calls with ' characters converted to &#39;
	// took me hours to figure that $#%@ out.
	//
	function func_eval($matches){
		
		$outbuf = "";
		
		
		if(trim($matches[1]) != ""){
			
			if(preg_match("/\(/",$matches[1])){
				
				if(preg_match("/::/",$matches[1])){
					
					$callvars = explode("::",$matches[1]);
					
					global ${$callvars[0]};
					
					
					$classobj = ${$callvars[0]};
					
					//if(is_object($classobj)) echo $callvars[0];
					
					$classfunc = substr($callvars[1],0,strpos($callvars[1],"("));
					$argbracketbegin = (strpos($callvars[1],"(") + 2);
					$argbracketend = (strpos($callvars[1],")")-1);	
					$args = substr($callvars[1],$argbracketbegin,($argbracketend-$argbracketbegin));
					$args = explode("','",$args);
					
					if(trim($args[1]) != ""){
						
						$outbuf = call_user_func(array(&$classobj,$classfunc), $args[0], $args[1]);
						
					}else{
						
						$outbuf = call_user_func(array(&$classobj,$classfunc), $args[0]);
						
					}
					
					
				}else{
					
					eval("\$outbuf = ".$matches[1].";");
				}
				
			}else{
				
				// this is a simple tag call
				
				if(preg_match("/::/",$matches[1])){
					
					$callvars = explode("::",$matches[1]);
					
					global ${$callvars[0]};
					
					if(method_exists(${$callvars[0]}, $callvars[1])){
						
						eval("\$outbuf = ".$callvars[0]."::".$callvars[1]."();");
						
					}

				}else if(function_exists($matches[1])){
					
					eval("\$outbuf = ".$matches[1]."();");
				}
				
			}
		}
		
		//ob_end_clean();
		
		return $outbuf;
		
	}///////////////////////////

	
	
	///////////////////////////////////////////////////////
	// END TEMPLATE PARSER
	///////////////////////////////////////////////////////


}

