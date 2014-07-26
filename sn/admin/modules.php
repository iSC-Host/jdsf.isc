<?php
ob_start("ob_gzhandler");
require('ov_head.php');

$cunity->getSaver()->check_admin();

if(isset($_GET['c']))
    require_once('includes/modules_'.$_GET['c'].'.php');
else
    require_once('includes/modules_overview.php');


require('ov_foot.php');
ob_end_flush();
?>