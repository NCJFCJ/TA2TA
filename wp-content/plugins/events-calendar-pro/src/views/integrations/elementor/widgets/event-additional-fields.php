<?php
/**
 * View: Elementor Event Additional Fields widget.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-additional-fields.php
 *
 * @since 6.4.0
 *
 * @var bool                    $show        Whether to show the widget.
 * @var bool                    $show_header Whether to show the widget header.
 * @var string                  $header_tag  The HTML tag for the widget.
 * @var string                  $header_text The header text.
 * @var array<string,mixed>     $fields      The event fields.
 *                                           In the format of [ 'label' => string, 'value' => mixed ].
 * @var Event_Additional_Fields $widget      The widget instance.
 */

use TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets\Event_Additional_Fields;

if ( empty( $fields ) ) {
	return;
}
?>
<div class="<?php tribe_classes( $widget->get_wrapper_class() ); ?>">
	<?php
	$this->template( 'views/integrations/elementor/widgets/event-additional-fields/header' );
	?>
	<?php
	$this->template( 'views/integrations/elementor/widgets/event-additional-fields/fields' );
	?>
</div>
