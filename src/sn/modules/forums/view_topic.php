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
   
	$p_steps = 10;
	$p_start = 0;
	
	getSmilies();
	
	define('TOPIC', true);
	
	// mark read
	$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."forums_unread WHERE user_id='".mysql_real_escape_string($_SESSION['userid'])."' AND topic_id='".$topic_id."' LIMIT 1");
		
		
	$res = $cunity->getDb()->query("SELECT COUNT(*) as counted FROM ".$cunity->getConfig("db_prefix")."forums_posts WHERE topic_id=".$topic_id);
	$p_count = mysql_fetch_assoc($res);
	$p_count = $p_count['counted'];
		
	if($page > 1 && $page != 'last')		
		$p_start = $p_steps * ($page - 1);
	
	if($p_start > $p_count || $page == 'last')
		$p_start = ceil(($p_count - $p_steps) / $p_steps) * $p_steps;
		
	if($p_start < 1 || $p_start > $p_count)
		$p_start = 0;
		
	$res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."forums_posts WHERE topic_id=".$topic_id." ORDER BY posttime DESC LIMIT ".$p_start.", ".$p_steps);
	
	$posts = array();
	$posts_list = '';
	
	while($data = mysql_fetch_assoc($res)) {
		$posts[] = $data;
	}

	// PageNav               
	if($p_count > $p_steps) {
		$p_nav .= '<div class="postnav">';
		if($page <= 1 && $page != 'last')
			$p_nav .= '<strong>'.$lang['view_topic_page'].'</strong>';
		else
			$p_nav .= '<a href="forums.php?fid='.$forum_id.'&tid='.$topic_id.'&page=1">'.$lang['view_topic_page'].'</a>';
			
		for($i = 2; $i < ceil($p_count / $p_steps); $i++) {
			if($page == $i)
				$p_nav .= ' - <strong>'.$i.'</strong>';
			else
				$p_nav .= ' - <a href="forums.php?fid='.$forum_id.'&tid='.$topic_id.'&page='.$i.'">'.$i.'</a>';
		}
		
		if($page == 'last')
			$p_nav .= ' - <strong>'.$i.'</strong>';
		else
			$p_nav .= ' - <a href="forums.php?fid='.$forum_id.'&tid='.$topic_id.'&page=last">'.$i.'</a>';
			
		$p_nav .= '</div>';
	}	
        
	$forum = mysql_fetch_assoc($cunity->getDb()->query("SELECT cat_id, name FROM ".$cunity->getConfig("db_prefix")."forums_boards WHERE board_id=".$forum_id." LIMIT 1"));
	$cat = mysql_fetch_assoc($cunity->getDb()->query("SELECT name FROM ".$cunity->getConfig("db_prefix")."forums_boards WHERE board_id=".$forum['cat_id']." LIMIT 1"));
	$topic = mysql_fetch_assoc($cunity->getDb()->query("SELECT subject, closed FROM ".$cunity->getConfig("db_prefix")."forums_topics WHERE topic_id=".$topic_id." LIMIT 1"));
	
	$posts_list .= $p_nav;
		
	$posts_list .= '<table class="cat_block" style="width: 100%;">';
	
	foreach($posts as $value) {
		$posts_list .= '<tr>
			<td class="post_sub" colspan="2">'.$value['subject'].'
			<span class="post_time">'.showDate('date_time', $value['posttime'],true).'</span>
			</td>
		</tr><tr>
			<td class="userinfo"width="130px">            
		        <img src="'.getAvatarPath($value['user_id']).'" class="post_avatar">                        
            </td>        
			<td class="post" style="border-left: 1px dashed #ccc;">'.parse($value['message']).'</td>            			
		</tr><tr>
            <td class="postnav" width="130px" style="text-align: center;"><button onclick="location.href=\'profile.php?user='.getUserHash($value['user_id']).'\'" class="jui-button">'.getUserName($value['user_id']).'</button></td>
			<td class="postnav buttonset">
			<button class="jui-button" icon="ui-icon-comment" onclick="location.href=\'forums.php?fid='.$forum_id.'&tid='.$topic_id.'&answer=1&quote=1&pid='.$value['post_id'].'\'">'.$lang['view_topic_cite_post'].'</button>';			
			if($cunity->getSaver()->admin() || $value['user_id'] == $_SESSION['userid']){
				$posts_list .= '<button class="jui-button" icon="ui-icon-close" onclick="deletePost(\''.$forum_id.'\',\''.$topic_id.'\',\''.$value['post_id'].'\');">'.$lang['view_topic_delete_post'].'</button>';
			}
            $posts_list .= '			
			</td>
		</tr>';
	}
		
	$posts_list .= '</table>';
	
	$posts_list .= $p_nav;				
    
	$tplModEngine->Template('view_topic');
	$tplModEngine->Assign('TITLE','<a href="forums.php">'.$cat['name'].'</a>&rArr;<a href="forums.php?fid='.$forum_id.'">'.$forum['name'].'</a>&rArr;<a href="forums.php?fid='.$forum_id.'&tid='.$topic_id.'">'.$topic['subject'].'</a>');
		
	
    $tplModEngine->Assign('edit_profile',$lang['profile_view_profile_edit']);
    $tplModEngine->Assign('edit_img',$lang['profile_edit_img_edit']);
    

	$tplModEngine->Assign('POSTS', $posts_list);
	$tplModEngine->Assign('POSTS_SUB', $posts_sub);
    $tplModEngine->Assign('view_topic_delete_confirm',$lang['view_topic_delete_confirm']);
    $tplModEngine->Assign('view_topic_response',$lang['view_topic_response']);
    $tplModEngine->Assign('delete_thread',$lang['forums_delete_thread']);
    $tplModEngine->Assign('forum_back',$lang['forums_back']);
    $tplModEngine->Assign('FORUM_ID', $forum_id);	
    $tplModEngine->Assign('TOPIC_ID', $topic_id);
?>