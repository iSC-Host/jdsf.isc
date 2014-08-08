<?php

require_once 'Cunity_Comments.class.php';

require_once 'Cunity_Likes.class.php';

define('GD', false); // GD Calculation

class Cunity_Galleries {

	private $cunity = null;
	private $liker = null;
	private $commentor = null;
	private $lang = array();

	public function Cunity_Galleries(Cunity $cunity){
		$this->cunity = $cunity;
		$this->liker = new Cunity_Likes($this->cunity);
		$this->commentor = new Cunity_Comments($this->cunity);
		$this->lang = $this->cunity->getLang();
	}

	public function countAllPhotos(){
		$ap = $this->cunity->getDb()->query("SELECT album_id, user_id, privacy FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums");
		$allPhotos = 0;
		while($a = mysql_fetch_assoc($ap)){
			if(($a['privacy'] == 1 &&$this->cunity->getFriender()->isFriend($_SESSION['userid'],$a['user_id'])|| $a['user_id'] == $_SESSION['userid']) || $a['privacy'] == 2){
				$allPhotosRes = $this->cunity->getDb()->query("SELECT COUNT(*) FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs WHERE album_id = '".$a['album_id']."'");
				$allPhotosAssoc = mysql_fetch_assoc($allPhotosRes);
				$allPhotos = $allPhotos + $allPhotosAssoc['COUNT(*)'];
			}elseif($a['privacy'] == 1 && $a['user_id'] == $_SESSION['userid']){
				$allPhotosRes = $this->cunity->getDb()->query("SELECT COUNT(*) FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs WHERE album_id = '".$a['album_id']."'");
				$allPhotosAssoc = mysql_fetch_assoc($allPhotosRes);
				$allPhotos = $allPhotos + $allPhotosAssoc['COUNT(*)'];
			}
		}
		return $allPhotos;
	}

	public function getAlbumData($albumid,$field=""){
		if($field=="")
			$res=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE album_id = '".$albumid."'");
		else
			$res=$this->cunity->getDb()->query("SELECT `".$field."`,user_id FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE album_id = '".$albumid."'");

		if(mysql_num_rows($res)==0) return false;
		$dataAlbum=mysql_fetch_assoc($res);
		$dataAlbum['name'] = ($dataAlbum['name']=='-cunity-profile-images-') ? $this->lang['galleries_profile_images'] : $dataAlbum['name'];
		$dataAlbum['description'] = ($dataAlbum['description']=='-cunity-profile-images-') ? $this->lang['galleries_profile_images'].' '.$this->lang['galleries_of'].' '.getUserName($dataAlbum['user_id']) : $dataAlbum['description'];

        $dataAlbum['name'] = ($dataAlbum['name']=='-cunity-wall-images-') ? $this->lang['galleries_wall_images'] :$dataAlbum['name'];
        $dataAlbum['description'] = ($dataAlbum['description']=='-cunity-wall-images-') ? $this->lang['galleries_wall_images'].' '.$this->lang['galleries_of'].' '.getUserName($dataAlbum['user_id']) : $dataAlbum['description'];
		if($field=="") return $dataAlbum;
		else return $dataAlbum[$field];
	}

	public function getImageData($imgid,$field=""){
		if($field=="") $res=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs WHERE id = ".(int)$imgid);
		else $res=$this->cunity->getDb()->query("SELECT `".$field."` FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs WHERE id = ".(int)$imgid);
		if(mysql_num_rows($res)==0) return false;
		if(isset($data['file']))
			$data['file'] = $this->cunity->getSetting("url").substr($data['file'],1);
		$data=mysql_fetch_assoc($res);
		if($field=="") return $data;
		else return $data[$field];
	}

	public function loadAlbums($list=""){
		$albums = "";
		switch($list){
			case "all":
				$queryString = "(SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE user_id = ".(int)$_SESSION['userid'].") UNION (SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE privacy != '0' AND user_id != ".(int)$_SESSION['userid']." AND name != '-cunity-profile-images-')";
				break;

			case "own":
				$queryString = "SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE user_id = ".(int)$_SESSION['userid']." ORDER BY updated";
				break;

			case "friends":
				$queryString = "SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE (privacy != '0') AND user_id != ".(int)$_SESSION['userid']." AND name != '-cunity-profile-images-' ORDER BY updated";
				break;

			case "":
			default:
				$queryString = "(SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE user_id = ".(int)$_SESSION['userid'].") UNION (SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE privacy != '0' AND user_id != ".(int)$_SESSION['userid']." AND name != '-cunity-profile-images-')";
				break;
		}
		$result=$this->cunity->getDb()->query($queryString);
		if(mysql_num_rows($result)==0)
			return newCunityError($this->lang['galleries_no_albums']);
		while($data=mysql_fetch_assoc($result)){
			$galleries[]=$data;
		}
		if($list=="friends"||$list=="all"||$list==""){
			require_once 'Cunity_Connector.class.php';
			$connector = new Cunity_Connector($this->cunity);
			if($connector->isConnected())
				$galleries = array_merge($galleries,$connector->getFriendsAlbums());
		}

		foreach($galleries AS $data){			
			if((($this->cunity->getFriender()->isFriend($_SESSION['userid'],$data['user_id'],($data['cunityId']>0)?$data['cunityId']:0)&&$data['privacy']==1)||$data['privacy']==2)&&($data['photo_count']>0)||$data['user_id']==$_SESSION['userid']){
				if($data['description']=='-cunity-profile-images-'&&$data['name']=='-cunity-profile-images-'){
					$data['name'] = $this->lang['galleries_profile_images'];
					$data['description'] = $this->lang['galleries_profile_images'].' '.$this->lang['galleries_of'].' '.getUserName($data['user_id'],(isset($data['cunityId'])&&$data['cunityId']>0&&$data['cunityId']!=$this->cunity->getcunityId()),$data['cunityId']);
				}elseif($data['description']=='-cunity-wall-images-'&&$data['name']=='-cunity-wall-images-'){
					$data['name'] = $this->lang['galleries_wall_images'];
					$data['description'] = $this->lang['galleries_wall_images'].' '.$this->lang['galleries_of'].' '.getUserName($data['user_id'],(isset($data['cunityId'])&&$data['cunityId']>0&&$data['cunityId']!=$this->cunity->getcunityId()),$data['cunityId']);
				}
				if($data['main_image'] == NULL)
					$data['main_image'] = 'style/'.$_SESSION['style'].'/img/no_avatar.jpg';
				else{
					$finfo = pathinfo($data['main_image']);
					$data['main_image'] = $finfo['dirname'].'/'.$finfo['filename'].'_thumb.jpg';
				}
				if(isset($data['cunityId'])&&$data['cunityId']>0&&$data['cunityId']!=$this->cunity->getcunityId())
					$cunityId = $data['cunityId']."-";
				else
					$cunityId = "";

				$albums .=$this->cunity->getTemplateEngine()->createTemplate('galleries_album',array(
						"NAME"=>$data['name'],
						"MAIN_IMAGE"=>$data['main_image'],
						"ALBUM_ID"=>$cunityId.$data['album_id'],
						"UPDATED"=>showDate('date_time',$data['updated']),
						"DESCRIPTION_SHORT"=>substr($data['description'],0,200),
						"PHOTOS"=>$data['photo_count'],
						"USERNAME"=>getUserName($data['user_id'],(isset($data['cunityId'])&&$data['cunityId']>0&&$data['cunityId']!=$this->cunity->getcunityId()),$data['cunityId']),
						"USERHASH"=>$cunityId.getUserHash($data['user_id'],(isset($data['cunityId'])&&$data['cunityId']>0&&$data['cunityId']!=$this->cunity->getcunityId()),$data['cunityId']),
						"galleries_created_by"=>$this->lang['galleries_created_by'],
						"galleries_updated_on"=>$this->lang['galleries_updated_on'],
						"galleries_photos"=>$this->lang['galleries_photos']
				));
			}
		}
		if($albums=="")
			return newCunityError($this->lang['galleries_no_albums']);
		return $albums;
	}

