<?php
/**
 * View: Elementor Event Related Events widget list item title.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-related/item/title.php
 *
 * @since 6.4.0
 *
 * @var bool    $show     Whether to show the event title.
 * @var string  $event_title_tag The HTML tag to use for the event title.
 * @var WP_Post $event    The related event.
 * @var Tribe\Events\Pro\Integrations\Elementor\Widgets\Related_Events $widget The widget instance.
 */

// Show based on user input.
if ( empty( $show_event_title ) ) {
	return;
}

$event_link = tribe_get_event_link( $event );

if ( ! $event_link ) {
	return;
}
?>
<<?php echo tag_escape( $event_title_tag ); ?> <?php tribe_classes( $widget->get_title_class() ); ?>>
	<a
		<?php tribe_classes( $widget->get_title_link_class() ); ?>
		href="<?php echo esc_url( $event_link ); ?>"
		rel="bookmark"
	>
		<?php echo wp_kses_post( get_the_title( $event->ID ) ); ?>
	</a>
</<?php echo tag_escape( $event_title_tag ); ?>>
