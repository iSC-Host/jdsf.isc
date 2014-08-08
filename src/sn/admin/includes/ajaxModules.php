<?php
ini_set('session.use_cookies', true);


require('../../config.php');
require('../../includes/functions.php');
require('functions.php');
require_once('../../classes/Cunity.class.php');

session_name('cunity_sess'.$_SERVER['DOCUMENT_ROOT']);
session_start();

$cunity = new Cunity(true);
$cunity->getSaver()->check_admin();
$langadmin = $cunity->getLang();

if($_GET['p'] == 'on')
{
    $id = explode("_", $_POST['id']);
    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."modules SET power = 1 WHERE id = '".mysql_real_escape_string($id[0])."'");
}
elseif($_GET['p'] == 'off')
{
    $id = explode("_", $_POST['id']);
    if($cunity->getSetting('friendstype')=='members'&&$id[2]=='members'&&!isset($_GET['v']))
        echo $langadmin['admin_modules_memberlist_error'];
    else
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."modules SET power = 0 WHERE id = '".mysql_real_escape_string($id[0])."'");
}
else if($_GET['save'] == 'speed')
{
    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = ".mysql_real_escape_string($_POST['speed'])." WHERE name = 'gallery_speed'");
    echo mysql_error();    
}
elseif($_GET['save'] == 'space')
{
    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = ".mysql_real_escape_string($_POST['space'])." WHERE name = 'user_space'");
    echo mysql_error();
}
else if($_GET['type'] == 'delete')
{
    $val = $_POST['val'];
    $cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."allowed_filetypes WHERE type = '".mysql_real_escape_string($val)."'");
}
elseif($_GET['type'] == 'add')
{
    $val = $_POST['val'];
    $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."allowed_filetypes (type) VALUES ('".mysql_real_escape_string($val)."')");
}
elseif($_GET['modules'] == 'none')
{
    $cunity->getDb()->query("TRUNCATE TABLE ".$cunity->getConfig("db_prefix")."allowed_filetypes");
}
elseif($_GET['modules'] == 'all')
{
    $cunity->getDb()->query("TRUNCATE TABLE ".$cunity->getConfig("db_prefix")."allowed_filetypes");
    $fields = explode(',',$_POST['fields']);
    foreach($fields AS $data)
    {
        $cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."allowed_filetypes (type) VALUES ('".mysql_real_escape_string($data)."')");
    }
}

?>