<?php
ob_start("ob_gzhandler");
require('ov_head.php');

$cunity->getSaver()->check_admin();

if(isset($_GET['c']))
{
    switch($_GET['c'])
    {
        case 'fields':
            require_once('includes/registration_fields.php');
        break;
    
        case 'general':
            require_once('includes/registration_general.php');
        break;
    
        default:
            require_once('includes/registration_general.php');
        break;
    }    
}
else    
    require_once('includes/registration_general.php');

require('ov_foot.php');
ob_end_flush();
?>