<?php
/**
 * View: Virtual Events Metabox API Setup Link.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/api/setup-link.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string               $api_id           The ID of the API rendering the template.
 * @var array<string,string> $attrs            Associative array of attributes of the API account.
 * @var string               $setup_link_url   The URL to the API settings.
 * @var string               $setup_link_label The label of the button to for the API settings.
 *
 * @see     tribe_get_event() For the format of the event object.
 */
?>

<div
	id="tribe-events-virtual-meetings-<?php echo esc_attr( $api_id ); ?>"
	class="tribe-dependent tec-events-virtual-meetings-api-container tribe-events-virtual-meetings-api-controls"
	<?php tribe_attributes( $attrs ) ?>
>

	<div class="tec-events-virtual-meetings-video-source__inner tec-events-virtual-meetings-api-details__inner">

		<a
			class="tribe-events-virtual-meetings-<?php echo esc_attr( $api_id ); ?>__connect-link"
			href="<?php echo esc_url( $setup_link_url ); ?>"
		>
			<?php echo esc_html( $setup_link_label ); ?>
		</a>

	</div>
</div>
