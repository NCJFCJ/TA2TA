<?php

namespace TEC\Events_Virtual\Integrations\Plugins\Tickets_Wallet_Plus;

use TEC\Common\Integrations\Traits\Plugin_Integration;
use TEC\Events_Virtual\Integrations\Integration_Abstract;
use TEC\Events_Virtual\Integrations\Plugins\Tickets_Wallet_Plus\Pdf;
use TEC\Tickets_Wallet_Plus\Controller as Tickets_Wallet_Plus;
use Tribe__Template;

/**
 * Class Controller
 *
 * @since   1.15.5
 *
 * @package TEC\Events_Virtual\Integrations\Plugins\Tickets_Wallet_Plus
 */
class Controller extends Integration_Abstract {
	use Plugin_Integration;

	/**
	 * {@inheritdoc}
	 */
	public static function get_slug(): string {
		return 'event-tickets-wallet-plus';
	}

	/**
	 * {@inheritdoc}
	 */
	public function load_conditionals(): bool {
		return tribe( Tickets_Wallet_Plus::class )->is_active();
	}

	/**
	 * {@inheritdoc}
	 */
	public function load(): void {
		$this->register_actions();
		$this->register_filters();
	}

	/**
	 * Register filters.
	 *
	 * @since 1.15.5
	 *
	 * @return void
	 */
	public function register_filters(): void {
		add_filter( 'tec_tickets_wallet_plus_pdf_sample_template_context', [ $this, 'add_link_to_sample_pdf' ] );
	}

	/**
	 * Register actions.
	 *
	 * @since 1.15.5
	 *
	 * @return void
	 */
	public function register_actions(): void {
		add_action( 'tribe_template_after_include:tickets-wallet-plus/pdf/pass/styles', [ $this, 'add_styles_to_pdf' ], 10, 3 );
		add_action( 'tribe_template_after_include:tickets-wallet-plus/pdf/pass/body/ticket-info', [ $this, 'add_link_to_pdf' ], 10, 3 );
	}

	/**
	 * Add styles to PDF.
	 *
	 * @since 1.15.5
	 *
	 * @param string           $file     Path to the file.
	 * @param string           $name     Name of the file.
	 * @param \Tribe__Template $template Template instance.
	 *
	 * @return void
	 */
	public function add_styles_to_pdf( $file, $name, $template ) {
		$this->container->make( Passes\Pdf::class )->add_styles( $file, $name, $template );
	}

	/**
	 * Add link to PDF.
	 *
	 * @since 1.15.5
	 *
	 * @param string           $file     Path to the file.
	 * @param string           $name     Name of the file.
	 * @param \Tribe__Template $template Template instance.
	 *
	 * @return void
	 */
	public function add_link_to_pdf( $file, $name, $template ) {
		$this->container->make( Passes\Pdf::class )->add_link( $file, $name, $template );
	}

	/**
	 * Add link to sample PDF.
	 *
	 * @since 1.15.5
	 *
	 * @param array $context Template context.
	 *
	 * @return array Modified template context.
	 */
	public function add_link_to_sample_pdf( $context ): array {
		return $this->container->make( Passes\Pdf::class )->add_link_to_sample( $context );
	}
}