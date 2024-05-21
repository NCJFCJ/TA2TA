/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 1.0.0
 *
 * @type {PlainObject}
 */
tribe.events = tribe.events || {};

/**
 * Configures Virtual Events Admin Object on the Global Tribe variable
 *
 * @since 1.0.0
 *
 * @type {PlainObject}
 */
tribe.events.virtualAdmin = tribe.events.virtualAdmin || {};

/**
 * Initializes in a Strict env the code that manages the Event Views
 *
 * @since 1.0.0
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.events.virtualAdmin
 *
 * @return {void}
 */
( function( $, obj, tribe_dropdowns ) {
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
		autoDetectButton: '.tribe-events-virtual-video-source-autodetect__button',
		autoDetectFields: '.tribe-events-virtual-video-source-autodetect__fields',
		autoDetectSource: '#tribe-events-virtual-autodetect-source',
		autoDetectMessagesWrap: '.tribe-events-virtual-video-source-autodetect__messages-wrap',
		autoDetectMessage: '.tec-events-virtual-settings-message__wrap',
		autoDetectloader: '.tribe-common-c-loader',
		autoDetectPreviewWrap: '.tec-autodetect-video-preview__container',
		autoDetectPreview: '.tec-autodetect-video-preview__inner',
		autoDetecthiddenElement: '.tribe-common-a11y-hidden',
		configure: '.tribe-configure-virtual-button',
		displayOption: '.tribe-events-virtual-display',
		displayOptionCheckbox: '.tribe-events-virtual-display input[type="checkbox"]',
		embedCheckbox: '#tribe-events-virtual-embed-video',
		remove: '.tribe-remove-virtual-event',
		setupCheckbox: '#tribe-events-virtual-setup',
		showOptions: '.tribe-events-virtual-show input',
		showAll: '#tribe-events-virtual-show-to-all',
		videoSource: '#tribe-events-virtual-video-source',
		videoSourcesWrap: '.tribe-events-virtual-video-sources-wrap',
		videoSourceDetails: '.tribe-events-virtual-video-sources',
		videoSourcesFloat: '.tribe-events-virtual-video-sources--float',
		virtualContainer: '#tribe-virtual-events',
		virtualUrl: '.tribe-events-virtual-video-source__virtual-url-input',

		// @Deprecated 1.8.0
		embedNotice: '.tribe-events-virtual-video-source__not-embeddable-notice',
		embedNoticeShow: '.tribe-events-virtual-video-source__not-embeddable-notice--show',
		embedNoticeText: '.tribe-events-virtual-video-source__not-embeddable-text',
	};

	/**
	 * Sets checkbox checked attribute
	 *
	 * @since 1.0.0
	 *
	 * @param {boolean} checked whether the checkbox is checked or not
	 *
	 * @return {function} Handler to check the checkbox or not
	 */
	obj.setCheckboxCheckedAttr = function( checked ) {
		if ( obj.isGutenbergActive() ) {
			return function() {
				const blocks = wp.data.select( 'core/block-editor' ).getBlocks();
				// Ensure we have a date block.
				const dateBlock = blocks.find( block => 'tribe/event-price' === block.name ); // eslint-disable-line es5/no-es6-methods,max-len

				if ( checked ) {
					// See if we have a VE block already.
					const existingBlock = blocks.filter( block => 'tribe/virtual-event' === block.name );
					// If we already have a block there's no need to add a new one.
					if ( existingBlock.length ) {
						$( obj.selectors.setupCheckbox )
							.prop( 'checked', checked ).trigger( 'verify.dependency' );
						return;
					}

					if ( dateBlock ) {
						// If the date block is present, insert immediately after it.
						const index = wp.data.select( 'core/block-editor' ).getBlockIndex( dateBlock.clientId );
						const newBlock = wp.blocks.createBlock( 'tribe/virtual-event' );
						wp.data.dispatch( 'core/block-editor' ).insertBlock(
							newBlock,
							index + 1,
							'',
							false
						);
					} else {
						// Insert at the end.
						const newBlock = wp.blocks.createBlock( 'tribe/virtual-event' );
						wp.data.dispatch( 'core/block-editor' ).insertBlock( newBlock );
					}

					// Trigger add event for hooking.
					$document.trigger( 'virtual.add' );

					// Attempt to keep from scrolling away from the metabox.
					const metabox = document.getElementById( 'tribe-virtual-events' );
					metabox.scrollIntoView();
				} else {
					// Add confirmation if deleting the virtual settings.
					var confirmed = confirm( tribe_events_virtual_strings.deleteConfirm );
					if ( ! confirmed ) {
						return;
					}

					tribe.events.metaboxDelete = true;
					// Remove the VE block if it's there.
					blocks.forEach( element => {
						if ( 'tribe/virtual-event' === element.name ) {
							wp.data.dispatch( 'core/block-editor' ).removeBlock( element.clientId );
						}
					} );

					// Trigger delete event for hooking.
					$document.trigger( 'virtual.delete' );

					tribe.events.metaboxDelete = false;
				}

				$( obj.selectors.setupCheckbox ).prop( 'checked', checked ).trigger( 'verify.dependency' );
			};
		}

		return function() {
			if ( checked ) {
				// Trigger add event for hooking.
				$document.trigger( 'virtual.add' );
			} else {
				// Add confirmation if deleting the virtual settings.
				var confirmed = confirm( tribe_events_virtual_strings.deleteConfirm );
				if ( ! confirmed ) {
					return;
				}

				// Trigger delete event for hooking.
				$document.trigger( 'virtual.delete' );
			}

			$( obj.selectors.setupCheckbox ).prop( 'checked', checked ).trigger( 'verify.dependency' );
		};
	};

	/**
	 * Show loader for the container.
	 *
	 * @since 1.8.0
	 *
	 * @param {jQuery} $container jQuery object of the container.
	 *
	 * @return {void}
	 */
	obj.show = function( $container ) {
		const $loader = $container.find( obj.selectors.autoDetectloader );

		if ( $loader.length ) {
			$loader.removeClass( obj.selectors.autoDetecthiddenElement.className() );
		}
	};

	/**
	 * Hide loader for the container.
	 *
	 * @since 1.8.0
	 *
	 * @param {jQuery} $container jQuery object of the container.
	 *
	 * @return {void}
	 */
	obj.hide = function( $container ) {
		const $loader = $container.find( obj.selectors.autoDetectloader );

		if ( $loader.length ) {
			$loader.addClass( obj.selectors.autoDetecthiddenElement.className() );
		}
	};

	/**
	 * Show video preview on loading of a saved event.
	 *
	 * @since 1.8.0
	 */
	obj.handleVideoPreviewOnLoad = function( ) {
		const $videoPreview = $( obj.selectors.autoDetectPreview );
		if ( 0 === $.trim( $videoPreview.html() ).length ) {
			return;
		}

		$( obj.selectors.autoDetectPreviewWrap ).removeClass( 'hide-preview' );
	};

	/**
	 * Handle the Autodetect Response for Oembed.
	 *
	 * @since 1.8.0
	 *
	 * @param {Event} event The trigger event.
	 * @param {data} data The data object included with the trigger event.
	 */
	obj.handleAutoDetectVideoPreview = function( event, data ) {
		if ( ! data.html ) {
			return;
		}

		const $videoPreview = $( data.html ).filter( obj.selectors.autoDetectPreview );
		if ( 0 === $videoPreview.length ) {
			return;
		}

		$( obj.selectors.autoDetectPreviewWrap ).removeClass( 'hide-preview' );
		$( obj.selectors.autoDetectPreview ).replaceWith( $videoPreview );

		$document.trigger( 'autodetect.videoPreview', { 'html' : data.html } );
	};

	/**
	 * Handles the successful response from the backend for autodetect.
	 *
	 * @since 1.8.0
	 *
	 * @param {string} html The HTML resposes from the autodetect.
	 */
	obj.onAutoDetectSuccess = function( html ) {
		obj.hide( $( '.tribe-events-virtual-video-source-autodetect__inner-controls' ) );

		$document.trigger( 'autodetect.complete', { 'html' : html } );

		const $message = $( html ).filter( obj.selectors.autoDetectMessage );
		const $autoDetectFields = $( html ).filter( obj.selectors.autoDetectFields );

		$( obj.selectors.autoDetectMessagesWrap ).html( $message );

		if ( 0 === $autoDetectFields.length ) {
			return;
		}

		$( obj.selectors.autoDetectFields ).replaceWith( $autoDetectFields );

		const $dropdowns = $( obj.selectors.autoDetectFields )
					.find( tribe_dropdowns.selector.dropdown )
					.not( tribe_dropdowns.selector.created );

		$dropdowns.tribe_dropdowns();

		$( obj.selectors.autoDetectSource )
			.trigger( 'setup.dependency' )
			.trigger( 'verify.dependency' );
	};

	/**
	 * Autodetect the source of the url in the video url field.
	 *
	 * @since 1.8.0
	 */
	obj.detectSource = function() {
		var $videoSource = $( obj.selectors.videoSource );
		if ( 'video' !== $videoSource.val() ) {
			return;
		}

		const $video_input = $( obj.selectors.virtualUrl );
		const video_url = $video_input.val();
		const url = $video_input.data( 'autodetectAjaxUrl' );
		const $autodetectFields = $( "[name^='tribe-events-virtual-autodetect']" );
		const ajaxData = {};
		$autodetectFields.map( function() {
			let field = $( this ).prop( 'name' ).match( /\[(.*?)\]/ )[1];
			ajaxData[ field ] = this.value;
		} ).get();

		obj.show( $( '.tribe-events-virtual-video-source-autodetect__inner-controls' ) );
		$( obj.selectors.autoDetectPreviewWrap ).addClass( 'hide-preview' );

		$.ajax(
			url,
			{
				contentType: 'application/json',
				context: $( obj.selectors.videoSourceDetails ),
				data: {
					video_url: video_url,
					ajax_data: ajaxData, // eslint-disable-line
				},
				success: obj.onAutoDetectSuccess,
			}
		);
	}

	/**
	 * Handle the enabling and disabling of the Show controls depending on the Display options.
	 *
	 * @since 1.0.0
	 */
	obj.handleShowOptionEnablement = function() {
		var checked = $( obj.selectors.displayOption ).find( ':checked:visible' ).length;
		var $showOptions = $( obj.selectors.showOptions );

		if ( checked > 0 ) {
			$showOptions.prop( { disabled: false } );

			return;
		}

		$showOptions.prop( { disabled: true } );
	};

	obj.handleShowOptionInteractivity = function( e ) {
		if ( ! ( e && Object.prototype.hasOwnProperty.call( e, 'target' ) ) ) {
			// Empty on new posts.
			return;
		}

		var $this = $( e.target );
		if ( ! $this.prop( 'checked' ) ) {
			return;
		}

		if ( 'all' === $this.val() ) {
			return;
		}

		$( obj.selectors.showAll ).prop( 'checked', false );
	};

	/**
	 * Bind events for virtual events admin
	 *
	 * @since 1.0.0
	 *
	 * @return {void}
	 */
	obj.bindEvents = function() {
		$( obj.selectors.virtualContainer )
			.on( 'click', obj.selectors.configure, obj.setCheckboxCheckedAttr( true ) )
			.on( 'click', obj.selectors.remove, obj.setCheckboxCheckedAttr( false ) )
			.on( 'click', obj.selectors.autoDetectButton, obj.detectSource )
			.on( 'click', obj.selectors.displayOptionCheckbox, obj.handleShowOptionEnablement )
			.on( 'change', obj.selectors.showOptions, obj.handleShowOptionInteractivity );

		$document.on( 'autodetect.complete', obj.handleAutoDetectVideoPreview );
	};

	/**
	 * Check if block editor is active adn virtual event block is registered.
	 *
	 * @since 1.7.0
	 * @since 1.7.3 - Add additional check if wp.data if defined and if the VE block is registered.
	 *
	 * @returns {boolean} Whether the block editor scripts are available and if VE block is registered.
	 */
	obj.isGutenbergActive = function() {
		if (
			typeof wp === 'undefined' ||
			typeof wp.blocks === 'undefined' ||
			typeof wp.data === 'undefined'
		) {
			return false;
		}

		return !!wp.data.select( 'core/blocks' ).getBlockType( 'tribe/virtual-event' );
	};

	/**
	 * Handles the initialization of the admin when Document is ready
	 *
	 * @since 1.0.0
	 * @since 1.6.0 - Support for video sources dropdown.
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		obj.bindEvents();

		// Trigger tribe dependency for video source fields to display.
		// Set on a delay or it does not correctly load the selected video source fields.
		setTimeout( function() {
			$( obj.selectors.videoSource ).trigger( 'verify.dependency' );
		}, 0 );

		obj.handleVideoPreviewOnLoad();
	};

	/**
	 * Checks the virtual URL for embeddability.
	 *
	 * @since 1.0.0
	 * @since 1.6.0 - Video source dropdown support.
	 * @deprecated 1.8.0
	 *
	 */
	obj.testEmbed = function() {
		console.info( 'Method deprecated and replaced with Autodetect feature.' ); // eslint-disable-line no-console, max-len
		var $videoSource = $( obj.selectors.videoSource );
		if ( 'video' !== $videoSource.val() ) {
			return;
		}

		const $input = $( obj.selectors.virtualUrl );
		const url = $input.val();
		const nonce = $input.attr( 'data-nonce' );
		const flag = $input.attr( 'data-oembed-test' );

		// Don't test null data. Or items we don't want tested.
		if ( ! flag || ! url || ! nonce ) {
			// But we'll make sure we hide the notice and enable the checkbox.
			obj.hideOembedNotice();
			return;
		}

		$.ajax( {
			type: 'post',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action: 'tribe_events_virtual_check_oembed',
				url: url,
				nonce: nonce,
			},
		} )
			.done( function() {
				obj.hideOembedNotice();
			} )
			.fail( function( response ) {
				obj.showOembedNotice( response );
			} );
	};

	/**
	 * Hide the notice and enable the checkbox.
	 * @deprecated 1.8.0
	 *
	 * @since 1.0.0
	 */
	obj.hideOembedNotice = function() {
		console.info( 'Method deprecated and replaced with Autodetect feature.' ); // eslint-disable-line no-console, max-len
		$( obj.selectors.embedNotice ).removeClass( obj.selectors.embedNoticeShow.className() );
		$( obj.selectors.embedCheckbox ).prop( { disabled: false } );
	};

	/**
	 * Show the notice, disable and uncheck the checkbox.
	 *
	 * @since 1.0.0
	 * @deprecated 1.8.0
	 *
	 * @param {object} response The ajax response object.
	 */
	obj.showOembedNotice = function( response ) {
		console.info( 'Method deprecated and replaced with Autodetect feature.' ); // eslint-disable-line no-console, max-len
		$( obj.selectors.embedNoticeText ).html( response.responseJSON.data );
		$( obj.selectors.embedNotice ).addClass( obj.selectors.embedNoticeShow.className() );
		$( obj.selectors.embedCheckbox ).prop( {
			disabled: true,
			checked: false,
		} );
	};

	// Configure on document ready
	$( obj.ready );
} )( jQuery, tribe.events.virtualAdmin, tribe_dropdowns );
