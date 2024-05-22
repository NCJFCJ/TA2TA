<?php
/**
 * View: Elementor Event Related Events widget.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-related.php
 *
 * @since 6.4.0
 *
 * @var bool           $show_header         Whether to show the widget header.
 * @var bool           $show_thumbnail      Whether to show the event thumbnail(s).
 * @var bool           $show_event_title    Whether to show the event tile(s).
 * @var bool           $show_event_datetime Whether to show the event date and time.
 * @var string         $datetime_tag        The HTML tag to use for the date and time.
 * @var string         $event_title_tag     The HTML tag to use for the event title.
 * @var array<WP_Post> $events              The related events.
 * @var string         $header_tag          The HTML tag for the widget header.
 * @var Related_Events $widget              The widget instance.
 */

use TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets\Related_Events;

// No events to show, no render.
if ( empty( $events ) ) {
	return;
}
?>
<div <?php tribe_classes( $widget->get_container_class() ); ?>>
	<?php
	$this->template( 'views/integrations/elementor/widgets/event-related/header' );

	$this->template( 'views/integrations/elementor/widgets/event-related/list' );
	?>
</div>
