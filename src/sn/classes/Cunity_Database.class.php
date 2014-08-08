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
   

class Cunity_Database {

	private $cnt = array();
	
		public function __construct($cunityConfig) {
			$this->cnt[0] = mysql_connect($cunityConfig["db_host"], $cunityConfig["db_user"], $cunityConfig["db_pass"]) or die('No database connection (Server not found or login failed!)');
			mysql_select_db($cunityConfig["db_name"], $this->cnt[0]) or die(mysql_error().'Database Error! ("Database '.$cunityConfig['db_name'].' not found!")');
			mysql_set_charset('utf8',$this->cnt[0]);  //Set the database characters to UTF8 as some German characters are not displayed correctly
			
		}
		
		public function query($string) {
			return mysql_query($string);
		}
		
		public function select($table, $values, $stuff = '') {
			return mysql_query('SELECT '.$values.' FROM '.$table.' '.$stuff);
		}
		
		public function select_assoc($table, $values, $stuff = '') {
			$res = $this->select($table, $values, $stuff);
			
			return mysql_fetch_array($res);
		}
		
		public function query_assoc($query) {
            $res = $this->query($query) or die(mysql_error());
            return mysql_fetch_assoc($res);
        }
		
		public function insert($table, $columns, $values, $stuff = '') {
		    
			return $this->query('INSERT INTO '.$table.' ('.$columns.') VALUES ('.$values.') '.$stuff);
		}
		
		public function __destruct() {
			if($this->cnt) {
				foreach($this->cnt as $value)
					mysql_close($value);
					
				unset($this->cnt);
			}
		}

}

?>