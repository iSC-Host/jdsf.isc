<?php
//start session
session_name('cunity_sess'.$_SERVER['DOCUMENT_ROOT']);
session_start();

ini_set('session.use_cookies', true);
require('../../config.php');
error_reporting($cunityConfig["error_reporting"]);
require('../../includes/functions.php');
require('../includes/functions.php');
require('../../classes/db.class.php');
require('../includes/phppacker.inc.php');

$cunity->getSaver()->check_admin();

//Set maximum execution time to 10 minutes
set_time_limit(600);

//create databaseObject
$db = new db($cunityConfig["db_host"], $cunityConfig["db_user"], $cunityConfig["db_pass"], $cunityConfig["db_name"]);

require('../common.php');

//update settings
$settings = array();
update_settings();

$url = 'http://www.cunity.net/update/update.php?updatecheck=1&version='.$cunity->getSetting('version');

if(file_getContents($url)==1){
    print '<script language="javascript" type="text/javascript" src="../../includes/jquery/jquery.js"></script>';
    print '<script language="javascript" type="text/javascript">
    var par = jQuery(window.parent.document);
	par.find("#startupdate").removeAttr("disabled");
	par.find("#updateloader").hide();
    </script>';
    die($langadmin['admin_update_no_update']);
}

if(isset($_FILES['update'])){
    $parts=explode('.',$_FILES['update']['name']);
    if($parts[count($parts)-1]!="cu")
        die($langadmin['admin_update_filetype_failed']);
    move_uploaded_file($_FILES['update']['tmp_name'],"update.cu");
}else{
    if(!socketCopy("http://www.cunity.net/update/update.cu","update.cu")){
        print '<p>'.$langadmin['admin_update_no_method'].'</p>';
        print '<form action="ajaxUpdateController.php" method="POST" enctype="multipart/form-data" style="display: block;"><input type="file" name="update"/><input type="submit"/></form>';
        exit();
    }
}
if(phpunpack("update.cu",'../..'))
{
    if(file_exists('../../update.sql'))
    {
        $file = file_get_contents('../../update.sql');
    }//end if
    else{
        unlink("update.cu");
        print '<script language="javascript" type="text/javascript" src="../../includes/jquery/jquery.js"></script>';
        print '<script language="javascript" type="text/javascript">
        var par = jQuery(window.parent.document);
    	par.find("#startupdate").removeAttr("disabled");
    	par.find("#updateloader").hide();
        par.find("#successmessage").show();
        </script>';
        die("Error! SQL-File Missing!");        
    } 


    if(!require('sync.php')) {
        print '<script language="javascript" type="text/javascript" src="../../includes/jquery/jquery.js"></script>';
        print '<script language="javascript" type="text/javascript">
        var par = jQuery(window.parent.document);
        par.find("#startupdate").removeAttr("disabled");
        par.find("#updateloader").hide();
        par.find("#successmessage").show();
        </script>';
        die("Sync.php missing!");
    }                                        

    if(file_exists('../../startMe.php')){require('../../startMe.php');}


    $url = 'http://www.cunity.net/update/update.php?getversion=1';
    $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."settings SET value = '".file_getContents($url)."' WHERE name = 'version'")or die(mysql_error());

    unlink('../../startMe.php');
    unlink('../../update.sql');
    unlink('update.cu');

    recursive_remove_directory('../../includes/templates_compiled',true);
    recursive_remove_directory('../includes/templates_compiled', true);

}
else{
    print '<script language="javascript" type="text/javascript" src="../../includes/jquery/jquery.js"></script>';
    print '<script language="javascript" type="text/javascript">
    var par = jQuery(window.parent.document);
	par.find("#startupdate").removeAttr("disabled");
	par.find("#updateloader").hide();
    par.find("#successmessage").show();
    </script>';
    die("Update failed! Cannot open update-File on Server!");
}      
?>