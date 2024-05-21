/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 1.9.0
 *
 * @type {PlainObject}
 */
tribe.events = tribe.events || {};

/**
 * Configures Virtual Events Admin Object on the Global Tribe variable
 *
 * @since 1.9.0
 *
 * @type {PlainObject}
 */
tribe.events.apiSettingsAdmin = tribe.events.apiSettingsAdmin || {};

( function( $, obj ) {
	'use-strict';

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 1.9.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		apiContainer: '.tec-settings-api-application',
		accountDetailsContainer: '.tec-settings-api-account-details',
		accountMessageContainer: '.tec-api-accounts-messages',
		accountStatus: '.tec-events-virtual-meetings-api-settings-switch__input.account-status',
		deleteAccount: '.tec-settings-api-account-details__delete-account',
		refreshAccount: '.tec-settings-api-account-details__account-refresh',
	};

	/**
	 * Handles the click to refresh an account
	 *
	 * @since 1.9.0
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleRefreshAccount = function( ev ) {
		ev.preventDefault();

		const confirmed = confirm(  $( this ).data( 'confirmation' ) );
		if ( ! confirmed ) {
			return;
		}

		const url = $( this ).data( 'apiRefresh' );
		window.location = url;
	};

	/**
	 * Handles the click to change the account status.
	 *
	 * @since 1.9.0
	 */
	obj.handleAccountStatus = function() {
		const $this = $( this );
		const url = $this.data( 'ajaxStatusUrl' );

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
	 * @since 1.9.0
	 *
	 * @param {string} html The HTML that adds a message on the settings page.
	 */
	obj.onAccountStatusSuccess = function( html ) {
		$( this ).closest( obj.selectors.apiContainer )
			.find( obj.selectors.accountMessageContainer ).html( html );

		// Enable the status switch.
		$( this ).prop( 'disabled', false );

		// Change the disable state of the refresh and delete buttons.
		const $accountSettings = $( this ).closest( obj.selectors.accountDetailsContainer );
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
	 * @since 1.9.0
	 *
	 * @param {Event} ev The click event.
	 */
	obj.handleDeleteAccount = function( ev ) {
		ev.preventDefault();

		const confirmed = confirm( $( this ).data( 'confirmation' )  );
		if ( ! confirmed ) {
			return;
		}

		const url = $( this ).data( 'ajaxDeleteUrl' );

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
	 * @since 1.9.0
	 *
	 * @param {string} html The HTML that adds a message on the settings page.
	 */
	obj.onAccountDeleteSuccess = function( html ) {
		$( this ).closest( obj.selectors.apiContainer )
			.find( obj.selectors.accountMessageContainer ).html( html );

		// Check if this is an error message.
		const $error = $( '.error', $( obj.selectors.accountMessageContainer ) );
		if ( $error.length > 0 ) {
			return;
		}

		// Remove the account from the list.
		$( this ).remove();
	};

	/**
	 * Bind Events for API Account Management.
	 *
	 * @since 1.9.0
	 *
	 */
	obj.bindEvents = function() {
		$( obj.selectors.apiContainer )
			.on( 'click', obj.selectors.refreshAccount, obj.handleRefreshAccount )
			.on( 'click', obj.selectors.accountStatus, obj.handleAccountStatus )
			.on( 'click', obj.selectors.deleteAccount, obj.handleDeleteAccount );
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
} )( jQuery, tribe.events.apiSettingsAdmin );
