<?php /* Smarty version 2.6.26, created on 2014-03-11 21:23:38
         compiled from file:style/newcunity/templates/profile_view.tpl.html */ ?>
<script language="javascript" type="text/javascript">function showMore(id){
    $("#" + id + "_content").hide();
    $("#" + id + "_more").show();
}

function showLess(id){
    $("#" + id + "_more").hide();
    $("#" + id + "_content").show();    
}

function reload(){
	location.reload();
}

$("document").ready(function(){
    $("#friendbutton-<?php echo $this->_tpl_vars['ID']; ?>
").click(function(){
        if($(this).val()=="nofriends")
            addasfriend('<?php echo $this->_tpl_vars['ID']; ?>
','<?php echo $this->_tpl_vars['USERDATA']; ?>
',<?php echo $this->_tpl_vars['REMOTE']; ?>
,reload);
        else if($(this).val()=="receivedrequest")
            respondRequest('<?php echo $this->_tpl_vars['ID']; ?>
','<?php echo $this->_tpl_vars['USERDATA']; ?>
',<?php echo $this->_tpl_vars['REMOTE']; ?>
,reload);
    })
    .next().click(function() {
	    if($("#friends_dropdown").is(':visible'))
		   $("#friends_dropdown").slideUp(100);
		else
		   $("#friends_dropdown").slideDown(100);
	})
    
    <?php if ($this->_tpl_vars['friendstatus'] == 'friends'): ?>
        $("#deletefriend").show();
    <?php else: ?>
        $("#removerequest").show();
    <?php endif; ?>
    
    $(".user_sample_photos:not(.jGalleryLink)").jGallery();
});</script>
<script language="javascript" type="text/javascript">var share=0;
var isImgUpload = false;
var link = "";
var stop_refresh;
var sort = "all";

