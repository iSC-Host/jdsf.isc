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

$cunity->getSaver()->login_required();
$cunity->getSaver()->module_power();

$tplModEngine = new Cunity_Template_Engine($cunity);
$tplModEngine->setPath('style/'.$_SESSION['style'].'/templates/messages/');

require_once 'Cunity_Messenger.class.php';
$messenger = new Cunity_Messenger($cunity);

$loggeduser=$_SESSION['userid'];
if((isset($_GET['c']) && $_GET['c'] == 'inbox') || !isset($_GET['c'])){	
	$_SESSION['inbox_counter']=0;
	$tplEngine->Assign('TITLE',$lang['inbox_title'].' - '.$lang['inbox_inbox']);
    $tplEngine->show();	
	$tplModEngine->Template('inbox');
	   $tplModEngine->Assign('STYLE', $_SESSION['style']);
       $tplModEngine->show();
}elseif(isset($_GET['c'])&&$_GET['c']=='conv'&&isset($_GET['u'])){
	$urlData = explode('-',$_GET['u']);
	if(count($urlData)>1&&(int)mysql_real_escape_string($urlData[0])>0&&(int)mysql_real_escape_string($urlData[0])!=$cunity->getcunityId()){
	    $cunityId = mysql_real_escape_string($urlData[0]);
		$remote=true;
		$user = getUserId(mysql_real_escape_string($urlData[1]),$remote,(int)$cunityId);
		$userhash = $urlData[1];		
		$remoteRes = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."connected_users AS u, ".$cunity->getConfig("db_prefix")."connected_cunities AS c WHERE u.cunityId = c.cunityId AND u.localid = '".$user."' AND u.cunityId = '".$cunityId."'") or die(mysql_error());
	    $cunityData = mysql_fetch_assoc($remoteRes);
	}else{
		$remote=false;
		if(count($urlData)>1){
            $user = getUserId(mysql_real_escape_string($urlData[1]));
		    $userhash = $urlData[1];
        }else{
            $user = getUserId(mysql_real_escape_string($urlData[0]));
		    $userhash = $urlData[0];
        }
		$cunityId = 0;
		$cunityData = array();
	}
					
	$username = getUserName((int)$user,$remote,(int)$cunityId);
	$tplEngine->Assign('TITLE',$lang['conversation'].' - '.$username);
    $tplEngine->show();
	$tplModEngine->Template('conversation_view');
		$tplModEngine->Assign('CONVERSATION_USERNAME',$username);
		$tplModEngine->Assign('with',$lang['with']);
		$tplModEngine->Assign('USERAVATAR',getSmallAvatar($user,40,$remote,$cunityId));
		
		$tplModEngine->Assign('USERHASH', $userhash);
		$tplModEngine->Assign('USER',$user);
		$tplModEngine->Assign('CUNITYID',$cunityId);
        
		$tplModEngine->show();
}
require('ov_foot.php');
ob_end_flush();
?>