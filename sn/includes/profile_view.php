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

$info = '';
$own = false;

$own = ($_SESSION['userid'] == $userid && !$remoteProfile);
$userhash = $profile->getUserData("userhash");

$replace['PROFILE_PIC'] = $profile->getProfileImage();

$replace['NAME'] = getUserName($data['userid']);

if ($cunity->getSetting('user_name') == 'nickname')
    $replace['NICKNAME_jui-button'] = ' <span style="font-weight: bold; font-size: 16px;">(' . $data['firstname'] . ' ' . $data['lastname'] . ')</span>';
else
    $replace['NICKNAME_jui-button'] = ' <span style="font-weight: bold; font-size: 16px;">(' . $data['nickname'] . ')</span>';

if ($data['title'] == 1)
    $replace['SEX'] = $lang['profile_view_male'];
else
    $replace['SEX'] = $lang['profile_view_female'];

$temp = strtotime($data['birthday']);
$age = time() - $temp;
$age = floor($age / 31536000); // 60 / 60 / 24 / 365
$replace['AGE'] = $age . ' ' . $lang['profile_view_years_old'];

if ($data['town'] != '')
    $replace['FROM'] = $lang['profile_view_from'] . ' ' . $data['plz'] . ' ' . $data['town'];
else
    $replace['FROM'] = "";

$replace['MEMBER_SINCE'] = $lang['profile_view_member_since'] . ' ' . date($_SESSION['date']['php']['date'], strtotime($data['registered']));

$replace['UPDATED_ON'] = $lang['profile_view_updated'] . ' ' . date($_SESSION['date']['php']['date'], strtotime($data['updated']));

$replace['BIRTHDAY'] = $lang['profile_view_born_on'] . ' ' . date($_SESSION['date']['php']['date'], strtotime($data['birthday']));


$replace['ID'] = $userid;

$replace['FULL_ID'] = $userhash;

$res = $cunity->getDb()->query("SELECT album_id,file,id FROM " . $cunity->getConfig("db_prefix") . "galleries_imgs WHERE uploader_id = '" . $userid . "' ORDER BY RAND() LIMIT 8");
$sample_photos = "";
while ($d = mysql_fetch_assoc($res)) {
    $finfo = pathinfo($d['file']);
    $sample_photos .= '<div class="photo_box"><a class="user_sample_photos" href="' . $cunity->getSetting("url") . '/' . $d['file'] . '" id="' . $d['id'] . '" cid="' . $cunity->getcunityId() . '"><img src="' . $cunity->getSetting("url") . '/' . $finfo['dirname'] . '/' . $finfo['filename'] . '_thumb.jpg" /></a></div>';
}
$replace['SAMPLE_PHOTOS'] = $sample_photos;


$fres = $cunity->getDb()->query("SELECT sender, receiver FROM " . $cunity->getConfig("db_prefix") . "friendships WHERE (sender = '" . $userid . "' OR receiver = '" . $userid . "') AND status = '1'");
$replace['FRIENDS_HEADER'] = $lang['profile_view_friends'] . ' (' . mysql_num_rows($fres) . ')';

$friends = "";
$i = 0;
while ($d = mysql_fetch_assoc($fres)) {
    if ($i == 12)
        break;
    if ($d['sender'] == $userid)
        $user = $d['receiver'];
    else
        $user = $d['sender'];
    $name = getUserName($user);
    $hash = getUserHash($user);
    if (file_exists('files/_avatars/' . $hash . '.jpg'))
        $img = 'files/_avatars/' . $hash . '.jpg';
    else
        $img = 'style/' . $_SESSION['style'] . '/img/no_avatar.jpg';

    $friends .= '<a href="profile.php?user=' . $hash . '"><img class="friends_image" src="' . $img . '" border="0" height="50" width="50" alt="' . $name . '" title="' . $name . '"/></a>';
    $i++;
}
$replace['FRIENDS'] = $friends;

$replace['NICK'] = $data['nickname'];

