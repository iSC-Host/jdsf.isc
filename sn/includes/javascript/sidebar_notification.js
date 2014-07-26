function refreshNotifications()
{
    var dataValues = '{"action": "loadNotifications"}';
    $.post("controllers/ajaxNotificationController.php", {json_data: dataValues},
    function(data) {
        if (data.status > 0)
        {
            $("#notification_status").attr('src', 'style/{-$STYLE}/img/new_notifications.png');
            if (data.status == 1)
                $("#more_notifications").hide();
            else
                $("#more_notifications").show();
        }
        else
        {
            $("#notification_status").attr('src', 'style/{-$STYLE}/img/notifications_empty.png');
            $("#more_notifications").hide();
        }
        $("#notification_count").html(data.status);
        $("#not_cont").html(data.notifications);
        /*var c = data.newest.length;
         for(var i = 0; i < c; i++)
         {
         $.sticky(data.newest[i]);            
         }         
         */	}, "json");
}

function NotificationRead(id)
{
    var dataValues = '{"action": "readNotification","id": "' + id + '"}';
    $.post("controllers/ajaxNotificationController.php", {json_data: dataValues}, function() {
    }, "json");
    return true;
}

$("document").ready(function() {
    $("#notification_headline").click(function() {
        $("#notifications_list").toggle("fast");
    });

    $(".notification_unread").live('mouseenter', function() {
        var el = this;
        var dataValues = '{"action": "readNotification","id": "' + $(this).attr('id') + '"}';
        $.post("controllers/ajaxNotificationController.php", {json_data: dataValues}, function() {
            $(el)
                    .removeClass('notification_unread')
                    .addClass('notification');

        }, "json");
    })

    refreshNotifications();

    window.setInterval('refreshNotifications()', 30000);

})