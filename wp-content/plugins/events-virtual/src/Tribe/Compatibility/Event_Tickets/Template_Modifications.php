<?php
/**
 * Handles template modifications.
 *
 * @since 1.0.4
 *
 * @package Tribe\Events\Virtual\Compatibility\Event_Tickets
 */

namespace Tribe\Events\Virtual\Compatibility\Event_Tickets;

use Tribe\Events\Virtual\Admin_Template;
use Tribe\Events\Virtual\Metabox;
use Tribe__Tickets__Tickets as Tickets;

/**
 * Class Template_Modifications.
 *
 * @since 1.0.4
 *
 * @package Tribe\Events\Virtual\Compatibility\Event_Tickets
 */
class Template_Modifications {
	/**
	 * Stores the template class used.
	 *
	 * @since 1.0.4
	 *
	 * @var Template
	 */
	protected $template;

	/**
	 * Template_Modifications constructor.
	 *
	 * @since 1.0.4
	 *
	 * @param Admin_Template $template An instance of the plugin template handler.
	 */
	public function __construct( Admin_Template $template ) {
		$this->template = $template;
	}

	/**
	 * Read-only method to access the protected template property.
	 *
	 * @since 1.0.4
	 *
	 * @return Template
	 */
	public function get_template() {
		return $this->template;
	}

	/**
	 * Render the virtual event share controls.
	 *
	 * @since 1.0.4
	 *
	 * @param WP_Post $event The event post we are editing.
	 * @param boolean $echo  To echo or not to echo, that is the question.
	 * @return string|false  Either the final content HTML or `false` if no template could be found.
	 */
	public function render_share_rsvp_controls( $event = null, $echo = true ) {
		$event = tribe_get_event( $event );

		return $this->template->template(
			'virtual-metabox/container/compatibility/event-tickets/share',
			[
				'post'       => $event,
				'metabox_id' => Metabox::$id,
			],
			$echo
		);
	}

	/**
	 * Render the virtual event share controls.
	 *
	 * @since 1.0.4
	 *
	 * @param WP_Post|null $event The event post we are editing.
	 * @param boolean      $echo  To echo or not to echo, that is the question.
	 * @return string|false       Either the final content HTML or `false` if no template could be found.
	 */
	public function render_share_ticket_controls( $event = null, $echo = true ) {
		$event = tribe_get_event( $event );

		if ( ! $event instanceof \WP_Post ) {
			return false;
		}

		$provider = Tickets::get_event_ticket_provider( $event->ID );
		// For some ET backwards compatibility.
		$provider = is_object( $provider ) ? $provider->class_name : $provider;

		if ( empty( $provider ) ) {
			return false;
		}

		if ( 'Tribe__Tickets__RSVP' === $provider ) {
			return false;
		}

		if ( ! array_key_exists( $provider, Tickets::modules() ) ) {
			return false;
		}

		return $this->template->template(
			'virtual-metabox/container/compatibility/event-tickets/share-tickets',
			[
				'post'       => $event,
				'metabox_id' => Metabox::$id,
			],
			$echo
		);
	}

	/**
	 * Render the virtual event show-to controls.
	 *
	 * @since 1.0.4
	 *
	 * @param WP_Post $event The event post we are editing.
	 * @param string  $html  The initial HTML.
	 * @param boolean $echo  To echo or not to echo, that is the question.
	 * @return string|false  Either the final content HTML or `false` if no template could be found.
	 */
	public function render_show_to_controls( $event = null, $html = null, $echo = true ) {
		$event = tribe_get_event( $event );

		if ( ! $event instanceof \WP_Post ) {
			return $html;
		}

		return $this->template->template(
			'virtual-metabox/container/compatibility/event-tickets/show-to',
			[
				'post'       => $event,
				'metabox_id' => Metabox::$id,
			],
			$echo
		);
	}

	/**
	 * Render the virtual event show-to RSVP attendee controls.
	 *
	 * @since 1.0.4
	 *
	 * @param WP_Post $event The event post we are editing.
	 * @param boolean $echo  To echo or not to echo, that is the question.
	 * @return string|false  Either the final content HTML or `false` if no template could be found.
	 */
	public function render_show_to_rsvp_controls( $event = null, $disabled = false, $echo = true ) {
		$event = tribe_get_event( $event );

		if ( ! $event instanceof \WP_Post ) {
			return false;
		}

		return $this->template->template(
			'virtual-metabox/container/compatibility/event-tickets/show-to-rsvp',
			[
				'disabled'   => $disabled,
				'post'       => $event,
				'metabox_id' => Metabox::$id,
			],
			$echo
		);
	}

	/**
	 * Render the virtual event show-to ticket attendee controls.
	 *
	 * @since 1.0.4
	 *
	 * @param WP_Post $event The event post we are editing.
	 * @param boolean $echo  To echo or not to echo, that is the question.
	 * @return string|false  Either the final content HTML or `false` if no template could be found.
	 */
	public function render_show_to_ticket_controls( $event = null, $disabled = false, $echo = true ) {
		$event = tribe_get_event( $event );

		if ( ! $event instanceof \WP_Post ) {
			return false;
		}

		return $this->template->template(
			'virtual-metabox/container/compatibility/event-tickets/show-to-tickets',
			[
				'disabled'   => $disabled,
				'post'       => $event,
				'metabox_id' => Metabox::$id,
			],
			$echo
		);
	}
}
