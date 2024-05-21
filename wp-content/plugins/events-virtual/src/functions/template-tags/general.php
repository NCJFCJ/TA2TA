<?php
/**
 * Tribe Events Virtual Template Tags.
 *
 * Display functions (template-tags) for use in WordPress templates.
 */

/**
 * Get virtual label.
 * Returns the capitalized version of the "Virtual" Term.
 *
 * Note: the output of this function is not escaped.
 * You should escape it wherever you use it!
 *
 * @since 1.0.4
 *
 * @return string
 */
function tribe_get_virtual_label() {
	$label = _x( 'Virtual', 'Capitalized version of the "virtual" term.', 'events-virtual' );

	/**
	 * Allows customization of the capitalized version of the "virtual" term.
	 *
	 * Note: the output of this filter is not escaped!
	 *
	 * @param string $label The capitalized version of the "virtual" term, defaults to "Virtual".
	 *
	 * @see tribe_get_event_label_plural
	 */
	return apply_filters( 'tribe_virtual_label', $label );
}

/**
 * Get lowercase virtual label.
 * Returns the lowercase version of the "Virtual" Term.
 *
 * Note: the output of this function is not escaped.
 * You should escape it wherever you use it!
 *
 * @since 1.0.4
 *
 * @return string The lowercase version of the "Virtual" Term.
 */
function tribe_get_virtual_label_lowercase() {
	$label = _x( 'virtual', 'Lowercase version of the "virtual" term.', 'events-virtual' );

	/**
	 * Allows customization of the lowercase version of the "virtual" term.
	 *
	 * Note: the output of this filter is not escaped!
	 *
	 * @param string $label The lowercase version of the "virtual" term, defaults to "virtual".
	 *
	 * @see tribe_get_event_label_plural
	 */
	return apply_filters( 'tribe_virtual_label_lowercase', $label );
}

/**
 * Get virtual event label singular.
 * Returns the singular version of the Event Label.
 *
 * Note: the output of this function is not escaped.
 * You should escape it wherever you use it!
 *
 * @since 1.0.0
 * @since 1.0.4 Implemented use of tribe_get_virtual_label() in pre-filter translation.
 *
 * @return string The singular version of the Event Label.
 */
function tribe_get_virtual_event_label_singular() {
	$label = sprintf(
		_x(
			'%1$s %2$s',
			'Capitalized "virtual" term, capitalized singular event term.',
			'events-virtual'
		),
		tribe_get_virtual_label(),
		tribe_get_event_label_singular()
	);

	/**
	 * Allows customization of the singular version of the Virtual Event Label.
	 *
	 * Note: the output of this filter is not escaped!
	 *
	 * @param string $label The singular version of the Virtual Event label,
	 *                      defaults to "Virtual Event"
	 *                      (or the filtered term for "Virtual" + the filtered term for "Event").
	 *
	 * @see tribe_get_event_label_plural
	 */
	return apply_filters( 'tribe_virtual_event_label_singular', $label );
}

/**
 * Get virtual event label singular lowercase.
 * Returns the lowercase singular version of the Event Label.
 *
 * Note: the output of this function is not escaped.
 * You should escape it wherever you use it!
 *
 * @since 1.0.0
 * @since 1.0.4 Implemented use of tribe_get_virtual_label_lowercase() in pre-filter translation.
 *
 * @return string
 */
function tribe_get_virtual_event_label_singular_lowercase() {
	$label = sprintf(
		_x(
			'%1$s %2$s',
			'Lowercase "virtual" term, singular lowercase event term.',
			'events-virtual'
		),
		tribe_get_virtual_label_lowercase(),
		tribe_get_event_label_singular_lowercase()
	);

	/**
	 * Allows customization of the singular lowercase version of the Virtual Event Label.
	 *
	 * Note: the output of this filter is not escaped!
	 *
	 * @param string $label The singular lowercase version of the Virtual Event label,
	 *                      defaults to "virtual events"
	 *                      (or the filtered term for "virtual" + the filtered term for "event").
	 *
	 * @see tribe_get_event_label_singular_lowercase
	 */
	return apply_filters( 'tribe_virtual_event_label_singular_lowercase', $label );
}

