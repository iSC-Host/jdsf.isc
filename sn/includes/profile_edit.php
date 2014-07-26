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
   
$messages_html_rows = "";

   
if(!isset($_GET['do']))
    $_GET['do'] = 'general';

$userhash = $_SESSION['userhash'];
$userid = $_SESSION['userid'];

   
if($_GET['do'] == 'img'){
	require_once 'Cunity_Galleries.class.php';
	$galleries = new Cunity_Galleries($cunity);
	//DELETE
    if(isset($_POST['delete_image'])){
        $res=$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET profile_image = 0 WHERE userid = ".$userid);        
    }
        
	if($data['x1']==0&&$data['x2']==0&&$data['y1']==0&&$data['y2']==0&&$data['w']==0&&$data['h']==0){
        $x1 = 0;
        $x2 = 120;
        $y1 = 0;
        $y2 = 120;
        $w = 120;
        $h = 120;
    }else{
        $x1 = $data['x1'];
        $x2 = $data['x2'];
        $y1 = $data['y1'];
        $y2 = $data['y2'];
        $w  = $data['w'];
        $h  = $data['h'];
    }

    if(isset($_FILES['new_profile_pic']) && $_FILES['new_profile_pic']['error'] == 0){
    	$albumId=$galleries->getProfileImagesAlbumId();    	
    	if($albumId==false)
    		$albumId=$galleries->newAlbum(array("album_name"=>"-cunity-profile-images-","album_descr"=>"-cunity-profile-images-","album_privacy"=>1));
    	$request=array("id"=>$albumId);
    	$_FILES['fu'] = $_FILES['new_profile_pic'];
    	unset($_FILES['new_profile_pic']);
    	$result=$galleries->uploadSingleFile($request,$_FILES);
    	if(isset($result['id'])&&!isset($result['status'])){
    		$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET profile_image =".$result['id']." WHERE userid = ".$userid);
    		$galleries->updateAlbumData($albumId,"main_image",$result['file']);
    	}
    	image_updated();
        header("Location: profile.php?c=edit&do=img");                
        exit;
    }

    if(isset($_POST['x']) && !isset($_POST['only_pic'])){
        $x1 = $_POST['x'];
        $x2 = $_POST['x2'];
        $y1 = $_POST['y'];
        $y2 = $_POST['y2'];
        $h = $_POST['h'];
        $w = $_POST['w'];
        if($x1 == 0 && $x2 == 0 && $y1 == 0 && $y2 == 0 && $h == 0 && $w == 0){
            $x1 = 0;
            $x2 = 120;
            $y1 = 0;
            $y2 = 120;
            $w = 120;
            $h = 120;
        }
        $src = $profile->getProfileImage();
        $size=getimagesize($src);
        
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET x1= '".$x1."', x2 = '".$x2."', y1 = '".$y1."', y2 = '".$y2."', w = '".$w."', h = '".$h."' WHERE userid = '".$userid."'");        

        $targ_w = $targ_h = 120;
        $jpeg_quality = 80;
        
        $ratio=($size[0]/$size[1]);
        
        $displayedWidth=200;
        $displayedHeight=(200/$ratio);
        
        $x1=$x1*($size[0]/$displayedWidth);
        $y1=$y1*($size[1]/$displayedHeight);
        $w=$w*($size[0]/$displayedWidth);
        $h=$h*($size[1]/$displayedHeight);
                
        $img_r = imagecreatefromjpeg($src);
        $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

        imagecopyresampled($dst_r,$img_r,0,0,$x1,$y1,$targ_w,$targ_h,$w,$h);

        if(!file_exists('./files/_avatars/'))
            mkdir('./files/_avatars/');
        imagejpeg($dst_r,'./files/_avatars/'.$userhash.'.jpg',$jpeg_quality);

        image_updated();
        header("Location: profile.php?c=edit&do=img");        
        exit;
    }
    $replace['X1'] = $x1;
    $replace['X2'] = $x2;
    $replace['Y1'] = $y1;
    $replace['Y2'] = $y2;
    $replace['H'] = $h;
    $replace['W'] = $w;
}elseif($_GET['do'] == 'notifications'){
    if(isset($_GET['extra']) && $_GET['extra'] == 'refresh'){        
        $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."notifications_settings WHERE userid = '".$userid."'");
        $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."notifications_settings (userid) VALUES ('".$userid."')");
        foreach($_POST AS $key => $d)
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."notifications_settings SET ".$key." = ".$d." WHERE userid = '".$userid."'");                  
    }
	$res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."notifications_settings WHERE userid = '".$userid."' LIMIT 1");
    $p = mysql_fetch_assoc($res);
    if(mysql_num_rows($res) == 1){
   		foreach($p AS $key => $n)
        	if($p[$key] == 1)
                $replace[strtoupper($key)] = 'checked="checked"';                   
    }    
}
elseif($_GET['do'] == 'blocked')
{    
	$q = "SELECT * FROM ".$cunity->getConfig("db_prefix")."friendships WHERE (blocker = 'sender' AND sender = '".$_SESSION['userid']."') OR (blocker = 'receiver' AND receiver = '".$_SESSION['userid']."') AND status = 2 ORDER BY time DESC";
    $res = $cunity->getDb()->query($q);    
    $blocked="";
     
    if(mysql_num_rows($res)>0){
        while($row = mysql_fetch_assoc($res)){

            if($row['sender']==$userid)
                $user = $row['receiver'];
            else
                $user = $row['sender'];
           
            if($row['cunityId']!=NULL&&$row['cunityId']>0){
                    $cunityId = $row['cunityId'];
                    $cunityTplId = $cunityId.'-'; 
                    $fromcunity = $lang['friends_from_cunity'].'&nbsp;"'.getCunityName($row['cunityId']).'"';
                    $remote = true;
                    $remoteRes = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."connected_users AS u, ".$cunity->getConfig("db_prefix")."connected_cunities AS c WHERE u.cunityId = c.cunityId AND u.localid = '".$user."'");
                    $remoteData = mysql_fetch_assoc($remoteRes);
                    $ud = base64_encode(json_encode($remoteData));
                }                    
                else{
                    $cunityId = 0;
                    $cunityTplId = "";
                    $fromcunity = "";
                    $remote = false;
                    $remoteData['cunityUrl'] = "";
                    $ud = "";
                }                    
              
            $buttons = '<div class="main_list_photos2" style="padding: 5px; display: block;"><div class="buttonset"><button class="jui-button" onclick="unblockFriend('.$user.',\''.$ud.'\','.(int)$remote.',removeUser);" icon="ui-icon-arrowreturnthick-1-w" text="1">'.$lang['friends_unblock_user'].'</button></div></div>';
            $blocked .= $cunity->getTemplateEngine()->createTemplate('friendList', array(
                'BUTTONS2'=>$buttons,
                'USERHASH'=>getUserHash($user,$remote,$cunityId),
                'AVATAR'=>getSmallAvatar($user,80,$remote,$cunityId),
                'UPDATED'=>$fromcunity,
                'USERID'=>$user,
                'USERNAME'=>getUserName($user,$remote,$cunityId),
                'CUNITYID'=>$cunityTplId
            ));
        }
        $blocked .= newCunityError($lang['friends_no_friends'],false);
    }else{        
        $blocked = newCunityError($lang['friends_no_friends']);
    }
    $replace['BLOCKED'] = $blocked;    
}
// Edit Password
elseif($_GET['do'] == 'passwd')
{
    $info = "";
    if(isset($_POST['pw1'])) 
    {
        if($_POST['pw2'] !== $_POST['pw3']) 
        {            
            $msg = newCunityError($lang['profile_edit_password_error']);
        }
        else 
        {
            $res = $cunity->getDb()->query("SELECT password FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = '".$userid."' LIMIT 1");
            $passData = mysql_fetch_assoc($res);

            if($passData['password'] === sha1($_POST['pw1'])){
                if(strlen($_POST['pw2']) < 6){                    
                    $msg = newCunityError($lang['profile_edit_password_sex']);
                }else{
                    $res = $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET password = '".mysql_real_escape_string(sha1($_POST['pw2']))."' WHERE userid = '".$userid."' LIMIT 1");
                    if($res !== false){                        
                        $msg = '<div class="message_green">'.$lang['profile_edit_password_changed'].'</div>';
                    }else{
                        
                        $msg = newCunityError('Database-Error!');
                    }
                }
            }else{                
                $msg = newCunityError($lang['profile_edit_password_false']);
            }
        }
    }

    $replace['INFO'] = $info;
}
elseif($_GET['do'] == 'privacy')
{	
	$colRes = $cunity->getDb()->query("SHOW COLUMNS FROM ".$cunity->getConfig("db_prefix")."privacy");
	while($col = mysql_fetch_assoc($colRes))
		$privacies[] = $col['Field'];
    if(isset($_POST['save']))
    {    	
    	foreach($privacies AS $field)
    		$privacy[$field] = mysql_real_escape_string($_POST[$field]);
    	
        $c = $cunity->getDb()->query("SELECT COUNT(*) as count FROM ".$cunity->getConfig("db_prefix")."privacy WHERE userid = '".$userid."' ORDER BY count");
        $c = mysql_fetch_assoc($c);
        if($c['count'] == '0')            
            $cunity->getDB()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."privacy (userid) VALUES ('".$userid."')");
        
        foreach($privacy AS $field => $val)
        	$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."privacy SET `".$field."` = '".$val."' WHERE userid = '".$userid."'");        
    }
    $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."privacy WHERE userid = '".$userid."'");
    $p = mysql_fetch_assoc($res);

    foreach($privacies AS $field){
    	$replace[strtoupper($field).'_'.$p[$field]] = 'selected="selected"';        
    }
}
elseif($_GET['do'] == 'general')
{
    $info = "";
    if(isset($_POST['mail'])){
        if($_POST['mail'] != $data['mail']){
            if(form_basics($_POST['mail'])&&preg_match("[^((?:(?:(?:\w[\.\-\+]?)*)\w)+)\@((?:(?:(?:\w[\.\-\+]?){0,62})\w)+)\.(\w{2,6})$]", $_POST['mail'])){				             		
       			$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET mail = '".mysql_real_escape_String($_POST['mail'])."' WHERE userid = ".$userid."") or die (mysql_error());          		    
           	}else{
                $msg = newCunityError($lang['profile_edit_invalid_mail']);
            }
        }        
        
        if($_POST['title'] != $data['title'])
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users_details SET title = '".mysql_real_escape_String($_POST['title'])."' WHERE userid = ".$userid."") or die (mysql_error());         
        //Check Birthday      
        if(!empty($_POST['day']) && !empty($_POST['month']) && !empty($_POST['year'])){
       		$temp_day = intval($_POST['day']);
       		$temp_month = intval($_POST['month']);
       		$temp_year = intval($_POST['year']);
    
       		if(($temp_day > 31 || $temp_day < 1) || ($temp_month > 12 || $temp_month < 1) || $temp_year < 1900){                
                $msg = newCunityError($lang['profile_edit_invalid_birthday']);
            }else{
    			if(strtotime($temp_year.'-'.$temp_month.'-'.$temp_day) > mktime(0, 0, 0, date('n'), date('j'), date('Y')-$cunity->getSetting('register_age'))){    				    				
    				$msg = newCunityError($lang['profile_edit_young_birthday'].$cunity->getSetting('register_age')." ".$lang['profile_view_years_old']." ".$lang['profile_edit_be']);
    			}else{
    			    $birthday = date("Y-m-d", mktime(0,0,0,$_POST['month'], $_POST['day'], $_POST['year']));
                    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users_details SET birthday = '".$birthday."' WHERE userid = '".$userid."'") or die (mysql_error());
                }
       		}
       	}else{
            if($field['birthday'] == 'M'){
                $msg = newCunityError($lang['profile_edit_empty_birthday']);                
            }
        }       
        
        profile_updated();   
        
        $res = $cunity->getDb()->query('SELECT * FROM '.$cunity->getConfig("db_prefix").'users LEFT JOIN '.$cunity->getConfig("db_prefix").'users_details ON '.$cunity->getConfig("db_prefix").'users.userid = '.$cunity->getConfig("db_prefix").'users_details.userid
        WHERE '.$cunity->getConfig("db_prefix").'users.userid = '.$userid.'
        ORDER BY nickname ASC LIMIT 1');
        
        $data = mysql_fetch_assoc($res);   
                   
    }    
    $replace['MAIL'] = $data['mail'];
    
    $birthday = strtotime($data['birthday']);        
    $replace['DAY'] = date('d', $birthday);    
    $replace['MONTH'] = date('m', $birthday);        
    $replace['YEAR'] = date('Y', $birthday); 
    
    if($data['title'] == 0){        
        $replace['WM'] = 'selected="selected"';        
        $replace['ME'] = '';
    }else{        
        $replace['WM'] = '';
        $replace['ME'] = 'selected="selected"';
    }
    
    $replace['INFO'] = $info;
}
elseif($_GET['do'] == 'personal'){   
    $info = "";
    $error = null;
    if(isset($_POST['relationship'])){
        if(isset($_POST['relationship_partner']) && $_POST['relationship_partner'] != $data['relationship_partner'] && $_POST['relationship_partner'] != 0){        
            $r1 = $cunity->getDb()->query("SELECT COUNT(*) FROM ".$cunity->getConfig("db_prefix")."relationship_requests WHERE sender = '".$userid."' AND receiver = '".$_POST['relationship_partner']."'");
            $cd = mysql_fetch_assoc($r1);
            if($cd['COUNT(*)']==0 && $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."relationship_requests (sender, receiver, status, relationship) VALUES ('".$userid."', '".$_POST['relationship_partner']."', 0, '".$_POST['relationship']."')")){
                $r = $cunity->getDb()->query("SELECT mail FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = '".$_POST['relationship_partner']."'");
                $d = mysql_fetch_assoc($r);                
                $objNotifier->sendNotification($d['mail'],"",getUserName($userid).' '.$lang['profile_edit_relationship_mail_subject'],getUserName($userid).' '.$lang['profile_edit_relationship_mail_content'].'<p><a href="'.$cunity->getSetting('url').'/profile.php?c=edit&do=personal_requests">'.$cunity->getSetting('url').'/profile.php?c=edit&do=personal_requests</a></p>',"","");
                print '<script language="javascript" type="text/javascript">$("document").ready(function(){
                    apprise("'.getUserName($_POST['relationship_partner']).' '.$lang['profile_edit_relationship_mail'].'");
                })</script>';
            }                                    
        }                        
        elseif(!isset($_POST['relationship_partner']) && $data['relationship_partner'] != 0){
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users_details SET relationship = '".mysql_real_escape_string($_POST['relationship'])."', relationship_partner = 0 WHERE userid = '".$userid."'") or die(mysql_error());
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users_details SET relationship_partner = 0 WHERE userid = '".$data['relationship_partner']."'") or die(mysql_error());
        }elseif(isset($_POST['relationship_partner']) && ($_POST['relationship_partner'] == 0 || $_POST['relationship_partner'] == "")){
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users_details SET relationship = '".mysql_real_escape_string($_POST['relationship'])."', relationship_partner = 0 WHERE userid = '".$userid."'") or die(mysql_error());
        }            
        $error = 0;
                                    
    }
    if(isset($_POST['interested_sent'])){
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users_details SET interested = '' WHERE userid = '".$userid."'");
        $interest = array();
        if(isset($_POST['interested_men']))
            $interest[] = $_POST['interested_men'];
        if(isset($_POST['interested_woman']))
            $interest[] = $_POST['interested_woman'];

        $in = json_encode($interest);
        if(!$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users_details SET interested = '".$in."' WHERE userid = '".$userid."'")){
            $error++;
        }else
            $error = 0;
    }
    foreach($_POST AS $key => $input){
        if($key != 'interested_men' && $key != 'interested_woman' && $key != 'interested_sent' && $key != 'mail' && $key != 'nickname' && $key != 'relationship_partner' && $key != 'partner' && $key != 'birthday'){
            $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."registration_fields WHERE name = '".$key."'");
            $names = mysql_fetch_assoc($res);
            if($names['type'] == 'C'){
                $update .= $names['name'].'=\'';
                if(count($_POST[$names['name']]) == 0){
                    $update .= '\',';
                }else{
                    foreach($_POST[$names['name']] AS $checkbox){
                        $update .= ''.$checkbox.'_';
                    }
                    $update = substr($update, 0, -1).'\'';
                }
                $q = "UPDATE ".$cunity->getConfig("db_prefix")."users_details SET ".$update." WHERE userid = '".$userid."'";
                if(!$cunity->getDb()->query($q)){
                    $error++;
                }else
                    $error = 0;
            }else{
                // DB -> Users_Details
                $q = "UPDATE ".$cunity->getConfig("db_prefix")."users_details SET ".$key." = '".$input."' WHERE userid = '".$userid."'";
                if(!$cunity->getDb()->query($q)){
                    $error++;
                }else
                    $error = 0;
            }
        }
    }
    if($error < 1 && $error != null){
        profile_updated();
    }elseif($error >= 1){        
        $msg = newCunityError($lang['profile_edit_error']);
    }
    
    $res = $cunity->getDb()->query('SELECT * FROM '.$cunity->getConfig("db_prefix").'users LEFT JOIN '.$cunity->getConfig("db_prefix").'users_details ON '.$cunity->getConfig("db_prefix").'users.userid = '.$cunity->getConfig("db_prefix").'users_details.userid
        WHERE '.$cunity->getConfig("db_prefix").'users.userid = '.$userid.'
        ORDER BY nickname ASC LIMIT 1');

        $data = mysql_fetch_assoc($res);
        
    $q = "SELECT * FROM ".$cunity->getConfig("db_prefix")."registration_fields WHERE cat = 'personal' OR cat = 'extra'";
    $res = $cunity->getDb()->query($q);
    $personal = "";
    if(mysql_num_rows($res) > 0)
    {
        while($assoc = mysql_fetch_assoc($res))
        {
            $personal .= '<tr class="privacy_line">';
            if($assoc['def'] == 'Y')
            {                                 
                $personal .= '<td style="vertical-align: middle;" class="privacy_label">'.$lang['profile_view_'.$assoc['name']].':</td>';
            }
            else
            {
                $personal .= '<td style="vertical-align: middle;" class="privacy_label">'.$assoc['name'].':</td>';
            }
            if($assoc['name'] != 'plz')
            {
                $personal .= '<td style="vertical-align: middle; text-align: left;" class="privacy_value" id="'.$assoc['name'].'">';
                if($assoc['name'] == 'town')
                {
                    $personal .= '
                            <input type="text" size="2" maxlength="5" name="plz" id="plz" value="'.$data['plz'].'"/>
                            &nbsp;
                            <input type="text" name="town" id="town" size="10" value="'.$data['town'].'"/>';
                }
                else
                {
                    if($assoc['type'] == 'T')
                    {                        
                        $personal .= '<input type="text" id="'.$assoc['name'].'" name="'.$assoc['name'].'" value="'.$data[$assoc['name']].'"/>';
                    }
                    elseif($assoc['type'] == 'C')
                    {
                        $values = explode("_", $data[$assoc['name']]);

                        for($i = 0; $i < 5; $i++)
                        {
                            if($assoc['value_'.$i] != "")
                            {
                                if(in_array($assoc['value_'.$i], $values))
                                {
                                    $personal .= '<input type="checkbox" name="'.$assoc['name'].'[]" value="'.$assoc['value_'.$i].'" id="'.$assoc['name'].'_'.$i.'" checked="checked"/><label for="'.$assoc['name'].'_'.$i.'">'.$assoc['value_'.$i].'</label><br />';
                                }
                                else
                                {
                                    $personal .= '<input type="checkbox" name="'.$assoc['name'].'[]" value="'.$assoc['value_'.$i].'" id="'.$assoc['name'].'_'.$i.'"/><label for="'.$assoc['name'].'_'.$i.'">'.$assoc['value_'.$i].'</label><br />';
                                }

                            }
                        }
                    }
                    elseif($assoc['type'] == 'R')
                    {
                        for($i = 0; $i < 5; $i++)
                        {
                            if($assoc['value_'.$i] != "")
                            {
                                if($data[$assoc['name']] == $assoc['value_'.$i])
                                {
                                    $personal .= '<input type="radio" name="'.$assoc['name'].'" value="'.$assoc['value_'.$i].'" id="'.$assoc['name'].'_'.$i.'" checked="checked"/><label for="'.$assoc['name'].'_'.$i.'">'.$assoc['value_'.$i].'</label><br />';
                                }
                                else
                                {
                                    $personal .= '<input type="radio" name="'.$assoc['name'].'" value="'.$assoc['value_'.$i].'" id="'.$assoc['name'].'_'.$i.'"/><label for="'.$assoc['name'].'_'.$i.'">'.$assoc['value_'.$i].'</label><br />';
                                }
                            }
                        }
                    }//end if
                    elseif($assoc['type'] == 'S')
                    {
                        $personal .= '<select name="'.$assoc['name'].'">';
                        for($i = 0; $i < 5; $i++)
                        {
                            if($assoc['value_'.$i] != "")
                            {
                                if($data[$assoc['name']] == $assoc['value_'.$i])
                                {
                                    $personal .= '<option name="'.$assoc['name'].'" value="'.$assoc['value_'.$i].'" selected="selected">'.$assoc['value_'.$i].'</option>';
                                }
                                else
                                {
                                    $personal .= '<option name="'.$assoc['name'].'" value="'.$assoc['value_'.$i].'">'.$assoc['value_'.$i].'</option>';
                                }

                            }
                        }
                        $personal .= '</select>';
                    }
                $personal .= '</td>';
                }//end else
            }//end if
            $personal .= '</tr>';
        }
    }
    $personal .= '<tr class="privacy_line">';
    $personal .= '<td style="vertical-align: middle;" class="privacy_label">'.$lang['profile_view_relationship'].'</td>';
    $personal .= '<td style="vertical-align: middle; text-align: left;" class="privacy_value">';
    $res1 = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."relationship_requests WHERE sender = '".$userid."' AND status = 0") or die (mysql_error());
    $rData = mysql_fetch_assoc($res1);
    $personal .= '<div class="info_addition" id="rel">';
    if(mysql_num_rows($res1)){
        $personal .= '<div class="relationship_request" id="request_'.$rData['relationship_id'].'"><span style="display: inline-block; vertical-align: middle; padding: 0px 2px; font-size: 13px;">'.$lang['profile_edit_sent_request_to'].' <a href=""profile.php?user='.getUserHash($rData['receiver']).'">'.getUserName($rData['receiver']).'</a></span><img src="style/'.$_SESSION['style'].'/img/fail.png" width="13px" height="13px" style="vertical-align: middle; cursor: pointer;" title="'.$lang['profile_edit_delete_request'].'" onclick="deleteRequest(\''.$rData['relationship_id'].'\')"/></div>';
        $personal .= '<div style="display: none;" id="relationship_wrap">';
    }else{
        $personal .= '<div id="relationship_wrap">';        
    }
        $personal .= '<select name="relationship" id="relationship_select">';
        switch($data['relationship']){
            case '0':
                $personal .= '
                            <option value="0" selected="selected"></option>
                            <option value="1">'.$lang['profile_view_in_relationship'].'</option>
                            <option value="2">'.$lang['profile_view_single'].'</option>
                            <option value="3">'.$lang['profile_view_married'].'</option>
                            <option value="4">'.$lang['profile_view_engaged'].'</option>
                ';
            break;
    
            case '1':
                $personal .= '
                            <option value="0"></option>
                            <option value="1" selected="selected">'.$lang['profile_view_in_relationship'].'</option>
                            <option value="2">'.$lang['profile_view_single'].'</option>
                            <option value="3">'.$lang['profile_view_married'].'</option>
                            <option value="4">'.$lang['profile_view_engaged'].'</option>
                ';
            break;
    
            case '2':
                $personal .= '
                            <option value="0"></option>
                            <option value="1">'.$lang['profile_view_in_relationship'].'</option>
                            <option value="2" selected="selected">'.$lang['profile_view_single'].'</option>
                            <option value="3">'.$lang['profile_view_married'].'</option>
                            <option value="4">'.$lang['profile_view_engaged'].'</option>
                ';
            break;
    
            case '3':
                $personal .= '
                            <option value="0"></option>
                            <option value="1">'.$lang['profile_view_in_relationship'].'</option>
                            <option value="2">'.$lang['profile_view_single'].'</option>
                            <option value="3" selected="selected">'.$lang['profile_view_married'].'</option>
                            <option value="4">'.$lang['profile_view_engaged'].'</option>
                ';
            break;
    
            case '4':
                $personal .= '
                            <option value="0"></option>
                            <option value="1">'.$lang['profile_view_in_relationship'].'</option>
                            <option value="2">'.$lang['profile_view_single'].'</option>
                            <option value="3">'.$lang['profile_view_married'].'</option>
                            <option value="4" selected="selected">'.$lang['profile_view_engaged'].'</option>
                ';
            break;
    
            default:
                $personal .= '
                            <option value="0" selected="selected"></option>
                            <option value="1">'.$lang['profile_view_in_relationship'].'</option>
                            <option value="2">'.$lang['profile_view_single'].'</option>
                            <option value="3">'.$lang['profile_view_married'].'</option>
                            <option value="4">'.$lang['profile_view_engaged'].'</option>
                ';
            break;
        }
        $personal .= '</select>'; 
        $personal .= '<div id="rel_part">';           
        if($data['relationship'] != 2 && $data['relationship_partner'] != 0){
            $personal .= '<span style="font-size: 14px;"><input type="hidden" name="relationship_partner" value="'.$data['relationship_partner'].'"/>&nbsp;'.$lang['profile_view_with'].'&nbsp;<a href="profile.php?user='.getUserHash($data['relationship_partner']).'">'.getUserName($data['relationship_partner']).'</a></span>';
        }elseif($data['relationship'] != 2 && $data['relationship_partner'] == 0){
            $personal .= '&nbsp;'.$lang['profile_view_with'].'&nbsp;<input type="hidden" name="relationship_partner" id="relationship_partner"/><input type="text" id="partner" name="partner"><div id="search_result" style="display: none;"></div>';
        }        
        $personal .= '</div>';
    $personal .= '</div>';
    $personal .= '</div>';    
    $personal .= '</td>';    
    $personal .= '</tr>';

    $personal .= '<tr class="privacy_line">';
    $personal .= '<td style="vertical-align: middle;" class="privacy_label">'.$lang['profile_view_interested'].'</td>';
    $personal .= '<td style="vertical-align: middle; text-align: left;" class="privacy_value">';
    $interested = json_decode($data['interested']);
    if(count($interested)>0){
        foreach($interested AS $key => $interest){
            $int[$interest] = 'checked="checked"';
        }
    }    
    $personal .= '
            <input type="checkbox" name="interested_men" id="men" value="men" '.$int['men'].'/>
            <label for="men">'.$lang['profile_view_men'].'</label>
            <br />
            <input type="checkbox" name="interested_woman" id="woman" value="woman" '.$int['woman'].'/>
            <label for="woman">'.$lang['profile_view_woman'].'</label>
            <input type="hidden" name="interested_sent" value="1"/>
    ';
    $personal .= '</td>';
    $personal .= '</tr>';
    $personal .= '<tr class="privacy_line">';
    $personal .= '<td style="vertical-align: middle;" class="privacy_label">'.$lang['profile_view_about'].'</td>';
    $personal .= '<td style="vertical-align: middle; text-align: left;" class="privacy_value">';
    $personal .= '<textarea name="about" class="about_edit_box" style="width: 300px; height: 100px;">'.$data['about'].'</textarea>';
    $personal .= '</td>';
    $personal .= '</tr>';
    
    $replace['PERSONAL_LIST'] = $personal;    
    $replace['INFO'] = $info;
}
elseif($_GET['do'] == 'personal_requests')
{
    $res2 = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."relationship_requests WHERE receiver = '".$userid."' AND status = 0 AND sender != '".$userid."'") or die(mysql_error());    
    if(mysql_num_rows($res2) >0)
    {
        $requests = "";
        while($dataRequests = mysql_fetch_assoc($res2))
        {
            $senderhash = getUserHash($dataRequests['sender']);
            $img = getAvatarPath($dataRequests['sender']);
            
            $relationship = "";
            switch($dataRequests['relationship'])
            {
                case 1:
                    $relationship = $lang['profile_view_in_relationship'];
                break;
                
                case 3:
                    $relationship = $lang['profile_view_married'];
                break;
                
                case 4:
                    $relationship = $lang['profile_view_engaged'];
                break;
            }
                
            $requests .= '
            <tr class="privacy_line">
                <td style="vertical-align: middle;" class="privacy_label"><a href="profile.php?user='.$senderhash.'"><img src="'.$img.'" width="40px" height="40px" style="vertical-align: middle;"/><span style="vertical-align: middle; padding-left: 10px;">'.getUserName($dataRequests['sender']).'</span></a><small style="margin: 5px 10px;">'.$relationship.'</small></td>
                <td style="vertical-align: middle; text-align: right;" class="privacy_value"><input type="button" value="'.$lang['profile_edit_confirm'].'" class="jui-button" onclick="confirmRequest(\''.$dataRequests['relationship_id'].'\')"/><input type="button" value="'.$lang['profile_edit_ignore'].'" class="jui-button" onclick="deleteRequest(\''.$dataRequests['relationship_id'].'\')"/></td>
            </tr>';
        }
    }
    else
    {
        header("Location: profile.php?c=edit&do=general");
        exit;
    }
    $replace['REQUESTS'] = $requests;
}
elseif($_GET['do'] == 'delete')
{
    if(isset($_POST['confirm']))
    {
        if(sha1($_POST['password']) == $data['password'])
        {
            $id = $userid;
            //Delete User data
        	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."users WHERE userid='".mysql_real_escape_string($id)."' LIMIT 1") or mysql_error();
        	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."users_details WHERE userid='".mysql_real_escape_string($id)."' LIMIT 1") or mysql_error();
    
            //news-comments
        	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."comments WHERE poster_id='".mysql_real_escape_string($id)."'") or mysql_error();
    
        	// FORUMS MODULE
        	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_unread WHERE userid='".mysql_real_escape_string($id)."'") or mysql_error();
        	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_users WHERE userid='".mysql_real_escape_string($id)."'") or mysql_error();
        	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_watch WHERE userid='".mysql_real_escape_string($id)."'") or mysql_error();
        	$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."forums_posts SET user_id=0 WHERE userid='".mysql_real_escape_string($id)."'") or mysql_error();
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_posts WHERE userid='".mysql_real_escape_string($id)."'") or mysql_error();
    
            //Gallery
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."galleries_albums WHERE user_id = '".$id."'") or mysql_error();
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."galleries_comments WHERE userid = '".$id."'") or mysql_error();
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."galleries_likes WHERE userid = '".$id."'") or mysql_error();
            $res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."galleries_imgs WHERE uploader_id = '".$id."'") or mysql_error();
            while($data = mysql_fetch_assoc($res))
            {
                unlink('../'.$data['file']);
            }
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."galleries_imgs WHERE uploader_id = '".$id."'") or mysql_error();
    
            //delete Invitation codes
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."invitation_codes WHERE userid = '".$id."'") or mysql_error();
    
            //Delete all messages
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."messages WHERE sender = '".$id."' OR receiver = '".$id."'") or mysql_error();
    
            //Delete Notifications +settings
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."notifications WHERE userid = '".$id."'") or mysql_error();
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."notifications_settings WHERE userid = '".$id."'") or mysql_error();
    
            //Delete pinboard
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."pinboard_likes WHERE userid = '".$id."'") or mysql_error();
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."pinboard_status WHERE userid = '".$id."'") or mysql_error();
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."pinboard_status_comments WHERE userid = '".$id."'") or mysql_error();
    
            //Delete privacy-settings
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."privacy WHERE userid = '".$id."'") or mysql_error();
    
            //Delete relationship-requests
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."relationship_requests WHERE userid = '".$id."'") or mysql_error();
    
            //delete chat
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."chat WHERE userid = '".$id."'") or mysql_error();
    
            //delete events
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."events SET founder_id = 0 WHERE founder_id = '".$id."'") or mysql_error();
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."events_guests WHERE userid = '".$id."'") or mysql_error();
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."events_comments WHERE userid = '".$id."'") or mysql_error();
    
            //Delete files
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."files WHERE user_id = '".$id."'") or mysql_error();
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."files_download WHERE user_id = '".$id."'") or mysql_error();
            $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."files_share WHERE uploader_id = '".$id."'") or mysql_error();
    
        	//delete avatar and profileimage
        	$avatar = '../files/_avatars/'.$id.'.jpg';
        	$ppic = '../files/_profile_imgs/'.$id.'.jpg';
    
        	if(file_exists($avatar))
        		unlink($avatar);
    
        	if(file_exists($ppic))
        		unlink($ppic);
        		
        	require_once 'Cunity_Connector.class.php';
        	$connector = new Cunity_Connector($cunity);
        	if($connector->isConnected())
        		$connector->deleteUserFromServer($_SESSION['userid']);
        	unset($connector);
        	
    
        	emptyTempFilesFolder();
            
        	clearChatHistory();
        
        	$_SESSION = array();
        
        	if(ini_get('session.use_cookies'))
            {
        		$params = session_get_cookie_params();
        		setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"]);
        	}
        
        	session_destroy();
        
        
        
        	header('location: index.php');
        	exit();
            
            print '<script language="javascript" type="text/javascript">$(document).ready(function(){
                apprise(\''.$lang['profile_edit_account_deleted'].'\', {verify:true}, function(r){
                    location.href=\'index.php\';
                });
            })</script>';
        }  
        else
        {            
            $msg = newCunityError($lang['profile_edit_password_failed']);
        }                  		
    }    
}

