<?php
/**
 * Block Controller
 */

namespace TEC\Events_Pro\Block_Templates;

use TEC\Events\Block_Templates\Block_Template_Contract;
use TEC\Events_Pro\Block_Templates\Single_Venue\Single_Block_Template as Single_Venue_Block_Template;
use TEC\Common\Contracts\Provider\Controller as Controller_Contract;


/**
 * Class Controller
 *
 * @since 6.3.2
 *
 * @package TEC\Events_Pro\Block_Templates
 */
class Controller extends Controller_Contract {
	/**
	 * Register the provider.
	 *
	 * @since 6.3.2
	 */
	public function do_register(): void {
		$this->add_filters();

		// Register the service provider itself on the container.
		$this->container->singleton( static::class, $this );
	}

	/**
	 * Unhooks actions and filters.
	 *
	 * @since 6.3.2
	 */
	public function unregister(): void {
		$this->remove_filters();
	}

	/**
	 * Should only be active if we are in a Site Editor theme.
	 *
	 * @since 6.3.2
	 *
	 * @return bool Only active during FS theme.
	 */
	public function is_active(): bool {
		return tec_is_full_site_editor();
	}

	/**
	 * Adds the filters required by the FSE components.
	 *
	 * @since 6.3.2
	 */
	protected function add_filters() {
		add_filter( 'tec_events_get_full_site_block_template_services', [ $this, 'filter_include_templates' ], 25, 3 );
		add_filter(
			'single_template_hierarchy',
			[
				$this,
				'filter_single_template_hierarchy',
			],
			10,
			1
		);
	}

	/**
	 * Removes registered filters.
	 *
	 * @since 6.3.2
	 */
	public function remove_filters() {
		remove_filter( 'tec_events_get_full_site_block_template_services', [ $this, 'filter_include_templates' ], 25 );
		remove_filter(
			'single_template_hierarchy',
			[
				$this,
				'filter_single_template_hierarchy',
			],
			10
		);
	}

	/**
	 * Redirect the post type template to our Single Event slug, as that is what is used for lookup in the database.
	 *
	 * @since 6.3.2
	 *
	 * @param array $templates Templates in order of display hierarchy.
	 *
	 * @return array Adjusted file name that is parsed to match our block template.
	 */
	public function filter_single_template_hierarchy( $templates ) {
		if ( empty( $templates ) ) {
			return $templates;
		}

		if ( ! is_array( $templates ) ) {
			return $templates;
		}

		// Is it our post type?
		$index = array_search( 'single-tribe_venue.php', $templates, true );
		if ( is_int( $index ) ) {
			// Switch to our faux template which maps to our slug.
			$templates[ $index ] = 'single-venue.php';
		}

		return $templates;
	}

	/**
	 * Filters and returns the available Event Block Template Services, used to locate
	 * WP_Block_Template instances.
	 *
	 * @since 6.3.2
	 *
	 * @param Block_Template_Contract[] $templates     The list of block templates to be filtered.
	 * @param string                    $template_type The type of templates we are fetching.
	 *
	 * @return Block_Template_Contract[] List of filtered Event Calendar templates.
	 */
	public function filter_include_templates( $templates, $template_type ): array {
		if ( $template_type === 'wp_template' ) {
			$templates = array_merge(
				$templates,
				[
					tribe( Single_Venue_Block_Template::class ),
				]
			);
		}

		return $templates;
	}
}
