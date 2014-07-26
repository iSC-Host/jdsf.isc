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

// Setup
require('modules/forums/includes/functions.php');

$tplEngine->Assign('TITLE', 'Forum - '.$settings['name']);
$tplEngine->show();
$tplModEngine = new Cunity_Template_Engine($cunity);
$tplModEngine->setPath('style/'.$_SESSION['style'].'/templates/forums/');


// parameters
$forum_id = -1;
$topic_id = -1;
$post_id = -1;
$page = -1;

if(isset($_GET['fid']))
$forum_id = (int)$_GET['fid'];

if(isset($_GET['tid']))
$topic_id = (int)$_GET['tid'];

if(isset($_GET['pid']))
$post_id = (int)$_GET['pid'];

if(isset($_GET['page']))
$page = (int)$_GET['page'];

// options
$newthread = false;
$answer = false;
$edit = false;
$preview = false;
$show_ip = false;
$deletepost = false;
$quote = false;

if(isset($_GET['newthread']))
$newthread = true;
elseif(isset($_GET['answer']))
$answer = true;
elseif(isset($_GET['edit']))
$edit = true;

if(isset($_REQUEST['preview']))
$preview = true;

if(isset($_GET['deletepost']))
$deletepost = true;

if(isset($_GET['show_ip']))
$show_ip = true;

if(isset($_GET['quote']))
$quote = true;

// Get unread Posts
$res = $cunity->getDb()->query("SELECT last_visit FROM ".$cunity->getConfig("db_prefix")."forums_users WHERE user_id=".mysql_real_escape_string($_SESSION['userid']));
if(mysql_num_rows($res) > 0) {
	$lv = mysql_fetch_assoc($res);
	$lv = (int)$lv['last_visit'];
	$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."forums_users SET last_visit='".time()."' WHERE user_id=".mysql_real_escape_string($_SESSION['userid']));
}
else {
	$lv = 0;
	$cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."forums_users (user_id, last_visit) VALUES (".mysql_real_escape_string($_SESSION['userid']).", '".time()."')");
}

if($cunity->getSaver()->admin() && isset($_GET['add_board']))
{
	$cunity->getDb()->query("INSERT INTO `".$cunity->getConfig("db_prefix")."forums_boards` (`board_id` ,`name` ,`description` ,`guest_readable` ,`guest_postable` ,`cat_id` ,`position` ,`flag`)VALUES (NULL , '".basic_parse($_GET['name'])."', '".basic_parse($_GET['description'])."', '0', '0', '".mysql_real_escape_string($_GET['add_board'])."', '0', '0');");
    header("Location: forums.php");
	exit;	
}
else if($cunity->getSaver()->admin() && isset($_GET['delete_board']))
{
	$res = $cunity->getDb()->query("SELECT flag FROM ".$cunity->getConfig("db_prefix")."forums_boards where board_id=".$_GET['delete_board']);
	$data = mysql_fetch_assoc($res);
	if($data['flag']==1)
        $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_boards WHERE cat_id = ".$_GET['delete_board']);
    else{
        $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_watch where topic_id IN (select topic_id from ".$cunity->getConfig("db_prefix")."forums_topics where board_id=".$row['board_id'].")");
		$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_topics where board_id=".$row['board_id']);
		$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_posts where board_id=".$row['board_id']);
		$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_unread where board_id=".$row['board_id']);
    }
    
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_boards where board_id=".$_GET['delete_board']);
	header("Location: forums.php");
	exit;
}
else if($cunity->getSaver()->admin() && isset($_GET['edit_board']))
{
	$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."forums_boards SET name= '".basic_parse($_GET['name'])."', description='".basic_parse($_GET['description'])."' WHERE board_id=".mysql_real_escape_string($_GET['edit_board']));
	header("Location: forums.php");
	exit;	
}
else if($cunity->getSaver()->admin() && isset($_GET['add_new_forum']))
{
	$cunity->getDb()->query("INSERT INTO `".$cunity->getConfig("db_prefix")."forums_boards` (`board_id` ,`name` ,`description` ,`guest_readable` ,`guest_postable` ,`cat_id` ,`position` ,`flag`)VALUES (NULL , '".basic_parse($_GET['forum_name'])."', '".basic_parse($_GET['forum_description'])."', '0', '0', '".mysql_real_escape_string($_GET['board_id'])."', '0', '1');");
    header("Location: forums.php");
	exit;
}
else if($cunity->getSaver()->admin() && isset($_GET['delete_thread']))
{    
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_topics WHERE topic_id=".$_GET['delete_thread']);
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_posts WHERE topic_id=".$_GET['delete_thread']);
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_unread WHERE topic_id=".$_GET['delete_thread']);
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_watch WHERE topic_id=".$_GET['delete_thread']);
	header("Location: forums.php");
	exit;
}
else if($deletepost)
{
    $res = $cunity->getDb()->query("SELECT user_id FROM ".$cunity->getConfig("db_prefix")."forums_posts WHERE post_id = '".mysql_real_escape_string($post_id)."'");
    $a = mysql_fetch_assoc($res);
    if($cunity->getSaver()->admin() || $a['user_id'] == $_SESSION['userid'])
    {
        $res = $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_posts WHERE post_id='".$post_id."' LIMIT 1");
        header('Location: forums.php?fid='.$forum_id.'&tid='.$topic_id);
        exit;
    }    
}

