<?php
/**
 * View: Virtual Events Metabox Text.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/components/text.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.8.0
 *
 * @version 1.8.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var array<string,string> $classes_input An array of classes for the toggle wrap.
 * @var array<string,string> $classes_wrap  An array of classes for the toggle button.
 * @var string               $label         The label for the text input.
 * @var string               $id            ID of the dropdown input.
 * @var string               $name          The name for the text input.
 * @var string               $placeholder   The placeholder for the text input.
 * @var array<string|mixed>  $page          The page data.
 * @var string               $value         The value of the text field.
 * @var array<string,string> $attrs         Associative array of attributes of the text input.
 */
?>
<div <?php tribe_classes( $classes_wrap ); ?> >
	<label
		class="screen-reader-text tec-events-virtual-meetings-control__label"
		for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label>
	<input
		id="<?php echo esc_attr( $id ); ?>"
		<?php tribe_classes( $classes_input ); ?>
		type="text"
		name="<?php echo esc_html( $name ); ?>"
		placeholder="<?php echo esc_html( $placeholder ); ?>"
		value="<?php echo esc_html( $value ); ?>"
		<?php tribe_attributes( $attrs ) ?>
	>
</div>