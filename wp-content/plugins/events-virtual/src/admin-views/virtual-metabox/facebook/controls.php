<?php
/**
 * View: Virtual Events Metabox Facebook Integration Fields.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/facebook/controls.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.8.2
 *
 * @version 1.8.2
 *
 * @link    http://evnt.is/1aiy
 *
 * @var \WP_Post            $event The event post object, as decorated by the `tribe_get_event` function.
 * @var array<string|mixed> $pages An array of Facebook Pages to be able to select, that are formatted to use as options.
 *
 * @see     tribe_get_event() For the format of the event object.
 */

?>

<div
	id="tribe-events-virtual-meetings-facebook"
	class="tribe-dependent tribe-events-virtual-meetings-facebook-details"
	data-depends="#tribe-events-virtual-video-source"
	data-condition="facebook"
>

	<div
		class="tec-events-virtual-meetings-video-source__inner tribe-events-virtual-meetings-source-facebook__inner-controls"
	>
		<div class="tribe-events-virtual-meetings-video-source__title">
			<?php echo esc_html( _x( 'Facebook Live', 'Title for Zoom Meeting or Webinar creation.', 'events-virtual' ) ); ?>
		</div>

		<?php $this->template( 'components/dropdown', $pages ); ?>
	</div>
</div>
