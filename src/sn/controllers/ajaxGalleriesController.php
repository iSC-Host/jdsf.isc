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
require_once 'Cunity_Galleries.class.php';

$galleries = new Cunity_Galleries($cunity);

$data_back = json_decode (stripslashes($_REQUEST['json_data']), true);

if(isset($_GET['setPosition'])&&!empty($_GET['setPosition'])){
	$cunity->returnJson(array("status"=>(int)$galleries->updateImagePositions($_GET['sort'])));
}elseif(isset($_POST['c'])&&$_POST['c']=='uploadfile'){ // HANDLE UPLOAD ITSELF
	$result=$galleries->uploadSingleFile($_POST,$_FILES);
	if(isset($data_back['multi']))
		exit;
	header('Content-Type: text/html; charset=utf-8', true,200);
	print '<script language="javascript" type="text/javascript" src="../includes/jquery/jquery.js"></script>';
	print '<script type="text/javascript">	
	var par = jQuery(window.parent.document);
	par.find("#fu'.(int)$_POST['form'].'").removeAttr("disabled").val("");';
	if(isset($result['id'])){
		if($_POST['form'] != 4){
			print 'par.find("#fu'.(int)$_POST['form'].'_sub").html("");
			par.find("#uploaded_wrap").show();
test			par.find("#uploaded_files").append("<p class=\"single-spaced\">'.$_FILES['fu']['name'].'&nbsp;<img src=\"style/'.$_SESSION['style'].'/img/ok.png\" style=\"width: 16px; height: 16px;\" id=\"okay_'.(int)$_POST['form'].'\"></p>");';
		}else		
			print "window.parent.location.href='galleries.php?c=show_album&id=".$_POST['id']."'";		
	}else if(isset($result['status'])&&$result["status"]==0){		
		print 'par.find("#fu'.(int)$_POST['form'].'_sub").html("<img src=\"style/'.$_SESSION['style'].'/img/fail.png\" style=\"width: 16px; height: 16px;\"/> '.$result["error"].'");';
	}
	print '</script>';	
}elseif($data_back['action']=='deleteImage'){    
    echo $cunity->returnJson(array("status"=>(int)$galleries->deleteImage($data_back['imgid'])));
}elseif($data_back['action']=='setCover'){
    echo $cunity->returnJson($galleries->setImageAsCover($data_back['imgid']));
}elseif($data_back['action'] == 'loadImageContainer'){
	$data=$galleries->getImageLikes($data_back['id'],$data_back['cid'],false,$_SESSION['userid'],0);
	echo $cunity->returnJson($data); 
}elseif($data_back['action']=="addStatusComment"){
	$comments=$galleries->addComment($data_back);
	if(!$comments) $data=array("status"=>0);
	else $data=array("status"=>1,"comments"=>$comments);
	echo $cunity->returnJson($data);
}elseif($data_back['action']=="deleteComment"){
	$data=array('status'=>(int)$galleries->deleteComment($data_back['id']));
	echo $cunity->returnJson($data);
}elseif($data_back['action'] == 'like'){
	echo $cunity->returnJson($galleries->likeImage($data_back));    
}elseif($data_back['action'] == 'dislike'){
	echo $cunity->returnJson($galleries->dislikeImage($data_back));
}elseif($data_back['action'] == 'getLikes'){
	$data = $galleries->showLikes($data_back);
	if(!$data) $back=array('persons'=>"",'title'=>"",'status'=>0);
	else $back=array('persons'=>$data['persons'],'title'=>$data['title'],'status'=>1);
	echo $cunity->returnJson($back);
}elseif($data_back['action']=='loadMorePhotos'){	
	echo $cunity->returnJson($galleries->loadMorePhotos($data_back['id'],$data_back['cid']));	
}elseif($data_back['action']=='editTitle'){
	echo $cunity->returnJson(array("status"=>(int)$galleries->updateImageData($data_back['id'],"title",$data_back['title'])));       
}elseif($data_back['action']=="loadAlbumImages"){
	echo $cunity->returnJson(array("status"=>1,"images"=>$galleries->getImagesOfAlbum($data_back['id'],$data_back['cid']))); 
}

?>