<?php /* Smarty version 2.6.26, created on 2014-03-12 12:34:12
         compiled from file:style/newcunity/templates/profile_edit.tpl.html */ ?>
<div class="bar">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1><?php echo $this->_tpl_vars['NAME']; ?>
 - <?php echo $this->_tpl_vars['EDIT_TITLE']; ?>
</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<!-- Main Blue bar End -->
<div class="clear"></div>

<!-- Profile Box Start -->
<div class="profile">

<?php echo $this->_tpl_vars['PROFILE_INFO']; ?>


	<!-- Picture Box Start  -->
	<div class="profile-box">
		<div class="profile-box-pic">
		<img src="<?php echo $this->_tpl_vars['PROFILE_PIC']; ?>
" style="width:200px;height:auto"/>
		</div>

	</div>
	<!-- Picture Box End  -->

	<!-- Profile Info Start -->
	<div class="profile-info">
		<button class="jui-button" style="float: right; margin-bottom: 10px;" onclick="location.href='profile.php'" icon="ui-icon-person"><?php echo $this->_tpl_vars['profile_edit_back']; ?>
</button>
		<div class="clear"></div>

    <div class="main_info_1"></div>
    <div class="main_info_2" style="width: 330px;">
        <ul class="sidebar_menu" style="margin: 0px;">
            <li class="sidebar_menu_item" style="background-image: url('style/thecunity/img/user.png');"><a href="profile.php"><?php echo $this->_tpl_vars['profile_edit_back']; ?>
</a></li>
            <li class="sidebar_menu_item" style="background-image: url('style/thecunity/img/edit.png');"><a href="profile.php?c=edit&do=general"><?php echo $this->_tpl_vars['profile_edit_general']; ?>
</a></li>
            <li class="sidebar_menu_item" style="background-image: url('style/thecunity/img/user_edit.png');"><a href="profile.php?c=edit&do=personal"><?php echo $this->_tpl_vars['profile_edit_personal']; ?>
</a></li>
            <li class="sidebar_menu_item" style="background-image: url('style/thecunity/img/lock.png');"><a href="profile.php?c=edit&do=privacy"><?php echo $this->_tpl_vars['profile_edit_privacy']; ?>
</a></li>
            <li class="sidebar_menu_item" style="background-image: url('style/thecunity/img/mail.png');"><a href="profile.php?c=edit&do=notifications"><?php echo $this->_tpl_vars['profile_edit_notifications']; ?>
</a></li>
            <li class="sidebar_menu_item" style="background-image: url('style/thecunity/img/edit_img.png');"><a href="profile.php?c=edit&do=img"><?php echo $this->_tpl_vars['profile_edit_image_edit']; ?>
</a></li>
            <li class="sidebar_menu_item" style="background-image: url('style/thecunity/img/edit-pw.png');"><a href="profile.php?c=edit&do=passwd"><?php echo $this->_tpl_vars['profile_edit_change_password']; ?>
</a></li>
            <li class="sidebar_menu_item" style="background-image: url('style/thecunity/img/block.png');"><a href="profile.php?c=edit&do=blocked"><?php echo $this->_tpl_vars['profile_edit_blocked_persons']; ?>
</a></li>
            <li class="sidebar_menu_item" style="background-image: url('style/thecunity/img/fail.png');"><a href="profile.php?c=edit&do=delete"><?php echo $this->_tpl_vars['profile_edit_delete_account']; ?>
</a></li>
        </ul>
    </div>
    <div class="main_info_3"></div>
	</div>
	<div class="profile-grid">
	<?php echo $this->_tpl_vars['MSG']; ?>

    <?php if ($this->_tpl_vars['do'] == 'notifications'): ?>
        <script language="javascript" type="text/javascript">
        $("document").ready(function(){
            $(".privacy_value").children('input').each(function(){
                $(this).button({
                    icons: {
                        primary: 'ui-icon-mail-closed'
                    },
                    text: false
                });
            })
        })
        </script>
        <form action="profile.php?c=edit&do=notifications&extra=refresh" method="POST">            
            <table border="0" cellpadding="1" cellspacing="1" id="privacy_table">
                <tr class="privacy_line" style="border: 0px;">
                    <td style="text-center; vertical-align: middle; border-bottom: 2px solid #aaa;" class="privacy_label"><label for="searching"><?php echo $this->_tpl_vars['profile_edit_notify_when_so']; ?>
