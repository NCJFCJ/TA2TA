<?php
/**
 * View: Virtual Events Metabox API Account Disabled Details.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/api/account-disabled-details.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var string $disabled_title The disabled title.
 * @var string $disabled_body  The disabled message.
 * @var string $link_url       The URL to check an API's settings.
 * @var string $link_label     The label of the button to check an API's settings.
 */
?>
<div class="tec-events-virtual-meetings-api-message">
	<div class="tec-events-virtual-meetings-api-error__details-header">
		<?php echo esc_html( $disabled_title ); ?>
	</div>

	<div class="tec-events-virtual-meetings-api-standard-details__wrapper tec-events-virtual-meetings-api-error__details-wrapper">
		<p class="tec-events-virtual-meetings-api-error__details-body">
			<?php echo wp_kses_post( $disabled_body ); ?>
		</p>
	</div>

	<div class="tec-events-virtual-meetings-api-error__link-wrapper">
		<a
			class="tec-events-virtual-meetings-api-error__link-connect"
			href="<?php echo esc_url( $link_url ); ?>"
		>
			<?php echo esc_html( $link_label ); ?>
		</a>
	</div>
</div>
