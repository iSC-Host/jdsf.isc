var share=0;
var isImgUpload = false;
var link = "";
var stop_refresh;
var sort = "all";

$(document).ready(function(){
    {-if PINBOARD}
    $(document).scroll(function(){
        if($(window).scrollTop() >= ($(document).height()-$(window).height()))
            loadMoreStatus();
    })
    {-/if}

    //initialise the pinboard
    init();

    //start the event handler
    eventHandler();
});

function init(){
    loadPinboard();

    //every 30 seconds: refresh pinboard
    window.setInterval('refreshPinboard()',30000);

    //Create Watermarkt and set ebent handler
    $("#watermark")
        .Watermark("{-$pinboard_status_watermark}")
        .live('mouseenter',function(){
            refreshPinboard();
        })
    $("#watermark_image")
        .Watermark("{-$pinboard_image_watermark}");

    //create likesdialog
    $("<div />",{id: "likeDiv"}).appendTo("body").dialog({autoOpen: false,buttons:{"{-$galleries_close}":function(){$(this).dialog("close");}}});

    $(".showLikes").each(function(){
    	$(this).toolTip({
	    	text: "{-$pinboard_info_show_likes}",
	    	bgColor: "rgba(0,0,0,0.9)",
	    	position: "center",
	    	vPosition: "top",
	    	fadeInDuration: 10,
	    	maxWidth:150
	    });
	});
    $(".showDislikes").each(function(){
    	$(this).toolTip({
	    	text: "gdsfdsafsad",
	    	bgColor: "rgba(0,0,0,0.9)",
	    	position: "center",
	    	vPosition: "top",
	    	fadeInDuration: 10,
	    	maxWidth:150
	    });
    });
    $(".showComments").each(function(){
    	$(this).toolTip({
    		text: "{-$pinboard_info_show_comments}",
        	bgColor: "rgba(0,0,0,0.9)",
        	position: "center",
        	vPosition: "top",
        	fadeInDuration: 10,
        	maxWidth:150
        });
    });
}

