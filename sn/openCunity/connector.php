<?php
session_name('cunity_sess'.$_SERVER['DOCUMENT_ROOT']);
session_start();
error_reporting(E_ALL & ~E_NOTICE);

$_SESSION['cunity_trunk_folder'] = substr(dirname($_SERVER['SCRIPT_FILENAME']),0,-11);

set_include_path($_SESSION['cunity_trunk_folder'].'/classes');
require '../includes/functions.php';
require 'Cunity.class.php';
require 'Cunity_Connection_Responder.class.php';


$cunity = new Cunity(false);
$responder = new Cunity_Connection_Responder($cunity);

$_POST = $responder->getCryptor()->decryptParameters($_POST,$responder->getCryptor()->readPrivateKeyFromDatabase());

$responder->setPublicKey($_POST['cunityPublicKey']);

$_SESSION['style']= $_POST['style'];
$_SESSION['language'] = $_POST['language'];
$cunity->setLang($_POST['language']);

$responder->setRequestData($_POST);
$responder->checkRequests();
?>