<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * file tools - functions to assist in managing files and directoires. 
 *
 * @package		ps_edittools
 * @author		Galbraith Desmond <galbraithdesmond@gmail.com>
 * @description Functions to assist in managing files and directoires..
 *
 * @version		1.0 (2012-05-02)
 */
class ps_files{

	var $File_Name;
	var $File_Path;
	var $File_Size;
	var $File_Owner;
	var $File_Grp;
	var $File_Perm = 0777;
	var $Extension;
	var $File_Type;
	var $Folder_Perm 	= 0777;
	var $Allowed_Files = array(".doc", ".xls",".txt",".pdf",".gif",".bmp",".jpg",".jpeg",".zip",".rar",".ppt",".mp3");
	var $Disallowed_Files = array(".exe",".bat",".msi",".sh","");
	var $_Tmp_Name;	
	var $_Mime_Array;
	var $_ErrCode;
	
	#####################################################
	# Constructor
	######################################################
		
	function __construct( $_file = '' ){
		$this->File_Name = $_file;
		$this->Get_Extension();
		
	}
	######################################################
	# ACTION METHODS
	######################################################
	/**
		Opens, reads the file and return the content
		@access Public
		@return string File Content
	*/
	function read(){
		$_filename = $this->File_Path.$this->File_Name;
		$_file = fread($fp = fopen($_filename, 'r'), filesize($_filename));
		fclose($fp);
		return $_file;
	}
	/**
		Delete the file
		inline {@internal checks the OS php is running on, and execute appropriate command}}
		@access Public
		@return string File Content
	*/
	function delete(){
		//if Windows
		if (substr(php_uname(), 0, 7) == "Windows") {
			$_filename  = str_replace( '/', '\\', $this->File_Path.$this->File_Name);
			system( 'del /F "'.$_filename.'"', $_result );
			if( $_result == 0 ){
				return true;
			} else {
				$this->_ErrCode = 'FILE_DEL'.$_result;
				return false;
			}
		//else unix assumed
		} else {
			chmod( $this->File_Path.$this->File_Name, 0775 );
			return unlink( $this->File_Path.$this->File_Name );
		}
	}
	/**
		Create a directory.
		@access Public
		@param string [$_path] path to locate the directory
		@param string [$_DirName] name of the directory to create		
		@return boolean
	*/	
	function Make_Dir($_path, $_DirName){
		if(!file_exists($_path."/".$_DirName)){
			$_oldumask = @umask($this->umask); 
			$_action = @mkdir($_path."/".$_DirName, $this->Folder_Perm);
			@umask($_oldumask);
			if($_action == true){
				return true;
			} else {
				$this->_ErrCode = 'DIR03';
				return false;
			}
		} else{
			$this->_ErrCode = 'DIR04';
			return false;
		}
	}



	
	function cleardir($dir){

		$current_dir = opendir($dir);

		while($entryname = readdir($current_dir)){

			if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!="..")){
				$this->deldir("${dir}/${entryname}");
			}else if($entryname != "." and $entryname!=".."){
				unlink("${dir}/${entryname}");
			}
		}
		
		closedir($current_dir);
		
		//rmdir(${dir});
	}
	


	//////////////////////////
	function deldir($dir){
	
		$this->cleardir($dir);
			
		rmdir($dir);
		

	}
	
	


	///////////////////////////
	function copydir($source, $dest){

		// Make destination directory
		if (!is_dir($dest)){
			@mkdir($dest,0777);
			@chmod($dest, 0777);
		}

		// Loop through the folder

		$dir = dir($source);

		if($dir){
			
		
		while (false !== $entry = $dir->read()){

			// Skip pointers
			if ($entry == '.' || $entry == '..'){
				continue;
			}

			// Deep copy directories
			if (is_dir("$source/$entry") && ($dest !== "$source/$entry")){
				$this->copydir("$source/$entry", "$dest/$entry");
			} else {
				copy("$source/$entry", "$dest/$entry");
				
			}
		}

		$dir->close();
		}

		return true;

	}////////////////////////////







	/////////////////////////
	function isDirEmpty($dirname){

		$isempty = true;

		if($dir = @opendir($dirname)){

			while( FALSE !== ($file = readdir($dir)) ){

				if($file != "." && $file != ".." && $file != "index.htm" && $file != ".htaccess"){
					$isempty = false;
				}
			}
		}

		return $isempty;

	}/////////////////





	/**
	* Creates a full path of directories. It will create parent directories if
	* they do not exits
	*
	* This is an exact copy of the same function that is in the common directory_copy helper file 
	*		
	* NOTE: warning turned off in the file function calls because basedir restriction
	* is in effect on some  servers (Media Temple)
	* @author peterdrinnan
	* @param dir string - full directory path to create
	* @secure bool - if TRUE adds a .htaccess file to each folder
	*
	*/
	function create_dirpath($dir,$secure=false){


		$dir_build = $dir;
	
		
		$dir = str_replace("/",DS,$dir);
		
		
		if(!file_exists($dir)){

			$dir_build = "";
			
			$seperated_dirs = explode(DS,$dir);

			$dir_build = $seperated_dirs[0];

			for($i=1; $i < count($seperated_dirs); $i++){

				$dir_build .= DS . $seperated_dirs[$i];
				
					
				if(!@file_exists($dir_build)){

					$oldumask = umask(0);

					@mkdir($dir_build, 0777); 
					
					@chmod($dir_build, 0777);

					@umask($oldumask);

					if(!@file_exists($dir_build.DS."index.html")){

						$NF = @fopen($dir_build.DS."index.html","w");

						@fwrite($NF,

						"<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n"
						."<HTML>\n"
						."<HEAD>\n"
						."<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=iso-8859-1\">\n"
						."<TITLE>FOLDER RESTRICTED</TITLE>\n"
						."</HEAD>\n"
						."<BODY>FOLDER RESTRICTED</BODY>\n"
						."</HTML>\n");

						@fclose($NF);

					}

					if($secure != false && !@file_exists($dir_build.DS.".htaccess") ){

						$htaccessvars = "AuthName \"BC RESTRICT\"\n"
						. "AuthType Basic\n"
						."<Limit GET POST>\n"
						."order deny,allow\n"
						."deny from all\n"
						."</Limit>\n";
						
						$NF = @fopen($dir_build.DS.".htaccess","w");
						@fwrite($NF,	$htaccessvars);
						@fclose($NF);

					}
				}
			}
		}

		return $dir_build;
	
	}





	/**
		Add an addtional extension to the disallowed file array
		@access Public		
		@param mixed [$_Extension] string or array of extensions to be added
	*/	
	function Set_Disallowed_Files($_Extension){
		if( is_array($_Extension) ){
			$this->Disallowed_Files .= $_Extension;
		}else{
			$this->Disallowed_Files[] = $_Extension;
		}
		array_unique ( $this->Disallowed_Files );
	}
	/**
		Add an addtional extension to the allowed file array
		@access Public		
		@param mixed [$_Extension] string or array of extensions to be added
	*/
	function Set_Allowed_Files($_Extension){
		if( is_array( $_Extension)){
			$this->Allowed_Files .= $_Extension;
		}else{
			$this->Allowed_Files[] = $_Extension;
		}
		array_unique ( $this->Allowed_Files );
	}
	/**
		reset the array to blank
		@access Public		
	*/	
	function Reset_Disallowed_Files(){
		unset($this->Disallowed_Files);
	}
	/**
		reset the array to blank
		@access Public		
	*/	
	function Reset_Allowed_Files(){
		unset($this->Allowed_Files);
	}		
	######################################################
	# GET PROPERTIES METHODS
	######################################################
	/**
		Get the mime type of a file
		@access Public		
	*/	