function eventHandler(){
    //event handler for the share-textarea


    //Check whether youtube-link is in the textarea
    $("#watermark").keyup(function(){
        var str = $(this).val();
        if(str.match(/www\.youtube/)){
            $("#watermark_loader").show();
        }
        var dataValues = '{"action": "checkVideo","str": "' + str + '"}';
		$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},
		function(data){
		 	if(data.status==1){
			    link = str;
			    $("#watermark_loader").hide();
			    $("#video").show().html(data.video);
			    $("#status").hide();
			    $("#watermark_video").focus();
			}
		}, "json");

		var el = this;
        var str = new String($(el).val());
        if(str.length > 0 && str != " " && str != "{-$pinboard_status_watermark}"){
        	$("#share_button").removeAttr('disabled').button('enable');
        	$("#share_button_wrap").slideDown();
        }else{
        	$("#share_button").attr('disabled', 'disabled').button('disable');
        	$("#share_button_wrap").slideUp();
        }

    })

    $("#imgUploadInput").live('change', function(){
    	$("#share_button_wrap").slideDown();
		$("#share_button").removeAttr('disabled').button('enable');
	});

    //event handlers for every pinboard-entry

    //event handler for showing the delete-status image
    $("#pinBoard > .main_list_wrap")
        .live('mouseenter', function(){
            var id = $(this).attr('id').split('-');
            $("#delete_status-"+id[1]).show();
        })
        .live('mouseleave', function(){
            var id = $(this).attr('id').split('-');
            $("#delete_status-"+id[1]).hide();
            if($("#status_drop_"+id[1]).length > 0)
                $("#status_drop_"+id[1]).hide();
        })

    //event handler for drowndown-menu
    $(".pinboard_dropdown").live('click',function(){
    	var id = $(this).attr('id');
        $(".status_dropdown_menu:visible").slideUp(100);
        if($("#status_drop_"+id).is(':visible'))
            $("#status_drop_"+id).slideUp(100);
        else
            $("#status_drop_"+id).slideDown(100);
    });

    //event handler for clicking on play youtube video (this shows the iFrame with the youtube object)
    $(".video_status_img").live('click', function(){
    	var v = $(this).attr('id');
    	var videoframe = $('<iframe />',{
    		height: 233,
    		width: 400,
    		frameborder: 0,
    		allowfullscreen: 1,
    		id: "video_"+v,
    		src: 'http://www.youtube.com/embed/'+v+'?autoplay=1'
    	})
        $(this).replaceWith(videoframe);
        $("#video_description-"+v).css("width","400px");
    })

    //event handler for menu
    $(".ui-menu-item")
       .live('mouseover', function(){
           $(this).children('a').addClass('ui-state-hover');
       })
       .live('mouseout', function(){
           $(this).children('a').removeClass('ui-state-hover');
       });

    //Event handlers for comment-functions

    //event handler for showing the comment-textarea
    $(".status_comment").live('click',function(){
        var id = $(this).attr('id');
        $("#comments-"+id).show();
        $("#comment_avatar-"+id).css("visibility","visible");
        $("#send_comment-"+id).show();
        $("#comment_area-"+id).focus();
    });

    //event handler for focus, blur or keyup on the comment area
    $(".new_comment_area")
        .live('focus', function(){
            var str = $(this).attr('id');
            var id = str.split("-");
            $(this).css({
                "height": "40px"
                });
            $("#comment_avatar-"+id[1]).css("visibility","visible");
            $("#send_comment-"+id[1]).show();
        })
        .live('blur', function(){
            if($(this).val() == ""){
                var str = $(this).attr('id');
                var id = str.split("-");
                $(this).css({
                    "height": "22px"
                    });
                $("#comment_avatar-"+id[1]).css("visibility","hidden");
                $("#send_comment-"+id[1]).hide();
            }
        })
        .live('keyup', function(){
            var el = this;
            var data = $(el).attr('id');
            var id = data.split('-');
            var str = new String($(el).val());
            if(str.length > 0 && str != " "){
                $("#send_comment-"+id[1]).children("button")
                    .removeAttr('disabled')
                    .button('enable');
            }else
                $("#send_comment-"+id[1]).children("button").attr('disabled', 'disabled').button('disable');
        })

    //event handler for showing comments, which are not visible
    $(".show_comments").live('click', function(){
        $(this).parent().hide();
        $("."+$(this).attr('id')).show();
    })

    //event handler for loading all comments
    $(".load_comments").live('click', function(){
        var id = $(this).attr('id').split('-');
        var dataValues = '{"userData":"{-$USERDATA}","cid":{-$CUNITYID},"userData":"{-$USERDATA}","cid":{-$CUNITYID},"p":{-$PINBOARD_ID},"r":"{-$PINBOARD_RECEIVER}","action": "loadComments","id":"'+ id[1] + '"}';
		$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},
		function(data){
		 	if(data.status ==1){
				$("#comments_box_"+id[1]).html(data.comments);
			}

		}, "json");
    })

    //event handler for showing the delete-comment image
    $(".comment")
        .live('mouseover', function(){
            var id = $(this).attr('id').split('-');
            $("#"+id[1]+"_del").show();
        })
        .live('mouseout', function(){
            var id = $(this).attr('id').split('-');
            $("#"+id[1]+"_del").hide();
        })

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


      //event handler for drowndown-menu
      $(".edit_dropdown").live('click',function(){
      	var id = $(this).attr('id');
          if($("#gallery_dropdown").is(':visible'))
              $("#gallery_dropdown").slideUp(100);
          else
              $("#gallery_dropdown").slideDown(100);
      });

}

function refreshPinboard(){
    var dataValues = '{"userData":"{-$USERDATA}","cid":{-$CUNITYID},"p":{-$PINBOARD_ID},"r":"{-$PINBOARD_RECEIVER}","action": "loadPinboard","do": "refresh","s":"'+sort+'"}';
	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},
		function(data){
		if(data.status==1&&data.count>0){
			$("#pinBoard").prepend(data.pinBoardRows);
			if(data.option=="off")
			    $("#submitLoadMoreStatus").hide();
			else
			    $("#submitLoadMoreStatus").show();
			$(".new_comment_area").Watermark("{-$pinboard_comment_watermark}");
			refreshButtons();
			$(".pinboard_image_link:not(.jGalleryLink)").jGallery();
		}

	}, "json");
}

