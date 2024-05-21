<?php
/**
 * View: Virtual Events Metabox Zoom API account list.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/zoom/api/authorize-fields/add-link
 *
 * See more documentation about our views templating system.
 *
 * @since   1.5.0
 * @since 1.9.0 - Add support using shared classes between APIs.
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var Api                 $api  An instance of the Zoom API handler.
 * @var Url                 $url  An instance of the URL handler.
 * @var array<string|mixed> $list An array of the Zoom accounts authorized for the site.
 */

if ( empty( $accounts ) ) {
	return;
}
?>
<ul>
	<?php foreach ( $accounts as $account_id => $account ) : ?>
		<li class="tec-settings-api-account-details tribe-settings-zoom-account-details tribe-common"
			data-account-id="<?php echo esc_attr( $account_id ); ?>"
		>
			<div class="tec-settings-api-account-details__account-name tribe-settings-zoom-account-details__account-name">
				<?php echo esc_html( $account['name'] ); ?>
			</div>
			<div class="tec-settings-api-account-details__refresh-account tribe-settings-zoom-account-details__refresh-account">
				<button
					class="tec-settings-api-account-details__account-refresh tribe-settings-zoom-account-details__account-refresh"
					type="button"
					data-api-refresh="<?php echo $url->to_authorize(); ?>"
					data-confirmation="<?php echo $api->get_confirmation_to_refresh_account(); ?>"
					<?php echo tribe_is_truthy( $account['status'] ) ? '' : 'disabled'; ?>
				>
					<?php $this->template( 'components/icons/refresh', [ 'classes' => [ 'tribe-events-virtual-virtual-event__icon-svg' ] ] ); ?>
					<span class="screen-reader-text">
						<?php echo esc_html_x( 'Refresh Zoom Account', 'Refreshes a Zoom account from the website.', 'events-virtual' ); ?>
					</span>
				</button>
			</div>
			<div class="tec-settings-api-account-details__account-status tribe-settings-zoom-account-details__account-status">
				<?php
				$this->template( 'components/switch', [
					'id'            => 'account-status-' . $account_id,
					'label'         => _x( 'Toggle to Change Account Status', 'Disables the Zoom Account for the Website.', 'events-virtual' ),
					'classes_wrap'  => [ 'tec-events-virtual-meetings-api-control', 'tribe-events-virtual-meetings-zoom-control', 'tec-events-virtual-meetings-api-control--switch', 'tribe-events-virtual-meetings-zoom-control--switch' ],
					'classes_input' => [ 'account-status', 'tec-events-virtual-meetings-api-settings-switch__input', 'tribe-events-virtual-meetings-zoom-settings-switch__input' ],
					'classes_label' => [ 'tec-events-virtual-meetings-api-settings-switch__label', 'tribe-events-virtual-meetings-zoom-settings-switch__label' ],
					'name'          => 'account-status',
					'value'         => 1,
					'checked'       => $account['status'],
					'attrs'         => [
						'data-ajax-status-url' => $url->to_change_account_status_link( $account_id ),
					],
				] );
				?>
			</div>
			<div class="tec-settings-api-account-details__account-delete tribe-settings-zoom-account-details__account-delete">
				<button
					class="dashicons dashicons-trash tec-settings-api-account-details__delete-account tribe-settings-zoom-account-details__delete-account"
					type="button"
					data-ajax-delete-url="<?php echo $url->to_delete_account_link( $account_id ); ?>"
					data-confirmation="<?php echo $api->get_confirmation_to_delete_account(); ?>"
					<?php echo tribe_is_truthy( $account['status'] ) ? '' : 'disabled'; ?>
				>
					<span class="screen-reader-text">
						<?php echo esc_html_x( 'Remove Zoom Account', 'Removes a Zoom account from the website.', 'events-virtual' ); ?>
					</span>
				</button>
			</div>
		</li>
	<?php endforeach; ?>
</ul>
