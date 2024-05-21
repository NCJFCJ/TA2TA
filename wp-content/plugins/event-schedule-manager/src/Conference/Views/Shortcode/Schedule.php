<?php
/**
 * Handles the schedule shortcode.
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Views\Shortcode
 */

namespace TEC\Conference\Views\Shortcode;

use TEC\Conference\Plugin;
use TEC\Conference\Vendor\StellarWP\Arrays\Arr;
use TEC\Conference\Vendor\StellarWP\Assets\Assets;
use WP_Query;

/**
 * Class Schedule
 *
 * @since   1.0.0
 *
 * @package TEC\Conference\Views\Shortcode
 */
class Schedule {

	/**
	 * Schedule Shortcode ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $shortcode_id = 'tec_schedule';

	/**
	 * Cache Key for Unique Session Dates.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $cache_key = 'tec_conference_unique_session_dates_';

	/**
	 * Add body class if shortcode or block exists.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string> $classes Classes for the body element.
	 *
	 * @return array<string> Modified body classes.
	 */
	public function add_body_class_for_tec_schedule( $body_classes ) {
		if ( ! is_singular() ) {
			return $body_classes;
		}

		global $post;

		// Search for shortcode and block in content.
		$shortcode_found = has_shortcode( $post->post_content, 'tec_schedule' );
		$block_found     = strpos( $post->post_content, '<!-- wp:tec/schedule-block' ) !== false;

		// If found, add a flag to the post object.
		$tec_schedule_exists = false;
		if ( $shortcode_found || $block_found ) {
			$tec_schedule_exists = true;
		}

		if ( $tec_schedule_exists ) {
			$body_classes[] = 'tec-event-schedule-shortcode';
		}

		return $body_classes;
	}

