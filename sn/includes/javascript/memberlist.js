$("document").ready(function(){
    $(".photo_available")
        .live('mouseover', function(){
            var el = this;
            $("#preview_"+$(el).attr('id')).show();
        })
        .live('mouseout', function(){
            var el = this;
            $("#preview_"+$(el).attr('id')).hide();
        });
});