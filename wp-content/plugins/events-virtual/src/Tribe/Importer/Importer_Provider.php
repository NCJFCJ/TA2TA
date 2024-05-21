<?php
/**
 * Handles the registration of Importer provider.
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Importer
 */

namespace Tribe\Events\Virtual\Importer;

use Tribe__Events__Importer__File_Importer_Events as CSV_Event_Importer;
use TEC\Common\Contracts\Service_Provider;

/**
 * Class Importer_Provider
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Importer
 */
class Importer_Provider extends Service_Provider {

	/**
	 * Registers the bindings, actions and filters required by the Importer provider to work.
	 *
	 * @since 1.8.0
	 */
	public function register() {
		// Register this providers in the container to allow calls on it, e.g. to check if enabled.
		$this->container->singleton( 'events-virtual.importer', static::class );
		$this->container->singleton( static::class, static::class );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Hooks the actions required for the importer to work correctly.
	 *
	 * @since 1.8.0
	 */
	protected function add_actions() {
		add_action( 'tec_events_csv_importer_post_update', [ $this, 'import_save_event_meta' ], 10, 3 );
	}

	/**
	 * Save virtual event meta of import of an event.
	 *
	 * @since 1.8.0
	 *
	 * @param integer             $post_id        The event ID to update.
	 * @param array<string|mixed> $record         An event record from the import.
	 * @param CSV_Event_Importer  $csv_events_obj An instance of the Tribe__Events__Importer__File_Importer_Events class.
	 */
	public function import_save_event_meta( int $event_id, array $record, $csv_events_obj ) {
		if ( ! $csv_events_obj instanceof CSV_Event_Importer ) {
			return;
		}

		return $this->container->make( Events::class )->import_save_event_meta( $event_id, $record, $csv_events_obj );
	}

	/**
	 * Hooks the filters required for the importer to work correctly.
	 *
	 * @since 1.8.0
	 */
	protected function add_filters() {
		add_filter( 'tribe_events_importer_event_column_names', [ $this, 'importer_column_mapping' ] );
	}

	/**
	 * Add virtual event fields to the importer columns.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|string> $column_names An array of column names for event import.
	 *
	 * @return array<string|string> The filtered array of column names for virtual event import.
	 */
	public function importer_column_mapping( $column_mapping ) {
		return $this->container->make( Events::class )->importer_column_mapping( $column_mapping );
	}
}
