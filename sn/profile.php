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

	if(!isset($_GET['getRemoteProfile'])&&$_GET['getRemoteProfile']!=1){				
		$cunity->getSaver()->login_required();	
	}    

    $type = 'profile_view'; // default
    $search = array();
    $replace = array();
    $link_add_as_friend='';
    $remoteProfile=false;     
    $msg = "";    

    if(isset($_GET['c']) && $_GET['c'] == 'edit'){ $type = 'profile_edit'; }
          
    if(strpos($_GET['user'],'-')!==false){
    	require_once 'classes/Cunity_Connector.class.php';
    	$hashData = explode('-', $_GET['user']);
    	$connector = new Cunity_Connector($cunity);
    	if($hashData[0]!=$connector->getcunityId()&&$hashData[0]!=0){
    		$profileData = $connector->getUserProfile($hashData[1],$hashData[0]);
	    	if($profileData==2){
	    		header("Location: index.php");
	    		exit;
	    	}
	    	define('OWN',false);    
	    	$remoteProfile=true;	
	    	$tplEngine->Template('profile_view');    	
	    	$tplEngine->Assign(array(
	            "REMOTE"=>1,
	            "USERDATA"=>base64_encode(json_encode($profileData['userData'])),
	    		"ANOTHERSCRIPT"=>'<script>$("document").ready(function(){$(".profile img").each(function(){$(this).attr("src","'.$profileData['userData']['cunityUrl'].'/"+$(this).attr("src"));});});</script>',
	    		"CUNITYID"=>$hashData[0]
	        ));        
	    	$tplEngine->showRemote($profileData['templateVars']);
	    	require('ov_foot.php');
			ob_end_flush();
			exit;    		 	
    	}else{
    		$_GET['user'] = $hashData[1];
    	}    	
    }                  

    if(isset($_GET['user']) && $_SESSION['userid']==getUserId($_GET['user']) || !isset($_GET['user']))
        define('OWN', true);
    else
        define('OWN', false);

    $ownId=$_SESSION['userid'];
    $cunityId=0;
    if(isset($_GET['getRemoteProfile'])&&$_GET['getRemoteProfile']==1){
    	require_once 'classes/Cunity_Connection_Responder.class.php';
    	$responder = new Cunity_Connection_Responder($cunity);    	
    	$_POST = $responder->getCryptor()->decryptParameters($_POST, $responder->getCryptor()->readPrivateKeyFromDatabase());
    	$responder->setRequestData($_POST);
    	$responder->setPublicKey($_POST['publicKey']);
    	$_SESSION['userid'] = 0;
    	$_SESSION['language'] = $_POST['language'];
    	$userid = getUserId($_POST['userhash']);
        $ownId=$_POST['ownId'];
        $cunityId=$_POST['cunityId'];
		$cunity->setLang($_POST['language']);
		$lang=array();
		$lang = $cunity->getLang();    	    		
    	$tplEngine->setRemote(true,$userid);
    	$tplEngine->setConnectionResponder($responder);
		$remoteProfile=true;
   	}else if(isset($_GET['user']))
        $userid = getUserId($_GET['user']);        
    else
        $userid = $_SESSION['userid'];
        
    if($cunity->getFriender()->getFriendshipStatus($ownId,$userid,$cunityId)==2){
        header("location: index.php");
        exit;
    }
    
    require_once 'Cunity_Profile.class.php';
    $profile = new Cunity_Profile($cunity,$userid);

    $data = array_merge($profile->getUserData(),$profile->getUserDetail());
        
    $privacy = getPrivacy($userid);        
    
    switch($type){
        case 'profile_view':
            require_once('includes/profile_view.php');
        break;
        
        case 'profile_edit':
            require_once('includes/profile_edit.php');
        break;
    }

require('ov_foot.php');
ob_end_flush();
?>