<?php
/**
 * View: Elementor Event Related Events widget list item thumbnail.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-related/item/thumb.php
 *
 * @since 6.4.0
 *
 * @var bool    $show_thumbnail     Whether to show the event thumbnail.
 * @var WP_Post $event    The related event.
 * @var Tribe\Events\Pro\Integrations\Elementor\Widgets\Related_Events $widget The widget instance.
 */

// Show based on user input.
if ( empty( $show_thumbnail ) ) {
	return;
}

?>
<div <?php tribe_classes( $widget->get_thumbnail_class() ); ?>>
	<a href="<?php echo esc_url( tribe_get_event_link( $event ) ); ?>" <?php tribe_classes( $widget->get_image_link_class() ); ?> rel="bookmark" tabindex="-1">
	<?php
	if ( has_post_thumbnail( $event->ID ) ) {
		echo get_the_post_thumbnail( $event->ID, 'large' );
	} else {
		?>
		<img
			src="<?php esc_url( trailingslashit( \Tribe__Events__Pro__Main::instance()->pluginUrl ) . 'src/resources/images/tribe-related-events-placeholder.png' ); ?>"
			alt="<?php esc_attr_e( 'placeholder image', 'tribe-events-calendar-pro' ); ?>"
		/>
		<?php
	}
	?>
	</a>
</div>
