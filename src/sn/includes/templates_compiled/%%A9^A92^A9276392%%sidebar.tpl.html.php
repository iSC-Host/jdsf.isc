<?php /* Smarty version 2.6.26, created on 2014-02-07 15:21:37
         compiled from file:style/newcunity/templates/sidebar.tpl.html */ ?>
</div>                       
<div class="main_page_col_b">
<?php if (LOGIN): ?>
<script language="javascript" type="text/javascript">function loadchat() {
    var data = '{"c":"loadonlinefriends"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data},
    function(data_back) {
        if (data_back.status == 1) {
            $("#friendsonline").html(data_back.onlinefriendslist);
            $("#friendsmore").html(data_back.friendsmore);
            $("#friendsonlinebutton").button("option", "label", data_back.friendscount);
            $("#changeOnlineItem").html(data_back.statusChange);
            if (data_back.online == false)
                $("#friendsonlinebutton").button('disable');
            else
                $("#friendsonlinebutton").button('enable');
            chatHeartbeat();
            refreshButtons();
        }
    }, "json")
            .error(function() {
        $("#friendsonlinebutton").button('option', {
            icons: {
                primary: 'ui-icon-alert'
            },
            label: "An error occurred!"
        });
    })
}

function showAllChat() {
    var data = '{"c":"loadonlinefriends","do":"all"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data},
    function(data_back) {
        $("#friendsonline").html(data_back.onlinefriendslist);
        $("#friendsmore").html(data_back.friendsmore);
        $("#friendsonlinebutton").button("option", "label", data_back.friendscount);
        $("#changeOnlineItem").html(data_back.statusChange);
        if (data_back.online == false)
            $("#friendsonlinebutton").button('disable');
        else
            $("#friendsonlinebutton").button('enable');
        chatHeartbeat();
        refreshButtons();
    }, "json");
}

function changeChatStatus() {
    var data = '{"c":"changeChatStatus"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data},
    function(data_back) {
        $("#chatmenu_dropdown").slideUp(100);
        loadchat();
    }, "json");
}

function removeRequest(userid, userData, remote, callback) {
    apprise("<?php echo $this->_tpl_vars['friends_confirm_remove_request']; ?>
", {verify: true}, function(r) {
        if (r) {
            var data = '{"c":"delete_friend", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
            $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                if (data_back.status == 1)
                    if (callback != undefined && typeof callback == 'function')
                        callback(userid);
                    else
                        apprise(data.error);
            }, "json");
        }
    })
}

function addasfriend(userid, userData, remote, callback) {
    var data = '{"c":"get_add_text", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
        if (data_back.status == 1) {
            apprise(data_back.text, {confirm: true, textOk: '<?php echo $this->_tpl_vars['friends_send_request']; ?>
'}, function(a) {
                if (a) {
                    var data = '{"c":"add_friend", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
                    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                        if (data_back.status == 1)
                            if (callback != undefined && typeof callback == 'function')
                                callback();
                            else
                                apprise(data_back.error);
                    }, "json");
                }
            })
        }
    }, "json");
}

function respondRequest(userid, userData, remote, callback) {
    var data = '{"c":"get_request_text", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
        if (data_back.status == 1) {
            apprise(data_back.text, {confirm: true, textOk: '<?php echo $this->_tpl_vars['friends_confirm_request']; ?>
'}, function(a) {
                if (a) {
                    var data = '{"c":"confirm_request", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
                    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                        if (data_back.status == 1)
                            if (callback != undefined && typeof callback == 'function')
                                callback();
                            else
                                apprise(data_back.error);
                    }, "json");
                }
            })
        }
    }, "json");
}

function deleteFriend(userid, userData, remote, callback) {
    var data = '{"c":"get_delete_text", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
        if (data_back.status == 1) {
            apprise(data_back.text, {confirm: true, textOk: '<?php echo $this->_tpl_vars['friends_confirm_delete']; ?>
'}, function(a) {
                if (a) {
                    var data = '{"c":"delete_friend", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
                    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                        if (data_back.status == 1)
                            if (callback != undefined && typeof callback == 'function')
                                callback(userid, data_back.newText);
                            else
                                apprise(data_back.error);
                    }, "json");
                }
            })
        }
    }, "json");
}

function blockFriend(userid, userData, remote, callback) {
    var data = '{"c":"get_block_text", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
        apprise(data_back.text, {confirm: true, textOk: '<?php echo $this->_tpl_vars['friends_block_friend']; ?>
'}, function(a) {
            if (a) {
                var data = '{"c":"block_friend", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
                $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                    if (data_back.status == 1)
                        if (callback != undefined && typeof callback == 'function')
                            callback(userid);
                        else
                            apprise(data.error);
                });
            }
        })
    }, "json")
}

