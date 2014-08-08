<?php

if(isset($_POST['send']))
{
    if($_POST['friendstype']=='friends')
    {
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = 'friends' WHERE name = 'friendstype'");
    }
    elseif($_POST['friendstype']=='members')
    {
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = 'members' WHERE name = 'friendstype'");
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."modules SET power = 1 WHERE name = 'members'");
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."modules SET power = 0 WHERE name = 'friends'");
    }
    elseif($_POST['friendstype']=='none'||!friends)
    {
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = 'none' WHERE name = 'friendstype'");
    }
}

$tplEngine->Template('modules_friends');

$res = $cunity->getDb()->query("SELECT value FROM ".$cunity->getConfig("db_prefix")."settings WHERE name = 'friendstype' LIMIT 1");
$data = mysql_fetch_assoc($res);
switch($data['value'])
{
    case 'friends':
        $tplEngine->Assign('friendstype_friends', 'on');
        $tplEngine->Assign('friendstype_members', 'none');
        $tplEngine->Assign('friendstype_none', 'none');
        $tplEngine->Assign('friends', 'checked="checked"');
    break;
    
    case 'members':
        $tplEngine->Assign('friendstype_friends', 'none');
        $tplEngine->Assign('friendstype_members', 'on');
        $tplEngine->Assign('friendstype_none', 'none');
        $tplEngine->Assign('members', 'checked="checked"');
    break;
    
    case 'none':
        $tplEngine->Assign('friendstype_friends', 'none');
        $tplEngine->Assign('friendstype_members', 'none');
        $tplEngine->Assign('friendstype_none', 'on');
        $tplEngine->Assign('none', 'checked="checked"');
    break;
}
?>