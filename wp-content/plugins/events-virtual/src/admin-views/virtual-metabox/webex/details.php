<?php
/**
 * View: Virtual Events Metabox Webex API link controls.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/webex/details.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var \WP_Post             $event                    The event post object, as decorated by the `tribe_get_event` function.
 * @var array<string,string> $attrs                    Associative array of attributes of the details template.
 * @var boolean              $connected                Whether the meeting or webinar was connected to the event instead of created by it.
 * @var string               $connected_msg            A html message to display if a Webex meeting or webinar is manually connected.
 * @var string               $account_name             The api account name of a Webex Meeting or Webinar.
 * @var string               $host_label               The label used to designate the host of a Webex Meeting or Webinar.
 * @var string               $remove_link_url          The URL to remove the event Webex Meeting.
 * @var string               $remove_link_label        The label of the button to remove the event Webex Meeting link.
 * @var array<string,string> $remove_attrs             Associative array of attributes of the remove link.
 * @var string               $details_title            The title of the details box.
 * @var string               $id_label                 The label used to prefix the meeting ID.
 * @var string               $message                  A html message to display.
 *
 * @see     tribe_get_event() For the format of the event object.
 */

?>

<?php
if ( ! isset( $event->virtual, $event->webex_join_url, $event->webex_meeting_id ) ) {
	return;
}

// Remove the query vars from the Webex URL to avoid too long a URL in display.
$short_webex_url = implode(
	'',
	array_intersect_key( wp_parse_url( $event->webex_join_url ), array_flip( [ 'host', 'path' ] ) )
);
?>


<div
	id="tribe-events-virtual-meetings-webex"
	class="tribe-dependent tec-events-virtual-meetings-api-container tec-events-virtual-meetings-webex-details"
	<?php tribe_attributes( $attrs ) ?>
>
	<div class="tec-events-virtual-meetings-video-source__inner tec-events-virtual-meetings-webex-details__inner tec-events-virtual-meetings-api-details__inner">
		<a
			class="tec-events-virtual-meetings-webex-details__remove-link tec-events-virtual-meetings-api-details__remove-link"
			href="<?php echo esc_url( $remove_link_url ); ?>"
			aria-label="<?php echo esc_attr( $remove_link_label ); ?>"
			title="<?php echo esc_attr( $remove_link_label ); ?>"
			<?php tribe_attributes( $remove_attrs ) ?>
		>
			×
		</a>

		<?php echo $message; ?>

		<?php
		 if ( $connected_msg ) {
		 	?>
			 <div class="tec-events-virtual-settings-message__wrap tec-events-virtual-meetings-api__connected-message">
					<?php echo $connected_msg; ?>
				</div>
		 	<?php
		 }
		?>

		<div class="tec-events-virtual-meetings-api__title">
			<?php echo esc_html( $details_title ); ?> <?php echo esc_html( $account_name ); ?>
		</div>

		<div class="tec-events-virtual-meetings-api__host">
			<?php echo esc_html( $host_label ); ?><?php echo esc_html( $event->webex_host_email ); ?>
		</div>

		<div class="tec-events-virtual-meetings-api-standard-details__wrapper tec-events-virtual-meetings-api__url-wrapper">
			<?php
			$this->template( 'components/icons/video', [
				'classes' => [
					'tribe-events-virtual-meeting-api__icon',
					'tribe-events-virtual-meeting-api__icon--video',
				],
			] );
			?>
			<div class="tec-events-virtual-meetings-api__url">
				<a
					href="<?php echo esc_url( $event->webex_join_url ); ?>"
					class="tec-events-virtual-meetings-api__url-meeting-link"
					target="_blank"
				>
					<?php echo esc_html( $short_webex_url ); ?>
				</a>
				<div class="tec-events-virtual-meetings-api__url-meeting-id">
					<?php echo esc_html( $id_label ); ?>
					<?php echo esc_html( $event->webex_meeting_id ); ?>
				</div>
			</div>
		</div>

		<div class="tec-events-virtual-meetings-api-standard-details__wrapper tec-events-virtual-meetings-api__url-wrapper">
			<?php
			$this->template(
				'components/icons/lock',
				[
					'classes' => [
						'tec-events-virtual-single-api-details__icon',
						'tec-events-virtual-single-api-details__icon--lock',
					],
				]
			);
			?>
			<div class="tec-events-virtual-meetings-api__url">
				<?php
				echo esc_html(
					sprintf(
						// translators: %1$s: Webex meeting password.
						_x(
							'Password*: %1$s',
							'The label for the webex Meeting password, prefixed by password label.',
							'events-virtual'
						),
						$event->webex_password
					)
				);
				?>
			</div>
		</div>
		<div class="tec-events-virtual-meetings-api-webex-password__message-wrap">
			*<?php echo esc_html_x( 'Webex meeting password will display based on the Virtual "Show to" settings. ', 'The message about how the Webex meeting password displays.', 'events-virtual' ); ?>
		</div>
	</div>
</div>
