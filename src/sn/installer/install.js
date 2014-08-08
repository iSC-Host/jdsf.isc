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
 

 $(document).ready(function() {

     
$('#d0').show(); //WITHOUT JAVASCRIPT ACTIVATED THERE IS ONLY AN ERROR
$('.footer').show();
$('#end').hide();
$('#next').hide();

//IF YOU SET THIS VARIABLE (debug_mode) TO 1 THEN YOU CAN WALK THROUGH THE INSTALLER WITHOUT QUESTIONS
//THIS IS ONLY FOR DEVELOPERS TO TEST CERTAIN STEPS WITHOUT INSTALLING THE DATABASE, ETC!
var debug_mode=0;
var debug_n=2; // This is the number of the step the software jumps to, when it's set to debug_mode.


//VARIABLES

var version_number="1.041"; //THIS IS IMPORTANT! IT HAS TO BE SET TO THE CURRENT CUNITY VERSION FOR THE CHECKING OF UPDATES

var secure_cunity=0;

//VARIABLES FOR THE CONFIG.PHP BEGIN
var d_db_name = '';
var d_db_user = '';
var d_db_pass = '';
var d_db_host = '';
var d_db_prefix = '';
//VARIABLES FOR THE CONFIG.PHP STOP

var installstep = '0'; // This is the variable given to installajax.php
var ignoredwarning =0;
var ignoredwarning2=0;
var admin_warning=0;  // This is a warning shown during the registration of the administrator
var nextdiv = '#d0';
var prevdiv = '#d0';

//WHICH LANGUAGE WAS CHOSEN?
var lang='{$lang}';

var come_from_selfextractor={$if_installed}; //IF THE SELF-EXTRACTOR WAS USED OR NOT. IF USED; SKIP THE FIRST 2 STEPS


if (debug_mode==1) {
        n=debug_n; //In the debug mode, you can set the variable (at the top) to the step where you want to start from
        $('#d0').hide();
		if (debug_n==0) debug_n=1;
        var divshow = '#d'+debug_n;
		$(divshow).show();  
		nextdiv = '#d'+(debug_n);
        prevdiv = '#d'+(debug_n-1);
        $('#next').show();
        
        }
    else if (come_from_selfextractor==1) {
        n=2;
        $('#d0').hide();
        $('#d2').show();
        nextdiv = '#d2';
        prevdiv = '#d1';
        $('#next').show();
        }
    else if (come_from_selfextractor==2) {
        n=1;
        $('#d0').hide();
        $('#d1').show();
        $('#next').show();
        $('#back').show();
        nextdiv = '#d1';
        prevdiv = '#d0';
        }
        
    else n=0; // This is the variable for the current step
            // n=0 if the installer should start from the beginning. If 
            //the self-extracting installer has been run, then the language has been selected
            //as well as the admin has agreed to terms & conditions.


var dbinstallallow = 0; // Has the user already tested the database?
var dbinstalled = 0;  // Was the database installed correctly?
var url_address = document.URL;  // This is the URL of the current Cunity
var file_path ='';  // This is the file path of the installation of Cunity (without '/installer') and will
//be retrieved by the installajax.php

var cur_filefolder=''; //File-path to the filesharing directory
var selected_folder=''; // Which is the currently selected folder for the filesharing folder?
var current_folder=''; //This is the current folder for the filesharing folder
var foldercheck=0; // If this is 0, you cannot proceed with the file-folder path 
var showpass=0;
var showpassbutton = 0; // This decides whether the Show-button in the summary shows the passwords or not
var summary1=''; // This will contain the summary of all settings in the end
var summary2=''; // This will contain the summary for the e-mail
var adminwritten=0; // If the admin / owner account was successfully stored then it will be set to 1

//Cunity Settings that will be displayed at the end
var cunity_settings='';
var d_db_prefix2 = '';
var cs_c_name = '';
var cs_c_slogan = '';
var cs_c_url = '';
var cs_c_contact = '';
var cs_c_purpose = '';
var cs_c_country = '';

var cs_filefolder = '';

var cs_smtp_host = '';
var cs_smtp_port = 0;
var cs_smtp_user = '';
var cs_smtp_pass = '';
var cs_smtp_sender_name = '';
var cs_smtp_sender_email = '';

var cs_admin_nick ='';
var cs_admin_first ='';
var cs_admin_last ='';
var cs_admin_email ='';
var cs_admin_pass ='';

//Function to check, whether something is an integer
function is_int(value){
  if((parseFloat(value) == parseInt(value)) && !isNaN(value)){
      return true;
  } else {
      return false;
  }
}

//FUnction to clear the required fields-warning
function clearreq(){
$('.req').removeClass('warning');
}

   //ERRORMESSAGE WILL BE SCROLLABLE
var $scrollingDiv = $("#error");

		$(window).scroll(function(){
			$scrollingDiv
				.stop()
				.animate({"marginTop": ($(window).scrollTop() - 30) + "px"}, "slow" );
		});


//IF SOMEBODY LEAVES A REQUIRED FIELD
$('.req').blur(function() {
     if(!this.value)  // zero-length string
           $(this).addClass('warning');
           else
           $(this).removeClass('warning');
                    })


//IF SOMEBODY ENTERS AGAIN A REQUIRED FIELD
$('.req').keyup(function() {
        $('#btn_dbinstall').hide();
        dbinstallallow = 0;
        $('#database_test').html('');
        $('#database_test').removeClass('warning2');
        $('#database_test').removeClass('ok');
        $('#btn_smtp_check').hide();

});

//IF SOMEBODY CHANGES SOMETHING IN THE FILESHARING FOLDER PATH
$('[name=filefolder]').keyup(function() {
        $('#next').hide();
        foldercheck = 0;
    });


//DATABASE-SETTINGS _ PASSWORD MATCH
$('[name=db_pass2]').keyup(function() {
        if ($('[name=db_pass1]').val()!=$('[name=db_pass2]').val()){
            $('.span_pw').removeClass('ok3');
            $('.span_pw').removeClass('warning3');
            $('.span_pw').addClass('warning3');
            $('.span_pw').html('{$pw_no_match}');
        } else  {
            $('.span_pw').removeClass('ok3');
            $('.span_pw').removeClass('warning3');
            $('.span_pw').addClass('ok3');
            $('.span_pw').html('{$pw_match}');
        }

});

//SMTP-SETTINGS _ PASSWORD MATCH
$('[name=smtp_pass2]').keyup(function() {

    if ($('[name=smtp_pass1]').val()!=null && $('[name=smtp_pass2]').val()!=null){
        if ($('[name=smtp_pass1]').val()!=$('[name=smtp_pass2]').val()){
            $('.span_pw').removeClass('ok3');
            $('.span_pw').removeClass('warning3');
            
            $('.span_pw').addClass('warning3');
            $('.span_pw').html('{$pw_no_match}');
        } else  {
            $('.span_pw').removeClass('ok3');
            $('.span_pw').removeClass('warning3');
            
            $('.span_pw').addClass('ok3');
            $('.span_pw').html('{$pw_match}');
        } 
        }
        else {
            $('.span_pw').html('');
        }     
});

//If somebody changes something on the SMTP settings
$('.smtp').keyup(function() {
        $('#btn_smtp_check').hide();
        });
$('.smtp2').change(function() {
        $('#btn_smtp_check').hide();
        });


//ADMIN-SETTINGS _ PASSWORD LENGTH

$('[name=admin_pass1]').keyup(function() {
        if (($('[name=admin_pass1]').val()).length<6) {
            $('.span_pw1').removeClass('ok3');
            $('.span_pw1').removeClass('warning3');
            $('.span_pw1').addClass('warning3');
            $('.span_pw1').html('{$pw_short}');
        }
        else  {
            $('.span_pw1').html('');
            }
});

//ADMIN-SETTINGS _ PASSWORD MATCH
$('[name=admin_pass2]').keyup(function() {
        if ($('[name=admin_pass1]').val()!=$('[name=admin_pass2]').val()){
            $('.span_pw').removeClass('ok3');
            $('.span_pw').removeClass('warning3');
            $('.span_pw').addClass('warning3');
            $('.span_pw').html('{$pw_no_match}');
        } else  {
            $('.span_pw').removeClass('ok3');
            $('.span_pw').removeClass('warning3');

            $('.span_pw').addClass('ok3');
            $('.span_pw').html('{$pw_match}'); }
           
});

//ADMIN-SETTINGS _ EMAIL MATCH
$('[name=admin_email2]').keyup(function() {
        if ($('[name=admin_email1]').val()!=$('[name=admin_email2]').val()){
            $('.span_em').removeClass('ok3');
            $('.span_em').removeClass('warning3');

            $('.span_em').addClass('warning3');
            $('.span_em').html('{$emails_no_match}');
        } else  {
            $('.span_em').removeClass('ok3');
            $('.span_em').removeClass('warning3');

            $('.span_em').addClass('ok3');
            $('.span_em').html('{$emails_match}');
        }
});

//EMAIL-VALIDATION
   function validatemail(email) {
   var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
   var address = email;
   //var address = document.forms[form_id].elements[email].value;
   if(reg.test(address) == false) {
      return false;}
    else {
        return true;}
    }; //Close function validate


//URL-VALIDATION
    function validateurl(s) {
	var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
	return regexp.test(s);
    }


//FOLDER-NAME-VALIDATION Create new folder: 
   function validatefolder (folder) {
   var reg = /^[a-zA-Z0-9\_\-]{1,20}$/;
   var address = folder;
   //var address = document.forms[form_id].elements[email].value;
   if(reg.test(address) == false) {
      return false;}
    else {
        return true;}
    }; //Close function validate


//SUMMARY OF CUNITY SETTINGS
function process_settings(showpass){
       
        cunity_settings='';
        var cs1 = new Array();
        var cs2 = new Array();
        var cs3 = new Array();
        var cs4 = new Array();
        var cs5 = new Array(); 
 
          cs1["Name of your Cunity"] =cs_c_name;
          cs1["Slogan"] = cs_c_slogan;
          cs1["URL"] = cs_c_url;
          cs1["Contact e-mail"] = cs_c_contact; 
          cs1["Purpose"] = cs_c_purpose;
          
          cs2["Nickname"] =cs_admin_nick;
          cs2["First name"] = cs_admin_first;
          cs2["Last name"] = cs_admin_last;
          cs2["Your e-mail"] = cs_admin_email;
          cs2["Your password"] = '{$admin_noshow}';
          
          cs3["DB Name"] =d_db_name;
          cs3["DB Host"] = d_db_host;
          cs3["DB User"] = d_db_user;
          cs3["DB Password"] = '{$db_noshow}';
          cs3["DB Prefix"] = d_db_prefix2;

          cs4["Folder path"] = cs_filefolder;
          
          cs5["SMTP Host"] =cs_smtp_host;
          cs5["SMTP Port"] = cs_smtp_port;
          cs5["SMTP Username"] = cs_smtp_user;
          cs5["SMTP Password"] = '{$smtp_noshow}';
          cs5["Sender Name"] = cs_smtp_sender_name;
          cs5["Sender E-Mail"] = cs_smtp_sender_email;

       if (showpass==0 || showpass==1) {         
        cunity_settings = '<table> <caption>{$general_settings}</caption>';
        for (identifier in cs1) {
        cunity_settings = cunity_settings+'<tr><td style="width:200px;">'+identifier+': </td>'+ '<td>'+cs1[identifier]+'</td></tr>';
        } 
        cunity_settings = cunity_settings+'</table><table> <caption>{$owners_account}</caption>';
        for (identifier in cs2) {
        cunity_settings = cunity_settings+'<tr><td style="width:200px;">'+identifier+': </td>'+ '<td>'+cs2[identifier]+'</td></tr>';
        }
        cunity_settings = cunity_settings+'</table><table> <caption>{$db_settings}</caption>';
        for (identifier in cs3) {
        cunity_settings = cunity_settings+'<tr><td style="width:200px;">'+identifier+': </td>'+ '<td>'+cs3[identifier]+'</td></tr>';
        }
        cunity_settings = cunity_settings+'</table><table> <caption>{$fs_settings}</caption>';
        for (identifier in cs4) {
        cunity_settings = cunity_settings+'<tr><td style="width:200px;">'+identifier+': </td>'+ '<td>'+cs4[identifier]+'</td></tr>';
        }
        cunity_settings = cunity_settings+'</table><table> <caption>{$smtp_settings}</caption>';
        for (identifier in cs5) {
        cunity_settings = cunity_settings+'<tr><td style="width:200px;">'+identifier+': </td>'+ '<td>'+cs5[identifier]+'</td></tr>';
        }           
        cunity_settings = cunity_settings+'</table>';
       
        
        if (showpass==1){
        cunity_settings = cunity_settings.replace("{$admin_noshow}", cs_admin_pass);
        cunity_settings = cunity_settings.replace("{$db_noshow}", d_db_pass);
        cunity_settings = cunity_settings.replace("{$smtp_noshow}", cs_smtp_pass);   
        }
        }
        else //If showpass ==3 --> this means that we need the summary not in HTML but for the e-mail in plain text
            {
                cunity_settings = '{$email_settings}#nl##nl#{$general_settings}#nl#';
        for (identifier in cs1) {
        cunity_settings = cunity_settings+identifier+': '+cs1[identifier]+'#nl#';
        }
        cunity_settings = cunity_settings+'#nl#{$owners_account}#nl#';
        for (identifier in cs2) {
        cunity_settings = cunity_settings+identifier+': '+cs2[identifier]+'#nl#';
        }
        cunity_settings = cunity_settings+'#nl#{$db_settings}#nl#';
        for (identifier in cs3) {
        cunity_settings = cunity_settings+identifier+': '+cs3[identifier]+'#nl#';
        }
          cunity_settings = cunity_settings+'#nl#{$fs_settings}#nl#';
        for (identifier in cs4) {
        cunity_settings = cunity_settings+identifier+': '+cs4[identifier]+'#nl#';
        }
          cunity_settings = cunity_settings+'#nl#{$smtp_settings}#nl#';
        for (identifier in cs5) {
        cunity_settings = cunity_settings+identifier+': '+cs5[identifier]+'#nl#';
        }
         if (showpassbutton==1){
        cunity_settings = cunity_settings.replace("{$admin_noshow}", cs_admin_pass);
        cunity_settings = cunity_settings.replace("{$db_noshow}", d_db_pass);
        cunity_settings = cunity_settings.replace("{$smtp_noshow}", cs_smtp_pass);
        }
            }
        
        return cunity_settings;
        
}
////////   END OF SUMMARIZING DATA



   //CLOSE BUTTON OF ERROR WINDOW
$('#btn_close').click(function(){
$('#error').hide();
})

   //TEST DATABASE BUTTON
$('#btn_dbtest').click(function(){
          $('#database_test').html('');
          $('#database_test').removeClass('ok');
          $('#database_test').removeClass('warning2');
    if (!$('[name=db_name]').val()||!$('[name=db_user]').val()||!$('[name=db_host]').val()||!$('[name=db_prefix]').val()) {
            $('#errormessage').html('{$fill_out}');
            $('.req').addClass('warning');
            $('#error').show();
            $('#btn_dbinstall').hide();
            dbinstallallow = 0;
            }
    else if ($('[name=db_pass1]').val()!=$('[name=db_pass2]').val()) {
            $('#errormessage').html('{$pw_no_match2}');
            $('#error').show();
            $('#btn_dbinstall').hide();
            dbinstallallow = 0;
            }
      else if (!ignoredwarning ==1&&(!$('[name=db_pass1]').val()||$('[name=db_user]').val()=='root')) {
            $('#errormessage').html('{$warning_root}');
            $('#error').show();
            ignoredwarning=1;      

            }
    else {
           clearreq();
           d_db_user = $('[name=db_user]').val();
           d_db_host = $('[name=db_host]').val();
           d_db_pass = $('[name=db_pass1]').val();
           d_db_name = $('[name=db_name]').val();
           installstep = '3';
           //var dataValues = [d_db_user, d_db_host, d_db_pass, d_db_name];


           var dataValues = '{"installstep":"'+installstep+'","user":"'+d_db_user+'","host":"'+d_db_host+'","pass":"'+d_db_pass+'","name":"'+d_db_name+'"}';

        $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                async: false,
                beforeSend: function(){
                    $('#database_test').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {

                             if (msg.status==0) //If database connection fails
                            {
                                $('#database_test').html(msg.statusmessage);
                                
                                $('#database_test').removeClass('ok');
                                $('#database_test').addClass('warning2',1000);
                                $('#btn_dbinstall').hide();
                                dbinstallallow = 0;
                            }
                        else {    //If database connection is OK
                                $('#database_test').html(msg.statusmessage);
                                $('#database_test').removeClass('warning2');
                                $('#database_test').addClass('ok',1000);
                                $('#btn_dbinstall').show();
                                $('#next').show();  // MUST BE LATER REMOVED
                                dbinstallallow = 1;
                                d_db_prefix2 = $('[name=db_prefix]').val()+'_';

                             }
                                        },
                error: function (){
                                $('#database_test').html('{$ajax_failed}');
                                $('#database_test').removeClass('ok');
                                $('#database_test').addClass('warning2',1000);
                                $('#btn_dbinstall').hide();
                                dbinstallallow = 0;
                }

 });




    }

   }) //TEST-DB-Button Function CLOSING BRACKETS

 //!!!!!!!!!!!!!!!!!!!!!!!!!
//INSTALL DATABASE BUTTON
//!!!!!!!!!!!!!!!!!!!!!!!!!!!
$('#btn_dbinstall').click(function(){
          $('#database_install').html('');
          $('#database_install').removeClass('ok');
          $('#database_install').removeClass('warning2');
    if (!$('[name=db_name]').val()||!$('[name=db_user]').val()||!$('[name=db_host]').val()||!$('[name=db_prefix]').val()) {
            $('#errormessage').html('{$fill_out}');
            $('.req').addClass('warning');
            $('#error').show();
            }
    else if ($('[name=db_pass1]').val()!=$('[name=db_pass2]').val()) {
            $('#errormessage').html('{$pw_no_match2}');
            $('#error').show();
            }
    else if (dbinstalled==1){
            $('#errormessage').html('{$warning_db_installed}');
            $('.req').addClass('warning');
            $('#error').show();
    }

    else {
           clearreq();
           d_db_user = $('[name=db_user]').val();
           d_db_host = $('[name=db_host]').val();
           d_db_pass = $('[name=db_pass1]').val();
           d_db_name = $('[name=db_name]').val();
           d_db_prefix = $('[name=db_prefix]').val();
           installstep = '4';
           //var dataValues = [d_db_user, d_db_host, d_db_pass, d_db_name];

           d_db_prefix=d_db_prefix+'_';
          
           var dataValues = '{"installstep":"'+installstep+'","user":"'+d_db_user+'","host":"'+d_db_host+'","pass":"'+d_db_pass+'","name":"'+d_db_name+'","prefix":"'+d_db_prefix+'"}';

        $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(){
                    $('#database_install').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {

                             if (msg.status==0) //If database import fails
                            {
                                $('#database_install').html(msg.statusmessage);
                                
                                $('#database_install').removeClass('ok');
                                $('#database_install').addClass('warning2',1000);
                                dbinstalled = 0;
                               
                            }
                        else {    //If database import is OK
                                $('#database_install').html(msg.statusmessage);
                                $('#database_install').removeClass('warning2');
                                $('#database_install').addClass('ok',1000);
                                dbinstalled = 1;
                                $('#next').show();
                                
                               

                             }
                                        },
                error: function (){
                                $('#database_install').html('{$ajax_failed}');
                                $('#database_install').removeClass('ok');
                                $('#database_install').addClass('warning2',1000);
                                dbinstalled = 0;
                                
                }

 });

    }

   }) //INSTALL-DB-Button Function

