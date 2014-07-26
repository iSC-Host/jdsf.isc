<?php
/*CUNITY(R) V1.0beta - An open source social network / "your private social network"
Copyright (C) 2011 Smart In Media GmbH & Co. KG
CUNITY(R) is a registered trademark of Dr. Martin R. Weihrauch
http://www.cunity.net


    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or any later version.

1. YOU MUST NOT CHANGE THE LICENSE FOR THE SOFTWARE OR ANY PARTS HEREOF! IT MUST REMAIN AGPL.
2. YOU MUST NOT REMOVE THIS COPYRIGHT NOTES FROM ANY PARTS OF THIS SOFTWARE!
3. NOTE THAT THIS SOFTWARE CONTAINS THIRD-PARTY-SOLUTIONS THAT MAY EVENTUALLY NOT FALL UNDER (A)GPL!
4. PLEASE READ THE LICENSE OF THE CUNITY SOFTWARE CAREFULLY!

	You should have received a copy of the GNU Affero General Public License
    along with this program (under the folder LICENSE).
	If not, see <http://www.gnu.org/licenses/>.

   If your software can interact with users remotely through a computer network,
   you have to make sure that it provides a way for users to get its source.
   For example, if your program is a web application, its interface could display
   a "Source" link that leads users to an archive of the code. There are many ways
   you could offer source, and different solutions will be better for different programs;
   see section 13 of the GNU Affero General Public License for the specific requirements. */

error_reporting(0);   //error reporting has to be off as php sends its errors (like database connect) via echo and hampers the ajax-connection
session_start();
//Variables
$status=0;
$statusmessage='';
$error_config='';

if ($_SESSION['lang_cur']=='en') require('./lang/ajax_en.php');
if ($_SESSION['lang_cur']=='de') require('./lang/ajax_de.php');

$data_back = json_decode (stripslashes($_REQUEST['json_data']), true);

if (!file_exists('./phpmailer/class.phpmailer.php') && $data_back['installstep']!='')
    {

	 $statusmessage=$warning_missing;
      $status = 0;

          $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
            die();

    }
    
require ('../config.php');
require('./phpmailer/class.phpmailer.php');

if ($cunityConfig['smtp_port']==null) $cunityConfig['smtp_port']=25;


if (!$data_back['installstep']) die ('<h1>'.$warning_script.'</h1>');

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

if ((file_exists('./installed.php') || $cunityConfig["cunity_installed"]==1) && $data_back['installstep']!='')
    {
        
	 $statusmessage=$warning_installed;
                $status = 0;
                
          $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
            die();
      
    }
    
    else if (file_exists('./installed.php'))
       {
           die ('<h1>'.$warning_installed.'</h1>');

       }

            

//DATABASE CONNECTION FUNCTION
function db_connector() {
    global $data_back, $status, $statusmessage, $cunityConfig, $connection_failed, $connection_ok, $warning_db;
    try
            {
            global $db_connect;
           
            if (!$db_connect = mysql_connect($cunityConfig['db_host'], $cunityConfig['db_user'], $cunityConfig['db_pass'])) {
            throw new Exception($warning_db);
                                }
           //If the exception is thrown, this code will not be run
            $status=1;
            $statusmessage=$connection_ok;
            if (!mysql_select_db($cunityConfig['db_name'])) {
            throw new Exception($connection_failed.' "'.$cunityConfig['db_name'].'"!');
                                }
            mysql_set_charset('utf8',$db_connect);                     
           //If the exception is thrown, this code will not be run
            
            }


catch(Exception $e)
            {
        $error = $e->getMessage();
        $status=0;
        $statusmessage=$error;
        $returndata=array('status'=>0, 'statusmessage'=>$statusmessage);
        $jsonData=json_encode2($returndata);
        echo $jsonData; // back to the client
        exit();
             }

    
    }



// HERE IS THE FUNCTION TO WRITE THE CONFIG.PHP FILE 

