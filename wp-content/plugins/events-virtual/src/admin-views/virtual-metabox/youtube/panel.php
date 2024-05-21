<?php
/**
 * View: Virtual Events YouTube Panel Content
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/youtube/panel.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.6.0
 *
 * @version 1.6.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var \WP_Post            $event  The event post object, as decorated by the `tribe_get_event` function.
 * @var array<string|mixed> $fields The array of values for switch fields.
 */

?>
<div class="tribe-events-virtual-meetings-source-youtube__content">
	<?php
	foreach ( $fields as $id => $field ) {
		if ( 'tribe-events-virtual[youtube_channel_id]' === $id ) {
			continue;
		}
		$this->template( 'youtube/components/switch-field', [
			'id'      => $id,
			'name'    => $id,
			'label'   => $field['label'],
			'tooltip' => $field['tooltip'],
			'value'   => $field['value'],
		] );
	}
	?>
</div>