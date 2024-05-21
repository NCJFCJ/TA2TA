<?php
/**
 * Community Events: Login Form
 *
 * Override this template in your own theme by creating a file at:
 *     [your-theme]/tribe-events/community/login-form.php
 *
 * See more documentation about our views templating system.
 *
 *
 * @since   4.10.14
 *
 * @version 4.10.14
 *
 * @var $caption    string
 * @var $page_slug    string
 */
?>

<div class="tribe-community-events">

	<?php do_action( 'tec_events_community_{$page_slug}_before_login_form' ); ?>

	<?php do_action( 'tribe_community_before_login_form' ); ?>

	<p><?php echo esc_html( $caption ); ?></p>

	<?php wp_login_form( [ 'form_id' => 'tribe_events_community_login' ] ); ?>

	<?php if ( get_option( 'users_can_register' ) ) : ?>
		<div class="tribe-ce-register">
			<?php wp_register( '<div class="tribe-ce-register">', '</div>', true ); ?>
		</div>
	<?php endif; ?>

	<a class="tribe-ce-lostpassword" href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
		<?php esc_html_e( 'Lost your password?', 'tribe-events-community' ); ?>
	</a>

	<?php do_action( 'tribe_community_after_login_form' ); ?>
</div>