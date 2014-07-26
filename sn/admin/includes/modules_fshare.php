<?php

$tplEngine->Template('modules_fshare');
if (isset($_POST['type'])) {
    $types = $_POST['type'];
    foreach ($types AS $data) {
        $cunity->getDb()->query("INSERT INTO " . $cunity->getConfig("db_prefix") . "allowed_filetypes (type) VALUES ('" . mysql_real_escape_string($data) . "')");
        echo mysql_error();
    }
}
if (isset($_POST['send'])) {
    if (file_exists($_POST['filepath'])) {
        if (is_writable($_POST['filepath'])) {
            if (!$cunity->getDb()->query("UPDATE " . $cunity->getConfig("db_prefix") . "settings SET value = '" . $_POST['filepath'] . "' WHERE name = 'files_dir'"))
                $tplEngine->Assign('FP_ERROR', '<img src="style/' . $cunity->getSetting('style_adminpanel') . '/img/cross.png" width="25px" height="25px" style="vertical-align: middle;" /><span style="vertical-align: middle;">' . $langadmin['admin_modules_fileshare_database_error'] . '</span>');
            else
                $tplEngine->Assign('FP_ERROR', "");
        }
        else
            $tplEngine->Assign('FP_ERROR', '<img src="style/' . $cunity->getSetting('style_adminpanel') . '/img/cross.png" width="22px" height="22px" style="vertical-align: middle;" /><span style="vertical-align: middle;">' . admin_modules_fileshare_path_rights_error . '</span>');
    }
    else
        $tplEngine->Assign('FP_ERROR', '<img src="style/' . $cunity->getSetting('style_adminpanel') . '/img/cross.png" width="22px" height="22px" style="vertical-align: middle;" /><span style="vertical-align: middle;">' . $langadmin['admin_modules_filesharing_path_not_exists'] . '</span>');
}
else
    $tplEngine->Assign('FP_ERROR', "");

//Filetypes added by user
$html_allowed_filetypes = "";
$user_allowed_filetypes = "";
$i = 0;
$res = $cunity->getDb()->query("SELECT * FROM " . $cunity->getConfig("db_prefix") . "allowed_filetypes");
$std_types = array("jpg", "bmp", "gif", "tif", "psd", "mp3", "wav", "m3u", "mp4", "m4v", "m4a", "wma", "avi", "mov", "mpg", "wmv", "png", "pdf", "doc", "xls", "aac", "3gp", "divx", "flv", "jpeg", "mid", "midi", "mkv", "mpeg", "ogg", "ppt", "odt", "ods", "odp", "zip", "rar");
while ($row = mysql_fetch_assoc($res)) {
    if (!in_array($row['type'], $std_types)) {
        $i++;
        if ($i % 11 == 0)
            $html_allowed_filetypes .= "</div><div style='float: left; width: 100px;'>";
        $html_allowed_filetypes .= "<input type='checkbox' checked='checked' name='type[]' value='rar' id='" . $row['type'] . "' style='width: auto;'/><label for='" . $row['type'] . "'>*." . $row['type'] . "</label><br />";
        $user_allowed_filetypes .= "," . $row['type'];
    }
}
$tplEngine->Assign('allowed_filetypes', $html_allowed_filetypes);
$tplEngine->Assign('user_allowed_filetypes', $user_allowed_filetypes);

$allowed_filetypes = "";
$result = $cunity->getDb()->query("select type from " . $cunity->getConfig("db_prefix") . "allowed_filetypes");
while ($row = mysql_fetch_assoc($result))
    $allowed_filetypes .= $row['type'] . ',';
$allowed_filetypes = substr($allowed_filetypes, 0, -1);
$tplEngine->Assign('ALLOWED_FILETYPES', $allowed_filetypes);

//FILEPATH
$tplEngine->Assign('PATH', $cunity->getSetting('files_dir'));

$res = $cunity->getDb()->query("SELECT * FROM " . $cunity->getConfig("db_prefix") . "allowed_filetypes");
while ($data = mysql_fetch_assoc($res)) {
    $tplEngine->Assign($data['type'], 'checked="checked"');
}
if (!fileshare) {
    $tplEngine->Assign('FAIL', '<div id="fail"><p>' . $langadmin['admin_modules_fail'] . '</p></div>');
} else {
    $tplEngine->Assign('FAIL', '');
}
$tplEngine->Assign('USER_SPACE', $cunity->getSetting('user_space'));
?>