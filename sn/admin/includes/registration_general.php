<?php
$tplEngine->Template('registration_general');

if(isset($_POST['send']))
{
    if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = '".mysql_real_escape_string($_POST['reg_method'])."' WHERE name = 'registration_method'"))
    {
        if($cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = '".mysql_real_escape_string($_POST['age'])."' WHERE name = 'register_age'"))
        {
            header("location: registration.php?c=general");
            exit;
        }            
    } 
       
}
else
{
    $res = $cunity->getDb()->query("SELECT value FROM ".$cunity->getConfig("db_prefix")."settings WHERE name = 'registration_method' LIMIT 1");
    $data = mysql_fetch_assoc($res);
    if($data['value'] == 'everybody')
    {
        $tplEngine->Assign('EVERY', 'checked="checked"');
    }    
    elseif($data['value'] == 'activate')
    {
        $tplEngine->Assign('ACTIVATE', 'checked="checked"');
    }
    elseif($data['value'] == 'code')
    {
        $tplEngine->Assign('CODE', 'checked="checked"');
    }
    
    $res = $cunity->getDb()->query("SELECT value FROM ".$cunity->getConfig("db_prefix")."settings WHERE name = 'register_age' LIMIT 1");
    $data = mysql_fetch_assoc($res);
    $tplEngine->Assign('AGE', $data['value']); 
}

?>