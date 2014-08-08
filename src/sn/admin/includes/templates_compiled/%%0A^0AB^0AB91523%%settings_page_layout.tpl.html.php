<?php /* Smarty version 2.6.26, created on 2014-03-12 12:41:11
         compiled from file:style/default/templates/settings_page_layout.tpl.html */ ?>
<h3><?php echo $this->_tpl_vars['admin_settings_land_page']; ?>
</h3>
<?php echo $this->_tpl_vars['MSG']; ?>

<script type="text/javascript" src="../includes/nicedit/nicEdit-latest.js"></script>
<script> bkLib.onDomLoaded(function(){var myEditor = new nicEditor({fullPanel : true }).panelInstance('landing_body'); var myEditor2 = new nicEditor({fullPanel : true }).panelInstance('header_body');});</script>
<h4 style="border-bottom: 1px solid black; width: 300px;"><?php echo $this->_tpl_vars['admin_settings_header_edit']; ?>
</h4>
<p><?php echo $this->_tpl_vars['admin_settings_header_info']; ?>
</p>
<form method="POST" action="settings.php?c=page_layout">
    <textarea name="header_body" id="header_body" style="width: 900px; height: 100px;"><?php echo $this->_tpl_vars['HEADER']; ?>
</textarea>
    <h4 style="border-bottom: 1px solid black; width: 300px;"><?php echo $this->_tpl_vars['admin_settings_land_page_edit']; ?>
</h4>
    <p><?php echo $this->_tpl_vars['admin_settings_land_page_info']; ?>
</p>
    <textarea name="landing_body" id="landing_body" rows="25" cols="70"><?php echo $this->_tpl_vars['BODY']; ?>
</textarea>
    <p></p>
    <input type="submit" value="<?php echo $this->_tpl_vars['admin_settings_save']; ?>
" name="save" class="jui-button"/>
</form>