function deleteStatus(id){
    apprise('{-$pinboard_delete_status}', {'verify':true},function(r){
        if(r){
            var dataValues = '{"userData":"{-$USERDATA}","cid":{-$CUNITYID},"p":{-$PINBOARD_ID},"r":"{-$PINBOARD_RECEIVER}","action": "deleteStatus","id":"'+ id+ '"}';
        	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},
        	function(data){
                $("#status-"+id).remove();
        	}, "json");
        }
    });
}

function loadPinboard(type){
	if(type==undefined)
		type=sort;
	var dataValues = '{"userData":"{-$USERDATA}","cid":{-$CUNITYID},"p":{-$PINBOARD_ID},"r":"{-$PINBOARD_RECEIVER}","action": "loadPinboard", "id": "{-$STATUS_ID}","s":"'+type+'"}';
	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},
		function(data){
		if(data.status ==1){
			sort=type;
			$("#pinBoard").html(data.pinBoardRows);
			if(data.option=="off"){
			    $("#submitLoadMoreStatus").hide();
			}else{
			    $("#submitLoadMoreStatus").show();
			}
            $(".new_comment_area").Watermark("{-$pinboard_comment_watermark}");
            refreshButtons();
            $(".pinboard_image_link:not(.jGalleryLink)").jGallery();
		}

	}, "json");
}

function comment(status_id,ressource_id,status_type){
    $("#send_comment-"+status_id).children("button").button("disable");
    $("#send_comment_load-"+status_id).show();
    var message = $("#comment_area-"+status_id).val();
    $("#comment_area-"+status_id).attr('disabled', 'disabled');
    var dataValues = '{"userData":"{-$USERDATA}","cid":{-$CUNITYID},"p":{-$PINBOARD_ID},"r":"{-$PINBOARD_RECEIVER}","action": "addStatusComment","sType":"'+status_type+'","rid":'+ressource_id+',"sid":'+ status_id + ', "message": "' + message + '"}';
	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},
	function(data){
	 	if(data.status ==1){
			$("#comments_abs-"+status_id).append(data.comments);
			$("#comment_area-"+status_id).val("");
			$("#send_comment_load-"+status_id).hide();
			$("#comment_area-"+status_id).removeAttr('disabled');
		}

	}, "json");
}

function showUploadedImage(msg){
    $("#watermark_image").val("").Watermark("{-$pinboard_image_watermark}");
    $("#watermark_loader").hide();
    $("#imgUploadInput").val("");
    $("#pinBoard").prepend(msg);
    $("#pinBoard > .main_list_wrap:first").fadeIn();
    refreshButtons();
    $(".pinboard_image_link:not(.jGalleryLink)").jGallery();
}

function shareStatus(){
    var myStatusField = $("#watermark").val();
    $("#watermark_loader").show();
    $("#share_button").attr('disabled', 'disabled').button('disable');
	if(isImgUpload){
	    $("#imgUploadMessage").val($("#watermark_image").val());
		$("#imgUploadForm").submit();
	}else if(myStatusField !="{-$pinboard_status_watermark}" && myStatusField !="" && myStatusField != " "){
        var dataValues = '{"userData":"{-$USERDATA}","cid":{-$CUNITYID},"p":{-$PINBOARD_ID},"r":"{-$PINBOARD_RECEIVER}","action": "insertPinboardStatus","statusMessage":"'+ encodeURIComponent(myStatusField)+ '","link":"'+encodeURIComponent(link)+'"}';
		$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},function(data){
			if(data.status ==1){
		        link = "";
		        $("#video").html("").hide();
		        $("#image").hide();
		        $("#url").hide();
		        $("#status").show();
                $("#watermark").val("").Watermark("{-$pinboard_status_watermark}");
                $("#watermark_loader").hide();
                $("#pinBoard").prepend(data.statusMessage);
                if($("#pinBoard .message_red").length>0)
                    $("#pinBoard .message_red").remove();
                $("#pinBoard > .main_list_wrap:first").fadeIn();
                refreshButtons();
			}
		},"json");
	}
}