/**
 * Get virtual event label plural.
 * Returns the plural version of the Event Label.
 *
 * Note: the output of this function is not escaped.
 * You should escape it wherever you use it!
 *
 * @since 1.0.0
 * @since 1.0.4 Implemented use of tribe_get_virtual_label() in pre-filter translation.
 *
 * @return string
 */
function tribe_get_virtual_event_label_plural() {
	$label = sprintf(
		_x(
			'%1$s %2$s',
			'Capitalized "virtual" term, capitalized plural event term.',
			'events-virtual'
		),
		tribe_get_virtual_label(),
		tribe_get_event_label_plural()
	);

	/**
	 * Allows customization of the plural version of the Virtual Event Label.
	 *
	 * Note: the output of this filter is not escaped!
	 *
	 * @param string $label The plural version of the Virtual Event label,
	 *                      defaults to "Virtual Events"
	 *                      (or the filtered term for "Virtual" + the filtered term for "Events").
	 *
	 * @see tribe_get_event_label_plural
	 */
	return apply_filters( 'tribe_virtual_event_label_plural', $label );
}

/**
 * Get virtual event label plural lowercase.
 * Returns the lowercase plural version of the Event Label.
 *
 * Note: the output of this function is not escaped.
 * You should escape it wherever you use it!
 *
 * @since 1.0.0
 * @since 1.0.4 Implemented use of tribe_get_virtual_label_lowercase() in pre-filter translation.
 *
 * @return string
 */
function tribe_get_virtual_event_label_plural_lowercase() {
	$label = sprintf(
		_x(
			'%1$s %2$s',
			'Lowercase "virtual" term, lowercase plural event term.',
			'events-virtual'
		),
		tribe_get_virtual_label_lowercase(),
		tribe_get_event_label_plural_lowercase()
	);

	/**
	 * Allows customization of the plural lowercase version of the Virtual Event Label.
	 *
	 * Note: the output of this filter is not escaped!
	 *
	 * @param string $label The plural lowercase version of the Virtual Event label,
	 *                      defaults to "virtual events" (lowercase)
	 *                      (or the filtered term for "virtual" + the filtered term for "events").
	 *
	 * @see tribe_get_event_label_plural_lowercase
	 */
	return apply_filters( 'tribe_virtual_event_label_plural_lowercase', $label );
}

/**
 * Get hybrid label.
 * Returns the capitalized version of the "Hybrid" Term.
 *
 * Note: the output of this function is not escaped.
 * You should escape it wherever you use it!
 *
 * @since 1.4.0
 *
 * @return string
 */
function tribe_get_hybrid_label() {
	$label = _x( 'Hybrid', 'Capitalized version of the "hybrid" term.', 'events-virtual' );

	/**
	 * Allows customization of the capitalized version of the "hybrid" term.
	 *
	 * Note: the output of this filter is not escaped!
	 *
	 * @param string $label The capitalized version of the "hybrid" term, defaults to "Hybrid".
	 *
	 * @see tribe_get_event_label_plural
	 */
	return apply_filters( 'tribe_hybrid_label', $label );
}

/**
 * Get hybrid event label singular.
 * Returns the singular version of the Event Label.
 *
 * Note: the output of this function is not escaped.
 * You should escape it wherever you use it!
 *
 * @since 1.4.0
 *
 * @return string The singular version of the Event Label.
 */
function tribe_get_hybrid_event_label_singular() {
	$label = sprintf(
		_x(
			'%1$s %2$s',
			'Capitalized "hybrid" term, capitalized singular event term.',
			'events-virtual'
		),
		tribe_get_hybrid_label(),
		tribe_get_event_label_singular()
	);

	/**
	 * Allows customization of the singular version of the Hybrid Event Label.
	 *
	 * Note: the output of this filter is not escaped!
	 *
	 * @param string $label The singular version of the Hybrid Event label,
	 *                      defaults to "Hybrid Event"
	 *                      (or the filtered term for "Hybrid" + the filtered term for "Event").
	 *
	 * @see tribe_get_event_label_plural
	 */
	return apply_filters( 'tribe_hybrid_event_label_singular', $label );
}