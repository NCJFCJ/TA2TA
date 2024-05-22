<?php
/**
 * View: Elementor Event Related Events widget list.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-related/list.php
 *
 * @since 6.4.0
 *
 * @var bool    $show_thumbnail      Whether to show the event thumbnail(s).
 * @var bool    $show_event_title    Whether to show the event tile(s).
 * @var bool    $show_event_datetime Whether to show the event date and time.
 * @var string  $title_tag           The HTML tag to use for the event title.
 * @var string  $datetime_tag        The HTML tag to use for the date and time.
 * @var WP_Post $event               The related event.
 * @var Tribe\Events\Pro\Integrations\Elementor\Widgets\Related_Events $widget The widget instance.
 */

// No event, no render.
if ( empty( $event ) ) {
	return;
}
?>

<li <?php tribe_classes( $widget->get_list_item_class() ); ?>>
	<?php
	$this->template( 'views/integrations/elementor/widgets/event-related/item/thumb' );
	?>
	<?php if ( $show_event_title || $show_event_datetime ) : ?>
	<div <?php tribe_classes( $widget->get_widget_class() . '-info' ); ?>>
	<?php endif; ?>
		<?php
		$this->template( 'views/integrations/elementor/widgets/event-related/item/title' );

		$this->template( 'views/integrations/elementor/widgets/event-related/item/datetime' );
		?>
	<?php if ( $show_event_title || $show_event_datetime ) : ?>
	</div>
	<?php endif; ?>
</li>
