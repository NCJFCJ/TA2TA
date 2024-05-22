<?php
/**
 * View: Elementor Event Additional Fields widget field label.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-additional-fields/field/label.php
 *
 * @since 6.4.0
 *
 * @var array<string,mixed> $field    The event field.
 * @var string              $label    The field label.
 * @var Tribe\Events\Pro\Integrations\Elementor\Widgets\Event_Additional_Fields $widget The widget instance.
 */

// sanity check.
if ( empty( $field ) ) {
	return;
}
?>
<dt <?php tribe_classes( $widget->get_field_label_class() ); ?>>
	<?php echo wp_kses_post( $field['label'] ); ?>
</dt>
