<?php /* Smarty version 2.6.26, created on 2014-03-11 21:22:21
         compiled from file:style/newcunity/templates/messages/inbox.tpl.html */ ?>
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
   <script language="javascript" type="text/javascript">$("document").ready(function(){
	loadConversations();	
	var receiver_length=0;
	$("#receiver").autocomplete({
		source: 'controllers/ajaxFriendsController.php?json_data={"action":"fieldSearch"}',
		minLength: 1,
		select: function( event, ui ) {
			$(this).val(ui.item.label);
			$("#receiver").after('<input type="hidden" id="receiver_id_input" name="receiver_data" value="'+ui.item.cid+'-'+ui.item.hash+'"/>');
			$("#receiver").autocomplete("close");
			receiver_length = $("#receiver").val().length;
		}
	});	
	$("#receiver").keyup(function(){
		if($("#receiver_id_input").length>0&&receiver_length>0){
			if($("#receiver").val().length!=receiver_length){
				$("#receiver").val("");
				$("#receiver_id_input").remove();
				receiver_length=0;
			}
		}
			
	})
	$("#msgTa").keyup(function(){
		if($(this).val().length>0)
			$("#sendMessageButton")
				.removeAttr('disabled')
				.button('enable');
		else
			$("#sendMessageButton")
				.attr('disabled','disabled')
				.button('disable');
	})
})
function loadConversations(){
	var data = '{"action":"loadConversations"}';
	$.post("controllers/ajaxMessageController.php", {json_data : data}, function(data) {
		if(data.status==1)
			$("#conversations").html(data.content);
		else
			apprise("An Error occurred! Please try again later!");
    }, "json");
}</script>
<div class="bar">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1><?php echo $this->_tpl_vars['inbox_title']; ?>
 - <?php echo $this->_tpl_vars['inbox_inbox']; ?>
</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<div class="box-main-a-1"></div>
<div class="box-main-a-2 options-a-2">
<button class="jui-button" onclick="newMessage('','',0);" class="jui-button" icon="ui-icon-pencil"><?php echo $this->_tpl_vars['inbox_new_message']; ?>
</button>
</div>
<div class="box-main-a-3"></div>
<div class="clear"></div>
<div id="conversations"></div>