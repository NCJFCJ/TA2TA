<?php
/**
 * View: Virtual Events Accordion
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/components/accordion.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.6.0
 *
 * @version 1.6.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string               $label          The accordion label.
 * @var string               $id             The id of the accordion contents.
 * @var string               $panel          The panel fields html.
 * @var array<string,string> $classes_wrap   An array of classes for the toggle wrap.
 * @var array<string,string> $classes_button An array of classes for the toggle button.
 * @var array<string,string> $classes_panel  An array of classes for the toggle content.
 * @var string               $panel_id       The id of the panel for the slide toggle.
 * @var string               $panel          The content of the panel for the slide toggle.
 * @var bool                 $expanded       Whether the panel starts open or closed.
 */
$accordion_wrap_classes = [ 'tribe-events-virtual-meetings__accordion-wrapper' ];
if ( ! empty( $classes_wrap ) ) {
	$accordion_wrap_classes = array_merge( $accordion_wrap_classes, $classes_wrap );
}

$accordion_button_classes = [ 'button', 'tribe-events-virtual-meetings__accordion-element', 'tribe-events-virtual-meetings__accordion-toggle' ];
if ( ! empty( $classes_button ) ) {
	$accordion_button_classes = array_merge( $accordion_button_classes, $classes_button );
}

$accordion_panel_classes = [ 'tribe-events-virtual-meetings__accordion-element', 'tribe-events-virtual-meetings__accordion-contents' ];
if ( ! empty( $classes_panel ) ) {
	$accordion_panel_classes = array_merge( $accordion_panel_classes, $classes_panel );
}

?>
<div
	<?php tribe_classes( $accordion_wrap_classes ); ?>
>
	<button
		type="button"
		<?php tribe_classes( $accordion_button_classes ); ?>
		data-js="tribe-events-accordion-trigger"
		aria-controls="<?php echo esc_html( $id ); ?>"
		aria-expanded="<?php echo $expanded ? 'true' : 'false'; ?>"
	>
		<span
			class="tribe-events-virtual-meetings__accordion-accordion__label"
		>
			<?php echo esc_html( $label ); ?>
		</span>
		<span
			class="tribe-events-virtual-meetings__accordion-accordion__arrow"
		>
			<?php
			// Change to the views directory to get the icon.
			$this->set_template_folder( 'src/views' );
			$this->template( 'v2/components/icons/caret-down', [ 'classes' => [ 'tribe-events-virtual-meetings__accordion-icon-caret-svg' ] ] );
			$this->set_template_folder( 'src/admin-views' );
			?>
		</span>
	</button>

	<div
		<?php tribe_classes( $accordion_panel_classes ); ?>
		aria-hidden="<?php echo $expanded ? 'false' : 'true'; ?>"
		id="<?php echo esc_html( $id ); ?>"
		<?php
		// Add inline style if expanded on initial load for the accordion to work correctly.
		echo $expanded ? 'style="display:block"' : '';
		?>
	>
		<?php echo $panel ?>
	</div>
</div>