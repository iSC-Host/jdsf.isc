<?php

class Cunity_Profile {
	
	private $cunity = null;
	private $userid = 0;
	private $userData = array();
	private $userDetails = array();
	
	public function Cunity_Profile(Cunity $cunity,$userid){
		$this->cunity = $cunity;
		$this->userid = $userid;
		$this->selectData();
	}
		
	private function selectData(){
		$res1=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."users WHERE userid=".$this->userid." LIMIT 1");
		$this->userData = mysql_fetch_assoc($res1);
		
		$res2=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."users_details WHERE userid=".$this->userid." LIMIT 1");
		$this->userDetails = mysql_fetch_assoc($res2);
	}
	
	public function getUserData($field=""){
		return (isset($this->userData[$field])&&$field!=="") ? $this->userData[$field] : $this->userData;
	}
	
	public function getUserDetail($field=""){
		return (isset($this->userDetails[$field])&&$field!=="") ? $this->userDetails[$field] : $this->userDetails;
	}
	
	public function getProfileImage(){
		if($this->userData['profile_image']==0)
			$file = './files/_profile_images/'.$this->userData['userhash'].'.jpg';
		else{
			require_once 'Cunity_Galleries.class.php';
			$galleries = new Cunity_Galleries($this->cunity);
			$data=$galleries->getImageData($this->userData['profile_image']);
			$file = $data['file'];
		}		
		if(file_exists($file))
			return $this->cunity->getSetting("url").'/'.$file;
		else
			return 'style/'.$_SESSION['style'].'/img/no_profile_img.jpg';
	}
	
	public function setUserData($field,$value){
		if(!isset($userData[$field])&&!isset($userDetails[$field])) return false;
		else if(isset($userData[$field])){
			if($value==$this->userData[$field]) return true;
			$this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."users SET `".$field."` = '".$value."' WHERE userid = ".$this->userid);
		}else{
			if($value==$this->userDetails[$field]) return true;
			$this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."users SET `".$field."` = '".$value."' WHERE userid = ".$this->userid);
		}		
	}
}

?>