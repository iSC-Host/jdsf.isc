<?php /* Smarty version 2.6.26, created on 2014-02-07 15:21:36
         compiled from file:style/newcunity/templates/overall_header.tpl.html */ ?>
<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $this->_tpl_vars['TITLE']; ?>
 - <?php echo $this->_tpl_vars['cunitysettings']['name']; ?>
</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<meta name="description" content="<?php echo $this->_tpl_vars['SLOGAN']; ?>
"/>
<link href="style/newcunity/img/icon.gif" rel="shortcut icon" type="image/x-icon" />
<link rel="stylesheet" href="includes/jScrollPane/jquery.jscrollpane.css"/>
<link rel="stylesheet" type="text/css" href="includes/jGallery/jGallery.css"/>
<link rel="stylesheet" type="text/css" href="style/newcunity/styles.css" />
<link rel="stylesheet" href="style/newcunity/apprise.css" type="text/css" />
<link rel="stylesheet" type="text/css" media="all" href="style/newcunity/jqueryui/jqueryui.css"/>
<link rel="stylesheet" type="text/css" media="all" href="includes/sticky/sticky.full.css"/>
<link rel="stylesheet" type="text/css" href="includes/uploadify/uploadify.css"/>
<?php if ($this->_tpl_vars['module']['chat']): ?>
<link type="text/css" rel="stylesheet" media="all" href="style/newcunity/chat.css" />
<link type="text/css" href="includes/jScrollPane/jquery.jscrollpane.css" rel="stylesheet" media="all" />
<!-- [if IE]>
<style>
.chatBoxDialog {
	position: absolute;
}
</style>
<![endif]-->
<?php endif; ?>
<!--[if IE]>
<style>
.main_nav ul li:hover {
    color: #333;
}

.notifications button {
    position: static;
    left: 0px;
    top: 0px;
}

.notifications {
    margin-top: 2px;
}
</style>
<![endif]-->
<script language="javascript" type="text/javascript" src="includes/jquery/jquery.js"></script>
<script type="text/javascript" src="includes/jScrollPane/jquery.jscrollpane.min.js"></script>
<script src="includes/uploadify/jquery.uploadify-3.1.min.js"></script>
<script language="javascript" type="text/javascript" src="includes/jquery/jquery-ui-1.8.7.custom.min.js"></script>
<script language="javascript" type="text/javascript" src="includes/jqueryothers/jquery.qtip-1.0.0-rc3.min.js"></script>
<script type="text/javascript" src="includes/jqueryothers/spin.min.js"></script>
<?php if (LOGIN): ?>
	<?php if ($this->_tpl_vars['module']['chat']): ?>
		<script type="text/javascript" src="includes/jScrollPane/jquery.mousewheel.js"></script>
		<script language="javascript" type="text/javascript">/*

Copyright (c) 2009 Anant Garg (anantgarg.com | inscripts.com)

This script may be used for non-commercial purposes only. For any
commercial purposes, please contact the author at
anant.garg@inscripts.com

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

*/

var windowFocus = true;
var username;
var chatHeartbeatCount = 0;
var minChatHeartbeat = 10000;
var maxChatHeartbeat = 33000;
var chatHeartbeatTime = minChatHeartbeat;
var originalTitle;
var blinkOrder = 0;
var jScrollPane = new Array();
var jScrollApi = new Array();

var chatboxFocus = new Array();
var newMessages = new Array();
var newMessagesWin = new Array();
var chatBoxes = new Array();

Array.prototype.remove=function(s){
	for(i=0;i<this.length;i++)
		if(s==this[i])
			this.splice(i, 1);	
}

$(document).ready(function(){
	originalTitle = document.title;
	startChatSession();

	$([window, document]).blur(function(){
		windowFocus = false;
	}).focus(function(){
		windowFocus = true;
		document.title = originalTitle;
	});
	
	$(".chatBoxDialog > .ui-dialog-titlebar").live('click',function(){
		$(this).next().toggle()
	})
});

function restructureChatBoxes() {
	count = 0;
	for (x in chatBoxes) {
		chatboxtitle = chatBoxes[x];
		xPos = $(document).width()-(260*(count+1));
		$("#chatbox_"+chatboxtitle).dialog('option','position',[xPos,0])
		count++;
	}
}

