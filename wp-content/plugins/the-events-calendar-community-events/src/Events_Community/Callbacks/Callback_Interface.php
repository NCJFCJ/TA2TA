<?php

namespace TEC\Events_Community\Callbacks;

/**
 * Interface Callback_Interface
 *
 * @since   4.10.14
 *
 * @package TEC\Events_Community\Callbacks
 */
interface Callback_Interface {

	/**
	 * Callback method to be implemented by subclasses.
	 *
	 * @since 4.10.14
	 *
	 * @return string
	 */
	public function callback(): string;

	/**
	 * Sets up the page arguments.
	 *
	 * @since 4.10.14
	 *
	 * @param array $args The page arguments.
	 *
	 * @return void
	 */
	public function setup( array $args ): void;
}