//////////////////////////
//
/////////////////////////
function GetMimeType($file_name){

	$extension = strrchr($file_name,".");
	$extension = strtolower($extension);

	$mimetypes = array(

	'.txt' => 'ASCII Text',
	'.js' => 'JavaScript',
	'.gif' => 'GIF Image',
	'.jpg' => 'JPEG Image',
	'.jpeg' => 'JPEG Image',
	'.bmp' => 'Bitmap Image',
	'.png' => 'PNG Image',
	'.htm' => 'HTML Document',
	'.html' => 'HTML Document',
	'.shtml' => 'HTML Document',
	'.rar' => 'RAR Archive',
	'.gz' => 'GZip Archive',
	'.zip' => 'Zip Archive',
	'.ra' => 'Real Audio',
	'.ram' => 'Real Audio',
	'.rm' => 'Real Audio',
	'.pl' => 'Perl Script',

	'.wav' => 'Wave Audio',

	'.asf' => 'Windows Video',
	'.wmv' => 'Windows Video',
	'.mov' => 'QuickTime Video',
	'.flv' => 'Flash Video',

	'.pdf' => 'Adobe Acrobat',
	'.php' => 'PHP Script',
	'.php3' => 'PHP Script',
	'.phtml' => 'PHP Script',
	'.exe' => 'Executable',
	'.css' => 'Style Sheet',
	'.mp3' => 'MPEG Audio',
	
	'.xls' => 'Excel Document',
	'.xlsx' => 'Excel Document',
	'.xlsm' => 'Excel Document',
	'.xltx' => 'Excel Document',
	'.xltm' => 'Excel Document',
	'.xlsb' => 'Excel Document',
	'.xlam' => 'Excel Document',
	
	
	'.doc' => 'Word Document',
	'.docx' => 'Word Document',
	'.docm' => 'Word Document',
	'.dotx' => 'Word Document',
	'.dotm' => 'Word Document',

	'.ppt' => 'PowerPoint Document',
	'.pptx' => 'PowerPoint Document',
	'.pptm' => 'PowerPoint Document',
	'.potx' => 'PowerPoint Document',
	'.potm' => 'PowerPoint Document',
	'.ppam' => 'PowerPoint Document',
	'.ppsx' => 'PowerPoint Document',
	'.ppsm' => 'PowerPoint Document',
	'.sldx' => 'PowerPoint Document',
	'.sldm' => 'PowerPoint Document',
	'.thmx' => 'PowerPoint Document',
		
	
	
	'.swf' => 'Flash Object',
	'.wpd' => 'WordPerfect Document'
	);


	if (isset( $mimetypes[$extension] ) ) {
		return $mimetypes[$extension];
	}else{
		return "Unknown";
	}

}/////////////////////

	
/**
* 
* @author peter drinnan
*/
function GetMimeCode($file_name){


	$mimecodes = array(
	".ez" => "application/andrew-inset",
	".hqx" => "application/mac-binhex40",
	".cpt" => "application/mac-compactpro",
	".doc" => "application/msword",
	".docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
	".bin" => "application/octet-stream",
	".dms" => "application/octet-stream",
	".lha" => "application/octet-stream",
	".lzh" => "application/octet-stream",
	".exe" => "application/octet-stream",
	".class" => "application/octet-stream",
	".so" => "application/octet-stream",
	".dll" => "application/octet-stream",
	".oda" => "application/oda",
	".pdf" => "application/pdf",
	".ai" => "application/postscript",
	".eps" => "application/postscript",
	".ps" => "application/postscript",
	".smi" => "application/smil",
	".smil" => "application/smil",
	".wbxml" => "application/vnd.wap.wbxml",
	".wmlc" => "application/vnd.wap.wmlc",
	".wmlsc" => "application/vnd.wap.wmlscriptc",
	".bcpio" => "application/x-bcpio",
	".vcd" => "application/x-cdlink",
	".pgn" => "application/x-chess-pgn",
	".cpio" => "application/x-cpio",
	".csh" => "application/x-csh",
	".dcr" => "application/x-director",
	".dir" => "application/x-director",
	".dxr" => "application/x-director",
	".dvi" => "application/x-dvi",
	".spl" => "application/x-futuresplash",
	".gtar" => "application/x-gtar",
	".hdf" => "application/x-hdf",
	".js" => "application/x-javascript",
	".skp" => "application/x-koan",
	".skd" => "application/x-koan",
	".skt" => "application/x-koan",
	".skm" => "application/x-koan",
	".latex" => "application/x-latex",
	".nc" => "application/x-netcdf",
	".cdf" => "application/x-netcdf",
	".sh" => "application/x-sh",
	".shar" => "application/x-shar",
	".swf" => "application/x-shockwave-flash",
	".sit" => "application/x-stuffit",
	".sv4cpio" => "application/x-sv4cpio",
	".sv4crc" => "application/x-sv4crc",
	".tar" => "application/x-tar",
	".tcl" => "application/x-tcl",
	".tex" => "application/x-tex",
	".texinfo" => "application/x-texinfo",
	".texi" => "application/x-texinfo",
	".t" => "application/x-troff",
	".tr" => "application/x-troff",
	".roff" => "application/x-troff",
	".man" => "application/x-troff-man",
	".me" => "application/x-troff-me",
	".ms" => "application/x-troff-ms",
	".ustar" => "application/x-ustar",
	".src" => "application/x-wais-source",
	".xhtml" => "application/xhtml+xml",
	".xht" => "application/xhtml+xml",
	".zip" => "application/zip",
	".au" => "audio/basic",
	".snd" => "audio/basic",
	".mid" => "audio/midi",
	".midi" => "audio/midi",
	".kar" => "audio/midi",
	".mpga" => "audio/mpeg",
	".mp2" => "audio/mpeg",
	".mp3" => "audio/mpeg",
	".aif" => "audio/x-aiff",
	".aiff" => "audio/x-aiff",
	".aifc" => "audio/x-aiff",
	".m3u" => "audio/x-mpegurl",
	".ram" => "audio/x-pn-realaudio",
	".rm" => "audio/x-pn-realaudio",
	".rpm" => "audio/x-pn-realaudio-plugin",
	".ra" => "audio/x-realaudio",
	".wav" => "audio/x-wav",
	".pdb" => "chemical/x-pdb",
	".xyz" => "chemical/x-xyz",
	".bmp" => "image/bmp",
	".gif" => "image/gif",
	".ief" => "image/ief",
	".jpeg" => "image/jpeg",
	".jpg" => "image/jpeg",
	".jpe" => "image/jpeg",
	".png" => "image/png",
	".tiff" => "image/tiff",
	".tif" => "image/tif",
	".djvu" => "image/vnd.djvu",
	".djv" => "image/vnd.djvu",
	".wbmp" => "image/vnd.wap.wbmp",
	".ras" => "image/x-cmu-raster",
	".pnm" => "image/x-portable-anymap",
	".pbm" => "image/x-portable-bitmap",
	".pgm" => "image/x-portable-graymap",
	".ppm" => "image/x-portable-pixmap",
	".rgb" => "image/x-rgb",
	".xbm" => "image/x-xbitmap",
	".xpm" => "image/x-xpixmap",
	".xwd" => "image/x-windowdump",
	".igs" => "model/iges",
	".iges" => "model/iges",
	".msh" => "model/mesh",
	".mesh" => "model/mesh",
	".silo" => "model/mesh",
	".wrl" => "model/vrml",
	".vrml" => "model/vrml",
	".css" => "text/css",
	".html" => "text/html",
	".htm" => "text/html",
	".asc" => "text/plain",
	".txt" => "text/plain",
	".rtx" => "text/richtext",
	".rtf" => "text/rtf",
	".sgml" => "text/sgml",
	".sgm" => "text/sgml",
	".tsv" => "text/tab-seperated-values",
	".wml" => "text/vnd.wap.wml",
	".wmls" => "text/vnd.wap.wmlscript",
	".etx" => "text/x-setext",
	".xml" => "text/xml",
	".xsl" => "text/xml",
	".mpeg" => "video/mpeg",
	".mpg" => "video/mpeg",
	".mpe" => "video/mpeg",
	".qt" => "video/quicktime",
	".mov" => "video/quicktime",

	".asf" => "video/x-ms-asf",
	".wmv" => "video/x-ms-wmv",

	".mxu" => "video/vnd.mpegurl",
	".avi" => "video/x-msvideo",
	".movie" => "video/x-sgi-movie",
	".ice" => "x-conference-xcooltalk"
	);

	if(strpos($file_name,"/") === false){
	
		$extension = strrchr($file_name,".");
		$extension = strtolower($extension);

		if (isset( $mimecodes[$extension] ) ) {
			return $mimecodes[$extension];
		}else{
			return "application/octet-stream";
		}

	}else{
	
		while ($fruit_name = current($mimecodes)) {
			if ($fruit_name == $file_name) {
				return key($mimecodes);
		}
		next($mimecodes);
	}
	
	}
	
}



