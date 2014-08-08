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
   
    define('OVERVIEW', true);
	$res1 = $cunity->getDb()->query("SELECT board_id, name, description FROM ".$cunity->getConfig("db_prefix")."forums_boards WHERE flag=1 ORDER BY name ASC");
	$res2 = $cunity->getDb()->query("SELECT board_id, cat_id, name, description FROM ".$cunity->getConfig("db_prefix")."forums_boards WHERE flag=0 ORDER BY name ASC");

	$categories = array();
	$forums = array();
	$forums_list = '';
	
	while($data = mysql_fetch_assoc($res1)) {
		$categories[$data['board_id']] = $data;
	}
	
	while($data = mysql_fetch_assoc($res2)) {
		$forums[$data['cat_id']][] = $data;
	}
	foreach($categories as $key => $value1) 
    {				
		
    		$forums_list .= '<table class="cat_block grid-share" border="0">';
    		$forums_list .= '<tr><th colspan="3" class="cat" style="width: 80%;">    		
    		<span class="cat_headline">'.$value1['name'].'</span>    		
    		<div class="cat_description"><span>'.$value1['description'].'</span>';
    		if($cunity->getSaver()->admin())
    		{
                $forums_list .= '<a href="javascript: editBoard(\''.$value1['name'].'\',\''.$value1['description'].'\',\''.$value1['board_id'].'\');" style="margin-left: 10px;"><img src="style/'.$_SESSION['style'].'/img/edit.png" style="height: 12px;"/></a><a href="javascript: deleteBoard(\''.$value1['board_id'].'\')"><img src="style/'.$_SESSION['style'].'/img/fail.png" style="height: 12px;" /></a>';
            }
            $forums_list .= '</div>
    		</th>
    		<th class="cat_lastpost" style="width: 20%; text-align:left;border-left:1px solid #fff">'.$lang['view_board_last_post'].'</th></tr>';
    		if(isset($forums[$key]))
    		{
    		    $count=0;
				foreach($forums[$key] as $value2) 
                {
					$c_posts = mysql_fetch_assoc($cunity->getDb()->query("SELECT COUNT(*) as counted FROM ".$cunity->getConfig("db_prefix")."forums_posts WHERE board_id=".$value2['board_id']));
					$c_topics = mysql_fetch_assoc($cunity->getDb()->query("SELECT COUNT(*) as counted FROM ".$cunity->getConfig("db_prefix")."forums_topics WHERE board_id=".$value2['board_id']));
						
					if($c_topics['counted'] > 0) {
						$last_post = mysql_fetch_assoc($cunity->getDb()->query("SELECT last_posttime FROM ".$cunity->getConfig("db_prefix")."forums_topics WHERE board_id=".$value2['board_id']." ORDER BY last_posttime DESC LIMIT 1"));
						$last_post = showDate('date_time',(int)$last_post['last_posttime'],true);
					}
					else
						$last_post = '-';

					$unread = mysql_fetch_assoc($cunity->getDb()->query("SELECT COUNT(*) as counted FROM ".$cunity->getConfig("db_prefix")."forums_unread WHERE user_id=".mysql_real_escape_string($_SESSION['userid'])." AND board_id=".$value2['board_id']));
			
					if($unread['counted'] > 0)
						$unread['counted'] = 'unread';
					else
						$unread['counted'] = 'read';

					if($cunity->getSaver()->admin())
					{
						$forums_list .= '
							<tr class="forum_line row_'.($count%2).'">
                                <td style="vertical-align: middle;">
                                    <img src="style/thecunity/img/forum_'.$unread['counted'].'.png" />                                    
                                </td>                				
								<td class="forum" style="width: 60%;">				                                            				    
									<div>                        																			        
										<a href="forums.php?fid='.$value2['board_id'].'" class="forums_headline">'.$value2['name'].'</a><br />
           								<span class="forums_description">'.$value2['description'].'</span>
                                        <a href="javascript: editBoard(\''.$value2['name'].'\',\''.$value2['description'].'\',\''.$value2['board_id'].'\');"><img src="style/'.$_SESSION['style'].'/img/edit.png" style="height: 12px;"/></a>
                                		<a href="javascript: deleteBoard(\''.$value2['board_id'].'\')"><img src="style/'.$_SESSION['style'].'/img/fail.png" style="height: 12px;" /></a>                                                                        										        								        								
        								<div class="clear"></div>    				    
        							</div>
        						</td>		
                                <td style="width: 15%; text-align: left; padding-right: 5px;">
                                    <div class="forum_posts">'.$c_posts['counted'].' '.$lang['view_board_posts'].' in <br />'.$c_topics['counted'].' '.$lang['view_board_thread'].'</div>
                                </td>		
        						<td class="lastpost" style="border-left:1px solid #fff;width: 20%;">'.$last_post.'</td>
        					</tr>';
					}
					else
					{
						$forums_list .= '					
							<tr class="forum_line row_'.($count%2).'">
                                <td style="vertical-align: middle;">                                    
                                    <img src="style/thecunity/img/forum_'.$unread['counted'].'.png" />
                                </td>                				
								<td class="forum" style="width: 60%;">				                                            				    
									<div>                        														        
										<a href="forums.php?fid='.$value2['board_id'].'" class="forums_headline">'.$value2['name'].'</a> 
										<br />
										<div class="forums_description">'.$value2['description'].'</div>										
										<div class="clear"></div>    				    
									</div>
								</td>	
                                <td style="width: 15%; text-align: right; padding-right: 5px;">
                                    <div class="forum_posts">'.$c_posts['counted'].' '.$lang['view_board_posts'].' in '.$c_topics['counted'].$lang['view_board_thread'].'</div>
                                </td>			
								<td class="lastpost" style="border-left:1px solid #fff;width: 20%;">'.$last_post.'</td>
							</tr>';
					}
					$count++;
				}
			}	
			if($cunity->getSaver()->admin())
    		{
    		$forums_list .= '
    			<tr class="forum_line">
    				<td class="forum" colspan="4" style="text-align: center;font-size:12px">
                        <button onclick="newBoard(\'board\', \''.$value1['board_id'].'\')" icon="ui-icon-plus" icon2="ui-icon-plus" text="1" class="jui-button">'.$lang['view_board_add_new_board'].'</button>
                    </td>
    			</tr>';
    		}
    
    		$forums_list .= '</table>';
				
	}
	
	if(count($categories)==0)
	    $forums_list = newCunityError($lang['view_board_no_forums']);
	$tplModEngine->Template('view_board');    	
    	
        $tplModEngine->Assign('edit_profile',$lang['profile_view_profile_edit']);
        $tplModEngine->Assign('edit_img',$lang['profile_edit_img_edit']);
        
    	
    	$tplModEngine->Assign('FORUMS', $forums_list);
    	$tplModEngine->Assign('view_board_new_posts',$lang['view_board_new_posts']);
    	$tplModEngine->Assign('view_board_no_posts',$lang['view_board_no_posts']);	
    	$tplModEngine->Assign('view_board_edit',$lang['view_board_edit']);
    	$tplModEngine->Assign('view_board_name',$lang['view_board_name']);
    	$tplModEngine->Assign('view_board_description',$lang['view_board_description']);
    	$tplModEngine->Assign('view_board_delete',$lang['view_board_delete_board']);
    	$tplModEngine->Assign('forum_back',$lang['forums_back']);    
    	$tplModEngine->Assign('view_board_delete_info',$lang['view_board_delete_info']);
    	$tplModEngine->Assign('view_board_add_new_forum',$lang['view_board_add_new_forum']);
    	$tplModEngine->Assign('view_board_add_new_board',$lang['view_board_add_new_board']);
?>