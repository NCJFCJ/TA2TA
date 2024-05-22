<?php

namespace TEC\Events_Community\Block_Conversion;

use DOMDocument;
use DOMXPath;
use Tribe__Events__Editor;
use WP_Post;

/**
 * Used for Block logic when creating or editing an event.
 *
 * @since 4.10.17
 */
class Blocks {


	/**
	 * Grab the original content for the content editor.
	 *
	 * @since 4.10.17
	 *
	 * @param string  $post_content Post Content.
	 * @param WP_Post $event The event.
	 *
	 * @return string
	 */
	public function tec_ce_remove_blocks_on_edit( string $post_content, WP_Post $event ): string {
		// Return if we are on the admin page.
		if ( is_admin() ) {
			return $post_content;
		}

		// No post, nothing to convert.
		if ( empty( $post_content ) ) {
			return $post_content;
		}

		return $this->extract_block_text( $post_content );
	}

	/**
	 * Takes the content with block editor markup and tries to filter out the main content to display on the submit
	 * event page.
	 *
	 * @since 4.10.17
	 *
	 * @param string $text The text with block editor formatting.
	 *
	 * @return string The content without block editor markup.
	 */
	public function extract_block_text( string $text ): string {
		$blocks           = parse_blocks( $text );
		$textarea_content = '';

		foreach ( $blocks as $block ) {
			if ( isset( $block['innerHTML'] ) ) {
				$textarea_content .= $block['innerHTML'] . "\n"; // Adding a newline for separation.
			}
		}

		$return_text   = $this->remove_wp_block_classes_and_clean( $textarea_content );
		$visual_editor = tribe( 'community.main' )->useVisualEditor;

		if ( ! $visual_editor ) {
			// Only add wpautop if the textarea is enabled.
			$return_text = wpautop( $return_text );
		}


		$return_text = trim( $return_text );
		return $return_text;
	}

	/**
	 * Convert the submitted event data to blocks.
	 *
	 * @since 4.10.17
	 *
	 * @param int     $event_id The event ID we are modifying meta for.
	 * @param array   $data The meta fields we want saved.
	 * @param WP_Post $event The event itself.
	 *
	 * @return void
	 */
	public function tec_ce_convert_content_to_blocks( int $event_id, array $data, WP_Post $event ): void {
		// Bail if it's not a Community Event edit.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_REQUEST['community-event'] ) ) {
			return;
		}

		if ( ! $this->is_community_event_post( $event_id ) ) {
			return;
		}

		$editor             = tribe( Tribe__Events__Editor::class );
		$should_load_blocks = (bool) $editor->are_blocks_enabled();

		if ( ! $should_load_blocks ) {
			// Blocks aren't enabled, bail.
			return;
		}

