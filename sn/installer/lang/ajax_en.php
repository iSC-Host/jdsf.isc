<?php
//SCRIPT INSTALLAJAX.PHP

$warning_script = "You cannot run this script from here!";
$warning_installed = "Cunity is already installed. You cannot run this script!";
$warning_missing = "The directory structure of your Cunity is not intact!";
$warning_db = "Could not connect to the database server!";
$connection_ok = "Database-Connection OK!";
$connection_failed = "Could not connect to the database with the name";
$automatic_msg = "This is an automatic message from the Website:";
$greeting = "Best regards,";
$status_dir = "There was an error opening the directory";
$status_db1 = "Database imported successfully!";
$status_db2 = "Database was NOT imported!";
$status_db3 = "There was an error writing to the database!";
$status_db4 = "Database updated successfully!";
$status_config1 = "The config.php file is corrupted. Please fix the config.php!";
$status_config2 = "File config.php updated successfully!";
$status_nick = "This nickname or e-mail address already exist. Please do not create 2 owner accounts!";
$status_folder1 = "Could not create folder!";
$status_folder2 = "New folder created successfully:";
$test_email1 = "This is a test e-mail from the Cunity you are currently installing!";
$test_email2 = "Cunity Installer test e-mail";
$test_email3 = "An e-mail was successfully sent to";
$test_email4 = "Sending of e-mail to";
$test_email5 = "failed because of:";
$status_folder3 = "The folder can be accessed. When you click on the Next-Button the folder-path will be stored in the database!";
$status_folder4 = "The folder does not exist. Please create a folder first!";
$status_folder5 = "Folder was written to database!";
$status_folder6 = "The folder does not exist!";
$status_folder7 = "The new folder is not writable. Please change permission of:";
$summary1 = "Summary of Cunity Installation";
$status_error = "There was an error:";
$status_email1 = "An e-mail was successfully sent to";
$status_email2 = "Sending of e-mail to";
$status_email3 = "failed because of:";
$config_error1 = "The installer does not have the permission to write into the config.php although tried to change permission to 0644! Please grant access!";
$config_error2 = "The installer is not able to write into the config.php and cannot change the permission.";
$config_error3 = "The file config.php does not exist. It should be provided in the installation-root directory. Please check the installation directory!";

//SCRIPT SECURECUNITY.PHP

$secure_sec1 = 'Your Cunity is already secured (installajax.php deleted)!';
$secure_sec2 = 'Your Cunity was secured successfully (installajax.php and/or install-cunity.php deleted)!';
$secure_sec3 = 'Your Cunity could NOT be completely secured. Please delete installajax.php yourself!';
$secure_sec4 = 'Your Cunity could NOT be completely secured. Please delete install-cunity.php yourself!';
$secure_sec5 = 'Your Cunity could NOT be secured. Please delete install-cunity.php and installajax.php yourself!';
$secure_sec6 = 'Your Cunity directories on your Linux system could not be completely secured! Please do this manually!';
$secure_sec7 = 'The Cunity directories that may not be accessed from the outside were secured with 0644!';
?>