function chatWith(userId, chatusername) {	
	createChatBox(userId, chatusername);

	$("#chatbox_"+userId+" .chatboxtextarea").focus();
}

function createChatBox(chatboxtitle,chatusername, minimizeChatBox) {
	if ($("#chatbox_"+chatboxtitle).length > 0) {
		if ($("#chatbox_"+chatboxtitle).css('display') == 'none') {
			$("#chatbox_"+chatboxtitle).css('display','block');
			restructureChatBoxes();
		}
		$("#chatbox_"+chatboxtitle+" .chatboxtextarea").focus();
		return;
	}
	xPos = $(document).width()-(260*(chatBoxes.length+1));
	$("<div />")
		.attr("id","chatbox_"+chatboxtitle)
		.addClass("chatbox")
		.appendTo($("body"))
		.html('<div class="chatboxcontent"></div><div class="chatboxinput"><textarea class="chatboxtextarea" onkeydown="javascript:return checkChatBoxInputKey(event,this,\''+chatboxtitle+'\');"></textarea></div>')
		.dialog({
			autoOpen:true,
			title: chatusername,
			resizable: false,
			draggable: false,
			position: [xPos,0],
			width:250,
			dialogClass: 'chatBoxDialog',
			close: function(){
				$.post("controllers/ajaxChatController.php?action=closechat", { chatbox: chatboxtitle} , function(data){
					$("#chatbox_"+chatboxtitle).remove();
					chatBoxes.remove(chatboxtitle);
					restructureChatBoxes();	
				});
			}
		});
		//$("#chatbox_"+chatboxtitle).parent().css({"position":"fixed","top":"0px","position":"absolute"});

	jScrollPane[chatboxtitle] = $("#chatbox_"+chatboxtitle+" .chatboxcontent").jScrollPane({autoReinitialise:true});

	jScrollApi[chatboxtitle] = jScrollPane[chatboxtitle].data('jsp');

	chatBoxes.push(chatboxtitle);
	$("#chatbox_"+chatboxtitle).dialog('open');
}


