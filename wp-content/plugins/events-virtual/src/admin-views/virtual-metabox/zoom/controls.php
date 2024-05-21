<?php
/**
 * View: Virtual Events Metabox Zoom API link controls for 2+ meeting types.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/zoom/controls.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.1.1
 * @since   1.6.0 - Modify for use with new video source dropdown.
 *
 * @version 1.6.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var \WP_Post $event               The event post object, as decorated by the `tribe_get_event` function.
 * @var string   $generate_link_url   The URL to generate a Zoom Meeting link.
 * @var string   $generate_link_label The label of the button to generate a Zoom Meeting link.
 *
 * @see     tribe_get_event() For the format of the event object.
 */
?>

<div
	id="tribe-events-virtual-meetings-zoom"
	class="tribe-dependent tribe-events-virtual-meetings-zoom-controls"
	data-depends="#tribe-events-virtual-video-source"
	data-condition="zoom"
>

	<div class="tec-events-virtual-meetings-video-source__inner tribe-events-virtual-meetings-zoom-details__inner">

		<a
			class="tribe-events-virtual-meetings-zoom__connect-link"
			href="<?php echo esc_url( $generate_link_url ); ?>"
		>
			<?php echo esc_html( $generate_link_label ); ?>
		</a>

	</div>
</div>
