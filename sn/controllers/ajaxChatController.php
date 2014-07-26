<?php
require_once('../includes/ajaxInit.inc.php');

if ($_GET['action'] == "chatheartbeat") { chatHeartbeat(); }
if ($_GET['action'] == "sendchat") { sendChat(); }
if ($_GET['action'] == "closechat") { closeChat(); }
if ($_GET['action'] == "startchatsession") { startChatSession(); }


if (!isset($_SESSION['openChatBoxes'])) {
	$_SESSION['openChatBoxes'] = array();
}

if (!isset($_SESSION['openHistory'])) {
	$_SESSION['openHistory'] = array();
}

function chatHeartbeat(){
	global $cunity;
	$query = $cunity->getDb()->query("SELECT * FROM ".$cunity->getConfig("db_prefix")."messages WHERE receiver = ".mysql_real_escape_string($_SESSION['userid'])." AND (remote IS NULL OR remote = 'sender') AND sentFrom = 'chat' AND `read` = 0 ORDER BY message_id ASC") or die(mysql_error());
	$itemsArray = array();
	$chatBoxes = array();
	$i = 0;

	while($chat = mysql_fetch_assoc($query)){    
		if (!isset($_SESSION['openChatBoxes'][$chat['cunityId']."-".$chat['sender']."-".$username]) && isset($_SESSION['chatHistory'][$chat['cunityId']."-".$chat['sender']]))
			$itemsArray = $_SESSION['chatHistory'][$chat['cunityId']."-".$chat['sender']."-".$username];
     		
		$username = getUserName($chat['sender']);
		
		$time = showDate('time',$chat['time']);	    
	    	    
	    $data = array("s"=>0,"c"=>$chat['cunityId'],"f"=>$chat['sender'], "m"=>$chat['message'],"u"=>$username,"t"=>$time);
		
        $itemsArray[] = $data;
       
    	$_SESSION['chatHistory'][$chat['cunityId']."-".$chat['sender']."-".$username][] = $data;

		unset($_SESSION['tsChatBoxes'][$chat['cunityId']."-".$chat['sender']."-".$username]);
		$_SESSION['openChatBoxes'][$chat['cunityId']."-".$chat['sender']."-".$username] = $chat['time'];
		$i++;
	}//end while        
	
    $query = $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."messages SET `read` = 1 WHERE receiver = ".mysql_real_escape_string($_SESSION['userid'])." AND `read`=0");	
	echo $cunity->returnJson(array("items"=>$itemsArray));	
    exit(0);
}

function chatBoxSession($chatbox) {
	$items = array();
	if (isset($_SESSION['chatHistory'][$chatbox])) 
		$items = $_SESSION['chatHistory'][$chatbox];
	return $items;
}

function startChatSession() {
    global $cunity;
    $items = array();	
	if (!empty($_SESSION['openChatBoxes']))
		foreach ($_SESSION['openChatBoxes'] as $chatbox => $void){
            $items[$chatbox] = chatBoxSession($chatbox);
        }
	$data_back = array("username"=>getUserName($_SESSION['userid']),"items"=>$items);
    echo $cunity->returnJson($data_back);
	exit(0);
}

function sendChat() {
    global $cunity;
	$from = $_SESSION['userid'];
	$toData = explode("-",$_POST['to']);
	$message = $_POST['message'];
	$to = $toData[1]; 
	$cunityId = $toData[0];

	$_SESSION['openChatBoxes'][$_POST['to']] = date('Y-m-d H:i:s', time());

	$messagesan = sanitize($message);
		
	$time = showDate('time', 'now');
    
    $data_back = array("s"=>1,"c"=>$cunityId,"f"=>$to,"m"=>$messagesan,"u"=>$_SESSION['username'],"t"=>$time);    

	$_SESSION['chatHistory'][$_POST['to']][] = $data_back;
    
	unset($_SESSION['tsChatBoxes'][$_POST['to']]);
	require_once '../classes/Cunity_Messenger.class.php';
	$messenger=new Cunity_Messenger($cunity);
	if($messenger->sendChatMessage($_SESSION['userid'], $to, $message, $cunityId))    
        echo $cunity->returnJson($data_back);    
	exit(0);
}

function closeChat(){
	unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);
	echo 1;
	exit(0);
}

function sanitize($text){	
	$text = str_replace("\n\r","\n",$text);
	$text = str_replace("\r\n","\n",$text);
	$text = str_replace("\n","<br>",$text);
	$text = htmlentities($text, ENT_QUOTES, 'UTF-8');
	return $text;
}

?>