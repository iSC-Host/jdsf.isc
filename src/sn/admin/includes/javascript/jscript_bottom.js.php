<?php ob_start('ob_gzhandler'); header('Content-Type: text/javascript'); ?>

	// Info Boxes
		jQuery('a.info_link')
			.click(function(evt){evt.preventDefault();})
			.mouseenter(function(){				
				var box = jQuery(this).next('.info_box');
				var offset = jQuery(this).offset();
				var top = offset.top - box.outerHeight();
				var left = offset.left + jQuery(this).outerWidth();
				
				if(top < 0)
					top = offset.top + jQuery(this).outerHeight();
					
				if(left + box.outerWidth() > jQuery('body').innerWidth())
					left = offset.left - box.outerWidth();
				
				box.css('top', top + 'px')
				   .css('left', left + 'px')
				   .fadeIn('fast');
			}).mouseleave(function(){
				var box = jQuery(this).next('.info_box');
				
				box.fadeOut('fast');
			});

<?php ob_end_flush(); ?>