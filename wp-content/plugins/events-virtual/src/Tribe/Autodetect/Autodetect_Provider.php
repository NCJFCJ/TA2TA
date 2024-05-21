<?php
/**
 * Handles the registration of Autodetect provider.
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Autodetect
 */

namespace Tribe\Events\Virtual\Autodetect;

use Tribe\Events\Virtual\Autodetect\Metabox as Autodetect_Metabox;
use Tribe\Events\Virtual\Meetings\Facebook\Page_API as FB_Page_API;
use Tribe\Events\Virtual\Meetings\Facebook\Template_Modifications as FB_Template_Modifications;
use Tribe\Events\Virtual\OEmbed;
use Tribe\Events\Virtual\Meetings\Zoom\Api as Zoom_API;
use Tribe\Events\Virtual\Meetings\Webex\Meetings as Webex_Meetings;
use Tribe\Events\Virtual\Meetings\Google\Meetings as Google_Meet;
use Tribe\Events\Virtual\Meetings\Microsoft\Meetings as Microsoft_Teams;
use Tribe\Events\Virtual\Traits\With_Nonce_Routes;
use TEC\Common\Contracts\Service_Provider;
use WP_Post;

/**
 * Class Autodetect_Provider
 *
 * @since   1.8.0
 *
 * @package Tribe\Events\Virtual\Autodetect
 */
class Autodetect_Provider extends Service_Provider {

	use With_Nonce_Routes;

	/**
	 * Registers the bindings, actions and filters required by the Autodetect provider to work.
	 *
	 * @since 1.10.1 updated route to ignore capabilities.
	 * @since 1.8.0
	 */
	public function register() {
		// Register this providers in the container to allow calls on it, e.g. to check if enabled.
		$this->container->singleton( 'events-virtual.autodetect', static::class );
		$this->container->singleton( static::class, static::class );

		$this->add_actions();
		$this->add_filters();
		$this->hook_templates();

		$this->public_route_by_nonce( $this->admin_routes() );
	}

	/**
	 * Hooks the actions required for the autodetect to work correctly.
	 *
	 * @since 1.8.0
	 */
	protected function add_actions() {
		add_action( 'tec_events_virtual_autodetect_video_preview', [ $this, 'autodetect_facebook_video_preview' ] );
		add_action( 'tec_events_virtual_autodetect_video_preview', [ $this, 'autodetect_oembed_preview' ] );
	}

	/**
	 * Hooks the filters required for the autodetect to work correctly.
	 *
	 * @since 1.8.0
	 */
	protected function add_filters() {
		add_filter( 'tec_events_virtual_autodetect_source', [ $this, 'filter_virtual_autodetect_facebook_video' ], 10, 5 );
		add_filter( 'tec_events_virtual_autodetect_source', [ $this, 'filter_virtual_autodetect_oembed' ], 100, 5 );
		add_filter( 'tec_events_virtual_autodetect_source', [ $this, 'filter_virtual_autodetect_webex' ], 30, 5 );
		add_filter( 'tec_events_virtual_autodetect_source', [ $this, 'filter_virtual_autodetect_google' ], 50, 5 );
		add_filter( 'tec_events_virtual_autodetect_source', [ $this, 'filter_virtual_autodetect_microsoft' ], 50, 5 );
		add_filter( 'tec_events_virtual_autodetect_source', [ $this, 'filter_virtual_autodetect_zoom' ], 10, 5 );
		add_filter( 'tec_events_virtual_video_source_autodetect_field_video', [ $this, 'filter_virtual_autodetect_field_video' ], 10, 5 );
		add_filter( 'tec_events_virtual_video_source_autodetect_field_video-source', [ $this, 'filter_virtual_autodetect_field_source' ], 15, 5 );
	}

	/**
	 * Hooks the template required for the integration to work.
	 *
	 * @since 1.8.0
	 */
	protected function hook_templates() {

		// Metabox.
		add_action(
			'tribe_template_entry_point:events-virtual/admin-views/virtual-metabox/container/video-source:video_sources',
			[ $this, 'render_classic_autodetect_video_source_ui' ],
			10,
			3
		);

	}

	/**
	 * Add a preview video of Facebook video to autodetect.
	 *
	 * @since 1.8.0
	 *
	 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return string The video player html, missing FB app id message, or an empty string.
	 */
	public function autodetect_facebook_video_preview( $event ) {
		return $this->container->make( FB_Template_Modifications::class )->autodetect_facebook_video_preview( $event );
	}

	/**
	 * Add a preview video of Oembed video to autodetect.
	 *
	 * @since 1.8.0
	 *
	 * @param WP_Post $event The event post object, as decorated by the `tribe_get_event` function.
	 *
	 * @return string The video player html, not embedable message, or an empty string.
	 */
	public function autodetect_oembed_preview( $event ) {
		return $this->container->make( OEmbed::class )->autodetect_oembed_preview( $event );
	}

