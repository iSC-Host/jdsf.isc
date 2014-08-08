<?php

class Cunity_Registration {

	private $cunity = null;
	private $regex = array(
		"mail"=>'/^[A-Z0-9.%+-_]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i',
		"nickname"=>'/^([a-z0-9_-]+)$/i',
		"password"=>'/^([a-z0-9_-]+)$/i'
	);
	private $registrationFields = array();
	
	public function Cunity_Registration(Cunity $cunity){
		$this->cunity = $cunity;		
	}
	
	public function form_basics($string, $mlen = 50, $len = 3) {
		if(isset($string) && strlen($string) >= $len && strlen($string) <= $mlen)return true;
		else return false;
	}
	
	public function setNameType($type){
		return $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."registration_fields SET importance = 'M' WHERE name = 'firstname' OR name = 'lastname'");
	}
	
	public function register_basics($field,$string,$mlen = 50, $len = 3){
	    if($string == ""){
	        if($this->registrationFields[$field]['importance'] == 'M'){
	            return false;
	        }elseif($this->registrationFields[$fields]['importance'] == 'O'){
	            return true;
	        }
	    }else{
	        if(strlen($string) >= $len && strlen($string) <= $mlen){
	            return true;
	        }else{
	            return false;
	        }
	    }                	
	}
	
	private function compareFields($field,$field,$field2){
		if($field==$field2){			
			return true;			
		}else
			return false;
	}
	
	private function getRegistrationFields(){
		$res = $this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."registration_fields WHERE active = 'Y'");
		while($data=mysql_fetch_assoc($res)){
			$name = $data['name'];
			unset($data['name']);
			$return[$name] = $data;
		}
		$this->registrationFields = $return;
		return true;
	}
	
	public function getNameFields(){
		$res = $this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."registration_fields WHERE name= 'firstname' OR name = 'lastname'");
		while($data=mysql_fetch_assoc($res)){
			$langName = ($data['def'] == 'Y') ? $this->cunity->getLang('register_'.$data['name']) : $data['name'];
			$names .= '<tr>';
			if($data['importance'] == 'M'){
				$names .= '<td><label for="'.$data['name'].'">'.$langName.'<span class="required_star">*</span></label></td>';
			}else{
				$names .= '<td><label for="'.$data['name'].'">'.$langName.'</label></td>';
			}
			$names .= '<td>';
			$names .= '<input type="text" id="'.$data['name'].'" name="'.$data['name'].'" value="'.$data['value'].'"/>';			
			$names .= '</td>';
			$names .= '</tr>';
		}
		return $names;
	}
	
	public function getAddedFields(array $fields=array(),$importance="'O','M'"){
		$return = array();
		$res = $this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."registration_fields WHERE active = 'Y' AND def != 'Y' AND `importance` IN (".$importance.")");
		while($data=mysql_fetch_assoc($res)){
			if(isset($fields[$data['name']])&&$fields[$data['name']]!="")
				continue;
			$name = $data['name'];
			unset($data['name']);
			$return[$name] = $data;
		}
		return $return;
	}
	
	public function createAddedFieldList(array $checkFields=array(),$importance="'O','M'"){
		$fields = $this->getAddedFields($checkFields,$importance);
		if(count($fields)==0)
			return false;		
		$new_fields = "";
		foreach($fields AS $name => $data){
			$new_fields .= '<tr>';	
            if($data['def'] == 'Y')
            	$langName = $lang['register_'.$name];
            else
            	$langName = $name;
			if($data['importance'] == 'M'){
               	$new_fields .= '<td><label for="'.$name.'">'.$langName.'<span class="required_star">*</span></label></td>';
            }else{
				$new_fields .= '<td><label for="'.$name.'">'.$langName.'</label></td>';
			}
            $new_fields .= '<td>';           	
            if($data['type'] == 'T'){
				$new_fields .= '<input type="text" id="'.$name.'" name="'.$name.'" value="'.$data['value'].'"/>';
            }elseif($data['type'] == 'C'){
            	$value = json_decode($data['value'],true);
            	foreach($value AS $count => $field)
            		$new_fields .= '<input type="checkbox" name="'.$name.'[]" value="'.$field.'" id="'.$name.'_'.$count.'" style="margin: 5px 0px;"/><label style="margin: 5px 3px;" for="'.$name.'_'.$count.'">'.$field.'</label><br />';                          
			}elseif($data['type'] == 'R'){
				$value = json_decode($data['value'],true);                        
				foreach($value AS $count => $field)
            		$new_fields .= '<input type="radio" name="'.$name.'" value="'.$field.'" id="'.$name.'_'.$count.'" style="margin: 5px 0px;"/><label style="margin: 5px 3px;" for="'.$name.'_'.$count.'">'.$field.'</label><br />';            	
			}elseif($data['type'] == 'S'){
				$value = json_decode($data['value'],true);
                $new_fields .= '<select name="'.$name.'" >';
                foreach($value AS $count => $field)
            		$new_fields .= '<option value="'.$field.'"/>'.$field.'</option>';                                        
            	$new_fields .= '</select>';
            }
			$new_fields .= '</td>';
            $new_fields .= '</tr>';
		}
		return $new_fields;
	}
	
	function checkMandatoryFields($userid){
	    global $cunity;
	    $q = "SELECT * FROM ".$cunity->getConfig("db_prefix")."registration_fields WHERE active = 'Y' AND importance = 'M' AND name != 'nickname' AND name != 'password' AND name != 'email'";
	    $q1 = "SELECT u.*,d.*  FROM ".$cunity->getConfig("db_prefix")."users AS u, ".$cunity->getConfig("db_prefix")."users_details AS d WHERE u.userid = d.userid AND u.userid = '".$userid."'";
	    $res = $cunity->getDb()->query($q);    
	    $res1 = $cunity->getDb()->query($q1);
	    $data = mysql_fetch_assoc($res1);
	    while($mdata = mysql_fetch_assoc($res)){
	        if($data[$mdata['name']] == "")
	            return true;
	    }
    return false;
}
	
	public function checkBirthday($day,$month,$year){
		if($day > 31 || $day < 1 || $month > 12 || $month < 1 || $year < 1900 || $year > date('Y',time()))
			return false;
		elseif(mktime(0,0,0,$month,$day,$year)>mktime(0, 0, 0, date('n'), date('j'), date('Y')-$this->cunity->getSetting('register_age'))){
			return false;
		}else
			return true;		
	}
	
	public function checkInput($field,$input,$checkExist=true){
		if($this->form_basics($input)){
       		if(preg_match($this->regex[$field], $input)){       			
       			if($checkExist){       				       				
       				$res = $this->cunity->getDb()->query("SELECT COUNT(*) AS c FROM ".$this->cunity->getConfig("db_prefix")."users WHERE `".$field."` = '".$input."'");
       				$data=mysql_fetch_assoc($res);       				       			
       				return $data['c']==0;
       			}else
       				return true;
       		}
       	}
       		return false;
	}
	
	public function createUniqueUserHash($nick){
		$userhash = sha1(time().$nick);
		$res = $this->cunity->getDb()->query("SELECT COUNT(*) FROM ".$this->cunity->getConfig("db_prefix")."users WHERE userhash = '".$userhash."'");
        $userhashdata = mysql_fetch_assoc($res);
        if($userhashdata['COUNT(*)'] > 0){
        	$string = $userhash.rand();
            return $this->createUniqueUserHash($string);
        }                                            
        else
        	return $userhash;
	}
	
	public function checkFullRegistration(array $requestData){
		$form_errors = array();
		if(!$this->checkInput('nickname', $requestData['nickname'],true))
			$form_errors[] = 'nickname';		
		if(!$this->checkInput("mail", $requestData['mail1'],true)){
			$form_errors[] = 'mail1';
		}elseif(!$this->compareFields('mail', $requestData['mail1'], $requestData['mail2'])){
			$form_errors[] = 'mail1';
			$form_errors[] = 'mail2';
		}
		if(strlen($requestData['pw1'])<6){
			$form_errors['error'][] = 'pw1';
		}elseif(!$this->compareFields('password', $requestData['pw1'], $requestData['pw2'])){
			$form_errors[] = 'pw1';
			$form_errors[] = 'pw2';
		}		
		if(!$this->checkBirthday($requestData['day'],$requestData['month'],$requestData['year'])){
			$form_errors[] = 'birthday';
		}
		if($requestData['terms']==false)
			$form_errors[] = 'terms';
		if($requestData['privacy']==false)
			$form_errors[] = 'privacy';
		if(count($form_errors)==0)
			return true;
		else 
			return $form_errors;
	}
	
	private function insertUserToDB(array $userData,$vkey){
		if($this->cunity->getSetting('registration_method')=='activate'){            
            $insert = $this->cunity->getDb()->query("
            INSERT INTO ".$this->cunity->getConfig("db_prefix")."users(
                `nickname`,
                `password`,
                `username`,
                `userhash`,
                `mail`,
                `groupid`,
                `invisible`,
                `vkey`,
                `vf_req`,
                `verif_mail`
            )VALUES(
                '".mysql_real_escape_string($userData['nickname'])."',
            	'".mysql_real_escape_string(sha1($userData['pw1']))."',
            	'".mysql_real_escape_string($userData['nickname'])."',
            	'".$this->createUniqueUserHash($userData['nickname'])."',
            	'".mysql_real_escape_string($userData['mail1'])."',
            	7,
            	'N',
            	'".mysql_real_escape_string($vkey)."',
            	0,
                0
            )");
        }else{
        	$insert = $this->cunity->getDb()->query("
            INSERT INTO ".$this->cunity->getConfig("db_prefix")."users(
                `nickname`,
                `password`,
                `username`,
                `userhash`,
                `mail`,
                `groupid`,
                `invisible`,
                `vkey`,
                `vf_req`,
                `verif_mail`
            )VALUES(
                '".mysql_real_escape_string($userData['nickname'])."',
            	'".mysql_real_escape_string(sha1($userData['pw1']))."',
            	'".mysql_real_escape_string($userData['nickname'])."',
            	'".$this->createUniqueUserHash($userData['nickname'])."',
            	'".mysql_real_escape_string($userData['mail1'])."',
            	7,
            	'N',
            	'".mysql_real_escape_string($vkey)."',
            	0,
                1
            )");            
        }
        if($insert)
        	return mysql_insert_id();
        else
        	return false;        
	}
	
	private function insertUserDetails($userid,array $request,$birthday){
		$res = $this->cunity->getDb()->query("SELECT name, importance FROM ".$this->cunity->getConfig("db_prefix")."registration_fields WHERE (active = 'Y' AND edit = 'Y')");    	
    	while($names = mysql_fetch_assoc($res)){
			$nameDb .= ''.$names['name'].',';
            $valueDb .= '\''.mysql_real_escape_string($request[$names['name']]).'\',';                           
        }
        $nameDb = substr($nameDb,0,-1);
        $valueDb = substr($valueDb,0,-1);		
        if(mysql_num_rows($res) == 0){
            $result = $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."users_details
                        (
                        userid,
                        title,
                        registered
                        )
                        VALUES
                        (
                        ".$userid.",
                        ".(int)$request['sex'].",
                        NOW()
                        )");
        }else{
            $result = $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."users_details
                        (
                        userid,
                        title,
                        ".$nameDb.",
                        registered,
                        birthday
                        )
                        VALUES
                        (
                        ".$userid.",
                        ".(int)$request['sex'].",
                        ".$valueDb.",
                        NOW(),
                        '".$birthday."'
                        )") or die(mysql_error());
        }
        if($result){        	
        	require_once 'Cunity_Connector.class.php';
        	$connector = new Cunity_Connector($this->cunity);
        	if($connector->isConnected())
	        	$connector->sendNewUserToServer($userid);
	        unset($connector);
        	return true;
        }
        return false;
	}
	
	public function createNewUser(array $request){	
        $birthday = date("Y-m-d", mktime(0,0,0,$request['month'], $request['day'], $request['year']));
            
    	// Create a random Verification-Key
    	$vkey_arr = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));
    	$vkey_arr_len = count($vkey_arr);
    	$vkey = '';

    	for($v = 0; $v < 12; $v++)
    		$vkey .= $vkey_arr[mt_rand(0, ($vkey_arr_len - 1))];    
    	
    	$userId = $this->insertUserToDB($request,$vkey);
    	if(!$this->insertUserDetails($userId, $request,$birthday))
    		return false;
        $this->cunity->getDb()->query("INSERT INTO
			".$this->cunity->getConfig("db_prefix")."events(
                founder_id,
                birthday,
                start_date,
                end_date,
                start_time,
		     	end_time
			)VALUES(
              	'".$userId."',
                1,
                '".$birthday."',
                '".$birthday."',
                '00:00:00',
                '23:59:59'
			)");     
        
        if(isset($_SESSION['reg_code']) && !empty($_SESSION['reg_code'])){
        	$res = $this->cunity->getDb()->query("SELECT userid FROM ".$this->cunity->getConfig("db_prefix")."invitation_codes WHERE code = '".mysql_real_escape_string($_SESSION['reg_code'])."'");
            $code = mysql_fetch_assoc($res);
            $codeId = $code['userid'];
            $this->cunity->getFriender()->addFriendship($codeId,$userId);
        }
        
        $vkeyMail = $userId.'-'.$vkey;
        
        $lang = $this->cunity->getLang();
        $msg = '<h3>'.$lang['register_thanks'].'</h3>';
    	if($this->cunity->getSetting('registration_method') == 'activate'){
            $msg .= '<p>'.$lang['register_activate_account_activate'].'</p>';
            $admin_msg = '<p>'.$lang['register_activate_user_body'].'</p>
                      <ul>
                        <li>Nickname: '.$request['nickname'].'</li>
                        <li>E-Mail: '.$request['mail1'].'</li>                        
                      </ul>
                      <p>
                        '.$lang['register_activate_here'].'<br/><a href="'.$this->cunity->getSetting('url').'/verify.php?k='.$vkeyMail.'&activate=1">'.$this->cunity->getSetting('url').'/verify.php?k='.$vkey.'&activate=1</a>                        
                      </p>';
            $this->cunity->getNotifier()->sendNotification($this->cunity->getSetting('contact_mail'),"Cunity Admin",$lang['register_activate_user_subj'], $admin_msg);
        }else{
            $msg .= '<p>'.$lang['register_activate_account'].'</p>';
            if($this->cunity->getSetting('notify_new_users') == 'yes')
    	        $this->cunity->getNotifier()->sendNotification($this->cunity->getSetting('contact_mail'),"Cunity Admin",$lang['register_new_user_subj'], '<p>'.$lang['register_new_user_body'].'</p>');
        }
    	$msg .= '<p><a href="'.$this->cunity->getSetting('url').'/verify.php?k='.$vkeyMail.'">'.$this->cunity->getSetting('url').'/verify.php?k='.$vkeyMail.'</a></p>';

    	$this->cunity->getNotifier()->sendNotification($request['mail1'],getUserName($userId),$lang['register_activate_subj'],$msg);
    	return true;
	}

	public function resetPw($email){
		$lang = $this->cunity->getLang();
		$res = $this->cunity->getDb()->query("SELECT userid FROM ".$this->cunity->getConfig("db_prefix")."users WHERE mail = '".mysql_real_escape_string($email)."' LIMIT 1");		

		if(mysql_num_rows($res) == 1){
			$data = mysql_fetch_assoc($res);
						
			$tmp1 = time();
			$tmp2 = sha1($email);

			$mkey = substr($tmp1, 0, 4).'-';
			$mkey .= substr($tmp2, 0, 7).'-';
			$mkey .= substr($tmp1, 4).'-';
			$mkey .= $data['userid'];

			$body = $lang['register_password_reset_try']."\r\n";
			$body .= $lang['register_password_reset_mail']."\r\n\r\n";
			$body .= '<p><a href="'.$this->cunity->getSetting('url')."/register.php?c=resetpw&mkey=".$mkey.'">'.$this->cunity->getSetting('url')."/register.php?c=resetpw&mkey=".$mkey."</a></p>\r\n\r\n";
			$body .= $lang['register_password_reset_mail2'];

			if($this->cunity->getNotifier()->sendNotification($email,getUserName($data['userid']),$lang['register_password_reset'],$body))
				return 1;
			else
				return 2;			
		}else{
			return 0;			
		}
	}

	public function sendNewPassword($key){
		$lang = $this->cunity->getLang();
		$tmp = explode('-', $key);
		$stamp = $tmp[0].$tmp[2];
		$mkey = $tmp[1];
		$userid = $tmp[3];

		$res = $this->cunity->getDb()->query("SELECT mail FROM ".$this->cunity->getConfig("db_prefix")."users WHERE userid = '".mysql_escape_string($userid)."' LIMIT 1");
		$data = mysql_fetch_assoc($res);
		$now = time();
		if($stamp >= ($now-172800) && $stamp <= $now){ // 48h
			if(mysql_num_rows($res) == 1 && $mkey == substr(sha1($data['mail']), 0, 7)) {
				// generate new pw
				$new_pw = '';
				$range = '0123456789ABCDEFGHJKLMNPQRSTUVWXYZ';
				$l = strlen($range)-1;
				for($i = 0; $i <= 6; $i++) {
					$new_pw .= $range[rand(0, $l)];
				}

				$res = $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."users SET password = '".mysql_real_escape_string(sha1($new_pw))."' WHERE userid = '".mysql_escape_string($userid)."' LIMIT 1");

				if($res){
					$body = '<h3>'.$lang['verify_password_reset'].'</h3>';
					$body .= $lang['verify_login_now'].'<br />';
					$body .= $lang['verify_user_name'].": ".getUserName($userid).'<br />';
					$body .= $lang['verify_new_password'].": ".$new_pw.'<br />';
					$body .= $lang['verify_password_change'].'<br />';					

					if(!$this->cunity->getNotifier()->sendNotification($data['mail'],getUserName($userid),$lang['verify_new_password'],$body))
						$replace = newCunityError($lang['verify_email_error']);
					else
						$replace = newCunitySuccess($lang['verify_password_reset2']);
				}else{
					$replace = newCunityError('Database-Error');
				}
			}else{
				$replace = newCunityError($lang['verify_key_invalid']);
			}
		}else{
			$replace = newCunityError($lang['verify_key_over'].'<p style="font-size:11px;">'.$lang['verify_key_time'].'</p>');
		}
		return $replace;	
	}

	public function logout(){		
		emptyTempFilesFolder();	
		$_SESSION = array();
		if(ini_get('session.use_cookies')){
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"]);
		}
		session_destroy();	
		header('location: index.php');
		exit();
	}
}
?>