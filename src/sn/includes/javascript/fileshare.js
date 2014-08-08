var allChecked=false,allSharedChecked=false,scrollApi;

$(document).ready(function(){

    var data_files = '{"action":"loadMyFilesList"}';
    $.post("controllers/ajaxFileShareController.php", {json_data : data_files},function(data){
        if(data.status==0){
            $("#myFilesArea").html(data.error);
        }else{
            $("#myFiles").html(data.myFiles);
    		refreshButtons();
    		$("#filesWrap").jScrollPane();
        }
    }, "json");

    data_shared = '{"action":"loadMyShareList"}';
    $.post("controllers/ajaxFileShareController.php", {json_data : data_shared},function(data_back) {
        if(data_back.status==0){
            $("#sharedFilesArea").html(data_back.error);
        }else{
            $("#mySharedFiles").html(data_back.myFiles);
            refreshButtons();
            $("#sharedWrap").jScrollPane();
        }
    }, "json");

    $("<div />",{id: "likeDiv"}).appendTo("body").dialog({autoOpen: false,buttons:{"{-$filesharing_close}":function(){$(this).dialog("close");}}});
    //Event handlers for comment-functions
    //event handler for focus, blur or keyup on the comment area
    $("#comment_area").live({
    	focus: function(){
            $(this).css("height","40px");
            $("#send_comment").show();
        },
        blur: function(){
            if($(this).val() == ""){
                $(this).css("height","22px");
                $("#send_comment").hide();
            }
        },
        keyup: function(){
            var str = new String($(this).val());
            if(str.length > 0 && str != " "){
                $("#send_comment").children("button")
                    .removeAttr('disabled')
                    .button('enable');
            }else
                $("#send_comment").children("button")
                    .attr('disabled', 'disabled')
                    .button('disable');
        }
    })
    //event handler for showing the delete-comment image
    $(".comment").live({
    	mouseenter: function(){
    		var id = $(this).attr('id').split('-');
            $("#"+id[1]+"_del").show();
    	},
    	mouseleave: function(){
    		var id = $(this).attr('id').split('-');
            $("#"+id[1]+"_del").hide();
    	}
    })
	$("#comment_wrap").live({
		mouseenter:function(){
			$("#comment_wrap .jspVerticalBar").fadeIn();
		},
		mouseleave:function(){
			$("#comment_wrap .jspVerticalBar").fadeOut();
		}
	})
	
	$(".state, .selectAll").live('change',function(){
        if($(this).is(":checked")&&$(".state:checked").length>0){
            $("#multipleMenu").slideDown(200);
        }else if($("#multipleMenu").is(":visible")&&$(".state:checked").length==0){
            $("#multipleMenu").slideUp(200);
        }
    })
})

function downloadFile(fileId,cunityId){
    location.href='download_file.php?id='+fileId+"&cid="+cunityId;
}

function deleteFile(fileId){
    apprise('{-$filesharing_confirm_delete}', {verify: true}, function(r){if(r){
        var dataValues = '{"action": "deleteFile","fileid":"'+fileId+'"}';
    	$.post("controllers/ajaxFileShareController.php", {json_data : dataValues},function(data){
            if(data.status==1){
                $("#detailContainer").dialog('close');
                $("#fileRow-"+fileId).remove();
    		}
    	}, "json");
	}});
}

function deleteMultipleFiles(){
    apprise('{-$filesharing_confirm_multiple_delete}', {verify: true}, function(r){
        if(r){
            var checks = new Array();
            $(".state:checked").each(function(){checks.push($(this).val());});
                var dataValues = '{"action": "deleteMultipleFiles","fileids":"'+checks+'"}';
            	$.post("controllers/ajaxFileShareController.php", {json_data : dataValues},function(data){
                    if(data.status==1){
                        $.each(checks,function(key,value){
                            $("#fileRow-"+value).remove();
                        })
            		}
            	}, "json");
	   }
    });
}

function unshareFile(fileId,cunityId){
    var dataValues = '{"action": "unshare","fileid":"'+fileId+'","cunityId":'+cunityId+'}';
	$.post("controllers/ajaxFileShareController.php", {json_data : dataValues},function(data){
        if(data.status==1){
            $("#detailContainer").dialog('close');
            $("#fileRow-"+fileId).remove();
		}
	}, "json");
}

function dropDown(){
    if($("#fileDrop").is(':visible'))
        $("#fileDrop").slideUp(200);
    else
        $("#fileDrop").slideDown(200);
}

function showMenu(menuId){
    if($("#"+menuId).is(':visible')){
        $(".file-dropdown-menu").slideUp(200);
    }else{
        $(".file-dropdown-menu").slideUp(200);
        $("#"+menuId).slideDown(100);
    }
}

function fileDetails(fileid,cunityId){
    var dataValues = '{"action": "fileDetails","fileid":"'+fileid+'","cunityId":'+cunityId+'}';
	$.post("controllers/ajaxFileShareController.php", {json_data : dataValues},function(data){
        if(data.status==1){
            $("<div />",{id:"detailContainer"}).html(data.content).dialog({title:data.title,width:830,buttons:{"{-$filesharing_close}":function(){scrollApi.destroy();$(this).dialog('close');$(this).remove();}}});
            refreshButtons();
            imgLoadCheck();
            if($("#likeCount").html()==0)
    			$("#image_likes").hide();
    		if($("#dislikeCount").html()==0)
    			$("#image_dislikes").hide();
    		if($("#dislikeCount").html()==0&&$("#likeCount").html()==0)
    			$("#like_container").hide();
            var scroll=$("#comment_wrap").jScrollPane();
            $("#comment_wrap .jspVerticalBar").hide();
            scrollApi=scroll.data('jsp');
		}
	}, "json");
}

