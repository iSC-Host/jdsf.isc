/*

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
					document.title = '{-$chat_get_message} '+x;
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
};