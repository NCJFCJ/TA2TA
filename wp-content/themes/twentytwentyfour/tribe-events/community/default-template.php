<?php
/**
 * Default Events Template placeholder:
 * Used to display community events content within the default events template itself.
 *
 * @link   https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since  4.10.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Allows filtering the classes for the main element.
 *
 * @since 5.8.0
 *
 * @param array<string> $classes An (unindexed) array of classes to apply.
 */
$classes = apply_filters( 'tribe_default_events_template_classes', [ 'tribe-events-pg-template' ] );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div class="wp-site-blocks">
		<header class="wp-block-template-part header-small">
			<?php block_header_area(); ?>
		</header>

			<div>
			<main
				id="tribe-events-pg-template"
				<?php tribe_classes( $classes ); ?>
			>
				<?php
					while ( have_posts() ) {
						the_post();
						the_content();
					}
				?>
			</main> <!-- #tribe-events-pg-template -->

		<footer class="wp-block-template-part footer">
            <?php block_footer_area(); ?>
        </footer>
    </div>
    <?php wp_footer(); ?>
</body>