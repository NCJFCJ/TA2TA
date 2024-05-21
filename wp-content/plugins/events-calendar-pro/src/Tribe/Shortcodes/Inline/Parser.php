<?php
/**
 * Provides a shortcode to place the Details for a Event inline.
 * Parses the shortcode data to place the Details for a Event inline.
 *
 * @since 4.4
 *
 * @see Tribe__Events__Pro__Shortcodes__Tribe_Inline
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

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
	 * @var array
	 */
	protected $placeholders = array();

	/**
	 * Argument placeholders to be parsed when the Event is private or password-protected.
	 *
	 * @since 6.3.1.1
	 *
	 * @var array
	 */
	protected $protected_placeholders = [];

	/**
	 * Argument placeholders to be excluded/removed when the Event is private or password-protected.
	 *
	 * @since 6.3.1.1
	 *
	 * @var array
	 */
	protected $excluded_placeholders = [];

	/**
	 * Container for the shortcode attributes.
	 *
	 * @since 4.4
	 *
	 * @var array
	 */
	protected $atts = array();

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
	protected $organizer_id = array();

	/**
	 * The content for the shortcode.
	 *
	 * @since 4.4
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * The shortcode instance.
	 *
	 * @since 4.4
	 *
	 * @var Tribe__Events__Pro__Shortcodes__Tribe_Inline
	 */
	public $shortcode;

	/**
	 * Constructor.
	 *
	 * @since 4.4
	 *
	 * @param Tribe__Events__Pro__Shortcodes__Tribe_Inline $shortcode
	 */
	public function __construct( Tribe__Events__Pro__Shortcodes__Tribe_Inline $shortcode ) {

		$this->shortcode = $shortcode;
		$this->atts      = $shortcode->atts;
		$this->id        = $this->atts['id'];
		$this->content   = $shortcode->content;

		/**
		 * Filter the Placeholders to be parsed in the inline content
		 *
		 * @param array $placeholders
		 */
		$this->placeholders           = apply_filters( 'tribe_events_pro_inline_placeholders', $this->placeholders() );
		$this->protected_placeholders = apply_filters( 'tribe_events_pro_inline_protected_placeholders', $this->protected_placeholders() );
		$this->excluded_placeholders  = apply_filters( 'tribe_events_pro_inline_excluded_placeholders', $this->excluded_placeholders() );

		$this->process();

		$this->process_multiple_organizers();

	}

	/**
	 * Placeholders to be parsed.
	 *
	 * @since 4.4
	 *
	 * @return array
	 */
	protected function placeholders() {
		return array(
			'{title}'              => 'get_the_title',
			'{name}'               => 'get_the_title',
			'{title:linked}'       => array( $this, 'linked_title' ),
			'{link}'               => 'get_permalink',
			'{url}'                => array( $this, 'url_open' ),
			'{/url}'               => array( $this, 'url_close' ),
			'{content}'            => array( $this, 'content' ),
			'{content:unfiltered}' => array( $this, 'content_unfiltered' ),
			'{description}'        => array( $this, 'content' ),
			'{excerpt}'            => array( $this, 'tribe_events_get_the_excerpt' ),
			'{thumbnail}'          => array( $this, 'thumbnail' ),
			'{start_date}'         => array( $this, 'start_date' ),
			'{start_time}'         => array( $this, 'start_time' ),
			'{end_date}'           => array( $this, 'end_date' ),
			'{end_time}'           => array( $this, 'end_time' ),
			'{event_website}'      => 'tribe_get_event_website_link',
			'{cost}'               => 'tribe_get_cost',
			'{cost:formatted}'     => array( $this, 'tribe_get_cost' ),
			'{venue}'              => 'tribe_get_venue',
			'{venue:name}'         => 'tribe_get_venue',
			'{venue:linked}'       => array( $this, 'linked_title_venue' ),
			'{venue_address}'      => array( $this, 'venue_address' ),
			'{venue_phone}'        => 'tribe_get_phone',
			'{venue_website}'      => 'tribe_get_venue_website_link',
			'{organizer}'          => array( $this, 'tribe_get_organizer' ),
			'{organizer:linked}'   => array( $this, 'linked_title_organizer' ),
			'{organizer_phone}'    => array( $this, 'tribe_get_organizer_phone' ),
			'{organizer_email}'    => array( $this, 'tribe_get_organizer_email' ),
			'{organizer_website}'  => array( $this, 'tribe_get_organizer_website_link' ),
		);
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

		/*if ( current_user_can( 'read', $this->id ) ) {
			$this->process_placeholders();
		} else {
			$this->process_protected_placeholders();
		}*/
		if ( current_user_can( 'read', $this->id ) ) {
			$this->process_placeholders();
		} elseif ( 'private' === get_post_status ( $this->id ) ) {
			$this->content = sprintf(
				/* translators: %1$s and %2$s are the opening and closing paragraph tags, respectively */
				__( '%1$sYou must log in to access this content.%2$s', 'tribe-events-calendar-pro' ),
				'<p>',
				'</p>'
			);
		} else {
			$this->process_protected_placeholders();
		}

		/**
		 * Filter Processed Content.
		 * Includes only first Organizer.
		 *
		 * Note this is after the protected/excluded placeholders are processed/removed.
		 *
		 * @param string $html
		 */
		$this->output = apply_filters( 'tribe_events_pro_inline_output', $this->content );
	}

	/**
	 * Process the placeholders.
	 *
	 * @since 6.3.1.1
	 */
	protected function process_placeholders() {
		$this->organizer_id = tribe_get_organizer_ids( $this->id );

		foreach ( $this->placeholders as $tag => $handler ) {
			if ( false === strpos( $this->content, $tag ) ) {
				continue;
			}

			$id = $this->id;
			//Used to support multiple organizers
			if ( 'organizer' === substr( $tag, 1, 9 ) ) {
				$id = 0;
			}

			$value         = is_callable( $handler ) ? call_user_func( $handler, $id ) : '';
			$this->content = str_replace( $tag, $value, $this->content );
		}
	}

	/**
	 * Process the placeholders for private and password-protected events.
	 *
	 * This only processes the placeholders in the $this->protected_placeholders array
	 * and it removes the ones in the $this->excluded_placeholders array.
	 *
	 * @since 6.3.1.1
	 */
	protected function process_protected_placeholders() {
		foreach ( $this->protected_placeholders as $tag => $handler ) {
			if ( false === strpos( $this->content, $tag ) ) {
				continue;
			}

			$id = $this->id;
			// Used to support multiple organizers.
			if ( 'organizer' === substr( $tag, 1, 9 ) ) {
				$id = 0;
			}

			if ( in_array( $tag, $this->excluded_placeholders, true ) ) {
				// Remove excluded placeholders.
				$value = '';
			} elseif ( is_callable( $handler ) ) {
				// Process the placeholder.
				$value = call_user_func( $handler, $id );
			} else {
				// Remove invalid placeholders.
				$value = '';
			}

			$this->content = str_replace( $tag, $value, $this->content );
		}
	}

	/**
	 * Placeholders to be parsed when the Event is private or password-protected.
	 *
	 * @since 6.3.1.1
	 */
	protected function protected_placeholders() {
		return [
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
	}

	/**
	 * Placeholders to be removed when the Event is private or password-protected.
	 *
	 * Generated on the fly to allow for filtering of the original placeholder arrays.
	 *
	 * @since 6.3.1.1
	 */
	protected function excluded_placeholders() {
		return array_keys( array_diff_key( $this->placeholders(), $this->protected_placeholders() ) );
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
			$this->output = apply_filters( 'tribe_events_pro_inline_event_multi_organizer_output', $this->content );
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
		if ( current_user_can( 'read', $this->id ) ) {
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

		$venue_address = array(
			'address'       => tribe_get_address( $this->id ),
			'city'          => tribe_get_city( $this->id ),
			'stateprovince' => tribe_get_stateprovince( $this->id ),
			'zip'           => tribe_get_zip( $this->id ),
			'country'       => tribe_get_country( $this->id ),
		);

		//Unset any address with no value for line
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