...</label></td>
                </tr>
                <tr class="privacy_line">
                    <td style="text-center; vertical-align: middle;" class="privacy_label"><label><?php echo $this->_tpl_vars['profile_edit_get_message']; ?>
</label></td>
                    <td style="text-center; vertical-align: middle;" class="privacy_value">                    
                        <input type="checkbox" name="get_message" value="1" id="get_msg" <?php echo $this->_tpl_vars['GET_MESSAGE']; ?>
/>
                        <label for="get_msg"><?php echo $this->_tpl_vars['profile_edit_get_message']; ?>
</label>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="text-center; vertical-align: middle;" class="privacy_label"><label><?php echo $this->_tpl_vars['profile_edit_add_friend']; ?>
</label></td>
                    <td style="text-center; vertical-align: middle;" class="privacy_value">
                        <input type="checkbox" name="add_friend" value="1" id="add_friend" <?php echo $this->_tpl_vars['ADD_FRIEND']; ?>
/>
                        <label for="add_friend"><?php echo $this->_tpl_vars['profile_edit_add_friend']; ?>
</label>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="text-center; vertical-align: middle;" class="privacy_label"><label><?php echo $this->_tpl_vars['profile_edit_post_on_pin']; ?>
</label></td>
                    <td style="text-center; vertical-align: middle;" class="privacy_value">
                        <input type="checkbox" name="post_on_pinboard" value="1" id="post_pin" <?php echo $this->_tpl_vars['POST_ON_PINBOARD']; ?>
/>
                        <label for="post_pin"><?php echo $this->_tpl_vars['profile_edit_post_on_pin']; ?>
</label>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="text-center; vertical-align: middle;" class="privacy_label"><label><?php echo $this->_tpl_vars['profile_edit_pinboard_comment_status']; ?>
</label></td>
                    <td style="text-center; vertical-align: middle;" class="privacy_value">
                        <input type="checkbox" name="status_comment" value="1" id="pin_com_stat" <?php echo $this->_tpl_vars['STATUS_COMMENT']; ?>
/>
                        <label for="pin_com_stat"><?php echo $this->_tpl_vars['profile_edit_pinboard_comment_status']; ?>
</label>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="text-center; vertical-align: middle;" class="privacy_label"><label><?php echo $this->_tpl_vars['profile_edit_comment_status']; ?>
</label></td>
                    <td style="text-center; vertical-align: middle;" class="privacy_value">
                        <input type="checkbox" name="status_comment" value="1" id="com_stat" <?php echo $this->_tpl_vars['STATUS_COMMENT']; ?>
/>
                        <label for="com_stat"><?php echo $this->_tpl_vars['profile_edit_comment_status']; ?>
</label>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="text-center; vertical-align: middle;" class="privacy_label"><label><?php echo $this->_tpl_vars['profile_edit_also_comment_status']; ?>
</label></td>
                    <td style="text-center; vertical-align: middle;" class="privacy_value">
                        <input type="checkbox" name="also_status_comment" value="1" id="also_com_stat" <?php echo $this->_tpl_vars['ALSO_STATUS_COMMENT']; ?>
/>
                        <label for="also_com_stat"><?php echo $this->_tpl_vars['profile_edit_also_comment_status']; ?>
