<?php /* Smarty version 2.6.26, created on 2014-03-11 21:25:42
         compiled from file:style/default/templates/overall_header.tpl.html */ ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $this->_tpl_vars['TITLE']; ?>
 - <?php echo $this->_tpl_vars['admin_overall_header_administration']; ?>
</title>
		<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
		<meta http-equiv="cache-control" content="no-cache">
		<link rel="stylesheet" type="text/css" href="style/<?php echo $this->_tpl_vars['STYLE']; ?>
/styles.css">
		<link href="style/default/img/icon.gif" rel="shortcut icon" type="image/x-icon" />
		<link href="./style/<?php echo $this->_tpl_vars['STYLE']; ?>
/apprise.css" rel="stylesheet" type="text/css"/>
		<script type="text/javascript" src="../includes/jquery/jquery.js"></script>
        <script type="text/javascript" src="../includes/jqueryothers/jquery.blockUI.js"></script>
        <script language="javascript" type="text/javascript" src="../includes/jquery/jquery-ui-1.8.7.custom.min.js"></script>	
		<link rel="stylesheet" type="text/css" media="all" href="../style/newcunity/jqueryui/jqueryui.css"/>
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
			$('.aButtons').append('<button class="jui-button" value="ok">'+args['textOk']+'</button>');
			$('.aButtons').append('<button class="jui-button" value="cancel">'+args['textCancel']+'</button>');
			}
		else if(args['verify'])
			{
			$('.aButtons').append('<button class="jui-button" value="ok">'+args['textYes']+'</button>');
			$('.aButtons').append('<button class="jui-button" value="cancel">'+args['textNo']+'</button>');
			}
		else
			{ $('.aButtons').append('<button class="jui-button" value="ok">'+args['textOk']+'</button>'); }
		}
    else
    	{ $('.aButtons').append('<button class="jui-button" value="ok">Ok</button>'); }

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
	<div id="mainframe">