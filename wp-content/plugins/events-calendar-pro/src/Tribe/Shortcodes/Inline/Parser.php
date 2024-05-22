<?php
/**
 * Provides a shortcode to place the Details for a Event inline.
 * Parses the shortcode data to place the Details for a Event inline.
 *
 * @since 4.4
 *
 * @see   Tribe__Events__Pro__Shortcodes__Tribe_Inline
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// phpcs:disable PEAR.NamingConventions.ValidClassName.Invalid
// phpcs:disable StellarWP.Classes.ValidClassName.NotSnakeCase

/**
 * Sets the Event Details Shortcode to be able to place the Details for an Event inline
 * Assists the Event Details shortcode in placing the details for an Event inline.
 *
 * @since 4.4
 */
class Tribe__Events__Pro__Shortcodes__Inline__Parser {

	/**
	 * The shortcode output.
	 *
	 * @since 4.4
	 *
	 * @var string
	 */
	protected $output = '';

	/**
	 * Argument placeholders to be parsed.
	 *
	 * @since 4.4
	 *
	 * @var array<string,callable>
	 */
	protected $placeholders = [];

	/**
	 * The shortcode object.
	 *
	 * @since 4.4
	 *
	 * @var Tribe__Events__Pro__Shortcodes__Tribe_Inline
	 */
	protected $shortcode;

	/**
	 * Argument placeholders to be parsed when the Event is private or password-protected.
	 *
	 * @since 6.3.1.1
	 * @since 6.3.3 Renamed to be more clear.
	 *
	 * @var array<string,callable>
	 */
	protected $public_placeholders = [];

	/**
	 * Argument placeholders to be excluded/removed when the Event is private or password-protected.
	 *
	 * @since 6.3.1.1
	 *
	 * @var array<string>
	 */
	protected $excluded_placeholders = [];

	/**
	 * Container for the shortcode attributes.
	 *
	 * @since 4.4
	 *
	 * @var array
	 */
	protected $atts = [];

	/**
	 * The Event ID.
	 *
	 * @since 4.4
	 *
	 * @var int
	 */
	protected $id = 0;

	/**
	 * Holds the Organizer IDs.
	 *
	 * @since 4.4
	 *
	 * @var array
	 */
	protected $organizer_id = [];

	/**
	 * The content for the shortcode.
	 *
	 * @since 4.4
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Constructor.
	 *
	 * @since 4.4
	 *
	 * @since 4.4
	 *
	 * @param Tribe__Events__Pro__Shortcodes__Tribe_Inline $shortcode The shortcode object.
	 */
	public function __construct( Tribe__Events__Pro__Shortcodes__Tribe_Inline $shortcode ) {
		$this->shortcode = $shortcode;
		$this->atts      = $shortcode->atts;
		$this->id        = $this->atts['id'];
		$this->content   = $shortcode->content;

		$this->public_placeholders = [
			'{title}'        => 'get_the_title',
			'{name}'         => 'get_the_title',
			'{title:linked}' => [ $this, 'linked_title' ],
			'{link}'         => 'get_permalink',
			'{url}'          => [ $this, 'url_open' ],
			'{/url}'         => [ $this, 'url_close' ],
			'{start_date}'   => [ $this, 'start_date' ],
			'{start_time}'   => [ $this, 'start_time' ],
			'{end_date}'     => [ $this, 'end_date' ],
			'{end_time}'     => [ $this, 'end_time' ],
		];

		$this->placeholders = [
			'{title}'              => 'get_the_title',
			'{name}'               => 'get_the_title',
			'{title:linked}'       => [ $this, 'linked_title' ],
			'{link}'               => 'get_permalink',
			'{url}'                => [ $this, 'url_open' ],
			'{/url}'               => [ $this, 'url_close' ],
			'{content}'            => [ $this, 'content' ],
			'{content:unfiltered}' => [ $this, 'content_unfiltered' ],
			'{description}'        => [ $this, 'content' ],
			'{excerpt}'            => [ $this, 'tribe_events_get_the_excerpt' ],
			'{thumbnail}'          => [ $this, 'thumbnail' ],
			'{start_date}'         => [ $this, 'start_date' ],
			'{start_time}'         => [ $this, 'start_time' ],
			'{end_date}'           => [ $this, 'end_date' ],
			'{end_time}'           => [ $this, 'end_time' ],
			'{event_website}'      => 'tribe_get_event_website_link',
			'{cost}'               => 'tribe_get_cost',
			'{cost:formatted}'     => [ $this, 'tribe_get_cost' ],
			'{venue}'              => 'tribe_get_venue',
			'{venue:name}'         => 'tribe_get_venue',
			'{venue:linked}'       => [ $this, 'linked_title_venue' ],
			'{venue_address}'      => [ $this, 'venue_address' ],
			'{venue_phone}'        => 'tribe_get_phone',
			'{venue_website}'      => 'tribe_get_venue_website_link',
			'{organizer}'          => [ $this, 'tribe_get_organizer' ],
			'{organizer:linked}'   => [ $this, 'linked_title_organizer' ],
			'{organizer_phone}'    => [ $this, 'tribe_get_organizer_phone' ],
			'{organizer_email}'    => [ $this, 'tribe_get_organizer_email' ],
			'{organizer_website}'  => [ $this, 'tribe_get_organizer_website_link' ],
		];

		$this->process();
		$this->process_multiple_organizers();
	}

