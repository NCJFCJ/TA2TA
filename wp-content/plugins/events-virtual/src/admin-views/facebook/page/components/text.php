<?php
/**
 * View: Virtual Events Metabox Facebook Live Page - Text Input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/facebook/page/components/text.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.7.0
 *
 * @version 1.7.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var array<string,string> $classes_input An array of classes for the toggle wrap.
 * @var array<string,string> $classes_wrap  An array of classes for the toggle button.
 * @var string               $label         The label for the text input.
 * @var string               $name          The name for the text input.
 * @var string               $placeholder   The placeholder for the text input.
 * @var string               $screen_reader The screen reader instructions for the text input.
 * @var array<string|mixed>  $page          The page data.
 * @var string               $value         The value of the text field.
 */
?>
<div <?php tribe_classes( $classes_wrap ); ?> >
	<fieldset class="tribe-settings-facebook-page-details__field tribe-field tribe-field-text">
		<legend class="tribe-settings-facebook-page-details__label tribe-field-label">
			<?php echo esc_html( $label ); ?>
		</legend>
		<div class="tribe-settings-facebook-page-details__field-wrap tribe-field-wrap">
			<input
				<?php tribe_classes( $classes_input ); ?>
				type="text"
				name="<?php echo esc_html( $name ); ?>"
				placeholder="<?php echo esc_html( $placeholder ); ?>"
				value="<?php echo esc_html( $value ); ?>"
			>
			<label class="screen-reader-text">
				<?php echo esc_html( $screen_reader ); ?>
			</label>
		</div>
	</fieldset>
</div>