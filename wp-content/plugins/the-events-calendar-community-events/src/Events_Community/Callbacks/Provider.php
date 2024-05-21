<?php

namespace TEC\Events_Community\Callbacks;

use TEC\Events_Community\Callbacks\Event\Callback_Add_Edit;
use TEC\Events_Community\Callbacks\Listing\Callback_Listing;
use TEC\Events_Community\Callbacks\Organizer\Callback_Edit as Organizer_Callback;
use TEC\Events_Community\Callbacks\Venue\Callback_Edit as Venue_Callback;

/**
 * The Provider class for registering community event route callbacks.
 *
 * @since   4.10.14
 *
 * @package TEC\Events_Community\Callbacks
 */
class Provider extends \TEC\Common\Contracts\Service_Provider {

	/**
	 * Register the provider.
	 *
	 * @since 4.10.14
	 *
	 * @return void
	 */
	public function register(): void {
		$this->container->singleton( Callback_Add_Edit::class, Callback_Add_Edit::class );
		$this->container->singleton( Callback_Listing::class, Callback_Listing::class );
		$this->container->singleton( Organizer_Callback::class, Organizer_Callback::class );
		$this->container->singleton( Venue_Callback::class, Venue_Callback::class );
	}

}