$replace['REGISTERED'] = showDate("date", $data['registered']);

$replace['UPDATED'] = showDate("date", $data['updated']);

$temp = strtotime($data['birthday']);
$age = time() - $temp;
$age = floor($age / 31536000); // 60 / 60 / 24 / 365
$replace['BIRTH'] = date($_SESSION['date']['php']['date'], $temp) . ' (' . $age . ')';

$replace['MAIL'] = $data['mail'];

if ($data['about'] != "") {
    define('ABOUT', true);
    $replace['ABOUT'] = $data['about'];
}

if (strlen($data['about']) > 200) {
    $about = '
        <div id="about_content">
            ' . substr($data['about'], 0, 200) . '
            <br /><p style="float: right;"><a href="javascript: showMore(\'about\')" class="more_link">(' . $lang['profile_more'] . ')</a></p>
        </div>
        <div id="about_more" style="display: none;">
            ' . $data['about'] . '
            <br /><p style="float: right;"><a href="javascript: showLess(\'about\')" class="more_link">(' . $lang['profile_less'] . ')</a></p>
        </div>';
} else {
    $about = '
        <div id="about_content">
            ' . $data['about'] . '            
        </div>';
}
$replace['ABOUT_BOXES'] = $about;

if ($data['interested'] != "" && $interested = json_decode($data['interested'], true)) {
    define('INTERESTED', true);
    $inter = "";
    foreach ($interested AS $i)
        $inter .= $lang['profile_view_' . $i] . ',';

    if (count($interested) > 1)
        $inter = substr($inter, 0, -1);
}
else
    $inter = $lang['profile_view_' . $data['interested']];
$replace['INTERESTED'] = $inter;

$replace['FULL_NAME'] = $data['firstname'] . " " . $data['lastname'];


if ($data['relationship'] != '0') {
    define('RELATIONSHIP', true);
    switch ($data['relationship']) {
        case '1':
            $rel = $lang['profile_view_in_relationship'];
            break;

        case '2':
            $rel = $lang['profile_view_single'];
            break;

        case '3':
            $rel = $lang['profile_view_married'];
            break;

        case '4':
            $rel = $lang['profile_view_engaged'];
            break;

        case '0':
            define('RELATIONSHIP', false);
            break;
    }
    if ($data['relationship_partner'] != 0 && $data['relationship'] != 2)
        $rel .= '&nbsp;' . $lang['profile_view_with'] . '&nbsp;<a href="profile.php?user=' . getUserHash($data['relationship_partner']) . '">' . getUserName($data['relationship_partner']) . '</a>';
    $replace['RELATIONSHIP'] = $rel;
}

$count = 0;
$res = $cunity->getDb()->query("SELECT * FROM " . $cunity->getConfig("db_prefix") . "registration_fields WHERE name = 'tel1' OR name = 'mobile' OR name = 'town' OR name = 'street'");
$contact = "";
while ($profileData = mysql_fetch_assoc($res)) {
    $resData = $cunity->getDb()->query("SELECT " . $profileData['name'] . " FROM " . $cunity->getConfig("db_prefix") . "users_details WHERE userid = '$userid'");
    $detailData = mysql_fetch_assoc($resData);
    if ($detailData[$profileData['name']] != "") {
        $contact .= '<div class="info_line ' . $count % 2 . '">';
        if ($profileData['def'] == 'Y') {
            $contact .= '<div class="info_label">' . $lang['profile_view_' . $profileData['name']] . ':</div>';
        } else {
            $contact .= '<div class="info_label">' . $profileData['name'] . ':</div>';
        }
        if (!checkPrivacy($userid, $_SESSION['userid'], 'address_viewing')) {
            $contact .= '<div class="info_value"><img src="style/' . $_SESSION['style'] . '/img/lock.png" /></div>';
        } else {
            $contact .= '<div class="info_value" id="' . $profileData['name'] . '">' . $detailData[$profileData['name']] . '</div>';
        }
        $contact .= '</div>';
    }
    $count++;
}

