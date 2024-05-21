<?php
/**
 * Event Schedule Manager Session Meta.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Admin\Meta
 */

namespace TEC\Conference\Admin\Meta;

use TEC\Conference\Admin\WP_Post;
use TEC\Conference\Plugin;
use TEC\Conference\Vendor\StellarWP\Arrays\Arr;

/**
 * Class Session
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Admin\Meta
 */
class Session {

	/**
	 * Saves post session details.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save_post_session( $post_id, $post ) {
		if (
			wp_is_post_revision( $post_id )
			|| $post->post_type != Plugin::SESSION_POSTTYPE
		) {
			return;
		}

		$meta_speak_list_nonce = Arr::get( $_POST, 'tec-meta-speakers-list-nonce' );
		if ( ! empty( $meta_speak_list_nonce ) && wp_verify_nonce( $meta_speak_list_nonce, 'edit-speakers-list' ) && current_user_can( 'edit_post', $post_id ) ) {

			$speakers = sanitize_text_field( Arr::get( $_POST, 'tec-speakers-list' ) );
			update_post_meta( $post_id, '_conference_session_speakers', $speakers );
		}

		$meta_session_nonce = Arr::get( $_POST, 'tec-meta-session-info' );
		if ( ! empty( $meta_session_nonce ) && wp_verify_nonce( $meta_session_nonce, 'edit-session-info' ) ) {

			$session_time = strtotime( sprintf( '%s %d:%02d %s', sanitize_text_field( Arr::get( $_POST, 'tec-session-date' ) ), absint( Arr::get( $_POST, 'tec-session-hour' ) ), absint( Arr::get( $_POST, 'tec-session-minutes' ) ), 'am' == Arr::get( $_POST, 'tec-session-meridiem' ) ? 'am' : 'pm' ) );
			update_post_meta( $post_id, '_tec_session_time', $session_time );

			$session_end_time = strtotime( sprintf( '%s %d:%02d %s', sanitize_text_field( Arr::get( $_POST, 'tec-session-date' ) ), absint( Arr::get( $_POST, 'tec-session-end-hour' ) ), absint( Arr::get( $_POST, 'tec-session-end-minutes' ) ), 'am' == Arr::get( $_POST, 'tec-session-end-meridiem' ) ? 'am' : 'pm' ) );
			update_post_meta( $post_id, '_tec_session_end_time', $session_end_time );

			$session_type = sanitize_text_field( Arr::get( $_POST, 'tec-session-type' ) );
			if ( ! in_array( $session_type, [ 'session', 'custom', 'mainstage' ] ) ) {
				$session_type = 'session';
			}
			update_post_meta( $post_id, '_tec_session_type', $session_type );

			$session_speakers = sanitize_text_field( Arr::get( $_POST, 'tec-session-speakers' ) );
			update_post_meta( $post_id, '_tec_session_speaker_names', $session_speakers );
		}
	}

	/**
	 * Adds the session information meta box.
	 *
	 * @since 1.0.0
	 */
	public function session_metabox() {
		$cmb = new_cmb2_box( [
			'id'           => 'tec_session_metabox',
			'title'        => esc_html_x( 'Session Information', 'Metabox title', 'event-schedule-manager' ),
			'object_types' => [ Plugin::SESSION_POSTTYPE ], // Post type
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left
		] );

		if ( has_filter( 'tec_filter_session_speaker_meta_field' ) ) {
			/**
			 * Filters the speaker meta field in the session information meta box.
			 *
			 * @since 1.0.0
			 *
			 * @param object $cmb CMB2 box object.
			 */
			$cmb = apply_filters( 'tec_filter_session_speaker_meta_field', $cmb );
		} else {
			// Speaker Name(s)
			$cmb->add_field( [
				'name' => esc_html_x( 'Speaker name(s)', 'Metabox field', 'event-schedule-manager' ),
				'id'   => '_tec_session_speaker_names',
				'type' => 'text'
			] );
		}
	}

