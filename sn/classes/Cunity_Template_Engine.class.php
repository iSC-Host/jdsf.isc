<?php

require_once 'smarty/Smarty.class.php';
class Cunity_Template_Engine extends Smarty {
	private $cunity = null;
    private $path = '/';	    
    private $ext = '.tpl.html';	
	private $file = '';
	private $shown = false;	
	private $template_vars = array();
	private $remote = false;
	private $responder = null;
	private $userid = 0;
	private $controller = false;
	private $langAssigned=false;
	
	public function __construct(Cunity $cunity)
    {
        $this->cunity = $cunity;
        $this->setErrorReporting($this->cunity->getConfig("error_reporting"));
        parent::__construct();
    }
    
    public function __destruct() {
        if(!$this->shown&&$this->file!="")
            $this->show();    
    }
    
    public function setRemote($remote,$userid){
    	$this->remote = $remote;
    	$this->userid=$userid;
    }
    
    public function setConnectionResponder(Cunity_Connection_Responder $res){
    	$this->responder = $res;
    }
    			
	public function setPath($path){
        $this->path = $path;     
    }

    public function getPath(){
    	return $this->path;
    }
    
    public function setController($c){
    	$this->controller = $c;
    }
    
    public function setErrorReporting($e){
        $this->error_reporting = $e;        
    }
    
	public function createTemplate($template, $replaces){
		$file = $_SESSION['cunity_trunk_folder'].'/style/'.$_SESSION['style'].'/templates/modules/'.$template.'.tpl.html';
	    if(file_exists($file)){                
	        foreach($replaces AS $search => $replace){
	            $searchArray[] = '{-$'.$search.'}';
	            $replaceArray[] = $replace;
	        }        
	        $temp = file_get_contents($file);                
	        $temp = str_replace($searchArray,$replaceArray, $temp);        
	        $temp = preg_replace('/{-\$[a-zA-Z0-9_]*}/', "", $temp);                        
	        return $temp;
	    }else
	        return "Error: template not found! (".$file.")";
	}

    public function assign($tpl_var, $value=""){
    	if (is_array($tpl_var)&&empty($value)){
            foreach ($tpl_var as $key => $val) {
                if(!empty($key)){
                	$this->template_vars[$key] = $val;
                    parent::assign($key,$val);
                }
            }
        }else{
            if (!empty($tpl_var)){
            	$this->template_vars[$tpl_var] = $value;
            	parent::assign($tpl_var,$value);
            }                
        }    	    
    }
    
    public function show(){    	  	        
        $this->shown = true;
        if($this->remote){
        	if($this->file!="overall_header"&&$this->file!="overall_footer"&&$this->file!="sidebar"){        		
        		$this->responder->respondProfile($this->getRemoteVars(),$this->userid);
        		$this->resetTemplateVars();
    			return;
        	}else
    			return;    		    		    	
    	}elseif(!$this->controller){
            if(!$langAssigned){
                $this->Assign($this->cunity->getLang());
                $langAssigned=true;
            }

            $this->Assign('cunitysettings', $this->cunity->getSetting());
            $this->Assign('USERNAME', $_SESSION['username']);
            $this->Assign('AVATAR', getAvatarPath($_SESSION['userid']));
            if($this->cunity->isAdmin())
                $this->Assign('STYLE',$this->cunity->getSetting("style_adminpanel"));
            else
                $this->Assign('STYLE',$_SESSION['style']);
	        $this->load_filter("pre","assignScript");	               
	        $this->display('file:'.$this->path.$this->file.$this->ext);
    	}    	    	    	       
    }   

    public function getRemoteVars(){
    	return $this->template_vars;
    }
    
    public function showRemote($tpl_vars){	    
    	foreach($tpl_vars AS $tpl_var => $value){    		    	
    		$this->Assign($tpl_var,$value);
    	}
    	$this->show();
    }
    
    public function Template($file)
    {
        if(!$this->shown&&!empty($this->file))
            $this->show();
        $this->file = $file;
        $this->shown=false;
    }
}


?>