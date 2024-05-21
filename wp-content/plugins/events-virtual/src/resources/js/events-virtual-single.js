/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 1.8.0
 *
 * @type {PlainObject}
 */
tribe.events = tribe.events || {};

/**
 * Configures Virtual Events Admin Object on the Global Tribe variable
 *
 * @since 1.8.0
 *
 * @type {PlainObject}
 */
tribe.events.virtualSingle = tribe.events.virtualSingle || {};

( function( $, obj ) {
	'use-strict';
	obj.GraphVersion = 'v9.0';

	/**
	 * Initialize the Facebook SDK.
	 *
	 * @since 1.8.0
	 */
	obj.facebookInit = function() {
		if ( typeof FB === 'undefined' ) {
			return false;
		}

		const facebookAppId = tribe_events_virtual_settings.facebookAppId;
		if ( ! facebookAppId || facebookAppId < 1 ) {
			return;
		}

		FB.init( { // eslint-disable-line no-undef
			appId: facebookAppId,
			autoLogAppEvents: true,
			xfbml: true,
			version: obj.GraphVersion,
		} );
	};

	/**
	 * Handles the initialization of the admin when Document is ready
	 *
	 * @since 1.8.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		window.facebookAsyncInit = obj.facebookInit();
	};

	// Configure on document ready
	$( obj.ready );
} )( jQuery, tribe.events.virtualSingle );