$(document).ready(function(){
    <?php if (PINBOARD): ?>
    $(document).scroll(function(){
        if($(window).scrollTop() >= ($(document).height()-$(window).height()))
            loadMoreStatus();
    })
    <?php endif; ?>

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
        .Watermark("<?php echo $this->_tpl_vars['pinboard_status_watermark']; ?>
")
        .live('mouseenter',function(){
            refreshPinboard();
        })
    $("#watermark_image")
        .Watermark("<?php echo $this->_tpl_vars['pinboard_image_watermark']; ?>
");

    //create likesdialog
    $("<div />",{id: "likeDiv"}).appendTo("body").dialog({autoOpen: false,buttons:{"<?php echo $this->_tpl_vars['galleries_close']; ?>
":function(){$(this).dialog("close");}}});

    $(".showLikes").each(function(){
    	$(this).toolTip({
	    	text: "<?php echo $this->_tpl_vars['pinboard_info_show_likes']; ?>
",
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
    		text: "<?php echo $this->_tpl_vars['pinboard_info_show_comments']; ?>
",
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
        if(str.length > 0 && str != " " && str != "<?php echo $this->_tpl_vars['pinboard_status_watermark']; ?>
"){
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
        var dataValues = '{"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"p":<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
,"r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","action": "loadComments","id":"'+ id[1] + '"}';
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
    var dataValues = '{"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"p":<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
,"r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","action": "loadPinboard","do": "refresh","s":"'+sort+'"}';
	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},
		function(data){
		if(data.status==1&&data.count>0){
			$("#pinBoard").prepend(data.pinBoardRows);
			if(data.option=="off")
			    $("#submitLoadMoreStatus").hide();
			else
			    $("#submitLoadMoreStatus").show();
			$(".new_comment_area").Watermark("<?php echo $this->_tpl_vars['pinboard_comment_watermark']; ?>
");
			refreshButtons();
			$(".pinboard_image_link:not(.jGalleryLink)").jGallery();
		}

	}, "json");
}

function deleteStatus(id){
    apprise('<?php echo $this->_tpl_vars['pinboard_delete_status']; ?>
', {'verify':true},function(r){
        if(r){
            var dataValues = '{"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"p":<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
,"r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","action": "deleteStatus","id":"'+ id+ '"}';
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
	var dataValues = '{"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"p":<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
,"r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","action": "loadPinboard", "id": "<?php echo $this->_tpl_vars['STATUS_ID']; ?>
","s":"'+type+'"}';
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
            $(".new_comment_area").Watermark("<?php echo $this->_tpl_vars['pinboard_comment_watermark']; ?>
");
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
    var dataValues = '{"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"p":<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
,"r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","action": "addStatusComment","sType":"'+status_type+'","rid":'+ressource_id+',"sid":'+ status_id + ', "message": "' + message + '"}';
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
    $("#watermark_image").val("").Watermark("<?php echo $this->_tpl_vars['pinboard_image_watermark']; ?>
");
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
	}else if(myStatusField !="<?php echo $this->_tpl_vars['pinboard_status_watermark']; ?>
" && myStatusField !="" && myStatusField != " "){
        var dataValues = '{"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"p":<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
,"r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","action": "insertPinboardStatus","statusMessage":"'+ encodeURIComponent(myStatusField)+ '","link":"'+encodeURIComponent(link)+'"}';
		$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},function(data){
			if(data.status ==1){
		        link = "";
		        $("#video").html("").hide();
		        $("#image").hide();
		        $("#url").hide();
		        $("#status").show();
                $("#watermark").val("").Watermark("<?php echo $this->_tpl_vars['pinboard_status_watermark']; ?>
");
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
    var dataValues = '{"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"p":<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
,"r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","action":"like","sid":'+status_id+',"rid":'+ressource_id+',"sType":"'+status_type+'"}';
	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},function(data){
		if(data.status ==1){
			$("#likes_"+status_id).html(data.likes);
			$("#like_"+status_id).hide().next().show();
			$("#buttonlist-"+status_id).buttonset('refresh');
		}
	},"json");
}

function dislike(status_id,ressource_id,status_type){
    var dataValues = '{"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"p":<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
,"r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","action":"dislike","sid":'+status_id+',"rid":'+ressource_id+',"sType":"'+status_type+'"}';
	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},function(data){
	 	if(data.status ==1){
            $("#likes_"+status_id).html(data.likes);
            $("#dislike_"+status_id).hide().prev().show();
            $("#buttonlist-"+status_id).buttonset('refresh');
		}
	}, "json");
}

function deleteComment(id){
    apprise('<?php echo $this->_tpl_vars['pinboard_delete_comment']; ?>
', {verify: true}, function(r){if(r){
            var dataValues = '{"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"p":<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
,"r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","action": "deleteComment","id":"'+ id+ '"}';
        	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},function(data){
                    if(data.status == 1) $("#comment-"+id).remove();
          	},"json");
        }
    });
}

function loadMoreStatus(){
    $("#moreStatusLoad").show();
    $("#submitLoadMoreStatus").hide();
	var dataValues = '{"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"p":<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
,"r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","action": "loadPinboard","type":"loadMoreStatus","s":"' + sort + '"}';
	$.post("controllers/ajaxPinboardController.php", {json_data : dataValues},function(data){
		if(data.status ==1){
			$("#pinBoard").append(data.pinBoardRows);
            $("#moreStatusLoad").hide();
            $("#submitLoadMoreStatus").show();
			if(data.option=="off")
			    $("#submitLoadMoreStatus").hide();
			else
			    $("#submitLoadMoreStatus").show();
            $(".new_comment_area").Watermark("<?php echo $this->_tpl_vars['pinboard_comment_watermark']; ?>
");
            refreshButtons();
            $(".pinboard_image_link:not(.jGalleryLink)").jGallery();
		}
	}, "json");
}

