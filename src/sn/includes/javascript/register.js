function compareField(field,field2,element){
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
	var minBirthday = new Date(currentDate.getFullYear()- {-$cunitysettings.register_age},currentDate.getMonth(),currentDate.getDate(),0,0,0,0);
	
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
})