<?php

class tplengine {

	private $path = './';
	private $scriptPath = './';
	private $ext = '.tpl.html';
	private $scriptExt = '.js';
	private $file = '';
	private $shown = false;	
	
	// private $delim = '{$VAR}';
	private $delim_first = '{$';
	private $delim_last = '}';
	
	private $variables = array();	
	private $values = array();
	
	private $scriptVariables = array();
	private $scriptValues = array();
	
	function __construct($path = './', $file = '', $ext = '.tpl.html') {
		if($path != $this->path)
			$this->setPath($path);
		if($file != $this->file)
			$this->setTemplate($file);
		if($ext != $this->ext)
			$this->setExt($ext);
	}
	
	function __destruct() {
		if(file_exists($this->path.$this->file.$this->ext) && !$this->shown)
			$this->show();
	}
	
	/*
		SET
	*/
	public function setPath($path) {
		if(is_dir($path)) {
			$this->path = $path;
			return true;
		}			
		
		die('TPLEngine::Error -> Path does not exist. ('.$path.')');
		return false;
	}
	
	public function setScriptPath($path) {
		if(is_dir($path)) {
			$this->scriptPath = $path;
			return true;
		}

		die('TPLEngine::Error -> Path does not exist. ('.$path.')');
		return false;
	}
	
	public function setExt($ext) {
		$this->ext = $ext;
	}
	
	public function setScriptExt($ext) {
        $this->scriptExt = $ext; 
    }
	
	public function Template($file) {
		if($this->shown == false && file_exists($this->path.$this->file.$this->ext))
			$this->show();

		if(file_exists($this->path.$file.$this->ext)) {
			$this->file = $file;
			
			$this->variables = array();
			$this->values = array();
			
			$this->shown = false;
			
			return true;
		}
		
		die('TPLEngine::Error -> File does not exist. ('.$this->path.$file.$this->ext.')');
		return false;
	}
	
	public function setTemplate($file) { // ALIAS
		return $this->Template($file);
	}
	
	/*
		METHODS
	*/
	public function Assign($var, $value = "") 
    {        
        if(is_array($var) && count($var) > 0 && $value == "")
        {
            foreach($var AS $key => $data)
            {
                $this->Assign($key, $data);
            }
            return true;
        }
		elseif(is_array($var) && is_array($value) && count($var) == count($value)) {
			$temp = count($var);
			for($i = 0; $i < $temp; $i++) {				
				$this->Assign($var[$i], $value[$i]);
			}
			return true;
		}
		elseif(is_string($var)) {
			$var = $this->delim_first.$var.$this->delim_last;
			
			if(preg_match('/^{\$[a-zA-Z0-9_]*}$/', $var)) {
				$this->variables[] 	= $var;
				$this->values[] 	= $value;
				return true;
			}
		}
		else {
			die('TPLEngine::Error -> Could not assign Variable ('.$var.')(Both parameters need to be the same type (and if arrays: same item-count))');
			return false;
		}
		
		die ('TPLEngine::Error -> Could not assign Variable ('.$var.')');
		return false;
	}
	
	public function assignScript($var, $script = "")
	{
	    if(is_array($var) && count($var) > 0) 
        {
            foreach($var AS $key => $data)
            {
                $this->AssignScript($key, $data);
            }
            return true;
        }
        elseif(is_string($var))
        {    
            if($script != "")
            {
                if(file_exists($this->scriptPath.$script.$this->scriptExt))
        	    {
        	        $var = $this->delim_first.$var.$this->delim_last;
    
        			if(preg_match('/^{\$[a-zA-Z0-9_]*}$/', $var)) {
        			    $temp = file_get_contents($this->scriptPath.$script.$this->scriptExt);
        				$this->scriptVariables[] = $var;
                        $this->scriptValues[] = '<script language="javascript" type="text/javascript">'.$temp.'</script>';
        				return true;
        			}
                    return true;
                }
            }   
            else {
                die ('TPLEngine::Error -> Could not assign Variable ('.$var.'). Second Parameter missing!');
		        return false;
            }                     
        }
        
        die ('TPLEngine::Error -> Could not assign Script ('.$var.')');
		return false;
    }
	
