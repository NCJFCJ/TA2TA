<?php
/**
 * View: Elementor Event Additional Fields widget field value.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-additional-fields/field/value.php
 *
 * @since 6.4.0
 *
 * @var array<string,mixed> $field    The event field.
 * @var string              $value    The field value.
 * @var Tribe\Events\Pro\Integrations\Elementor\Widgets\Event_Additional_Fields $widget The widget instance.
 */

// sanity check.
if ( empty( $field ) ) {
	return;
}
?>
<dd <?php tribe_classes( $widget->get_field_value_class() ); ?>>
	<?php echo wp_kses_post( $field['value'] ); ?>
</dd>
