jQuery(document).ready(function($) {

	var previous = $('#lh_archive_post_status-post_expires').val();
var plant = document.getElementById('lh_archived_post_status-js-date_format');
var date_format = plant.getAttribute('data-value'); // fruitCount = '12'
	$('#lh_archive_post_status-post_expires').datepicker({
		dateFormat: date_format
	});

	$('#pw-spe-edit-expiration, .pw-spe-hide-expiration').click( function(e) {

		e.preventDefault();

		var date = $('#lh_archive_post_status-post_expires').val();

		if( $(this).hasClass('cancel' ) ) {

			$('#lh_archive_post_status-post_expires').val( previous );
		
		} else if( date ) {

			$('#pw-spe-expiration-label').text( $('#lh_archive_post_status-post_expires').val() );

		}

		$('#pw-spe-expiration-field').slideToggle();

	});

});