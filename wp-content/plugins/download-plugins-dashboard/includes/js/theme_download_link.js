/**
 * theme_download_link
 *
 * @version 1.1.0
 * @since   1.1.0
 *
 * @author  WPFactory
 */

jQuery( document ).ready( function() {
	jQuery( 'div.theme-actions' ).each( function() {
		var theme_name = jQuery( this ).parents( 'div.theme' ).attr( 'data-slug' );
		jQuery( this ).append( '<a class="button alg_download_theme" href="?alg_download_theme=' + theme_name + '">' + alg_object.download_link_text + '</a>' );
	} );
} );
