/* eslint-disable template-curly-spacing */
/**
 * Configures Event Schedule Manager Pro Admin Object.
 *
 * @since 1.0.0
 *
 * @type {PlainObject}
 */
const conferenceScheduleProAdmin= {};

(function( $, obj ) {
	'use-strict';

	/**
	 * Selectors used for configuration and setup.
	 *
	 * @since 1.0.0
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		// Sessions.
		integrationList: '#tec-session-date',

		// Admin Sorting.
		sortingOrder: '.tec-sponsor-order',
	};

	/**
	 * Setup Datepicker for sessions.
	 *
	 * @since 1.0.0
	 */
	obj.setupDatePicker = function() {
		$( obj.selectors.integrationList ).datepicker( {
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true
		} );
	};

	/**
	 * Setup reordering for speakers and sponsors.
	 *
	 * @since 1.0.0
	 */
	obj.setupReorder = function() {
		$( obj.selectors.sortingOrder ).sortable();
	};

	/**
	 * Bind the integration events.
	 *
	 * @since 1.0.0
	 */
	obj.bindEvents = function() {};

	/**
	 * Unbind the integration events.
	 *
	 * @since 1.0.0
	 */
	obj.unbindEvents = function() {};

	/**
	 * Handles the initialization of the admin when Document is ready.
	 *
	 * @since 1.0.0
	 */
	obj.ready = function() {
		obj.setupDatePicker();
		obj.setupReorder();
		obj.bindEvents();
	};

	// Configure on document ready
	$( obj.ready );
})( jQuery, conferenceScheduleProAdmin );
