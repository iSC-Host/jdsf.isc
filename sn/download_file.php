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
session_name('cunity_sess'.$_SERVER['DOCUMENT_ROOT']);
session_start();
ini_set('session.use_cookies', true);
require_once 'classes/Cunity.class.php';

$cunity = new Cunity();

function serve_file($filename, $size, $path){
	header("Cache-Control: ");# leave blank to avoid IE errors
	header("Pragma: ");# leave blank to avoid IE errors
	header("Content-type: Image");
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header('Content-length:'.$size);
	readfile($path); 
}
if(isset($_GET['id'])){
	$result = $cunity->getDb()->query("(SELECT file_path, file_name, file_size from ".$cunity->getConfig("db_prefix")."files where file_id=".mysql_real_escape_string($_GET['id'])." and user_id=".$_SESSION['userid'].") UNION (SELECT file_path, file_name, file_size FROM ".$cunity->getConfig("db_prefix")."files_share AS A JOIN ".$cunity->getConfig("db_prefix")."files AS B ON A.file_id = B.file_id where A.file_id=".mysql_real_escape_string($_GET['id'])." and A.friend_id=".$_SESSION['userid'].");");
	if(mysql_num_rows($result)>0){
		$row = mysql_fetch_assoc($result);						
		    serve_file($row['file_name'], $row['file_size'], $cunity->getSetting('files_dir').$row['file_path']);
	}else
        header("HTTP/1.0 403 Access Denied! You are not allowed to download this file.");
}
?>