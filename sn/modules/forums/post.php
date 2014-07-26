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
   
	// Setup
	$action = 'forums.php?fid='.$forum_id;
	define('NEW_THREAD', $newthread);
	$subject = '';
	$smilies = '';
	
	if(isset($_REQUEST['message']))
		$msg = $_REQUEST['message'];
	elseif($quote && $post_id != -1) {
		$res = $cunity->getDb()->query("SELECT message FROM ".$cunity->getConfig("db_prefix")."forums_posts WHERE post_id=".$post_id." LIMIT 1");
		if(mysql_num_rows($res) > 0) {
			$data = mysql_fetch_assoc($res);
			$msg = '[quote]'.$data['message'].'[/quote]';
		}
		else
			$msg = '';
	}
	else
		$msg = '';
	
	if($topic_id > 0)
		$action .= '&tid='.$topic_id;
	if(post_id > 0)
		$action .= '&pid='.$post_id;
		
	if($newthread)
		$action .= '&newthread=1';
	elseif($edit)
		$action .= '&edit=1';
	elseif($answer)
		$action .= '&answer=1';
	
	// Topic Data
	if(!$newthread) {
		$topic = mysql_fetch_assoc($cunity->getDb()->query("SELECT subject, closed FROM ".$cunity->getConfig("db_prefix")."forums_topics WHERE topic_id=".$topic_id." LIMIT 1"));
		if(!$cunity->getSaver()->admin() && $topic['closed'])
			die($lang['post_thread_closed']);
	}
	
	// Subject
		if(!$newthread)
			$subject = 'RE: '.$topic['subject'];
		elseif(isset($_REQUEST['subject']))
			$subject = $_REQUEST['subject'];

	//Smilies
	getSmilies();
 	if(count($smilies) > 0) {
		$sc = 0;
		$i = 0;
		$smilies_table .= '<table id="smilietable"><tr><td colspan="3" class="headline">Smilies</td></tr><tr>';
		foreach($smilies AS $smiley)
        {
			if($sc%3 == 0) {
				$smilies_table .= '</tr><tr>';
			}
			if($i%2==0)
			{
                $smilies_table .= '<td><a href="#">'.$smiley.'</a></td>';
                    $sc++;       
            }			    
			
			$i++;
		}
		$smilies_table .= '</tr></table>';
	} 	
			
	// Template 
	$tplModEngine->Template('posts');
	   
	   $tplModEngine->Assign('posts_message',$lang['posts_message']);
	   $tplModEngine->Assign('posts_subject',$lang['posts_subject']);
	   $tplModEngine->Assign('posts_quote',$lang['posts_quote']);
	   $tplModEngine->Assign('posts_pin_thread',$lang['posts_pin_thread']);
	   $tplModEngine->Assign('forum_back',$lang['forums_back']);
	   $tplModEngine->Assign('posts_preview',$lang['posts_preview']);
	   $tplModEngine->Assign('posts_send',$lang['posts_send']);
	   $tplModEngine->Assign('posts_back',$lang['posts_back']);
	   
       $tplModEngine->Assign('edit_profile',$lang['profile_view_profile_edit']);
       $tplModEngine->Assign('edit_img',$lang['profile_edit_img_edit']);
       
       $tplModEngine->Assign('FORUM_ID',$forum_id);
		
	if($preview) {
		$tplModEngine->Assign('PREVIEW_MSG', parse($_REQUEST['message']));
	}
	
	$error = '';
	if(isset($_REQUEST['send'])) {
		if(strlen(trim($_REQUEST['subject'])) < 3)
			$error .= $lang['post_subject_char'].'<br>';
	}
	
	$tplModEngine->Assign('ACTION_URL', $action);
	$tplModEngine->Assign('ERROR', $error);
	$tplModEngine->Assign('SUBJECT', $subject);
	$tplModEngine->Assign('MSG', $msg);
	$tplModEngine->Assign('SMILIES', $smilies_table);	
?>