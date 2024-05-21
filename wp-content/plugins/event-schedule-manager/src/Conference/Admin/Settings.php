<?php
/**
 * Organizes Event Schedule Manager Settings.
 *
 * @since   1.0.0
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

use TEC\Conference\Plugin;
use TEC\Conference\Vendor\StellarWP\Arrays\Arr;
use TEC\Conference\Vendor\StellarWP\Assets\Assets;
use TEC\Conference\Vendor\StellarWP\Telemetry\Config;
use TEC\Conference\Vendor\StellarWP\Telemetry\Opt_In\Status as Telemetry_Status;
use TEC\Conference\Vendor\StellarWP\Uplink\Admin\License_Field;
use TEC\Conference\Vendor\StellarWP\Uplink\Config as Uplink_Config;

/**
 * Class Settings
 *
 * Handles the settings for the Event Schedule Manager.
 *
 * @since   1.0.0
 * @package TEC\Conference\Admin
 */
class Settings extends Menu {

	/**
	 * Event Schedule Manager settings page slug.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $settings_page_id = 'event-schedule-manager-settings';

	/**
	 * Registers options page for settings.
	 *
	 * @since 1.0.0
	 */
	public function options_page() {

		// Divider.
		add_submenu_page(
		    $this->get_menu_slug(),
		    '',
		    '',
		    'read',
		    '#'
		);

		$page = add_submenu_page(
			$this->get_menu_slug(),
			 esc_html_x( 'Settings', 'submenu page title', 'event-schedule-manager' ),
			 esc_html_x( 'Settings', 'submenu menu title', 'event-schedule-manager' ),
			'manage_options',
			static::$settings_page_id,
			[ $this, 'options_page_html' ]
		);

		add_action( "admin_print_scripts-$page", array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueues Admin Assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		Assets::instance()->enqueue_group( 'event-schedule-manager-admin' );
	}

	/**
	 * Initializes settings and fields.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		// Register a settings section in the "tec" page.
		add_settings_section( 'tec_esm_section_settings', esc_html_x( 'General Settings', 'settings section title', 'event-schedule-manager' ), [ $this, 'section_settings_cb' ], 'tec_esm' );

		// Register schedule page URL setting for "tec" page.
		register_setting( 'tec_esm', 'esm_schedule_page_url' );

		// Register schedule page URL field in the "tec_section_info" section, inside the "tec" page.
		add_settings_field( 'esm_schedule_page_url', esc_html_x( 'Schedule page URL', 'settings field title', 'event-schedule-manager' ), [ $this, 'field_schedule_page_url_cb' ], 'tec_esm', 'tec_esm_section_settings' );

		// Register speakers page URL setting for "tec" page.
		register_setting( 'tec_esm', 'esm_speakers_page_url', [ $this, 'sanitize_field_speakers_page_url' ] );

		// Register speakers page URL field in the "tec_section_info" section, inside the "tec" page.
		add_settings_field( 'esm_speakers_page_url', esc_html_x( 'Speakers page URL', 'settings field title', 'event-schedule-manager' ), [ $this, 'field_speakers_page_url_cb' ], 'tec_esm', 'tec_esm_section_settings' );

		// Register sponsor page URL setting for "tec" page.
		register_setting( 'tec_esm', 'tec_field_sponsor_page_url', [ $this, 'sanitize_field_sponsor_page_url' ] );

		// Register sponsor page URL field in the "tec_section_info" section, inside the "tec" page.
		add_settings_field( 'tec_field_sponsor_page_url', esc_html_x( 'Sponsor URL redirect', 'settings field title', 'event-schedule-manager' ), [ $this, 'field_sponsor_page_url_cb' ], 'tec_esm', 'tec_esm_section_settings' );

		register_setting( 'tec_esm', 'tec_conference_sponsor_level_order', [ $this, 'validate_sponsor_options' ] );

		add_settings_field(
			'tec_field_sponsor_level_order',
			 esc_html_x( 'Sponsor level order', 'settings field title', 'event-schedule-manager' ),
			[
				$this,
				'render_order_sponsor_levels'
			],
		'tec_esm',
		'tec_esm_section_settings'
		);

		register_setting( 'tec_esm', 'tec_speaker_level_order', [ $this, 'validate_sponsor_options' ] );

		add_settings_field(
			'tec_field_speaker_group_order',
			 esc_html_x( 'Speaker group order', 'settings field title', 'event-schedule-manager' ),
			[
				$this,
				'render_order_speaker_levels'
			],
			'tec_esm',
			'tec_esm_section_settings'
		);

		$opted = Config::get_container()->get( Telemetry_Status::class )->is_active();
		switch( $opted ) {
			case Telemetry_Status::STATUS_ACTIVE :
				$label = esc_html_x( 'Opt out of Telemetry', 'Settings label for opting out to Telemetry.', 'event-schedule-manager' );
			default :
				$label = esc_html_x( 'Opt in to Telemetry', 'Settings label for opting in to Telemetry.', 'event-schedule-manager' );
		}

		add_settings_field(
			'tec_field_telemetry_optin',
			$label,
			[
				$this,
				'render_telemetry_optin'
			],
			'tec_esm',
			'tec_esm_section_settings'
		);

		register_setting( 'tec_esm', 'tec_field_telemetry_optin', [ $this, 'validate_boolean' ] );

		// Add a 'License' settings section
		add_settings_section(
			'tec_esm_section_license',
			esc_html_x( 'License Settings', 'settings section title', 'event-schedule-manager' ),
			[ $this, 'section_license_cb' ],
			'tec_esm_license'
		);

		add_settings_field(
			'tec_field_license',
			esc_html_x( 'License', 'settings license field title', 'event-schedule-manager' ),
			[
				$this,
				'render_license'
			],
			'tec_esm_license',
			'tec_esm_section_license'
		);

	}

	public function validate_boolean( $value ) {
		return filter_var( $value, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Settings section callback for general settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments passed to the callback.
	 */
	public function section_settings_cb( $args ) {
		?>
		<div id="<?php echo esc_attr( $args['id'] ); ?>" class="tec-conference-settings-top-section__wrap">
			<h3><?php echo esc_html_x( 'Creating and displaying your event schedule.', 'Header title on the Settings page.', 'event-schedule-manager' ); ?></h3>
			<div class="tec-conference-settings-header-links-sections__wrap">
				<div class="tec-conference-settings-header-links-section__wrap">
					<ul>
						<li><?php echo esc_html_x( 'Documentation', 'Section header for documentation links on the Settings page.', 'event-schedule-manager' ); ?></li>
						<li>
							<a href="https://evnt.is/1bd1"><?php echo esc_html_x( 'Getting Started Guide', 'Documentation link on the Settings page.', 'event-schedule-manager' ); ?></a>
						</li>
						<li>
							<a href="https://evnt.is/1bd5" rel="noopener" target="_blank"><?php echo esc_html_x( 'Event Schedule Creation Guide', 'Documentation link on the Settings page.', 'event-schedule-manager' ); ?></a>
						</li>
						<li>
							<a href="https://evnt.is/1bd6" rel="noopener" target="_blank"><?php echo esc_html_x( 'Shortcode KB', 'Documentation link on the Settings page.', 'event-schedule-manager' ); ?></a>
						</li>
					</ul>
				</div>
				<div class="tec-conference-settings-header-links-section__wrap">
					<ul>
						<li><?php echo esc_html_x( 'Having trouble?', 'Section header for help links on the Settings page.', 'event-schedule-manager' ); ?></li>
						<li><a href="https://theeventscalendar.com/support/#contact" target="_blank"><?php echo esc_html_x( 'Help', 'Help link on the Settings page.', 'event-schedule-manager' ); ?></a></li>
					</ul>
				</div>
				<div class="tec-conference-settings-header-links-section__wrap">
					<ul>
						<li><?php echo esc_html_x( 'Shortcodes', 'Section header for help links on the Settings page.', 'event-schedule-manager' ); ?></li>
						<li>
							<code>[tec_schedule]</code> <?php echo esc_html_x( '(Displays the Schedule using date, tracks, session_link, color_scheme, align, layout, row_height, and content parameters)', 'Explanation of [tec_schedule] shortcode on the Settings page.', 'event-schedule-manager' ); ?>
						</li>
						<li>
							<code>[tec_speakers]</code> <?php echo esc_html_x( '(Displays the Speakers page using show_image, image_size, show_content, posts_per_page, orderby, order, speaker_link, track, group, columns, gap and align parameters)', 'Explanation of [tec_speakers] shortcode on the Settings page.', 'event-schedule-manager' ); ?>
						</li>
						<li>
							<code>[tec_sponsors]</code> <?php echo esc_html_x( '(Displays the Sponsors page using link, title, content, excerpt_length, and heading_level)', 'Explanation of [tec_sponsors] shortcode on the Settings page.', 'event-schedule-manager' ); ?>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Schedule page URL field callback.
	 *
	 * @since 1.0.0
	 */
	public function field_schedule_page_url_cb() {
		?>
		<input type="text" name="esm_schedule_page_url" value="<?php echo get_option( 'esm_schedule_page_url' ); ?>" style="width: 450px;">
		<p class="description">
			<?php echo esc_html_x( 'The URL of the page that your conference schedule is embedded on.', 'description for the Schedule Page URL,= field.', 'event-schedule-manager' ); ?>
		</p>
		<?php
	}

	/**
	 * Speakers page URL field callback.
	 *
	 * @since 1.0.0
	 */
	public function field_speakers_page_url_cb() {
		?>
		<input type="text" name="esm_speakers_page_url" value="<?php echo get_option( 'esm_speakers_page_url' ); ?>" style="width: 450px;">
		<p class="description">
			<?php echo esc_html_x( 'The URL of the page that your speakers are embedded on.', 'The description for the speaker page url.', 'event-schedule-manager' ); ?>
		</p>
		<?php
	}

	/**
	 * Sanitize the speakers page URL value before being saved to database.
	 *
	 * @since 1.0.0
	 *
	 * @param string $speakers_page_url The URL for the speakers page.
	 *
	 * @return string Sanitized URL.
	 */
	public function sanitize_field_speakers_page_url( $speakers_page_url ) {
		return sanitize_text_field( $speakers_page_url );
	}

	/**
	 * Sponsor page url callback.
	 *
	 * @since 1.0.0
	 */
	public function field_sponsor_page_url_cb() {
		$sponsor_url = get_option( 'tec_field_sponsor_page_url' );
		?>
		<select name="tec_field_sponsor_page_url" id="tec-sponsors-url">
			<option
				value="sponsor_page"
				<?php selected( $sponsor_url === 'sponsor_page' ); ?>
			>
				<?php echo esc_html_x( 'Redirect to Sponsor Page', 'Sponsor url redirect option for the sponsor internal page.', 'event-schedule-manager' ); ?>
			</option>
			<option
				value="sponsor_site"
				<?php selected( $sponsor_url === 'sponsor_site' ); ?>
			>
				<?php echo esc_html_x( 'Redirect to Sponsor Site', 'Sponsor url redirect option to send to the sponsor url.', 'event-schedule-manager' ); ?>
			</option>
		</select>
		<p class="description">
			<?php echo esc_html_x( 'The location to redirect sponsor links to on the session single page.', 'description for sponsor url redirect options.', 'event-schedule-manager' ); ?>
		</p>
		<?php
	}

	/**
	 * Sanitize the sponsor page URL value before being saved to database.
	 *
	 * @since 1.0.0
	 *
	 * @param string $redirect The redirect option for the sponsor link.
	 *
	 * @return string Sanitized redirect option.
	 */
	public function sanitize_field_sponsor_page_url( $redirect ) {
		$valid_redirects = [ 'sponsor_page', 'sponsor_site' ];

		if ( in_array( $redirect, $valid_redirects, true ) ) {
			return $redirect;
		}

		return '';
	}

	/**
	 * Displays the settings form.
	 *
	 * @since 1.0.0
	 */
	public function options_page_html() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check if the user have submitted the settings.
		// WordPress will add the "settings-updated" $_GET parameter to the url.
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'tec_messages', 'tec_message', esc_html_x( 'Settings Saved', 'Settings saved message.', 'event-schedule-manager' ), 'updated' );
		}

