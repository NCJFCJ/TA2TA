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
 * Class Event_Organizer
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets
 */
class Event_Organizer {


	/**
	 * Add a control for the the link to the organizer profile.
	 *
	 * @since 6.4.0
	 *
	 * @param Abstract_Widget $widget The widget instance.
	 * @param string          $section_id The section ID.
	 * @param array           $args The arguments.
	 */
	public static function add_name_link( $widget, $section_id, $args ) {
		if ( $section_id !== 'organizer_name_content_options' ) {
			return;
		}

		// Link Organizer Name control.
		$widget->add_control(
			'link_organizer_name',
			[
				'label'     => esc_html__( 'Link to Organizer Profile', 'tribe-events-calendar-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__( 'Yes', 'tribe-events-calendar-pro' ),
				'label_off' => esc_html__( 'No', 'tribe-events-calendar-pro' ),
				'default'   => 'yes',
				'condition' => [ 'show_organizer_name' => 'yes' ],
			]
		);
	}

	/**
	 * Add the data for the the link to the organizer profile.
	 *
	 * @since 6.4.0
	 *
	 * @param array $args The arguments.
	 * @param bool  $preview Whether or not the preview is active.
	 * @param mixed $widget The widget instance.
	 */
	public static function add_name_link_data( $args, $preview, $widget ) {
		if ( $widget::get_slug() !== 'event_organizer' ) {
			return $args;
		}

		$settings = $widget->get_settings_for_display();

		$args['link_organizer_name'] = tribe_is_truthy( $settings['link_organizer_name'] ?? false );

		if ( ! $args['link_organizer_name'] ) {
			return $args;
		}

		foreach ( $args['organizers'] as $organizer_id => $organizer ) {
			$args['organizers'][ $organizer_id ]['link'] = tribe_get_organizer_link( $organizer_id, false );
		}

		return $args;
	}

	/**
	 * Filter the organizer name template.
	 *
	 * @since 6.4.0
	 *
	 * @param string $found_file The found file.
	 *
	 * @return string
	 */
	public function filter_organizer_name_template( $found_file ): string {
		// Ensure we account for folks changing the directory name.
		return str_replace( dirname( TRIBE_EVENTS_FILE ), dirname( EVENTS_CALENDAR_PRO_FILE ), $found_file );
	}
}
