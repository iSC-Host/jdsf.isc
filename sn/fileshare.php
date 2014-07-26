<?php
/*
########################################################################################
## CUNITY(R) V1.0beta - An open source social network / "your private social network" ##
########################################################################################
##  Copyright (C) 2011 Smart In Media GmbH & Co. KG                                   ##
## CUNITY(R) is a registered trademark of Dr. Martin R. Weihrauch                     ##
##  http://www.cunity.net                                                             ##
##                                                                                    ##
########################################################################################

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or any later version.

1. YOU MUST NOT CHANGE THE LICENSE FOR THE SOFTWARE OR ANY PARTS HEREOF! IT MUST REMAIN AGPL.
2. YOU MUST NOT REMOVE THIS COPYRIGHT NOTES FROM ANY PARTS OF THIS SOFTWARE!
3. NOTE THAT THIS SOFTWARE CONTAINS THIRD-PARTY-SOLUTIONS THAT MAY EVENTUALLY NOT FALL UNDER (A)GPL!
4. PLEASE READ THE LICENSE OF THE CUNITY SOFTWARE CAREFULLY!

	You should have received a copy of the GNU Affero General Public License
    along with this program (under the folder LICENSE).
	If not, see <http://www.gnu.org/licenses/>.

   If your software can interact with users remotely through a computer network,
   you have to make sure that it provides a way for users to get its source.
   For example, if your program is a web application, its interface could display
   a "Source" link that leads users to an archive of the code. There are many ways
   you could offer source, and different solutions will be better for different programs;
   see section 13 of the GNU Affero General Public License for the specific requirements. 
   
   #####################################################################################
   */
   
ob_start("ob_gzhandler");
require('ov_head.php');
$cunity->getSaver()->module_power();                                 
$cunity->getSaver()->login_required();

$tplModEngine = new Cunity_Template_Engine($cunity);
$tplModEngine->setPath('style/'.$_SESSION['style'].'/templates/filesharing/');

require_once 'Cunity_Filesharing.class.php';
$filesharing = new Cunity_Filesharing($cunity);

if(!$filesharing->checkFilesystem()){
    $tplEngine->Assign('TITLE', "ERROR - ".$cunity->getSetting('name'));
    $tplEngine->show();
    $tplModEngine->Template('error');

    if($cunity->getSaver()->admin())
        $tplModEngine->Assign('ERROR',$lang['filesharing_files_dir_admin_error']);
    else
        $tplModEngine->Assign('ERROR',$lang['filesharing_files_dir_error']);    
    $tplModEngine->show();
}else if(!isset($_GET['c'])){
    $tplEngine->Assign('TITLE','Filesharing - '.$cunity->getSetting('name'));
    $tplEngine->show();
	$tplModEngine->Template('fileshare');
	
	$tplModEngine->Assign('SESSION_ID', session_id());
	
    
	$tplModEngine->show();
}else if(isset($_GET['c'])&&$_GET['c']=="singleupload"){
    list($space,$used) = $filesharing->getUserSpace();
    $percentage = ($space > 0) ? $used/$space*100 : 0;
    $tplEngine->Assign('TITLE', $lang['filesharing_single_upload']);
    $tplEngine->show();
	$tplModEngine->Template('singleupload');
	
	$tplModEngine->Assign('PERCENTAGE',$percentage);
	$tplModEngine->Assign('SPACE',$space);
	$tplModEngine->Assign('LEFT',$used);
	
    
	$tplModEngine->show();
}else if(isset($_GET['c'])&&$_GET['c']=='multiupload'){
    $allowed = $filesharing->getAllowedFileTypes();
    foreach($allowed AS $filetype)
        $allowed_filetypes.='*.'.$filetype.'; ';
    $allowed_filetypes = substr($allowed_filetypes,0,-1);
    list($space,$used) = $filesharing->getUserSpace();
    $percentage = ($space > 0) ? $used/$space*100 : 0;

    $max_size = 5; //in MB
    $max_upload = (int)(ini_get('upload_max_filesize'));
    $max_post = (int)(ini_get('post_max_size'));
    $memory_limit = (int)(ini_get('memory_limit'));
    $max_size = min($max_size, $max_upload, $max_post, $memory_limit);

    
    $tplEngine->Assign('TITLE', $lang['filesharing_multi_upload']);
    $tplEngine->show();
	$tplModEngine->Template('multiupload');
	$tplModEngine->Assign('PERCENTAGE', $percentage);
	$tplModEngine->Assign('SPACE',$space);
	$tplModEngine->Assign('LEFT',$used);
	$tplModEngine->Assign('MAX_SIZE',$max_size);
	
    
	
	$tplModEngine->Assign('ALLOWED_FILETYPES', $allowed_filetypes);
	$tplModEngine->Assign('SESSION_ID', session_id());
    $tplModEngine->Assign('left',$space['space']-$used['used']);
	$tplModEngine->show();
}
require('ov_foot.php');
ob_end_flush();
?>