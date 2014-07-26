<?php

ob_start("ob_gzhandler");
require('ov_head.php');

$cunity->getSaver()->check_admin();

$tplEngine->Template('round_mail');
if(isset($_GET['c']))
{
    switch($_GET['c'])
    {
        case 'overview':
            define('MAIL_OVERVIEW', true);
        break;
    
        case 'new':
            define('NEW_MAIL',true);
        break;
    
        default:
            define('MAIL_OVERVIEW',true);
        break;
    }
}
else
    define('MAIL_OVERVIEW',true);
$subj = '';
$body = '';
$msg = '';
$roger = true;

if(isset($_GET['message']))
{
    if($_GET['messageType'] == true)
    {
        $msg = '<div class="msg_green">'.$_GET['message'].'</div>';
    }
    else
    {
        $msg = '<div class="msg_red">'.$_GET['message'].'</div>';
    }

}
if(isset($_POST['send']))
{ //sent
    $subj = trim($_POST['subj']);
    $body = trim($_POST['body']);    
    $mailId = $_POST['id'];
    $signature = "\r\n\r\n---\r\nhttp://".$cunity->getSetting('url');
    
    $mail = new mail();
    if(!$mail->subject($subj)) 
    {
        $roger = false;
        $msg .= '<br>'.$langadmin['overview_subject_invalid'];
        $msgType = false;
    }
    
    if(!$mail->message($body.$signature)) 
    {
        $roger = false;
        $msg .= '<br>'.$langadmin['overview_message_empty'];
        $msgType = false;
    }
    
    if($_POST['reply'] == 'no')
    {
        $mail->from('no-reply@'.$cunity->getSetting('url'));
    }
    else
        $mail->from($_SESSION['email']);
    
    if($_POST['preview'] == 'yes') 
    { // Send Preview
        $mail->to($_SESSION['email']);
        $mail->send();
        $msg = $langadmin['overview_preview'].' "'.$_SESSION['email'].'" '.$langadmin['overview_sent'];
        $msgType = true;
    }
    else
    { // Send ALL
        $res = $cunity->getDb()->query("SELECT mail FROM ".$cunity->getConfig("db_prefix")."users WHERE groupid=3 OR groupid=2 OR groupid=1");
        $didnt_pass = array();
        $num = mysql_num_rows($res);
        
        if($num > 0) 
        {
            while($data = mysql_fetch_assoc($res)) 
            {
                if($mail->to($data['mail'])) 
                {
                    $mail->send();
                    usleep(10000); // sleep for 0.01sec
                }
                else
                    $didnt_pass[] = $data['mail'];
            }
        }
        
        $enum = count($didnt_pass);
        if($enum == 0) 
        {
            $msg = $langadmin['overview_message_sent'].$num.$langadmin['overview_members'];
            $msgType = true;
            if($mailId == '{$ID}')
            {
                $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."round_mails (subject,content,date,sent) VALUES ('".mysql_real_escape_string($subj)."','".mysql_real_escape_string($body)."',NOW(),NOW())");
            }
            else
            {
                $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."round_mails SET subject = '".mysql_real_escape_string($subj)."', content = '".mysql_real_escape_string($body)."',date = NOW(), sent = NOW() WHERE id = '".mysql_real_escape_string($mailId)."'");
            }
        }
        else 
        {
            $msg = $langadmin['overview_message_could'].$enum.$langadmin['overview_from'].$num.$langadmin['overview_member_name'].'<div>';
            foreach($didnt_pass as $value) 
            {
                $msg .= $value.'<br>';
            }
            $msg .='</div>';
            $msgType = false;
        }
    }
    header("Location: round_mail.php?c=new&message=".$msg."&messageType=".$msgType);
    exit;
}
if(isset($_POST['save']))
{
    $subject = $_POST['subj'];
    $content = $_POST['body'];
    $mailId = $_POST['id'];
    
    if($mailId == '{$ID}')
    {
        if($cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."round_mails (subject,content,date) VALUES ('".mysql_real_escape_string($subject)."','".mysql_real_escape_string($content)."',NOW())"))
        {
            $msg = $langadmin['admin_round_mail_saved'];
        }
    }
    else
    {    
        if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."round_mails SET subject = '".mysql_real_escape_string($subject)."', content = '".mysql_real_escape_string($content)."',date = NOW() WHERE id = '".mysql_real_escape_string($mailId)."'"))
        {
            $msg = $langadmin['admin_round_mail_saved'];
        }
    }
    header("location:round_mail.php");
    exit;
}
if(isset($_GET['c']) && $_GET['c'] == 'new' && isset($_GET['sub']) && isset($_GET['cont']))
    {
    $sub = $_GET['sub'];
    $cont = $_GET['cont'];
    $mailId = 0;
    if(isset($_GET['id']))
        $mailId = $_GET['id'];    
    
    $tplEngine->Assign('SUBJECT',$sub);
    $tplEngine->Assign('MBODY',$cont);
    $tplEngine->Assign('ID', $mailId);

}
if(isset($_GET['c']) && $_GET['c'] == 'overview' OR !isset($_GET['c']))
{
    $res = $cunity->getDb()->query("SELECT *,DATE_FORMAT(date, '%d.%m.%Y') AS date,DATE_FORMAT(sent, '%d.%m.%Y %H:%i') AS sent FROM ".$cunity->getConfig("db_prefix")."round_mails");
    $count = 0;
    $roundmails = "";
    if(mysql_num_rows($res) == '0')
    {
        $roundmails = '<tr><td colspan="6" align="center">'.$langadmin['admin_round_mail_no_mails'].'</td></tr>';
    }
    else
    {
        while($assoc = mysql_fetch_assoc($res))
        {
            if($assoc['sent'] == null)
            {
                $sent = $langadmin['admin_round_mail_not_sent'];
            }
            else
            {
                $sent = $assoc['sent'];
            }
            $roundmails .= '
            <tr class="row_'.$count % 2 .'">
            <td><input type="checkbox" name="'.$assoc['id'].'" style="width: 20px;" id="'.$assoc['id'].'"/></td>
            <td><a href="round_mail.php?c=new&sub='.$assoc['subject'].'&cont='.$assoc['content'].'&id='.$assoc['id'].'">'.$assoc['subject'].'</a></td>
            <td>'.$assoc['date'].'</td>
            <td>'.$sent.'</td>
            <td width="20px"><a href="round_mail.php?c=new&sub='.$assoc['subject'].'&cont='.$assoc['content'].'&id='.$assoc['id'].'"><img src="style/default/img/pencil.png" title="edit"/></a></td>
            <td width="20px"><a href="round_mail.php?c=del&id='.$assoc['id'].'" onclick="return confirm(\''.$langadmin['admin_round_mail_confirm_del'].'\')"><img  src="style/default/img/cross.png" title="delete"/></a></td>
            </tr>';
            $count++;
        }
    }

}
if(isset($_GET['c']) && $_GET['c'] == 'del' && isset($_GET['id']))
{
    $id = $_GET['id'];
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."round_mails WHERE id = $id");
}


$tplEngine->Assign('MSG', $msg);
$tplEngine->Assign('SUBJECT', $subj);
$tplEngine->Assign('MBODY', $body);
$tplEngine->Assign('URL', $cunity->getSetting('url'));

if(isset($roundmails))
    $tplEngine->Assign('ROUND_MAILS',$roundmails);
		
require('ov_foot.php');
ob_end_flush();
?>