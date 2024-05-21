<?php
/**
 * Handles the the labels for the classic editor ui for an API integration.
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Integrations\Editor
 */

namespace Tribe\Events\Virtual\Integrations\Editor;

/**
 * Class Abstract_Classic_Labels
 *
 * @since 1.9.0
 *
 * @package Tribe\Events\Virtual\Integrations\Editor
 */
abstract class Abstract_Classic_Labels extends Abstract_Classic {

	/**
	 * Returns the localized, but not HTML-escaped, title for the UI.
	 *
	 * @since 1.9.0
	 *
	 * @return string The localized, but not HTML-escaped, message to set up an APO integration.
	 */
	protected function get_ui_title() {
		return sprintf(
			// translators: the placeholders is for the API name.
			_x(
				'%1$s Meeting',
				'Title for an API integration in the event classic editor UI.',
				'events-virtual'
			),
			static::$api_name
		);
	}

	/**
	 * Returns the localized, but not HTML-escaped, message to set up an API integration.
	 *
	 * @since 1.9.0
	 *
	 * @return string The localized, but not HTML-escaped, message to set up an API integration.
	 */
	protected function get_connect_to_label() {
		return sprintf(
			// translators: the placeholders is for the API name.
			_x(
				'Set up %1$s integration',
				'Label for the link to set up an API integration in the event classic editor UI.',
				'events-virtual'
			),
			static::$api_name
		);
	}

	/**
	 * Returns the remove link label.
	 *
	 * @since 1.9.0
	 *
	 * @return string The remove link label, unescaped.
	 */
	protected function get_remove_link_label() {
		return sprintf(
			// translators: the placeholders is for the API name.
			_x(
				'Remove %1$s link',
				'The label for the admin UI control that allows removing an API integration from the event.',
				'events-virtual'
			),
			static::$api_name
		);
	}

	/**
	 * Get the label for the account disabled template.
	 *
	 * @since 1.9.0
	 *
	 * @param bool $is_loaded Whether the account is loaded or not.
	 *
	 * @return string The link label for the account disabled template.
	 */
	protected function get_is_loaded_label( $is_loaded ) {
		return $is_loaded
			? sprintf(
				// translators: the placeholder is for the API name.
				_x(
					'%1$s Account Disabled',
					'Header of the disabled account details section when an account is disabled.',
					'events-virtual'
				),
				static::$api_name
			)
			: sprintf(
				// translators: the placeholder is for the API name.
				_x(
					'%1$s Account Not Found',
					'Header of the account details section shown when no account is loaded.',
					'events-virtual'
				),
				static::$api_name
			);
	}

	/**
	 * Get the body text for the account disabled template.
	 *
	 * @since 1.9.0
	 *
	 * @param bool $is_loaded Whether the account is loaded or not.
	 *
	 * @return string The link label for the account disabled template.
	 */
	protected function get_is_loaded_body( $is_loaded ) {
		return $is_loaded
			? sprintf(
				// translators: the placeholder is for the API name.
				_x(
					'The %1$s account is disabled on your website, please use the link to go to the API settings and activate it.',
					'The message to display when an API account is disabled.',
					'events-virtual'
				),
				static::$api_name
			)
			: sprintf(
				// translators: the placeholder is for the API name.
				_x(
					'The %1$s account is not found on your website, please use the link and add back the account to your site.',
					'The message to display when an API account is not found.',
					'events-virtual'
				),
				static::$api_name
			);
	}

	/**
	 * Get the link label for the account disabled template.
	 *
	 * @since 1.9.0
	 *
	 * @param bool $is_loaded Whether the account is loaded or not.
	 *
	 * @return string The link label for the account disabled template.
	 */
	protected function get_is_loaded_link_label( $is_loaded ) {
		return $is_loaded
			? _x(
				'Enable your account on the settings page',
				'The label of the button to link back to the settings to enable an API account.',
				'events-virtual'
			)
			: _x(
				'Add your account on the settings page',
				'The label of the button to link back to the settings to add an API account.',
				'events-virtual'
			);
	}

	/**
	 * Returns the generic message to indicate an error to perform an action in the context of an API integration.
	 *
	 * @since 1.9.0
	 *
	 * @return string The error message, unescaped.
	 */
	protected function get_unknown_error_message() {
		return sprintf(
			// translators: the placeholder is for the API name.
			_x(
				'Unknown error from %1$s',
				'A message to indicate an unknown error happened while interacting with the an API integration.',
				'events-virtual'
			),
			static::$api_name
		);
	}

	/**
	 * Returns the no hosts found title.
	 *
	 * @since 1.9.0
	 *
	 * @return string The error message, unescaped.
	 */
	protected function get_no_hosts_found_title() {
		return _x(
			'No Hosts Found',
			'Header shown if no hosts are found before generating an API connection.',
			'events-virtual'
		);
	}

	/**
	 * Returns the no hosts found body message.
	 *
	 * @since 1.9.0
	 *
	 * @return string The error message, unescaped.
	 */
	protected function get_no_hosts_found_message() {
		return sprintf(
			// translators: the placeholder is for the API name.
			_x(
				'The %1$s account could not load any hosts, please follow the link to refresh your account and try again.',
				'The message shown if no hosts are found before generating an API connection.',
				'events-virtual'
			),
			static::$api_name
		);
	}

	/**
	 * Returns the title of the error container.
	 *
	 * @since 1.9.0
	 *
	 * @return string The error title, unescaped.
	 */
	protected function get_the_error_message_title() {
		return sprintf(
			// translators: the placeholder is for the API name.
			_x(
				'%1$s Link',
				'Header of the details shown when an attempt to generate an API connection fails.',
				'events-virtual'
			),
			static::$api_name
		);
	}

	/**
	 * Returns the message of the error container.
	 *
	 * @since 1.9.0
	 *
	 * @return string The error message, unescaped.
	 */
	protected function get_the_error_message() {
		return sprintf(
			// translators: the placeholder is for the API name.
			_x(
				'We were not able to generate a %1$s link.',
				'Message shown when an attempt to generate an API connection fails.',
				'events-virtual'
			),
			static::$api_name
		);
	}

	/**
	 * Returns the no hosts found body message.
	 *
	 * @since 1.9.0
	 *
	 * @return string The error message, unescaped.
	 */
	protected function get_the_error_message_details_title() {
		return sprintf(
			// translators: the placeholder is for the API name.
			_x(
				'%1$s error:',
				'Header of the error details section shown when an attempt to generate an API connection link fails.',
				'events-virtual'
			),
			static::$api_name
		);
	}

	/**
	 * Get the confirmation text for removing an API connection.
	 *
	 * @since 1.9.0
	 *
	 * @return string The confirmation text.
	 */
	protected function get_remove_confirmation_text() {
		return sprintf(
			// translators: the placeholder is for the API name.
			_x(
				'Are you sure you want to remove the %1$s meeting from this event? This operation cannot be undone.',
				'The message to display to confirm a user would like to remove an API connection from an event.',
				'events-virtual'
			),
			static::$api_name
		);
	}
}