$replace['CONTACT'] = $contact;
$replace['PINBOARD_VIEW'] = checkPrivacy($userid, $_SESSION['userid'], 'pinboard_viewing');



$count = 0;
$personal = "";
$res = $cunity->getDb()->query("SELECT * FROM " . $cunity->getConfig("db_prefix") . "registration_fields WHERE active = 'Y' AND edit = 'Y' AND name != 'firstname' AND name != 'lastname' AND name != 'tel1' AND name != 'mobile' AND name != 'town' AND name != 'street'");
if (mysql_num_rows($res) != 0) {
    if ($own)
        define('EXTRA_OWN', true);
    else
        define('EXTRA', true);
    while ($profileData = mysql_fetch_assoc($res)) {
        if ($profileData['name'] == 'birthday') {
            $resData = $cunity->getDb()->query("SELECT DATE_FORMAT(birthday, '%d.%m.%Y') AS birthday FROM " . $cunity->getConfig("db_prefix") . "users_details WHERE userid = '$userid'");
        } else {
            $resData = $cunity->getDb()->query("SELECT " . $profileData['name'] . " FROM " . $cunity->getConfig("db_prefix") . "users_details WHERE userid = '" . $userid . "'");
        }
        $detailData = mysql_fetch_assoc($resData);
        echo mysql_error();
        if ($detailData[$profileData['name']] != "") {
            $personal .= '<div class="info_line ' . $count % 2 . '">';
            if ($profileData['def'] == 'Y') {
                $personal .= '<div class="info_label">' . $lang['profile_view_' . $profileData['name']] . ':</div>';
            } else {
                $personal .= '<div class="info_label">' . $profileData['name'] . ':</div>';
            }
            if (!checkPrivacy($userid, $_SESSION['userid'], 'profile_viewing')) {
                $personal .= '<div class="info_value"><img src="style/' . $_SESSION['style'] . '/img/lock.png" /></div>';
            } else {
                if ($profileData['type'] == 'C') {
                    $valueCheckbox = json_decode($detailData[$profileData['name']], true);
                    if (count($valueCheckbox) == 1) {
                        $valueCheckbox[0] . '</div>';
                    } else {
                        foreach ($valueCheckbox AS $values) {
                            $valuesCheckbox .= $values . ', ';
                        }
                        $valuesCheckbox = substr($valuesCheckbox, 0, -2);
                        $personal .= '<div class="info_value" id="' . $profileData['name'] . '"><ul class="info_inner_list">' . $valuesCheckbox . '</ul></div>';
                    }
                } else {
                    $personal .= '<div class="info_value" id="' . $profileData['name'] . '">' . $detailData[$profileData['name']] . '</div>';
                }
            }
            $personal .= '</div>';
        }
        $count++;
    }
} else {
    if (OWN)
        define('EXTRA', false);
    else
        define('EXTRA', false);
}

$replace['ADDED'] = $personal;

if ($personal == '-' || $personal == "") {
    $extra_boxes = '<div class="info_line">' . $lang['profile_view_no_extra'] . '</div>';
} else {
    $extra_boxes = '
        <div id="contact_content" class="small">
            ' . mb_stristr($personal, '<div class="info_line 1"', true) . '
            <br /><p style="float: right;"><a href="javascript: showMore(\'extra\')" class="more_link" style="font-size: 12px;">(' . $lang['profile_more'] . ')</a></p>
        </div>
        <div id="contact_more" style="display: none;" class="small">
            ' . $personal . '
            <br /><p style="float: right;"><a href="javascript: showLess(\'extra\')" class="more_link" style="font-size: 12px;">(' . $lang['profile_less'] . ')</a></p>
        </div>';
}

$replace['EXTRA_BOXES'] = $extra_boxes;