function unblockFriend(userid, userData, remote, callback) {
    var data = '{"c":"get_unblock_text", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
    $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
        apprise(data_back.text, {confirm: true, textOk: '<?php echo $this->_tpl_vars['friends_unblock_friend']; ?>
'}, function(a) {
            if (a) {
                var data = '{"c":"delete_friend", "userid": "' + userid + '","userData":"' + userData + '","remote":"' + remote + '"}';
                $.post("controllers/ajaxFriendsController.php", {json_data: data}, function(data_back) {
                    if (data_back.status == 1)
                        if (callback != undefined && typeof callback == 'function')
                            callback(userid, data_back.newText);
                        else
                            apprise(data_back.error);
                }, "json");
            }
        })
    }, "json")
}

window.setTimeout('loadchat()', 500);
window.setInterval('loadchat()', <?php echo $this->_tpl_vars['timeoutFriendsOnline']; ?>
);</script>
<?php endif; ?>
<?php if ($this->_tpl_vars['module']['chat']): ?>
<script language="javascript" type="text/javascript">
$("document").ready(function(){
    $("#chatbuttons").buttonset();
    $("#chatsettings").live('click', function(){
        if($("#chatmenu_dropdown").is(':visible'))
		   $("#chatmenu_dropdown").slideUp(100);
		else
		   $("#chatmenu_dropdown").slideDown(100);
    })
    
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
})
</script>
<div class="find-friends" style="position: relative; overflow: visible;margin-bottom: 0px;">
<div id="chatbuttons">
<button id="friendsonlinebutton" class="jui-button" icon="ui-icon-comment" style="width: 160px; text-align: left;"><?php echo $this->_tpl_vars['sidebar_loading']; ?>
...</button><button icon="ui-icon-gear" icon2="ui-icon-triangle-1-s" class="jui-button" id="chatsettings" text="false">&nbsp;</button>
</div>
<ul id="chatmenu_dropdown" class="ui-widget ui-widget-content ui-corner-all ui-menu" style="display: none; width: 130px; position: absolute; right: 15px; top: 25px; text-align: left;">
    <li class="ui-menu-item">
        <a class="ui-corner-all" href="javascript: changeChatStatus();" id="changeOnlineItem"></a>
    </li>
</ul>
<ul id="friendsonline"></ul>
<div id="friendsmore" style="text-align: center;padding-right:10px;padding-top:3px"></div>
</div>
<?php endif; ?>
<?php if (! LOGIN): ?>
    <div class="small_info_1" style="margin-top: 10px;"></div>    
	<form action="register.php?c=login" method="POST" class="small_info_2" style="text-align: left; width:180px;">
        <a style="font-weight: bold; font-size: 15px;">Login</a><br />       	    
        E-Mail: <br />
        <input type="text" id="email" name="mail" value="" style="margin-bottom:7px;"/>
        <br />
        <?php echo $this->_tpl_vars['register_pw']; ?>
: <input type="password" id="pass" name="pass" value=""/>
        <div style="margin-top: 5px; margin-left: 3px; padding:3px 0px; border-bottom: 1px dashed #ccc;border-top: 1px dashed #ccc">
        <input type="checkbox" id="save_login" name="save_login" value="yes" style="vertical-align: bottom;">        
        <label for="save_login" style="display: inline-block; vertical-align: top;"><?php echo $this->_tpl_vars['login_online']; ?>
</label><br />
        </div>
        <div style="margin-left: 3px; padding:3px 0px; border-bottom: 1px dashed #ccc;">
        <span class="ui-icon-text ui-icon-info" style="display:inline-block">&nbsp;</span><b><a href="register.php?c=resetpw" style="vertical-align:bottom;"><?php echo $this->_tpl_vars['login_forgot_pw']; ?>
</a></b>
        </div>		
        <input class="jui-button" type="submit" name="login" value="Login" style="margin: 5px;margin-bottom:0px;float:right;"/>
        <div class="clear"></div>                
	</form>
    <div class="small_info_3"></div>	
<?php endif; ?>
    <div class="clear"></div>
    <?php if (LOGIN): ?>
	<!-- Adds start --->
	<div class="adds-main">
        <div class="small_info_1" style="margin-top: 10px;"></div>
        <div class="small_info_2" id="friends_sidebar">
        <?php echo $this->_tpl_vars['FRIENDSUGGESTIONS']; ?>

        </div>
        <div class="small_info_3"></div>	
	</div>
	<!-- Adds End --->
	<?php endif; ?>
	</div>
		<!-- Main Col B End -->


	</div>
	<div class="main_c"></div>
	</div>
</div>