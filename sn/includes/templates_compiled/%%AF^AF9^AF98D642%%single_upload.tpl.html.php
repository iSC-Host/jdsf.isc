<?php /* Smarty version 2.6.26, created on 2014-03-12 12:36:21
         compiled from file:style/newcunity/templates/galleries/single_upload.tpl.html */ ?>
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
    <button class="jui-button" onclick="location.href='galleries.php?c=show_album&id=<?php echo $this->_tpl_vars['ALBUM_ID']; ?>
'"><?php echo $this->_tpl_vars['galleries_upload_back_album']; ?>
</button>
    <button class="jui-button" onclick="location.href='galleries.php?c=upload&id=<?php echo $this->_tpl_vars['ALBUM_ID']; ?>
'"><?php echo $this->_tpl_vars['galleries_upload_multi_uploader']; ?>
</button>
</div>
<div class="box-main-a-3"></div>

<div class="box-main-a-1" style="margin-top: 10px;"></div>
<div class="box-main-a-2 options-a-2">
    <a href="#"><?php echo $this->_tpl_vars['galleries_space']; ?>
</a> :
    <span id="space_left"><?php echo $this->_tpl_vars['left']; ?>
MB <?php echo $this->_tpl_vars['galleries_space_left']; ?>
! (~ <?php echo $this->_tpl_vars['photos_left']; ?>
)</span>
    <div id="progressbar" style="width:200px; height:17px"></div>
    <script>
        $(document).ready(function() {
          $("#progressbar").progressbar({ value: <?php echo $this->_tpl_vars['percentage']; ?>
 });
        });
    </script>
    <div><p><?php echo $this->_tpl_vars['galleries_upload_single_desc']; ?>
</p></div>
</div>
<div class="box-main-a-3"></div>
<div class="box-main-a-1" style="margin-top: 10px;"></div>
<div class="box-main-a-2">
    <form action="controllers/ajaxGalleriesController.php" name="form_fu1" method="POST" target="ifu1" enctype="multipart/form-data" style="padding: 7px 0px; border-bottom: 1px solid #ddd;">
		<input id="fu1" name="fu" type="file" style="border: 0px;"/>
		<span id="fu1_sub"></span>
		<input type="hidden" name="form" value="1"/>
		<input type="hidden" name="c" value="uploadfile" />
		<input type="hidden" name="id" value="<?php echo $this->_tpl_vars['ALBUM_ID']; ?>
" />
		<iframe id="ifu1" name="ifu1" style="display: none;"></iframe>
	</form>

	<form action="controllers/ajaxGalleriesController.php" name="form_fu2" method="POST" target="ifu2" enctype="multipart/form-data" style="padding: 7px 0px; border-bottom: 1px solid #ddd;">
		<input id="fu2" name="fu" type="file" style="border: 0px;"/>
		<span id="fu2_sub"></span>
		<input type="hidden" name="form" value="2"/>
		<input type="hidden" name="c" value="uploadfile" />
		<input type="hidden" name="id" value="<?php echo $this->_tpl_vars['ALBUM_ID']; ?>
" />
		<iframe id="ifu2" name="ifu2" style="display: none;"></iframe>
	</form>

	<form action="controllers/ajaxGalleriesController.php" name="form_fu3" method="POST" target="ifu3" enctype="multipart/form-data" style="padding: 7px 0px;">
		<input id="fu3" name="fu" type="file" style="border: 0px;"/>
		<span id="fu3_sub"></span>
		<input type="hidden" name="form" value="3"/>
		<input type="hidden" name="c" value="uploadfile" />
		<input type="hidden" name="id" value="<?php echo $this->_tpl_vars['ALBUM_ID']; ?>
" />
		<iframe id="ifu3" name="ifu3" style="display: none;"></iframe>
	</form>
</div>
<div class="box-main-a-3"></div>

<div id="uploaded_wrap" style="display: none;">
<div class="bar" style="margin-top: 10px;">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1><?php echo $this->_tpl_vars['galleries_uploaded']; ?>
:</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<div class="box-main-a-1" style="margin-top: 10px;"></div>
<div class="box-main-a-2 options-a-2" id="uploaded_files">

</div>
<div class="box-main-a-3"></div>
</div>
	<script language="javascript" type="text/javascript">var sent1;
var sent2;
var sent3;
jQuery('#fu1').change(function() {
	document.forms['form_fu1'].submit();

	$('#fu1_sub').html('<?php echo $this->_tpl_vars['LOADING']; ?>
');
	$('#fu1').attr('disabled', 'disabled');
});

jQuery('#fu2').change(function() {
	document.forms['form_fu2'].submit();

	$('#fu2_sub').html('<?php echo $this->_tpl_vars['LOADING']; ?>
');
	$('#fu2').attr('disabled', 'disabled');
});

jQuery('#fu3').change(function() {
	document.forms['form_fu3'].submit();

	$('#fu3_sub').html('<?php echo $this->_tpl_vars['LOADING']; ?>
');
	$('#fu3').attr('disabled', 'disabled');
});</script>	