//-------------------------
// SAVE WEB SITE SETTINGS BUTTON
//-------------------------

$('#btn_settings').click(function(){
if (!$('[name=web_name]').val()||!$('[name=web_slogan]').val()||!validateurl($('[name=web_url]').val())||!validatemail($('[name=web_email]').val())||$('[name=web_country]').val()=='all') {
            $('#errormessage').html('{$warning_url}');
            //$('.req').addClass('warning');
            $('#error').show();
            }
            else {
             clearreq();
             installstep = '5';
                 var dataValues = '{"installstep":"'+installstep+'","name":"'+$('[name=web_name]').val()+'","slogan":"'+$('[name=web_slogan]').val()+'","url":"'+$('[name=web_url]').val()+'","email":"'+$('[name=web_email]').val()+'","lang":"'+lang+'"}';
                 $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(){
                    $('#d4_request').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {

                             if (msg.status==0) //If database import fails
                            {
                                $('#d4_request').html(msg.statusmessage);
                                 $('#d4_request').removeClass('ok');
                                $('#d4_request').addClass('warning2',1000);
                                dbinstalled = 0;

                            }
                        else {    //If writing of settings is OK
                                $('#d4_request').html(msg.statusmessage);
                                $('#d4_request').removeClass('warning2');
                                $('#d4_request').addClass('ok',1000);
                                dbinstalled = 1;
                                $('#next').show();
                                cs_c_name = $('[name=web_name]').val();
                                cs_c_slogan = $('[name=web_slogan]').val();
                                cs_c_url = $('[name=web_url]').val();
                                cs_c_contact = $('[name=web_email]').val();
                                cs_c_purpose = $('[name=web_purpose]').val();
                                cs_c_country = $('[name=web_country]').val();
                                version_number = msg.cunity_version;


                             }
                                        },
                error: function (){
                                $('#d4_request').html('{$ajax_failed}');
                                $('#d4_request').removeClass('ok');
                                $('#d4_request').addClass('warning2',1000);
                                dbinstalled = 0;

                }

 }); //AJAX

 } //ELSE-ANWEISUNG

}) //BTN_SETTINGS FUNCTION

