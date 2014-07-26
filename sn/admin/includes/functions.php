<?php
  
	function admin() {
	// logged in && admin?
		if(!$cunity->getSaver()->login() || !$cunity->getSaver()->admin()) {
			header('location: ../index.php');
			exit;
		}
		// adminlogin not confirmed
		elseif(!isset($_SESSION['admin']['confirmed']) || $_SESSION['admin']['confirmed'] !== true) {
			header('location: index.php');
			exit;
		}
		// timeout?
		elseif(!isset($_SESSION['admin']['last_action_stamp']) || $_SESSION['admin']['last_action_stamp'] < time() - 1800) { // 30min sparetime
			header('location: index.php?timeout');
			exit;
		}
		
		// ready to rumble, set new timestamp to prevent a timeout
		$_SESSION['admin']['last_action_stamp'] = time();	
	}
	
	function ftp_copy($source,$destination){	   
        try {
        	$ftp = new Ftp;
        
        	$ftp->connect('www.cunity.net');
        
        	$ftp->login('cunityupdater', 'cunity2012');
        
        	$ftp->fget($source, $destination, Ftp::ASCII);
            
            return true;            
        } catch (FtpException $e) {
        	return false;
        } 
    }
	

	
?>