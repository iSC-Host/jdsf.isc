	$.ajaxSetup ({
		cache: false
	});

	$("document").ready(function(){
	    $("#searchFriend").Watermark("{-$search_member}");                	    

		$("#searchMember").live('click',function(){
			var el = this;
			{-if $cunityconnected == true}
			if($("#searchOption").val()=="none")
				localSearch($("#searchFriend").val());
			else if($("#searchOption").val()=="open")
				openSearch($("#searchFriend").val());
			{-else}
				localSearch($("#searchFriend").val());
			{-/if}					                               
        })
        
        

        $(".main_list_wrap")
            .live('mouseover',function(){
                $(this).children(".main_list_photos").show();
            })

            .live('mouseout', function(){
                $(this).children(".main_list_photos").hide();
            })            
    });

	window.setTimeout(function() {
	   {-if SEARCH}
	   $(document).ready(function() {
		   var data = '{"action":"instantSearch", "searchTerm": "{-$Q}"}';
            $.post( "controllers/ajaxFriendsController.php", {json_data: data}, function(data_back){
                if(data_back.status == 1){
                    $("#myFriends").html(data_back.membersFound);
                    $("#search_load").hide();
                    $("#SearchMembersOnPartner").removeAttr('disabled');
                    $("#searchFriend").removeAttr('disabled');
                    $("#selectPartnerCunities").removeAttr('disabled');                    
                    refreshButtons();
                    imgLoadCheck();
                }
            }, "json");
       })
	   {-else}	   
	   $(document).ready(function() {
			var data = '{"c":"myFriends", "userid":"{-$USER}"}';
			$.post("controllers/ajaxFriendsController.php", {json_data:data},
				function (data_back) {
					$("#myFriends").html(data_back.messages);
					if(data_back.navigation=="on")
        			    $("#divLoadMoreStatus").show();
                    else
        			    $("#divLoadMoreStatus").hide();
        			refreshButtons();
        			imgLoadCheck();
                }, "json");
		});	   	   		
		{-/if}
	}, 200);
	
function openSearch(term){
	prepareSearch();
	var data = '{"action":"openSearch", "searchTerm": "' + term + '"}';
    $.post( "controllers/ajaxFriendsController.php", {json_data: data}, function(data_back){
        if(data_back.status == 1){
            $("#myFriends").html(data_back.membersFound);
            successSearch();
            refreshButtons();
            imgLoadCheck();
        }
    }, "json");
}

function localSearch(term){
	prepareSearch();
	var data = '{"action":"instantSearch", "searchTerm": "' + term + '"}';
    $.post( "controllers/ajaxFriendsController.php", {json_data: data}, function(data_back){
        if(data_back.status == 1){
            $("#myFriends").html(data_back.membersFound);
            successSearch();
            refreshButtons();
        }
    }, "json");
}

function prepareSearch(){
	$("#search_load").show();
	$("#searchMember").button("disable");
	$("#searchFriend").attr('disabled','disabled');
	$("#searchOption").attr('disabled','disabled');
}

function successSearch(){
	$("#search_load").hide();
	$("#searchMember").button("enable");
	$("#searchFriend").removeAttr('disabled');
	$("#searchOption").removeAttr('disabled');
}

function refreshFriends(data){
    var data = '{"c":"myFriends","userid": "{-$USER}"}';
	$.post("controllers/ajaxFriendsController.php", {json_data:data},function (data_back) {
		$("#myFriends").html(data_back.messages);
		if(data_back.navigation=="on"){
		    $("#divLoadMoreStatus").show();
		}else{
		    $("#divLoadMoreStatus").hide();
		}
		refreshButtons();
		imgLoadCheck();
	}, "json");	
}

function removeUser(user){
	$("#friend-"+user).fadeOut(200,function(){
		$(this).remove();
	})
}

function addFriendButton(id,text){
	$("#friendButton-"+id).button("option","label",text);
	$("#friendButton-"+id).button("option","icons",{primary: 'ui-icon-clock'});
	$("#friendButton-"+id).attr("onclick","");
}

function loadAllFriends(){
    $("#loadMoreFriends").hide();
    $("#moreFriendsLoad").show();    
    var data = '{"c":"myFriends","userid": "{-$USER}", "option": "showALL"}';
	$.post("controllers/ajaxFriendsController.php", {json_data:data},
		function (data_back) {
			$("#myFriends").html(data_back.messages);
			$("#divLoadMoreStatus").hide();
			$("#loadMoreFriends").show();
            $("#moreFriendsLoad").hide();
            refreshButtons();
            imgLoadCheck();
		}, "json");
}