	/**
	 * @return array<string,callable>
	 * @deprecated Updated name to placeholder_callbacks().
	 */
	protected function placeholders() {
		_deprecated_function( __METHOD__, '6.3.3', 'Use placeholder_callbacks() instead.' );

		return $this->placeholder_callbacks();
	}

	/**
	 * Placeholders to be parsed.
	 *
	 * @since 4.4
	 * @since 6.3.3 Renamed to more closely match intent.
	 *
	 * @return array<string,callable>
	 */
	protected function placeholder_callbacks() {
		/**
		 * Filter the Placeholders to be parsed in the inline content
		 *
		 * @param array<string,callable> $placeholders
		 */
		$this->placeholders = (array) apply_filters( 'tec_events_pro_inline_placeholders', $this->placeholders );
		$this->placeholders = (array) apply_filters_deprecated( 'tribe_events_pro_inline_placeholders', [ $this->placeholders ], '6.3.3', 'tec_events_pro_inline_placeholders' );

		return $this->placeholders;
	}

	/**
	 * Process the placeholders
	 *
	 * @since 4.4
	 * @since 6.3.1.1 Excludes private and password-protected posts.
	 */
	protected function process() {
		// Prevents unbalanced tags (and thus broken HTML) on final shortcode output.
		$this->content = force_balance_tags( $this->content );

		$placeholders = [];
		if ( ! is_user_logged_in() && 'private' === get_post_status( $this->id ) ) {
			$this->content = sprintf(
				/* translators: %1$s and %2$s are the opening and closing paragraph tags, respectively */
				_x(
					'%1$sYou must log in to access this content.%2$s',
					'Message to display for inline shortcodes when the event is private.',
					'tribe-events-calendar-pro'
				),
				'<p>',
				'</p>'
			);
		} elseif ( ! is_user_logged_in() || current_user_can( 'read_post', $this->id ) ) {
			$placeholders = $this->placeholder_callbacks();
		} else {
			$placeholders = $this->public_placeholder_callbacks();
		}

		/**
		 * Filter Processed Content.
		 * Includes only first Organizer.
		 *
		 * Note this is after the protected/excluded placeholders are processed/removed.
		 *
		 * @param string $html
		 */
		$this->output = apply_filters( 'tec_events_pro_inline_output', $this->parse_content( $placeholders ) );
		$this->output = apply_filters_deprecated( 'tribe_events_pro_inline_output', [ $this->output ], '6.3.3', 'tec_events_pro_inline_output' );
	}

