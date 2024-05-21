<?php
/**
 * Google details section for event single.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/google/single/google-details.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.11.0
 *
 * @var WP_Post $event             The event post object with properties added by the `tribe_get_event` function.
 * @var array   $link_button_attrs Associative array of link button attributes.
 * @var array   $google_link_attrs  Associative array of Google link attributes.
 *
 * @see tribe_get_event() For the format of the event object.
 */

// Remove the query vars from the Google URL to avoid too long a URL in display.
if ( ! empty( $event->google_join_url ) ) {
	$short_google_url = implode(
		'',
		array_intersect_key( wp_parse_url( $event->google_join_url ), array_flip( [ 'host', 'path' ] ) )
	);
}
?>
<div class="tec-events-virtual-single-api-details tribe-events-single-section tribe-events-event-meta tribe-clearfix">
	<?php if ( $event->virtual_linked_button && ! empty( $event->google_join_url ) ) : ?>
		<div class="tec-events-virtual-single-api-details__meta-group tec-events-virtual-single-google-details__meta-group tec-events-virtual-single-google-details__meta-group--link-button tribe-events-meta-group">
			<?php
			$this->template(
				'components/link-button',
				[
					'url'   => $event->google_join_url,
					'label' => $event->virtual_linked_button_text,
					'attrs' => $link_button_attrs,
				]
			);
			?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $event->google_join_url ) ) : ?>
		<div class="tec-events-virtual-single-api-details__meta-group tec-events-virtual-single-google-details__meta-group--google-link tribe-events-meta-group">
			<?php
			$this->template(
				'v2/components/icons/video',
				[
					'classes' => [
						'tec-events-virtual-single-api-details__icon',
						'tec-events-virtual-single-api-details__icon--video',
					],
				]
			);
			?>
			<div class="tec-events-virtual-single-api-details__meta-group-content tec-events-virtual-single-google-details__meta-group-content">
				<a
					href="<?php echo esc_url( $event->google_join_url ); ?>"
					class="tec-events-virtual-single-api-details__text tec-events-virtual-single-api-details__link tec-events-virtual-single-api-details__api-link"
					target="_blank"
					<?php tribe_attributes( $google_link_attrs ); ?>
				>
					<?php echo esc_html( $short_google_url ); ?>
				</a>
				<span class="tec-events-virtual-single-api-details__text tec-events-virtual-single-api-details__api-id">
					<?php
					echo esc_html(
						sprintf(
							// translators: %1$s: Google Meet ID.
							_x(
								'ID: %1$s',
								'The label for the Google Meet ID, prefixed by ID label.',
								'events-virtual'
							),
							$event->google_conference_id
						)
					);
					?>
				</span>
			</div>
		</div>
	<?php endif; ?>
	<?php if ( ! empty( $event->google_global_dial_in_numbers ) ) : ?>
		<div class="tec-events-virtual-single-api-details__meta-group tribe-events-virtual-single-google-details__meta-group tribe-events-virtual-single-google-details__meta-group--google-phone tribe-events-meta-group">
			<?php
			$this->template(
				'v2/components/icons/phone',
				[
					'classes' => [
						'tec-events-virtual-single-api-details__icon',
						'tec-events-virtual-single-api-details__icon--phone',
					],
				]
			);
			?>
			<div class="tec-events-virtual-single-api-details__meta-group-content tribe-events-virtual-single-google-details__meta-group-content">
				<ul class="tec-events-virtual-single-api-details__phone-number-list tribe-events-virtual-single-google-details__phone-number-list">
					<?php foreach ( $event->google_global_dial_in_numbers as $number => $phone_details ) : ?>
						<li class="tec-events-virtual-single-api-details__phone-number-list-item tribe-events-virtual-single-google-details__phone-number-list-item">
							<a
								href="<?php echo esc_url( $phone_details['uri'] ); ?>"
								class="tec-events-virtual-single-api-details__phone-number tribe-events-virtual-single-google-details__phone-number"
								target="_blank"
							>
								<?php echo esc_html( "{$phone_details['country']} {$number}" ); ?>
							</a>
							<?php if ( ! empty( $phone_details['pin'] ) ) : ?>
								<div class="tec-events-virtual-single-api-details__text tec-events-virtual-meetings-api__phone-list-item-pin">
									<?php
										echo esc_html(
											sprintf(
												// translators: %1$s: Google Meet phone pin.
												_x(
													'Pin: %1$s',
													'The label for the Google Phone Pin, prefixed by the Pin label.',
													'events-virtual'
												),
												$phone_details['pin']
											)
										);
									?>
								</div>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>
</div>