function like(status_id,ressource_id,status_type){
    var dataValues = '{"userData":"{-$USERDATA}","cid":{-$CUNITYID},"p":{-$PINBOARD_ID},"r":"{-$PINBOARD_RECEIVER}","action":"like","sid":'+status_id+',"rid":'+ressource_id+',"sType":"'+status_type+'"}';
	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},function(data){
		if(data.status ==1){
			$("#likes_"+status_id).html(data.likes);
			$("#like_"+status_id).hide().next().show();
			$("#buttonlist-"+status_id).buttonset('refresh');
		}
	},"json");
}

function dislike(status_id,ressource_id,status_type){
    var dataValues = '{"userData":"{-$USERDATA}","cid":{-$CUNITYID},"p":{-$PINBOARD_ID},"r":"{-$PINBOARD_RECEIVER}","action":"dislike","sid":'+status_id+',"rid":'+ressource_id+',"sType":"'+status_type+'"}';
	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},function(data){
	 	if(data.status ==1){
            $("#likes_"+status_id).html(data.likes);
            $("#dislike_"+status_id).hide().prev().show();
            $("#buttonlist-"+status_id).buttonset('refresh');
		}
	}, "json");
}

function deleteComment(id){
    apprise('{-$pinboard_delete_comment}', {verify: true}, function(r){if(r){
            var dataValues = '{"userData":"{-$USERDATA}","cid":{-$CUNITYID},"p":{-$PINBOARD_ID},"r":"{-$PINBOARD_RECEIVER}","action": "deleteComment","id":"'+ id+ '"}';
        	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},function(data){
                    if(data.status == 1) $("#comment-"+id).remove();
          	},"json");
        }
    });
}

function loadMoreStatus(){
    $("#moreStatusLoad").show();
    $("#submitLoadMoreStatus").hide();
	var dataValues = '{"userData":"{-$USERDATA}","cid":{-$CUNITYID},"p":{-$PINBOARD_ID},"r":"{-$PINBOARD_RECEIVER}","action": "loadPinboard","type":"loadMoreStatus","s":"' + sort + '"}';
	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},function(data){
		if(data.status ==1){
			$("#pinBoard").append(data.pinBoardRows);
            $("#moreStatusLoad").hide();
            $("#submitLoadMoreStatus").show();
			if(data.option=="off")
			    $("#submitLoadMoreStatus").hide();
			else
			    $("#submitLoadMoreStatus").show();
            $(".new_comment_area").Watermark("{-$pinboard_comment_watermark}");
            refreshButtons();
            $(".pinboard_image_link:not(.jGalleryLink)").jGallery();
		}
	}, "json");
}

function showLikes(id, type,statusType){
    var dataValues = '{"userData":"{-$USERDATA}","userData":"{-$USERDATA}","cid":{-$CUNITYID},"p":{-$PINBOARD_ID},"r":"{-$PINBOARD_RECEIVER}","action": "getLikes","id": "'+id+'","type":"'+type+'","sType":"'+statusType+'"}';
	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},
		function(data_back){
		if(data_back.status==1){
			$("#likeDiv").html(data_back.persons).dialog('option','title', data_back.title).dialog('open');
		}
	}, "json");
}

function shareImage(){
	$("document").ready(function(){
		$("#status").hide();
		$("#image").show();
		isImgUpload=true;
	})
}

function showStatus(){
    $("document").ready(function(){
		$("#status").show();
		$("#image").hide();
		isImgUpload=false;
	})
}

function more_comment_cont(comment_id){
    $("#more_comment-"+comment_id).hide();
    $("#more_comment_cont-"+comment_id).show();
}

function less_comment_cont(comment_id){
	$("#more_comment_cont-"+comment_id).hide();
	$("#more_comment-"+comment_id).show();
}

function more_descr(status_id){
    $("#realmoredescr_"+status_id).show();
    $("#more_descr_"+status_id).hide();
}

function less_descr(status_id){
    $("#realmoredescr_"+status_id).hide();
    $("#more_descr_"+status_id).show();
}

