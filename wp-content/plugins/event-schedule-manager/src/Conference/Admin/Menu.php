<?php
/**
 * Organizes Event Schedule Manager Post Types in Admin Nav.
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

use TEC\Conference\Plugin;
use TEC\Conference\Admin\Traits\Menu_Utilities;

/**
 * Class Menu
 *
 * @since 1.0.0
 *
 * @package TEC\Conference\Admin
 */
class Menu {

	use Menu_Utilities;

	/**
	 * Adds Event Schedule Manager menu item in the WordPress Admin Nav.
	 *
	 * @since 1.0.0
	 */
	public function add_conference_schedule_menu() {

		$menu_icon = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTgiIHZpZXdCb3g9IjAgMCA4OSA3NiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICAgIDxwYXRoIGQ9Ik03My43OTI3IDEuOTkzNDRWNi4yMjk1MUg3OC4yODAxQzg0LjIwMDUgNi4yMjk1MSA4OSAxMS4wMjY3IDg5IDE2Ljk0NDNWNjUuMjg1MkM4OSA3MS4yMDI4IDg0LjIwMDUgNzYgNzguMjgwMSA3NkgyMy44OUMyMi45MjYyIDc2IDIyLjE0NDkgNzUuMjE5MSAyMi4xNDQ5IDc0LjI1NTdDMjIuMTQ0OSA3My4yOTI0IDIyLjkyNjIgNzIuNTExNSAyMy44OSA3Mi41MTE1SDc4LjI4MDFDODIuMjczIDcyLjUxMTUgODUuNTA5OCA2OS4yNzYyIDg1LjUwOTggNjUuMjg1MlYyNC40MTU4SDE5LjY5NDdWMjcuNTU3MUMxOS42OTQ3IDI4LjUyMDQgMTguOTEzNCAyOS4zMDEzIDE3Ljk0OTYgMjkuMzAxM0MxNi45ODU4IDI5LjMwMTMgMTYuMjA0NSAyOC41MjA0IDE2LjIwNDUgMjcuNTU3MVYxNi45NDQzQzE2LjIwNDUgMTEuMDI2NyAyMS4wMDM5IDYuMjI5NTEgMjYuOTI0NCA2LjIyOTUxSDMyLjkwNzZWMS45OTM0NEMzMi45MDc2IDEuMTY1ODMgMzMuNDEyMSAwLjQ1NjAxOCAzNC4xMzA2IDAuMTU0NTc2QzM0LjMxNzggMC4wNTU4NzMyIDM0LjUzMTIgMCAzNC43NTc2IDBDMzQuNzc5NCAwIDM0LjgwMTEgMC4wMDA1MTg5OTkgMzQuODIyNyAwLjAwMTU0NTU5QzM0Ljg0OSAwLjAwMDUxODk5OSAzNC44NzU0IDAgMzQuOTAyIDBDMzYuMDAzNCAwIDM2Ljg5NjQgMC44OTI0OTQgMzYuODk2NCAxLjk5MzQ0VjYuMjI5NTFINjkuODAzOVYxLjk5MzQ0QzY5LjgwMzkgMS4xMjgyMiA3MC4zNTU0IDAuMzkxNzM2IDcxLjEyNjIgMC4xMTYwMjhDNzEuMjkzNSAwLjA0MTQ1MzQgNzEuNDc4OSAwIDcxLjY3MzkgMEM3MS42OTI3IDAgNzEuNzExNCAwLjAwMDM4NDAyMSA3MS43MyAwLjAwMTE0ODI2QzcxLjc1MjcgMC4wMDAzODQwMjEgNzEuNzc1NCAwIDcxLjc5ODMgMEM3Mi44OTk4IDAgNzMuNzkyNyAwLjg5MjQ5NCA3My43OTI3IDEuOTkzNDRaIgogICAgICAgICAgZmlsbD0icmdiYSgyNDAsIDI0NiwgMjUyLCAwLjYpIi8+CiAgICA8cGF0aCBkPSJNNTcuODM0NyAzOS40NjgyQzU3LjgzNDcgNDAuNTY5MSA1Ni45NDE4IDQxLjQ2MTUgNTUuODQwNCA0MS40NjE1SDQzLjg3NDZDNDIuNzczMiA0MS40NjE1IDQxLjg4MDMgNDAuNTY5MSA0MS44ODAzIDM5LjQ2ODJDNDEuODgwMyAzOC4zNjczIDQyLjc3MzIgMzcuNDc0OCA0My44NzQ2IDM3LjQ3NDhINTUuODQwNEM1Ni45NDE4IDM3LjQ3NDggNTcuODM0NyAzOC4zNjczIDU3LjgzNDcgMzkuNDY4MloiCiAgICAgICAgICBmaWxsPSIjYTdhYWFkIi8+CiAgICA8cGF0aCBkPSJNNzYuOTggNDkuMDM2MkM3Ni45OCA1MC4xMzcxIDc2LjA4NzEgNTEuMDI5NiA3NC45ODU3IDUxLjAyOTZINDcuNDY0NEM0Ni4zNjMgNTEuMDI5NiA0NS40NzAxIDUwLjEzNzEgNDUuNDcwMSA0OS4wMzYyQzQ1LjQ3MDEgNDcuOTM1MyA0Ni4zNjMgNDcuMDQyOCA0Ny40NjQ0IDQ3LjA0MjhINzQuOTg1N0M3Ni4wODcxIDQ3LjA0MjggNzYuOTggNDcuOTM1MyA3Ni45OCA0OS4wMzYyWiIKICAgICAgICAgIGZpbGw9IiNhN2FhYWQiLz4KICAgIDxwYXRoIGQ9Ik03MC45OTcxIDU4LjYwNDNDNzAuOTk3MSA1OS43MDUyIDcwLjEwNDIgNjAuNTk3NiA2OS4wMDI4IDYwLjU5NzZINDUuMDcxMkM0My45Njk4IDYwLjU5NzYgNDMuMDc2OSA1OS43MDUyIDQzLjA3NjkgNTguNjA0M0M0My4wNzY5IDU3LjUwMzQgNDMuOTY5OCA1Ni42MTA5IDQ1LjA3MTIgNTYuNjEwOUg2OS4wMDI4QzcwLjEwNDIgNTYuNjEwOSA3MC45OTcxIDU3LjUwMzQgNzAuOTk3MSA1OC42MDQzWiIKICAgICAgICAgIGZpbGw9IiNhN2FhYWQiLz4KICAgIDxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIgogICAgICAgICAgZD0iTTM2Ljg5NjQgNTEuMzMxMkMzNi44OTY0IDYxLjUxNDkgMjguNjM2OCA2OS43NzA1IDE4LjQ0ODIgNjkuNzcwNUM4LjI1OTUzIDY5Ljc3MDUgMCA2MS41MTQ5IDAgNTEuMzMxMkMwIDQxLjE0NzQgOC4yNTk1MyAzMi44OTE4IDE4LjQ0ODIgMzIuODkxOEMyOC42MzY4IDMyLjg5MTggMzYuODk2NCA0MS4xNDc0IDM2Ljg5NjQgNTEuMzMxMlpNMTguMjczNiA0MC44NjU2QzE3LjUyNzQgNDAuODY1NiAxNi45NTI0IDQxLjU1OTEgMTYuOTUyNCA0Mi4zNzVWNTIuNzYxNEMxNi45NTI0IDUzLjU3NzIgMTcuNTI3NCA1NC4yNzA4IDE4LjI3MzYgNTQuMjcwOEgyNS40NTMxQzI2LjE5OTMgNTQuMjcwOCAyNi43NzQzIDUzLjU3NzIgMjYuNzc0MyA1Mi43NjE0QzI2Ljc3NDMgNTEuOTQ1NSAyNi4xOTkzIDUxLjI1MTkgMjUuNDUzMSA1MS4yNTE5SDE5LjU5NDhWNDIuMzc1QzE5LjU5NDggNDEuNTU5MSAxOS4wMTk4IDQwLjg2NTYgMTguMjczNiA0MC44NjU2WiIKICAgICAgICAgIGZpbGw9IiNhN2FhYWQiLz4KPC9zdmc+Cg==';

		add_menu_page(
			esc_html_x( 'Event Schedule Manager', 'Admin menu page title.','event-schedule-manager' ),
			esc_html_x( 'Schedule', 'Admin menu title.','event-schedule-manager' ),
			'read',
			$this->get_menu_slug(),
			'',
			$menu_icon,
			21
		);
	}

