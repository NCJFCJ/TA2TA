<?php
/**
 * View: Default Template for the Single Venue on FSE.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/blocks/single-venue.php
 *
 * See more documentation about our views templating system.
 *
 * @link    http://evnt.is/1aiy
 *
 * @version 6.3.2
 */

use Tribe\Events\Views\V2\Assets as Event_Assets;
use Tribe\Events\Views\V2\Template_Bootstrap;

/**
 * @var array $attributes List of attributes for this block.
 */
tribe_asset_enqueue_group( Event_Assets::$group_key );
?>
<div <?php tribe_classes( $attributes['className'], 'tribe-block', 'tec-block__single-venue' ); ?>>
	<?php echo tribe( Template_Bootstrap::class )->get_view_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, StellarWP.XSS.EscapeOutput.OutputNotEscaped ?>
</div>
