$.fn.messageBox = function(options){
	options = $.extend({
	    name: '',
	    open: '',
		header: '',
		content: '',
		bottom: '',
		closeText: '',
		close: ''
	}, options);
 
	function createMessage(){
		messageBorder = $("<div>", {
                id: options.name,			
    			css: {				
    				display: "none"
    			}		
		    })
		    .addClass("message_border")
		    .appendTo("body");
		
		messageWindow = $("<div>")
		    .addClass("message_window")
		    .appendTo(messageBorder);
		
		messageHeader = $("<div>")
    		.addClass("message_header")
    		.appendTo(messageWindow);
		
		headline = $("<h4>")
            .addClass("message_header_head")        
            .appendTo(messageHeader)
            .html(options.header);
            
        closeImage = $("<img/>")
            .attr({
                src: 'style/{$STYLE}/img/del_mail.png',
                title: options.closeText
            })
            .addClass('message_close_img')
            .appendTo(messageHeader)
            .click(function(){
                $("#"+options.name).fadeOut();
            });
              
	   clearDiv = $("<div>")
           .addClass('clear')
           .appendTo(messageHeader);
           
       messageContent = $("<div>")
	       .addClass("message_cont")
	       .html(options.content)
	       .appendTo(messageWindow);
           
       messageFooter = $("<div>")
		    .addClass("message_footer")
		    .html(options.bottom)
		    .appendTo(messageWindow);		
	}
	
	createMessage(); 
    	
	$(options.open).live('click',function(){        
        $("#"+options.name).fadeIn();
    })
    
    $(options.close).live('click',function()
    {
        $("#"+options.name).fadeOut();
    })
}