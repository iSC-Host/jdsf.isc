<?php /* Smarty version 2.6.26, created on 2014-03-11 21:25:42
         compiled from file:style/default/templates/login.tpl.html */ ?>
<h3><?php echo $this->_tpl_vars['admin_login_confirm']; ?>
</h3>

<div class="padder message"><?php echo $this->_tpl_vars['MSG']; ?>
</div>

<form action="index.php" method="POST">

	<table class="nottoowidth">
		<tr><td><?php echo $this->_tpl_vars['admin_login_email']; ?>
</td><td><input type="text" name="email" value=""/></td></tr>
		<tr><td><?php echo $this->_tpl_vars['admin_login_password']; ?>
</td><td><input type="password" name="pass" value=""/></td></tr>
		<tr><td colspan="2" class ="padder" style="text-align:right"><input type="submit" name="admin" value="Login" class="jui-button"/></td></tr>
	</table>

</form>