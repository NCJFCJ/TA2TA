<?php
/**
 * Handles migrating existing Zoom installs to multiple accounts.
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;
/**
 * Class Migration
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
class Migration {

	/**
	 * If there is no original account for Zoom, save the first one to use to update individual events.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string,mixed>  An array of Accounts formatted for options dropdown.
	 * @param array<string|string> $account_data The array of data for an account to add to the list.
	 * @param string               $api_id       The id of the API in use.
	 */
	public function update_original_account( $accounts, $account_data, $app_id ) {
		if ( 'zoom' !== $app_id ) {
			return;
		}
		// If there are no options and the original_account is empty, lets save the first account added.
		if (
			empty( $accounts )
			&& ! tribe_get_option( Settings::$option_prefix . 'original_account' )
		) {
			tribe_update_option( Settings::$option_prefix . 'original_account', esc_attr( $account_data['id'] ) );
		}
	}
}
