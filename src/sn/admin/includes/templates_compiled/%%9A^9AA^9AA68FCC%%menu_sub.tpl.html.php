<?php /* Smarty version 2.6.26, created on 2014-03-11 21:25:42
         compiled from file:style/default/templates/menu_sub.tpl.html */ ?>
<ul id="submenu">

    <?php if ($this->_tpl_vars['index']): ?>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['overview']): ?>
		<li <?php echo $this->_tpl_vars['stats_sub']; ?>
><a href="overview.php?c=stats"><?php echo $this->_tpl_vars['admin_submenu_statistics']; ?>
</a></li>
		<!-- <li <?php echo $this->_tpl_vars['update_sub']; ?>
><a href="update.php"><?php echo $this->_tpl_vars['admin_submenu_updates']; ?>
</a></li> -->
	<?php endif; ?>

	<?php if ($this->_tpl_vars['round_mail']): ?>
		<li <?php echo $this->_tpl_vars['overview_sub']; ?>
><a href="round_mail.php?c=overview"><?php echo $this->_tpl_vars['admin_submenu_overview']; ?>
</a></li>
		<li <?php echo $this->_tpl_vars['new_sub']; ?>
><a href="round_mail.php?c=new"><?php echo $this->_tpl_vars['admin_submenu_new']; ?>
</a></li>
	<?php endif; ?>


	<?php if ($this->_tpl_vars['users']): ?>
		<li <?php echo $this->_tpl_vars['general_sub']; ?>
><a href="users.php?c=general"><?php echo $this->_tpl_vars['admin_submenu_general']; ?>
</a></li>
		<li <?php echo $this->_tpl_vars['list_sub']; ?>
><a href="users.php?c=list"><?php echo $this->_tpl_vars['admin_submenu_user_list']; ?>
</a></li>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['settings']): ?>
		<li <?php echo $this->_tpl_vars['general_sub']; ?>
><a href="settings.php?c=general"><?php echo $this->_tpl_vars['admin_submenu_general']; ?>
</a></li>
		<li <?php echo $this->_tpl_vars['page_layout_sub']; ?>
><a href="settings.php?c=page_layout"><?php echo $this->_tpl_vars['admin_submenu_page_layout']; ?>
</a></li>
		<li <?php echo $this->_tpl_vars['menu_sub']; ?>
><a href="settings.php?c=menu"><?php echo $this->_tpl_vars['admin_submenu_menu']; ?>
</a></li>
		<li <?php echo $this->_tpl_vars['language_sub']; ?>
><a href="settings.php?c=language"><?php echo $this->_tpl_vars['admin_submenu_language']; ?>
</a></li>
		<li <?php echo $this->_tpl_vars['smtp_sub']; ?>
><a href="settings.php?c=smtp"><?php echo $this->_tpl_vars['admin_submenu_smtp']; ?>
</a></li>
		<li <?php echo $this->_tpl_vars['pages_sub']; ?>
><a href="settings.php?c=pages"><?php echo $this->_tpl_vars['admin_submenu_terms']; ?>
</a></li>
	<?php endif; ?>

    <?php if ($this->_tpl_vars['opencunity']): ?>
        <li <?php echo $this->_tpl_vars['general_sub']; ?>
><a href="opencunity.php"><?php echo $this->_tpl_vars['admin_submenu_general']; ?>
</a></li>
    <?php endif; ?>
    
	<?php if ($this->_tpl_vars['registration']): ?>
	    <li <?php echo $this->_tpl_vars['general_sub']; ?>
><a href="registration.php?c=general"><?php echo $this->_tpl_vars['admin_submenu_general']; ?>
</a></li>
		<li <?php echo $this->_tpl_vars['fields_sub']; ?>
><a href="registration.php?c=fields"><?php echo $this->_tpl_vars['admin_submenu_fields']; ?>
</a></li>
	<?php endif; ?>

    <?php if ($this->_tpl_vars['modules']): ?>
		<li <?php echo $this->_tpl_vars['overview_sub']; ?>
><a href="modules.php?c=overview"><?php echo $this->_tpl_vars['admin_submenu_overview']; ?>
</a></li>
		<li <?php echo $this->_tpl_vars['fshare_sub']; ?>
><a href="modules.php?c=fshare"><?php echo $this->_tpl_vars['admin_submenu_filesharing']; ?>
</a></li>
		<li <?php echo $this->_tpl_vars['friends_sub']; ?>
><a href="modules.php?c=friends"><?php echo $this->_tpl_vars['admin_submenu_friends']; ?>
</a></li>
		<li <?php echo $this->_tpl_vars['chat_sub']; ?>
><a href="modules.php?c=chat"><?php echo $this->_tpl_vars['admin_submenu_chat']; ?>
</a></li>
	<?php endif; ?>
	<?php if ($this->_tpl_vars['update']): ?>
	    <li><a href="overview.php?c=stats"><?php echo $this->_tpl_vars['admin_submenu_statistics']; ?>
</a></li>
		<li class="active"><a href="update.php"><?php echo $this->_tpl_vars['admin_submenu_updates']; ?>
</a></li>
	<?php endif; ?>

</ul>

<div id="content">
	<div class="padder" id="mainpadder">