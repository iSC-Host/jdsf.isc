<?php
function smarty_prefilter_assignScript($params, &$tplEngine)
{                                                                                       
    while(0 != preg_match('/{-\$SCRIPT-[a-zA-Z0-9_]*}/', $params, $matches))
    {        
        $scriptinfo = explode('-', $matches[0]);        
        $scriptname = strtolower(substr($scriptinfo[2], 0,-1));
        $temp = file_get_contents('./includes/javascript/'.$scriptname.'.js');        
        $tempScript = '<script language="javascript" type="text/javascript">'.$temp.'</script>';                               
        $params = str_replace('{-$SCRIPT-'.substr($scriptinfo[2],0,-1).'}', $tempScript, $params);
    }     
    return $params;   
}
?>