	/**
	 * Filter the autodetect source to detect if a Facebook Video.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_facebook_video( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
		return $this->container->make( FB_Page_API::class )
						->filter_virtual_autodetect_facebook_video( $autodetect, $video_url, $video_source, $event, $ajax_data );
	}

	/**
	 * Filter the autodetect source to detect if a WordPress oembed.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
     *
     * @return array<string|mixed> An array of the autodetect results.
     */
    public function filter_virtual_autodetect_oembed( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
        return $this->container->make( OEmbed::class )
                        ->filter_virtual_autodetect_oembed( $autodetect, $video_url, $video_source, $event, $ajax_data );
    }

	/**
	 * Filter the autodetect source to detect if a Webex link.
	 *
	 * @since 1.9.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_webex( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
		return $this->container->make( Webex_Meetings::class )
		                ->filter_virtual_autodetect_webex( $autodetect, $video_url, $video_source, $event, $ajax_data );
	}

	/**
	 * Filter the autodetect source to detect if a Google Meet link.
	 *
	 * @since 1.11.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_google( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
		return $this->container->make( Google_Meet::class )
		                ->filter_virtual_autodetect_google( $autodetect, $video_url, $video_source, $event, $ajax_data );
	}

	/**
	 * Filter the autodetect source to detect if a Microsoft Team link.
	 *
	 * @since 1.13.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_microsoft( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
		return $this->container->make( Microsoft_Teams::class )
		                ->filter_virtual_autodetect_microsoft( $autodetect, $video_url, $video_source, $event, $ajax_data );
	}

	/**
	 * Filter the autodetect source to detect if a Zoom link.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect defaults.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_zoom( $autodetect, $video_url, $video_source, $event, $ajax_data ) {
		return $this->container->make( Zoom_API::class )
		                ->filter_virtual_autodetect_zoom( $autodetect, $video_url, $video_source, $event, $ajax_data );
	}

	/**
	 * Renders the autodetect video source.
	 *
	 * @since 1.8.0
	 *
	 * @param string           $file        The path to the template file, unused.
	 * @param string           $entry_point The name of the template entry point, unused.
	 * @param \Tribe__Template $template    The current template instance.
	 */
	public function render_classic_autodetect_video_source_ui( $file, $entry_point, \Tribe__Template $template ) {
		$this->container->make( Autodetect_Metabox::class )
		                ->classic_autodetect_video_source_ui( $template->get( 'post' ) );
	}

	/**
	 * Add the video field to the autodetect fields.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect resukts.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_field_video( $autodetect_fields, $video_url, $video_source, $event, $ajax_data ) {
		return $this->container->make( OEmbed::class )
		                ->add_video_url_autodetect_field( $autodetect_fields, $video_url, $video_source, $event, $ajax_data );
	}

	/**
	 * Add the source dropdown field to the autodetect fields.
	 *
	 * @since 1.8.0
	 *
	 * @param array<string|mixed> $autodetect   An array of the autodetect resukts.
	 * @param string              $video_url    The url to use to autodetect the video source.
	 * @param string              $video_source The optional name of the video source to attempt to autodetect.
	 * @param \WP_Post|null       $event        The event post object, as decorated by the `tribe_get_event` function.
	 * @param array<string|mixed> $ajax_data    An array of extra values that were sent by the ajax script.
	 *
	 * @return array<string|mixed> An array of the autodetect results.
	 */
	public function filter_virtual_autodetect_field_source( $autodetect_fields, $video_url, $video_source, $event, $ajax_data ) {
		return $this->container->make( Metabox::class )
		                ->classic_autodetect_video_source_dropdown( $autodetect_fields, $video_url, $video_source, $event, $ajax_data );
	}

	/**
	 * Provides the routes that should be used to handle autodetect requests.
	 *
	 * The map returned by this method will be used by the `Tribe\Events\Virtual\Traits\With_Nonce_Routes` trait.
	 *
	 * @since 1.8.0
	 *
	 * @return array<string,callable> A map from the nonce actions to the corresponding handlers.
	 */
	public function admin_routes() {
		return [
			AJAX::$autodetect_action => $this->container->callback( AJAX::class, 'detect_source' ),
		];
	}

	/**
	 * Get the capability required to use the autodetect integration ajax features.
	 *
	 * @since 1.8.0
	 *
	 * @return string The capability required to use the ajax features, default delete_others_pages.
	 */
	public function get_autodetect_admin_ajax_capability() {
		/**
		 * Allows filtering of the capability required to use the autodetect integration ajax features.
		 *
		 * @since 1.8.0
		 *
		 * @param string $ajax_capability The capability required to use the ajax features, default delete_others_pages.
		 */
		return apply_filters( 'tec_events_virtual_autodetect_admin_ajax_capability', 'delete_others_pages' );
	}
}
