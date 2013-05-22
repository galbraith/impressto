<?php
/**
*	Create gradient from two HEX-Colors in $n steps 
* @author Michael Feinbier <mf@tiberianmaster.net>
* @version 1.0
* @package main class
*
*
*  $fade_from = "#FF0000"; // HEX-Color to fade from
*  $fade_to = "#0000FF";   // HEX-Color to fade to
*  $steps = 15;						// number of steps creating the gradient
*  $gradient = new gradient($fade_from,$fade_to,$steps); 		// define the class
*  echo "Gradient from <b>".$gradient->color1."</b> to <b>".$gradient->color2."</b></p><pre>";
*  print_r($gradient->createArray()); 												// outputs the array to see the values
*  echo "</pre><table width='300' border='0'>";							// creates the table with colors 
*  foreach($gradient->createArray() AS $color){
*   	echo "<tr><td bgcolor=$color>&nbsp;</td><td>$color</td></tr>";
*   };
*  echo "</table>";
*/

class gradient 
{
 	var $color1 ;
    var $color2 ;
    var $n = 10;
    var $c1codes;
    var $c2codes;

      
    /* constructor */
    function gradient($params){ //$color1,$color2,$n){
		$this->color1 = $params['color1'];
		$this->color2 = $params['color2']; //$color2;
		$this->n = $params['n']; //$n;
      	$this->c1codes=$this->getHexValues($this->color1);
  			$this->c2codes=$this->getHexValues($this->color2);
        }
     
     /* internal: do not call */   
     function getHexValues($color) {
				$color = substr($color, 1);
				return array(hexdec(substr($color,0,2)),hexdec(substr($color,2,2)),hexdec(substr($color,4,2)));    
				}
        
     function createArray(){				
				  $red=($this->c2codes[0]-$this->c1codes[0])/($this->n-1);
				  $green=($this->c2codes[1]-$this->c1codes[1])/($this->n-1);
				  $blue=($this->c2codes[2]-$this->c1codes[2])/($this->n-1);				  
				  
				  for($i=0;$i<$this->n;$i++){
						  	$newred=dechex($this->c1codes[0]+round($i*$red));
						    if(strlen($newred)<2) $newred="0".$newred;
						  	
						    $newgreen=dechex($this->c1codes[1]+round($i*$green));
						    if(strlen($newgreen)<2) $newgreen="0".$newgreen;
						   
						   	$newblue=dechex($this->c1codes[2]+round($i*$blue));
						    if(strlen($newblue)<2) $newblue="0".$newblue;
						    
						    $return[$i]="#".$newred.$newgreen.$newblue;
				   }
				  
				  return $return;
  			}
}

