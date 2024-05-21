<?php
/**
 * Zoom details dial-in content section for ticket emails.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/zoom/email/details/dial-in-content.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

?>
<td>
	<ul class="tribe-events-virtual-email-zoom-details__phone-number-list" style="list-style: none; margin-top: 0;padding-left: 0;">
		<?php foreach ( $event->zoom_global_dial_in_numbers as $phone_number ) : ?>
			<li class="tribe-events-virtual-email-zoom-details__phone-number-list-item">
				<a
					href="<?php echo esc_url( 'tel:' . $phone_number['compact'] ); ?>"
					class="tribe-events-virtual-email-zoom-details__phone-number"
					style="font-size:15px;line-height: 18px;"
				>
					<?php
					echo esc_html(
						sprintf(
							'(%1$s) %2$s',
							$phone_number['country'],
							$phone_number['visual']
						)
					);
					?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</td>
