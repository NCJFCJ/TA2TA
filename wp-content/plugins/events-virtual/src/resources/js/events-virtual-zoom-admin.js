/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 1.0.0
 *
 * @type   {PlainObject}
 */
window.tribe = window.tribe || {};
tribe.events = tribe.events || {};
tribe.events.views = tribe.events.views || {};

/**
 * Configures Virtual Events Admin Object on the Global Tribe variable
 *
 * @since 1.0.0
 *
 * @type {PlainObject}
 */
tribe.events.virtualAdmin = tribe.events.virtualAdmin || {};

/**
 * Configures Virtual Events Admin Object on the Global Tribe variable
 *
 * @since 1.0.0
 * @deprecated 1.9.0 - Replaced with tribe.events.virtualAdminAPI
 *
 * @type   {PlainObject}
 */
tribe.events.virtualAdminZoom = tribe.events.virtualAdminZoom || {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since 1.0.0
 *
 * @param  {PlainObject} $            jQuery
 * @param  {PlainObject} obj          tribe.events.virtualAdminZoom
 * @param  {PlainObject} virtualAdmin tribe.events.virtualAdmin
 *
 * @return {void}
 */
( function( $, obj, virtualAdmin, tribe_dropdowns ) {
	'use-strict';
	var $document = $( document );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 1.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		configureZoom: '.tribe-events-virtual-meetings-zoom-details__generate-zoom-button',
		displayLinkOption: '#tribe-events-virtual-meetings-zoom-display-details',
		embedVideoOption: '#tribe-events-virtual-embed-video',
		hidden: '.tribe-events-virtual-hidden',
		linkedButtonOption: '#tribe-events-virtual-linked-button',
		accountSelect: '.tribe-events-virtual-meetings-zoom-details__account-select-link',
		hostsDropdown: '#tribe-events-virtual-zoom-host',
		createOptions: '.tribe-events-virtual-meetings-zoom-details__types',
		meetingHostsDropdown: '#tribe-events-virtual-zoom-host',
		meetingCreate: '.tribe-events-virtual-meetings-zoom-details__create-link',
		meetingDetails: '.tribe-events-virtual-meetings-zoom-details',
		meetingDetailsFloat: '.tribe-events-virtual-meetings-zoom-details--float',
		meetingRemove: '.tribe-events-virtual-meetings-zoom-details__remove-link',
		remove: '.tribe-remove-virtual-event',
		setupZoomCheckbox: '#tribe-events-virtual-zoom-link-generate',
		urlField: '.tribe-events-virtual-video-source__virtual-url-input',
		videoSource: '#tribe-events-virtual-video-source',
		virtualContainer: '#tribe-virtual-events',
		zoomMeetingsContainer: '#tribe-events-virtual-meetings-zoom',
		zoomType: 'input[name="tribe-events-virtual[zoom-meeting-type]"]:checked',
		zoomLoader: '.tribe-common-c-loader',
		zoomHiddenElement: '.tribe-common-a11y-hidden',
		zoomMessagesWrap: '.tec-events-virtual-video-source-zoom-setup__messages-wrap',
		zoomMessage: '.tec-events-virtual-settings-message__wrap',
	};

	/**
	 * Original state of the UI controls related to Zoom meetings.
	 *
	 * @since 1.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.originalState = {
		linkedButtonOption: {
			checked: $( obj.selectors.linkedButtonOption ).prop( 'checked' ),
		},
	};

	/**
	 * State of the UI controls related to Zoom meeting.
	 *
	 * @since 1.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.state = {
		urlField: {
			value: '',
		},
	};

	/**
	 * Handles the click on a link to generate a meeting.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 - Include host id with the ajax request.
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleAccountSelection = function( ev ) {
		ev.preventDefault();
		var url = $( ev.target ).attr( 'href' );
		var accountId = $( '#tribe-events-virtual-zoom-account option:selected' ).val();

		obj.show( $( obj.selectors.zoomMeetingsContainer ) );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $( obj.selectors.zoomMeetingsContainer ),
				data: {
					zoom_account_id: accountId,
				},
				success: obj.onMeetingHandlingSuccess,
			}
		);
	};

	/**
	 * Handles the selection of the host dropdown validation.
	 *
	 * @since 1.8.2
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleUserValidation = function( ev ) {
		ev.preventDefault();
		var url = $( obj.selectors.hostsDropdown ).data('validateUrl');
		var hostId = $( obj.selectors.hostsDropdown ).find( 'option:selected' ).val();
		var accountId = $( obj.selectors.zoomMeetingsContainer ).data( 'accountId' );
		if ( ! url || ! hostId || ! accountId ) {
			return;
		}

		obj.show( $( obj.selectors.zoomMeetingsContainer ) );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $( obj.selectors.createOptions ),
				data: {
					zoom_account_id: accountId,
					zoom_host_id: hostId,
				},
				success: obj.onMeetingValidateUserSuccess,
			}
		);
	};

	/**
	 * Handles the successful response from the backend to a user validation request.
	 *
	 * @since 1.8.2
	 *
	 * @param {string} html The HTML that should replace the current meeting selection.
	 */
	obj.onMeetingValidateUserSuccess = function( html ) {
		obj.hide( $( obj.selectors.zoomMeetingsContainer ) );

		const $message = $( html ).filter( obj.selectors.zoomMessage );
		const $createOptions = $( html ).filter( obj.selectors.createOptions );

		$( obj.selectors.zoomMessagesWrap ).html( $message );

		if ( 0 === $createOptions.length ) {
			return;
		}

		$( obj.selectors.createOptions ).replaceWith( $createOptions );
	};

	/**
	 * Handles the click on a link to generate a meeting.
	 *
	 * @since 1.0.0
	 * @since 1.4.0 - Include host id with the ajax request.
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleMeetingRequest = function( ev ) {
		ev.preventDefault();
		var url = $( obj.selectors.zoomType ).val();
		var hostId = $( obj.selectors.hostsDropdown ).find( 'option:selected' ).val();
		var accountId = $( obj.selectors.zoomMeetingsContainer ).data( 'accountId' );

		obj.show( $( obj.selectors.zoomMeetingsContainer ) );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $( obj.selectors.zoomMeetingsContainer ),
				data: {
					zoom_host_id: hostId,
					zoom_account_id: accountId,
				},
				success: obj.onMeetingHandlingSuccess,
			}
		);
	};

	/**
	 * Handles the click on a link to remove a meeting.
	 *
	 * @since 1.4.0
	 * @since 1.5.0 - Add confirmation check and move ajax call to it's own method.
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleRemoveRequest = function( ev ) {
		ev.preventDefault();

		var confirmed = confirm( tribe_events_virtual_placeholder_strings.removeConfirm );
		if ( ! confirmed ) {
			return;
		}

		var url = $( ev.target ).attr( 'href' );
		if ( ! url ) {
			return;
		}

		obj.removeRequestAjax( url );
	};

	/**
	 * Ajax call to remove Zoom details from Event.
	 *
	 * @param {string} url The url to make the ajax call.
	 */
	obj.removeRequestAjax = function( url ) {
		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $( obj.selectors.zoomMeetingsContainer ),
				success: obj.onMeetingHandlingSuccess,
			}
		);
	};

	/**
	 * Handles the successful response from the backend to a meeting-related request.
	 *
	 * @since 1.0.0
	 * @since 1.6.0 - Support for video sources dropdown.
	 *
	 * @param {string} html The HTML that should replace the current meeting controls HTML.
	 */
	obj.onMeetingHandlingSuccess = function( html ) {
		$( obj.selectors.zoomMeetingsContainer ).replaceWith( html );
		obj.setupControls();
		obj.checkButtons();
		obj.initTribeDropdowns();
		$( virtualAdmin.selectors.videoSource ).trigger( 'verify.dependency' );

		if (
			virtualAdmin.handleShowOptionInteractivity &&
			typeof virtualAdmin.handleShowOptionInteractivity === 'function'
		) {
			virtualAdmin.handleShowOptionInteractivity();
		}
	};

	/**
	 * Handle the Autodetect Response for Zoom.
	 *
	 * @since 1.8.0
	 *
	 * @param {Event} event The trigger event.
	 * @param {data} data The data object included with the trigger event.
	 */
	obj.handleAutoDetectZoom = function( event, data ) {
		if ( ! data.html ) {
			return;
		}

		const $meetingDetails = $( data.html ).filter( obj.selectors.zoomMeetingsContainer );
		if ( 0 === $meetingDetails.length ) {
			return;
		}

		$( obj.selectors.zoomMeetingsContainer ).html( $meetingDetails );
		obj.setupControls();
		obj.checkButtons();
		obj.initTribeDropdowns();
		$( virtualAdmin.selectors.videoSource )
			.val('zoom')
			.trigger('change')
			.trigger( 'verify.dependency' );

		if (
			virtualAdmin.handleShowOptionInteractivity &&
			typeof virtualAdmin.handleShowOptionInteractivity === 'function'
		) {
			virtualAdmin.handleShowOptionInteractivity();
		}
	};

	/**
	 * Wait for Virtual Events to become visible to correctly setup fields.
	 *
	 * @since 1.4.0
	 * @since 1.6.0 - Support for video sources dropdown.
	 */
	obj.waitForVirtualEventsToLoad = function() {
		var counter = 10;
		var checkExist = setInterval( function() {
			counter--;
			if ( $( obj.selectors.meetingDetails ).length || counter === 0 ) {
				clearInterval( checkExist );
			}
		}, 200 );
	};

	/**
	 * Ensures that when we delete the virtual meta, we also delete the Meeting meta/details.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 - modify to call ajax remove method directly to bypass new confirmation.
	 *
	 */
	obj.handleLinkedMetaRemove = function() {
		var url = $( obj.selectors.meetingRemove ).attr( 'href' );
		if ( ! url ) {
			return;
		}

		obj.removeRequestAjax( url );
	};

	/**
	 * Bind events for virtual events admin.
	 *
	 * @since 1.0.0
	 *
	 * @return {void}
	 */
	obj.bindEvents = function() {
		$( obj.selectors.virtualContainer )
			.on( 'click', obj.selectors.configureZoom, obj.setZoomCheckboxCheckedAttr( true ) )
			.on( 'click', obj.selectors.accountSelect, obj.handleAccountSelection )
			.on( 'click', obj.selectors.meetingCreate, obj.handleMeetingRequest )
			.on( 'click', obj.selectors.meetingRemove, obj.handleRemoveRequest );
		$document.on( 'virtual.delete', obj.handleLinkedMetaRemove );
		$document.on( 'autodetect.complete', obj.handleAutoDetectZoom );
		$document.on( 'change', obj.selectors.meetingHostsDropdown, obj.handleUserValidation );
	};

	/**
	 * Check both the "Linked Button" and "Zoom Link w/ details" options.
	 *
	 * @since 1.0.0
	 *
	 * @return {void}
	 */
	obj.checkButtons = function() {
		var $displayLinkOption = $( obj.selectors.displayLinkOption );
		var $linkedButtonOption = $( obj.selectors.linkedButtonOption );

		$linkedButtonOption.prop( 'checked', true );
		$displayLinkOption.prop( 'checked', true );
	};

	/**
	 * Sets up the UI controls in accord w/ the current Zoom Meeting details state.
	 *
	 * @since 1.0.0
	 *
	 * @return {void}
	 */
	obj.setupControls = function() {
		var $urlField = $( obj.selectors.urlField );
		var $embedVideoOptionItem = $( obj.selectors.embedVideoOption ).closest( 'li' );
		var $displayLinkOptionItem = $( obj.selectors.displayLinkOption ).closest( 'li' );
		var $linkedButtonOption = $( obj.selectors.linkedButtonOption );

		if ( $( obj.selectors.meetingDetails ).length ) {
			// Hide the "Embed Video" option.
			$embedVideoOptionItem.addClass( obj.selectors.hidden.className() );
			// Show the Zoom link display option.
			$displayLinkOptionItem.removeClass( obj.selectors.hidden.className() );
			// Store URL field value in state.
			obj.state.urlField.value = $urlField.val();
			$urlField.val( '' );
		} else {
			// Enable the URL field.
			$urlField
				.prop( { disabled: false } )
				.attr(
					'placeholder',
					tribe_events_virtual_placeholder_strings.video
				);
			// Show the "Embed Video" option.
			$embedVideoOptionItem.removeClass( obj.selectors.hidden.className() );
			// Hide the Zoom link display option.
			$displayLinkOptionItem.addClass( obj.selectors.hidden.className() );
			// Restore the status of the "Linked Button" option to its original state.
			$linkedButtonOption.prop( 'checked', obj.originalState.linkedButtonOption.checked );
			// Restore URL field value from state.
			$urlField.val( obj.state.urlField.value );
			obj.state.urlField.value = '';
		}
	};

	/**
	 * Initialize Tribe Dropdowns in the Meeting Container.
	 *
	 * @since 1.4.0
	 */
	obj.initTribeDropdowns = function() {
		var $zoomMeetingContainer = $( document ).find( obj.selectors.zoomMeetingsContainer );
		var $dropdowns = $zoomMeetingContainer
			.find( tribe_dropdowns.selector.dropdown )
			.not( tribe_dropdowns.selector.created );

		// Initialize dropdowns
		$dropdowns.tribe_dropdowns();
	};

	/**
	 * Sets checkbox checked attribute
	 *
	 * @since 1.4.0
	 *
	 * @param {boolean} checked whether the checkbox is checked or not
	 *
	 * @return {function} Handler to check the checkbox or not
	 */
	obj.setZoomCheckboxCheckedAttr = function( checked ) {
		return function() {
			$( obj.selectors.setupZoomCheckbox )
				.prop( 'checked', checked )
				.trigger( 'verify.dependency' );
		};
	};

	/**
	 * Show loader for the container.
	 *
	 * @since 1.8.2
	 *
	 * @param {jQuery} $container jQuery object of the container.
	 *
	 * @return {void}
	 */
	obj.show = function( $container ) {
		const $loader = $container.find( obj.selectors.zoomLoader );

		if ( $loader.length ) {
			$loader.removeClass( obj.selectors.zoomHiddenElement.className() );
		}
	};

	/**
	 * Hide loader for the container.
	 *
	 * @since 1.8.2
	 *
	 * @param {jQuery} $container jQuery object of the container.
	 *
	 * @return {void}
	 */
	obj.hide = function( $container ) {
		const $loader = $container.find( obj.selectors.zoomLoader );

		if ( $loader.length ) {
			$loader.addClass( obj.selectors.zoomHiddenElement.className() );
		}
	};

	/**
	 * Handles the initialization of the admin when Document is ready
	 *
	 * @since 1.0.0
	 * @deprecated 1.9.0 - Replaced with tribe.events.virtualAdminAPI
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		console.info( 'Scripts deprecated and replaced with tribe.events.virtualAdminAPI.' ); // eslint-disable-line no-console, max-len
		obj.bindEvents();
	};

	// Configure on document ready
	$( obj.ready );
} )( jQuery, tribe.events.virtualAdminZoom, tribe.events.virtualAdmin, tribe_dropdowns );
