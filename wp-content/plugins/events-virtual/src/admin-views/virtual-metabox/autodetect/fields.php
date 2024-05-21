<?php
/**
 * View: Virtual Events Metabox Autodetect Fields.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/autodetect/components/fields.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.8.0
 *
 * @version 1.8.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var \WP_Post             $event             The event post object, as decorated by the `tribe_get_event` function.
 * @var array<string|string> $autodetect_fields An array of field types to display.
 *
 * @see     tribe_get_event() For the format of the event object.
 */
?>
<div class="tribe-events-virtual-video-source-autodetect__fields">
	<?php
	foreach ( $autodetect_fields as $autodetect_field ) {
		if ( empty( $autodetect_field['path'] ) || empty( $autodetect_field['field'] ) ) {
			continue;
		}
		?>
			<?php $this->template( $autodetect_field['path'], $autodetect_field['field'] ); ?>
		<?php
	}
	?>
</div>
