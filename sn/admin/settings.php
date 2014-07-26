<?php
ob_start("ob_gzhandler");
require('ov_head.php');

	$cunity->getSaver()->check_admin();

	// Page Content
	if(!isset($_GET['c'])){
		require_once('includes/settings_general.php');
	}else		
		require_once('includes/settings_'.$_GET['c'].'.php');
	
require('ov_foot.php');
ob_end_flush();
?>