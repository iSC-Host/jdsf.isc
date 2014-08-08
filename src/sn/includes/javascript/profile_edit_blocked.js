function removeUser(user){
	$("#friend-"+user).remove();
    if($("#blocked_friends .main_list_wrap").length==0)
        $("#blocked_friends .message_red").show();
}