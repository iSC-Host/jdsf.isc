<?php /* Smarty version 2.6.26, created on 2014-03-11 21:26:14
         compiled from file:style/default/templates/settings_general.tpl.html */ ?>
<h3><?php echo $this->_tpl_vars['admin_settings_general_settings']; ?>
</h3>

<div class="padder message"><?php echo $this->_tpl_vars['MSG']; ?>
</div>

<form method="POST" action="settings.php?c=general">

<table>

	<tr>
		<th colspan="3" class="tab_headline"><?php echo $this->_tpl_vars['admin_settings_general_website']; ?>
</th>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_settings_general_website_name']; ?>
</th>
		<td class="padder tab_value"><input type="text" name="name" value="<?php echo $this->_tpl_vars['NAME']; ?>
"></td>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['admin_settings_general_website_expl']; ?>

		<br><strong><?php echo $this->_tpl_vars['admin_settings_general_example']; ?>
</strong> <?php echo $this->_tpl_vars['admin_settings_general_car_expl1']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_settings_general_slogan']; ?>
</th>
		<td class="padder tab_value"><input type="text" name="slogan" value="<?php echo $this->_tpl_vars['SLOGAN']; ?>
"></td>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['admin_settings_general_text']; ?>

		<br><strong><?php echo $this->_tpl_vars['admin_settings_general_example']; ?>
</strong> <?php echo $this->_tpl_vars['admin_settings_general_car_expl2']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_settings_general_design']; ?>
</th>
		<td class="padder tab_value">
        <select name="design" style="width: 186px;">
			<?php echo $this->_tpl_vars['DESIGNS']; ?>

		</select>
        <a href="javascript:void(0)" target="_blank" onclick="this.href='../pinboard.php?preview='+document.getElementsByName('design')[0].value;" style="vertical-align: middle;"><img src="style/default/img/preview.png" title="<?php echo $this->_tpl_vars['admin_settings_general_preview']; ?>
"/></a>		
		<td class="padder tab_value"><?php echo $this->_tpl_vars['admin_settings_general_design_info']; ?>
</td>
	</tr>	
	<tr>
		<th class="padder tab_label" style="padding: 0px 10px;">Design-Switch</th>
		<td class="padder tab_value" colspan="2" style="padding: 0px 5px;">
        <input type="checkbox" name="designswitch" <?php echo $this->_tpl_vars['DESIGNSWITCH']; ?>
 id="designswitch" style="width: auto;" value="1"/>
        <label for="designswitch"><?php echo $this->_tpl_vars['admin_settings_general_design_switch']; ?>
</label>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_settings_general_admin_design']; ?>
</th>
		<td class="padder tab_value">
            <select name="admindesign" style="width: 186px;">
    			<?php echo $this->_tpl_vars['ADMIN_DESIGNS']; ?>

    		</select>
    		<a href="javascript:void(0)" target="_blank" onclick="this.href='overview.php?preview='+document.getElementsByName('admindesign')[0].value;" style="vertical-align: middle;"><img src="style/default/img/preview.png" title="<?php echo $this->_tpl_vars['admin_settings_general_preview']; ?>
"/></a>
		</td>		
		<td class="padder tab_value"><?php echo $this->_tpl_vars['admin_settings_general_admin_design_info']; ?>
</td>
	</tr>
	<tr>
		<td colspan="3" class="tab_spacer">&nbsp;</td>
	</tr>
	<tr>
		<th colspan="3" class="tab_headline"><?php echo $this->_tpl_vars['admin_settings_general_contact']; ?>
</th>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_settings_general_email']; ?>
</th>
		<td class="padder tab_value"><input type="text" name="mail" value="<?php echo $this->_tpl_vars['MAIL']; ?>
"/></td>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['admin_settings_general_admin_email']; ?>
</td>
	</tr>
	<tr>
		<td colspan="3" class="tab_spacer">&nbsp;</td>
	</tr>



	<tr>
		<th colspan="3" class="tab_headline"><?php echo $this->_tpl_vars['admin_settings_general_save']; ?>
</th>
	</tr>
	<tr>
		<td colspan="3" class="padder centered">
			<input type="submit" value="<?php echo $this->_tpl_vars['admin_settings_general_save']; ?>
" name="save" class="jui-button"/>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="tab_spacer">&nbsp;</td>
	</tr>

</table>

</form>