$profile_information = "";
$res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."relationship_requests WHERE receiver = '".$userid."' AND status = 0 AND sender != '".$data['relationship_partner']."'") or die(mysql_error());
$d = mysql_fetch_assoc($res);
if(mysql_num_rows($res) > 0){
    $profile_information = '<div id="profile_information"><a href="profile.php?c=edit&do=personal_requests">'.getUserName($d['sender']).' '.$lang['profile_edit_relationship_info'].'</a></div>';
}

$replace['PROFILE_INFO'] = $profile_information;

$replace['NAME'] = $data['username'];

if($cunity->getSetting('user_name') == 'nickname')
    $replace['NICKNAME_jui-button'] = '';
else
    $replace['NICKNAME_jui-button'] = ' <span style="font-weight: bold; font-size: 16px;">('.$data['nickname'].')</span>';

if($data['title'] == 1)
    $replace['SEX'] = $lang['profile_view_male'];
else
    $replace['SEX'] = $lang['profile_view_female'];

$temp = strtotime($data['birthday']);
$age = time() - $temp;
$age = floor($age / 31536000); // 60 / 60 / 24 / 365
$replace['AGE'] = $age.' '.$lang['profile_view_years_old'];

if($data['town'] != '')
    $replace['FROM'] = $lang['profile_view_from'].' '.$data['plz'].' '.$data['town'];