		$editor->update_post_content_to_blocks( $event_id );
	}

	/**
	 * Removes empty HTML elements and elements with classes starting with 'wp-block-'
	 * but preserves their inner content, <br> tags, and whitespace.
	 *
	 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	 *
	 * @since 4.10.17
	 *
	 * @param string $content The HTML content to process.
	 *
	 * @return string The cleaned content.
	 */
	public function remove_wp_block_classes_and_clean( string $content ): string {
		$dom = new DOMDocument();
		libxml_use_internal_errors( true );
		$dom->loadHTML( mb_convert_encoding( '<div id="root">' . $content . '</div>', 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		libxml_clear_errors();
		libxml_use_internal_errors( false );

		$xpath = new DOMXPath( $dom );

		// Query all div elements with a class containing 'wp-block-' or empty divs not containing <br> or significant whitespace.
		$nodes = $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " wp-block-") or (not(node()) or normalize-space(.) = "" and not(.//br))]' );

		foreach ( $nodes as $node ) {
			// Create a document fragment to hold the inner HTML for non-empty elements.
			$fragment = $dom->createDocumentFragment();
			while ( $node->childNodes->length > 0 ) {
				$fragment->appendChild( $node->childNodes->item( 0 ) );
			}

			// Check if the node should be removed entirely or just have its contents moved up.
			if ( $node->hasAttributes() || $node->childNodes->length > 0 ) {
				$node->parentNode->replaceChild( $fragment, $node );
			} else {
				// For truly empty nodes that shouldn't be preserved, remove them without replacement.
				$node->parentNode->removeChild( $node );
			}
		}

		// Extract the content from the temporary root div, preserving <br> tags and whitespace.
		$root     = $dom->getElementById( 'root' );
		$new_html = '';
		foreach ( $root->childNodes as $child ) {
			$new_html .= $dom->saveHTML( $child );
		}

		return trim( $new_html );
	}

	/**
	 * Reverses the effects of wpautop by converting HTML paragraph and break tags back into plain text line breaks.
	 *
	 * This function aims to revert the automatic formatting applied by WordPress's wpautop function, which converts
	 * double line breaks in text into paragraph elements and single line breaks into <br> tags.
	 *
	 * @since 4.10.17
	 *
	 * @param string $content The HTML content to process, typically containing <p>, </p>, and <br> tags.
	 *
	 * @return string The processed content.
	 */
	public function reverse_wpautop( string $content ): string {
		// Ensures that the content starts and ends with paragraph tags for consistent processing.
		$content = '<p>' . $content . '</p>';

		$content = preg_replace( '/<p[^>]*>/', "\n\n", $content ); // Replace opening <p> tags with two newlines.
		$content = str_replace( '</p>', '', $content ); // Remove closing </p> tags.
		$content = preg_replace( '/<br ?\/?>/', "\n", $content ); // Replace <br> tags with a single newline.
		$content = preg_replace( "/(\n\s*){3,}/", "\n\n", $content ); // Reduce three or more newlines to two newlines.

		return trim( $content ); // Trim whitespace from the beginning and end.
	}

	/**
	 * Migrates additional fields parameters to blocks.
	 *
	 * This function checks if the given slug matches a specific condition
	 * and updates the parameters accordingly.
	 *
	 * @since 4.10.17
	 *
	 * @param array|string $params The original parameter.
	 * @param string       $slug The slug to check against.
	 * @param WP_Post      $post The post object to extract the ID from.
	 *
	 * @return mixed Modified or original parameters.
	 */
	public function tribe_blocks_editor_update_classic_content_params( $params, string $slug, WP_Post $post ) {
		if ( ! $this->is_community_event_post( $post ) ) {
			return $params;
		}

		switch ( $slug ) {
			case 'tribe/event-venue':
				$value = (int) tribe_get_venue_id( $post->ID );
				if ( $value > 0 ) {
					$params = [ 'venue' => $value ];
				}
				break;
			case 'tribe/event-organizer':
				$value = (int) tribe_get_organizer_id( $post->ID );
				if ( $value > 0 ) {
					$params = [ 'organizer' => $value ];
				}
				break;
			case 'tribe/event-website':
				$value = tribe_get_event_website_link( $post->ID );
				if ( ! empty( $value ) ) {
					$params = [ 'urlLabel' => __( 'External Link:', 'tribe-events-community' ) ];
				}
				break;
		}

		return $params;
	}

	/**
	 * Filters and adjusts the template array for classic templates.
	 *
	 * Removes 'classic-event-details' blocks and ensures that an 'event-organizer'
	 * block is included in the template array.
	 *
	 * @since 4.10.17
	 *
	 * @param array $template The original template blocks array.
	 *
	 * @return array The modified template array.
	 */
	public function tribe_events_editor_default_classic_template( array $template ): array {
		$transformations = [
			'tribe/event-links' => 'tribe/event-website',
		];

		$required_blocks = [
			'tribe/event-organizer',
			'tribe/event-website',
			'tribe/event-price',
		];

		$new_template   = [];
		$present_blocks = [];

		foreach ( $template as $block ) {
			if ( 'tribe/classic-event-details' === $block[0] ) {
				continue;
			}

			$block[0] = $transformations[ $block[0] ] ?? $block[0];

			$new_template[]   = $block;
			$present_blocks[] = $block[0];
		}

		// Ensure required blocks are added if they were missing.
		foreach ( $required_blocks as $required_block ) {
			if ( ! in_array( $required_block, $present_blocks ) ) {
				$new_template[] = [ $required_block ];
			}
		}

		return $new_template;
	}

	/**
	 * Removes specified blocks from the provided content.
	 *
	 * This method is designed to strip out blocks that aren't needed from the post content.
	 *
	 * @since 4.10.17
	 *
	 * @param string  $content The original content from which blocks will be removed.
	 * @param WP_Post $post The post object related to the content being processed.
	 * @param array   $blocks An array of block structures related to the content.
	 *
	 * @return string The content with specified blocks removed.
	 */
	public function tribe_blocks_editor_update_classic_content( $content, $post, $blocks ) {
		if ( ! $this->is_community_event_post( $post ) ) {
			return;
		}
		// Patterns to match the sections to be removed
		// Includes: tribe/tickets, tribe/rsvp, and tribe/attendees.
		$patterns = [
			'/<!-- wp:tribe\/tickets -->.*?<!-- \/wp:tribe\/tickets -->/s',
			'/<!-- wp:tribe\/rsvp\s+\/-->/s',
			'/<!-- wp:tribe\/attendees\s+\/-->/s',
		];

		foreach ( $patterns as $pattern ) {
			$content = preg_replace( $pattern, '', $content );
		}

		return $content;
	}


	/**
	 * Checks the origin of the given event post or post ID.
	 *
	 * Check to see if the specific post was created via Community Events.
	 *
	 * @since 4.10.17
	 *
	 * @param WP_Post|int $event The event post or post ID.
	 *
	 * @return string|false The value of the '_EventOrigin' post meta, or false if not found.
	 */
	public function is_community_event_post( $event ): bool {
		$post_id = ( $event instanceof WP_Post ) ? $event->ID : absint( $event );

		// Fetch and return the '_EventOrigin' post meta.
		$event_origin = get_post_meta( $post_id, '_EventOrigin', true );

		return 'community-events' === $event_origin;
	}

}