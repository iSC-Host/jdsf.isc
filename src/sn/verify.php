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

ob_start("ob_gzhandler");
require('ov_head.php');

$search = 'MSG';
$replace = '';

$tplEngine->Assign('TITLE', $cunity->getSetting('name'));        

// Verify
if(isset($_GET['k']) && !empty($_GET['k']) && !isset($_GET['activate'])) {
	$vkey = explode('-', $_GET['k'], 2);
	$userid = $vkey[0];
	$vkey = $vkey[1];

	$res = $cunity->getDb()->query("SELECT vkey,groupid,verif_mail FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = ".(int)mysql_real_escape_string($userid)." AND vkey = '".$vkey."' LIMIT 1");

    if(mysql_num_rows($res)==0)
    	$replace = newCunityError($lang['verify_not_verified']);
    else{
    	$data = mysql_fetch_assoc($res);
    	if($cunity->getSetting('registration_method') == 'activate'){
    		$res = $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET verif_mail = 1 WHERE ".$cunity->getConfig("db_prefix")."users.userid = '".mysql_real_escape_string($userid)."' LIMIT 1");
			if($res){
				if($data['groupid']==3)
					$replace = newCunitySuccess($lang['verify_success']);
				else
					$replace = newCunitySuccess($lang['verify_activate_success']);
			}else
				$replace = newCunityError($lang['verify_failure_db']);							    
    	}else{
    		$res = $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET ".$cunity->getConfig("db_prefix")."users.groupid = 3 WHERE ".$cunity->getConfig("db_prefix")."users.userid = '".mysql_real_escape_string($userid)."' LIMIT 1");
			if($res)
				$replace = newCunitySuccess($lang['verify_success']);
			else
				$replace = newCunityError($lang['verify_failure_db']);
    	}    	        
    }    	
}elseif(isset($_GET['activate'])){	            
    if(isset($_GET['k'])){                        
        $cunity->getSaver()->login_required();
        	                                 
        $vkey = explode('-', $_GET['k'], 2);
    	$userid = $vkey[0];
    	$vkey = $vkey[1];
    
    	$res = $cunity->getDb()->query("SELECT vkey,verif_mail,mail FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = ".(int)mysql_real_escape_string($userid)." AND vkey = '".$vkey."' AND groupid = 7 LIMIT 1");
            
    	if(mysql_num_rows($res) == 1){
    		$data = mysql_fetch_assoc($res);    
    		$res = $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET ".$cunity->getConfig("db_prefix")."users.groupid = 3 WHERE ".$cunity->getConfig("db_prefix")."users.userid = '".mysql_real_escape_string($userid)."' LIMIT 1");
    		if($res){
				$cunity->getNotifier()->sendNotification($data['mail'],getUserName($userid),$lang['verify_account_has_been_activated_subj'],'<p>'.$lang['verify_account_has_been_activated'].'</p>');
                $replace = newCunitySuccess($lang['verify_activated_user'].' "'.getUserName($userid).'" '.$lang['verify_activated_with_mail'].' "'.$data['mail'].'" '.$lang['verify_activated_done']);                                                            
            }else
    			$replace = newCunityError($lang['verify_failure_db']);
    	}else
    		$replace = newCunityError($lang['verify_failure_code']);    		
	}                
}else
    page_not_found();

$tplEngine->Template('verify');

$tplEngine->Assign($search, $replace);



require('ov_foot.php');
ob_end_flush();
?>