//------------------------------
// FILEFOLDER CHECKING BUTTON
//------------------------------

$('#btn_foldercheck').click(function(){

cur_filefolder=$('[name=filefolder]').val();

if ($('[name=filefolder]').val()=='') {
            $('#errormessage').html('{$warning_folder1}');
            $('#error').show();
            }
    else if (cur_filefolder.indexOf(file_path)>-1 && ignoredwarning2==0){
            $('#errormessage').html('{$warning_folder2} ('+cur_filefolder+'){$warning_folder3} ('+file_path+')! {$warning_folder4}');
            $('#error').show();
            ignoredwarning2=0;
            
            }
            else {
                clearreq();
               installstep = '12';
                 var dataValues = '{"installstep":"'+installstep+'","file_path":"'+file_path+'","cur_filefolder":"'+cur_filefolder+'"}';
                 $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(){
                    $('#d5_request').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {

                             if (msg.status==0) //If filefolder creation
                            {
                                $('#d5_request').html(msg.statusmessage);
                                 $('#d5_request').removeClass('ok');
                                $('#d5_request').addClass('warning2',1000);
                                dbinstalled = 0;

                            }
                        else {    //If Folder exists
                                $('#d5_request').html(msg.statusmessage);
                                $('#d5_request').removeClass('warning2');
                                $('#d5_request').addClass('ok',1000);
                                dbinstalled = 1;
                                $('#next').show();
                                foldercheck=1;


                             }
                                        },
                error: function (){
                                $('#d5_request').html('{$ajax_failed}');
                                $('#d5_request').removeClass('ok');
                                $('#d5_request').addClass('warning2',1000);
                                dbinstalled = 0;

                }

 }); //AJAX

 } //ELSE-ANWEISUNG

}) //BTN_FOLDERCHECK FUNCTION

