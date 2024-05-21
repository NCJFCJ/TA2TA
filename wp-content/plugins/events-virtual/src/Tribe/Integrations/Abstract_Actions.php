<?php
/**
 * Manages Action name for API integrations.
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */

namespace Tribe\Events\Virtual\Integrations;

/**
 * Class Abstract_Actions
 *
 * @since   1.13.0
 *
 * @package Tribe\Events\Virtual\Integrations
 */
abstract class Abstract_Actions {

	/**
	 * The internal id of the API integration.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $api_id = '';

	/**
	 * The name of the action used to generate the OAuth authentication URL.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $authorize_nonce_action = '';

	/**
	 * The name of the action used to get an account setup to generate use an API.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $select_action = '';

	/**
	 * The name of the action used to change the status of an account to enabled or disabled.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $status_action;

	/**
	 * The name of the action used to delete an account.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $delete_action;

	/**
	 * The name of the action used to generate a meeting creation link.
	 * The property also provides a reasonable default for the abstract class.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $create_action = '';

	/**
	 * The name of the action used to remove a meeting creation link.
	 * The property also provides a reasonable default for the abstract class.
	 *
	 * @since 1.13.0
	 *
	 * @var string
	 */
	public static $remove_action = '';

	/**
	 *
	 *
	 * @since 1.13.0
	 *
	 * @param $api_id
	 */
	public function setup( $api_id ) {
		static::$authorize_nonce_action = "tec-events-virtual-meetings-{$api_id}-oauth-authorize";
		static::$status_action          = "tec-events-virtual-meetings-{$api_id}-settings-status";
		static::$delete_action          = "tec-events-virtual-meetings-{$api_id}-settings-delete";
		static::$select_action          = "tec-events-virtual-{$api_id}-account-setup";
		static::$create_action          = "tec-events-virtual-meetings-{$api_id}-meeting-create";
		static::$remove_action          = "tec-events-virtual-meetings-{$api_id}-meeting-remove";
	}
}
