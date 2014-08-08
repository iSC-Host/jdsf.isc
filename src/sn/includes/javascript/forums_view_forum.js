function deleteThread(catid)
{
    apprise('{-$view_forum_delete}', {confirm: true}, function(r){
        if(r)
        {
            location.href='forums.php?delete_thread='+catid;
        }
    });
}

function deleteThread(catid)
{    
    var content = '<h1>{-$view_forum_delete}</h1><small style="font-weight: bold;">{-$view_forum_delete_info}</small><div class="aInput"><input type="password" class="aTextbox" id="password"/></div>';
    apprise(content, {confirm: true}, function(r){
        if(r!='')
        {
            $.post('controllers/ajaxForumsController.php?c=checkpass&p='+r,function(data){
            if(data.status == 1)
                location.href='forums.php?delete_thread='+catid;
            else
                apprise(data.error);                    
            },"json");
        }
    });
}