function comment(fileId,message){
    $("#send_comment").children("button").button("disable");
    $("#send_comment_load").show();
    $("#comment_area").attr('disabled', 'disabled');
    var dataValues = '{"action": "addComment","id":"'+ fileId + '", "message": "' + message + '"}';
	$.post("controllers/ajaxFileShareController.php", {json_data : dataValues},function(data){
		if(data.status ==1){
			$("#comments_box").append(data.comments);
			$("#send_comment_load").hide();
			$("#comment_area").val("").removeAttr('disabled');
			scrollApi.reinitialise();
            scrollApi.scrollToPercentY(100);
		}
	}, "json");
}

function deleteComment(comment_id){
    apprise('{-$filesharing_delete_comment}', {verify: true}, function(r){if(r){
        var dataValues = '{"action": "deleteComment","id":"'+ comment_id+ '"}';
    	$.post("controllers/ajaxFileShareController.php", {json_data : dataValues},function(data){
            if(data.status == 1){
            	$("#comment-"+comment_id).remove();
            	scrollApi.reinitialise();
            }
      	}, "json");
    }
    });
}

function likeFile(fileId){
    var dataValues = '{"action":"like","id":'+fileId+'}';
	$.post("controllers/ajaxFileShareController.php", {json_data : dataValues},function(data){
		if(data.status ==1){
			if(data.likeCount>0||data.dislikeCount>0)
				$("#like_container").show();
			else if(data.likeCount==0&&data.dislikeCount==0)
				$("#like_container").hide();
			if(data.likeCount==0)
	 			$("#image_likes").hide();
	 		else{
	 			$("#likeImages").html(data.likes);
	 			$("#likeCount").html(data.likeCount);
	 			$("#image_likes").show();
	 		}
	 		if(data.dislikeCount==0)
	 			$("#image_dislikes").hide();
	 		else{
	 			$("#dislikeImages").html(data.dislikes);
				$("#dislikeCount").html(data.dislikeCount);
				$("#image_dislikes").show();
	 		}
			$("#likeButton").hide().next().show();
		}
	},"json");
}

function dislikeFile(fileId){
    var dataValues = '{"action":"dislike","id":'+fileId+'}';
	$.post("controllers/ajaxFileShareController.php", {json_data : dataValues},function(data){
	 	if(data.status ==1){
	 		if(data.likeCount>0||data.dislikeCount>0)
				$("#like_container").show();
	 		else if(data.likeCount==0&&data.dislikeCount==0)
				$("#like_container").hide();
	 		if(data.likeCount==0)
	 			$("#image_likes").hide();
	 		else{
	 			$("#likeImages").html(data.likes);
	 			$("#likeCount").html(data.likeCount);
	 			$("#image_likes").show();
	 		}
	 		if(data.dislikeCount==0)
	 			$("#image_dislikes").hide();
	 		else{
	 			$("#dislikeImages").html(data.dislikes);
				$("#dislikeCount").html(data.dislikeCount);
				$("#image_dislikes").show();
	 		}
            $("#dislikeButton").hide().prev().show();
		}
	}, "json");
}

function newComment(){
    $("#send_comment").show();
    $("#comment_area").css("height","40px").focus();
    scrollApi.scrollToPercentY(100);
}

function more_comment_cont(comment_id){
    $("#more_comment-"+comment_id).hide();
    $("#more_comment_cont-"+comment_id).show();
}

function less_comment_cont(comment_id){
	$("#more_comment_cont-"+comment_id).hide();
	$("#more_comment-"+comment_id).show();
}

function showLikes(id, type){
    var dataValues = '{"action": "getLikes","id": "'+id+'","type":"'+type+'"}';
	$.post("controllers/ajaxFileShareController.php", {json_data : dataValues},function(data_back){
		if(data_back.status==1)
			$("#likeDiv").html(data_back.persons).dialog('option','title', data_back.title).dialog('open');
	}, "json");
}

function selectAll(el) {
    if(!allChecked){
        $("input.state").each(function(){
            $(this).attr('checked', 'checked');
        })
        $(".select-all").attr('checked', $(el).attr('checked'));        
        allChecked = true;
    }else{
        $("input.state").each(function(){
            $(this).removeAttr('checked');
        })
        $(".select-all").attr('checked', $(el).attr('checked'));
        allChecked = false;
    }
}

function selectAllShared(el) {
    if(!allSharedChecked){
        $("input.share_state").each(function(){
            $(this).attr('checked', 'checked');
        })
        $(".select-all-shared").attr('checked', $(el).attr('checked'));
        allSharedChecked = true;
    }else{
        $("input.share_state").each(function(){
            $(this).removeAttr('checked');
        })
        $(".select-all-shared").attr('checked', $(el).attr('checked'));
        allSharedChecked = false;
    }
}

function openShareFile(fileId){
    var dataValues = '{"action":"getFriendsForShare","id":"' + fileId + '"}';
	$.post("controllers/ajaxFileShareController.php", {json_data : dataValues},function(data){
        if(data.status==1){
            $("#shareFriends").html(data.friends);
            $("#shareWrap").slideDown(100);
        }
	}, "json");
}

function shareFile(fileId){
    var checks = new Array();
    $("input.share_check:checked").each(function(){
        checks.push($(this).val());
    });
    var dataValues = '{"action":"shareFile","id":'+fileId+',"users":"' + checks + '"}';
	$.post("controllers/ajaxFileShareController.php", {json_data : dataValues},function(data){
        if(data.status==1){
            $("#shareWrap").slideUp(100);
        }
	}, "json");
}