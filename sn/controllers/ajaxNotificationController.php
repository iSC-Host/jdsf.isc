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

if($data_back['action'] == "loadNotifications")
{
    $back = "";
    $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."notifications WHERE receiver_id = '".$_SESSION['userid']."' ORDER BY time DESC LIMIT 5");
    echo mysql_error();
    $i = 0;
    $newest = array();
    while($n = mysql_fetch_assoc($res))
    {        
        $not_data = json_decode (stripslashes($n['message']), true);
        if($n['read'] == '1')
        {
            $back .= '<div class="notification" id="'.$n['notification_id'].'">';
        }   
        else
        {
            $back .= '<div class="notification_unread" id="'.$n['notification_id'].'">';
        }
        $back .= '<h5 class="notification_msg" id="'.$n['notification_id'].'"><a href="'.$not_data['link'].'">'.$not_data['message'].'</a></h5>';
        $back .= '<span class="notification_time">'.date($_SESSION['date']['php']['date_time'], strtotime($n['time'])).'</span>';
        $back .= '</div>';
        if($i < mysql_num_rows($res))
            $back .= '<hr />';
        $i++;
    }
    $n = null;
    $not_data = null;
    $res = null;
    $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."notifications WHERE receiver_id = '".$_SESSION['userid']."' AND `read` = 0 AND  TIMEDIFF(NOW(), time) < SEC_TO_TIME(35) ORDER BY time DESC LIMIT 1");        
    if(mysql_num_rows($res)>0)
    {
        $n = mysql_fetch_assoc($res);
        $not_data = json_decode (stripslashes($n['message']), true);
        $newest[] = '<a href="'.$not_data['link'].'" onclick="return NotificationRead(\''.$n['notification_id'].'\')">'.$not_data['message'].'</a>';
    }               
    $res1 = $cunity->getDb()->query("SELECT `read` FROM ".$cunity->getConfig("db_prefix")."notifications WHERE `read` = 0 AND receiver_id = '".$_SESSION['userid']."'");
    $data=array('notifications'=>$back,'status'=>mysql_num_rows($res1), 'newest'=>$newest);

	$jsonData=json_encode($data);

	echo $jsonData;
}
elseif($data_back['action'] == 'getFullNotifications')
{
        $back = "";
    $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."notifications WHERE receiver_id = '".$_SESSION['userid']."' ORDER BY time DESC LIMIT 5");
    echo mysql_error();
    $i = 0;
    $newest = array();
    while($data = mysql_fetch_assoc($res))
    {
        $mdata = json_decode (stripslashes($data['message']), true);        
        $img = getAvatarPath($mdata['from']);
        if($n['read'] == '1')
        {
            $class = '';
        }
        else
        {
            $class = 'notification_unread';
        }
        $back .= '<div class="main_list_wrap '.$class.'" style="height: 45px;">';
        $back .= '<div class="main_list_img_wrap" style="width: 45px;">';
        $back .= '<a href="profile.php?user='.getUserHash($mdata['from']).'">';
        $back .= '<img src="'.$img.'" class="main_list_img" style="width: 40px; height: 40px; padding: 3px;"/>';
        $back .= '</a>';
        $back .= '</div>';
        $back .= '<div class="main_list_cont" style="vertical-align: top;">';
        $back .= '<div class="main_list_small_header">';
        $back .= '<a href="'.$mdata['link'].'">';
        $back .= $mdata['message'];
        $back .= '</a>';
        $back .= '</div>';
        $back .= '<span class="main_list_updated">';
        $back .= date($_SESSION['date']['php']['date_time'], strtotime($data['time']));
        $back .= '</span>';
        $back .= '</div>';
        $back .= '<div class="clear"></div>';
        $back .= '</div>';
    }
    $n = null;
    $not_data = null;
    $res = null;
    $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."notifications WHERE receiver_id = '".$_SESSION['userid']."' AND `read` = 0 AND  TIMEDIFF(NOW(), time) < SEC_TO_TIME(35) ORDER BY time DESC LIMIT 1");
    if(mysql_num_rows($res)>0)
    {
        $n = mysql_fetch_assoc($res);
        $not_data = json_decode (stripslashes($n['message']), true);
        $newest[] = '<a href="'.$not_data['link'].'" onclick="return NotificationRead(\''.$n['notification_id'].'\')">'.$not_data['message'].'</a>';
    }
    $res1 = $cunity->getDb()->query("SELECT `read` FROM ".$cunity->getConfig("db_prefix")."notifications WHERE `read` = 0 AND receiver_id = '".$_SESSION['userid']."'");
    $data=array('notifications'=>$back,'status'=>mysql_num_rows($res1), 'newest'=>$newest);

	$jsonData=json_encode($data);

	echo $jsonData;
}
elseif($data_back['action'] == 'readNotification')
{
    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."notifications SET `read` = '1' WHERE notification_id = '".$data_back['id']."'");
}
?>