<?php

class Cunity_Filesharing {
    
    private $cunity = null;
    private $lang = array();
    
    public function Cunity_Filesharing(Cunity $cunity){
        $this->cunity = $cunity;
        $this->lang = $this->cunity->getLang();
    }
    
    public function checkFilesystem(){
        return ($this->cunity->getSetting('files_dir') != "" && is_writable($this->cunity->getSetting('files_dir')));
    }
    
    public function getAllowedFileTypes(){
        $allowed_filetypes=array();
    	$result = $this->cunity->getDb()->query("SELECT type FROM ".$this->cunity->getConfig("db_prefix")."allowed_filetypes");
    	while($row = mysql_fetch_assoc($result))
    			$allowed_filetypes[] = $row['type'];
        return $allowed_filetypes;
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
	
	private function checkFileType($extension){
        return in_array(strtolower($extension),$this->getAllowedFileTypes());
    }
    
    private function addFile(array $data){
        $res=$this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."files (`file_name`,`file_path`,`file_size`,`user_id`,`time`) VALUES ('".mysql_real_escape_string($data['file_name'])."','".mysql_real_escape_string($data['file_path'])."','".mysql_real_escape_string($data['file_size'])."','".mysql_real_escape_string($_SESSION['userid'])."',NOW())") or die(mysql_error());
        return ($res===false) ? false : mysql_insert_id();
    }
    
