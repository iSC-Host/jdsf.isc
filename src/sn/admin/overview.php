<?php
ob_start("ob_gzhandler");
require('ov_head.php');

	$cunity->getSaver()->check_admin();

	// Page Content
	$tplEngine->Template('overview_stats');

	$infobox = '';
		
	$res = $cunity->getDb()->query("SELECT DATE_FORMAT(last_login, '".$_SESSION['date']['mysql']['date_time']."') as last_login, last_ip FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = '".$_SESSION['userid']."'");
	$data = mysql_fetch_assoc($res);
	   $lastLogin = '<div class="infobox"><img src="style/'.$cunity->getSetting('style_adminpanel').'/img/information.png" height="25px" width="25px" style="vertical-align: middle; margin-right: 10px;"/><span style="vertical-align: middle;">'.$langadmin['admin_overview_last_login'].' '.$data['last_login'].' '.$langadmin['admin_overview_last_login_from_ip'].' '.$data['last_ip'].'</span></div>';

    $infobox = "";
	// Stats
	$stats = array();

	$res = $cunity->getDb()->query('SELECT count(*) as rows FROM '.$cunity->getConfig("db_prefix").'users WHERE groupid=1 OR groupid = 2 OR groupid = 3');
	$data = mysql_fetch_assoc($res);
	$stats['USER_COUNT'] = $data['rows'];
	
	$res = $cunity->getDb()->query('SELECT count(*) as rows FROM '.$cunity->getConfig("db_prefix").'users WHERE groupid = 5');
	$data = mysql_fetch_assoc($res);
	$stats['NOT_ACTIVATED_USER_COUNT'] = $data['rows'];

	$res = $cunity->getDb()->query('SELECT count(*) as rows FROM '.$cunity->getConfig("db_prefix").'users WHERE groupid = 6');
	$data = mysql_fetch_assoc($res);
	$stats['BLOCKED_USER_COUNT'] = $data['rows'];

	$res = $cunity->getDb()->query('SELECT count(*) as rows FROM '.$cunity->getConfig("db_prefix").'forums_topics');
	$data = mysql_fetch_assoc($res);
	$stats['THREAD_COUNT'] = $data['rows'];

	$res = $cunity->getDb()->query('SELECT count(*) as rows FROM '.$cunity->getConfig("db_prefix").'forums_posts');
	$data = mysql_fetch_assoc($res);
	$stats['POST_COUNT'] = $data['rows'];
	
	$res = $cunity->getDb()->query('SELECT COUNT(*) AS rows FROM '.$cunity->getConfig("db_prefix").'forums_boards WHERE flag = 0');
	$data = mysql_fetch_assoc($res);
	$stats['FORUMS_COUNT'] = $data['rows'];
	
	$res = $cunity->getDb()->query('SELECT COUNT(*) AS rows FROM '.$cunity->getConfig("db_prefix").'galleries_imgs');
	$data = mysql_fetch_assoc($res);
	$stats['GALLERY_IMG_COUNT'] = $data['rows'];
		
	$stats['GALLERY_IMG_SIZE'] = 0;
	$res = $cunity->getDb()->query('SELECT file FROM '.$cunity->getConfig("db_prefix").'galleries_imgs');
	while($data = mysql_fetch_assoc($res))
	{
	    $finfo = pathinfo($data['file']);
        $filesize = filesize('../'.$data['file']);
        $stats['GALLERY_IMG_SIZE'] = $stats['GALLERY_IMG_SIZE'] + $filesize;
        $filesizeThumbs = filesize('../'.$finfo['dirname'].'/'.$finfo['filename'].'_thumb.jpg');
        $stats['GALLERY_IMG_SIZE'] = $stats['GALLERY_IMG_SIZE'] + $filesize;
    }
    $stats['GALLERY_IMG_SIZE'] = round($stats['GALLERY_IMG_SIZE'] / (1024*1024),1);
	$stats['GALLERY_IMG_SIZE'] = $stats['GALLERY_IMG_SIZE'].' MB';
	
	$res = $cunity->getDb()->query('SELECT COUNT(*) AS rows FROM '.$cunity->getConfig("db_prefix").'galleries_albums');
	$data = mysql_fetch_assoc($res);
	$stats['GALLERY_ALBUMS_COUNT'] = $data['rows'];
	
	$entireAlbumSize = 0;
	$albumSize = array();
	$res = $cunity->getDb()->query('SELECT id FROM '.$cunity->getConfig("db_prefix").'galleries_albums');
	while($data = mysql_fetch_assoc($res))
	{
	    $imgRes = $cunity->getDb()->query('SELECT file FROM '.$cunity->getConfig("db_prefix").'galleries_imgs WHERE album_id = '.mysql_real_escape_string($data['id']));
	    while($dataImg = mysql_fetch_assoc($imgRes))
	    {
            $finfo = pathinfo($dataImg['file']);
            $filesize = filesize('../'.$dataImg['file']);
            $entireAlbumSize = $entireAlbumSize + $filesize;
            $filesizeThumbs = filesize('../'.$finfo['dirname'].'/'.$finfo['filename'].'_thumb.jpg');
            $entireAlbumSize = $entireAlbumSize + $filesizeThumbs;           
        }
        $albumSize[] = $entireAlbumSize;
    }
    $sum = array_sum($albumSize);
    $count = count($albumSize);
	if ($count!=0){
    $stats['GALLERY_ALBUMS_AVERAGE'] = $sum / $count;
    $stats['GALLERY_ALBUMS_AVERAGE'] = round($stats['GALLERY_ALBUMS_AVERAGE'] / (1024*1024),1);
	$stats['GALLERY_ALBUMS_AVERAGE'] = $stats['GALLERY_ALBUMS_AVERAGE'].' MB';
	}
	else {$stats['GALLERY_ALBUMS_AVERAGE']='0 MB';}
	
	$res = $cunity->getDb()->query('SELECT count(*) as rows FROM '.$cunity->getConfig("db_prefix").'users WHERE groupid = 7');
	$data = mysql_fetch_assoc($res);
	$stats['INACTIVE_USER_COUNT'] = $data['rows'];

	$tplEngine->Assign($stats);

	$tplEngine->Assign('INFOBOX', $infobox);
	$tplEngine->Assign('LASTLOGIN', $lastLogin);
require('ov_foot.php');
ob_end_flush();
?>