	/**
	 * Schedule Block and Shortcode Dynamic content Output.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string|mixed> $attr Array of attributes from shortcode.
	 *
	 * @return string The HTML output the shortcode.
	 */
	public function render_shortcode( $atts ) {
		Assets::instance()->enqueue_group( 'event-schedule-manager-views' );

		// Prepare the shortcodes arguments
		$attr = shortcode_atts( [
			'date'         => null,
			'tracks'       => 'all',
			'session_link' => 'permalink',
			'color_scheme' => 'light',
			'align'        => '',
			'layout'       => 'table',
			'row_height'   => 'match',
			'content'      => 'none',
		], $atts, 'tec_schedule' );

		$output = '';

		$attr = $this->preprocess_schedule_attributes( $attr );

		$dates_arr = $attr['date'] ?? '';
		$dates     = explode( ',', $dates_arr );

		if ( $dates !== [] ) {
			$current_tab = Arr::get( $_GET, 'tec-tab', null );

			if ( count( $dates ) > 1 ) {

				$classes = [
					'tec-tabs',
					'tec-color-scheme-' . $attr['color_scheme'],
					$attr['align']
				];

				$classes    = implode( ' ', array_filter( $classes ) );
				$output    .= '<div class="' . esc_attr( $classes ) . '">';
				$date_count = count( $dates );

				// Inline style variable is used in grid calcs.
				$output   .= '<div class="tec-tabs-list" style="--tec-tabs-grid-template-columns:' . $date_count . '" role="tablist" aria-label="Event Schedule Manager Tabs">';
				$tab_count = 1;
				foreach ( $dates as $date ) {

					if ( $current_tab ) {
						$tabindex = ( $tab_count == $current_tab ) ? 0 : - 1;
						$selected = ( $tab_count == $current_tab ) ? 'true' : 'false';
					} else {
						$tabindex = ( $tab_count == 1 ) ? 0 : - 1;
						$selected = ( $tab_count == 1 ) ? 'true' : 'false';
					}

					$output .= '<button class="tec-tabs-list-button" role="tab" aria-selected="' . esc_html( $selected ) . '" aria-controls="tec-panel-' . esc_html( $tab_count ) . '" id="tab-' . esc_html(  $tab_count ) . '" data-id="' . esc_html( $tab_count ) . '" tabindex="' . esc_html( $tabindex ) . '">' . date( 'l, F j', strtotime( $date ) ) . '</button>';
					$tab_count ++;
				}
				$output .= '</div>';
			}

			$panel_count = 1;
			foreach ( $dates as $date ) {
				$attr['date'] = trim( $date );

				if ( count( $dates ) > 1 ) {
					if ( $current_tab ) {
						$hidden = ( $panel_count == $current_tab ) ? '' : 'hidden';
					} else {
						$hidden = ( $panel_count == 1 ) ? '' : 'hidden';
					}

					$output .= '<div class="tec-tabs-panel" id="tec-panel-' . esc_html( $panel_count ) . '" role="tabpanel" tabindex="0" aria-labelledby="tab-' . esc_html(  $panel_count ) . '" ' . esc_html( $hidden ) . '>';
					$panel_count ++;
				}


				$tracks                      = $this->get_schedule_tracks( $attr['tracks'] );
				$tracks_explicitly_specified = 'all' !== $attr['tracks'];
				$sessions                    = $this->get_schedule_sessions( $attr['date'], $tracks_explicitly_specified, $tracks );
				$columns                     = $this->get_schedule_columns( $tracks, $sessions, $tracks_explicitly_specified );

				if ( $attr['layout'] == 'table' ) {

					$html = '<div class="tec-schedule-wrapper ' . esc_html( $attr['align'] ) . '">';
					$html .= '<table class="tec-schedule tec-color-scheme-' . esc_html(  $attr['color_scheme'] ) . ' tec-layout-' . esc_html(  $attr['layout'] ) . '" border="0">';
					$html .= '<thead>';
					$html .= '<tr>';

					// Table headings.
					$html .= '<th class="tec-col-time">' . esc_html__( 'Time', 'event-schedule-manager' ) . '</th>';
					foreach ( $columns as $term_id ) {
						$track = get_term( $term_id, 'tec_track' );
						$html  .= sprintf( '<th class="tec-col-track"> <span class="tec-track-name">%s</span> <span class="tec-track-description">%s</span> </th>', isset( $track->term_id ) ? esc_html( $track->name ) : '', isset( $track->term_id ) ? esc_html( $track->description ) : '' );
					}

					$html .= '</tr>';
					$html .= '</thead>';

					$html .= '<tbody>';

					$time_format = get_option( 'time_format', 'g:i a' );

					foreach ( $sessions as $time => $entry ) {

						$skip_next = $colspan = 0;

						$columns_html = '';
						foreach ( $columns as $key => $term_id ) {

							// Allow the below to skip some items if needed.
							if ( $skip_next > 0 ) {
								$skip_next --;
								continue;
							}

							// For empty items print empty cells.
							if ( empty( $entry[ $term_id ] ) ) {
								$columns_html .= '<td class="tec-session-empty"></td>';
								continue;
							}

							// For custom labels print label and continue.
							if ( is_string( $entry[ $term_id ] ) ) {
								$columns_html .= sprintf( '<td colspan="%d" class="tec-session-custom">%s</td>', count( $columns ), esc_html( $entry[ $term_id ] ) );
								break;
							}

							// Gather relevant data about the session
							$colspan              = 1;
							$classes              = [];
							$session              = get_post( $entry[ $term_id ] );
							/**
							 * Filter the session title for the schedule shortcode.
							 *
							 * @since 1.0.0
							 *
							 * @param string $session_title The session title.
							 */
							$session_title        = apply_filters( 'the_title', $session->post_title, $session->ID );
							$session_tracks       = get_the_terms( $session->ID, 'tec_track' );
							$session_track_titles = is_array( $session_tracks ) ? implode( ', ', wp_list_pluck( $session_tracks, 'name' ) ) : '';
							$session_type         = get_post_meta( $session->ID, '_tec_session_type', true );
							/**
							 * Filter the session speakers for the schedule shortcode.
							 *
							 * @since 1.0.0
							 *
							 * @param string $speakers The session speakers.
							 */
							$speakers = apply_filters( 'tec_filter_session_speakers', esc_html( get_post_meta( $session->ID, '_tec_session_speaker_names', true ) ), $session->ID );


							if ( ! in_array( $session_type, [ 'session', 'custom', 'mainstage' ] ) ) {
								$session_type = 'session';
							}

							// Add CSS classes to help with custom styles
							if ( is_array( $session_tracks ) ) {
								foreach ( $session_tracks as $session_track ) {
									$classes[] = 'tec-track-' . $session_track->slug;
								}
							}

							$classes[] = 'tec-session-type-' . $session_type;
							$classes[] = 'tec-session-' . $session->post_name;

							$content = '';
							$content .= '<div class="tec-session-cell-content">';

							/**
							 * Filter the session content header for the schedule shortcode.
							 *
							 * @since 1.0.0
							 *
							 * @param int $session_ID The session ID.
							 */
							$tec_session_content_header = apply_filters( 'tec_session_content_header', $session->ID );
							$content                     .= ( $tec_session_content_header != $session->ID ) ? $tec_session_content_header : '';

							// Determine the session title
							if ( 'permalink' == $attr['session_link'] && ( 'session' == $session_type || 'mainstage' == $session_type ) ) {
								$session_title_html = sprintf( '<h3><a class="tec-session-title" href="%s">%s</a></h3>', esc_url( get_permalink( $session->ID ) ),  $session_title );
							} elseif ( 'anchor' == $attr['session_link'] && ( 'session' == $session_type || 'mainstage' == $session_type ) ) {
								$session_title_html = sprintf( '<h3><a class="tec-session-title" href="%s">%s</a></h3>', esc_url( '#' . get_post_field( 'post_name', $session->ID ) ), $session_title );
							} else {
								$session_title_html = sprintf( '<h3><span class="tec-session-title">%s</span></h3>', $session_title );
							}

							$content .= $session_title_html;

							if ( $attr['content'] == 'full' ) {
								$session_content = get_post_field( 'post_content', $session->ID );
								if ( $session_content ) {
									$content .= '<div class="tec-session-content">' . wp_kses_post( $session_content ) . '</div>';
								}
							} elseif ( $attr['content'] == 'excerpt' ) {
								$session_excerpt = get_the_excerpt( $session->ID );
								if ( $session_excerpt ) {
									$content .= '<div class="tec-session-content">' . esc_html( $session_excerpt ) . '</div>';
								}
							}

							// Add speakers names to the output string.
							if ( $speakers ) {
								$content .= sprintf( ' <div class="tec-session-speakers">%s</div>',  $speakers );
							}

							/**
							 * Filter the session content footer for the schedule shortcode.
							 *
							 * @since 1.0.0
							 *
							 * @param int $session_ID The session ID.
							 */
							$tec_session_content_footer = apply_filters( 'tec_session_content_footer', $session->ID );
							$content                     .= ( $tec_session_content_footer != $session->ID ) ? $tec_session_content_footer : '';

							// End of cell-content.
							$content .= '</div>';

							$columns_clone = $columns;

							// If the next element in the table is the same as the current one, use colspan
							if ( $key != key( array_slice( $columns, - 1, 1, true ) ) ) {
								// while ( $pair = each( $columns_clone ) ) {
								//foreach($columns_clone as $pair) {
								foreach ( $columns_clone as $pair['key'] => $pair['value'] ) {
									if ( $pair['key'] == $key ) {
										continue;
									}

									if ( ! empty( $entry[ $pair['value'] ] ) && $entry[ $pair['value'] ] == $session->ID ) {
										$colspan ++;
										$skip_next ++;
									} else {
										break;
									}
								}
							}

							$columns_html .= sprintf( '<td colspan="%d" class="%s" data-track-title="%s" data-session-id="%s">%s</td>', $colspan, esc_attr( implode( ' ', $classes ) ), esc_html( $session_track_titles ), esc_attr( $session->ID ), $content );
						}

						$global_session      = $colspan === count( $columns ) ? ' tec-global-session tec-global-session-' . esc_html( $session_type ) : '';
						$global_session_slug = $global_session !== '' && $global_session !== '0' ? ' ' . sanitize_html_class( sanitize_title_with_dashes( $session->post_title ) ) : '';

						$html .= sprintf( '<tr class="%s">', sanitize_html_class( 'tec-time-' . date( $time_format, $time ) ) . $global_session . $global_session_slug );
						$html .= sprintf( '<td class="tec-time">%s</td>', str_replace( ' ', '&nbsp;', esc_html( date( $time_format, $time ) ) ) );
						$html .= $columns_html;
						$html .= '</tr>';
					}

					$html .= '</tbody>';
					$html .= '</table>';
					$html .= '</div>';
					if ( count( $dates ) > 1 ) {
						$html .= '</div><!-- tab -->';
					}

					$output .= $html;

				} elseif ( $attr['layout'] == 'grid' ) {

					$html          = '';
					$schedule_date = $attr['date'];
					$time_format   = get_option( 'time_format', 'g:i a' );

					$query_args = [
						'post_type'      => Plugin::SESSION_POSTTYPE,
						'posts_per_page' => - 1,
						'meta_key'       => '_tec_session_time',
						'orderby'        => 'meta_value_num',
						'order'          => 'ASC',
						'meta_query'     => [
							'relation' => 'AND',
							[
								'key'     => '_tec_session_time',
								'compare' => 'EXISTS'
							]
						]
					];
					if ( $schedule_date && strtotime( $schedule_date ) ) {
						$query_args['meta_query'][] = [
							'key'     => '_tec_session_time',
                            'value'   => [
                                strtotime( $schedule_date ),
                                strtotime( $schedule_date . ' +1 day' )
                            ],
                            'compare' => 'BETWEEN',
                            'type'    => 'NUMERIC'
						];
					}
					// If tracks were provided, restrict the lookup in WP_Query.
					if ( $tracks_explicitly_specified && ! empty( $tracks ) ) {
						$query_args['tax_query'][] = [
							'taxonomy' => 'tec_track',
							'field' => 'id',
							'terms' => array_values( wp_list_pluck( $tracks, 'term_id' ) )
						];
					}

					$sessions_query = new WP_Query( $query_args );

					$array_times = [];
					foreach ( $sessions_query->posts as $session ) {
						$time     = absint( get_post_meta( $session->ID, '_tec_session_time', true ) );
						$end_time = absint( get_post_meta( $session->ID, '_tec_session_end_time', true ) );
						$terms    = get_the_terms( $session->ID, 'tec_track' );

						if ( ! in_array( $end_time, $array_times ) ) {
							$array_times[] = $end_time;
						}

						if ( ! in_array( $time, $array_times ) ) {
							$array_times[] = $time;
						}

					}
					asort( $array_times );
					// Reset PHP Array Index
					$array_times = array_values( $array_times );
					// Remove last time item
					unset( $array_times[ count( $array_times ) - 1 ] );

					if ( $attr['row_height'] == 'match' ) {
						$row_height = '1fr';
					} elseif ( $attr['row_height'] == 'auto' ) {
						$row_height = 'auto';
					}

					$html .= '<style>
			@media screen and (min-width:700px) {
				#tec_' . $array_times[0] . '.tec-layout-grid {
					display: grid;
					grid-template-rows:
						[tracks] auto';

					foreach ( $array_times as $array_time ) {
						$html .= '[time-' . $array_time . '] ' . $row_height;
					}

					$html .= ';';

					$html .= 'grid-template-columns: [times] 4em';

					// Reset PHP Array Index
					$tracks = array_values( $tracks );

					$len = count( $tracks );

					// Check the above var dump for issue
					for ( $i = 0; $i < ( $len ); $i ++ ) {
						if ( $i == 0 ) {
							$html .= '[' . $tracks[ $i ]->slug . '-start] 1fr';
						} elseif ( $i == ( $len - 1 ) ) {
							$html .= '[' . $tracks[ ( $i - 1 ) ]->slug . '-end ' . $tracks[ $i ]->slug . '-start] 1fr';
							$html .= '[' . $tracks[ $i ]->slug . '-end];';
						} else {
							$html .= '[' . $tracks[ ( $i - 1 ) ]->slug . '-end ' . $tracks[ $i ]->slug . '-start] 1fr';
						}
					}

					$html .= ';';

					$html .= '
				}
			}
			</style>';

					// Schedule Wrapper
					$html .= '<div id="tec_' . esc_html( $array_times[0] ) . '" class="schedule tec-schedule tec-color-scheme-' . $attr['color_scheme'] . ' tec-layout-' . esc_html( $attr['layout'] ) . ' tec-row-height-' . esc_html( $attr['row_height'] ) . ' ' . esc_html( $attr['align'] ) . '" aria-labelledby="schedule-heading">';

					// Track Titles
					if ( $tracks !== [] ) {
						foreach ( $tracks as $track ) {
							$html .= sprintf( '<span class="tec-col-track" style="grid-column: ' . esc_html( $track->slug ) . '; grid-row: tracks;"> <span class="tec-track-name">%s</span> <span class="tec-track-description">%s</span> </span>', isset( $track->term_id ) ? esc_html( $track->name ) : '', isset( $track->term_id ) ? esc_html( $track->description ) : '' );
						}
					}

					// Time Slots
					if ( $array_times !== [] ) {
						foreach ( $array_times as $array_time ) {
							$html .= '<h2 class="tec-time" style="grid-row: time-' . esc_html( $array_time ) . ';">' . date( $time_format, $array_time ) . '</h2>';
						}
					}

					$sessions_query = new WP_Query( $query_args );

					foreach ( $sessions_query->posts as $session ) {
						$classes              = [];
						$session              = get_post( $session );
						$session_url          = get_the_permalink( $session->ID );
						/**
						 * Filter the session title for the schedule shortcode.
						 *
						 * @since 1.0.0
						 *
						 * @param string $session_title The session title.
						 */
						$session_title        = apply_filters( 'the_title', $session->post_title, $session->ID );
						$session_tracks       = get_the_terms( $session->ID, 'tec_track' );
						$session_track_titles = is_array( $session_tracks ) ? implode( ', ', wp_list_pluck( $session_tracks, 'name' ) ) : '';
						$session_type         = get_post_meta( $session->ID, '_tec_session_type', true );
						/**
						 * Filter the session speakers for the schedule shortcode.
						 *
						 * @since 1.0.0
						 *
						 * @param string $speakers The session speakers.
						 */
						$speakers = apply_filters( 'tec_filter_session_speakers', esc_html( get_post_meta( $session->ID, '_tec_session_speaker_names', true ) ), $session->ID );

						$start_time = get_post_meta( $session->ID, '_tec_session_time', true );
						$end_time   = get_post_meta( $session->ID, '_tec_session_end_time', true );
						$minutes    = ( $end_time - $start_time ) / 60;

						if ( ! in_array( $session_type, [ 'session', 'custom', 'mainstage' ] ) ) {
							$session_type = 'session';
						}

						$tracks_array       = [];
						$tracks_names_array = [];
						if ( $session_tracks ) {
							foreach ( $session_tracks as $session_track ) {

								// Check if the session track is in the main tracks array.
								if ( $track ) {
									$remove_track = false;
									foreach ( $tracks as $track ) {
										if ( $track->slug == $session_track->slug ) {
											$remove_track = true;
										}
									}
								}

								// Don't save session track if track doesn't exist.
								if ( $remove_track == true ) {
									//$tracks_array.array_push($tracks_array, $session_track->slug);
									$tracks_array[] = $session_track->slug;
									//$tracks_names_array.array_push($tracks_names_array, $session_track->name);
									$tracks_names_array[] = $session_track->name;
								}

							}
						}
						$tracks_classes = implode( " ", $tracks_array );

						// Add CSS classes to help with custom styles
						if ( is_array( $session_tracks ) ) {
							foreach ( $session_tracks as $session_track ) {
								$classes[] = 'tec-track-' . $session_track->slug;
							}
						}
						$classes[] = 'tec-session-type-' . $session_type;
						$classes[] = 'tec-session-' . $session->post_name;

						$tracks_array_length = esc_attr( count( $tracks_array ) );

						$grid_column_end = '';
						if ( $tracks_array_length != 1 ) {
							$grid_column_end = ' / ' . $tracks_array[ $tracks_array_length - 1 ];
						}

						$html .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . ' ' . $tracks_classes . '" style="grid-column: ' . esc_html( $tracks_array[0] . $grid_column_end ) . '; grid-row: time-' . esc_html(  $start_time ) . ' / time-' . esc_html( $end_time ) . ';">';

						$html .= '<div class="tec-session-cell-content">';

						/**
						 * Filter the session content header for the schedule shortcode.
						 *
						 * @since 1.0.0
						 *
						 * @param int $session_ID The session ID.
						 */
						$tec_session_content_header = apply_filters( 'tec_session_content_header', $session->ID );
						$html                        .= ( $tec_session_content_header != $session->ID ) ? $tec_session_content_header : '';

						// Determine the session title
						if ( 'permalink' == $attr['session_link'] && ( 'session' == $session_type || 'mainstage' == $session_type ) ) {
							$html .= sprintf( '<h3><a class="tec-session-title" href="%s">%s</a></h3>', esc_url( get_permalink( $session->ID ) ), $session_title );
						} elseif ( 'anchor' == $attr['session_link'] && ( 'session' == $session_type || 'mainstage' == $session_type ) ) {
							$html .= sprintf( '<h3><a class="tec-session-title" href="%s">%s</a></h3>', esc_url( '#' . get_post_field( 'post_name', $session->ID ) ), $session_title );
						} else {
							$html .= sprintf( '<h3><span class="tec-session-title">%s</span></h3>', $session_title );
						}

						// Add time to the output string
						$html .= '<div class="tec-session-time">';
						$html .= date( $time_format, $start_time ) . ' - ' . date( $time_format, $end_time );
						if ( $minutes ) {
							$html .= '<span class="tec-session-time-duration"> (' . esc_html( $minutes ) . ' min)</span>';
						}
						// Close .tec-session-time.
						$html .= '</div>';

						// Add tracks to the output string
						$html .= '<div class="tec-session-track">' . esc_html(  implode( ", ", $tracks_names_array ) ) . '</div>';

						if ( $attr['content'] == 'full' ) {
							$content = get_post_field( 'post_content', $session->ID );
							if ( $content ) {
								$html .= '<div class="tec-session-content">' . wp_kses_post( $content ) . '</div>';
							}
						} elseif ( $attr['content'] == 'excerpt' ) {
							$excerpt = get_the_excerpt( $session->ID );
							if ( $excerpt ) {
								$html .= '<div class="tec-session-content">' . esc_html(  $excerpt ) . '</div>';
							}
						}

						// Add speakers names to the output string.
						if ( $speakers ) {
							// Escaped at the source.
							$html .= sprintf( ' <div class="tec-session-speakers">%s</div>', wp_specialchars_decode( $speakers ) );
						}

						/**
						 * Filter the session content footer for the schedule shortcode.
						 *
						 * @since 1.0.0
						 *
						 * @param int $session_ID The session ID.
						 */
						$tec_session_content_footer = apply_filters( 'tec_session_content_footer', $session->ID );
						$html                        .= ( $tec_session_content_footer != $session->ID ) ? $tec_session_content_footer : '';
						// Close .tec-session-cell-content.
						$html .= '</div>';


						$html .= '</div>';
					}

					$html .= '</div>';

					if ( count( $dates ) > 1 ) {
						$html .= '</div><!-- tab -->';
					}

					$output .= $html;

				}

			}
		}

