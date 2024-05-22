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

$post_slug = str_replace(" ","-", $fields['Organization']);
		
$post_slug = str_replace("'","", $post_slug);
		
$url = "https://$_SERVER[HTTP_HOST]/".$post_slug;


?>

<?php if ( ! empty( $fields['Organization'] ) ) : ?>

	<h4 class="tribe-events-calendar-list__event-title tribe-common-h6 tribe-common-h5--min-medium">
		<?php echo "Hosted by: " ?>
		<a
			href="<?php echo esc_url( $url ); ?>"
			title="<?php echo esc_attr( $fields['Organization'] ); ?>"
			rel="bookmark"
			class="tribe-events-calendar-list__event-link tribe-common-anchor-thin"
		>
			<?php
			// phpcs:ignore
			echo $fields['Organization'];
			?>
		</a>
	</h4>
  
<?php endif; ?>

