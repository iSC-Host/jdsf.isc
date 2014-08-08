<?php

set_include_path($_SESSION['cunity_trunk_folder'].'/classes/phpseclib');

require_once('Net/SSH2.php');
require_once('Crypt/RSA.php');
require_once('Crypt/AES.php');

set_include_path($_SESSION['cunity_trunk_folder'].'/classes');

class Cunity_Cryptor {

	private $cunity;
    private $rsa;   
    private $database;
    private $db;
    private $privateKey;
    private $publicKey; 
    public $settingstable;
          
    public function Cunity_Cryptor(Cunity $cunity){    	       
        $this->rsa = new Crypt_Rsa();
        $this->cunity = $cunity;                        
    }
    
	public function createEncryptedText($text, $cunityId,$public_key=""){
    	$aes = new Crypt_AES();
    	$key = $this->getCunityAES($cunityId);    	
    	if($key!==false){    		
    		$aes->setKeyLength(256);    	
	    	$aes->setKey($key);
	    	$encrypted = $aes->encrypt($text);	    	
	    	$result = base64_encode($encrypted);
    	}else{
    		$key = md5(time().$public_key);
    		$aes->setKeyLength(256);    	
	    	$aes->setKey($key);
	    	$this->rsa->loadKey($public_key);
	    	$encrypted = $aes->encrypt($text);
	    	$encrypted .= '---BEGIN_RSA---'.$this->rsa->encrypt($key);
	    	$result = base64_encode($encrypted);
    	}    	
    	return $result;    	
    }
    
    public function decryptText($text,$private_key=""){    	
    	$aes = new Crypt_AES();
    	$aes->setKeyLength(256);    	
    	$text = base64_decode($text);
    	$parts=explode('---BEGIN_RSA---', $text);          		
    	if(count($parts)>1){
    		$this->rsa->loadKey($private_key);
    		$aes->setKey($this->rsa->decrypt($parts[1]));    		
    		$result = $aes->decrypt($parts[0]);
    	}else{  		
    		$aes->setKey($this->getOwnAES());
    		$result = $aes->decrypt($text);
    	}  	    	
    	return $result;   	
    }
    
    private function getCunityAES($cunityId){
    	if($cunityId==0){
    		$res = $this->cunity->getDb()->query("SELECT `value` AS aes_key FROM ".$this->cunity->getConfig("db_prefix")."open".$cunity->getConfig("db_prefix")."settings WHERE setting = 'cunity_aes' LIMIT 1");    		
    	}else{
    		$res = $this->cunity->getDb()->query("SELECT aes_key FROM ".$this->cunity->getConfig("db_prefix")."connected_cunities WHERE cunityId = '".$cunityId."'");    		    	
    	}
    	$data = mysql_fetch_assoc($res);
    	if(mysql_num_rows($res)==0||$data['aes_key']=="")
    		return false;
		else    		    	
    		return $data['aes_key'];   	    	
    }
    
    public function getOwnAES(){
    	$result = $this->cunity->getDb()->query("SELECT `value` FROM ".$this->cunity->getConfig("db_prefix")."open".$cunity->getConfig("db_prefix")."settings WHERE setting = 'aes_key'");
		$data = mysql_fetch_assoc($result);
		return $data['value'];
    }
                         
    public function createKeys(){
        if($this->readPrivateKeyFromDatabase()==""||$this->readPublicKeyFromDatabase()==""){
            extract($this->rsa->createKey());
            $this->privateKey = $privatekey;
            $this->publicKey = $publickey;
        }                
    }
    
    public function saveNewKeys(){
        if($this->privateKey!=""&&$this->publicKey!=""){
        	$res = $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings SET `value` = '".$this->privateKey."' WHERE `setting` = 'private_key'") or die(mysql_error());
        	$res1 = $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings SET `value` = '".$this->publicKey."' WHERE `setting` = 'public_key'") or die(mysql_error());
        	if($res&&$res1)            
            	return true;
            return false;
        }        
        return false;
    }

    public function encryptParameters(array $parameters,$cunityId,$public_key){    	
    	$encryptedParametersTemp = json_encode($parameters);
    	$encryptedParametersTemp = $this->createEncryptedText($encryptedParametersTemp, $cunityId,$public_key);
    	$encryptedParameters['jsonData']=$encryptedParametersTemp;
    	return $encryptedParameters;
    }
    
    public function decryptParameters(array $parameters,$private_key){    	    	
    	$decryptedParameters = array();
    	$params = $this->decryptText($parameters['jsonData'], $private_key);    	   		
	    $decryptedParameters = json_decode($params,true);
	    return $decryptedParameters;
    }
    
    public function readPrivateKeyFromDatabase(){
        $result = $this->cunity->getDb()->query("SELECT `value` FROM ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings WHERE setting = 'private_key'");
		$data = mysql_fetch_assoc($result);
		return $data['value'];
    }
    public function readPublicKeyFromDatabase(){
        $result = $this->cunity->getDb()->query("SELECT `value` FROM ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings WHERE setting = 'public_key'");
		$data = mysql_fetch_assoc($result);
		return $data['value'];
    }  

    public function getCunityPublicKey(){
    	$result = $this->cunity->getDb()->query("SELECT `value` FROM ".$this->cunity->getConfig("db_prefix")."open".$this->cunity->getConfig("db_prefix")."settings WHERE setting = 'cunity_server_public_key'");
		$data = mysql_fetch_assoc($result);
		return $data['value'];
    }
}

?>