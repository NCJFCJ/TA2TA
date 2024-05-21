<?php
/**
 * Manages the Zoom Users
 *
 * @since   1.4.0
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Encryption;
use Tribe\Events\Virtual\Integrations\Abstract_Users;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Event_Meta;
use Tribe\Events\Virtual\Metabox;
use Tribe__Utils__Array as Arr;

/**
 * Class Users
 *
 * @since   1.4.0
 * @since   1.13.0 - Utilize an abstract class.
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
class Users extends Abstract_Users {

	/**
	 * The name of the action used to get an account setup to generate a Zoom meeting or webinar.
	 *
	 * @since 1.8.2
	 * @deprecated 1.13.0 - Use Actions::$validate_user_action.
	 *
	 * @var string
	 */
	public static $validate_user_action = 'events-virtual-zoom-user-validate';

	/**
	 * Users constructor.
	 *
	 * @since 1.4.0
	 * @since 1.8.2 - Add the admin template class.
	 * @since 1.13.0 - Add the Action class.
	 *
	 * @param Api            $api        An instance of the Zoom API handler.
	 * @param Encryption     $encryption An instance of the Encryption handler.
	 * @param Admin_Template $template   An instance of the Template class to handle the rendering of admin views.
	 * @param Actions        $actions    An instance of the Actions name handler.
	 */
	public function __construct( Api $api, Encryption $encryption, Admin_Template $admin_template, Actions $actions ) {
		self::$api_id         = Zoom_Event_Meta::$key_source_id;
		$this->api            = $api;
		$this->encryption     = ( ! empty( $encryption ) ? $encryption : tribe( Encryption::class ) );
		$this->admin_template = $admin_template;
		$this->actions        = $actions;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_users_array( $available_hosts ) {
		if ( empty( $available_hosts['users'] ) ) {
			return [];
		}

		return $available_hosts['users'];
	}

	/**
	 * {@inheritDoc}
	 */
	protected function get_formatted_user_info( $user ) {
		$user_info              = [];
		$user_info['email']     = Arr::get( $user, 'email', '' );
		$user_info['name']      = Arr::get( $user, 'first_name', '' ) . ' ' . Arr::get( $user, 'last_name', '' ) . ' - ' . Arr::get( $user, 'email', '' );
		$user_info['last_name'] = Arr::get( $user, 'last_name', '' );
		$user_info['id']        = Arr::get( $user, 'id', '' );
		$user_info['value']     = Arr::get( $user, 'id', '' );
		$user_info['type']      = Arr::get( $user, 'type', 0 );

		if ( empty( $user_info['last_name'] ) ) {
			$user_info['last_name'] = Arr::get( $user, 'first_name', '' );
		}

		return $user_info;
	}

	/**
	 * Get the alternative users that can be used as hosts.
	 *
	 * @since 1.4.0
	 * @since 1.9.0 - Update formatting with changes from get_formatted_hosts_list().
	 *
	 * @param array<string,mixed>   An array of Zoom Users to use as the alternative hosts.
	 * @param string $selected_alt_hosts The list of alternative host emails.
	 * @param string $current_host       The email of the current host.
	 * @param null|string $account_id The account id to use to get the users with.
	 *
	 * @return array|bool|mixed An array of Zoom Users to use as the alternative hosts.
	 */
	public function get_alternative_users( $alternative_hosts = [], $selected_alt_hosts = '', $current_host = '', $account_id = null ) {
		$all_users = $this->get_formatted_hosts_list( $account_id );

		$selected_alt_hosts = explode( ';', $selected_alt_hosts );

		// Filter out the current host email and any user that is not a valid alternative host.
		// Using array_values to reindex from zero or the options do not show in the multiselect.
		$alternative_hosts = array_values(
			array_filter(
				$all_users,
				static function ( $user ) use ( $current_host )  {
					return isset( $user['alternative_host'] )
						&& true === $user['alternative_host']
						&& $user['text'] !== $current_host;
				}
			)
		);

		// Change the dropdown value to the email for alternative hosts because that is what Zoom returns.
		$alternative_hosts_email_id = array_map(
			static function ( $user ) use ( $selected_alt_hosts ) {
				$user['id'] = $user['email'];
				$user['selected'] = in_array( $user['email'], $selected_alt_hosts ) ? true : false;
				return $user;
			},
			$alternative_hosts
		);

		return $alternative_hosts_email_id;
	}

	/**
	 * Handles the request to validate a user type.
	 *
	 * @since 1.9.0
	 *
	 * @param string|null $nonce The nonce that should accompany the request.
	 *
	 * @return string The html from the request containing success or error information.
	 */
	public function validate_user( $nonce = null ) {
		if ( ! $this->check_ajax_nonce( $this->actions::$validate_user_action, $nonce ) ) {
			return false;
		}

		$event = $this->check_ajax_post();
		if ( empty( $event ) ) {
			$error_message = _x( 'User validation failed because no event was found.', 'The event is missing error message for Zoom user validation.', 'events-virtual' );
			$this->admin_template->template( 'components/message', [
				'message' => $error_message,
				'type'    => 'error',
			] );

			wp_die();
		}

		$zoom_host_id = tribe_get_request_var( 'host_id' );
		// If no host id found, fail the request.
		if ( empty( $zoom_host_id ) ) {
			$error_message = _x( 'The Zoom Host ID is missing to access the API, please select a host from the dropdown and try again.', 'Host ID is missing error message for Zoom user validation.', 'events-virtual' );
			$this->admin_template->template( 'components/message', [
				'message' => $error_message,
				'type'    => 'error',
			] );

			wp_die();
		}

		$zoom_account_id = tribe_get_request_var( 'account_id' );
		// If no account id found, fail the request.
		if ( empty( $zoom_account_id ) ) {
			$error_message = _x( 'The Zoom Account ID is missing to access the API.', 'Account ID is missing error message for Zoom user validation.', 'events-virtual' );
			$this->admin_template->template( 'components/message', [
				'message' => $error_message,
				'type'    => 'error',
			] );

			wp_die();
		}

		$account_loaded = $this->api->load_account_by_id( $zoom_account_id );
		// If there is no token, then stop as the connection will fail.
		if ( ! $account_loaded ) {
			$error_message = _x( 'The Zoom Account could not be loaded to access the API. Please try refreshing the account in the Events API Settings.', 'Zoom account loading error message for Zoom user validation.', 'events-virtual' );

			$this->admin_template->template( 'components/message', [
				'message' => $error_message,
				'type'    => 'error',
			] );

			wp_die();
		}

		$settings        = $this->api->fetch_user( $zoom_host_id, true );
		if ( empty( $settings['feature'] ) ) {
			$error_message = _x( 'The Zoom API did not return the user settings. Please try refreshing the account in the Events Integration Settings.', 'Zoom API loading error message for Zoom user validation.', 'events-virtual' );

			$this->admin_template->template( 'components/message', [
				'message' => $error_message,
				'type'    => 'error',
			] );

			wp_die();
		}

		$webinar_support       = $this->api->get_webinars_support( $settings );
		$password_requirements = $this->api->get_password_requirements( $settings );

		/** @var \Tribe\Events\Virtual\Meetings\Zoom\Classic_editor */
		$classic_editor = tribe( Classic_Editor::class );
		$generation_urls = $classic_editor->get_link_creation_urls( $event, $webinar_support );

		$this->admin_template->template(
		'virtual-metabox/api/type-options',
			[
				'api_id'                => $this->api::$api_id,
				'generation_urls'       => $generation_urls,
				'password_requirements' => $password_requirements,
				'metabox_id'            => Metabox::$id,
			],
			true
		);

		wp_die();
	}
}