//---------------------
//FUNCTION: GO BACK TO INSTALL_FOLDER
//---------------------

function backtoinstall(){
installstep = '8';
                clearreq();
                 $('#span_folder').animate({scrollTop:0});
                 var dataValues = '{"installstep":"'+installstep+'"}';
                 $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(){
                    $('#local_folder').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {


                             if (msg.status==0) //If file-folder retrieval fails
                            {
                                $('#local_folder').html(msg.statusmessage);
                                 $('#local_folder').removeClass('ok');
                                $('#local_folder').addClass('warning2',1000);


                            }
                        else {    //If file-folder reading is OK

                                 file_path = msg.statusmessage;
                                 current_folder = file_path;
                                 file_path=file_path.substring(0,file_path.lastIndexOf('/installer'));
                                 file_path_short = file_path.substring(0,file_path.lastIndexOf('/'));
                                 read_dir = msg.read_dir;
                                 temp_dir = msg.temp_dir;

                                 $('#span_folder').html(temp_dir+read_dir);
                                 $('#current_folder').html(current_folder);



                                $('#local_folder').html(file_path);
                                $('#local_folder').removeClass('warning2');
                                $('#local_folder').removeClass('ok',1000);
                                $('[name=filefolder]').val(file_path_short+'/files');

                            }
                                        },
                error: function (){
                                $('#local_folder').html('{$ajax_failed}');
                                $('#local_folder').removeClass('ok');
                                $('#local_folder').addClass('warning2',1000);


                }

            }); //AJAX

} // CLOSE FUNCTION: GO BACK TO INSTALL-FOLDER


//-------------------------------------------------------------------
/// BUTTON CREATE FOLDERS
//-----------------------------------------------------------------------

