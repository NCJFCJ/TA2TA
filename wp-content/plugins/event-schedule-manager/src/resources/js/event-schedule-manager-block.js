( ( wp ) => {

	const { registerBlockType } = wp.blocks;
	const { InspectorControls, ServerSideRender } = wp.editor;
	const { SelectControl, CheckboxControl, PanelBody } = wp.components;
	const apiFetch = wp.apiFetch;
	const { createElement: el } = wp.element;
	const { __ } = wp.i18n;

	/**
	 * Format a JavaScript Date object into a string in 'YYYY-MM-DD' format.
	 *
	 * @since 1.0.0
	 *
	 * @param {Date} [date = new Date()] - The date to format, defaults to the current date.
	 *
	 * @return {string} The formatted date string.
	 */
	const dateFormatted = ( date ) => {
		if ( ! date ) return '';

		let dd = String( date.getDate() ).padStart( 2, '0' );
		let mm = String( date.getMonth() + 1 ).padStart( 2, '0' ); //January is 0!
		let yyyy = date.getFullYear();
		return `${yyyy}-${mm}-${dd}`;
	};

	let trackTermsArray = [];

	/**
	 * Fetch track terms from the WP API and populate the trackTermsArray with term details.
	 *
	 * @since 1.0.0
	 */
	const fetchTrackTerms = () => {
		apiFetch( {path: "/wp/v2/session_track"} ).then( posts => {
			posts.forEach( ( val, key ) => {
				trackTermsArray.push( {id: val.id, name: val.name, slug: val.slug} );
			} );
		} );
	};

	fetchTrackTerms();

	registerBlockType( 'tec/schedule-block', {
		title: 'Event Schedule',
		icon: getEventScheduleIcon(),
		description: __( 'Displays sessions in a schedule.', 'event-schedule-manager' ),
		category: 'common',
		supports: {
			align: [ 'wide', 'full' ]
		},
		attributes: {
			date: { type: 'string', default: dateFormatted() },
			color_scheme: { type: 'string', default: 'light' },
			layout: { type: 'string', default: 'table' },
			row_height: { type: 'string', default: 'match' },
			session_link: { type: 'string', default: 'permalink' },
			align: { type: 'string', default: '' },
			content: { type: 'string', default: 'none' },
			tracks: { type: 'string', default: 'all' },
		},

		edit: ( props ) => {
			const { attributes, setAttributes } = props;
			const { date, color_scheme, layout, row_height, session_link, align, content, tracks } = attributes;
			let tracksArray = tracks ? tracks.split( ',' ) : [];

			// Convert the comma-separated string back into an array of dates for Flatpickr.
			const defaultDates = date ? date.split( ',' ) : [];

			const initializeFlatpickr = ( inputElement ) => {
				if ( inputElement ) {
					flatpickr( inputElement, {
						defaultDate: defaultDates,
						dateFormat: 'Y-m-d',
						mode: 'multiple',
						onChange: ( selectedDates ) => {
							// Flatpickr provides selectedDates as an array of Date objects,
							// we convert them to strings formatted as 'YYYY-MM-DD'
							const datesString = selectedDates.map( d => {
								return dateFormatted( d ); // Use the existing dateFormatted function
							} ).join( ',' );
							setAttributes( { date: datesString } );
						}
					} );
				}
			};

			/**
			 * Update the date attribute when the date picker is changed.
			 *
			 * @since 1.0.0
			 *
			 * @param {Event} event - The event object.
			 *
			 * @return {void}
			 */
/*            const updateDate = ( event ) => {
                setAttributes( { date: event.target.value } );
            };*/

			// Initialize a boolean variable to hold the checked state of the "All" checkbox.
			let allChecked = tracks === 'all';

			// Create the "All" checkbox.
			const allCheckbox = el( CheckboxControl, {
				key: 'all',
				label: __( 'All', 'event-schedule-manager' ),
				name: 'tracks[]',
				className: 'tec-all-tracks-checkbox',
				value: 'all',
				checked: allChecked,
				heading: 'Tracks',
				onChange: ( isChecked ) => {
					if ( isChecked ) {
						setAttributes( {tracks: 'all'} );
					} else {
						setAttributes( {tracks: null} );
					}
				}
			} );

			// Create checkboxes for individual tracks.
			const trackCheckboxes = trackTermsArray.map( ( term, index ) => {
				return el( CheckboxControl, {
					key: term.slug,
					label: term.name,
					name: 'tracks[]',
					value: term.slug,
					checked: tracksArray.includes( term.slug ),
					heading: index === 0 ? null : null,  // Heading is already set in "All" checkbox.
					onChange: ( isChecked ) => {
						const track = term.slug;
						const index = tracksArray.indexOf( track );
						if ( isChecked ) {
							if ( index === -1 ) {
								tracksArray.push( track );
							}
						} else {
							if ( index > -1 ) {
								tracksArray.splice( index, 1 );
							}
						}
						setAttributes( {tracks: tracksArray.join()} );
					}
				} );
			} );

			// Prepend the "All" checkbox to the trackCheckboxes array.
			trackCheckboxes.unshift( allCheckbox );

			return [
				el( ServerSideRender, {
					block: "tec/schedule-block",
					attributes: attributes,
					key: 'server-side-render'
				} ),
				el( InspectorControls, { key: 'inspector-controls' },
					el( PanelBody, { 
						title: 'Schedule Settings',
						className: 'tec-schedule-settings-panel',
						initialOpen: true,
					},
					[
						el( 'label', {
						    htmlFor: 'date-picker',
						    key: 'date-picker-label',
						    className: 'tec-schedule-settings-panel__label'
						}, __( 'Date(s)', 'event-schedule-manager' ) ),
						el( 'input', {
							ref: initializeFlatpickr, // Use the callback ref here
							type: 'text',
							key: 'flatpickr-datepicker',
							className: 'flatpickr-datepicker',
							// No need for visibility style if we're trying to show it all the time
						} ),
						el( 'div', {
						    className: 'tec-schedule-settings-panel__helper',
						    key: 'date-picker-help'
						}, __( 'Date(s) of sessions', 'event-schedule-manager' ) ),
						el( SelectControl, {
							label: __( 'Color Scheme', 'event-schedule-manager' ),
							value: color_scheme,
							className: 'tec-color-scheme-select',
							options: [
								{ value: 'light', label: __( 'Light', 'event-schedule-manager' ) },
								{ value: 'dark', label: __( 'Dark', 'event-schedule-manager' ) },
							],
							onChange: ( value ) => setAttributes( { color_scheme: value } ),
							key: 'color-scheme-select'
						} ),
						el( SelectControl, {
							label: __( 'Layout', 'event-schedule-manager' ),
							value: layout,
							options: [
								{ value: 'table', label: __( 'Table', 'event-schedule-manager' ) },
								{ value: 'grid', label: __( 'Grid', 'event-schedule-manager' ) },
							],
							onChange: ( value ) => setAttributes( { layout: value } ),
							key: 'layout-select'
						} ),
						el( SelectControl, {
							label: __( 'Row height', 'event-schedule-manager' ),
							value: row_height,
							options: [
								{ value: 'match', label: __( 'Match', 'event-schedule-manager' ) },
								{ value: 'auto', label: __( 'Auto', 'event-schedule-manager' ) },
							],
							onChange: ( value ) => setAttributes( { row_height: value } ),
							key: 'row-height-select'
						} ),
						el( SelectControl, {
							label: __( 'Session link', 'event-schedule-manager' ),
							value: session_link,
							options: [
								{ value: 'permalink', label: __( 'Permalink', 'event-schedule-manager' ) },
								{ value: 'anchor', label: __( 'Anchor', 'event-schedule-manager' ) },
								{ value: 'none', label: __( 'None', 'event-schedule-manager' ) },
							],
							onChange: ( value ) => setAttributes( { session_link: value } ),
							key: 'session-link-select'
						} ),
						el( SelectControl, {
							label: __( 'Align', 'event-schedule-manager' ),
							value: align,
							options: [
								{ value: '', label: __( 'Standard', 'event-schedule-manager' ) },
								{ value: 'wide', label: __( 'Wide', 'event-schedule-manager' ) },
								{ value: 'full', label: __( 'Full', 'event-schedule-manager' ) },
							],
							onChange: ( value ) => setAttributes( { align: value } ),
							key: 'align-select'
						} ),
						el( SelectControl, {
							label: __( 'Content', 'event-schedule-manager' ),
							value: content,
							options: [
								{ value: 'none', label: __( 'None', 'event-schedule-manager' ) },
								{ value: 'full', label: __( 'Full', 'event-schedule-manager' ) },
								{ value: 'excerpt', label: __( 'Excerpt', 'event-schedule-manager' ) },
							],
							onChange: ( value ) => setAttributes( { content: value } ),
							key: 'content-select'
						} ),
						trackCheckboxes,
					] )
				),
			];
		},

		save: () => null

	} );

	/**
  * Get custom SVG icon.
  *
  * @since 1.1.0
  *
  * @return {Object} SVG icon element.
  */
	function getEventScheduleIcon() {
		return el( 'svg', {width: 20, height: 18, viewBox: '0 0 89 76'},
			el( 'path', {
				d: "M73.7927 1.99344V6.22951H78.2801C84.2005 6.22951 89 11.0267 89 16.9443V65.2852C89 71.2028 84.2005 76 78.2801 76H23.89C22.9262 76 22.1449 75.2191 22.1449 74.2557C22.1449 73.2924 22.9262 72.5115 23.89 72.5115H78.2801C82.273 72.5115 85.5098 69.2762 85.5098 65.2852V24.4158H19.6947V27.5571C19.6947 28.5204 18.9134 29.3013 17.9496 29.3013C16.9858 29.3013 16.2045 28.5204 16.2045 27.5571V16.9443C16.2045 11.0267 21.0039 6.22951 26.9244 6.22951H32.9076V1.99344C32.9076 1.16583 33.4121 0.456018 34.1306 0.154576C34.3178 0.0558732 34.5312 0 34.7576 0C34.7794 0 34.8011 0.000518999 34.8227 0.00154559C34.849 0.000518999 34.8754 0 34.902 0C36.0034 0 36.8964 0.892494 36.8964 1.99344V6.22951H69.8039V1.99344C69.8039 1.12822 70.3554 0.391736 71.1262 0.116028C71.2935 0.0414534 71.4789 0 71.6739 0C71.6927 0 71.7114 0.000384021 71.73 0.00114826C71.7527 0.000384021 71.7754 0 71.7983 0C72.8998 0 73.7927 0.892494 73.7927 1.99344Z"
			} ),
			el( 'path', {
				d: "M57.8347 39.4682C57.8347 40.5691 56.9418 41.4615 55.8404 41.4615H43.8746C42.7732 41.4615 41.8803 40.5691 41.8803 39.4682C41.8803 38.3673 42.7732 37.4748 43.8746 37.4748H55.8404C56.9418 37.4748 57.8347 38.3673 57.8347 39.4682Z"
			} ),
			el( 'path', {
				d: "M76.98 49.0362C76.98 50.1371 76.0871 51.0296 74.9857 51.0296H47.4644C46.363 51.0296 45.4701 50.1371 45.4701 49.0362C45.4701 47.9353 46.363 47.0428 47.4644 47.0428H74.9857C76.0871 47.0428 76.98 47.9353 76.98 49.0362Z"
			} ),
			el( 'path', {
				d: "M70.9971 58.6043C70.9971 59.7052 70.1042 60.5976 69.0028 60.5976H45.0712C43.9698 60.5976 43.0769 59.7052 43.0769 58.6043C43.0769 57.5034 43.9698 56.6109 45.0712 56.6109H69.0028C70.1042 56.6109 70.9971 57.5034 70.9971 58.6043Z"
			} ),
			el( 'path', {
				d: "M36.8964 51.3312C36.8964 61.5149 28.6368 69.7705 18.4482 69.7705C8.25953 69.7705 0 61.5149 0 51.3312C0 41.1474 8.25953 32.8918 18.4482 32.8918C28.6368 32.8918 36.8964 41.1474 36.8964 51.3312ZM18.2736 40.8656C17.5274 40.8656 16.9524 41.5591 16.9524 42.375V52.7614C16.9524 53.5772 17.5274 54.2708 18.2736 54.2708H25.4531C26.1993 54.2708 26.7743 53.5772 26.7743 52.7614C26.7743 51.9455 26.1993 51.2519 25.4531 51.2519H19.5948V42.375C19.5948 41.5591 19.0198 40.8656 18.2736 40.8656Z",
				'fill-rule': "evenodd",
				'clip-rule': "evenodd"
			} )
		);
	}
} )( window.wp );
