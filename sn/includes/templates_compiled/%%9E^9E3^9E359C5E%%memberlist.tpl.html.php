<?php /* Smarty version 2.6.26, created on 2014-03-11 21:23:34
         compiled from file:style/newcunity/templates/memberlist.tpl.html */ ?>
<script language="javascript" type="text/javascript">$("document").ready(function(){
    $(".photo_available")
        .live('mouseover', function(){
            var el = this;
            $("#preview_"+$(el).attr('id')).show();
        })
        .live('mouseout', function(){
            var el = this;
            $("#preview_"+$(el).attr('id')).hide();
        });
});</script>
<div class="bar">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1><?php echo $this->_tpl_vars['memberlist_users']; ?>
</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<table class="grid-share" summary="Gird Share File">
	<thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col"><a href="members.php?s=nickname"><?php echo $this->_tpl_vars['memberlist_user_name']; ?>
</a></th>
			<th scope="col"><a href="members.php?s=firstname"><?php echo $this->_tpl_vars['memberlist_first_name']; ?>
</a></th>
			<th scope="col"><a href="members.php?s=lastname"><?php echo $this->_tpl_vars['memberlist_last_name']; ?>
</a></th>
			<th scope="col"><a href="members.php?s=town"><?php echo $this->_tpl_vars['memberlist_city']; ?>
</a></th>
			<th scope="col"><a href="members.php?s=registered"><?php echo $this->_tpl_vars['memberlist_registered']; ?>
</a></th>
		</tr>
	</thead>
    <?php echo $this->_tpl_vars['LIST']; ?>

</table>