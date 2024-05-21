<?php
/**
 * View: Virtual Events Metabox Autodetect Video Preview.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/autodetect/video-preview.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.8.0
 *
 * @version 1.8.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
 *
 * @see     tribe_get_event() For the format of the event object.
 */
?>
<div class="tec-autodetect-video-preview__container hide-preview">
	<h3 class="tec-events-virtual-autodetect-video-preview__label">
		<?php
			echo esc_html_x(
					'Preview',
					'Label for video preview of the Smart URL/autodetect.',
					'events-virtual'
				);
		?>
	</h3>
	<div class="tec-autodetect-video-preview__inner">
		<?php
			/**
			 * Hook to add a preview of the video source.
			 *
			 * @since 1.8.0
			 *
			 * @param \WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
			 */
			do_action( 'tec_events_virtual_autodetect_video_preview', $event );
		?>
	</div>
</div>
