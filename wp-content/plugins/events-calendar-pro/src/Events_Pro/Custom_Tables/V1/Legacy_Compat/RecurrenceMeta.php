<?php
/**
 * Handle integration with the legacy Tribe__Events__Pro__Recurrence__Meta class.
 *
 * @since   6.3.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Legacy_Compat
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Legacy_Compat;

use TEC\Events\Custom_Tables\V1\Models\Occurrence;
use wpdb;

/**
 * Class RecurrenceMeta
 *
 * @since   6.3.0
 *
 * @package TEC\Events_Pro\Custom_Tables\V1\Legacy_Compat
 */
class RecurrenceMeta {
	/**
	 * Will fetch the occurrence dates for the post specified.
	 *
	 * @since 6.3.0
	 *
	 * @param null|array $occurrences The results if any have been filtered.
	 * @param int        $post_id     The post ID to fetch occurrences for.
	 *
	 * @return string[] The occurrence dates found.
	 */
	public function recurrence_get_start_dates( $occurrences, $post_id ): array {
		/**
		 * @var wpdb $wpdb
		 */
		global $wpdb;

		$occurrences = Occurrence::where( 'post_id', $post_id )
		                         ->join( $wpdb->posts, 'ID', 'post_id' )
		                         ->where_raw( 'post_status NOT IN(%s,%s,%s)', [ 'inherit', 'auto-draft', 'trash' ] )
		                         ->order_by( 'start_date', 'ASC' )
		                         ->get();

		if ( ! is_array( $occurrences ) ) {
			$occurrences = [];
		}

		return wp_list_pluck( $occurrences, 'start_date' );
	}
}