</label>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="text-center; vertical-align: middle;" class="privacy_label"><label><?php echo $this->_tpl_vars['profile_edit_invited']; ?>
</label></td>
                    <td style="text-center; vertical-align: middle;" class="privacy_value">
                        <input type="checkbox" name="invite_events" value="1" id="invited" <?php echo $this->_tpl_vars['INVITE_EVENTS']; ?>
/>
                        <label for="invited"><?php echo $this->_tpl_vars['profile_edit_invited']; ?>
</label>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="text-center; vertical-align: middle;" class="privacy_label"><label><?php echo $this->_tpl_vars['profile_edit_file_shared']; ?>
</label></td>
                    <td style="text-center; vertical-align: middle;" class="privacy_value">
                        <input type="checkbox" name="file_shared" value="1" id="file_shared" <?php echo $this->_tpl_vars['FILE_SHARED']; ?>
/>
                        <label for="file_shared"><?php echo $this->_tpl_vars['profile_edit_file_shared']; ?>
</label>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="text-center; vertical-align: middle;" class="privacy_label"><label><?php echo $this->_tpl_vars['profile_edit_forum_new_post']; ?>
</label></td>
                    <td style="text-center; vertical-align: middle;" class="privacy_value">
                        <input type="checkbox" name="post_forum" value="1" id="forum_post" <?php echo $this->_tpl_vars['POST_FORUM']; ?>
/>
                        <label for="forum_post"><?php echo $this->_tpl_vars['profile_edit_forum_new_post']; ?>
</label>
                    </td>
                </tr>
            </table>
            <input class="jui-button input_submit" type="submit" value="<?php echo $this->_tpl_vars['profile_edit_save']; ?>
"/>
        </form>
    <?php elseif ($this->_tpl_vars['do'] == 'privacy'): ?>    
        <form action="profile.php?c=edit&do=privacy" method="POST">            
            <table border="0" cellpadding="1" cellspacing="1" id="privacy_table">
                <tr class="privacy_line">
                    <td style="text-center; vertical-align: middle;" class="privacy_label"><label for="searching"><?php echo $this->_tpl_vars['profile_edit_privacy_searching']; ?>
</label></td>
                    <td style="text-center; vertical-align: middle;" class="privacy_value">
                        <select name="searching" id="searching" class="privacy_select">
                            <option value="0" <?php echo $this->_tpl_vars['SEARCHING_0']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone']; ?>
</option>
                            <option value="1" <?php echo $this->_tpl_vars['SEARCHING_1']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone_in_cunity']; ?>
</option>
                            <option value="2" <?php echo $this->_tpl_vars['SEARCHING_2']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_of_friends']; ?>
</option>
                            <option value="3" <?php echo $this->_tpl_vars['SEARCHING_3']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_only']; ?>
</option>
                        </select>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><label for="friending"><?php echo $this->_tpl_vars['profile_edit_privacy_friending']; ?>
</label></td>
                    <td style="vertical-align: middle;" class="privacy_value">
                        <select name="friending" id="friending" class="privacy_select">
                            <option value="0" <?php echo $this->_tpl_vars['FRIENDING_0']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone']; ?>
</option>
                            <option value="1" <?php echo $this->_tpl_vars['FRIENDING_1']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone_in_cunity']; ?>
</option>
                            <option value="2" <?php echo $this->_tpl_vars['FRIENDING_2']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_of_friends']; ?>
</option>
                            <option value="3" <?php echo $this->_tpl_vars['FRIENDING_3']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_only']; ?>
</option>
                        </select>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><label for="messaging"><?php echo $this->_tpl_vars['profile_edit_privacy_messages']; ?>
</label></td>
                    <td style="vertical-align: middle;" class="privacy_value">
                        <select name="messaging" id="messaging" class="privacy_select">
                            <option value="0" <?php echo $this->_tpl_vars['MESSAGING_0']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone']; ?>
</option>
                            <option value="1" <?php echo $this->_tpl_vars['MESSAGING_1']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone_in_cunity']; ?>
</option>
                            <option value="2" <?php echo $this->_tpl_vars['MESSAGING_2']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_of_friends']; ?>
