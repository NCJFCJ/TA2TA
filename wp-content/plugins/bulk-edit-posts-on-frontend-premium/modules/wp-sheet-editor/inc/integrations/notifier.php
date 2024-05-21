<?php
if ( ! class_exists( 'WPSE_Extension_Notifier' ) ) {

	class WPSE_Extension_Notifier {

		private static $instance = false;

		private function __construct() {        }

		public function init() {
			if ( ! is_admin() || ! current_user_can( 'install_plugins' ) || ! function_exists( 'freemius' ) || ! class_exists( 'FS_Options' ) ) {
				return;
			}
			add_filter( 'vg_sheet_editor/options_page/options', array( $this, 'add_settings_page_options' ) );

			if ( ! empty( VGSE()->options['disable_important_extensions_toolbar'] ) ) {
				return;
			}
			if ( ! VGSE()->helpers->is_editor_page() ) {
				return;
			}
			$suggested_extensions = $this->get_suggested_extensions();
			if ( ! $suggested_extensions ) {
				return;
			}
			add_action( 'vg_sheet_editor/editor/before_init', array( $this, 'register_toolbar' ), 20 );
		}

		/**
		 * Add fields to options page
		 * @param array $sections
		 * @return array
		 */
		public function add_settings_page_options( $sections ) {
			$sections['misc']['fields'][] = array(
				'id'    => 'disable_important_extensions_toolbar',
				'type'  => 'switch',
				'title' => __( 'Disable the popup that asks you to install important extensions?', VGSE()->textname ),
				'desc'  => __( 'We show a popup asking you to install free extensions when we detect that you are using a third-party plugin that requires special compatibility, which helps us prevent errors.', VGSE()->textname ),
			);
			return $sections;
		}

		public function is_extension_outdated( $folder_name, $official_version ) {
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$php_files = VGSE()->helpers->get_files_list( WP_PLUGIN_DIR . '/' . $folder_name );
			$out       = false;
			foreach ( $php_files as $php_file ) {
				$plugin_data = get_plugin_data( $php_file );

				if ( ! empty( $plugin_data['Version'] ) && version_compare( $official_version, $plugin_data['Version'] ) === 1 ) {
					$out = true;
					break;
				}
			}
			return $out;
		}

		public function get_suggested_extensions() {
			$file_path = __DIR__ . '/extensions.json';
			$out       = array();
			if ( ! file_exists( $file_path ) || ! file_get_contents( $file_path ) ) {
				return $out;
			}
			$fs_options     = FS_Options::instance( WP_FS__ACCOUNTS_OPTION_NAME, true );
			$raw_fs_plugins = wp_list_filter( $fs_options->get_option( WP_FS__MODULE_TYPE_PLUGIN . 's' ), array( 'is_premium' => true ) );

			if ( empty( $raw_fs_plugins ) ) {
				return $out;
			}
			$fs_plugins = wp_list_pluck( $raw_fs_plugins, 'file', 'id' );
			$extensions = json_decode( file_get_contents( $file_path ), true );

			foreach ( $extensions as $extension ) {
				// Skip extensions that are installed already if they are up to date
				if ( class_exists( $extension['className'] ) ) {
					if ( $this->is_extension_outdated( $extension['file_name'], $extension['version'] ) ) {
						$extension['requires_update'] = true;
					} else {
						continue;
					}
				} else {
					$extension['requires_update'] = false;
				}
				$extension_key = $extension['file_name'];
				if ( $extension['status'] !== 'publish' || empty( $extension['name'] ) || empty( $extension['description'] ) ) {
					continue;
				}

				// Skip if a required paid plan doesn't exists
				if ( ! empty( $extension['freemiusFunctionName'] ) && function_exists( $extension['freemiusFunctionName'] ) && ! $extension['freemiusFunctionName']()->can_use_premium_code__premium_only() ) {
					continue;
				}
				// Check if required fs plugins are found
				$all_plugins_exist = array();
				foreach ( $extension['requiredWPSEPlugin'] as $plugins ) {
					if ( is_array( $plugins ) ) {
						$plugin_is_valid = false;
						foreach ( $plugins as $plugin_id ) {
							if ( ! empty( $fs_plugins[ $plugin_id ] ) && is_plugin_active( $fs_plugins[ $plugin_id ] ) ) {
								$plugin_is_valid = true;
								break;
							}
						}
					} else {
						$plugin_is_valid = ! empty( $fs_plugins[ $plugins ] ) && is_plugin_active( $fs_plugins[ $plugins ] );
					}
					$all_plugins_exist[] = $plugin_is_valid;
				}

				// Skip if at least one check is false
				if ( in_array( false, $all_plugins_exist, true ) ) {
					continue;
				}

				// Check if required classes are valid
				$all_classes_exist = array();
				foreach ( $extension['dependencyClasses'] as $class ) {
					if ( is_array( $class ) ) {
						$class_is_valid = false;
						foreach ( $class as $class_name ) {
							if ( class_exists( $class_name ) ) {
								$class_is_valid = true;
								break;
							}
						}
					} else {
						$class_is_valid = class_exists( $class );
					}
					$all_classes_exist[] = $class_is_valid;
				}

				// Skip if at least one class check is false
				if ( in_array( false, $all_classes_exist, true ) ) {
					continue;
				}

				// Check if required functions are valid
				$all_functions_exist = array();
				foreach ( $extension['dependencyFunctions'] as $function ) {
					if ( is_array( $function ) ) {
						$function_is_valid = false;
						foreach ( $function as $function_name ) {
							if ( function_exists( $function_name ) ) {
								$function_is_valid = true;
								break;
							}
						}
					} else {
						$function_is_valid = function_exists( $function );
					}
					$all_functions_exist[] = $function_is_valid;
				}

				// Skip if at least one check is false
				if ( in_array( false, $all_functions_exist, true ) ) {
					continue;
				}
				// Check if required constants are valid
				$all_constants_exist = array();
				foreach ( $extension['dependencyConstants'] as $constant ) {
					if ( is_array( $constant ) ) {
						$constant_is_valid = false;
						foreach ( $constant as $constant_name ) {
							if ( defined( $constant_name ) ) {
								$constant_is_valid = true;
								break;
							}
						}
					} else {
						$constant_is_valid = defined( $constant );
					}
					$all_constants_exist[] = $constant_is_valid;
				}

				// Skip if at least one check is false
				if ( in_array( false, $all_constants_exist, true ) ) {
					continue;
				}
				$out[ $extension_key ] = $extension;
			}
			return $out;
		}

		public function register_toolbar( $editor ) {

			$post_types = $editor->args['enabled_post_types'];
			foreach ( $post_types as $post_type ) {
				// Skip if the current editor doesn't have a license toolbar with fs_id
				// because we need the fs object to get the buyer email
				$license_toolbar = $editor->args['toolbars']->get_item( 'wpse_license', $post_type, 'secondary' );
				if ( empty( $license_toolbar ) || empty( $license_toolbar['fs_id'] ) ) {
					continue;
				}
				$editor->args['toolbars']->register_item(
					'suggested_extensions',
					array(
						'type'                  => 'button',
						'content'               => __( 'Integrations', VGSE()->textname ),
						'extra_html_attributes' => 'data-remodal-target="modal-suggested-extensions"',
						'footer_callback'       => array( $this, 'render_suggested_extensions' ),
						'parent'                => 'extensions',
						'fs_id'                 => $license_toolbar['fs_id'],
					),
					$post_type
				);
			}
		}

		/**
		 * Render filters modal html
		 * @param string $current_post_type
		 */
		public function render_suggested_extensions( $current_post_type ) {
			$license_toolbar = VGSE()->helpers->get_provider_editor( $current_post_type )->args['toolbars']->get_item( 'wpse_license', $current_post_type, 'secondary' );
			$fs_id           = (int) $license_toolbar['fs_id'];
			$fs              = freemius( $fs_id );
			if ( ! $fs ) {
				return;
			}
			$user = $fs->get_user();
			if ( ! $user ) {
				return;
			}
			$email                = $user->email;
			$suggested_extensions = $this->get_suggested_extensions();
			if ( empty( $suggested_extensions ) ) {
				return;
			}
			$extensions_to_update  = wp_list_filter( $suggested_extensions, array( 'requires_update' => true ) );
			$extensions_to_install = wp_list_filter( $suggested_extensions, array( 'requires_update' => false ) );
			?>


			<div class="remodal suggested-extensions-modal" data-remodal-id="modal-suggested-extensions" data-remodal-options="closeOnOutsideClick: false">

				<div class="modal-content">
					<?php if ( ! empty( $extensions_to_install ) ) { ?>
						<h3><?php _e( 'Important extensions', VGSE()->textname ); ?></h3>
						<p><?php _e( 'Some of your plugins require special compatibility, and we have created these extensions that you can download for free if you purchased our plugin.', VGSE()->textname ); ?></p>
						<p><?php _e( 'It\'s important that you install them to prevent errors, but you can do it later if you want. Find this popup in the toolbar > extensions > Integrations.', VGSE()->textname ); ?></p>
						<p><?php _e( 'If you click on the "download" button, we will download the file from the official website: wpsheeteditor.com.', VGSE()->textname ); ?></p>
						<p><?php _e( 'If you don\'t want to install these extensinos, you can go to our advanced settings and enable the option "Disable the popup that asks you to install important extensions?".', VGSE()->textname ); ?></p>

						<?php do_action( 'vg_sheet_editor/suggested_extensions/above_list', $extensions_to_install, $current_post_type ); ?>

						<table>
							<thead>
								<tr>
									<th class="extension-name"><?php _e( 'Extension name', VGSE()->textname ); ?></th>
									<th><?php _e( 'Description', VGSE()->textname ); ?></th>
									<th><?php _e( 'Action', VGSE()->textname ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $extensions_to_install as $extension_key => $extension ) { ?>
									<tr>
										<td class="extension-name"><?php echo esc_html( str_replace( 'WP Sheet Editor - ', '', $extension['name'] ) ); ?></td>
										<td><?php echo esc_html( $extension['description'] ); ?></td>
										<td><button data-success-text="<?php echo esc_attr( __( 'Already downloaded', VGSE()->textname ) ); ?>" data-email="<?php echo esc_attr( $email ); ?>" data-extension-key="<?php echo esc_attr( $extension_key ); ?>" class="download-extension button"><?php _e( 'Download', VGSE()->textname ); ?></button></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>

					<?php } ?>

					<?php if ( ! empty( $extensions_to_update ) ) { ?>
					<h3><?php _e( 'Extensions that require update', VGSE()->textname ); ?></h3>
					<table>
						<thead>
							<tr>
								<th class="extension-name"><?php _e( 'Extension name', VGSE()->textname ); ?></th>
								<th><?php _e( 'Description', VGSE()->textname ); ?></th>
								<th><?php _e( 'Action', VGSE()->textname ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $extensions_to_update as $extension_key => $extension ) { ?>
								<tr>
									<td class="extension-name"><?php echo esc_html( str_replace( 'WP Sheet Editor - ', '', $extension['name'] ) ); ?></td>
									<td><?php echo esc_html( $extension['description'] ); ?></td>
									<td><button data-success-text="<?php echo esc_attr( __( 'Already downloaded', VGSE()->textname ) ); ?>" data-email="<?php echo esc_attr( $email ); ?>" data-extension-key="<?php echo esc_attr( $extension_key ); ?>" class="download-extension button"><?php _e( 'Download update', VGSE()->textname ); ?></button></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<?php } ?>
					<p><?php _e( 'When you finish downloading the plugins, you need to go to wp-admin > plugins > add new and install them.', VGSE()->textname ); ?></p>
					<p><?php _e( 'Do you need help?.', VGSE()->textname ); ?> <a target="_blank" href="<?php echo esc_url( VGSE()->get_support_links( 'contact_us', 'url', 'required-extension' ) ); ?>"><?php _e( 'Contact us', VGSE()->textname ); ?></a></p>

					<?php
					do_action( 'vg_sheet_editor/suggested_extensions/after_list', $current_post_type, $suggested_extensions );
					?>
					<button data-remodal-action="confirm" class="remodal-cancel"><?php _e( 'Close', VGSE()->textname ); ?></button>
				</div>
				<br>
			</div>
			<script>
				jQuery(document).ready(function() {
					var $buttons = jQuery('.suggested-extensions-modal .download-extension');
					$buttons.each(function() {
						var $button = jQuery(this);
						var apiUrl = 'https://wpsheeteditor.com/wp-json/vgfs/v1/downloads/download-file';
						$button.data('original-text', $button.text());
						$button.click(function(e) {
							$button.text('...');
							jQuery.post(apiUrl, {
								email: $button.data('email'),
								file_name: $button.data('extension-key'),
							}, function(response) {
								if (response.success) {
									$button.addClass('disabled').attr('disabled', 'disabled').text($button.data('success-text'));
									window.location.href = response.download_url;
								} else {
									alert(response.message);
								}
							});
						});
					});

					// Auto open the popup after the rows loaded and if other popups aren't opened
					if (!window.location.hash) {
						setTimeout(function() {
							jQuery('[data-remodal-id="modal-suggested-extensions"]').remodal().open();
						}, 5000);
					}
				});
			</script>
			<?php
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new WPSE_Extension_Notifier();
				self::$instance->init();
			}
			return self::$instance;
		}

		public function __set( $name, $value ) {
			$this->$name = $value;
		}

		public function __get( $name ) {
			return $this->$name;
		}
	}
}

if ( ! function_exists( 'WPSE_Extension_Notifier_Obj' ) ) {

	function WPSE_Extension_Notifier_Obj() {
		return WPSE_Extension_Notifier::get_instance();
	}
}

add_action( 'vg_sheet_editor/initialized', 'WPSE_Extension_Notifier_Obj' );
