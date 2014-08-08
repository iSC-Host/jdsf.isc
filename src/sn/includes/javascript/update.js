$("document").ready(function(){
    apprise('{-$update_msg}', {verify: true}, function(result){
        if(result)
        {
            location.href='admin/overview.php';
        }
        else
        {
            location.href='pinboard.php';
        }
    })
})