function write_config () {
    global $cunityConfig;
    global $web_name;
    global $error_config;
    $cunityConfig['email_header'] = '<br />'.$automatic_msg.' '.$web_name.'<br />';
    $cunityConfig['email_footer'] = '<br/>'.$greeting.'<br />'.$web_name.' Team';
    
    if (!file_exists("../config.php"))
            {
                $error_config=$config_error3; //The file config.php does not exist. It should be provided in the installation-root directory. Please check the installation directory!
                return false;
            }
    
    
	if (!$file = fopen ("../config.php","w"))
	{
        if(chmod ("../config.php", 0777))
                {
                    
                if (!$file = fopen ("../config.php","w"))
                    {
                        $error_config=$config_error1; //The installer does not have the permission to write into the config.php although tried to change permission to 0777! Please grant access!
                        return false;
                    }
                    
                else {
                      fclose ($file);
                      write_config();
                }  
                    
                }
                else {
                      $error_config=$config_error2; //The installer is not able to write into the config.php and cannot change the permission.
                      return false;
                }    
                    
    }
    
    else {
        	fputs ($file, "
            <?php\n			
			\$cunityConfig['db_host'] = '".utf8_decode($cunityConfig['db_host'])."';\n
        	\$cunityConfig['db_user'] = '".utf8_decode($cunityConfig['db_user'])."';\n
        	\$cunityConfig['db_pass'] = '".utf8_decode($cunityConfig['db_pass'])."';\n
        	\$cunityConfig['db_name'] = '".utf8_decode($cunityConfig['db_name'])."';\n
        	\$cunityConfig['db_prefix'] = '".utf8_decode($cunityConfig['db_prefix'])."';\n
        	\$cunityConfig['smtp_host'] = '".utf8_decode($cunityConfig['smtp_host'])."';\n
        	\$cunityConfig['smtp_port'] = ".utf8_decode($cunityConfig['smtp_port']).";\n
            \$cunityConfig['smtp_username'] = '".utf8_decode($cunityConfig['smtp_username'])."';\n
            \$cunityConfig['smtp_password'] = '".utf8_decode($cunityConfig['smtp_password'])."';\n
        	\$cunityConfig['smtp_method'] = '".utf8_decode($cunityConfig['smtp_method'])."';\n
            \$cunityConfig['smtp_auth'] = '".utf8_decode($cunityConfig['smtp_auth'])."';\n
        	\$cunityConfig['smtp_sender_address'] = '".utf8_decode($cunityConfig['smtp_sender_address'])."';\n
            \$cunityConfig['smtp_sender_name'] = '".utf8_decode($cunityConfig['smtp_sender_name'])."';\n
            \$cunityConfig['email_header'] = '".utf8_decode($cunityConfig['email_header'])."';\n
            \$cunityConfig['email_footer'] = '".utf8_decode($cunityConfig['email_footer'])."';\n
            \$cunityConfig['cunity_installed'] = ".$cunityConfig['cunity_installed'].";  //Leave 1, if Cunity is installed\n
			\$cunityConfig['error_reporting'] = E_ALL & ~E_NOTICE; //Set to -1, if you want to see all errors/warnings, leave E_ALL & ~E_NOTICE, if not\n
        	?>");
        	fclose ($file);
        	return true;
        	}
}


//---------------------------------------------------------------------------------
//FUNCTION TO READ THE SERVER DIRECTORIES FOR SELECTION OF THE FILESHARING FOLDER
//---------------------------------------------------------------------------------


function readdirectory($folder) {
$read_dir=array();
$tempdir=array(); 
 
if ($handle = opendir($folder))
            {
           
             while (false !== ($temp = readdir($handle)))
            {
                
                if ($temp=='null') $temp='';
            if (is_dir($folder.'/'.$temp)) {
               
                if ($temp=='..') $temp = 'GO UP ONE DIR..';

                if ($temp!='.' && $temp!='GO UP ONE DIR..' && $temp!='') $tempdir[]= '<img src="img/folder-open.png"/>&nbsp;<span class="filefolder">'.$temp.'</span><br/>';
                if ($temp=='GO UP ONE DIR..') $tempdir[] = '<img src="img/arrow-skip-090.png"/>&nbsp;<span class="filefolder">'.$temp.'</span><br/>';
            }
            else {
            //$read_dir = $read_dir.'<img src="img/document-text.png"/>&nbsp;'.$temp."<br/>";
            $read_dir[] = '<img src="img/document-text.png"/>&nbsp;'.$temp."<br/>";
            
            }

            } // close while loop
            closedir($handle);
            
                }
            else {
                $status = 0;
                $statusmessage=$status_dir;
                $read_dir2=$status_dir;
                $tempdir2='';
            }
            
    //if ($read_dir=='null' || $read_dir==null) $read_dir='';
    
    function comp($a, $b)
{
    return(strcmp(strtolower($a), strtolower($b)));
}
    
    usort($tempdir,"comp");
    usort($read_dir,"comp");
    for ($i=0;$i<count($read_dir);$i++){
         $read_dir2=$read_dir2.$read_dir[$i];
          
    }
    for ($i=0;$i<count($tempdir);$i++){
         $tempdir2=$tempdir2.$tempdir[$i];
    }
    
    $return_dir = array('read_dir' => $read_dir2, 'temp_dir' => $tempdir2);
    return $return_dir;
} // FUNCTION readdirectory closed 





    
//installstep: this variable shows, where the user is currently within the installer

//-----------------------------------------------------------
//INSTALLSTEP = 3 IS THE TESTING OF THE DATABASE CONNECTION  
//-----------------------------------------------------------  
if ($data_back['installstep']=='3')
{
  // This happens, when the user tests the database connection on Step Nr. 3
  // So here, only the db-connection is tested
  // The variables are: user (database user), host, pass, name (name of database)
    
            $cunityConfig['db_host'] = $data_back['host'];
            $cunityConfig['db_user'] = $data_back['user'];
            $cunityConfig['db_pass'] = $data_back['pass'];
            $cunityConfig['db_name'] = $data_back['name'];
            $cunityConfig['db_prefix'] = $data_back['prefix'];

    
    db_connector();
    $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
    $jsonData=json_encode2($returndata);
    echo $jsonData; // back to the client
    mysql_close($db_connect);
  

} // CLOSING Install Step 3

//----------------------------------------------------------------------------------------------        
//INSTALLSTEP = 4 IS THE INSTALLATION OF THE DATABASE DUMP INTO THE DATABASE ASSIGNED BY THE USER
//----------------------------------------------------------------------------------------------
        
if ($data_back['installstep']=='4')
{
  // This happens, when the user installs the database on Step Nr. 3
  // The variables are: user (database user), host, pass, name (name of database)

    
            $cunityConfig['db_host'] = $data_back['host'];
            $cunityConfig['db_user'] = $data_back['user'];
            $cunityConfig['db_pass'] = $data_back['pass'];
            $cunityConfig['db_name'] = $data_back['name'];
            $cunityConfig['db_prefix'] = $data_back['prefix'];
            
            db_connector();
            
            if (!write_config())
            {
                $status = 0;
                $statusmessage=$error_config;   
            }
             else
                { 
                            $status=1;
            
                       //If the exception is thrown, this code will not be run
                       //   HERE IS THE CODE TO CREATE THE ACTUAL DATABASE; THE DUMP FILE IS READ AND
                       // THEN IMPORTED INTO THE DATABASE
            
            
                        $file = file_get_contents('installdump.sql');
                        $sql_commands = explode(';',$file);
            
            
                        for($i=0;$i<count($sql_commands)-1 && $error=='';$i++)
                        {
                      
                            
                        
                        $sql_commands[$i]=str_replace('cunity_', $data_back['prefix'], $sql_commands[$i]);
                        if (strpos(substr($sql_commands[$i], 0, 5), "--")===false && strpos(substr($sql_commands[$i], 0, 5), "/*")===false && $sql_commands[$i]!="") {
                        if(!mysql_query($sql_commands[$i])) 
                                        {
                                        $error =  mysql_error()." in command: ".$sql_commands[$i]." in command: ".$i." with last command: ".$sql_commands[$i-1];
                                        }
                              }          
                        }
            
                        if(!$error){
                             $statusmessage=$status_db1;
                             $status = 1;
                        }else{
                            $statusmessage=$status_db2.": ".$error;
                            $status = 0;
                         }

                } //else-block of "if (!write_config()))"

            $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
            mysql_close($db_connect);    


} // CLOSING DATABASE IMPORT INSTALLSTEP 4




//----------------------------------------------------------------------------------------------
//INSTALLSTEP=5: WRITE THE WEBSITE NAME; SLOGAN; URL; CONTACT ADDRESS
//----------------------------------------------------------------------------------------------

if ($data_back['installstep']=='5')
{

    db_connector();

         $status=1;

           //If the exception is thrown, this code will not be run
           //   HERE IS THE CODE TO CREATE THE ACTUAL DATABASE; THE DUMP FILE IS READ AND
           // THEN IMPORTED INTO THE DATABASE
        
        $sql_query[0] = "UPDATE ".$cunityConfig['db_prefix']."settings SET value = '".$data_back['name']."' WHERE name='name'";
        $sql_query[1] = " UPDATE ".$cunityConfig['db_prefix']."settings SET value = '".$data_back['slogan']."' WHERE name='slogan'";
        $sql_query[2] = " UPDATE ".$cunityConfig['db_prefix']."settings SET value = '".$data_back['url']."' WHERE name='url'";
        $sql_query[3] = " UPDATE ".$cunityConfig['db_prefix']."settings SET value = '".$data_back['email']."' WHERE name='contact_mail'";
        $sql_query[4] = " UPDATE ".$cunityConfig['db_prefix']."settings SET value = '".$data_back['lang']."' WHERE name='language'";

        for ($i=0;$i<5&&$status==1;$i++){
        $result = mysql_query($sql_query[$i]);
            if(!$result){
                 $statusmessage= $status_db3;
                 $status = 0;
            }else{
                $statusmessage=$status_db4;
                $status = 1;
             }
    
            }
        
        
        $sql_query[4] = "SELECT * FROM ".$cunityConfig['db_prefix']."settings";
        $result2 = mysql_query($sql_query[4]);
        $result3 = mysql_fetch_array($result2);
        $cunity_version=$result3['version'];
          
            $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage, 'cunity_version'=>$cunity_version);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
            mysql_close($db_connect);



} // CLOSING WEBSITE SETTINGS INSTALLSTEP 5