</option>
                            <option value="3" <?php echo $this->_tpl_vars['MESSAGING_3']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_only']; ?>
</option>
                        </select>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><label for="pinboard_viewing"><?php echo $this->_tpl_vars['profile_edit_privacy_pinboard']; ?>
</label></td>
                    <td style="vertical-align: middle;" class="privacy_value">
                        <select name="pinboard_viewing" id="pinboard_viewing" class="privacy_select">
                            <option value="0" <?php echo $this->_tpl_vars['PINBOARD_VIEWING_0']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone']; ?>
</option>
                            <option value="1" <?php echo $this->_tpl_vars['PINBOARD_VIEWING_1']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone_in_cunity']; ?>
</option>
                            <option value="2" <?php echo $this->_tpl_vars['PINBOARD_VIEWING_2']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_of_friends']; ?>
</option>
                            <option value="3" <?php echo $this->_tpl_vars['PINBOARD_VIEWING_3']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_only']; ?>
</option>
                        </select>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><label for="profile_viewing"><?php echo $this->_tpl_vars['profile_edit_privacy_profile']; ?>
</label></td>
                    <td style="vertical-align: middle;" class="privacy_value">
                        <select name="profile_viewing" id="profile_viewing" class="privacy_select">
                            <option value="0" <?php echo $this->_tpl_vars['PROFILE_VIEWING_0']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone']; ?>
</option>
                            <option value="1" <?php echo $this->_tpl_vars['PROFILE_VIEWING_1']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone_in_cunity']; ?>
</option>
                            <option value="2" <?php echo $this->_tpl_vars['PROFILE_VIEWING_2']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_of_friends']; ?>
</option>
                            <option value="3" <?php echo $this->_tpl_vars['PROFILE_VIEWING_3']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_only']; ?>
</option>
                        </select>
                    </td>
                </tr>
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><label for="address_viewing"><?php echo $this->_tpl_vars['profile_edit_privacy_address']; ?>
</label></td>
                    <td style="vertical-align: middle;" class="privacy_value">
                        <select name="address_viewing" id="address_viewing" class="privacy_select">
                            <option value="0" <?php echo $this->_tpl_vars['ADDRESS_VIEWING_0']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone']; ?>
</option>
                            <option value="1" <?php echo $this->_tpl_vars['ADDRESS_VIEWING_1']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_everyone_in_cunity']; ?>
</option>
                            <option value="2" <?php echo $this->_tpl_vars['ADDRESS_VIEWING_2']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_of_friends']; ?>
</option>
                            <option value="3" <?php echo $this->_tpl_vars['ADDRESS_VIEWING_3']; ?>
><?php echo $this->_tpl_vars['profile_edit_privacy_friends_only']; ?>
</option>
                        </select>
                    </td>
                </tr>
            </table>
            <input class="jui-button" type="submit" class="input_submit" name="save" value="<?php echo $this->_tpl_vars['profile_edit_save']; ?>
"/>
        </form>
    <?php elseif ($this->_tpl_vars['do'] == 'img'): ?>       
        <script src="includes/jcrop/js/jquery.Jcrop.pack.js"></script>
        <link rel="stylesheet" href="includes/jcrop/css/jquery.Jcrop.css" type="text/css" />
        <script language="javascript" type="text/javascript">
