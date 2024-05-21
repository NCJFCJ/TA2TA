<?php
/**
 * Handles the creation and updates of Zoom Webinars via the Zoom API.
 *
 * @since   1.1.1
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */

namespace Tribe\Events\Virtual\Meetings\Zoom;

/**
 * Class Meetings
 *
 * @since   1.1.1
 *
 * @package Tribe\Events\Virtual\Meetings\Zoom
 */
class Webinars extends Abstract_Meetings {
	/**
	 * The name of the action used to generate a webinar creation link.
	 *
	 * @since 1.1.1
	 * @deprecated 1.13.0 - Use Actions::$webinar_create_action.
	 *
	 * @var string
	 */
	public static $create_action = 'events-virtual-meetings-zoom-webinar-create';

	/**
	 * The name of the action used to update a webinar.
	 *
	 * @since 1.4.0
	 * @deprecated 1.13.0 - No replacement.
	 *
	 * @var string
	 */
	public static $update_action = 'events-virtual-meetings-zoom-wedbinar-update';

	/**
	 * The name of the action used to remove a webinar link.
	 *
	 * @since 1.1.1
	 * @deprecated 1.13.0 - Use Actions::$webinar_remove_action.
	 *
	 * @var string
	 */
	public static $remove_action = 'events-virtual-meetings-zoom-webinar-remove';

	/**
	 * The type of the meeting handled by the class instance.
	 * Defaults to the Meetings one.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 */
	public static $meeting_type = 'webinar';

	/**
	 * The Zoom API endpoint used to create and manage the meeting.
	 * Defaults to the one used for Meetings.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 */
	public static $api_endpoint = 'webinars';

	/**
	 * The URL that will contain the meeting join instructions.
	 * Defaults to the one used for Meetings.
	 *
	 * @since 1.1.1
	 *
	 * @var string
	 */
	protected static $join_instructions_url = 'https://support.zoom.us/hc/en-us/articles/115004954946-Joining-and-participating-in-a-webinar-attendee-';
}
