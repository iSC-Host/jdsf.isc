var sent1;
var sent2;
var sent3;
jQuery('#fu1').change(function() {
	document.forms['form_fu1'].submit();

	$('#fu1_sub').html('{-$LOADING}');
	$('#fu1').attr('disabled', 'disabled');
});

jQuery('#fu2').change(function() {
	document.forms['form_fu2'].submit();

	$('#fu2_sub').html('{-$LOADING}');
	$('#fu2').attr('disabled', 'disabled');
});

jQuery('#fu3').change(function() {
	document.forms['form_fu3'].submit();

	$('#fu3_sub').html('{-$LOADING}');
	$('#fu3').attr('disabled', 'disabled');
});