function deleteImage(imgid){
    apprise('{-$galleries_delete_image}',{verify: true},function(c){if(c){
            var data = '{"action":"deleteImage", "imgid": "'+imgid+'"}';
            $.post( "controllers/ajaxGalleriesController.php", {json_data: data}, function(data_back){
                if(data_back.status==1){
                    $("#sort_"+imgid)
                        .fadeOut()
                        .remove();
                    if($(".gal_thumb").length==0)
                    	$("#gallery").html('{-$NO_IMAGE_ERROR}');

                    $("#jGalleryImageContainer").hide();
                    $("#jGalleryImageInfo").css("right",-300);
                    $("#jGalleryTitleBox").hide();
        			$("#likeDiv").dialog("close");
        			$("#jGalleryImg").remove();
        			$("body").css("overflow","auto");
                }
            },"json");
        }
    });
}

function setCover(imgid){
    var data = '{"action":"setCover", "imgid": "'+imgid+'"}';
    $.post( "controllers/ajaxGalleriesController.php", {json_data: data}, function(data_back){
    	if(data_back.status==1){
            $(".set_cover_"+imgid).remove();
            $("#gallery_dropdown").slideUp(100);
            apprise("{-$galleries_set_as_cover}");
        }
    },"json");
}

var scrollApi;

function imageComment(id,cid){
	$("#send_comment").children("button").button("disable");
	$("#send_comment_load").show();
	var message = $("#comment_area").val();
	$("#comment_area").attr('disabled', 'disabled');
	var dataValues = '{"action": "addStatusComment","id":"'+ id + '", "message": "' + message + '","cid":'+cid+'}';
	$.post("controllers/ajaxGalleriesController.php", {json_data : dataValues},function(data){
		if(data.status ==1){
			$("#comments_box").append(data.comments);
			$("#send_comment_load").hide();
			$("#comment_area").val("").removeAttr('disabled');
			scrollApi.reinitialise();
		}
	}, "json");
}

function likeImage(img_id,cid){
	var dataValues = '{"action":"like","id":'+img_id+',"cid":'+cid+'}';
	$.post("controllers/ajaxGalleriesController.php", {json_data : dataValues},function(data){
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
			$("#imgLike").hide().next().show();
			$("#commentPane").height($(window).height()-$("#mainImgInfo").height()-20);
		}
	},"json");
}

function dislikeImage(img_id,cid){
	var dataValues = '{"action":"dislike","id":'+img_id+',"cid":'+cid+'}';
	$.post("controllers/ajaxGalleriesController.php",{json_data : dataValues},function(data){
	 	if(data.status==1){
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
	        $("#imgDislike").hide().prev().show();
	        $("#commentPane").height($(window).height()-$("#mainImgInfo").height()-20);
		}
	}, "json");
}

function deleteImageComment(id,cid){
	apprise('{-$pinboard_delete_comment}', {verify: true}, function(r){if(r){
	        var dataValues = '{"action": "deleteComment","id":"'+ id+ '","cid":'+cid+'}';
	    	$.post("controllers/ajaxGalleriesController.php", {json_data : dataValues},function(data){
	            if(data.status == 1){
	            	$("#comment-"+id).remove();
	            	scrollApi.reinitialise();
	            }
	      	}, "json");
	    }
	});
}

function addTitle(id){
    apprise('{-$galleries_edit_title}', {input: true}, function(r){
        if(r!==false){
            var dataValues = '{"action": "editTitle","id":"'+ id + '", "title": "' + r + '"}';
        	$.post("controllers/ajaxGalleriesController.php", {json_data : dataValues},
        	function(data){
           	 	if(data.status ==1)
        			$("#jGalleryTitle").html(r);
        	}, "json");
        }
    });
}

function showImageLikes(id, type){
	var dataValues = '{"cid":{-$CUNITYID},"action": "getLikes","id": "'+id+'","type":"'+type+'"}';
	$.post("controllers/ajaxGalleriesController.php", {json_data : dataValues},
		function(data_back){
		if(data_back.status==1)
			$("#likeDiv").html(data_back.persons).dialog('option','title', data_back.title).dialog('open');
	}, "json");
}