<?php
/**
 * View: Virtual Events Metabox Radio Input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/components/radio.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1aiy
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @var string               $api_id        The ID of the API rendering the template.
 * @var string               $label         Label for the radio input.
 * @var string               $link          The link for the ajax request to generate the meeting or webinar.
 * @var string               $metabox_id    The metabox current ID.
 * @var string               $classes_label Class attribute for the radio input label.
 * @var string               $classes_wrap  Class attribute for the radio input wrap.
 * @var string               $name          Name attribute for the radio input.
 * @var string|int           $checked       The checked radio option id.
 * @var boolean              $disabled      The checked radio option id.
 * @var string               $type          The type of event to create in the API.
 * @var array<string,string> $attrs         Associative array of attributes of the radio input.
 */

$label_classes = [ 'tec-events-virtual-meetings-control__label' ];
if ( ! empty( $classes_label ) ) {
	$label_classes = array_merge( $label_classes, $classes_label );
}

$wrap_classes = [ 'tribe-events-virtual-meetings-api-control', 'tribe-events-virtual-meetings-api-control--radio-wrap', ];
if ( ! empty( $classes_wrap ) ) {
	$wrap_classes = array_merge( $wrap_classes, $classes_wrap );
}

?>
<div
	<?php tribe_classes( $wrap_classes ); ?>
>
	<label
		for="<?php echo esc_attr( "{$metabox_id}-api-{$api_id}-{$type}-type" ); ?>"
		<?php tribe_classes( $label_classes ); ?>
	>
		<input
			id="<?php echo esc_attr( "{$metabox_id}-api-{$api_id}-{$type}-type" ); ?>"
			class="<?php echo esc_attr( "tribe-events-virtual-meetings-api-control-{$api_id}-{$type}-radio-input" ); ?>"
			name="<?php echo esc_attr( $name ); ?>"
			type="radio"
			value="<?php echo esc_attr( $link ); ?>"
			<?php tribe_attributes( $attrs ) ?>
			<?php echo $checked === $type ? 'checked' : ''; ?>
			<?php echo $disabled ? 'disabled' : ''; ?>
		/>
		<?php echo esc_html( $label ); ?>
	</label>
	<?php
	if ( ! empty( $tooltip['message'] ) ) {
		$this->template( 'components/tooltip', $tooltip );
	}
	?>
</div>
