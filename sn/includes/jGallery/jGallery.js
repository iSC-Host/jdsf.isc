(function( $ ) {
	var cunityId=0,nextimg=0,prevImg=0;	
	var imgContainer = $("<div />",{id: "jGalleryImageContainer"}).appendTo("body").hide();
	var imgTitleBox = $("<div />",{id: "jGalleryTitleBox"}).appendTo(imgContainer).hide();
	var imgNext = $("<div />",{id: "jGalleryNext"}).html("next >").appendTo(imgTitleBox);
	var imgTitle = $("<div />",{id: "jGalleryTitle"}).appendTo(imgTitleBox)
	var imgPrev = $("<div />",{id: "jGalleryPrev"}).html("< previous").appendTo(imgTitleBox).click(function(){previousImage();});
	var imgInfoBox = $("<div />",{id: "jGalleryImageInfo"}).appendTo(imgContainer);
	var imgLoader = $("<div />",{id: "jGalleryLoader"}).appendTo(imgContainer);
	var clr = $("<div />").addClass("clear").appendTo(imgContainer);
	var image=null;
	
	imgContainer.on('click',function(e){
		if(e.target==this)			
			hide();					
	})
	
	imgPrev.on('click',function(){
		previousImage();
	})
	
	imgNext.on('click',function(){
		nextImage();
	})
	
	$(document.documentElement).keyup(function (event) {
	  if (event.keyCode == 37)
		  previousImage();
	  else if (event.keyCode == 39)
		  nextImage();
	  else if (event.keyCode == 27)
		  hide();		  
	});
	
	function previousImage(){
		if(prevImg==null) return;
		imgTitleBox.hide();
		$("#likeDiv").dialog("close");
		$(".jGalleryImages").remove();	
		loadImageContainer(prevImg,true,cunityId);
	}
	
	function nextImage(){
		if(nextImg==null) return;
		imgTitleBox.hide();
		$("#likeDiv").dialog("close");
		$(".jGalleryImages").remove();
		loadImageContainer(nextImg,true,cunityId);
	}
	
	function hide(){			
		imgContainer.hide();
		imgInfoBox.hide();
		imgTitleBox.hide();
		$("#likeDiv").dialog("close");			
		$(".jGalleryImages").remove();
		$("body").css("overflow","auto");
	}
				
	function loadImageContainer(id,switchImage,cid){
		cunityId = cid;
		if(!switchImage)
			imgContainer.show();
		$("body").css("overflow","hidden");
		var spinner = new Spinner({lines: 13,length: 15,width: 5,radius: 25,rotate: 50,color: '#fff',speed: 1.1,trail: 72,shadow: true,className: 'spinner',zIndex: 2e9,top: 'auto',left: 'auto'}).spin(document.getElementById('jGalleryLoader'));			
		var dataValues = '{"action": "loadImageContainer","id":"'+ id + '","cid":' + cunityId + '}';
		$.post("controllers/ajaxGalleriesController.php", {json_data : dataValues},function(data){
			if(data.status ==1){
				image = $('<img />',{id: "jGalleryImg",src: data.img,css:{display:"none"}}).addClass("jGalleryImages").prependTo(imgContainer);					
				image.load(function(){						
					nextImg=data.nextId;
					prevImg=data.prevId;
					image.css({maxWidth:($(window).outerWidth()-400),maxHeight:($(window).height()-100)});
					image.css({"marginTop":($(window).outerHeight()-image.outerHeight())/2,"marginLeft":($(document).width()-300-image.width())/2,"marginRight":($(document).width()-300-image.width())/2});						
					imgTitleBox.width(image.width()).css({"marginBottom":(($(window).outerHeight()-image.outerHeight())/2)-imgTitle.outerHeight(),"marginLeft":image.css("marginLeft"),"marginRight":image.css("marginRight")});
					imgTitle.html(data.title).width(image.width()-160);
					imgInfoBox.html(data.template);						
					if($("#likeCount").html()==0)
						$("#image_likes").hide();
					if($("#dislikeCount").html()==0)
						$("#image_dislikes").hide();
					if($("#dislikeCount").html()==0&&$("#likeCount").html()==0)
						$("#like_container").hide();
					$("#comment_area").Watermark($("#comment_area").val());						
					$("#commentPane").height($(window).height()-$("#mainImgInfo").height()-20);
					refreshButtons();		
					imgLoadCheck();
					var scrollPane = $("#commentPane");
					var scrollContent = $("#comment_abs");
					var scroll = scrollPane.jScrollPane({
						contentWidth:280,
						hideFocus:true
					});
					scrollApi=scroll.data('jsp');
					scrollApi.scrollToPercentY(100);
					$("#commentPane .jspVerticalBar").hide();
					$("#imgLikeInfo").live({
						mouseenter:function(){
							$("#commentPane .jspVerticalBar").fadeIn();
						},
						mouseleave:function(){
							$("#commentPane .jspVerticalBar").fadeOut();
						}
					})
					spinner.stop();
					image.show();
					imgTitleBox.fadeIn(1000);
					imgInfoBox.show().children().show();
					return true;
				})
			}
		},"json");
		return false;
	}
				
	$.fn.jGallery = function() {
		$(this).each(function(){
			$(this).addClass("jGalleryLink");
		})
		$(this).on({
			click: function(){				
				return loadImageContainer($(this).attr("id"),false,$(this).attr("cid"));
			}
		})
	};
})( jQuery );
