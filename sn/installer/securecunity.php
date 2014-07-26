<?php

// (C) Dr. M. Weihrauch/Smart In Media GmbH & Co. KG 2011, 10th of March, 9 p. m.
session_start(); 
error_reporting(0);   //error reporting has to be off as php sends its errors (like database connect) via echo and hampers the ajax-connection
//Variables
$status=0;
$statusmessage='';
$data_back = json_decode (stripslashes($_REQUEST['json_data']), true);

if ($_SESSION['lang_cur']=='de') require('./lang/ajax_de.php');
else require('./lang/ajax_en.php');


// JSON_ENCODE2 as json_encode() doesn't convert German and other characters correctly (ä ö ü ß)
function json_encode2($jsonarray){
        foreach($jsonarray AS &$text)
        {
            $text=utf8_encode($text);
        }
        unset($text);
        $jsonarray=json_encode($jsonarray);
        return $jsonarray;
        }




//INSTALLSTEP = 16: DELETE INSTALLAJAX.PHP AND THE INSTALL-CUNITY.PHP AS A MEASURE TO SECURE CUNITY    
if ($data_back['installstep']=='16')
{
    
    if (!file_exists('./installed.php'))
    {
        $file = fopen ("./installed.php","w");
    	fputs ($file, "<?php\n?>");
    	fclose ($file);
	}
   
   if (!file_exists('./installajax.php') && !file_exists('../install-cunity.php'))
    {
        
        $status=1;
        $statusmessage=$secure_sec1;
    }
    else 
        {
          if (file_exists('../install-cunity.php'))
          {
               if (unlink('../install-cunity.php')) $install_cunity=1;
               else $install_cunity=0;
              
          }
          else $install_cunity=1;
          
          if (file_exists('./installajax.php'))
          {
               if (unlink('./installajax.php')) $installajax=1;
               else $installajax=0;

          }
          else $installajax=1;
          
          if ($install_cunity==1 && $installajax==1)
               {
                  $status=1;
                  $statusmessage=$secure_sec2;
              }  
              else if ($install_cunity==1 && $installajax==0)
              {
                  $status=0;
                  $statusmessage=$secure_sec3;   
              }
              else if ($install_cunity==0 && $installajax==1)
              {
                  $status=0;
                  $statusmessage=$secure_sec4;
              }
              else if ($install_cunity==0 && $installajax==0)
              {
                  $status=0;
                  $statusmessage=$secure_sec5;
              }
        }

    $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
    $jsonData=json_encode2($returndata);
    echo $jsonData; // back to the client

} // CLOSING Install Step 16

//INSTALLSTEP = 17: Secure Linux folders
if ($data_back['installstep']=='17')
{
    $cur_dir=dirname($_SERVER['SCRIPT_FILENAME']); ////READ CURRENT DIRECTORY
    $install_pos=strrpos($cur_dir, '/installer');
    $cur_dir=substr($cur_dir, 0, $install_pos);     
    
    $errors = array();
    
    $errors[]=chmod ($cur_dir.'/classes', 0755);
    $errors[]=chmod ($cur_dir.'/languages', 0755);
    $errors[]=chmod ($cur_dir.'/config.php', 0644);
    
    $error_combined=1;
    foreach ($errors as $value)
       {
           if (!$value) $error_combined=0;
       } 
    
    
   if ($error_combined==0)
    {

        $status=0;
        $statusmessage=$secure_sec6;
    }
    else
        {
                  $status=1;
                  $statusmessage=$secure_sec7;
        }
              
        
    $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
    $jsonData=json_encode2($returndata);
    echo $jsonData; // back to the client

} // CLOSING Install Step 17        

//INSTALLSTEP = 18: Renaming .htinaccess into .htaccess so it will work
if ($data_back['installstep']=='18')
{
    rename("./.htinaccess", "./.htaccess");
    
} // CLOSING Install Step 18

    
?>