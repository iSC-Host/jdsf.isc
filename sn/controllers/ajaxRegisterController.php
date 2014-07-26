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
   
session_name('cunity_sess'.$_SERVER['DOCUMENT_ROOT']);
session_start();
   
ini_set('session.use_cookies', true);
set_include_path($_SESSION['cunity_trunk_folder'].'/classes');

require_once 'Cunity.class.php';
require_once 'Cunity_Connector.class.php';

$cunity = new Cunity();

error_reporting($cunity->getConfig("error_reporting"));

$connector = new Cunity_Connector($cunity);

$loggeduser = $_SESSION['userid'];

$lang = $cunity->getLang();


require '../includes/functions.php';

require_once 'Cunity_Registration.class.php';

$register = new Cunity_Registration($cunity);

$data_back = json_decode (stripslashes($_REQUEST['json_data']), true);

if(isset($data_back['action'])&&$data_back['action']=="checkInput"){
	echo $cunity->returnJson(array("status"=>(int)$register->checkInput($data_back['field'], $data_back['input'],$data_back['checkExist'])));	
}elseif(isset($data_back['action'])&&$data_back['action']=="sendRegistration"){
	$data = explode('&',$data_back['data']);
	foreach($data AS $value){
		$tempData = explode('=',$value);
		$formData[$tempData[0]] = rawurldecode($tempData[1]);
	}
	$res = $register->checkFullRegistration($formData);
	if(is_array($res)){
		echo $cunity->returnJson(array("status"=>0,"errors"=>$res));
		exit;
	}else{
		echo $cunity->returnJson(array("status"=>(int)$register->createNewUser($formData)));
		exit;
	}	
}
?>