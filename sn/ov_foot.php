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

$timeoutFriendsOnline = '10000';
$timeoutNotificationOnline = '30000';


if (LOGIN) {
    //Friend Suggestions
    $friends = array();
    $friendslist = "";
    $messages_html_rows = "";
    $friends = getFriendList($_SESSION['userid'], 1);
    $sentRequests = getFriendList($_SESSION['userid'], 0);
    if (count($friends) > 0) {
        $friendslist = '(';
        for ($i = 0; $i < count($friends); $i++) {
            if ($i < count($friends) - 1)
                $friendslist .= 'sender = ' . $friends[$i] . ' OR receiver = ' . $friends[$i] . ' OR ';
            else
                $friendslist .= 'sender = ' . $friends[$i] . ' OR receiver = ' . $friends[$i];
        }
        $friendslist .= ')  AND';
    }
    $query = "SELECT sender, receiver FROM " . $cunity->getConfig("db_prefix") . "friendships WHERE " . $friendslist . " sender != '" . $_SESSION['userid'] . "' AND receiver != '" . $_SESSION['userid'] . "'";
    $res = $cunity->getDb()->query($query);
    $i = 0;
    while ($data = mysql_fetch_assoc($res)) {
        if (((in_array($data['receiver'], $friends) && !in_array($data['sender'], $friends)) || (!in_array($data['receiver'], $friends) && in_array($data['sender'], $friends)) && (!in_array($data['receiver'], $sentRequests) && !in_array($data['sender'], $sentRequests))) && $i < 3) {
            if (in_array($data['sender'], $friends))
                $user = $data['receiver'];
            elseif (in_array($data['receiver'], $friends))
                $user = $data['sender'];
            $i++;
            $friend_suggestions .= '<div style="padding: 3px 0px; border-bottom: 1px solid #ccc" id="suggest_' . $user . '">';
            $friend_suggestions .= '<a href="profile.php?user=' . getUserHash($user) . '" style="display: inline-block; float: left;">' . getSmallAvatar($user, 40) . '</a>';
            $friend_suggestions .= '<div style="display: inline-block; float: left;">';
            $friend_suggestions .= '<a href="profile.php?user=' . getUserHash($user) . '" style="font-weight: bold; display: block;">' . getUserName($user) . '</a>';
            if (!isFriend($user)) {
                $friend_suggestions .= '<a href="javascript:void(0);" id="' . $user . '" class="addasfriend" style="font-size: 12px; margin-top: 9px; color: #000000; display: block;">' . $lang['friends_add_as_friend'] . '</a>';
            }
            $friend_suggestions .= '</div><div class="clear"></div>';
            $friend_suggestions .= '</div>';
        }
    }
    if ($i == 0)
        $friend_suggestions = '<br /><i>' . $lang['friends_no_suggestions'] . '</i>';

    $events = "";
    $q = "SELECT * FROM " . $cunity->getConfig("db_prefix") . "events WHERE id IN (SELECT " . $cunity->getConfig("db_prefix") . "events_guests.event_id FROM " . $cunity->getConfig("db_prefix") . "events_guests WHERE " . $cunity->getConfig("db_prefix") . "events_guests.userid = '" . $_SESSION['userid'] . "' AND ((SELECT " . $cunity->getConfig("db_prefix") . "events.end_date FROM " . $cunity->getConfig("db_prefix") . "events WHERE " . $cunity->getConfig("db_prefix") . "events.id = " . $cunity->getConfig("db_prefix") . "events_guests.event_id)>CURDATE()) OR ((SELECT " . $cunity->getConfig("db_prefix") . "events.end_date FROM " . $cunity->getConfig("db_prefix") . "events WHERE " . $cunity->getConfig("db_prefix") . "events.id = " . $cunity->getConfig("db_prefix") . "events_guests.event_id) = CURDATE() AND (SELECT " . $cunity->getConfig("db_prefix") . "events.end_time FROM " . $cunity->getConfig("db_prefix") . "events WHERE " . $cunity->getConfig("db_prefix") . "events.id = " . $cunity->getConfig("db_prefix") . "events_guests.event_id) > NOW())) ORDER BY start_date LIMIT 3";
    $res = $cunity->getDb()->query($q);
    while ($data = mysql_fetch_assoc($res)) {
        if (strlen($data['name']) > 20)
            $name = substr($data['name'], 0, 17) . '...';
        else
            $name = $data['name'];
        $events .= '<div style="padding: 3px 0px; border-bottom: 1px solid #ccc">';
        $events .= '<a href="events.php?e=' . getEventHash($data['id']) . '" style="display: inline-block; float: left;"><img src="' . $data['img_file'] . '" width="40px" height="40px" /></a>';
        $events .= '<div style="display: inline-block; float: left; padding-left: 5px;">';
        $events .= '<a href="events.php?e=' . getEventHash($data['id']) . '" style="font-weight: bold; display: block;" title="' . $data['name'] . '">' . $name . '</a>';
        $events .= '<a class="suggestion_time">' . date($_SESSION['date']['php']['date'], strtotime($data['start_date'])) . '</a>';
        $events .= '</div>';
        $events .= '<div class="clear"></div>';
        $events .= '</div>';
    }
    if (mysql_num_rows($res) == 0)
        $events = '<br /><i>' . $lang['events_no_events'] . '</i>';

    if (rand() % 2 == 0 && $i > 0)
        $sidebar_list = '<a>' . $lang['friends_you_may_know'] . '</a>' . $friend_suggestions;
    else
        $sidebar_list = '<a>' . $lang['events_upcoming_events'] . '</a>' . $events;
}
else
    $sidebar_list = "";

$tplEngine->Template('sidebar');
if ($cunity->getSaver()->login()) {
    $tplEngine->Assign('timeoutFriendsOnline', $timeoutFriendsOnline);
    $tplEngine->Assign('timeoutNotificationOnline', $timeoutNotificationOnline);
    $tplEngine->Assign('FRIENDSUGGESTIONS', $sidebar_list);
}

$tplEngine->Assign('MSG', $msg);
$tplEngine->Assign('registration_method', $cunity->getSetting('registration_method'));
$tplEngine->Assign('module', $cunity->getActiveModules());

$tplEngine->Template('overall_footer');
$tplEngine->Assign('LANG', $_SESSION['language']);
$tplEngine->Assign('LANGUAGES', $languagesOptions);

$tplEngine->Assign('VERSION', $cunity->getSetting('version'));
$tplEngine->Assign('ONLINE', who_is_online());
$tplEngine->Assign('ONLINE_USER_COUNT', who_is_online_Count());
$tplEngine->Assign('timeoutFriendsOnline', $timeoutFriendsOnline);
$tplEngine->Assign('timeoutNotificationOnline', $timeoutNotificationOnline);
$tplEngine->show();
unset($tplEngine);
unset($db);
?>