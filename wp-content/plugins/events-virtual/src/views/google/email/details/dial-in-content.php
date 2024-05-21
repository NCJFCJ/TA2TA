<?php
/**
 * Google details dial-in content section for ticket emails.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/google/email/details/dial-in-content.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.11.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

use Tribe\Events\Virtual\Meetings\Google\Phone_Number;

// The default url might not contain the pin - make sure we include it for emails.
$global_dial_in_numbers = tribe( Phone_Number::class )->get_google_meet_number( $event, true );
?>
<td>
	<ul class="tribe-events-virtual-email-google-details__phone-number-list" style="list-style: none; margin-top: 0;padding-left: 0;">
		<?php foreach ( $global_dial_in_numbers as $number => $phone_details ) : ?>
			<li class="tribe-events-virtual-email-google-details__phone-number-list-item">
				<a
					href="<?php echo esc_url( $phone_details['uri'] ); ?>"
					class="tribe-events-virtual-email-google-details__phone-number"
					style="font-size:15px;line-height: 18px;"
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
</td>
