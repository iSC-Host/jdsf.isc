<?php /* Smarty version 2.6.26, created on 2014-03-11 21:26:39
         compiled from file:style/default/templates/round_mail.tpl.html */ ?>
<h3><?php echo $this->_tpl_vars['admin_round_mail_overview']; ?>
</h3>
<script language="javascript" type="text/javascript">
function confirmSave()
{
    conf = confirm('<?php echo $this->_tpl_vars['admin_round_mail_confirm_back']; ?>
');
    if(conf == true)
    {
        location.href = 'round_mail.php'
    }
      
}

$(document).ready(function(){
    $("#preview_button").click(function(){
        $(".message_cont").html($(".nicEdit-main").html());
        $("#preview_subject").append($("#subject").val());
        $(".message_border").fadeIn();
    });
    $(".preview_close").click(function(){
        $(".message_border").fadeOut();
    })
});
</script>

<?php if (NEW_MAIL): ?>
<script type="text/javascript" src="../includes/nicedit/nicEdit-latest.js"></script><script>bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });</script>
<form method="POST" action="round_mail.php">
<button class="jui-button" name="open_mail" onclick="confirmSave()"><?php echo $this->_tpl_vars['admin_round_mail_back_overview']; ?>
</button>
<p><?php echo $this->_tpl_vars['admin_round_mail_warning']; ?>
</p>

<div class="padder message"><?php echo $this->_tpl_vars['MSG']; ?>
</div>


<table>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_round_mail_subject']; ?>
</th>
		<td class="padder tab_value" style="font-size: medium;"><input type="text" id="subject" name="subj" value="<?php echo $this->_tpl_vars['SUBJECT']; ?>
"></td>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['admin_round_mail_subject_explain']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_round_mail_msg']; ?>
</th>
		<td colspan="2" class="padder tab_value" style="font-size: 14px;">
		<textarea name="body" class="newsarea" id="msg_body"><?php echo $this->_tpl_vars['MBODY']; ?>
</textarea>
        </td>
	</tr>
	<tr>
		<td colspan="3" class="tab_spacer">&nbsp;</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_round_mail_preview']; ?>
</th>
		<td class="padder tab_value">
			<input type="radio" class="input_radio" name="preview" value="yes" id="preview_yes" checked="checked"/> <label for="preview_yes"><?php echo $this->_tpl_vars['admin_round_mail_yes']; ?>
</label>
			<br><input type="radio" class="input_radio" name="preview" value="no" id="preview_no"/> <label for="preview_no"><?php echo $this->_tpl_vars['admin_round_mail_no']; ?>
</label>
		</td>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['admin_round_mail_send_explain']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_round_mail_allow_reply']; ?>
</th>
		<td class="padder tab_value">
			<input type="radio" class="input_radio" name="reply" value="yes" id="reply_yes"> <label for="reply_yes"><?php echo $this->_tpl_vars['admin_round_mail_yes']; ?>
</label>
			<br><input type="radio" class="input_radio" name="reply" value="no" checked="checked" id="reply_no"/> <label for="reply_no"><?php echo $this->_tpl_vars['admin_round_mail_no']; ?>
</label>
		</td>
		<td class="padder tab_value"><strong><?php echo $this->_tpl_vars['admin_round_mail_yes']; ?>
</strong>: <?php echo $this->_tpl_vars['admin_round_mail_sender']; ?>

		<br><strong><?php echo $this->_tpl_vars['admin_round_mail_no']; ?>
</strong>: no-reply@<?php echo $this->_tpl_vars['URL']; ?>
 <?php echo $this->_tpl_vars['admin_round_mail_sender2']; ?>
</td>
	</tr>
	<tr>
		<td colspan="3" class="tab_spacer"><input type="hidden" name="id" value="<?php echo $this->_tpl_vars['ID']; ?>
"/>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3" class="padder centered">
		    <input type="button" value="<?php echo $this->_tpl_vars['admin_round_mail_preview']; ?>
" name="preview" id="preview_button" class="jui-button"/>
            <input type="submit" value="<?php echo $this->_tpl_vars['admin_round_mail_send']; ?>
" name="send" class="jui-button"/>
            <input type="submit" value="<?php echo $this->_tpl_vars['admin_round_mail_save']; ?>
" name="save" class="jui-button"/>
        </td>
	</tr>
</table>
<div class="message_border" style="display: none;">
    <div class="message_window">
        <div class="message_header">
            <h4 style="float: left;"><?php echo $this->_tpl_vars['admin_round_mail_preview']; ?>
</h4>
            <h4 style="float: left; margin-left: 30px;" id="preview_subject"><?php echo $this->_tpl_vars['admin_round_mail_subject']; ?>
:&nbsp;</h4>
            <img src="style/default/img/close.png" class="preview_close" style="float: right; cursor: pointer;" title="<?php echo $this->_tpl_vars['admin_round_mail_close']; ?>
"/>
            <div class="clear"></div>
        </div>
        <div class="message_cont"></div>
        <div class="message_footer">
            <input type="submit" value="<?php echo $this->_tpl_vars['admin_round_mail_send']; ?>
" name="send" class="jui-button" style="width: 180px;"/>
            <input type="submit" value="<?php echo $this->_tpl_vars['admin_round_mail_save']; ?>
" name="save" class="jui-button" style="width: 180px;"/>
            <input type="button" value="<?php echo $this->_tpl_vars['admin_round_mail_close']; ?>
" class="jui-button preview_close" style="width: 180px;"/>
        </div>
    </div>
</div>
</form>
<?php endif; ?>
<?php if (MAIL_OVERVIEW): ?>
<?php echo $this->_tpl_vars['MSG']; ?>

<table border="1" style="border: 2px solid #FFFFFF;" id="mailList">
    <thead>
        <tr class="row_head">
            <th width="20px">#</th>
            <th><?php echo $this->_tpl_vars['admin_round_mail_subject']; ?>
</th>
            <th><?php echo $this->_tpl_vars['admin_round_mail_date']; ?>
</th>
            <th><?php echo $this->_tpl_vars['admin_round_mail_sent']; ?>
</th>
            <th colspan="2"><?php echo $this->_tpl_vars['admin_round_mail_action']; ?>
</th>
        </tr>
    </thead>
    <tbody>
        <?php echo $this->_tpl_vars['ROUND_MAILS']; ?>

    </tbody>
</table>
<?php endif; ?>