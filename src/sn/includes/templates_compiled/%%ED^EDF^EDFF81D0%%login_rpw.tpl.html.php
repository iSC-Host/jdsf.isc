<?php /* Smarty version 2.6.26, created on 2014-02-07 15:24:47
         compiled from file:style/newcunity/templates/login_rpw.tpl.html */ ?>
<div class="bar">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1><?php echo $this->_tpl_vars['login_rpw_new_pw']; ?>
</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<div class="box-main-a-1"></div>
<div class="box-main-a-2 options-a-2">
    <p><?php echo $this->_tpl_vars['login_rpw_reset']; ?>
</p>
</div>
<div class="box-main-a-3"></div>
<form action="register.php?c=resetpw" method="POST">
<div class="form_error"><?php echo $this->_tpl_vars['MSG']; ?>
</div>

<div class="box-main-a-1" style="margin-top: 10px;"></div>
<div class="box-main-a-2 options-a-2">
    <p><?php echo $this->_tpl_vars['login_rpw_email']; ?>
</p>
    <input type="text" name="email" value="">
    <input class="jui-button" type="submit" value="<?php echo $this->_tpl_vars['login_rpw_reset_button']; ?>
" style="float: right;">
</div>
<div class="box-main-a-3"></div>	
</form>