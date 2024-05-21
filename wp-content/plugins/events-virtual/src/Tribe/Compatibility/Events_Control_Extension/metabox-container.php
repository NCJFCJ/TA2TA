<?php
/**
 * Events ControlMetabox replacement.
 *
 * This metabox template will replace the one used by the Events Control extension when both the extension and
 * this plugin are active to ensure the online/virtual event status is managed by this plugin.
 *
 * @version 1.0.0
 *
 * @var Template                               $this       Template instance we are using.
 * @var WP_Post                                $post       Post we are dealing with.
 * @var Tribe\Extensions\EventsControl\Metabox $metabox_id The metabox instance, as passed by the extension..
 * @var array<fields>                          $fields     Array of Field values.
 */

namespace  Tribe\Extensions\EventsControl;

?>
<div class="tribe-events-control-metabox-container" style="margin-top: 24px;">
	<?php wp_nonce_field( $metabox::$nonce_action, "{$metabox::$id}[nonce]" ); ?>
	<p>
		<label for="<?php echo esc_attr( "{$metabox::$id}-status" ); ?>">
			<?php echo esc_html_x( 'Set status:', 'Event status label the select field', 'tribe-ext-events-control' ); ?>
		</label>
		<select
			id="<?php echo esc_attr( "{$metabox::$id}-status" ); ?>"
			name="<?php echo esc_attr( "{$metabox::$id}[status]" ); ?>"
		>
			<option value=""><?php echo esc_html_x( 'Scheduled', 'Event status default option', 'events-virtual' ); ?></option>
			<option
				value="canceled"
				<?php selected( 'canceled' === $fields['status'] ); ?>
			>
				<?php echo esc_html_x( 'Canceled', 'Event status of being canceled in the select field', 'events-virtual' ); ?>
			</option>
			<option
				value="postponed"
				<?php selected( 'postponed' === $fields['status'] ); ?>
			>
				<?php echo esc_html_x( 'Postponed', 'Event status of being postponed in the select field', 'events-virtual' ); ?>
			</option>
		</select>
	</p>
	<div
		class="tribe-dependent"
		data-depends="#<?php echo esc_attr( "{$metabox::$id}-status" ); ?>"
		data-condition="postponed"
	>
		<p>
			<label for="<?php echo esc_attr( "{$metabox::$id}-status-postponed-reason" ); ?>">
				<?php echo esc_html_x( 'Reason (optional)', 'Label for postponed reason field', 'tribe-ext-events-control' ); ?>
			</label>
			<textarea
				class="components-textarea-control__input"
				id="<?php echo esc_attr( "{$metabox::$id}-status-postponed-reason" ); ?>"
				name="<?php echo esc_attr( "{$metabox::$id}[status-postponed-reason]" ); ?>"
			><?php echo esc_textarea( $fields['status-postponed-reason'] ); ?></textarea>
		</p>
	</div>
	<div
		class="tribe-dependent"
		data-depends="#<?php echo esc_attr( "{$metabox::$id}-status" ); ?>"
		data-condition="canceled"
	>
		<p>
			<label for="<?php echo esc_attr( "{$metabox::$id}-status-canceled-reason" ); ?>">
				<?php echo esc_html_x( 'Reason (optional)', 'Label for canceled reason field', 'tribe-ext-events-control' ); ?>
			</label>
			<textarea
				class="components-textarea-control__input"
				id="<?php echo esc_attr( "{$metabox::$id}-status-canceled-reason" ); ?>"
				name="<?php echo esc_attr( "{$metabox::$id}[status-canceled-reason]" ); ?>"
			><?php echo esc_textarea( $fields['status-canceled-reason'] ); ?></textarea>
		</p>
	</div>
</div>
