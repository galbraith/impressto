<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class highlighter{

	var $counter;
	var $casesensitive;
	var $extracts;
	var $wordsfound = 0;
	var $matchall = false;




    function highlight_text($casesensitive=false,$extracts=true,$matchall=false){

    	$this->counter=0;
    	$this->casesensitive=$casesensitive;
    	$this->extracts=$extracts;
    	$this->matchall=$matchall;

    }


	function highlight($words,&$string,$open='<b>',$close='</b>',$doublecheck=0)
	{

		if(is_array($words)){
			foreach($words as $word)
			$this->highlight($word,$string,$open,$close,$doublecheck);

		}else	if($this->casesensitive == true){

			if(strstr($string,$words)!== false){

				$wordreg=preg_replace('/([\.\*\+\(\)\[\]])/','\\\\\1',$words);
				$string=preg_replace('/(<)([^>]*)('.("$wordreg").')([^<]*)(>)/se',"'\\1'.preg_replace('/'.(\"$wordreg\").'/i','###','\\2\\3\\4').'\\5'",stripslashes($string));
				$string=preg_replace('/('.$wordreg.')/s',$open.'\\1'.$close,stripslashes($string));
				$string=preg_replace('/###/s',$words,$string);

				if($this->counter>0 && $doublecheck){
					$tc=str_replace('/','\/',$close);
					$string=preg_replace('/('.$open.')([^<]*)('.$open.')([^<]+)('.$tc.')([^<]*)('.$tc.')/si','\\1\\2\\4\\6\\7',$string);
				}
				$this->counter++;
				$this->wordsfound++;

			}else if($this->matchall == true){ $string = ""; $words = ""; }

		}else if(stristr($string,$words)!== false){

			$wordreg=preg_replace('/([\.\*\+\(\)\[\]])/','\\\\\1',$words);
			$string=preg_replace('/(<)([^>]*)('.("$wordreg").')([^<]*)(>)/sei',"'\\1'.preg_replace('/'.(\"$wordreg\").'/i','###','\\2\\3\\4').'\\5'",stripslashes($string));
			$string=preg_replace('/('.$wordreg.')/si',$open.'\\1'.$close,stripslashes($string));
			$string=preg_replace('/###/si',$words,$string);

			if($this->counter>0 && $doublecheck){
				$tc=str_replace('/','\/',$close);
				$string=preg_replace('/('.$open.')([^<]*)('.$open.')([^<]+)('.$tc.')([^<]*)('.$tc.')/si','\\1\\2\\4\\6\\7',$string);
			}
			$this->counter++;
			$this->wordsfound++;

		}else if($this->matchall == true){ $string = ""; $words = ""; }
	

	}


	function dohighlight($words,$string,$open='<span style="background-color: yellow;">',$close='</span>',$doublecheck=0, $pagelink=''){

		global $foundpagenum;

		$foundpagenum = 1;



		$this->wordsfound = 0;

		$this->highlight($words,$string,$open,$close,$doublecheck);

		if($this->wordsfound > 0){

			if($this->extracts == true){

				$string = explode("\n",$string);

				$newstring = "";

				foreach($string as $line){

					if (strpos ($line,'[PAGEBREAK]') !== false) $foundpagenum ++;


					if(strpos($line,$open) !== false){

						if($pagelink != "") $newstring .= "<a href=\"" . $pagelink . "&pagenum=" . $foundpagenum . "\">";

						$newstring .= "... " . $line . " ... <br>\n";	

						if($pagelink != "") $newstring .= "</a>";

					}
				}

				return $newstring;
			}else{
				return $string;
			}

		}else{
			return ""; 
		}

	}
}

