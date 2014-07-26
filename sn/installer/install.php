<?php
/*CUNITY(R) V1.0beta - An open source social network / "your private social network"
Copyright (C) 2011 Smart In Media GmbH & Co. KG
CUNITY(R) is a registered trademark of Dr. Martin R. Weihrauch
http://www.cunity.net


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
   see section 13 of the GNU Affero General Public License for the specific requirements. */
   
if (version_compare(PHP_VERSION, '5.0.0', '<') ) 
	exit("Sorry, Cunity will only run on PHP version 5 or greater!\n");
	
error_reporting(0);
session_start();
include 'terms_conditions.php'; //Text for terms and conditions
if ($_REQUEST["lang_install"]=="de") {require_once ('lang/lang_de.php');$_SESSION['lang_cur']='de';} // If it comes from the self-extracting installer, the language is selected
    else {require_once('lang/lang_en.php');$_SESSION['lang_cur']='en';} // Else it's taken as English for now.

if (!is_writable('../config.php')) die($lang_install['is_writable']);
    
include ('countries.php');
require_once ('tplengine.class.php');

if ($_REQUEST["installerrun"]=="yes") {$lang_js["if_installed"]='1';}
    else if ($_REQUEST["installerrun"]=="no") {$lang_js["if_installed"]='2';}
    else {$lang_js["if_installed"]='0';} 
  
$tplEngine = new tplengine();
$tplEngine->setPath('./');
$tplEngine->Template('./install');
    $scripts = array('INSTALL_SCRIPT' => 'install');
    $tplEngine->AssignScript($scripts);
	$tplEngine->Assign($lang_install);
	$tplEngine->Assign($lang_js);
	$tplEngine->Assign('terms_conditions', $terms_conditions_en);
	$tplEngine->Assign('countries', $countries);
	$tplEngine->show();

?>

	