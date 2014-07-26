<?php
/*
########################################################################################
## CUNITY(R) V1.0beta - An open source social network / "your private social network" ##
########################################################################################
##  Copyright (C) 2011 Smart In Media GmbH & Co. KG                                   ##
## CUNITY(R) is a registered trademark of Dr. Martin R. Weihrauch                     ##
##  http://www.cunity.net                                                             ##
##                                                                                    ##
########################################################################################

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or any later version.

1. YOU MUST NOT CHANGE THE LICENSE FOR THE SOFTWARE OR ANY PARTS HEREOF! IT MUST REMAIN AGPL.
2. YOU MUST NOT REMOVE THIS COPYRIGHT NOTES FROM ANY PARTS OF THIS SOFTWARE!
3. NOTE THAT THIS SOFTWARE CONTAINS THIRD-PARTY-SOLUTIONS THAT MAY EVENTUALLY NOT FALL UNDER (A)GPL!
4. PLEASE READ THE LICENSE OF THE CUNITY SOFTWARE CAREFULLY!

	You should have received a copy of the GNU Affero General Public License
    along with this program (under the folder LICENSE).
	If not, see <http://www.gnu.org/licenses/>.

   If your software can interact with users remotely through a computer network,
   you have to make sure that it provides a way for users to get its source.
   For example, if your program is a web application, its interface could display
   a "Source" link that leads users to an archive of the code. There are many ways
   you could offer source, and different solutions will be better for different programs;
   see section 13 of the GNU Affero General Public License for the specific requirements. 
   
   #####################################################################################
   */
   
function newCunityError($msg,$display=true){
    if(!$display)
        return '<div class="message_red" style="display: none;">'.$msg.'</div>';
    return '<div class="message_red">'.$msg.'</div>';
}

function newCunitySuccess($msg,$display=true){
    if(!$display)
        return '<div class="message_red" style="display: none;">'.$msg.'</div>';
    return '<div class="message_green">'.$msg.'</div>';
}

function getSex($userid){
    global $cunity;
    $res = $cunity->getDb()->query("SELECT title FROM ".$cunity->getConfig("db_prefix")."users_details WHERE userid = '".mysql_real_escape_string($userid)."'") or die (mysql_error());
    $d = mysql_fetch_assoc($res);
    return $d['title'];
}

function getAvatarPath($userid,$remote=false,$cunityId=0) {

    if(file_exists($_SESSION['cunity_trunk_folder']."/files/_avatars/".  getUserHash($userid).".jpg"))
        return "./files/_avatars/".  getUserHash($userid).".jpg";
    else
        return 'style/'.$_SESSION['style'].'/img/no_avatar.jpg';
}

function getProfileImagesAlbumId($userid)
{
    global $cunity;
    $res = $cunity->getDb()->query_assoc("SELECT album_id FROM ".$cunity->getConfig("db_prefix")."galleries_albums WHERE user_id = '".mysql_real_escape_string($userid)."' AND name = '-cunity-profile-images-'");
    if(isset($res['album_id']))
        return $res['album_id'];
    else
        return false;
}

function showDate($format, $timestring,$isTimestamp=false){
    global $_SESSION;
    if($format!='time'&&$format!='date'&&$format!='date_time')
        return false;

    if(!$isTimestamp){
        $datestring = strtotime($timestring);
        if(!$datestring)
            $datestring = time();
        if($datestring==0)
            $datestring = time();
    }else{
        $datestring=$timestring;
    }
    if(!date($_SESSION['date']['php'][$format], $datestring))
        return date($_SESSION['date']['php']['date_time']);
    else
        return date($_SESSION['date']['php'][$format], $datestring);
}

function profile_updated(){
    global $cunity;
    if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users_details SET updated = NOW() WHERE userid = '".mysql_real_escape_string($_SESSION['userid'])."'")){        
        if((time() - $_SESSION['profile_updated']) <= 3600){
            if($_SESSION['profile_updated_id'] = insertPinboard($_SESSION['userid'], 0, '','profile_update','main')){
                $_SESSION['profile_updated'] = time();
                return true;
            }
        }else{
        	updatePinboardEntryTime($_SESSION['profile_updated_id']);
            return true;
        }                 
    }    
}

function image_updated()
{
    global $cunity;
    if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users_details SET updated = NOW() WHERE userid = '".mysql_real_escape_string($_SESSION['userid'])."'")){
        if((time() - $_SESSION['image_updated']) <= 3600){
            if($_SESSION['image_updated_id'] = insertPinboard($_SESSION['userid'],0,'','image_update','main'))            
            {
                $_SESSION['image_updated'] = time();
                return true;
            }
        }else{
        	updatePinboardEntryTime($_SESSION['image_updated_id']);
            return true;
        }
    }
}