		// Get the current active tab
		$active_tab = Arr::get( $_GET, 'tab', 'general' );

		/**
		 * Telemetry uses this to determine when/where the optin modal should be shown.
		 * i.e. the modal is shown when we run this.
		 *
		 * @since 1.0.0
		 *
		 * @param string $plugin_slug The slug of the plugin showing the modal.
		 */
		do_action( 'stellarwp/telemetry/optin', PLUGIN::SLUG );

		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<a
					href="<?php echo esc_url( $this->get_url( [ 'tab' => 'general'] )); ?>"
					class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>"
				>
					<?php echo esc_html_x( 'General', 'settings tab title.', 'event-schedule-manager' ); ?>
				</a>
				<a
					href="<?php echo esc_url( $this->get_url( [ 'tab' => 'license'] )); ?>"
					class="nav-tab <?php echo $active_tab === 'license' ? 'nav-tab-active' : ''; ?>"
				>
					<?php echo esc_html_x( 'License', 'settings tab title.', 'event-schedule-manager' ); ?>
				</a>
			</h2>
			<form action="options.php" method="post">
				<?php
				// Render settings conditionally based on the active tab
				if ( $active_tab === 'general' ) {
					settings_fields( 'tec_esm' );
					do_settings_sections( 'tec_esm' );
				} elseif ( $active_tab === 'license' ) {
					settings_fields( 'tec_esm_license' );
					do_settings_sections( 'tec_esm_license' );
				}

