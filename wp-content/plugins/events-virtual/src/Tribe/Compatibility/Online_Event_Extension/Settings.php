<?php
/**
 * Handles overriding of settings from Online Events extension to Virtual Events plugin.
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Compatibility\Online_Event_Extension
 */

namespace Tribe\Events\Virtual\Compatibility\Online_Event_Extension;

/**
 * Class Settings
 *
 * @since   1.0.0
 *
 * @package Tribe\Events\Virtual\Compatibility\Online_Event_Extension
 */
class Settings {

	/**
	 * Injects some additional messaging into the extension
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,mixed> $fields The current setting fields for the tab.
	 * @param string              $tab    The current tab slug.
	 *
	 * @return array<string,mixed> The tab fields, modified if required.
	 */
	public function inject_extension_settings( $fields, $tab ) {
		if ( 'online-events' !== $tab ) {
			return $fields;
		}

		$fields['info-box-description']['html'] = wp_kses_post(
			sprintf(
			/* Translators: Opening and closing tags. */
				__(
					'%1$sYou have the %2$sVirtual Events%3$s plugin installed, these settings are superseded by it.%4$s',
					'events-virtual'
				),
				'<p>',
				'<a href="' . esc_url( '#' ) . '">',
				'</a>',
				'</p>'
			)
			. sprintf(
			/* Translators: Opening and closing tags. */
				__(
					'%1$sYou should deactivate The Events Control extension as all functionality is handled by the Virtual Events plugin.%2$s',
					'events-virtual'
				),
				'<p>',
				'</p>'
			)
		);

		return $fields;
	}
}
