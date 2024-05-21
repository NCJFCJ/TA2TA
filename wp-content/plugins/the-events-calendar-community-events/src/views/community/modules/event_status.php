<?php
// Don't load directly

defined( 'WPINC' ) or die;

use Tribe\Events\Event_Status\Classic_Editor;

// Check if the necessary class is available.
if ( ! class_exists( 'Tribe\Events\Event_Status\Classic_Editor' ) ) {
	return;
}


if ( ! isset( $event ) ) {
	$event = Tribe__Events__Main::postIdHelper();
}

/**
 * @var Metabox $event_status_meta_box
 */
$event_status_meta_box = tribe( Classic_Editor::class );

?>
<div id="event_tribe_event_status" class="tribe-section tribe-section-event-status">
	<div class="tribe-section-header">
		<h3 class="<?php echo tribe_community_events_field_has_error( 'event_status' ) ? 'error' : ''; ?>">
			<?php
			echo $event_status_meta_box->get_title();
			echo tribe_community_required_field_marker( 'event_status' );
			?>
		</h3>
	</div>
	<div class="tribe-section-content">
		<div class="tribe-section-content-field">
			<?php
			/**
			 * Allow developers to hook and add content to the beginning of this section.
			 *
			 * @since 4.8.11
			 */
			do_action( 'tribe_events_community_section_before_event_status' );

			$event_status_meta_box->render( $event );

			/**
			 * Allow developers to hook and add content to the end of this section.
			 *
			 * @since 4.8.11
			 */
			do_action( 'tribe_events_community_section_after_event_status' );
			?>
		</div>
	</div>
</div>

