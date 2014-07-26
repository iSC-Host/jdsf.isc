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

$cunity->getSaver()->login_required();
$cunity->getSaver()->module_power();

require_once 'Cunity_Events.class.php';
$events = new Cunity_Events($cunity);

$tplModEngine = new Cunity_Template_Engine($cunity);
$tplModEngine->setPath('style/'.$_SESSION['style'].'/templates/events/');

if(isset($_GET['e']) && !empty($_GET['e'])&&!isset($_GET['c'])){
    $eventId = $events->getEventId($_GET['e']);
    $showResult=$events->showEvent($eventId);
    $status=$events->getInvitationStatus($eventId,$_SESSION['userid']);
        define('PINBOARD', true);
        $tplEngine->Assign('TITLE', $showResult['name']);
        $tplEngine->show();
        $tplModEngine->Template('event_show');
            $tplModEngine->Assign('PINBOARD_ID', $eventId);
            $tplModEngine->Assign('CUNITYID', 0);
            $tplModEngine->Assign('USERDATA',"");
            $tplModEngine->Assign('PINBOARD_RECEIVER', "event");
            $tplModEngine->Assign('ATTENDING', $showResult['attending']);
            $tplModEngine->Assign('PRIVACY', $showResult['privacy']);
            $tplModEngine->Assign('PRIVACY_ICON', $showResult['privacy_icon']);
            $tplModEngine->Assign('EVENT_NAME', $showResult['name']);
            $tplModEngine->Assign('EVENT_IMG', $showResult['img_file']);
            $tplModEngine->Assign('EVENT_ID', $_GET['e']);
            $tplModEngine->Assign('EVENTID',$eventId);
            $tplModEngine->Assign('EVENT_PRIVACY', $showResult['privacy']);
            $tplModEngine->Assign('TIME', $showResult['time']);
            $tplModEngine->Assign('PLACE', $showResult['place']);
            $tplModEngine->Assign('OWN_EVENT',$events->isOwnEvent($eventId));
            $tplModEngine->Assign('INVITED',$status!==false);
            $tplModEngine->Assign('INVITE_LINK',($status!==false&&$status!==1));
            $tplModEngine->Assign('CREATED_NAME',$showResult['created_name']);
            $tplModEngine->Assign('CREATED_HASH',$showResult['created_hash']);
            $tplModEngine->Assign('INFOS', $showResult['info']);
            $tplModEngine->Assign('YES_COUNT',$showResult['yes_count']);
            $tplModEngine->Assign('MAYBE_COUNT',$showResult['maybe_count']);
            $tplModEngine->Assign('INVITED_COUNT',$showResult['invited_count']);
            $tplModEngine->Assign('ATTENDING_BUTTONS', $showResult['attending_buttons']);
            $tplModEngine->Assign('GUEST_LIST_ADD', $showResult['guestList']);
            $tplModEngine->Assign('STATUS_ID',0);
}elseif(isset($_GET['c'])&&$_GET['c']=="edit"){
    $eventid = getEventId($_GET['e']);

    if(isset($_POST['save'])){
        if(isset($_FILES)) $_POST['files'] = $_FILES;
        $result=$events->editEvent($events->getEventId($_GET['e']),$_POST);
        if($result['status']==1)
            header("Location: events.php?e=".$result['eventid']);
        exit;    
    }

    $data = $events->getEventData($events->getEventId($_GET['e']));

    $timesEnd = "";
    $timesStart = "";
    for($m = 0; $m < 24; $m++){
        $fullTime = showDate("time",mktime($m, 0,0),true);
        $halfTime = showDate("time", mktime($m, 30,0),true);
        $fullTimeVal = date('H:i', mktime($m, 0,0));
        $halfTimeVal = date('H:i', mktime($m, 30,0));

        if($fullTimeVal == date('H:i', strtotime($data['start_time'])))
            $timesStart .= '<option value="'.$fullTimeVal.'" selected="selected">'.$fullTime.'</option>';
        else
            $timesStart .= '<option value="'.$fullTimeVal.'">'.$fullTime.'</option>';
        if($halfTimeVal == date('H:i', strtotime($data['start_time'])))
            $timesStart .= '<option value="'.$halfTimeVal.'" selected="selected">'.$halfTime.'</option>';
        else
            $timesStart .= '<option value="'.$halfTimeVal.'">'.$halfTime.'</option>';
        if($fullTimeVal == date('H:i', strtotime($data['end_time'])))
            $timesEnd .= '<option value="'.$fullTimeVal.'" selected="selected">'.$fullTime.'</option>';
        else
            $timesEnd .= '<option value="'.$fullTimeVal.'">'.$fullTime.'</option>';
        if($halfTimeVal == date('H:i', strtotime($data['end_time'])))
            $timesEnd .= '<option value="'.$halfTimeVal.'" selected="selected">'.$halfTime.'</option>';
        else
            $timesEnd .= '<option value="'.$halfTimeVal.'">'.$halfTime.'</option>';
    }

    $tplEngine->Assign('TITLE', $data['name'].' - '.$lang['events_edit_event']);
    $tplEngine->show();
    $tplModEngine->Template('events_edit');
        $tplModEngine->Assign('START_DATE', showDate("date",$data['start_date']));
        $tplModEngine->Assign('START_DATE_VAL', $data['start_date']);
        $tplModEngine->Assign('END_DATE', showDate("date",$data['end_date']));
        $tplModEngine->Assign('END_DATE_VAL', $data['end_date']);
        $tplModEngine->Assign('TIME_START', $timesStart);
        $tplModEngine->Assign('TIME_END', $timesEnd);
        $tplModEngine->Assign('DATE_FORMAT',$_SESSION['date']['js']['date']);
        $tplModEngine->Assign('NAME', $data['name']);
        $tplModEngine->Assign('PLACE', $data['place']);
        $tplModEngine->Assign('INFO', $data['info']);
        $tplModEngine->Assign('PIC_SRC', $data['img_file']);
        $tplModEngine->Assign('EVENT_ID', $data['eventid']);
        $tplModEngine->Assign('ERROR', (!empty($errormsg)?newCunityError($errormsg):""));
        $tplModEngine->Assign('PUBLIC',($data['privacy']==0)?"":"checked");
        $tplModEngine->Assign('PRIVATE',($data['privacy']==0)?"checked":"");
}elseif(isset($_GET['c']) && $_GET['c'] == 'new'){
    $eventid = getEventId($_GET['e']);

    if(isset($_POST['save'])){
        if(isset($_FILES)) $_POST['files'] = $_FILES;
        $result=$events->editEvent($events->getEventId($_GET['e']),$_POST,"new");
        if($result['status']==1)
            header("Location: events.php?e=".$result['eventid']);
        exit;
    }

    $timesEnd = "";
    $timesStart = "";
    for($m = 0; $m < 24; $m++){
        $fullTime = showDate("time",mktime($m, 0,0),true);
        $halfTime = showDate("time", mktime($m, 30,0),true);
        $fullTimeVal = date('H:i', mktime($m, 0,0));
        $halfTimeVal = date('H:i', mktime($m, 30,0));
        $timesStart .= '<option value="'.$fullTimeVal.'">'.$fullTime.'</option>';
        $timesStart .= '<option value="'.$halfTimeVal.'">'.$halfTime.'</option>';
        $timesEnd .= '<option value="'.$fullTimeVal.'">'.$fullTime.'</option>';
        $timesEnd .= '<option value="'.$halfTimeVal.'">'.$halfTime.'</option>';
    }

    $start_date_show = date("d.m.Y");
    $start_date = date('Y-m-d');
    $end_date_show = date("d.m.Y");
    $end_date = date('Y-m-d');

    $tplEngine->Assign('TITLE', $lang['events_add_event']);
    $tplEngine->show();
    $tplModEngine->Template('events_new');
        $tplModEngine->Assign('START_DATE', $start_date_show);
        $tplModEngine->Assign('START_DATE_VAL', $start_date);
        $tplModEngine->Assign('END_DATE', $end_date_show);
        $tplModEngine->Assign('END_DATE_VAL', $end_date);
        $tplModEngine->Assign('DATE_FORMAT',$_SESSION['date']['js']['date']);
        $tplModEngine->Assign('TIME_START', $timesStart);
        $tplModEngine->Assign('TIME_END', $timesEnd);
        $tplModEngine->Assign('ERROR', (!empty($errormsg)?newCunityError($errormsg):""));
}elseif(isset($_GET['c']) && $_GET['c'] == 'requests'){
    $tplEngine->Assign('TITLE', $lang['events_events_requests']);
    $tplEngine->show();
    $tplModEngine->Template('events_requests');
}elseif(isset($_GET['d']) && $_GET['d'] != ""){
    $day = showDate('date', $_GET['d']);
    $tplEngine->Assign('TITLE', $day);
    $tplEngine->show();
    $tplModEngine->Template('event_day');
        $tplModEngine->Assign('DAY', $day);
        $tplModEngine->Assign('DAY_VAL',$_GET['d']);
}else{
    $requests = ' ('.$events->getEventsRequests($_SESSION['userid']).')';
    $tplEngine->Assign('TITLE', $lang['events_events']);
    $tplEngine->show();
    $tplModEngine->Template('events');
        $tplModEngine->Assign('REQUESTS', $requests);
        $tplModEngine->Assign('UP_EVENTS', $events->getUpcomingEvents($_SESSION['userid']));
}
$tplModEngine->show();
require('ov_foot.php');
ob_end_flush();
?>