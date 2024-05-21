<?php
/**
 * Manages the Webex Users
 *
 * @since 1.9.0
 * @since   1.13.0 - Utilize an abstract class.
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */

namespace Tribe\Events\Virtual\Meetings\Webex;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Integrations\Abstract_Users;
use Tribe\Events\Virtual\Meetings\Webex\Event_Meta as Webex_Event_Meta;
use Tribe__Utils__Array as Arr;

/**
 * Class Users
 *
 * @since 1.9.0
 * @since   1.13.0 - Utilize an abstract class.
 *
 * @package Tribe\Events\Virtual\Meetings\Webex
 */
class Users extends Abstract_Users {

	/**
	 * Users constructor.
	 *
	 * @since 1.9.0
	 *
	 * @param Api            $api        An instance of the Webex API handler.
	 * @param Admin_Template $template   An instance of the Template class to handle the rendering of admin views.
	 */
	public function __construct( Api $api, Admin_Template $admin_template ) {
		self::$api_id        = Webex_Event_Meta::$key_source_id;
		$this->api            = $api;
		$this->admin_template = $admin_template;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_users_array( $available_hosts ) {
		if ( empty( $available_hosts['items'] ) ) {
			return [];
		}

		return $available_hosts['items'];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_formatted_user_info( $user ) {
		$emails = Arr::get( $user, 'emails', '' );
		$email  = isset( $emails[0] ) ? $emails[0] : '';
		$user_info              = [];
		$user_info['email']     = $email;
		$user_info['name']      = Arr::get( $user, 'firstName', '' ) . ' ' . Arr::get( $user, 'lastName', '' ) . ' - ' . $email;
		$user_info['last_name'] = Arr::get( $user, 'lastName', '' );
		$user_info['id']        = $email;
		$user_info['value']     = Arr::get( $user, 'id', '' );

		if ( empty( $user_info['last_name'] ) ) {
			$user_info['last_name'] = Arr::get( $user, 'firstName', '' );
		}

		return $user_info;
	}
}
