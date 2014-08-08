<?php
$tplEngine->Template('settings_page_layout');


if(isset($_POST['landing_body']))
{        
    $body = htmlentities($_POST['landing_body']);
    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = '".$body."' WHERE name = 'landing_body'");

    $body = "";
    $body = str_replace('<img ', '<img height="80px"', $body);
    $body = htmlentities($_POST['header_body']);    
    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = '".$body."' WHERE name = 'header_body'");
    
    $tplEngine->Assign('MSG', '<div class="message_green">'.$langadmin['settings_page_layout_done'].'</div>');
}
else
{
    $tplEngine->Assign('MSG', "");
}

$res = $cunity->getDb()->query("SELECT value FROM ".$cunity->getConfig("db_prefix")."settings WHERE name = 'landing_body'");
$dataHome = mysql_fetch_assoc($res);
$res = $cunity->getDb()->query("SELECT value FROM ".$cunity->getConfig("db_prefix")."settings WHERE name = 'header_body'");
$dataHeader = mysql_fetch_assoc($res);

$tplEngine->Assign('BODY', $dataHome['value']);
$tplEngine->Assign('HEADER', $dataHeader['value']);

?>