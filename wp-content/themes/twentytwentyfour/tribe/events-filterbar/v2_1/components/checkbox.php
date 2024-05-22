<?php
/**
 * View: Checkbox Component
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-filterbar/v2_1/components/checkbox.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @var string  $label   Label for the checkbox.
 * @var string  $value   Value for the checkbox.
 * @var string  $id      ID of the checkbox.
 * @var string  $name    Name attribute for the checkbox.
 * @var boolean $checked Whether the checkbox is checked or not.
 *
 * @version 5.0.0
 *
 */

$event_type_options = get_field( 'event_type', 'options', true );
$colors=[];
foreach($event_type_options as $event_color){
	$colors += [
		$event_color['name'] => [
			'background_color' => $event_color['background_color'], 
			'color' => $event_color['color']
			]
		];
}
?>
<div
	class="tribe-filter-bar-c-checkbox tribe-common-form-control-checkbox"
	data-js="tribe-filter-bar-c-checkbox"
>
	<input
		class="tribe-common-form-control-checkbox__input"
		style="background-color:<?php if (in_array( $label, array_keys($colors))){ echo $colors[ $label ]['background_color'];} else { echo "#ffffff";} ?>";
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		type="checkbox"
		value="<?php echo esc_attr( $value ); ?>"
		<?php checked( $checked ); ?>
		data-js="tribe-filter-bar-c-checkbox-input"
	/>
	<label
		class="tribe-common-form-control-checkbox__label"
		for="<?php echo esc_attr( $id ); ?>"
	>
		<?php echo esc_html( $label ); ?>
	</label>
</div>
