<?php
/**
 * Microsoft details join link header section for ticket emails.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/microsoft/email/details/join-header.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.13.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 */

$join_header = _x( 'Join Skype Video', 'The header for Microsoft Skype link in a ticket email.', 'events-virtual' );
if ( $event->microsoft_provider === 'teamsForBusiness' ) {
	$join_header = _x( 'Join Teams Video', 'The header for Microsoft Teams link in a ticket email.', 'events-virtual' );
}
?>
<td width="220">
	<h6 style="color:#909090 !important; margin:0 0 10px 0; font-family: 'Helvetica Neue', Helvetica, sans-serif; text-transform:uppercase; font-size:13px; line-height: 16px;font-weight:700 !important;">
		<?php echo esc_html( $join_header ); ?>
	</h6>
</td>
