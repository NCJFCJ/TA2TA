<?php
$page_obj = get_queried_object();
$is_admin = $page_obj->post_author == get_current_user_id();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php wp_head(); ?>

		<style>
<?php if (!empty(VGSE()->options['frontend_main_color'])) { ?>
				.site-header,
				.site-footer {
					background-color: <?php echo sanitize_text_field(VGSE()->options['frontend_main_color']); ?>;
					margin-bottom: 30px;
				}
<?php } ?>

<?php if (!empty(VGSE()->options['frontend_links_color'])) { ?>
				.site-header a,
				.site-footer a,
				.site-info {
					color: <?php echo sanitize_text_field(VGSE()->options['frontend_links_color']); ?>;
				}
<?php } ?>
		</style>
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
				<header id="masthead" class="site-header" role="banner"> 
					<div class="site-header-main">
						<div class="site-branding">
							<?php
							if (!empty(VGSE()->options['frontend_logo']) && !empty(VGSE()->options['frontend_logo']['url'])) {
								?>
								<img src="<?php echo esc_url(VGSE()->options['frontend_logo']['url']); ?>" height="80" />
								<?php
							} else {

								if ($is_admin) {
									echo '<a href="' . esc_url(admin_url('admin.php?page=vgsefe_welcome_page_options')) . '">Edit logo</a>';
								}
							}
							?>
						</div><!-- .site-branding -->


						<div id="site-header-menu" class="site-header-menu">
							<?php if (!empty(VGSE()->options['frontend_menu'])) { ?>
								<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e('Primary Menu', 'twentysixteen'); ?>">
									<?php
									wp_nav_menu(array(
										'menu' => VGSE()->options['frontend_menu'],
										'menu_class' => 'primary-menu',
									));
									?>
								</nav><!-- .main-navigation -->
								<?php
							} else {


								if ($is_admin) {
									echo '<a href="' . esc_url(admin_url('admin.php?page=vgsefe_welcome_page_options')) . '">Edit menu</a>';
								}
							}
							?>


						</div><!-- .site-header-menu -->
					</div><!-- .site-header-main -->
				</header><!-- .site-header -->

				<div id="content" class="site-content">


					<div id="primary" class="content-area">
						<main id="main" class="site-main" role="main">
							<?php
// Start the loop.
							while (have_posts()) : the_post();
								?>

								<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
									<header class="entry-header">
										<?php the_title('<h1 class="entry-title">', '</h1>'); ?>
									</header><!-- .entry-header -->

									<div class="entry-content">
										<?php
										the_content();
										?>
									</div><!-- .entry-content -->

									<?php
									edit_post_link(
											sprintf(
													/* translators: %s: Name of current post */
													__('Edit<span class="screen-reader-text"> "%s"</span>', 'twentysixteen'), get_the_title()
											), '<footer class="entry-footer"><span class="edit-link">', '</span></footer><!-- .entry-footer -->'
									);
									?>

								</article><!-- #post-## -->
								<?php
// End of the loop.
							endwhile;
							?>

						</main><!-- .site-main -->

					</div><!-- .content-area -->

				</div><!-- .site-content -->

				<footer id="colophon" class="site-footer" role="contentinfo">
					<div class="site-info">
						<span class="site-title">&copy; <?php
							echo (int) date("Y");
							echo " ";
							echo esc_html(bloginfo('name'));
							?></span>
					</div><!-- .site-info -->
				</footer><!-- .site-footer -->
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
