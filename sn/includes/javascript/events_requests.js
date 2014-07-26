	$.ajaxSetup ({
		cache: false
	});
	
	window.setTimeout('refreshRequests()', 200);

	$("document").ready(function(){
	   $(".ui-menu-item")
           .live('mouseover', function(){
               $(this).children('a').addClass('ui-state-hover'); 
           })
           .live('mouseout', function(){
               $(this).children('a').removeClass('ui-state-hover');
           })

	   $(".respond_event_request").live('click', function(){
            var el = this;
            var event_id = $(el).attr('id');
            var attending = $(el).attr('attending');
            var data = '{"action":"respond_request", "event_id":"'+event_id+'","rsvp":"'+attending+'"}';
            $.post('controllers/ajaxEventsController.php', {json_data:data},function(data_back) {
                if(data_back.status==1){
                    location.href='events.php?e='+event_id;
                }else if(data_back.status==2){
                    refreshRequests();
                }
            },"json")
        });
    });
    
function refreshRequests()
{
    var data = '{"action":"myRequests"}';
	$.post("controllers/ajaxEventsController.php", {json_data:data},
		function (data_back) {
			$("#Requests").html(data_back.messages);
		}, "json");
}