		if ( count( $dates ) > 1 ) {
			$output .= '</div><!-- tabs -->';
		}

		wp_reset_postdata();

		return $output;
	}

	/**
	 * Return an associative array of term_id -> term object mapping for all selected tracks.
	 *
	 * In case of 'all' is used as a value for $selected_tracks, information for all available tracks
	 * gets returned.
	 *
	 * @param string $selected_tracks Comma-separated list of tracks to display or 'all'.
	 *
	 * @return array Associative array of terms with term_id as the key.
	 */
	public function get_schedule_tracks( $selected_tracks ) {
		$tracks = [];
		if ( 'all' === $selected_tracks ) {
			// Include all tracks.
			$tracks = get_terms( Plugin::TRACK_TAXONOMY );
		} else {
			// Loop through given tracks and look for terms.
			$terms = array_map( 'trim', explode( ',', $selected_tracks ) );

			foreach ( $terms as $term_slug ) {
				$term = get_term_by( 'slug', $term_slug, Plugin::TRACK_TAXONOMY );
				if ( $term ) {
					$tracks[ $term->term_id ] = $term;
				}
			}
		}

		return $tracks;
	}

	/**
	 * Return a time-sorted associative array mapping timestamp -> track_id -> session id.
	 *
	 * @param string $schedule_date               Date for which the sessions should be retrieved.
	 * @param bool   $tracks_explicitly_specified True if tracks were explicitly specified in the shortcode,
	 *                                            false otherwise.
	 * @param array  $tracks                      Array of terms for tracks from tec_get_schedule_tracks().
	 *
	 * @return array Associative array of session ids by time and track.
	 */
	public function get_schedule_sessions( $schedule_date, $tracks_explicitly_specified, $tracks ) {
		$query_args = [
			'post_type'      => Plugin::SESSION_POSTTYPE,
			'posts_per_page' => - 1,
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'     => '_tec_session_time',
					'compare' => 'EXISTS'
				]
			]
		];

		if ( $schedule_date && strtotime( $schedule_date ) ) {
			$query_args['meta_query'][] = [
				'key'     => '_tec_session_time',
                'value'   => [
                    strtotime( $schedule_date ),
                    strtotime( $schedule_date . ' +1 day' )
                ],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC'
			];
		}

		// If tracks were provided, restrict the lookup in WP_Query.
		if ( $tracks_explicitly_specified && ! empty( $tracks ) ) {
			$query_args['tax_query'][] = [
				'taxonomy' => Plugin::TRACK_TAXONOMY,
				'field' => 'id',
				'terms' => array_values( wp_list_pluck( $tracks, 'term_id' ) )
			];
		}

		// Loop through all sessions and assign them into the formatted
		// $sessions array: $sessions[ $time ][ $track ] = $session_id
		// Use 0 as the track ID if no tracks exist.
		$sessions       = [];
		$sessions_query = new WP_Query( $query_args );

		foreach ( $sessions_query->posts as $session ) {
			$time  = absint( get_post_meta( $session->ID, '_tec_session_time', true ) );
			$terms = get_the_terms( $session->ID, Plugin::TRACK_TAXONOMY );

			if ( ! isset( $sessions[ $time ] ) ) {
				$sessions[ $time ] = [];
			}

			if ( empty( $terms ) ) {
				$sessions[ $time ][0] = $session->ID;
			} else {
				foreach ( $terms as $track ) {
					$sessions[ $time ][ $track->term_id ] = $session->ID;
				}
			}
		}

		// Sort all sessions by their key (timestamp).
		ksort( $sessions );

		return $sessions;
	}

	/**
	 * Return an array of columns identified by term ids to be used for schedule table.
	 *
	 * @param array $tracks                      Array of terms for tracks from tec_get_schedule_tracks().
	 * @param array $sessions                    Array of sessions from tec_get_schedule_sessions().
	 * @param bool  $tracks_explicitly_specified True if tracks were explicitly specified in the shortcode,
	 *                                           false otherwise.
	 *
	 * @return array Array of columns identified by term ids.
	 */
	public function get_schedule_columns( $tracks, $sessions, $tracks_explicitly_specified ) {
		$columns = [];

		// Use tracks to form the columns.
		if ( $tracks !== [] ) {
			foreach ( $tracks as $track ) {
				$columns[ $track->term_id ] = $track->term_id;
			}
		} else {
			$columns[0] = 0;
		}

		// Remove empty columns unless tracks have been explicitly specified.
		if ( ! $tracks_explicitly_specified ) {
			$used_terms = [];

			foreach ( $sessions as $time => $entry ) {
				if ( is_array( $entry ) ) {
					foreach ( array_keys( $entry ) as $term_id ) {
						$used_terms[ $term_id ] = $term_id;
					}
				}
			}

			$columns = array_intersect( $columns, $used_terms );
			unset( $used_terms );
		}

		return $columns;
	}

	/**
	 * Update and preprocess input attributes for [schedule] shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attr Array of attributes from shortcode.
	 *
	 * @return array Array of attributes, after preprocessing.
	 */
	public function preprocess_schedule_attributes( $attr ) {
		// Set Attribute values base on props.
		$attr['date'] = $this->fetch_unique_session_dates( $attr );

		if ( $attr['align'] === 'wide' ) {
			$attr['align'] = 'alignwide';
		} elseif ( $attr['align'] === 'full' ) {
			$attr['align'] = 'alignfull';
		}

		foreach ( [ 'tracks', 'session_link', 'color_scheme' ] as $key_for_case_sensitive_value ) {
			$attr[ $key_for_case_sensitive_value ] = strtolower( $attr[ $key_for_case_sensitive_value ] );
		}

		if ( ! in_array( $attr['session_link'], [ 'permalink', 'anchor', 'none' ], true ) ) {
			$attr['session_link'] = 'permalink';
		}

		return $attr;
	}

	/**
	 * Fetch unique session dates based on the provided attributes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attr The attributes for the schedule shortcode.
	 *
	 * @return string The specific date or comma separated list of unique session dates.
	 */
	protected function fetch_unique_session_dates( $attr ) {
		// If there is a value in the date field use it.
		if ( ! empty ( $attr['date'] ) ) {
			return $attr['date'];
		}

		// Generate a cache key based on the attributes
		$cache_key = $this->cache_key . md5( serialize( $attr ) );

		// Try to get the result from cache
		$cached_result = get_transient( $cache_key );

		if ( $cached_result !== false ) {
			return $cached_result;
		}

		$current_timestamp   = time();
		$last_year_timestamp = strtotime( '-6 months', $current_timestamp );
		$next_year_timestamp = strtotime( '+1 year', $current_timestamp );

		$args = [
			'post_type'      => Plugin::SESSION_POSTTYPE,
			'meta_key'       => '_tec_session_time',
			'orderby'        => 'meta_value_num',
			'order'          => 'ASC',
			'posts_per_page' => - 1,
			'meta_query'     => [
				[
					'key'     => '_tec_session_time',
					'value'   => [ $last_year_timestamp, $next_year_timestamp ],
					'compare' => 'BETWEEN',
					'type'    => 'NUMERIC'
				],
			],
			'fields'         => 'ids'
		];

		$query    = new WP_Query( $args );
		$post_ids = $query->posts;
		$dates    = [];
		foreach ( $post_ids as $post_id ) {
			$session_timestamp   = get_post_meta( $post_id, '_tec_session_time', true );
			$date_part           = date( 'Y-m-d', $session_timestamp );
			$dates[ $date_part ] = true;
		}

		// Convert keys to indexed array and sort
		$unique_dates = array_keys( $dates );
		sort( $unique_dates );

		// Convert array to comma-separated string
		$unique_dates_str = implode( ', ', $unique_dates );

		// Cache the result for 6 hours
		set_transient( $cache_key, $unique_dates_str, 6 * HOUR_IN_SECONDS );

		return $unique_dates_str;
	}

	/**
	 * Clear the cache for unique session dates.
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id The post ID.
	 */
	public function clear_session_dates_cache( $post_id ) {
		// If this is a revision, don't clear the cache yet.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		// Only clear the cache if it's a tec_session post type.
		if ( Plugin::SESSION_POSTTYPE !== $post_type ) {
			return;
		}

		global $wpdb;

		// Delete transients that match the pattern
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $wpdb->options
				WHERE `option_name`
				LIKE %s",
				'%_transient_' . $this->cache_key . '%'
			)
		);
	}
}
