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

require_once 'Cunity_Registration.class.php';
$register = new Cunity_Registration($cunity);
/* REGISTER NEW USER */
if (!isset($_GET['c']) || $_GET['c'] == 'register') {
    if ($cunity->getSetting('registration_method') == 'code' && !isset($_POST['code']) && !isset($_GET['code'])) {
        $tplEngine->Template('register_code');
        $tplEngine->Assign('MSG', "");
    } elseif ((isset($_REQUEST['code']) && $cunity->getSetting('registration_method') == 'code') || $cunity->getSetting('registration_method') != 'code') {
        if (isset($_REQUEST['code'])) {
            $code = $_REQUEST['code'];
            $mail = $_REQUEST['m'];
            $res = $cunity->getDb()->query("SELECT code FROM " . $cunity->getConfig("db_prefix") . "invitation_codes WHERE code = '" . mysql_real_escape_string($code) . "' AND email = '$mail' LIMIT 1");
            if (mysql_num_rows($res) == 0) {
                $msg = newCunityError($lang['register_code_invalid']);
                $tplEngine->Template('register_code');
                $tplEngine->Assign('MSG', $msg);
                $check = false;
            } else {
                $check = true;
                $_SESSION['reg_code'] = $code;
            }
        }
        else
            $check = true;
        if ($check) {
            $new_fields = $register->createAddedFieldList();
            $names = $register->getNameFields();

            $days .= '<option value="">' . $lang['register_day'] . '</option>';
            for ($i = 1; $i <= 31; $i++) {
                if (strlen($i) == 1)
                    $day = '0' . $i;
                else
                    $day = $i;
                $days .= '<option value="' . $day . '">' . $day . '</option>';
            }

            $months .= '<option value="">' . $lang['register_month'] . '</option>';
            for ($i = 1; $i <= 12; $i++) {
                if (strlen($i) == 1)
                    $month = '0' . $i;
                else
                    $month = $i;
                $months .= '<option value="' . $month . '">' . $lang['month_' . $month] . '</option>';
            }
            $years .= '<option value="">' . $lang['register_year'] . '</option>';
            for ($year = date("Y", time()); $year >= 1911; $year--) {
                $years .= '<option value="' . $year . '">' . $year . '</option>';
            }

            $res = $cunity->getDb()->query("SELECT * FROM " . $cunity->getConfig("db_prefix") . "pages");
            while ($page = mysql_fetch_assoc($res))
                $pages[$page['slug']] = htmlspecialchars_decode($page['text']);

            require_once('includes/register_scripts.php');

            $tplEngine->Template('register');

            $tplEngine->Assign('SCRIPTS', $scripts);
            $tplEngine->Assign('NEW_FIELDS', $new_fields);
            $tplEngine->Assign('TERMS', $pages['terms']);
            $tplEngine->Assign('PRIVACY', $pages['privacy']);
            $tplEngine->Assign('NAMES', $names);
            $tplEngine->Assign(array("DAYS" => $days, "MONTHS" => $months, "YEARS" => $years));
        }
    }
}
/* RESET PW */ elseif ($_GET['c'] == 'resetpw' && !$cunity->getSaver()->login()) {
    if (isset($_GET['mkey'])) {
        $msg = $register->sendNewPassword($_GET['mkey']);
        $tplEngine->Template('newPw');

        $tplEngine->Assign('MSG', $msg);
        $tplEngine->Assign('login_rpw_new_pw', $lang['login_rpw_new_pw']);
    } else {
        if (isset($_POST['email'])) {
            $result = $register->resetPw($_POST['email']);
            if ($result == 1)
                $msg = newCunitySuccess($lang['register_success'] . ' ' . $lang['register_check_inbox']);
            elseif ($result == 0)
                $msg = newCunityError($lang['register_email_unknown']);
            else
                $msg = newCunityError('Mailing error!');
        }
        $tplEngine->Template('login_rpw');

        $tplEngine->Assign('MSG', $msg);
    }
}elseif ($_GET['c'] == 'login') {
    if (isset($_POST['mail'])) {
        $failed = false;

        $q = "SELECT * FROM " . $cunity->getConfig("db_prefix") . "users WHERE mail = '" . mysql_real_escape_string($_POST['mail']) . "' AND password = '" . mysql_real_escape_string(sha1($_POST['pass'])) . "' LIMIT 1";
        $res = $cunity->getDb()->query($q);
        $data = mysql_fetch_assoc($res);

        if (mysql_num_rows($res) == 1 && $_POST['mail'] == $data['mail'] && sha1($_POST['pass']) == $data['password'] && $data['groupid'] != 7 && $data['groupid'] != 5 && $data['groupid'] != 6 && $data['verif_mail'] == 1) {
            if (isset($_POST['save_login']) && $_POST['save_login'] == 'yes')
                session_set_cookie_params(2592000); // 30 days


            session_regenerate_id(); // prevent session fixation
            // after(!) set_cookie_params, otherwise the new time isn't set
            // get IP
            if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']))
                $ip = $_SERVER['REMOTE_ADDR'];
            else
                $failed = true;

            // create Fingerprint
            $fp = $cunity->getSaver()->fingerprint();

            if (!$fp)
                $failed = true;

            $cunity->refreshSettings();
            if ($cunity->getSetting('user_name') == 'nickname') {
                if ($data['username'] != $data['nickname']) {
                    $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "users SET username = '" . $data['nickname'] . "' WHERE userid = '" . $data['userid'] . "'");
                }
                $username = $data['nickname'];
            } elseif ($cunity->getSetting('user_name') == 'full_name') {
                $res = $cunity->getDb()->query("SELECT firstname, lastname FROM " . $cunity->getConfig("db_prefix") . "users_details WHERE userid = '" . $data['userid'] . "'");
                $d = mysql_fetch_assoc($res);
                $username = $d['firstname'] . " " . $d['lastname'];
                if ($username == " " || empty($username)) {
                    $username = $data['nickname'];
                }
                $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "users SET username = '" . $username . "' WHERE userid = '" . $data['userid'] . "'");
            }

            if ($data['userhash'] == "") {
                $userhash = $register->createUniqueUserHash($data['nickname']);
                if ($userhash != "")
                    $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "users SET userhash = '" . $userhash . "' WHERE userid = '" . $data['userid'] . "'");
            }

            $_SESSION['logged_in'] = true;
            $_SESSION['userid'] = $data['userid'];
            $_SESSION['nickname'] = $data['nickname'];
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $data['mail'];
            $_SESSION['groupid'] = $data['groupid'];
            $_SESSION['invisible'] = $data['invisible'];
            $_SESSION['isPhotoUploadPage'] = 0;
            $_SESSION['fingerprint'] = $fp;
            $_SESSION['userhash'] = $data['userhash'];
            $_SESSION['url'] = $cunity->getSetting('url');
            echo "sdf";
            if (!$cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "users SET last_ip = '" . mysql_real_escape_string($ip) . "',last_login = NOW() WHERE userid = '" . mysql_real_escape_string($data['userid']) . "'"))
                $failed = true;
            if (!$failed) {
                if ($register->checkMandatoryFields($data['userid'])) {
                    header('location: register.php?c=edit_fields');
                    exit();
                } else {
                    if (isset($_SESSION['login_referrer']) && !empty($_SESSION['login_referrer'])) {
                        header("Location:" . $_SESSION['login_referrer']);
                        unset($_SESSION['login_referrer']);
                        exit();
                    }
                    header('location: index.php');
                    exit();
                }
            } else {
                
            }
        } elseif (mysql_num_rows($res) == 1 && (string) $_POST['mail'] == $data['mail'] && sha1($_POST['pass']) == $data['password'] && ($data['groupid'] == 7 && $data['verif_mail'] == 0)) {
            $msg = $lang['register_account_activate'];
            $top = 105;
        } elseif (mysql_num_rows($res) == 1 && (string) $_POST['mail'] == $data['mail'] && sha1($_POST['pass']) == $data['password'] && ($data['groupid'] == 7 && $data['verif_mail'] == 1)) {
            $msg = $lang['register_account_admin_activate'];
            $top = 105;
        } elseif (mysql_num_rows($res) == 1 && (string) $_POST['mail'] == $data['mail'] && sha1($_POST['pass']) == $data['password'] && ($data['groupid'] == 6 && $data['verif_mail'] == 1)) {
            $msg = $lang['register_account_blocked'];
            $top = 80;
        } elseif (mysql_num_rows($res) == 1 && (string) $_POST['mail'] == $data['mail'] && sha1($_POST['pass']) == $data['password'] && ($data['groupid'] == 5 && $data['verif_mail'] == 1)) {
            $msg = $lang['register_account_inactive'];
            $top = 80;
        } else {
            $msg = $lang['register_user_invalid'];
            $top = 80;
        }
    } else {
        $msg = $lang['register_login_required'];
        $top = 105;
    }
    $tplEngine->Template('login');
    $tplEngine->Assign('MSG', $msg);
    $tplEngine->Assign('BODY', $cunity->getSetting('landing_body'));
    $tplEngine->Assign('TOP', $top);
}
/* LOGOUT */ elseif ($_GET['c'] == 'logout') {
    $register->logout();
}
/* EDIT_FIELDS */ elseif ($_GET['c'] == 'edit_fields') {
    $res = $cunity->getDb()->query("SELECT * FROM " . $cunity->getConfig("db_prefix") . "users_details WHERE userid = " . (int) $_SESSION['userid'] . " LIMIT 1");
    $userData = mysql_fetch_assoc($res);
    if (isset($_POST['send'])) {
        $error = array();
        foreach ($_POST AS $key => $data) {
            if ($key == 'send')
                continue;
            if (is_array($data))
                $data = json_encode($data);
            elseif (!$register->register_basics($key, $data))
                $error = true;
            $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "users_details SET " . $key . " = '" . $data . "' WHERE userid = '" . $_SESSION['userid'] . "'");
        }
        $msg = $lang['register_check_fields'];
        if (count($error) == 0) {
            header("Location: index.php");
            exit;
        } else {
            $msg = $lang['register_error'];
        }
    }
    $new_fields = $register->createAddedFieldList($userData, "'M'");
    if (!$new_fields) {
        header("Location: index.php");
        exit;
    }
    if (!empty($msg))
        $msg = newCunityError($msg);

    $tplEngine->Template('register_edit_fields');
    $tplEngine->Assign('LIST', $new_fields);
    $tplEngine->Assign('MSG', $msg);
}

require('ov_foot.php');
ob_end_flush();
?>