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
//Start session
session_name('cunity_sess' . base64_encode($_SERVER['DOCUMENT_ROOT']));
session_start();
$_SESSION['cunity_trunk_folder'] = dirname($_SERVER['SCRIPT_FILENAME']);
//Set PHP-Settings                                           
ini_set('session.use_cookies', true);
ini_set('session.cookie_lifetime', "0");
set_include_path($_SESSION['cunity_trunk_folder'] . '/classes');

require_once 'Cunity.class.php';
require_once 'Cunity_Template_Engine.class.php';

//create Cunity-Main-Class
$cunity = new Cunity();
error_reporting($cunity->getConfig("error_reporting"));

$lang = $cunity->getLang();

$thispath = pathinfo($_SERVER['SCRIPT_FILENAME']);
$_SESSION['include_path'] = $thispath['dirname'] . '/classes/Zend';

//require functions
require_once 'includes/functions.php';

if (isset($_POST['switch-design']) && !empty($_POST['switch-design']) && $cunity->getSetting('designswitch') == 1) {
    if (file_exists('style/' . $_POST['switch-design'] . '/info.xml')) {
        if ($cunity->getSaver()->login())
            $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "users SET design = '" . mysql_real_escape_string($_POST['switch-design']) . "' WHERE userid = '" . $_SESSION['userid'] . "'");

        $_SESSION['style'] = $_POST['switch-design'];
    }
}else {
    if ($cunity->getSaver()->login() && $cunity->getSetting('designswitch') == 1) {
        $res = $cunity->getDb()->query("SELECT design FROM " . $cunity->getConfig("db_prefix") . "users WHERE userid = '" . $_SESSION['userid'] . "'");
        $data = mysql_fetch_assoc($res);
        if ($data['design'] == "")
            $data['design'] = $cunity->getSetting('style');
        $_SESSION['style'] = $data['design'];
    }
    else
        $_SESSION['style'] = $cunity->getSetting('style');

    if (isset($_GET['preview']) && file_exists('./style/' . $_GET['preview'] . '/info.xml'))
        $_SESSION['style'] = $_GET['preview'];
}

if (isset($_POST['languageswitch'])) {
    if ($cunity->getSaver()->login()) {
        $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "users SET lang = '" . mysql_real_escape_string($_POST['languageswitch']) . "' WHERE userid = '" . $_SESSION['userid'] . "'");
        $cunity->setLang($_POST['languageswitch']);
    } else {
        $_SESSION['language'] = $_GET['setlang'];
        $cunity->setLang($_POST['languageswitch']);
    }
}

if ($cunity->getSaver()->login()) {
    define('LOGIN', true);
    if ($cunity->getSaver()->admin())
        define('ADMIN', true);
    else
        define('ADMIN', false);
    //Select Avatar
    if (file_exists('files/_avatars/' . getUserHash($_SESSION['userid']) . '.jpg'))
        $user_avatar = 'files/_avatars/' . getUserHash($_SESSION['userid']) . '.jpg';
    else
        $user_avatar = 'style/' . $_SESSION['style'] . '/img/no_avatar.jpg';

    //Count messages and select image
    $r = $cunity->getDb()->query("SELECT COUNT(*) FROM " . $cunity->getConfig("db_prefix") . "messages WHERE receiver = '" . $_SESSION['userid'] . "' AND `read` = '0' AND receiver_deleted = '0'");
    $d = mysql_fetch_assoc($r);
    if ($d['COUNT(*)'] > 0) {
        $count = ' (' . $d['COUNT(*)'] . ')';
        $msgimg = 'new_mails.png';
    } else {
        $count = '';
        $msgimg = 'mail.png';
    }
} else {
    define('LOGIN', false);
    define('ADMIN', false);
}

$languagesOptions = "";
$dh = opendir('./languages/');
while (false !== ($file = readdir($dh))) {
    if (@file_exists('./languages/' . $file . '/lang_info.xml') && is_dir('./languages/' . $file)) {
        $info = simplexml_load_file('./languages/' . $file . '/lang_info.xml');
        if (!getXMLValueViaAttribute($info, 'name', 'name', 'global'))
            $name = getXMLValueViaAttribute($info, 'name', 'name', 'local');
        else
            $name = getXMLValueViaAttribute($info, 'name', 'name', 'global');

        if ($file == $_SESSION['language']) {
            $languagesOptions .= '<option value="' . $file . '" selected="selected">' . $name . '</option>';
        } else {
            $languagesOptions .= '<option value="' . $file . '">' . $name . '</option>';
        }
    }
}
closedir($dh);

$dh = opendir('./style/') or die($lang['settings_general_error_style']);
while (false !== ($file = readdir($dh))) {
    if (@file_exists('./style/' . $file . '/info.xml') && is_dir('./style/' . $file)) {
        $info = simplexml_load_file('./style/' . $file . '/info.xml');
        if ($info->directory == $_SESSION['style']) {
            $designs .= '<option value="' . $info->directory . '" selected="selected">' . $info->name . '</option>';
        } else {
            $designs .= '<option value="' . $info->directory . '">' . $info->name . '</option>';
        }
    }
}
closedir($dh);

$menu = $cunity->getCunityMainMenu();

//create template-Engine
$cunity->getTemplateEngine()->setPath('style/' . $_SESSION['style'] . '/templates/');
$tplEngine = $cunity->getTemplateEngine();

//Assign veriables for overall_header Template
$tplEngine->Template('overall_header');
$tplEngine->Assign('DESIGNS', $designs);
$tplEngine->Assign('ACTIVE_' . strtoupper($cunity->getCurrentFile()), $cunity->isCurrentModule($cunity->getCurrentFile()));
$tplEngine->Assign('HEADER', $cunity->getSetting('header_body'));
$tplEngine->Assign('NAME', $cunity->getSetting('name'));
$tplEngine->Assign('SLOGAN', $cunity->getSetting('slogan'));
$tplEngine->Assign('LANG', $_SESSION['language']);
$tplEngine->Assign('FILE', $cunity->getCurrentFile());
$tplEngine->Assign('MENU', $menu);
$tplEngine->Assign('LANGUAGES', $languagesOptions);
$tplEngine->Assign('module', $cunity->getActiveModules());
$tplEngine->Assign('menu_messages_count', $lang['menu_messages'] . $count);
$tplEngine->Assign('messages_img', $msgimg);
?>