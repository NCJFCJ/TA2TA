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
	<!-- <h3 class="tribe-events-single-section-title"> <?php //esc_html_e( 'TA2TA Details', 'tribe-events-calendar-pro' ) ?> </h3> -->

	<div class="event-ta-details">
		<?php 
		// echo '<pre>';
		// print_r($fields);
		// echo '</pre>';
		
		?>

		<div class="ta-details">
			<?php 
			$gps = $fields['Grant Programs'] ?? '';
			$tas = $fields['Topic Area'] ?? '';
			$tos = $fields['Target Audiences'] ?? '';
			
			if ( isset( $gps ) && $gps != '' ) {
				?>
				<div class="ta-details-box ta-grant-programs">
					<h5>Grant Programs</h5>
					<ul class="columns-list">
					<?php 
						$gps = explode(',', $gps);
						foreach ( $gps as $gp ):
							echo '<li>'. $gp . '</li>';
						endforeach
					?>
					</ul>
				</div>
			<?php
			}
			?>
			<?php 
			if ( isset( $tas ) &&  $tas != '' ) {
				if ( isset( $gps ) && $gps != '' ){
					echo '<div class="separator"></div>';
				}
				?>
				<div class="ta-details-box ta-topic-areas">
					<h5>Topic Areas</h5>
					<ul class="columns-list">
					<?php 
						$tas = explode(',', $tas) ?? '';
						foreach ( $tas as $ta ):
							echo '<li>'. $ta . '</li>';
						endforeach
					?>
					</ul>
				</div>
				<?php
			}
			?>
			<?php 
			if ( isset( $tos ) && $tos != '' ) {
				if ( isset( $gps ) || isset( $tas ) ){
					echo '<div class="separator"></div>';
				}
				?>
				<div class="ta-details-box ta-target-audiences">
					<h5>Target Audiences</h5>
					<ul class="columns-list">
					<?php 
						$tos = explode(',', $tos);
						foreach ( $tos as $to ):
							echo '<li>'. $to . '</li>';
						endforeach
					?>
					</ul>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
