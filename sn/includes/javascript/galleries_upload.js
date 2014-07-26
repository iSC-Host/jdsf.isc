$(document).ready(function() {
	$('#fileupload').uploadify({
		buttonClass   : 'jui-button',
		buttonText    : 'Select Files',
		height        : 23,
		width         : 100,
		fileTypeDesc  : 'Image Files',
        fileTypeExts  : '*.gif; *.jpg; *.png',
        swf           : 'includes/uploadify/uploadify.swf',
        uploader      : 'controllers/ajaxGalleriesController.php?session_id={-$SESSION_ID}',
        formData      : {'c':'uploadfile','id':'{-$ALBUM_ID}','multi':'true'},
        fileObjName   : 'fu',        
        fileSizeLimit : '5MB',
        auto  		  : false,
        queueID       : "list"/*,
        onFallback    : function(){
        	location.href='galleries.php?c=single_upload&id={-$ALBUM_ID}';
        }*/
	})
});