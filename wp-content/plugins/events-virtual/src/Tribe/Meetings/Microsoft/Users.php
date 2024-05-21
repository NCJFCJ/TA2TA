<?php
/**
 * Manages the Microsoft Users
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */

namespace Tribe\Events\Virtual\Meetings\Microsoft;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Integrations\Abstract_Users;
use Tribe\Events\Virtual\Meetings\Microsoft\Event_Meta as Microsoft_Event_Meta;
use Tribe__Utils__Array as Arr;

/**
 * Class Users
 *
 * @since 1.13.0
 *
 * @package Tribe\Events\Virtual\Meetings\Microsoft
 */
class Users extends Abstract_Users {

	/**
	 * Users constructor.
	 *
	 * @since 1.13.0
	 *
	 * @param Api            $api        An instance of the Microsoft API handler.
	 * @param Admin_Template $template   An instance of the Template class to handle the rendering of admin views.
	 */
	public function __construct( Api $api, Admin_Template $admin_template ) {
		static::$api_id        = Microsoft_Event_Meta::$key_source_id;
		$this->api            = $api;
		$this->admin_template = $admin_template;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_users_array( $available_hosts ) {
		if ( empty( $available_hosts ) ) {
			return [];
		}

		return $available_hosts;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_formatted_user_info( $user ) {
		$user_info              = [];
		$user_info['email']     = Arr::get( $user, 'userPrincipalName', '' );
		$user_info['name']      = Arr::get( $user, 'displayName', '' );
		$user_info['last_name'] = Arr::get( $user, 'surname', '' );
		$user_info['id']        = Arr::get( $user, 'userPrincipalName', '' );
		$user_info['value']     = Arr::get( $user, 'id', '' );

		if ( empty( $user_info['last_name'] ) ) {
			$user_info['last_name'] = Arr::get( $user, 'givenName', '' );
		}

		return $user_info;
	}
}
