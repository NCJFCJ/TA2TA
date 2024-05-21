<?php
/**
 * View: Refresh Icon
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/admin-views/components/icons/refresh.php
 *
 * See more documentation about our views templating system.
 *
 * @link    http://evnt.is/1aiy
 *
 * @var array<string> $classes Additional classes to add to the svg icon.
 *
 * @version 1.9.0
 */

$svg_classes = [ 'tribe-events-virtual-icon', 'tribe-common-c-svgicon', 'tribe-common-c-svgicon--refresh' ];

if ( ! empty( $classes ) ) {
	$svg_classes = array_merge( $svg_classes, $classes );
}
?>
<svg <?php tribe_classes( $svg_classes ); ?> viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M10.282 15.114c-2.77 0-5.023-2.197-5.424-5.023H7.13L3.94 5.374 1 10.09h2.024C3.425 14.025 6.5 17.1 10.282 17.1c1.852 0 3.533-.726 4.832-1.929l-1.223-1.49c-.974.898-2.234 1.433-3.61 1.433zM18.074 7.99C17.673 4.056 14.598 1 10.816 1c-1.852 0-3.533.726-4.831 1.929l1.222 1.49a5.22 5.22 0 013.59-1.433c2.75 0 5.023 2.177 5.424 5.023h-2.253l3.208 4.717 2.922-4.736h-2.024z"/>
</svg>