function file_getContents($url, $timeout=60) {
    if((bool)ini_get('allow_url_fopen')==false)
    {
        // URL zerlegen
        $parsedurl = @parse_url($url);
        // Host ermitteln, ungültigen Aufruf abfangen
        if (empty($parsedurl['host']))
            return false;
        $host = $parsedurl['host'];
        // Pfadangabe ermitteln
        $documentpath = empty($parsedurl['path']) ? '/' : $documentpath = $parsedurl['path'];
        // Parameter ermitteln
        if (!empty($parsedurl['query']))
            $documentpath .= '?'.$parsedurl['query'];
        // Port ermitteln
        $port = empty($parsedurl['port']) ? 80 : $port = $parsedurl['port'];

        // Socket öffnen
        $fp = @fsockopen ($host, $port, $errno, $errstr, $timeout);
        if (!$fp)
            return false;

        // Request senden
        fputs ($fp, "GET {$documentpath} HTTP/1.0\r\nHost: {$host}\r\n\r\n");

        // Header auslesen
        $header = '';
        do {
            $line = chop(fgets($fp));
            $header .= $line."\n";
        } while (!empty($line) and !feof($fp));
        // Daten auslesen
        $result = '';
        while (!feof($fp)) {
            $result .= fgets($fp);
        }
        // Socket schliessen
        fclose($fp);

        // Header auswerten
        preg_match('~^HTTP/1\.\d (?P<status>\d+)~', $header, $matches);
        $status = $matches['status'];
        if ($status == 200) { // OK
            return $result;
        } elseif ($status == 204 or $status == 304) { // No Content | Not modified
            return '';
        } elseif (in_array($status, Array(300, 301,302,303,307))) {
            preg_match('~Location: (?P<location>\S+)~', $header, $match);
        $result = file_getContents($match['location'], $timeout);
        } elseif ($status >= 400) { // Any error
            return false;
        }

        // Ergebnis zurückgeben
        return $result;
    }
    else
        return file_get_contents($url);
}

function socketCopy($remoteFile,$destinationfile){
    if(!$file=file_getContents($remoteFile))
        return false;
    if(file_put_contents($destinationfile,$file)!==false)
        return true;
    else
        return false;
}

function getUserName($u,$remote=false,$cunityId=0)
{
    if(($u == $_SESSION['nickname'] || $u == $_SESSION['userid'])&&!$remote&&$cunityId==0){
        return $_SESSION['username'];
    }else{    	
        global $cunity;
        if(!$remote)
        	$res = $cunity->getDb()->query("SELECT username FROM ".$cunity->getConfig("db_prefix")."users WHERE nickname = '".mysql_real_escape_string($u)."' OR userid = '".mysql_real_escape_string($u)."'");
        elseif($remote&&$cunityId>0){        	
        	$res = $cunity->getDb()->query("SELECT username FROM ".$cunity->getConfig("db_prefix")."connected_users WHERE localid = ".(int)$u." AND cunityId = ".$cunityId."") or die(mysql_error());
        }
        	
        	
        $data = mysql_fetch_assoc($res);
        return $data['username'];
    }    
}

function getNickname($u)
{
    if($u == $_SESSION['userid']){
        return $_SESSION['nickname'];
    }else{
        global $cunity;
        $res = $cunity->getDb()->query("SELECT nickname FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = '".mysql_real_escape_string($u)."'");
        $data = mysql_fetch_assoc($res);
        return $data['nickname'];
    }    
}

function getXMLValueViaAttribute($xml,$elName,$attr,$key){    
    foreach($xml->children() AS $a){
        if($a->getName()==$elName&&$a[$attr]==$key){            
            return (String)$a;
        }                        
    }
    return false;    
}

function getEventName($e)
{    
    global $cunity;
    $res = $cunity->getDb()->query("SELECT name FROM ".$cunity->getConfig("db_prefix")."events WHERE id = '".mysql_real_escape_string($e)."'");
    $data = mysql_fetch_assoc($res);
    return $data['name'];    
}

function getEventHash($e)
{
    global $cunity;
    $res = $cunity->getDb()->query("SELECT eventid FROM ".$cunity->getConfig("db_prefix")."events WHERE id = '".mysql_real_escape_string($e)."'");
    $data = mysql_fetch_assoc($res);
    return $data['eventid'];
}

function getEventId($e)
{
    global $cunity;
    $res = $cunity->getDb()->query("SELECT id FROM ".$cunity->getConfig("db_prefix")."events WHERE eventid = '".mysql_real_escape_string($e)."'");
    $data = mysql_fetch_assoc($res);
    return $data['id'];
}

function createUniqueEventId($string)
{
    global $cunity;
    $string = sha1($string);
    $res = $cunity->getDb()->query("SELECT COUNT(*) FROM ".$cunity->getConfig("db_prefix")."events WHERE eventid = '".$string."'");
    $data = mysql_fetch_assoc($res);
    if($data['COUNT(*)']==0)
        return $string;
    else{
        $string += time();
        return createUniqueEventId($string);
    }
}

function page_not_found()
{
    global $tplEngine,$lang;
    $tplEngine->Template("page_not_found");
        $tplEngine->Assign('page_not_found',$lang['page_not_found']);        
}

