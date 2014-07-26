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
                  
	$p_steps = 30;
	$p_start = 0;

	getSmilies();
	
	define('FORUM', true);

	if($page > 1)
		$p_start = $p_steps * $page;

	$res = $cunity->getDb()->query("SELECT COUNT(*) as counted FROM ".$cunity->getConfig("db_prefix")."forums_topics WHERE board_id=".$forum_id);
	$t_count = mysql_fetch_assoc($res);
	$t_count = $t_count['counted'];

	if($page > 1 && $page != 'last')
		$p_start = $p_steps * ($page - 1);

	if($p_start > $t_count || $page == 'last')
		$p_start = ceil(($t_count - $p_steps) / $p_steps) * $p_steps;

	$res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."forums_topics WHERE board_id=".$forum_id." ORDER BY flag DESC, last_posttime DESC LIMIT ".$p_start.", ".$p_steps);

	$topics = array();
	$unread = array();
	$topics_list = '';

	while($data = mysql_fetch_assoc($res)) {
		$topics[$data['topic_id']] = $data;
	}

	$res = $cunity->getDb()->query("SELECT topic_id FROM ".$cunity->getConfig("db_prefix")."forums_unread WHERE user_id='".mysql_real_escape_string($_SESSION['userid'])."' AND board_id='".$forum_id."'");

	while($data = mysql_fetch_assoc($res)) {
		$unread[] = $data['topic_id'];
	}

	// PageNav
	if($t_count > $p_steps) {
		$p_nav .= '<div class="postnav">';
		if($page <= 1 && $page != 'last')
			$p_nav .= '<strong>'.$lang['view_forum_page'].'</strong>';
		else
			$p_nav .= '<a href="forums.php?fid='.$forum_id.'&page=1">'.$lang['view_forum_page'].'</a>';

		for($i = 2; $i < ceil($t_count / $p_steps); $i++) {
			if($page == $i)
				$p_nav .= ' - <strong>'.$i.'</strong>';
			else
				$p_nav .= ' - <a href="forums.php?fid='.$forum_id.'&page='.$i.'">'.$i.'</a>';
		}

		if($page == 'last')
			$p_nav .= ' - <strong>'.$i.'</strong>';
		else
			$p_nav .= ' - <a href="forums.php?fid='.$forum_id.'&page=last">'.$i.'</a>';

		$p_nav .= '</div>';
	}

	$forum = mysql_fetch_assoc($cunity->getDb()->query("SELECT cat_id, name, description FROM ".$cunity->getConfig("db_prefix")."forums_boards WHERE board_id=".$forum_id." LIMIT 1"));
	$cat = mysql_fetch_assoc($cunity->getDb()->query("SELECT name, description FROM ".$cunity->getConfig("db_prefix")."forums_boards WHERE board_id=".$forum['cat_id']." LIMIT 1"));

	$topics_list .= $p_nav;

	$topics_list .= '<table class="cat_block grid-share" border="0">';
	$topics_list .= '<tr><th colspan="2" class="cat">Name</th>
	<th class="cat_lastpost" style="border-left: 1px solid #fff; text-align: right;">'.$lang['view_forum_answers'].'</th>
	<th class="cat_lastpost" style="border-left: 1px solid #fff; text-align: left;">'.$lang['view_forum_last_posts'].'</th></tr>';

    if(count($topics)>0){        
        foreach($topics as $key => $value) {
    		if(array_search($value['topic_id'], $unread) === false)
    			$this_unread = 'read';
    		else
    			$this_unread = 'unread';
    
    		$last_post = showDate('date_time', (int)$value['last_posttime'],true);
    
    		$responses = mysql_fetch_assoc($cunity->getDb()->query("SELECT COUNT(*) as counted FROM ".$cunity->getConfig("db_prefix")."forums_posts WHERE topic_id=".$value['topic_id']));
    		$responses = $responses['counted'];
    		if($responses > 0)
    		    $responses = $responses - 1; // -first_post
    
    		$topics_list .= '
    		<tr>
    			<td class="indic"><img src="style/'.$_SESSION['style'].'/img/forum_'.$this_unread.'.png" /></td>
    			<td class="topic" rel="'.$value['first_post_id'].'">
    			<a href="forums.php?fid='.$forum_id.'&tid='.$value['topic_id'].'">';    	
    		$topics_list .=	$value['subject'].'</a>';
    		if($cunity->getSaver()->admin())
    		{
                $topics_list .= '<a href="javascript: deleteThread(\''.$value['topic_id'].'\')" style="margin-left: 5px; float: left;"><img src="style/'.$_SESSION['style'].'/img/fail.png" style="height: 12px; width: 12px;"></a>';
            }
            $topics_list .= '
    			</td>
    			<td class="posts">'.$responses.'</td>
    			<td class="lastpost">
    			<a href="forums.php?fid='.$forum_id.'&tid='.$value['topic_id'].'&page=last" title="'.$lang['view_forum_last_post'].'">
    			'.$last_post.'</a>
    			</td>
    		</tr>';
    	}
    }else{
        $topics_list = newCunityError('No topics found!');
    }	

	$topics_list .= '</table>';

	$topics_list .= $p_nav;
    
	$tplModEngine->Template('view_forum');
	$tplModEngine->Assign('TITLE','<a href="forums.php">'.$cat['name'].'</a>&rArr;<a href="forums.php?fid='.$forum_id.'">'.$forum['name'].'</a>');
		
	
    $tplModEngine->Assign('edit_profile',$lang['profile_view_profile_edit']);
    $tplModEngine->Assign('edit_img',$lang['profile_edit_img_edit']);
    

	$tplModEngine->Assign('TOPICS', $topics_list);
	$tplModEngine->Assign('view_forum_new_posts',$lang['view_forum_new_posts']);
	$tplModEngine->Assign('view_forum_no_posts',$lang['view_forum_no_posts']);
	$tplModEngine->Assign('view_forum_last_post',$lang['view_forum_last_post']);
	$tplModEngine->Assign('view_forum_last_posts',$lang['view_forum_last_posts']);
    $tplModEngine->Assign('view_forum_create_thread',$lang['view_forum_create_thread']);
    $tplModEngine->Assign('view_forum_answers',$lang['view_forum_answers']);
    $tplModEngine->Assign('view_forum_delete',$lang['view_forum_delete_thread_confirm']);
    $tplModEngine->Assign('view_forum_delete_info',$lang['view_forum_delete_info']);
    $tplModEngine->Assign('view_board_delete',$lang['view_board_delete_board']);
	$tplModEngine->Assign('view_board_delete_info',$lang['view_board_delete_info']);
	$tplModEngine->Assign('delete_board',$lang['forums_delete_board']);
	$tplModEngine->Assign('forum_back',$lang['forums_back']);
    $tplModEngine->Assign('FORUM_ID', $forum_id);



?>