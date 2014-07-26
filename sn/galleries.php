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
require("ov_head.php");

require_once 'Cunity_Connector.class.php';
require_once 'Cunity_Galleries.class.php';
$galleries = new Cunity_Galleries($cunity);
$connector = new Cunity_Connector($cunity);

$cunity->getSaver()->login_required();
$cunity->getSaver()->module_power();

$tplModEngine = new Cunity_Template_Engine($cunity);
$tplModEngine->setPath('style/'.$_SESSION['style'].'/templates/galleries/');

if($_GET['c']=="galleries"||!isset($_GET['c'])){
	$photoCount=$galleries->countAllPhotos();
	if((!isset($_GET['list'])||$_GET['list']=="all")&&!isset($_GET['user'])&&$photoCount>0){
		$allRes = $cunity->getDb()->query("SELECT MAX(updated) FROM ".$cunity->getConfig("db_prefix")."galleries_albums");
		$m = mysql_fetch_assoc($allRes);
		$updated = showDate("date_time",$m['MAX(updated)']);
		$albums=$cunity->getTemplateEngine()->createTemplate('galleries_album',array(
			"NAME"=>$lang['galleries_all_of_all'],
			"MAIN_IMAGE"=>'style/'.$_SESSION['style'].'/img/photos.jpg',
			"ALBUM_ID"=>"000",
			"UPDATED"=>$updated,
			"PHOTOS"=>$photoCount,
			"USERNAME"=>$lang['galleries_all_users'],
			"USERHASH"=>"",
			"galleries_created_by"=>$lang['galleries_created_by'],
			"galleries_updated_on"=>$lang['galleries_updated_on'],
			"galleries_photos"=>$lang['galleries_photos']
		));
	}
	$albums.=$galleries->loadAlbums($_GET['list']);
	if(isset($_GET['list']) && $_GET['list'] == 'own')
        $title = $lang['galleries_galleries'].' - '.$lang['galleries_my_albums'];
    else if(isset($_GET['list']) && $_GET['list'] == 'all'){
    	if($connector->isConnected()) $title = $lang['galleries_galleries'].' - '.$lang['galleries_all_albums_of_cunity'];
    	else $title = $lang['galleries_galleries'].' - '.$lang['galleries_all_albums'];
    }else if(isset($_GET['list']) && $_GET['list'] == 'friends')
        $title = $lang['galleries_galleries'].' - '.$lang['galleries_friends_albums'];
    else
        $title = $lang['galleries_galleries'];

    $tplEngine->Assign('TITLE', $title);
    $tplEngine->show();
	$tplModEngine->Template("galleries");
    	$tplModEngine->Assign('TITLE', $title);
    	$tplModEngine->Assign('own',(!isset($_GET['user'])));
    	$tplModEngine->Assign('GALLERIES',$albums);
    	$tplModEngine->show();
}else if(isset($_GET['c'])&&$_GET['c']=="show_album"){
	if($_GET['id']=="000") $images=$galleries->showAllPhotos($_GET['sort']);
	else if(strpos($_GET['id'],"-")!==false){
		$urlData=explode("-", $_GET['id']);
		$cunityId=$urlData[0];
		$albumid =$urlData[1];
	}else{
        $cunityId=0;
        $albumid=$_GET['id'];
    }
	list($name,$descr,$images,$morePhotos)=$galleries->showAlbum($albumid,$cunityId);
	$tplEngine->Assign('TITLE', $lang['galleries_galleries']." - ".$name);
    $tplEngine->show();
	$tplModEngine->Template("album_show");
    	$tplModEngine->Assign('TITLE',$lang['galleries_galleries']." - ".$name);
    	$tplModEngine->Assign('OWN_GALLERY',$galleries->isOwnAlbum($_GET['id']));
    	$tplModEngine->Assign('REMOTE_ALBUM',$cunityId>0);
    	$tplModEngine->Assign('NOT_EDITABLE',($name==$lang['galleries_profile_images']||$name==$lang['galleries_wall_images']||$cunityId>0));
    	$tplModEngine->Assign('GALLERY_ADMIN',$cunity->getSaver()->admin());
    	$tplModEngine->Assign('ALL_OF_ALL',($_GET['id']=="000"));
    	$tplModEngine->Assign('DESCRIPTION',$descr);
    	$tplModEngine->Assign('IMAGES',$images);
    	$tplModEngine->Assign('sort'.$_GET['sort'],'selected="selected"');
    	$tplModEngine->Assign('ALBUM_ID',$albumid);
    	$tplModEngine->Assign('CUNITYID',$cunityId);
    	$tplModEngine->Assign('MOREPHOTOS',$morePhotos);
    	$tplModEngine->Assign('NO_IMAGE_ERROR',newCunityError($lang['galleries_no_images']));
    	$tplModEngine->show();
}else if(isset($_GET['c'])&&$_GET['c']=="new_album"){

	if(isset($_POST['send'])){
		$result=$galleries->newAlbum($_POST);
		if(is_array($result)&&$result["status"]==0)
			$error=newCunityError($result["error"]);
		elseif($result){
			header("Location: galleries.php?c=show_album&id=".$result);
			exit;
		}
	}
    list($space,$used) = $galleries->getUserSpace();
    $tplEngine->Assign('TITLE', $title);
    $tplEngine->show();
    $tplModEngine->Template("album_new");
    	$tplModEngine->Assign('ERRORS',$error);
    	$tplModEngine->Assign('TITLE', $title);
    	$tplModEngine->Assign('left', $space-$used);
    	$tplModEngine->Assign('percentage',$used/$space*100);
    	$tplModEngine->Assign('photos_left', floor(($space-$used)/0.06).' '.$lang['galleries_photos']);
    	$tplModEngine->show();
}elseif(isset($_GET['c'])&&$_GET['c']=="edit"){

	if(isset($_POST['send'])){
		$result=$galleries->editAlbum($_POST);
		if(is_array($result)&&$result["status"]==0)
			$error=newCunityError($result["error"]);
		else if($result){
			header("Location: galleries.php?c=show_album&id=".$result);
			exit;
		}
	}
	$data=$galleries->getAlbumData($_GET['id']);
	$tplEngine->Assign('TITLE', $title);
    $tplEngine->show();
    $tplModEngine->Template("edit");
    	$tplModEngine->Assign('TITLE', $title);
    	$tplModEngine->Assign('galleries_edit_album_name',$data['name']);
    	$tplModEngine->Assign('galleries_edit_description',$data['description']);
    	$tplModEngine->Assign('galleries_edit_priv_'.$data['privacy'],'checked');
    	$tplModEngine->Assign('ALBUM_ID',$data['album_id']);
    	$tplModEngine->Assign('NOT_EDITABLE',$data['name']==$lang['galleries_profile_images']||$data['name']==$lang['galleries_wall_images']);
    	$tplModEngine->Assign('ERRORS',$error);
    	$tplModEngine->show();
}else if(isset($_GET['c'])&&$_GET['c']=="single_upload"){
    list($space,$used) = $galleries->getUserSpace();
    $tplEngine->Assign('TITLE', $lang['galleries_galleries'].' - '.$lang['galleries_add_images']);
    $tplEngine->show();
	$tplModEngine->Template("single_upload");
	    $tplModEngine->Assign('TITLE', $lang['galleries_galleries'].' - '.$lang['galleries_add_images']);
    	$tplModEngine->Assign('left', $space-$used);
    	$tplModEngine->Assign('percentage',$used/$space*100);
    	$tplModEngine->Assign('photos_left', floor(($space-$used)/0.06).' '.$lang['galleries_photos']);
    	$tplModEngine->Assign('ALBUM_ID',$_GET['id']);
    	$tplModEngine->show();
}else if(isset($_GET['c'])&&$_GET['c']=="delete_album"){
	$result=$galleries->deleteAlbum($_GET['id']);
	if($result===true){
		header("Location: galleries.php");
		exit(0);
	}
}else if(isset($_GET['c'])&&$_GET['c']=="upload"){
    list($space,$used) = $galleries->getUserSpace();
    $max_size = 5; //in MB
	$max_upload = (int)(ini_get('upload_max_filesize'));
	$max_post = (int)(ini_get('post_max_size'));
	$memory_limit = (int)(ini_get('memory_limit'));
	$max_size = min($max_size, $max_upload, $max_post, $memory_limit);
	$tplEngine->Assign('TITLE', $lang['galleries_galleries'].' - '.$lang['galleries_add_images']);
    $tplEngine->show();
	$tplModEngine->Template("upload");
    	$tplModEngine->Assign('TITLE', $lang['galleries_galleries'].' - '.$lang['galleries_add_images']);
    	$tplModEngine->Assign('left', $space-$used);
    	$tplModEngine->Assign('percentage',$used/$space*100);
    	$tplModEngine->Assign('photos_left', floor(($space-$used)/0.06).' '.$lang['galleries_photos']);
    	$tplModEngine->Assign('ALBUM_ID',$_GET['id']);
    	$tplModEngine->Assign('SESSION_ID',session_id());
    	$tplModEngine->Assign('MAX_SIZE',$max_size);
    	$tplModEngine->show();
}
require('ov_foot.php');
ob_end_flush();
?>