else
    $replace['FROM'] = "";

$replace['MEMBER_SINCE'] = $lang['profile_view_member_since'].' '.date($_SESSION['date']['php']['date'], strtotime($data['registered']));

$replace['UPDATED_ON'] = $lang['profile_view_updated'].' '.date($_SESSION['date']['php']['date'], strtotime($data['updated']));

$replace['BIRTHDAY'] = $lang['profile_view_born_on'].' '.date($_SESSION['date']['php']['date'], strtotime($data['birthday']));
        
$replace['IMG_WIDTH'] = $size[0]+32;
$replace['IMG_HEIGHT'] = $size[1]+32;

// Avatar
$avatar = './files/_avatars/'.$userhash.'.jpg';
if(!file_exists($avatar))
    $avatar = 'style/'.$_SESSION['style'].'/img/no_avatar.jpg';
$replace['AVATAR'] = $avatar;

$replace['PROFILE_PIC'] = $profile->getProfileImage();


$profile_size = getimagesize($profile->getProfileImage());
$replace['PROFILE_PIC_WIDTH'] = $profile_size[0];
$replace['PROFILE_PIC_HEIGHT'] = $profile_size[1];

$replace['ID'] = $userid;

$replace['MSG'] = $msg;

$tplEngine->Assign('TITLE', $replace['NAME'].' - '.$lang['profile_edit_'.$_GET['do']]);

