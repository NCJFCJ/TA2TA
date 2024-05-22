<?php
/**
 * View: List View - Single Event Title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/list/event/title.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.0.0
 *
 * @var WP_Post $event The event post object with properties added by the `tribe_get_event` function.
 *
 * @see tribe_get_event() For the format of the event object.
 */
// Get the event ID.
$event_id = get_the_ID();
 
// Fetch from this Event all custom fields and their values.
$fields = tribe_get_custom_fields( $event_id );

?>

<?php if ( ! empty( $fields['Event Webpage URL'] ) ) : ?>	
		<?php echo "Event Webpage: " ?>
		<a
			href="<?php echo esc_url( $fields['Event Webpage URL'] ); ?>"
			title="<?php echo esc_attr( "Event Webpage URL" ); ?>"
			rel="bookmark"
		    target="_blank"
			class=""
		>
			<?php
			// phpcs:ignore
			echo $fields['Event Webpage URL'];
			?>
		</a>  
<?php endif; ?>

