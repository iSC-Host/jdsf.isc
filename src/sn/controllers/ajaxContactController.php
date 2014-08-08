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
session_name('cunity_sess'.$_SERVER['DOCUMENT_ROOT']);
session_start();
   
ini_set('session.use_cookies', true);
set_include_path($_SESSION['cunity_trunk_folder'].'/classes');

require_once 'Cunity.class.php';

$cunity = new Cunity();

$lang = $cunity->getLang();

error_reporting($cunity->getConfig("error_reporting"));

require '../includes/functions.php';
if(isset($_GET['c']) && $_GET['c'] == 'getForm'){
    if($cunity->getSaver()->login()){
        $mail = $_SESSION['email'];
        $name = $_SESSION['username'];
    }
    $form .= '
    <form id="contact_form">
        <h1>Kontakt</h1>
        <table border="0" cellpadding="5" cellspacing="1" id="contact_table">
            <tr>
                <td><label for="contact_name" style="font-weight: bold;">'.$lang['pages_contact_name'].':</label></td>';
                if($cunity->getSaver()->login())
                    $form .= '<td><input type="text" name="contact_name" id="contact_name" value="'.$name.'" disabled="disabled"/></td>';
                else
                    $form .= '<td style="padding: 5px;"><input type="text" name="contact_name" id="contact_name"/></td>';
            $form .= '              
            </tr>
            <tr>
                <td><label for="contact_email" style="font-weight: bold;">'.$lang['pages_contact_email'].':</label></td>';
                if($cunity->getSaver()->login())
                    $form .= '<td><input type="text" name="contact_email" id="contact_email" value="'.$mail.'" disabled="disabled"/></td>';
                else
                    $form .= '<td style="padding: 5px;"><input type="text" name="contact_email" id="contact_email"/></td>';
            $form .= '
            </tr>
            <tr>
                <td colspan="2"><label for="contact_message" style="font-weight: bold;">'.$lang['pages_contact_message'].':</label></td>
            </tr>
            <tr>
                <td colspan="2"><textarea name="contact_message" id="contact_message" style="width: 380px; height: 190px;"></textarea></td>
            </tr>
        </table>
    </form>';
    echo $form;
}elseif(isset($_GET['c']) && $_GET['c'] == 'sendContact'){
    $error = 0;    
    
    if($_GET['name'] == 'na' && $cunity->getSaver()->login())    
        $name = $_SESSION['username'];    
    elseif($_GET['name'] == 'na' && !$cunity->getSaver()->login())
        $error++;
    else
        $name = $_GET['name'];
        
    if($_GET['mail'] == 'na' && $cunity->getSaver()->login())
        $mail = $_SESSION['email'];
    elseif($_GET['mail'] && preg_match('/^[A-Z0-9.%+-_]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i', $_GET['mail']))
        $mail = $_GET['mail'];        
    else
        $error++;
    
    if(!preg_match('[^((?:(?:(?:\w[\.\-\+]?)*)\w)+)\@((?:(?:(?:\w[\.\-\+]?){0,62})\w)+)\.(\w{2,6})$]', $mail))
    	die($lang['register_email_invalid']);
        
    $msg = $lang['inbox_sender'].': '.$name.'<br />';
    $msg .= 'E-Mail: '.$mail.'<br />';
    $msg .= $lang['inbox_message'].': <br />';
    
    if(strlen($_GET['msg']) == 0 || !isset($_GET['msg']) || $_GET['msg'] == 'na')
        $error++;
    else
        $msg .= $_GET['msg'];
        
    if($error == 0){
        if($cunity->getMailer()->sendmail($cunity->getSetting('contact_mail'), "Cunity Admin",$lang['pages_contact_header'], $msg,$mail, $name))
            echo $lang['pages_contact_success'];
    }else{
        die($lang['pages_contact_error']);
    } 
    
}

?>