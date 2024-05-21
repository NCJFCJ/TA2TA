<?php
$page_obj = get_queried_object();
$is_admin = $page_obj->post_author == get_current_user_id();
$editor_id = (int) $_GET['wpse_frontend_sheet_iframe'];

/* function pm_remove_all_scripts() {
  global $wp_scripts;
  $wp_scripts->queue = array();
  }
  add_action('wp_print_scripts', 'pm_remove_all_scripts', 100); */

function pm_remove_all_styles() {
	global $wp_styles;
	$wp_styles->queue = array();
}

add_filter('show_admin_bar', '__return_false');
add_action('wp_print_styles', 'pm_remove_all_styles', 100);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="stylesheet" id="boostrap-css" href="<?php echo plugins_url('/assets/frontend/css/bootstrap.css', VGSE_FRONTEND_EDITOR_FILE); ?>" type="text/css" media="all">
		<script src="<?php echo includes_url('/js/jquery/jquery.js'); ?>"></script>
	</head> 

	<body <?php
	$classes = array();
	if (!is_user_logged_in()) {
		$classes[] = 'vg-sheet-editor-is-guest';
	} else {
		$user = get_userdata(get_current_user_id());
		$classes = array_merge($classes, $user->roles);
	}
	body_class($classes);
	?>>
		<div id="page" class="site">
			<div class="site-inner">
				<div id="content" class="site-content">


					<div id="primary" class="content-area">
						<main id="main" class="site-main" role="main">
							<?php
// Start the loop.
							while (have_posts()) : the_post();
								?>

								<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
									<div class="entry-content">
										<?php
										echo do_shortcode('[vg_sheet_editor editor_id="' . (int) $editor_id . '"]');
										?>
									</div><!-- .entry-content -->
								</article><!-- #post-## -->
								<?php
// End of the loop.
							endwhile;
							?>

						</main><!-- .site-main -->

					</div><!-- .content-area -->

				</div><!-- .site-content -->
			</div><!-- .site-inner -->
		</div><!-- .site -->

		<?php wp_footer(); ?>
		<script>
			jQuery(document).ready(function () {
				jQuery('.wpse-select-rows-options').val('current_search');
			});
		</script>
	</body>
</html>
