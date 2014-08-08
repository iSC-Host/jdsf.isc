<?php

//start session
session_name('cunity_sess' . base64_encode($_SERVER['DOCUMENT_ROOT']));
session_start();

ini_set('session.use_cookies', true);
require('../includes/functions.php');
require('./includes/functions.php');
require_once('../classes/Cunity_Template_Engine.class.php');
require_once('../classes/Cunity.class.php');

$cunity = new Cunity(true);
error_reporting($cunity->getConfig("error_reporting"));

if ($cunity->getSaver()->login() && $cunity->getSaver()->admin())
    define('LOGIN_ADMIN', true);
else
    define('LOGIN_ADMIN', false);

set_include_path($_SESSION['cunity_trunk_folder'] . '/classes');

$langadmin = $cunity->getLang();
//check which modules are ON
$res = $cunity->getDb()->query("SELECT * FROM " . $cunity->getConfig("db_prefix") . "modules WHERE name = 'galleries' OR name = 'fileshare' OR name = 'chat' OR name = 'friends'");
while ($data = mysql_fetch_assoc($res)) {
    if ($data['power'] == 1) {
        define($data['name'], true);
    } else {
        define($data['name'], false);
    }
}

//Set language-flag-file
$german_flag = 'style/' . $cunity->getSetting('style_adminpanel') . '/img/de.png';
$english_flag = 'style/' . $cunity->getSetting('style_adminpanel') . '/img/en.png';

//get curretn filename
$f = $_SERVER['PHP_SELF'];
$finfo = pathinfo($f);
$file = $finfo['filename'];
//Create templateEngine    
$cunity->getTemplateEngine()->setPath('style/' . $cunity->getSetting('style_adminpanel') . '/templates/');
$tplEngine = $cunity->getTemplateEngine();


$tplEngine->Template('overall_header');
$tplEngine->Assign('TITLE', $cunity->getSetting('name'));
$tplEngine->Assign('STYLE', $cunity->getSetting('style_adminpanel'));
$tplEngine->Assign('ok', $langadmin['ok']);
$tplEngine->Assign('no', $langadmin['no']);
$tplEngine->Assign('yes', $langadmin['yes']);
$tplEngine->Assign('cancel', $langadmin['cancel']);


$tplEngine->Template('menu_main');
$tplEngine->Assign($file, 'style="background-color: #ccc; border-bottom: 2px solid #000;"');


$tplEngine->Template('menu_sub');
if (galleries) {
    $tplEngine->Assign('admin_submenu_gallery', $langadmin['admin_submenu_gallery'] . " [ON]");
} else {
    $tplEngine->Assign('admin_submenu_gallery', $langadmin['admin_submenu_gallery'] . " [OFF]");
}
if (fileshare) {
    $tplEngine->Assign('admin_submenu_filesharing', $langadmin['admin_submenu_filesharing'] . " [ON]");
} else {
    $tplEngine->Assign('admin_submenu_filesharing', $langadmin['admin_submenu_filesharing'] . " [OFF]");
}
if (chat) {
    $tplEngine->Assign('admin_submenu_chat', $langadmin['admin_submenu_chat'] . " [ON]");
} else {
    $tplEngine->Assign('admin_submenu_chat', $langadmin['admin_submenu_chat'] . " [OFF]");
}
$tplEngine->Assign($file, true);
if (!isset($_GET['c']) && $file != 'index') {
    $tplEngine->Assign('overview_sub', 'class="active"');
    $tplEngine->Assign('general_sub', 'class="active"');
    $tplEngine->Assign('stats_sub', 'class="active"');
}
else
    $tplEngine->Assign($_GET['c'] . "_sub", 'class="active"');
$tplEngine->show();
?>