	/**
	 * For the passed placeholders, rendering the content with the appropriate content.
	 *
	 * @since 6.3.3
	 *
	 * @param array<string,callable> $placeholders The list of placeholders we are parsing the content for.
	 *
	 * @return string The content with placeholders rendered.
	 */
	protected function parse_content( array $placeholders ) {
		$this->organizer_id    = tribe_get_organizer_ids( $this->id );
		$content               = $this->content;
		$excluded_placeholders = $this->excluded_placeholders();

		// Iterate on all of them.
		foreach ( $this->placeholder_callbacks() as $tag => $handler ) {
			// Not even there? Skip other steps.
			if ( false === strpos( $this->content, $tag ) ) {
				continue;
			}

			// If it is not one of the placeholders we are parsing for, remove the tag.
			if ( ! isset( $placeholders[ $tag ] ) || in_array( $tag, $excluded_placeholders, true ) ) {
				$content = str_replace( $tag, '', $content );
				continue;
			}

			$id = $this->id;
			// Used to support multiple organizers.
			if ( 'organizer' === substr( $tag, 1, 9 ) ) {
				$id = 0;
			}

			$value   = is_callable( $handler ) ? call_user_func( $handler, $id ) : '';
			$content = str_replace( $tag, $value, $content );
		}

		return $content;
	}

	/**
	 * Process the placeholders.
	 *
	 * @since      6.3.1.1
	 * @deprecated Handling processing in a centralized location.
	 */
	protected function process_placeholders() {
		_deprecated_function( __METHOD__, '6.3.3', 'Use parse_content() instead.' );
	}

	/**
	 * Process the placeholders for private and password-protected events.
	 *
	 * This only processes the placeholders in the $this->public_placeholders array
	 * and it removes the ones in the $this->excluded_placeholders array.
	 *
	 * @since      6.3.1.1
	 * @deprecated Moving processing to a centralized parser.
	 */
	protected function process_protected_placeholders() {
		_deprecated_function( __METHOD__, '6.3.3', 'Use parse_content() instead.' );
	}

	/**
	 * Placeholders to be parsed when the Event is private or password-protected.
	 *
	 * @since 6.3.1.1
	 * @since 6.3.3 Renamed as this takes a different structure to the other placeholder properties.
	 */
	protected function public_placeholder_callbacks() {
		/**
		 * Filter the Protected Placeholders to be parsed in the inline content
		 *
		 * @since 6.3.1.1
		 *
		 * @param array<string,callable> $placeholders
		 */
		$this->public_placeholders = (array) apply_filters( 'tribe_events_pro_inline_public_placeholders', $this->public_placeholders );
		$this->public_placeholders = (array) apply_filters_deprecated( 'tribe_events_pro_inline_protected_placeholders', [ $this->public_placeholders ], '6.3.3', 'tribe_events_pro_inline_public_placeholders' ); // @todo

		return $this->public_placeholders;
	}

	/**
	 * @since      6.3.1.1
	 * @deprecated Moved to protected_placeholder_callbacks().
	 */
	protected function protected_placeholders() {
		_deprecated_function( __METHOD__, '6.3.3', 'Use protected_placeholder_callbacks() instead.' );

		return $this->protected_placeholder_callbacks();
	}

	/**
	 * Placeholders to be removed.
	 *
	 * Generated on the fly to allow for filtering of the original placeholder arrays.
	 *
	 * @since 6.3.1.1
	 * @since 6.3.3 Removed default which is redundant to the already evaluated set of placeholders to use based on event
	 *        status.
	 */
	protected function excluded_placeholders(): array {
		$placeholders = (array) apply_filters_deprecated( 'tribe_events_pro_inline_excluded_placeholders', [ [] ], '6.3.3', 'tec_events_pro_inline_excluded_placeholders' );

		/**
		 * Filter the Protected Placeholder tags to be parsed in the inline content
		 *
		 * @since 6.3.1.1
		 *
		 * @param array<string> $placeholders
		 */
		return (array) apply_filters( 'tec_events_pro_inline_excluded_placeholders', $placeholders );
	}

