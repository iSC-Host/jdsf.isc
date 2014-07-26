<?php /* Smarty version 2.6.26, created on 2014-03-12 12:51:41
         compiled from file:style/default/templates/settings_pages.tpl.html */ ?>
<script type="text/javascript" src="../includes/nicedit/nicEdit-latest.js"></script><script> bkLib.onDomLoaded(function(){var myEditor = new nicEditor({fullPanel : true }).panelInstance('imprint');});</script>
<form method="POST" action="settings.php?c=pages">
<?php echo $this->_tpl_vars['admin_pages_title_terms']; ?>
: <input name="terms_title" type="text" value="<?php echo $this->_tpl_vars['terms_title']; ?>
"><br>
<textarea name="terms" cols="50" rows="10"><?php echo $this->_tpl_vars['terms']; ?>
</textarea>
<br>
<br>
<?php echo $this->_tpl_vars['admin_pages_title_privacy']; ?>
: <input name="privacy_title" type="text" value="<?php echo $this->_tpl_vars['privacy_title']; ?>
"><br>
<textarea name="privacy" cols="50" rows="10"><?php echo $this->_tpl_vars['privacy']; ?>
</textarea>
<br><br>
<?php echo $this->_tpl_vars['admin_pages_title_imprint']; ?>
: <input name="imprint_title" type="text" value="<?php echo $this->_tpl_vars['imprint_title']; ?>
"><br>
<textarea id="imprint" name="imprint" cols="50" rows="10"><?php echo $this->_tpl_vars['imprint']; ?>
</textarea><br>
<input type="submit" value="<?php echo $this->_tpl_vars['admin_settings_save']; ?>
" name="save" class="jui-button">
</form>