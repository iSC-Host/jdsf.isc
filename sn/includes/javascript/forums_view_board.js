var glname, gldescription;

function editBoard(name, description, catid)
{
    glname = name;
    gldescription = description;
    $("#name").live('keyup',function(){
        glname = $("#name").val();
    })

    $("#description").live('keyup',function(){
        gldescription = $("#description").val();
    })
        var content = '<h1>{-$view_board_edit}</h1><div class="aInput" style="text-align: left;"><label for="name">{-$view_board_name}:</label><br /><input id="name" type="text" class="aTextbox" value="'+name+'" /><br /><label for="description">{-$view_board_description}:</label><br /><input type="text" class="aTextbox" id="description" value="'+description+'"/>'
    apprise(content, {confirm: true}, function(r){
        if(r)
        {
            if(glname == "")
                editBoard(name, description, catid);
            else
                location.href='forums.php?edit_board='+catid+'&name='+glname+'&description='+gldescription;
        }
    });
}

function newBoard(type, catid)
{
    glname = "";
    gldescription = "";
    $("#name").live('keyup',function(){
        glname = $("#name").val();
    })

    $("#description").live('keyup',function(){
        gldescription = $("#description").val();
    })
    if(type == 'forum')
    {
        var content = '<h1>{-$view_board_add_new_forum}</h1><div class="aInput" style="text-align: left;"><label for="name">{-$view_board_name}:</label><br /><input id="name" type="text" class="aTextbox"/><br /><label for="description">{-$view_board_description}:</label><br /><input type="text" class="aTextbox" id="description"/>'
    }
    else
    {
        var content = '<h1>{-$view_board_add_new_board}</h1><div class="aInput" style="text-align: left;"><label for="name">{-$view_board_name}:</label><br /><input id="name" type="text" class="aTextbox"/><br /><label for="description">{-$view_board_description}:</label><br /><input type="text" class="aTextbox" id="description"/>'
    }
    apprise(content, {confirm: true}, function(r){
        if(r)
        {
            if(glname == "")
                newBoard(type, catid);
            else
            {
                if(type == 'forum')
                {
                    location.href='forums.php?add_new_forum&forum_name='+glname+'&forum_description='+gldescription;
                }
                else
                {
                    location.href='forums.php?add_board='+catid+'&name='+glname+'&description='+gldescription;
                }
            }
        }
    });
}

function deleteBoard(catid)
{
    var content = '<h1>{-$view_board_delete}</h1><small style="font-weight: bold;">{-$view_board_delete_info}</small><div class="aInput"><input type="password" class="aTextbox" id="password"/></div>';
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