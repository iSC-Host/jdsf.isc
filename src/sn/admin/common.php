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
//session_name('cunity_sess'.$_SERVER['DOCUMENT_ROOT']);
//session_start();
#header('Cache-control: private'); // IE 6 FIX


$res = $cunity->getDb()->query("SELECT lang FROM ".$cunity->getConfig("db_prefix")."users WHERE userid = '".$_SESSION['userid']."'");
$d = mysql_fetch_assoc($res);
if($d['lang'] != NULL)
{
    $lang = $d['lang'];
}
else
{
    $lang = $cunity->getSetting('language');
}
$_SESSION['language'] = $lang;
switch ($lang) {
	case 'en':
		$lang_file = 'lang.en.php';

		$_SESSION['date']['php']['date'] = "F j, Y";
		$_SESSION['date']['php']['time'] = "g:i a";
		$_SESSION['date']['php']['date_time'] = $_SESSION['date']['php']['date']." ".$_SESSION['date']['php']['time'];
        $_SESSION['date']['mysql']['date'] = "%M %e, %Y";
		$_SESSION['date']['mysql']['time'] = "%h:%i %p";
		$_SESSION['date']['mysql']['date_time'] = $_SESSION['date']['mysql']['date']." ".$_SESSION['date']['mysql']['time'];
	break;

	case 'de':
		$lang_file = 'lang.de.php';

        $_SESSION['date']['php']['date'] = "d.m.Y";
		$_SESSION['date']['php']['time'] = "H:i";
		$_SESSION['date']['php']['date_time'] = $_SESSION['date']['php']['date']." ".$_SESSION['date']['php']['time'];
        $_SESSION['date']['mysql']['date'] = "%d.%m.%Y";
		$_SESSION['date']['mysql']['time'] = "%H:%i";
		$_SESSION['date']['mysql']['date_time'] = $_SESSION['date']['mysql']['date']." ".$_SESSION['date']['mysql']['time'];
	break;

	default:
		$lang_file = 'lang.en.php';

        $_SESSION['date']['php']['date'] = "F j, Y";
		$_SESSION['date']['php']['time'] = "g:i a";
		$_SESSION['date']['php']['date_time'] = $_SESSION['date']['php']['date']." ".$_SESSION['date']['php']['time'];
        $_SESSION['date']['mysql']['date'] = "%M %e, %Y";
		$_SESSION['date']['mysql']['time'] = "$h:%i %p";
		$_SESSION['date']['mysql']['date_time'] = $_SESSION['date']['mysql']['date']." ".$_SESSION['date']['mysql']['time'];
    break;
}

require_once 'languages/'.$lang_file;

?>