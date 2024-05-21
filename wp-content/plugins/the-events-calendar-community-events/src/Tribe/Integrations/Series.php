<?php

namespace Tribe\Events\Community\Integrations;

class Series {

	/**
	 * Hook the required Methods to the correct filters/actions.
	 *
	 * @since 4.10.0
	 */
	public function hooks() {
		add_filter( 'tribe_community_settings_tab', [ $this, 'include_events_community_settings' ] );
		add_action( 'tribe_community_before_event_page', [ $this, 'prevent_tickets_message_on_edit_page' ] );
	}

	/**
	 * Include settings.
	 *
	 * @since 4.10.0
	 *
	 * @param $settings
	 *
	 * @return mixed
	 */
	public function include_events_community_settings( $settings ) {

		$new_venues_position = array_search( 'prevent_new_venues', array_keys( $settings['fields'] ) );
		$first_block         = array_slice( $settings['fields'], 0, $new_venues_position );
		$second_block        = array_slice( $settings['fields'], $new_venues_position );

		$first_block['attach_to_series'] = [
			'type'            => 'checkbox_bool',
			'label'           => __( 'Users can attach events to Series', 'tribe-events-community' ),
			'tooltip'         => __( 'Users will be limited to choosing from existing Series.<br>Remove the <code>tec_community_events_use_series</code> filter to deactivate.', 'tribe-events-community' ),
			'default'         => true,
			'validation_type' => 'boolean',
			'attributes' => [
				'disabled'        =>'disabled',
			]
		];

		$settings['fields'] = array_merge( $first_block, $second_block );

		return $settings;
	}

	/**
	 * When we are loading series compatibility into CE we need to make sure we prevent the recurrence warning to show
	 * unless we have community tickets active.
	 *
	 * @since 4.10.0
	 */
	public function prevent_tickets_message_on_edit_page(): void {
		if ( ! class_exists( 'Tribe__Tickets__Main' ) ) {
			return;
		}

		if ( class_exists( 'Tribe__Events__Community__Tickets__Service_Provider' ) ) {
			return;
		}

		// Makes sure series is loaded.
		if ( ! tribe_isset_var( 'tec.custom-tables-v1.editors.classic.provider' ) ) {
			return;
		}

		remove_action(
			'tribe_events_date_display',
			[ tribe( 'tec.custom-tables-v1.editors.classic.provider' ), 'filter_recurrence_template_add_recurrence_button_after' ],
			15
		);
	}
}