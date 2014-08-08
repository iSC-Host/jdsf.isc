<?php /* Smarty version 2.6.26, created on 2014-02-07 15:21:49
         compiled from file:style/newcunity/templates/register.tpl.html */ ?>
<div class="bar">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1><?php echo $this->_tpl_vars['register']; ?>
</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<script language="javascript" type="text/javascript">function compareField(field,field2,element){
	if(field==field2)
		fieldChecked(element);
	else
		fieldFailed(element);
}

function fieldChecked(fieldname){
	$("#"+fieldname+"_checked")
		.removeClass('register_field_failed')
		.addClass('register_field_checked');
}

function fieldFailed(fieldname){
	$("#"+fieldname+"_checked")
		.removeClass('register_field_checked')
		.addClass('register_field_failed');
}

function checkFieldLength(value,minlength,fieldname){
	if(value.length>=minlength)
		fieldChecked(fieldname);
	else
		fieldFailed(fieldname);
}

function checkField(fieldname,value,checkExist){
	 var data = '{"action":"checkInput", "field": "'+fieldname+'","input":"'+value+'","checkExist":"'+checkExist+'"}';
     $.post( "controllers/ajaxRegisterController.php", {json_data: data}, function(data_back){
         if(data_back.status == 0){
        	 fieldFailed(fieldname);
         }else if(data_back.status==1){
        	 fieldChecked(fieldname);
         }        	
     }, "json");	
}

function showPage(title){
	$("#"+title+"_dialog").dialog('open');	
}

$("document").ready(function(){
	var currentDate = new Date();
	var minBirthday = new Date(currentDate.getFullYear()- <?php echo $this->_tpl_vars['cunitysettings']['register_age']; ?>
,currentDate.getMonth(),currentDate.getDate(),0,0,0,0);
	
	$("#terms_dialog, #privacy_dialog").dialog({
		modal:true,
		autoOpen:false,
		stack:false
	});
	
	
	$("#year,#month,#day").change(function(){
		if($("#year").val()==""||$("#month").val()==""||$("#day").val()=="")
			fieldFailed('birthday');
		else{
			enteredDate = new Date($("#year").val(),$("#month").val(),$("#day").val(),0,0,0,0);
			if(enteredDate.getTime()>minBirthday)
				fieldFailed('birthday');
			else
				fieldChecked('birthday')
		}
	})
	
    $("#subReg").click(function(){
        $("#error").hide();
        $("#load").show();
        var formData =$("#register_form").serialize();
        var data = '{"action":"sendRegistration", "data": "'+formData+'"}';
        $.post("controllers/ajaxRegisterController.php", {json_data: data}, function(data_back){
            if(data_back.status == 1){
            	$("#load").hide();
            	$("#register_form").hide();            	
            	$("#register_success").show();
            }else if(data_back.status==0){
            	$("#load").hide();
            	$("#error").show();
            	$.each(data_back.errors, function(i,error){
            		$("#"+error).css("borderColor","#FF0000");
            	})
            }        	
        }, "json");
        return false;
    })
})</script>
<div class="message_red" style="display: none;" id="error"><?php echo $this->_tpl_vars['register_error']; ?>
</div>
<div id="load" style="display: none">
<div class="box-main-a-1" style="margin-top: 10px;"></div>
<div class="box-main-a-2" style="text-align:center">
	<img src="style/default/img/load.gif"/>
</div>
<div class="box-main-a-3"></div>
</div>
<div style="display:none;" id="register_success">
<div class="box-main-a-1" style="margin-top: 10px;"></div>
<div class="box-main-a-2">	
	<div style="background: url('style/newcunity/img/check.png') left top no-repeat; padding-left: 58px;height:70px;">
		<h2 style="padding: 5px 0px;"><?php echo $this->_tpl_vars['register_thanks']; ?>
</h2>		
        <p style="font-size: 13px;"><?php echo $this->_tpl_vars['register_success_account']; ?>
</p>
	</div>
</div>
<div class="box-main-a-3"></div>
</div>
<form action="register.php" method="post" id="register_form" style="font-size:14px;width:550px">
	<div class="box-main-a-1" style="margin-top: 10px;"></div>
	<div class="box-main-a-2">
    <table border="0" width="531px">
        <colgroup>
            <col width="210px" />
            <col width="340px" />
        </colgroup>
        
        <tr>
            <td><label for="nickname"><?php echo $this->_tpl_vars['profile_view_nick']; ?>
