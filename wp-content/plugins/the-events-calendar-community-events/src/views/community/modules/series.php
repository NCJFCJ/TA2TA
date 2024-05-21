<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Event Submission Form Metabox For Series
 * This is used to add a metabox to the event submission form to allow for choosing or
 * creating a series for user submitted events.
 *
 * This is ALSO used in the Series edit view. Be careful to test changes in both places.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/series.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since 4.10.0
 *
 * @version 4.10.0
 */

// If the user cannot create new series *and* if there are no seriess
// to select from then there's no point in generating this UI
if ( ! tribe( 'community.main' )->event_form()->should_show_series_module() ) {
    return;
}

use TEC\Events_Pro\Custom_Tables\V1\Series\Post_Type as Series;

// We need the variables here otherwise it will throw notices
$series_label_singular = tribe( Series::class )->get_label_singular();

if ( ! isset( $event ) ) {
	$event = null;
}
?>

<div id="event_tribe_series" class="tribe-section tribe-section-series eventForm <?php echo tribe_community_events_single_geo_mode() ? 'tribe-single-geo-mode' : ''; ?>">
	<div class="tribe-section-header">
		<h3 class="<?php echo tribe_community_events_field_has_error( 'series' ) ? 'error' : ''; ?>">
			<?php
			printf( esc_html_x( '%1$s %2$s', 'Event Series metabox title on Community Edit Page', 'tribe-events-community' ), tribe_get_event_label_singular(), $series_label_singular );
			echo tribe_community_required_field_marker( 'series' );
			?>
		</h3>
	</div>

	<?php
	/**
	 * Allow developers to hook and add content to the beginning of this section
	 *
	 * @since 4.10.0
	 */
	do_action( 'tribe_events_community_section_before_series' );
	?>

	<table class="tribe-section-content">
		<colgroup>
			<col class="tribe-colgroup tribe-colgroup-label">
			<col class="tribe-colgroup tribe-colgroup-field">
		</colgroup>

		<?php
		tribe_community_events_series_select_menu( $event );

		// The series meta box will render everything within a <tbody>
		$metabox = new Tribe__Events__Linked_Posts__Chooser_Meta_Box( $event, Series::POSTTYPE );
		$metabox->render();
		?>
	</table>

	<?php
	/**
	 * Allow developers to hook and add content to the end of this section
	 *
	 * @since 4.10.0
	 */
	do_action( 'tribe_events_community_section_after_series' );
	?>
</div>