				if ( $active_tab !== 'license' ) {
					submit_button( esc_html_x( 'Save Settings', 'Settings saved button text.', 'event-schedule-manager' ) );
				}
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Returns the main admin settings URL.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments to pass to the URL.
	 *
	 * @return string The URL to the admin settings page.
	 */
	public function get_url( array $args = [] ) {
		$defaults = [
			'page' => static::$settings_page_id,
		];

		if ( ! is_network_admin() ) {
			$defaults['post_type'] = Plugin::SESSION_POSTTYPE;
		}

		// Allow the link to be "changed" on the fly.
		$args = wp_parse_args( $args, $defaults );

		$wp_url = is_network_admin() ? network_admin_url( 'settings.php' ) : admin_url( 'edit.php' );

		// Keep the resulting URL args clean.
		$url = add_query_arg( $args, $wp_url );

		/**
		 * Filters the admin settings URL.
		 *
		 * @since 1.0.0
		 *
		 * @param string $url The admin settings URL.
		 */
		return apply_filters( 'tec_event_schedule_manager_settings_url', $url );
	}

	/**
	 * Renders the Order Sponsor Levels admin page.
	 *
	 * @since 1.0.0
	 */
	public function render_order_sponsor_levels() {
		$this->render_order_levels(
			'tec_conference_sponsor_level_order',
			Plugin::SPONSOR_LEVEL_TAXONOMY,
			 esc_html_x( 'Change the order of sponsor levels displayed in the sponsors page template. Create Sponsor Levels to unlock this feature.', 'Directions for ordering of sponsor levels in the settings.', 'event-schedule-manager' )
		);
	}

