<?php
/**
 * Marker for a single virtual event.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events-virtual/single/virtual-marker.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @since 1.1.2
 * @since 1.4.0 - Add check for hybrid event.
 *
 * @version 1.4.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */

use Tribe\Events\Virtual\Event_Meta;

// Don't print anything when this event is not virtual.
if ( ! $event->virtual || ! $event->virtual_show_on_event ) {
	return;
}

// Don't print anything when this event is not virtual.
if ( Event_Meta::$value_virtual_event_type !== $event->virtual_event_type ) {
	return;
}

$virtual_event_label = tribe_get_virtual_event_label_singular();

?>
<div class="tribe-events-virtual-single-marker">
	<em
		class="tribe-events-virtual-single-marker__icon"
		title="<?php echo esc_attr( $virtual_event_label ); ?>"
	>
		<?php $this->template(
			'v2/components/icons/virtual',
			[
				'classes' => [ 'tribe-events-virtual-single-marker__icon-svg' ],
				'icon_title' =>  esc_attr( $virtual_event_label ),
			]
		); ?>
	</em>
	<?php echo esc_html( $virtual_event_label ); ?>
</div>
