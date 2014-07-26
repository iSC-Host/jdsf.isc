<?php /* Smarty version 2.6.26, created on 2014-03-12 12:35:42
         compiled from file:style/newcunity/templates/galleries/album_new.tpl.html */ ?>
<div class="bar">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1><?php echo $this->_tpl_vars['TITLE']; ?>
</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<div class="box-main-a-1"></div>
<div class="box-main-a-2 options-a-2">
    <button class="jui-button" icon="ui-icon-triangle-1-w" onclick="location.href='galleries.php'" id="galleryback"><?php echo $this->_tpl_vars['galleries_back']; ?>
</button>
</div>
<div class="box-main-a-3" style="margin-botttom:10px"></div>
<div><?php echo $this->_tpl_vars['ERRORS']; ?>
</div>
<form action="galleries.php?c=new_album" method="POST">
    <div class="info_line">
        <div class="info_label"><?php echo $this->_tpl_vars['galleries_new_name']; ?>
:</div>
        <span class="info_value"><input type="text" name="album_name" id="album_name" style="width: 250px;" maxlength="25"/></span>
    </div>
    <div class="info_line">
        <div class="info_label" style="vertical-align: top;"><?php echo $this->_tpl_vars['galleries_new_desc']; ?>
:</div>
        <span class="info_value"><textarea name="album_descr" id="album_descr" maxlength="250" style="width: 250px; height: 75px;"></textarea></span>
    </div>
    <div class="info_line">
        <div class="info_label" style="vertical-align: top;"><?php echo $this->_tpl_vars['galleries_visibility']; ?>
:</div>
        <span class="info_value">
            <div style="padding: 2px;">
            <input type="radio" name="album_privacy" value="0" id="opt1" checked="checked"/>
                    <label for="opt1"><?php echo $this->_tpl_vars['galleries_show_me']; ?>
</label>
			</div>
			<div style="padding: 2px;">
                <input type="radio" name="album_privacy" value="1" id="opt2"/>
                    <label for="opt2"><?php echo $this->_tpl_vars['galleries_show_friends']; ?>
</label>
			</div>
			<div style="padding: 2px;">
                <input type="radio" name="album_privacy" value="2" id="opt3"/>
                    <label for="opt3"><?php echo $this->_tpl_vars['galleries_show_all_users']; ?>
</label></span>
            </div>			
    </div>
    <div class="info_line">
        <div class="info_label" style="vertical-align: top;"><?php echo $this->_tpl_vars['galleries_space']; ?>
:</div>
        <span class="info_value">
            <div id="progressbar" style="width:200px; height:15px"></div>
            <script language="javascript">$(document).ready(function() {$("#progressbar").progressbar({ value: <?php echo $this->_tpl_vars['percentage']; ?>
 });});</script>
            <span id="space_left" style="font-size: 11px; color: #0391c1;"><?php echo $this->_tpl_vars['left']; ?>
MB <?php echo $this->_tpl_vars['galleries_space_left']; ?>
! (~ <?php echo $this->_tpl_vars['photos_left']; ?>
)</span>
        </span>
    </div>
        <div style="margin-top: 10px; margin-left: 345px;">        
		<input class="jui-button" name="send" type="submit" value="<?php echo $this->_tpl_vars['galleries_create']; ?>
"/>
		</div>
		
</form>