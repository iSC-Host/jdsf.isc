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
   
ob_start("ob_gzhandler");
require('ov_head.php');

	$cunity->getSaver()->login_required();
	$cunity->getSaver()->module_power();
	
	

	if(!isset($_GET['c']) || $_GET['c'] == 'list') {
		//
		$memberlist = '';
		$membersperpage = 10;
		$start = 0;
		$end = $membersperpage;
		$userid = $_SESSION['userid'];  

		if(isset($_GET['page']) && !empty($_GET['page'])) {
			$page = (int)$_GET['page'];
			$start = $page * $membersperpage;
		}
		
		if(isset($_GET['s']))
		{
		    $newSort = $_GET['s'];
            $cunity->getDb()->query('UPDATE '.$cunity->getConfig("db_prefix").'users SET SortMembersBy = \''.$newSort.'\' WHERE userid = '.$userid);
        }
	
		
		$sort = $cunity->getDb()->query('SELECT SortMembersBy FROM '.$cunity->getConfig("db_prefix").'users WHERE userid = '.$userid);
		
		$sorting = mysql_fetch_object($sort);
		$sort = $sorting->SortMembersBy;

		$res = $cunity->getDb()->query('
            SELECT 
                '.$cunity->getConfig("db_prefix").'users.userid, 
                '.$cunity->getConfig("db_prefix").'users.nickname, 
                '.$cunity->getConfig("db_prefix").'users_details.firstname, 
                '.$cunity->getConfig("db_prefix").'users_details.lastname, 
                '.$cunity->getConfig("db_prefix").'users_details.town, 
                '.$cunity->getConfig("db_prefix").'users_details.registered
		    FROM 
                '.$cunity->getConfig("db_prefix").'users 
            LEFT JOIN 
                '.$cunity->getConfig("db_prefix").'users_details ON '.$cunity->getConfig("db_prefix").'users.userid = '.$cunity->getConfig("db_prefix").'users_details.userid
		WHERE '.$cunity->getConfig("db_prefix").'users.groupid != 4 AND '.$cunity->getConfig("db_prefix").'users.groupid != 5 AND '.$cunity->getConfig("db_prefix").'users.groupid != 7 
		ORDER BY '.$sort.' ASC');

		if(mysql_num_rows($res) < 1)
			die(members_no_data);

		$i = $start;

		while($data = mysql_fetch_assoc($res)) {
			$i++;

			$memberlist .= '<tr>
			<td class="tab_value">'.$i.'</td>
			<td class="tab_value">
                <a href="profile.php?user='.getUserHash($data['userid']).'" title="'.$lang['members_call_profile'].'">'.$data['nickname'].'</a>
            ';
            if(file_exists('files/_profile_imgs/'.getUserHash($data['userid']).'.jpg'))
            {
                $memberlist .= '<img src="style/'.$_SESSION['style'].'/img/gallery.png" class="photo_available" id="'.$data['userid'].'">';
            }
            $memberlist .= '   
            <br class="clear"/>
            </td>
			<td class="tab_value">'.$data['firstname'].'</td>
			<td class="tab_value">'.$data['lastname'].'</td>
			<td class="tab_value">'.$data['town'].'</td>
			<td class="tab_value">'.date('d.m.Y, H:i', strtotime($data['registered'])).'</td>
			</tr>
			';
			if(file_exists('files/_profile_imgs/'.getUserHash($data['userid']).'.jpg'))
			{       
    			$memberlist .= '
    			<div id="preview_'.$data['userid'].'" style="display: none;" class="profile_preview">
                    <img src="files/_profile_imgs/'.getUserHash($data['userid']).'.jpg" class="profile_preview_image"/>
                </div>
                ';
            }
		}

		// Template
		$tplEngine->Assign('TITLE', $lang['memberlist_users']);
		$tplEngine->Template('memberlist');
		    
            		    
		    
    		$tplEngine->Assign('LIST', $memberlist);
    		$tplEngine->show();
	}

require('ov_foot.php');
ob_end_flush();
?>