function chatHeartbeat(){

 var itemsfound = 0;

	if (windowFocus == false) {

		var blinkNumber = 0;
		var titleChanged = 0;
		for (x in newMessagesWin) {
			if (newMessagesWin[x] == true) {
				
				++blinkNumber;
				if (blinkNumber >= blinkOrder) {
					document.title = '<?php echo $this->_tpl_vars['chat_get_message']; ?>
 '+x;
					titleChanged = 1;
					break;
				}
			}
		}

		if (titleChanged == 0) {
			document.title = originalTitle;
			blinkOrder = 0;
		} else {
			++blinkOrder;
		}

	} else {
		for (x in newMessagesWin) {
			newMessagesWin[x] = false;
		}
	}

	for (x in newMessages) {
		if (newMessages[x] == true) {
			if (chatboxFocus[x] == false) {
				$('#chatbox_'+x+' .chatboxhead').toggleClass('chatboxblink');
			}
		}
	}

	$.ajax({
	  url: "controllers/ajaxChatController.php?action=chatheartbeat",
	  cache: false,
	  dataType: "json",
	  success: function(data)
      {
		$.each(data.items, function(i,item)
        {
			if(item)
            {
				chatboxtitle = item.c+"-"+item.f;

                if($("#chatbox_"+chatboxtitle).length == 0)
                {
					createChatBox(chatboxtitle, item.u);
				}
				if($("#chatbox_"+chatboxtitle).css('display') == 'none')
                {
					$("#chatbox_"+chatboxtitle).css('display','block');
					restructureChatBoxes();
				}
				if(item.s == 1)
                {
					item.u = username;
				}
				newMessages[item.u] = true;
				newMessagesWin[item.u] = true;


                if($("#chatbox_"+chatboxtitle+" .chatboxmessagecontent:last").hasClass('whiteBG') && $("#chatbox_"+chatboxtitle+" .chatboxmessage").length>0)
			    {
			        if($("#chatbox_"+chatboxtitle+" .jspPane").length>0)
                        $("#chatbox_"+chatboxtitle+" .jspPane").append('<div class="chatboxmessage"><div class="chatboxmessageheader"><span class="chatboxmessagefrom">'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent coloredBG">'+item.m+'</span></div>');
                    else
                        $("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage""><div class="chatboxmessageheader"><span class="chatboxmessagefrom">'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent coloredBG">'+item.m+'</span></div>');
                }
				else
			    {
			        if($("#chatbox_"+chatboxtitle+" .jspPane").length>0)
			            $("#chatbox_"+chatboxtitle+" .jspPane").append('<div class="chatboxmessage"><div class="chatboxmessageheader"><span class="chatboxmessagefrom" >'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent whiteBG">'+item.m+'</span></div>');
			        else
                        $("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><div class="chatboxmessageheader"><span class="chatboxmessagefrom" >'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent whiteBG">'+item.m+'</span></div>');
                }
				itemsfound += 1;
			}

		});


		chatHeartbeatCount++;


		if (itemsfound > 0) {
			chatHeartbeatTime = minChatHeartbeat;
			chatHeartbeatCount = 1;
		} else if (chatHeartbeatCount >= 10) {
			chatHeartbeatTime *= 2;
			chatHeartbeatCount = 1;
			if (chatHeartbeatTime > maxChatHeartbeat) {
				chatHeartbeatTime = maxChatHeartbeat;
			}
		}

             for(i=0;i<chatBoxes.length;i++)
            {
                chatboxtitle=chatBoxes[i];
                jScrollApi[chatboxtitle].reinitialise();
                jScrollApi[chatboxtitle].scrollToBottom();
            }
	}});

}
function toggleChatBoxGrowth(chatboxtitle) {
	if ($('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display') == 'none') {

		var minimizedChatBoxes = new Array();

		if ($.cookie('chatbox_minimized')) {
			minimizedChatBoxes = $.cookie('chatbox_minimized').split(/\|/);
		}

		var newCookie = '';

		for (i=0;i<minimizedChatBoxes.length;i++) {
			if (minimizedChatBoxes[i] != chatboxtitle) {
				newCookie += chatboxtitle+'|';
			}
		}

		newCookie = newCookie.slice(0, -1)


		$.cookie('chatbox_minimized', newCookie);
		$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','block');
		$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','block');
		$("#chatbox_"+chatboxtitle+" .chatboxcontent").scrollTop($("#chatbox_"+chatboxtitle+" .chatboxcontent")[0].scrollHeight);
	} else {

		var newCookie = chatboxtitle;

		if ($.cookie('chatbox_minimized')) {
			newCookie += '|'+$.cookie('chatbox_minimized');
		}


		$.cookie('chatbox_minimized',newCookie);
		$('#chatbox_'+chatboxtitle+' .chatboxcontent').css('display','none');
		$('#chatbox_'+chatboxtitle+' .chatboxinput').css('display','none');
	}

}

function checkChatBoxInputKey(event,chatboxtextarea,chatboxtitle) {

	if(event.keyCode == 13 && event.shiftKey == 0)  {

		message = $(chatboxtextarea).val();
		message = message.replace(/^\s+|\s+$/g,"");


		$(chatboxtextarea).val('');
		$(chatboxtextarea).focus();
		$(chatboxtextarea).css('height','44px');
		if (message != '') {
			$.post("controllers/ajaxChatController.php?action=sendchat", {to: chatboxtitle, message: message} , function(item)
            {
				if($("#chatbox_"+chatboxtitle+" .chatboxmessagecontent:last").hasClass('whiteBG') && $("#chatbox_"+chatboxtitle+" .chatboxmessage").length>0)
			    {
                    if($("#chatbox_"+chatboxtitle+" .jspPane").length>0)
                        $("#chatbox_"+chatboxtitle+" .jspPane").append('<div class="chatboxmessage"><div class="chatboxmessageheader"><span class="chatboxmessagefrom">'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent coloredBG">'+item.m+'</span></div>');
                    else
                        $("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage""><div class="chatboxmessageheader"><span class="chatboxmessagefrom">'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent coloredBG">'+item.m+'</span></div>');
                }
				else
			    {
			        if($("#chatbox_"+chatboxtitle+" .jspPane").length>0)
			            $("#chatbox_"+chatboxtitle+" .jspPane").append('<div class="chatboxmessage"><div class="chatboxmessageheader"><span class="chatboxmessagefrom" >'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent whiteBG">'+item.m+'</span></div>');
			        else
                        $("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><div class="chatboxmessageheader"><span class="chatboxmessagefrom" >'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent whiteBG">'+item.m+'</span></div>');
                }

    			jScrollApi[chatboxtitle].reinitialise();
				jScrollApi[chatboxtitle].scrollToBottom();
			},"json");
		}
		chatHeartbeatTime = minChatHeartbeat;
		chatHeartbeatCount = 1;

		return false;
	}

	var adjustedHeight = chatboxtextarea.clientHeight;
	var maxHeight = 94;

	if (maxHeight > adjustedHeight) {
		adjustedHeight = Math.max(chatboxtextarea.scrollHeight, adjustedHeight);
		if (maxHeight)
			adjustedHeight = Math.min(maxHeight, adjustedHeight);
		if (adjustedHeight > chatboxtextarea.clientHeight)
			$(chatboxtextarea).css('height',adjustedHeight+8 +'px');
	} else {
		$(chatboxtextarea).css('overflow','auto');
	}

}

function startChatSession(){
    $.ajax({url: "controllers/ajaxChatController.php?action=startchatsession",cache: false,dataType: "json",success: function(data) {
		username = data.username;
		$.each(data.items, function(i,it){
			if(it){
                iData = i.split("-");
                chatboxtitle = i;
                if($("#chatbox_"+chatboxtitle).length == 0)
                	createChatBox(chatboxtitle, iData[2]);
                if($("#chatbox_"+chatboxtitle).css('display') == 'none'){
                	$("#chatbox_"+chatboxtitle).css('display','block');
                	restructureChatBoxes();
                }
                newMessages[chatboxtitle] = true;
                newMessagesWin[chatboxtitle] = true;

                $.each(it, function(a,item){
                    if($("#chatbox_"+chatboxtitle+" .chatboxmessagecontent:last").hasClass('whiteBG') && $("#chatbox_"+chatboxtitle+" .chatboxmessage").length>0){
                        if($("#chatbox_"+chatboxtitle+" .jspPane").length>0)
                            $("#chatbox_"+chatboxtitle+" .jspPane").append('<div class="chatboxmessage"><div class="chatboxmessageheader"><span class="chatboxmessagefrom">'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent coloredBG">'+item.m+'</span></div>');
                        else
                            $("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage""><div class="chatboxmessageheader"><span class="chatboxmessagefrom">'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent coloredBG">'+item.m+'</span></div>');
                    }else{
                        if($("#chatbox_"+chatboxtitle+" .jspPane").length>0)
                            $("#chatbox_"+chatboxtitle+" .jspPane").append('<div class="chatboxmessage"><div class="chatboxmessageheader"><span class="chatboxmessagefrom" >'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent whiteBG">'+item.m+'</span></div>');
                        else
                            $("#chatbox_"+chatboxtitle+" .chatboxcontent").append('<div class="chatboxmessage"><div class="chatboxmessageheader"><span class="chatboxmessagefrom" >'+item.u+'</span><span class="chatboxmessagetime">'+item.t+'</span><div class="clear"></div></div><span class="chatboxmessagecontent whiteBG">'+item.m+'</span></div>');
                    }
                })
			}

		});

	}});
}

/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};</script>
	<?php endif; ?>
	<script language="javascript" type="text/javascript" src="includes/jqueryothers/jquery.watermarkinput.js"></script>
	<script language="javascript" type="text/javascript" src="includes/sticky/sticky.full.js"></script>
<?php endif; ?>
<script language="javascript" type="text/javascript">// Apprise 1.5 by Daniel Raftery
// http://thrivingkings.com/apprise
//
// Button text added by Adam Bezulski
//

function apprise(string, args, callback)
	{
	var default_args =
		{
		'confirm'		:	false, 		// Ok and Cancel buttons
		'verify'		:	false,		// Yes and No buttons
		'input'			:	false, 		// Text input (can be true or string for default text)
		'animate'		:	false,		// Groovy animation (can true or number, default is 400)
		'textOk'		:	'<?php echo $this->_tpl_vars['ok']; ?>
',		// Ok button default text
		'textCancel'	:	'<?php echo $this->_tpl_vars['cancel']; ?>
',	// Cancel button default text
		'textYes'		:	'<?php echo $this->_tpl_vars['yes']; ?>
',		// Yes button default text
		'textNo'		:	'<?php echo $this->_tpl_vars['no']; ?>
'		// No button default text
		}

	if(args)
		{
		for(var index in default_args)
			{ if(typeof args[index] == "undefined") args[index] = default_args[index]; }
		}

	var aHeight = $(document).height();
	var aWidth = $(document).width();
	
	$('.appriseOverlay').remove();
	$('.appriseOuter').remove();
		
	$('body').append('<div class="appriseOverlay" id="aOverlay"></div>');
	$('.appriseOverlay').css('height', aHeight).css('width', aWidth).fadeIn(100);
	$('body').append('<div class="appriseOuter"></div>');
	$('.appriseOuter').append('<div class="appriseInner"></div>');
	$('.appriseInner').append(string);
    $('.appriseOuter').css("left", ( $(window).width() - $('.appriseOuter').width() ) / 2+$(window).scrollLeft() + "px");

    if(args)
		{
		if(args['animate'])
			{
			var aniSpeed = args['animate'];
			if(isNaN(aniSpeed)) { aniSpeed = 400; }
			$('.appriseOuter').css('top', '-200px').show().animate({top:"100px"}, aniSpeed);
			}
		else
			{ $('.appriseOuter').css('top', '100px').fadeIn(200); }
		}
	else
		{ $('.appriseOuter').css('top', '100px').fadeIn(200); }

    if(args)
    	{
    	if(args['input'])
    		{
    		if(typeof(args['input'])=='string')
    			{
    			$('.appriseInner').append('<div class="aInput"><input type="text" class="aTextbox" t="aTextbox" value="'+args['input']+'" /></div>');
    			}
    		else
    			{
				$('.appriseInner').append('<div class="aInput"><input type="text" class="aTextbox" t="aTextbox" /></div>');
				}
    		}
    	}

    $('.appriseInner').append('<div class="aButtons"></div>');
    if(args)
    	{
		if(args['confirm'] || args['input'])
			{
			$('.aButtons').append('<button value="ok">'+args['textOk']+'</button>');
			$('.aButtons').append('<button value="cancel">'+args['textCancel']+'</button>');
			}
		else if(args['verify'])
			{
			$('.aButtons').append('<button value="ok">'+args['textYes']+'</button>');
			$('.aButtons').append('<button value="cancel">'+args['textNo']+'</button>');
			}
		else
			{ $('.aButtons').append('<button value="ok">'+args['textOk']+'</button>'); }
		}
    else
    	{ $('.aButtons').append('<button value="ok">Ok</button>'); }

	$(document).keydown(function(e)
		{
		if($('.appriseOverlay').is(':visible'))
			{
			if(e.keyCode == 13)
				{ $('.aButtons > button[value="ok"]').click(); }
			if(e.keyCode == 27)
				{ $('.aButtons > button[value="cancel"]').click(); }
			}
		});

    $('.aTextbox').focus();

	var aText = $('.aTextbox').val();
	if(!aText) { aText = false; }
	$('.aTextbox').keyup(function()
    	{ aText = $(this).val(); });

    $('.aButtons > button').click(function()
    	{
    	$('.appriseOverlay').remove();
		$('.appriseOuter').remove();
    	if(callback)
    		{
			var wButton = $(this).attr("value");
			if(wButton=='ok')
				{
				if(args)
					{
					if(args['input'] || aText != "")
						{ callback(aText); }
					else
						{ callback(true); }
					}
				else
					{ callback(true); }
				}
			else if(wButton=='cancel')
				{ callback(false); }
			}
		});
	}</script>
</head>
<body>
    <noscript><strong><?php echo $this->_tpl_vars['overall_header_attention']; ?>
</strong>
<?php echo $this->_tpl_vars['overall_header_javascript']; ?>
 <a
	href="http://www.google.com/support/bin/answer.py?answer=23852"
	target="_blank"> <?php echo $this->_tpl_vars['overall_header_here']; ?>
</a>.</noscript>
<?php if ($this->_tpl_vars['module']['messages']): ?>
<div id="newMessageDiv" title="<?php echo $this->_tpl_vars['inbox_new_message']; ?>
" style="width: 500px; display: none;text-align:left;">
	<label for="sendMessageReceiver"><?php echo $this->_tpl_vars['conversation_receiver']; ?>
</label>
	<br/>
	<input type="text" name="receiver" id="sendMessageReceiver" style="width: 200px;"/>
	<input type="hidden" id="sendMessageReceiver_id_input" name="receiver_data"/>
	<br />
	<label for="sendMessagemsgTa"><?php echo $this->_tpl_vars['conversation_message']; ?>
</label>
	<textarea id="sendMessagemsgTa" style="width: 500px; height: 270px;" name="message"></textarea>
</div>
<script language="javascript" type="text/javascript">
function imgLoadCheck(){
	$("img").error(function(){
    	$(this).attr('src','style/<?php echo $this->_tpl_vars['STYLE']; ?>
/img/no_avatar.jpg');
    })
}

function newMessage(name,userid,userhash,cid){
	$("#newMessageDiv").dialog({
		width:547,
		height:430,
		buttons: {
			"<?php echo $this->_tpl_vars['inbox_close']; ?>
": function(){
				$(this).dialog('close');
			},
			"<?php echo $this->_tpl_vars['conversation_send']; ?>
":function(){
				sendMessage($('#msgTa').val());
			}
		}
	});
	if(name!=""&&userhash!=""&&userid>0){
        $("#sendMessageReceiver").val(name).attr("disabled","disabled");
        $("#sendMessageReceiver_id_input").val(cid+"-"+userid+"-"+userhash);
    }
}
function sendMessage(){
	if($("#sendMessageReceiver_id_input").length>0){
		var data = '{"action":"sendMessage","receiverData":"'+$("#sendMessageReceiver_id_input").val()+'","message":"' + $("#sendMessagemsgTa").val() + '"}';
		$.post("controllers/ajaxMessageController.php", {json_data : data}, function(jData) {
			if(jData.status==1){
                $("#newMessageDiv").dialog('close');
				apprise("<?php echo $this->_tpl_vars['inbox_message_sent']; ?>
<br/>"+jData.cLink);
				$("#sendMessagemsgTa").val("");
				$("#sendMessageReceiver_id_input").val("");
				if(document.URL.indexOf("messages.php") >= 0)
                    loadConversations();
			}else
				apprise("An Error occurred! Please try again later!");
	    }, "json");
	}else{
		apprise("<?php echo $this->_tpl_vars['inbox_no_receiver']; ?>
");
	}
}
</script>
<?php endif; ?>
<!--Header Start--->
<div class="header">
<div class="headercon">
	<!--Logo Start--->
	<div class="logo">
	<?php echo $this->_tpl_vars['HEADER']; ?>

	</div>
	<!--Logo Start--->

	<!--Welcome  MSG! End--->
	<div class="welcome-msg">
	<ul>
		<li><?php echo $this->_tpl_vars['overall_header_welcome']; ?>
</li>
		<li>|</li>
        <?php if (LOGIN): ?>
		<li style="padding-left: 1px;"><span class="ui-icon ui-icon-power" style="display:inline-block">&nbsp;</span><a href="register.php?c=logout"><?php echo $this->_tpl_vars['menu_logout']; ?>
</a></li>
		<?php else: ?>
		<li><a href="register.php"><?php echo $this->_tpl_vars['menu_register']; ?>
</a></li>
		<?php endif; ?>
	</ul>
	</div>
	<!--Welcome  MSG End--->
</div>
</div>
<div class="clear"></div>
<!--Header End--->

<!--body--->
<div class="wrapper">
   <script type="text/javascript">
$(document).ready(function(){
    $(".show_notifications").click(function() {
        if(!$("#notification_content").is(":visible"))
            $("#notification_content").slideDown(400);
        else
            $("#notification_content").slideUp(400);
    });

    $(".main_nav ul li").not('.active')
        .mouseover(function(){
            $(this).addClass('active');
            $(this).children().css('color', 'white');
        })
        .mouseout(function(){
            $(this).removeClass('active');
            $(this).children().css('color', 'black');
        })
        .click(function(){
            location.href=$(this).children('a').attr('href');
        })
});
</script>
	<!--Top Bar Start --->
	<script language="javascript" type="text/javascript">
    function refreshNotifications(){
        var dataValues = '{"action": "getFullNotifications"}';
    	$.post("controllers/ajaxNotificationController.php", {json_data : dataValues},
    	function(data){
    		if(data.status >= 1)
    			$("#notification_status").attr('src', 'style/<?php echo $this->_tpl_vars['STYLE']; ?>
/img/new_notifications.png');
    		else
                $("#notification_status").attr('src', 'style/<?php echo $this->_tpl_vars['STYLE']; ?>
/img/notifications_empty.png');
            $("#notification_count").html(data.status);
            $("#notification_content").html(data.notifications);
            var c = data.newest.length;
            for(var i = 0; i < c; i++)
                $.sticky(data.newest[i]);
    	}, "json");
    }

    function NotificationRead(id){
        var dataValues = '{"action": "readNotification","id": "' + id + '"}';
    	$.post("controllers/ajaxNotificationController.php", {json_data : dataValues},function(){
        },"json");
        return true;
    }

    function refreshButtons(){
        $(".jui-button").each(function(){
            if($(this).attr('text')!==undefined)
                var t = Boolean(Number($(this).attr('text')));
            else
                var t = true;
            if($(this).attr('icon')!==undefined){
                if($(this).attr('icon2')!==undefined){
                    $(this).button({
                        icons: {
                            primary: $(this).attr('icon'),
                            secondary: $(this).attr('icon2')
                        },
                        text: t
                    });
                }else{
                    $(this).button({
                        icons: {
                            primary: $(this).attr('icon')
                        },
                        text: t
                    });
                }
            }else if($(this).attr('icon2')!==undefined){
                if($(this).attr('icon')!==undefined){
                    $(this).button({
                        icons: {
                            primary: $(this).attr('icon'),
                            secondary: $(this).attr('icon2')
                        },
                        text: t
                    });
                }else{
                    $(this).button({
                        icons: {
                            secondary: $(this).attr('icon2')
                        },
                        text: t
                    });
                }
            }else
                $(this).button();
        })
        $(".buttonset").buttonset();
    }

    $("document").ready(function(){
        $("#notification_headline").click(function(){
            $("#notifications_list").toggle("fast");
        });

        $(".notification_unread").live('mouseenter', function(){
            var el = this;
            var dataValues = '{"action": "readNotification","id": "'+$(this).attr('id')+'"}';
    		$.post("controllers/ajaxNotificationController.php", {json_data : dataValues}, function(){
                $(el)
                    .removeClass('notification_unread');

            },"json");
        })
    refreshButtons();
    <?php if (LOGIN): ?>
    refreshNotifications();

    window.setInterval('refreshNotifications()', 30000);
    <?php endif; ?>
    })
    </script>
   <div class="top_bar">
		<div class="top_bar_a"></div>
		<div class="top_bar_b">
        <?php if (LOGIN): ?>
		<!-- Notification start -->
			<div class="notifications">
			<button class="bordered_button show_notifications" style="margin: 0px; float: left;"><?php echo $this->_tpl_vars['notifications_header']; ?>
</button>
            <a style="float: left;" href="notifications.php" class="link"><?php echo $this->_tpl_vars['notifications_header']; ?>
 (<span id="notification_count">0</span>)</a>
            <img src="style/newcunity/img/notification-downarrow.png" alt="<?php echo $this->_tpl_vars['menu_notifications']; ?>
" class="show_notifications img"/>
            <div class="clear"></div>
            <div id="notification_content" style="display: none;"></div>
			</div>
		<!-- Notification start -->
        <?php endif; ?>
		</div>
		<div class="top_bar_c"></div>
   </div>
	<!--Top Bar End --->

	<!--body--->
	<div class="a_side">

		<div class="a_side_a"></div>
		<div class="a_side_b">

        <?php if (LOGIN): ?>
		<!-- Profile -->
			<div class="profile_main">
				<a href="profile.php" style="display:block;"><img src="<?php echo $this->_tpl_vars['AVATAR']; ?>
"/></a>
				<h1><a href="profile.php"><?php echo $this->_tpl_vars['USERNAME']; ?>
</a></h1>
		  </div>
		<!-- Profile End -->
		<?php else: ?>
		    <div class="main_nav">
		      <ul>
		        <?php if ($this->_tpl_vars['FILE'] == 'start'): ?>
		        <li><a href="register.php"><img src="style/newcunity/img/plus.png" alt="<?php echo $this->_tpl_vars['menu_register']; ?>
"/><?php echo $this->_tpl_vars['menu_register']; ?>
</a></li>
		        <?php else: ?>
		        <li><a href="start.php"><img src="style/newcunity/img/home.png" alt="Start"/>Start</a></li>
                <?php endif; ?>
                <li><a href="pages.php?id=privacy"><img src="style/newcunity/img/members.png" alt="<?php echo $this->_tpl_vars['menu_privacy']; ?>
"/><?php echo $this->_tpl_vars['menu_privacy']; ?>
</a></li>
                <li><a href="pages.php?id=terms"><img src="style/newcunity/img/forums.png" alt="<?php echo $this->_tpl_vars['menu_terms']; ?>
"/><?php echo $this->_tpl_vars['menu_terms']; ?>
</a></li>
                <li><a href="pages.php?id=imprint"><img src="style/newcunity/img/imprint.png" alt="<?php echo $this->_tpl_vars['menu_imprint']; ?>
"/><?php echo $this->_tpl_vars['menu_imprint']; ?>
</a></li>
                <li><a href="javascript: contact();"><img src="style/newcunity/img/inbox.png" alt="<?php echo $this->_tpl_vars['menu_contact']; ?>
"/><?php echo $this->_tpl_vars['menu_contact']; ?>
</a></li>
              </ul>
		    </div>
		<?php endif; ?>
		</div>
		<div class="a_side_c"></div>
		<?php if (LOGIN): ?>
		<div class="a_side_a"></div>
		<div class="a_side_b" style="text-align: right;">
            <script language="javascript" type="text/javascript">
            $("document").ready(function(){$("#searchside").Watermark("<?php echo $this->_tpl_vars['pages_search_member']; ?>
");})
            </script>
            <form action="friends.php" method="GET">
            <img src="style/newcunity/img/magnifier-zoom.png" style="display: inline-block; vertical-align: middle; width: 16px; height: 16px;"/>
            <input name="q" type="text" style="display: inline-block; vertical-align: middle; margin-right: 10px; width: 115px;" id="searchside"/>
            </form>
		</div>
		<div class="a_side_c"></div>

		<!-- Side Menu Container Start -->
		<div class="a_side_a"></div>
		<div class="a_side_b">

		<!-- Side Menu Start -->
		<div class="main_nav">
			<ul>
				<?php echo $this->_tpl_vars['MENU']; ?>

                <?php if (ADMIN): ?>
                <li><a href="admin/index.php"><img src="style/newcunity/img/burn.png" alt="<?php echo $this->_tpl_vars['menu_administration']; ?>
"/><?php echo $this->_tpl_vars['menu_administration']; ?>
</a></li>
                <?php endif; ?>
			</ul>
		</div>
		<!-- Side Menu End -->

		</div>
		<div class="a_side_c"></div>

		<!-- Side Menu Container End -->
        <?php endif; ?>
        <div class="a_side_a"></div>
		<div class="a_side_b" style="text-align: right;" title="<?php echo $this->_tpl_vars['change_language']; ?>
">
		    <form id="languageswitch" method="POST">
                <img src="style/thecunity/img/<?php echo $this->_tpl_vars['LANG']; ?>
.png" style="display: inline-block; vertical-align: middle;"/>
                <select style="display: inline-block; vertical-align: middle; margin-right: 10px; width: 125px;" name="languageswitch" onchange="document.getElementById('languageswitch').submit();">
                    <?php echo $this->_tpl_vars['LANGUAGES']; ?>

                </select>
            </form>
		</div>
		<div class="a_side_c"></div>
		<?php if ($this->_tpl_vars['cunitysettings']['designswitch'] == 1): ?>
		<div class="a_side_a"></div>
		<div class="a_side_b" style="text-align: right;" title="<?php echo $this->_tpl_vars['switch_design']; ?>
">
		    <form method="POST" id="designswitchform">
	            <img src="style/newcunity/img/icons_gallery.png" style="display: inline-block; vertical-align: middle; width: 16px; height: 16px;"/>
	            <select style="display: inline-block; vertical-align: middle; margin-right: 10px; width: 125px;" onchange="document.getElementById('designswitchform').submit();" name="switch-design">
	                <?php echo $this->_tpl_vars['DESIGNS']; ?>

	            </select>
            </form>
		</div>
		<div class="a_side_c"></div>
		<?php endif; ?>
	</div>

	<div class="main">
		<div class="main_a"></div>
		<div class="main_b">

		<!-- Main Col A Start -->
		<div class="main_page_col_a">