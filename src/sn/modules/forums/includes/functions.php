<?php
/*
########################################################################################
## CUNITY(R) V1.0beta - An open source social network / "your private social network" ##
########################################################################################
##  Copyright (C) 2011 Smart In Media GmbH & Co. KG                                   ##
## CUNITY(R) is a registered trademark of Dr. Martin R. Weihrauch                     ##
##  http://www.cunity.net                                                             ##
##                                                                                    ##
########################################################################################

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or any later version.

1. YOU MUST NOT CHANGE THE LICENSE FOR THE SOFTWARE OR ANY PARTS HEREOF! IT MUST REMAIN AGPL.
2. YOU MUST NOT REMOVE THIS COPYRIGHT NOTES FROM ANY PARTS OF THIS SOFTWARE!
3. NOTE THAT THIS SOFTWARE CONTAINS THIRD-PARTY-SOLUTIONS THAT MAY EVENTUALLY NOT FALL UNDER (A)GPL!
4. PLEASE READ THE LICENSE OF THE CUNITY SOFTWARE CAREFULLY!

	You should have received a copy of the GNU Affero General Public License
    along with this program (under the folder LICENSE).
	If not, see <http://www.gnu.org/licenses/>.

   If your software can interact with users remotely through a computer network,
   you have to make sure that it provides a way for users to get its source.
   For example, if your program is a web application, its interface could display
   a "Source" link that leads users to an archive of the code. There are many ways
   you could offer source, and different solutions will be better for different programs;
   see section 13 of the GNU Affero General Public License for the specific requirements. 
   
   #####################################################################################
   */
   

	function basic_parse($string) {	
		$string = stripslashes($string);
		$string = htmlspecialchars($string);
		
		return $string;
	}

	function parse($string, $bbc = true) {
		$string = basic_parse($string);
		$string = nl2br($string);
	
		//BBC
		if($bbc) {
			//bold
				bb_replace(array('[b]', '[/b]'), array('<strong>', '</strong>'), $string);
			//italic
				bb_replace(array('[i]', '[/i]'), array('<i>', '</i>'), $string);
			//underline
				bb_replace(array('[u]', '[/u]'), array('<u>', '</u>'), $string);
			//quotes
				bb_replace(array('[quote]', '[/quote]'), array('<cite>', '</cite>'), $string);
		}
		
		$string = smilies($string);
		
		return $string;
	}
	
	function bb_replace($tags, $html, &$string) {
		$count = 0;
		
		if(stripos($string, $tags[0]) !== false) {
			$string = str_ireplace($tags, $html, $string, $count);
			if($count % 2 != 0)
				$string .= $html[1];
		}
	}
	
    function getSmilies()
	{
        global $smilies, $settings;
        include('style/'.$_SESSION['style'].'/smilies/smilies.php');
        
        return true;
    }   
    	
	function smilies(&$string) {
		global $smilies;

		foreach($smilies AS $code => $filename)
		{
            $string = str_ireplace($code, $filename, $string);
        }

    
		return $string;
		
	} 
?>