	/**
	 * Adds meta boxes for the session post type.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'session-info',
			esc_html_x( 'Session Info', 'Session Metabox Name.','event-schedule-manager' ),
			[ $this, 'metabox_session_info' ],
			Plugin::SESSION_POSTTYPE,
			'normal'
		);
	}

	/**
	 * Displays the session information meta box.
	 *
	 * @since 1.0.0
	 */
	public function metabox_session_info() {
		$post             = get_post();
		$session_time     = absint( get_post_meta( $post->ID, '_tec_session_time', true ) );
		$session_date     = ( $session_time ) ? date( 'Y-m-d', $session_time ) : date( 'Y-m-d' );
		$session_hours    = ( $session_time ) ? date( 'g', $session_time ) : '8';
		$session_minutes  = ( $session_time ) ? date( 'i', $session_time ) : '00';
		$session_meridiem = ( $session_time ) ? date( 'a', $session_time ) : 'am';
		$session_type     = get_post_meta( $post->ID, '_tec_session_type', true );

		$session_end_time     = absint( get_post_meta( $post->ID, '_tec_session_end_time', true ) );
		$session_end_hours    = ( $session_end_time ) ? date( 'g', $session_end_time ) : '5';
		$session_end_minutes  = ( $session_end_time ) ? date( 'i', $session_end_time ) : '00';
		$session_end_meridiem = ( $session_end_time ) ? date( 'a', $session_end_time ) : 'pm';
		?>

		<?php wp_nonce_field( 'edit-session-info', 'tec-meta-session-info' ); ?>

		<p>
			<label for="tec-session-date"><?php echo esc_html_x( 'Date:', 'Session date label', 'event-schedule-manager' ); ?></label>
			<input type="text" id="tec-session-date" data-date="<?php echo esc_attr( $session_date ); ?>" name="tec-session-date" value="<?php echo esc_attr( $session_date ); ?>"/><br/>
			<label><?php echo esc_html_x( 'Time:', 'Session time label', 'event-schedule-manager' ); ?></label>

			<select name="tec-session-hour" aria-label="<?php echo esc_html_x( 'Session Start Hour', 'Aria label for session start hour', 'event-schedule-manager' ); ?>">
				<?php for ( $i = 1; $i <= 12; $i ++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_hours ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select> :

			<select name="tec-session-minutes" aria-label="<?php echo esc_html_x( 'Session start minutes', 'Aria label for session start minutes', 'event-schedule-manager' ); ?>">
				<?php for ( $i = '00'; (int) $i <= 55; $i = sprintf( '%02d', (int) $i + 5 ) ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_minutes ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select>

			<select name="tec-session-meridiem" aria-label="<?php echo esc_html_x( 'Session meridiem', 'Aria label for session meridiem', 'event-schedule-manager' ); ?>">
				<option value="am" <?php selected( 'am', $session_meridiem ); ?>>am</option>
				<option value="pm" <?php selected( 'pm', $session_meridiem ); ?>>pm</option>
			</select>
		</p>

		<p>
			<label><?php echo esc_html_x( 'End time:', 'Session end time label', 'event-schedule-manager' ); ?></label>

			<select name="tec-session-end-hour" aria-label="<?php echo esc_html_x( 'Session end hour', 'Aria label for session end hour', 'event-schedule-manager' ); ?>">
				<?php for ( $i = 1; $i <= 12; $i ++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_end_hours ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select> :

			<select name="tec-session-end-minutes" aria-label="<?php echo esc_html_x( 'Session end minutes', 'Aria label for session end minutes', 'event-schedule-manager' ); ?>">
				<?php for ( $i = '00'; (int) $i <= 55; $i = sprintf( '%02d', (int) $i + 5 ) ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_end_minutes ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select>

			<select name="tec-session-end-meridiem" aria-label="<?php echo esc_html_x( 'Session end meridiem', 'Aria label for session end meridiem', 'event-schedule-manager' ); ?>">
				<option value="am" <?php selected( 'am', $session_end_meridiem ); ?>>am</option>
				<option value="pm" <?php selected( 'pm', $session_end_meridiem ); ?>>pm</option>
			</select>
		</p>

		<p>
			<label for="tec-session-type"><?php echo esc_html_x( 'Type:', 'Session type label', 'event-schedule-manager' ); ?></label>
			<select id="tec-session-type" name="tec-session-type">
				<option value="session" <?php selected( $session_type, 'session' ); ?>><?php echo esc_html_x( 'Regular Session', 'Session type', 'event-schedule-manager' ); ?></option>
				<option value="mainstage" <?php selected( $session_type, 'mainstage' ); ?>><?php echo esc_html_x( 'Mainstage', 'Session type', 'event-schedule-manager' ); ?></option>
				<option value="custom" <?php selected( $session_type, 'custom' ); ?>><?php echo esc_html_x( 'Break, Lunch, etc.', 'Session type', 'event-schedule-manager' ); ?></option>
			</select>
		</p>
		<?php
	}
}