// Creates / Updates the entries of the _online table and results the list of users
function who_is_online($limit=10) {
	global $cunity,$lang;	
	if($cunity->getSaver()->login()){
        $online = array();
    	$rowlist ="";
    	$return = array();
    	
    	if(!isset($_SESSION['chatonline']))
    	    $_SESSION['chatonline']=1;
    
        $cunity->getDb()->query('DELETE FROM '.$cunity->getConfig("db_prefix").'online WHERE timestamp < TIMESTAMPADD(MINUTE, -5, CURRENT_TIMESTAMP)') or die(mysql_error());
    
        $res = $cunity->getDb()->query('SELECT * FROM '.$cunity->getConfig("db_prefix").'online WHERE online = 1 ORDER BY nickname ASC');
    	if(mysql_num_rows($res) >= 1){
    		while($data = mysql_fetch_assoc($res)){
                if(isFriend($data['userid'])&&$cunity->getSetting('chat_with')=="friends")
                    $online[$data['userid']] = $data['nickname'];
                elseif($cunity->getSetting('chat_with')=="all")
                    $online[$data['userid']] = $data['nickname'];
    		}
    	}
    	$r = $cunity->getDb()->query("SELECT COUNT(*) FROM ".$cunity->getConfig("db_prefix")."online WHERE userid = '".$_SESSION['userid']."'");
    	$d = mysql_fetch_assoc($r);
    	if($d['COUNT(*)'] == 0)
            $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."online (userid, nickname, ip, timestamp, online) VALUES ('".intval($_SESSION['userid'])."', '".mysql_real_escape_string($_SESSION['nickname'])."', '".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."', CURRENT_TIMESTAMP, '".$_SESSION['chatonline']."')") or die(mysql_error());
    	else
    	    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."online SET timestamp = CUURENT_TIMESTAMP WHERE userid = '".intval($_SESSION['userid'])."'");
    
    
    	$count = count($online);
    	if($count > 0){
    		$onlinelist = '';
    		$c=0;
    		foreach($online as $key => $value){
    			if($key!=$_SESSION['userid']&&$c<$limit){
    			    $userhash = getUserHash($key);
    			    $username = getUserName($key);
                    $img = getAvatarPath($key);
        				    				
                    $rowlist .= '<div class="friendsonline_line"><a href="javascript: chatWith(\'0-'.$key.'\',\''.$username.'\');" style="display: inline-block; float: left;">'.getSmallAvatar($key, 40).'</a><div style="display: inline-block; float: left;"><a href="javascript: chatWith(\'0-'.$key.'\',\''.$username.'\');" style="font-weight: bold; display: block;">'.$username.'</a></div><div class="clear"></div></div>';
                    $c++;                    		
    			}    			
    		}
    	}
    	$return['count'] = $c;
    	$return['list'] = $rowlist;
    	return $return;
    }    
}

function is_online($userid)
{
    global $cunity;
    $res = $cunity->getDb()->query("SELECT COUNT(*) FROM ".$cunity->getConfig("db_prefix")."online WHERE userid = '".$userid."' AND online = 1 LIMIT 1");
    $o = mysql_fetch_assoc($res);
    if($o['COUNT(*)'] == 1)
        return true;
    else
        return false;
}


function who_is_online_Count() {
	global $cunity;
    if($cunity->getSaver()->login())
    {
        global $cunity;
    	$count = 0;
    	$res = $cunity->getDb()->query('SELECT userid FROM '.$cunity->getConfig("db_prefix").'online WHERE userid != \''.$_SESSION['userid'].'\' AND online = 1');
    	while($count_friends = mysql_fetch_assoc($res)){    	        	    
            if(isFriend($count_friends['userid'])&&$cunity->getSetting('chat_with')=="friends")
                $count++;
            elseif($cunity->getSetting('chat_with')=="all")
                $count++;
        }
    
    	return $count;
    }	
    else
        return 0;

}

function changeChatStatus($userid, $status)
{
    global $cunity;
    if($status==1||$status==0)
    {
        if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."online SET online = '".mysql_real_escape_string($status)."' WHERE userid = '".intval($_SESSION['userid'])."'"))
        {
            $_SESSION['chatonline']=$status;
            return true;
        }
        
    }
    return false;        
}

function clearChatHistory()
{
	global $cunity;
	$cunity->getDb()->query('Delete FROM '.$cunity->getConfig("db_prefix").'chat where '.$cunity->getConfig("db_prefix").'chat.from='.$_SESSION['nickname'].' OR '.$cunity->getConfig("db_prefix").'chat.to='.$_SESSION['nickname']);
	
	
}



function emptyTempFilesFolder()
{
	$dirname='files/_tempfiles/user'.$_SESSION['userid'];
	recursive_remove_directory($dirname,TRUE);
}

function getmail($userid)
{
    global $cunity;
    $res = $cunity->getDb()->query("SELECT mail FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = '".$userid."'");
    $u = mysql_fetch_assoc($res);
    return $u['mail'];
}

