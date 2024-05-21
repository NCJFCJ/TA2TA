<?php
/**
 * Marker for a hybrid event.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/v2/components/hybrid-event.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @since 1.4.0
 *
 * @version 1.4.0
 *
 * @var \WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

use Tribe\Events\Virtual\Event_Meta;

// Don't print anything when this event is not virtual.
if ( ! $event->virtual || ! $event->virtual_show_on_views ) {
	return;
}

// Don't print anything when this event is not hybrid.
if ( Event_Meta::$value_hybrid_event_type !== $event->virtual_event_type ) {
	return;
}

// translators: %s (singular)
$hybrid_label = tribe_get_hybrid_label();
// translators: %s: Event (singular)
$hybrid_event_label = tribe_get_hybrid_event_label_singular();

?>
<div class="tribe-common-b2 tribe-common-b2--bold tribe-events-virtual-hybrid-event">
	<em
		class="tribe-events-virtual-hybrid-event__icon"
		title="<?php echo esc_attr( $hybrid_label ); ?>"
	>
		<?php $this->template(
			'v2/components/icons/hybrid',
			[
				'classes' => [ 'tribe-events-virtual-hybrid-event__icon-svg' ],
				'icon_label' => $hybrid_label
			]
		); ?>
	</em>
	<span class="tribe-events-virtual-virtual-event__text">
		<?php echo esc_html( $hybrid_event_label ); ?>
	</span>
</div>
