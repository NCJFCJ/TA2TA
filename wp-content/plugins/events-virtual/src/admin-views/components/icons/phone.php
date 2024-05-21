<?php
/**
 * Template for the phone icon.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/components/icons/phone.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 1.9.0
 *
 * @var array $classes Additional classes to add to the svg icon.
 */

$svg_classes = [ 'tribe-events-virtual-icon', 'tribe-events-virtual-icon--phone' ];

if ( ! empty( $classes ) ) {
	$svg_classes = array_merge( $svg_classes, $classes );
}
?>
<svg <?php tribe_classes( $svg_classes ); ?> xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M10.682 9.882L9.378 11.51a13.762 13.762 0 01-4.89-4.887l1.63-1.304c.393-.315.525-.855.32-1.315L4.953.658A1.108 1.108 0 003.66.036L.833.769C.286.912-.067 1.441.01 2A16.435 16.435 0 0014 15.99a1.114 1.114 0 001.23-.823l.734-2.828a1.11 1.11 0 00-.622-1.292l-3.346-1.485c-.46-.205-1-.073-1.314.32z" /></svg>
