<?php
/**
 * Handles the plugin support and integration with the Custom Tables V1
 * functionality from TEC and ECP.
 *
 * @since   1.12.0
 *
 * @package TEC\Events_Virtual\Custom_Tables\V1
 */

namespace TEC\Events_Virtual\Custom_Tables\V1;

use Exception;
use TEC\Common\Contracts\Service_Provider;
use TEC\Events\Custom_Tables\V1\Provider as TEC_Provider;
use Throwable;

/**
 * Class Provider
 *
 * @since   1.12.0
 *
 * @package TEC\Events_Virtual\Custom_Tables\V1
 */
class Provider extends Service_Provider {

	/**
	 * A flag property indicating whether the Service Provider did register or not.
	 *
	 * @since 1.12.0
	 *
	 * @var bool
	 */
	private $did_register = false;

	/**
	 * Registers the filters and implementations required by the Custom Tables implementation.
	 *
	 * @since 1.12.0
	 */
	public function register() {
		if ( ! ( class_exists( TEC_Provider::class ) && TEC_Provider::is_active() ) ) {
			return false;
		}

		if ( $this->did_register ) {
			// Let's avoid double filtering by making sure we're registering at most once.
			return true;
		}

		$this->did_register = true;

		if ( ! defined( 'TEC_VIRTUAL_CUSTOM_TABLES_V1_ROOT' ) ) {
			define( 'TEC_VIRTUAL_CUSTOM_TABLES_V1_ROOT', __DIR__ );
		}

		if ( ! defined( 'TEC_VIRTUAL_CUSTOM_TABLES_V1_VERSION' ) ) {
			define( 'TEC_VIRTUAL_CUSTOM_TABLES_V1_VERSION', '1.0.5-alpha.29' );
		}

		try {
			// Register this provider to allow getting hold of it from third-party code.
			$this->container->singleton( self::class, self::class );
			$this->container->singleton( 'tec.virtual.custom-tables.v1.provider', self::class );

			$this->container->register( Views\V2\Assets::class );

			return true;
		} catch ( Throwable $t ) {
			// This code will never fire on PHP 5.6, but will do in PHP 7.0+.

			/**
			 * Fires an action when an error or exception happens in the
			 * context of Custom Tables v1 implementation AND the server
			 * runs PHP 7.0+.
			 *
			 * @since 1.12.0
			 *
			 * @param Throwable $t The thrown error.
			 */
			do_action( 'tec_custom_tables_v1_error', $t );
		} catch ( Exception $e ) {
			// PHP 5.6 compatible code.

			/**
			 * Fires an action when an error or exception happens in the
			 * context of Custom Tables v1 implementation AND the server
			 * runs PHP 5.6.
			 *
			 * @since 1.12.0
			 *
			 * @param Exception $e The thrown error.
			 */
			do_action( 'tec_custom_tables_v1_error', $e );
		}
	}
}