function getSmallAvatar($userid, $size = 40,$remote = false, $cunityId=0){                	
	global $cunity;
    return '<img src="'.getAvatarPath($userid,$remote,$cunityId).'" class="left_comment" style="height: '.$size.'px; width: '.$size.'px;"/>';    
}

function getUserHash($userid,$remote=false,$cunityId=0){
	global $cunity;
	if($remote&&$cunityId>0){
		$res = $cunity->getDb()->query("SELECT userhash FROM ".$cunity->getConfig("db_prefix")."connected_users WHERE localid = '".mysql_real_escape_string($userid)."' AND cunityId = '".mysql_real_escape_string($cunityId)."'");
        $data = mysql_fetch_assoc($res);
        return $data['userhash'];
	}elseif($userid != $_SESSION['userid']){    	           
        $res = $cunity->getDb()->query("SELECT userhash FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = '".mysql_real_escape_string($userid)."'");
        $data = mysql_fetch_assoc($res);
        return $data['userhash'];       
    }elseif(!$remote){
        return $_SESSION['userhash'];
    }
}

function getUserId($userhash,$remote=false,$cunityId=0){        
    global $cunity;
	if($remote&&$cunityId>0){
		$res = $cunity->getDb()->query("SELECT localid FROM ".$cunity->getConfig("db_prefix")."connected_users WHERE userhash = '".mysql_real_escape_string($userhash)."' AND cunityId = '".mysql_real_escape_string($cunityId)."'");
        $data = mysql_fetch_assoc($res);
        return $data['localid'];
	}elseif($userhash != $_SESSION['userhash']){    	           
        $res = $cunity->getDb()->query("SELECT userid FROM ".$cunity->getConfig("db_prefix")."users WHERE userhash = '".mysql_real_escape_string($userhash)."'");
        $data = mysql_fetch_assoc($res);
        return $data['userid'];       
    }elseif(!$remote&&$cunityId==0){
        return $_SESSION['userid'];
    } 
}

function recursive_remove_directory($directory, $empty=FALSE)
{
	// if the path has a slash at the end we remove it here
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	// if the path is not valid or is not a directory ...
	if(!file_exists($directory) || !is_dir($directory))
	{
		// ... we return false and exit the function
		return FALSE;
     
	// ... if the path is not readable
	}elseif(!is_readable($directory))
	{
		// ... we return false and exit the function
		return FALSE;

	// ... else if the path is readable
	}else{

		// we open the directory
		$handle = opendir($directory);

		// and scan through the items inside
		while (FALSE !== ($item = readdir($handle)))
		{
			// if the filepointer is not the current directory
			// or the parent directory
			if($item != '.' && $item != '..')
			{
				// we build the new path to delete
				$path = $directory.'/'.$item;

				// if the new path is a directory
				if(is_dir($path)) 
				{
					// we call this function with the new path
					recursive_remove_directory($path);

				// if the new path is a file
				}else{
					// we remove the file
					unlink($path);
				}
			}
		}
		// close the directory
		closedir($handle);

		// if the option to empty is not set to true
		if($empty == FALSE)
		{
			// try to delete the now empty directory
			if(!rmdir($directory))
			{
				// return false if not possible
				return FALSE;
			}
		}
		// return success
		return TRUE;
	}
}

function getAllUsers()
{
    global $cunity;
    $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."users WHERE groupid = 1 OR groupid = 2 OR groupid = 3 AND userid != '".$_SESSION['userid']."'");
    while($data = mysql_fetch_assoc($res))
    {
        $users[] = $data['userid'];
    }
    return $users;
}

function getFriendList($userid, $status,$cunityId=0) {
    global $cunity;
    if($cunity->getSetting('friendstype')=='members')
        return getAllUsers();
               
	$res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = '".$userid."' OR receiver = '".$userid."') AND status = '".$status."' AND cunityId = '".$cunityId."'") or die(mysql_error());       
    $myFriends = array();
    while($data = mysql_fetch_assoc($res)){
        if($data['sender'] == $userid)
        {
            $myFriends[] = $data['receiver'];
        }
        elseif($data['receiver'] == $userid)
        {
            $myFriends[] = $data['sender'];
        }
    }
    return $myFriends;
}

function getReceivedFriendList($userid,$cunityId=0) {    
    global $cunity;
    if($cunity->getSetting('friendstype')=='members')
        return false;
    $q = "SELECT * FROM ".$cunity->getConfig("db_prefix")."friendships WHERE receiver = ".$_SESSION['userid']." AND status = 0 AND (remote = 'sender' OR remote IS NULL)";
    $res = $cunity->getDb()->query($q);
    $myFriends = array();
    while($data = mysql_fetch_assoc($res))
            $myFriends[] = $data['sender'];
    return $myFriends;
}

