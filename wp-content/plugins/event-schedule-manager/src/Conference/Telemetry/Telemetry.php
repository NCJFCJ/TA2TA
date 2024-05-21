<?php
/**
 * Class that handles interfacing with TEC\Conference\Vendor\StellarWP\Telemetry.
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Telemetry
 */

namespace TEC\Conference\Telemetry;

use TEC\Conference\Plugin;
use TEC\Conference\Vendor\StellarWP\Arrays\Arr;
use TEC\Conference\Vendor\StellarWP\Telemetry\Core as Core;
use TEC\Conference\Vendor\StellarWP\Telemetry\Config;
use TEC\Conference\Vendor\StellarWP\Telemetry\Opt_In\Status as Telemetry_Status;

/**
 * Class Telemetry
 *
 * @since 1.0.0
 * @package TEC\Conference\Telemetry
 */
class Telemetry {

	/**
	 * The Telemetry plugin slug for Event Schedule Manager.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $plugin_slug = Plugin::SLUG;

	/**
	 * The "plugin path" for Event Schedule Manager main file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected static $plugin_path = Plugin::FILE;

	/**
	 * Boot the Telemetry library for ESM.
	 *
	 * @since 1.0.0
	 *
	 * @param \TEC\Conference\Contracts\Container $container The container to use configure with Telemetry.
	 */
	public static function boot( $container ) {
		Config::set_container( $container );
		// Set the full URL for the Telemetry Server API.
		$telemetry_server = ! defined( 'STELLARWP_TELEMETRY_SERVER' ) ? 'https://telemetry.stellarwp.com/api/v1': STELLARWP_TELEMETRY_SERVER;
		Config::set_server_url( $telemetry_server );
		// Set a unique prefix for actions & filters.
		Config::set_hook_prefix( self::$plugin_slug );
		// Set a unique plugin slug.
		Config::set_stellar_slug( self::$plugin_slug );
		// Initialize the library.
		Core::instance()->init( CONFERENCE_SCHEDULE_PRO_FILE );
	}

	/**
	 * Filters the modal optin args to be specific to ESM.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string|mixed> $original_optin_args The original args.
	 *
	 * @return array<string|mixed> The filtered args.
	 */
	public function filter_telemetry_optin_args( $original_optin_args ): array {
		$intro_message = sprintf(
			/* Translators: %1$s - the user name. */
			__( 'Hi, %1$s! This is an invitation to help our StellarWP community.', 'event-schedule-manager' ),
			wp_get_current_user()->display_name // escaped after string is assembled, below.
		);

		$intro_message .= ' ' . __( 'If you opt-in, some data about your usage of Event Schedule Manager and future StellarWP Products will be shared with our teams (so they can work their butts off to improve).' , 'event-schedule-manager');
		$intro_message .= ' ' . __( 'We will also share some helpful info on WordPress, and our products from time to time.' , 'event-schedule-manager');
		$intro_message .= ' ' . __( 'And if you skip this, that’s okay! Our products still work just fine.', 'event-schedule-manager' );

		$tec_optin_args = [
			'plugin_logo'        => Plugin::get_asset_path() . 'images/tec-brand.svg',
			'plugin_logo_width'  => 'auto',
			'plugin_logo_height' => 42,
			'plugin_logo_alt'    => 'The Events Calendar Logo',
			'plugin_name'        => 'Event Schedule Manager',
			'plugin_slug'        => self::$plugin_slug,
			'heading'            => __( 'We hope you love Event Schedule Manager!', 'event-schedule-manager' ),
			'intro'              => esc_html( $intro_message ),
			'permissions_url'    => esc_url( $this->get_permissions_url() ),
			'tos_url'            => esc_url( $this->get_terms_url() ),
			'privacy_url'        => esc_url( $this->get_privacy_url() ),
		];

		return array_merge( $original_optin_args, $tec_optin_args );
	}

	/**
	 * Get the URL for the permission link in the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_permissions_url() {
		return esc_url( 'https://evnt.is/1bcl' );
	}

	/**
	 * Get the URL for the TOS link in the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_terms_url() {
		return esc_url( 'https://evnt.is/1bcm' );
	}

	/**
	 * Get the URL for the Privacy Policy link in the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_privacy_url() {
		return esc_url( 'https://evnt.is/1bcn' );
	}

	/**
	 * Render the checkbox, label, and description.
	 * Hooked to the `render_esm_telemetry_setting` action in `src/Conference/Admin/Settings.php`
	 * 
	 * @since 1.0.0
	 */
	public function  render_esm_telemetry_setting() {
		$opted = Config::get_container()->get( Telemetry_Status::class )->is_active();
		?>
		<label for="tec_field_telemetry_optin" class="tec-visually-hide">
			<?php esc_html_e( 'Help us improve Event Schedule Manager by sharing usage data.', 'event-schedule-manager' ); ?>
		</label>
		<input type="checkbox" id="tec_field_telemetry_optin" name="tec_field_telemetry_optin" value="1" <?php checked( $opted ); ?> />
		<p class="description tec-conference-description">
			<?php  
				echo sprintf(
					/* Translators: Description of the Telemetry optin setting.
					%1$s: opening anchor tag for permissions link.
					%2$s: opening anchor tag for terms of service link.
					%3$s: opening anchor tag for privacy policy link.
					%4$s: closing anchor tags.
					*/
					_x(
						'Share usage data with Event Schedule Manager and StellarWP. Disregard this setting if you already opted in when you installed the plugin. %1$sWhat permissions are being granted?%4$s %2$sRead our terms of service%4$s. %3$sRead our privacy policy%4$s.',
						'Description of optin setting.',
						'event-schedule-manager'
					),
					'<a href=" ' . $this->get_permissions_url() . ' ">',
					'<a href=" ' . $this->get_terms_url() . ' ">',
					'<a href=" ' . $this->get_privacy_url() . ' ">',
					'</a>'
				)
			?>
		</p>
		<?php
	}

	/**
	 * Update Telemetry based on the ESM optin setting field.
	 * 
	 * @since 1.0.0
	 */
	public function action_save_opt_in_setting_field() {
		// check that we're coming from the correct options page.
		$option_page = Arr::get( $_POST, 'option_page', '' );
		if ( $option_page !== 'tec_esm' ) {
			return;
		}

		// Get an instance of the Status class.
		$status = Config::get_container()->get( Telemetry_Status::class );

		// Get the value submitted on the settings page - as an integer.
		$value = (int) filter_input( INPUT_POST, 'tec_field_telemetry_optin', FILTER_VALIDATE_BOOLEAN );

		// Tell Telemetry we're opting in (or out).
		$status->set_status( $value, self::$plugin_slug );
	}
}
