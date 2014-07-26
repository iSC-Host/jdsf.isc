<?php
$tplEngine->Template('settings_menu');
$tplEngine->Assign('STYLE',$cunity->getSetting('style'));
$res = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."menu ORDER BY menu_position");
while($data=mysql_fetch_assoc($res)){
	if($data['def']==1)
		$name = $langadmin['menu_'.$data['name']];
	else
    	$name = $data['name'];	
	$menuEntries .= '<li class="ui-state-default cunity_menu_item" id="menu-'.$data['id'].'"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>'.$name.'</li>';
}
$tplEngine->Assign('MENU_ENTRIES',$menuEntries);
?>