	/**
	 * Process the Organizers - for multiple Organizers.
	 *
	 * @since 4.4
	 */
	protected function process_multiple_organizers() {

		$multiple = count( $this->organizer_id ) > 1;

		// Only parse again if multiple Organizers connected to the Event.
		if ( $multiple ) {
			preg_match_all( '/{(organizer.*?)(\\d+)}/', $this->content, $match );

			if ( null !== $match && is_array( $match[1] ) ) {
				foreach ( $match[1] as $key => $tag ) {
					if ( ! isset( $match[2][ $key ] ) ) {
						continue;
					}

					$id_array_num = $match[2][ $key ] - 1;
					if ( ! isset( $this->organizer_id[ $id_array_num ] ) ) {
						return false;
					}

					$tag     = '{' . $tag . '}';
					$replace = $match[0][ $key ];
					$handler = $this->placeholders[ $tag ];

					$value         = is_callable( $handler ) ? call_user_func( $handler, $this->organizer_id[ $id_array_num ] ) : '';
					$this->content = str_replace( $replace, $value, $this->content );

				}
			}

			/**
			 * Filter Processed Content After Multiple Organizers
			 *
			 * @param string $html
			 */
			$this->output = apply_filters( 'tec_events_pro_inline_event_multi_organizer_output', $this->content );
			$this->output = apply_filters_deprecated( 'tribe_events_pro_inline_event_multi_organizer_output', [ $this->output ], '6.3.3', 'tec_events_pro_inline_event_multi_organizer_output' );
		}

		return false;
	}

	/**
	 * Linked Event/Post title.
	 *
	 * @since 4.4
	 *
	 * @return string
	 */
	public function linked_title() {
		return '<a href="' . get_permalink( $this->id ) . '">' . get_the_title( $this->id ) . '</a>';
	}

	/**
	 * Opening URL tag.
	 *
	 * @since 4.4
	 *
	 * @return string
	 */
	public function url_open() {
		return '<a href="' . get_permalink( $this->id ) . '">';
	}

	/**
	 * Closing URL tag.
	 *
	 * @since 4.4
	 *
	 * @return string
	 */
	public function url_close() {
		return '</a>';
	}

	/**
	 * Content with applied filters.
	 * This excludes posting portions of private and password protected posts.
	 * But it allows filtering after that decision.
	 *
	 * @since 4.4
	 * @since 6.3.1.1 Now uses content_unfiltered() to get the content.
	 * @since 6.3.1.1 Excludes private and password-protected posts.
	 *
	 * @return string The value of the post field on success, empty string on failure. (filtered)
	 */
	public function content() {

		$content = $this->content_unfiltered();

		return apply_filters( 'the_content', $content );
	}

	/**
	 * Get the unfiltered content.
	 * This excludes posting portions of private and password protected posts.
	 *
	 * @since 4.4
	 * @since 6.3.1.1 Excludes private and password-protected posts.
	 *
	 * @return string
	 */
	public function content_unfiltered() {
		$content = '';

		// If the user can't access the post, we bail.
		if ( ! is_user_logged_in() || ! current_user_can( 'read_post', $this->id ) ) {
			$content = get_post_field( 'post_content', $this->id );
		}

		return $content;
	}

	/**
	 * Get the excerpt using TEC's function.
	 *
	 * @since 4.4
	 *
	 * @return string
	 */
	public function tribe_events_get_the_excerpt() {
		return tribe_events_get_the_excerpt( $this->id, wp_kses_allowed_html( 'post' ) );
	}

	/**
	 * Featured image with no link.
	 *
	 * @since 4.4
	 *
	 * @return string
	 */
	public function thumbnail() {
		return tribe_event_featured_image( $this->id, 'full', false );
	}

	/**
	 * Start date formatted by Events setting.
	 *
	 * @since 4.4
	 *
	 * @return null|string
	 */
	public function start_date() {
		return tribe_get_start_date( $this->id, false );
	}

	/**
	 * Start time if not all day Event.
	 *
	 * @since 4.4
	 *
	 * @return null|string
	 */
	public function start_time() {
		if ( ! tribe_event_is_all_day( $this->id ) ) {
			return tribe_get_start_date( $this->id, false, get_option( 'time_format', Tribe__Date_Utils::TIMEFORMAT ) );
		}

		return false;
	}

	/**
	 * End date formatted by Events setting.
	 *
	 * @since 4.4
	 *
	 * @return null|string
	 */
	public function end_date() {
		return tribe_get_end_date( $this->id, false );
	}

	/**
	 * End time if not all day Event.
	 *
	 * @since 4.4
	 *
	 * @return null|string
	 */
	public function end_time() {
		if ( ! tribe_event_is_all_day( $this->id ) ) {
			return tribe_get_end_date( $this->id, false, get_option( 'time_format', Tribe__Date_Utils::TIMEFORMAT ) );
		}

		return false;
	}

