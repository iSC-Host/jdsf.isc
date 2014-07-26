{-if $OWN_GALLERY}
    $("document").ready(function(){
        $("#gallery").sortable({
            placeholder: "ui-state-highlight",
            forcePlaceholderSize: true,
            tolerance: 'pointer',
            update: function(){
                $.get('controllers/ajaxGalleriesController.php?setPosition={-$ALBUM_ID}&'+$("#gallery").sortable('serialize'), function(data_back){
                	if(data_back.status==0)
                		apprise("error");
                },"json")
            }
        })
        .disableSelection();
    })
    
    function delAlbum(){
	   apprise('{-$galleries_del_album_confirm}',{verify: true},function(conf){
	        if(conf == true)        
	            location.href = 'galleries.php?c=delete_album&id={-$ALBUM_ID}';        
	    });
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

	
	function setCover(imgid){
	    var data = '{"action":"setCover","imgid":"'+imgid+'"}';
	    $.post( "controllers/ajaxGalleriesController.php", {json_data: data}, function(data_back){
	    	if(data_back.status==1){
	    		$(this).remove();
	    		$("#gallery_dropdown").slideUp(100);
	            apprise("{-$galleries_set_as_cover}");	            
	        }
	    },"json");
	}
{-/if}
var scrollApi
function morePhotos(albumid){
	var data = '{"action":"loadMorePhotos","id":"' + albumid + '","cid":{-$CUNITYID}}';
	$.post("controllers/ajaxGalleriesController.php", {json_data : data},
        function(data_back) {
        $("#morephotoslink").before(data_back.photos);
        if(data_back.morephotos==false)
        	$("#morephotoslink").hide();
        
		$("#gallery").sortable("refresh");
		$("#gallery").sortable("refreshPositions");
		
		$("a[rel^='imgDialog']:not(.jGalleryLink)").jGallery();
		
	}, "json");
}

$(document).ready(function(){
    $(document).scroll(function(){
        if($(window).scrollTop() >= ($(document).height()-$(window).height())){
            morePhotos('{-$ALBUM_ID}');
        }
    })
    
    $("<div />",{id: "likeDiv"}).appendTo("body").dialog({autoOpen: false,buttons:{"{-$galleries_close}":function(){$(this).dialog("close");}}});
    
	$("a[rel^='imgDialog']").jGallery();
	
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
    
    //event handler for drowndown-menu
    $(".edit_dropdown").live('click',function(){
    	var id = $(this).attr('id');
        if($("#gallery_dropdown").is(':visible'))
            $("#gallery_dropdown").slideUp(100);
        else
            $("#gallery_dropdown").slideDown(100);
    });
});

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
			scrollApi.scrollToPercentY(100);
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

function showImageLikes(id, type){
    var dataValues = '{"cid":{-$CUNITYID},"action": "getLikes","id": "'+id+'","type":"'+type+'"}';
	$.post("controllers/ajaxGalleriesController.php", {json_data : dataValues},
		function(data_back){
		if(data_back.status==1)
			$("#likeDiv").html(data_back.persons).dialog('option','title', data_back.title).dialog('open');		
	}, "json");
}

function more_comment_cont(comment_id)
{
    $("#more_comment-"+comment_id).hide();
    $("#more_comment_cont-"+comment_id).show();
}

function less_comment_cont(comment_id)
{
	$("#more_comment_cont-"+comment_id).hide();
	$("#more_comment-"+comment_id).show();    
}

function downloadImage(img_id){
	
}