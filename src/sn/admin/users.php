<?php
ob_start("ob_gzhandler");
require('ov_head.php');

	$cunity->getSaver()->check_admin();

    if(isset($_GET['c']))        
        $template = $_GET['c'];
    else
        $template = '';
	// Page Content
	if(isset($_GET['success']) && $_GET['success'] == true)
    {
        print '<script language="javascript" type="text/javascript">
                $("document").ready(function(){
                    $("#change_success").fadeIn();
                    $("#change_success").delay(500);
                    $("#change_success").fadeOut();
                });
            </script>';
    }
	if($template == '' || $template == 'general') 
    {
    
        if(isset($_POST['send']))
        {            
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = '".mysql_real_escape_string($_POST['notify_new_user'])."' WHERE name = 'notify_new_users'");
            
            if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = '".mysql_real_escape_string($_POST['user_name'])."' WHERE name = 'user_name'")){
            	if($_POST['user_name']=="full_name"){
            		require_once 'Cunity_Registration.class.php';
            		$register = new Cunity_Registration($cunity);
            		if($register->setNameType(mysql_real_escape_string($_POST['user_name'])))
            			unset($register);
            		else
            			die("An error occurred :(");
            	}
            }
            
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = '".mysql_real_escape_string($_POST['space'])."' WHERE name = 'user_space'");

            header("location: users.php?c=general&success=true");
            exit;
        }        
		$tplEngine->Template('users_general');
        $res = $cunity->getDb()->query("SELECT value FROM ".$cunity->getConfig("db_prefix")."settings WHERE name = 'notify_new_users'");
        $assoc = mysql_fetch_assoc($res);
        if($assoc['value'] == 'no')
        {
            $tplEngine->Assign('NO', 'checked="checked"');
            $tplEngine->Assign('NO_POWER', 'off');
            $tplEngine->Assign('YES_POWER', 'none');
        }
        elseif($assoc['value'] == 'yes')
        {
            $tplEngine->Assign('YES', 'checked="checked"');
            $tplEngine->Assign('NO_POWER', 'none');
            $tplEngine->Assign('YES_POWER', 'on');
        }
        
        $res = $cunity->getDb()->query("SELECT value FROM ".$cunity->getConfig("db_prefix")."settings WHERE name = 'user_name'");
        $assoc = mysql_fetch_assoc($res);
        if($assoc['value'] == 'full_name')
        {
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."registration_fields SET importance = 'M' WHERE name = 'firstname' OR name = 'lastname'");
            $tplEngine->Assign('REAL', 'checked="checked"');
            $tplEngine->Assign('REALNAME', 'on');
            $tplEngine->Assign('NICKNAME', 'none');
        }
        elseif($assoc['value'] == 'nickname')
        {
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."registration_fields SET importance = 'O' WHERE name = 'firstname' OR name = 'lastname'");
            $tplEngine->Assign('NICK', 'checked="checked"');            
            $tplEngine->Assign('NICKNAME', 'on');
            $tplEngine->Assign('REALNAME', 'none');
        }
        
        $res = $cunity->getDb()->query("SELECT value FROM ".$cunity->getConfig("db_prefix")."settings WHERE name = 'user_space'");
        $data = mysql_fetch_assoc($res);
        $tplEngine->Assign('SPACE', $data['value']);
            
	}
	if($template == 'list')
	{
	   if(isset($_POST['activeMarked']))
       {
            if(isset($_POST['check']))
            {
                $checks = $_POST['check'];
                foreach ($checks AS $id)
                {
                    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET groupid = '3' WHERE userid = '".mysql_real_escape_string($id)."'");
                }
                header("location: users.php?c=list");
                exit;
            }
            else
            {
                header("location: users.php?c=list");
                exit;
            }
       }
       elseif(isset($_POST['inactiveMarked']))
       {
            if(isset($_POST['check']))
            {
                $checks = $_POST['check'];
                foreach ($checks AS $id)
                {
                    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET groupid = '5' WHERE userid = '".mysql_real_escape_string($id)."'");
                }
                header("location: users.php?c=list");
                exit;
            }
            else
            {
                header("location: users.php?c=list");
                exit;
            }
       }
	   elseif(isset($_POST['deleteMarked']))
	   {
            if(isset($_POST['check']))
            {
                $checks = $_POST['check'];
                foreach ($checks AS $id)
                {
                    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = '".mysql_real_escape_string($id)."'");   
                }
                header("location: users.php?c=list");
                exit; 
            }
            else
            {
                header("location: users.php?c=list");
                exit;
            }
       }
       elseif(isset($_POST['mailMarked']))
       {
            if(isset($_POST['check']))
            {
                foreach($_POST['check'] AS $receiver)
                {
                    $res = $cunity->getDb()->query("SELECT nickname FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = '".mysql_real_escape_string($receiver)."'");
                    $assoc = mysql_fetch_assoc($res);
                    $receivers .= '<a href="../profile.php?user='.getUserHash($receiver).'">'.$assoc['nickname'].'</a>&nbsp;|&nbsp;';
                }
                $tplEngine->Template('users_mail');
                  $tplEngine->Assign('RECEIVER', $receivers);                  
            }
            else
            {
                header("location: users.php?c=list");
                exit;
            }
            
              
       }       
       else
       {      
	   $count = 0;
	   $counter = 0;
	   $res = $cunity->getDb()->query("SELECT userid,nickname,mail,groupid,last_ip FROM ".$cunity->getConfig("db_prefix")."users WHERE groupid = 7");
       while($data = mysql_fetch_assoc($res))
       {
            $counter++;
	        $userId = $data['userid'];
            $regRes = $cunity->getDb()->query("SELECT DATE_FORMAT(registered, '%d.%m.%Y') AS registered FROM ".$cunity->getConfig("db_prefix")."users_details WHERE userid = '".mysql_real_escape_string($userId)."'");
            $regData = mysql_fetch_assoc($regRes);

            

            $users_list .= '<tr class="table_row row_'.$count % 2 .'" id="'.$data['userid'].'">';
            $users_list .= '<td>'.$counter.'</td>';
            if($data['groupid'] != '1')
            {
                if(isset($_GET['checkall']) && $_GET['checkall'] == '1')
                {
                    $users_list .= '<td><input type="checkbox" style="width: auto;" name="check[]" value="'.$data['userid'].'" checked="checked"/></td>';
                }
                else
                {
                    $users_list .= '<td><input type="checkbox" style="width: auto;" name="check[]" value="'.$data['userid'].'"/></td>';
                }
            }
            else
            {
                $users_list .= '<td></td>';
            }

            $users_list .= '<td><a href="../profile.php?user='.getUserHash($data['userid']).'">'.$data['nickname'].'</a></td>';
            $users_list .= '<td>'.$data['mail'].'</td>';
            $users_list .= '<td>'.$regData['registered'].'</td>';
            $users_list .= '<td>'.$data['last_ip'].'</td>';                        
            $users_list .= '<td><img src="style/default/img/cross.png" id="'.$data['userid'].'" title="'.$langadmin['admin_users_delete_user'].'" class="message_show delete_user_link" style="cursor: pointer;"/></td>';
            $users_list .= '<td><a href="users.php?c=activate&user='.$data['userid'].'"><img src="style/default/img/reblock.png" title="'.$langadmin['admin_users_activate'].'" /></a></td>';


            $users_list .= '</tr>';
            $count++;
       }
       
       if(mysql_num_rows($res) > 0)
       {
           $act_list = '<p>'.$langadmin['admin_users_not_activated'].'</p>
            <table border="1" id="user_table">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>#</th>
                        <th>'.$langadmin['admin_users_nickname'].'</th>
                        <th>'.$langadmin['admin_users_email'].'</th>
                        <th>'.$langadmin['admin_users_reg_date'].'</th>
                        <th>'.$langadmin['admin_users_last_ip'].'</th>
                        <th colspan="2">'.$langadmin['admin_users_action'].'</th>
                    </tr>
                </thead>
                <tbody>';
           $act_list .= $users_list;
           $act_list .= '</tbody></table>';
       }    
       else
       {
           $act_list = "";
       }    
       $users_list = "";
        
	   $res = $cunity->getDb()->query("SELECT userid,nickname,mail,groupid,last_ip,space FROM ".$cunity->getConfig("db_prefix")."users WHERE groupid != 7");
	   while($data = mysql_fetch_assoc($res))
	    {
	        $counter++;
	        $userId = $data['userid'];
            $regRes = $cunity->getDb()->query("SELECT DATE_FORMAT(registered, '%d.%m.%Y') AS registered FROM ".$cunity->getConfig("db_prefix")."users_details WHERE userid = '".mysql_real_escape_string($userId)."'");
            $regData = mysql_fetch_assoc($regRes);
            
            if($data['space'] == null)
            {
                $space = $cunity->getSetting('user_space');
            }
            else
            {
                $space = $data['space'];
            }
            
            if($data['groupid'] == '5')
            {
                $status = $langadmin['admin_users_inactive'];
            }
            elseif($data['groupid'] == '6')
            {
                $status = $langadmin['admin_users_blocked'];
            }
            elseif($data['groupid'] == '3')
            {
                $status = $langadmin['admin_users_active'];
            }
            elseif($data['groupid'] == '1')
            {
                $status = $langadmin['admin_users_owner'];
            }
            elseif($data['groupid'] == '2')
            {
                $status = $langadmin['admin_users_admin'];
            }
            
            $users_list .= '<tr class="table_row row_'.$count % 2 .'" id="'.$data['userid'].'">';
            $users_list .= '<td>'.$counter.'</td>';
            if($data['groupid'] != '1')
            {
                if(isset($_GET['checkall']) && $_GET['checkall'] == '1')
                {
                    $users_list .= '<td><input type="checkbox" style="width: auto;" name="check[]" value="'.$data['userid'].'" checked="checked"/></td>';
                }
                else
                {
                    $users_list .= '<td><input type="checkbox" style="width: auto;" name="check[]" value="'.$data['userid'].'"/></td>';
                }
            }
            else
            {
                $users_list .= '<td></td>';
            }
            
            $users_list .= '<td><a href="../profile.php?user='.getUserHash($data['userid']).'">'.$data['nickname'].'</a></td>';
            $users_list .= '<td>'.$data['mail'].'</td>';
            $users_list .= '<td>'.$regData['registered'].'</td>';
            $users_list .= '<td>'.$data['last_ip'].'</td>';
            $users_list .= '<td>'.$status.'</td>';
            $users_list .= '<td><input type="text" size="3" name="space" style="width: auto;" value="'.$space.'" class="edit_space" id="'.$data['userid'].'" max="10000" min="1" maxlength="5"/></td>';
            if($data['groupid'] == '1')
            {
                $users_list .= '<td></td>';
                $users_list .= '<td></td>';
                $users_list .= '<td></td>';
            }
            else
            {
                $users_list .= '<td><img src="style/default/img/cross.png" id="'.$data['userid'].'" title="'.$langadmin['admin_users_delete_user'].'" class="message_show delete_user_link" style="cursor: pointer;"/></td>';
                $users_list .= '<td><a href="../messages.php?c=sendmessage&userid='.getUserhash($data['userid']).'"><img src="style/default/img/mail-send.png" title="'.$langadmin['admin_users_send_message'].'"/></td>';
            
                if($data['groupid'] != '6')
                {
                    $users_list .= '<td><a href="users.php?c=block&user='.$data['userid'].'"><img src="style/default/img/block.png" title="'.$langadmin['admin_users_block_user'].'" /></a></td>';
                }
                elseif($data['groupid'] == '6')
                {
                    $users_list .= '<td><a href="users.php?c=block&user='.$data['userid'].'&reblock=true"><img src="style/default/img/reblock.png" title="'.$langadmin['admin_users_reblock_user'].'" /></a></td>';
                }
            }
            if($data['groupid'] != '2' && $data['groupid'] != '1')
            {
                $users_list .= '<td><a onclick="return confirm(\''.$langadmin['admin_users_admin_info'].'\');" href="users.php?c=admin&user='.$data['userid'].'"><img src="style/default/img/burn-plus.png" title="'.$langadmin['admin_users_add_admin'].'" /></a></td>';
            }
            elseif($data['groupid'] == '2' && $data['groupid'] != '1')
            {                 
                $users_list .= '<td><a href="users.php?c=admin&user='.$data['userid'].'&readmin=true"><img src="style/default/img/burn-minus.png" title="'.$langadmin['admin_users_del_admin'].'" /></a></td>';
            }
            elseif($data['groupid'] == '1')
            {
                $users_list .= '<td></td>';
            }
            
            $users_list .= '</tr>';
            $count++;
        }
	    
	
        $tplEngine->Template('users_list');
		  $tplEngine->Assign('USER_LIST',$users_list);
		  $tplEngine->Assign('ACT_LIST',$act_list);
		  }
    }
       
    if($template == 'block')
    {
        $userId = $_GET['user'];
        if(isset($_GET['reblock']) && $_GET['reblock'] == 'true')
        {
            if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET groupid = '3' WHERE userid = '$userId'"))
            {
                header("location: users.php?c=list");
                exit;
            }
        }
        else
        {
            if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET groupid = '6' WHERE userid = '$userId'"))
            {
                header("location: users.php?c=list");
                exit;
            }
        }        
    }
    
    if($template == 'admin')
    {
        $userId = $_GET['user'];
        if(isset($_GET['readmin']) && $_GET['readmin'] == 'true')
        {
            if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET groupid = '3' WHERE userid = '$userId'"))
            {
                header("location: users.php?c=list");
                exit;
            }
        }
        else
        {
            if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET groupid = '2' WHERE userid = '$userId'"))
            {
                header("location: users.php?c=list");
                exit;
            }
        }
    }
    	
    if($template == 'setNone')
    {
        $user = $_GET['user'];
        
        if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET use_forum = 'N', use_gallery = 'N', use_messaging = 'N', use_friends = 'N', use_filesharing = 'N' WHERE userid = '$user'"))
        {
            header("Location: users.php?c=list");
            exit;
        }
        
    }
    if($template == 'setAll')
    {
        $user = $_GET['user'];

        if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET use_forum = 'Y', use_gallery = 'Y', use_messaging = 'Y', use_friends = 'Y', use_filesharing = 'Y' WHERE userid = '$user'"))
        {
            header("Location: users.php?c=list");
            exit;
        }

    }
    if($template == 'activate')
    {
        $user = $_GET['user'];
        if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET groupid = '3' WHERE userid = '".$user."'"))
        {
            header("Location: users.php?c=list");
            exit;
        }
        
    }

require('ov_foot.php');
ob_end_flush();
?>