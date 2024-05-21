<?php
/**
 * View: Virtual Events Metabox API display controls.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/virtual-metabox/api/display.php
 *
 * See more documentation about our views templating system.
 *
 * @since 1.9.0
 *
 * @version 1.9.0
 *
 * @link    http://evnt.is/1aiy
 *
 * @var \WP_Post $event      The event post object, as decorated by the `tribe_get_event` function.
 * @var string   $metabox_id The metabox current ID.
 *
 * @see     tribe_get_event() For the format of the event object.
 */

use Tribe\Events\Virtual\Meetings\Google\Event_Meta as Google_Event_Meta;
use Tribe\Events\Virtual\Meetings\Microsoft\Event_Meta as Microsoft_Event_Meta;;
use Tribe\Events\Virtual\Meetings\Webex\Event_Meta as Webex_Event_Meta;
use Tribe\Events\Virtual\Meetings\Zoom\Event_Meta as Zoom_Event_Meta;

$is_api = $event->virtual_meeting &&
			(
				Zoom_Event_Meta::$key_source_id === $event->virtual_video_source
				|| Webex_Event_Meta::$key_source_id === $event->virtual_video_source
				|| Google_Event_Meta::$key_source_id === $event->virtual_video_source
				|| Microsoft_Event_Meta::$key_source_id === $event->virtual_video_source
			);

$classes = [
	'tec-events-virtual-display__list-item',
	'tribe-events-virtual-hidden' => ! $is_api,
];

?>
<li <?php tribe_classes( $classes ); ?>>
	<label for="<?php echo esc_attr( "{$metabox_id}-meetings-api-display-details" ); ?>">
		<input
			id="<?php echo esc_attr( "{$metabox_id}-meetings-api-display-details" ); ?>"
			name="<?php echo esc_attr( "{$metabox_id}[meetings-api-display-details]" ); ?>"
			type="checkbox"
			value="yes"
			<?php checked( tribe_is_truthy( $event->virtual_meeting_display_details ) ); ?>
		/>
		<?php
		echo esc_html_x(
			'Meeting link with details',
			'Option to display Meeting link details in event.',
			'events-virtual'
		);
		?>
	</label>
</li>
