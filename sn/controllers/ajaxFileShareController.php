<?php
require_once '../includes/ajaxInit.inc.php';

$data_back = json_decode (stripslashes($_REQUEST['json_data']), true);
require_once 'Cunity_Filesharing.class.php';
$filesharing = new Cunity_Filesharing($cunity);
if(isset($_POST['c'])&&$_POST['c']=='uploadfile'){
	$result=$filesharing->uploadSingleFile($_FILES);
	header('Content-Type: text/html; charset=utf-8', true,200);
 	if(isset($_POST['multi'])){
        list($space,$used) = $filesharing->getUserSpace();
        print ($space > 0) ? $used/$space*100 : 0;
        exit;
    }
	print '<script language="javascript" type="text/javascript" src="../includes/jquery/jquery.js"></script>';
	print '<script type="text/javascript">
	var par = jQuery(window.parent.document);
	par.find("#fu'.(int)$_POST['form'].'").removeAttr("disabled").val("");';
	if(isset($result['status'])&&$result["status"]==0)
	    print 'par.find("#fu'.(int)$_POST['form'].'_sub").html("<img src=\"style/'.$_SESSION['style'].'/img/fail.png\" style=\"width: 16px; height: 16px;\"/> '.$result["error"].'");';
	else{
        print '
        par.find("#uploaded_wrap").show();
		par.find("#uploaded_files").append("<p class=\"single-spaced\">'.$_FILES['fu']['name'].'&nbsp;<img src=\"style/'.$_SESSION['style'].'/img/ok.png\" style=\"width: 16px; height: 16px;\" id=\"okay_'.(int)$_POST['form'].'\"></p>");';
    }
	print '</script>';
}else if($data_back['action']=="loadMyFilesList")
    echo $cunity->returnJson($filesharing->loadFiles());
else if($data_back['action']=="loadMyShareList")
    echo $cunity->returnJson($filesharing->loadSharedFiles());
else if($data_back['action']=="deleteFile")
    echo $cunity->returnJson($filesharing->deleteFile($data_back['fileid'],true));
else if($data_back['action']=='fileDetails')
    echo $cunity->returnJson($filesharing->getFileDetails($data_back['fileid'],$data_back['cunityId']));
else if($data_back['action']=="addComment")
    echo $cunity->returnJson($filesharing->addComment($data_back['id'],$data_back['message']));
else if($data_back['action']=="like")
    echo $cunity->returnJson($filesharing->likeFile($data_back['id']));
else if($data_back['action']=="dislike")
    echo $cunity->returnJson($filesharing->dislikeFile($data_back['id']));
else if($data_back['action']=="deleteComment")
    echo $cunity->returnJson($filesharing->deleteComment($data_back['id']));
else if($data_back['action']=="getLikes")
    echo $cunity->returnJson($filesharing->showLikes($data_back['id'],$data_back['type']));
else if($data_back['action']=="getFriendsForShare")
    echo $cunity->returnJson($filesharing->getFriendsForShare($data_back['id']));
else if($data_back['action']=="shareFile")
    echo $cunity->returnJson($filesharing->shareFile($data_back['id'],$data_back['users']));
else if($data_back['action']=="unshare")
    echo $cunity->returnJson($filesharing->unshareFile($data_back['fileid']));
else if($data_back['action']=="deleteMultipleFiles")
    echo $cunity->returnJson($filesharing->deleteMultipleFiles(explode(",",$data_back['fileids'])));
?>