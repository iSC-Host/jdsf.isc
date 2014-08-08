$(document).ready(function() {
	$('#fileupload').uploadify({
		buttonClass   : 'jui-button',
		buttonText    : 'Select Files',
		height        : 23,
		width         : 100,
		fileTypeDesc  : 'Image Files',
        fileTypeExts  : '{-$ALLOWED_FILETYPES}',
        swf           : 'includes/uploadify/uploadify.swf',
        uploader      : 'controllers/ajaxFileShareController.php',
        formData      : {'c':'uploadfile','multi':true},
        fileObjName   : 'fu',
        fileSizeLimit : '5MB',
        auto  		  : false,
        queueID       : "list",
        onFallback    : function(){
            $("#multiuploader").html("<div class=\"message_red\">{-$filesharing_no_flash}</div>");
        },
        onUploadSuccess: function(file,data,response){
            $("#progressbar").progressbar('value',data);
        }
	})
});