if ($cunity->getFriender()->getFriendshipStatus($ownId, $userid, $cunityId) === 0) {
    $addFriendText = $lang['profile_view_sent_request'];
    $addFriendIcon = 'ui-icon-clock';
    $friendstatus = 'sentrequest';
} else if ($cunity->getFriender()->getFriendshipStatus($ownId, $userid, $cunityId) === 3) {
    $addFriendText = $lang['profile_view_respond_request'];
    $addFriendIcon = 'ui-icon-help';
    $friendstatus = 'receivedrequest';
} else if ($cunity->getFriender()->isFriend($ownId, $userid, $cunityId)) {
    $addFriendText = $lang['friends_friends'];
    $addFriendIcon = 'ui-icon-person';
    $friendstatus = 'friends';
} else if (!$cunity->getFriender()->isFriend($ownId, $userid, $cunityId)){
    $addFriendText = $lang['profile_view_add_as_friend'];
    $addFriendIcon = 'ui-icon-plus';
    $friendstatus = 'nofriends';
}

$res = $cunity->getDb()->query("SELECT COUNT(*) FROM " . $cunity->getConfig("db_prefix") . "galleries_albums WHERE user_id = '" . $userid . "'");
$galleries_count = mysql_fetch_assoc($res);

$tplEngine->Assign('TITLE', $replace['NAME']);

$tplEngine->Template('profile_view');
if (!$remoteProfile) {
    $tplEngine->Assign(array(
        "REMOTE" => 0,
        "USERDATA" => "",
        "CUNITYID" => 0
    ));
}

$tplEngine->Assign($replace);
$tplEngine->Assign('PRIVACY', checkPrivacy($userid, $_SESSION['userid'], 'profile_viewing'));

$tplEngine->Assign('PINBOARD_ID', $userid);
if (OWN && !$remoteProfile)
    $tplEngine->Assign('PINBOARD_RECEIVER', "main");
else
    $tplEngine->Assign('PINBOARD_RECEIVER', "friend");

$tplEngine->Assign('addFriendText', $addFriendText);
$tplEngine->Assign('addFriendIcon', $addFriendIcon);
$tplEngine->Assign('friendstatus', $friendstatus);

