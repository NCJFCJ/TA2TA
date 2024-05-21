<?php
/**
 * Microsoft details section for event single.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/microsoft/single/microsoft-details.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.13.0
 *
 * @var WP_Post $event             The event post object with properties added by the `tribe_get_event` function.
 * @var array   $link_button_attrs Associative array of link button attributes.
 * @var array   $microsoft_link_attrs  Associative array of Microsoft link attributes.
 *
 * @see tribe_get_event() For the format of the event object.
 */

// Remove the query vars from the Microsoft URL to avoid too long a URL in display.
if ( ! empty( $event->microsoft_join_url ) ) {
	$short_microsoft_url = implode(
		'',
		array_intersect_key( wp_parse_url( $event->microsoft_join_url ), array_flip( [ 'host' ] ) )
	);
}
?>
<div class="tec-events-virtual-single-api-details tribe-events-single-section tribe-events-event-meta tribe-clearfix">
	<?php if ( $event->virtual_linked_button && ! empty( $event->microsoft_join_url ) ) : ?>
		<div class="tec-events-virtual-single-api-details__meta-group tec-events-virtual-single-microsoft-details__meta-group tec-events-virtual-single-microsoft-details__meta-group--link-button tribe-events-meta-group">
			<?php
			$this->template(
				'components/link-button',
				[
					'url'   => $event->microsoft_join_url,
					'label' => $event->virtual_linked_button_text,
					'attrs' => $link_button_attrs,
				]
			);
			?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $event->microsoft_join_url ) ) : ?>
		<div class="tec-events-virtual-single-api-details__meta-group tec-events-virtual-single-microsoft-details__meta-group--microsoft-link tribe-events-meta-group">
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
			<div class="tec-events-virtual-single-api-details__meta-group-content tec-events-virtual-single-microsoft-details__meta-group-content">
				<a
					href="<?php echo esc_url( $event->microsoft_join_url ); ?>"
					class="tec-events-virtual-single-api-details__text tec-events-virtual-single-api-details__link tec-events-virtual-single-api-details__api-link"
					target="_blank"
					<?php tribe_attributes( $microsoft_link_attrs ); ?>
				>
					<?php echo esc_html( $short_microsoft_url ); ?>
				</a>
				<span class="tec-events-virtual-single-api-details__text tec-events-virtual-single-api-details__api-id">
					<?php
					echo esc_html(
						sprintf(
							// translators: %1$s: Microsoft Meet ID.
							_x(
								'ID: %1$s',
								'The label for the Microsoft Meet ID, prefixed by ID label.',
								'events-virtual'
							),
							$event->microsoft_conference_id
						)
					);
					?>
				</span>
			</div>
		</div>
	<?php endif; ?>
</div>
