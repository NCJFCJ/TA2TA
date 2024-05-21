<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPSE_Logger' ) ) {

	class WPSE_Logger {

		private static $instance = false;
		public $directory        = null;
		public $secret_key       = null;
		public $current_job_id   = null;


		private function __construct() {

		}

		function file_expiration_hours() {
			// Expire in 7 days
			return apply_filters( 'vg_sheet_editor/logs/file_expiration_hours', 24 * 7 );
		}

		function get_site_key() {
			if ( $this->secret_key ) {
				return $this->secret_key;
			}
			if ( ! get_option( 'vgse_secret_key' ) ) {
				update_option( 'vgse_secret_key', md5( VGSE()->helpers->get_uuid() ), false );
			}
			// We use the secret key to add extra security to the file names
			$this->secret_key = get_option( 'vgse_secret_key' );
			return $this->secret_key;

		}

		function maybe_download_log_file() {
			if ( empty( $_GET['wpseelf'] ) || ! VGSE()->helpers->user_can_manage_options() ) {
				return;
			}

			if ( strpos( $_GET['wpseelf'], '.' ) !== false || strpos( $_GET['wpseelf'], '/' ) !== false || strpos( $_GET['wpseelf'], '\\' ) !== false ) {
				die();
			}
			$job_id           = sanitize_file_name( $_GET['wpseelf'] );
			$path             = $this->get_job_file( $job_id );
			$file_name        = current( explode( '.', basename( $path ) ) );
			$public_file_name = str_replace( '-' . $this->get_site_key(), '', $file_name );

			if ( ! file_exists( $path ) ) {
				die( __( 'The log file does not exist.', 'vg_sheet_editor' ) );
			}

			// output headers so that the file is downloaded rather than displayed
			header( 'Content-type: text/plain' );
			header( "Content-disposition: attachment; filename = $public_file_name.txt" );
			VGSE()->helpers->readfile_chunked( $path );
			die();
		}

		function maybe_create_directories() {
			if ( ! is_dir( $this->directory ) ) {
				wp_mkdir_p( $this->directory );
			}
			if ( ! file_exists( $this->directory . '/index.html' ) ) {
				file_put_contents( $this->directory . '/index.html', '' );
			}
			if ( ! file_exists( $this->directory . '/.htaccess' ) ) {
				file_put_contents( $this->directory . '/.htaccess', 'deny from all' );
			}
		}

		function delete_old_files() {
			$files = VGSE()->helpers->get_files_list( $this->directory, '.txt' );
			foreach ( $files as $file ) {
				$expiration_hours = (int) $this->file_expiration_hours();
				if ( file_exists( $file ) && ( time() - filemtime( $file ) > $expiration_hours * 3600 ) ) {
					unlink( $file );
				}
			}
		}

		function get_log_download_url( $job_id ) {
			$out = null;
			if ( ! empty( $job_id ) ) {
				$out = esc_url_raw( add_query_arg( 'wpseelf', sanitize_file_name( $job_id ), admin_url( 'index.php' ) ) );
			}
			return $out;
		}

		function get_job_file( $job_id ) {
			if ( strpos( $job_id, '-' . $this->get_site_key() ) === false ) {
				$job_id .= '-' . $this->get_site_key();
			}
			$file_name = str_replace( array( '.', '/', '\\', ':' ), '', wp_normalize_path( sanitize_file_name( $job_id ) ) );
			$file_path = wp_normalize_path( $this->directory . '/' . $file_name . '.txt' );
			if ( ! file_exists( $file_path ) ) {
				file_put_contents( $file_path, '' );
			}
			return $file_path;
		}

		function set_current_job_id( $job_id ) {
			$this->current_job_id = $job_id;
		}

		function entry( $message, $job_id = null ) {
			if ( ! $job_id && $this->current_job_id ) {
				$job_id = $this->current_job_id;
			}
			if ( ! $job_id ) {
				return false;
			}
			$file_path = $this->get_job_file( $job_id );
			if ( ! file_exists( $file_path ) ) {
				return $this;
			}
			$t     = microtime( true );
			$micro = sprintf( '%06d', ( $t - floor( $t ) ) * 1000000 );

			$time = current_time( 'mysql' ) . '.' . $micro;

			$fp = fopen( $file_path, 'a' ); //opens file in append mode
			fwrite( $fp, $time . ' - ' . html_entity_decode( wp_kses_post( $message ) ) . PHP_EOL . PHP_EOL );
			fclose( $fp );
			return $this;
		}

		function init() {
			$this->directory = apply_filters( 'vg_sheet_editor/logs/directory', WP_CONTENT_DIR . '/uploads/wp-sheet-editor/logs' );
			do_action( 'wpse_delete_old_csvs', array( $this, 'delete_old_files' ) );
			if ( is_admin() ) {
				$this->maybe_create_directories();
				add_action( 'vg_sheet_editor/initialized', array( $this, 'maybe_download_log_file' ) );
				add_action( 'admin_init', array( $this, 'delete_old_files' ) );
				add_action( 'vg_sheet_editor/editor/before_init', array( $this, 'register_toolbar' ), 80 );
			}
			register_shutdown_function( array( $this, 'log_errors' ) );
		}
		function register_toolbar( $editor ) {

			$post_types = $editor->args['enabled_post_types'];
			foreach ( $post_types as $post_type ) {
				$editor->args['toolbars']->register_item(
					'error_log',
					array(
						'type'                  => 'button',
						'allow_in_frontend'     => false,
						'content'               => __( 'Error log', 'vg_sheet_editor' ),
						'toolbar_key'           => 'secondary',
						'extra_html_attributes' => 'data-remodal-target="modal-error-log"',
						'parent'                => 'support',
						'footer_callback'       => array( $this, 'render_error_log_modal' ),
						'required_capability'   => 'manage_options',
					),
					$post_type
				);
			}
		}

		function get_last_n_lines( $job_id, $max_lines = 100 ) {
			$file_path = $this->get_job_file( $job_id );
			$lines     = array();

			if ( file_exists( $file_path ) ) {
				$fp = fopen( $file_path, 'r' );
				while ( ! feof( $fp ) ) {
					$line = fgets( $fp, 4096 );
					array_push( $lines, $line );
					if ( count( $lines ) > $max_lines ) {
						array_shift( $lines );
					}
				}
				fclose( $fp );
			}

			$lines = array_map( 'trim', $lines );
			return $lines;
		}
		function render_error_log_modal() {
			$errors = implode( PHP_EOL, $this->get_last_n_lines( 'error_log', 500 ) );
			?>
			<div data-remodal-id="modal-error-log" data-remodal-options="closeOnOutsideClick: false"
				class="remodal remodal-error-log modal-error-log remodal-extra-large">

				<h2><?php _e( 'Error log', 'vg_sheet_editor' ); ?></h2>
				<?php if ( empty( $errors ) ) { ?>
					<p><?php _e( 'We haven\'t detected any fatal errors yet.', 'vg_sheet_editor' ); ?></p>
				<?php } else { ?>
					<p><?php printf( __( 'Here you can see the fatal errors related to WP Sheet Editor. If you experience any error while using WP Sheet Editor, you can see if any entry appears below with a related date and time, and send the full error message to the WP Sheet Editor support team. This log is reset every %d days.', 'vg_sheet_editor' ), $this->file_expiration_hours() / 24 ); ?></p>
					<pre><?php echo esc_html( sanitize_textarea_field( trim( $errors ) ) ); ?></pre>
				<?php } ?>
				<button data-remodal-action="confirm" class="remodal-cancel"><?php _e( 'Close', 'vg_sheet_editor' ); ?></button>
			</div>
			<?php
		}


		/**
		 * Ensures fatal errors are logged so they can be picked up in the status report.
		 *
		 * @since 2.25.6
		 */
		public function log_errors() {
			$error = error_get_last();
			if ( $error && in_array( $error['type'], array( E_ERROR, E_PARSE, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR ), true ) && preg_match( '/(sheet-editor|bulk-edit-events|bulk-edit-posts-on-frontend|bulk-edit-categories-tags|bulk-edit-user-profiles-in-spreadsheet|woo-coupons-bulk-editor|woo-bulk-edit-products|woo-products-bulk-editor)/', $error['message'] ) ) {
				$this->entry( sprintf( __( '%1$s in %2$s on line %3$s', 'vg_sheet_editor' ), $error['message'], $error['file'], $error['line'] ), 'error_log' );
			}
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new WPSE_Logger();
				self::$instance->init();
			}
			return self::$instance;
		}

		function __set( $name, $value ) {
			$this->$name = $value;
		}

		function __get( $name ) {
			return $this->$name;
		}

	}

}

if ( ! function_exists( 'WPSE_Logger_Obj' ) ) {

	function WPSE_Logger_Obj() {
		return WPSE_Logger::get_instance();
	}
}
WPSE_Logger_Obj();
