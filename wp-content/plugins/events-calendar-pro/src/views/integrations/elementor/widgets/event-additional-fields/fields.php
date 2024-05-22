<?php
/**
 * View: Elementor Event Additional Fields widget fields section.
 *
 * You can override this template in your own theme by creating a file at
 * [your-theme]/tribe/events-pro/integrations/elementor/widgets/event-additional-fields/fields.php
 *
 * @since 6.4.0
 *
 * @var array<string,mixed> $fields   The event fields.
 * @var Tribe\Events\Pro\Integrations\Elementor\Widgets\Event_Additional_Fields $widget The widget instance.
 */

// sanity check.
if ( empty( $fields ) ) {
	return;
}
?>
<dl <?php tribe_classes( $widget->get_wrapper_class() ); ?>>
	<?php foreach ( $fields as $field ) : ?>
		<?php
		$this->template( 'views/integrations/elementor/widgets/event-additional-fields/field/label', [ 'field' => $field ] );
		?>
		<?php
		$this->template( 'views/integrations/elementor/widgets/event-additional-fields/field/value', [ 'field' => $field ] );
		?>
	<?php endforeach; ?>
</dl>
