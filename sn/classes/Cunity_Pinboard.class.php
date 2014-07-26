<?php

require_once 'Cunity_Comments.class.php';

require_once 'Cunity_Likes.class.php';

class Cunity_Pinboard {

	private $cunity = null;
	private $lang = array();
	private $commentor = null;
	private $liker = null;

	public function Cunity_Pinboard(Cunity $cunity,$remote=false,$user=0){
		$this->cunity = $cunity;
		$this->lang = $cunity->getLang();
		$this->cunity->getTemplateEngine()->setController(true);
		$this->commentor = new Cunity_Comments($this->cunity);
		$this->liker = new Cunity_Likes($this->cunity);
	}

	public function showLikes(array $request){
		$ressource_id = $request['id'];
		$ressource_name = $request['sType'];
		$type = $request['type'];
		$cunityId=$request['cid'];
		$data = array();
		if($cunityId==0)
			$likes = ($type==0) ? $this->liker->getLikes($ressource_id,$ressource_name) : $this->liker->getDislikes($ressource_id,$ressource_name);
		else{
			require_once 'Cunity_Connector.class.php';
			$cunityData= ($request['userData']!="") ? json_decode(base64_decode($request['userData']),true) : array();
			$connector = new Cunity_Connector($this->cunity);
			$likes = $connector->getLikes($ressource_id,$ressource_name,$type,$cunityId,$cunityData);
			unset($connector);
		}
		if(count($likes)==0) return false;
	    foreach($likes AS $p){
	    	if($cunityId==0){
	    		$userhash = getUserHash($p['userid'],$p['remote'],$p['cunityId']);
	    		$username = getUserName($p['userid'],$p['remote'],$p['cunityId']);
	    		$avatar = getSmallAvatar($p['userid'], 45,$p['remote'],$p['cunityId']);
	    	}else{
	    		$userhash = $p['userhash'];
	    		$username = $p['username'];
	    		$avatar = $p['avatar'];
	    	}
	        $persons .= '<div class="main_list_wrap" style="height: 50px;">';
	        $persons .= '<div class="main_list_img_wrap" style="width: 45px;">';
	        $persons .= '<a href="profile.php?user='.$p['cunityId']."-".$userhash.'">'.$avatar.'</a>';
	        $persons .= '</div><div class="main_list_cont" style="width: 200px; text-align: left;">';
	        $persons .= '<a href="profile.php?user='.$p['cunityId']."-".$userhash.'" class="main_list_name">'.$username.'</a><br />';
	        $persons .= '</div>';
	        $persons .= '</div>';
	    }
	    $data['persons'] = $persons;
	    $data['title'] = ($type==0) ? $this->lang['pinboard_people_who_like'] :$this->lang['pinboard_people_who_dislike'];
	    return $data;
	}

