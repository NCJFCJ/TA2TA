/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 1.9.0
 *
 * @type   {PlainObject}
 */
window.tribe = window.tribe || {};
tribe.events = tribe.events || {};
tribe.events.views = tribe.events.views || {};

/**
 * Configures Virtual Events Admin Object on the Global Tribe variable
 *
 * @since 1.9.0
 *
 * @type {PlainObject}
 */
tribe.events.virtualAdmin = tribe.events.virtualAdmin || {};

/**
 * Configures Virtual Events Admin Object on the Global Tribe variable
 *
 * @since 1.9.0
 *
 * @type   {PlainObject}
 */
tribe.events.virtualAdminAPI = tribe.events.virtualAdminAPI || {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since 1.9.0
 *
 * @param  {PlainObject} $            jQuery
 * @param  {PlainObject} obj          tribe.events.virtualAdminAPI
 * @param  {PlainObject} virtualAdmin tribe.events.virtualAdmin
 *
 * @return {void}
 */
( function( $, obj, virtualAdmin, tribe_dropdowns ) {
	'use-strict';
	const $document = $( document );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 1.9.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		// Meeting API Selectors
		meetingContainer: '.tec-events-virtual-meetings-api-container',
		meetingAccountDropdown: '.tec-events-virtual-meetings-api__account-dropdown',
		meetingAccountSelect: '.tec-events-virtual-meetings-api-action__account-select-link',
		meetingCreate: '.tec-events-virtual-meetings-api-action__create-link',
		meetingCreateOptions: '.tec-events-virtual-meetings-api-create__types',
		meetingCreateType: 'input[name="tribe-events-virtual[%%APIID%%-meeting-type]"]:checked',
		meetingHostsDropdown: '.tec-events-virtual-meetings-api__host-dropdown',
		meetingRemove: '.tec-events-virtual-meetings-api-details__remove-link',
		meetingMessage: '.tec-events-virtual-settings-message__wrap',
		meetingMessagesWrap: '.tec-events-virtual-video-source-api-setup__messages-wrap',
		meetingDisplayLinkOption: '#tribe-events-virtual-meetings-api-display-details',
		// API Selectors
		googleMeetContainer: '#tribe-events-virtual-meetings-google',
		microsoftTeamsContainer: '#tribe-events-virtual-meetings-microsoft',
		webexMeetingContainer: '#tribe-events-virtual-meetings-webex',
		zoomMeetingContainer: '#tribe-events-virtual-meetings-zoom',
		// VE related selectors
		virtualContainer: '#tribe-virtual-events',
		virtualEmbedVideoOption: '#tribe-events-virtual-embed-video',
		virtualHiddenElement: '.tribe-events-virtual-hidden',
		virtualLinkedButtonOption: '#tribe-events-virtual-linked-button',
		virtualLoader: '.tribe-common-c-loader',
		virtualLoaderHiddenElement: '.tribe-common-a11y-hidden',
	};

	/**
	 * Original state of the UI controls related to an API.
	 *
	 * @since 1.9.0
	 *
	 * @type {PlainObject}
	 */
	obj.originalState = {
		linkedButtonOption: {
			checked: $( obj.selectors.virtualLinkedButtonOption ).prop( 'checked' ),
		},
	};

	/**
	 * Handles the click on a link to generate a meeting.
	 *
	 * @since 1.9.0
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleAccountSelection = function( ev ) {
		ev.preventDefault();
		const $apiContainer = $( this ).closest( obj.selectors.meetingContainer );
		const url = $( ev.target ).attr( 'href' );
		const $accountDropdown = $apiContainer.find( obj.selectors.meetingAccountDropdown );
		const accountId = $accountDropdown.find( 'option:selected' ).val();

		obj.show( $apiContainer );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $apiContainer,
				data: {
					account_id: accountId,
				},
				success: obj.onMeetingHandlingSuccess,
			}
		);
	};

	/**
	 * Handles the selection of the host dropdown validation for Zoom.
	 *
	 * @since 1.9.0
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleUserValidation = function( ev ) {
		ev.preventDefault();
		const $apiContainer = $( this ).closest( obj.selectors.meetingContainer );
		const url = $( obj.selectors.meetingHostsDropdown ).data( 'validateUrl' );
		const hostId = $( obj.selectors.meetingHostsDropdown ).find( 'option:selected' ).val();
		const accountId = $apiContainer.data( 'accountId' );

		obj.show( $apiContainer );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $apiContainer,
				data: {
					account_id: accountId,
					host_id: hostId,
				},
				success: obj.onMeetingValidateUserSuccess,
			}
		);
	};

	/**
	 * Handles the response from the backend to a user validation request.
	 *
	 * @since 1.9.0
	 *
	 * @param {string} html The HTML that should replace the current meeting selection.
	 */
	obj.onMeetingValidateUserSuccess = function( html ) {

		const $apiContainer = $( this ).closest( obj.selectors.meetingContainer );
		obj.hide( $apiContainer );

		const $message = $( html ).filter( obj.selectors.meetingMessage );
		$apiContainer.find( obj.selectors.meetingMessagesWrap ).html( $message );

		const $createOptions = $( html ).filter( obj.selectors.meetingCreateOptions );
		if ( 0 === $createOptions.length ) {
			return;
		}

		$apiContainer.find( obj.selectors.meetingCreateOptions ).replaceWith( $createOptions );
	};

	/**
	 * Handles the click on a link to generate a meeting.
	 *
	 * @since 1.9.0
	 * @since 1.11.0 - Add support for password requirements.
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleCreateRequest = function( ev ) {
		ev.preventDefault();
		const $apiContainer = $( this ).closest( obj.selectors.meetingContainer );
		const apiId = $apiContainer.data( 'apiId' );
		const meetingCreateType = obj.selectors.meetingCreateType;
		const meetingApiCreateType = meetingCreateType.replace('%%APIID%%', apiId);
		const meetingType = $( meetingApiCreateType ).data( 'type' );
		const meetingApiPasswordRequirements = $( meetingApiCreateType ).data( 'passwordRequirements' );
		const url = $apiContainer.find( meetingApiCreateType ).val();
		const accountId = $apiContainer.data( 'accountId' );
		let hostId = $apiContainer
							.find( obj.selectors.meetingHostsDropdown )
							.find( 'option:selected' ).val();

		// If a single host, it is stored in the data attribute.
		if ( ! hostId ) {
			hostId = $apiContainer
							.find( obj.selectors.meetingHostsDropdown )
							.data( 'hostId' );
		}

		$document.trigger( 'tec.virtual.api.create.validation', {
			'apiId' : apiId,
			'meetingApiCreateType' : meetingApiCreateType,
			'accountId' : accountId,
			'hostId' : hostId
		} );

		obj.show( $apiContainer );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $apiContainer,
				data: {
					host_id: hostId,
					account_id: accountId,
					password_requirements: meetingApiPasswordRequirements,
					meeting_type: meetingType,
					EventStartDate: $( '#EventStartDate' ).val(),
					EventStartTime: $( '#EventStartTime' ).val(),
					EventEndDate: $( '#EventEndDate' ).val(),
					EventEndTime: $( '#EventEndTime' ).val(),
					EventTimezone: $( '#event-timezone option:selected' ).val(),
					allDayCheckbox: $( '#allDayCheckbox' ).prop( 'checked' ) ? true : '',
				},
				success: obj.onMeetingHandlingSuccess,
			}
		);
	};

	/**
	 * Handles the click on a link to remove a meeting.
	 *
	 * @since 1.9.0
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleRemoveRequest = function( ev ) {
		ev.preventDefault();

		const confirmed = confirm( $( this ).data( 'confirmation' )  );
		if ( ! confirmed ) {
			return;
		}

		const url = $( ev.target ).attr( 'href' );
		const $apiContainer = $( this ).closest( obj.selectors.meetingContainer );

		if ( ! url ) {
			return;
		}

		obj.removeRequestAjax( url, $apiContainer );
	};

	/**
	 * Ajax call to remove an API details from an Event.
	 *
	 * @param {string} url The url to make the ajax call.
	 * @param {object} $apiContainer The API container jQuery object being removed.
	 */
	obj.removeRequestAjax = function( url, $apiContainer ) {
		if ( $apiContainer ) {
			obj.show( $apiContainer );
		}

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $apiContainer,
				success: obj.onMeetingHandlingSuccess,
			}
		);
	};

	/**
	 * Handles the successful response from the backend to a meeting-related request.
	 *
	 * @since 1.9.0
	 *
	 * @param {string} html The HTML that should replace the current meeting controls HTML.
	 */
	obj.onMeetingHandlingSuccess = function( html ) {
		const $apiContainer = $( this ).closest( obj.selectors.meetingContainer );

		$apiContainer.replaceWith( html );
		obj.setupApiFields( '' );
	};

	/**
	 * Handle the Autodetect Response for Google Meet.
	 *
	 * @since 1.11.0
	 *
	 * @param {Event} event The trigger event.
	 * @param {data} data The data object included with the trigger event.
	 */
	obj.handleAutoDetectGoogle = function( event, data ) {
		if ( ! data.html ) {
			return;
		}
		const $apiContainer = $document.find( obj.selectors.googleMeetContainer );
		const $meetingDetails = $( data.html ).filter( obj.selectors.googleMeetContainer );
		if ( 0 === $meetingDetails.length ) {
			return;
		}

		$apiContainer.html( $meetingDetails );
		obj.setupApiFields( 'google' );
	};

	/**
	 * Handle the Autodetect Response for Microsoft Teams.
	 *
	 * @since 1.13.0
	 *
	 * @param {Event} event The trigger event.
	 * @param {data} data The data object included with the trigger event.
	 */
	obj.handleAutoDetectMicrosoft = function( event, data ) {
		if ( ! data.html ) {
			return;
		}
		const $apiContainer = $document.find( obj.selectors.microsoftTeamsContainer );
		const $meetingDetails = $( data.html ).filter( obj.selectors.microsoftTeamsContainer );
		if ( 0 === $meetingDetails.length ) {
			return;
		}

		$apiContainer.html( $meetingDetails );
		obj.setupApiFields( 'microsoft' );
	};

	/**
	 * Handle the Autodetect Response for Webex.
	 *
	 * @since 1.9.0
	 *
	 * @param {Event} event The trigger event.
	 * @param {data} data The data object included with the trigger event.
	 */
	obj.handleAutoDetectWebex = function( event, data ) {
		if ( ! data.html ) {
			return;
		}
		const $apiContainer = $document.find( obj.selectors.webexMeetingContainer );
		const $meetingDetails = $( data.html ).filter( obj.selectors.webexMeetingContainer );
		if ( 0 === $meetingDetails.length ) {
			return;
		}

		$apiContainer.html( $meetingDetails );
		obj.setupApiFields( 'webex' );
	};

	/**
	 * Handle the Autodetect Response for an API.
	 *
	 * @since 1.9.0
	 *
	 * @param {Event} event The trigger event.
	 * @param {data} data The data object included with the trigger event.
	 */
	obj.handleAutoDetectZoom = function( event, data ) {
		if ( ! data.html ) {
			return;
		}
		const $apiContainer = $document.find( obj.selectors.zoomMeetingContainer );
		const $meetingDetails = $( data.html ).filter( obj.selectors.zoomMeetingContainer );
		if ( 0 === $meetingDetails.length ) {
			return;
		}

		$apiContainer.html( $meetingDetails );
		obj.setupApiFields( 'zoom' );
	};

	/**
	 * Sets up the API fields.
	 *
	 * @since 1.9.0
	 *
	 * @param api_id
	 */
	obj.setupApiFields = function( api_id ) {
		if ( api_id ) {
			$( virtualAdmin.selectors.videoSource )
				.val( api_id )
				.trigger( 'change' )
				.trigger( 'verify.dependency' );
		} else {
			$( virtualAdmin.selectors.videoSource ).trigger( 'verify.dependency' );
		}

		obj.setupControls();
		obj.checkButtons();
		obj.initTribeDropdowns();

		if (
			virtualAdmin.handleShowOptionInteractivity &&
			typeof virtualAdmin.handleShowOptionInteractivity === 'function'
		) {
			virtualAdmin.handleShowOptionInteractivity();
		}
	};

	/**
	 * Ensures that when we delete the virtual meta, we also delete the Meeting meta/details.
	 *
	 * @since 1.9.0
	 *
	 */
	obj.handleLinkedMetaRemove = function() {
		// change this to use the link in the remove by the click event
		const url = $( obj.selectors.meetingRemove ).attr( 'href' );
		if ( ! url ) {
			return;
		}

		obj.removeRequestAjax( url );
	};

	/**
	 * Check both the "Linked Button" and "Zoom Link w/ details" options.
	 *
	 * @since 1.9.0
	 *
	 * @return {void}
	 */
	obj.checkButtons = function() {
		const $displayLinkOption = $( obj.selectors.meetingDisplayLinkOption );
		const $linkedButtonOption = $( obj.selectors.virtualLinkedButtonOption );

		$linkedButtonOption.prop( 'checked', true );
		$displayLinkOption.prop( 'checked', true );
	};

	/**
	 * Sets up the UI controls in accord w/ the current API details state.
	 *
	 * @since 1.9.0
	 *
	 * @return {void}
	 */
	obj.setupControls = function() {
		const $embedVideoOptionItem = $( obj.selectors.virtualEmbedVideoOption ).closest( 'li' );
		const $displayLinkOptionItem = $( obj.selectors.meetingDisplayLinkOption ).closest( 'li' );
		const $linkedButtonOption = $( obj.selectors.virtualLinkedButtonOption );
		const videoSourceVal = $( virtualAdmin.selectors.videoSource ).val();

		if ( 'webex' === videoSourceVal || 'zoom' === videoSourceVal ) {
			// Hide the "Embed Video" option.
			$embedVideoOptionItem.addClass( obj.selectors.virtualHiddenElement.className() );
			// Show the Zoom link display option.
			$displayLinkOptionItem.removeClass( obj.selectors.virtualHiddenElement.className() );
		} else {
			// Show the "Embed Video" option.
			$embedVideoOptionItem.removeClass( obj.selectors.virtualHiddenElement.className() );
			// Hide the Zoom link display option.
			$displayLinkOptionItem.addClass( obj.selectors.virtualHiddenElement.className() );
			// Restore the status of the "Linked Button" option to its original state.
			$linkedButtonOption.prop( 'checked', obj.originalState.linkedButtonOption.checked );
		}
	};

	/**
	 * Initialize Tribe Dropdowns in the API Containers.
	 *
	 * @since 1.9.0
	 */
	obj.initTribeDropdowns = function() {
		const $apiContainer = $( document ).find( obj.selectors.meetingContainer );
		const $dropdowns = $apiContainer
			.find( tribe_dropdowns.selector.dropdown )
			.not( tribe_dropdowns.selector.created );

		// Initialize dropdowns
		$dropdowns.tribe_dropdowns();
	};

	/**
	 * Show loader for the container.
	 *
	 * @since 1.9.0
	 *
	 * @param {jQuery} $container jQuery object of the container.
	 *
	 * @return {void}
	 */
	obj.show = function( $container ) {
		const $loader = $container.find( obj.selectors.virtualLoader );

		if ( $loader.length ) {
			$loader.removeClass( obj.selectors.virtualLoaderHiddenElement.className() );
		}
	};

	/**
	 * Hide loader for the container.
	 *
	 * @since 1.9.0
	 *
	 * @param {jQuery} $container jQuery object of the container.
	 *
	 * @return {void}
	 */
	obj.hide = function( $container ) {
		const $loader = $container.find( obj.selectors.virtualLoader );

		if ( $loader.length ) {
			$loader.addClass( obj.selectors.virtualLoaderHiddenElement.className() );
		}
	};

	/**
	 * Bind events for virtual events admin.
	 *
	 * @since 1.9.0
	 *
	 * @return {void}
	 */
	obj.bindEvents = function() {
		$( obj.selectors.virtualContainer )
			.on( 'click', obj.selectors.meetingAccountSelect, obj.handleAccountSelection )
			.on( 'click', obj.selectors.meetingCreate, obj.handleCreateRequest )
			.on( 'click', obj.selectors.meetingRemove, obj.handleRemoveRequest );
		$document.on( 'virtual.delete', obj.handleLinkedMetaRemove );
		$document.on( 'change', obj.selectors.meetingHostsDropdown, obj.handleUserValidation );

		$document
			.on( 'autodetect.complete', obj.handleAutoDetectGoogle )
			.on( 'autodetect.complete', obj.handleAutoDetectMicrosoft )
			.on( 'autodetect.complete', obj.handleAutoDetectWebex )
			.on( 'autodetect.complete', obj.handleAutoDetectZoom );
	};

	/**
	 * Handles the initialization of the admin when Document is ready
	 *
	 * @since 1.9.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		obj.bindEvents();
	};

	// Configure on document ready
	$( obj.ready );
} )( jQuery, tribe.events.virtualAdminAPI, tribe.events.virtualAdmin, tribe_dropdowns );
