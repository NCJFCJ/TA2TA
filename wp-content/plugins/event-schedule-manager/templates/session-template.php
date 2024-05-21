<?php
/**
 * The template for displaying the single session posts
 *
 * @since   1.0.0
 * @package wp_conference_schedule
 */

use \TEC\Conference\Plugin;

get_header(); ?>

	<section id="tec-primary" class="tec-content-area">
		<main id="tec-main" class="tec-site-main">

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();
				$time_format           = get_option( 'time_format', 'g:i a' );
				$post                  = get_post();
				$session_time          = absint( get_post_meta( $post->ID, '_tec_session_time', true ) );
				$session_end_time      = absint( get_post_meta( $post->ID, '_tec_session_end_time', true ) );
				$session_date          = ( $session_time ) ? date( 'F j, Y', $session_time ) : date( 'F j, Y' );
				$session_type          = get_post_meta( $post->ID, '_tec_session_type', true );
				$session_speakers_text = get_post_meta( $post->ID, '_tec_session_speaker_names', true );
				$session_speakers_html = ( $session_speakers_text ) ? '<div class="tec-single-session-speakers"><h2 class="tec-single-session-heading">' . esc_html_x( 'Speaker', 'Speaker heading title for single sessions.', 'event-schedule-manager' ) . '</h2> <div class="tec-single-session-speakers__names">' . esc_html( $session_speakers_text ) . '</div></div>' : '';
				$session_speakers      = apply_filters( 'tec_filter_single_session_speakers', $session_speakers_html, $post->ID );
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'tec-single-session' ); ?>>
					<header class="tec-entry-header">
						<?php if ( get_option( 'esm_schedule_page_url' ) ) { ?>
							<p>
								<a class="tec-return-link" href="<?php echo esc_url( get_option( 'esm_schedule_page_url' ) ); ?>">
									&laquo; <?php echo esc_html_x( 'Return to schedule', 'Return to schedule page link text on single session template.', 'event-schedule-manager' ); ?>
								</a>
							</p>
						<?php } ?>

						<?php the_title( '<h1 class="tec-single-session__title">', '</h1>' ); ?>

						<?php
						// Check if end time is available. This is for pre version 1.0.1 as the end time wasn't available.
						if ( $session_date && ! $session_end_time ) {
							echo '<h2 class="tec-single-session-time"> ' . esc_html( $session_date ) . ' @ ' . date( $time_format, $session_time ) . '</h2>';
						}

						if ( $session_date && $session_end_time ) {
							echo '<h2 class="tec-single-session-time"> ' . esc_html( $session_date ) . ' @ ' . date( $time_format, $session_time ) . ' - ' . date( $time_format, $session_end_time ) . '</h2>';
						}
						?>

						<div class="tec-single-session-meta">
							<ul class="tec-single-session-taxonomies">
								<?php
								$terms = get_the_terms( get_the_ID(), Plugin::LOCATION_TAXONOMY );
								if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
									$term_names = wp_list_pluck( $terms, 'name' );
									$terms      = implode( ", ", $term_names );
									if ( $terms ) {
										echo '<li class="tec-single-session-taxonomy-term  tec-single-session-location"><span class="tec-tax-icon tec-map-icon"></span>' . esc_html( $terms ) . '</li>';
									}
								}
								?>
							</ul>

							<ul class="tec-single-session-taxonomies">
								<?php
								$terms = get_the_terms( get_the_ID(), Plugin::TRACK_TAXONOMY );
								if ( ! is_wp_error( $terms ) ) {
									$term_names = wp_list_pluck( $terms, 'name' );
									$terms      = implode( ", ", $term_names );
									if ( $terms ) {
										echo '<li class="tec-single-session-taxonomy-term tec-single-session-tracks"><span class="tec-tax-icon tec-location-icon"></span>' . esc_html( $terms ) . '</li>';
									}
								}
								?>
							</ul>
						</div><!-- .meta-info -->
					</header>

					<div class="tec-single-entry-content">
						<h2 class="tec-single-session-heading">
							<?php echo esc_html_x( 'About Session', 'Session content title on single session template.', 'event-schedule-manager' ); ?>
						</h2>
						<?php the_content(); ?>
					</div><!-- .entry-content -->

					<?php
						// Escaped on creation.
						echo $session_speakers;
					?>

					<?php
					$sponsor_list = get_post_meta( $post->ID, 'tec_session_sponsors', true );
					if ( ! empty( $sponsor_list ) ) {
						?>
						<div class="tec-sponsor-single">
							<h2 class="tec-single-session-heading">
								<?php echo esc_html_x( 'Presented by', 'Sponsor title on single session template.', 'event-schedule-manager' ); ?>
							</h2>
							<div class="tec-sponsor-single-row">
								<?php
								$sponsor_url = "";
								$target      = "";
								foreach ( $sponsor_list as $sponsor_li ) {
									$sponsor_img = get_the_post_thumbnail_url( $sponsor_li );
									if ( ! empty( $sponsor_img ) ) {
										$sponsor_url     = get_option( 'tec_field_sponsor_page_url' );
										$tec_website_url = get_post_meta( $sponsor_li, 'tec_website_url', true );

										if ( $sponsor_url == "sponsor_site" ) {
											if ( ! empty( $tec_website_url ) ) {
												$sponsor_url = $tec_website_url;
												$target      = "_blank";
											} else {
												$sponsor_url = "#";
												$target      = "";
											}
										} else {

											$sponsor_url = get_the_permalink( $sponsor_li );
										}
										?>
										<div class="tec-sponsor-single-image">
											<a href="<?php echo esc_url( $sponsor_url ); ?>" target="<?php echo esc_attr( $target ); ?>"><img src="<?php echo get_the_post_thumbnail_url( $sponsor_li ); ?>" alt=""></a>
										</div>
										<?php
									}
								}
								?>
							</div>
						</div>
					<?php } ?>
				</article><!-- #post-${ID} -->

			<?php

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_footer();