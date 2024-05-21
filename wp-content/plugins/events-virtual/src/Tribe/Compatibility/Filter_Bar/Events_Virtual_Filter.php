<?php
/**
 * Handles the compatibility with the Filter Bar plugin.
 *
 * @since   1.0.4
 *
 * @package Tribe\Events\Virtual\Compatibility\Filter_Bar
 */

namespace Tribe\Events\Virtual\Compatibility\Filter_Bar;

use Tribe\Events\Filterbar\Views\V2\Filters\Context_Filter;
use Tribe\Events\Virtual\Event_Meta;
use Tribe\Events\Virtual\Utils;

/**
 * Class Events_Virtual_Filter.
 *
 * @since   1.0.4
 *
 * @package Tribe\Events\Virtual\Compatibility\Online_Event_Extension
 */
class Events_Virtual_Filter extends \Tribe__Events__Filterbar__Filter {
	use Context_Filter;

	/**
	 * Value checked for "all" filter.
	 *
	 * @since 1.0.4
	 */
	const EXPLICITLY_ALL = 'all';

	/**
	 * Value checked for virtual filter.
	 */
	const EXPLICITLY_VIRTUAL = 'yes';

	/**
	 * Value checked for non-virtual filter
	 */
	const EXPLICITLY_NON_VIRTUAL = 'no';

	/**
	 * @var string The table alias that will be used for the postmeta table.
	 */
	protected $alias = 'virtual_events_filterbar_alias';

	/**
	 * @var string The table alias that will be used for the postmeta table.
	 */
	protected $hybridalias = 'hybrid_events_filterbar_alias';

	/**
	 * The control type.
	 *
	 * @since 1.0.4
	 *
	 * @var string
	 */
	public $type = 'radio';

	/**
	 * The filter slug.
	 *
	 * @since 1.0.4
	 *
	 * @var string
	 */
	public $slug = 'filterbar_events_virtual';

	/**
	 * Name for the Filter.
	 *
	 * @var string
	 */
	public $name = 'virtual';

	/**
	 * Constructor.
	 *
	 * @since 1.0.4
	 */
	public function __construct() {
		$name = tribe_get_virtual_event_label_plural();

		parent::__construct( $name, $this->slug );
	}

	/**
	 * Returns the admin form HTML.
	 *
	 * @return string
	 */
	public function get_admin_form() {
		$title = $this->get_title_field();

		return $title;
	}

	/**
	 * Get the name for the admin field.
	 *
	 * @since 1.0.4
	 *
	 * @param string $name The individual name for the individual control (ie radio button).
	 * @return string
	 */
	protected function get_admin_field_name( $name ) {
		return "tribe_filter_options[{$this->slug}][{$name}]";
	}

	/**
	 * Returns the value supported by this filter.
	 *
	 * One actually.
	 *
	 * @since 1.0.4
	 *
	 * @return array
	 */
	protected function get_values() {
		return [
			'all'         => [
				'name'  => sprintf(
					/* Translators: %1$s is the lowercase plural event term. */
					esc_html__( 'Show all %1$s', 'events-virtual' ),
					tribe_get_event_label_plural_lowercase()
				),
				'value' => self::EXPLICITLY_ALL,
			],
			'virtual'     => [
				'name'  => sprintf(
					/* Translators: %1$s is the lowercase plural virtual event term. */
					esc_html__( 'Show only %1$s', 'events-virtual' ),
					tribe_get_virtual_event_label_plural_lowercase()
				),
				'value' => self::EXPLICITLY_VIRTUAL,
			],
			'non-virtual' => [
				'name'  => sprintf(
					/* Translators: %1$s is the lowercase plural virtual event term. */
					esc_html__( 'Hide %1$s', 'events-virtual' ),
					tribe_get_virtual_event_label_plural_lowercase()
				),
				'value' => self::EXPLICITLY_NON_VIRTUAL,
			],
		];
	}

	/**
	 * Sets up our join clause for the query.
	 *
	 * @since 1.0.4
	 */
	protected function setup_join_clause() {
		// If they choose all - don't modify the query.
		// phpcs:ignore
		if ( self::EXPLICITLY_ALL === $this->currentValue ) {
			return;
		}

		/** @var \wpdb $wpdb */
		global $wpdb;

		// phpcs:ignore
		if ( tribe_is_truthy( $this->currentValue ) ) {
			$clause = "INNER JOIN {$wpdb->postmeta} AS {$this->alias}
				ON ( {$wpdb->posts}.ID = {$this->alias}.post_id
				AND {$this->alias}.meta_key = %s )
				LEFT JOIN {$wpdb->postmeta} AS {$this->hybridalias}
				ON ( {$wpdb->posts}.ID = {$this->hybridalias}.post_id
				AND {$this->hybridalias}.meta_key = %s )";

			// phpcs:ignore
			$this->joinClause = $wpdb->prepare( $clause, Event_Meta::$key_virtual, Event_Meta::$key_type );
		} else {
			// No virtual events - no need to alter the query.
			if ( empty( Utils::get_virtual_events_count() ) ) {
				return;
			}

			$clause = "LEFT JOIN {$wpdb->postmeta} AS {$this->alias}
				ON ( {$wpdb->posts}.ID = {$this->alias}.post_id
				AND {$this->alias}.meta_key = %s )
				LEFT JOIN {$wpdb->postmeta} AS {$this->hybridalias}
				ON ( {$wpdb->posts}.ID = {$this->hybridalias}.post_id
				AND {$this->hybridalias}.meta_key = %s )";

			// phpcs:ignore
			$this->joinClause = $wpdb->prepare( $clause, Event_Meta::$key_virtual, Event_Meta::$key_type );
		}
	}

	/**
	 * Sets up our where clause for the query.
	 *
	 * @since 1.0.4
	 */
	protected function setup_where_clause() {
		// If they choose all - don't modify the query.
		// phpcs:ignore
		if ( self::EXPLICITLY_ALL === $this->currentValue || 0 === Utils::get_virtual_events_count() ) {
			return;
		}

		// phpcs:ignore
		if ( tribe_is_truthy( $this->currentValue ) ) {
			// phpcs:ignore
			$this->whereClause = " AND ( {$this->alias}.meta_value = 'yes'
				OR {$this->alias}.meta_value = '1'
				OR {$this->alias}.meta_value = 'true'
				OR {$this->alias}.meta_value IS NOT NULL ) ";
		} else {
			// phpcs:ignore
			$this->whereClause = " AND ( {$this->alias}.meta_value = 'no'
				OR {$this->hybridalias}.meta_value = 'hybrid'
				OR {$this->alias}.meta_value = '0'
				OR {$this->alias}.meta_value = 'false'
				OR {$this->alias}.meta_value = ''
				OR {$this->alias}.meta_value IS NULL ) ";
		}
	}
}