//-----------------------------------------            
//INSTALLSTEP=6: WRITE THE SMTP SETTINGS
//-----------------------------------------
if ($data_back['installstep']=='6')
{ 
     try{
            $cunityConfig['smtp_host'] = $data_back['host'];
            $cunityConfig['smtp_port'] = (int)$data_back['port'];
            $cunityConfig['smtp_username'] = $data_back['user'];
            $cunityConfig['smtp_password'] = $data_back['pass'];
            $cunityConfig['smtp_sender_address'] = $data_back['email'];
            $cunityConfig['smtp_sender_name'] = $data_back['sender'];
            $web_name = $data_back['web_name'];
            $cunityConfig['smtp_auth'] = $data_back['smtp_auth'];
            $cunityConfig['smtp_method'] = $data_back['smtp_method'];
            }
        
        catch(Exception $e)
            {
        $error = $e->getMessage();
        $status=0;
        $statusmessage='{$status_config1}';
        $returndata=array('status'=>0, 'statusmessage'=>$statusmessage);
        $jsonData=json_encode2($returndata);
        echo $jsonData; // back to the client
        exit();
             }
       
                $statusmessage=$status_config2;
                $status = 1;
            $cunityConfig['cunity_installed']=0; // If somebody returned from step 7, then this will again set the cunity_installed to 0.
            if (!write_config())
                {
                    $status=0;
                    $statusmessage=$error_config;
                }

            $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
             

} // CLOSING SMTP SETTINGS INSTALLSTEP 6   


