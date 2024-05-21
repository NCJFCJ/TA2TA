<?php
/**
 * Manages the Google Users
 *
 * @since 1.11.0
 * @since   1.13.0 - Utilize an abstract class.
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */

namespace Tribe\Events\Virtual\Meetings\Google;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Integrations\Abstract_Users;
use Tribe\Events\Virtual\Meetings\Google\Event_Meta as Google_Event_Meta;
use Tribe__Utils__Array as Arr;

/**
 * Class Users
 *
 * @since 1.11.0
 * @since   1.13.0 - Utilize an abstract class.
 *
 * @package Tribe\Events\Virtual\Meetings\Google
 */
class Users extends Abstract_Users {

	/**
	 * Users constructor.
	 *
	 * @since 1.11.0
	 *
	 * @param Api            $api        An instance of the Google API handler.
	 * @param Admin_Template $template   An instance of the Template class to handle the rendering of admin views.
	 */
	public function __construct( Api $api, Admin_Template $admin_template ) {
		self::$api_id        = Google_Event_Meta::$key_source_id;
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
		$user_info['email']     = Arr::get( $user, 'email', '' );
		$user_info['name']      = Arr::get( $user, 'name', '' );
		$user_info['last_name'] = Arr::get( $user, 'family_name', '' );
		$user_info['id']        = Arr::get( $user, 'email', '' );
		$user_info['value']     = Arr::get( $user, 'sub', '' );

		if ( empty( $user_info['last_name'] ) ) {
			$user_info['last_name'] = Arr::get( $user, 'name', '' );
		}

		return $user_info;
	}
}
