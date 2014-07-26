<?php
ob_start("ob_gzhandler");
require('ov_head.php');

$cunity->getSaver()->check_admin();

set_time_limit(100);

require_once '../classes/Cunity_Connector.class.php';
$connector = new Cunity_Connector($cunity);

if($connector->isConnected()){
    $tplEngine->Template('opencunity_general');
    $tplEngine->Assign('connected',true);
    $tplEngine->show();
}else{
    if(isset($_POST['purpose'])&&isset($_POST['country'])){
    	$connector->createNewRegisterData();
    	$response=$connector->registerNewCunity($_POST['purpose'],$_POST['country']);

        if($response!==false){
            if(!is_array($response)){die("An Error occured!");}
            if($response['status']==1){
            	$data = array('value'=>1);
                $connector->sendUserList();
                $connector->sendGalleryList();
            }
            elseif($response['status']==2)
            	die("Your Cunity is already connected with the Open-Cunity-Network!");
            else
            	die("An Error occured on our server! please try again later!");
        }
    }

    require_once 'Zend/Locale.php';
    $countries = "";
    $z = new Zend_Locale();
    $c = Zend_Locale::getTranslationList('territory',Zend_Locale::BROWSER,2);
    foreach($c AS $key => $country){
    	if($key==$z->getRegion())
    		$countries .= '<option value="'.$key.'" selected="selected">'.htmlentities($country,ENT_QUOTES,'UTF-8').'</option>';
    	else
    		$countries .= '<option value="'.$key.'">'.htmlentities($country,ENT_QUOTES,'UTF-8').'</option>';
    }
    $tplEngine->Template('opencunity_register');
    $tplEngine->Assign('countries',$countries);
    $tplEngine->show();
}
require('ov_foot.php');
ob_end_flush();
?>