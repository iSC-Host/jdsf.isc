<?php

$tplEngine->Template('settings_general');
$msg = '';

// Change Values
if (isset($_POST['save'])) {
    $errors = '';

    $name = trim(strip_tags($_POST['name']));
    if (strlen($name) > 100)
        $errors .= $langadmin['settings_general_name_long'] . '<br>';

    $slogan = trim(strip_tags($_POST['slogan']));
    if (strlen($slogan) > 100)
        $errors .= $langadmin['settings_general_motto'] . '<br>';

    if (!is_dir('../style/' . $_POST['design']))
        $errors .= $langadmin['settings_general_invalid_design'] . '<br>';
    else
        $design = $_POST['design'];

    if (!is_dir('./style/' . $_POST['admindesign']))
        $errors .= $langadmin['settings_general_invalid_design'] . '<br>';
    else
        $admindesign = $_POST['admindesign'];

    if (!preg_match('/^[A-Z0-9.%+-_]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i', $_POST['mail']))
        $errors .= $langadmin['settings_general_email_invalid'] . '<br>';
    else
        $mail = $_POST['mail'];

    if (empty($errors)) {
        if ($name != $cunity->getSetting('name')) {
            $res = $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "settings SET value='" . mysql_real_escape_string($name) . "' WHERE name='name' LIMIT 1");
        }

        if ($slogan != $cunity->getSetting('slogan')) {
            $res = $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "settings SET value='" . mysql_real_escape_string($slogan) . "' WHERE name='slogan' LIMIT 1");
        }

        if ($design != $cunity->getSetting('style')) {
            $res = $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "settings SET value='" . mysql_real_escape_string($design) . "' WHERE name='style' LIMIT 1");
            $res = $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "users SET design = '" . mysql_real_escape_string($design) . "' WHERE design != ''");
        }

        if ($admindesign != $cunity->getSetting('style_adminpanel')) {
            $res = $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "settings SET value='" . mysql_real_escape_string($admindesign) . "' WHERE name='style_adminpanel' LIMIT 1");
        }

        if (isset($_POST['designswitch']) && $_POST['designswitch'] == 1) {
            $res = $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "settings SET value=1 WHERE name = 'designswitch' LIMIT 1");
        } else {
            $res = $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "settings SET value=0 WHERE name = 'designswitch' LIMIT 1");
        }

        if ($mail != $cunity->getSetting('contact_mail')) {
            $res = $cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "settings SET value='" . mysql_real_escape_string($mail) . "' WHERE name='contact_mail' LIMIT 1");
        }

        $cunity->refreshSettings();
        $msg = $langadmin['settings_general_save_success'];
    }
    else
        $msg .= $errors . '' . $langadmin['settings_general_entry_not_saved'];
}

// Grab the Designs
$designs = '';

$dh = opendir('../style/') or die($langadmin['settings_general_error_style']);
while (false !== ($file = readdir($dh))) {
    if (file_exists('../style/' . $file . '/info.xml') && is_dir('../style/' . $file)) {
        $info = simplexml_load_file('../style/' . $file . '/info.xml');
        if ($info->directory == $cunity->getSetting('style')) {
            $designs .= '<option value="' . $info->directory . '" selected="selected">' . $info->name . '</option>';
        } else {
            $designs .= '<option value="' . $info->directory . '">' . $info->name . '</option>';
        }
    }
}

closedir($dh);

// Grab the ADMIN-Designs
$admindesigns = '';

$dh = opendir('./style/') or die($langadmin['settings_general_error_style']);
while (false !== ($file = readdir($dh))) {

    if (file_exists('./style/' . $file . '/info.xml') && is_dir('./style/' . $file)) {
        $info = simplexml_load_file('./style/' . $file . '/info.xml');
        if ($info->directory == $cunity->getSetting('style_adminpanel')) {
            $admindesigns .= '<option value="' . $info->directory . '" selected="selected">' . $info->name . '</option>';
        } else {
            $admindesigns .= '<option value="' . $info->directory . '">' . $info->name . '</option>';
        }
    }
}

closedir($dh);

if ($cunity->getSetting('designswitch') == 1)
    $designswitch = 'checked="checked"';
else
    $designswitch = "";

$tplEngine->Assign(array('NAME' => $cunity->getSetting('name'), 'SLOGAN' => $cunity->getSetting('slogan'), 'DESIGNS' => $designs, 'ADMIN_DESIGNS' => $admindesigns, 'DESIGNSWITCH' => $designswitch));

// Contact

$tplEngine->Assign('MAIL', $cunity->getSetting('contact_mail'));
$tplEngine->Assign('MSG', $msg);
?>