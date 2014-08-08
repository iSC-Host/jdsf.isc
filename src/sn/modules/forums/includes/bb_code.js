	var msgbox = jQuery('#bb_msg');
	var msgbox_id = document.getElementById('bb_msg');

	var button_b = jQuery('#bb_b');
	var button_i = jQuery('#bb_i');
	var button_u = jQuery('#bb_u');
	var button_quote = jQuery('#bb_quote');
	
	var smilies = jQuery('#smilietable').find('a');

////////////////////////////
	
	button_b.click(function() {
		insertText('[b]', '[/b]');
	});
	
	button_i.click(function() {
		insertText('[i]', '[/i]');
	});
	
	button_u.click(function() {
		insertText('[u]', '[/u]');
	});
	
	button_quote.click(function() {
		insertText('[quote]', '[/quote]');
	});
	
	smilies.click(function(evt) {
		evt.preventDefault();
		insertText('', ' ' + jQuery(this).find('img').attr('title') + ' ');
	});
	
////////////////////////////

	function insertText(a, b) {
		msgbox_id.focus();

		if(typeof document.selection != 'undefined') //IE, Opera
			insertIE(a, b);
		else if (typeof msgbox_id.selectionStart != 'undefined') // Gecko (FF)
			insertGecko(a, b);
	}

	function insertIE(a, b) {
		rangeIE = document.selection.createRange();
		// if(rangeIE.parentElement().id != msgbox_id) {
			// rangeIE = null; return;
		// }

		var oldText = rangeIE.text;
		rangeIE.text = a + oldText + b;
		
		if (oldText.length == 0)
			rangeIE.move('character', -b.length);
		else
			rangeIE.moveStart('character', rangeIE.text.length);

		rangeIE.select();
		rangeIE = null;
	}
	
	function insertGecko(a, b) {
		from = msgbox_id.selectionStart;  
		until = msgbox_id.selectionEnd; 

		begin = msgbox_id.value.slice(0, from);
		mid  = msgbox_id.value.slice(from, until);
		end   = msgbox_id.value.slice(until); 

		msgbox_id.value = begin + a + mid + b + end;

		if(until - from == 0) {
			msgbox_id.selectionStart = from + a.length;
			msgbox_id.selectionEnd   = msgbox_id.selectionStart;
		}
		else {
			msgbox_id.selectionEnd   = until + a.length + b.length;
			msgbox_id.selectionStart = msgbox_id.selectionEnd;
		}
	}