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

if(isset($_GET['c']) && $_GET['c'] == 'link'){
    $tplEngine->Assign('TITLE', $lang['pinboard_header']);
    $tplEngine->Template('standalone');        
        $tplEngine->Assign('STATUS_ID', $_GET['id']);
        $tplEngine->Assign('PINBOARD_ID', 0);
        $tplEngine->Assign('CUNITYID',0);
        $tplEngine->Assign('USERDATA',"");
        $tplEngine->Assign('PINBOARD_RECEIVER', 'main');    
    define('PINBOARD', false);
}else{
    define('PINBOARD', true);
    $tplEngine->Assign('TITLE', $lang['pinboard_header']);
    $tplEngine->Template('pinboard');        
        $tplEngine->Assign('STATUS_ID', 0);
        $tplEngine->Assign('PINBOARD_ID', 0);
        $tplEngine->Assign('CUNITYID',0);
        $tplEngine->Assign('PINBOARD_RECEIVER', 'main');
}
require('ov_foot.php');
ob_end_flush();
?>