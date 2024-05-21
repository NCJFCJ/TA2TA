<?php

namespace TEC\Events_Community\Callbacks;

use Tribe__Template;

/**
 * Abstract base class for callbacks.
 *
 * @since 4.10.14
 *
 * @package TEC\Events_Community\Callbacks
 */
abstract class Abstract_Callback implements Callback_Interface {

	/**
	 * The template instance.
	 *
	 * @var Tribe__Template|null
	 */
	protected ?Tribe__Template $template = null;

	/**
	 * The base slug for the page.
	 *
	 * @var string
	 */
	protected static string $slug;

	/**
	 * Page arguments.
	 *
	 * @var array
	 */
	protected array $page_args = [];

	/**
	 * The tagline to display on the login form.
	 *
	 * @var string
	 */
	protected string $logout_page_tagline = 'Please log in to use Community Events.';


	/**
	 * Gets the template instance used to setup the rendering html.
	 *
	 * @since 4.10.14
	 *
	 * @return \Tribe__Template The template instance.
	 */
	public function get_template(): \Tribe__Template {
		if ( empty( $this->template ) ) {
			$this->template = new Tribe__Template();
			$this->template->set_template_origin( tribe( 'community.main' ) );
			$this->template->set_template_folder( 'src/views' );
			$this->template->set_template_context_extract( true );
			$this->template->set_template_folder_lookup( true );
		}

		return $this->template;
	}

	/**
	 * @inheritDoc
	 *
	 * @since 4.10.14
	 *
	 * @return string
	 */
	abstract public function callback(): string;

	/**
	 * @inheritDoc
	 *
	 * @since 4.10.14
	 *
	 * @param array $args The page arguments.
	 *
	 * @return void
	 */
	public function setup( array $args ): void {
		$this->page_args = $args;
	}

	/**
	 * Check if the user has access to the event form.
	 *
	 * @since 4.10.14
	 *
	 * @return string|null The access message if user doesn't have access, null otherwise.
	 */
	public function get_access_message(): ?string {
		if ( ! is_user_logged_in() ) {
			return $this->display_login_form();
		}
		return null;
	}

	/**
	 * Displays the login form.
	 *
	 * @since 4.10.14
	 *
	 * @return string The login form HTML.
	 */
	public function display_login_form(): string {

		$this->default_template_compatibility();

		/**
		 * Filters the login page tagline displayed on the Community Events login form.
		 *
		 * @since 4.10.14
		 *
		 * @param string $login_tagline The login page tagline.
		 * @param string $page_slug     The page slug.
		 */
		$login_tagline = apply_filters(
			'tec_events_community_login_page_tagline',
			__( $this->get_logout_page_tagline(), 'tribe-events-community' ),
			$this->get_slug()
		);

		$args = [
			'caption'   => $login_tagline,
			'page_slug' => $this->get_slug(),
		];

		/**
		 * Fires before the login form is displayed for a specific Community Events page.
		 *
		 * The dynamic portion of the hook name, `$this->get_slug()`, refers to the page slug.
		 *
		 * @since 4.10.14
		 */
		do_action( "tec_events_community_{$this->get_slug()}_login_form" );

		/**
		 * Fires before the login form is displayed on any Community Events page.
		 *
		 * @since 4.10.14
		 */
		do_action( 'tec_events_community_login_form' );

		do_action_deprecated( 'tribe_tribe_events_community_event_list_login_form', [], '4.10.14', 'The action tribe_tribe_events_community_event_list_login_form has been renamed to tec_events_community_{$this->get_slug()}_login_form' );

		return $this->display_template( 'community/login-form', $args );

	}

	/**
	 * Returns the page slug.
	 *
	 * @since 4.10.14
	 *
	 * @return string The page slug.
	 */
	public static function get_slug(): string {
		return static::$slug;
	}

	/**
	 * Retrieves the page argument value by name.
	 *
	 * @since 4.10.14
	 *
	 * @param string $argname The argument name.
	 *
	 * @return mixed|null The argument value, or null if not found.
	 */
	public function get_page_args( string $argname ) {
		return $this->page_args[ $argname ] ?? null;
	}

	/**
	 * Returns the logout page tagline.
	 *
	 * @since 4.10.14
	 *
	 * @return string The logout page tagline.
	 */
	public function get_logout_page_tagline(): string {
		return $this->logout_page_tagline;
	}

	/**
	 * Used to enqueue our styles to properly display the CE pages.
	 *
	 * @since 4.10.14
	 *
	 * @return void
	 */
	public function default_template_compatibility(): void {
		tribe_asset_enqueue_group( 'events-styles' );
	}

	/**
	 * Manage additional filters that must run before the callback is setup.
	 *
	 * @since 4.10.14
	 *
	 * @return void
	 */
	public function pre_filters(): void {
		$community_events = tribe( 'community.main' );
		add_filter( 'edit_post_link', [ $community_events, 'removeEditPostLink' ] );
		// Required so that extra spaces aren't added.
		remove_filter( 'the_content', 'wpautop' );
		// Required so characters aren't escaped by accident.
		remove_filter( 'the_content', 'wptexturize' );
	}

	/**
	 * Manage additional filters that must run after the callback is setup.
	 *
	 * @since 4.10.14
	 *
	 * @return void
	 */
	public function post_filters(): void {
		$community_events = tribe( 'community.main' );
		remove_filter( 'edit_post_link', [ $community_events, 'removeEditPostLink' ] );
		// Adding back the wpautop filter.
		add_filter( 'the_content', 'wpautop' );
		// Adding back the wptexturize filter.
		add_filter( 'the_content', 'wptexturize' );
	}

	/**
	 * Displays a template with the given arguments.
	 *
	 * @since 4.10.14
	 *
	 * @param string $template The template file name.
	 * @param array  $args     The template arguments.
	 *
	 * @return string The rendered template HTML.
	 */
	protected function display_template( string $template, array $args ): string {
		$this->pre_filters();

		ob_start();
		echo '<div class="tribe-community-events-content">';
		echo $this->custom_above_content();
		echo $this->get_template()->template( $template, $args, false );
		echo $this->custom_below_content();
		echo '</div>';

		return ob_get_clean();
	}

	/**
	 * Get the custom content to display above the content on each Community Events Page.
	 *
	 * @since 4.10.14
	 *
	 * @return string The custom content.
	 */
	public function custom_above_content(): string {
		$output = '';

		ob_start();
		tribe_events_before_html();
		$output = ob_get_clean();

		/**
		 * Filter the custom content that displays above the content on each Community Events Page.
		 *
		 * @param string $output The content found within Settings -> Display -> Add HTML before event content.
		 * @param string $slug   The slug of the page you are on.
		 */
		$output = apply_filters( 'tec_community_tickets_before_html', $output, $this->get_slug() );

		return $output;
	}

	/**
	 * Get the custom content to display below the content on each Community Events Page.
	 *
	 * @since 4.10.14
	 *
	 * @return string The custom content.
	 */
	public function custom_below_content(): string {
		$output = '';

		ob_start();
		tribe_events_after_html();
		$output = ob_get_clean();

		/**
		 * Filter the custom content that displays above the content on each Community Events Page.
		 *
		 * @param string $output The content found within Settings -> Display -> Add HTML after event content.
		 * @param string $slug   The slug of the page you are on.
		 */
		$output = apply_filters( 'tec_community_tickets_after_html', $output, $this->get_slug() );

		return $output;
	}

}