function showLikes(id, type,statusType){
    var dataValues = '{"userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","userData":"<?php echo $this->_tpl_vars['USERDATA']; ?>
","cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"p":<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
,"r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","action": "getLikes","id": "'+id+'","type":"'+type+'","sType":"'+statusType+'"}';
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
    apprise('<?php echo $this->_tpl_vars['galleries_delete_image']; ?>
',{verify: true},function(c){if(c){
            var data = '{"action":"deleteImage", "imgid": "'+imgid+'"}';
            $.post( "controllers/ajaxGalleriesController.php", {json_data: data}, function(data_back){
                if(data_back.status==1){
                    $("#sort_"+imgid)
                        .fadeOut()
                        .remove();
                    if($(".gal_thumb").length==0)
                    	$("#gallery").html('<?php echo $this->_tpl_vars['NO_IMAGE_ERROR']; ?>
');

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
            apprise("<?php echo $this->_tpl_vars['galleries_set_as_cover']; ?>
");
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
	apprise('<?php echo $this->_tpl_vars['pinboard_delete_comment']; ?>
', {verify: true}, function(r){if(r){
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
    apprise('<?php echo $this->_tpl_vars['galleries_edit_title']; ?>
', {input: true}, function(r){
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
	var dataValues = '{"cid":<?php echo $this->_tpl_vars['CUNITYID']; ?>
,"action": "getLikes","id": "'+id+'","type":"'+type+'"}';
	$.post("controllers/ajaxGalleriesController.php", {json_data : dataValues},
		function(data_back){
		if(data_back.status==1)
			$("#likeDiv").html(data_back.persons).dialog('option','title', data_back.title).dialog('open');
	}, "json");
}</script>
<script type="text/javascript" src="includes/jGallery/jGallery.js"></script>
<script language="javascript" type="text/javascript">
$("document").ready(function(){
    <?php if ($this->_tpl_vars['FRIENDS'] != ""): ?>
    /*var data = '{"c":"myFriends","userid": "<?php echo $this->_tpl_vars['FULL_ID']; ?>
","target":"sidebar"}';
	$.post("controllers/ajaxFriendsController.php", {json_data:data},
		function (data_back) {
            $("#friends_sidebar").html('<a href="friends.php?user=<?php echo $this->_tpl_vars['FULL_ID']; ?>
"><?php echo $this->_tpl_vars['NAME']; ?>
<?php echo $this->_tpl_vars['profile_view_friends_of']; ?>
('+data_back.count+')</a><br />');

			$("#friends_sidebar").append(data_back.messages);
		}, "json");*/
	<?php endif; ?>

    <?php if ($this->_tpl_vars['PINBOARD_VIEW'] == 1): ?>
    $("#watermark").keyup(function(){
        var el = this;
        var str = new String($(el).val());
        if(str.length > 0 && str != " " && str != "<?php echo $this->_tpl_vars['pinboard_status_watermark_profile']; ?>
"){
            $("#share_button_wrap").slideDown();
        }else
            $("#share_button_wrap").slideUp();
    })
    <?php endif; ?>
})

</script>
<!-- Main Blue bar Start -->
<div class="bar">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1><?php echo $this->_tpl_vars['NAME']; ?>
</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<!-- Main Blue bar End -->
<div class="clear"></div>
<!-- Profile Box Start -->
<div class="profile">	
	<div style="text-align: right; position: relative; margin-bottom: 10px;">
        <?php if (OWN): ?>
        <button class="jui-button" icon="ui-icon-gear" onclick="location.href='profile.php?c=edit&do=general'"><?php echo $this->_tpl_vars['profile_view_profile_edit']; ?>
</button>
        <?php endif; ?>
        <?php if (SEND_MESSAGE_BOOL): ?>
        <button onclick="newMessage('<?php echo $this->_tpl_vars['NAME']; ?>
',<?php echo $this->_tpl_vars['ID']; ?>
,'<?php echo $this->_tpl_vars['FULL_ID']; ?>
',<?php echo $this->_tpl_vars['CUNITYID']; ?>
);" class="jui-button" icon="ui-icon-mail-closed"><?php echo $this->_tpl_vars['profile_view_send_message']; ?>
</button>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['CHAT_BOOL']): ?>
        <button class="jui-button" icon="ui-icon-comment" onclick="chatWith('<?php echo $this->_tpl_vars['CUNITYID']; ?>
-<?php echo $this->_tpl_vars['ID']; ?>
','<?php echo $this->_tpl_vars['NAME']; ?>
');"><?php echo $this->_tpl_vars['profile_view_chat']; ?>
</button>
        <?php endif; ?>
        <?php if (! OWN): ?>
        <div style="display: inline;" class="buttonset"><button class="jui-button" icon="<?php echo $this->_tpl_vars['addFriendIcon']; ?>
" id="friendbutton-<?php echo $this->_tpl_vars['ID']; ?>
" value="<?php echo $this->_tpl_vars['friendstatus']; ?>
"><?php echo $this->_tpl_vars['addFriendText']; ?>
</button><button class="jui-button" icon="ui-icon-triangle-1-s" text="0"><?php echo $this->_tpl_vars['profile_view_friends_options']; ?>
</button></div>
        <ul id="friends_dropdown" class="ui-widget ui-widget-content ui-corner-all ui-menu" style="display: none; width: 160px; position: absolute; right: 4px; top: 100%; text-align: left;">
            <?php if ($this->_tpl_vars['friendstatus'] == 'friends'): ?>
            <li class="ui-menu-item" id="deletefriend">
                <a class="ui-corner-all" href="javascript: deleteFriend(<?php echo $this->_tpl_vars['ID']; ?>
,'<?php echo $this->_tpl_vars['USERDATA']; ?>
',<?php echo $this->_tpl_vars['REMOTE']; ?>
,reload);"><?php echo $this->_tpl_vars['friends_delete_friend']; ?>
</a>
            </li>             
            <?php endif; ?>
            <?php if ($this->_tpl_vars['friendstatus'] == 'sentrequest' || $this->_tpl_vars['friendstatus'] == 'receivedrequest'): ?>
            <li class="ui-menu-item">
                <a class="ui-corner-all" href="javascript: removeRequest(<?php echo $this->_tpl_vars['ID']; ?>
,'<?php echo $this->_tpl_vars['USERDATA']; ?>
',<?php echo $this->_tpl_vars['REMOTE']; ?>
,reload);"><?php echo $this->_tpl_vars['friends_remove_request']; ?>
</a>
            </li>
            <?php endif; ?>
            <li class="ui-menu-item">
                <a class="ui-corner-all" href="javascript: blockFriend(<?php echo $this->_tpl_vars['ID']; ?>
,'<?php echo $this->_tpl_vars['USERDATA']; ?>
',<?php echo $this->_tpl_vars['REMOTE']; ?>
,reload);"><?php echo $this->_tpl_vars['friends_block']; ?>
</a>
            </li>
            <!--
            <li class="ui-menu-item">
                <a class="ui-corner-all" href="javascript: "><?php echo $this->_tpl_vars['profile_view_report']; ?>
</a>
            </li>          -->
        </ul>
        <?php endif; ?>
    </div>
    
    <!-- Picture Box Start  -->
	<div class="profile-box">
		<div class="profile-box-pic">
		<img src="<?php echo $this->_tpl_vars['PROFILE_PIC']; ?>
" style="cursor: pointer;width:200px;height:auto"/>
		</div>

    <?php if ($this->_tpl_vars['SAMPLE_PHOTOS'] != "" && $this->_tpl_vars['PRIVACY'] == 1): ?>
	<div class="small_info_1" style="margin-top: 10px;"></div>
    <div class="small_info_2" style="width: 180px; height: 128px;" id="albums">
        <a href="galleries.php?user=<?php echo $this->_tpl_vars['FULL_ID']; ?>
" style="margin-left: 8px;"><?php echo $this->_tpl_vars['NAME']; ?>
<?php echo $this->_tpl_vars['profile_view_his_galleries']; ?>
</a>
        <div style="padding: 5px 8px;">
        <?php echo $this->_tpl_vars['SAMPLE_PHOTOS']; ?>

        </div>
    </div>
    <div class="small_info_3"></div>
    <?php endif; ?>
	</div>
	<!-- Picture Box End  -->

	<!-- Profile Info Start -->
	<div class="profile-info">     
    <div class="main_info_1"></div>
    <div class="main_info_2">
        <div class="info_line">
            <div class="info_label"><?php echo $this->_tpl_vars['profile_view_nickname']; ?>
:</div><span class="info_value"><?php echo $this->_tpl_vars['NICK']; ?>
</span>
        </div>
        <div class="info_line">
            <div class="info_label"><?php echo $this->_tpl_vars['profile_view_name']; ?>
:</div><span class="info_value"><?php echo $this->_tpl_vars['FULL_NAME']; ?>
</span>
        </div>
        <div class="info_line">
            <div class="info_label"><?php echo $this->_tpl_vars['profile_view_birthday']; ?>
:</div><span class="info_value"><?php echo $this->_tpl_vars['BIRTH']; ?>
</span>
        </div>
        <div class="info_line">
            <div class="info_label"><?php echo $this->_tpl_vars['profile_view_registered']; ?>
:</div><span class="info_value"><?php echo $this->_tpl_vars['REGISTERED']; ?>
</span>
        </div>
        <div class="info_line">
            <div class="info_label"><?php echo $this->_tpl_vars['profile_view_updated']; ?>
:</div><span class="info_value"><?php echo $this->_tpl_vars['UPDATED']; ?>
</span>
        </div>
        <?php if (RELATIONSHIP): ?>
        <div class="info_line">
            <div class="info_label"><?php echo $this->_tpl_vars['profile_view_relationship']; ?>
:</div><span class="info_value" id="relationship"><?php echo $this->_tpl_vars['RELATIONSHIP']; ?>
</span>
        </div>
        <?php endif; ?>
        <?php if (INTERESTED): ?>
        <div class="info_line">
            <div class="info_label"><?php echo $this->_tpl_vars['profile_view_interested']; ?>
:</div><span class="info_value" id="interested"><?php echo $this->_tpl_vars['INTERESTED']; ?>
</span>
        </div>
        <?php endif; ?>
        <?php if (ABOUT): ?>
        <div class="info_line">
            <div class="info_label"><?php echo $this->_tpl_vars['profile_view_about']; ?>
:</div><div class="info_value" style="text-align: justify;"><?php echo $this->_tpl_vars['ABOUT']; ?>
</div>
        </div>
        <?php endif; ?>
    </div>
    <div class="main_info_3"></div>	
    
    <div class="main_info_1" style="margin-top: 10px;"></div>
    <div class="main_info_2">
        <div class="info_line">
            <div class="info_label"><?php echo $this->_tpl_vars['profile_view_email']; ?>
:</div><span class="info_value" id="mail"><?php echo $this->_tpl_vars['MAIL']; ?>
</span>
        </div>
        <?php echo $this->_tpl_vars['CONTACT']; ?>

    </div>
    <div class="main_info_3"></div>

    <?php if (EXTRA): ?>
    <div class="main_info_1" style="margin-top: 10px;"></div>
    <div class="main_info_2">
        <?php echo $this->_tpl_vars['ADDED']; ?>

    </div>
    <div class="main_info_3"></div>
    <?php endif; ?>    
	<!-- Grid Start -->

	</div>
	<!-- Profile Info End -->
    <div class="clear"></div>
</div>
<!-- Profile Box End -->


<!-- Box A Buttons box Start -->

<?php if ($this->_tpl_vars['PINBOARD_VIEW'] == 1): ?>
<div class="box-main-a">
	<div class="box-main-a-1"></div>
	<div class="box-main-a-2">
        <div id="video" style="float: left; border: 1px solid #ccc; border-radius: 5px; display: none"></div>
		<div id="video" style="float: left; border: 1px solid #ccc; border-radius: 5px; display: none"></div>
               <div id="image" style="float: left; border: 1px solid #ccc; border-radius: 5px; display: none">
				<textarea class="input" id="watermark_image" name="watermark" style="width: 494px; background-color: #FFFFFF; border: 0px; border-radius: 5px; height: 50px; padding: 3px;"></textarea>
				<div id="img_upload_container" style="border-top: 1px dashed #ccc;">
					<form action="controllers/ajaxPinboardController.php" target="imgUploadFrame" id="imgUploadForm" style="width: 490px; padding: 3px 5px;" enctype="multipart/form-data" method="post">
						<div id="imgUploadFormLeft" style="float: left; width: 250px">
						<label for="pinimg"><small>Select an image to upload!</small></label>
						<br/>
						<input type="file" name="pinimg" id="imgUploadInput" style="border: 0px; padding-left: 0px;"/>
						<input type="hidden" name="json_data" value='{"p":"<?php echo $this->_tpl_vars['PINBOARD_ID']; ?>
","r":"<?php echo $this->_tpl_vars['PINBOARD_RECEIVER']; ?>
","c":"imgUpload"}'/>
                           <input type="hidden" name="status_message" value="undefined" id="imgUploadMessage"/>							
						</div>
						<iframe name="imgUploadFrame" id="imgUploadFrame" style="display: none;"></iframe>							
						<small style="color: #aaa; display:block" class="clear">You can upload images in JPG,png,gif format!</small>
					</form>
				</div>                
               </div>
               <div id="url"></div>
               <div id="status" style="float: left;">
                   <textarea class="input" id="watermark" name="watermark" style="width: 493px; background-color: #FFFFFF; border: 1px solid #ccc; border-radius: 5px; height: 50px; padding: 3px;"></textarea>                    
               </div>
               <div style="float: left; padding: 2px; padding-left: 5px;">
               	<a href="javascript: showStatus();" id="showStatusDiv" style="display:block;width:22px;height:22px" title="<?php echo $this->_tpl_vars['pinboard_share_image']; ?>
"><img src="style/newcunity/img/newspaper.png" style="padding: 3px;"/></a>
                   <a href="javascript: shareImage();" id="showImageDiv" style="display:block;width:22px;height:22px" title="<?php echo $this->_tpl_vars['pinboard_share_image']; ?>
"><img src="style/newcunity/img/gallery.png" style="padding: 3px;"/></a>
                   <!-- <a href="javascript: shareUrl();" style="display:block" title="<?php echo $this->_tpl_vars['pinboard_share_url']; ?>
"><img src="style/newcunity/img/upload_file.png" style="padding: 3px;"/></a> -->
               </div>
               <div class="clear"></div>
    </div>
	<div class="box-main-a-3"></div>
<!-- Box A  Sub Start -->
<div id="share_button_wrap" style="display: none;">
<div class="box-main-a-sub-1">
	<div class="comment-share-btn" id="button">
	<img id="watermark_loader" src="style/newcunity/img/loading.gif" style="float: left; margin: 5px 10px; display: none;"/>
	<!-- <button><?php echo $this->_tpl_vars['pinboard_share_settings']; ?>
</button> -->
	<button id="share_button" class="jui-button" onclick="shareStatus();"><?php echo $this->_tpl_vars['pinboard_post']; ?>
</button>
	<div class="clear"></div>
	</div>
    <div class="clear"></div>
</div>
<div class="box-main-a-sub-2"></div>

<!-- Box A  Sub End -->
</div>
</div>
<!-- Box A Buttons box End -->
<div class="line-grey"></div>
 
<div id="wall">
</div><div id="pinBoard"></div>
<div id="divLoadMoreStatus" style="display:none;text-align:center">
    <img src="style/default/img/load_big.gif" id="moreStatusLoad" style="display: none; margin: 5px;"/>
    <button  onclick="loadMoreStatus();" class="jui-button" id="submitLoadMoreStatus" icon="ui-icon-plus" icon2="ui-icon-plus"><?php echo $this->_tpl_vars['pinboard_more']; ?>
</button>
</div>
<?php endif; ?>