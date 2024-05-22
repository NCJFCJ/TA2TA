<?php
/**
 * View: Elementor Event Related Events widget header.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-related/header.php
 *
 * @since 6.4.0
 *
 * @var bool   $show        Whether to show the widget heading.
 * @var string $header_tag  The HTML tag for the widget header.
 * @var array  $posts       The related events.
 * @var Tribe\Events\Pro\Integrations\Elementor\Widgets\Related_Events $widget The widget instance.
 */

if ( empty( $show_header ) ) {
	return;
}
?>
<<?php echo tag_escape( $header_tag ); ?> <?php tribe_classes( $widget->get_header_class() ); ?>>
	<?php echo esc_html( $widget->get_title() ); ?>
</<?php echo tag_escape( $header_tag ); ?>>
