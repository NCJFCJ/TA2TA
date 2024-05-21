<?php
/**
 * The template for displaying the single speaker posts
 *
 * @since   1.0.0
 * @package wp_conference_schedule_pro
 */

get_header(); ?>

	<section id="tec-primary" class="tec-content-area">
		<main id="tec-main" class="tec-site-main">

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post();
				$post_id           = get_the_ID();
				$first_name        = get_post_meta( $post_id, 'tec_first_name', true );
				$last_name         = get_post_meta( $post_id, 'tec_last_name', true );
				$full_name         = $first_name . ' ' . $last_name;
				$title             = get_post_meta( $post_id, 'tec_title', true );
				$organization      = get_post_meta( $post_id, 'tec_organization', true );
				$schedule_page_url = get_option( 'esm_schedule_page_url' );
				$speaker_page_url  = get_option( 'esm_speakers_page_url' );

				function tec_get_social_links() {
					$social_icons = [];
					foreach ( [ 'facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'website' ] as $social_icon ) {
						$url = get_post_meta( get_the_ID(), 'tec_' . $social_icon . '_url', true );
						if ( $url ) {

							$social_label = $social_icon;
							if ( $social_icon == 'website' ) {
								$social_icon = 'admin-site-alt3';
							}
							if ( $social_icon == 'facebook' ) {
								$social_icon = 'facebook-alt';
							}

							$social_icons[] = '<a class="tec-speaker-social-icon-link" href="' . esc_url( $url ) . '" target="_blank" aria-label="' . esc_html( $social_label ) . '"><span class="dashicons dashicons-' . esc_html( $social_icon ) . '"></span></a>';
						}
					}

					return $social_icons;
				}

				$args     = [
					'numberposts' => - 1,
					'post_type'   => 'tec_session',
					'meta_query'  => [ [ 'key' => 'tec_session_speakers', 'value' => $post->ID, 'compare' => 'LIKE' ] ]
				];
				$sessions = get_posts( $args );
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="entry-content">

						<div class="tec-speaker-grid">

							<?php if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'full', [ 'alt' => $full_name ] );
							} ?>

							<div>
								<h1 class="entry-title"><?php echo esc_html( $full_name ); ?></h1>

								<div class="tec-speaker-details">
									<?php if ( $title ) {
										echo '<p class="tec-speaker-title">' . esc_html( $title ) . '</p>';
									} ?>
									<?php if ( $organization ) {
										echo '<p class="tec-speaker-organization">' . esc_html( $organization ) . '</p>';
									} ?>
								</div>

								<?php
								$social_icons = tec_get_social_links();
								if ( $social_icons ) { ?>
									<ul class="tec-speaker-social">
										<?php foreach ( $social_icons as $social_icon ) { ?>
											<li class="tec-speaker-social-icon"><?php echo $social_icon; ?></li>
										<?php } ?>
									</ul>
								<?php } ?>

								<h2><?php echo esc_html_x( 'About', 'Speaker single template prefix text for name.', 'event-schedule-manager' ) ?> <?php echo esc_html( $full_name ); ?></h2>

								<?php the_content(); ?>

								<?php if ( $sessions ) { ?>
									<h2><?php echo esc_html_x( 'Sessions', 'Speaker single template session heading.', 'event-schedule-manager' ) ?></h2>
									<ul>
										<?php foreach ( $sessions as $session ) { ?>
											<li>
												<a href="<?php echo get_the_permalink( $session->ID ); ?>"><?php echo esc_html( $session->post_title ); ?></a>
											</li>
										<?php } ?>
									</ul>
								<?php } ?>

								<?php if ( $speaker_page_url || $schedule_page_url ) { ?>
									<p class="tec-speaker-links">
										<?php if ( $speaker_page_url ) { ?>
											<a class="tec-speaker-link tec-speaker-link-speakers" href="<?php echo esc_url( $speaker_page_url ); ?>"><?php echo esc_html_x( 'Go to Speakers List', 'Speaker single template link to speaker list.', 'event-schedule-manager' ) ?></a>
										<?php } ?>

										<?php if ( $schedule_page_url ) { ?>
											<a class="tec-speaker-link-schedule" href="<?php echo esc_url( get_bloginfo( 'url' ) ); ?>"><?php echo esc_html_x( 'Go to Conference Home', 'Speaker single template link to conference home.', 'event-schedule-manager' ) ?></a>
										<?php } ?>
									</p>
								<?php } ?>

							</div>

						</div>

					</div><!-- .entry-content -->

				</article><!-- #post-${ID} -->

			<?php

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_footer();