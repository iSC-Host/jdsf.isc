<?php

if(isset($_POST['chat_with']))
{
    $chat_with = mysql_real_escape_string($_POST['chat_with']);
    if($chat_with == 'friends' || $chat_with == 'all')
        $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = '".$chat_with."' WHERE name = 'chat_with'") or die(mysql_error());    
    $cunity->updateSetting('chat_with', $chat_with);
    print '<script language="javascript" type="text/javascript">
            $("document").ready(function(){
                $("#change_success").fadeIn();
                $("#change_success").delay(500);
                $("#change_success").fadeOut();
            });
        </script>';
   	$cunity->refreshSettings();
}
if($cunity->getSetting('chat_with')=='friends')
{
    $chat_with = '
                <label for="friends">
                <div class="cell_on" id="friends_cell" style="width: 200px; float: none;">
                    <input type="radio" name="chat_with" id="friends" style="width: auto;" checked="checked" value="friends"/>
                    '.$langadmin['admin_modules_friends'].'
                </div>
                </label>
                <label for="all">
                <div class="cell_none" id="all_cell" style="width: 200px; float: none;">
                    <input type="radio" name="chat_with" id="all" style="width: auto;" value="all"/>
                    '.$langadmin['admin_modules_all'].'
                </div>
                </label>
                <div class="clear"></div>';
}
else
{
    $chat_with = '
                <label for="friends">
                <div class="cell_none" id="friends_cell" style="width: 200px; float: none;">
                    <input type="radio" name="chat_with" id="friends" style="width: auto;" value="friends"/>
                    '.$langadmin['admin_modules_friends'].'
                </div>
                </label>
                <label for="all">
                <div class="cell_on" id="all_cell" style="width: 200px; float: none;">
                    <input type="radio" name="chat_with" id="all" style="width: auto;" checked="checked" value="all"/>
                    '.$langadmin['admin_modules_all'].'
                </div>
                </label>
                <div class="clear"></div>';    
}

$tplEngine->Template('modules_chat');
if(!chat)
{
    $tplEngine->Assign('FAIL', '<div id="fail"><p>'.$langadmin['admin_modules_fail'].'</p></div>');
}
else
{
    $tplEngine->Assign('FAIL', '');
}
$tplEngine->Assign('CHAT_WITH', $chat_with);
?>