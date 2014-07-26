function showMore(id){
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
    $("#friendbutton-{-$ID}").click(function(){
        if($(this).val()=="nofriends")
            addasfriend('{-$ID}','{-$USERDATA}',{-$REMOTE},reload);
        else if($(this).val()=="receivedrequest")
            respondRequest('{-$ID}','{-$USERDATA}',{-$REMOTE},reload);
    })
    .next().click(function() {
	    if($("#friends_dropdown").is(':visible'))
		   $("#friends_dropdown").slideUp(100);
		else
		   $("#friends_dropdown").slideDown(100);
	})
    
    {-if $friendstatus=='friends'}
        $("#deletefriend").show();
    {-else if $friendstatus=='sentrequest'||$friendstatus=='receivedrequest'}
        $("#removerequest").show();
    {-/if}
    
    $(".user_sample_photos:not(.jGalleryLink)").jGallery();
});