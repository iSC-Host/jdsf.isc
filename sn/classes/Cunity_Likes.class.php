<?php
class Cunity_Likes {
	
	private $cunity = null;
	
	public function Cunity_Likes(Cunity $cunity){
		$this->cunity = $cunity;
	}
	
	public function like($userid,$ressource_id,$ressource_name,$cunityId=0){		
		if($cunityId>0)		
			$result = $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."likes (`userid`,`ressource_name`,`ressource_id`,`dislike`,`cunityId`) VALUES (".(int)$userid.",'".$ressource_name."',".(int)$ressource_id.",0,".(int)$cunityId.")");
		else 
			$result = $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."likes (`userid`,`ressource_name`,`ressource_id`,`dislike`,`cunityId`) VALUES (".(int)$userid.",'".$ressource_name."',".(int)$ressource_id.",0,0)") or die(mysql_error());
		return $result;
	}
	
	public function dislike($userid,$ressource_id,$ressource_name,$cunityId=0){
		if($this->cunity->getSetting('allow_dislike')==0)
			return false;
		if($cunityId>0)		
			$result = $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."likes (`userid`,`ressource_name`,`ressource_id`,`dislike`,`cunityId`) VALUES (".(int)$userid.",'".$ressource_name."',".(int)$ressource_id.",1,".(int)$cunityId.")");
		else 
			$result = $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."likes (`userid`,`ressource_name`,`ressource_id`,`dislike`,`cunityId`) VALUES (".(int)$userid.",'".$ressource_name."',".(int)$ressource_id.",1,0)");
		return $result;
	}
	
	public function getLike($userid,$ressource_id,$ressource_name,$cunityId=0){
		if($cunityId>0&&$cunityId!=$this->cunity->getcunityId())
			$res=$this->cunity->getDb()->query("SELECT dislike FROM ".$this->cunity->getConfig("db_prefix")."likes WHERE userid = ".intval($userid)." AND ressource_name = '".$ressource_name."' AND ressource_id = ".intval($ressource_id)." AND cunityId = ".intval($cunityId)." LIMIT 1");
		else
			$res=$this->cunity->getDb()->query("SELECT dislike FROM ".$this->cunity->getConfig("db_prefix")."likes WHERE userid = ".intval($userid)." AND ressource_name = '".$ressource_name."' AND ressource_id = ".intval($ressource_id)." LIMIT 1");
	    $data=mysql_fetch_assoc($res);
	    if(mysql_num_rows($res)==0) return false;
	    else return (int)$data['dislike']; 
	}
	
	public function getLikes($ressource_id,$ressource_name){
		$likes=array();
		$result=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."likes WHERE ressource_id = ".(int)$ressource_id." AND ressource_name = '".$ressource_name."' AND dislike = 0");
		while($data=mysql_fetch_assoc($result)){
			$cunityId = ($data['cunityId']==0) ? $this->cunity->getcunityId() : $data['cunityId'];
			$likes[] = array(
				"userid"=>$data['userid'],
				"remote"=>($data['cunityId']>0),
				"cunityId"=>$cunityId,
				"username"=>getUserName($data['userid'],($data['cunityId']>0),$cunityId),
				"userhash"=>getUserHash($data['userid'],($data['cunityId']>0),$cunityId),
				"avatar"=>getAvatarPath($data['userid'],($data['cunityId']>0),$cunityId)
			);
		}			
		return $likes;
	}
	
	public function getDislikes($ressource_id,$ressource_name){
		$likes=array();
		$result=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."likes WHERE ressource_id = ".(int)$ressource_id." AND ressource_name = '".$ressource_name."' AND dislike=1");
		while($data=mysql_fetch_assoc($result)){
			$cunityId = ($data['cunityId']==0) ? $this->cunity->getcunityId() : $data['cunityId'];
			$likes[] = array(
				"userid"=>$data['userid'],
				"remote"=>($data['cunityId']>0),
				"cunityId"=>$cunityId,
				"username"=>getUserName($data['userid'],($data['cunityId']>0),$cunityId),
				"userhash"=>getUserHash($data['userid'],($data['cunityId']>0),$cunityId),
				"avatar"=>getAvatarPath($data['userid'],($data['cunityId']>0),$cunityId)
			);
		}			
		return $likes;
	}
	
	public function countLikes($ressource_id,$ressource_name){
		$result=$this->cunity->getDb()->query_assoc("SELECT COUNT(*) AS count FROM ".$this->cunity->getConfig("db_prefix")."likes WHERE ressource_id = ".(int)$ressource_id." AND ressource_name = '".$ressource_name."' AND dislike=0");
		return $result['count'];
	}
	
	public function countDislikes($ressource_id,$ressource_name){
		$result=$this->cunity->getDb()->query_assoc("SELECT COUNT(*) AS count FROM ".$this->cunity->getConfig("db_prefix")."likes WHERE ressource_id = ".(int)$ressource_id." AND ressource_name = '".$ressource_name."' AND dislike=1");
		return $result['count'];
	}
	
	
	public function deleteLike($userid,$ressource_id,$ressource_name,$cunityId=0,$remoteField=""){		
		if($cunityId>0)
			$res=$this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."likes WHERE userid=".(int)$userid." AND ressource_id = ".(int)$ressource_id." AND ressource_name = '".$ressource_name."' AND cunityId = ".(int)$cunityId."");
		else
			$res=$this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."likes WHERE userid=".(int)$userid." AND ressource_id = ".(int)$ressource_id." AND ressource_name = '".$ressource_name."' AND cunityId = 0");
		return $res;
	}
	
	public function deleteAllLikes($ressource_id,$ressource_name){
		return $this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."likes WHERE ressource_id = ".$ressource_id." AND ressource_name = '".$ressource_name."'");
	}
	
	private function searchUserData($userid,$cunityId,array $array){
		if($array["userid"]==$userid&&$array["cunityId"]==$cunityId)
			return true;		
		foreach($array AS $arrayTemp)
			if(is_array($arrayTemp))
				return $this->searchUserData($userid,$cunityId,$arrayTemp);						
		return false;
	}
	
	public function createLikes($id, $ressource_name,$ownId,$ownCunityId){
		$likes=$this->getLikes($id,$ressource_name);
		$dislikes=$this->getDislikes($id,$ressource_name);
		
		$lang = $this->cunity->getLang();	    
	    if($this->searchUserData($ownId,$ownCunityId,$likes)){        
	        if(count($likes) == 2){
	            $output .= '<span class="like_you">'.$lang['galleries_you'].' </span>'.$lang['galleries_and'].' <a href="profile.php?user='.$likes[1]["cunityId"].'-'.getUserHash($likes[1]["userid"],$likes[1]["remote"],$likes[1]["cunityId"]).'">'.getUserName($likes[1]["userid"],$likes[1]["remote"],$likes[1]["cunityId"]).'</a> '.$lang['galleries_like_this'];
	        }elseif(count($likes) > 2){
	            $output .= '<span class="like_you">'.$lang['galleries_you'].'</span>, <a href="profile.php?user='.$likes[1]["cunityId"].'-'.getUserHash($likes[1]["userid"],$likes[1]["remote"],$likes[1]["cunityId"]).'">'.getUserName($likes[1]["userid"],$likes[1]["remote"],$likes[1]["cunityId"]).'</a> '.$lang['galleries_and'].' <span class="show_other" id="'.$id.'">'.(count($likes)-2).' '.$lang['galleries_other'].'</span> '.$lang['galleries_like_this'];
	        }else{
	            $output .= '<span class="like_you">'.$lang['galleries_you'].' </span>'.$lang['galleries_like_this'];
	        }
		}else{      
	        if(count($likes) == 1){            
	            $output .= '<a href="profile.php?user='.$likes[0]["cunityId"].'-'.getUserHash($likes[0]["userid"],$likes[0]["remote"],$likes[0]["cunityId"]).'">'.getUserName($likes[0]["userid"],$likes[0]["remote"],$likes[0]["cunityId"]).'</a> '.$lang['galleries_likes_this'];
	        }elseif(count($likes) == 2){
	            $output .= '<a href="profile.php?user='.$likes[0]["cunityId"].'-'.getUserHash($likes[0]["userid"],$likes[0]["remote"],$likes[0]["cunityId"]).'">'.getUserName($likes[0]["userid"],$likes[0]["remote"],$likes[0]["cunityId"]).'</a> '.$lang['galleries_and'].' <a href="profile.php?user='.$likes[1]["cunityId"].'-'.getUserHash($likes[1]["userid"],$likes[1]["remote"],$likes[1]["cunityId"]).'">'.getUserName($likes[1]["userid"],$likes[1]["remote"],$likes[1]["cunityId"]).'</a> '.$lang['galleries_like_this'];
	        }elseif(count($likes) > 2){
	            $output .= '<a href="profile.php?user='.$likes[0]["cunityId"].'-'.getUserHash($likes[0]["userid"],$likes[0]["remote"],$likes[0]["cunityId"]).'">'.getUserName($likes[0]["userid"],$likes[0]["remote"],$likes[0]["cunityId"]).'</a> '.$lang['galleries_and'].' <span class="show_other" id="'.$id.'">'.(count($likes)-1).' '.$lang['galleries_other'].'</span> '.$lang['galleries_like_this'];
	        }else{
	            $output .= '';
	        }
	    }
	    
	    if(count($dislikes)>0&&count($likes)>0)
	        $output .= '&nbsp;|&nbsp;';
	    
	    if($this->searchUserData($ownId,$ownCunityId,$dislikes)){     
	        if(count($dislikes) == 2){
	            $output .= '<span class="like_you">'.$lang['galleries_you'].' </span>'.$lang['galleries_and'].' <a href="profile.php?user='.$dislikes[1]["cunityId"].'-'.getUserHash($dislikes[1]["userid"],$dislikes[1]["remote"],$dislikes[1]["cunityId"]).'">'.getUserName($dislikes[1]["userid"],$dislikes[1]["remote"],$dislikes[1]["cunityId"]).'</a> '.$lang['galleries_dislike_this'];
	        }elseif(count($dislikes) > 2){
	            $output .= '<span class="like_you">'.$lang['galleries_you'].'</span>, <a href="profile.php?user='.$dislikes[1]["cunityId"].'-'.getUserHash($dislikes[1]["userid"],$dislikes[1]["remote"],$dislikes[1]["cunityId"]).'">'.getUserName($dislikesArray[1]).'</a> '.$lang['galleries_and'].' <span class="show_other" id="'.$id.'">'.(count($dislikes)-2).' '.$lang['galleries_other'].'</span> '.$lang['galleries_dislike_this'];
	        }else{
	            $output .= '<span class="like_you">'.$lang['galleries_you'].' </span>'.$lang['galleries_dislike_this'];
	        }
	    }else{
	        if(count($dislikes) == 1){
	            $output .= '<a href="profile.php?user='.$dislikes[0]["cunityId"].'-'.getUserHash($dislikes[0]["userid"],$dislikes[0]["remote"],$dislikes[0]["cunityId"]).'">'.getUserName($dislikes[0]["userid"],$dislikes[0]["remote"],$dislikes[0]["cunityId"]).'</a> '.$lang['galleries_dislikes_this'];
	        }elseif(count($dislikes) == 2){
	            $output .= '<a href="profile.php?user='.$dislikes[0]["cunityId"].'-'.getUserHash($dislikes[0]["userid"],$dislikes[0]["remote"],$dislikes[0]["cunityId"]).'">'.getUserName($dislikes[0]["userid"],$dislikes[0]["remote"],$dislikes[0]["cunityId"]).'</a> '.$lang['galleries_and'].' <a href="profile.php?user='.$dislikes[1]["cunityId"].'-'.getUserHash($dislikes[1]["userid"],$dislikes[1]["remote"],$dislikes[1]["cunityId"]).'">'.getUserName($dislikes[1]["userid"],$dislikes[1]["remote"],$dislikes[1]["cunityId"]).'</a> '.$lang['galleries_dislike_this'];
	        }elseif(count($dislikes) > 2){
	            $output .= '<a href="profile.php?user='.$dislikes[0]["cunityId"].'-'.getUserHash($dislikes[0]["userid"],$dislikes[0]["remote"],$dislikes[0]["cunityId"]).'">'.getUserName($dislikes[0]["userid"],$dislikes[0]["remote"],$dislikes[0]["cunityId"]).'</a> '.$lang['galleries_and'].' <span class="show_other" id="'.$id.'">'.(count($dislikes)-1).' '.$lang['galleries_other'].'</span> '.$lang['galleries_dislike_this'];
	        }else{
	            $output .= '';
	        }
	    }
	    $likeline = '<div class="likeline">';
	    if(count($likes)>0)
	        $likeline .= '<a class="showLikes" href="javascript: showLikes('.$id.', 0,\''.$ressource_name.'\');"><img src="style/'.$_SESSION['style'].'/img/friends.png" style="margin-right:3px"/>'.count($likes).'</a>';	    
	    if(count($dislikes)>0)
	        $likeline .= '<a class="showDislikes" href="javascript: showLikes('.$id.', 1,\''.$ressource_name.'\');"><img src="style/'.$_SESSION['style'].'/img/fail.png" style="margin-right:3px"/>'.count($dislikes).'</a>';	    
	    $likeline .= '<div class="clear"></div></div>';	    	            
	    $data = array($output, $likeline,count($likes)+count($dislikes),$likes,$dislikes);
	    return $data;
	}
}