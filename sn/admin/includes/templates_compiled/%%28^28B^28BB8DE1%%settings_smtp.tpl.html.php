<?php /* Smarty version 2.6.26, created on 2014-03-12 12:51:42
         compiled from file:style/default/templates/settings_smtp.tpl.html */ ?>

<script type="text/javascript">

    $(document).ready(function() {
        // Handler for .ready() called.  	
        hideSmtpSettings();
        $('select[name=mailOptions]').click(function() {
            hideSmtpSettings();
        });
        $('input[name=testMail]').click(function() {
            apprise("<?php echo $this->_tpl_vars['admin_settings_mail_test_enter_email']; ?>
", {'input': true}, function(r) {
                if (typeof(r) === "string") {
                    $.ajax({
                        type: "POST",
                        url: "controllers/mailController.php?act=sendTestMail&email=" + r + "&host=" + $('input[name=host]').val() + "&port=" + $('input[name=port]').val() + "&mailMethod=" + $('select[name=mailOptions]').val() + "&isAuth=" + $('select[name=isAuthOption]').val() + "&username=" + $('input[name=username]').val() + "&password=" + $('input[name=password]').val() + "&senderName=" + $('input[name=sender_name]').val() + "&senderAddres=" + $('input[name=sender_address]').val(),
                        data: '{"action":"sendTestMail","to":"' + r + '"}',
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        success: function(msg) {

                            if (msg.status == "1") {

                                apprise("<?php echo $this->_tpl_vars['admin_settings_mail_test_sent_success']; ?>
");

                            } else {
                                apprise(msg.error);
                            }
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(textStatus);
                            alert(errorThrown);
                        }
                    });
                }
                else
                {

                }
            });

        });
    });
    function hideSmtpSettings()
    {

        if ($('select[name=mailOptions]').val() !== "smtp")
        {
            $('.smtpSettings').hide();
        } else
        {

            $('.smtpSettings').show();
        }
    }
</script>

<h3><?php echo $this->_tpl_vars['admin_submenu_smtp']; ?>
</h1>
<form method="POST" action="settings.php?c=smtp">
    <h4 style="border-bottom: 1px solid #000; width: 300px;"><?php echo $this->_tpl_vars['admin_settings_smtp_settings']; ?>
</h4>
    <table class="nottoowidth">
        <div class="infobox"><img width="25px" height="25px" style="vertical-align: middle; margin-right: 10px;" src="style/default/img/information.png"><span style="vertical-align: middle;"><?php echo $this->_tpl_vars['admin_settings_mail_mail_note']; ?>
</span></div>
        <tr>
            <td><?php echo $this->_tpl_vars['admin_settings_smtp_host']; ?>
: </td>
            <td><input name="host" type="text" value="<?php echo $this->_tpl_vars['host']; ?>
"/></td>
        </tr>
        <tr>
            <td><?php echo $this->_tpl_vars['admin_settings_smtp_port']; ?>
: </td>
            <td><input name="port" type="text" value="<?php echo $this->_tpl_vars['port']; ?>
"/></td>
        </tr>
        <tr >
            <td><?php echo $this->_tpl_vars['admin_settings_mail_options']; ?>
: </td>
            <td>
                <select name="mailOptions">
                    <?php echo $this->_tpl_vars['mail_selected_options']; ?>

                </select>
            </td>
        </tr>
        <tr class="smtpSettings">
            <td><?php echo $this->_tpl_vars['admin_settings_smtp_is_authentication']; ?>
: </td>
            <td><select name="isAuthOption">
                    <?php echo $this->_tpl_vars['smtp_selected_auth']; ?>
			
                </select> </td>
        </tr>
        <tr class="smtpSettings">
            <td><?php echo $this->_tpl_vars['admin_settings_smtp_username']; ?>
: </td>
            <td><input name="username" type="text" value="<?php echo $this->_tpl_vars['username']; ?>
"/></td>
        </tr>
        <tr class="smtpSettings">
            <td><?php echo $this->_tpl_vars['admin_settings_smtp_password']; ?>
: </td>
            <td><input name="password" type="password" value="<?php echo $this->_tpl_vars['password']; ?>
"/></td>
        </tr>
        <tr>
            <td><?php echo $this->_tpl_vars['admin_settings_smtp_sender_name']; ?>
: </td>
            <td><input name="sender_name" type="text" value="<?php echo $this->_tpl_vars['sender_name']; ?>
"/></td>
        </tr>
        <tr>
            <td><?php echo $this->_tpl_vars['admin_settings_smtp_sender_address']; ?>
: </td>
            <td><input name="sender_address" type="text" value="<?php echo $this->_tpl_vars['sender_address']; ?>
"/></td>
        </tr>

    </table>

    <h4 style="border-bottom: 1px solid #000; width: 300px;"><?php echo $this->_tpl_vars['admin_settings_smtp_mail_design']; ?>
</h4>
    <script type="text/javascript" src="../includes/nicedit/nicEdit-latest.js"></script>
    <script> bkLib.onDomLoaded(function() {
            var myEditor = new nicEditor({fullPanel: true}).panelInstance('mail_header');
            var myEditor = new nicEditor({fullPanel: true}).panelInstance('mail_footer');
        });</script>
    <p><?php echo $this->_tpl_vars['admin_settings_smtp_mail_header']; ?>
</p>
    <textarea name="mail_header" id="mail_header" style="width: 700px; height: 200px;"><?php echo $this->_tpl_vars['email_header']; ?>
</textarea>
    <p><?php echo $this->_tpl_vars['admin_settings_smtp_mail_footer']; ?>
</p>
    <textarea name="mail_footer" id="mail_footer" style="width: 700px; height: 200px;"><?php echo $this->_tpl_vars['email_footer']; ?>
</textarea>
    <input type="submit" name="save" class="jui-button" value="<?php echo $this->_tpl_vars['admin_settings_mail_btn_submit']; ?>
"/>&nbsp;&nbsp;<input type="button" name="testMail" value="<?php echo $this->_tpl_vars['admin_settings_mail_btn_send']; ?>
" class="jui-button"/>
</form>