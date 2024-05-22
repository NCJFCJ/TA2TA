<?php
/**
 * View: Elementor detected notice.
 *
 * @since 6.4.0
 *
 * @var string $title       The notice title.
 * @var string $description The notice description.
 */

?>

<div class="tec-events-pro-admin__elementor-notice">
	<h3 class="tec-events-pro-admin__elementor-notice-title">
		<?php echo esc_html( $title ); ?>
	</h3>
	<div class="tec-events-pro-admin__elementor-notice-description">
		<?php echo wp_kses_post( $description ); ?>
	</div>
</div>
