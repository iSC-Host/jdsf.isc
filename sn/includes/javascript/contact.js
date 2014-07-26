function contact()
{
    $("document").ready(function() {
        var name = 'na';
        var mail = 'na';
        var msg = 'na';
        $("#contact_name").live('change', function() {
            name = $("#contact_name").val();

        });

        $("#contact_email").live('change', function() {
            mail = $("#contact_email").val();

        });

        $("#contact_message").live('change', function() {
            msg = $("#contact_message").val();

        });

        $.post('controllers/ajaxContactController.php?c=getForm',function(data){
                    apprise(data, {'verify': true, 'textYes': '{-$pages_contact_send}', 'textNo': '{-$pages_contact_cancel}'},
                    function(b)
                    {
                        if (b)
                        {
                            $.post(
                                    'controllers/ajaxContactController.php?c=sendContact&name=' + name + '&mail=' + mail + '&msg=' + msg,
                                    function(data)
                                    {
                                        apprise(data);
                                    }
                            );
                        }
                    }
                    );
                }
        );
    });
}