if((($answer || $edit) && $topic_id != -1 && $forum_id != -1) || ($newthread && $forum_id != -1)) {
	if($_SESSION['lastpost'] > time() - 30) {
		$tplModEngine->Template('message');
		    
    		$tplModEngine->Assign('MSG',newCunityError($lang['forums_delay']));
    		$tplModEngine->Assign('forum_back',$lang['forums_back']);
    		$tplModEngine->Assign('TO', 'forums.php');
    		
            $tplModEngine->Assign('edit_profile',$lang['profile_view_profile_edit']);
            $tplModEngine->Assign('edit_img',$lang['profile_edit_img_edit']);
            

    		$tplModEngine->show();
	}
	elseif(isset($_REQUEST['send']) && strlen(trim($_REQUEST['subject'])) >= 3 && strlen(trim($_REQUEST['message'])) >= 3) {
		// Subject
		if(!$newthread) {
			$topic = mysql_fetch_assoc($cunity->getDb()->query("SELECT subject, closed FROM ".$cunity->getConfig("db_prefix")."forums_topics WHERE topic_id=".$topic_id." LIMIT 1"));			
			$subject = mysql_real_escape_string(trim('RE: '.$topic['subject']));
		}
		else
		$subject = mysql_real_escape_string(trim($_REQUEST['subject']));
		// Message
		$msg = mysql_real_escape_string(trim($_REQUEST['message']));

		if($answer || $newthread) {
			if($topic_id < 0)
			$topic_id = 0;

			$timestamp = time();
			$res1 = $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."forums_posts
				(topic_id, board_id, user_id, user_name, ip, subject, message, posttime)
				VALUES
				('".$topic_id."', '".$forum_id."',
				'".$_SESSION['userid']."', '".$_SESSION['nickname']."', '".$_SERVER['REMOTE_ADDR']."',
				'".$subject."', '".$msg."', '".$timestamp."')");

			if($answer)
			$res2 = $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."forums_topics SET last_posttime='".$timestamp."' WHERE topic_id='".$topic_id."' LIMIT 1");
			elseif($newthread){
				$lid = mysql_insert_id();

				$res2 = $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."forums_topics
					SET
					board_id='".$forum_id."',
					subject='".$subject."',
					first_post_id='".$lid."',
					flag='".$flag."',
					closed=0,
					last_posttime='".$timestamp."'") or die(mysql_error());

				$topic_id = mysql_insert_id();

				$res2 = $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."forums_posts SET topic_id='".$topic_id."' WHERE post_id='".$lid."' LIMIT 1");
			}

			if($res1 && $res2) {               
				$tplModEngine->Template('message');
				    
    				$tplModEngine->Assign('MSG',newCunitySuccess($lang['forums_reply_saved']));
    				$tplModEngine->Assign('forum_back',$lang['forums_back']);
    				$tplModEngine->Assign('TO', 'forums.php?fid='.$forum_id.'&tid='.$topic_id.'&page=last');
    				
                    $tplModEngine->Assign('edit_profile',$lang['profile_view_profile_edit']);
                    $tplModEngine->Assign('edit_img',$lang['profile_edit_img_edit']);
                    
      				$tplModEngine->show();
			}
			else
			die('DB Error');

			$_SESSION['lastpost'] = time();
		}
	}
	else
	require('modules/forums/post.php');
}
elseif($forum_id != -1 && $topic_id == -1) {
	require('modules/forums/view_forum.php');
}
elseif($forum_id != -1 && $topic_id != -1) {
	require('modules/forums/view_topic.php');
}
else {
	require('modules/forums/view_board.php');
	$board=true;
}


if(isset($_GET['deleteforum']) && $_GET['deleteforum'] == '1')
{
    $fid = $_GET['fid'];
    if($cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_boards WHERE board_id = '$fid'"))
    {
        $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_boards WHERE cat_id = '$fid'");
        $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_posts WHERE board_id = '$fid' OR topic_id = '$fid'");
        header("location: forums.php");
        exit;    
    }
}

// Footer
if(!($cunity->getSaver()->admin() && isset($board)))
        
	$tplModEngine->Template('footer');
    	    	
    	$tplModEngine->show();


require('ov_foot.php');
ob_end_flush();
?>