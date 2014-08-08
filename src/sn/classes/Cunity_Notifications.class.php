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

class Cunity_Notifications {

    private $cunity = null;
    private $lang = array();

    public function Cunity_Notifications(Cunity $cunity) {
        $this->cunity = $cunity;
        $this->lang = $this->cunity->getLang();
    }

    public function addNotification($type, $userid, $from, $id = 0, $opt = 0, $remote = false, $cunityId = 0) {
        if (($userid != $from && !$remote) || $remote) {
            switch ($type) {
                case 'status_comment':
                    $data = array('type' => 'status_comment', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_status_comment'], 'link' => 'pinboard.php?c=link&id=' . $id);
                    $link = 'pinboard.php?c=link&id=' . $id;
                    break;

                case 'pinboard_status_comment':
                    $data = array('type' => 'pinboard_status_comment', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_pinboard_status_comment'], 'link' => 'pinboard.php?c=link&id=' . $id);
                    $link = 'pinboard.php?c=link&id=' . $id;
                    break;

                case 'also_status_comment':
                    if ($from == $opt) {
                        if (getSex($from) == 1)
                            $data = array('type' => 'also_status_comment', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_also_status_comment_on_his'], 'link' => 'pinboard.php?c=link&id=' . $id);
                        else
                            $data = array('type' => 'also_status_comment', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_also_status_comment_on_her'], 'link' => 'pinboard.php?c=link&id=' . $id);
                    }
                    else
                        $data = array('type' => 'also_status_comment', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_also_status_comment_on'] . ' ' . getUserName($opt, $remote, $cunityId) . $this->lang['notification_subject_also_status_comment_status'], 'link' => 'pinboard.php?c=link&id=' . $id);
                    $link = 'pinboard.php?c=link&id=' . $id;
                    break;

                case 'image_comment':
                    $data = array('type' => 'image_comment', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_image_comment'], 'link' => 'galleries.php?c=show_album&id=' . $id);
                    $link = 'galleries.php?c=show_album&id=' . $id;
                    break;

                case 'also_image_comment':
                    if ($from == $opt) {
                        if (getSex($from) == 1)
                            $data = array('type' => 'also_image_comment', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_also_image_comment_on_his'], 'link' => 'galleries.php?c=show_album&id=' . $id);
                        else
                            $data = array('type' => 'also_image_comment', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_also_image_comment_on_her'], 'link' => 'galleries.php?c=show_album&id=' . $id);
                    }
                    else
                        $data = array('type' => 'also_status_comment', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_also_image_comment_on'] . ' ' . getUserName($opt, $remote, $cunityId) . $this->lang['notification_subject_also_image_comment_status'], 'link' => 'galleries.php?c=show_album&id=' . $id);
                    $link = 'galleries.php?c=show_album&id=' . $id;
                    break;

                case 'add_friend':
                    $data = array('type' => 'add_friend', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_add_friend'], 'link' => 'profile.php?user=' . getUserhash($from));
                    $link = 'profile.php?user=' . getUserHash($from);
                    break;

                case 'accepted_friend':
                    $data = array('type' => 'accepted_friend', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_accepted_friend'], 'link' => 'profile.php?user=' . getUserhash($from));
                    $link = 'profile.php?user=' . getUserHash($from);
                    break;

                case 'status_like':
                    $data = array('type' => 'status_like', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_status_like'], 'link' => 'pinboard.php?c=link&id=' . $id);
                    $link = 'pinboard.php?c=link&id=' . $id;
                    break;

                case 'get_message':
                    $data = array('type' => 'get_message', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_get_message'], 'link' => 'messages.php?c=viewmessage&msgid=' . $id);
                    $link = 'messages.php?c=conv&u=' . $cunityId . "-" . getUserHash($from);
                    break;

                case 'invite_event':
                    $data = array('type' => 'invite_event', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_invite_event'], 'link' => 'events.php?e=' . getEventHash($id));
                    $link = 'events.php?e=' . getEventHash($id);
                    break;

                case 'file_shared':
                    $data = array('type' => 'file_shared', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_file_shared'], 'link' => 'fileshare.php');
                    $link = 'fileshare.php';
                    break;

                case 'forum_post':
                    $data = array('type' => 'forum_post', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_forum_post'], 'link' => 'forums.php');
                    $link = 'forums.php';
                    break;

                case 'post_on_pinboard':
                    $data = array('type' => 'post_on_pinboard', 'message' => getUserName($from, $remote, $cunityId) . ' ' . $this->lang['notification_subject_post_pinboard'], 'link' => 'pinboard.php?c=link&id=' . $id);
                    $link = 'pinboard.php?c=link&id=' . $id;
                    break;
            }
            $data['remote'] = $remote;
            $data['from'] = $from;
            $data['cunityId'] = $cunityId;
            $subj = $data['message'];
            $message = $data['message'];
            $message .= '<br /><a href="' . $this->cunity->getSetting('url') . '/' . $link . '">' . $this->lang['notification_visit'] . ' ' . $this->cunity->getSetting('name') . '</a>';
            if ($this->checkSendNotification($type, $userid) && $this->insertNotification($userid, json_encode($data))) {                
                return $this->sendNotification($this->fetch_user_email($userid), getUserName($userid), $subj, $message);
            }
            else
                return true;
        }
    }

    public function getNotifications($userid) {
        $res = $this->cunity->getDb()->query("SELECT * FROM " . $this->cunity->getConfig("db_prefix") . "notifications WHERE receiver_id = '" . $userid . "' ORDER BY time DESC");
        $back = "";

        while ($data = mysql_fetch_assoc($res)) {
            $mdata = json_decode(stripslashes($data['message']), true);
            $cunityId = ($mdata['remote']) ? "-" . $mdata['cunityId'] : "";
            $this->cunity->getTemplateEngine()->createTemplate('notification', array(
                "USERHASH" => $cunityId . getUserHash($mdata['from'], $mdata['remote'], $mdata['cunityId']),
                "AVATAR" => getAvatarPath($mdata['from'], $mdata['remote'], $mdata['cunityId']),
                "LINK" => $mdata['link'],
                "MESSAGE" => $mdata['message'],
                "TIME" => showDate("date_time", $data['time'])
            ));
        }
        return $back;
    }

    public function markAllAsRead($userid) {
        $this->cunity->getDb()->query("UPDATE " . $this->cunity->getConfig("db_prefix") . "notifications SET `read` = 1 WHERE receiver_id = '" . $userid . "'");
    }

    private function checkSendNotification($type, $userid) {
        $res = $this->cunity->getDb()->query("SELECT " . $type . " FROM " . $this->cunity->getConfig("db_prefix") . "notifications_settings WHERE userid = " . (int) $userid);
        ;
        $data = mysql_fetch_assoc($res);
        if (mysql_num_rows($res) == 0)
            return true;
        else
            return (bool) $data[$type];
    }

    private function fetch_user_email($userid, $remote = false, $cunityId = 0) {
        if ($remote && $cunityId > 0)
            $r = $this->cunity->getDb()->query("SELECT mail FROM " . $this->cunity->getConfig("db_prefix") . "connected_users WHERE localid = " . $userid . " AND cunityId = " . $cunityId);
        else
            $r = $this->cunity->getDb()->query("SELECT mail FROM " . $this->cunity->getConfig("db_prefix") . "users WHERE userid = " . $userid);
        $d = mysql_fetch_assoc($r);
        return $d['mail'];
    }

    public function sendNotification($receiver, $receiver_name, $ssubject, $smessage) {
        return $this->cunity->getMailer()->sendmail($receiver, $receiver_name, $ssubject, $smessage);
    }

    public function insertNotification($to_id, $message) {
        $query = 'INSERT INTO ' . $this->cunity->getConfig("db_prefix") . 'notifications (' . $this->cunity->getConfig("db_prefix") . 'notifications.receiver_id,' . $this->cunity->getConfig("db_prefix") . 'notifications.message,' . $this->cunity->getConfig("db_prefix") . 'notifications.time,' . $this->cunity->getConfig("db_prefix") . 'notifications.read)VALUES (\'' . $to_id . '\', \'' . $message . '\',NOW(), 0)';
        $res = $this->cunity->getDb()->query($query);
        return (bool) $res;
    }

    function getHasError() {
        return $this->cunity->getMailer()->getHasError();
    }

    function getErrorMsg() {
        return $this->cunity->getMailer()->getErrorMsg();
    }

    public function __destruct() {
        
    }

}

?>