//--------------------------------------------------
//INSTALLSTEP=7: WRITE THE ADMIN ACCOUNT
//--------------------------------------------------
if ($data_back['installstep']=='7')
{

    db_connector();

       $status=1;
       $sql_query[3] = "SELECT * FROM ".$cunityConfig['db_prefix']."users WHERE nickname='".$data_back['nickname']."' OR mail='".$data_back['email']."'";
       $sql_query[0] = "INSERT INTO ".$cunityConfig['db_prefix']."users (nickname, password, mail, groupid, invisible, space, vf_req, verif_mail) VALUES ('".$data_back['nickname']."', '".sha1($data_back['pass'])."', '".$data_back['email']."', 1,'N',30,0,1)";
       $sql_query[1] = "SELECT * FROM ".$cunityConfig['db_prefix']."users WHERE nickname='".$data_back['nickname']."'";
      
      //sql-query[0]
    
      $result = mysql_query($sql_query[3]);
            if(!$result){
                 $statusmessage= $status_db3;
                 $status = 0;
                        }
             else if(mysql_num_rows($result) != 0){
                        $statusmessage=$status_nick;
                        $status = 0;
                        }else
                        {
                   
                           $result = mysql_query($sql_query[0]);
                           $user_id = mysql_insert_id(); 
                           //$result = mysql_query($sql_query[1]);
                           //$row = mysql_fetch_array($result);
                           //$user_id =$row['userid'];
                           
                           $sql_query[2] = "INSERT INTO ".$cunityConfig['db_prefix']."users_details (userid, firstname, lastname, registered) VALUES (".$user_id.",'".$data_back['firstname']."', '".$data_back['lastname']."', NOW())";
                           
                           $result = mysql_query($sql_query[2]);
                           
                           $statusmessage=$status_db4;
                           $status = 1;
                           
                        }
            $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
            mysql_close($db_connect);



} // CLOSING WEBSITE SETTINGS INSTALLSTEP 7

