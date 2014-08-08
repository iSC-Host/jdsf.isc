<?php
require_once('Cunity_Cryptor.class.php');
require_once('Cunity_Connector.class.php');

class Cunity_Connection_Responder {
		
	private $request;
	private $cunity;
	private $cryptor;
	private $connector;
	private $publicKey;
		
	public function Cunity_Connection_Responder(Cunity $cunity){
		$this->cunity = $cunity;		
		$this->cryptor = new Cunity_Cryptor($this->cunity);
		$this->connector = new Cunity_Connector($this->cunity);
	}	
	
	public function setRequestData(array $requestData){
		$this->request = $requestData;
	}
	
	public function getCryptor(){
		return $this->cryptor;
	}
	
	public function checkRequests(){
		eval('$this->'.$this->request['c'].'();');		
	}
	
	public function sendMessage(){
		$sender = $this->request['sender'];
		$receiver = $this->request['receiver'];
		$message = $this->request['message'];
		$cunityId = $this->request['cunityId'];
		require_once 'Cunity_Messenger.class.php';
		$messenger = new Cunity_Messenger($this->cunity);
		$status = ($messenger->sendMessage($sender, $receiver, $message, $cunityId,"sender")!==false);
		$this->respond(json_encode(array("status"=>(int)$status)));
		unset($messenger);
	}
	
	public function insertPinboard(){
		require_once 'Cunity_Pinboard.class.php';
		$pinboard = new Cunity_Pinboard($this->cunity);
		$id=$pinboard->insertPinboard($this->request['sender'], $this->request['pinboard_id'], $this->request['message'], $this->request['type'], $this->request['receiver'],$this->request['cunityId'],'user',$this->request['status_id']);
		if($id!==false
			&& $this->connector->insertNewConnectedCunity($this->request['cunityId'],$this->request['cunityName'],$this->request['cunityUrl'],$this->request['cunityPublicKey'],"") 
			&& $this->connector->createNewRemoteUser($this->request['name'],$this->request['userhash'],$this->request['localid'],$this->request['nickname'],$this->request['mail'],$this->request['cunityId'],$this->request['sex'])){
			if($this->cunity->getNotifier()->addNotification('post_on_pinboard', $this->request['pinboard_id'], $this->request['sender'], mysql_insert_id(),0,true,$this->request['cunityId']))
				$this->respond($this->cunity->returnJson(array("status"=>1,"status_id"=>$id)));
				return;	
		}
		unset($pinboard);
		$this->respond($this->cunity->returnJson(array("status"=>0)));
		return;
	}
	
	public function loadPinboard(){
		require_once 'Cunity_Pinboard.class.php';
		$pinboard = new Cunity_Pinboard($this->cunity);				
		$remotePinboard = json_encode($pinboard->loadPinboard($this->request,true));				
		$this->respond($remotePinboard);
		unset($pinboard);		
		return;
	}
	
	public function deleteStatus(){
		require_once 'Cunity_Pinboard.class.php';
		$pinboard = new Cunity_Pinboard($this->cunity);
		$data=$pinboard->getStatusData($this->request['status_id']);			
		$result=$pinboard->deleteStatus($this->request['status_id'],false,0,$this->request['userid'],$this->request['cunityId'],false);
		$this->respond($this->cunity->returnJson(array("status"=>(int)$result,"id"=>$data['remoteId'])));
		return;
	}
	
	private function getAlbumData(){
		require_once 'Cunity_Galleries.class.php';
		$galleries = new Cunity_Galleries($this->cunity);
		$data=$galleries->getAlbumData($this->request['albumid']);		
		$this->respond($this->cunity->returnJson(array("status"=>(int)($data!==false),"data"=>$data)));
		unset($galleries);
		return;
	}
	
	private function getImagesOfAlbum(){
		require_once 'Cunity_Galleries.class.php';
		$galleries = new Cunity_Galleries($this->cunity);
		$imgs=$galleries->getImagesOfAlbum($this->request['albumid'],$this->request['limit']);
		foreach($imgs AS $key => $data)
			$imgs[$key]['file'] = ($data['file']!="") ? $this->cunity->getSetting("url").'/'.$data['file'] : "";
		$this->respond($this->cunity->returnJson(array("status"=>(int)($data!==false),"data"=>$imgs)));
		unset($galleries);
		return;
	}
	