	public function show($type = '') 
    {
		if(file_exists($this->path.$this->file.$this->ext)) {
			$temp = file_get_contents($this->path.$this->file.$this->ext);
            
            $temp = str_replace($this->scriptVariables, $this->scriptValues, $temp);
			$temp = str_replace($this->variables, $this->values, $temp);
            $temp = $this->if_handler($temp);						

			$this->shown = true;   

			if($type != 'string')
				print $temp;
			else
				return $temp;

			return true;
		}

		die('TPLEngine::Error -> File does not exist. ('.$this->path.$file.$this->ext.')');
		return false;
	}

	public function getString() { // show ALIAS
		return $this->show('string');
	}
	
	private function if_handler($str) { // recursive!
		$inverted = false;
		$condition = '';
		$result = '';

		$if = $this->delim_first.'IF ';
		$else = $this->delim_first.'ELSE:'.$this->delim_last;
		$endif = $this->delim_first.'ENDIF;'.$this->delim_last;

		$else_len = strlen($else);
		$endif_len = strlen($endif);
		$dl_len = strlen($this->delim_last);

		$begin = strpos($str, $if);
		$end = strpos($str, $endif);

		if($begin !== false && $end !== false) {
			$before = substr($str, 0, $begin);
			$after = substr($str, $end + $endif_len);

			$if_passage = substr($str, $begin, $end - $begin + $endif_len);
			// print '<div style="background-color: #666;"><pre>'.htmlentities($if_passage).'</pre></div>';

			$if_a = strpos($if_passage, ' ');
			$if_b = strpos($if_passage, ':');
			$endif_pos = strpos($if_passage, $endif);

			$condition = substr($if_passage, $if_a + 1, $if_b - $if_a - 1);

			$else_pos = strpos($if_passage, $else);
			if($else_pos !== false) {
				$todo[0] = substr($if_passage, $if_b + 1 + $dl_len, $else_pos - ($if_b + 1 + $dl_len));
				// print '<div style="background-color: #666;"><pre>'.htmlentities($todo[0]).'</pre></div>';
				$todo[1] = substr($if_passage, $else_pos + $else_len, $endif_pos - ($else_pos + $else_len));
				// print '<div style="background-color: #666;"><pre>'.htmlentities($todo[1]).'</pre></div>';
			}
			else {
				$todo = substr($if_passage, $if_b + 1 + $dl_len, $endif_pos - ($if_b + 1 + $dl_len));
				// print '<div style="background-color: #666;"><pre>'.htmlentities($todo).'</pre></div>';
			}

			if(strpos($condition, '!') === 0) {
				$condition = substr($condition, 1);
				$inverted = true;
			}

			if(function_exists($condition)) { // if-condition is a function
				$result = call_user_func($condition);
			}
			elseif(defined($condition)) { // if-condition is a constant
				$result = constant($condition);
			}
			else {
				// print 'TPLEngine::Error -> Could not resolve condition / term of IF-Structure ('.$condition.').'; //
				// return false; //
			}

			$str = $before;

				if(!$inverted) {
					if($result) {
						if(is_array($todo))
							$str .= $todo[0];
						else
							$str .= $todo;
					}
					else {
						if(is_array($todo))
							$str .= $todo[1];
					}
				}
				else {
					if(!$result) {
						if(is_array($todo))
							$str .= $todo[0];
						else
							$str .= $todo;
					}
					else {
						if(is_array($todo))
							$str .= $todo[1];
					}
				}

			$str .= $after;

			return $this->if_handler($str);
		}
		elseif($begin !== false || $end !== false)
			print 'TPLEngine::Error -> IF-Structure not correct.';

		return $str;
	}

}

?>