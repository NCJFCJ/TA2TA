/* eslint-disable no-var */
/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 1.6.0
 *
 * @type {PlainObject}
 */
tribe.events = tribe.events || {};

/**
 * Configures Virtual Events Admin Object on the Global Tribe variable
 *
 * @since 1.6.0
 *
 * @type {PlainObject}
 */
tribe.events.youtubeSettingsAdmin = tribe.events.youtubeSettingsAdmin || {};

( function( $, obj ) {
	'use-strict';

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 1.6.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		youtubeContainer: '.tribe-settings-youtube-integration',
		youtubeAccordionContainer: '.tribe-events-virtual-meetings-youtube-settings__accordion-wrapper',
		youtubeMessageContainer: '.tec-youtube-accounts-messages',
		youtubeChannelIdContainer: '.tribe-settings-youtube-integration__channel-id',
		youtubeDeleteChannelId: '.tribe-settings-youtube-integration__delete-channel',
	};

	/**
	 * Bind the YouTube events.
	 *
	 * @since 1.6.0
	 */
	obj.bindEvents = function() {
		$( obj.selectors.youtubeContainer )
			.on( 'click', obj.selectors.youtubeDeleteChannelId, obj.handleDeleteChannelId );
	};

	/**
	 * Handles the click to delete a YouTube channel id.
	 *
	 * @since 1.6.0
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleDeleteChannelId = function( ev ) {
		ev.preventDefault();

		var confirmed = confirm( tribe_events_virtual_youtube_settings_strings.deleteConfirm );
		if ( ! confirmed ) {
			return;
		}

		var url = $( this ).data( 'ajaxDeleteUrl' );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $( this ).closest( obj.selectors.youtubeChannelIdContainer ),
				success: obj.onChannelIdDeleteSuccess,
			}
		);
	};

	/**
	 * Handles the successful response from the backend to delete account request.
	 *
	 * @since 1.6.0
	 *
	 * @param {string} html The HTML that adds a message on the settings page.
	 */
	obj.onChannelIdDeleteSuccess = function( html ) {
		$( obj.selectors.youtubeMessageContainer ).html( html );

		// Check if this is an error message.
		var $error = $( '.error', $( obj.selectors.youtubeMessageContainer ) );
		if ( $error.length > 0 ) {
			return;
		}

		$( obj.selectors.youtubeDeleteChannelId ).prop( 'disabled', function( i, v ) {
			return ! v;
		} );

		// Clear value on successful delete.
		$( this ).find( 'input' ).val( '' );
	};

	/**
	 * Initializes the default settings accordion
	 *
	 * @since 1.6.0
	 */
	obj.initSettingsAccordion = function() {
		if ( ! tribe.events.views.accordion ) {
			return;
		}

		var accordion = tribe.events.views.accordion;
		accordion.bindAccordionEvents( $( obj.selectors.youtubeAccordionContainer ) );
	};

	/**
	 * Handles the initialization of the admin when Document is ready
	 *
	 * @since 1.6.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		obj.bindEvents();
		obj.initSettingsAccordion();
	};

	// Configure on document ready
	$( obj.ready );
} )( jQuery, tribe.events.youtubeSettingsAdmin );