//--------------------------------------
//INSTALLSTEP=8: ASK FOR THE LOCAL FOLDER
//--------------------------------------
if ($data_back['installstep']=='8')
{
     $statusmessage = dirname ($_SERVER['SCRIPT_FILENAME']);
     $status = 1;
            
         
         $return_dir=readdirectory(dirname($_SERVER['SCRIPT_FILENAME'])); ////READ CURRENT DIRECTORY 
            $read_dir = $return_dir['read_dir'];
            
            $tempdir = $return_dir['temp_dir'];
            
            $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage, 'read_dir'=>$read_dir, 'temp_dir'=>$tempdir);
           
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
            
} // CLOSING WEBSITE SETTINGS INSTALLSTEP 8


//------------------------------------
//INSTALLSTEP=9: CREATE NEW FOLDER
//-------------------------------------
if ($data_back['installstep']=='9')
{
            $current_folder=$data_back['current_folder'];
            if ($current_folder[strlen($current_folder)-1] =='/' || $current_folder[strlen($current_folder)-1]=='\\')
            {
                $current_folder=substr($current_folder, 0, -1);
            }
            $newfolder=$current_folder.'/'.$data_back['newfolder'];
            if (!mkdir($newfolder, 0777)) {
            $status =0; 
            $statusmessage = '<br/>'.$status_folder1;
            }
            else{
                if(is_writable($newfolder))
                 {
                    $status=1;
                    $statusmessage = '<br/>'.$status_folder2.' '.$newfolder;
                }
                else {
                    $status=0;
                    $statusmessage = '<br/>'.$status_folder7.' '.$newfolder;
              }
              }
          
            $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
            
} // CLOSING WEBSITE SETTINGS INSTALLSTEP 8



