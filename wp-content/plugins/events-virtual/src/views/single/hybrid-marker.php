<?php
/**
 * Marker for a single hybrid event.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/single/hybrid-marker.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @since 1.4.0
 *
 * @version 1.4.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

use Tribe\Events\Virtual\Event_Meta;

// Don't print anything when not set to show.
if ( ! $event->virtual_show_on_event ) {
	return;
}

// Don't print anything when this event is not hybrid.
if ( Event_Meta::$value_hybrid_event_type !== $event->virtual_event_type ) {
	return;
}

$hybrid_label = tribe_get_hybrid_event_label_singular();

?>
<div class="tribe-events-hybrid-single-marker">
	<em
		class="tribe-events-hybrid-single-marker__icon"
		title="<?php echo esc_attr( $hybrid_label ); ?>"
	>
		<?php $this->template(
			'v2/components/icons/hybrid',
			[
				'classes'    => [ 'tribe-events-hybrid-single-marker__icon-svg' ],
				'icon_label' => $hybrid_label,
			]
		); ?>
	</em>
	<?php echo esc_html( $hybrid_label ); ?>
</div>