$('#btn_foldercreate').click(function(){
newfolder=prompt("{$enter_folder1}", "");
var checksyntax = validatefolder (newfolder);

if (newfolder=='' || !checksyntax) {
            $('#errormessage').html('{$enter_folder2}');
            $('.req').addClass('warning');
            $('#error').show();
            }
else if (newfolder!=null) {

               installstep = '9';
                 var dataValues = '{"installstep":"'+installstep+'","current_folder":"'+current_folder+'","newfolder":"'+newfolder+'"}';
                 $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                async: false,
                 beforeSend: function(){
                    $('#foldercreate_feedback').html('<img src="./img/ajax-loader.gif"/>');
                                    },
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                

                success: function (msg) {

                             if (msg.status==0) //If creation of folder fails
                            {
                                $('#foldercreate_feedback').html(msg.statusmessage+'<br/>');
                                 $('#foldercreate_feedback').removeClass('ok4');
                                $('#foldercreate_feedback').addClass('warning4',1000);


                            }
                        else {    //If filepath creation is OK
                                
                                $('#foldercreate_feedback').html(msg.statusmessage+'<br/>');
                                $('#foldercreate_feedback').removeClass('warning4');
                                $('#foldercreate_feedback').addClass('ok4',1000);
                                $('#next').show();
                                folderload('*/refresh/*');
                                


                             }
                                        },
                error: function (){
                                $('#foldercreate_feedback').html('{$ajax_failed}');
                                $('#foldercreate_feedback').removeClass('ok3');
                                $('#foldercreate_feedback').addClass('warning3',1000);


                }

 }); //AJAX

 } //ELSEIF-ANWEISUNG

}) //CREATE A FILEFOLDER
//////////////////////////////////////////////////////////////////////



 //-------------------------------------
 //BTN_FILEFOLDER _ "GO BACK TO INSTALL FOLDER"
 //-------------------------------------
$('#btn_filefolder').click(function(){
      backtoinstall();
       });  ////BTN_FILEFOLDER _ "GO BACK TO CURRENT"

//----------------------------------------------------------
//WHEN FOLDER IS CLICKED THIS WILL BE THE SELECTED FOLDER (EITHER GO UP ONE DIR OR GO INTO LOWER DIRECTORY)
//------------------------------------------------------------
$(".filefolder").live("click", function(){

folderload($(this).html());
}); //END FILEFOLDER CLICK

//--------HERE IS THE FUNCTION TO SELECT A FOLDER

function folderload (sel_fol){
        if (sel_fol!='*/refresh/*')
        {
           
             $('#foldercreate_feedback').html('');
             $('#foldercreate_feedback').removeClass('warning4');
             $('#foldercreate_feedback').removeClass('ok4');
        }
    installstep = '11';
    $('#span_folder').animate({scrollTop:0});
    if (sel_fol=='*/refresh/*')
        {
            selected_folder=current_folder;
        }
    else if (sel_fol=='GO UP ONE DIR..') {
    selected_folder='/up/';
     }
    else {

    if ((current_folder.charAt(current_folder.length-1))=='/')
    selected_folder= current_folder+sel_fol;
    else selected_folder= current_folder+'/'+sel_fol;
    $('[name=filefolder]').val(selected_folder);

    }

    var dataValues = '{"installstep":"'+installstep+'","selected_folder":"'+selected_folder+'","current_folder":"'+current_folder+'"}';
    
    $('#current_folder').html('&nbsp;&nbsp;&nbsp;<img src="./img/ajax-loader2.gif"/>');
    
 	
    $.post("installajax.php", {json_data : dataValues},
     	function(msg){
             read_dir = msg.read_dir;
             temp_dir = msg.temp_dir;
             current_folder = msg.current_folder;
             $('#span_folder').html(temp_dir+read_dir);
             $('#current_folder').html(current_folder);
            
             $('#d5_request').html(msg.statusmessage);
             $('#d5_request').removeClass('ok');
             $('#d5_request').removeClass('warning2',1000);
             
             
 }, "json");
}



//-------------------------
// SMTP_SETTINGS - WRITE TO CONFIG.PHP BUTTON
//-------------------------

$('#btn_smtp_write').click(function(){
if (!$('[name=smtp_host]').val()||!$('[name=smtp_sender]').val()||!validatemail($('[name=smtp_email]').val()) || !$('[name=smtp_port]').val()|| !is_int($('[name=smtp_port]').val())) {
            $('#errormessage').html('{$warning_email}');
            $('.req').addClass('warning');
            $('#error').show();
            }
            else if ($('[name=smtp_pass1]').val()!=$('[name=smtp_pass2]').val()) {
            $('#errormessage').html('{$pw_no_match2}');
            //$('.req').addClass('warning');
            $('#error').show();
            }
            else {
                clearreq();
               installstep = '6';
                 var dataValues = '{"installstep":"'+installstep+'","host":"'+$('[name=smtp_host]').val()+'","user":"'+$('[name=smtp_user]').val()+'","pass":"'+$('[name=smtp_pass1]').val()+'","sender":"'+$('[name=smtp_sender]').val()+'","port":"'+$('[name=smtp_port]').val()+'","email":"'+$('[name=smtp_email]').val()+'","web_name":"'+$('[name=web_name]').val()+'","smtp_method":"'+$('[name=smtp_method]').val()+'","smtp_auth":"'+$('[name=smtp_auth]').val()+'"}';
                
                 $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(){
                    $('#d6_request').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {

                             if (msg.status==0) //If writing of config.php fails
                            {
                                $('#d6_request').html(msg.statusmessage);
                                 $('#d6_request').removeClass('ok');
                                $('#d6_request').addClass('warning2',1000);
                                dbinstalled = 0;

                            }
                        else {    //If writing of SMTP to config.php is OK
                                $('#d6_request').html(msg.statusmessage);
                                $('#d6_request').removeClass('warning2');
                                $('#d6_request').addClass('ok',1000);
                                dbinstalled = 1;
                                $('#next').show();
                                cs_smtp_host = $('[name=smtp_host]').val();
                                cs_smtp_port = $('[name=smtp_port]').val();
                                cs_smtp_user = $('[name=smtp_user]').val();
                                cs_smtp_pass = $('[name=smtp_pass1]').val();
                                cs_smtp_sender_name = $('[name=smtp_sender]').val();
                                cs_smtp_sender_email = $('[name=smtp_email]').val();
                                $('#btn_smtp_check').show();
                                $('#d6_request2').html('');

                             }
                                        },
                error: function (){
                                $('#d6_request').html('{$ajax_failed}');
                                $('#d6_request').removeClass('ok');
                                $('#d6_request').addClass('warning2',1000);
                                dbinstalled = 0;

                }

 }); //AJAX

 } //ELSE-ANWEISUNG

}) //SMTP_SETTINGS FUNCTION


//------------------------------------------
// SMTP_CHECK SETTINGS BY SENDING AN E-MAIL
//------------------------------------------

