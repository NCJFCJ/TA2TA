<?php
/**
 * View: Virtual Events Metabox Microsoft API link controls.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/microsoft/details.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.13.0
 *
 * @version 1.13.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var \WP_Post             $event                    The event post object, as decorated by the `tribe_get_event` function.
 * @var array<string,string> $attrs                    Associative array of attributes of the details template.
 * @var boolean              $connected                Whether the meeting or webinar was connected to the event instead of created by it.
 * @var string               $connected_msg            A html message to display if a Microsoft meeting or webinar is manually connected.
 * @var string               $account_name             The api account name of a Microsoft Meeting or Webinar.
 * @var string               $host_label               The label used to designate the host of a Microsoft Meeting or Webinar.
 * @var string               $remove_link_url          The URL to remove the event Microsoft Meeting.
 * @var string               $remove_link_label        The label of the button to remove the event Microsoft Meeting link.
 * @var array<string,string> $remove_attrs             Associative array of attributes of the remove link.
 * @var string               $details_title            The title of the details box.
 * @var string               $id_label                 The label used to prefix the meeting ID.
 * @var string               $message                  A html message to display.
 *
 * @see     tribe_get_event() For the format of the event object.
 */

?>

<?php
if ( ! isset( $event->virtual, $event->microsoft_join_url, $event->microsoft_meeting_id ) ) {
	return;
}

$parsed_url = wp_parse_url( $event->microsoft_join_url );

// Remove the query vars from the Microsoft URL to avoid too long a URL in display.
$short_microsoft_url = implode(
	'',
	array_intersect_key( wp_parse_url( $event->microsoft_join_url ), array_flip( [ 'host' ] ) )
);
?>


<div
	id="tribe-events-virtual-meetings-microsoft"
	class="tribe-dependent tec-events-virtual-meetings-api-container tec-events-virtual-meetings-microsoft-details"
	<?php tribe_attributes( $attrs ) ?>
>
	<div class="tec-events-virtual-meetings-video-source__inner tec-events-virtual-meetings-microsoft-details__inner tec-events-virtual-meetings-api-details__inner">
		<a
			class="tec-events-virtual-meetings-microsoft-details__remove-link tec-events-virtual-meetings-api-details__remove-link"
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
			<?php echo esc_html( $host_label ); ?><?php echo esc_html( $event->microsoft_host_email ); ?>
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
					href="<?php echo esc_url( $event->microsoft_join_url ); ?>"
					class="tec-events-virtual-meetings-api__url-meeting-link"
					target="_blank"
				>
					<?php echo esc_html( $short_microsoft_url ); ?>
				</a>
				<div class="tec-events-virtual-meetings-api__url-meeting-id">
					<?php echo esc_html( $id_label ); ?>
					<?php echo esc_html( $event->microsoft_conference_id ); ?>
				</div>
			</div>
		</div>
	</div>
</div>