//--------------------------------------------------
//INSTALLSTEP=10: TEST SMTP SETTINGS BY SENDING AN E-MAIL
//--------------------------------------------------
if ($data_back['installstep']=='10')
{
      $email_to = $data_back['email_to']; // This is what the user gave as a receiver e-mail in the prompt
      $message = $test_email1; //This is the message text of the test e-mail
      $subject = $test_email2; //Subject of the test e-mail
      
        $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
      //Deciding which mailing-method was selected by user
      if ($cunityConfig['smtp_method']=='SMTP'){$mail->IsSMTP();} // telling the class to use SMTP
      else if ($cunityConfig['smtp_method']=='Mail'){$mail->IsMail();} // telling the class to use PHP Mail
      else {$mail->IsSendmail();} // telling the class to use Sendmail
        
        try {
          $mail->Host       = $cunityConfig['smtp_host']; // SMTP server
          //$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
          if ($cunityConfig['smtp_auth']=='true') $mail->SMTPAuth = true;                  // enable SMTP authentication
          else  $mail->SMTPAuth = false;
          $mail->Port       = $cunityConfig['smtp_port'];                    // set the SMTP port for the GMAIL server
          $mail->Username   = $cunityConfig['smtp_username']; // SMTP account username
          $mail->Password   = $cunityConfig['smtp_password'];        // SMTP account password
          $mail->AddReplyTo($cunityConfig['smtp_sender_address'], $cunityConfig['smtp_sender_name']);
          $mail->AddAddress($email_to);
          $mail->SetFrom($cunityConfig['smtp_sender_address'], $cunityConfig['smtp_sender_name']);
          $mail->Subject = $subject;
          $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
          $mail->MsgHTML($message);
          $mail->Send();
          $status = 1;
          $statusmessage=$test_email3.' '.$email_to.'!';
        } 
            catch (phpmailerException $e) {
          $statusmessage1 = $e->errorMessage(); //Pretty error messages from PHPMailer
          $statusmessage=$test_email4.' '.$email_to.' '.$test_email5.' '.$statusmessage1;
          $status = 0;
          } catch (Exception $e) {
          $statusmessage2 = $e->getMessage(); //Boring error messages from anything else!
          $statusmessage=$test_email4.' '.$email_to.' '.$test_email5.' '.$statusmessage1;
          $status = 0;
          }
            $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
} // CLOSING SMTP CHECKING INSTALLSTEP 10

//-------------------------------------
//INSTALLSTEP=11: A FOLDER WAS SELECTED
//-------------------------------------
if ($data_back['installstep']=='11')
{
     $current_folder = $data_back['current_folder'];
     $selected_folder = $data_back['selected_folder'];
     if ($selected_folder=='/up/') { //if the user wants to go one directory up
                $old_folder = $current_folder;
                $current_folder=dirname($current_folder); }
         
     else {
     $current_folder = $selected_folder;
        } 
     $status = 1;
            //READ CURRENT DIRECTORY
            
            $current_folder = str_replace('\\', '/', $current_folder);
            
            $return_dir=readdirectory($current_folder); ////READ CURRENT DIRECTORY
            $read_dir = $return_dir['read_dir'];
            $tempdir = $return_dir['temp_dir'];    
            if ($read_dir=='null' || $read_dir==null) $read_dir="";
            
            $returndata = array('status'=>$status, 'current_folder'=>$current_folder, 'read_dir'=>$read_dir, 'temp_dir'=>$tempdir);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
            
} // CLOSING WEBSITE SETTINGS INSTALLSTEP 11


//-------------------------------------
//INSTALLSTEP=12: FOLDER CHECK
//-------------------------------------
if ($data_back['installstep']=='12')
{
     
     $cur_filefolder = $data_back['cur_filefolder'];
     if (file_exists($cur_filefolder)) {
     $status=1;
     $statusmessage=$status_folder3;
    } else {
    $status=0;
    $statusmessage=$status_folder4;
    }
     
            $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
     
} // CLOSING FOLDER CHECK INSTALLSTEP 12

