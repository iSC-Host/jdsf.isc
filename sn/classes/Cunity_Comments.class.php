<?php

class Cunity_Comments {
	
	private $cunity = null;
	
	public function Cunity_Comments(Cunity $cunity){
		$this->cunity = $cunity;
	}

	public function addComment($userid,$ressource_id,$ressource_name,$comment,$cunityId=0){
		if($cunityId>0)
			$res = $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."comments (`id` ,`ressource_id` ,`ressource_name` ,`userid` ,`comment` ,`time` ,`cunityId`) VALUES (NULL,".(int)$ressource_id.",'".$ressource_name."',".(int)$userid.",'".mysql_real_escape_string($comment)."',NOW(),".(int)$cunityId.")");
		else
			$res = $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."comments (`id` ,`ressource_id` ,`ressource_name` ,`userid` ,`comment` ,`time` ,`cunityId`) VALUES (NULL,".(int)$ressource_id.",'".$ressource_name."',".(int)$userid.",'".mysql_real_escape_string($comment)."',NOW(),0)");		
		return mysql_insert_id();
	}
	
	public function deleteComment($comment_id){		
		return $this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."comments WHERE id = ".(int)$comment_id);
	}
	
	public function getComments($ressource_id,$ressource_name){
		$comments=array();
		$result=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."comments WHERE ressource_id = ".(int)$ressource_id." AND ressource_name = '".$ressource_name."' ORDER BY time ASC");
		while($data=mysql_fetch_assoc($result)){
			$cunityId=($data['cunityId']==0) ? $this->cunity->getcunityId() : $data['cunityId'];
			$comments[] = array(
				"id"=>$data['id'],
				"userid"=>$data['userid'],
				"comment"=>$data['comment'],
				"time"=>$data['time'],
				"remote"=>($data['cunityId']>0),				
				"cunityId"=>$cunityId);
		}			
		return $comments;
	}
	
	public function deleteAllComments($ressource_id,$ressource_name){
		return $this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."comments WHERE ressource_id = ".$ressource_id." AND ressource_name = '".$ressource_name."'");
	}
	
	public function countComments($ressource_id,$ressource_name){
		$result=$this->cunity->getDb()->query_assoc("SELECT COUNT(*) AS count FROM ".$this->cunity->getConfig("db_prefix")."comments WHERE ressource_id = ".(int)$ressource_id." AND ressource_name = '".$ressource_name."'");
		return $result['count'];
	}

	public function getCommentData($comment_id){
		$res=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."comments WHERE id = ".(int)$comment_id." LIMIT 1");
		return mysql_fetch_assoc($res);
	}
}
?>