<?php
/**
 * View: Virtual Events Metabox Autodetect Video Source.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/autodetect/controls.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.8.0
 *
 * @version 1.8.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var array<string|string> $autodetect_fields An array of field types to display.
 * @var array<string|string> $autodetect_message_classes An array of message classes.
 * @var \WP_Post             $event                      The event post object, as decorated by the `tribe_get_event` function.
 * @var string               $metabox_id                 The current metabox id.
 * @var Url                  $url                        An instance of the Autodetect URL handler.
 *
 * @see     tribe_get_event() For the format of the event object.
 */
?>

<div
	id="tribe-events-virtual-video-source-autodetect"
	class="tribe-dependent tribe-events-virtual-video-autodetect-details"
	data-depends="#tribe-events-virtual-video-source"
	data-condition="video"
>

	<div
		class="tec-events-virtual-meetings-video-source__inner tribe-events-virtual-video-source-autodetect__inner-controls"
	>
		<div class="tribe-events-virtual-meetings-video-source__title tribe-events-virtual-video-source-autodetect__title">
			<?php echo esc_html( _x( 'Video or Meeting Link URL', 'Title for video source.', 'events-virtual' ) ); ?>
		</div>

		<div
			<?php tribe_classes( $autodetect_message_classes ); ?>
			role="alert"
		>
		</div>
		<?php
			$this->template( 'virtual-metabox/autodetect/fields', [
				'event'             => $event,
				'autodetect_fields' => $autodetect_fields,
			] );

			$button_label = _x(
				'Find',
				'Label for button to find the video source.',
				'events-virtual'
			);

			$this->template( '/virtual-metabox/autodetect/components/button', [ 'button_label' => $button_label ] );

			$this->template( 'virtual-metabox/autodetect/video-preview' );

			$this->template( '/components/loader' );
		?>
	</div>
</div>
