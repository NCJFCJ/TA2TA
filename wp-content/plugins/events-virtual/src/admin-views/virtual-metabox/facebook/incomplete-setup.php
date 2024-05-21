<?php
/**
 * View: Virtual Events Metabox Facebook Incomplete setup.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/facebook/incomplete-setup.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.8.2
 *
 * @version 1.8.2
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string $disabled_title The disabled title.
 * @var string $disabled_body  The disabled message.
 * @var string $link_url       The URL to generate a Facebook Meeting link.
 * @var string $link_label     The label of the button to generate a Facebook Meeting link.
 */
?>
<div
	id="tribe-events-virtual-meetings-facebook"
	class="tribe-dependent tribe-events-virtual-meetings-facebook-details"
	data-depends="#tribe-events-virtual-video-source"
	data-condition="facebook"
>
	<div class="tec-events-virtual-meetings-video-source__inner tribe-events-virtual-meetings-source-facebook__inner-controls">
		<div class="tribe-events-virtual-meetings-facebook-error__details-header">
			<?php echo esc_html( $disabled_title ); ?>
		</div>

		<div class="tribe-events-virtual-meetings-facebook-error__details-wrapper">
			<p class="tribe-events-virtual-meetings-facebook-error__details-body">
				<?php echo wp_kses_post( $disabled_body ); ?>
			</p>
		</div>

		<div class="tribe-events-virtual-meetings-facebook-error__link-wrapper">
			<a
				class="tribe-events-virtual-meetings-facebook-error__link-connect"
				href="<?php echo esc_url( $link_url ); ?>"
			>
				<?php echo esc_html( $link_label ); ?>
			</a>
		</div>
	</div>
</div>
