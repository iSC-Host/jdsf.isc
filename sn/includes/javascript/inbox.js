$("document").ready(function(){
	loadConversations();	
	var receiver_length=0;
	$("#receiver").autocomplete({
		source: 'controllers/ajaxFriendsController.php?json_data={"action":"fieldSearch"}',
		minLength: 1,
		select: function( event, ui ) {
			$(this).val(ui.item.label);
			$("#receiver").after('<input type="hidden" id="receiver_id_input" name="receiver_data" value="'+ui.item.cid+'-'+ui.item.hash+'"/>');
			$("#receiver").autocomplete("close");
			receiver_length = $("#receiver").val().length;
		}
	});	
	$("#receiver").keyup(function(){
		if($("#receiver_id_input").length>0&&receiver_length>0){
			if($("#receiver").val().length!=receiver_length){
				$("#receiver").val("");
				$("#receiver_id_input").remove();
				receiver_length=0;
			}
		}
			
	})
	$("#msgTa").keyup(function(){
		if($(this).val().length>0)
			$("#sendMessageButton")
				.removeAttr('disabled')
				.button('enable');
		else
			$("#sendMessageButton")
				.attr('disabled','disabled')
				.button('disable');
	})
})
function loadConversations(){
	var data = '{"action":"loadConversations"}';
	$.post("controllers/ajaxMessageController.php", {json_data : data}, function(data) {
		if(data.status==1)
			$("#conversations").html(data.content);
		else
			apprise("An Error occurred! Please try again later!");
    }, "json");
}