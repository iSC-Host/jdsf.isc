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
$userid = mysql_real_escape_string(getUserId($_REQUEST['userid']));

$myFriends = getFriendList($loggeduser, 1);
$blockedFriends = getFriendList($loggeduser, 2);
$sentRequests = getSendFriendList($loggeduser);
$receivedRequests = getReceivedFriendList($loggeduser);

if($data_back['action']=="openSearch"){
	try {
		$membersFound = "";
		$searchResult = $connector->searchUserOnServer($data_back['searchTerm']);				
		foreach($searchResult AS $userid => $userData){
		    if($userData['userhash']==$_SESSION['userhash']&&$userData['cunityId']==$connector->getcunityId()){
		      continue;
		    }else{
		    	if($userData['cunityId']==$connector->getcunityId())
		    		$remote = 0;
		    	else
		    		$remote = 1;
		    		
                if(isFriend($userData['localid'],$userData['cunityId']))
                    $buttons = '<div class="buttonset"><button icon="ui-icon-mail-open" class="jui-button" onclick="newMessage(\''.getUserName($userData['localid'],true,$userData['cunityId']).'\','.$userData['localid'].',\''.getUserHash($userData['localid'],true,$userData['cunityId']).'\','.$userData['cunityId'].');" text="0">'.$lang['friends_message_user'].'</button><button class="jui-button" onclick="blockFriend('.$userData['localid'].','.base64_encode(json_encode($userData)).',1,refreshFriends);" id="'.$userid.'" icon="ui-icon-closethick" text="0">'.$lang['friends_block_user'].'</button><button class="jui-button" onclick="deleteFriend('.$userData['localid'].',\''.base64_encode(json_encode($userData)).'\','.$remote.',refreshFriends);" icon="ui-icon-trash" text="0">'.$lang['friends_end_friendship'].'</button></div>';
                else
                    $buttons = '<div class="buttonset"><button class="jui-button"id="friendButton-'.$userData['localid'].'"  onclick="addasfriend('.$userData['localid'].',\''.base64_encode(json_encode($userData)).'\','.$remote.',addFriendButton);" icon="ui-icon-plusthick" text="1">'.$lang['friends_add_as_friend'].'</button><button icon="ui-icon-mail-open" class="jui-button" onclick="newMessage(\''.getUserName($userData['localid'],true,$userData['cunityId']).'\','.$userData['localid'].',\''.getUserHash($userData['localid'],true,$userData['cunityId']).'\','.$userData['cunityId'].');" text="0">'.$lang['friends_message_user'].'</button><button class="jui-button" onclick="blockFriend('.$userData['localid'].',\''.base64_encode(json_encode($userData)).'\','.$remote.',refreshFriends);" icon="ui-icon-closethick" text="0">'.$lang['friends_block_user'].'</button></div>';


    			$membersFound .= $cunity->getTemplateEngine()->createTemplate('friendList', array(
                    	'BUTTONS'=>$buttons,
                    	'USERHASH'=>$userData['cunityId'].'-'.$userData['userhash'],
                    	'AVATAR'=>$userData['cunityUrl'].'/files/_avatars/'.$userData['userhash'].'.jpg',
                    	'galleries_updated_on'=>"from Cunity ",
                    	'UPDATED'=>'"'.$userData['cunityName'].'"',
                    	'USERNAME'=>$userData['username'],
                		'USERID'=>$userid
    			));
            }				
		}
		
		if(count($searchResult)==0)
                $membersFound = newCunityError($lang['members_no_results']);         
		$data = array("status"=>1,"membersFound"=>$membersFound);
		echo $cunity->returnJson($data);
	}catch (Exception $e){
		echo $cunity->returnJson($data);
	}
}elseif($data_back['action']=="fieldSearch"){
    $friends = $cunity->getFriender()->getFriendList($_SESSION['userid']);
    foreach($friends AS $row)
        if(checkPrivacy($row['id'], $_SESSION['userid'], 'messaging'))
            $data[] = array("label"=>getUserName($row['id']),"img"=>getAvatarPath($row['id'],($row['cunityId']>0),$row['cunityId']),"id"=>$row['id'],"hash"=>getUserHash($row['id']),"cid"=>$row['cunityId']);
	$jsonData=json_encode($data);
	echo $jsonData;	
}else if ($data_back['action']=="instantSearch"){
	try {
		$queryString='
        SELECT
            DISTINCT '.$cunity->getConfig("db_prefix").'users.userid,'.$cunity->getConfig("db_prefix").'users_details.updated
        FROM
            '.$cunity->getConfig("db_prefix").'users
        LEFT JOIN
            '.$cunity->getConfig("db_prefix").'users_details
        ON
            '.$cunity->getConfig("db_prefix").'users.userid = '.$cunity->getConfig("db_prefix").'users_details.userid
        WHERE
            '.$cunity->getConfig("db_prefix").'users.nickname LIKE \'%'.$data_back['searchTerm'].'%\'
        OR
            '.$cunity->getConfig("db_prefix").'users_details.firstname LIKE \'%'.$data_back['searchTerm'].'%\'
        OR
            '.$cunity->getConfig("db_prefix").'users_details.lastname LIKE \'%'.$data_back['searchTerm'].'%\'
        OR
            '.$cunity->getConfig("db_prefix").'users.username LIKE \'%'.$data_back['searchTerm'].'%\'
        ORDER BY updated DESC';
	
    	$membersFound='';
    	
		$res = $cunity->getDb()->query($queryString);
		while($row = mysql_fetch_assoc($res)){
            if($row['userid'] != $_SESSION['userid']&&$cunity->getFriender()->getFriendshipStatus($_SESSION['userid'],$row['userid'])!=2&&checkPrivacy($row['userid'], $loggeduser, 'searching')){
                $userid = $row['userid'];
                
                if($cunity->getFriender()->getFriendshipStatus($_SESSION['userid'],$userid)===false)
                    $buttons = '<div class="buttonset"><button class="jui-button" id="friendButton-'.$userid.'" onclick="addasfriend('.$userid.',\'\',0,addFriendButton);" icon="ui-icon-plusthick" text="1">'.$lang['friends_add_as_friend'].'</button><button icon="ui-icon-mail-open" class="jui-button" onclick="newMessage(\''.getUserName($userid).'\','.$userid.',\''.getUserHash($userid).'\',0);" text="0">'.$lang['friends_message_user'].'</button><button class="jui-button" onclick="blockFriend('.$userid.',\'\',0,removeUser);" icon="ui-icon-closethick" text="0">'.$lang['friends_block_user'].'</button></div>';
                else if($cunity->getFriender()->getFriendshipStatus($_SESSION['userid'],$userid)==0)
                    $buttons = '<div class="buttonset"><button class="jui-button" id="friendButton-'.$userid.'" icon="ui-icon-clock" text="1">'.$lang['profile_view_sent_request'].'</button><button icon="ui-icon-mail-open" class="jui-button" onclick="newMessage(\''.getUserName($userid).'\',\''.getUserHash($userid).'\',0);" text="0">'.$lang['friends_message_user'].'</button><button class="jui-button" onclick="blockFriend('.$userid.',\'\',0,removeUser);" icon="ui-icon-closethick" text="0">'.$lang['friends_block_user'].'</button></div>';
                else if($cunity->getFriender()->getFriendshipStatus($_SESSION['userid'],$userid)==3)
                    $buttons = '<div class="buttonset"><button class="jui-button" id="friendButton-'.$userid.'" onclick="respondRequest('.$userid.',\'\',0,addFriendButton);" icon="ui-icon-help" text="1">'.$lang['profile_view_respond_request'].'</button><button icon="ui-icon-mail-open" class="jui-button" onclick="newMessage(\''.getUserName($userid).'\','.$userid.',\''.getUserHash($userid).'\',0);" text="0">'.$lang['friends_message_user'].'</button><button class="jui-button" onclick="blockFriend('.$userid.',\'\',0,removeUser);" icon="ui-icon-closethick" text="0">'.$lang['friends_block_user'].'</button></div>';
                else if($cunity->getFriender()->isFriend($_SESSION['userid'],$userid))
                    $buttons = '<div class="buttonset"><button class="jui-button" onclick="newMessage(\''.getUserName($userid).'\','.$userid.',\''.getUserHash($userid).'\',0);" icon="ui-icon-mail-open" text="0">'.$lang['friends_message_user'].'</button><button class="jui-button" onclick="blockFriend('.$userid.',\'\',0,removeUser);" icon="ui-icon-closethick" text="0">'.$lang['friends_block_user'].'</button><button class="jui-button" onclick="deleteFriend('.$userid.',\'\',0,removeUser);" icon="ui-icon-trash" text="0">'.$lang['friends_end_friendship'].'</button></div>';
                $membersFound .= $cunity->getTemplateEngine()->createTemplate('friendList', array(
                	'BUTTONS'=>$buttons,
                	'USERHASH'=>getUserHash($userid),
                	'AVATAR'=>getAvatarPath($userid),
                	'galleries_updated_on'=>$lang['galleries_updated_on'],
                	'UPDATED'=>showDate("date_time",$row['updated']),
                	'USERNAME'=>getUserName($userid),
                	'USERID'=>$userid
                ));            
            }
		}
		if(mysql_num_rows($res)==0)
            $membersFound = newCunityError($lang['members_no_results']);
		$data=array('membersFound'=>$membersFound,'status'=>1);
		echo $cunity->returnJson($data);
	}catch(Exception $e){
		echo $cunity->returnJson(array('status'=>0));
	}
}elseif ($data_back['c']=="myFriends" ){
    if(isset($data_back['userid']) && getUserId($data_back['userid']) != $_SESSION['userid'])
        $loggeduser = getUserId($data_back['userid']);
    else
        $loggeduser = $_SESSION['userid'];
    $friends = $cunity->getFriender()->getFriendList($loggeduser);
	if(count($friends)>0){
        foreach($friends AS $row){
            $userid=$row['id'];
			if($row['cunityId']>0&&$row['cunityId']!=$cunity->getcunityId()){
                $cunityId = $row['cunityId'];
                $cunityTplId = $cunityId.'-'; 
                $subHeader = $lang['friends_from_cunity'].'&nbsp;"'.getCunityName($row['cunityId']).'"';
            }else{
                $cunityId = 0;
                $cunityTplId = "";
                $userRes = $cunity->getDb()->query("SELECT updated FROM ".$cunity->getConfig("db_prefix")."users_details WHERE userid = ".$userid);
                $userData = mysql_fetch_assoc($userRes);
                $subHeader=showDate("date_time",$userData['updated']);
                $fromcunity = "";
                $remote = false;
            }
            
            if(!isset($data_back['target'])|| (isset($data_back['target']) && $data_back['target'] != 'sidebar')){            	                                   
                $messages_html_rows .= $cunity->getTemplateEngine()->createTemplate("friendList", array(
                	'BUTTONS'=>'<div class="buttonset"><button icon="ui-icon-mail-open" class="jui-button" onclick="newMessage(\''.getUserName($userid,$remote,$cunityId).'\','.$userid.',\''.getUserHash($userid,$remote,$cunityId).'\','.$cunityId.');" text="0">'.$lang['friends_message_user'].'</button><button class="jui-button" icon="ui-icon-closethick" onclick="blockFriend('.$userid.',\''.$ud.'\','.(int)$remote.',removeUser)" text="0">'.$lang['friends_block_user'].'</button><button class="jui-button" onclick="deleteFriend('.$userid.',\''.$ud.'\','.(int)$remote.',removeUser);" icon="ui-icon-trash" text="0">'.$lang['friends_end_friendship'].'</button></div>',
                	'USERHASH'=>getUserHash($userid,$remote,$cunityId),
                	'AVATAR'=>getAvatarPath($userid,$remote,$cunityId),
                	'galleries_updated_on'=>$lang['galleries_updated_on'],
                	'UPDATED'=>$subHeader,
                	'USERNAME'=>getUserName($userid,$remote,$cunityId),
                	'CUNITYID'=>$cunityTplId,
                	'USERID'=>$userid
                ));
            }else{
                $messages_html_rows .= '<div style="padding: 3px 0px; border-bottom: 1px dashed #ccc">';
                $messages_html_rows .= '<a href="profile.php?user='.$cunityTplId.getUserHash($userid,$remote,$cunityId).'" style="display: inline-block; float: left;">'.getSmallAvatar($userid,40,$remote,$cunityId).'</a>';
                $messages_html_rows .= '<div style="display: inline-block; float: left;">';
                $messages_html_rows .= '<a href="profile.php?user='.$cunityTplId.getUserHash($userid,$remote,$cunityId).'" style="font-weight: bold; display: block;">'.getUserName($userid,$remote,$cunityId).'</a>';
                if(!$cunity->getFriender()->isFriend($userid,$cunityId))
                    $messages_html_rows .= '<a href="javascript:addasfriend('.$userData['localid'].',\''.base64_encode(json_encode($userData)).'\','.$remote.',addFriendButton);" style="font-size: 12px; margin-top: 9px; color: #000000; display: block;">'.$lang['friends_add_as_friend'].'</a>';
                $messages_html_rows .= '</div>';
                $messages_html_rows .= '<div class="clear"></div></div>';
            }
		}
	}elseif($data_back['target']!='sidebar')
		$messages_html_rows=newCunityError($lang['friends_no_friends']);

	$data=array('messages'=>$messages_html_rows,'count'=>count($friends));

	echo $cunity->returnJson($data);
}elseif ($data_back['c'] == "myRequests" ){
	$q = "SELECT * FROM ".$cunity->getConfig("db_prefix")."friendships WHERE receiver = ".$_SESSION['userid']." AND status = 0 AND (remote = 'sender' OR remote IS NULL)";
	
    $res = $cunity->getDb()->query($q);
    if(mysql_num_rows($res)>0){
    	$messages_html_rows = "";
    	while($row = mysql_fetch_assoc($res)){
   			if($row['sender']==$loggeduser)
			    $user = $row['receiver'];
			else
			    $user = $row['sender'];

                $userRes = $cunity->getDb()->query("SELECT town,DATE_FORMAT(updated, '".$_SESSION['date']['mysql']['date']."') as updated FROM ".$cunity->getConfig("db_prefix")."users_details WHERE userid = '".$user."'");
                $userData = mysql_fetch_assoc($userRes);
                if($row['cunityId']!=NULL&&$row['cunityId']>0){
                    $cunityId = $row['cunityId'];
                    $cunityTplId = $cunityId.'-'; 
                    $fromcunity = '<br />'.$lang['friends_from_cunity'].'&nbsp;"'.getCunityName($row['cunityId']).'"';
                    $remote = true;
                    $remoteRes = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."connected_users AS u, ".$cunity->getConfig("db_prefix")."connected_cunities AS c WHERE u.cunityId = c.cunityId AND u.localid = '".$user."'");
                    $remoteData = mysql_fetch_assoc($remoteRes);
                    $rData=base64_encode(json_encode($remoteData));
                }                    
                else{
                    $cunityId = 0;
                    $cunityTplId = "";
                    $fromcunity = "";
                    $remote = false;
                    $remoteData=array();
                    $remoteData['cunityUrl'] = "";
                    $rData="";
                }   

                $buttons = '<div class="main_list_photos2" style="padding: 5px 0;width:190px"><div class="buttonset"><button class="jui-button" onclick="respondRequest('.$user.',\''.$rData.'\','.(int)$remote.',removeUser);" id="friendButton-'.$user.'" icon="ui-icon-help" text="1" id="'.$user.'">'.$lang['profile_view_respond_request'].'</button><button icon="ui-icon-mail-open" class="jui-button" onclick="newMessage(\''.getUserName($user,$remote,$cunityId).'\','.$user.',\''.getUserHash($user,$remote,$cunityId).'\','.$cunityId.');" text="0">'.$lang['friends_message_user'].'</button><button class="jui-button" onclick="blockFriend('.$user.',\''.$rData.'\','.(int)$remote.',removeUser)" icon="ui-icon-closethick" text="0">'.$lang['friends_block_user'].'</button></div></div>';
                $messages_html_rows .= $cunity->getTemplateEngine()->createTemplate('friendList', array(
                	'BUTTONS2'=>$buttons,
                	'USERHASH'=>getUserHash($user,$remote,$cunityId),
                	'AVATAR'=>getAvatarPath($user,80,$remote,$cunityId),
                	'galleries_updated_on'=>$lang['galleries_updated_on'],
                	'UPDATED'=>$userData['updated'].$fromcunity,
                	'USERNAME'=>getUserName($user,$remote,$cunityId),
                	'CUNITYID'=>$cunityTplId,
                	'USERID'=>$user
                ));            
		}
        $messages_html_rows .= newCunityError($lang['friends_no_requests'],false);
    }else
        $messages_html_rows = newCunityError($lang['friends_no_requests']);
    $data=array('messages'=>$messages_html_rows);
	echo $cunity->returnJson($data);
}elseif($data_back['c']=="loadonlinefriends"){
    if($_SESSION['chatonline']==1){
    	if(!isset($_SESSION['chat_list_count']))
    		$_SESSION['chat_list_count']=10;
        $friendslist = array();
        if(isset($data_back['do'])&&$data_back['do']=="all"){
        	$friendslist = who_is_online();
        	$_SESSION['chat_list_count'] = $friendslist['count'];
        }else
    		$friendslist = who_is_online($_SESSION['chat_list_count']);
    	$friendscount= 'Chat ('.$friendslist['count'].')';
    	if($friendslist['count'] > 10)
    	    $friendsmore = '<button onclick="showAllChat()" class="jui-button">'.$lang['friends_more_online_friends'].'</button>';
    	$data=array('onlinefriendslist'=>$friendslist['list'],'friendscount'=>$friendscount,'statusChange'=>$lang['sidebar_go_offline'],'online'=>true,'friendsmore'=>$friendsmore,'status'=>1);
    }else
        $data=array('onlinefriendslist'=>"",'friendscount'=>$lang['sidebar_offline'],'statusChange'=>$lang['sidebar_go_online'],'online'=>false,'friendsmore'=>$friendsmore,'status'=>1);    
	echo $cunity->returnJson($data);
}elseif($data_back['c']=="changeChatStatus"){
    if($_SESSION['chatonline']==1)
        $r = changeChatStatus($_SESSION['userid'],0);
    else
        $r = changeChatStatus($_SESSION['userid'],1);
    $data=array("status"=>(int)$r);
	echo $cunity->returnJson($data);
}elseif($data_back['c'] == 'add_friend'){
	$userData=array();
	if((bool)$data_back['remote'])
		$userData= json_decode(base64_decode($data_back['userData']),true);
    else
        $userData = array();						
	echo $cunity->returnJson(array(
		"status"=>(int)$cunity->getFriender()->sendFriendRequest($_SESSION['userid'],$data_back['userid'],(bool)$data_back['remote'],(int)$userData['cunityId'],$userData),
		"newText"=>$lang['profile_view_sent_request']
	));    
}elseif($data_back['c'] == 'block_friend'){
	$userData=array();
	if((bool)$data_back['remote'])
		$userData= json_decode(base64_decode($data_back['userData']),true);
	echo $cunity->returnJson(array(
	"status"=>(int)$cunity->getFriender()->blockFriend($_SESSION['userid'],$data_back['userid'],(bool)$data_back['remote'],(int)$userData['cunityId'],$userData)
	));	
}elseif($data_back['c'] == 'delete_friend'){
	$userData=array();
	if((bool)$data_back['remote'])
		$userData= json_decode(base64_decode($data_back['userData']),true);
	echo $cunity->returnJson(array(
		"status"=>(int)$cunity->getFriender()->deleteFriend($_SESSION['userid'],$data_back['userid'],(bool)$data_back['remote'],(int)$userData['cunityId']),
		"newText"=>$lang['friends_add_as_friend']
	));	
}
elseif($data_back['c'] == 'confirm_request')
{
	$userData=array();
	if((bool)$data_back['remote'])
		$userData= json_decode(base64_decode($data_back['userData']),true);			
	echo $cunity->returnJson(array(
		"status"=>(int)$cunity->getFriender()->confirmFriendRequest($data_back['userid'],$_SESSION['userid'],(bool)$data_back['remote'],(int)$userData['cunityId'])
	));    
}
elseif($data_back['c'] == 'ignore_request')
{
	$userData=array();
	if((bool)$data_back['remote'])
		$userData= json_decode(base64_decode($data_back['userData']),true);
	echo $cunity->returnJson(array(
		"status"=>(int)$cunity->getFriender()->deleteFriend($_SESSION['userid'],$data_back['userid'],(bool)$data_back['remote'],(int)$userData['cunityId'])
	));    
}
elseif($data_back['c'] == 'count_blocked')
{
    if($res = $cunity->getDb()->query("SELECT COUNT(*) FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (blocker = 'sender' AND sender = '".$_SESSION['userid']."') OR (blocker = 'receiver' AND receiver = '".$_SESSION['userid']."') AND status = 2")){
        $c = mysql_fetch_assoc($res);
        $data = array('status'=>1, 'count'=>$c['COUNT(*)']);
        $cunity->returnJson($data);
    }else{
		$data = array('status'=>0, 'error'=>$lang['global_error']);
        $cunity->returnJson($data);
	}
}
elseif($data_back['c'] == 'get_add_text')
{
	if(!isset($data_back['userData'])||$data_back['userData']==""){
		$userid = $data_back['userid'];
		$username = getUserName($userid);
    	$avatar = getAvatarPath($userid);    
	}else{
		$data_back['userData'] = json_decode(base64_decode($data_back['userData']),true);		
		$userid = $data_back['userid'];
		$username = $data_back['userData']['username'];		
		$avatar = $data_back['userData']['cunityUrl'].'/files/_avatars/'.$data_back['userData']['userhash'].'.jpg';
	}       
    $text = '
    <h3 style="text-align: left; margin: 3px 2px;">'.$username." ".$lang['friends_as_friend'].'</h3>
    <div style="width: 520px;">

        <img src="'.$avatar.'" id="profile_img" style="float: left; max-height: 200px; max-width: 200px;"/>
        <div style="float: left; width: 300px; text-align: left; margin-left: 10px;" class="add_friend_info">
            '.$lang['friends_add_info'].'
        </div>
        <div class="clear">
        </div>
    </div>
    ';
    $data = array('status'=>1, 'text'=>$text);
    echo $cunity->returnJson($data);
}
elseif($data_back['c'] == 'get_block_text')
{
	if(!isset($data_back['userData'])||$data_back['userData']==""){
		$userid = $data_back['userid'];
		$username = getUserName($userid);
    	$avatar = getAvatarPath($userid);    
	}else{
		$data_back['userData'] = json_decode(base64_decode($data_back['userData']),true);
		$userid = $data_back['userid'];
		$username = $data_back['userData']['username'];		
		$avatar = $data_back['userData']['cunityUrl'].'/files/_avatars/'.$data_back['userData']['userhash'].'.jpg';
	}    
    $text = '
    <h3 style="text-align: left; margin: 3px 2px;">'.$username." ".$lang['friends_block_friend_header'].'</h3>
    <div style="width: 520px;">

        <img src="'.$avatar.'" id="profile_img" style="float: left; max-height: 200px; max-width: 200px;"/>
        <div style="float: left; width: 300px; text-align: left; margin-left: 10px;" class="add_friend_info">
            '.$lang['friends_block_info'].'
        </div>
        <div class="clear">
        </div>
    </div>
    ';
    $data = array('status'=>1, 'text'=>$text);
    echo $cunity->returnJson($data);
}
elseif($data_back['c'] == 'get_delete_text')
{
	if(!isset($data_back['userData'])||$data_back['userData']==""){
		$userid = $data_back['userid'];
		$username = getUserName($userid);
    	$avatar = getAvatarPath($userid);    
	}else{
		$data_back['userData'] = json_decode(base64_decode($data_back['userData']),true);
		$userid = $data_back['userid'];
		$username = $data_back['userData']['username'];		
		$avatar = $data_back['userData']['cunityUrl'].'/files/_avatars/'.$data_back['userData']['userhash'].'.jpg';
	}    
    $text = '
    <h3 style="text-align: left; margin: 3px 2px;">'.$lang['friends_delete_friend_header_1'].' '.$username." ".$lang['friends_delete_friend_header_2'].'</h3>
    <div style="width: 520px;">

        <img src="'.$avatar.'" id="profile_img" style="float: left; max-height: 200px; max-width: 200px;"/>
        <div style="float: left; width: 300px; text-align: left; margin-left: 10px;" class="add_friend_info">
            '.$lang['friends_delete_info'].' '.$username.' '.$lang['friends_delete_info_2'].'
        </div>
        <div class="clear">
        </div>
    </div>
    ';
    $data = array('status'=>1, 'text'=>$text);
    echo $cunity->returnJson($data);
}
elseif($data_back['c'] == 'get_unblock_text')
{
	if(!isset($data_back['userData'])||$data_back['userData']==""){
		$userid = $data_back['userid'];
		$username = getUserName($userid);
    	$avatar = getAvatarPath($userid);    
	}else{
		$data_back['userData'] = json_decode(base64_decode($data_back['userData']),true);
		$userid = $data_back['userid'];
		$username = $data_back['userData']['username'];		
		$avatar = $data_back['userData']['cunityUrl'].'/files/_avatars/'.$data_back['userData']['userhash'].'.jpg';
	}    
    $text = '
    <h3 style="text-align: left; margin: 3px 2px;">'.$username." ".$lang['friends_unblock_friend_header'].'</h3>
    <div style="width: 520px;">

        <img src="'.$avatar.'" id="profile_img" style="float: left; max-height: 200px; max-width: 200px;"/>
        <div style="float: left; width: 300px; text-align: left; margin-left: 10px;" class="add_friend_info">
            '.$lang['friends_unblock_info'].'
        </div>
        <div class="clear">
        </div>
    </div>
    ';
    $data = array('status'=>1, 'text'=>$text);
    echo $cunity->returnJson($data);
}

elseif($data_back['c'] == 'get_request_text')
{
	if(!isset($data_back['userData'])||$data_back['userData']==""){
		$userid = $data_back['userid'];
		$username = getUserName($userid);
    	$avatar = getAvatarPath($userid);    
	}else{
		$data_back['userData'] = json_decode(base64_decode($data_back['userData']),true);
		$userid = $data_back['userid'];
		$username = $data_back['userData']['username'];		
		$avatar = $data_back['userData']['cunityUrl'].'/files/_avatars/'.$data_back['userData']['userhash'].'.jpg';
	}    
    $text = '
    <h3 style="text-align: left; margin: 3px 2px;">'.$lang['friends_confirm'].' '.$username." ".$lang['friends_confirm_as_friend'].'</h3>
    <div style="width: 520px;">

        <img src="'.$avatar.'" id="profile_img" style="float: left; max-height: 200px; max-width: 200px;"/>
        <div style="float: left; width: 300px; text-align: left; margin-left: 10px;" class="add_friend_info">
            '.$lang['friends_respond_info'].'
        </div>
        <div class="clear">
        </div>
    </div>
    ';
    $data = array('status'=>1, 'text'=>$text);
    echo $cunity->returnJson($data);    
}
?>