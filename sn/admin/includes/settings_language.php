<?php
$done = false;
if(isset($_POST['send']))
{
    if(isset($_POST['language']))
        $language = $_POST['language'];
    else   
        $language = 'english';

    if($language == 'english')
    {
        $language = 'en';
    }    
    elseif($language == 'german')
    {
        $language = 'de';
    }
    
    
    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = '".mysql_real_escape_string($language)."' WHERE name = 'language'");
    echo mysql_error();
    $done = true;    
}
if(isset($_GET['success']) && $_GET['success'] == true)
{
    print '<script language="javascript" type="text/javascript">
            $("document").ready(function(){
                $("#change_success").fadeIn();
                $("#change_success").delay(500);
                $("#change_success").fadeOut();
            });
        </script>';
}

$res = $cunity->getDb()->query("SELECT value FROM ".$cunity->getConfig("db_prefix")."settings WHERE name = 'language'");
$default = mysql_fetch_assoc($res);

$tplEngine->Template('settings_language');
    if($default['value'] == 'en')
    {
        $tplEngine->Assign('ENGLISH', 'checked="checked"');
        $tplEngine->Assign('ENGLISH_POWER', "on");
        $tplEngine->Assign('GERMAN_POWER', "none");
    }
    elseif($default['value'] == 'de')
    {
        $tplEngine->Assign('GERMAN', 'checked="checked"');
        $tplEngine->Assign('GERMAN_POWER', "on");
        $tplEngine->Assign('ENGLISH_POWER', "none");
    }
    if($done)
    {
        header("location: settings.php?c=language&success=true");
        exit;    
    }

?>