<?php

class Cunity_Friends {
	
	private $cunity;
	
	public function Cunity_Friends(Cunity $cunity){
		$this->cunity = $cunity;			
	}
		
	public function friendshipExists($sender,$receiver,$cunityId=0){
		$res = $this->cunity->getDb()->query("SELECT COUNT(*) as c FROM ".$this->cunity->getConfig("db_prefix")."friendships WHERE (sender = '".$sender."' AND receiver = '".$receiver."') OR (sender = '".$receiver."' AND receiver = '".$sender."') AND cunityId = '".$cunityId."'");
		$data = mysql_fetch_assoc($res);
		if($data['c']>0)
			return true;
		return false;
	}
	
	public function getFriendshipStatus($ownUserId,$userid,$cunityId=0){
        $data=$this->getFriendShipData($ownUserId,$userid,$cunityId);
        if($data!==false){
            if($data['status']==0)
                return ($data['sender']==$userid) ? 3 : 0;
            return $data['status'];
        }            
        return false;
    }
	
	public function isFriend($ownUserId,$userid,$cunityId=0){
		return ($this->getFriendshipStatus($ownUserId,$userid,$cunityId)==1);
	}
	
	public function getFriendList($userid){
		$friendList = array();
		$res=$this->cunity->getDb()->query("SELECT cunityId,(CASE WHEN sender != ".$userid." AND receiver = ".$userid." THEN sender WHEN receiver != ".$userid." AND sender = ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$this->cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1");		
		while($data=mysql_fetch_assoc($res)){
			$data['cunityId']=($data['cunityId']==0) ? $this->cunity->getcunityId() : $data['cunityId'];
			$friendList[] = array(
				"id"=>$data['friend'],
				"cunityId"=>$data['cunityId']
			);
		}
		return $friendList;
	}
	
	public function getFriendShipData($sender,$receiver,$cunityId=0){
        $q="SELECT * FROM ".$this->cunity->getConfig("db_prefix")."friendships WHERE ((sender = ".$sender." AND receiver = ".$receiver.") OR (sender = ".$receiver." AND receiver = ".$sender.")) AND cunityId = ".$cunityId;
		$res = $this->cunity->getDb()->query($q);
		if(mysql_num_rows($res)==0) return false;
		return mysql_fetch_assoc($res);		
	}
	
	public function addFriendship($sender,$receiver,$cunityId=0,$remoteUser=""){
		if(!$this->friendshipExists($sender,$receiver,$cunityId)){
            if($cunityId>0)
				return $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."friendships (`sender`,`receiver`,`remote`,`cunityId`,`status`,`time`) VALUES ('".$sender."','".$receiver."','".$remoteUser."',".$cunityId.",0,NOW())");
			else
				return $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."friendships (`sender`,`receiver`,`remote`,`cunityId`,`status`,`time`) VALUES ('".$sender."','".$receiver."',NULL,0,0,NOW())");
        }
		return false;
	}
	
	public function removeFriendship($sender,$receiver,$cunityId=0){
		if($this->friendshipExists($sender, $receiver,$cunityId))
			return $this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."friendships WHERE ((sender = '".$sender."' AND receiver = '".$receiver."') OR (sender = '".$receiver."' AND receiver = '".$sender."')) AND cunityId = ".$cunityId."");
		return false;
	}
	
	public function confirmFriendship($sender,$receiver,$cunityId=0){		
		if($this->friendshipExists($sender, $receiver,$cunityId)){						
			return $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."friendships SET status = 1 WHERE ((sender = '".$sender."' AND receiver = '".$receiver."') OR (sender = '".$receiver."' AND receiver = '".$sender."')) AND cunityId = '".$cunityId."'");
		}			
		return false;
	}
	
	public function blockFriendship($blocker,$receiver,$cunityId=0){	
		if($this->friendshipExists($blocker, $receiver,$cunityId)){
			$data = $this->getFriendShipData($blocker, $receiver);
			if($data['sender']==$blocker)									
				return $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."friendships SET blocker = 'sender',status = 2 WHERE sender = '".$blocker."' AND receiver = '".$receiver."' AND cunityId = '".$cunityId."'") or die(mysql_error());
			else 
				return $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."friendships SET blocker = 'receiver',status = 2 WHERE sender = '".$receiver."' AND receiver = '".$blocker."' AND cunityId = '".$cunityId."'") or die(mysql_error());
		}else{
			if($cunityId>0)
				return $this->cunity->getDB()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."friendships (`sender`,`receiver`,`remote`,`cunityId`,`status`,`time`,`blocker`) VALUES ('".$blocker."','".$receiver."','sender','".$cunityId."',2,NOW(),'sender')");
			else
				return $this->cunity->getDB()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."friendships (`sender`,`receiver`,`remote`,`cunityId`,`status`,`time`,`blocker`) VALUES ('".$blocker."','".$receiver."',NULL,0,2,NOW(),'sender')");
		}			
		return false;
	}
	
	public function sendFriendRequest($sender,$receiver,$remote=false,$cunityId=0,$userData=array()){		
    	if(!$remote){			
	        if($this->addFriendship($sender, $receiver))
	            if($this->cunity->getNotifier()->addNotification('add_friend',$receiver, $sender))	           
	            	return true;
                return true;            		    
    	}elseif($remote&&$cunityId>0&&count($userData)>0){
    		require_once 'Cunity_Connector.class.php';
    		$connector = new Cunity_Connector($this->cunity);
    		if($connector->handleFriendRequest($sender,$receiver,$cunityId, 'add',$userData))
    			return $this->addFriendship($sender,$receiver, $cunityId,'receiver');
    	}
    	return false;		
    }    
    
    public function blockFriend($sender,$receiver,$remote=false,$cunityId=0,$userData=array()){
    	if(!$remote){
    		if($this->blockFriendship($sender, $receiver))
		        return true;
    	}elseif($remote&&$cunityId>0&&count($userData)>0){    		
    		require_once 'Cunity_Connector.class.php';
    		$connector = new Cunity_Connector($this->cunity);
    		if($connector->handleFriendRequest($sender,$receiver,$cunityId, 'block',$userData))    			
    			if($this->blockFriendship($sender, $receiver, $cunityId))
    				return true;
    	}
    	return false;
    }
      
    public function deleteFriend($sender,$receiver,$remote=false,$cunityId=0){    	
    	if(!$remote){    		
    		if($this->removeFriendship($sender, $receiver))
		        return true;		    		    
    	}elseif($remote && $cunityId>0){
    		require_once 'Cunity_Connector.class.php'; 		
    		$connector = new Cunity_Connector($this->cunity);    		
    		if($connector->handleFriendRequest($sender,$receiver,$cunityId, 'remove'))
    			return $this->removeFriendship($sender, $receiver, $cunityId);    				
    	}
    	return false;
    }
    
    public function confirmFriendRequest($sender,$receiver,$remote=false,$cunityId=0){    	
    	if(!$remote){    		
		    if($this->confirmFriendship($sender, $receiver))		    			    	    
	            return $this->cunity->getNotifier()->addNotification('accepted_friend', $receiver, $sender);		    	            	            		        		    		  
    	}elseif($remote&&$cunityId>0){
    		require_once 'Cunity_Connector.class.php';
    		$connector = new Cunity_Connector($this->cunity);    		
    		if($connector->handleFriendRequest($sender, $receiver, $cunityId, 'confirm'))
    			if($this->confirmFriendship($sender, $receiver, $cunityId))    			
    				return true;    		    			
    	}
    	return false;
    }	
}