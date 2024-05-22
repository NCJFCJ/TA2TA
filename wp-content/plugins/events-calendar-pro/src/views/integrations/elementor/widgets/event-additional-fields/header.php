<?php
/**
 * View: Elementor Event Additional Fields widget header.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-additional-fields/header.php
 *
 * @since 6.4.0
 *
 * @var bool   $show         Whether to show the header.
 * @var string $header_tag   The HTML tag to use for the header.
 * @var string $header_text  The header text.
 * @var Tribe\Events\Pro\Integrations\Elementor\Widgets\Event_Additional_Fields $widget The widget instance.
 */

if ( ! $show_header ) {
	return;
}
?>

<<?php echo tag_escape( $header_tag ); ?> <?php tribe_classes( $widget->get_header_class() ); ?>><?php echo wp_kses_post( $header_text ); ?></<?php echo tag_escape( $header_tag ); ?>>
