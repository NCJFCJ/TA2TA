<?php
/**
 * View: Virtual Events Metabox Microsoft API Account Authorization.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/microsoft/api/authorize-fields.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.13.0
 *
 * @version 1.13.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var Api    $api     An instance of the Microsoft API handler.
 * @var Url    $url     An instance of the URL handler.
 * @var string $message A message to display above the account list on loading.
 */

$accounts = $api->get_list_of_accounts( true );
?>
<fieldset id="tec-field-microsoft_token" class="tec-meetings-api-fields tribe-field tribe-field-text tribe-size-medium">
	<legend class="tribe-field-label"><?php esc_html_e( 'Connected Accounts', 'events-virtual' ); ?></legend>
	<div class="tec-api-accounts-messages tec-microsoft-accounts-messages">
		<?php
		$this->template( 'components/message', [
			'message' => $message,
			'type'    => 'standard',
		] );
		?>
	</div>
	<div class="tec-microsoft-accounts-wrap <?php echo is_array( $accounts ) && count( $accounts ) > 4 ? 'long-list' : ''; ?>">
		<?php
		$this->template( 'microsoft/api/accounts/list', [
				'api'      => $api,
				'url'      => $url,
				'accounts' => $accounts,
			] );
		?>
	</div>
	<div class="tec-microsoft-add-wrap">
		<?php
		$this->template( 'microsoft/api/authorize-fields/add-link', [
				'api' => $api,
				'url' => $url,
			] );
		?>
	</div>
</fieldset>
