<?php
class Cunity_Security {
	
	private $cunity;
	
    public function Cunity_Security(Cunity $cunity) {
		$this->cunity = $cunity;
    } 

	public function checkInstallation(){
	    //If Cunity is not installed
	    if (version_compare(PHP_VERSION, '5.0.0', '<') ) 
	    	exit("Sorry, Cunity will only run on PHP version 5 or greater!\n");
	    else if ($this->cunity->getConfig("cunity_installed")==0){
	    	if (file_exists('./installer/install.php')){
	        	header("Location: ./installer/install.php");
	        }else die ('Your config.php file shows that your Cunity has not been installed (variable \$cunityConfig["cunity_installed"] should be 1, if installed correctly)');
	    }
    }
    
	public function fingerprint() {
		return sha1(/* $ip[0].$ip[1]. */$_SERVER['HTTP_USER_AGENT']);
	}
        
	public function login($isController=false) {
		// logged_in Check
		if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
			// Fingerprint / ID Check
			if(!$isController)
				return ($_SESSION['fingerprint'] == $this->fingerprint());
			return true;
			
		}
	
		return false;
	}
	
	public function admin() {
		if($this->login()) {
			if($_SESSION['groupid'] == 2 || $_SESSION['groupid'] == 1)
			  return true;
		}
	
		return false;
	}
	
	public function check_admin() {
	// logged in && admin?
		if(!$this->login() || !$this->admin()) {
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
	
	public function login_required($isController = false) {       
	    if(!$this->login($isController)) {
	        if(!$isController){
	            if(!isset($_SESSION['login_referrer']))
	    	       $_SESSION['login_referrer'] = basename($_SERVER['REQUEST_URI']);
    	    	   header("location: register.php?c=login");	    	    	
	        }else
	        	header("HTTP/1.0 403 Access Denied!");	        	
			exit;
		}                	    	
	}
	
	function module_power() {
	    $f = $_SERVER['PHP_SELF'];
	    $finfo = pathinfo($f);
	    $file = $finfo['filename'];	    
	    if(!$this->cunity->getModule($file))
	    {
	        header("location: index.php");
	        exit;
	    }
	}
}

?>