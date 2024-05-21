<?php
/**
 * View: Virtual Events Metabox Zoom API Multiselect.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/zoom/components/multiselect.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1aiy
 *
 * @version 1.4.0
 *
 * @var string               $label    Label for the multiselect.
 * @var string               $class    Class attribute for the multiselect.
 * @var string               $id       ID of the multiselect.
 * @var string               $name     Name attribute for the multiselect.
 * @var string|int           $selected The selected option id.
 * @var array<string,string> $attrs    Associative array of attributes of the multiselect.
 */
?>
<div
	class="tribe-events-virtual-meetings-zoom-control tribe-events-virtual-meetings-zoom-control--multiselect"
>
	<label
		class="tribe-events-virtual-meetings-zoom-control__label"
		for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label>
	<select
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		class="tribe-dropdown <?php echo esc_attr( $class ); ?>"
		value="<?php echo $selected; ?>"
		multiple
		style="width: 100%;" <?php /* This is required for selectWoo styling to prevent select box overflow */ ?>
		<?php tribe_attributes( $attrs ) ?>
	>
	</select>
</div>
