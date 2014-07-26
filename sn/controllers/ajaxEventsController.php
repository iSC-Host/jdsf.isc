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
require_once('../includes/ajaxInit.inc.php');

$userid = $_SESSION['userid'];
$data_back = json_decode (stripslashes($_REQUEST['json_data']), true);

require_once 'Cunity_Events.class.php';
$events = new Cunity_Events($cunity);

if($data_back['action']=='getEvents'){
    echo $cunity->returnJson($events->showDayTooltip($data_back['d']));
}elseif($data_back['action']=='getCalendar'){
    echo $cunity->returnJson($events->getCalendar($data_back['q']));
}elseif($data_back['action']=="getDayEvents"){
    echo $cunity->returnJson($events->getDayEvents($_SESSION['userid'],$data_back['d']));
}elseif($data_back['action']=='getEventsList'){
    echo $cunity->returnJson($events->getMonthEvents($_SESSION['userid'],$data_back['q']));
}elseif($data_back['action']=='addGuests'){
    echo $cunity->returnJson($events->addGuests($data_back['event_id'],explode(',',$data_back['guests'])));
}elseif($data_back['action']=='getGuestList'){
    echo $cunity->returnJson($events->getGuestList($data_back['event_id'],$data_back['status']));
}elseif($data_back['action'] == "myRequests" ){
    echo $cunity->returnJson($events->getEventRequests());
}else if($data_back['action']=="respondRequest")
    echo $cunity->returnJson($events->respondInvitation(intval($data_back['event_id']),intval($data_back['status'])));
?>