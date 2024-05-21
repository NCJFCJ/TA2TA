<?php
/**
 * The template for displaying the single sponsor posts
 *
 * @since   1.0.0
 * @package wp_conference_schedule_pro
 */

get_header(); ?>

	<section id="tec-primary" class="tec-content-area">
		<main id="tec-main" class="tec-site-main">

			<?php while ( have_posts() ) : the_post();
				$post_id     = get_the_ID();
				$website_url = get_post_meta( $post_id, 'tec_website_url', true );
				$terms       = get_the_terms( $post_id, 'tec_sponsor_level' );
				if ( ! is_wp_error( $terms ) ) {
					$levels       = wp_list_pluck( $terms, 'name' );
					$levels_label = ' Level Sponsor';
					$levels       = implode( ', ', $levels );
				}
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="entry-content">

						<div class="tec-sponsor-grid">

							<?php if ( has_post_thumbnail() ) {
								the_post_thumbnail( 'full', ['alt' => get_the_title()] );
							} ?>

							<div>
								<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

								<?php if ( $levels ) { ?>
									<p class="tec-sponsor-level"><?php echo esc_html( $levels . $levels_label ); ?></p>
								<?php } ?>

								<?php the_content(); ?>

								<?php if ( $website_url ) { ?>
									<p class="tec-sponsor-website-link"><a target="_blank" href="<?php echo esc_url( $website_url ); ?>">Visit <?php echo get_the_title(); ?> <?php echo esc_html_x( 'Website', 'Sponsor single template website field label.', 'event-schedule-manager' ) ?></a></p>
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