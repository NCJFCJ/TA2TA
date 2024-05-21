<?php
/**
 * View: Series events list metabox - Title
 *
 * @since 6.3.0
 *
 * @version 6.3.0
 *
 * @var WP_Post $event The current post object.
 * @var string $start_date The start date for that event.
 * @var boolean $has_recurrence Whether the occurrence is part of a recurrence or not.
 */

if ( ! $event instanceof WP_Post ) {
	return;
}

$recurring_label = __( 'Recurring', 'tribe-events-calendar-pro' );
?>
<div class="tec-events-pro-series__metabox-events-list__title-column">
	<div class="tec-events-pro-series__metabox-events-list__title-column--title">
		<?php
		echo esc_html( _draft_or_post_title( $event ) );
		_post_states( $event );
		?>
	</div>
	<div class="tec-events-pro-series__metabox-events-list__title-column--start-date">
		<?php echo esc_html( $start_date ) ?>
		<?php if ( $has_recurrence ): ?>
			<svg style="margin-left: 10px;" viewBox="0 0 12 12" width="12" height="12">
				<title><?php echo esc_html( $recurring_label ) ?></title>
				<use xlink:href="#recurring" />
			</svg>
		<?php endif; ?>
	</div>
</div>