	/**
	 * Renders the Order Speaker Levels admin page.
	 *
	 * @since 1.0.0
	 */
	public function render_order_speaker_levels() {
		$this->render_order_levels(
			'tec_speaker_level_order',
			Plugin::GROUP_TAXONOMY,
			esc_html_x( 'Change the order of speaker groups displayed in the speaker page template. Create Groups to unlock this feature.', 'Directions for ordering of speaker groups in the settings.', 'event-schedule-manager' )
		);
	}

	/**
	 * General method to render the order levels.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option   The option key to fetch from the database.
	 * @param string $taxonomy The taxonomy to fetch terms for.
	 * @param string $message  The instructional message to display.
	 */
	private function render_order_levels( string $option, string $taxonomy, string $message ) {
		if ( ! isset( $_REQUEST['updated'] ) ) {
			$_REQUEST['updated'] = false;
		}
		?>
		<div class="description tec-sponsor-order-instructions">
			<?php echo $message; ?>
		</div>
		<?php
			$levels = $this->get_sponsor_levels( $option, $taxonomy );
			if ( empty( $levels ) ) {
				return;
			}
		?>
		<ul class="tec-sponsor-order">
			<?php
				foreach ( $levels as $term ) :
					if ( ! is_a( $term, 'WP_Term' ) ) {
						continue;
					}
			?>
				<li class="level">
					<input type="hidden" class="level-id" name="<?php echo $option; ?>[]" value="<?php echo esc_attr( $term->term_id ); ?>"/>
					<?php echo esc_html( $term->name ); ?>
				</li>
			<?php
				endforeach;
			?>
		</ul>
		<?php
	}