// Remember to invoke within jQuery(window).load(...)
// If you don't, Jcrop may not initialize properly
$("document").ready(function(){

jQuery('#profile_pic').Jcrop({
onChange: showPreview,
        onSelect: updateCoords,
        setSelect:   [<?php echo $this->_tpl_vars['X1']; ?>
, <?php echo $this->_tpl_vars['Y1']; ?>
, <?php echo $this->_tpl_vars['X2']; ?>
, <?php echo $this->_tpl_vars['Y2']; ?>
],
        aspectRatio: 1
        });
        var rx = 120 / <?php echo $this->_tpl_vars['W']; ?>
;
        var ry = 120 / <?php echo $this->_tpl_vars['H']; ?>
;
        jQuery('#avatar_preview').css({
            marginLeft: '-' + Math.round(rx * <?php echo $this->_tpl_vars['X1']; ?>
) + 'px',
            marginTop: '-' + Math.round(ry * <?php echo $this->_tpl_vars['Y1']; ?>
) + 'px',
            width:Math.round(rx * $("#profile_pic").width()) + 'px',
            height:Math.round(ry * $("#profile_pic").height()) + 'px'
        });
        $("#new_profile_pic").change(function(){
$("#new_profile_pic").fadeOut();
        $("#img_form").submit();
        });
});
// Our simple event handler, called from onChange and onSelect
// event handlers, as per the Jcrop invocation above
        function showPreview(coords){
        if (parseInt(coords.w) > 0){
        var rx = 100 / coords.w;
                var ry = 100 / coords.h;
                $('#avatar_preview').css({
        marginLeft: '-' + Math.round(rx * coords.x) + 'px',
                marginTop: '-' + Math.round(ry * coords.y) + 'px',
                width:Math.round(rx * $("#profile_pic").width()) + 'px',
                height:Math.round(ry * $("#profile_pic").height()) + 'px'
        });
        }
        }

function updateCoords(c){
$('#x').val(c.x);
        $('#y').val(c.y);
        $('#x2').val(c.x2);
        $('#y2').val(c.y2);
        $('#w').val(c.w);
        $('#h').val(c.h);
};</script>
        <form enctype="multipart/form-data" action="profile.php?c=edit&do=img" method="POST" id="img_form">
        	<table border="0" cellpadding="1" cellspacing="1" id="privacy_table">
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><?php echo $this->_tpl_vars['profile_edit_new_img']; ?>
<br /><span class="table_info"><?php echo $this->_tpl_vars['profile_edit_jpg']; ?>
</span></td>
                    <td style="vertical-align: middle; text-align: left;" class="privacy_value"><input type="file" name="new_profile_pic" id="new_profile_pic"/></td>
                </tr>
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><?php echo $this->_tpl_vars['profile_edit_img']; ?>
<br /><span class="table_info"><?php echo $this->_tpl_vars['profile_edit_img_appears']; ?>
</span></td>
                    <td style="vertical-align: middle; text-align: left;" class="privacy_value"><img src="<?php echo $this->_tpl_vars['PROFILE_PIC']; ?>
" style="width:200px;height:auto" id="profile_pic"/></td>
                </tr>
        </form>
        <form action="profile.php?c=edit&do=img" method="POST">
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><?php echo $this->_tpl_vars['profile_edit_avatar_preview']; ?>
<br /><span class="table_info"><?php echo $this->_tpl_vars['profile_edit_select_avatar']; ?>
</span></td>
                    <td style="vertical-align: middle; text-align: left;" class="privacy_value">
                        <div style="overflow: hidden; height: 100px; width: 100px;"><img src="<?php echo $this->_tpl_vars['PROFILE_PIC']; ?>
" style="width:200px;height:auto" class="avatar" id="avatar_preview"/></div>
                    </td>
                </tr>
            </table>
            <input type="hidden" id="x" name="x" value=""/>
    		<input type="hidden" id="y" name="y" value=""/>
    		<input type="hidden" id="x2" name="x2" value="" />
    		<input type="hidden" id="y2" name="y2" value="" />
    		<input type="hidden" id="w" name="w" value="" />
    		<input type="hidden" id="h" name="h" value="" />
            <input class="jui-button" type="submit" class="input_submit" value="<?php echo $this->_tpl_vars['profile_edit_save']; ?>
" id="submit_full"/>
            <input class="jui-button" type="submit" class="input_submit" value="<?php echo $this->_tpl_vars['profile_edit_delete']; ?>