function getSendFriendList($userid,$cunityId=0) {
    global $cunity;
    if($cunity->getSetting('friendstype')=='members')
        return false;
    $res = $cunity->getDb()->query("SELECT receiver FROM ".$cunity->getConfig("db_prefix")."friendships WHERE sender = '".$userid."' AND (remote = NULL OR remote = 'receiver') AND status = 0 AND cunityId = '".$cunityId."'");
    $myFriends = array();
    while($data = mysql_fetch_assoc($res))
    {
            $myFriends[] = $data['receiver'];
    }
    return $myFriends;
}

function getPrivacy($userid)
{
    global $cunity;
    $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."privacy WHERE userid = '".$userid."'");
    if(mysql_num_rows($res) > 0)
        $p = mysql_fetch_assoc($res);
    
    $res = $cunity->getDb()->query("SHOW COLUMNS FROM ".$cunity->getConfig("db_prefix")."privacy");
    while($col=mysql_Fetch_assoc($res))
    	$privacy[$col['Field']] = $p[$col['Field']];
    
    return $privacy;
}

function checkFriendOfFriend($user1, $user2)
{      
	global $cunity;
    if($cunity->getSetting('friendstype')=='members')
        return true;
    $ul1 = getFriendList($user1, 1);
    $ul2 = getFriendList($user2, 1);
        
    for($i = 0; $i < count($ul1); $i++)
    {
        if(in_array($ul1[$i], $ul2))
        {
            return true;
        }        
    }        
    return false;
}

function checkPrivacy($checkUserId, $ownUserId, $priv)
{
    $privacy = getPrivacy($checkUserId);    
    //userid = 0 is the OpenCunity user!        
    if(
        ($privacy[$priv] == 2 && (checkFriendOfFriend($checkUserId, $ownUserId) || isFriend($checkUserId))) 
        || 
        $privacy[$priv] == 1 && $ownUserId!=0 
        || 
        $privacy[$priv] == 0 
        ||                  
        ($privacy[$priv] == 3 && isFriend($checkUserId))
        ||
        $checkUserId == $ownUserId
      )
    {        
        return true;
    }
    else
    {
        return false;
    }
}

function isFriend($userid,$cunityId=0)
{   
    global $cunity;
    if(($cunity->getSetting('friendstype')=='members'||$_SESSION['userid']==$userid)&&$cunityId==0)
        return true;         
    if($cunityId>0){
        $res = $cunity->getDb()->query("SELECT COUNT(*) AS c FROM ".$cunity->getConfig("db_prefix")."friendships WHERE ((sender = '".$_SESSION['userid']."' AND receiver = '".$userid."') OR (sender = '".$userid."' AND receiver = '".$_SESSION['userid']."')) AND cunityId = '".$cunityId."' AND status = 1");
    }else{
        $res = $cunity->getDb()->query("SELECT COUNT(*) AS c FROM ".$cunity->getConfig("db_prefix")."friendships WHERE ((sender = '".$_SESSION['userid']."' AND receiver = '".$userid."') OR (sender = '".$userid."' AND receiver = '".$_SESSION['userid']."')) AND status = 1");
    }
    $data = mysql_fetch_assoc($res);
    return (bool)$data['c'];        
}

function checkSendNotification($type, $userid)
{
    global $cunity;
    $res = $cunity->getDb()->query("SELECT '".$type."' FROM ".$cunity->getConfig("db_prefix")."notifications_settings WHERE userid = '".$userid."'");
    $data = mysql_fetch_assoc($res);    
    return $data[$type];
}

function getCunityName($cunityId){
    global $cunity;
    $res = $cunity->getDb()->query("SELECT cunityname FROM ".$cunity->getConfig("db_prefix")."connected_cunities WHERE cunityId = '".$cunityId."'");
    $data = mysql_fetch_assoc($res);
    return $data['cunityname'];
}


function check_date($date,$format,$sep)
{    
    
    $pos1    = strpos($format, 'd');
    $pos2    = strpos($format, 'm');
    $pos3    = strpos($format, 'Y'); 
    
    $check    = explode($sep,$date);
    
    return checkdate($check[$pos2],$check[$pos1],$check[$pos3]);

}

//Pinboard



function insertPinboard($userid, $pinboard_id, $message, $type, $receiver)
{
    global $cunity;    
    $cunity->getDb()->insert($cunity->getConfig("db_prefix").'pinboard',
			'`userid`,`pinboard_id`,`message`,`time`,`type`,`receiver`',
			"
            '".$userid."',
			'".$pinboard_id."',
			'".$message."',
			NOW(),
			'".$type."',
			'".$receiver."'
    ");
    return mysql_insert_id();
}
function updatePinboardEntryTime($id){
	global $cunity;
	return $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."pinboard SET `time` = NOW() WHERE status_id = ".$id."");
}

function loadPinboardEntry($id)
{
    global $cunity;
    return "
        SELECT
            *
        FROM
            ".$cunity->getConfig("db_prefix")."pinboard
        WHERE
            ".$cunity->getConfig("db_prefix")."pinboard.status_id = '".$id."'";
}

