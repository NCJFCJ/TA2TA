<?php

namespace TEC\Events_Community\Block_Conversion;

use TEC\Common\Contracts\Provider\Controller as Controller_Base;
use WP_Post;

/**
 * Used for Block logic when creating or editing an event.
 *
 * @since 4.10.17
 */
class Controller extends Controller_Base {

	/**
	 * Determines if this controller will register.
	 * This is present due to how UOPZ works, it will fail we have the boolean living on the method.
	 *
	 * @since 4.10.17
	 * @var bool
	 */
	protected bool $is_active = true;

	/**
	 * @inheritDoc
	 */
	public function is_active(): bool {
		// Disable activation. @todo @redscar Bring back in a later MR.
		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function do_register(): void {
		$this->boot();
		$this->add_filters();
		$this->add_actions();
	}

	/**
	 * @inheritDoc
	 */
	public function unregister(): void {
		$this->remove_filters();
		$this->remove_actions();
	}

	/**
	 * @inheritDoc
	 */
	public function add_actions(): void {
		add_action( 'tribe_events_update_meta', [ $this, 'tec_ce_convert_content_to_blocks' ], 10, 3 );
	}

	/**
	 * @inheritDoc
	 */
	public function remove_actions(): void {
		remove_action( 'tribe_events_update_meta', [ $this, 'tec_ce_convert_content_to_blocks' ], 10, 3 );
	}

	/**
	 * @inheritDoc
	 */
	public function add_filters(): void {
		add_filter( 'tec_events_community_event_editor_post_content', [ $this, 'tec_ce_remove_blocks_on_edit' ], 10, 2 );
		add_filter( 'tribe_blocks_editor_update_classic_content_params', [ $this, 'tribe_blocks_editor_update_classic_content_params' ], 10, 3 );
		add_filter( 'tribe_events_editor_default_classic_template', [ $this, 'tribe_events_editor_default_classic_template' ], 10, 1 );
		add_filter( 'tribe_blocks_editor_update_classic_content', [ $this, 'tribe_blocks_editor_update_classic_content' ], 100, 3 );
	}

	/**
	 * @inheritDoc
	 */
	public function remove_filters(): void {
		remove_filter( 'tec_events_community_event_editor_post_content', [ $this, 'tec_ce_remove_blocks_on_edit' ], 10 );
		remove_filter( 'tribe_blocks_editor_update_classic_content_params', [ $this, 'tribe_blocks_editor_update_classic_content_params' ], 20 );
		remove_filter( 'tribe_events_editor_default_classic_template', [ $this, 'tribe_events_editor_default_classic_template' ], 10 );
	}


	/**
	 * Add the container to convert the content to blocks.
	 *
	 * @since 4.10.17
	 *
	 * @param int     $event_id The unique identifier for the event being processed.
	 * @param array   $data Associative array of event data, intended for conversion.
	 * @param WP_Post $event WP_Post object representing the event.
	 *
	 * @return void
	 */
	public function tec_ce_convert_content_to_blocks( int $event_id, array $data, WP_Post $event ): void {
		$this->container->make( Blocks::class )->tec_ce_convert_content_to_blocks( $event_id, $data, $event );
	}

	/**
	 * Add the container to convert the content to blocks.
	 *
	 * @since 4.10.17
	 *
	 * @param string  $post_content The original content of the event post.
	 * @param WP_Post $event WP_Post object representing the event being edited.
	 *
	 * @return string The modified content, potentially with block editor markup removed or altered.
	 */
	public function tec_ce_remove_blocks_on_edit( string $post_content, WP_Post $event ): string {
		return $this->container->make( Blocks::class )->tec_ce_remove_blocks_on_edit( $post_content, $event );
	}

	/**
	 * Add the container to add additional params to blocks.
	 *
	 * @since 4.10.17
	 *
	 * @param array   $params The original parameters.
	 * @param string  $slug The slug to check against.
	 * @param WP_Post $post The post object to extract the ID from.
	 *
	 * @return array Modified or original parameters.
	 */
	public function tribe_blocks_editor_update_classic_content_params( $params, $slug, $post ) {
		return $this->container->make( Blocks::class )->tribe_blocks_editor_update_classic_content_params( $params, $slug, $post );
	}

	/**
	 * Sets the default classic template for events editor.
	 *
	 * @since 4.10.17
	 *
	 * @param string $template The current template.
	 *
	 * @return string The modified or original template.
	 */
	public function tribe_events_editor_default_classic_template( $template ) {
		return $this->container->make( Blocks::class )->tribe_events_editor_default_classic_template( $template );
	}

	/**
	 * Add the container to alter the block content after converting from classic editor.
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
		return $this->container->make( Blocks::class )->tribe_blocks_editor_update_classic_content( $content, $post, $blocks );
	}


}