	/**
	 * Validates the sponsor options ensuring they are an array of integers.
	 *
	 * @since 1.0.0
	 *
	 * @param array|null $input The input options array.
	 *
	 * @return array|null Sanitized array of integers or null.
	 */
	public function validate_sponsor_options( ?array $input ): ?array {
		if ( ! is_array( $input ) ) {
			return null;
		}

		return array_values( array_map( 'intval', $input ) );
	}

	/**
	 * Returns the sponsor level terms in set order.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option   The option key to fetch from the database.
	 * @param string $taxonomy The taxonomy to fetch terms for.
	 *
	 * @return array Array of term objects.
	 */
	public function get_sponsor_levels( string $option, string $taxonomy ): array {
		$option       = (array) get_option( $option, [] );
		$term_objects = get_terms( $taxonomy, [ 'get' => 'all' ] );
		$terms        = [];

		foreach ( $term_objects as $term ) {
			if ( ! is_a( $term, 'WP_Term' ) ) {
				continue;
			}

			$terms[ $term->term_id ] = $term;
		}

		return $this->order_terms_by_option( $terms, $option );
	}

	/**
	 * Orders the terms by a given option.
	 *
	 * @since 1.0.0
	 *
	 * @param array $terms  The terms to be ordered.
	 * @param array $option The order option.
	 *
	 * @return array The ordered terms.
	 */
	private function order_terms_by_option( array $terms, array $option ): array {
		$ordered_terms = [];

		foreach ( $option as $term_id ) {
			if ( isset( $terms[ $term_id ] ) ) {
				$ordered_terms[] = $terms[ $term_id ];
				unset( $terms[ $term_id ] );
			}
		}

		return array_merge( $ordered_terms, array_values( $terms ) );
	}

	/**
	 * Renders the license
	 *
	 * @since 1.0.0
	 */
	public function render_license() {
		$fields = Uplink_Config::get_container()->get( License_Field::class );
  		$fields->render();
	}

	/**
	 * Renders the telemetry optin.
	 * 
	 * @since 1.0.0
	 */
	public function render_telemetry_optin() {
		do_action( 'render_esm_telemetry_setting' );
	}

	/**
	 * Settings section callback for license settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments passed to the callback.
	 */
	public function section_license_cb( $args ) {}
}