$('#btn_smtp_check').click(function(){

var email_to=prompt("{$prompt_email1}", $('[name=web_email]').val());
var checksyntax = validatemail (email_to);

if (email_to=='' || !checksyntax) {
            $('#errormessage').html('{$warning_email2}');
            $('.req').addClass('warning');
            $('#error').show();
            }
else if (email_to!=null) {
               installstep = '10';
                 var dataValues = '{"installstep":"'+installstep+'","email_to":"'+email_to+'"}';
                 clearreq();
                 $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(){
                    $('#d6_request2').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {

                             if (msg.status==0) //If database import fails
                            {
                                $('#d6_request2').html(msg.statusmessage);
                                 $('#d6_request2').removeClass('ok');
                                $('#d6_request2').addClass('warning2',1000);
                                dbinstalled = 0;

                            }
                        else {    //If database import is OK
                                $('#d6_request2').html(msg.statusmessage);
                                $('#d6_request2').removeClass('warning2');
                                $('#d6_request2').addClass('ok',1000);
                                dbinstalled = 1;
                                $('#next').show();


                             }
                                        },
                error: function (){
                                $('#d6_request2').html('{$ajax_failed}');
                                $('#d6_request2').removeClass('ok');
                                $('#d6_request2').addClass('warning2',1000);
                                dbinstalled = 0;

                }

 }); //AJAX

 } //ELSE-ANWEISUNG

}) //SMTP_SETTINGS FUNCTION





//-------------------------
// ADMIN_SETTINGS
//-------------------------

$('#btn_admin').click(function(){
if (!$('[name=nickname]').val()||!$('[name=firstname]').val()||!$('[name=lastname]').val()||!$('[name=admin_email1]').val()||!$('[name=admin_pass1]').val()) {
            $('#errormessage').html('{$fill_out}');
            $('.req').addClass('warning');
            $('#error').show();
            }
            else if ($('[name=admin_pass1]').val()!=$('[name=admin_pass2]').val()) {
            $('#errormessage').html('{$pw_no_match2}');
            $('.req').addClass('warning');
            $('#error').show();
            }
            else if ($('[name=admin_email1]').val()!=$('[name=admin_email2]').val()) {
            $('#errormessage').html('{$email_match}');
            $('.req').addClass('warning');
            $('#error').show();
            }
            else if (!validatemail($('[name=admin_email1]').val())) {
            $('#errormessage').html('{$warning_email3}');
            $('.req').addClass('warning');
            $('#error').show();
            } 
            else if (($('[name=admin_pass1]').val()).length<6) {
            $('#errormessage').html('{$warning_pw1}');
            $('.req').addClass('warning');
            $('#error').show();
            }
             else {
             clearreq();
               installstep = '7';
                 var dataValues = '{"installstep":"'+installstep+'","nickname":"'+$('[name=nickname]').val()+'","firstname":"'+$('[name=firstname]').val()+'","lastname":"'+$('[name=lastname]').val()+'","email":"'+$('[name=admin_email1]').val()+'","pass":"'+$('[name=admin_pass1]').val()+'"}';
                 $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(){
                    $('#d7_request').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {

                             if (msg.status==0) //If database writing fails
                            {
                                $('#d7_request').html(msg.statusmessage);
                                 $('#d7_request').removeClass('ok');
                                $('#d7_request').addClass('warning2',1000);
                                

                            }
                        else {    //If writing of owner data to the database is OK
                                $('#d7_request').html(msg.statusmessage);
                                $('#d7_request').removeClass('warning2');
                                $('#d7_request').addClass('ok',1000);
                                $('#next').show();
                                cs_admin_nick =$('[name=nickname]').val();
                                cs_admin_first =$('[name=firstname]').val();
                                cs_admin_last =$('[name=lastname]').val();
                                cs_admin_email =$('[name=admin_email1]').val();
                                cs_admin_pass =$('[name=admin_pass1]').val();
                                $('#back').hide();//Back-button is hidden
                                adminwritten=1;
                                   }
                                        },
                error: function (){
                                $('#d7_request').html('{$ajax_failed}');
                                $('#d7_request').removeClass('ok');
                                $('#d7_request').addClass('warning2',1000);
                                

                }

 }); //AJAX

 } //ELSE-ANWEISUNG

}) //ADMIN_SETTINGS FUNCTION
//////////////////////////////////////////////////////////////////////


   //BUTTON: SHOW THE PASSWORDS
$('#btn_showpass').click(function(){
        if (showpassbutton==0){
        summary1 = process_settings(1);
        $('#summary').html(summary1);
        $('#btn_showpass').html('Hide passwords');
        showpassbutton=1;
        }
        else{
        summary1 = process_settings(0);
        $('#summary').html(summary1);
        $('#btn_showpass').html('Show passwords');
        showpassbutton=0;
        }


})

//BUTTON: SEND SUMMARY AS E_MAIL
$('#btn_sendemail').click(function(){
            if (showpassbutton==0)  alert ('{$alert_pw1}');
                        
                var email_to=prompt("{$prompt_email1}", $('[name=web_email]').val());
                var checksyntax = validatemail (email_to);
          if (email_to!=null && checksyntax) {
                 summary1 = process_settings(3);
                 installstep = '14';
                 var dataValues = '{"installstep":"'+installstep+'","email_to":"'+email_to+'","summary":"'+summary1+'"}';
                 $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(){
                    $('#d8_request3').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {

                             if (msg.status==0) //If e-mail sending fails
                            {
                                $('#d8_request3').html(msg.statusmessage);
                                 $('#d8_request3').removeClass('ok');
                                $('#d8_request3').addClass('warning2',1000);


                            }
                        else {    //If e-mail sending is OK
                                $('#d8_request3').html(msg.statusmessage);
                                $('#d8_request3').removeClass('warning2');
                                $('#d8_request3').addClass('ok',1000);
                                
                                   }
                                        },
                error: function (){
                                $('#d8_request3').html('{$ajax_failed}');
                                $('#d8_request3').removeClass('ok');
                                $('#d8_request3').addClass('warning2',1000);
                                 }
 }); //AJAX

 } //ELSE-ANWEISUNG

}) //sendemail-function

//--------------------------------
//BUTTON: HELP to IMPROVE CUNITY
//----------------------------------

