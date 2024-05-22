<?php
/**
 * Controller for Events Calendar Pro Elementor integrations.
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Integrations\Plugins\Elementor
 */

namespace TEC\Events_Pro\Integrations\Plugins\Elementor;

use TEC\Common\Integrations\Traits\Plugin_Integration;
use TEC\Events_Pro\Custom_Tables\V1\Templates\Single_Event_Modifications;
use TEC\Events_Pro\Integrations\Integration_Abstract;
use TEC\Events\Custom_Tables\V1\Models\Occurrence;
use TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets\Event_Additional_Fields;
use TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets\Related_Events;
use TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets\Event_Organizer;
use TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets\Event_Venue;
use TEC\Events\Integrations\Plugins\Elementor\Assets_Manager;
use Tribe__Events__Main as TEC;

/**
 * Class Controller
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Integrations\Plugins\Elementor
 */
class Controller extends Integration_Abstract {
	use Plugin_Integration;

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.4.0
	 */
	public static function get_slug(): string {
		return 'elementor';
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 6.4.0
	 *
	 * @return bool Whether or not integrations should load.
	 */
	public function load_conditionals(): bool {
		return ! empty( ELEMENTOR_PATH );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since 6.4.0
	 */
	public function load(): void {
		$this->register_actions();
		$this->register_filters();
	}

	/**
	 * Register actions.
	 *
	 * @since 6.4.0
	 */
	public function register_actions(): void {
		add_action( 'elementor/document/after_save', [ $this, 'action_elementor_document_after_save' ], 10, 2 );
		add_action( 'tec_events_elementor_register_editor_styles', [ $this, 'register_editor_styles' ] );
		add_action( 'tec_events_elementor_register_widget_assets', [ $this, 'register_override_styles' ] );
		add_action( 'elementor/element/before_section_end', [ Event_Organizer::class, 'add_name_link' ], 10, 3 );
		add_action( 'elementor/element/before_section_end', [ Event_Venue::class, 'add_name_link' ], 10, 3 );
		add_action(
			'tribe_template_include_html:events/integrations/elementor/widgets/event-title',
			[ $this, 'add_single_series_text_marker' ],
			10,
			4
		);

		add_action( 'tec_events_elementor_widget_organizer_enqueue_style', [ $this, 'enqueue_organizer_styles' ] );
	}

	/**
	 * Register filters.
	 *
	 * @since 6.4.0
	 */
	public function register_filters(): void {
		add_filter( 'tec_events_elementor_widget_classes', [ $this, 'include_widgets' ] );

		/* Organizer widget modifications */
		add_filter( 'tec_events_elementor_widget_event_organizer_template_data', [ Event_Organizer::class, 'add_name_link_data' ], 15, 3 );
		add_filter( 'tribe_template_file', [ $this, 'filter_organizer_name_template' ], 10, 4 );

		/* Venue widget modifications */
		add_filter( 'tec_events_elementor_widget_event_venue_template_data', [ Event_Venue::class, 'add_name_link_data' ], 15, 3 );
		add_filter( 'tribe_template_file', [ $this, 'filter_venue_name_template' ], 10, 4 );
	}

	/**
	 * Register the icon styles.
	 *
	 * @since 6.4.0
	 */
	public function register_editor_styles(): void {
		tribe_asset(
			tribe( 'events-pro.main' ),
			'tec-events-pro-elementor-icons',
			'integrations/plugins/elementor/icons.css',
			[],
			null,
			[
				'groups' => [ Assets_Manager::$icon_group_key ],
			]
		);
	}

	/**
	 * Register the organizer style overrides.
	 *
	 * @since 6.4.0
	 */
	public function register_override_styles(): void {
		tribe_asset(
			tribe( 'events-pro.main' ),
			'tec-events-pro-elementor-widget-organizer-styles',
			'integrations/plugins/elementor/widgets/organizer.css',
			[],
			null,
			[
				'groups' => [ Assets_Manager::$group_key ],
			]
		);
	}

	/**
	 * Enqueue the organizer style overrides.
	 *
	 * @since 6.4.0
	 */
	public function enqueue_organizer_styles(): void {
		tribe_asset_enqueue( 'tec-events-pro-elementor-widget-organizer-styles' );
	}

	/**
	 * Test function to re-save the metadata as the base post in a series.
	 *
	 * This is a temporary solution to fix the issue with the Elementor data not being saved on the real post.
	 * It's NOT WORKING CORRECTLY yet, and the issue is still being investigated.
	 *
	 * @since 6.4.0
	 *
	 * @param \Elementor\Core\DocumentTypes\Post $document The document.
	 * @param array                              $editor_data The editor data.
	 */
	public function action_elementor_document_after_save( $document, $editor_data ): void {
		if ( empty( $document ) ) {
			return;
		}

		$occurrence_id = $document->get_main_id();
		$event         = tribe_get_event( $occurrence_id );

		// This is an occurrence the real post ID is hold as a reference on the occurrence table.
		if ( empty( $event->_tec_occurrence->post_id ) || ! $event->_tec_occurrence instanceof Occurrence ) {
			return;
		}

		$saved_meta = get_post_meta( $occurrence_id, '_elementor_data', true );

		$real_id = $event->_tec_occurrence->post_id;

		// Don't use `update_post_meta` that can't handle `revision` post type.
		$is_meta_updated = update_metadata( 'post', $real_id, '_elementor_data', $saved_meta );
	}

	/**
	 * Include the Events Calendar Pro widgets to our TEC elementor integration.
	 *
	 * @since 6.4.0
	 *
	 * @param array $widget_classes Pre-existing widget classes.
	 *
	 * @return array
	 */
	public function include_widgets( array $widget_classes ): array {
		$widget_classes[] = Related_Events::class;
		$widget_classes[] = Event_Additional_Fields::class;
		return $widget_classes;
	}

	/**
	 * Appends the Series relationship marker to the input HTML code, if required.
	 *
	 * @since 6.0.0
	 *
	 * @param string $html    The HTML code to append the marker to.
	 *
	 * @return string The HTML with the marker HTML appended to it, if required.
	 */
	public function add_single_series_text_marker( $html ) {
		if ( ! is_singular( TEC::POSTTYPE ) ) {
			return $html;
		}

		$series_text_marker = $this->container->make( Single_Event_Modifications::class )
											->get_series_relationship_text_marker( get_the_ID() );

		return $series_text_marker . $html;
	}

	/**
	 * Filter the template file for the organizer name.
	 *
	 * @since 6.4.0
	 *
	 * @param string          $found_file The found file.
	 * @param array<string>   $name       The name of the file.
	 * @param Tribe__Template $template   The current template object.
	 */
	public function filter_organizer_name_template( $found_file, $name, $template ): string {
		if ( ! in_array( 'event-organizer', $name ) || ! in_array( 'names', $name ) ) {
			return $found_file;
		}

		return $this->container->make( Event_Organizer::class )->filter_organizer_name_template( $found_file );
	}

	/**
	 * Filter the template file for the venue name.
	 *
	 * @since 6.4.0
	 *
	 * @param string          $found_file The found file.
	 * @param array<string>   $name       The name of the file.
	 * @param Tribe__Template $template   The current template object.
	 */
	public function filter_venue_name_template( $found_file, $name, $template ): string {
		if ( ! in_array( 'event-venue', $name ) || ! in_array( 'name', $name ) ) {
			return $found_file;
		}

		return $this->container->make( Event_Venue::class )->filter_venue_name_template( $found_file );
	}
}