/////////////////////////////
//
/////////////////////////////
function GetMimeIcon($file_name){

	$extension = strrchr($file_name,".");
	$extension = strtolower($extension);

	$mimeicons = array(
      ".mid" => "mid",
	".txt" => "txt",
	".js" => "js",
	".gif" => "gif",
	".jpg" => "jpg",
	".jpeg" => "jpg",
	".htm" => "html",
	".html" => "html",
	".shtml" => "html",
	".rar" => "zip",
	".gz" => "zip",
	".zip" => "zip",
	".tar" => "zip",
	".ra" => "ram",
	".ram" => "ram",
	".rm" => "ram",
	".pl" => "pl",
	".pdf" => "pdf",
	".wav" => "wav",
	".php" => "php",
	".php3" => "php",
	".php4" => "php",
	".php5" => "php",
	".phtml" => "php",
	".exe" => "exe",
	".bmp" => "bmp",
	".png" => "gif",
	".css" => "css",
	".mp3" => "mp3",
	
	".xls" => "xls",
	".xlsx" => "xls",
	".xlsm" => "xls",
	".xltx" => "xls",
	".xltm" => "xls",
	".xlsb" => "xls",
	".xlam" => "xls",
		
	
	".doc" => "doc",
	".docx" => "doc",
	".docm" => "doc",
	".dotx" => "doc",
	".dotm" => "doc",
		

	".ppt" => "ppt",
	".pptx" => "ppt",
	".pptm" => "ppt",
	".potx" => "ppt",
	".potm" => "ppt",
	".ppam" => "ppt",
	".ppsx" => "ppt",
	".ppsm" => "ppt",
	".sldx" => "ppt",
	".sldm" => "ppt",
	".thmx" => "ppt",

	
	".asf" => "wmv",
	".wmv" => "wmv",
	".mov" => "mov",

	".swf" => "swf",
	".wpd" => "doc"
	
	);


	if (isset( $mimeicons[$extension] ) ) {
		return $mimeicons[$extension];
	}else{
		return "default";
	}


}////////////////////////





	/**
		Get the owner of a file
		@access Public		
	*/	
	function Get_Owner(){
		$_filename = $this->File_Path.$this->File_Name;
		$this->File_Owner = fileowner( $_filename );
	}
	/**
		Get the group of the file owner
		@access Public		
	*/	
	function Get_Grp(){
		$_filename = $this->File_Path.$this->File_Name;
		$this->File_Grp = filegroup( $_filename);
	}
	/**
		Get the file size
		@access Public		
	*/	
	function Get_Size(){
		if( !$this->File_Size ){
			$this->File_Size = @filesize( $this->File_Path.$this->File_Name );
		}
	}
	/**
		Return everything after the . of the file name (including the .)
		@access Public		
	*/	
    function Get_Extension(){
		$this->Extension = strrchr( $this->File_Name, "." );
    }	
	/**
		Return the error message of an action made on a file (upload, delete, write...)
		@return string error message		
	*/	
	function Status_Message(){
		switch( $this->_ErrCode ){
			case 0:
					$_msg = "The file <b>".$this->File_Name."</b> was succesfully uploaded.\n";
					break;
			case 1:
					$_msg = "<b>".$this->File_Name."</b> was not uploaded. <b>".$this->Extension."</b> Extension is not accepted!\n";
					break;
			case 2:
					$_msg = "The file <b>$this->cls_filename</b> is too big or does not exists!";
					break;
			case 3:
			        $_msg = "Remote file could not be deleted!\n";
					break;
			case 4:
					$_msg = "The file <b>".$this->File_Name."</b> exists and overwrite is not set in class!\n";
					break;
			case 5:
					$_msg = "Copy successful, but renaming the file failed!\n";
					break;
			case 6:
					$_msg = "Unable to copy file :(\n";
					break;
			case 7:
			        $_msg = "You don't have permission to use this script!\n";
                    break;
			case 8:
			        $_msg = ""; // if user does not select a file
					break;
			case "DIR01":
					$_msg = "Can't write File [no fwrite]";
					break;
			case "DIR02":
					$_msg = "Can't write File [no filename | no content]";
					break;
			case "DIR02":
					$_msg = "Can't create Folder [mkdir failed]";
					break;
			case "DIR04":
					$_msg = "Folder exists";
					break;
			case "FILE_DEL1":
					$_msg = "File deletion impossible";
					break;
			default:
					$_msg = "Unknown error!";
		}
		return $_msg ;
	}


	function download_file($target_file){


		global $pf,  $_SERVER, $HTTP_SERVER_VARS;

		if ( !empty($_SERVER['HTTP_USER_AGENT']) ) $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		else if ( !empty($HTTP_SERVER_VARS['HTTP_USER_AGENT']) ) $HTTP_USER_AGENT = $HTTP_SERVER_VARS['HTTP_USER_AGENT'];
		else if ( !isset($HTTP_USER_AGENT) ) $HTTP_USER_AGENT = '';

		if ( preg_match('/Opera(/| )([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version) ) define('USR_BROWSER_AGENT', 'OPERA');
		else if ( preg_match('/MSIE ([0-9].[0-9]{1,2})/', $HTTP_USER_AGENT, $log_version) ) define('USR_BROWSER_AGENT', 'IE');
		else define('USR_BROWSER_AGENT', 'OTHER');

	
		if (file_exists($target_file)){

			$pi = pathinfo($target_file);
			$path = $pi['dirname'].'/';
			$filename = $pi['basename'];
			$pf->load->library("op_admin");
			$contenttype = $this->GetMimeCode($filename);
			
			if(USR_BROWSER_AGENT == 'IE' || USR_BROWSER_AGENT == 'OPERA'){
				$contenttype = 'application/octetstream';
			}else{
				$contenttype = 'application/octet-stream';
			}

			$filesize=filesize($target_file);

			if (USR_BROWSER_AGENT == 'IE'){
				header("Pragma: public");
				header("Content-Type: $contenttype");
				header("Content-Length: ".$filesize);
				header('Content-Disposition: inline; filename="'.$filename.'"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header("Content-Transfer-Encoding: binary");

			}else{
				header('Pragma: no-cache');
				header("Content-Type: $contenttype");
				header("Content-Length: ".$filesize);
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				header('Expires: 0');
			}
	

			$fp = fopen($target_file, 'rb');
			$dump_buffer = fread($fp, $filesize);
			fclose ($fp);
			echo $dump_buffer;
				
		}

	}

}