	/**
	 * Event Cost with formatting.
	 *
	 * @since 4.4
	 *
	 * @return string
	 */
	public function tribe_get_cost() {
		return tribe_get_cost( $this->id, true );
	}

	/**
	 * Linked Venue title.
	 *
	 * @since 4.4
	 *
	 * @return bool|string
	 */
	public function linked_title_venue() {

		$venue_id = tribe_get_venue_id( $this->id );

		if ( ! $venue_id ) {
			return false;
		}

		return '<a href="' . get_permalink( $venue_id ) . '">' . get_the_title( $venue_id ) . '</a>';
	}

	/**
	 * Venue address displayed inline.
	 *
	 * @since 4.4
	 *
	 * @return bool|string
	 */
	public function venue_address() {

		$venue_address = [
			'address'       => tribe_get_address( $this->id ),
			'city'          => tribe_get_city( $this->id ),
			'stateprovince' => tribe_get_stateprovince( $this->id ),
			'zip'           => tribe_get_zip( $this->id ),
			'country'       => tribe_get_country( $this->id ),
		];

		// Unset any address with no value for line.
		foreach ( $venue_address as $key => $line ) {
			if ( ! $venue_address[ $key ] ) {
				unset( $venue_address[ $key ] );
			}
		}

		if ( ! empty( $venue_address ) ) {
			return implode( ', ', $venue_address );
		}

		return false;
	}

	/**
	 * Organizer name.
	 *
	 * @since 4.4
	 *
	 * @param int $org_id The Organizer ID.
	 *
	 * @return string
	 */
	public function tribe_get_organizer( $org_id ) {

		if ( 0 === $org_id && isset( $this->organizer_id[ $org_id ] ) ) {
			$org_id = $this->organizer_id[ $org_id ];
		}
		if ( $org_id ) {
			return tribe_get_organizer( $org_id );
		}

		return false;
	}

	/**
	 * Linked Organizer title.
	 *
	 * @since 4.4
	 *
	 * @param int $org_id The Organizer ID.
	 *
	 * @return bool|string
	 */
	public function linked_title_organizer( $org_id ) {

		if ( 0 === $org_id && isset( $this->organizer_id[ $org_id ] ) ) {
			$org_id = $this->organizer_id[ $org_id ];
		}
		if ( $org_id ) {
			return '<a href="' . get_permalink( $org_id ) . '">' . get_the_title( $org_id ) . '</a>';
		}

		return false;
	}

	/**
	 * Get Organizer phone.
	 *
	 * @since 4.4
	 *
	 * @param int $org_id The Organizer ID.
	 *
	 * @return bool|string
	 */
	public function tribe_get_organizer_phone( $org_id ) {

		if ( 0 === $org_id && isset( $this->organizer_id[ $org_id ] ) ) {
			$org_id = $this->organizer_id[ $org_id ];
		}

		if ( $org_id ) {
			return tribe_get_organizer_phone( $org_id );
		}

		return false;
	}

	/**
	 * Get Organizer email.
	 *
	 * @since 4.4
	 *
	 * @param int $org_id The Organizer ID.
	 *
	 * @return bool|string
	 */
	public function tribe_get_organizer_email( $org_id ) {

		if ( 0 === $org_id && isset( $this->organizer_id[ $org_id ] ) ) {
			$org_id = $this->organizer_id[ $org_id ];
		}
		if ( $org_id ) {
			return tribe_get_organizer_email( $org_id );
		}

		return false;
	}

	/**
	 * Get Organizer website Link.
	 *
	 * @since 4.4
	 *
	 * @param int $org_id The Organizer ID.
	 *
	 * @return bool|string
	 */
	public function tribe_get_organizer_website_link( $org_id ) {

		if ( 0 === $org_id && isset( $this->organizer_id[ $org_id ] ) ) {
			$org_id = $this->organizer_id[ $org_id ];
		}
		if ( $org_id ) {
			return tribe_get_organizer_website_link( $org_id );
		}

		return false;
	}

	/**
	 * Returns the output of the parsed content for this shortcode
	 *
	 * @since 4.4
	 *
	 * @return string
	 */
	public function output() {
		return $this->output;
	}
}
