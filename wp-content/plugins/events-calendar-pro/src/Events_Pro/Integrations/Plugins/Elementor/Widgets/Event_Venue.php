<?php
/**
 * Related Events Elementor Widget.
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets
 */

namespace TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets;

use Elementor\Controls_Manager;

/**
 * Class Event_Venue
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets
 */
class Event_Venue {


	/**
	 * Add a control for the the link to the venue profile.
	 *
	 * @since 6.4.0
	 *
	 * @param Abstract_Widget $widget The widget instance.
	 * @param string          $section_id The section ID.
	 * @param array           $unused_args The widget arguments.
	 */
	public static function add_name_link( $widget, $section_id, $unused_args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		if ( $section_id !== 'venue_name_content_options' ) {
			return;
		}

		// Link Venue Name control.
		$widget->add_control(
			'link_venue_name',
			[
				'label'     => esc_html__( 'Link to Venue Profile', 'tribe-events-calendar-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off' => esc_html__( 'No', 'tribe-events-calendar-pro' ),
				'default'   => 'yes',
				'condition' => [
					'show_venue_name' => 'yes',
				],
			]
		);
	}

	/**
	 * Add the data for the the link to the venue profile.
	 *
	 * @since 6.4.0
	 *
	 * @param array $args The arguments.
	 * @param bool  $preview Whether or not the preview is active.
	 * @param mixed $widget The widget instance.
	 */
	public static function add_name_link_data( $args, $preview, $widget ) {
		if ( $widget::get_slug() !== 'event_venue' ) {
			return $args;
		}

		$settings = $widget->get_settings_for_display();

		$args['link_venue_name'] = tribe_is_truthy( $settings['link_venue_name'] ?? false );

		if ( ! $args['link_venue_name'] ) {
			return $args;
		}

		foreach ( $args['venues'] as $venue_id => $venue ) {
			$args['venues'][ $venue_id ]['link_name'] = tribe_get_venue_link( $venue_id, false );
		}

		return $args;
	}

	/**
	 * Filter the venue name template.
	 *
	 * @since 6.4.0
	 *
	 * @param string $found_file The found file.
	 *
	 * @return string
	 */
	public function filter_venue_name_template( $found_file ): string {
		// Ensure we account for folks changing the directory name.
		return str_replace( dirname( TRIBE_EVENTS_FILE ), dirname( EVENTS_CALENDAR_PRO_FILE ), $found_file );
	}
}
