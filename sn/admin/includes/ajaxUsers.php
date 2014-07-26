<?php
ini_set('session.use_cookies', true);


require('../../config.php');
require('../../includes/functions.php');
require('functions.php');
require_once('../../classes/Cunity.class.php');

session_name('cunity_sess'.$_SERVER['DOCUMENT_ROOT']);
session_start();

$cunity = new Cunity(true);
$cunity->getSaver()->check_admin();
$langadmin = $cunity->getLang();


if(isset($_GET['do']) && $_GET['do'] == 'space')
{
    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET space = ".mysql_real_escape_string($_POST['val'])." WHERE userid = ".mysql_real_escape_string($_POST['id'])."");
    echo mysql_error();
}

if(isset($_GET['do']) && $_GET['do'] == 'delete_cont')
{
    $userid = $_POST['id'];
    $res = $cunity->getDb()->query("SELECT nickname FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = '$userid'");
    $data = mysql_fetch_assoc($res);
    $str = '<p style="color: #FF0000;">'.$langadmin['admin_users_delete_do_you'].'&nbsp;<b>'.$data['nickname'].'</b>&nbsp;'.$langadmin['admin_users_delete_sure'].'</p>';
    $str .= '<p>'.$langadmin['admin_users_delete_forum'].'</p>';
    $str .= '<input type="radio" name="forum" class="forum_radio" value="yes" id="yes" style="width: auto;"/><label for="yes">'.$langadmin['admin_users_delete_yes'].'</label><br />';
    $str .= '<input type="radio" name="forum" class="forum_radio" value="no" id="no" style="width: auto;" checked="checked"/><label for="no">'.$langadmin['admin_users_delete_no'].'</label><p></p>';
    $str .= '<img src="style/default/img/exclamation.png" style="float: left; width: 35px; margin-right: 5px;"/><span style="float: left; width: 530px;">'.$langadmin['admin_users_delete_option'].'</span><div class="clear"></div>';
    echo $str;
}
if(isset($_GET['do']) && $_GET['do'] == 'delete_user')
{
    $id = $_POST['id'];
    $forum = $_POST['forum'];

    //Delete User data
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."users WHERE userid='".mysql_real_escape_string($id)."' LIMIT 1") or mysql_error();
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."users_details WHERE userid='".mysql_real_escape_string($id)."' LIMIT 1") or mysql_error();

    //
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."comments WHERE poster_id='".mysql_real_escape_string($id)."'") or mysql_error();

	// FORUMS MODULE
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_unread WHERE userid='".mysql_real_escape_string($id)."'") or mysql_error();
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_users WHERE userid='".mysql_real_escape_string($id)."'") or mysql_error();
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_watch WHERE userid='".mysql_real_escape_string($id)."'") or mysql_error();
	$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."forums_posts SET user_id='0' WHERE userid='".mysql_real_escape_string($id)."'") or mysql_error();

	if($forum == 'yes')
		$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_posts WHERE userid='".mysql_real_escape_string($id)."'") or mysql_error();
	else
		$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."forums_posts SET user_id='0' WHERE userid='".mysql_real_escape_string($id)."'") or mysql_error();

    //Gallery
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."galleries_albums WHERE user_id = '".$id."'") or mysql_error();
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."galleries_comments WHERE userid = '".$id."'") or mysql_error();
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."galleries_likes WHERE userid = '".$id."'") or mysql_error();
    $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."galleries_imgs WHERE uploader_id = '".$id."'") or mysql_error();
    while($data = mysql_fetch_assoc($res))
    {
        unlink('../'.$data['file']);
    }
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."galleries_imgs WHERE uploader_id = '".$id."'") or mysql_error();
    
    //delete Invitation codes
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."invitation_codes WHERE userid = '".$id."'") or mysql_error();
    
    //Delete all messages
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."messages WHERE sender = '".$id."' OR receiver = '".$id."'") or mysql_error();
    
    //Delete Notifications +settings
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."notifications WHERE userid = '".$id."'") or mysql_error();
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."notifications_settings WHERE userid = '".$id."'") or mysql_error();
    
    //Delete pinboard
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."pinboard_likes WHERE userid = '".$id."'") or mysql_error();
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."pinboard_status WHERE userid = '".$id."'") or mysql_error();
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."pinboard_status_comments WHERE userid = '".$id."'") or mysql_error();
    
    //Delete privacy-settings
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."privacy WHERE userid = '".$id."'") or mysql_error();
    
    //Delete relationshio_requests
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."relationship_requests WHERE userid = '".$id."'") or mysql_error();
    
    //delete avatar-settings
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."avatars WHERE userid = '".$id."'") or mysql_error();
    
    //delete chat
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."chat WHERE userid = '".$id."'") or mysql_error();
    
    //delete events
    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."events SET founder_id = 0 WHERE founder_id = '".$id."'") or mysql_error();
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."events_guests WHERE userid = '".$id."'") or mysql_error();
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."events_comments WHERE userid = '".$id."'") or mysql_error();
    
    //Delete files
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."files WHERE user_id = '".$id."'") or mysql_error();
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."files_download WHERE user_id = '".$id."'") or mysql_error();
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."files_share WHERE uploader_id = '".$id."'") or mysql_error();

	//delete avatar and profileimage
	$avatar = '../files/_avatars/'.$id.'.jpg';
	$ppic = '../files/_profile_imgs/'.$id.'.jpg';

	if(file_exists($avatar))
		unlink($avatar);

	if(file_exists($ppic))
		unlink($ppic);
}
?>