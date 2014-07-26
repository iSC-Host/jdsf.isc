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
   
require_once '../includes/ajaxInit.inc.php';
require_once '../classes/Cunity_Messenger.class.php';

$data_back = json_decode (stripslashes($_REQUEST['json_data']), true);
$messenger = new Cunity_Messenger($cunity);

if($data_back['action']=="loadConversations"){
	$conversations=$messenger->getAllConversations($_SESSION['userid']);
	echo $cunity->returnJson(array("status"=>(int)($conversations!==false),"content"=>$conversations));
	exit;
}else if($data_back['action']=="deleteConversation"){
	echo $cunity->returnJson(array("status"=>(int)$messenger->deleteConversation($data_back['userid'],$data_back['cid'])));
}else if($data_back['action']=="loadConversation"){
	$cunityId = (int)mysql_real_escape_string($data_back['cid']);
	$user = (int)mysql_real_escape_string($data_back['conversation']);
	$remote= ($cunityId>0);				
	$c = $messenger->readConversation($_SESSION['userid'],$user,$remote,$cunityId,false);
	$c .= '<div class="clear"></div>';
	echo $cunity->returnJson(array("status"=>1,"content"=>$c));
	exit;
}else if($data_back['action']=="refreshConversation"){
	$cunityId = (int)mysql_real_escape_string($data_back['cid']);
	$user = (int)mysql_real_escape_string($data_back['conversation']);
	$remote= ($cunityId>0);		
	$c = $messenger->readConversation((int)$_SESSION['userid'],$user,$remote,$cunityId,true);
	$c .= '<div class="clear"></div>';
	echo $cunity->returnJson(array("status"=>1,"content"=>$c));
	exit;
}else if($data_back['action']=="sendMessage"){
	$message = mysql_real_escape_string($data_back['message']);
	if(isset($data_back['receiverData'])){
		$d = explode('-',mysql_real_escape_string($data_back['receiverData']));
		$receiver = $d[1];
		$receiverHash=$d[2];
		$cunityId = (int)$d[0];
	}else{
		$receiver = mysql_real_escape_string($data_back['conversation']);
		$receiverHash=getUserHash($receiver);
		$cunityId = (int)mysql_real_escape_string($data_back['cid']);
	}
	$remoteUser = ($cunityId>0) ? 'receiver' : 'none';	
	$msgData=$messenger->sendMessage($_SESSION['userid'], $receiver, $message, $cunityId,$remoteUser);        
	$_SESSION['max_message_id-'.$receiver] = $msgData['message_id'];
	if($msgData!==false){
		$msg = $messenger->returnOwnConversationMessage($msgData);
		echo $cunity->returnJson(array("status"=>1,"message"=>$msg,"cLink"=>'<a href="messages.php?c=conv&u='.$cunityId.'-'.$receiverHash.'">'.$lang['inbox_to_conversation'].'</a>'));
	}else
		echo $cunity->returnJson(array("status"=>0));
	exit;
}else if($data_back['action']=="deleteMessage"){
	$message_id = mysql_real_escape_string($data_back['message_id']);
	$result = $messenger->deleteMessage($message_id);
	echo $cunity->returnJson(array("status"=>(int)$result));
	exit;
}

?>