<?php

class Cunity_Messenger {

    private $cunity = null;

    public function Cunity_Messenger(Cunity $cunity) {
        $this->cunity = $cunity;
        $this->lang = $this->cunity->getLang();
    }

    public function getAllConversations($ownId, $previewLength = 150) {
        $q = "SELECT * FROM " . $this->cunity->getConfig("db_prefix") . "messages WHERE (sender = " . $ownId . " AND (remote IS NULL OR remote = 'receiver')) OR (receiver = " . $ownId . " AND (remote IS NULL OR remote = 'sender')) ORDER BY time DESC";
        $res = $this->cunity->getDb()->query($q);
        $conversations = array();
        while ($data = mysql_fetch_assoc($res)) {
            if ($data['sender'] == $ownId)
                $data['friend'] = $data['receiver'];
            elseif ($data['receiver'] == $ownId)
                $data['friend'] = $data['sender'];

            if (isset($conversations[$data['friend']])) {
                if ($conversations[$data['friend']]['time'] < $data['time'])
                    $conversations[$data['friend']] = $data;
            }
            else
                $conversations[$data['friend']] = $data;
        }
        if (count($conversations) > 0) {
            foreach ($conversations AS $friend => $data) {
                if ($data['remote'] != NULL && $data['cunityId'] > 0) {
                    $remote = true;
                    $cunityId = $data['cunityId'];
                    $cunityIdTpl = $cunityId . '-';
                    $remoteRes = $this->cunity->getDb()->query("SELECT * FROM " . $this->cunity->getConfig("db_prefix") . "connected_users AS u, " . $this->cunity->getConfig("db_prefix") . "connected_cunities AS c WHERE u.cunityId = c.cunityId AND u.localid = '" . $friend . "'");
                    $remoteData = mysql_fetch_assoc($remoteRes);
                } else {
                    $remote = false;
                    $cunityId = 0;
                    $cunityIdTpl = "";
                    $remoteData = array();
                }
                $conversation .= $this->cunity->getTemplateEngine()->createTemplate('message_conversation', array(
                    "USERHASH" => getUserhash($friend, $remote, $cunityId),
                    "USERNAME" => getUserName($friend, $remote, (int) $cunityId),
                    "CUNITYID" => $cunityIdTpl,
                    "LAST_MSG" => str_replace(array('<br>', '<br/>', '<br />'), "", substr($data['message'], 0, $previewLength)),
                    "TIME" => showDate('date_time', $data['time']),
                    "AVATAR" => getSmallAvatar($friend, 60, $remote, $cunityId)
                ));
            }
            return $conversation;
        }
        else
            return newCunityError($this->lang['inbox_no_messages']);
    }

    public function deleteConversation($userid, $cunityId) {
        $res = $this->cunity->getDb()->query("SELECT message_id FROM " . $this->cunity->getConfig("db_prefix") . "messages WHERE cunityId = " . $cunityId . " AND ((sender = " . $_SESSION['userid'] . " AND receiver = " . $userid . " AND (remote = 'receiver' OR remote IS NULL)) OR (sender = " . $userid . " AND receiver = " . $_SESSION['userid'] . " AND (remote = 'sender' OR remote IS NULL))) ORDER BY time") or die(mysql_error());
        while ($data = mysql_fetch_assoc($res))
            $delRes[] = $this->deleteMessage($data['message_id']);
        return (!in_array(false, $DelRes));
    }

    public function returnOwnConversationMessage(array $data) {
        $message = $this->cunity->getTemplateEngine()->createTemplate('message_entry', array(
            "SENDER" => "own",
            "MESSAGE" => $data['message'],
            "TIME" => showDate('date_time', $data['time']),
            "AVATAR" => getSmallAvatar($data['sender'], 40),
            "CUNITYID" => $cunityTplId,
            "USERHASH" => getUserHash($data['sender']),
            "USERNAME" => getUserName($data['sender']),
            "MESSAGE_ID" => $data['message_id'],
            "sentFrom" => $this->lang['conversation_sent_from_messages'],
            "sentIcon" => 'script'
        ));
        return $message;
    }