<span class="required_star">*</span></label></td>

            <td><input type="text" name="nickname" id="nickname" class="inp" onkeyup="checkField('nickname',$(this).val(),true)"/><span id="nickname_checked"></span></td>
        </tr>
        
        <?php echo $this->_tpl_vars['NAMES']; ?>


        <tr>
            <td><label for="mail1">E-Mail<span class="required_star">*</span></label></td>

            <td><input type="text" name="mail1" id="mail1"  class="inp" onkeyup="checkField('mail',$(this).val(),true)"/><span  id="mail_checked"></span></td>
        </tr>

        <tr>
            <td><label for="mail2"><?php echo $this->_tpl_vars['register_rpt']; ?>
<span class="required_star">*</span></label></td>

            <td><input type="text" name="mail2" id="mail2"  class="inp" onkeyup="compareField($('#mail1').val(),$(this).val(),'mail2');"/><span  id="mail2_checked"></span></td>
        </tr>

        <tr>
            <td><label for="pw1"><?php echo $this->_tpl_vars['register_pw']; ?>
<span class="required_star">*</span></label></td>

            <td><input type="password" name="pw1" id="pw1"  class="inp" onkeyup="checkFieldLength($(this).val(),6,'pw1');"/><span  id="pw1_checked"></span></td>
        </tr>

        <tr>
            <td><label for="pw2"><?php echo $this->_tpl_vars['register_rpt']; ?>
<span class="required_star">*</span></label></td>

            <td><input type="password" name="pw2" id="pw2" onkeyup="compareField($('#pw1').val(),$(this).val(),'pw2');"/><span  id="pw2_checked"></span></td>
        </tr>
        
        <tr>
            <td>
                <label id="title_label"><?php echo $this->_tpl_vars['register_address']; ?>
<span class="required_star">*</span></label>
            </td>
            <td>    
            	<select name="sex" style="width:227px;padding:4px;">
            		<option value="1"><?php echo $this->_tpl_vars['register_mr']; ?>
</option>
            		<option value="0"><?php echo $this->_tpl_vars['register_mrs']; ?>
</option> 
            	</select>                              			
            </td>
        </tr>
		<tr>
			<td>
				<label for="birthday"><?php echo $this->_tpl_vars['register_birthday']; ?>
<span class="required_star">*</span></label>
			</td>
			<td>
				<select id="day" name="day" style="padding:4px">
					<?php echo $this->_tpl_vars['DAYS']; ?>

				</select>
				<select id="month" name="month" style="padding:4px">
					<?php echo $this->_tpl_vars['MONTHS']; ?>

				</select>
				<select id="year" name="year" style="padding:4px">
					<?php echo $this->_tpl_vars['YEARS']; ?>

				</select>
				<span id="birthday_checked"></span>
			</td>
		</tr>
        <?php echo $this->_tpl_vars['NEW_FIELDS']; ?>

        <tr><td></td></tr>
        <tr>
            <td colspan="2"><input type="checkbox" id="terms" name="terms" value="yes" /> <label for="terms" id="terms_label"><?php echo $this->_tpl_vars['register_accept']; ?>
 <a href="javascript: showPage('terms');" style="text-decoration: underline;"><?php echo $this->_tpl_vars['register_terms']; ?>
</a></label></td>
        </tr>

        <tr>
            <td colspan="2"><input type="checkbox" id="privacy" name="privacy" value="yes"/> <label for="privacy" id="privacy_label"><?php echo $this->_tpl_vars['register_accept']; ?>
 <a href="javascript: showPage('privacy');" style="text-decoration: underline;"><?php echo $this->_tpl_vars['register_privacy']; ?>
</a></label></td>
        </tr>
    </table>
    </div>
    <div class="box-main-a-3"></div>
    <div class="box-main-a-1" style="margin-top: 10px;"></div>
	<div class="box-main-a-2" style="text-align:right">
    	<button class="jui-button" type="submit" name="subm" id="subReg" style="font-size: 15px;"><?php echo $this->_tpl_vars['register_button']; ?>
</button>
    </div>
    <div class="box-main-a-3"></div>
</form>
<div id="terms_dialog" title="<?php echo $this->_tpl_vars['register_terms']; ?>
"><?php echo $this->_tpl_vars['TERMS']; ?>
</div>
<div id="privacy_dialog" title="<?php echo $this->_tpl_vars['register_privacy']; ?>
"><?php echo $this->_tpl_vars['PRIVACY']; ?>
</div>