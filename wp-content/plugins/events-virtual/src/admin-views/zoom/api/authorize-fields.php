<?php
/**
 * View: Virtual Events Metabox Zoom API auth dis/connect button/link.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/zoom/api/authorize-fields.php
 *
 * See more documentation about our views templating system.
 *
 * @since   1.0.0
 * @since   1.5.0 - Add $message variable.
 * @since   1.6.0 - Use a common message component.
 * @since 1.9.0 - Add support using shared classes between APIs.
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var Api    $api     An instance of the Zoom API handler.
 * @var Url    $url     An instance of the URL handler.
 * @var string $message A message to display above the account list on loading.
 */

$accounts = $api->get_list_of_accounts( true );
?>
<fieldset id="tribe-field-zoom_token" class="tec-meetings-api-fields tribe-field tribe-field-text tribe-size-medium">
	<legend class="tribe-field-label"><?php esc_html_e( 'Connected Accounts', 'events-virtual' ); ?></legend>
	<div class="tec-api-accounts-messages tec-zoom-accounts-messages">
		<?php
		$this->template( 'components/message', [
			'message' => $message,
			'type'    => 'standard',
		] );
		?>
	</div>
	<div class="tec-zoom-accounts-wrap <?php echo is_array( $accounts ) && count( $accounts ) > 4 ? 'long-list' : ''; ?>">
		<?php
		$this->template( 'zoom/api/accounts/list', [
				'api'      => $api,
				'url'      => $url,
				'accounts' => $accounts,
			] );
		?>
	</div>
	<div class="tec-zoom-add-wrap">
		<?php
		$this->template( 'zoom/api/authorize-fields/add-link', [
				'api' => $api,
				'url' => $url,
			] );
		?>
	</div>
</fieldset>
