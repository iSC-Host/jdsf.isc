$("document").ready(function(){
    $("#guestsDialog").dialog({
        autoOpen: false,
        buttons: {
            "{-$events_close}":function(){ $(this).dialog("close");}
            },
        minHeight: 400,
        minWidth: 600
    });
    
    $("#inviteDialog").dialog({
        autoOpen: false,
        buttons: {
            "{-$events_save}":function() {
                addGuests();
            },
            "{-$events_close}":function(){ $(this).dialog("close");}
            },
        minHeight: 400,
        minWidth: 600
    });

    $(".ui-menu-item")
       .live('mouseover', function(){
           $(this).children('a').addClass('ui-state-hover');
       })
       .live('mouseout', function(){
           $(this).children('a').removeClass('ui-state-hover');
       })
       .live('click', function(){
           $("#attending_dropdown").slideUp(100);
       })

    $(".guest_box").live('click', function(){
        $(this).toggleClass('guest_box_selected');
        $(this).after('<input type="hidden" value="'+$(this).attr('id')+'" id="inp_'+$(this).attr('id')+'"/>');
    })     
});

function atDrop(){
    if($("#attending_dropdown").is(':visible'))
	   $("#attending_dropdown").slideUp(100);
	else
	   $("#attending_dropdown").slideDown(100);
}

function delete_event(){
    apprise("{-$events_confirm_delete}", {verify: true}, function(r){
        if(r)
            location.href='events.php?e={-$EVENT_ID}&d=1';
    });             
}

function showGuests(status,event_id){
    var data = '{"action":"getGuestList", "event_id":"'+event_id+'","status":'+status+'}';
    $.post("controllers/ajaxEventsController.php", {json_data:data},function(data_back) {
        if(data_back.status==1&&data_back.count>0)
            $("#guestsDialog").html(data_back.list).dialog('option','title',data_back.title).dialog('open');
    },"json")
}

function inviteGuests(){
    $("#inviteDialog").dialog('open');
}

function addGuests(){
    var checks = new Array();
    $("#inviteDialog input").each(function(){
        checks.push($(this).val());
    });
    var dataValues = '{"action":"addGuests","event_id":"{-$EVENT_ID}","guests":"' + checks + '"}';
	$.post("controllers/ajaxEventsController.php", {json_data : dataValues},function(data){
        if(data.status==1)
            $("#inviteDialog").dialog('close');
	}, "json");
}

function respondInvitation(event_id, attending,newText){
    var data = '{"action":"respondRequest", "event_id":"'+event_id+'","status":"'+attending+'"}';
    $.post('controllers/ajaxEventsController.php', {json_data:data},function(data_back) {
        if(data_back.status==1)
            location.reload();
        else if(data_back.status==0)
            apprise(data_back.error);
    },"json");
}