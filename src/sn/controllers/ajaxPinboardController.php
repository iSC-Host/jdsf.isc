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
   
require_once '../includes/ajaxInit.inc.php';

require_once 'Cunity_Pinboard.class.php';

$pinboard = new Cunity_Pinboard($cunity);
$data_back = json_decode(stripslashes($_REQUEST['json_data']), true);
$photocount=$_SESSION['count'];

if(isset($data_back['p'])){
    $pinboard_id = $data_back['p'];
}else{
    $pinboard_id = 0;
}
if(isset($data_back['r'])){
    $receiver = $data_back['r'];
}else{
    $receiver = "main";
}

clearstatcache();
if($data_back['c']=="imgUpload"){
	$pinboard->imgUpload($_POST, $_FILES);
}else if($data_back['action']=="insertPinboardStatus"){
	echo $cunity->returnJson($pinboard->insertPinboardStatus($data_back));
}else if($data_back['action']=="loadPinboard"){
	echo $cunity->returnJson($pinboard->loadPinboard($data_back));
}else if($data_back['action']=="addStatusComment"){	
	$comments=$pinboard->addStatusComment($data_back);
    if(!$comments) $data=array("status"=>0);
    else $data=array("status"=>1,"comments"=>$comments);
	echo $cunity->returnJson($data);
}else if ($data_back['action']=="deleteStatus"){	
	$result=$pinboard->deleteStatus($data_back['id'],($data_back['cid']>0),$data_back['cid'],$_SESSION['userid'],0);
	echo $cunity->returnJson(array("status"=>(int)$result));
}else if($data_back['action']=="deleteComment"){        
	$data=array('status'=>(int)$pinboard->deleteComment($data_back['id']));    	
	echo $cunity->returnJson($data);
}else if($data_back['action'] == 'like'){    
    echo $cunity->returnJson($pinboard->likeStatus($data_back));    
}else if($data_back['action'] == 'dislike'){
    echo $cunity->returnJson($pinboard->dislikeStatus($data_back));
}else if($data_back['action'] == 'checkVideo'){
	echo $cunity->returnJson($pinboard->checkVideo($data_back['str']));    
}else if($data_back['action'] == 'loadComments'){
    $comments = $pinboard->loadComments($data_back['id']);    
    if(!$comments)
		$data=array('comments'=>"",'status'=>0);
	else 
		$data=array('comments'=>$output,'status'=>1);
	echo $cunity->returnJson($data);
}else if($data_back['action'] == 'getLikes'){             
    $data = $pinboard->showLikes($data_back);
    if(!$data) $back=array('persons'=>"",'title'=>"",'status'=>0);
    else $back=array('persons'=>$data['persons'],'title'=>$data['title'],'status'=>1);    
    echo $cunity->returnJson($back);
}
?>