    public function readConversation($ownId, $userid, $remote, $cunityId, $refresh = false) {
        $conversations = "";
        $cunityData = array();
        if (!$refresh) {
            $res = $this->cunity->getDb()->query("SELECT * FROM " . $this->cunity->getConfig("db_prefix") . "messages WHERE cunityId = " . $cunityId . " AND ((sender = " . $ownId . " AND receiver = " . $userid . " AND sender_deleted = 0 AND (remote = 'receiver' OR remote IS NULL)) OR (sender = " . $userid . " AND receiver = " . $ownId . " AND receiver_deleted = 0 AND (remote = 'sender' OR remote IS NULL))) ORDER BY time") or die(mysql_error());
        } else {
            $res = $this->cunity->getDb()->query("SELECT * FROM " . $this->cunity->getConfig("db_prefix") . "messages WHERE cunityId = " . $cunityId . " AND ((sender = " . $ownId . " AND receiver = " . $userid . " AND sender_deleted = 0 AND (remote = 'receiver' OR remote IS NULL)) OR (sender = " . $userid . " AND receiver = " . $ownId . " AND receiver_deleted = 0 AND (remote = 'sender' OR remote IS NULL))) AND message_id > " . (int) $_SESSION['max_message_id-' . $userid] . " ORDER BY time") or die(mysql_error());
        }

        while ($data = mysql_fetch_assoc($res)) {
            if ($data['remote'] != NULL && $cunityId > 0) {
                $remote = true;
                $cunityIdTpl = $cunityId . '-';
                $remoteRes = $this->cunity->getDb()->query("SELECT * FROM " . $this->cunity->getConfig("db_prefix") . "connected_users AS u, " . $this->cunity->getConfig("db_prefix") . "connected_cunities AS c WHERE u.cunityId = c.cunityId AND u.localid = " . $userid . " AND u.cunityId = " . $cunityId . "");
                $cunityData = mysql_fetch_assoc($remoteRes);
            } else {
                $remote = false;
                $cunityIdTpl = "";
                $cunityData = array();
            }
            if ($data['sentFrom'] == "messages") {
                $sent = "script";  //Set jqueryUi-Icon ui-icon-script
            } elseif ($data['sentFrom'] == "chat") {
                $sent = "comment"; //Set jqueryUi-Icon ui-icon-comment
            }

            if ($data['sender'] == $ownId && ($data['remote'] == "receiver" || $data['remote'] == NULL)) {
                $conversations .= $this->cunity->getTemplateEngine()->createTemplate('message_entry', array(
                    "SENDER" => "own",
                    "MESSAGE" => $data['message'],
                    "TIME" => showDate('date_time', $data['time']),
                    "AVATAR" => getSmallAvatar($data['sender'], 40),
                    "CUNITYID" => "",
                    "USERHASH" => getUserHash($data['sender']),
                    "USERNAME" => getUserName($data['sender']),
                    "MESSAGE_ID" => $data['message_id'],
                    "sentFrom" => $this->lang['conversation_sent_from_' . $data['sentFrom']],
                    "sentIcon" => $sent
                ));
            } else {
                $conversations .= $this->cunity->getTemplateEngine()->createTemplate('message_entry', array(
                    "SENDER" => "other",
                    "MESSAGE" => $data['message'],
                    "TIME" => showDate('date_time', $data['time']),
                    "AVATAR" => getSmallAvatar($data['sender'], 40, $remote, $cunityId),
                    "CUNITYID" => $cunityIdTpl,
                    "USERHASH" => getUserHash($data['sender'], $remote, $cunityId),
                    "USERNAME" => getUserName($data['sender'], $remote, $cunityId),
                    "MESSAGE_ID" => $data['message_id'],
                    "sentFrom" => $this->lang['conversation_sent_from_' . $data['sentFrom']],
                    "sentIcon" => $sent
                ));
            }
        }
        $resMax = $this->cunity->getDb()->query("SELECT MAX(message_id) FROM " . $this->cunity->getConfig("db_prefix") . "messages WHERE cunityId = " . $cunityId . " AND ((sender = " . $ownId . " AND receiver = " . $userid . " AND (remote = 'receiver' OR remote IS NULL)) OR (sender = " . $userid . " AND receiver = " . $ownId . " AND (remote = 'sender' OR remote IS NULL)))");
        $d = mysql_fetch_assoc($resMax);
        $_SESSION['max_message_id-' . $userid] = $d["MAX(message_id)"];
        return $conversations;
    }

