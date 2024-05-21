<?php
/**
 * Single Event Meta (Additional Fields) Template
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe-events/pro/modules/meta/additional-fields.php
 *
 * @package TribeEventsCalendarPro
 * @version 4.4.28
 */

if ( ! isset( $fields ) || empty( $fields ) || ! is_array( $fields ) ) {
	return;
}

?>

<div class="tribe-events-meta-group tribe-events-meta-group-other">
	<h3 class="tribe-events-single-section-title"> <?php esc_html_e( 'TA2TA Details', 'tribe-events-calendar-pro' ) ?> </h3>

	<div class="event-ta-details">
		<?php 
		echo '<pre>';
		print_r($fields);
		echo '</pre>';

		$fullname = '';
		if($name === 'First Name'){
			$fullname = $value;
			echo 'Name : '. $fullname;
		}
		if($name === 'Last Name'){
			$fullname = $fullname . ' ' . $value;
			echo 'Name : '. $fullname;
		}
				
		?>
		<?php foreach ( $fields as $name => $value ): ?>
			<div class="event-ta-details-block">
				<div class="event-ta-details-title">
					<?php echo esc_html( $name ); ?>
				</div>
				<div class="tribe-meta-value">
					<?php echo $value; ?>
				</div>
			</div>
		<?php endforeach ?>
	</div>
</div>
