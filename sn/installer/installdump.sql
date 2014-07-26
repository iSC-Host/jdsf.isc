CREATE TABLE IF NOT EXISTS `cunity_allowed_filetypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ressource_id` int(11) NOT NULL,
  `ressource_name` varchar(10) NOT NULL,
  `userid` int(11) NOT NULL,
  `comment` text NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cunityId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_connected_cunities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cunityId` int(11) NOT NULL,
  `cunityname` varchar(200) NOT NULL,
  `cunityUrl` varchar(256) NOT NULL,
  `cunityPublicKey` text NOT NULL,
  `aes_key` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cunity_connected_users` (
  `localid` int(11) NOT NULL,
  `userhash` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `nickname` varchar(100) NOT NULL,
  `mail` varchar(200) NOT NULL,
  `sex` int(1) NOT NULL,
  `cunityId` int(11) NOT NULL,
  PRIMARY KEY (`localid`,`cunityId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `founder_id` int(11) NOT NULL,
  `birthday` tinyint(1) NOT NULL,
  `name` varchar(100) NOT NULL,
  `info` varchar(1000) NOT NULL,
  `place` varchar(50) NOT NULL,
  `img_file` varchar(250) NOT NULL DEFAULT './style/default/img/no_profile_img.jpg',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `privacy` tinyint(1) NOT NULL COMMENT '0 = private event, 1=public event',
  `eventid` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_events_guests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `status` enum('0','1','2','3') NOT NULL COMMENT '0=sent invitation, \r\n\r\n1=no, 2=maybe, 3=yes',
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_id` (`event_id`,`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(250) NOT NULL,
  `file_path` varchar(250) NOT NULL,
  `file_size` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `time` datetime DEFAULT NULL,
  `privacy` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=private,1=public',
  `description` varchar(200) NOT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_files_share` (
  `share_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned NOT NULL,
  `uploader_id` int(10) unsigned NOT NULL,
  `friend_id` int(10) unsigned NOT NULL,
  `time` datetime DEFAULT NULL,
  `remote` varchar(6) DEFAULT NULL,
  `cunityId` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`share_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_forums_boards` (
  `board_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `description` varchar(250) NOT NULL,
  `guest_readable` tinyint(1) NOT NULL,
  `guest_postable` tinyint(1) NOT NULL,
  `cat_id` tinyint(3) unsigned NOT NULL,
  `position` tinyint(3) unsigned NOT NULL,
  `flag` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`board_id`),
  UNIQUE KEY `board_id` (`board_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_forums_posts` (
  `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic_id` int(10) unsigned NOT NULL,
  `board_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_name` varchar(20) NOT NULL,
  `ip` varchar(25) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `posttime` int(11) NOT NULL,
  `edittime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_forums_topics` (
  `topic_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `board_id` int(10) unsigned NOT NULL,
  `subject` varchar(100) NOT NULL,
  `first_post_id` int(10) unsigned NOT NULL,
  `flag` tinyint(3) unsigned NOT NULL,
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `last_posttime` int(11) NOT NULL,
  PRIMARY KEY (`topic_id`),
  UNIQUE KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_forums_unread` (
  `user_id` int(10) unsigned NOT NULL,
  `board_id` int(10) unsigned NOT NULL,
  `topic_id` int(10) unsigned NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_forums_users` (
  `user_id` int(10) unsigned NOT NULL,
  `last_visit` int(11) NOT NULL,
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_forums_watch` (
  `topic_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_friendships` (
  `friendship_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `time` datetime DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0 = request sent, 1 = friends',
  `blocker` varchar(10) DEFAULT NULL,
  `remote` varchar(10) DEFAULT NULL,
  `cunityId` int(11) NOT NULL,
  PRIMARY KEY (`friendship_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_galleries_albums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` varchar(40) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(25) NOT NULL,
  `main_image` varchar(250) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` varchar(250) NOT NULL,
  `privacy` enum('0','1','2') NOT NULL COMMENT '0:me, 1:friends, 2:public',
  `photo_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `album_id` (`album_id`),
  KEY `id` (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_galleries_imgs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `album_id` varchar(40) NOT NULL,
  `file` varchar(250) NOT NULL,
  `title` varchar(200) NOT NULL,
  `size` int(11) NOT NULL,
  `uploader_id` int(10) unsigned NOT NULL,
  `uploader_ip` varchar(10) NOT NULL,
  `album_pos` smallint(5) unsigned NOT NULL COMMENT 'to sort the images',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `album_id` (`album_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_groups` (
  `groupid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(20) NOT NULL,
  UNIQUE KEY `groupid` (`groupid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

INSERT INTO `cunity_groups` (`groupid`, `groupname`) VALUES
(1, 'OWNER'),
(2, 'ADMIN'),
(3, 'REGISTERED'),
(4, 'GUEST'),
(5, 'INACTIVE'),
(6, 'BLOCKED'),
(7, 'NOT_ACTIVATED');

CREATE TABLE IF NOT EXISTS `cunity_invitation_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `nickname` varchar(20) NOT NULL,
  `code` varchar(32) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `ressource_name` varchar(20) NOT NULL,
  `ressource_id` int(11) NOT NULL,
  `dislike` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = like, 1 = dislike',
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cunityId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userid` (`userid`,`ressource_name`,`ressource_id`,`dislike`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(20) NOT NULL,
  `name` varchar(20) NOT NULL,
  `def` tinyint(1) NOT NULL,
  `target` varchar(100) NOT NULL,
  `menu_position` int(11) NOT NULL,
  `icon` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

INSERT INTO `cunity_menu` (`id`, `tag`, `name`, `def`, `target`, `menu_position`, `icon`) VALUES
(1, 'profile', 'profile', 1, 'profile.php', 5, 'style/[STYLE]/img/icons_profile.png'),
(2, 'pinboard', 'pinboard', 1, 'pinboard.php', 0, 'style/[STYLE]/img/icons_news.png'),
(3, 'messages', 'messages', 1, 'messages.php', 1, 'style/[STYLE]/img/inbox.png'),
(4, 'galleries', 'galleries', 1, 'galleries.php', 2, 'style/[STYLE]/img/icons_gallery.png'),
(5, 'friends', 'friends', 1, 'friends.php', 3, 'style/[STYLE]/img/icons_my_friends.png'),
(6, 'fileshare', 'fileshare', 1, 'fileshare.php', 4, 'style/[STYLE]/img/icons_file_share.png'),
(7, 'events', 'events', 1, 'events.php', 6, 'style/[STYLE]/img/icons_event.png'),
(8, 'forums', 'forums', 1, 'forums.php', 8, 'style/[STYLE]/img/forums.png'),
(9, 'members', 'members', 1, 'members.php', 7, 'style/[STYLE]/img/members.png');

CREATE TABLE IF NOT EXISTS `cunity_messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `message` text NOT NULL,
  `time` datetime DEFAULT NULL,
  `read` int(11) NOT NULL DEFAULT '0',
  `receiver_deleted` int(11) NOT NULL,
  `sender_deleted` int(11) NOT NULL,
  `remote` varchar(10) DEFAULT NULL,
  `cunityId` int(11) NOT NULL,
  `sentFrom` varchar(10) NOT NULL DEFAULT 'messages',
  `subject` varchar(100) NOT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `power` tinyint(1) NOT NULL COMMENT '1=module is on, 0 = off',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

INSERT INTO `cunity_modules` (`id`, `name`, `power`) VALUES
(1, 'fileshare', 1),
(2, 'galleries', 1),
(3, 'friends', 1),
(4, 'messages', 1),
(5, 'members', 1),
(6, 'forums', 1),
(7, 'pinboard', 1),
(8, 'events', 1),
(9, 'chat', 1);

CREATE TABLE IF NOT EXISTS `cunity_notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `receiver_id` int(11) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `time` datetime DEFAULT NULL,
  `read` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`notification_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_notifications_settings` (
  `userid` int(11) NOT NULL,
  `get_message` int(1) NOT NULL,
  `add_friend` int(1) NOT NULL,
  `accepted_friend` int(1) NOT NULL,
  `post_on_pinboard` int(1) NOT NULL,
  `status_comment` int(1) NOT NULL,
  `also_status_comment` int(1) NOT NULL,
  `invite_events` int(1) NOT NULL,
  `file_shared` int(1) NOT NULL,
  `post_forum` int(1) NOT NULL,
  `invite_event` int(1) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_online` (
  `userid` bigint(20) unsigned NOT NULL,
  `nickname` varchar(20) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `online` int(1) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_opencunity_settings` (
  `setting` varchar(100) NOT NULL,
  `value` varchar(2000) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

INSERT INTO `cunity_opencunity_settings` (`setting`, `value`, `id`) VALUES
('public_key', '', 1),
('private_key', '', 2),
('connected_success', '0', 3),
('cunityId', '0', 4),
('cunity_server_public_key', '', 5),
('aes_key', '', 6),
('cunity_aes', '', 7);

CREATE TABLE IF NOT EXISTS `cunity_pages` (
  `slug` varchar(50) NOT NULL,
  `title` text NOT NULL,
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_pinboard` (
  `status_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `pinboard_id` int(11) NOT NULL DEFAULT '0',
  `message` text,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` varchar(20) NOT NULL,
  `receiver` varchar(10) NOT NULL,
  `privacy` int(1) NOT NULL COMMENT '0 = friends, 1=friends of friends, 2 = all in this cunity, 3 = all',
  `remote` varchar(10) DEFAULT NULL,
  `cunityId` int(11) NOT NULL,
  `remoteId` int(11) DEFAULT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_privacy` (
  `userid` int(11) NOT NULL,
  `searching` int(1) NOT NULL,
  `messaging` int(1) NOT NULL,
  `friending` int(1) NOT NULL,
  `pinboard_viewing` int(1) NOT NULL,
  `profile_viewing` int(1) NOT NULL,
  `address_viewing` int(1) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_registration_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` enum('C','R','T','S') NOT NULL DEFAULT 'T',
  `importance` enum('M','O') NOT NULL COMMENT 'M=mandatory, O=optional',
  `def` enum('N','Y') NOT NULL,
  `active` enum('Y','N') NOT NULL,
  `edit` enum('Y','N') NOT NULL DEFAULT 'Y',
  `cat` varchar(10) NOT NULL,
  `value` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11;

INSERT INTO `cunity_registration_fields` (`id`, `name`, `type`, `importance`, `def`, `active`, `edit`, `cat`, `value`) VALUES
(1, 'password', 'T', 'M', 'Y', 'Y', 'N', '', ''),
(2, 'nickname', 'T', 'M', 'Y', 'Y', 'N', '', ''),
(3, 'email', 'T', 'M', 'Y', 'Y', 'N', '', ''),
(4, 'birthday', 'T', 'M', 'Y', 'Y', 'N', '', ''),
(5, 'firstname', 'T', 'M', 'Y', 'Y', 'Y', 'personal', ''),
(6, 'lastname', 'T', 'M', 'Y', 'Y', 'Y', 'personal', ''),
(7, 'town', 'T', 'O', 'Y', 'Y', 'Y', 'personal', ''),
(8, 'street', 'T', '', '', '', '', '', ''),
(9, 'tel1', 'T', '', '', '', '', '', ''),
(10, 'mobile', 'T', '', '', '', '', '', '');

CREATE TABLE IF NOT EXISTS `cunity_relationship_requests` (
  `relationship_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL,
  `relationship` int(11) NOT NULL,
  PRIMARY KEY (`relationship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_round_mails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(100) NOT NULL,
  `content` varchar(10000) NOT NULL,
  `date` date NOT NULL,
  `sent` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_settings` (
  `name` varchar(30) NOT NULL,
  `value` varchar(10000) NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `cunity_settings` (`name`, `value`) VALUES
('name', ''),
('slogan', ''),
('style', 'newcunity'),
('register_age', '14'),
('url', ''),
('contact_mail', ''),
('version', '1.1'),
('style_adminpanel', 'default'),
('user_space', '20'),
('notify_new_users', 'yes'),
('landing_body', 'In the admin panel you can change this text!'),
('image_download', '1'),
('registration_method', 'everybody'),
('header_body', ''),
('user_name', 'full_name'),
('language', 'en'),
('chat_with', 'friends'),
('files_dir', ''),
('friendstype', 'friends'),
('designswitch', '1'),
('allow_dislike', '1');

CREATE TABLE IF NOT EXISTS `cunity_users` (
  `userid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `userhash` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `profile_image` int(11) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `groupid` bigint(20) unsigned NOT NULL,
  `invisible` enum('Y','N') NOT NULL DEFAULT 'N',
  `last_ip` varchar(25) NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `vkey` varchar(12) NOT NULL,
  `vf_req` tinyint(1) NOT NULL DEFAULT '0',
  `verif_mail` int(1) NOT NULL,
  `SortMembersBy` varchar(20) NOT NULL DEFAULT 'nickname',
  `space` int(11) DEFAULT NULL,
  `lang` varchar(5) NOT NULL,
  `design` varchar(20) NOT NULL,
  `x1` int(11) NOT NULL,
  `x2` int(11) NOT NULL,
  `y1` int(11) NOT NULL,
  `y2` int(11) NOT NULL,
  `w` int(11) NOT NULL,
  `h` int(11) NOT NULL,
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cunity_users_details` (
  `userid` bigint(20) unsigned NOT NULL,
  `title` tinyint(1) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `birthday` date NOT NULL,
  `town` varchar(50) NOT NULL,
  `interested` varchar(50) NOT NULL,
  `about` text NOT NULL,
  `privacy` varchar(10) NOT NULL,
  `registered` datetime NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `relationship` int(1) NOT NULL,
  `relationship_partner` int(11) NOT NULL,
  `street` varchar(50) DEFAULT NULL,
  `plz` int(10) unsigned DEFAULT NULL,
  `tel1` varchar(20) DEFAULT NULL,
  `tel2` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `firstname` varchar(50) NOT NULL,
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;