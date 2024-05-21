/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 1.0.1
 * @deprecated 1.9.0 - Replaced with tribe.events.apiSettingsAdmin
 *
 * @type {PlainObject}
 */
tribe.events = tribe.events || {};

/**
 * Configures Virtual Events Admin Object on the Global Tribe variable
 *
 * @since 1.0.1
 * @deprecated 1.9.0 - Replaced with tribe.events.apiSettingsAdmin
 *
 * @type {PlainObject}
 */
tribe.events.zoomSettingsAdmin = tribe.events.zoomSettingsAdmin || {};

( function( $, obj ) {
	'use-strict';

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 1.0.1
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		authorizedClass: 'tribe-zoom-authorized',
		clientIdInput: '#zoom-application__client-id',
		clientSecretInput: '#zoom-application__client-secret',
		refreshAccount: '.tribe-settings-zoom-account-details__account-refresh',
		accountStatus: '.tribe-events-virtual-meetings-zoom-settings-switch__input.account-status',
		deleteAccount: '.tribe-settings-zoom-account-details__delete-account',
		accountDetailsContainer: '.tribe-settings-zoom-account-details',
		accountMessageContainer: '.tec-zoom-accounts-messages',
		virtualContainer: '#tribe-settings-zoom-application',
		zoomToken: '#tribe-field-zoom_token',
	};

	obj.handleConnectButton = function() {
		const clientId = $( obj.selectors.clientIdInput ).val();
		const clientSecret = $( obj.selectors.clientSecretInput ).val();

		const nonce = $( obj.selectors.virtualContainer ).attr( 'data-nonce' );
		const data = {
			action: 'events_virtual_meetings_zoom_autosave_client_keys',
			clientId: clientId,
			clientSecret: clientSecret,
			security: nonce,
		};

		$.ajax( {
			type: 'post',
			url: ajaxurl,
			dataType: 'text/html',
			data: data,
		} )
			.always( obj.swapConnectButton );
	};

	obj.swapConnectButton = function( response ) {
		if ( 'undefined' === typeof response.responseText ) {
			return;
		}

		const html = response.responseText;
		$( obj.selectors.zoomToken ).find( '.tribe-field-wrap' ).html( html );
	};

	obj.bindEvents = function() {
		if ( $( obj.selectors.virtualContainer ).hasClass( obj.selectors.authorizedClass ) ) {
			return;
		}

		$( obj.selectors.virtualContainer )
			.on( 'click', obj.selectors.refreshAccount, obj.handleRefreshAccount )
			.on( 'click', obj.selectors.accountStatus, obj.handleAccountStatus )
			.on( 'click', obj.selectors.deleteAccount, obj.handleDeleteAccount )
			.on( 'blur', obj.selectors.clientIdInput, obj.handleConnectButton )
			.on( 'blur', obj.selectors.clientSecretInput, obj.handleConnectButton );
	};

	/**
	 * Handles the click to refresh an account
	 *
	 * @since 1.5.0
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleRefreshAccount = function( ev ) {
		ev.preventDefault();

		var confirmed = confirm( tribe_events_virtual_settings_strings.refreshConfirm );
		if ( ! confirmed ) {
			return;
		}

		var url = $( this ).data( 'zoomRefresh' );
		window.location = url;
	};

	/**
	 * Handles the click to change the account status.
	 *
	 * @since 1.5.0
	 */
	obj.handleAccountStatus = function() {
		var $this = $( this );
		var url = $this.data( 'ajaxStatusUrl' );

		// Disable the status switch.
		$this.prop( 'disabled', true );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $this,
				success: obj.onAccountStatusSuccess,
			}
		);
	};

	/**
	 * Handles the successful response from the backend to account status request.
	 *
	 * @since 1.5.0
	 *
	 * @param {string} html The HTML that adds a message on the settings page.
	 */
	obj.onAccountStatusSuccess = function( html ) {
		$( obj.selectors.accountMessageContainer ).html( html );

		// Enable the status switch.
		$( this ).prop( 'disabled', false );

		// Change the disable state of the refresh and delete buttons.
		var $accountSettings = $( this ).closest( obj.selectors.accountDetailsContainer );
		$accountSettings.find( obj.selectors.refreshAccount ).prop( 'disabled', function( i, v ) {
			return ! v;
		} );
		$accountSettings.find( obj.selectors.deleteAccount ).prop( 'disabled', function( i, v ) {
			return ! v;
		} );
	};

	/**
	 * Handles the click to delete an account.
	 *
	 * @since 1.5.0
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleDeleteAccount = function( ev ) {
		ev.preventDefault();

		var confirmed = confirm( tribe_events_virtual_settings_strings.deleteConfirm );
		if ( ! confirmed ) {
			return;
		}

		var url = $( this ).data( 'ajaxDeleteUrl' );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $( this ).closest( obj.selectors.accountDetailsContainer ),
				success: obj.onAccountDeleteSuccess,
			}
		);
	};

	/**
	 * Handles the successful response from the backend to delete account request.
	 *
	 * @since 1.5.0
	 *
	 * @param {string} html The HTML that adds a message on the settings page.
	 */
	obj.onAccountDeleteSuccess = function( html ) {
		$( obj.selectors.accountMessageContainer ).html( html );

		// Check if this is an error message.
		var $error = $( '.error', $( obj.selectors.accountMessageContainer ) );
		if ( $error.length > 0 ) {
			return;
		}

		// Remove the account from the list.
		$( this ).remove();
	};

	/**
	 * Handles the initialization of the admin when Document is ready
	 *
	 * @since 1.0.1
	 * @deprecated 1.9.0 - Replaced with tribe.events.apiSettingsAdmin
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		console.info( 'Scripts deprecated and replaced with tribe.events.apiSettingsAdmin.' ); // eslint-disable-line no-console, max-len
		obj.bindEvents();
	};

	// Configure on document ready
	$( obj.ready );
} )( jQuery, tribe.events.zoomSettingsAdmin );