" name="delete_image"/>
        </form>
    <?php elseif ($this->_tpl_vars['do'] == 'passwd'): ?>
        <form action="profile.php?c=edit&do=passwd" method="POST" id="change_pw_table">           
            <table border="0" cellpadding="1" cellspacing="1" id="privacy_table">
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><?php echo $this->_tpl_vars['profile_edit_pw_current']; ?>
</td>
                    <td style="vertical-align: middle; text-align: left;" class="privacy_value"><input type="password" name="pw1"/></td>
                </tr>
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><?php echo $this->_tpl_vars['profile_edit_pw_new']; ?>
</td>
                    <td style="vertical-align: middle; text-align: left;" class="privacy_value"><input type="password" name="pw2"/></td>
                </tr>
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><?php echo $this->_tpl_vars['profile_edit_pw_rpt']; ?>
</td>
                    <td style="vertical-align: middle; text-align: left;" class="privacy_value"><input type="password" name="pw3"/></td>
                </tr>
            </table>
        	<input class="jui-button input_submit" type="submit" value="<?php echo $this->_tpl_vars['profile_edit_save']; ?>
" style="display: inline-block;"/>
        	<div style="display: inline-block;"><?php echo $this->_tpl_vars['INFO']; ?>
</div>
        </form>
    <?php elseif ($this->_tpl_vars['do'] == 'blocked'): ?>
        <script language="javascript" type="text/javascript">function removeUser(user){
	$("#friend-"+user).remove();
    if($("#blocked_friends .main_list_wrap").length==0)
        $("#blocked_friends .message_red").show();
}</script>        
        <h3 style="margin: 0px; padding: 0px;"><?php echo $this->_tpl_vars['profile_edit_blocked_persons']; ?>
</h3>
        <div id="blocked_friends"><?php echo $this->_tpl_vars['BLOCKED']; ?>