function loadMoreMainPinboard($sort,$userid,$statusCount)
{
    global $cunity;
    if($sort!=""&&$sort!="all")
    {
        $queryString="
        SELECT
            *
        FROM
            ".$cunity->getConfig("db_prefix")."pinboard
        WHERE
        (
            ".$cunity->getConfig("db_prefix")."pinboard.userid IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
        OR
            ".$cunity->getConfig("db_prefix")."pinboard.userid = '".$userid."'
        )
        AND
        (
        (
            (
                (
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
                OR
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '".$userid."'
                )
                AND
                    ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'friend'
            )
            OR
            (
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT event_id FROM ".$cunity->getConfig("db_prefix")."events_guests WHERE userid = '".$userid."' AND status = '3')
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'event'
            )
            OR
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '0'
        )
        )
        AND
            ".$cunity->getConfig("db_prefix")."pinboard.type = '".mysql_real_escape_string($sort)."'        
        ORDER BY
            ".$cunity->getConfig("db_prefix")."pinboard.time
        DESC
            LIMIT ".$statusCount.",10";
    }
    else
    {
        $queryString="
        SELECT
            *
        FROM
            ".$cunity->getConfig("db_prefix")."pinboard
        WHERE
        (
            ".$cunity->getConfig("db_prefix")."pinboard.userid IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
        OR
            ".$cunity->getConfig("db_prefix")."pinboard.userid = '".$userid."'
        )
        AND
        (
            (
                (
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
                OR
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '".$userid."'
                )
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'friend'
            )
            OR
            (
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT event_id FROM ".$cunity->getConfig("db_prefix")."events_guests WHERE userid = '".$userid."' AND status = '3' OR status = '2')
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'event'
            )
            OR
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '0'
        )
        ORDER BY
            ".$cunity->getConfig("db_prefix")."pinboard.time
        DESC
            LIMIT ".$statusCount.",10
         ";
    }	
	return $queryString;
}

function getRefreshedMainPinBoard($sort,$userid,$lastStatusId){
    global $cunity;
    if($sort!=""&&$sort!="all"){
        $queryString="
        SELECT
            *
        FROM
            ".$cunity->getConfig("db_prefix")."pinboard
        WHERE
        (
            ".$cunity->getConfig("db_prefix")."pinboard.userid IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
        OR
            ".$cunity->getConfig("db_prefix")."pinboard.userid = '".$userid."'
        )
        AND
        (
        (
            (
                (
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
                OR
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '".$userid."'
                )
                AND
                    ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'friend'
            )
            OR
            (
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT event_id FROM ".$cunity->getConfig("db_prefix")."events_guests WHERE userid = '".$userid."' AND status = '3')
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'event'
            )
            OR
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '0'
        )                   
        )
        AND
            ".$cunity->getConfig("db_prefix")."pinboard.type = '".mysql_real_escape_string($sort)."'
        AND
            ".$cunity->getConfig("db_prefix")."pinboard.status_id > ".(int)$lastStatusId."
        ORDER BY
            ".$cunity->getConfig("db_prefix")."pinboard.time
        DESC";
    }else{
        $queryString="
        SELECT
            *
        FROM
            ".$cunity->getConfig("db_prefix")."pinboard
        WHERE
        (
            ".$cunity->getConfig("db_prefix")."pinboard.userid IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
        OR
            ".$cunity->getConfig("db_prefix")."pinboard.userid = '".$userid."'
        )
        AND
        ((
            (
                (
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
                OR
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '".$userid."'
                )
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'friend'
            )
            OR
            (
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT event_id FROM ".$cunity->getConfig("db_prefix")."events_guests WHERE userid = '".$userid."' AND status = '3')
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'event'
            )
            OR
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '0'
        )
        )
        AND 
            ".$cunity->getConfig("db_prefix")."pinboard.status_id > ".(int)$lastStatusId."
        ORDER BY
            ".$cunity->getConfig("db_prefix")."pinboard.time
        DESC
         ";
    }       
    return $queryString; 
}

function loadSortMainPinBoard($sort,$userid)
{
    global $cunity;
    $queryString="
        SELECT
            *
        FROM
            ".$cunity->getConfig("db_prefix")."pinboard
        WHERE
        (
            ".$cunity->getConfig("db_prefix")."pinboard.userid IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
        OR
            ".$cunity->getConfig("db_prefix")."pinboard.userid = '".$userid."'
        )
        AND
        ((
            (
                (
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
                OR
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '".$userid."'
                )
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'friend'
            )
            OR
            (
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT event_id FROM ".$cunity->getConfig("db_prefix")."events_guests WHERE userid = '".$userid."' AND status = '3')
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'event'
            )
            OR
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '0'
        )
        )
        AND
            ".$cunity->getConfig("db_prefix")."pinboard.type = '".mysql_real_escape_string($sort)."'
        ORDER BY
            ".$cunity->getConfig("db_prefix")."pinboard.time
        DESC
            LIMIT 0,10
         ";       
    return $queryString;
}

