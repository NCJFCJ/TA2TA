jQuery(document).ready(function($) {

	// Check All / Uncheck All
	$('#eeBFEL_checkAll').on('click', function() {
		$('.eeBFEL_DenyRoleCheck input[type="checkbox"]').prop('checked', true);
	});
	$('#eeBFEL_uncheckAll').on('click', function() {
		$('.eeBFEL_DenyRoleCheck input[type="checkbox"]').prop('checked', false);
	});
	

	$('#eeBFEL_CopyShortcode').on('click', function() {
		var copyText = document.getElementById("eeBFEL_Shortcode");
		copyText.select();
		copyText.setSelectionRange(0, 99999); // For mobile devices
		document.execCommand("copy");
		alert("Copied the shortcode: " + copyText.value);
	});
	
	
	
});