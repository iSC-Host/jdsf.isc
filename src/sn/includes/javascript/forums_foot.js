	// Hover
		jQuery('#forums').find('tr').mouseover(function(){
			if(jQuery(this).find('td.forum').size() > 0 || jQuery(this).find('td.topic').size() > 0) {
				var href = jQuery(this).find('td').find('a').attr('href');
				jQuery(this).find('td').addClass('hover');
			}
		});

		jQuery('#forums').find('tr').mouseleave(function(){
			jQuery(this).find('td').removeClass('hover');
		});