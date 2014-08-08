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
   
require_once('../includes/ajaxInit.inc.php');

$data_back = json_decode (stripslashes($_REQUEST['json_data']), true);
$objNotifier = new notifier($cunityConfig["smtp_method"],$cunityConfig["smtp_host"], $cunityConfig["smtp_auth"], $cunityConfig["smtp_username"], $cunityConfig["smtp_password"],$db,$cunityConfig["smtp_port"],$cunityConfig["smtp_sender_address"],$cunityConfig["smtp_sender_name"],$cunityConfig["email_header"],$cunityConfig["email_footer"],$cunity->getConfig("db_prefix"), $settings, $lang);

if(isset($data_back['action']) && $data_back['action'] == 'deleteRequest')
{
    try {
        $relationship_id = $data_back['relationship_id'];
        $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."relationship_requests WHERE relationship_id = '".$relationship_id."'") or die (mysql_error());
        $data = mysql_fetch_assoc($res);
        if($_SESSION['userid'] == $data['sender'] || $_SESSION['userid'] == $data['receiver'])
        {
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."relationship_requests WHERE relationship_id = '".$relationship_id."'") or die (mysql_error());
            $data=array('status'=>'1');

    		$jsonData=json_encode($data);
    
    		echo $jsonData;
        }
        else
        {
            $data=array('status'=>'1', 'error'=>'User-Ids doesn\'t match');

        	$jsonData=json_encode($data);
        
        	echo $jsonData;
        }        
                
    }   
    catch(Exception $e)
    {
        $data=array('status'=>'0', 'error'=>$e);

		$jsonData=json_encode($data);

	    echo $jsonData;
    }         
}
elseif(isset($data_back['action']) && $data_back['action'] == 'confirm_request')
{
    try {
        $relationship_id = mysql_real_escape_string($data_back['relationship_id']);
        $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."relationship_requests WHERE relationship_id = '".$relationship_id."'") or die (mysql_error());
        $data = mysql_fetch_assoc($res);
        if($_SESSION['userid'] == $data['receiver'])
        {
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."relationship_requests WHERE relationship_id = '".$relationship_id."'") or die (mysql_error());
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users_details SET relationship = '".$data['relationship']."', relationship_partner = '".$data['sender']."' WHERE userid = '".$_SESSION['userid']."'") or die (mysql_error());
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users_details SET relationship_partner = '".$_SESSION['userid']."' WHERE userid = '".$data['sender']."'") or die(mysql_error());
            
            $data=array('status'=>'1');

    		$jsonData=json_encode($data);
    
    		echo $jsonData;
        } 
        else
        {
            $data=array('status'=>'0', 'error'=>'User-Ids does not match!');

    		$jsonData=json_encode($data);
    
    		echo $jsonData;
        }       
    }
    catch(Exception $e)
    {
        $data=array('status'=>'0', 'error'=>$e);

		$jsonData=json_encode($data);

		echo $jsonData;
    }
}

?>