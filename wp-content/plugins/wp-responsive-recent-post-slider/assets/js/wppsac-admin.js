( function( $ ) {

	"use strict";

	/* Vertical Tab */
	$( document ).on( "click", ".wppsac-vtab-nav a", function() {

		$(".wppsac-vtab-nav").removeClass('wppsac-active-vtab');
		$(this).parent('.wppsac-vtab-nav').addClass("wppsac-active-vtab");

		var selected_tab = $(this).attr("href");
		$('.wppsac-vtab-cnt').hide();

		/* Show the selected tab content */
		$(selected_tab).show();

		/* Pass selected tab */
		$('.wppsac-selected-tab').val(selected_tab);
		return false;
	});

	$(window).load(function() {
		var sel_tab = $('.wppsac-selected-tab').val();

		if( typeof(sel_tab) !== 'undefined' && sel_tab != '' && $(sel_tab).length > 0 ) {
			$('.wppsac-vtab-nav [href="'+sel_tab+'"]').click();
		} else {
			$('.wppsac-vtab-nav:first-child a').click();
		}
	});

	/* Click to Copy the Text */
	$(document).on('click', '.wpos-copy-clipboard', function() {
		var copyText = $(this);
		copyText.select();
		document.execCommand("copy");
	});

	/* Drag widget event to render layout for Beaver Builder */
	$('.fl-builder-content').on( 'fl-builder.preview-rendered', wppsac_fl_render_preview );

	/* Save widget event to render layout for Beaver Builder */
	$('.fl-builder-content').on( 'fl-builder.layout-rendered', wppsac_fl_render_preview );

	/* Publish button event to render layout for Beaver Builder */
	$('.fl-builder-content').on( 'fl-builder.didSaveNodeSettings', wppsac_fl_render_preview );

})( jQuery );

/* Function to render shortcode preview for Beaver Builder */
function wppsac_fl_render_preview() {
	wppsac_post_slider_init();
	wppsac_post_carousel_slider_init();
}