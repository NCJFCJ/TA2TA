<?php
/**
 * View: Virtual Events Metabox API Account Disabled.
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
 * @var string $api_id         The ID of the API rendering the template.
 * @var string $disabled_title The disabled title.
 * @var string $disabled_body  The disabled message.
 * @var string $link_url       The URL to check an API's settings.
 * @var string $link_label     The label of the button to check an API's settings.
 * @var  bool  $echo           Whether to echo the template to the page or not.
 */
?>
<div
	id="tribe-events-virtual-meetings-<?php echo esc_attr( $api_id ); ?>"
	class="tec-events-virtual-meetings-api-details"
>
	<div
		class="tec-events-virtual-meetings-video-source__inner tec-events-virtual-meetings-api-error"
	>
		<?php
		 $this->template(
			'virtual-metabox/api/account-disabled-details',
			[
				'disabled_title' => $disabled_title,
				'disabled_body'  => $disabled_body,
				'link_url'       => $link_url,
				'link_label'     => $link_label,
			],
			 $echo
		);
		?>
	</div>
</div>