$tplEngine->Assign('profile_view_profile_edit', $lang['profile_view_profile_edit']);
$tplEngine->Assign('profile_view_my_gallery', $lang['profile_view_my_gallery']);
$tplEngine->Assign('profile_view_galleries', $lang['profile_view_galleries']);
$tplEngine->Assign('profile_view_friends_of', $lang['profile_view_friends_of']);
$tplEngine->Assign('profile_view_friends_options', $lang['profile_view_friends_options']);
$tplEngine->Assign('profile_view_his_galleries', $lang['profile_view_his_galleries']);
$tplEngine->Assign('profile_view_send_message', $lang['profile_view_send_message']);
$tplEngine->Assign('profile_view_lastname', $lang['profile_view_lastname']);
$tplEngine->Assign('profile_view_firstname', $lang['profile_view_firstname']);
$tplEngine->Assign('profile_view_town', $lang['profile_view_city']);
$tplEngine->Assign('profile_view_street', $lang['profile_view_street']);
$tplEngine->Assign('profile_view_birthday', $lang['profile_view_dob']);
$tplEngine->Assign('profile_view_email', $lang['profile_view_email']);
$tplEngine->Assign('profile_view_chat', $lang['profile_view_chat']);
$tplEngine->Assign('profile_view_mobile', $lang['profile_view_mobile']);
$tplEngine->Assign('profile_view_name', $lang['profile_view_name']);
$tplEngine->Assign('profile_view_nickname', $lang['profile_view_nick']);
$tplEngine->Assign('profile_view_personal', $lang['profile_view_personal']);
$tplEngine->Assign('profile_view_registered', $lang['profile_view_registered']);
$tplEngine->Assign('profile_view_contact', $lang['profile_view_contact']);
$tplEngine->Assign('profile_view_tel1', $lang['profile_view_tel1']);
$tplEngine->Assign('profile_view_interested', $lang['profile_view_interested']);
$tplEngine->Assign('profile_view_men', $lang['profile_view_men']);
$tplEngine->Assign('profile_view_woman', $lang['profile_view_woman']);
$tplEngine->Assign('profile_view_about', $lang['profile_view_about']);
$tplEngine->Assign('profile_view_edit_img', $lang['profile_view_edit_img']);
$tplEngine->Assign('profile_view_updated', $lang['profile_view_updated']);
$tplEngine->Assign('profile_view_overview', $lang['profile_view_overview']);
$tplEngine->Assign('profile_view_relationship', $lang['profile_view_relationship']);
$tplEngine->Assign('profile_view_extra', $lang['profile_view_extra']);
$tplEngine->Assign('profile_view_edit', $lang['profile_view_edit']);
$tplEngine->Assign('profile_view_save', $lang['profile_view_save']);
$tplEngine->Assign('profile_view_cancel', $lang['profile_view_cancel']);
$tplEngine->Assign('profile_view_sent_request', $lang['profile_view_sent_request']);
$tplEngine->Assign('profile_view_pinboard', $lang['profile_view_pinboard']);
$tplEngine->Assign('profile_view_with', $lang['profile_view_with']);
$tplEngine->Assign('friends_add_friend', $lang['friends_add_as_friend']);
$tplEngine->Assign('friends_block_friend', $lang['friends_block_friend']);
$tplEngine->Assign('friends_delete_friend', $lang['friends_delete_friend']);
$tplEngine->Assign('friends_send_request', $lang['friends_send_request']);
$tplEngine->Assign('friends_cancel', $lang['friends_cancel']);
$tplEngine->Assign('friends_add_info', $lang['friends_add_info']);
$tplEngine->Assign('friends_respond_info', $lang['friends_respond_info']);
$tplEngine->Assign('profile_view_respond_to_request', $lang['friends_respond_to_request']);
$tplEngine->Assign('friends_confirm_request', $lang['friends_confirm_request']);
$tplEngine->Assign('friends_ignore_request', $lang['friends_ignore']);
$tplEngine->Assign('friends_block', $lang['friends_block_this_person']);
$tplEngine->Assign('friends_remove_request', $lang['friends_remove_request']);
$tplEngine->Assign('profile_view_more_photos', $lang['profile_view_more_photos']);
$tplEngine->Assign('galleries_close', $lang['galleries_close']);
$tplEngine->Assign('pinboard_share_settings', $lang['pinboard_share_settings']);
$tplEngine->Assign('pinboard_delete_status', $lang['pinboard_confirm_delete_status']);
$tplEngine->Assign('pinboard_delete_comment', $lang['pinboard_confirm_delete_comment']);
$tplEngine->Assign('pinboard_status_watermark', $lang['pinboard_status_watermark_profile']);
$tplEngine->Assign('pinboard_comment_watermark', $lang['pinboard_comments_watermark']);
$tplEngine->Assign('pinboard_post', $lang['pinboard_post']);
$tplEngine->Assign('pinboard_more', $lang['pinboard_more']);

if ($cunity->getModule('messages') && checkPrivacy($userid, $_SESSION['userid'], 'messaging') && $userid != $_SESSION['userid'])
    define('SEND_MESSAGE_BOOL', true);
else
    define('SEND_MESSAGE_BOOL', false);
if ($cunity->getModule('friends') && checkPrivacy($userid, $_SESSION['userid'], 'friending') && !OWN)
    $tplEngine->Assign('ADD_FRIEND', $link_add_as_friend);
else
    $tplEngine->Assign('ADD_FRIEND', '');
if (!$own && checkPrivacy($userid, $_SESSION['userid'], 'address_viewing'))
    define('SHOW_ADDRESS', true);
else
    define('SHOW_ADDRESS', true);
if (is_online($userid) && !OWN && $cunity->getModule('chat'))
    $tplEngine->Assign('CHAT_BOOL', true);
else
    $tplEngine->Assign('CHAT_BOOL', false);

$tplEngine->Assign('STATUS_ID', 0);
$tplEngine->show();
?>