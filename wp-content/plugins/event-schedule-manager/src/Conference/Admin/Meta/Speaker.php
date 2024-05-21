<?php
/**
 * Event Schedule Manager Speaker Meta.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Admin\Meta
 */

namespace TEC\Conference\Admin\Meta;

use TEC\Conference\Plugin;

/**
 * Class Speaker
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Admin\Meta
 */
class Speaker {

	/**
	 * Adds the session information meta box.
	 *
	 * @since 1.0.0
	 */
	public function speaker_metabox() {
		$cmb = new_cmb2_box( [
			'id'           => 'tec_speaker_metabox',
			'title'        => _x( 'Speaker Information', 'speaker meta box title', 'event-schedule-manager' ),
			'object_types' => [ Plugin::SPEAKER_POSTTYPE ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		] );

		// First Name
		$cmb->add_field( [
			'name' => _x( 'First name', 'speaker meta box field', 'event-schedule-manager' ),
			'id'   => 'tec_first_name',
			'type' => 'text'
		] );

		// Last Name
		$cmb->add_field( [
			'name' => _x( 'Last name', 'speaker meta box field', 'event-schedule-manager' ),
			'id'   => 'tec_last_name',
			'type' => 'text'
		] );

		// Title
		$cmb->add_field( [
			'name' => _x( 'Title', 'speaker meta box field', 'event-schedule-manager' ),
			'id'   => 'tec_title',
			'type' => 'text'
		] );

		// Organization
		$cmb->add_field( [
			'name' => _x( 'Organization', 'speaker meta box field', 'event-schedule-manager' ),
			'id'   => 'tec_organization',
			'type' => 'text'
		] );

		// Facebook URL
		$cmb->add_field( [
			'name'      => _x( 'Facebook URL', 'speaker meta box field', 'event-schedule-manager' ),
			'id'        => 'tec_facebook_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );

		// Twitter URL
		$cmb->add_field( [
			'name'      => _x( 'Twitter URL', 'speaker meta box field', 'event-schedule-manager' ),
			'id'        => 'tec_twitter_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );

		// Instagram URL
		$cmb->add_field( [
			'name'      => _x( 'Instagram URL', 'speaker meta box field', 'event-schedule-manager' ),
			'id'        => 'tec_instagram_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );

		// LinkedIn URL
		$cmb->add_field( [
			'name'      => _x( 'LinkedIn URL', 'speaker meta box field', 'event-schedule-manager' ),
			'id'        => 'tec_linkedin_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );

		// YouTube URL
		$cmb->add_field( [
			'name'      => _x( 'YouTube URL', 'speaker meta box field', 'event-schedule-manager' ),
			'id'        => 'tec_youtube_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );

		// Website URL
		$cmb->add_field( [
			'name'      => _x( 'Website URL', 'speaker meta box field', 'event-schedule-manager' ),
			'id'        => 'tec_website_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );
	}

	/**
	 * Filters session speaker meta field.
	 *
	 * @since 1.0.0
	 *
	 * @param array $cmb The current CMB2 box object.
	 */
	public function filter_session_speaker_meta_field( $cmb ) {
		// Speaker Display Type
		$cmb->add_field( [
			'name'             => _x( 'Speaker display', 'session meta field', 'event-schedule-manager' ),
			'id'               => 'tec_session_speaker_display',
			'type'             => 'radio',
			'show_option_none' => false,
			'options'          => [
				'typed' => _x( 'Speaker Names (Typed)', 'session meta field option', 'event-schedule-manager' ),
				'cpt'   => _x( 'Speaker Select (from Speakers Custom Post Type)', 'session meta field option', 'event-schedule-manager' )
			],
			'default'          => 'typed'
		] );

		// Fetch speakers
		$args     = [
			'numberposts' => - 1,
			'post_type'   => 'tec_speaker',
		];
		$speakers = get_posts( $args );
		$speakers = wp_list_pluck( $speakers, 'post_title', 'ID' );

		// Speaker Select Field
		$cmb->add_field( [
			'name'       => _x( 'Speakers', 'session meta field', 'event-schedule-manager' ),
			'id'         => 'tec_session_speakers',
			'desc'       => _x( 'Select speakers. Drag to reorder.', 'session meta field description', 'event-schedule-manager' ),
			'type'       => 'pw_multiselect',
			'options'    => $speakers,
			'attributes' => [
				'data-conditional-id'    => 'tec_session_speaker_display',
				'data-conditional-value' => 'cpt'
			]
		] );

		// Speaker Names Field
		$cmb->add_field( [
			'name'       => _x( 'Speaker Name(s)', 'session meta field', 'event-schedule-manager' ),
			'id'         => '_tec_session_speaker_names',
			'type'       => 'text',
			'attributes' => [
				'data-conditional-id'    => 'tec_session_speaker_display',
				'data-conditional-value' => 'typed'
			]
		] );

		// Fetch sponsors
		$args     = [
			'numberposts' => - 1,
			'post_type'   => Plugin::SPONSOR_POSTTYPE,
		];
		$sponsors = get_posts( $args );
		$sponsors = wp_list_pluck( $sponsors, 'post_title', 'ID' );

		// Sponsor Select Field
		$cmb->add_field( [
			'name'    => _x( 'Sponsors', 'session meta field', 'event-schedule-manager' ),
			'id'      => 'tec_session_sponsors',
			'desc'    => _x( 'Select sponsor. Drag to reorder.', 'session meta field description', 'event-schedule-manager' ),
			'type'    => 'pw_multiselect',
			'options' => $sponsors
		] );
	}
}
