<?php


$selectedMailOption='';
$selectedSmtpAuth='';

if(isset($_POST['save']))
{
	$cunityConfig["smtp_host"] = $_POST['host'];
	$cunityConfig["smtp_port"] = $_POST['port'];
	$cunityConfig["smtp_username"] = $_POST['username'];
	$cunityConfig["smtp_password"] = $_POST['password'];
	$cunityConfig["smtp_sender_name"] = $_POST['sender_name'];
	$cunityConfig["smtp_sender_address"] = $_POST['sender_address'];
	$cunityConfig["email_header"] = $_POST['mail_header'];
	$cunityConfig["email_footer"] = $_POST['mail_footer'];
	$mail_function=$_POST['mailOptions'];
	$cunityConfig["smtp_auth"]=$_POST['isAuthOption'];
	
	$file = fopen ("../config.php","w");
	fputs ($file, '<?php
		$cunityConfig["db_host"] = "'.$cunity->getConfig("db_host").'";
		$cunityConfig["db_user"] = "'.$cunity->getConfig("db_user").'";
		$cunityConfig["db_pass"] = "'.$cunity->getConfig("db_pass").'";
		$cunityConfig["db_name"] = "'.$cunity->getConfig("db_name").'";
		$cunityConfig["db_prefix"] = "'.$cunity->getConfig("db_prefix").'";
		$cunityConfig["smtp_method"] = "'.$mail_function.'";	 
		$cunityConfig["smtp_host"] = "'.$cunityConfig["smtp_host"].'";
		$cunityConfig["smtp_port"] = '.$cunityConfig["smtp_port"].';
		$cunityConfig["smtp_username"] = "'.$cunityConfig["smtp_username"].'";
		$cunityConfig["smtp_password"] = "'.$cunityConfig["smtp_password"].'"; 
		$cunityConfig["smtp_auth"] = '.$cunityConfig["smtp_auth"].';
		$cunityConfig["smtp_sender_address"] = "'.$cunityConfig["smtp_sender_address"].'";
		$cunityConfig["smtp_sender_name"] = "'.$cunityConfig["smtp_sender_name"].'";
		$cunityConfig["email_header"] = \''.$cunityConfig["email_header"].'\';
		$cunityConfig["email_footer"] = \''.$cunityConfig["email_footer"].'\';
		$cunityConfig["cunity_installed"] = 1;
		$cunityConfig["error_reporting"] = E_ALL & ~E_NOTICE;
		?>');
	fclose ($file);
}
$cunity->refreshConfigData();
if($cunity->getConfig("smtp_method")=='sendMail'){
	$selectedMailOption='<option value="php" >'.$langadmin['admin_settings_mail_php_function'].'</option><option value="sendMail" selected="selected">'.$langadmin['admin_settings_mail_send_function'].'</option><option value="smtp">'.$langadmin['admin_settings_smtp_function'].'</option>';
}elseif ($cunity->getConfig("smtp_method")=='smtp'){
	$selectedMailOption='<option value="php" >'.$langadmin['admin_settings_mail_php_function'].'</option><option value="sendMail" >'.$langadmin['admin_settings_mail_send_function'].'</option><option value="smtp" selected="selected">'.$langadmin['admin_settings_smtp_function'].'</option>';
}else{
	$selectedMailOption='<option value="php" selected="selected">'.$langadmin['admin_settings_mail_php_function'].'</option><option value="sendMail" >'.$langadmin['admin_settings_mail_send_function'].'</option><option value="smtp" >'.$langadmin['admin_settings_smtp_function'].'</option>';
}

if($cunity->getConfig("smtp_auth") !=null){
	$selectedSmtpAuth='<option  selected="selected" value="true">'.$langadmin['admin_settings_smtp_auth_on'].'</option><option value="false" >'.$langadmin['admin_settings_smtp_auth_off'].'</option>';
}else{
	$selectedSmtpAuth='<option  value="true">'.$langadmin['admin_settings_smtp_auth_on'].'</option><option value="false" selected="selected">'.$langadmin['admin_settings_smtp_auth_off'].'</option>';
}

$tplEngine->Template('settings_smtp');
$tplEngine->Assign('host', $cunity->getConfig("smtp_host"));
$tplEngine->Assign('port', $cunity->getConfig("smtp_port"));
$tplEngine->Assign('username', $cunity->getConfig("smtp_username"));
$tplEngine->Assign('password', $cunity->getConfig("smtp_password"));
$tplEngine->Assign('sender_address', $cunity->getConfig("smtp_sender_address"));
$tplEngine->Assign('sender_name', $cunity->getConfig("smtp_sender_name"));
$tplEngine->Assign('email_header', $cunity->getConfig("email_header"));
$tplEngine->Assign('email_footer', $cunity->getConfig("email_footer"));
$tplEngine->Assign('smtp_port', 'Port');

$tplEngine->Assign('mail_selected_options', $selectedMailOption);
$tplEngine->Assign('smtp_selected_auth', $selectedSmtpAuth);


?>