$tplEngine->Template('profile_edit');        
    
    $tplEngine->Assign($replace);
    $tplEngine->Assign('do', $_GET['do']);
    $tplEngine->Assign('EDIT_TITLE', $lang['profile_edit_'.$_GET['do']]);
    $tplEngine->Assign('profile_edit_save',$lang['profile_edit_save']);
    $tplEngine->Assign('profile_edit_back',$lang['profile_edit_back']);
    
    //Menu    
    $tplEngine->Assign('profile_edit_image_edit',$lang['profile_edit_img_edit']);
    $tplEngine->Assign('profile_edit_blocked_persons',$lang['profile_edit_blocked_list']);
    $tplEngine->Assign('profile_edit_privacy',$lang['profile_edit_privacy']);
    $tplEngine->Assign('profile_edit_change_password',$lang['profile_edit_change_pw']);
    $tplEngine->Assign('profile_edit_general',$lang['profile_edit_general']);
    $tplEngine->Assign('profile_edit_personal',$lang['profile_edit_personal']);
    $tplEngine->Assign('profile_edit_blocked',$lang['profile_edit_blocked_list']);
    
    //General
    $tplEngine->Assign('profile_edit_email',$lang['profile_edit_email']);
    $tplEngine->Assign('profile_edit_title',$lang['profile_edit_title']);
    $tplEngine->Assign('profile_edit_birthday',$lang['profile_edit_birthday']);
    $tplEngine->Assign('profile_edit_woman',$lang['profile_edit_woman']);
    $tplEngine->Assign('profile_edit_men',$lang['profile_edit_men']);
    $tplEngine->Assign('profile_edit_date_format',$lang['profile_edit_date_format']); 
    
    
    //Delete account
    $tplEngine->Assign('profile_edit_delete_account',$lang['profile_edit_delete_account']);
    $tplEngine->Assign('profile_edit_account_delete_info',$lang['profile_edit_account_delete_info']);
    $tplEngine->Assign('profile_edit_password',$lang['profile_edit_password']);    
    $tplEngine->Assign('profile_edit_cancel',$lang['profile_edit_cancel']);
    
    //Personal
    $tplEngine->Assign('profile_view_with',$lang['profile_view_with']);
    $tplEngine->Assign('profile_edit_confirm_delete_request',$lang['profile_edit_cofirm_delete_request']);
    
    //change pw
    $tplEngine->Assign('profile_edit_pw_current',$lang['profile_edit_pw_current']);
    $tplEngine->Assign('profile_edit_pw_new',$lang['profile_edit_pw_new']);
    $tplEngine->Assign('profile_edit_pw_rpt',$lang['profile_edit_pw_rpt']);
    $tplEngine->Assign('profile_edit_passwd',$lang['profile_edit_change_pw']);
    
    //blocked list
    $tplEngine->Assign('blocked_friends', $messages_html_rows);
    $tplEngine->Assign('friends_unblock_friend',$lang['friends_unblock_user']);
    $tplEngine->Assign('friends_unblock_friend_header',$lang['friends_unblock_friend_header']);
    $tplEngine->Assign('friends_unblock_info',$lang['friends_unblock_info']);
    $tplEngine->Assign('friends_cancel',$lang['friends_cancel']);
    
    //privacy
    $tplEngine->Assign('profile_edit_privacy_searching',$lang['profile_edit_privacy_searching']);
    $tplEngine->Assign('profile_edit_privacy_friending',$lang['profile_edit_privacy_friending']);
    $tplEngine->Assign('profile_edit_privacy_messages',$lang['profile_edit_privacy_messages']);
    $tplEngine->Assign('profile_edit_privacy_pinboard',$lang['profile_edit_privacy_pinboard']);
    $tplEngine->Assign('profile_edit_privacy_profile',$lang['profile_edit_privacy_profile']);
    $tplEngine->Assign('profile_edit_privacy_address',$lang['profile_edit_privacy_address']);
    
    $tplEngine->Assign('profile_edit_privacy_everyone',$lang['profile_edit_privacy_everyone']);
    $tplEngine->Assign('profile_edit_privacy_everyone_in_cunity',$lang['profile_edit_privacy_everyone_in_cunity']);
    $tplEngine->Assign('profile_edit_privacy_friends_of_friends',$lang['profile_edit_privacy_friends_of_friends']);
    $tplEngine->Assign('profile_edit_privacy_friends_only',$lang['profile_edit_privacy_friends_only']);
    
    //Image
    $tplEngine->Assign('profile_edit_img',$lang['profile_edit_img']);
    $tplEngine->Assign('profile_edit_img_appears',$lang['profile_edit_img_appears']);
    $tplEngine->Assign('profile_edit_new_img',$lang['profile_edit_new_img']);
    $tplEngine->Assign('profile_edit_jpg',$lang['profile_edit_jpg']);
    $tplEngine->Assign('profile_edit_avatar_preview',$lang['profile_edit_avatar_preview']);
    $tplEngine->Assign('profile_edit_select_avatar',$lang['profile_edit_select_avatar']);
    $tplEngine->Assign('profile_edit_delete',$lang['profile_edit_delete']);
    
    //Notification
    $tplEngine->Assign('profile_edit_notify_when_so',$lang['profile_edit_notify_when_so']);
    $tplEngine->Assign('profile_edit_get_message',$lang['profile_edit_get_message']);
    $tplEngine->Assign('profile_edit_add_friend',$lang['profile_edit_add_friend']);
    $tplEngine->Assign('profile_edit_post_on_pin',$lang['profile_edit_post_on_pin']);
    $tplEngine->Assign('profile_edit_comment_status',$lang['profile_edit_comment_status']);
    $tplEngine->Assign('profile_edit_invited',$lang['profile_edit_invited']);
    $tplEngine->Assign('profile_edit_file_shared',$lang['profile_edit_file_shared']);
    $tplEngine->Assign('profile_edit_forum_new_post',$lang['profile_edit_forum_new_post']);
    $tplEngine->Assign('profile_edit_notifications',$lang['profile_edit_notifications']);
    $tplEngine->Assign('profile_edit_also_comment_status',$lang['profile_edit_also_status_comment']);
    $tplEngine->Assign('profile_edit_pinboard_comment_status',$lang['profile_edit_pinboard_status_comment']);
    
?>