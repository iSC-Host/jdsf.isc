<?php /* Smarty version 2.6.26, created on 2014-03-11 21:23:32
         compiled from file:style/newcunity/templates/galleries/galleries.tpl.html */ ?>
<div class="bar">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1><?php echo $this->_tpl_vars['TITLE']; ?>
</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<?php if ($this->_tpl_vars['own']): ?>
<div class="options-a">
	<div class="box-main-a-1"></div>
	<div class="box-main-a-2 options-a-2">
    	<button class="jui-button" icon="ui-icon-plus" onclick="location.href='galleries.php?c=new_album'">
            <?php echo $this->_tpl_vars['galleries_new_album']; ?>

        </button>
        <button class="jui-button" icon="ui-icon-person" onclick="location.href='galleries.php?c=galleries&list=own'">
            <?php echo $this->_tpl_vars['galleries_list_my']; ?>

        </button>
        <button class="jui-button" icon="ui-icon-image" onclick="location.href='galleries.php?c=galleries&list=all'">
            <?php echo $this->_tpl_vars['galleries_list_all']; ?>

        </button>
        <button class="jui-button" icon="ui-icon-heart" onclick="location.href='galleries.php?c=galleries&list=friends'">
            <?php echo $this->_tpl_vars['galleries_list_friends']; ?>

        </button>
    	<div class="clear"></div>
	</div>
	<div class="box-main-a-3"></div>
</div>
<?php endif; ?>
<div id="gallery"><?php echo $this->_tpl_vars['GALLERIES']; ?>
</div>