function LoadMorePagePinBoard($id, $receiver,$statusCount)
{
    global $cunity;
    $query = "
        SELECT
            *
        FROM
            ".$cunity->getConfig("db_prefix")."pinboard
        WHERE
        (
            (
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = ".$id."
            AND
            	(".$cunity->getConfig("db_prefix")."pinboard.remote = 'user' OR ".$cunity->getConfig("db_prefix")."pinboard.remote IS NULL || ".$cunity->getConfig("db_prefix")."pinboard.remote = '')
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = '".$receiver."'
            )            
        OR
            (
                ".$cunity->getConfig("db_prefix")."pinboard.userid = ".$id."
            AND
            	(".$cunity->getConfig("db_prefix")."pinboard.remote = 'pinboard' OR ".$cunity->getConfig("db_prefix")."pinboard.remote IS NULL || ".$cunity->getConfig("db_prefix")."pinboard.remote = '')
            )
        )
        ORDER BY
            ".$cunity->getConfig("db_prefix")."pinboard.time
        DESC
            LIMIT ".$statusCount.",10
        ";    
    return $query;
}

function getRefreshedPagePinBoard($id, $receiver,$lastStatusId)
{
    global $cunity;
    $query = "
        SELECT
            *
        FROM
            ".$cunity->getConfig("db_prefix")."pinboard
        WHERE
        (
            (
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = ".$id."
            AND
            	(".$cunity->getConfig("db_prefix")."pinboard.remote = 'user' OR ".$cunity->getConfig("db_prefix")."pinboard.remote IS NULL || ".$cunity->getConfig("db_prefix")."pinboard.remote = '')
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = '".$receiver."'
            )            
        OR
            (
                ".$cunity->getConfig("db_prefix")."pinboard.userid = ".$id."
            AND
            	(".$cunity->getConfig("db_prefix")."pinboard.remote = 'pinboard' OR ".$cunity->getConfig("db_prefix")."pinboard.remote IS NULL || ".$cunity->getConfig("db_prefix")."pinboard.remote = '')
            )
        )
        AND
            ".$cunity->getConfig("db_prefix")."pinboard.status_id > ".$lastStatusId."
        ORDER BY
            ".$cunity->getConfig("db_prefix")."pinboard.time
        DESC
            LIMIT 0,10
        ";    
    return $query;
}

function getPagePinBoard($id, $receiver)
{
    global $cunity;
    $query = "
        SELECT
            *
        FROM
            ".$cunity->getConfig("db_prefix")."pinboard
        WHERE
        (
            (
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '".$id."'
            AND
            	(".$cunity->getConfig("db_prefix")."pinboard.remote = 'user' OR ".$cunity->getConfig("db_prefix")."pinboard.remote IS NULL || ".$cunity->getConfig("db_prefix")."pinboard.remote = '')
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = '".$receiver."'
            )            
        OR
            (
                ".$cunity->getConfig("db_prefix")."pinboard.userid = '".$id."'
            AND
            	(".$cunity->getConfig("db_prefix")."pinboard.remote = 'pinboard' OR ".$cunity->getConfig("db_prefix")."pinboard.remote IS NULL || ".$cunity->getConfig("db_prefix")."pinboard.remote = '')
            )
        )
        ORDER BY
            ".$cunity->getConfig("db_prefix")."pinboard.time
        DESC        
            LIMIT 0,10
        ";    
    return $query;
}

function getMainPinBoard($userid)
{
    global $cunity;
    $queryString="
        SELECT
            *
        FROM
            ".$cunity->getConfig("db_prefix")."pinboard
        WHERE
        (
            ".$cunity->getConfig("db_prefix")."pinboard.userid IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
        OR
            ".$cunity->getConfig("db_prefix")."pinboard.userid = '".$userid."'
        )
        AND
        (
        (
            (
                (
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT (CASE WHEN sender != ".$userid." THEN sender WHEN receiver != ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1)
                OR
                    ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '".$userid."'
                )
                AND
                    ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'friend'
            )
            OR
            (
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id IN (SELECT event_id FROM ".$cunity->getConfig("db_prefix")."events_guests WHERE userid = '".$userid."' AND status = '3')
            AND
                ".$cunity->getConfig("db_prefix")."pinboard.receiver = 'event'
            )
            OR
                ".$cunity->getConfig("db_prefix")."pinboard.pinboard_id = '0'
        )
        )
        ORDER BY
            ".$cunity->getConfig("db_prefix")."pinboard.time
        DESC";
   	return $queryString; 
}