    public function uploadSingleFile(array $files){

		$max_size = 5; //in MB
		$max_upload = (int)(ini_get('upload_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		$memory_limit = (int)(ini_get('memory_limit'));
		$max_size = min($max_size, $max_upload, $max_post, $memory_limit);

		if(!empty($files['fu']['name']) && !empty($files['fu']['tmp_name']) && $files['fu']['error'] != UPLOAD_ERR_NO_FILE && $files['fu']['error'] != UPLOAD_ERR_PARTIAL){
			$pathinfo = pathinfo($files['fu']['name']);
			if($this->checkFileType($pathinfo['extension'])){
				if($files['fu']['size'] <= ($max_size * 1024 * 1024)) { // check size
					if($files['fu']['error'] == UPLOAD_ERR_OK){
						list($space,$used) = $this->getUserSpace();
						if(($space-$used)*1024*1024 >= $files['fu']['size']){
                            $targetDir=$this->cunity->getSetting('files_dir').'/_files/user'.$_SESSION['userid'];

							if (!file_exists($targetDir))
								mkdir($targetDir, 0777, true);

                            $fileName = $files["fu"]["name"];
                            $originalFileName=$fileName;

                            $fileName=time().'_'.rand().'_'.$fileName;
                            
                            $file = $targetDir . '/' . $fileName;

                            $fileData=$this->addFile(array(
                                "file_name"=>$originalFileName,
                                "file_path"=>'/_files/user'.$_SESSION['userid'].'/'.$fileName,
                                "file_size"=>$_FILES['fu']['size']));
                            if(is_int($fileData))
                                if(!move_uploaded_file($files['fu']['tmp_name'], $file)){
                                    $this->deleteFile($fileData);
                                    return array("status"=>0,"error"=>"upload-error!");
                                }else
                                    return array("status"=>1);
                            }else
                                return array("status"=>0,"error"=>"db-error");
						}else
							return array("status"=>0,"error"=>$this->lang['galleries_no_space']);
				}else
					return array("status"=>0,"error"=>$this->lang['galleries_image_large1'].$max_size.$this->lang['galleries_image_large2']);
			}else
				return array("status"=>0,"error"=>$this->lang['galleries_accept_files']);
		}else
			return array("status"=>0,"error"=>$this->lang['galleries_no_file']);
	}
	
	public function loadFiles(){
        $queryString='SELECT * FROM '.$this->cunity->getConfig("db_prefix").'files WHERE user_id ='.$_SESSION['userid'].' ORDER BY time DESC';
        $userFiles = $this->cunity->getDb()->query($queryString);
        $filesCount= mysql_fetch_assoc($userFiles);

        if($filesCount==0)
            return array("status"=>0,"error"=>newCunityError($this->lang['filesharing_no_files']));
            
        while($row=mysql_fetch_assoc($userFiles)){
            $fileSize=$row['file_size']/1000;
            if($fileSize>100){
            	$fileSize=round($row['file_size']/1000000, 2);
            	$fileSize=$fileSize. ' MB';
            }else{
            	$fileSize=round($fileSize, 2);
            	$fileSize=$fileSize.' KB';
            }

            $filename = (strlen($row['file_name'])>50) ? substr($row['file_name'],0,50) : $row['file_name'];

            $rows .= $this->cunity->getTemplateEngine()->createTemplate("filesharing_line",array(
                "FILEID"=>$row['file_id'],
                "FILENAME"=>$filename,
                "FILESIZE"=>$fileSize,
                "TIME"=>showDate("date_time",$row['time']),
                "filesharing_delete"=>$this->lang['filesharing_delete'],
                "filesharing_share"=>$this->lang['filesharing_share'],
                "filesharing_download"=>$this->lang['filesharing_download'],
                "filesharing_options"=>$this->lang['filesharing_options'],
            ));
        }
        return array('status'=>1,'myFiles'=>$rows,'count'=>$filesCount);
    }
    
    public function loadSharedFiles(){
        $queryString='SELECT shares.*,files.* FROM '.$this->cunity->getConfig("db_prefix").'files_share AS shares JOIN '.$this->cunity->getConfig("db_prefix").'files AS files WHERE shares.file_id=files.file_id AND shares.friend_id='.$_SESSION['userid'].' AND (remote IS NULL OR remote = \'file\')';
    	$userFiles = $this->cunity->getDb()->query($queryString);
    	$fileCount = mysql_num_rows($userFiles);
    	
    	if($fileCount==0)
            return array("status"=>0,"error"=>newCunityError($this->lang['filesharing_no_shared_files']));
            
        while($row = mysql_fetch_assoc($userFiles)){
            if($row['cunityId']>0)
                $row['file_name']=$row['filename'];
            $filename = (strlen($row['file_name'])>50) ? substr($row['file_name'],0,50) : $row['file_name'];

            $rows .= $this->cunity->getTemplateEngine()->createTemplate("filesharing_shared_line",array(
                "FILEID"=>$row['file_id'],
                "CUNITYID"=>$row['cunityId'],
                "FILENAME"=>$filename,
                "SHARENAME"=>getUserName($row['uploader_id'],($row['cunityId']>0),$row['cunityId']),
                "SHAREHASH"=>getUserHash($row['uploader_id'],($row['cunityId']>0),$row['cunityId']),
                "filesharing_options"=>$this->lang['filesharing_options'],
                "filesharing_delete_list"=>$this->lang['filesharing_unshare_file'],
                "filesharing_download"=>$this->lang['filesharing_download']
            ));
    	}
    	return array('status'=>1,'myFiles'=>$rows,'count'=>$fileCount);
    }
    
    public function deleteFile($fileId,$unlink=false){
        $fileData=$this->getFileData($fileId);
        if($unlink) if(!unlink($this->cunity->getSetting('files_dir').$fileData['file_path'])) return false;
        return array("status"=>intval($this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."files WHERE file_id=".$fileId)));
    }
    
    public function deleteMultipleFiles(array $files){
        $res=array();
        foreach($files AS $file)
            $res[]= is_array($this->deleteFile($file,true));
        return array("status"=>!in_array(false,$res));
    }
    
    private function getFileData($fileId){
        $res=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."files WHERE file_id = ".intval($fileId));
        return mysql_fetch_assoc($res);
    }
    
