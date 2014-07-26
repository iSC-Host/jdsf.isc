<?php /* Smarty version 2.6.26, created on 2014-03-12 12:36:03
         compiled from file:style/newcunity/templates/galleries/upload.tpl.html */ ?>
<div class="bar">
	<div class="bar-sub-a"></div>
	<div class="bar-sub-b">
		<h1><?php echo $this->_tpl_vars['TITLE']; ?>
</h1>
	</div>
	<div class="bar-sub-c"></div>
</div>
<script language="javascript" type="text/javascript">$(document).ready(function() {
	$('#fileupload').uploadify({
		buttonClass   : 'jui-button',
		buttonText    : 'Select Files',
		height        : 23,
		width         : 100,
		fileTypeDesc  : 'Image Files',
        fileTypeExts  : '*.gif; *.jpg; *.png',
        swf           : 'includes/uploadify/uploadify.swf',
        uploader      : 'controllers/ajaxGalleriesController.php?session_id=<?php echo $this->_tpl_vars['SESSION_ID']; ?>
',
        formData      : {'c':'uploadfile','id':'<?php echo $this->_tpl_vars['ALBUM_ID']; ?>
','multi':'true'},
        fileObjName   : 'fu',        
        fileSizeLimit : '5MB',
        auto  		  : false,
        queueID       : "list"/*,
        onFallback    : function(){
        	location.href='galleries.php?c=single_upload&id=<?php echo $this->_tpl_vars['ALBUM_ID']; ?>
';
        }*/
	})
});</script>
<div class="box-main-a-1"></div>
<div class="box-main-a-2 options-a-2">
    <button class="jui-button" onclick="location.href='galleries.php?c=show_album&id=<?php echo $this->_tpl_vars['ALBUM_ID']; ?>
'"><?php echo $this->_tpl_vars['galleries_upload_back_album']; ?>
</button>
    <button class="jui-button" onclick="location.href='galleries.php?c=single_upload&id=<?php echo $this->_tpl_vars['ALBUM_ID']; ?>
'"><?php echo $this->_tpl_vars['galleries_upload_single_uploader']; ?>
</button>
</div>
<div class="box-main-a-3"></div>    

<div class="box-main-a-1" style="margin-top: 10px;"></div>
<div class="box-main-a-2 options-a-2">
	<div style="width:100%;height:90px">
    <p><a><?php echo $this->_tpl_vars['galleries_space']; ?>
</a> :</p>
    <div id="progressbar" style="width:200px; height:17px;float: left; margin-left: 5px;"></div>
    <div class="clear"></div>
    <script>
        $(document).ready(function() {
          $("#progressbar").progressbar({ value: <?php echo $this->_tpl_vars['percentage']; ?>
 });
        });
    </script>
              
    <span id="space_left" style="font-size: 11px; color: #0391c1; margin-left: 135px;"><?php echo $this->_tpl_vars['left']; ?>
MB <?php echo $this->_tpl_vars['galleries_space_left']; ?>
! (~ <?php echo $this->_tpl_vars['photos_left']; ?>
)</span>    
    <p><?php echo $this->_tpl_vars['galleries_upload_desc']; ?>
</p>
    <p><?php echo $this->_tpl_vars['galleries_upload_please_note']; ?>
</p>
    <p>Max Size: <?php echo $this->_tpl_vars['MAX_SIZE']; ?>
</p>
	</div>    
	<div style="border:1px solid #ccc;border-radius:7px;padding:10px">
		<button id="fileupload"></button>
		<button class="jui-button" onclick="$('#fileupload').uploadify('upload','*');">Upload files</button>
		<div id="list"></div>
	</div>
</div>
<div class="box-main-a-3"></div>