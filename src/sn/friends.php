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

$loggeduser=$_SESSION['userid'];
$loggednickname = $_SESSION['nickname'];
$title = $lang['friends_my_friends'];

if(isset($_GET['c']) && $_GET['c'] == 'invite'){
    if(isset($_POST['to'])){
        foreach($_POST['to'] AS $receiver){
            $res = $cunity->getDb()->query("SELECT COUNT(*),code FROM ".$cunity->getConfig("db_prefix")."invitation_codes WHERE email = '".mysql_real_escape_string($receiver)."' LIMIT 1");
            $data = mysql_fetch_assoc($res);
            if($data['COUNT(*)'] == 1){
                $code = $data['code'];
                $again = true;
            }else{
                $code = md5($receiver.$loggeduser.time()."code");
                $again = false;
            }
            if(strlen($_POST['message']) <= 500){
                $message = '<p>'.getUserName($_SESSION['userid']).' '.$lang['friends_invite_header'].'</p>';
                $message .= '<p>'.$_POST['message'].'</p>';
                $message .= '<p>'.$lang['friends_invite_mail_info'].'</p>';
                $cunity->getMailer()->sendmail($receiver,"",$_SESSION['nickname'].' '.$lang['friends_invite_subject'],$message.'<a href="'.$cunity->getSetting('url').'/register.php?code='.$code.'&m='.$receiver.'">'.$cunity->getSetting('url').'/register.php?code='.$code.'&m='.$receiver.'</a>',$_SESSION['email'],getUserName($_SESSION['userid']));
                $msg = newCunitySuccess($lang['friends_invite_mail_success']);

                if($again == false)
                    $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."invitation_codes (userid,nickname,code,email) VALUES ('$loggeduser','$loggednickname','$code','$receiver')");
            }else
                $error[] = 'long';

        }
        if(count($error) > 0){
            $msg = newCunityError($lang['friends_invite_message_long']);
        }
    }
    $tplEngine->Assign('TITLE', $lang['friends_invite_headline']);
    $tplEngine->Template('invite_friends');
        $tplEngine->Assign('MSG', $msg);
}elseif(isset($_GET['c']) && $_GET['c'] == 'requests'){
    $tplEngine->Assign('TITLE', $lang['friends_friends_requests']);
    $tplEngine->Template('friends_requests');


}else{
    if(!isset($_GET['user'])){
        $_GET['user'] = $_SESSION['userhash'];
        define('OWN_FRIENDS', true);

        $request = getReceivedFriendList($_SESSION['userid']);
        $requests = ' ('.(($request!==false)?count($request):0).')';
    }else
        define('OWN_FRIENDS', false);

    $res =$cunity->getDb()->query_assoc("SELECT `value` FROM ".$cunity->getConfig("db_prefix")."open".$cunity->getConfig("db_prefix")."settings WHERE `setting` = 'connected_success'");
    if($res['value']==0)
        $cunityconnected=false;
    else
        $cunityconnected=true;

    if(isset($_GET['q']) && $_GET['q'] != ""){
        define('SEARCH', true);
        $title = $lang['search_result_for'].': '.$_GET['q'];
    }else
    	define('SEARCH', false);

	$tplEngine->Assign('TITLE', $title);
	$tplEngine->Template('myfriends');
        $tplEngine->Assign('USER', $_GET['user']);

        $tplEngine->Assign('REQUESTS', $requests);
        $tplEngine->Assign('Q', $_GET['q']);
        $tplEngine->Assign('TITLE', $title);
        $tplEngine->Assign('cunityconnected', $cunityconnected);


}
require('ov_foot.php');
ob_end_flush();
?>