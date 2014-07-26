<?php /* Smarty version 2.6.26, created on 2014-03-12 13:09:38
         compiled from file:style/newcunity/templates/forums/view_board.tpl.html */ ?>
<!--
########################################################################################
## CUNITY(R) V1.0beta - An open source social network / "your private social network" ##
########################################################################################
##  Copyright (C) 2011 Smart In Media GmbH & Co. KG                                   ##
## CUNITY(R) is a registered trademark of Dr. Martin R. Weihrauch                     ##
##  http://www.cunity.net                                                             ##
##                                                                                    ##
########################################################################################

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or any later version.

1. YOU MUST NOT CHANGE THE LICENSE FOR THE SOFTWARE OR ANY PARTS HEREOF! IT MUST REMAIN AGPL.
2. YOU MUST NOT REMOVE THIS COPYRIGHT NOTES FROM ANY PARTS OF THIS SOFTWARE!
3. NOTE THAT THIS SOFTWARE CONTAINS THIRD-PARTY-SOLUTIONS THAT MAY EVENTUALLY NOT FALL UNDER (A)GPL!
4. PLEASE READ THE LICENSE OF THE CUNITY SOFTWARE CAREFULLY!

	You should have received a copy of the GNU Affero General Public License
    along with this program (under the folder LICENSE).
	If not, see <http://www.gnu.org/licenses/>.

   If your software can interact with users remotely through a computer network,
   you have to make sure that it provides a way for users to get its source.
   For example, if your program is a web application, its interface could display
   a "Source" link that leads users to an archive of the code. There are many ways
   you could offer source, and different solutions will be better for different programs;
   see section 13 of the GNU Affero General Public License for the specific requirements. 
   
   #####################################################################################
   -->
<div class="bar">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1>Forum:</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<?php if (ADMIN): ?>
<div class="box-main-a-1"></div>
<div class="box-main-a-2 options-a-2 buttonset">    
    <button class="jui-button" icon="ui-icon-plus" onclick="newBoard('forum','');"><?php echo $this->_tpl_vars['view_board_add_new_forum']; ?>
</button>
</div>
<div class="box-main-a-3"></div>
<?php endif; ?>
<div id="forums">
<script language="javascript" type="text/javascript">var glname, gldescription;

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
        var content = '<h1><?php echo $this->_tpl_vars['view_board_edit']; ?>
</h1><div class="aInput" style="text-align: left;"><label for="name"><?php echo $this->_tpl_vars['view_board_name']; ?>
:</label><br /><input id="name" type="text" class="aTextbox" value="'+name+'" /><br /><label for="description"><?php echo $this->_tpl_vars['view_board_description']; ?>
:</label><br /><input type="text" class="aTextbox" id="description" value="'+description+'"/>'
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
        var content = '<h1><?php echo $this->_tpl_vars['view_board_add_new_forum']; ?>
</h1><div class="aInput" style="text-align: left;"><label for="name"><?php echo $this->_tpl_vars['view_board_name']; ?>
:</label><br /><input id="name" type="text" class="aTextbox"/><br /><label for="description"><?php echo $this->_tpl_vars['view_board_description']; ?>
:</label><br /><input type="text" class="aTextbox" id="description"/>'
    }
    else
    {
        var content = '<h1><?php echo $this->_tpl_vars['view_board_add_new_board']; ?>
</h1><div class="aInput" style="text-align: left;"><label for="name"><?php echo $this->_tpl_vars['view_board_name']; ?>
:</label><br /><input id="name" type="text" class="aTextbox"/><br /><label for="description"><?php echo $this->_tpl_vars['view_board_description']; ?>
:</label><br /><input type="text" class="aTextbox" id="description"/>'
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
    var content = '<h1><?php echo $this->_tpl_vars['view_board_delete']; ?>
</h1><small style="font-weight: bold;"><?php echo $this->_tpl_vars['view_board_delete_info']; ?>
</small><div class="aInput"><input type="password" class="aTextbox" id="password"/></div>';
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
}</script>
<div class="share-file-grid-con">
<?php echo $this->_tpl_vars['FORUMS']; ?>

</div>
</div>