	/**
	 * Organizes the post types under the Event Schedule Manager menu item.
	 *
	 * @since 1.0.0
	 */
	public function organize_post_types() {
		// Sessions.
		add_submenu_page(
			$this->get_menu_slug(),
			esc_html_x( 'Sessions', 'Submenu page title for Sessions.', 'event-schedule-manager' ),
            esc_html_x( 'Sessions', 'Admin submenu title for Sessions.', 'event-schedule-manager' ),
			'read',
			'edit.php?post_type=' . Plugin::SESSION_POSTTYPE
		);
		// Submenu for Tracks Taxonomy.
		add_submenu_page(
			$this->get_menu_slug(),
			esc_html_x( 'Tracks', 'Submenu page title for Tracks.', 'event-schedule-manager' ),
            esc_html_x( 'Tracks', 'Admin submenu title for Tracks.', 'event-schedule-manager' ),
			'read',
			'edit-tags.php?taxonomy=' . Plugin::TRACK_TAXONOMY . '&post_type=' . Plugin::SESSION_POSTTYPE
		);
		// Submenu for Locations Taxonomy.
		add_submenu_page(
			$this->get_menu_slug(),
			esc_html_x( 'Locations', 'Submenu page title for Locations.', 'event-schedule-manager' ),
            esc_html_x( 'Locations', 'Admin submenu title for Locations.', 'event-schedule-manager' ),
			'read',
			'edit-tags.php?taxonomy=' . Plugin::LOCATION_TAXONOMY . '&post_type=' . Plugin::SESSION_POSTTYPE
		);
		// Submenu for Tags Taxonomy.
/*		add_submenu_page(
			$this->get_menu_slug(),
            esc_html_x( 'Tags', 'Submenu page title for Tags.', 'event-schedule-manager' ),
            esc_html_x( 'Tags', 'Admin submenu title for Tags.', 'event-schedule-manager' ),
			'read',
			'edit-tags.php?taxonomy=' . Plugin::TAGS_TAXONOMY . '&post_type=' . Plugin::SESSION_POSTTYPE
		);*/ // Hide tags per TEC-4925 and will add better support later.
		add_submenu_page(
		    'wp_separator_' . 21,
		    '',
		    '',
		    'read',
		    ''
		);

		// Divider.
		add_submenu_page(
		    $this->get_menu_slug(),
		    '',
		    '',
		    'read',
		    '#'
		);

		// Speakers.
		add_submenu_page(
			$this->get_menu_slug(),
			esc_html_x( 'Speakers', 'Submenu page title for Speakers.', 'event-schedule-manager' ),
            esc_html_x( 'Speakers', 'Admin submenu title for Speakers.', 'event-schedule-manager' ),
			'read',
			'edit.php?post_type=' . Plugin::SPEAKER_POSTTYPE
		);
		// Submenu for Groups Taxonomy.
		add_submenu_page(
			$this->get_menu_slug(),
			esc_html_x( 'Speaker Groups', 'Submenu page title for Groups.', 'event-schedule-manager' ),
			esc_html_x( 'Speaker Groups', 'Admin submenu title for Groups.', 'event-schedule-manager' ),
			'read',
			'edit-tags.php?taxonomy=' . Plugin::GROUP_TAXONOMY . '&post_type=' . Plugin::SPEAKER_POSTTYPE
		);

		// Divider.
		add_submenu_page(
		    $this->get_menu_slug(),
		    '',
		    '',
		    'read',
		    '#'
		);

		// Sponsors.
		add_submenu_page(
			$this->get_menu_slug(),
			esc_html_x( 'Sponsors', 'Submenu page title for Sponsors.', 'event-schedule-manager' ),
            esc_html_x( 'Sponsors', 'Admin submenu title for Sponsors.', 'event-schedule-manager' ),
			'read',
			'edit.php?post_type=' . Plugin::SPONSOR_POSTTYPE
		);
		// Submenu for Sponsor Levels Taxonomy.
		add_submenu_page(
			$this->get_menu_slug(),
			esc_html_x( 'Sponsor Levels', 'Submenu page title for Sponsor Levels.', 'event-schedule-manager' ),
            esc_html_x( 'Sponsor Levels', 'Admin submenu title for Sponsor Levels.', 'event-schedule-manager' ),
            'read',
			'edit-tags.php?taxonomy=' . Plugin::SPONSOR_LEVEL_TAXONOMY . '&post_type=' . Plugin::SPONSOR_POSTTYPE
		);
	}

	/**
	 * Remove duplicate submenu items.
	 * Required as Speaker and Sponsor custom post would display twice in the menu.
	 * This is expected as the admin list for both of those keeps the Conference Submenu open.
	 *
	 * @since 1.0.0
	 */
	public function remove_duplicate_submenu() {
		global $submenu;

		$menu_slug = $this->get_menu_slug();

		if ( isset( $submenu[ $menu_slug ] ) ) {
			// Iterate through submenu items to find duplicates.
			$titles = [];

			foreach ( $submenu[ $menu_slug ] as $index => $item ) {
				if ( empty( $item[0] ) ) {
					continue;
				}

				if ( isset( $titles[ $item[0] ] ) ) {
					// Remove the first instance, keep the current (last) one
					unset( $submenu[ $menu_slug ][ $titles[ $item[0] ] ] );
				}
				$titles[ $item[0] ] = $index; // Store the latest index
			}

			// Re-index array.
			$submenu[ $menu_slug ] = array_values( $submenu[ $menu_slug ] );
		}
	}
}