	private function createLikes(){
		require_once 'Cunity_Likes.class.php';
		$liker = new Cunity_Likes($this->cunity);
		$data = $liker->createLikes($this->request['id'],$this->request['rn'],$this->request['userid'],$this->request['cunityId']);
		$this->respond($this->cunity->returnJson(array("status"=>(int)($data!==false),"likes"=>$data)));
		unset($liker);
		return;
	}
	
	public function addComment(){
		require_once 'Cunity_Comments.class.php';		
		$comment_id=0;				
		$commentor = new Cunity_Comments($this->cunity);									
		$comment_id = $commentor->addComment($this->request['sender'],$this->request['id'],$this->request['rn'],$this->request['message'],$this->request['cunityId'],'user');
		if($comment_id !==false
			&& $this->connector->insertNewConnectedCunity($this->request['cunityId'],$this->request['cunityName'],$this->request['cunityUrl'],$this->request['cunityPublicKey'],"") 
			&& $this->connector->createNewRemoteUser($this->request['senderName'],$this->request['senderUserhash'],$this->request['sender'],$this->request['senderNickname'],$this->request['senderEmail'],$this->request['cunityId'],$this->request['senderSex'])){
			if($this->request['rn']=="pinboard"){
				require_once 'Cunity_Pinboard.class.php';
				$pinboard=new Cunity_Pinboard($this->cunity);
				$statusData=$pinboard->getStatusData($this->request['id']);
				if($pinboard->sendCommentNotifications($statusData['userid'], $statusData['pinboard_id'], $this->request['id'], $this->request['sender']))
					$this->respond($this->cunity->returnJson(array("status"=>1,"comment_id"=>$comment_id)));
			}			
			$this->respond($this->cunity->returnJson(array("status"=>1,"comment_id"=>$comment_id)));
			return;
		}
		$this->respond($this->cunity->returnJson(array("status"=>0,"comment_id"=>0)));
		return;
	}
	
	public function getLikes(){
		$ressource_id=$this->request['id'];
		$ressource_name=$this->request['rn'];
		$type=$this->request['type'];
		require_once 'Cunity_Likes.class.php';
		$liker=new Cunity_Likes($this->cunity);		
		$likes = ($type==0) ? $liker->getLikes($ressource_id,$ressource_name) : $liker->getDislikes($ressource_id,$ressource_name);
		unset($liker);
		$this->respond($this->cunity->returnJson(array("status"=>(int)(count($likesSend)>0),"likes"=>$likes)));		
		return;
	}
	
	public function remoteLike(){
		$action = $this->request['action'];
		require_once 'Cunity_Likes.class.php';
		require_once '../includes/functions.php';
		$liker = new Cunity_Likes($this->cunity);
		$liked = $liker->getLike($this->request['sender'],$this->request['id'],$this->request['rn'],$this->request['cunityId']);
		if($action=="dislike"){						
			if($this->cunity->getSetting("allow_dislike")==1){
				if($liked==1) // status already disliked
					return array("status"=>0,"msg"=>"already disliked");
				elseif($liked==0||!$liked){
					if($liker->deleteLike($this->request['sender'],$this->request['id'],$this->request['rn'],$this->request['cunityId']))
						$likeRes=$liker->dislike($this->request['sender'],$this->request['id'],$this->request['rn'],$this->request['cunityId']);
				}else return array("status"=>0,"msg"=>"internal error!");
			}elseif($liked==0)
				$likeRes=$liker->deleteLike($this->request['sender'],$this->request['id'],$this->request['rn'],$this->request['cunityId']);
		}elseif($action=="like"){
			if(!$liked||($liked==1&&$liker->deleteLike($this->request['sender'],$this->request['id'],$this->request['rn'],$this->request['cunityId'])))
	    		$likeRes=$liker->like($this->request['sender'],$this->request['id'],$this->request['rn'],$this->request['cunityId']);	    		
	    	else return array("status"=>0,"msg"=>"already liked or db error");
		}
		if($likeRes)
			$this->respond($this->cunity->returnJson(array("status"=>1,"likes"=>$liker->createLikes($this->request['id'],$this->request['rn'],$this->request['sender'],$this->request['cunityId']))));
		else
			$this->respond($this->cunity->returnJson(array("status"=>0,"likes"=>"")));
		return;
	}
	
	public function getImageLikes(){
		require_once 'Cunity_Galleries.class.php';
		$galleries = new Cunity_Galleries($this->cunity);
		$data = $galleries->getImageLikes($this->request['imgId'],0,true,$this->request['sender'],$this->request['cunityId']);
		unset($galleries);
		$this->respond($this->cunity->returnJson(array("status"=>1,"data"=>$data)));
		return;
	}
		
