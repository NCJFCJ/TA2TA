<?php
/**
 * View: Virtual Events YouTube Settings Message
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/youtube/components/message.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.6.0
 *
 * @version 1.6.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string $id      The ID of the field.
 * @var string $name    The name of the field.
 * @var string $label   The label of the field.
 * @var string $tooltip The tooltip for the field.
 * @var string $value   The value of the field.
 */

?>
<fieldset id="tribe-field-<?php echo esc_attr( $id ); ?>" class="tribe-field tribe-field-text tribe-size-medium tribe-field-switch">
	<span class="tribe-field-switch-inner-wrap">
		<legend class="tribe-field-label">
			<?php echo esc_html( $label ); ?>
			<?php if ( $tooltip ) : ?>
				<div class="tribe-tooltip event-helper-text tribe-events-virtual-show-to-ticket-attendees-helper-text" aria-expanded="false">
					<span class="dashicons dashicons-info"></span>
					<div class="down">
						<p>
							<?php echo wp_kses( $tooltip, [ 'a' => [ 'href' => [] ] ] ); ?>
						</p>
					</div>
				</div>
			<?php endif; ?>
		</legend>
		<div class="tribe-field-wrap">
			<?php
			$this->template( 'components/switch', [
				'id'            => $id,
				'label'         => $label,
				'classes_wrap'  => [ 'tribe-events-virtual-meetings-youtube-control', 'tribe-events-virtual-meetings-youtube-control--switch' ],
				'classes_input' => [ 'tribe-events-virtual-meetings-youtube-settings-switch__input' ],
				'classes_label' => [ 'tribe-events-virtual-meetings-youtube-settings-switch__label' ],
				'name'          => $name,
				'value'         => 1,
				'checked'       => $value,
				'attrs'         => [],
			] );
			?>
		</div>
	</span>
</fieldset>