function createYoutubeObject($message,$id) {
    global $cunity;
    $statusMessage = "";
    $data = json_decode($message,true);
    if(strlen($data['description'])>200)
		          $data['description'] = substr($data['description'],0,200).'<a href="javascript: more_descr(\''.$id.'\')" id="more_descr_'.$id.'"> '.$lang['pinboard_show_more'].'</a><span id="realmoredescr_'.$row['status_id'].'" style="display: none;">'.substr($data['description'],200).'<a href="javascript: less_descr(\''.$id.'\')" id="less_descr_'.$id.'"> '.$lang['pinboard_show_less'].'</a></span>';		
	
    if(!isset($data['image']))
	    $data['image'] = 'http://i4.ytimg.com/vi/'.$data['v'].'/hqdefault.jpg';

    $statusMessage = '<p class="video_message">'.$data['message'].'</p>';
	$embed_video='<iframe title="YouTube video player" width="420" height="360" src="http://www.youtube.com/embed/'.$data['v'].'" frameborder="0" allowfullscreen id="video_'.$data['v'].'" style="display: none;"></iframe>';
    $statusMessage .= $embed_video.'<img src="'.$data['image'].'" class="video_status_img" id="'.$data['v'].'" title="'.$lang['pinboard_play'].'"/>
                        <div class="video_status_description">
                            <a href="http://www.youtube.com/watch?v='.$data['v'].'" target="_blank"><b>'.$data['title'].'</b></a><br />
                            <p><i>'.$data['description'].'</i></p>
                        </div>';
    
    return $statusMessage;
}

function addComment($ressource_id, $ressource_name, $userid, $message,$cunityId,$remoteField)
{
    global $cunity;
    if($cunityId>0){
    	$cunity->getDb()->insert($cunity->getConfig("db_prefix").'comments',
				'`ressource_id`,`ressource_name`,`userid`,`comment`,`time`',
				"'".$ressource_id."',
				'".mysql_real_escape_string($ressource_name)."',
				'".$userid."',
				'".mysql_real_escape_string(rawurldecode($message))."',
				NOW(),'".$remoteField."',".(int)$cunityId."");
    }else{
    	$cunity->getDb()->insert($cunity->getConfig("db_prefix").'comments',
				'`ressource_id`,`ressource_name`,`userid`,`comment`,`time`',
				"'".$ressource_id."',
				'".mysql_real_escape_string($ressource_name)."',
				'".$userid."',
				'".mysql_real_escape_string(rawurldecode($message))."',
				NOW(),NULL,0");
    }    
	return mysql_insert_id();
}

function sortNewFriendsEntry($row)
{
    if($row['message'] == $pinboard_id)
    {
        $f1 = $row['message'];
        $f2 = $row['userid'];
    }
    elseif($row['userid'] == $pinboard_id)
    {
        $f1 = $row['userid'];
        $f2 = $row['message'];
    }
    elseif(isFriend($row['userid'])  && isFriend($row['message']))
    {
        $f1 = $row['message'];
        $f2 = $row['userid'];
    }
    elseif(isFriend($row['userid']))
    {
        $f1 = $row['userid'];
        $f2 = $row['message'];
    }
    elseif(isFriend($row['message']))
    {
        $f1 = $row['message'];
        $f2 = $row['userid'];
    }
    elseif($row['message'] == $_SESSION['userid'])
    {
        $f1 = $row['message'];
        $f2 = $row['userid'];
    }
    elseif($row['userid'] == $_SESSION['userid'])
    {
        $f1 = $row['userid'];
        $f2 = $row['message'];
    }
    if($f2 == $pinboard_id)
    {
        $f2 = $f1;
        $f1 = $pinboard_id;
    }
    return array($f1,$f2);
}

function like($ressource_id, $ressource_name, $dislike,$img=false)
{  
    global $cunity;      
    
}

function getLike($userid, $ressource_id, $ressource_name)
{
    //important!! this function return an integer value 0 or 1, and a boolean false
    //Use the === operator to compare the result!
    global $cunity;
    $res = $cunity->getDb()->query("SELECT dislike FROM ".$cunity->getConfig("db_prefix")."likes WHERE userid = '".$userid."' AND ressource_name = '".$ressource_name."' AND ressource_id = '".$ressource_id."' LIMIT 1");
    $data =mysql_fetch_assoc($res);
    if(mysql_num_rows($res)==0)
        return false;
    else
        return (int)$data['dislike'];    
}
function findUrl($u){
  $url = $u[0];
  $afterUrl = '';
  while(preg_match('#[[:punct:]]$#', $url, $found)){
    $chr = $found[0];
    if($chr==='.' || $chr===',' || $chr==='!' || $chr==='?' || $chr===':' || $chr===';' || $chr==='>' || $chr==='<'){

      $afterUrl = $chr.$afterUrl;
      $url = substr($url, 0, -1);
    }
    elseif($chr===')' && strpos($url, '(')!==false || $chr===']' && strpos($url, '[')!==false || $chr==='}' && strpos($url, '{')!==false)
      break;
    elseif($chr===')' || $chr===']' || $chr==='}'){
      $afterUrl = $chr.$afterUrl;
      $url = substr($url, 0, -1);
    }
    elseif($chr==='(' || $chr==='[' || $chr==='{'){
      $afterUrl = $chr.$afterUrl;
      $url = substr($url, 0, -1);
    }
    else
      break;
  }
  return '<a href="'.$url.'" title="'.str_replace('http://', '', $url).'">'.$url.'</a>'.$afterUrl;
}
?>