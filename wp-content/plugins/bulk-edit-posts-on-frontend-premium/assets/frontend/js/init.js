jQuery(document).ready(function () {
	// This is needed by the handsontable-chosen library, we added it explicitly here to prevent errors
	window.$ = jQuery;

	// Restrict the spreadsheet to the width of the container
	if (typeof hot !== 'undefined') {
		function is_touch_enabled() {
			return ( 'ontouchstart' in window ) || 
				   ( navigator.maxTouchPoints > 0 ) || 
				   ( navigator.msMaxTouchPoints > 0 );
		}

		jQuery('body').addClass('wpse-has-sheet');
		var fixedColumnsLeft = hot.getSettings().columns.length > 4 ? hot.getSettings().fixedColumnsLeft : false;
		hot.updateSettings({
			fixedColumnsLeft: fixedColumnsLeft
		});

		// If it's a touch device, set a fixed height after the rows have loaded, because the touch
		// disabled because it seems to work now.
		// if(is_touch_enabled()){
		// 	jQuery('body').on('vgSheetEditor:afterRowsInsert', function (event, data) {
		// 		console.log('data', data);
		// 		var tableContentHeight = jQuery('.handsontable tbody:first').outerHeight() + 100;
		// 		hot.updateSettings({
		// 			height: tableContentHeight
		// 		});
		// 	});
		// }

		// We need a fixed width to ensure the sheet can be scroll horizontally in touch devices
		jQuery('body').on('click', '.wpse-full-screen-toggle', function (e) {
			setTimeout(function () {

				hot.updateSettings({
					width: jQuery('#post-data').parent().width()
				});
			}, 500);
		});
	}
	jQuery('.modal-formula .wpse-select-rows-options').val('selected').trigger('change');
	if (!jQuery('.button-container.run_filters-container').length) {
		jQuery('#vgse-wrapper #vg-header-toolbar .vgse-current-filters').hide();
	}
	// Load more rows when we scroll to the bottom inside the frontend sheet, because
	// the frontend sheet has its own scroll bar
	jQuery('#vgse-wrapper').on('scroll', _throttle(function () {
		if (!window.wpseFullScreenActive || !window.scrroll || typeof window.beOriginalData === 'undefined'){
			return true;
		}

		var el = document.getElementById('vgse-wrapper');
		var scrolledToTheBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 1;

		// Infinite scroll check
		if (scrolledToTheBottom) {
			jQuery('.load-more').trigger('click');
			window.scrroll = false;
		}
	}, 500, {
		leading: true,
		trailing: true
	}));
});