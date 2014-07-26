<?php

class Cunity_Events {
    
    private $cunity = null;
    private $lang = array();
    
    public function Cunity_Events(Cunity $cunity){
        $this->cunity = $cunity;
        $this->lang = $this->cunity->getLang();
    }
    
    public function getEventData($eventId){
        $res=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."events WHERE id = ".intval($eventId));
        return mysql_fetch_assoc($res);
    }
    
    public function getEventGuests($eventId,$status=4){
        $guests=array();
        if($status==4)
            $res=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE event_id = ".intval($eventId));
        else
            $res=$this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE event_id = ".intval($eventId)." AND `status` = '".intval($status)."'");
        while($d=mysql_fetch_assoc($res))
            $guests[] = $d['userid'];
        return $guests;
    }
    
    public function getInvitationStatus($eventId,$userid){
        $res=$this->cunity->getDb()->query("SELECT status FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE event_id = ".$eventId." AND userid = ".$userid." LIMIT 1");
        if(mysql_num_rows($res)==0) return false;
        $data=mysql_fetch_assoc($res);
        return $data['status'];
    }
    
    public function isOwnEvent($eventId){
        $eventData=$this->getEventData($eventId);
        return $eventData['founder_id']===$_SESSION['userid'];
    }
    
    public function deleteEvent($eventId){
        $eventData=$this->getEventData($eventId);
        if($this->isOwnEvent($eventId)){
            $res=array();
            $res[]=$this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE event_id = ".intval($eventId));
            $res[]=$this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."pinboard WHERE pinboard_id = ".intval($eventId)." AND receiver = 'event'");
            $res[]=$this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."events WHERE id = ".intval($eventId));
            return !in_array(false,$res);
        }
        return false;
    }
    
    public function editEvent($eventId,array $data,$action="edit"){
        $eventData=$this->getEventData($eventId);
        $data['name'] = htmlentities($data['name'],ENT_QUOTES,"UTF-8");
        $data['place'] = htmlentities($data['place'],ENT_QUOTES,"UTF-8");
        $data['info'] = htmlentities($data['info'],ENT_QUOTES,"UTF-8");
        $data['path']=$eventData['img_file'];
        $data['eventId'] = $eventId;
        $errormsg="";
        if(isset($data['files']['event_image_file']) && $data['files']['event_image_file']['error'] == 0){
            $temp = $data['files']['event_image_file']['tmp_name'];
    		$max_width = 200;
    		$max_height = 300;

    		$size = getimagesize($temp);
    		$width = $size[0];
    		$height = $size[1];
    		$imgtype = $size[2];

    		$new_width = $width;
    		$new_height = $height;

    		if($new_height > $max_height) {
    			$new_width = $new_width / ($new_height / $max_height);
    			$new_height = $max_height;
    		}

    		if($new_width > $max_width) {
    			$new_height = $new_height / ($new_width / $max_width);
    			$new_width = $max_width;
    		}

    		if($imgtype == IMAGETYPE_JPEG){
                $old_img = imagecreatefromjpeg($temp);

    			$img = imagecreatetruecolor($new_width, $new_height); // Neues TrueColor Bild anlegen
    			imagecopyresampled($img, $old_img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    			imageinterlace($img, true);
    			$filename = rand().time();

    			imagejpeg($img, './files/_event_imgs/'.$filename.'.jpg',100);

    			imagedestroy($old_img);
    			imagedestroy($img);
    			$data['path'] = './files/_event_imgs/'.$filename.'.jpg';
       		}

            if(!file_exists($data['path']))
    	        $data['path'] = $eventData['img_file'];
    	}
    	if($data['path']=="")
    	    $data['path'] = "./style/default/img/no_profile_img.jpg";    	
        if(strtotime($data['startdate'].$data['starttime']) <=  strtotime($data['enddate'].$data['endtime'])){
            if($action=="new"){
                $data['eventHash']=$this->createUniqueEventId($_SESSION['userid'].$data['name']);
                if($this->insertNewEvent($data))
                    return array("status"=>intval($this->insertNewGuest(mysql_insert_id(),$_SESSION['userid'],3)),"eventid"=>$data['eventHash']);
            }else{
                return array("status"=>intval($this->updateEvent($data)));
            }
        }else
            return array("status"=>0,"error"=>$this->lang['events_date_invalid']);
    }
    
    protected function updateEvent(array $data){
        return $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."events SET name = '".$data['name']."', place = '".$data['place']."', info = '".$data['info']."', img_file = '".$data['path']."', start_date = '".$data['startdate']."', start_time = '".$data['starttime']."', end_date = '".$data['enddate']."', end_time = '".$data['endtime']."', privacy = '".$data['privacy']."' WHERE id = ".$data['eventId']);
    }
    
    protected function insertNewEvent(array $data){
        return $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."events (eventid,founder_id, birthday, name,place,info,img_file,start_date, end_date, start_time, end_time, privacy) VALUES ('".$data['eventHash']."','".$_SESSION['userid']."',0,'".$data['name']."', '".$data['place']."', '".$data['info']."', '".$data['path']."', '".$data['startdate']."', '".$data['enddate']."', '".$data['starttime']."', '".$data['endtime']."', '".$data['privacy']."')") or die(mysql_error());
    }
    
    public function getEventsRequests(){
        $res = $this->cunity->getDb()->query("SELECT COUNT(*) FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE status = '0' AND userid = '".$userid."' AND (SELECT end_date FROM ".$this->cunity->getConfig("db_prefix")."events WHERE id = ".$this->cunity->getConfig("db_prefix")."events_guests.event_id) >= CURDATE()");
        $d = mysql_fetch_assoc($res);
        return $d['COUNT(*)'];
    }
    
    public function getDayEvents($userid,$date){
        $q = "SELECT
                *
               FROM
                ".$this->cunity->getConfig("db_prefix")."events
               WHERE(
                start_date <= '".$date."' AND end_date >= '".$date."'
                AND id IN 
                (SELECT event_id FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE userid = ".$userid." AND status='3' OR status = '2')
               )OR(
                    birthday = 1
                    AND
                    DATE_FORMAT(start_date,'%m-%d') = '".date("m-d", strtotime($date))."'
                    AND
                    founder_id IN (SELECT (CASE WHEN sender != ".$userid." AND receiver = ".$userid." THEN sender WHEN receiver != ".$userid." AND sender = ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$this->cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1 AND cunityId = 0)
                )";
        $res=$this->cunity->getDb()->query($q);
        while($data = mysql_fetch_assoc($res)){
            if($data['birthday'] == 0){
                $eventData=$this->showEvent($data['id']);
                $events .= $this->cunity->getTemplateEngine()->createTemplate("events_list",array(
                    "TIME"=>$eventData['time'],
                    "GUESTS"=>count($eventData['guests']),
                    "EVENT_ID"=>$data['id'],
                    "EVENT_HASH"=>$eventData['eventid'],
                    "EVENT_NAME"=>$data['name'],
                    "EVENT_IMG"=>$data['img_file'],
                    "FOUNDER_HASH"=>$eventData['created_hash'],
                    "FOUNDER_NAME"=>$eventData['created_name'],
                    "ATTENDING"=>$eventData['attending'],
                    "events_created_by"=>$this->lang['events_created_by'],
                    "events_select_rsvp"=>$this->lang['events_select_rsvp'],
                    "events_im_yes_attending"=>$this->lang['events_im_yes_attending'],
                    "events_im_maybe_attending"=>$this->lang['events_im_maybe_attending'],
                    "events_im_not_attending"=>$this->lang['events_im_not_attending'],
                    "events_remove_event"=>$this->lang['events_remove_event'],
                    "events_guests"=>$this->lang['events_guests']
                ));
            }else{
                $age   = intval((mktime(0,0,0,$m,1, $y) - strtotime($data['start_date'])) / (3600 * 24 * 365));
                $events .= '<div class="event_list_wrap" style="height: 20px; padding: 5px;">';
                $events .= '<img src="style/'.$_SESSION['style'].'/img/cake.png"/><a class="event_list_name" style="font-size: 12px; padding-left: 5px;" href="profile.php?user='.getUserHash($data['founder_id']).'">'.$this->lang['events_birthday_of']." ".getUserName($data['founder_id']).'&nbsp;('.$age.')</a>&nbsp;<small style="font-weight: bold; color: #333;">('.date('d', strtotime($data['start_date'])).' '.$this->lang['month_'.(date('m', strtotime($data['start_date']))-1)].')</small><br />';
                $events .= '</div>';
            }
        }
        return array("status"=>1, "events"=>$events,"count"=>mysql_num_rows($res));
    }
    
    public function getMonthEvents($userid,$dateString=""){
        if(empty($dateString)){
            $dateString = date('m_Y', time());
            $m = date('m', time());
            $y = date('Y', time());
        }else{
            $b = explode("_", $dateString);
            $m = $b[0];
            $y = $b[1];
        }
        $res = $this->cunity->getDb()->query("SELECT
                        *
                       FROM
                        ".$this->cunity->getConfig("db_prefix")."events
                       WHERE (
                        DATE_FORMAT(start_date,'%m_%Y') = '".mysql_real_escape_string($dateString)."'
                       OR
                        DATE_FORMAT(end_date,'%m_%Y') = '".mysql_real_escape_string($dateString)."'
                       OR(
                            birthday = 1
                            AND
                            DATE_FORMAT(start_date,'%m') = '".mysql_real_escape_string($m)."'
                            AND 
                            founder_id IN (SELECT (CASE WHEN sender != ".$userid." AND receiver = ".$userid." THEN sender WHEN receiver != ".$userid." AND sender = ".$userid." THEN receiver ELSE sender END) AS friend FROM ".$this->cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$userid." OR receiver = '".$userid."') AND status = 1 AND cunityId = 0)
                        )
                       )
                       AND 
                        id IN (SELECT event_id FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE userid = ".$userid." AND status = '3' OR status = '2')
                       ");
        while($data = mysql_fetch_assoc($res)){
            if($data['birthday'] == 0){
                $eventData=$this->showEvent($data['id']);
                $events .= $this->cunity->getTemplateEngine()->createTemplate("events_list",array(
                    "TIME"=>$eventData['time'],
                    "GUESTS"=>count($eventData['guests']),
                    "EVENT_ID"=>$data['id'],
                    "EVENT_HASH"=>$eventData['eventid'],
                    "EVENT_NAME"=>$data['name'],
                    "EVENT_IMG"=>$data['img_file'],
                    "FOUNDER_HASH"=>$eventData['created_hash'],
                    "FOUNDER_NAME"=>$eventData['created_name'],
                    "ATTENDING"=>$eventData['attending'],
                    "events_created_by"=>$this->lang['events_created_by'],
                    "events_select_rsvp"=>$this->lang['events_select_rsvp'],
                    "events_im_yes_attending"=>$this->lang['events_im_yes_attending'],
                    "events_im_maybe_attending"=>$this->lang['events_im_maybe_attending'],
                    "events_im_not_attending"=>$this->lang['events_im_not_attending'],
                    "events_remove_event"=>$this->lang['events_remove_event'],
                    "events_guests"=>$this->lang['events_guests']
                ));
            }else{
                $age   = intval((mktime(0,0,0,$m,1, $y) - strtotime($data['start_date'])) / (3600 * 24 * 365));
                $events .= '<div class="event_list_wrap" style="height: 20px; padding: 5px;">';
                $events .= '<img src="style/'.$_SESSION['style'].'/img/cake.png"/><a class="event_list_name" style="font-size: 12px; padding-left: 5px;" href="profile.php?user='.getUserHash($data['founder_id']).'">'.$this->lang['events_birthday_of']." ".getUserName($data['founder_id']).'&nbsp;('.$age.')</a>&nbsp;<small style="font-weight: bold; color: #333;">('.date('d', strtotime($data['start_date'])).' '.$this->lang['month_'.(date('m', strtotime($data['start_date']))-1)].')</small><br />';
                $events .= '</div>';
            }
        }
        return array("status"=>1, "events"=>$events);
    }
    
    public function getEvents($userid){
        $res = $this->cunity->getDb()->query("SELECT event_id FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE userid = ".$userid);
        while($dataevents = mysql_fetch_assoc($res))
            $myEvents[] = $dataevents['event_id'];
        return $myEvents;
        
    }
    
    public function getEventHash($eventId){
        $res = $this->cunity->getDb()->query("SELECT eventid FROM ".$this->cunity->getConfig("db_prefix")."events WHERE id = '".mysql_real_escape_string($e)."'");
        $data = mysql_fetch_assoc($res);
        return $data['eventid'];
    }

    public function getEventId($e){
        if(is_int($e))
            return $e;
        $res = $this->cunity->getDb()->query("SELECT id FROM ".$this->cunity->getConfig("db_prefix")."events WHERE eventid = '".mysql_real_escape_string($e)."'");
        $data = mysql_fetch_assoc($res);
        return $data['id'];
    }

    protected function createUniqueEventId($string){
        $string = sha1($string);
        $res = $this->cunity->getDb()->query("SELECT COUNT(*) FROM ".$this->cunity->getConfig("db_prefix")."events WHERE eventid = '".$string."'");
        $data = mysql_fetch_assoc($res);
        if($data['COUNT(*)']==0)
            return $string;
        else
            return $this->createUniqueEventId($string.time());
    }
    
    public function getUpcomingEvents($userid){
        $q = "SELECT * FROM ".$this->cunity->getConfig("db_prefix")."events WHERE id IN (SELECT ".$this->cunity->getConfig("db_prefix")."events_guests.event_id FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE ".$this->cunity->getConfig("db_prefix")."events_guests.userid = ".$userid." AND ((SELECT ".$this->cunity->getConfig("db_prefix")."events.end_date FROM ".$this->cunity->getConfig("db_prefix")."events WHERE ".$this->cunity->getConfig("db_prefix")."events.id = ".$this->cunity->getConfig("db_prefix")."events_guests.event_id)>CURDATE()) OR ((SELECT ".$this->cunity->getConfig("db_prefix")."events.end_date FROM ".$this->cunity->getConfig("db_prefix")."events WHERE ".$this->cunity->getConfig("db_prefix")."events.id = ".$this->cunity->getConfig("db_prefix")."events_guests.event_id) = CURDATE() AND (SELECT ".$this->cunity->getConfig("db_prefix")."events.end_time FROM ".$this->cunity->getConfig("db_prefix")."events WHERE ".$this->cunity->getConfig("db_prefix")."events.id = ".$this->cunity->getConfig("db_prefix")."events_guests.event_id) > NOW())) ORDER BY start_date";
        $res1 = $this->cunity->getDb()->query($q);
        $ups = "";
        $i = 0;
        while($data = mysql_fetch_assoc($res1)){
            if($i == 0)
                $ups .= '<div class="events_slide_container" style="margin-top: 10px;">';
            else
                $ups .= '<div class="events_slide_container">';

            if(file_exists('./files/_event_imgs/'.$data['id'].'.jpg'))
                $img = './files/_event_imgs/'.$data['id'].'.jpg';
            else
                $img = 'style/'.$_SESSION['style'].'/img/no_avatar.jpg';
            $ups .= '<a href="events.php?e='.getEventHash($data['id']).'" style="border: 0px; background-image: url(\''.$img.'\'); text-decoration: none;" class="events_slide_image">&nbsp;</a>';
            $ups .= '<div class="events_slide_content">';
            $ups .= '<a style="font-weight: bold; font-size: 13px;" href="events.php?e='.getEventHash($data['id']).'">'.$data['name'].'</a><br /><p style="font-style: italic; font-size: 11px;">';
            if($data['start_date'] == date('Y-m-d', time())&&$data['start_time'] > date('H:i:s', time()))
                $ups .= '<b>'.$this->lang['events_is_today'].'</b>';
            elseif($data['end_date'] >= date('Y-m-d', time())&&$data['start_date'] <= date('Y-m-d', time()))
                $ups .= '<b>'.$this->lang['events_currently'].'</b>';
            else
                $ups .= date($_SESSION['date']['php']['date'], strtotime($data['start_date'])).' um '.date($_SESSION['date']['php']['time'], strtotime($data['start_time']));
            $ups .= '</p>';

            $ups .= '</div><div class="clear"></div></div>';
            $i++;
        }
        return $ups;
    }

    protected function insertNewGuest($eventId,$guest,$status=0){
        return $this->cunity->getDb()->query("INSERT INTO ".$this->cunity->getConfig("db_prefix")."events_guests(
                            `event_id`,
                            `userid`,
                            `status`
                        )VALUES(
                            ".mysql_real_escape_string($eventId).",
                            ".mysql_real_escape_string($guest).",
                            '".$status."'
                        )");
    }
    
    public function addGuests($eventId,array $guests){
        $eventId=$this->getEventId($eventId);
        $guestsA = $this->getEventGuests($eventId);
        $res=array();
        if(!empty($guests)){
            foreach($guests AS $guest)
                if(!in_array($guest,$guestsA))
                    if($this->insertNewGuest($eventId,$guest))
                        $res[]=$this->cunity->getNotifier()->addNotification('invite_event', $guest, $_SESSION['userid'], $eventId);
            return array("status"=>(int)!in_array(false,$res));
        }return array("status"=>0);
    }
    
    protected function isAttending($userid,$eventId){
        $res=$this->cunity->getDb()->query("SELECT COUNT(*) AS count FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE event_id = ".intval($eventId)." AND userid = ".intval($userid)." LIMIT 1");
        $data=mysql_fetch_assoc($res);
        return (bool)$data['count'];
    }
    
    public function getCalendar($dateString=""){
        $calendar = "";

        if(empty($dateString)){
            $month= date('m');
            $year = date('Y');
        }else{
            $data = explode("_",$dateString);
            $month = $data[0];
            $year = $data[1];
        }
        $curDay = date("Y-m-d");
        if($month==12){
            $next = date('m_Y', mktime(0,0,0,01,01,$year+1));
            $last = date('m_Y', mktime(0,0,0,$month-1,01,$year));
        }else if($month==1){
            $last = date('m_Y', mktime(0,0,0,12,01,$year-1));
            $next = date('m_Y', mktime(0,0,0,$month+1,01,$year));
        }else{
            $next = date('m_Y', mktime(0,0,0,$month+1,01,$year));
            $last = date('m_Y', mktime(0,0,0,$month-1,01,$year));
        }
        if($month == date('n') && $year == date('Y'))
            $today = date('d');
        else
            $today = 0;

        $first=date('w', mktime(0,0,0,$month,1,$year));
        $month_count=date('t', mktime(0,0,0,$month,1,$year));
        $last_month_count = date('t', mktime(0,0,0,$month-1,1,$year));

        $curr_month = $this->lang['month_'.$month];
        if($first==0)
            $first=7;
        for($i=1;$i<$first;$i++){
            $cal .= '<td class="cal_cell_last_month" id="'.$last.'">'.($last_month_count-($first-2)).'</td>';
            $last_month_count++;
        }
        $i=1;
        while($i<=$month_count){
            $rest=($i+$first-1)%7;
            $thisday = date('Y-m-d', mktime(0,0,0,$month, $i, $year));
                         $q = "SELECT
                                    COUNT(*)
                                   FROM
                                    ".$this->cunity->getConfig("db_prefix")."events
                                   WHERE (
                                   
                                    id IN (SELECT event_id FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE userid = ".$_SESSION['userid']." AND status = '3' OR status = '2')
                                   AND start_date <= '".$thisday."' AND end_date >= '".$thisday."'
                                    
                                   )
                                   OR
                                    (
                                        birthday = '1'
                                        AND
                                        DATE_FORMAT(start_date,'%m-%d') = '".date("m-d", strtotime($thisday))."'
                                        AND
                                        founder_id IN (SELECT (CASE WHEN sender != ".$_SESSION['userid']." AND receiver = ".$_SESSION['userid']." THEN sender WHEN receiver != ".$_SESSION['userid']." AND sender = ".$_SESSION['userid']." THEN receiver ELSE sender END) AS friend FROM ".$this->cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$_SESSION['userid']." OR receiver = '".$_SESSION['userid']."') AND status = 1 AND cunityId = 0)
                                    )";

            //echo $q.'<br />';
            $res = $this->cunity->getDb()->query($q);
            $data = mysql_fetch_assoc($res);
            $count = $data['COUNT(*)'];

            if($count > 0 && $i == $today)
                $cal .= '<td class="cal_cell cal_cell_today_events" id="'.$thisday.'">'.$i.'</td>';
            elseif($i==$today)
                $cal .= '<td class=" cal_cell cal_cell_today" id="'.$thisday.'">'.$i.'</td>';
            elseif($count > 0)
                $cal .= '<td class="cal_cell cal_cell_events" id="'.$thisday.'">'.$i.'</td>';
            else
                $cal .= '<td class="cal_cell cal_cell_no_events" id="'.$thisday.'">'.$i.'</td>';
            if($rest==0)
                $cal .= '</tr><tr class="cal_row">';
            $i++;
        }//End while

        $i = 1;
        while($i <= 7){
            $cal .= '<td class="cal_cell_next_month" id="'.$next.'">'.$i.'</td>';
            if(date('w', mktime(0,0,0,$month+1,$i,$year)) == 0)
                break;
            $i++;
        }

        $calendar = '
            <span class="last" id="'.$last.'"><<</span>
            <h3 class="month_year">'.$this->lang['month_'.$month]." ".$year.'</h3>
            <span class="next" id="'.$next.'">>></span>
           <table border="0" class="cal_table">
                <tr class="event_mini_cal_table_head" id="calender_table">
                        <td>'.$this->lang['day_mon'].'</td>
                        <td>'.$this->lang['day_tue'].'</td>
                        <td>'.$this->lang['day_wed'].'</td>
                        <td>'.$this->lang['day_thu'].'</td>
                        <td>'.$this->lang['day_fri'].'</td>
                        <td>'.$this->lang['day_sat'].'</td>
                        <td>'.$this->lang['day_sun'].'</td>
                </tr>
                <tr class="cal_row">
                    '.$cal.'
            </table>
            <div id="event_tooltip" style="display: none;">
            </div>
        ';
        return array("status"=>1,"calendar"=>$calendar);
    }
    
    public function respondInvitation($eventId,$action=3){
        switch($action){
            case 3:
                $res = $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."events_guests SET status = '3'  WHERE userid = ".$_SESSION['userid']." AND event_id = ".$eventId);
                $newText=$this->lang['events_you_are_attending'];
            break;
            case 2:
                $res = $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."events_guests SET status = '2'  WHERE userid = ".$_SESSION['userid']." AND event_id = ".$eventId);
                $newText=$this->lang['events_you_may_be_attending'];
            break;
            case 1:
                $res = $this->cunity->getDb()->query("UPDATE ".$this->cunity->getConfig("db_prefix")."events_guests SET status = '1'  WHERE userid = ".$_SESSION['userid']." AND event_id = ".$eventId);
                $newText=$this->lang['events_you_are_not_attending'];
            break;
            case 0:
                $res = $this->cunity->getDb()->query("DELETE FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE userid = ".$_SESSION['userid']." AND event_id = ".$eventId);
                $newText=$this->lang['events_your_rsvp'];
            break;
        }
        return array("status"=>intval($res),"newText"=>$newText);
    }
    
    public function showEvent($eventId){
        $eventData=$this->getEventData($eventId);
        
        $today = showDate("date",time());
        $startDate=showDate("date",$eventData['start_date']);
        $startTime=showDate("time",$eventData['start_time']);
        $endDate=showDate("date",$eventData['end_date']);
        $endTime=showDate("time",$eventData['end_time']);

        $startString = $startDate." ".$startTime;
        $endString = $endDate." ".$endTime;

        if($startDate == $today && $endDate == $today)
            $eventData['time'] = $this->lang['events_today']." ".$startTime." - ".$endTime;
        elseif($startDate == $endDate)
            $eventData['time'] = $startString." - ".$endTime;
        elseif($startDate == $today && $endDate != $today)
            $eventData['time'] = $this->lang['events_today']." ".$startTime." - ".$endString;
        elseif($endDate == $today && $startDate != $today)
            $eventData['time'] = $startString." - ".$this->lang['events_today']." ".$endTime;
        else
            $eventData['time'] = $startString." - ".$endString;

        $invitationStatus=$this->getInvitationStatus($eventId,$_SESSION['userid']);

        if($eventData['privacy']==1||$this->isAttending($_SESSION['userid'],$eventId)||$this->isOwnEvent($eventId)){
            if($this->isOwnEvent($eventId)||$invitationStatus==3)
                $eventData['attending'] = $this->lang['events_im_yes_attending'];
            elseif($invitationStatus==0||!$invitationStatus)
                $eventData['attending'] = $this->lang['events_your_rsvp'];
            elseif($invitationStatus==1)
                $eventData['attending'] = $this->lang['events_im_not_attending'];
            elseif($invitationStatus==2)
                $eventData['attending'] = $this->lang['events_im_maybe_attending'];
        }else
            $eventData['attending'] = $this->lang['events_must_invited'];

        if($eventData['privacy'] == 1){
            $eventData['privacy'] = $this->lang['events_public_event'];
            $eventData['privacy_icon'] = 'unlocked';
        }elseif($eventData['privacy'] == 0){
            $eventData['privacy'] = $this->lang['events_closed_event'];
            $eventData['privacy_icon'] = 'locked';
        }
        
        $eventData['created_name'] = getUserName($eventData['founder_id']);
        $eventData['created_hash'] = getUserHash($eventData['founder_id']);
        $eventData['yes_count'] = count($this->getEventGuests($eventId,3));
        $eventData['maybe_count'] = count($this->getEventGuests($eventId,2));
        $eventData['invited_count'] = count($this->getEventGuests($eventId,0));

        $eventData['guests']=$this->getEventGuests($eventId);
        $friends = $this->cunity->getFriender()->getFriendList($_SESSION['userid']);
        if(!empty($friends))
            foreach($friends AS $friend)
                if(!in_array($friend['id'],$eventData['guests'])&&$friend['id']!=$eventData['founder_id'])
                    $eventData['guestList'] .= '<div class="guest_box" id="'.$friend['id'].'"><img src="'.getAvatarPath($friend['id']).'" class="guest_img"/><span class="guest_name" id="'.$friend['id'].'">'.getUserName($friend['id']).'</span><div class="clear"></div></div>';

        if($eventData['guestList']=="")
            $eventData['guestList'] = newCunityError($this->lang['events_no_guests_to_invite']);
        return $eventData;
    }
    
    public function showDayTooltip($date){
        $list="";
        $q = "SELECT
                *
               FROM
                ".$this->cunity->getConfig("db_prefix")."events
               WHERE(
                start_date <= '".$date."' AND end_date >= '".$date."'
                AND id IN
                (SELECT event_id FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE userid = ".$_SESSION['userid']." AND (status = '3' OR status = '2'))
               )OR(
                    birthday = 1
                    AND
                    DATE_FORMAT(start_date,'%m-%d') = '".date("m-d", strtotime($date))."'
                    AND
                    founder_id IN (SELECT (CASE WHEN sender != ".$_SESSION['userid']." AND receiver = ".$_SESSION['userid']." THEN sender WHEN receiver != ".$_SESSION['userid']." AND sender = ".$_SESSION['userid']." THEN receiver ELSE sender END) AS friend FROM ".$this->cunity->getConfig("db_prefix")."friendships WHERE (sender = ".$_SESSION['userid']." OR receiver = '".$_SESSION['userid']."') AND status = 1 AND cunityId = 0)
                )";
        $res=$this->cunity->getDb()->query($q);
        $eventCount = mysql_num_rows($res);
        $displayDate=($date == date("Y-m-d", time())) ? $this->lang['events_today'] : showDate("date",$date);
        $eventCounter=($eventCount==0||$eventCount>1) ? $eventCount." Events" : $eventCount." Event";
        $list = '<div id="tooltip_head">
            <span id="tooltip_day">'.$displayDate.'</span>
            <span id="tooltip_event_count">'.$eventCounter.'</span>
            <div class="clear"></div>
        </div>
        <div id="tooltip_events_wrapper">';
        if($eventCount>0){
            while($data=mysql_fetch_assoc($res)){
                if($data['birthday'] == 0){
                    if($this->isAttending($_SESSION['userid'],$data['id'])){
                        $startTime = showDate("time",$data['start_time']);
                        $endTime = showDate("time",$data['end_time']);
                        $guests=$this->getEventGuests($data['id'],3);

                        $guests=(count($guests)==0)?$this->lang['events_no']:count($guests);
                        $list .= '
                        <div class="event">
                            <div class="event_img_wrap">
                                <img src="'.$data['img_file'].'" class="event_img"/>
                            </div>
                            <div class="event_info">
                                <a class="event_name" href="events.php?e='.$data['eventid'].'">'.$data['name'].'</a>
                                <br />
                                <span class="event_guests">'.$guests." ".$this->lang['events_guests'].'</span>
                                <br />
                                <span class="event_time">'.$startTime." ".$this->lang['events_till']." ".$endTime.'</span>
                            </div>
                            <div class="clear"></div>
                        </div>';

                    }
                }elseif($data['birthday'] == 1){
                    if(!file_exists('./files/_profile_imgs/'.$data['founder_id'].'.jpg'))
                        $file = './style/'.$_SESSION['style'].'/img/no_profile_img.jpg';
                    else
                        $file = './files/_profile_imgs/'.$data['founder_id'].'.jpg';
                    $list .= '
                    <div class="event">
                        <div class="event_img_wrap">
                            <img src="'.$file.'" class="event_img"/>
                        </div>
                        <div class="event_info">
                            <a class="event_name" style="font-size: 12px;" href="profile.php?user='.getUserHash($data['founder_id']).'">'.$this->lang['events_birthday_of']." ".getUserName($data['founder_id']).'</a>
                            <br />

                            <!--<a class="event_time" href="messages.php?c=sendmessage&userid='.getUserHash($data['founder_id']).'">'.$this->lang['events_send_greetings'].'</a>-->
                        </div>
                        <div class="clear"></div>
                    </div>';
                }
            }
            if(count($events)>3){
                $list .= '
                    <div class="event" style="min-height: 20px;">
                        <div class="event_info" style="float: none; text-align: center; width: auto;">
                            <a class="event_name" style="font-size: 12px;" href="events.php?d='.$date.'">'.$this->lang['events_and_x'].' '.(count($events)-3).' '.$this->lang['events_and_x_other'].'</a>
                        </div>
                        <div class="clear"></div>
                    </div>';
            }
        }else
            $list .= '<p class="no_events">'.$this->lang['events_no_events'].'</p>';
        $list .= '</div>';
        return array("events"=>$list,"status"=>1,"count"=>$eventCount);
    }

    public function getEventRequests(){
        $res = $this->cunity->getDb()->query("SELECT * FROM ".$this->cunity->getConfig("db_prefix")."events WHERE id IN (SELECT event_id FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE userid = ".$_SESSION['userid']." AND status = '0')");
        if(mysql_num_rows($res)>0){
            while($data = mysql_fetch_assoc($res)){
                $eventData=$this->showEvent($data['id']);
                $events .= $this->cunity->getTemplateEngine()->createTemplate("events_list",array(
                    "TIME"=>$eventData['time'],
                    "GUESTS"=>count($eventData['guests']),
                    "EVENT_ID"=>$data['id'],
                    "EVENT_HASH"=>$eventData['eventid'],
                    "EVENT_NAME"=>$data['name'],
                    "EVENT_IMG"=>$data['img_file'],
                    "FOUNDER_HASH"=>$eventData['created_hash'],
                    "FOUNDER_NAME"=>$eventData['created_name'],
                    "ATTENDING"=>$eventData['attending'],
                    "events_created_by"=>$this->lang['events_created_by'],
                    "events_select_rsvp"=>$this->lang['events_select_rsvp'],
                    "events_im_yes_attending"=>$this->lang['events_im_yes_attending'],
                    "events_im_maybe_attending"=>$this->lang['events_im_maybe_attending'],
                    "events_im_not_attending"=>$this->lang['events_im_not_attending'],
                    "events_remove_event"=>$this->lang['events_remove_event'],
                    "events_guests"=>$this->lang['events_guests']
                ));
            }
        }else
            $events = newCunityError($this->lang['events_no_requests']);

        return array('messages'=>$events);
    }
    
    public function getGuestList($eventId,$status=4){
        if(!is_int($eventId))
            $eventId = $this->getEventId($eventId);
        $title = $this->lang['events_guests'].' - ';
        if($status===0)
            $title .= $this->lang['events_sent_invitation'];
        else if($status===2)
            $title .= $this->lang['events_maybe_rsvp'];
        else if($status===3)
            $title .= $this->lang['events_yes_rsvp'];

        if($status<4)
            $q = "SELECT * FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE event_id = ".$eventId." AND `status` = '".$status."'";
        else
            $q = "SELECT * FROM ".$this->cunity->getConfig("db_prefix")."events_guests WHERE event_id = ".$eventId;
        $res = $this->cunity->getDb()->query($q);

        $guest_list = "";
        $guestCount=mysql_num_rows($res);
        if($guestCount===0){
            return array("status"=>1,"count"=>0,"list"=>newCunityError($this->lang['events_no'].' '.$this->lang['events_guests']),"title"=>$title);
        }else{
            while($gData = mysql_fetch_assoc($res)){
                $userhash = getUserHash($gData['userid']);
                $username = getUserName($gData['userid']);
                $img = getAvatarPath($gData['userid']);
                
                $guest_list .= '<div class="main_list_wrap" style="height: 50px;text-align:left">';
    			$guest_list .= '<div class="main_list_img_wrap" style="width: 45px;">';
    			$guest_list .= '<a href="profile.php?user='.$userhash.'"><img src="'.$img.'" style="height:40px;width:40px" class="left_comment"/></a>';
    			$guest_list .= '</div><div class="main_list_cont" style="width: 200px; text-align: left;">';
    			$guest_list .= '<a href="profile.php?user='.$userhash.'" class="main_list_name">'.$username.'</a><br />';
    			$guest_list .= '</div>';
    			$guest_list .= '</div>';
            }
            $guest_list .= '<div class="clear"></div>';
        }
        return array("status"=>1, "list"=>$guest_list,"title"=>$title,"count"=>$guestCount);
    }
}