$('#btn_improve').click(function(){
installstep = '15';
var dataValues = '{"installstep":"'+installstep+'","lang":"'+lang+'","name":"'+cs_c_name+'","url":"'+cs_c_url+'","email":"'+cs_c_contact+'","purpose":"'+cs_c_purpose+'","country":"'+cs_c_country+'","version":"'+version_number+'"}';

    $.ajax
    ({
      // Data for jsonp.
      dataType: 'jsonp',

      // Function Callback name between client and server
      jsonp: 'jsonpCallbackFunction',
      //data: take these datavalues
      data: {json_data : dataValues},

      // The External URL that is called
      url: 'http://www.cunity.net/install/update.php',

      // This happens in case of SUCCESS
      success: function (msg)
               {
                  if (msg.status==0) //If check fails
                            {
                                $('#d9_request').html(msg.statusmessage);
                                 $('#d9_request').removeClass('ok');
                                $('#d9_request').addClass('warning2',1000);


                            }
                        else {    //If check sending is OK
                                $('#d9_request').html(msg.statusmessage);
                                $('#d9_request').removeClass('warning2');
                                $('#d9_request').addClass('ok',1000);

                                   }
               },
               error: function (){
                                $('#d9_request').html('{$ajax_failed}');
                                $('#d9_request').removeClass('ok');
                                $('#d9_request').addClass('warning2',1000);
                                 }
               
     }); //AJAX CLOSING BRACKETS

 }); //BUTTON CLICK


function reg_anon(){
        installstep = '20';
        var dataValues = '{"installstep":"'+installstep+'","purpose":"'+cs_c_purpose+'","country":"'+cs_c_country+'"}';
        
            $.ajax
            ({
              // Data for jsonp.
              dataType: 'jsonp',
        
              // Function Callback name between client and server
              jsonp: 'jsonpCallbackFunction',
              //data: take these datavalues
              data: {json_data : dataValues},
        
              // The External URL that is called
              url: 'http://www.cunity.net/install/update.php',
        
              // This happens in case of SUCCESS
              success: function (msg)
                       {
                       
                       },
                       error: function (){
                                         }
        
             }); //AJAX CLOSING BRACKETS

 } //FUNCTION REG_ANON




 // DELETE INSTALLAJAX.PHP TO SECURE CUNITY
$('#btn_delete').click(function(){
installstep = 16;
secure_cunity=1;
var dataValues = '{"installstep":"'+installstep+'"}';

    $.ajax({
                type: "POST",
                url: "securecunity.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(){
                    $('#d10_request1').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {

                             if (msg.status==0) //If database writing fails
                            {
                                $('#d10_request1').html(msg.statusmessage);
                                 $('#d10_request1').removeClass('ok');
                                $('#d10_request1').addClass('warning2',1000);


                            }
                        else {    //If deleting is OK
                                $('#d10_request1').html(msg.statusmessage);
                                $('#d10_request1').removeClass('warning2');
                                $('#d10_request1').addClass('ok',1000);
                                   }
                                        },
                error: function (){
                                $('#d10_request1').html('{$ajax_failed}');
                                $('#d10_request1').removeClass('ok');
                                $('#d10_request1').addClass('warning2',1000);


                }

 }); //AJAX

 }); //BUTTON CLICK


 // SECURE LINUX DIRECTORIES AND CONFIG.PHP WITH 0740
$('#btn_linux').click(function(){
installstep = 17;
var dataValues = '{"installstep":"'+installstep+'"}';

    $.ajax({
                type: "POST",
                url: "securecunity.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(){
                    $('#d10_request2').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {

                             if (msg.status==0) //If database writing fails
                            {
                                $('#d10_request2').html(msg.statusmessage);
                                 $('#d10_request2').removeClass('ok');
                                $('#d10_request2').addClass('warning2',1000);


                            }
                        else {    //If deleting is OK
                                $('#d10_request2').html(msg.statusmessage);
                                $('#d10_request2').removeClass('warning2');
                                $('#d10_request2').addClass('ok',1000);
                                   }
                                        },
                error: function (){
                                $('#d10_request2').html('{$ajax_failed}');
                                $('#d10_request2').removeClass('ok');
                                $('#d10_request2').addClass('warning2',1000);


                }

 }); //AJAX

 }); //BUTTON CLICK


 //END CUNITY BUTTON
 $("#end").click(function() {
    
    if (secure_cunity==0)
        {
           $('#errormessage').html('{$warning_security1}');
           secure_cunity=1;
            $('#error').show();  
        }
     else 
            {
                installstep = 18;
                var dataValues = '{"installstep":"'+installstep+'"}';

    $.ajax({
                type: "POST",
                url: "securecunity.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                
                success: function (msg) {
                            }

            }); //AJAX
            }
            window.location="../index.php"; // REDIRECTING TO THE CUNITY START   
	}); // BACK BUTTON




//------------------------------
/////CLICKING ON SKIP BUTTON
//------------------------------

$('#skip').click(function(){
    n++;
    $('.req').removeClass('warning');
    prevdiv = nextdiv;
    nextdiv = '#d'+n;
    if (!(prevdiv==nextdiv)) {
    $(prevdiv).hide();
    $(nextdiv).show();
    if (n>6) $('#skip').hide();
    }
});

//------------------------------
/////CLICKING ON JUMP BUTTON
//------------------------------

$('#btn_jump').click(function(){
    if (confirm('{$jump_confirm}'))
     {
        var jump_n=0;
        jump_n=parseInt($('[name=jump]').val());
        n=jump_n;
        $('.req').removeClass('warning');
        prevdiv = '#d'+(n-1);
        nextdiv = '#d'+n;
        if(n==5) backtoinstall(); 
        $('#d2').hide();
        $(nextdiv).show();
    }
    
});



//------------------------------
/////CLICKING ON NEXT BUTTON
//------------------------------

