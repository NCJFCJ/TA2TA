<?php
/**
 * View: Virtual Events YouTube Panel Content
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/youtube/components/panel.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.6.0
 *
 * @version 1.6.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var array<string|mixed> $fields The array of values for switch fields.
 */

?>
<div class="tribe-events-virtual-settings-youtube__content">
	<?php
	foreach ( $fields as $id => $field ) {
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