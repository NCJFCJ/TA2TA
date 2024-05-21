<?php
/**
 * Handles the rendering of the metabox.
 *
 * @since 1.0.0
 *
 * @package Tribe\Events\Virtual
 */

namespace Tribe\Events\Virtual;

use Tribe__Context as Context;
use Tribe__Events__Main as Events_Plugin;
use Tribe__Utils__Array as Arr;
use WP_Post;

/**
 * Class Metabox.
 *
 * @since 1.0.0
 *
 * @package Tribe\Events\Virtual
 */
class Metabox {

	/**
	 * ID for the metabox in WP.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $id = 'tribe-events-virtual';

	/**
	 * Action name used for the nonce on saving the metabox.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public static $nonce_action = 'tribe-events-virtual-nonce';

	/**
	 * Stores the template class used.
	 *
	 * @since 1.0.0
	 *
	 * @var Admin_Template
	 */
	protected $template;

	/**
	 * The context the metabox is rendering into.
	 *
	 * @var Context
	 */
	protected $context;

	/**
	 * Metabox constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Admin_Template $template An instance of the plugin template handler.
	 * @param Context|null   $context  The instance of the Context the metabox should use, or `null` to use the global
	 *                                 one.
	 */
	public function __construct( Admin_Template $template, Context $context = null ) {
		$this->context  = null !== $context ? $context : tribe_context();
		$this->template = $template;
	}

	/**
	 * Fetches the Metabox title.
	 * Note: we specifically do NOT use the template-tag functions in the admin views!
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html_x( 'Virtual Event', 'Meta box title for the Virtual Event controls', 'events-virtual' );
	}

	/**
	 * Render the Virtual Events Metabox.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post_id   Which post we are using here.
	 * @param array   $arguments Arguments from the metabox, which we use to determine compatibility usage.
	 *
	 * @return string The metabox HTML.
	 */
	public function render_template( $post_id, array $arguments = [] ) {
		$event = tribe_get_event( $post_id );

		if ( ! $event instanceof WP_Post ) {
			return '';
		}

		$args = array_merge(
			$arguments,
			[
				'metabox' => $this,
				'post'    => $event,
			]
		);

		return $this->template->template( 'virtual-metabox/container', $args, false );
	}

	/**
	 * Prints the rendered the Virtual Events Metabox.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $post_id   Which post we are using here.
	 * @param array   $arguments Arguments from the metabox, which we use to determine compatibility usage.
	 */
	public function print_template( $post_id, array $arguments = [] ) {
		/**
		 * We look inside of the arguments param for a `args` key since that is how WordPress metabox will pass
		 * the values used to register the metabox initially. If that doesn't exist we pass the whole arguments variable.
		 */
		$args = Arr::get( $arguments, 'args', $arguments );

		echo $this->render_template( $post_id, $args ); /* phpcs:ignore */
	}

	/**
	 * Renders, echoing to the page, the API meeting display controls.
	 *
	 * @since 1.9.0
	 *
	 * @param null|\WP_Post|int $post The post object or ID of the event to generate the controls for, or `null` to use
	 *                                the global post object.
	 * @param bool              $echo Whether to echo the template contents to the page (default) or to return it.
	 *
	 * @return string The template contents, if not rendered to the page.
	 */
	public function render_classic_display_controls( $post = null, $echo = true ) {
		return $this->template->template(
			'virtual-metabox/api/display',
			[
				'event'      => $post,
				'metabox_id' => Metabox::$id,
			],
			$echo
		);
	}

	/**
	 * Registers the plugin meta box for Blocks Editor support.
	 *
	 * @since 1.0.0
	 */
	public function register_blocks_editor_legacy() {
		add_meta_box(
			static::$id,
			$this->get_title(),
			[ $this, 'print_template' ],
			Events_Plugin::POSTTYPE,
			'normal',
			'default',
			[
				'block_editor_compatibility' => tribe( 'editor' )->should_load_blocks(),
			]
		);
	}

	/**
	 * Register all the fields in the Rest API for this metabox.
	 *
	 * @since 1.0.0
	 */
	public function register_fields() {
		foreach ( Event_Meta::$virtual_event_keys as $key ) {
			// Register the appropriate data type from our schema definitions, defaulting to 'string'.
			$type = Event_Meta::$meta_field_types[ $key ] ?? 'string';
			switch ( $type ) {
				case 'array':
					register_post_meta(
						'tribe_events',
						$key,
						[
							'show_in_rest'  => [
								'schema' => [
									'items' => [
										'type'              => 'string',
										// The schema type of each item in the array
										'sanitize_callback' => 'sanitize_text_field',
										// Sanitization callback for each item
									]
								],
							],
							'single'        => true,
							'type'          => 'array',
							'auth_callback' => static function () {
								return current_user_can( 'edit_posts' );
							},
						]
					);
					break;
				case 'string':
					register_post_meta(
						'tribe_events',
						$key,
						[
							'show_in_rest'     => true,
							'single'           => true,
							'type'             => 'string',
							'prepare_callback' => static function ( $val ) {
								return is_string( $val ) ? $val : serialize( $val );
							},
							'auth_callback'    => static function () {
								return current_user_can( 'edit_posts' );
							},
						]
					);
					break;
			}

		}
	}