	public function getStatusData($statusId){
		$res = $this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."pinboard WHERE status_id = ".(int)$statusId);
		return mysql_fetch_assoc($res);
	}

	private function updateStatusField($id,$field,$value){
		return $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."pinboard SET `".$field."` = '".$value."' WHERE status_id = ".(int)$id);
	}

	public function insertPinboard($userid, $pinboard_id, $message, $type, $receiver,$cunityId=0,$remote=NULL,$remoteId=0){
		$this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."pinboard (
			`userid`,
			`pinboard_id`,
			`message`,
			`time`,
			`type`,
			`receiver`,
			`cunityId`,
			`remote`,
			`remoteId`
		)VALUES(
			".(int)$userid.",
			".(int)$pinboard_id.",
			'".$message."',
			NOW(),
			'".$type."',
			'".$receiver."',
			".(int)$cunityId.",
			'".$remote."',
			".(int)$remoteId."
		)") or die(mysql_error());
	    return mysql_insert_id();
	}

	public function loadComments($id){
	    $output = "";
	    $statusData = $this->getStatusData($id);
		$ressource_name = ($statusData['type']=="image") ? "galleries" : "pinboard";
	    $res = $this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."comments WHERE ressource_id = '".$id."' AND ressource_name = '".$ressource_name."'");
	    if(mysql_num_rows($res)==0)
	    	return false;
	    while($data = mysql_fetch_assoc($res)){
	        if(strlen($data['comment']) > 250){
	            $comment = substr($data['comment'],0,250);
	            $comment .= ' <span class="read_more_comment" id="more_comment-'.$data['id'].'">('.$this->lang['pinboard_show_more'].')</span>';
	            $comment .= '<span class="more_comment" id="more_comment_cont-'.$data['id'].'" style="display: none;">'.substr($data['comment'],250).'</span>';
	        }else{
	            $comment = $data['comment'];
	        }
	        if($data['userid']==$_SESSION['userid']&&$remote=NULL) $delete = '<a href="javascript: deleteComment('.$data['id'].','.$data['cunityId'].');"><img src="style/'.$_SESSION['style'].'/img/del_mail.png" id="'.$data['id'].'_del" class="del_comment_img" style="display: none;"/></a>';
	        else $delete ="";

	        $remote = ($data['remote']==NULL);
	        $replaces = array(
	            "COMMENT_ID"=>$data['id'],
	            "COMMENT_TIME"=>showDate('date_time', $data['time']),
	            "STYLE"=>$_SESSION['style'],
	            "COMMENT"=>$comment,
	            "AVATAR"=>getSmallAvatar($data['userid'],40,$remote,$data['cunityId']),
	            "USERNAME"=>getUserName($data['userid'],$remote,$data['cunityId']),
	            "USERHASH"=>getUserHash($data['userid'],$remote,$data['cunityId']),
	            "DELETE"=>$delete
	       );
	       $output = $this->cunity->getTemplateEngine()->$this->cunity->getTemplateEngine()->createTemplate('comment', $replaces);
	    }
	    return $output;
	}

	function getYouTubeData($id){
	    set_include_path(get_include_path() . PATH_SEPARATOR . '../classes');
	    require_once 'Zend/Gdata/Youtube.php';
	    $video = array();

	    try {
	        $yt = new Zend_Gdata_YouTube();

	        $videoEntry = $yt->getVideoEntry($id);

	            $videoThumbnails = $videoEntry->getVideoThumbnails();
	            $video = array(
	                'thumbnail' => $videoThumbnails[0]['url'],
	                'title' => $videoEntry->getVideoTitle(),
	                'description' => $videoEntry->getVideoDescription(),
	                'tags' => implode(', ', $videoEntry->getVideoTags()),
	                'url' => $videoEntry->getVideoWatchPageUrl(),
	                'flash' => $videoEntry->getFlashPlayerUrl(),
	                'dura' => $videoEntry->getVideoDuration(),
	                'id' => $videoEntry->getVideoId()
	            );

	    } catch (Exception $e) {
	        echo $e->getMessage();
	        exit();

	    }

	    return $video;
	}

	public function checkVideo($str){
	    if(preg_match("/www\.youtube/",$str)){
		    preg_match('/[\\?\\&]v=([^\\?\\&]+)/',$str,$matches);
		    $youtubeData = $this->getYouTubeData($matches[1]);
			if(strlen($youtubeData['description'])>200)
			          $youtubeData['description'] = substr($youtubeData['description'],0,200).'...';
			$video='
	            <div class="insert_video_status">
	                <img src="'.$youtubeData['thumbnail'].'" class="video_status_img" id=""/>
	                <div class="video_status_description">
	                    <h3><a href="'.$youtubeData['url'].'" target="_blank" style="font-size:12px">'.$youtubeData['title'].'</a></h3>
	                    <a href="http://www.youtube.com" style="color:#0391C1;font-size:11px;">www.youtube.com</a>
	                    <p>'.$youtubeData['description'].'</p>
	                </div>
				</div>
                <div class="insert_video_status_text" style="border-top: 1px dashed #ccc">
                    <textarea class="input" id="watermark_video" name="watermark" style="width: 494px; background-color: #FFFFFF; border: 0px; border-radius: 5px; height: 50px; padding: 3px;">'.$youtubeData['url'].'</textarea>
                </div>';
			return array("video"=>$video,"status"=>1);
		}return array("video"=>false,"status"=>0);
	}

	public function deleteStatus($id,$remote,$cunityId,$ownUserId,$ownCunityId=0,$remoteDelete=true){
		$verify=false;
		if($remote&&$cunityId>0){
			require_once 'Cunity_Connector.class.php';
			$connector = new Cunity_Connector($this->cunity);
			$id=$connector->deleteStatus($id,$_SESSION['userid'],$cunityId);
			if(!$id)
				return false;
			else $verify=true;
		}
		$data=$this->getStatusData($id);
		if(($data['type']!="friend"&&(($data['userid']==$ownUserId && ($data['remote']==NULL||$row['remote']=="" || $data['remote']=="pinboard" || ($data['remote']=="user"&&$data['cunityId']==$ownCunityId)))||($data['pinboard_id']==$ownUserId&&($data['remote']==NULL||$row['remote']==""||$data['remote']=="user"||($data['remote']=="pinboard"&&$data['cunityId']==$ownCunityId)))))||$verify){
			if($data['remoteId']>0&&!$remote&&$cunityId==0&&$remoteDelete&&!$verify){
				require_once 'Cunity_Connector.class.php';
				$connector = new Cunity_Connector($this->cunity);
				$result=$connector->deleteStatus($data['remoteId'],$_SESSION['userid'],$data['cunityId']);
				if(!$result)
					return false;
			}
			$res = $this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."pinboard WHERE ".$this->cunity->getConfig("db_prefix")."pinboard.status_id=".$id);
			if($data['type']=="image"){
				require_once 'Cunity_Galleries.class.php';
				$galleries = new Cunity_Galleries($this->cunity);
				if($galleries->getImageData($data['message'])===false)
					return true;
				return $galleries->deleteImage($data['message']);
			}else{
				$commentsQuery = $this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."comments WHERE ressource_id = '".$id."' AND ressource_name = 'pinboard'");
				$likeQuery = $this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."likes WHERE ressource_id = '".$id."' AND ressource_name = 'pinboard'");
				return true;
			}
		}else return false;
	}

	public function imgUpload(array $request,array $files){
			if(isset($request['p'])){
			    $pinboard_id = $request['p'];
			}else{
			    $pinboard_id = 0;
			}
			if(isset($request['r'])){
			    $receiver = $request['r'];
			}else{
			    $receiver = "main";
			}
			if($request['cid']>0){
				$remote=true;
				$cunityId = $request['cid'];
				$remoteField = 'pinboard';
			}else{
				$remote=false;
				$cunityId=0;
				$remoteField=NULL;
			}

		    $valid_formats = array("jpg", "png", "gif", "bmp");
			$name = $files['pinimg']['name'];
			$size = $files['pinimg']['size'];

			if($request['status_message']!=$this->lang['pinboard_image_watermark'])
			    $title = $request['status_message'];
			else
			   $title = "";

			$title= nl2br(mysql_real_escape_string(rawurldecode($title)));
			$title = preg_replace_callback('#https?://[^/\s]{4,}(/[^\s]*)?#s', 'findUrl', $title);

			require_once 'Cunity_Galleries.class.php';
			$galleries = new Cunity_Galleries($this->cunity);
			$album_id = $galleries->getPinboardAlbumId();
			if(!$album_id)
				$album_id=$galleries->newAlbum(array("album_name"=>"-cunity-wall-images-","album_descr"=>"-cunity-wall-images-","album_privacy"=>1));
			$request['id'] = $album_id;
			$result=$galleries->uploadSingleFile($request,$_FILES);
			if(isset($result['id'])&&!isset($result['status'])){
				$galleries->updateImageData($result['id'],'title',$title);
				$statusid = insertPinboard($_SESSION['userid'], $pinboard_id, $result['id'], 'image', $receiver);
				if($remote){
					$userData= json_decode(base64_decode($request['userData']),true);
					require_once 'Cunity_Connector.class.php';
					$connector = new Cunity_Connector($this->cunity);
					$connector->insertPinboard($_SESSION['userid'], $pinboard_id, $message, $type, $receiver,$cunityId,$userData);
					unset($connector);
				}
	            $_SESSION['last_status_id-'.$cunityId.'-'.$pinboard_id] = $statusid;
	            $right_cont = '<div class="main_list_right_cont"><img src="style/'.$_SESSION['style'].'/img/fail.png" title="'.$this->lang['pinboard_delete_status'].'" class="delete_status" style="display: none;"id="delete_status-'.$row['status_id'].'"/></div>';
	            $dropdown = '<button id="'.$statusid.'" class="pinboard_dropdown">&nbsp;</button>';

	            if($pinboard_id==0)
	                $postedText=$this->lang['pinboard_added_photo'];
	            else
	                $postedText = "";

	            $replaces = array(
	            		"STYLE"=>$_SESSION['style'],
	            		"AVATAR"=>getAvatarPath($_SESSION['userid']),
	            		"STATUS_ID"=>$statusid,
	            		"RESSOURCE_ID"=>$result['id'],
	            		"STATUS_TYPE"=>$type,
	            		"CUNITYID"=>$cunityId,
	            		"REMOTEID"=>0,
	            		"pinboard_comment"=>$this->lang['pinboard_comment'],
	            		"DISPLAY"=>"none"
	            );
	            $newComment = $this->cunity->getTemplateEngine()->createTemplate('pinboard_new_comment', $replaces);

	            $right_cont = '<div class="main_list_right_cont"><img src="style/'.$_SESSION['style'].'/img/fail.png" title="'.$this->lang['pinboard_delete_status'].'" class="delete_status" style="display: none;"id="delete_status-'.$statusid.'"/></div>';
	            $dropdown = '<button id="'.$statusid.'" class="pinboard_dropdown">&nbsp;</button>';

	            $replaces = array(
	            		"USERHASH"=>getUserHash($_SESSION['userid']),
	            		"USERNAME"=>getUserName($_SESSION['userid']),
	            		"USERNAME_EXTRA"=>$postedText,
	            		"STATUS_MESSAGE"=>'<a href="'.$result['file'].'" id="'.$result['id'].'" cid="'.$cunityId.'" class="pinboard_image_link"><img src="'.$result['file'].'" style="width: 300px; padding: 3px; background-color: #FFF; border:1px solid #ccc; margin-top: 5px;"/></a><p style="padding: 2px; color: #333; font-size: 13px; margin: 3px 0px;">'.$title.'</p>',
	            		"STYLE"=>$_SESSION['style'],
	            		"AVATAR"=>getSmallAvatar($_SESSION['userid'],50),
	            		"STATUS_ID"=>$statusid,
	            		"RESSOURCE_ID"=>$result['id'],
	            		"STATUS_TYPE"=>$type,
	            		"STATUS_TIME"=>showDate('date_time', $_SESSION['time']),
	            		"RIGHT_CONT"=>$right_cont,
	            		"NEW_COMMENT"=>$newComment,
	            		"DROPDOWN_BUTTON"=>$dropdown,
	            		"pinboard_like"=>$this->lang['pinboard_like'],
	            		"pinboard_comment"=>$this->lang['pinboard_comment'],
	            		"pinboard_dislike"=>$this->lang['pinboard_dislike'],
	            		"pinboard_delete_status"=>$this->lang['pinboard_delete_status']
	            );
	            $pinBoard = $this->cunity->getTemplateEngine()->createTemplate('pinboard_entry', $replaces);

	            if($pinboard_id != 0){
	                $this->cunity->getNotifier()->addNotification('post_on_pinboard', $pinboard_id, $_SESSION['userid'], mysql_insert_id());
	            }
				header('Content-Type: text/html; charset=utf-8', true,200);
		        print '<script src="../includes/jquery/jquery.js" language="javascript"></script>';
		    	echo '<div id="content">'.$pinBoard.'</div><script language="javascript" type="text/javascript">parent.showUploadedImage($("#content").html());</script>';
		    	return;
			}else{
				echo "error while uploading image!";
				return;
			}
	}

	private function decodeMessage($str){
		$str= nl2br(mysql_real_escape_string(rawurldecode($str)));
		$str = preg_replace_callback('#https?://[^/\s]{4,}(/[^\s]*)?#s', 'findUrl', $str);
		return $str;
	}

	public function insertPinboardStatus(array $request){
		try {
			$type="status";
			$remoteStatusId=0;

			$pinboard_id = (isset($request['p'])) ? $request['p'] : 0;
			$receiver = (isset($request['r'])) ? $request['r'] : "main";

			if($request['cid']>0){
				$remote=true;
				$cunityId = $request['cid'];
				$remoteField = 'pinboard';
			}else{
				$remote=false;
				$cunityId=0;
				$remoteField=NULL;
			}

		    if($pinboard_id == $_SESSION['userid']&&($receiver=="friend"||$receiver=="main")&&!$remote){
	            $pinboard_id = 0;
	            $receiver = "main";
	        }

	        $message = urldecode($request['statusMessage']);
	        $link = (isset($request['link'])) ? urldecode($request['link']) : "";

			if($link!=""&&preg_match("/www\.youtube/",$link)){
				preg_match('/[\\?\\&]v=([^\\?\\&]+)/',$link,$matches);
	            $youtubeData = $this->getYouTubeData($matches[1]);
	            $youtubeData['description'] = preg_replace_callback('#https?://[^/\s]{4,}(/[^\s]*)?#s', 'findUrl', $youtubeData['description']);
	            $message = array('message'=>$this->decodeMessage($message),'title'=>htmlentities($youtubeData['title'], ENT_QUOTES, 'UTF-8'),'description'=>htmlentities($youtubeData['description'], ENT_QUOTES, 'UTF-8'),'image'=>$youtubeData['thumbnail'],'v'=>$youtubeData['id'],'url'=>$youtubeData['url']);
	            foreach($message AS $key => $val)
	            	$message[$key] = mysql_real_escape_string($val);
	            $message = json_encode($message);
	            $type = "video";
			}else{
				$this->decodeMessage($message);
			}

			$id = $this->insertPinboard($_SESSION['userid'], $pinboard_id, $message, $type, $receiver,$cunityId,$remoteField,$remoteStatusId);
			if($remote){
				$userData= json_decode(base64_decode($request['userData']),true);
				require_once 'Cunity_Connector.class.php';
				$connector = new Cunity_Connector($this->cunity);
				$remoteStatusId = $connector->insertPinboard($_SESSION['userid'],$id,$pinboard_id, $message, $type, $receiver,$cunityId,$userData);
				$this->updateStatusField($id,'remoteId',$remoteStatusId);
				unset($connector);
			}
			$_SESSION['last_status_id-'.$cunityId.'-'.$pinboard_id] = $id;

			$message = ($type=="video") ? $this->createYoutubeObject($message,$id) : $message;

			$replaces = array(
					"STYLE"=>$_SESSION['style'],
					"AVATAR"=>getAvatarPath($_SESSION['userid']),
					"STATUS_ID"=>$id,
					"RESSOURCE_ID"=>$id,
					"STATUS_TYPE"=>"status",
					"CUNITYID"=>$cunityId,
					"REMOTEID"=>$remoteStatusId,
					"pinboard_comment"=>$this->lang['pinboard_comment'],
					"DISPLAY"=>"none"
			);
			$newComment = $this->cunity->getTemplateEngine()->createTemplate('pinboard_new_comment', $replaces);

	        $right_cont = '<div class="main_list_right_cont"><img src="style/'.$_SESSION['style'].'/img/fail.png" title="'.$this->lang['pinboard_delete_status'].'" class="delete_status" style="display: none;"id="delete_status-'.$id.'"/></div>';
	        $dropdown = '<button id="'.$id.'" class="pinboard_dropdown">&nbsp;</button>';

			$replaces = array(
	            "USERHASH"=>getUserHash($_SESSION['userid']),
	            "USERNAME"=>getUserName($_SESSION['userid']),
	            "STYLE"=>$_SESSION['style'],
	            "AVATAR"=>getSmallAvatar($_SESSION['userid'],50),
	            "STATUS_ID"=>$id,
				"RESSOURCE_ID"=>$id,
				"STATUS_TYPE"=>"status",
	            "STATUS_TIME"=>showDate('date_time', $_SESSION['time']),
	            "STATUS_MESSAGE"=>$message,
	            "RIGHT_CONT"=>$right_cont,
				"NEW_COMMENT"=>$newComment,
	            "DROPDOWN_BUTTON"=>$dropdown,
	            "pinboard_like"=>$this->lang['pinboard_like'],
	            "pinboard_comment"=>$this->lang['pinboard_comment'],
	            "pinboard_dislike"=>$this->lang['pinboard_dislike'],
	            "pinboard_delete_status"=>$this->lang['pinboard_delete_status']
	        );

	        $pinBoard = $this->cunity->getTemplateEngine()->createTemplate('pinboard_entry', $replaces);

			if($pinboard_id != 0){
	            $this->cunity->getNotifier()->addNotification('post_on_pinboard', $pinboard_id, $_SESSION['userid'], mysql_insert_id());
	        }
	        return array('statusMessage'=>$pinBoard,'status'=>1);
		} catch (Exception $e) {
			return array('status'=>0);
		}
	}
	function createYoutubeObject($message,$id) {
	    $data = json_decode($message,true);
	    $data['title'] = html_entity_decode($data['title'],ENT_QUOTES, 'UTF-8');
	    $data['description'] = html_entity_decode($data['description'],ENT_QUOTES, 'UTF-8');
	    if(strlen($data['description'])>200)
			$data['description'] = substr($data['description'],0,200).'<a href="javascript: more_descr(\''.$id.'\')" id="more_descr_'.$id.'"> '.$lang['pinboard_show_more'].'</a><span id="realmoredescr_'.$row['status_id'].'" style="display: none;">'.substr($data['description'],200).'<a href="javascript: less_descr(\''.$id.'\')" id="less_descr_'.$id.'"> '.$lang['pinboard_show_less'].'</a></span>';

	    if(!isset($data['image']))
		    $data['image'] = 'http://i4.ytimg.com/vi/'.$data['v'].'/hqdefault.jpg';
		if(!isset($data['url']))
			$data['url'] = 'http://www.youtube.com/watch?v='.$data['v'];

	    $statusMessage = '<p class="video_message">'.$data['message'].'</p>';
		$statusMessage .= '<img src="'.$data['image'].'" class="video_status_img" id="'.$data['v'].'" title="'.$lang['pinboard_play'].'"/>
	                        <div class="video_status_description" id="video_description-'.$data['v'].'">
	                        	<h3><a href="'.$data['url'].'" target="_blank" style="font-size:12px">'.$data['title'].'</a></h3>
			                    <a href="http://www.youtube.com" class="video_page_link">www.youtube.com</a>
			                    <p>'.$data['description'].'</p>
	                        </div>';
	    return $statusMessage;
	}

	public function getPinboardQuery(array $request,$receiver,$pinboard_id,$lastStatusId,$statusCount,$userid,$cunityId){
		require_once '../includes/functions.php';
		if(isset($request['id'])&&$request['id']!=0){
            $queryString = loadPinboardEntry($request['id']);
        }elseif($pinboard_id==0){
            if(isset($request['do'])&&$request['do']=='refresh')
            	$queryString = getRefreshedMainPinBoard($request['s'],$userid,$lastStatusId);
            elseif(isset($request['type'])&&$request['type']=="loadMoreStatus")
            	$queryString = loadMoreMainPinBoard($request['s'],$userid,$statusCount);
            elseif(isset($request['s'])&&$request['s']!="all")
            	$queryString = loadSortMainPinBoard($request['s'],$userid);
            else
            	$queryString = getMainPinBoard($userid);
        }else{
            if(isset($request['do'])&&$request['do']=='refresh')
            	$queryString = getRefreshedPagePinBoard($pinboard_id, $receiver,$lastStatusId);
            elseif(isset($request['type'])&&$request['type']=="loadMoreStatus")
            	$queryString = loadMorePagePinBoard($pinboard_id,$receiver,$statusCount);
            else
            	$queryString = getPagePinBoard($pinboard_id,$receiver);
        }
        return $queryString;
	}

	private function decodeRemotePinboard($array,$cunityid,$pinboard_id){
		if(!is_array($array))return $array;
		foreach($array AS $template => $data){
			$template=explode('-',$template);
			$data['STYLE'] = $_SESSION['style'];
			$data['COMMENTS'] = $this->decodeRemotePinboard($data['COMMENTS'],$cunityid,$pinboard_id);
			$data['LIKES'] = $this->decodeRemotePinboard($data['LIKES'],$cunityid,$pinboard_id);
			$return .= $this->cunity->getTemplateEngine()->createTemplate($template[0],$data);
		}
		return $return;
	}

	public function getMaxRemoteId(array $array){
		$ids=array();
		foreach($array AS $data) foreach($data AS $d) $ids[] = $d['STATUS_ID'];
		return max($ids);
	}

	public function loadPinboard(array $request,$remotePinboard=false){
		try {
			$pinboard_id = (isset($request['p'])) ? $request['p'] : 0;
			$receiver = (isset($request['r'])) ? $request['r'] : "main";
			if($request['cid']>0){
				$remote=true;
				$cunityId = $request['cid'];
				$remoteField = 'pinboard';
			}else{
				$remote=false;
				$cunityId=0;
				$remoteField=NULL;
			}
			$queryString='';
			$option='on';
			$update = "";
			$standalone = false;
			$pinboardRows=array();
			$remotePinboardRows=array();
			$entryCount=0;
			$i = 0;
			$ids=array();

			if($remotePinboard){
				$ownUserId=$request['userid'];
				$ownCunityId=$request['cunityId'];
			}else{
				$ownUserId=$_SESSION['userid'];
				$ownCunityId=0;
			}

			if($pinboard_id!=0&&$receiver!="event")
			    $receiver = "friend";

			if(!isset($_SESSION['status_count-'.$cunityId.'-'.$pinboard_id])) $_SESSION['status_count-'.$cunityId.'-'.$pinboard_id] = 0;
			if(!isset($_SESSION['last_status_id-'.$cunityId.'-'.$pinboard_id])) $_SESSION['last_status_id-'.$cunityId.'-'.$pinboard_id] = 0;

			$lastStatusId = (isset($request['lastStatusId'])) ? $request['lastStatusId'] : $_SESSION['last_status_id-'.$cunityId.'-'.$pinboard_id];
			$statusCount = (isset($request['statusCount'])) ? $request['statusCount'] : $_SESSION['status_count-'.$cunityId.'-'.$pinboard_id];

			if(!$remote){
				$queryString = $this->getPinboardQuery($request,$receiver,$pinboard_id,$lastStatusId,$statusCount,$ownUserId,$ownCunityId);
			}else{
				require_once 'Cunity_Connector.class.php';
				$connector = new Cunity_Connector($this->cunity);
				$userData= json_decode(base64_decode($request['userData']),true);
				$remotePin = $connector->getRemotePinboard($ownUserId,$cunityId, $pinboard_id, $receiver,$request,$userData,$lastStatusId,$statusCount);
				$pin = "";
				foreach(json_decode($remotePin,true) AS $p)
					$pin .= $this->decodeRemotePinboard($p,$cunityId,$pinboard_id);
				$entryCount = count(json_decode($remotePin,true));

				if(isset($request['id'])&&$request['id']!=0)
					$standalone=true;
				else if($request['do']=="refresh"&&$entryCount>0)
					$_SESSION['status_count-'.$cunityId.'-'.$pinboard_id] = 10;
				else if($request['type']=='loadMoreStatus'&&$entryCount>0)
					$_SESSION['status_count-'.$cunityId.'-'.$pinboard_id] = (int)$_SESSION['status_count-'.$cunityId.'-'.$pinboard_id]+10;
				else if($entryCount>0)
					$_SESSION['status_count-'.$cunityId.'-'.$pinboard_id] = 10;

				if($entryCount>0&&!isset($request['type'])&&$request['type']!="loadMoreStatus")
					$_SESSION['last_status_id-'.$cunityId.'-'.$pinboard_id] = $this->getMaxRemoteId(json_decode($remotePin,true));

				return array('count'=>count(json_decode($remotePin,true)),'pinBoardRows'=>$pin,'status'=>1,'option'=>"on");
				unset($connector);
			}

			$pinBoardStatusList = $this->cunity->getDb()->query($queryString) or die(mysql_error().$queryString);
			while($row = mysql_fetch_assoc($pinBoardStatusList))
				$pinboardRows[] = $row;
			$entryCount = count($pinboardRows);

			if(isset($request['id'])&&$request['id']!=0)
				$standalone=true;
			else if($request['do']=="refresh"&&$entryCount>0)
				$_SESSION['status_count-'.$cunityId.'-'.$pinboard_id] = 10;
			else if($request['type']=='loadMoreStatus'&&$entryCount>0)
				$_SESSION['status_count-'.$cunityId.'-'.$pinboard_id] = (int)$_SESSION['status_count-'.$cunityId.'-'.$pinboard_id]+10;
			else if($entryCount>0)
				$_SESSION['status_count-'.$cunityId.'-'.$pinboard_id] = 10;

			$pinBoard='';
			if($entryCount == 0 && $standalone){
	            $pinBoard = newCunityError($this->lang['pinboard_not_found']);
	            return array('pinBoardRows'=>$pinBoard,'status'=>1);
	        }elseif($entryCount == 0 && $request['do'] != 'refresh' && $request['type'] != 'loadMoreStatus' && !$standalone){
	            $pinBoard = newCunityError($this->lang['pinboard_no_results']);
	            return array('pinBoardRows'=>$pinBoard,'status'=>1);
	        }

	        foreach($pinboardRows AS $row){
				$id = $row['status_id'];
				$statusMessage='';
				$likeRes = "";
				$likes = "";
				$output = "";
				$remote=false;
				$x = 0;
				$commentCount=0;
				$likesRemote=array();
				$commentsRemote = array();

	            if(!checkPrivacy($row['userid'], $ownUserId,'pinboard_viewing'))
			        continue;

			    $remote = ($row['cunityId']>0);
	            $statusMessage = ($row['type']=="video") ? $this->createYoutubeObject($row['message'],$id) : $row['message'];

	            if($row['type']=='image'){
	                $ressource_name='galleries';
	                $ressource_id = $row['message'];
	            }else{
	                $ressource_name='pinboard';
	                $ressource_id = $id;
	            }
	            $like = $this->liker->createLikes($ressource_id, $ressource_name,$ownUserId,$ownCunityId);
	            if($like[2]>0){
	                $replaces = array(
	                    "LIKELINE"=>$like[1],
	                    "LIKES"=>$like[0],
	                    "STYLE"=>$_SESSION['style']
	                );
	                $likes = $this->cunity->getTemplateEngine()->createTemplate('pinboard_likes', $replaces);
	                $likesRemote['pinboard_likes']= $replaces;
	            }

	            $comments = $this->commentor->getComments($ressource_id,$ressource_name);
	        	$commentCount = count($comments);
	        	if($commentCount > 4 && $commentCount <= 50 &&!$standalone){
	        	    $output .= '<div class="line"><span class="show_comments" id="status_comment-'.$id.'">'.$this->lang['pinboard_show_all'].' '.$commentCount.' '.$this->lang['pinboard_comments'].'</span></div>';
	        	}elseif($commentCount > 50 &&!$standalone){
	                $output .= '<div class="line"><span class="load_comments" id="status_comment-'.$id.'">'.$this->lang['pinboard_show_all'].' '.$commentCount.' '.$this->lang['pinboard_comments'].'</span></div>';
	                $queryString='SELECT * FROM '.$this->cunity->getConfig("db_prefix").'comments WHERE ressource_id='.$id.' ORDER BY id ASC LIMIT '.($commentCount-4).'4';
	            	$commentsRes = $this->cunity->getDb()->query($queryString) ;
	            }
	            $_SESSION['last_comment_id-'.$row['status_id'].'-'.$pinboard_id] = 0;
	            foreach($comments AS $data){
	                $_SESSION['last_comment_id-'.$id.'-'.$pinboard_id] = $data['id'];
	        	    if(strlen($data['comment']) > 250){
	                    $comment = substr($data['comment'],0,250);
	                    $comment .= ' <span class="read_more_comment" onclick="more_comment_cont('.$data['id'].');" id="more_comment-'.$data['id'].'">('.$this->lang['pinboard_show_more'].')</span>';
	                    $comment .= '<span class="more_comment" id="more_comment_cont-'.$data['id'].'" style="display: none;">'.substr($data['comment'],250).' <span class="read_less_comment" onclick="less_comment_cont('.$data['id'].');" id="less_comment-'.$data['id'].'">('.$this->lang['pinboard_show_less'].')</span></span>';
	                }else{
	                    $comment = $data['comment'];
	                }

	                $delete = ($data['userid']==$ownUserId||$row['userid']==$ownUserId) ? '<a href="javascript:deleteComment('.$data['id'].','.$data['cunityId'].');" class="ui-icon ui-icon-close del_comment_link" id="'.$data['id'].'_del" style="display: none;">&nbsp;</a>' : '';
	                $display = ($x < ($commentCount - 4)&&!$standalone) ? "none" : "";

	                $replaces = array(
	                    "COMMENT_ID"=>$data['id'],
	                    "COMMENT_TIME"=>showDate('date_time', $data['time']),
	                    "STYLE"=>$_SESSION['style'],
	                    "COMMENT"=>$comment,
	                    "AVATAR"=>getSmallAvatar($data['userid'],40,$data['remote'],$data['cunityId']),
	                    "USERNAME"=>getUserName($data['userid'],$data['remote'],$data['cunityId']),
	                    "USERHASH"=>$data['cunityId']."-".getUserHash($data['userid'],$data['remote'],$data['cunityId']),
	                    "DELETE"=>$delete,
	                    "DISPLAY"=>$display,
	                    "CLASS"=> 'status_comment-'.$id

	                );
	                $output .= $this->cunity->getTemplateEngine()->createTemplate('comment', $replaces);
	                $commentsRemote['comment-'.$x] = $replaces;
	                $x++;
	            }

	            $display = ($commentCount>0) ? "" : "none";

                $replaces = array(
                    "STYLE"=>$_SESSION['style'],
                    "AVATAR"=>getAvatarPath($ownUserId),
                    "STATUS_ID"=>$row['status_id'],
                	"RESSOURCE_ID"=>$ressource_id,
                	"STATUS_TYPE"=>$row['type'],
                	"CUNITYID"=>$row['cunityId'],
                	"REMOTEID"=>$row['remoteId'],
                    "pinboard_comment"=>$this->lang['pinboard_comment'],
                    "DISPLAY"=>$display
                );
                $newComment = $this->cunity->getTemplateEngine()->createTemplate('pinboard_new_comment', $replaces);
                $newCommentRemote['pinboard_new_comment'] = $replaces;

                $liked=$this->liker->getLike($ownUserId,$ressource_id,$ressource_name,$ownCunityId);
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


	            if($row['type']!="friend"&&(($row['userid']==$ownUserId && ($row['remote']==NULL || $row['remote']=="pinboard" || ($row['remote']=="user"&&$row['cunityId']==$ownCunityId)))||($row['pinboard_id']==$ownUserId&&($row['remote']==NULL||$row['remote']=="user"||($row['remote']=="pinboard"&&$row['cunityId']==$ownCunityId))))){
	                $right_cont = '<div class="main_list_right_cont" style="margin:0px;padding:0px"><button onclick="deleteStatus('.$row['status_id'].');" class="jui-button" icon="ui-icon-closethick" text="0"style="display: none;width:1.8em;height:1.8em"id="delete_status-'.$row['status_id'].'">'.$this->lang['pinboard_delete_status'].'</button></div>';
	                $dropdown = '<button id="'.$row['status_id'].'" class="pinboard_dropdown jui-button" icon="ui-icon-triangle-1-s" text="0">&nbsp;</button>';
	            }else{
	                $right_cont = "";
	                $dropdown = "";
	            }

	            if($row['type'] == 'image'){
	                $imgid = $row['message'];
	                $imageRes = $this->cunity->getDb()->query("SELECT album_id,file,title FROM ".$this->cunity->getConfig("db_prefix")."galleries_imgs WHERE id = '".mysql_real_escape_string($imgid)."'");
	                $imageData = mysql_fetch_assoc($imageRes);

	                if($row['pinboard_id']==0)
	                    $postedText=$this->lang['pinboard_added_photo'];
	                else
	                    $postedText = "";

	                $username = "";
	                if($row['pinboard_id'] != 0 && $row['pinboard_id'] != $pinboard_id && ($pinboard_id == 0 || $pinboard_id = $row['userid']) && $row['receiver']=='friend'){
	                    $username = getUserName($row['userid'],$remote,$row['cunityId']).'</a> <span style="display: inline-block" class="ui-icon-text ui-icon-triangle-1-e">&nbsp;</span> <a href="profile.php?user='.getUserHash($row['pinboard_id']).'" class="main_list_small_name">'.getUserName($row['pinboard_id']);
	                }elseif($row['pinboard_id'] != 0 && $row['pinboard_id'] != $pinboard_id && ($pinboard_id == 0 || $pinboard_id = $row['userid']) && $row['receiver']=='event'){
	                    $username = getUserName($row['userid'],$remote,$row['cunityId']).'</a> <span style="display: inline-block" class="ui-icon-text ui-icon-triangle-1-e">&nbsp;</span> <a href="events.php?e='.getEventHash($row['pinboard_id']).'" class="main_list_small_name">'.getEventName($row['pinboard_id']);
	                }else{
	                    $username = getUserName($row['userid'],$remote,$row['cunityId']);
	                }
	                $remote = ($row['cunityId']>0);
	                $row['cunityId'] = ($row['cunityId']==0&&$remotePinboard) ? $this->cunity->getcunityId() : $row['cunityId'];
	                $replaces = array(
	                    "USERHASH"=>getUserHash($row['userid'],$remote,$row['cunityId']),
	                	"USERNAME"=>$username,
	                	"USERNAME_EXTRA"=>$postedText,
	                    "STYLE"=>$_SESSION['style'],
	                    "AVATAR"=>getSmallAvatar($row['userid'],50,$remote,$row['cunityId']),
	                    "STATUS_ID"=>$row['status_id'],
	                    "STATUS_MESSAGE"=>'<a href="'.$this->cunity->getSetting("url").'/'.$imageData['file'].'" class="pinboard_image_link" id="'.$imgid.'" cid="'.$row['cunityId'].'"><img src="'.$this->cunity->getSetting("url").'/'.$imageData['file'].'" style="width: 300px; padding: 3px; background-color: #FFF; border:1px solid #ccc; margin-top: 5px;"/></a><p style="padding: 2px; color: #333; font-size: 13px; margin: 3px 0px;">'.$imageData['title'].'</p>'
	                );
	            }else if($row['type'] == 'friend'){
                    $f1=$row['message'];
                    $f2 = $row['userid'];

	                $replaces = array(
	                    "USERHASH"=>getUserHash($f1,$remote,$row['cunityId']),
	                    "STYLE"=>$_SESSION['style'],
	                    "AVATAR"=>getSmallAvatar($f1,50,$remote,$row['cunityId']),
	                    "STATUS_ID"=>$row['status_id'],
	                    "USERNAME_EXTRA"=>'<a href="profile.php?user='.getUserHash($f1,$remote,$row['cunityId']).'" class="main_list_small_name">'.getUserName($f1,$remote,$row['cunityId']).'</a> '.$this->lang['pinboard_and'].' <a href="profile.php?user='.getUserHash($f2,$remote,$row['cunityId']).'" class="main_list_small_name">'.getUserName($f2,$remote,$row['cunityId']).'</a> '.$this->lang['pinboard_now_friends']
	                );
	            }elseif($row['type'] == 'profile_update'){
	                if(getSex($row['userid']) == 1)
	                    $update_text = $this->lang['pinboard_profile_updated_m'];
	                else
	                    $update_text = $this->lang['pinboard_profile_updated_f'];

	                $replaces = array(
	                    "USERHASH"=>getUserHash($row['userid'],$remote,$row['cunityId']),
	                    "STYLE"=>$_SESSION['style'],
	                    "AVATAR"=>getSmallAvatar($row['userid'],50,$remote,$row['cunityId']),
	                    "STATUS_ID"=>$row['status_id'],
	                    "USERNAME"=>getUserName($row['userid'],$remote,$row['cunityId']),
	                    "USERNAME_EXTRA"=>$update_text
	                );
	            }elseif($row['type'] == 'image_update'){
	                if(getSex($row['userid']) == 1)
	                    $update_text = $this->lang['pinboard_image_updated_m'];
	                else
	                    $update_text = $this->lang['pinboard_image_updated_f'];

	                $replaces = array(
	                    "USERHASH"=>getUserHash($row['userid'],$remote,$row['cunityId']),
	                    "STYLE"=>$_SESSION['style'],
	                    "AVATAR"=>getSmallAvatar($row['userid'],50,$remote,$row['cunityId']),
	                    "STATUS_ID"=>$row['status_id'],
	                	"USERNAME"=>getUserName($row['userid'],$remote,$row['cunityId']),
	                    "USERNAME_EXTRA"=>$update_text
	                );
	            }else{
	            	$userhash = getUserHash($row['userid'],($row['remote']=="user"),$row['cunityId']);
	                $username = getUserName($row['userid'],($row['remote']=="user"),$row['cunityId']);
	                $userhash2=getUserHash($row['pinboard_id'],($row['remote']=="pinboard"),$row['cunityId']);
	                $username2=getUserName($row['pinboard_id'],($row['remote']=="pinboard"),$row['cunityId']);
	                if($row['remote']=="user"){
	                	$userhash = $row['cunityId']."-".$userhash;
	                	$userhash2 = $this->cunity->getcunityId()."-".$userhash2;
	                }else{
	                	$userhash = $this->cunity->getcunityId()."-".$userhash;
	                	$userhash2 = $row['cunityId']."-".$userhash2;
	                }
	                if($row['pinboard_id'] != 0 && $row['pinboard_id'] != $pinboard_id && ($pinboard_id == 0 || $pinboard_id = $row['userid']) && $row['receiver']=='friend'){
	                    $username = $username.'</a> <span style="display: inline-block" class="ui-icon-text ui-icon-triangle-1-e">&nbsp;</span> <a href="profile.php?user='.$userhash2.'" class="main_list_small_name">'.$username2;
	                }elseif($row['pinboard_id'] != 0 && $row['pinboard_id'] != $pinboard_id && ($pinboard_id == 0 || $pinboard_id = $row['userid']) && $row['receiver']=='event'){
	                    $username = $username.'</a> <span style="display: inline-block" class="ui-icon-text ui-icon-triangle-1-e">&nbsp;</span> <a href="events.php?e='.getEventHash($row['pinboard_id']).'" class="main_list_small_name">'.getEventName($row['pinboard_id']);
	                }else{
	                    $username = $username;
	                }

	                $replaces = array(
	                    "USERHASH"=>$userhash,
	                    "USERNAME"=>$username,
	                    "STYLE"=>$_SESSION['style'],
	                    "AVATAR"=>getSmallAvatar($row['userid'],50,($row['remote']=="user"),$row['cunityId']),
	                    "STATUS_ID"=>$row['status_id'],
	                	"STATUS_MESSAGE"=>$statusMessage
	                );
	            }
	            $cunityIdTpl=($remote) ? $row['cunityId']."-" : "";

	            $mustReplaces=array(
            		"CUNITYID"=>$row['cunityId'],
            		"STATUS_TIME"=>showDate('date_time', $row['time']),
            		"STATUS_TYPE"=>$row['type'],
            		"RESSOURCE_ID"=>$ressource_id,
                    "COMMENTS"=>$output,
                	"NEW_COMMENT"=>$newComment,
                    "LIKES"=>$likes,
                    "LIKE_DISPLAY"=>$likeDisplay,
                    "DISLIKE_DISPLAY"=>$dislikeDisplay,
                    "DROPDOWN_BUTTON"=>$dropdown,
                	"RIGHT_CONT"=>$right_cont,
            		"REMOTEID"=>$row['remoteId'],
                    "pinboard_like"=>$this->lang['pinboard_like'],
                    "pinboard_comment"=>$this->lang['pinboard_comment'],
                    "pinboard_dislike"=>$this->lang['pinboard_dislike'],
                    "pinboard_delete_status"=>$this->lang['pinboard_delete_status']
	            );
	            $replaces =array_merge($replaces,$mustReplaces);
	            if($remotePinboard){
	            	$replacesremote = $replaces;
		            $replacesremote['LIKES'] = $likesRemote;
		            $replacesremote['COMMENTS'] = $commentsRemote;
		            $remotePinboardRows[]['pinboard_entry-'.$i]= $replacesremote;
		            $i++;
	            }
	            $pinBoard .= $this->cunity->getTemplateEngine()->createTemplate('pinboard_entry', $replaces);
	            $ids[] = $row['status_id'];
			}

			if($remotePinboard)
				return $remotePinboardRows;
	        if(count($ids)>0&&!isset($request['type'])&&$request['type']!="loadMoreStatus")
	            $_SESSION['last_status_id-'.$cunityId.'-'.$pinboard_id] = max($ids);

	        if(mysql_num_rows($pinBoardStatusList)<10)
				$option='off';
			return array('count'=>mysql_num_rows($pinBoardStatusList),'pinBoardRows'=>$pinBoard,'status'=>1,'option'=>$option);
		} catch (Exception $e) {
			return array('pinBoardRows'=>"error",'status'=>0);
		}
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

	public function addStatusComment(array $request){
		$cunityId=$request['cid'];
		$cunityData= ($request['userData']!="") ? json_decode(base64_decode($request['userData']),true) : array();
		$statusId=$request['sid'];
		$ressourceId=$request['rid'];
		$statusType=$request['sType'];
		$ressource_name = ($statusType=="image") ? "galleries" : "pinboard";
		if($cunityId>0){
			require_once 'Cunity_Connector.class.php';
			$connector=new Cunity_Connector($this->cunity);
			$comment_id = $connector->addComment($_SESSION['userid'],$cunityId,$ressourceId,$ressource_name,$request['message'],$cunityData);
		}else if($cunityId==0){
			$comment_id=$this->commentor->addComment($_SESSION['userid'],$ressourceId,$ressource_name,$request['message'],$cunityId);
		}
		if(!$comment_id)
			return false;
		if($cunityId==0)
			$this->sendCommentNotifications($statusData['userid'], $statusData['pinboard_id'], $request['id'], $sender);

		$replaces = array(
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

	public function likeStatus($request){
		$cunityId=$request['cid'];
		$cunityData= ($request['userData']!="") ? json_decode(base64_decode($request['userData']),true) : array();
		$statusId=$request['sid'];
		$ressourceId=$request['rid'];
		$rn = ($request['sType']=="image") ? "galleries" : "pinboard";
	    $likes = "";
	    if($cunityId>0){
	    	require_once 'Cunity_Connector.class.php';
	    	$connector=new Cunity_Connector($this->cunity);
	    	$likeRes=$connector->like($_SESSION['userid'],$cunityId,$ressourceId,$rn,"like",$cunityData);
	    	unset($connector);
	    }else if($cunityId==0){
	    	$liked = $this->liker->getLike($_SESSION['userid'],$ressourceId,$rn,$cunityId);
	    	if(!$liked||($liked==1&&$this->liker->deleteLike($_SESSION['userid'],$ressourceId,$rn,$cunityId)))
	    		$likeRes=$this->liker->like($_SESSION['userid'],$ressourceId,$rn,$cunityId);
	    	else return array("status"=>0,"msg"=>"already liked or db error");
	    }
	    if($likeRes!==false){
	    	$like = ($cunityId>0) ? $likeRes : $this->liker->createLikes($ressourceId, $rn,$_SESSION['userid'],0);
	    	$replaces = array(
	    			"LIKELINE"=>$like[1],
	    			"LIKES"=>$like[0],
	    			"STYLE"=>$_SESSION['style']
	    	);
	    	$likes = ($like[2]>0) ? $this->cunity->getTemplateEngine()->createTemplate('pinboard_likes', $replaces) : "";
	    }else return array("status"=>0,"msg"=>"Db-Error!");
	    return array('likes'=>$likes,'status'=>(int)$likeRes);
	}

	public function dislikeStatus($request){
		$cunityId=$request['cid'];
		$cunityData= ($request['userData']!="") ? json_decode(base64_decode($request['userData']),true) : array();
		$statusId=$request['sid'];
		$ressourceId=$request['rid'];
		$rn = ($request['sType']=="image") ? "galleries" : "pinboard";
	    $likes = "";
	    if($cunityId>0){
	    	require_once 'Cunity_Connector.class.php';
	    	$connector=new Cunity_Connector($this->cunity);
	    	$likeRes=$connector->like($_SESSION['userid'],$cunityId,$ressourceId,$rn,"dislike",$cunityData);
	    	unset($connector);
	    }
	    else if($cunityId==0){
		    $liked = $this->liker->getLike($_SESSION['userid'],$ressourceId,$rn,$cunityId);
		    if($this->cunity->getSetting("allow_dislike")==1){
		    	if($liked==1) // status already disliked
		    		return array("status"=>0,"msg"=>"already disliked");
		    	elseif($liked==0||!$liked){
					if($this->liker->deleteLike($_SESSION['userid'],$ressourceId,$rn,$cunityId))
						$likeRes=$this->liker->dislike($_SESSION['userid'],$ressourceId,$rn,$cunityId);
		    	}else return array("status"=>0,"msg"=>"internal error!");
		    }elseif($liked==0){
		    	$likeRes=$this->liker->deleteLike($_SESSION['userid'],$ressourceId,$rn,$cunityId);
		    }
	    }
	    if($likeRes!==false){
	        $like = ($cunityId>0) ? $likeRes : $this->liker->createLikes($ressourceId, $rn,$_SESSION['userid'],0);
	        $replaces = array(
	                "LIKELINE"=>$like[1],
	                "LIKES"=>$like[0],
	                "STYLE"=>$_SESSION['style']
	            );
	        $likes = ($like[2]>0) ? $this->cunity->getTemplateEngine()->createTemplate('pinboard_likes', $replaces) : "";
	        return array('likes'=>$likes,'status'=>(int)$likeRes);
	    }else
	        return array("status"=>0,"db-error");
	}

	public function deleteComment($comment_id){
	    $comment_id = mysql_real_escape_string($comment_id);
	    $res = $this->cunity->getDb()->query("SELECT userid,`ressource_id` FROM ".$this->cunity->getConfig("db_prefix")."comments WHERE id = ".(int)$comment_id." LIMIT 1");
	    $data = mysql_fetch_assoc($res);
	    $statusData = $this->getStatusData($data['ressource_id']);
	    if(mysql_num_rows($res)==1&&($data['userid']==$_SESSION['userid']||$statusData['userid']==$_SESSION['userid']||($statusData['pinboard_id']==$_SESSION['userid']&&$statusData['receiver']=="friend")))
	        return $this->commentor->deleteComment($comment_id);
	    return false;
	}
}
?>