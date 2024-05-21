<?php
/**
 * View: Virtual Events Metabox YouTube Integration Fields.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/youtube/controls.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.6.0
 *
 * @version 1.6.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var \WP_Post            $event  The event post object, as decorated by the `tribe_get_event` function.
 * @var array<string|mixed> $fields The array of values for switch fields.
 *
 * @see     tribe_get_event() For the format of the event object.
 */

$metabox_id = 'tribe-events-virtual';
?>

<div
	id="tribe-events-virtual-meetings-youtube"
	class="tribe-dependent tribe-events-virtual-meetings-youtube-details"
	data-depends="#tribe-events-virtual-video-source"
	data-condition="youtube"
>

	<div
		class="tec-events-virtual-meetings-video-source__inner tribe-events-virtual-meetings-source-youtube__inner-controls"
	>
		<div class="tribe-events-virtual-meetings-video-source__title">
			<?php echo esc_html( _x( 'YouTube Live', 'Title for Zoom Meeting or Webinar creation.', 'events-virtual' ) ); ?>
		</div>

		<label
			class="tribe-events-virtual-meetings-source-youtube__channel-id-text-label"
			for="<?php echo esc_attr( "{$metabox_id}-youtube-channel-id" ); ?>"
		>
			<?php
			echo esc_html_x(
				'YouTube Channel ID:',
				'Label for virtual events YouTube channel id.',
				'events-virtual'
			);
			?>
		</label>
		<input
			id="<?php echo esc_attr( "{$metabox_id}-youtube-channel-id" ); ?>"
			name="<?php echo esc_attr( "{$metabox_id}[youtube_channel_id]" ); ?>"
			value="<?php echo esc_attr( $fields['tribe-events-virtual[youtube_channel_id]']['value'] ); ?>"
			type="text"
			class="tribe-events-virtual-meetings-source-youtube__channel-id-text-input components-text-control__input"
		/>

		<?php
		$args = [
			'label'        => _x( 'Video Settings', 'The settings for YouTube Integration for an event.', 'events-virtual' ),
			'id'           => 'tribe-events-virtual-meetings-source-youtube__settings',
			'classes_wrap' => [ 'tribe-events-virtual-meetings-youtube-settings__accordion-wrapper' ],
			'panel'        => $this->template( 'virtual-metabox/youtube/panel', [ 'event' => $event, 'fields' => $fields ], false ),
			'expanded'     => empty( $fields['tribe-events-virtual[youtube_channel_id]']['value'] ) ? true : false,
		];
		$this->template( 'components/accordion', $args );
		?>
	</div>
</div>
