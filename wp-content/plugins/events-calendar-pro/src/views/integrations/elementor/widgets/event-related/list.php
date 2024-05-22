<?php
/**
 * View: Elementor Event Related Events widget list.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-related/list.php
 *
 * @since 6.4.0
 *
 * @var bool           $show_thumbnail      Whether to show the event thumbnail(s).
 * @var bool           $show_event_title    Whether to show the event tile(s).
 * @var bool           $show_event_datetime Whether to show the event date and time.
 * @var string         $datetime_tag        The HTML tag to use for the date and time.
 * @var string         $title_tag           The HTML tag to use for the event title.
 * @var array<WP_Post> $events              The related events.
 * @var Tribe\Events\Pro\Integrations\Elementor\Widgets\Related_Events $widget The widget instance.
 */

// No events, no render.
if ( empty( $events ) ) {
	return;
}
?>

<ul <?php tribe_classes( $widget->get_list_class() ); ?>>
	<?php
	foreach ( $events as $event ) {
		$this->template( 'views/integrations/elementor/widgets/event-related/list-item', [ 'event' => $event ] );
	}
	?>
</ul>
