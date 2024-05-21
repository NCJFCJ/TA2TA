/* eslint-disable template-curly-spacing */
/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 1.7.0
 *
 * @type {PlainObject}
 */
tribe.events = tribe.events || {};

/**
 * Configures Virtual Events Admin Object on the Global Tribe variable
 *
 * @since 1.7.0
 *
 * @type {PlainObject}
 */
tribe.events.facebookSettingsAdmin = tribe.events.facebookSettingsAdmin || {};

( function( $, obj ) {
	'use-strict';
	const $document = $( document );
	obj.GraphVersion = 'v9.0';

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 1.7.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		messageWrap: '.tec-events-virtual-settings-message__wrap',
		facebookContainer: '.tribe-settings-facebook-integration',
		facebookSaveApp: '.tribe-settings-facebook-application__connect-container',
		facebookSaveAppBtn: '.tribe-settings-facebook-application__connect-button',
		facebookPages: '.tribe-settings-facebook-application-pages__container',
		facebookPageList: '.tribe-settings-facebook-page-list',
		facebookAddPage: '.tribe-events-virtual-meetings-facebook-settings__add-page-button',
		facebookPageContainer: '.tribe-settings-facebook-page-details__container',
		facebookAppMessageContainer: '.tec-facebook-app-messages',
		facebookPageMessageContainer: '.tec-facebook-page-messages',
		facebookPageName: '.tribe-settings-facebook-page-details__page-name-input',
		facebookPageId: '.tribe-settings-facebook-page-details__page-id-input',
		facebookAccessToken: '.tribe-settings-facebook-page-details__page-access-token-input',
		facebookClearAccess: '.tribe-settings-facebook-page-details__clear-access',
		facebookDeletePage: '.tribe-settings-facebook-page-details__delete-page',
		facebookSavePage: '.tribe-settings-facebook-page-details__save-page',

		facebookAppId: 'input[name="tribe_facebook_app_id"]',
		facebookAppSecret: 'input[name="tribe_facebook_app_secret"]',
		autoDetectPreviewClass: '.tec-virtual-video-embed__facebook',
	};

	/**
	 * Handles the successful response from the backend to save a Facebook Page.
	 *
	 * @since 1.7.0
	 *
	 * @param {string} html The HTML that adds a message on the settings page.
	 */
	obj.onAppSaveSuccess = function( html ) {
		const $message = $( html ).filter( obj.selectors.messageWrap );
		const $facebookPages = $( html ).filter( obj.selectors.facebookPages );

		$( obj.selectors.facebookAppMessageContainer ).html( $message );

		if ( 0 === $facebookPages.length ) {
			return;
		}

		$( obj.selectors.facebookSaveApp ).replaceWith( $facebookPages );
	};

	/**
	 * Handles saving the Facebook ID and Secret.
	 *
	 * @since 1.7.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleSaveApp = function( event ) {
		event.preventDefault();

		const $this = $( this );
		const url = $this.data( 'ajaxSaveUrl' );
		const facebookAppId = $( obj.selectors.facebookAppId ).val();
		const facebookAppSecret = $( obj.selectors.facebookAppSecret ).val();

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $( this ).closest( obj.selectors.facebookContainer ),
				data: {
					facebook_app_id: facebookAppId,
					facebook_app_secret: facebookAppSecret,
				},
				success: obj.onAppSaveSuccess,
			}
		);
	};

	/**
	 * Enables saving of a page once there is a page name and page id.
	 *
	 * @since 1.7.0
	 */
	obj.handleEnableSave = function() {
		const $this = $( this );
		const $page = $this.closest( obj.selectors.facebookPageContainer );
		const pageName = $page.find( obj.selectors.facebookPageName ).val();
		const pageId = $page.find( obj.selectors.facebookPageId ).val();
		const $pageSave = $page.find( obj.selectors.facebookSavePage );

		$pageSave.prop( 'disabled', true );
		if ( pageName && pageId ) {
			$pageSave.prop( 'disabled', false );
		}
	};

	/**
	 * Handles the successful response from the backend to save a Facebook Page.
	 *
	 * @since 1.7.0
	 *
	 * @param {string} html The HTML that adds a message and the page fields html.
	 */
	obj.onPageSaveSuccess = function( html ) {
		const $message = $( html ).filter( obj.selectors.messageWrap );
		const $facebookPage = $( html ).filter( obj.selectors.facebookPageContainer );

		$( obj.selectors.facebookPageMessageContainer ).html( $message );

		if ( 0 === $facebookPage.length ) {
			return;
		}

		const localId = $facebookPage.data( 'localId' );
		const existingPage = $document.find( `[data-local-id='${localId}']` );
		existingPage.replaceWith( $facebookPage );

		// Reload the Facebook buttons.
		FB.XFBML.parse(); // eslint-disable-line no-undef
	};

	/**
	 * Handles saving the Page Name and ID
	 *
	 * @since 1.7.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleSavePage = function( event ) {
		event.preventDefault();

		const $this = $( this );
		const url = $this.data( 'ajaxSaveUrl' );
		const $page = $this.closest( obj.selectors.facebookPageContainer );
		const localId = $page.data( 'localId' );
		const pageName = $page.find( obj.selectors.facebookPageName ).val();
		const pageId = $page.find( obj.selectors.facebookPageId ).val();
		const accessToken = $page.find( obj.selectors.facebookAccessToken ).val();

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $( this ).closest( obj.selectors.facebookPageContainer ),
				data: {
					local_id: localId,
					page_name: pageName,
					page_id: pageId,
					access_token: accessToken,
				},
				success: obj.onPageSaveSuccess,
			}
		);
	};

	/**
	 * Handles clearing the Faceboook Page name and id.
	 *
	 * @since 1.7.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleClearAccess = function( event ) {
		event.preventDefault();

		const $this = $( this );
		const url = $this.attr( 'href' );
		const $facebookPage = $this.closest( obj.selectors.facebookPageContainer );
		const localId = $facebookPage.data( 'localId' );
		const confirmed = confirm(
			tribe_events_virtual_facebook_settings_strings.pageClearAccessConfirmation
		);
		if ( ! confirmed ) {
			return;
		}

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $facebookPage,
				data: {
					local_id: localId,
				},
				success: obj.onPageSaveSuccess,
			}
		);
	};

	/**
	 * Handles the successful response from the backend to delete a Facebook page.
	 *
	 * @since 1.7.0
	 *
	 * @param {string} html The HTML that adds a message on the settings page.
	 */
	obj.onPageDeleteSuccess = function( html ) {
		$( obj.selectors.facebookPageMessageContainer ).html( html );

		// Delete marked Facebook Page wrap.
		$( `${ obj.selectors.facebookPageContainer }.to-delete` ).remove();
	};

	/**
	 * Handles deleting the Facebook Page.
	 *
	 * @since 1.7.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleDeletePage = function( event ) {
		event.preventDefault();

		const $this = $( this );
		const url = $this.data( 'ajaxDeleteUrl' );
		const $facebookPage = $this.closest( obj.selectors.facebookPageContainer );
		const localId = $facebookPage.data( 'localId' );
		const confirmed = confirm(
			tribe_events_virtual_facebook_settings_strings.pageDeleteConfirmation
		);
		if ( ! confirmed ) {
			return;
		}

		// Add a class to mark for deletion.
		$facebookPage.addClass( 'to-delete' );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $( this ).closest( obj.selectors.facebookPageContainer ),
				data: {
					local_id: localId,
				},
				success: obj.onPageDeleteSuccess,
			}
		);
	};

	/**
	 * Initialize the Facebook SDK.
	 *
	 * @since 1.7.0
	 * @since 1.7.3 Add check for Facebook App ID.
	 *
	 */
	obj.facebookInit = function() {
		if ( typeof FB === 'undefined' ) {
			return false;
		}

		// If no app id on the page load from the localized option.
		let facebookAppId = $( obj.selectors.facebookAppId ).val();
		if ( ! facebookAppId || facebookAppId < 1 ) {
			facebookAppId = tribe_events_virtual_facebook_settings_strings.facebookAppId;
		}

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
	 * Display a message.
	 *
	 * @since 1.7.0
	 *
	 * @param {string} message The message to display.
	 * @param {string} messageClass The class of the message: updated(default) or error.
	 */
	obj.displayMessage = function( message, messageClass = 'updated' ) { // eslint-disable-line es5/no-default-parameters,max-len
		const messageWrap = `
			<div
				id="tribe-events-virtual-settings-message"
				class="tec-events-virtual-settings-message__wrap ${messageClass}"
			>
				${message}
			</div>
		`;

		$( obj.selectors.facebookPageMessageContainer ).html( messageWrap );
	};

	/**
	 * Handles saving the Page Access Token
	 *
	 * @since 1.7.0
	 *
	 * @param {string} localId The local local_id used to save the Facebook Page name and id.
	 * @param {object} $facebookPage The jQuery object of the Facebook page wrap div being authorized.
	 * @param {integer} pageId The Facebook page id.
	 * @param {integer} accessToken The Facebook user long term token.
	 */
	obj.handleSavePageAccess = function( localId, $facebookPage, pageId, accessToken ) {
		const url = $facebookPage.data( 'ajaxSaveAccessUrl' );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $facebookPage,
				data: {
					local_id: localId,
					page_id: pageId,
					access_token: accessToken,
				},
				success: obj.onPageSaveSuccess,
			}
		);
	};

	/**
	 * Get a Facebook Page access token.
	 *
	 * @param {integer} pageId The Facebook page id.
	 * @param {integer} accessToken The Facebook user long term token.
	 * @param {object} $facebookPage The jQuery object of the Facebook page wrap div being authorized.
	 */
	obj.getPageAccessToken = function( pageId, accessToken, $facebookPage ) {
		$.ajax( {
			url: `https://graph.facebook.com/${pageId}?fields=access_token&access_token=${accessToken}`,
			type: 'GET',
			dataType: 'json',
			success: function( result ) {
				if ( 'undefined' === typeof result.access_token ) {
					const errorMessage = tribe_events_virtual_facebook_settings_strings.pageTokenFailure;
					// no translation of the error so it can be provided to support.
					const error = 'no page access token';
					obj.displayMessage( `${errorMessage}: ${error}`, 'error' );

					return;
				}

				$facebookPage.find( obj.selectors.facebookAccessToken ).val( result.access_token );
				obj.handleSavePageAccess( $facebookPage.data( 'localId' ), $facebookPage, pageId, result.access_token ); // eslint-disable-line max-len
			},
			error: function( xhr, ajaxOptions, error ) {
				const errorMessage = tribe_events_virtual_facebook_settings_strings.pageTokenFailure;
				obj.displayMessage( `${errorMessage}: ${error}`, 'error' );
			},
		} );
	};

	/**
	 * Get a Facebook user long lived access token.
	 *
	 * @since 1.7.0
	 *
	 * @param {string} appId The Facebook app id.
	 * @param {string} appSecret Teh Facebook app secret.
	 * @param {string} userAccessToken The short term user access token from Facebook.
	 * @param {integer} pageId The Facebook page id.
	 * @param {object} $facebookPage The jQuery object of the Facebook page wrap div being authorized.
	 */
	obj.getExtendedAccessToken = function( appId, appSecret, userAccessToken, pageId, $facebookPage ) { // eslint-disable-line max-len
		if (
			! appId ||
			! appSecret ||
			! userAccessToken ||
			! pageId ||
			! $facebookPage
		) {
			const errorMessage = tribe_events_virtual_facebook_settings_strings.userTokenFailure;
			// no translation of the error so it can be provided to support.
			const error = 'missing parameter';
			obj.displayMessage( `${errorMessage}: ${error}`, 'error' );

			return;
		}

		$.ajax( {
			url: `https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id=${appId}&client_secret=${appSecret}&fb_exchange_token=${userAccessToken}`, // eslint-disable-line max-len
			type: 'GET',
			dataType: 'json',
			success: function( result ) {
				if ( 'undefined' === typeof result.access_token ) {
					const errorMessage = tribe_events_virtual_facebook_settings_strings.userTokenFailure;
					// no translation of the error so it can be provided to support.
					const error = 'no user access token';
					obj.displayMessage( `${errorMessage}: ${error}`, 'error' );

					return;
				}

				obj.getPageAccessToken( pageId, result.access_token, $facebookPage );
			},
			error: function( xhr, ajaxOptions, error ) {
				const errorMessage = tribe_events_virtual_facebook_settings_strings.userTokenFailure;
				obj.displayMessage( `${errorMessage}: ${error}`, 'error' );
			},
		} );
	};

	/**
	 * Authorization after using Facebook login button.
	 *
	 * @since 1.7.0
	 *
	 * @param {string} localId The local local_id used to save the Facebook Page name and id.
	 */
	obj.facebookAuthorization = function( localId ) {
		if ( localId.length < 1 ) {
			obj.displayMessage( tribe_events_virtual_facebook_settings_strings.localIdFailure, 'error' );

			return;
		}

		const $facebookPage = $( obj.selectors.facebookContainer ).find( `[data-local-id='${localId}']` ); // eslint-disable-line max-len
		if ( 'undefined' === typeof $facebookPage ) {
			obj.displayMessage( tribe_events_virtual_facebook_settings_strings.pageWrapFailure, 'error' );

			return;
		}

		FB.getLoginStatus( function( response ) { // eslint-disable-line no-undef
			if ( 'connected' !== response.status ) {
				obj.displayMessage( tribe_events_virtual_facebook_settings_strings.connectionFailure, 'error' ); // eslint-disable-line max-len

				return;
			}

			const userAccessToken = response.authResponse.accessToken;
			const facebookAppId = $( obj.selectors.facebookAppId ).val();
			const facebookAppSecret = $( obj.selectors.facebookAppSecret ).val();
			const pageId = $facebookPage.find( obj.selectors.facebookPageId ).val();

			obj.getExtendedAccessToken( facebookAppId, facebookAppSecret, userAccessToken, pageId, $facebookPage ); // eslint-disable-line max-len
		} );
	};

	/**
	 * Handles the successful response from the backend to add a Facebook Page fields.
	 *
	 * @since 1.7.0
	 *
	 * @param {string} html The HTML that adds a message and the page fields html.
	 */
	obj.onAddPageSuccess = function( html ) {
		const message = $( html ).filter( obj.selectors.messageWrap );
		const pageWrap = $( html ).filter( obj.selectors.facebookPageContainer );

		$( obj.selectors.facebookPageMessageContainer ).html( message );

		if ( 0 === pageWrap.length ) {
			return;
		}

		$( obj.selectors.facebookPageList ).append( pageWrap );
	};

	/**
	 * Handles adding a new Facebook Page fields.
	 *
	 * @since 1.7.0
	 *
	 * @param {Event} event The click event.
	 */
	obj.handleAddPage = function( event ) {
		event.preventDefault();
		const url = $( this ).attr( 'href' );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $( obj.selectors.facebookPageList ),
				success: obj.onAddPageSuccess,
			}
		);
	};

	/**
	 * Handle the Autodetect Response for Facebook Video.
	 *
	 * @since 1.8.0
	 *
	 * @param {Event} event The trigger event.
	 * @param {data} data The data object included with the trigger event.
	 */
	obj.handleAutoDetectFacebookVideo = function( event, data ) {
		if ( ! data.html ) {
			return;
		}

		const $videoPreview = $( data.html ).filter( obj.selectors.autoDetectPreviewClass );
		if ( 0 === $videoPreview.length ) {
			return;
		}

		// Initialize the Facebook App to load the video preview.
		obj.facebookInit();
	};

	/**
	 * Bind the Facebook events.
	 *
	 * @since 1.7.0
	 */
	obj.bindEvents = function() {
		$document
			.on(
				'change',
				`${ obj.selectors.facebookPageName }, ${ obj.selectors.facebookPageId }`,
				obj.handleEnableSave
			)
			.on( 'click', obj.selectors.facebookSaveAppBtn, obj.handleSaveApp )
			.on( 'click', obj.selectors.facebookSavePage, obj.handleSavePage )
			.on( 'click', obj.selectors.facebookClearAccess, obj.handleClearAccess )
			.on( 'click', obj.selectors.facebookDeletePage, obj.handleDeletePage );
		$( obj.selectors.facebookContainer )
			.on( 'click', obj.selectors.facebookAddPage, obj.handleAddPage );

		$document.on( 'autodetect.videoPreview', obj.handleAutoDetectFacebookVideo );
	};

	/**
	 * Handles the initialization of the admin when Document is ready
	 *
	 * @since 1.7.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		obj.bindEvents();
		window.facebookAsyncInit = obj.facebookInit();
	};

	// Configure on document ready
	$( obj.ready );
} )( jQuery, tribe.events.facebookSettingsAdmin );