	/**
	 * Saves the metabox, which will be triggered in `save_post`.
	 *
	 * @since 1.0.0
	 *
	 * @param int     $post_id Which post ID we are dealing with when saving.
	 * @param WP_Post $post    WP Post instance we are saving.
	 * @param boolean $update  If we are updating the post or not.
	 */
	public function save( $post_id, $post, $update ) {
		// Skip non-events.
		if ( ! tribe_is_event( $post_id ) ) {
			return;
		}

		// All fields will be stored in the same array for simplicity.
		$data = $this->context->get( 'events_virtual_data', [] );

		// Add nonce for security and authentication.
		$nonce_name = Arr::get( $data, 'virtual-nonce', false );

		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, static::$nonce_action ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_tribe_events', $post_id ) ) {
			return;
		}

		if ( tribe_context()->is( 'bulk_edit' ) ) {
			return;
		}

		if ( tribe_context()->is( 'inline_save' ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$virtual = tribe_is_truthy( Arr::get( $data, 'virtual', false ) );
		if ( $virtual ) {
			$this->update_fields( $post_id, $data );
		} else {
			$this->delete_fields( $post_id, $data );
		}

		/**
		 * Fires after the Metabox saved the data from the current request.
		 *
		 * @since 1.0.0
		 *
		 * @param int $post_id The post ID of the event currently being saved.
		 * @param array<string,mixed> The whole data received by the metabox.
		 */
		do_action( 'tribe_events_virtual_metabox_save', $post_id, $data );
	}

	/**
	 * Update Virtual Event Meta Fields
	 *
	 * @since 1.0.0
	 * @since 1.6.0 - Add video source support.
	 *
	 * @param int   $post_id Which post ID we are dealing with when saving.
	 * @param array $data    An array of meta field values.
	 */
	public function update_fields( $post_id, $data ) {
		update_post_meta( $post_id, Event_Meta::$key_type, Arr::get( $data, 'event-type', false ) );
		update_post_meta( $post_id, Event_Meta::$key_virtual, Arr::get( $data, 'virtual', false ) );
		update_post_meta( $post_id, Event_Meta::$key_video_source, Arr::get( $data, 'video-source', false ) );
		update_post_meta( $post_id, Event_Meta::$key_virtual_url, Arr::get( $data, 'virtual-url', false ) );
		update_post_meta( $post_id, Event_Meta::$key_linked_button_text, Arr::get( $data, 'virtual-button-text', false ) );
		update_post_meta( $post_id, Event_Meta::$key_linked_button, Arr::get( $data, 'linked-button', false ) );
		update_post_meta( $post_id, Event_Meta::$key_show_embed_at, Arr::get( $data, 'show-embed-at', false ) );
		update_post_meta( $post_id, Event_Meta::$key_show_embed_to, Arr::get( $data, 'show-embed-to', false ) );
		update_post_meta( $post_id, Event_Meta::$key_show_on_event, Arr::get( $data, 'show-on-event', false ) );
		update_post_meta( $post_id, Event_Meta::$key_show_on_views, Arr::get( $data, 'show-on-views', false ) );

		/**
		 * Allows extensions and Compatibilities to save their associated meta.
		 *
		 * @since 1.0.4
		 *
		 * @param int   $post_id ID of the post we're saving.
		 * @param array $data The meta data we're trying to save.
		 */
		do_action( 'tribe_events_virtual_update_post_meta', $post_id, $data );

		// These need some logic around them.
		$embed_video       = Arr::get( $data, 'embed-video', false );
		$virtual_url       = Arr::get( $data, 'virtual-url', false );
		$video_source      = Arr::get( $data, 'video-source', '' );
		$autodetect_source = get_post_meta( $post_id, Event_Meta::$key_autodetect_source, true );
		if ( ! $autodetect_source ) {
			$autodetect_source      = Arr::get( $data, 'autodetect-source', '' );
		}

		// If the link is not embeddable, uncheck key_embed_video.
		if (
			Event_Meta::$key_video_source_id !== $video_source ||
			Event_Meta::$key_oembed_source_id !== $autodetect_source ||
			tribe( OEmbed::class )->is_embeddable( $virtual_url )
		) {
			update_post_meta( $post_id, Event_Meta::$key_embed_video, $embed_video );
		} else {
			delete_post_meta( $post_id, Event_Meta::$key_embed_video );
		}

	}

	/**
	 * Delete Virtual Events Meta Fields
	 *
	 * @since 1.0.0
	 *
	 * @param int   $post_id Which post ID we are dealing with when saving.
	 * @param array $data    An array of meta field values.
	 */
	public function delete_fields( $post_id, $data ) {
		foreach ( Event_Meta::$virtual_event_keys as $key ) {
			delete_post_meta(
				$post_id,
				$key
			);
		}
	}
}