</div>
    <?php elseif ($this->_tpl_vars['do'] == 'personal'): ?>
    <script language="javascript" type="text/javascript">$("#relationship_select").live('change', function(){
    switch($(this).val())
    {
        case '1':
            $("#rel_part").html('&nbsp;<?php echo $this->_tpl_vars['profile_view_with']; ?>
 <input type="hidden" name="relationship_partner" id="relationship_partner"/><input type="text" id="partner" name="partner"><div id="search_result" style="display: none;"></div>');
        break;
        
        case '2':
            $("#rel_part").html("");
        break;

		case '3':
            $("#rel_part").html('&nbsp;<?php echo $this->_tpl_vars['profile_view_with']; ?>
 <input type="hidden" name="relationship_partner" id="relationship_partner"/><input type="text" id="partner" name="partner"><div id="search_result" style="display: none;"></div>');
        break;

		case '4':
            $("#rel_part").html('&nbsp;<?php echo $this->_tpl_vars['profile_view_with']; ?>
 <input type="hidden" name="relationship_partner" id="relationship_partner"/><input type="text" id="partner" name="partner"><div id="search_result" style="display: none;"></div>');
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
    apprise('<?php echo $this->_tpl_vars['profile_edit_confirm_delete_request']; ?>
', {confirm: true}, function(r){
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
}</script>
        <form action="profile.php?c=edit&do=personal" method="POST" id="personal_table">           
            <table border="0" cellpadding="1" cellspacing="1" id="privacy_table">
                <?php echo $this->_tpl_vars['PERSONAL_LIST']; ?>

            </table>
        	<input class="jui-button input_submit" type="submit" value="<?php echo $this->_tpl_vars['profile_edit_save']; ?>
" style="display: inline-block;"/>
        	<div style="display: inline-block;"><?php echo $this->_tpl_vars['INFO']; ?>
</div>
        </form>
    <?php elseif ($this->_tpl_vars['do'] == 'general'): ?>
        <form action="profile.php?c=edit&do=general" method="POST" id="general_table">        
            <table border="0" cellpadding="1" cellspacing="1" id="privacy_table">
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><?php echo $this->_tpl_vars['profile_edit_email']; ?>
</td>
                    <td style="vertical-align: middle; text-align: left;" class="privacy_value"><input type="email" class="jui-button_input" name="mail" value="<?php echo $this->_tpl_vars['MAIL']; ?>
"/></td>
                </tr>
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><?php echo $this->_tpl_vars['profile_edit_title']; ?>
</td>
                    <td style="vertical-align: middle; text-align: left;" class="privacy_value"><select name="title" class="jui-button_input"><option value="0" <?php echo $this->_tpl_vars['WM']; ?>
><?php echo $this->_tpl_vars['profile_edit_woman']; ?>
</option><option value="1" <?php echo $this->_tpl_vars['ME']; ?>
><?php echo $this->_tpl_vars['profile_edit_men']; ?>
</option></select></td>
                </tr>
                <tr class="privacy_line">
                    <td style="vertical-align: middle;" class="privacy_label"><?php echo $this->_tpl_vars['profile_edit_birthday']; ?>
&nbsp;<small style="font-size: 11px;"><?php echo $this->_tpl_vars['profile_edit_date_format']; ?>
</small></td>
                    <td style="vertical-align: middle; text-align: left;" class="privacy_value"><input type="text" name="day" value="<?php echo $this->_tpl_vars['DAY']; ?>
" size="2"/>&nbsp;<input type="text" name="month" value="<?php echo $this->_tpl_vars['MONTH']; ?>
" size="2"/>&nbsp;<input type="text" name="year" value="<?php echo $this->_tpl_vars['YEAR']; ?>
" size="4"/></td>
                </tr>
            </table>
        	<input class="jui-button input_submit" type="submit" value="<?php echo $this->_tpl_vars['profile_edit_save']; ?>
" style="display: inline-block;"/>
        	<div style="display: inline-block;"><?php echo $this->_tpl_vars['INFO']; ?>
</div>
        </form>
    <?php elseif ($this->_tpl_vars['do'] == 'personal_requests'): ?>
        <script language="javascript" type="text/javascript">
        function deleteRequest(rid)
        {
            apprise('<?php echo $this->_tpl_vars['profile_edit_confirm_delete_request']; ?>
', {confirm: true}, function(r){
                if(r)
                {
                    var r = rid;
                    var data = '{"action":"deleteRequest", "relationship_id": "'+ rid +'"}';
                    $.post("controllers/ajaxProfileController.php", {
                		json_data : data
                	}, function(data_back) {
                	   if(data_back.status == 1)
        	               location.href='profile.php?c=edit&do=personal';
                }
            })
        }
        function confirmRequest(rid)
        {
            var r = rid;
            var data = '{"action":"confirm_request", "relationship_id": "'+ rid +'"}';
            $.post("controllers/ajaxProfileController.php", {
        		json_data : data
        	}, function(data_back) {
        	   if(data_back.status == 1)
        	       location.href='profile.php?c=edit&do=personal';
        	}, "json");
        }
        </script>        
        <table border="0" cellpadding="1" cellspacing="1" id="privacy_table">
            <?php echo $this->_tpl_vars['REQUESTS']; ?>

        </table>
    <?php elseif ($this->_tpl_vars['do'] == 'delete'): ?>       
        <form action="profile.php?c=edit&do=delete" method="post" style="text-align: center;">
            <p class="message_red"><?php echo $this->_tpl_vars['profile_edit_account_delete_info']; ?>
</p>
            <p style="padding: 10px 0px;"><label for="password"><?php echo $this->_tpl_vars['profile_edit_password']; ?>
: </label><input type="password" name="password" id="password"/><input type="submit" value="<?php echo $this->_tpl_vars['profile_edit_delete_account']; ?>
" class="jui-button" name="confirm"/></p>                                    
        </form>
    <?php endif; ?>
    </div>
	<!-- Profile Info End -->

</div>