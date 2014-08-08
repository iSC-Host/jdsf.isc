$.ajaxSetup ({
	cache: false
});

$("document").ready(function(){
	refreshRequests();			 	    
});
function refreshRequests(){
    var data = '{"c":"myRequests"}';
	$.post("controllers/ajaxFriendsController.php", {json_data:data},
		function (data_back) {
			$("#Requests").html(data_back.messages);
			refreshButtons();
		}, "json");
}
function removeUser(user){
	$("#friend-"+user).remove();
    if($("#Requests .main_list_wrap").length==1)
        $("#Requests .message_red").show();
}