	public function handleFriendRequest(){			
		switch($this->request['action']){
			case 'add':								
				if($this->connector->insertNewConnectedCunity($this->request['cunityId'],$this->request['cunityName'],$this->request['cunityUrl'],$this->request['cunityPublicKey'],"") 
				&& $this->connector->createNewRemoteUser($this->request['name'],$this->request['userhash'],$this->request['localid'],$this->request['nickname'],$this->request['mail'],$this->request['cunityId'],$this->request['sex']) 
				&& $this->cunity->getFriender()->addFriendship($this->request['localid'], $this->request['userid'], $this->request['cunityId'],'sender')){																			
					$this->respond(json_encode(array("status"=>1)));										
				}else					
					$this->respond(json_encode(array("status"=>0)));			
			break;
			
			case 'block':
				if($this->connector->insertNewConnectedCunity($this->request['cunityId'],$this->request['cunityName'],$this->request['cunityUrl'],$this->request['cunityPublicKey'],"") 
				&& $this->connector->createNewRemoteUser($this->request['name'],$this->request['userhash'],$this->request['localid'],$this->request['nickname'],$this->request['mail'],$this->request['cunityId'],$this->request['sex']) 
				&& $this->cunity->getFriender()->blockFriendship($this->request['localid'], $this->request['userid'],$this->request['cunityId'])){																			
					$this->respond(json_encode(array("status"=>1)));										
				}else					
					$this->respond(json_encode(array("status"=>0)));							
			break;
			
			case 'confirm':
				if($this->cunity->getFriender()->confirmFriendship($this->request['userid'], $this->request['localid'], $this->request['cunityId'])){
					if($this->cunity->getNotifier()->addNotification('accepted_friend', $this->request['userid'],$this->request['senderName']))
						$this->respond(json_encode(array("status"=>1)));
					else
						$this->respond(json_encode(array("status"=>0)));
				}else
					$this->respond(json_encode(array("status"=>0)));
				
			break;
			
			case 'remove':
				$this->respond(json_encode(array("status"=>$this->cunity->getFriender()->removeFriendship($this->request['userid'],$this->request['localid'],$this->request['cunityId']))));
			break;
		}
	}
	
	public function addNotification(){
		$this->cunity->getNotifier()->insertNotification($this->request['receiver'], $this->request['message']);
	}
	
	public function respondProfile($profileData,$userid){
		$data['templateVars'] = htmlentities('---BEGIN_TPL_VARS---'.json_encode($profileData));
		$data['userData'] = $this->cunity->getUserData($userid);
		$cunityData = array(
			'cunityId'=> $this->connector->getcunityId(),
			'cunityName'=> $this->cunity->getSetting('name'),
			'cunityUrl'=>$this->cunity->getSetting('url'),
			'cunityPublicKey'=> $this->cryptor->readPublicKeyFromDatabase()
		);
		$data['userData'] = array_merge($data['userData'],$cunityData);
		$this->respond(json_encode($data));
	}
	
	public function shareFile(){
        require_once 'Cunity_Filesharing.class.php';
        $filesharing = new Cunity_Filesharing($this->cunity);
        $result=$filesharing->shareFile($this->request['fileId'],$this->request['userid'],$this->request['filename']);
        $this->respond($this->cunity->returnJson($result));
        unset($filesharing);
    }
    
    public function unshareFile(){
        require_once 'Cunity_Filesharing.class.php';
        $filesharing = new Cunity_Filesharing($this->cunity);
        $result=$filesharing->unshareFile($this->request['fileId'],$this->request['userid']);
        $this->respond($this->cunity->returnJson($result));
        unset($filesharing);
    }
    
    public function getFileDetails(){
        require_once 'Cunity_Filesharing.class.php';
        $filesharing = new Cunity_Filesharing($this->cunity);
        $result=$filesharing->getFileDetails($this->request['fileId'],0,true,$this->request['userid'],$this->request['cunityId']);
        $this->respond($this->cunity->returnJson($result));
        unset($filesharing);
    }
	
	public function respond($string){		
		echo $this->cryptor->createEncryptedText($string, $this->request['cunityId'],$this->publicKey);
	}
	
	public function setPublicKey($key){
		$this->publicKey = $key;
	}
}
?>