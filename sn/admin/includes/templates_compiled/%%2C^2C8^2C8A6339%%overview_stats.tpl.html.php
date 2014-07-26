<?php /* Smarty version 2.6.26, created on 2014-03-11 21:26:10
         compiled from file:style/default/templates/overview_stats.tpl.html */ ?>
<h3><?php echo $this->_tpl_vars['admin_overview_stats_overview']; ?>
</h3>

<?php echo $this->_tpl_vars['INFOBOX']; ?>

<!-- <?php echo $this->_tpl_vars['LASTLOGIN']; ?>
 -->

<table>
	<tr>
		<th colspan="2" class="tab_headline"><?php echo $this->_tpl_vars['admin_overview_stats_user']; ?>
</th>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_overview_stats_reg_users']; ?>
</th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['USER_COUNT']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label">
            <?php echo $this->_tpl_vars['admin_overview_stats_not_activated_users']; ?>
            
        </th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['NOT_ACTIVATED_USER_COUNT']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label">
            <?php echo $this->_tpl_vars['admin_overview_stats_inactive_users']; ?>

            <a class="info_link"><img src="style/default/img/info.gif" style="float: right;"/></a>
            <span class="hidden info_box"><?php echo $this->_tpl_vars['admin_overview_stats_inactive_info']; ?>
</span>
        </th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['INACTIVE_USER_COUNT']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_overview_stats_restricted_users']; ?>
</th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['BLOCKED_USER_COUNT']; ?>
</td>
	</tr>
	<!-- 
	<tr>
		<th colspan="2" class="tab_headline"><?php echo $this->_tpl_vars['admin_overview_stats_user_graph']; ?>
</th>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_overview_stats_profile_img']; ?>
</th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['PROFILE_IMGS_COUNT']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_overview_stats_profile_size']; ?>
</th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['PROFILE_IMGS_SIZE']; ?>
</td>
	</tr> -->
	<tr>
		<th colspan="2" class="tab_headline"><?php echo $this->_tpl_vars['admin_overview_stats_gallery']; ?>
</th>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_overview_stats_gallery_images']; ?>
</th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['GALLERY_IMG_COUNT']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_overview_stats_gallery_images_size']; ?>
</th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['GALLERY_IMG_SIZE']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_overview_stats_gallery_albums']; ?>
</th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['GALLERY_ALBUMS_COUNT']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_overview_stats_gallery_albums_average']; ?>
</th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['GALLERY_ALBUMS_AVERAGE']; ?>
</td>
	</tr>	
	<tr>
		<th colspan="2" class="tab_headline"><?php echo $this->_tpl_vars['admin_overview_stats_forum']; ?>
</th>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_overview_stats_forums']; ?>
</th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['FORUMS_COUNT']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_overview_stats_threads']; ?>
</th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['THREAD_COUNT']; ?>
</td>
	</tr>
	<tr>
		<th class="padder tab_label"><?php echo $this->_tpl_vars['admin_overview_stats_posts']; ?>
</th>
		<td class="padder tab_value"><?php echo $this->_tpl_vars['POST_COUNT']; ?>
</td>
	</tr>
</table>