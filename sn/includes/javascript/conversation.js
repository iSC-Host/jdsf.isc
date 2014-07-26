$("document").ready(function(){
	$("#sendMessageButton").button('disable');	
	$("#msgTa")
		.keyup(function(){
			var letters = 750-$(this).val().length;
			$("#lettersLeft").html(letters);
			if($(this).val().length>0){
				$("#lettersLeftWrap").slideDown(100);
				$("#sendMessageButton")
					.removeAttr('disabled')
					.button('enable');
			}	
			else{
				$("#lettersLeftWrap").slideUp(100);
				$("#sendMessageButton")
					.attr('disabled','disabled')
					.button('disable');
			}				
		})
		.focus(function(){
			$(this).height(52);
		})
		.blur(function(){
			if($(this).val().length==0)
				$(this).height(40);
		})	
	
	$(".deleteMessage")
		.live('mouseenter', function(){			
			$(this)
				.removeClass("ui-icon-close")
				.addClass("ui-icon-circle-close");
		})
		.live('mouseleave', function(){
			$(this)
				.removeClass("ui-icon-circle-close")
				.addClass("ui-icon-close");
		});
})

window.setTimeout('loadConversation()',50);
window.setInterval('refreshConversation()', 10000);
var scrollApi;

function deleteConversation(){
	apprise("",{confirm:true},function(r){
		if(r){
			var data = '{"action":"deleteConversation","userid":{-$USER},"cid":{-$CUNITYID}}';
			$.post("controllers/ajaxMessageController.php", {json_data : data}, function(jData) {
				if(jData.status==1)    			
					location.href='messages.php';
				else
					apprise("An Error occurred! Please try again later!");
		    }, "json");
		}
	})	
}

function deleteMessage(message_id){
	apprise("{-$conversation_delete_confirmation}", {verify:true},function(r){
		if(r){
			var data = '{"action":"deleteMessage","message_id":' + message_id + '}';
			$.post("controllers/ajaxMessageController.php", {json_data : data}, function(jData){
				if(jData.status==1){    			
					$("#message-"+message_id)
						.fadeOut(200,function(){
							$("#message-"+message_id).remove();
						})
						
				}   
		    }, "json");
		}
	})	
}

function loadConversation(){	
	var data = '{"action":"loadConversation","conversation":{-$USER},"cid":{-$CUNITYID}}';
	$.post("controllers/ajaxMessageController.php", {json_data : data}, function(jData) {
		if(jData.status==1){    			
			$("#conversation").html(jData.content);
			refreshButtons();
			scroll=$("#conversationWrap").jScrollPane();
			scrollApi=scroll.data('jsp');
			scrollApi.scrollToPercentY(100);
		}    			
		else
			apprise("An Error occurred! Please try again later!");
    }, "json");
}

function refreshConversation(){
	var data = '{"action":"refreshConversation","conversation":{-$USER},"cid":{-$CUNITYID}}';
	$.post("controllers/ajaxMessageController.php", {json_data : data}, function(jData) {
		if(jData.status==1){    			
			$("#conversation").append(jData.content);			
			refreshButtons();
			scrollApi.reinitialise();
		}   
    }, "json");
}

function sendMessage(msg){
	$("#messageLoader").show();
	var data = '{"action":"sendMessage","conversation":{-$USER},"cid":{-$CUNITYID},"message":"' + msg + '"}';
	$.post("controllers/ajaxMessageController.php", {json_data : data}, function(jData) {
		if(jData.status==1){
			$("#messageLoader").hide();
			$("#msgTa").val("");
			$("#sendMessageButton")
				.attr('disabled','disabled')
				.button('disable');
			$("#conversation").append(jData.message);
			$("#lettersLeft").html(750-$("#msgTa").val().length);
			$("#lettersLeftWrap").slideUp(100);
			refreshButtons();
			scrollApi.reinitialise();
			scrollApi.scrollToPercentY(100);
		}    			
		else
			apprise("An Error occurred! Please try again later!");
    }, "json");
}