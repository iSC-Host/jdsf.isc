<?php
set_include_path($_SESSION['cunity_trunk_folder'].'/classes');

require_once('Zend/Http/Client.php');

require_once('Cunity_Cryptor.class.php');

class Cunity_Connector {

	private $cunityServerHost = "http://server.cunity.net/";
	private $cunity;
	private $client;
	private $crypt;
	private $cunityId;

	public function Cunity_Connector(Cunity $cunity){
		$this->cunity = $cunity;
		$this->crypt = new Cunity_Cryptor($this->cunity);
		$this->client = new Zend_Http_Client();
		$this->client->setConfig(array(
            'keepalive'=>true,
            'maxdirects'=>0
		));
		$this->client->setMethod(Zend_Http_Client::POST);
	}

	public function getCryptor(){
		return $this->crypt;
	}

	public function registerNewCunity($purpose,$country){
		$this->client->resetParameters();
		$this->client->setParameterPost(array(
			"c"=>"registerNewCunity",
            'name'=>$this->cunity->getSetting('name'),
            'slogan'=>$this->cunity->getSetting('slogan'),
            'country'=>$country,
			'language'=>$this->cunity->getSetting('language'),
            'purpose'=>$purpose,
            'admin_mail'=>$this->cunity->getSetting('contact_mail'),
            'domain'=>$this->cunity->getSetting('url'),
            'cunityPublicKey'=>$this->crypt->readPublicKeyFromDatabase()
		));
		$this->client->setUri($this->cunityServerHost.'connector.php');
		$response=$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			$back = json_decode($cont,true);
			if($back['status']==1){
				$this->savecunityId($back['cunityId']);
				$this->saveAesKey($back['aes_key']);
				$this->saveOwnAesKey($back['own_aes']);
				$this->saveCunityServerPublicKey($back['cunity_server_public_key']);
				$this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings SET `value` = 1 WHERE `setting` = 'connected_success'");
			}
			return $back;
		}
		return false;
	}

	private function saveCunityServerPublicKey($key){
		return $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings SET `value` = '".$key."' WHERE `setting` = 'cunity_server_public_key'");;
	}

	private function saveAesKey($key){
		$result = $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings SET `value` = '".$key."' WHERE `setting` = 'cunity_aes'");
		return (bool)$result;
	}

	private function saveOwnAesKey($key){
		$result = $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings SET `value` = '".$key."' WHERE `setting` = 'aes_key'");
		return (bool)$result;
	}

	public function createNewRegisterData(){
		$this->crypt->createKeys();
		$this->crypt->saveNewKeys();
		return true;
	}

	public function sendUserList(){
		$userlist = json_encode($this->createNewUserList());
		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"sendUserList",
			'users'=>$userlist
		),0,$this->crypt->getCunityPublicKey()));
		$this->client->setUri($this->cunityServerHost.'connector.php');
		$response=$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$back = json_decode($content,true);
			if($back['status']==1)
				return true;
		}
		return false;
	}

	private function createNewUserList(){
		$res = $this->cunity->getDb()->query("SELECT u.*,d.* FROM ".$this->cunity->getConfig('db_prefix')."users AS u, ".$this->cunity->getConfig('db_prefix')."users_details AS d WHERE u.userid=d.userid") or die(mysql_error());
		while($row = mysql_fetch_object($res)){
			$users[$row->userid] = array(
	    		'userid'   => $row->userid,
	    		'userhash' => $row->userhash,
	    		'nickname' => $row->nickname,
	    		'username' => $row->username,
	    		'mail'     => $row->mail,
				'title'    => $row->title,
				'cunityId'=>$this->getcunityId()
			);
		}
		return $users;
	}

	public function sendGalleryList(){
		$galleries = array();
		$res=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums");
		while($data=mysql_fetch_assoc($res)) $galleries[] = $data;
		$galleries = json_encode($galleries);
		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"sendGalleryList",
			'galleries'=>$galleries
		),0,$this->crypt->getCunityPublicKey()));
		$this->client->setUri($this->cunityServerHost.'connector.php');
		$response=$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, 0,$this->crypt->readPrivateKeyFromDatabase());
			$back = json_decode($cont,true);
			if($back['status']==1)
				return true;
		}
		return false;
	}

	public function updateGallery($albumid){
		$res=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE album_id = '".$albumid."'");
		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"updateGallery",
			'data'=>json_encode(mysql_fetch_assoc($res))
		),0,$this->crypt->getCunityPublicKey()));
		$this->client->setUri($this->cunityServerHost.'connector.php');
		$response=$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, 0,$this->crypt->readPrivateKeyFromDatabase());
			$back = json_decode($cont,true);
			if($back['status']==1)
				return true;
		}
		return false;
	}

	public function deleteGallery($albumid){
		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"deleteGallery",
			"id"=>$albumid,
			"userid"=>$_SESSION['userid']
		)),0,$this->crypt->getCunityPublicKey());
		$this->client->setUri($this->cunityServerHost.'connector.php');
		$response=$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, 0,$this->crypt->readPrivateKeyFromDatabase());
			$back = json_decode($cont,true);
			if($back['status']==1)
				return true;
		}
		return false;
	}

	public function addNewGallery($albumid){
		$res=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."galleries_albums WHERE album_id = '".$albumid."'");
		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"addNewGallery",
			'data'=>json_encode(mysql_fetch_assoc($res))
		),0,$this->crypt->getCunityPublicKey()));
		$this->client->setUri($this->cunityServerHost.'connector.php');
		$response=$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, 0,$this->crypt->readPrivateKeyFromDatabase());
			$back = json_decode($cont,true);
			if($back['status']==1)
				return true;
		}
		return false;
	}

	public function getAlbumData($albumid,$cunityId,array $cunityData=array()){
		$cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
			return false;

		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"getAlbumData",
			"albumid"=>$albumid
		),$cunityId,$cunityData['cunityPublicKey']));
		$this->client->setUri($cunityData['cunityUrl'].'/openCunity/connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			$result=json_decode($cont,true);
			if($result['status']==1)
				return $result['data'];
			return false;
		}
	}

	public function getImagesOfAlbum($albumid,$cunityId,$limit=0,array $cunityData=array()){
		$cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
			return false;

		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"getImagesOfAlbum",
			"albumid"=>$albumid,
            "limit"=>$limit
		),$cunityId,$cunityData['cunityPublicKey']));
		$this->client->setUri($cunityData['cunityUrl'].'/openCunity/connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			$result=json_decode($cont,true);
			if($result['status']==1)
				return $result['data'];
			return false;
		}
	}

	public function getFriendsAlbums(){
		require_once 'Cunity_Friends.class.php';
		$friender = new Cunity_Friends($this->cunity);
		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
				"c"=>"getFriendsAlbums",
				'friendList'=>json_encode($friender->getFriendList($_SESSION['userid']))
		),0,$this->crypt->getCunityPublicKey()));
		$this->client->setUri($this->cunityServerHost.'connector.php');
		$response=$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content,0,$this->crypt->readPrivateKeyFromDatabase());
			$back = json_decode($cont,true);
			if($back['status']==1)
				return $back['rows'];
		}
		return false;
	}

	public function savecunityId($id){
		$result = $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings SET `value` = ".(int)$id." WHERE `setting` = 'cunityId'");
		return (bool)$result;
	}

	public function getcunityId(){
		$result = $this->cunity->getDb()->query("SELECT `value` FROM ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings WHERE setting = 'cunityId'");
		$data = mysql_fetch_assoc($result);
		return $data['value'];
	}

	public function isConnected(){
		$result = $this->cunity->getDb()->query("SELECT `value` FROM ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings WHERE setting = 'connected_success'");
		$data = mysql_fetch_assoc($result);
		return (bool)$data['value'];
	}

	public function sendNewUserToServer($userid){
		$res=$this->cunity->getDb()->query("SELECT u.userhash,u.username,u.nickname,u.mail,d.title FROM ".$this->cunity->getConfig("db_prefix")."users AS u,".$this->cunity->getConfig("db_prefix")."users_details AS d WHERE u.userid=d.userid AND u.userid = ".(int)$userid." LIMIT 1");
		$row = mysql_fetch_object($res);
		$userData = array(
    		'userid'   => $userid,
    		'userhash' => $row->userhash,
    		'nickname' => $row->nickname,
    		'username' => $row->username,
    		'mail'     => $row->mail,
			'title'    => $row->title,
			'cunityId'=>$this->getcunityId()
		);
		$userData = json_encode($userData);

		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array("c"=>"newUser","data"=>$userData),0,$this->crypt->getCunityPublicKey()));
		$this->client->setUri($this->cunityServerHost.'connector.php');
		$response=$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, 0,$this->crypt->readPrivateKeyFromDatabase());
			$back = json_decode($cont,true);
			if($back['status']==1)
				return true;
		}
		return false;
	}

	public function deleteUserFromServer($userid){
		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array("c"=>"deleteUser","userid"=>$userid,"cunityId"=>$this->getcunityId()),0,$this->crypt->getCunityPublicKey()));
		$this->client->setUri($this->cunityServerHost.'connector.php');
		$response=$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, 0,$this->crypt->readPrivateKeyFromDatabase());
			$back = json_decode($cont,true);
			if($back['status']==1)
				return true;
		}
		return false;
	}

	public function searchUserOnServer($term){
		$this->client->resetParameters();
		$params = $this->crypt->encryptParameters(array(
            "c"=>"searchUser",
            "term"=>$term,
			"cunityId"=>$this->getcunityId(),
            "cunityPublicKey"=>$this->crypt->readPublicKeyFromDatabase()
            ),0,$this->crypt->getCunityPublicKey());
		$this->client->setParameterPost($params);
		$this->client->setUri($this->cunityServerHost.'connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, 0,$this->crypt->readPrivateKeyFromDatabase());
			return json_decode($cont,true);
		}
		return false;
	}

	public function getCunityDataFromServer($cunityId){
		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
            "c"=>"getCunityData",
			"searchCunityId"=>$cunityId,
			"cunityId"=>$this->getcunityId(),
            "cunityPublicKey"=>$this->crypt->readPublicKeyFromDatabase()
            ),0,$this->crypt->getCunityPublicKey()));
		$this->client->setUri($this->cunityServerHost.'connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			return json_decode($cont,true);
		}
		return false;
	}

	public function getCunityData($cunityId){
		$res = $this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."connected_cunities WHERE cunityId = '".$cunityId."'");
		if(mysql_num_rows($res)==0)
			return false;
		return mysql_fetch_assoc($res);
	}

	public function handleFriendRequest($sender,$receiver,$cunityId,$action,$userData=array()){
		if(!isset($userData['cunityUrl']))
			$userData = array_merge($userData,$this->getCunityData($cunityId));

		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"handleFriendRequest",
			"action"=>$action,
			"userid"=>$receiver,
			"localid"=>$sender,
			"name"=>getUserName($sender),
			"userhash"=>getUserHash($sender),
			"mail"=>getmail($sender),
			"sex"=>getSex($sender),
			"nickname"=>getNickname($sender)
		),$cunityId,$userData['cunityPublicKey']));

		$this->client->setUri($userData['cunityUrl'].'/openCunity/connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());

			$response = json_decode($cont,true);
			if(!(bool)$response['status'])
				return false;
			switch($action){
				case 'add':
					if($this->insertNewConnectedCunity($cunityId, $userData['cunityName'], $userData['cunityUrl'], $userData['cunityPublicKey'], "")
					&&
					$this->createNewRemoteUser($userData['username'], $userData['userhash'], $receiver, $userData['nickname'],$userData['mail'], $cunityId, $sex))
						return true;
				break;

				case 'block':
					if($this->insertNewConnectedCunity($cunityId, $userData['cunityName'], $userData['cunityUrl'], $userData['cunityPublicKey'], "")
					&&
					$this->createNewRemoteUser($userData['username'], $userData['userhash'], $receiver, $userData['nickname'],$userData['mail'], $cunityId, $sex))
						return true;
				break;

				default:
					return true;
				break;
			}
			return true;
		}
	}

	public function sendMessage($sender,$receiver,$msg,$cunityId,array $cunityData=array()){
		if(count($cunityData)==0)
			$cunityData = $this->getCunityData($cunityId);
		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"sendMessage",
			"message"=>$msg,
			"sender"=>$sender,
			"receiver"=>$receiver,
			"senderName"=>getUserName($sender),
			"senderUserhash"=>getUserHash($sender),
			"senderEmail"=>getmail($sender),
			"senderSex"=>getSex($sender),
			"senderNickname"=>getNickname($sender)
		),$cunityId,$cunityData['cunityPublicKey']));

		$this->client->setUri($cunityData['cunityUrl'].'/openCunity/connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			$response = json_decode($cont,true);
			return $response['status'];

			return false;
		}
	}

	public function getUserProfile($userhash,$cunityId){
		$cunityData=$this->getCunityData($cunityId);
		if($cunityData===false)
			$cunityData = $this->getCunityDataFromServer($cunityId);

		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"userhash"=>$userhash,
			"language"=>$_SESSION['language'],
			"ownId"=>$_SESSION['userid']
		),$cunityId,$cunityData['cunityPublicKey']));
		$this->client->setUri($cunityData['cunityUrl'].'/profile.php?getRemoteProfile=1');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content = $this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content,$this->crypt->readPrivateKeyFromDatabase());
			$data = json_decode($cont,true);
			if(!isset($data['status'])){
				$data['templateVars']=explode('---BEGIN_TPL_VARS---', $data['templateVars']);
				$response = json_decode(html_entity_decode($data['templateVars'][1]),true);
				return array('templateVars'=>$response,'userData'=>$data['userData']);
            }else if(isset($data['status'])&&$data['status']==2)
				return 2;//User is blocked
		}
	}

	public function createNewRemoteUser($name,$hash,$lid,$nick,$mail,$cid,$sex){
		$check = $this->cunity->getDb()->query("SELECT COUNT(*) FROM ".$this->cunity->getConfig('db_prefix')."connected_users WHERE localid = '".$lid."' AND cunityId = '".$cid."'") or die(mysql_error());
		$d = mysql_fetch_assoc($check);
		if((int)$d['COUNT(*)']>0)
			return true;

		$res = $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig('db_prefix')."connected_users
			(
				`localid`,
				`userhash`,
				`username`,
				`nickname`,
				`mail`,
				`sex`,
				`cunityId`
			)
			VALUES
			(
				'".$lid."',
				'".$hash."',
				'".$name."',
				'".$nick."',
				'".$mail."',
				'".$sex."',
				'".$cid."'
			)
			") or die(mysql_error());
		return $res;
	}

	public function insertNewConnectedCunity($cunityId,$cunityname,$cunityUrl,$cunitypublickey,$cunityAES){
		$check = $this->cunity->getDb()->query("SELECT COUNT(*) FROM ".$this->cunity->getConfig('db_prefix')."connected_cunities WHERE cunityId = '".$cunityId."'") or die(mysql_error());
		$d=mysql_fetch_assoc($check);
		if((int)$d['COUNT(*)']==1)
			return true;

		$res = $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig('db_prefix')."connected_cunities
		(
			`cunityId`,
			`cunityname`,
			`cunityUrl`,
			`cunityPublicKey`,
			`aes_key`
		)
		VALUES
		(
			'".$cunityId."',
			'".$cunityname."',
			'".$cunityUrl."',
			'".$cunitypublickey."',
			'".$cunityAES."'
		)
		") or die(mysql_error());
		return $res;
	}

	public function insertPinboard($userid,$status_id,$pinboard_id, $message, $type, $receiver,$cunityId,array $userData=array(),$imgContent=""){
		$cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
			return false;

		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"insertPinboard",
			"message"=>$message,
			"sender"=>$userid,
			"pinboard_id"=>$pinboard_id,
			"status_id"=>$status_id,
			"receiver"=>$receiver,
			"type"=>$type,
			"imgContent"=>$imgContent,
			"senderName"=>getUserName($userid),
			"senderUserhash"=>getUserHash($userid),
			"senderEmail"=>getmail($userid),
			"senderSex"=>getSex($userid),
			"senderNickname"=>getNickname($userid)
		),$cunityId,$cunityData['cunityPublicKey']));
		$this->client->setUri($cunityData['cunityUrl'].'/openCunity/connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			$response = json_decode($cont,true);
			if($response['status']==1)
				return $response['status_id'];
		}
		return false;
	}

	public function deleteStatus($id,$userid,$cunityId,$remoteDelete=false){
		$cunityData=$this->getCunityData($cunityId);
		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"deleteStatus",
			"userid"=>$userid,
			"status_id"=>$id,
			"remoteDelete"=>$remoteDelete
		),$cunityId,$cunityData['cunityPublicKey']));
		$this->client->setUri($cunityData['cunityUrl'].'/openCunity/connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			$response = json_decode($cont,true);
			if($response['status']==1)
				return $response['id'];
		}
		return false;
	}

	public function getRemotePinboard($sender,$cunityId, $pinboard_id, $receiver,array $request,array $cunityData=array(),$lastStatusId,$statusCount){
		$cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
			return false;

		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"loadPinboard",
			"userid"=>$sender,
			"p"=>$pinboard_id,
			"s"=>$request['s'],
			"r"=>$request['r'],
			"type"=>$request['type'],
			"id"=>$request['id'],
			"do"=>$request['do'],
			"style"=>$_SESSION['style'],
			"language"=>$_SESSION['language'],
			"lastStatusId"=>$lastStatusId,
			"statusCount"=>$statusCount
		),$cunityId,$cunityData['cunityPublicKey']));
		$this->client->setUri($cunityData['cunityUrl'].'/openCunity/connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			return $cont;
		}
	}

	public function addComment($sender,$cunityId,$ressource_id,$ressource_name,$message,array $cunityData=array()){
		$cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
			return false;

		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"addComment",
			"sender"=>$sender,
			"id"=>$ressource_id,
			"rn"=>$ressource_name,
			"message"=>$message,
			"style"=>$_SESSION['style'],
			"language"=>$_SESSION['language'],
			"senderName"=>getUserName($sender),
			"senderUserhash"=>getUserHash($sender),
			"senderEmail"=>getmail($sender),
			"senderSex"=>getSex($sender),
			"senderNickname"=>getNickname($sender)
		),$cunityId,$cunityData['cunityPublicKey']));
		$this->client->setUri($cunityData['cunityUrl'].'/openCunity/connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			$result=json_decode($cont,true);
			if($result['status']==1)
				return $result['comment_id'];
			return false;
		}
	}

	public function createLikes($ressource_id,$ressource_name,$userid,$cunityId,array $cunityData=array()){
		$cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
		return false;

		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
				"c"=>"createLikes",
				"userid"=>$userid,
				"id"=>$ressource_id,
				"rn"=>$ressource_name,
				"language"=>$_SESSION['language'],
				"cunityId"=>$this->getcunityId(),
				"cunityName"=>$this->cunity->getSetting('name'),
				"cunityUrl"=>$this->cunity->getSetting('url'),
				"cunityPublicKey"=>$this->crypt->readPublicKeyFromDatabase()
		),$cunityId,$cunityData['cunityPublicKey']));
		$this->client->setUri($cunityData['cunityUrl'].'/openCunity/connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			$result=json_decode($cont,true);
			if($result['status']==1)
				return $result['likes'];
			return false;
		}
	}

	public function getLikes($ressource_id,$ressource_name,$type,$cunityId,array $cunityData=array()){
		$cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
		return false;

		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"getLikes",
			"id"=>$ressource_id,
			"rn"=>$ressource_name,
			"type"=>$type
		),$cunityId,$cunityData['cunityPublicKey']));
		$this->client->setUri($cunityData['cunityUrl'].'/openCunity/connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			$result=json_decode($cont,true);
			if($result['status']==1)
				return $result['likes'];
			return false;
		}
	}

	public function like($sender,$cunityId,$ressource_id,$ressource_name,$action="like",array $cunityData=array()){
		$cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
			return false;

		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters(array(
			"c"=>"remoteLike",
			"sender"=>$sender,
			"action"=>$action,
			"id"=>$ressource_id,
			"rn"=>$ressource_name,
			"style"=>$_SESSION['style'],
			"language"=>$_SESSION['language'],
			"senderName"=>getUserName($sender),
			"senderUserhash"=>getUserHash($sender),
			"senderEmail"=>getmail($sender),
			"senderSex"=>getSex($sender),
			"senderNickname"=>getNickname($sender)
		),$cunityId,$cunityData['cunityPublicKey']));
		$this->client->setUri($cunityData['cunityUrl'].'/openCunity/connector.php');
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			$result=json_decode($cont,true);
			if($result['status']==1)
				return $result['likes'];
			return false;
		}
	}

	public function getImageLikes($imgId,$cunityId,array $cunityData=array()){
		$cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
			return false;

		$result=$this->sendRequest(array(
			"c"=>"getImageLikes",
			"sender"=>$_SESSION['userid'],
			"imgId"=>$imgId
		),$cunityId,$cunityData['cunityPublicKey'],$cunityData['cunityUrl'].'/openCunity/connector.php');
		if($result!==false){
			if($result['status']==1)
				return $result['data'];
		}
		return false;
	}

	private function sendRequest(array $params,$cunityId,$publicKey,$url){
        $params = array_merge(array(
            "cunityId"=>$this->getcunityId(),
			"cunityName"=>$this->cunity->getSetting('name'),
			"cunityUrl"=>$this->cunity->getSetting('url'),
			"cunityPublicKey"=>$this->crypt->readPublicKeyFromDatabase()),$params);
		$this->client->resetParameters();
		$this->client->setParameterPost($this->crypt->encryptParameters($params,$cunityId,$publicKey));
		$this->client->setUri($url);
		$this->client->request(Zend_Http_Client::POST);
		if($this->client->getLastResponse()->isSuccessful()){
			$content=$this->client->getLastResponse()->getBody();
			$cont = $this->crypt->decryptText($content, $this->crypt->readPrivateKeyFromDatabase());
			return json_decode($cont,true);
		}
		return false;
	}

	public function shareFile($fileId,$userid,$cunityId){
        $cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
			return false;

		$result=$this->sendRequest(array(
			"c"=>"shareFile",
			"fileId"=>$fileId,
			"filename"=>$filename,
			"userid"=>$userid
		),$cunityId,$cunityData['cunityPublicKey'],$cunityData['cunityUrl'].'/openCunity/connector.php');
		if($result!==false)
			return ($result['status']==1);
		return false;
    }

    public function unshareFile($fileId,$userid,$cunityId){
        $cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
			return false;

		$result=$this->sendRequest(array(
			"c"=>"unshareFile",
			"fileId"=>$fileId,
			"userid"=>$userid
		),$cunityId,$cunityData['cunityPublicKey'],$cunityData['cunityUrl'].'/openCunity/connector.php');
		if($result!==false)
			return ($result['status']==1);
		return false;
    }

	public function getFileDetails($fileId,$cunityId){
        $cunityDataDb=$this->getCunityData($cunityId);
		if($cunityDataDb)
			$cunityData = $cunityDataDb;
		elseif(!$cunityData&&count($cunityData)==0)
			return false;

		$result=$this->sendRequest(array(
			"c"=>"getFileDetails",
			"fileId"=>$fileId,
			"userid"=>$_SESSION['userid'],
			"language"=>$_SESSION['language']
		),$cunityId,$cunityData['cunityPublicKey'],$cunityData['cunityUrl'].'/openCunity/connector.php');
		if($result!==false)
			if($result['status']==1)
                return $result;
		return false;
    }
}
?>