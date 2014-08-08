$("#relationship_select").live('change', function(){
    switch($(this).val())
    {
        case '1':
            $("#rel_part").html('&nbsp;{-$profile_view_with} <input type="hidden" name="relationship_partner" id="relationship_partner"/><input type="text" id="partner" name="partner"><div id="search_result" style="display: none;"></div>');
        break;
        
        case '2':
            $("#rel_part").html("");
        break;

		case '3':
            $("#rel_part").html('&nbsp;{-$profile_view_with} <input type="hidden" name="relationship_partner" id="relationship_partner"/><input type="text" id="partner" name="partner"><div id="search_result" style="display: none;"></div>');
        break;

		case '4':
            $("#rel_part").html('&nbsp;{-$profile_view_with} <input type="hidden" name="relationship_partner" id="relationship_partner"/><input type="text" id="partner" name="partner"><div id="search_result" style="display: none;"></div>');
        break;
    }
})

var clicked;

$("#partner").live('click', function(){
    $(this).removeAttr('disabled');
})

$("#partner").live('blur', function(){
    $(this).attr('disabled', 'disabled');
})


$("#partner").live('keyup',function(){
    var data = '{"action":"getFriendList", "searchTerm": "'+ $("#partner").val() +'"}';
	$.post("controllers/ajaxInboxController.php", {
		json_data : data
	}, function(obj) {
		if(obj.status == '1' && obj.membersFound != null)
		{
            $("#search_result")
                .show()
                .html(obj.membersFound);
        }
        else
        {
            $("#search_result")
                .html("")
                .hide()
        }
	}, "json")
})

$(".result_line").live('mousedown',function(){
    var id = $(this).attr('id');
    var name = $(this).attr('title');

    $("#search_result").hide();
    $("#search_result").html("");
    $("#partner")
        .val(name)
        .attr('disabled', 'disabled');
    $("#relationship_partner").val(id);

})

function deleteRequest(rid)
{
    apprise('{-$profile_edit_confirm_delete_request}', {confirm: true}, function(r){
        if(r)
        {
            var r = rid;
            var data = '{"action":"deleteRequest", "relationship_id": "'+ rid +'"}';
            $.post("controllers/ajaxProfileController.php", {
        		json_data : data
        	}, function(data_back) {
        	   if(data_back.status == '1')
        	   {
                    $("#request_"+r)
        	            .fadeOut(600, function(){
                         $(this).remove();
                         $("#relationship_wrap").fadeIn();
                     })
               }
        	}, "json");
        }
    })    
}