    private function getMessageData($messageId) {
        $res = $this->cunity->getDb()->query("SELECT * FROM " . $this->cunity->getConfig("db_prefix") . "messages WHERE message_id = " . (int) $messageId . "");
        return mysql_fetch_assoc($res);
    }

    public function deleteMessage($messageId) {
        $data = $this->getMessageData($messageId);
        if (($data['remote'] != NULL && ($data['cunityId'] > 0 || $data['cunityId'] != $this->cunity->getcunityId())) || ($data['sender_deleted'] == 1 && $data['sender'] != $_SESSION['userid']) || ($data['receiver_deleted'] == 1 && $data['receiver'] != $_SESSION['userid']))
            return $this->cunity->getDb()->query("DELETE FROM " . $this->cunity->getConfig("db_prefix") . "messages WHERE message_id = " . (int) $messageId . "");
        else if ($data['sender'] == $_SESSION['userid'])
            return $this->cunity->getDb()->query("UPDATE " . $this->cunity->getConfig("db_prefix") . "messages SET sender_deleted = 1 WHERE message_id = " . (int) $messageId . "");
        else
            return $this->cunity->getDb()->query("UPDATE " . $this->cunity->getConfig("db_prefix") . "messages SET receiver_deleted = 1 WHERE message_id = " . (int) $messageId . "");
    }

    public function sendMessage($sender, $receiver, $message, $cunityId, $remoteUser = "none") {
        $msgData = $this->insertMessage($sender, $receiver, $message, $remoteUser, $cunityId, "messages");
        if ($cunityId == 0) {            
            if(!is_online($receiver))
                if(!$this->cunity->getNotifier()->addNotification('get_message', $receiver, $sender, $msgData['message_id']))
                    return false;            
            return $msgData;
        }elseif ($cunityId > 0 && $remoteUser != "sender") {
            require_once 'Cunity_Connector.class.php';
            $connector = new Cunity_Connector($this->cunity);
            if ($connector->sendMessage($sender, $receiver, $message, $cunityId))
                return $msgData;
            else
                return false;
        }
    }

    public function sendChatMessage($sender, $receiver, $message, $cunityId, $remoteUser = "none") {
        if ($msgData = $this->insertMessage($sender, $receiver, $message, $remoteUser, $cunityId, "chat"))
            return $msgData;
    }

    private function insertMessage($sender, $receiver, $message, $remoteUser, $cunityId, $sentFrom) {
        if ($remoteUser == "none") {
            $res = $this->cunity->getDb()->query("INSERT INTO " . $this->cunity->getConfig("db_prefix") . "messages(
				sender,
				receiver,
				message,
				cunityId,
				time,
				`read`,
				remote,
				sentFrom
			)VALUES(
				" . $sender . ",
				" . $receiver . ",
				'" . $message . "',
				" . $cunityId . ",
				NOW(),
				0,
				NULL,
				'" . $sentFrom . "'
				)") or die(mysql_error());
        } else {
            $res = $this->cunity->getDb()->query("INSERT INTO " . $this->cunity->getConfig("db_prefix") . "messages(
				sender,
				receiver,
				message,
				cunityId,
				time,
				`read`,
				remote,
				sentFrom
			)VALUES(
				" . $sender . ",
				" . $receiver . ",
				'" . $message . "',
				" . $cunityId . ",
				NOW(),
				0,
				'" . $remoteUser . "',
				'" . $sentFrom . "'
				)") or die(mysql_error());
        }

        if ($res)
            return mysql_fetch_assoc($this->cunity->getDb()->query("SELECT * FROM " . $this->cunity->getConfig("db_prefix") . "messages WHERE message_id = " . mysql_insert_id()));
        return false;
    }

}

?>