<?php
/**
 * View: Elementor Event Related Events widget list item date-time.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-related/item/datetime.php
 *
 * @since 6.4.0
 *
 * @var bool    $show         Whether to show the event date and time.
 * @var string  $html_tag     The HTML tag to use for the date and time.
 * @var WP_Post $event        The related event.
 * @var Tribe\Events\Pro\Integrations\Elementor\Widgets\Related_Events $widget The widget instance.
 */

// Show based on user input.
if ( empty( $show_event_datetime ) ) {
	return;
}
?>
<<?php echo tag_escape( $datetime_tag ); ?>
	<?php tribe_classes( $widget->get_datetime_class() ); ?>
>
	<?php echo wp_kses_post( tribe_events_event_schedule_details( $event ) ); ?>
</<?php echo tag_escape( $datetime_tag ); ?>>
