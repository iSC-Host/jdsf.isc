<?php
ob_start("ob_gzhandler");
require('ov_head.php');
    if(isset($_GET['setlang']))
    {
        if($cunity->getSaver()->login())
            $cunity->getDb()->query("UPDATE ".$cunity->getConfig("db_prefix")."users SET lang = '".mysql_real_escape_string($_GET['setlang'])."' WHERE userid = '".$_SESSION['userid']."'");
        else
            $_SESSION['language'] = $_GET['setlang'];
            
        header("location: index.php");
        exit;
    }
	// Login
	if(isset($_POST['admin'])) {
		$failed = false;

		$res = $cunity->getDb()->query("SELECT nickname, password, groupid FROM ".$cunity->getConfig("db_prefix")."users WHERE mail = '".mysql_escape_string($_POST['email'])."' AND password = '".mysql_escape_string(sha1($_POST['pass']))."' LIMIT 1");
		$data = mysql_fetch_assoc($res);
		if(mysql_num_rows($res) == 1 && $data['nickname'] == $_SESSION['nickname'] && $cunity->getSaver()->admin() && $cunity->getSaver()->login())
		{
			session_regenerate_id(); // prevent session fixation

			$_SESSION['admin']['confirmed'] = true;
			$_SESSION['admin']['last_action_stamp'] = time();
		}
		elseif(!$cunity->getSaver()->login()) { // doppelt hält besser ^.^
			header('location: ../index.php');
			exit;
		}
		elseif(!$cunity->getSaver()->admin()) { //       ''
			$error = '<p>'.$langadmin['index_missing_rights'].'</p>';
		}
		else {
			$error = '<p>'.$langadmin['indes_wrong_user'].'</p>';
		}
	}

	// logged in && admin?
	if(!$cunity->getSaver()->login() || !$cunity->getSaver()->admin()) {
		header('location: ../index.php');
		exit;
	}
	// adminlogin not confirmed
	elseif(!isset($_SESSION['admin']['confirmed']) || $_SESSION['admin']['confirmed'] !== true || isset($_GET['timeout'])) {
		if(isset($_GET['timeout'])) {
			$error = $langadmin['index_no_action'];
			$_SESSION['admin']['confirmed'] = false;
		}

		$tplEngine->Template('login');
		$tplEngine->Assign('MSG', $error);

	}
	// alright, good to go!
	elseif($_SESSION['admin']['confirmed'] === true && $cunity->getSaver()->login() && $cunity->getSaver()->admin()) {
		header('location: overview.php');
		exit;
	}else {
		header('location: ../index.php');
		exit;
	}

require('ov_foot.php');
ob_end_flush();
?>