    private function getFileShares($fileId){
        $r=array();
        $res=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."files_share WHERE file_id = ".$fileId);
        if(mysql_num_rows($res)>0)
            while($data=mysql_fetch_assoc($res))
                $r[]=$data['friend_id'];
        return $r;
    }
    
    private function isShared($fileId,$userid,$cunityId=0){
        $shares=$this->getFileShares($fileId);
        foreach($shares AS $share)
            if($share['friend_id']==$userid&&$share['cunityId']==$cunityId&&$share['remote']=="file")
                return true;
        return false;
    }

    public function getFileDetails($fileId,$cunityId=0,$getRemote=false,$ownId=0,$ownCid=0){
        if($cunityId>0){
            require_once 'Cunity_Connector.class.php';
            $connector = new Cunity_Connector($this->cunity);
            $result=$connector->getFileDetails($fileId,$cunityId);
            $comments = "";
            if($result['content']['commentCount']>0)
                foreach($result['comments'] AS $commentReplaces)
                    $comments .= $this->cunity->getTemplateEngine()->createTemplate("comment",$commentReplaces);
            $result['content']['COMMENTS']=$comments;
            $result['content']['AVATAR'] = getAvatarPath($_SESSION['userid']);
            $result['content'] = $this->cunity->getTemplateEngine()->createTemplate("filesharing_details",$result['content']);
            unset($result['comments']);
            $result['status']=1;
            return $result;
        }
        if($ownId==0)
            $ownId=$_SESSION['userid'];
        $fileData=$this->getFileData($fileId);
        $fileShares=$this->getFileShares($fileId);
        require_once 'Cunity_Comments.class.php';
        require_once 'Cunity_Likes.class.php';
        $commentor = new Cunity_Comments($this->cunity);
        $liker = new Cunity_Likes($this->cunity);
        $like = $liker->createLikes($fileId,"fileshare",$ownId,$ownCid);
		$likes="";
		$dislikes="";
		$remoteComments=array();
		$likesCount = count($like[3]);
		$dislikesCount = count($like[4]);
		if($likesCount>0)
			foreach($like[3] AS $l)
				$likes .= '<a href="profile.php?user='.$l['cunityId']."-".$l['userhash'].'" style="border: 0px; text-decoration: none;" title="'.$l['username'].'"><img src="'.$l['avatar'].'" class="likeimg"/></a>';
		if($dislikesCount>0)
			foreach($like[4] AS $l)
				$dislikes .= '<a href="profile.php?user='.$l['cunityId']."-".$l['userhash'].'" style="border: 0px; text-decoration: none;" title="'.$l['username'].'"><img src="'.$l['avatar'].'" class="likeimg"/></a>';
        $c = $commentor->getComments($fileId,"fileshare");
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

				$delete = ($data['userid']==$ownId&&$data['cunityId']==$ownCid||$fileData['user_id']==$ownId&&$ownCid==0) ? '<a href="javascript:deleteComment('.$data['id'].');" class="ui-icon ui-icon-close del_comment_link" id="'.$data['id'].'_del" style="display: none;">&nbsp;</a>' : '';
				$replaces = array(
					"COMMENT_ID"=>$data['id'],
					"COMMENT_TIME"=>showDate('date_time', $data['time']),
					"STYLE"=>$_SESSION['style'],
					"COMMENT"=>$comment,
					"AVATAR"=>getSmallAvatar($data['userid'],40,$data['remote'],$data['cunityId']),
					"USERNAME"=>getUserName($data['userid'],$data['remote'],$data['cunityId']),
					"USERHASH"=>$data['cunityId']."-".getUserHash($data['userid'],$data['remote'],$data['cunityId']),
					"DELETE"=>$delete,
					"CLASS"=>'file_comment'
				);
				if($getRemote)
					$remoteComments[] = $replaces;
				else
					$comments .= $this->cunity->getTemplateEngine()->createTemplate('comment', $replaces);
				$x++;
			}
		}
		$liked=$liker->getLike($ownId,$fileId,"fileshare",$ownCid);
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
		if($fileData['privacy']==1){
            $sharedWith = $this->lang['filesharing_everybody'];
            $downloadDisplay="";
            $addDisplay="";
            $commentDisplay="";
        }else{
            $c=count($fileShares);
            $sharedWith = ($c==0) ? $this->lang['filesharing_nobody'] : $c." ".$this->lang['filesharing_people'];
            $downloadDisplay = ($this->isShared($fileId,$ownId,$ownCid)||($fileData['user_id']==$ownId&&$ownCid==0)) ? "" : "none";
            $addDisplay = ($this->isShared($fileId,$ownId,$ownCid)||($fileData['user_id']==$ownId&&$ownCid==0)) ? "none" : "";
            $commentDisplay=($this->isShared($fileId,$ownId,$ownCid)||($fileData['user_id']==$ownId&&$ownCid==0)) ? "" : "none";
            $requestDisplay=(!$this->isShared($fileId,$ownId,$ownCid)&&($fileData['user_id']!=$ownId&&$ownCid!=0)) ? "" : "none";
        }
        $shareDisplay=($fileData['user_id']==$ownId&&$ownCid==0) ? "" : "none";
        $unShareDisplay=($this->isShared($fileId,$ownId,$ownCid)&&$fileData['user_id']!=$ownId&&$ownCid!=0) ? "" : "none";
        $ownDisplay = ($fileData['user_id']==$ownId&&$ownCid==0) ? "" : "none";
		
        $fileData['description'] = ($fileData['description']=="") ? $this->lang['filesharing_no_description'] : $fileData['description'];
        $fileSize=$fileData['file_size']/1000;
        $fileSize=(($fileData['file_size']/1000)>100) ? round($fileData['file_size']/1000000, 2)." MB" : round(($fileData['file_size']/1000),2)." KB";
        $replaces=array(
            "FILEID"=>$fileData['file_id'],
            "TITLE"=>$this->lang['filesharing_file'].": ".$fileData['file_name'],
            "DESCRIPTION"=>$fileData['description'],
            "OWNER"=>getUserName($fileData['user_id']),
            "OWNERHASH"=>getUserHash($fileData['user_id']),
            "TIME"=>showDate("date_time",$data['time']),
            "SIZE"=>$fileSize,
            "AVATAR"=>getAvatarPath($ownId),
            "COMMENTS"=>$comments,
            "COMMENTSCOUNT"=>$commentCount,
            "LIKES"=>$likes,
            "DISLIKES"=>$dislikes,
            "LIKESCOUNT"=>$likesCount,
            "DISLIKESCOUNT"=>$dislikesCount,
            "LIKEDISPLAY"=>$likeDisplay,
            "DISLIKEDISPLAY"=>$dislikeDisplay,
            "SHARED"=>$sharedWith,
            "ADDDISPLAY"=>$addDisplay,
            "SHAREDISPLAY"=>$shareDisplay,
            "UNSHARE_DISPLAY"=>$unShareDisplay,
            "DOWNLOADDISPLAY"=>$downloadDisplay,
            "COMMENTDISPLAY"=>$commentDisplay,
            "REQUESTDISPLAY"=>$requestDisplay,
            "OWN_DISPLAY"=>$ownDisplay,
            "filesharing_share_request"=>$this->lang['filesharing_share_request'],
            "filesharing_shared_with"=>$this->lang['filesharing_shared_with'],
            "filesharing_like"=>$this->lang['galleries_like'],
            "filesharing_dislike"=>$this->lang['galleries_dislike'],
            "filesharing_download"=>$this->lang['filesharing_download'],
            "filesharing_comment"=>$this->lang['galleries_comment'],
            "filesharing_size"=>$this->lang['filesharing_size'],
            "filesharing_uploaded"=>$this->lang['filesharing_uploaded'],
            "filesharing_comments"=>$this->lang['filesharing_comments'],
            "filesharing_likes"=>$this->lang['filesharing_likes'],
            "filesharing_dislikes"=>$this->lang['filesharing_dislikes'],
            "filesharing_uploaded_by"=>$this->lang['filesharing_uploaded_by'],
            "filesharing_share"=>$this->lang['filesharing_share'],
            "filesharing_add_list"=>$this->lang['filesharing_add_list'],
            "filesharing_select_friends"=>$this->lang['filesharing_select_friends'],
            "filesharing_cancel"=>$this->lang['filesharing_cancel'],
            "filesharing_delete_file"=>$this->lang['filesharing_delete'],
            "filesharing_unshare_file"=>$this->lang['filesharing_unshare_file'],
            "filesharing_edit_file"=>$this->lang['filesharing_edit_file']
        );
        if($getRemote)
            return array("status"=>1,"content"=>$replaces,"comments"=>$remoteComments,"title"=>$this->lang['filesharing_details_of']." ".$fileData['file_name']);
        else
            $details = $this->cunity->getTemplateEngine()->createTemplate("filesharing_details",$replaces);
        return array("status"=>1,"content"=>$details,"title"=>$this->lang['filesharing_details_of']." ".$fileData['file_name']);
    }
    
    public function addComment($fileId,$content){
        require_once 'Cunity_Comments.class.php';
        $commentor = new Cunity_Comments($this->cunity);
		$comment_id=$commentor->addComment($_SESSION['userid'],$fileId,"fileshare",$content);
		if(!$comment_id) return false;
		$replaces = array(
				"CLASS"=> 'file_comment',
				"COMMENT_ID"=>$comment_id,
				"COMMENT_TIME"=>showDate('date_time', time()),
				"STYLE"=>$_SESSION['style'],
				"COMMENT"=>$content,
				"AVATAR"=>getSmallAvatar($_SESSION['userid'],40),
				"USERNAME"=>getUserName($_SESSION['userid']),
				"USERHASH"=>getUserHash($_SESSION['userid']),
				"DELETE"=>'<a href="javascript:deleteComment('.$comment_id.');" class="ui-icon ui-icon-close del_comment_link" id="'.$comment_id.'_del" style="display: none;">&nbsp;</a>'
		);
		$output = $this->cunity->getTemplateEngine()->createTemplate('comment', $replaces);
		return array("status"=>1,"comments"=>$output);
    }
    
    public function likeFile($fileId){
		$likes = "";
		require_once 'Cunity_Likes.class.php';
		$liker=new Cunity_Likes($this->cunity);
		$liked = $liker->getLike($_SESSION['userid'],$fileId,"fileshare");
		if(!$liked||($liked==1&&$liker->deleteLike($_SESSION['userid'],$fileId,"fileshare")))
			$likeRes=$liker->like($_SESSION['userid'],$fileId,"fileshare");
		else return array("status"=>0,"msg"=>"already liked or db error");
		
		if($likeRes!==false){
            $like=$liker->createLikes($fileId, "fileshare",$_SESSION['userid'],0);
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

	public function dislikeFile($fileId){
        $likes="";
        require_once 'Cunity_Likes.class.php';
		$liker=new Cunity_Likes($this->cunity);
		$liked = $liker->getLike($_SESSION['userid'],$fileId,"fileshare");
		if($this->cunity->getSetting("allow_dislike")==1){
			if($liked==1) // status already disliked
				return array("status"=>0,"msg"=>"already disliked");
			elseif($liked==0||!$liked){
				if($liker->deleteLike($_SESSION['userid'],$fileId,"fileshare"))
					$likeRes=$liker->dislike($_SESSION['userid'],$fileId,"fileshare");
			}else return array("status"=>0,"msg"=>"internal error!");
		}elseif($liked==0){
			$likeRes=$liker->deleteLike($_SESSION['userid'],$fileId,"fileshare");
		}
		if($likeRes!==false){
			$like = $liker->createLikes($fileId,"fileshare",$_SESSION['userid'],0);
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
	   require_once 'Cunity_Comments.class.php';
        $commentor = new Cunity_Comments($this->cunity);
		$comment_id = mysql_real_escape_string($comment_id);
		$res = $this->cunity->getDb()->query("SELECT `userid`,`ressource_id` FROM ".$this->cunity->getConfig("db_prefix")."comments WHERE id = ".(int)$comment_id." LIMIT 1");
		$data = mysql_fetch_assoc($res);
		$fileData = $this->getFileData($data['ressource_id']);
		if(mysql_num_rows($res)==1&&($data['userid']==$_SESSION['userid']||$imageData['user_id']==$_SESSION['userid']))
			return array("status"=>(int)$commentor->deleteComment($comment_id));
        return array("status"=>0);
	}

	public function showLikes($fileId,$type){
		$ressource_id = $fileId;
		$ressource_name = "fileshare";
		require_once 'Cunity_Likes.class.php';
		$liker=new Cunity_Likes($this->cunity);
		$data = array();
			$likes = ($type==0) ? $liker->getLikes($ressource_id,$ressource_name) : $liker->getDislikes($ressource_id,$ressource_name);
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
	
	public function shareFile($fileId,$userids,$filename=""){
        if(is_string($userids))
            $ids=explode(",",$userids);
        else if(is_int($userids))
            $ids=array($userids);
        else if(is_array($userids))
            $ids = $userids;
        $res=array();
        if(count($ids)>0&&$this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."files_share WHERE file_id =".$fileId)){
            $fileData = $this->getFileData($fileId);
            foreach($ids AS $userid){
                if($_SESSION['userid']==$fileData['user_id']&&!$this->isShared($fileId,$userid))
                    $res[]=$this->cunity->getDb()->query("INSERT INTO  ".$this->cunity->getConfig("db_prefix")."files_share (`file_id`,`uploader_id`,`friend_id`,`time`,`filename`)VALUES(".intval($fileId).",".intval($_SESSION['userid']).",".intval($userid).",NOW(),'".$filename."')");
            }
        }
        return array("status"=>intval(!in_array(false,$res)));
    }
    
    public function unshareFile($fileId,$userid){
        return array("status"=>intval($this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."files_share WHERE file_id =".$fileId." AND friend_id = ".$userid)));
    }
    
    public function addFileToList($fileId){
        $fileData=$this->getFileData($fileId);
        if($fileData['privacy']==1&&$this->cunity->getFriender()->isFriend($_SESSION['userid'],$fileData['user_id']))
            return $this->cunity->getDb()->query("INSERT INTO  ".$this->cunity->getConfig("db_prefix")." (`file_id`,`uploader_id`,`friend_id`,`time`)VALUES(".intval($fileId).",".intval($fileData['user_id']).",".intval($_SESSION['userid']).",NOW())");
    }
    
    public function getFriendsForShare($fileId){
        $friends=$this->cunity->getFriender()->getFriendList($_SESSION['userid']);
        $shares=$this->getFileShares($fileId);
        $friendsBack="";
        if(count($friends)==0)
            return array("status"=>1,"friend"=>newCunityError($this->lang['friends_no_friends']));
        foreach($friends AS $friend){
            $cunityTplId = ($friend['cunityId']>0) ? $friend['cunityId']."-" : "";
            $checked = (in_array($friend['id'],$shares)) ? "checked" : "";
            $friendsBack .= $this->cunity->getTemplateEngine()->createTemplate("filesharing_choose_friends", array(
            	'USERHASH'=>getUserHash($friend['id'],($friend['cunityId']>0),$friend['cunityId']),
            	'AVATAR'=>getAvatarPath($friend['id'],($friend['cunityId']>0),$friend['cunityId']),
            	'USERNAME'=>getUserName($friend['id'],($friend['cunityId']>0),$friend['cunityId']),
            	'CUNITYIDTPL'=>$cunityTplId,
                'CUNITYID'=>$friends['cunityId'],
            	'USERID'=>$friend['id'],
                'CHECKED'=>$checked
            ));
        }
        return array("status"=>1,"friends"=>$friendsBack);
    }
}
?>