$("#next").click(function() {
    
    switch (n)
    {
    case 0: 
    $('#back').show();
    n++;   //FROM LANGUAGE TO THE TERMS
    $('.req').removeClass('warning');
    break;
  
    
    case 1:        //FROM TERMS TO EXPLANATIONS
    	    
            if (!$('#cb_terms').attr('checked') && debug_mode==0) {
            $('#errormessage').html('{$warning_terms}');
            $('#error').show();
            }
            else {
            //$('#next').hide(); 
            n++;
            
            $('.req').removeClass('warning');}
    break;
    
    case 2:
    n++;   //FROM EXPLANATIONS TO THE DATABASE INSTALLATION
    $('.req').removeClass('warning');
    if (debug_mode==0) $('#next').hide();
    break;

    
    case 3:  //FROM DATABASE INSTALLATION TO SETTINGS OF WEBPAGE
    
            if (dbinstallallow==0 && debug_mode==0) {
            $('#errormessage').html('{$warning_db1}');
            $('#error').show();    
            }
            else if (dbinstalled == 0 && debug_mode==0) {
            $('#errormessage').html('{$warning_db2}');
            $('#error').show();
            }
            else {
                n++;
                url_address=url_address.substring(0,url_address.indexOf('/installer'));
                $('[name=web_url]').val(url_address);
                if (debug_mode==0) $('#next').hide();
                ;
                 
            }     
    
     break;
   case 4:  //THIS IS FROM WEBSITE SETTINGS TO FILE PATH
             if ((!$('[name=web_name]').val()||!$('[name=web_slogan]').val()||!$('[name=web_url]').val()||!$('[name=web_email]').val())&& debug_mode==0) {
            $('#errormessage').html('{$fill_out}');
            $('.req').addClass('warning');
            $('#error').show();
            }
            else {
                
                backtoinstall();
                if(debug_mode==0) $('#next').hide();
                n++;
                      
                }
        break;
   
   case 5:  //THIS IS THE STEP FROM SETTING THE FILE PATH OUTSIDE THE CUNITY TO THE SMTP SETTINGS
            
            if ($('[name=filefolder]').val()=='' && debug_mode==0){
             $('#errormessage').html('{$warning_fs1}');   
            }
            else if (foldercheck==0 && debug_mode==0){
             $('#errormessage').html('{$warning_fs2}');   
            }
            
            else    
            {
            installstep = '13';
            var cur_filefolder=$('[name=filefolder]').val();
            var dataValues = '{"installstep":"'+installstep+'","cur_filefolder":"'+cur_filefolder+'"}';
           
    $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json",
                beforeSend: function(){
                    $('#d5_request').html('<img src="./img/ajax-loader.gif"/>');
                                    },

                success: function (msg) {

                             if (msg.status==0) //If writing filefolder fails
                            {
                                $('#d5_request').html(msg.statusmessage);
                                $('#d5_request').removeClass('ok');
                                $('#d5_request').addClass('warning2',1000);
                                if (debug_mode==0) $('#next').hide();
                                
                                
                            }
                        else {    //If writing of the filefolder to the config.php is OK
                                $('#d5_request').html(msg.statusmessage);
                                $('#d5_request').removeClass('warning2');
                                $('#d5_request').addClass('ok',1000);
                                cs_filefolder = cur_filefolder;
                                if(debug_mode==0) $('#next').hide();
                                n++;
                                 
                             }
                                        },
                error: function (){
                                $('#d5_request').html('{$ajax_failed}');
                                $('#d5_request').removeClass('ok');
                                $('#d5_request').addClass('warning2',1000);
                                if (debug_mode==0) $('#next').hide();
                                }
                                
            }); //AJAX
            url_address=url_address.replace('http://', '');
            $('[name=smtp_host]').val(url_address);
            $('[name=smtp_sender]').val($('[name=web_name]').val());
            $('[name=smtp_email]').val('noreply@'+url_address.replace('www', ''));
            
            }
       break;
       
            
   case 6:    //THIS IS THE STEP FROM THE SMTP SETTINGS TO CREATING THE ADMIN ACCOUNT
            if ((!$('[name=smtp_host]').val()||!$('[name=smtp_email]').val()||!$('[name=smtp_sender]').val())&& debug_mode==0) {
            $('#errormessage').html('{$fill_out}');
            $('.req').addClass('warning');
            $('#error').show();
            }
            else {
                if(debug_mode==0) $('#next').hide();
                n++;

                }
      break;
                     
   
   case 7: //THIS IS THE STEP FROM THE ADMIN ACCOUNT CREATION TO THE SUMMARY 
         
          if (adminwritten==0 && debug_mode==0) {
         
            $('#errormessage').html('{$warning_admin}');
            $('.req').addClass('warning');
            $('#error').show();
            }
            else if (($('[name=admin_pass1]').val()).length<6 && debug_mode==0) {
            $('#errormessage').html('{$warning_pw1}');
            $('.req').addClass('warning');
            $('#error').show();
            }
            else if ($('[name=admin_pass1]').val()!=$('[name=admin_pass2]').val() && debug_mode==0) {
            $('#errormessage').html('{$pw_no_match2}');
            $('.req').addClass('warning');
            $('#error').show();
            }
            else if ($('[name=admin_email1]').val()!=$('[name=admin_email2]').val() && debug_mode==0) {
            $('#errormessage').html('{$emails_no_match}');
            $('.req').addClass('warning');
            $('#error').show();
            }
            
            else {
         
                //Show the summary
                summary1 = process_settings(0);
                $('#summary').html(summary1);
                n++;

                }
                        
   break;
   
   case 8: //THIS IS THE STEP FROM THE SUMMARY TO CHECKING FOR UPDATES
             reg_anon();
             n++;

   break;

   case 9: //THIS IS THE STEP FROM UPDATES TO DELETING OF INSTALL FOLDER
              
           //NOW WE ARE GOING TO SET THE $cunity_installed variable in the config.php to 1, so 
           //from now on, if you start the index.php, it will start the software and not
           //the installer. 
            installstep = '19';
            var dataValues = '{"installstep":"'+installstep+'"}';

            $.ajax({
                type: "POST",
                url: "installajax.php",
                data: {json_data : dataValues},
                async: false,
                //contentType: "application/json; charset=utf-8",
                dataType: "json"
            }); //AJAX
             $('#d10path').html(file_path);
             $('#next').hide();
             n++;
   break;

   default:

   }//switch

 
if (n>10) n=10;
$('.req').removeClass('warning');
prevdiv = nextdiv;
nextdiv = '#d'+n;
if (!(prevdiv==nextdiv)) {
$(prevdiv).hide();
$(nextdiv).show();
}
if (n!=4 || debug_mode==1)  {$("#back").show();}
else $('#back').hide();

if (n==10)
    {
        $('#back').hide();
        $('#next').hide();
        $('#end').show();
    }

if (n==5 || n==6) $('#skip').show();
else $('#skip').hide();

});//THIS IS CLOSING BRACKETS IF NEXT-BUTTON IS CLICKED

$("#back").click(function() {
n--;
if (n==5 || n==6) $('#skip').show();
else $('#skip').hide();
if (n<=0) {n=0; $("#back").hide();}
prevdiv = nextdiv;
nextdiv = '#d'+n;
if (!(prevdiv==nextdiv)) {
$(prevdiv).hide();
$(nextdiv).show();
if (n==0) $('#next').hide();
else $('#next').show();

	}

	}); // BACK BUTTON




 });//(document).ready()


  // 



