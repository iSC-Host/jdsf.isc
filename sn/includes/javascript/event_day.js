$("document").ready(function(){
    var data = '{"action":"getDayEvents","d":"{-$DAY_VAL}"}';
    $.post('controllers/ajaxEventsController.php', {json_data: data}, function(data_back){
        if(data_back.status==1)
            $("#events_list").html(data_back.events)
    }, "json");
})

function respondInvitation(event_id, attending, el){
    var data = '{"action":"respond_request", "event_id":"'+event_id+'","rsvp":"'+attending+'"}';
    $.post('controllers/ajaxEventsController.php', {json_data:data},function(data_back) {
        if(data_back.status==1)
            $("#event_"+event_id+" .attending_button").button( "option", "label", $(el).html() );
        else if(data_back.status==2)
            $("#event_"+event_id).fadeOut().remove();
        else if(data_back.status==0)
            apprise(data_back.error);
    },"json")
}
