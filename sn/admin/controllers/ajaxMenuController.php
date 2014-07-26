<?php
session_name('cunity_sess'.$_SERVER['DOCUMENT_ROOT']);
session_start();
error_reporting(0);
require('../../includes/functions.php');
require('../includes/functions.php');    
require '../../classes/Cunity.class.php';

$cunity = new Cunity(true);

$langadmin = $cunity->getLang();

function cleanStr($str){
	return strtolower(strip_tags(str_replace(" ", "", $str)));
}

if(isset($_GET['setPosition'])){	
	foreach($_GET['menu'] AS $x => $entry){		
		$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."menu SET menu_position = ".$x." WHERE id = ".(int)$entry."");
	}
}elseif(isset($_POST['c'])&&$_POST['c']=='getDetail'){
	$id=explode('-',$_POST['id']);
	$res=$cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."menu WHERE id = ".$id[1]);
	$data=mysql_fetch_assoc($res);	
	
    if($data['def']==1){
    	$name = $langadmin['menu_'.$data['name']];
    	$disabled = 'disabled="disabled"';
    	$deleteLink = $langadmin['admin_settings_menu_no_del'];     	
    }else{
    	$name = $data['name'];
    	$disabled = "";
    	$deleteLink = '<a href="javascript: deleteEntry('.$id[1].');" style="color:#ff0000 !important;">'.$langadmin['admin_settings_menu_delete'].'</a>';
    }
    $data['icon'] = str_replace('[STYLE]', $_SESSION['style'], $data['icon']);        
	echo '
	<div style="background-color:#fff;padding:5px">
	<label for="nameDetail">Name:</label><br/><input type="text" name="name" id="nameDetail" value="'.$name.'" '.$disabled.' style="width:300px"/><br/>
	<label for="targetDetail">'.$langadmin['admin_settings_menu_target'].':</label><br/><input type="text" id="targetDetail" name="target" value="'.$data['target'].'" '.$disabled.' style="width:300px"/><br/>
	<label for="iconDetail" style="vertical-align:middle">Icon:</label>
	<div><input type="text" name="icon" id="iconDetail" value="'.$data['icon'].'" style="vertical-align:top;width:300px"/><img src="../'.$data['icon'].'" style="padding: 2px;background-color:#fff;" id="icon_preview"/></div>
	<small style="color:#ff0000 !important;">'.$deleteLink.'</small>
	';
}elseif(isset($_POST['c'])&&$_POST['c']=='saveData'){
	$name = htmlentities(mysql_real_escape_string($_POST['name']));
	$target = str_replace($cunity->getSetting("url")."/","",mysql_real_escape_string($_POST['target']));
	$icon = str_replace($_SESSION['style'], "[STYLE]",mysql_real_escape_string($_POST['icon']));
	$res=$cunity->getDb()->query("SELECT id,def FROM ".$cunity->getConfig("db_prefix")."menu WHERE target = '".$target."'") or die(mysql_error());
	if(mysql_num_rows($res)==0){
		$cunity->getDb()->query("INSERT INTO ".$cunity->getConfig("db_prefix")."menu (tag,name,target,icon) VALUES ('".cleanStr($name)."','".$name."','".$target."','".$icon."')") or die(mysql_error());
	}else{
		$data=mysql_fetch_assoc($res);		
		if($data['def']==0)
			$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."menu SET tag = '".strtolower(str_replace(" ", "", $name))."',name = '".$name."', target = '".$target."', icon = '".$icon."' WHERE id = ".(int)$data['id']."") or die(mysql_error());
		else
			$cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."menu SET icon = '".$icon."' WHERE id = ".(int)$data['id']."") or die(mysql_error());
	} 		
}elseif(isset($_POST['c'])&&$_POST['c']=='deleteEntry'){
	$id = mysql_real_escape_string($_POST['id']);
	$res=$cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."menu WHERE id = ".$id);
	$data=mysql_fetch_assoc($res);	
	if($data['def']==0)
		$cunity->getDb()->query("DELETE FROM ".$cunity->getConfig("db_prefix")."menu WHERE id = ".$id);
}
?>