	public function getImagesOfAlbum($albumid,$limit=0,$cunityId=0){
		if($cunityId>0){
			require_once 'Cunity_Connector.class.php';
			$connector = new Cunity_Connector($this->cunity);
			return $connector->getImagesOfAlbum($albumid,$cunityId,$limit);
		}
		$q = "SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs WHERE album_id= '".$albumid."' ORDER BY album_pos ASC LIMIT ".$limit.",15";		
		$res = $this->cunity->getDb()->query($q);
		if(mysql_num_rows($res)==0) return false;
		while($data=mysql_fetch_assoc($res)){
			$data['comments'] = $this->commentor->countComments($data['id'],"galleries");
			$data['likes'] = $this->liker->countLikes($data['id'],"galleries");
			$data['dislikes'] = $this->liker->countDislikes($data['id'],"galleries");
			$images[]=$data;
		}
			
		return $images;
	}

	public function countImagesOfAlbum($albumid){
		$PhotosRes = $this->cunity->getDb()->query_assoc("SELECT COUNT(*) FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs WHERE album_id = '".$albumid."'");
		return $PhotosRes['COUNT(*)'];
	}

	public function updatePhotoCount($albumid){
		if($this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."galleries_albums SET photo_count = (SELECT COUNT(*) FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs WHERE album_id = '".$albumid."') WHERE album_id = '".$albumid."'")){
			require_once 'Cunity_Connector.class.php';
			$connector = new Cunity_Connector($this->cunity);
			if($connector->isConnected())
				return $connector->updateGallery($albumid);
			return true;
		}
		return false;
	}

	public function getUserSpace(){
		$space_table = $this->cunity->getDb()->query("SELECT space FROM ".$this->cunity->getConfig("db_prefix")."users WHERE userid=".$_SESSION['userid']);
		$used_table = $this->cunity->getDb()->query("SELECT A.used+B.used as used
				FROM (SELECT COALESCE((SUM(file_size)), 0) div 1048576 as used FROM ".$this->cunity->getConfig("db_prefix")."files where user_id=".$_SESSION['userid'].") AS A,
				(SELECT COALESCE((SUM(size) div 1048576), 0) as used FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs where uploader_id=".$_SESSION['userid'].") AS B");
		$space = mysql_fetch_assoc($space_table);
		$used = mysql_fetch_assoc($used_table);
		if($space['space'] == '' || $space['space'] == NULL)
			$space['space'] = $this->cunity->getSetting('user_space');
		return array($space['space'],$used['used']);
	}

	public function showImage($albumData, $imgData,$cunityId){
		$finfo = pathinfo($imgData['file']);
		$image = '<div class="gal_thumb ui-state-default" id="sort_'.$imgData['id'].'">
		<a href="'.$imgData['file'].'" rel="imgDialog" class="thumb_link" id="'.$imgData['id'].'" cid="'.$cunityId.'" aid="'.$albumData['album_id'].'">
		<div style="background-image: url(\''.$finfo['dirname'].'/'.$finfo['filename'].'_thumb.jpg\');margin:0px auto" class="gal_thumb_spacer"></div>
		</a>
		<div class="main_list_img_photos">
		<div style="display: inline-block; float: left; margin: 1px 2px;" title="'.$imgData['comments'].' '.$this->lang['galleries_comments'].'"><img src="style/'.$_SESSION['style'].'/img/balloon.png" height="14px" width="14px" style="vertical-align: middle;"/><span style="vertical-align: middle; padding: 0px 2px;">'.$imgData['comments'].'</span></div>
		<div style="display: inline-block; float: left; margin: 1px 2px;" title="'.$imgData['likes'].' '.$this->lang['galleries_likes'].'"><img src="style/'.$_SESSION['style'].'/img/thumb-up.png" height="14px" width="14px" style="vertical-align: middle;"/><span style="vertical-align: middle; padding: 0px 2px;">'.$imgData['likes'].'</span></div>
		';
		if($this->cunity->getSetting("allow_dislike")==1)
			$image .='<div style="display: inline-block; float: left; margin: 1px 2px;" title="'.$imgData['dislikes'].' '.$this->lang['galleries_likes'].'"><img src="style/'.$_SESSION['style'].'/img/thumb.png" height="14px" width="14px" style="vertical-align: middle;"/><span style="vertical-align: middle; padding: 0px 2px;">'.$imgData['dislikes'].'</span></div>';		
		$image .='</div></div>';
		return $image;
	}

	public function isOwnAlbum($albumid){
		$data=$this->getAlbumData($albumid);
		return ($data['user_id']==$_SESSION['userid']);
	}

	public function showAlbum($albumid,$cunityId=0){
		$images="";
		$remote = ($cunityId>0);
		if($remote){
			require_once 'Cunity_Connector.class.php';
			$connector = new Cunity_Connector($this->cunity);
			$dataAlbum = $connector->getAlbumData($albumid,$cunityId);
			unset($connector);
		}else $dataAlbum=$this->getAlbumData($albumid);
		if(!$dataAlbum)
			return array($this->lang['galleries_album_not_exist'],"",newCunityError($this->lang['galleries_album_not_exist']),false);
		$_SESSION['count_photo']=15;

		if($dataAlbum['user_id']==$_SESSION['userid']&&!$remote)
			$ownGallery=true;

		if($ownGallery||($dataAlbum['privacy']==0&&$ownGallery||($this->cunity->getFriender()->isFriend($_SESSION['userid'],$dataAlbum['user_id'],$cunityId)&& $dataAlbum['privacy']==1)||$dataAlbum['privacy']==2)){
				$imgs=$this->getImagesOfAlbum($albumid,0,$cunityId);
			if(!$imgs) return array($dataAlbum['name'],$dataAlbum['description'],newCunityError($this->lang['galleries_no_images']),false);
			foreach($imgs AS $data)
				$images .= $this->showImage($dataAlbum,$data,$cunityId);
				
			return array($dataAlbum['name'],$dataAlbum['description'],$images,$dataAlbum['photo_count']>15);
		}else
			return array("","",newCunityError($this->lang['galleries_no_access_area']),false);
	}

	public function getImageLikes($imgId,$cunityId=0,$getRemote=false,$ownId=0,$ownCid=0){
		if($cunityId>0&&$cunityId!=$this->cunity->getcunityId()){
			$remote=true;
			require_once 'Cunity_Connector.class.php';
			$connector = new Cunity_Connector($this->cunity);
			$data=$connector->getImageLikes($imgId,$cunityId);
			$commentCount=count($data['comments']);
			if($commentCount>0)
    			foreach($data['comments'] AS $comment)
    				$comments .= $this->cunity->getTemplateEngine()->createTemplate('comment', $comment);
				
			$replaces = array(
				"STYLE"=>$_SESSION['style'],
				"AVATAR"=>getAvatarPath($_SESSION['userid']),
				"STATUS_ID"=>$imgId,
				"CID"=>$cunityId,
				"pinboard_comment"=>$this->lang['pinboard_comment'],
				"pinboard_comment_watermark"=>$this->lang['pinboard_comments_watermark']
			);
			$new_comment = $this->cunity->getTemplateEngine()->createTemplate('galleries_new_comment', $replaces);
			$data['template']['COMMENTS'] = $comments;
			$data['template']['NEW_COMMENT'] = $new_comment;
			$data['template']['ALBUMID'] = $cunityId."-".$data['template']['ALBUMID'];
			$tpl = $this->cunity->getTemplateEngine()->createTemplate('galleries_image_info',$data['template']);
				
			$data=array('status'=>1,'template'=>$tpl,"img"=>$data['imgData']['file'],"title"=>$data['imgData']['title'],"prevId"=>$data['prevId'],"nextId"=>$data['nextId']);
			return $data;
		}
		$cid = ($getRemote) ? $this->cunity->getcunityId() : 0;
		
		$remote=false;
		$remoteComments=array();
		$like = $this->liker->createLikes($imgId,"galleries",$ownId,$ownCid);

		$imgData=$this->getImageData($imgId);
		if(!$imgData)
			return array("status"=>0);

		$albumData = $this->getAlbumData($imgData['album_id']);				
		$likes="";
		$dislikes="";
		$likesCount = count($like[3]);
		$dislikesCount = count($like[4]);
		if($likesCount>0)
			foreach($like[3] AS $l)
				$likes .= '<a href="profile.php?user='.$l['cunityId']."-".$l['userhash'].'" style="border: 0px; text-decoration: none;" title="'.$l['username'].'"><img src="'.$l['avatar'].'" class="likeimg"/></a>';
		if($dislikesCount>0)
			foreach($like[4] AS $l)
				$dislikes .= '<a href="profile.php?user='.$l['cunityId']."-".$l['userhash'].'" style="border: 0px; text-decoration: none;" title="'.$l['username'].'"><img src="'.$l['avatar'].'" class="likeimg"/></a>';
		$c = $this->commentor->getComments($imgId,"galleries");
		$x=0;
		$comments="";
		$commentCount=count($c);
		if($commentCount>0){
			foreach($c AS $data){
				if(strlen($data['comment']) > 250){
					$comment = substr($data['comment'],0,250);
					$comment .= ' <span class="read_more_comment" onclick="more_comment_cont('.$data['id'].');" id="more_comment-'.$data['id'].'">('.$this->lang['pinboard_show_more'].')</span>';
					$comment .= '<span class="more_comment" id="more_comment_cont-'.$data['id'].'" style="display: none;">'.substr($data['comment'],250).' <span class="read_less_comment" onclick="less_comment_cont('.$data['id'].');" id="less_comment-'.$data['id'].'">('.$this->lang['pinboard_show_less'].')</span></span>';
				}else
					$comment = $data['comment'];				
					
				$delete = ($data['userid']==$_SESSION['userid']||$imgData['uploader_id']==$_SESSION['userid']) ? '<a href="javascript:deleteImageComment('.$data['id'].','.$data['cunityId'].');" class="ui-icon ui-icon-close del_comment_link" id="'.$data['id'].'_del" style="display: none;">&nbsp;</a>' : '';
				$replaces = array(
					"COMMENT_ID"=>$data['id'],
					"COMMENT_TIME"=>showDate('date_time', $data['time']),
					"STYLE"=>$_SESSION['style'],
					"COMMENT"=>$comment,
					"AVATAR"=>getSmallAvatar($data['userid'],40,$data['remote'],$data['cunityId']),
					"USERNAME"=>getUserName($data['userid'],$data['remote'],$data['cunityId']),
					"USERHASH"=>$data['cunityId']."-".getUserHash($data['userid'],$data['remote'],$data['cunityId']),
					"DELETE"=>$delete,
					"CLASS"=> 'gallery_comment image_comment-'.$imgId							
				);
				if($getRemote)
					$remoteComments[] = $replaces;
				else
					$comments .= $this->cunity->getTemplateEngine()->createTemplate('comment', $replaces);
				$x++;
			}
		}
		$replaces = array(
				"STYLE"=>$_SESSION['style'],
				"AVATAR"=>getAvatarPath($_SESSION['userid']),
				"STATUS_ID"=>$imgId,
				"CID"=>$cid,
				"pinboard_comment"=>$this->lang['pinboard_comment'],
				"pinboard_comment_watermark"=>$this->lang['pinboard_comments_watermark']
		);
		if(!$getRemote)
			$new_comment = $this->cunity->getTemplateEngine()->createTemplate('galleries_new_comment', $replaces);

		$liked=$this->liker->getLike($ownId,$imgId,"galleries",$ownCid);
		if($liked===0){
			$likeDisplay = "none";
			$dislikeDisplay = "";
		}else if($liked===1){
			$likeDisplay = "";
			$dislikeDisplay = "none";
		}else{
			$likeDisplay="";
			$dislikeDisplay = ($this->cunity->getSetting('allow_dislike')==1) ? "" : "none";
		}
		
		$images = $this->getImagesOfAlbum($imgData['album_id']);
		$posInAlbum=$this->array_multi_search('id',$imgId,$images);
		
		if(!isset($images[$posInAlbum-1]))
			$prevId=$images[count($images)-1]['id'];
		else
			$prevId=$images[$posInAlbum-1]['id'];
		if(!isset($images[$posInAlbum+1]))
			$nextId=$images[0]['id'];
		else
			$nextId=$images[$posInAlbum+1]['id'];
		
		if($imgData['title']==""&&$this->isOwnAlbum($imgData['album_id']))
			$title='<a href="javascript: addTitle('.$imgId.');" style="font-weight:normal !important;color:#fff">['.$this->lang['galleries_edit_title'].']</a>';			
		else
			$title = $imgData['title'];
		
		$replaces = array(
				"AVATAR"=>getAvatarPath($imgData['uploader_id']),
				"USERHASH"=>getUserHash($imgData['uploader_id']),
				"USERNAME"=>getUserName($imgData['uploader_id']),
				"X"=>$posInAlbum+1,
				"Y"=>count($images),
				"ALBUMNAME"=>$albumData['name'],
				"ALBUMID"=>$albumData['album_id'],
				"TIME"=>showDate('date_time',$imgData['timestamp']),
				"COMMENTS"=>$comments,
				"NEW_COMMENT"=>$new_comment,
				"LIKES"=>$likes,
				"DISLIKES"=>$dislikes,
				"LIKECOUNT"=>$likesCount,
				"DISLIKECOUNT"=>$dislikesCount,
				"COMMENTCOUNT"=>$commentCount,
				"IMG_ID"=>$imgId,
				"CID"=>$cid,
				"LIKE_DISPLAY"=>$likeDisplay,
				"DISLIKE_DISPLAY"=>$dislikeDisplay,
				"OWN_DISPLAY"=>($imgData['uploader_id']==$_SESSION['userid']) ? "list-item" : "none",
				"galleries_image"=>$this->lang['galleries_image'],
				"galleries_of"=>$this->lang['galleries_of'],
				"galleries_from_album"=>$this->lang['galleries_from_album'],
				"galleries_like"=>$this->lang['galleries_like'],
				"galleries_dislike"=>$this->lang['galleries_dislike'],
				"galleries_comment"=>$this->lang['galleries_comment'],
				"galleries_likes"=>$this->lang['galleries_likes'],
				"galleries_dislikes"=>$this->lang['galleries_dislikes'],
				"galleries_comments"=>$this->lang['galleries_comments'],
				"galleries_edit_image"=>$this->lang['galleries_edit_image'],
				"galleries_delete_image"=>$this->lang['galleries_del_image'],
				"galleries_cover_image"=>$this->lang['galleries_cover_image'],
				"galleries_download_image"=>$this->lang['galleries_download_image']
		);

		if(!$getRemote)
			$tpl = $this->cunity->getTemplateEngine()->createTemplate('galleries_image_info',$replaces);
		else
			$template = $replaces;
		
		if($getRemote){
			$imgData['file'] = $this->cunity->getSetting("url").'/'.$imgData['file'];
			return array("comments"=>$remoteComments,"template"=>$template,"imgData"=>$imgData,"prevId"=>$prevId,"nextId"=>$nextId);
		}

		$data=array('status'=>1,'template'=>$tpl,"img"=>$imgData['file'],"title"=>$title,"prevId"=>$prevId,"nextId"=>$nextId);
		return $data;
	}
	
	private function array_multi_search($key,$val,array $array){
		foreach($array AS $search_key => $a){
			if($a[$key]==$val) return $search_key;
		}
		return false;
	}

	public function loadMorePhotos($albumid,$cunityId){
		$images="";
		if($cunityId>0){			
			require_once 'Cunity_Connector.class.php';
			$connector = new Cunity_Connector($this->cunity);
			$dataAlbum = $connector->getAlbumData($albumid,$cunityId);
			unset($connector);
		}else $dataAlbum=$this->getAlbumData($albumid);
		if(!$dataAlbum)
			return array("status"=>0,"error"=>$this->lang['galleries_album_not_found']);

		if($dataAlbum['user_id']==$_SESSION['userid'])
			$ownGallery=true;

		$count = $this->countImagesOfAlbum($albumid);

        if($ownGallery||($dataAlbum['privacy']==0&&$ownGallery||($this->cunity->getFriender()->isFriend($_SESSION['userid'],$dataAlbum['user_id'],$cunityId)&& $dataAlbum['privacy']==1)||$dataAlbum['privacy']==2)){
			$imgs=$this->getImagesOfAlbum($albumid,$_SESSION['count_photo'],$cunityId);
			if($imgs===false) return array("status"=>0,"error"=>$this->lang['galleries_no_images']);
			foreach($imgs AS $data)
				$images .= $this->showImage($dataAlbum,$data,$cunityId);				
			$_SESSION['count_photo']=$_SESSION['count_photo']+15;
				
			return array("status"=>1,"photos"=>$images,"morephotos"=>($count-$_SESSION['count_photo']>0));
		}else
			return array("status"=>0,"error"=>$this->lang['galleries_no_access_area']);
	}

	public function showAllPhotos($sort="none"){
		if($sort=="") $sort="none";
		if($sort == 'users'){
			$userRes = $this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE name != '-cunity-profile-images-' GROUP BY user_id");
			if(mysql_num_rows($userRes)==0) return newCunityError($this->lang['galleries_no_images']);
			while($dataAlbum = mysql_fetch_assoc($userRes)){
				if($dataAlbum['description']=='-cunity-wall-images-'&&$dataAlbum['name']=='-cunity-wall-images-'){
					$dataAlbum['name'] = $this->lang['galleries_wall_images'];
					$dataAlbum['description'] = $this->lang['galleries_wall_images'].' '.$this->lang['galleries_of'].' '.getUserName($dataAlbum['user_id']);
				}
				$imgs=$this->getImagesOfAlbum($dataAlbum['album_id']);
				if(!$imgs) return newCunityError($this->lang['galleries_no_images']);
				if(count($imgs)>0&&($dataAlbum['privacy']==0&&$ownGallery||($this->cunity->getFriender()->isFriend($_SESSION['userid'],$dataAlbum['user_id'])&& $dataAlbum['privacy']==1)||$dataAlbum['privacy']==2)){
					$images .= 'User: <a href="profile.php?user='.getUserHash($dataAlbum['user_id']).'">'.getUserName($dataAlbum['user_id']).'</a>';
					$images .= '&nbsp;Album: <a href="galleries.php?c=show_album&id='.$dataAlbum['album_id'].'">'.$dataAlbum['name'].'</a>';
					$images .= '<div style="overflow: visible;">';
					foreach($imgs AS $data)
						$images .= $this->showImage($dataAlbum,$data);
					$images .= '</div><hr class="clear"/>';
					$images .= '';
				}
			}
		}else{
			$albumRes = $this->cunity->getDb()->query("(SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE privacy = 0 AND name != '-cunity-profile-images-' AND user_id = '".$userid."' ORDER BY name ASC) UNION (SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE name != '-cunity-profile-images-' AND privacy != 0 ORDER BY name ASC)");
			if(mysql_num_rows($albumRes)==0) return newCunityError($this->lang['galleries_no_images']);
			while($dataAlbum = mysql_fetch_assoc($albumRes)){
				if($dataAlbum['description']=='-cunity-wall-images-'&&$dataAlbum['name']=='-cunity-wall-images-'){
					$dataAlbum['name'] = $this->lang['galleries_wall_images'];
					$dataAlbum['description'] = $this->lang['galleries_wall_images'].' '.$this->lang['galleries_of'].' '.getUserName($dataAlbum['user_id']);
				}
				$imgs=$this->getImagesOfAlbum($dataAlbum['album_id']);
				if(!$imgs) return newCunityError($this->lang['galleries_no_images']);
				if(count($imgs)>0&&($dataAlbum['privacy']==0&&$ownGallery||($this->cunity->getFriender()->isFriend($_SESSION['userid'],$dataAlbum['user_id'])&& $dataAlbum['privacy']==1)||$dataAlbum['privacy']==2)){
					if($sort == 'album')
						$images .= 'Album <a href="galleries.php?c=show_album&id='.$dataAlbum['album_id'].'">'.$dataAlbum['name'].'</a>'.$this->lang['galleries_of'].'User <a href="profile.php?user='.getUserHash($dataAlbum['user_id']).'">'.getUserName($dataAlbum['user_id']).'</a>';
					$images .= '<div style="overflow: visible;">';
					foreach($imgs AS $data)
						$images .= $this->showImage($dataAlbum, $data);
					$images .= '</div><hr class="clear"/>';
				}
			}
			$images .= '<p>&nbsp;</p>';
		}
		return $images;
	}

	public function updateImageData($imgid,$field,$value){
		return $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."galleries_imgs SET `".$field."` = '".$value."' WHERE id = ".$imgid." AND uploader_id = ".$_SESSION['userid']);
	}

	public function updateAlbumData($albumid,$field,$value=""){
		if(is_array($field)){
			$res=array();
			foreach($field AS $key =>$value)
				$res[]=$this->updateAlbumData($albumid,$key,$value);
			return !in_array(false,$res);
		}
		return $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."galleries_albums SET `".$field."` = '".$value."' WHERE album_id = '".$albumid."'");
	}

	public function updateAlbumTime($albumid){
		return $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."galleries_albums SET updated = NOW() WHERE album_id = '".$albumid."'");
	}

	public function editAlbum(array $request){
		$albumName = htmlspecialchars(trim($request['album_name']));
		$albumDesc = htmlspecialchars(trim($request['album_descr']));
		if($albumName==$this->lang['galleries_profile_images']||$albumName==$this->lang['galleries_wall-images']){
		    $priv = ($request['album_privacy']==0||$request['album_privacy']==1||$request['album_privacy']==2) ? $request['album_privacy'] : 0;
            if($this->updateAlbumData($request['id'],array("privacy"=>$priv))&&$this->updateAlbumTime($request['id'])){
				require_once 'Cunity_Connector.class.php';
				$connector = new Cunity_Connector($this->cunity);
				if($connector->isConnected())
					$connector->updateGallery($request['id']);
				unset($connector);
				return $request['id'];
			}
            return array("status"=>0,"error"=>$this->lang['galleries_db_error']);
        }
		if(strlen($albumName) <= 25&&$albumName!=""&&strlen($albumName)>0){
			if(strlen($descr) <= 250) {
				$priv = ($request['album_privacy']==0||$request['album_privacy']==1||$request['album_privacy']==2) ? $request['album_privacy'] : 0;
				if($this->updateAlbumData($request['id'],array("name"=>$albumName,"description"=>$albumDesc,"privacy"=>$priv))&&$this->updateAlbumTime($request['id'])){
					require_once 'Cunity_Connector.class.php';
					$connector = new Cunity_Connector($this->cunity);
					if($connector->isConnected())
						$connector->updateGallery($request['id']);
					unset($connector);
					return $request['id'];
				}
				else return array("status"=>0,"error"=>$this->lang['galleries_db_error']);
			}else return array("status"=>0,"error"=>$this->lang['galleries_description_long']);
		}else return array("status"=>0,"error"=>$this->lang['galleries_no_name']);

	}

	public function createUniqueAlbumId($name){
		$id = sha1(time().$name);
		$res = $this->cunity->getDb()->query("SELECT COUNT(*) FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE album_id = '".$id."'");
		$hashdata = mysql_fetch_assoc($res);
		if($hashdata['COUNT(*)'] > 0)
			return $this->createUniqueAlbumId($id.rand());
		return substr($id,0,40);
	}

	public function newAlbum(array $request){
		$name = htmlspecialchars(trim($request['album_name']));
		$descr = htmlspecialchars(trim($request['album_descr']));
		$album_id = $this->createUniqueAlbumId($name);		
		$request['album_privacy'] = (int)$request['album_privacy'];		
			
		$res = $this->cunity->getDb()->query("SELECT COUNT(*) FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE user_id = ".$_SESSION['userid']." AND name = '".$name."'");
		$c = mysql_fetch_assoc($res);
		if($c['COUNT(*)']>0) return array("status"=>0,"error"=>$this->lang['galleries_album_already_exist']);
		if(strlen($name) <= 25&&$name!=""&&strlen($name)>0){
			if(isset($request['from'])&&$request['from'] == 'start'){
				if($this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."galleries_albums (album_id, name, user_id, description, privacy) VALUES ('".$album_id."','".mysql_real_escape_string($name)."','".(int)$_SESSION['userid']."','',1)")){
					require_once 'Cunity_Connector.class.php';
					$connector = new Cunity_Connector($this->cunity);
					if($connector->isConnected())
						$connector->addNewGallery($album_id);
					return $album_id;
				}
				else return array("status"=>0,"error"=>$this->lang['galleries_db_error']);
			}elseif(strlen($descr) <= 250){				
				$priv = ($request['album_privacy']==0||$request['album_privacy']==1||$request['album_privacy']==2) ? $request['album_privacy'] : 0;
				if($this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."galleries_albums (album_id, name, user_id, description, privacy) VALUES ('".$album_id."','".mysql_real_escape_string($name)."', ".(int)$_SESSION['userid'].", '".mysql_real_escape_string($descr)."',".$priv.")")){
					require_once 'Cunity_Connector.class.php';
					$connector = new Cunity_Connector($this->cunity);
					if($connector->isConnected())
						$connector->addNewGallery($album_id);
					return $album_id;
				}
				else return array("status"=>0,"error"=>$this->lang['galleries_db_error']);
			}else return array("status"=>0,"error"=>$this->lang['galleries_description_long']);
		}else return array("status"=>0,"error"=>$this->lang['galleries_no_name']);
	}

	public function deleteAlbum($albumid){
		$data=$this->getAlbumData($albumid);
		if($data['user_id'] == $_SESSION['userid'] || $this->cunity->getSaver()->admin()){
			if($this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE album_id = '".$albumid."' LIMIT 1")){
				$res = $this->cunity->getDb()->query("SELECT id FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs WHERE album_id = '".$albumid."'");
				if(mysql_num_rows($res) > 0)
					while($data = mysql_fetch_assoc($res))
					$res[]=$this->deleteImage($data['id']);
				if(!in_array(false,$res)){
					require_once 'Cunity_Connector.class.php';
					$connector = new Cunity_Connector($this->cunity);
					if($connector->isConnected())
						return $connector->deleteGallery($albumid);
					else return true;
				}
			}
		}
		return false;
	}

	public function deleteImage($id){
		$imgData = $this->getImageData($id);
		if($imgData['uploader_id']!=$_SESSION['userid'])
			return false;
		$image = $imgData['file'];
		$fi = pathinfo($image);
		$res=array();
		chdir($_SESSION['cunity_trunk_folder']);
		if(file_exists($image))
			$res[]=unlink($image);
		if(file_exists($fi['dirname'].'/'.$fi['filename'].'_thumb.jpg'))
			$res[]=unlink($fi['dirname'].'/'.$fi['filename'].'_thumb.jpg');
		$res[]=$this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs WHERE id=".$id);
		$res[]=$this->liker->deleteAllLikes($id,"galleries");
		$res[]=$this->commentor->deleteAllComments($id,"galleries");
		$res[]=$this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."pinboard WHERE message = ".$id." AND userid = ".$imgData['uploader_id']." AND type = 'image'");				
		$this->updatePhotoCount($imgData['album_id']);
		return !in_array(false,$res);
	}

	public function sendCommentNotifications($userid,$pinboard_id,$status_id,$sender){
		$commentors = $this->commentor->getComments($status_id, 'pinboard');
		if(count($commentors)>0)
			foreach($commentors AS $users)
			$this->cunity->getNotifier()->addNotification('also_status_comment',$users['userid'],$sender, $request['id'], $userid);
		if($userid!=$sender)
			$this->cunity->getNotifier()->addNotification('status_comment',$userid,$sender, $request['id']);
		if($pinboard_id != 0)
			$this->cunity->getNotifier()->addNotification('status_comment',$pinboard,$sender, $request['id']);
		return true;
	}

	public function addComment(array $request){
		$ressourceId=$request['id'];
		$ressource_name = "galleries";
		$cunityId = $request['cid'];
		if($cunityId>0){
			require_once 'Cunity_Connector.class.php';
			$connector=new Cunity_Connector($this->cunity);
			$comment_id = $connector->addComment($_SESSION['userid'],$cunityId,$ressourceId,$ressource_name,$request['message']);
		}else if($cunityId==0){
			$comment_id=$this->commentor->addComment($_SESSION['userid'],$ressourceId,$ressource_name,$request['message']);
		}
		if(!$comment_id) return false;
		if($cunityId==0) $this->sendCommentNotifications($statusData['userid'], $statusData['pinboard_id'], $request['id'], $sender);		
		$replaces = array(
				"CLASS"=> 'gallery_comment image_comment-'.$ressourceId,
				"COMMENT_ID"=>$comment_id,
				"COMMENT_TIME"=>showDate('date_time', time()),
				"STYLE"=>$_SESSION['style'],
				"COMMENT"=>$request['message'],
				"AVATAR"=>getSmallAvatar($_SESSION['userid'],40),
				"USERNAME"=>getUserName($_SESSION['userid']),
				"USERHASH"=>getUserHash($_SESSION['userid']),
				"DELETE"=>'<a href="javascript:deleteComment('.$comment_id.','.$cunityId.');" class="ui-icon ui-icon-close del_comment_link" id="'.$comment_id.'_del" style="display: none;">&nbsp;</a>'
		);
		$output = $this->cunity->getTemplateEngine()->createTemplate('comment', $replaces);

		return $output;
	}

	public function likeImage(array $request){
		$ressourceId=$request['id'];
		$rn = "galleries";
		$likes = "";
		$cunityId = $request['cid'];
		if($cunityId>0){
			require_once 'Cunity_Connector.class.php';
			$connector=new Cunity_Connector($this->cunity);
			$likeRes=$connector->like($_SESSION['userid'],$cunityId,$ressourceId,$rn,"like");
			unset($connector);
		}else if($cunityId==0){
			$liked = $this->liker->getLike($_SESSION['userid'],$ressourceId,$rn);
			if(!$liked||($liked==1&&$this->liker->deleteLike($_SESSION['userid'],$ressourceId,$rn)))
				$likeRes=$this->liker->like($_SESSION['userid'],$ressourceId,$rn);
			else return array("status"=>0,"msg"=>"already liked or db error");
		}
		$likes=$this->liker->createLikes($ressourceId, $rn,$_SESSION['userid'],0);
		if($likeRes!==false){
			$like = ($cunityId>0) ? $likeRes : $this->liker->createLikes($ressourceId, $rn,$_SESSION['userid'],0);
			$likesCount = count($like[3]);
			$dislikesCount = count($like[4]);
			if($likesCount>0)
				foreach($like[3] AS $l)
				$likesImgs .= '<a href="profile.php?user='.$l['userhash'].'" style="border: 0px; text-decoration: none;" title="'.$l['username'].'"><img src="'.$l['avatar'].'" class="likeimg"/></a>';
			$l="";
			if($dislikesCount>0)
				foreach($like[4] AS $l)
					$dislikesImgs .= '<a href="profile.php?user='.$l['userhash'].'" style="border: 0px; text-decoration: none;" title="'.$l['username'].'"><img src="'.$l['avatar'].'" class="likeimg"/></a>';
			return array('likes'=>$likesImgs,'dislikes'=>$dislikesImgs,'likeCount'=>$likesCount,'dislikeCount'=>$dislikesCount,'status'=>(int)$likeRes);
		}else return array("status"=>0,"msg"=>"Db-Error!");
	}

	public function dislikeImage(array $request){
		$ressourceId=$request['id'];
		$rn = "galleries";
		$likes = "";
		$cunityId = $request['cid'];
		if($cunityId>0){
			require_once 'Cunity_Connector.class.php';
			$connector=new Cunity_Connector($this->cunity);
			$likeRes=$connector->like($_SESSION['userid'],$cunityId,$ressourceId,$rn,"dislike");
			unset($connector);
		}
		else if($cunityId==0){
			$liked = $this->liker->getLike($_SESSION['userid'],$ressourceId,$rn);
			if($this->cunity->getSetting("allow_dislike")==1){
				if($liked==1) // status already disliked
					return array("status"=>0,"msg"=>"already disliked");
				elseif($liked==0||!$liked){
					if($this->liker->deleteLike($_SESSION['userid'],$ressourceId,$rn))
						$likeRes=$this->liker->dislike($_SESSION['userid'],$ressourceId,$rn);
				}else return array("status"=>0,"msg"=>"internal error!");
			}elseif($liked==0){
				$likeRes=$this->liker->deleteLike($_SESSION['userid'],$ressourceId,$rn);
			}
		}
		if($likeRes!==false){
			$like = ($cunityId>0) ? $likeRes : $this->liker->createLikes($ressourceId, $rn,$_SESSION['userid'],0);
			$likesCount = count($like[3]);
			$dislikesCount = count($like[4]);
			if($likesCount>0)
				foreach($like[3] AS $l)
				$likesImgs .= '<a href="profile.php?user='.$l['userhash'].'" style="border: 0px; text-decoration: none;" title="'.$l['username'].'"><img src="'.$l['avatar'].'" class="likeimg"/></a>';
			$l="";
			if($dislikesCount>0)
				foreach($like[4] AS $l)
					$dislikesImgs .= '<a href="profile.php?user='.$l['userhash'].'" style="border: 0px; text-decoration: none;" title="'.$l['username'].'"><img src="'.$l['avatar'].'" class="likeimg"/></a>';
			return array('likes'=>$likesImgs,'dislikes'=>$dislikesImgs,'likeCount'=>$likesCount,'dislikeCount'=>$dislikesCount,'status'=>(int)$likeRes);
		}else
			return array("status"=>0,"db-error");
	}

	public function deleteComment($comment_id){
		$comment_id = mysql_real_escape_string($comment_id);
		$res = $this->cunity->getDb()->query("SELECT `userid`,`ressource_id` FROM ".$this->cunity->getConfig("db_prefix")."comments WHERE id = ".(int)$comment_id." LIMIT 1");
		$data = mysql_fetch_assoc($res);
		$imageData = $this->getImageData($data['ressource_id']);
		if(mysql_num_rows($res)==1&&($data['userid']==$_SESSION['userid']||$imageData['uploader_id']==$_SESSION['userid']))
			return $this->commentor->deleteComment($comment_id);
		return false;
	}

	public function showLikes(array $request){
		$ressource_id = $request['id'];
		$ressource_name = "galleries";
		$type = $request['type'];
		$data = array();
		$cunityId = $request['cid'];
		if($cunityId==0)
			$likes = ($type==0) ? $this->liker->getLikes($ressource_id,$ressource_name) : $this->liker->getDislikes($ressource_id,$ressource_name);
		else{
			require_once 'Cunity_Connector.class.php';
			$connector = new Cunity_Connector($this->cunity);
			$likes = $connector->getLikes($ressource_id,$ressource_name,$type,$cunityId);
			unset($connector);
		}
		if(count($likes)==0) return false;
		foreach($likes AS $p){
			$persons .= '<div class="main_list_wrap" style="height: 50px;">';
			$persons .= '<div class="main_list_img_wrap" style="width: 45px;">';
			$persons .= '<a href="profile.php?user='.$p['cunityId']."-".$p['userhash'].'"><img src="'.$p['avatar'].'" style="height:40px;width:40px" class="left_comment"/></a>';
			$persons .= '</div><div class="main_list_cont" style="width: 200px; text-align: left;">';
			$persons .= '<a href="profile.php?user='.$p['cunityId']."-".$p['userhash'].'" class="main_list_name">'.$p['username'].'</a><br />';
			$persons .= '</div>';
			$persons .= '</div>';
		}
		$data['persons'] = $persons;
		$data['title'] = ($type==0) ? $this->lang['pinboard_people_who_like'] :$this->lang['pinboard_people_who_dislike'];
		return $data;
	}

	public function setImageAsCover($imgid){
		$data=$this->getImageData($imgid);
		if(!$data) return array("status"=>0,"error"=>"image not found!");
		if($data['uploader_id'] == $_SESSION['userid']){
			if($resfront = $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."galleries_albums SET main_image = '".$data['file']."' WHERE album_id = '".$data['album_id']."' LIMIT 1"))
				return array("status"=>1);
		}else return array('status'=>'0','error'=>"You are not the owner of this image!");
	}

	public function updateImagePositions(array $request){
		$res=array();
		foreach($request AS $position => $id)
			$res[]=$this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."galleries_imgs SET album_pos = ".$position." WHERE id = ".$id);
		return !in_array(false,$res);
	}

	public function addImageToDb(array $data){
		$q="INSERT INTO ".$this->cunity->getConfig("db_prefix")."galleries_imgs (album_id, file,uploader_id, size,uploader_ip, album_pos) VALUES ('".$data['album_id']."', '".$data['file']."', '".(int)$_SESSION['userid']."', ".$data['size'].",'".mysql_real_escape_string($_SESSION['ip'])."',0)";
		if($this->cunity->getDb()->query($q)){
			$id=mysql_insert_id();
			$this->updatePhotoCount($data['album_id']);
			return $id;
		}
		return false;
	}

	private function imageUnlink($file){
		$fi = pathinfo($image);
		if(file_exists($image))
			$res[]=unlink($image);
		if(file_exists($fi['dirname'].'/'.$fi['filename'].'_thumb.jpg'))
			$res[]=unlink($fi['dirname'].'/'.$fi['filename'].'_thumb.jpg');
		$res[]=$this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs WHERE file='".mysql_real_escape_string($image)."'");
		return !in_array(false,$res);
	}
	
	public function getPinboardAlbumId(){
		$res=$this->cunity->getDb()->query("SELECT album_id FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE user_id = ".$_SESSION['userid']." AND name = '-cunity-wall-images-' AND description = '-cunity-wall-images-'");
		if(mysql_num_rows($res)>0){
			$data=mysql_fetch_assoc($res);
			return $data['album_id'];
		}else
			return false;
	}
	
	public function getProfileImagesAlbumId(){
		$res=$this->cunity->getDb()->query("SELECT album_id FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE user_id = ".$_SESSION['userid']." AND name = '-cunity-profile-images-' AND description = '-cunity-profile-images-'");
		if(mysql_num_rows($res)>0){
			$data=mysql_fetch_assoc($res);
			return $data['album_id'];
		}else
			return false;
	}

	public function uploadSingleFile($request,$files){
		if(!$this->isOwnAlbum($request['id']))
			return array("status"=>0,"error"=>$this->lang['galleries_no_access_area']);

		$max_size = 5; //in MB
		$max_upload = (int)(ini_get('upload_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		$memory_limit = (int)(ini_get('memory_limit'));
		$max_size = min($max_size, $max_upload, $max_post, $memory_limit);
				
		if(isset($request['id']) && !empty($files['fu']['name']) && !empty($files['fu']['tmp_name']) && $files['fu']['error'] != UPLOAD_ERR_NO_FILE && $files['fu']['error'] != UPLOAD_ERR_PARTIAL){
			$pathinfo = pathinfo($files['fu']['name']);
			if(strtolower($pathinfo['extension'])=="jpg"||strtolower($pathinfo['extension'])=="jpeg"){
				if($files['fu']['size'] <= ($max_size * 1024 * 1024)) { // check size
					if($files['fu']['error'] == UPLOAD_ERR_OK){						
						list($space,$used) = $this->getUserSpace();
						if(($space-$used) >= 0.06){
							//Directory-path from this file
							$dir = $_SESSION['cunity_trunk_folder'].'/files/_galleries/u_'.$_SESSION['userid'].'_'.$_SESSION['userhash'];
							//absolute path from cunity-main-folder
							$dirAbs = './files/_galleries/u_'.$_SESSION['userid'].'_'.$_SESSION['userhash'];
								
							if (!file_exists($dir))
								mkdir($dir, 0777, true);

							$filename = sha1(time()).'.jpg';
							
							$file = $dir.'/'.$filename;
							$fileAbs = $dirAbs.'/'.$filename;
							
							if(!move_uploaded_file($files['fu']['tmp_name'], $file))
								return false;
							$albumData=$this->getAlbumData($request['id']);
							if($albumData['main_image'] == NULL){
								if(!$this->updateAlbumData($request['id'],"main_image",$dirAbs.'/'.$filename))
									return array("status"=>0,"error"=>$this->lang['galleries_db_error']);
							}														
								
							$imgId=$this->addImageToDb(array("album_id"=>$request['id'],"file"=>$fileAbs,"size"=>filesize($file)));
							$finfo=pathinfo($file);
							$size = getimagesize($file);
							$width = $size[0];
							$height = $size[1];
							if($this->cunity->getSetting('image_download')==0){
								$width=750;
								$height=750;
							}
							if($this->imgResize($file,$file,$width,$height,true)&&
									$this->imgResize($file,$finfo['dirname'].'/'.$finfo['filename'].'_thumb.jpg',150,150,false)&&
									$imgId!==false)
								return $this->getImageData($imgId);
							else{
								$this->imgUnlink($file);
								return array("status"=>0,"error"=>$this->lang['galleries_db_error']);
							}
						}else
							return array("status"=>0,"error"=>$this->lang['galleries_no_space']);
					}
				}else
					return array("status"=>0,"error"=>$this->lang['galleries_image_large1'].$max_size.$this->lang['galleries_image_large2']);
			}else
				return array("status"=>0,"error"=>$this->lang['galleries_accept_files']);
		}else
			return array("status"=>0,"error"=>$this->lang['galleries_no_file']);
	}

	public function imgResize($source,$destination,$maxWidth,$maxHeight,$delete=true) {
		$quality = 75;		

		$size = getimagesize($source);
		$width = $size[0];
		$height = $size[1];
		$type = $size[2];
		
		if($height <= $maxHeight && $width <= $maxWidth) {
			$new_width = $width;
			$new_height = $height;
		}
		else if($height <= $width) {
			$new_width = $maxWidth;
			$new_height = ($maxWidth / $width) * $height;
		}
		else {
			$new_width = ($maxHeight / $height) * $width;
			$new_height =$maxHeight;
		}
		$mem_limit = (int)ini_get("memory_limit");
		$mem = ceil((($width * $height * $size['bits']) + memory_get_usage()) / 1024 / 1024);
		$okay = true;
		if(GD && $mem > $mem_limit)
			$okay = false;

		if($okay && $type == IMAGETYPE_JPEG) {
			$old_img = imagecreatefromjpeg($source);
		}else if($okay && $type == IMAGETYPE_PNG) {
			$old_img = imagecreatefrompng($source);

			imagealphablending($old_img, false);
			imagesavealpha($old_img, true);
		}else if($okay && $type == IMAGETYPE_GIF) {
			$old_img = imagecreatefromgif($source);
		}

		if($okay && isset($old_img)) {
			$img = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($img, $old_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		}else die('too large!');

		if($delete)
			if(file_exists($source)) // delete original
				unlink($source);

		
		if(!imagejpeg($img, $destination, $quality)) // create resized one
			return false;		
		
		if(isset($old_img))
			imagedestroy($old_img);
		imagedestroy($img);

		return true;
	}



	// function to create thumbnails
	private function imgCreateThumbnail($image, $interlacing = false) {
		$quality = 75;

		$size = getimagesize($image);
		$width = $size[0];
		$height = $size[1];
		$type = $size[2];

		if($height <= 150 && $width <= 150) {
			$new_width = $width;
			$new_height = $height;
		}
		else if($height <= $width) {
			$new_width = 150;
			$new_height = (150 / $width) * $height;
		}
		else {
			$new_width = (150 / $height) * $width;
			$new_height = 150;
		}
		
		$mem_limit = (int)ini_get("memory_limit");
		$mem = ceil((($width * $height * $size['bits']) + memory_get_usage()) / 1024 / 1024);
		$okay = true;
		if(GD && $mem > $mem_limit)
			$okay = false;

		if($okay && $type == IMAGETYPE_JPEG) {
			$old_img = imagecreatefromjpeg($image);
		}else if($okay && $type == IMAGETYPE_PNG) {
			$old_img = imagecreatefrompng($image);

			imagealphablending($old_img, false);
			imagesavealpha($old_img, true);
		}else if($okay && $type == IMAGETYPE_GIF)
			$old_img = imagecreatefromgif($image);		
			
		if($okay){
			$img = imagecreatetruecolor($new_width, $new_height); // Neues TrueColor Bild anlegen
			imagecopyresampled($img, $old_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		}else
			die('Error.');


		if($interlacing)
			imageinterlace($img, true);

		$fileinfo = pathinfo($image);
		if(!imagejpeg($img, $fileinfo['dirname'].'/'.$fileinfo['filename'].'_thumb.jpg', $quality))
			return false;

		if(isset($old_img))
			imagedestroy($old_img);
		imagedestroy($img);

		return true;
	}
}
?>