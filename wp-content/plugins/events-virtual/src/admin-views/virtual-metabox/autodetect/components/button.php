<?php
/**
 * View: Virtual Events Autodetect Find or Retry Button.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/autodetect/components/button.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.8.0
 *
 * @version 1.8.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var \WP_Post $event        The event post object, as decorated by the `tribe_get_event` function.
 * @var string   $button_label The button label text.
 *
 * @see     tribe_get_event() For the format of the event object.
 */

?>

<div
	class="tribe-events-virtual-video-source-autodetect__button-wrapper"
>
	<button
		type="button"
		class="button tribe-events-virtual-video-source-autodetect__button"
	>
		<?php echo esc_html( $button_label ); ?>
	</button>
</div>