//-------------------------------------------
//INSTALLSTEP=13: WRITING FOLDER TO DATABASE
//-------------------------------------------
if ($data_back['installstep']=='13')
{
     
     $cur_filefolder = $data_back['cur_filefolder'];
     if ($cur_filefolder[strlen($cur_filefolder)-1] =='/' || $cur_filefolder[strlen($cur_filefolder)-1]=='\\')
            {
                $cur_filefolder=substr($cur_filefolder, 0, -1);
            }
     
     if (file_exists($cur_filefolder)) {
                $status=1;
                db_connector();
                $sql_query[0] = "UPDATE ".$cunityConfig['db_prefix']."settings SET value = '".$cur_filefolder."' WHERE name='files_dir'";
                $result = mysql_query($sql_query[0]);
            if(!$result){
                 $statusmessage= $status_db3;
                 $status = 0;
                        }
              else
                        {
                        $status=1;
                        $statusmessage=$status_folder5;
                        }
                        }

                        else {
                            $status=0;
                            $statusmessage=$status_folder6;
                        }

            $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client

} // CLOSING FOLDER CHECK INSTALLSTEP 13

//-------------------------------------------
//INSTALLSTEP=14: SENDING SUMMARY AS E-MAIL
//-------------------------------------------
if ($data_back['installstep']=='14')
{
      $email_to = $data_back['email_to']; // This is what the user gave as a receiver e-mail in the prompt
      $message = utf8_decode($data_back['summary']);
      $message = str_replace('#nl#','<br/>',$message); //This is the message text of the summary e-mail
      $subject = $summary1; //Subject of the test e-mail
      
      $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
      //Deciding which mailing-method was selected by user
      if ($cunityConfig['smtp_method']=='SMTP'){$mail->IsSMTP();} // telling the class to use SMTP
      else if ($cunityConfig['smtp_method']=='Mail'){$mail->IsMail();} // telling the class to use PHP Mail
      else {$mail->IsSendmail();} // telling the class to use Sendmail

        try {
          $mail->Host       = $cunityConfig['smtp_host']; // SMTP server
          //$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
          if ($cunityConfig['smtp_auth']=='true') $mail->SMTPAuth = true;                  // enable SMTP authentication
          else  $mail->SMTPAuth = false;
          $mail->Port       = $cunityConfig['smtp_port'];                    // set the SMTP port for the GMAIL server
          $mail->Username   = $cunityConfig['smtp_username']; // SMTP account username
          $mail->Password   = $cunityConfig['smtp_password'];        // SMTP account password
          $mail->AddReplyTo($cunityConfig['smtp_sender_address'], $cunityConfig['smtp_sender_name']);
          $mail->AddAddress($email_to);
          $mail->SetFrom($cunityConfig['smtp_sender_address'], $cunityConfig['smtp_sender_name']);
          $mail->Subject = $subject;
          $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
          $mail->MsgHTML($message);
          $mail->Send();
          $status = 1;
          $statusmessage=$test_email3.' '.$email_to.'!';
        }
            catch (phpmailerException $e) {
          $statusmessage1 = $e->errorMessage(); //Pretty error messages from PHPMailer
          $statusmessage=$test_email4.' '.$email_to.' '.$test_email5.' "'.$statusmessage1.'"';
          $status = 0;
          } catch (Exception $e) {
          $statusmessage2 = $e->getMessage(); //Boring error messages from anything else!
          $statusmessage=$test_email4.' '.$email_to.' '.$test_email5.' "'.$statusmessage2.'"';
          $status = 0;
          }
      
           $returndata = array('status'=>$status, 'statusmessage'=>$statusmessage);
            $jsonData=json_encode2($returndata);
            echo $jsonData; // back to the client
          $cunityConfig['cunity_installed']=1;
          write_config(); // With this, the variable "cunity_installed" is set to one. After that step, Cunity is said to be fully installed

       
} // CLOSING SUMMARY SENDING INSTALLSTEP 14

//-------------------------------------------
//INSTALLSTEP=19: SETTING THE $cunityConfig['cunity_installed'] to 1 in the config.php
//-------------------------------------------
if ($data_back['installstep']=='19')
{
       $cunityConfig['cunity_installed']=1;
       write_config(); // With this, the variable "cunity_installed" is set to one. After that step, Cunity is said to be fully installed

} // CLOSING INSTALLSTEP 19


    
?>