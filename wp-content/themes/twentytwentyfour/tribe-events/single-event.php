<?php
/**
 * Single Event Template
 * A single event. This displays the event title, description, meta, and
 * optionally, the Google map for the event.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/single-event.php
 *
 * @package TribeEventsCalendar
 * @version 4.6.19
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$events_label_singular = tribe_get_event_label_singular();
$events_label_plural   = tribe_get_event_label_plural();

$event_id = Tribe__Events__Main::postIdHelper( get_the_ID() );

/**
 * Allows filtering of the event ID.
 *
 * @since 6.0.1
 *
 * @param int $event_id
 */
$event_id = apply_filters( 'tec_events_single_event_id', $event_id );

/**
 * Allows filtering of the single event template title classes.
 *
 * @since 5.8.0
 *
 * @param array  $title_classes List of classes to create the class string from.
 * @param string $event_id The ID of the displayed event.
 */
$title_classes = apply_filters( 'tribe_events_single_event_title_classes', [ 'tribe-events-single-event-title' ], $event_id );
$title_classes = implode( ' ', tribe_get_classes( $title_classes ) );

/**
 * Allows filtering of the single event template title before HTML.
 *
 * @since 5.8.0
 *
 * @param string $before HTML string to display before the title text.
 * @param string $event_id The ID of the displayed event.
 */
$before = apply_filters( 'tribe_events_single_event_title_html_before', '<h1 class="' . $title_classes . '">', $event_id );

/**
 * Allows filtering of the single event template title after HTML.
 *
 * @since 5.8.0
 *
 * @param string $after HTML string to display after the title text.
 * @param string $event_id The ID of the displayed event.
 */
$after = apply_filters( 'tribe_events_single_event_title_html_after', '</h1>', $event_id );

/**
 * Allows filtering of the single event template title HTML.
 *
 * @since 5.8.0
 *
 * @param string $after HTML string to display. Return an empty string to not display the title.
 * @param string $event_id The ID of the displayed event.
 */
$title = apply_filters( 'tribe_events_single_event_title_html', the_title( $before, $after, false ), $event_id );
$cost  = tribe_get_formatted_cost( $event_id );


/**
 * Color of event depending on the categories
 */

 $event_type = get_the_terms( $event_id, 'tribe_events_cat' );

 $is_virtual = get_post_meta( $event_id, '_tribe_events_is_virtual', true);

 
 $event_type_options = get_field( 'event_type', 'options', true );
$colors=[];
foreach($event_type_options as $event_color){
	$colors += [
		$event_color['name'] => [
			'background_color' => $event_color['background_color'], 
			'color' => $event_color['color']
			]
		];
}
?>

<div id="tribe-events-content" class="tribe-events-single">

 	<div class="ev-s-page">

	<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10","padding":{"top":"0","bottom":"0"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
	<div class="wp-block-group" style="padding-top:0;padding-bottom:0">
		<!-- wp:post-title {"level":1,"fontSize":"x-large"} /-->

		<!-- wp:template-part {"slug":"post-meta","style":{"padding":{"top":"0","bottom":"0","left":"0","right":"0"}}} /-->
	</div>
	<!-- /wp:group -->
 		<!-- The TA2TA event CATEGORY -->
			<?php echo '<div class="tribe-events-event-categories" style="width: fit-content; margin: 37px 0px; padding:4px 10px; border-radius: 3px; -webkit-box-sizing: border-box; box-sizing: border-box; box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14), 0 1px 5px 0 rgba(0,0,0,0.12), 0 3px 1px -2px rgba(0,0,0,0.2); background-color:'. $colors[$event_type[0]->name]['background_color'] .'; color: '. $colors[$event_type[0]->name]['color'] .'">'. $event_type[0]->name . '</div>'; ?>
 		<!-- Notices -->
		<div class="ta2ta-event-notice">
			<?php tribe_the_notices() ?>
		</div>
		
		<div class="event-info-meta">
			<div class="event-header-left">
			
			<div class="tribe-events-schedule tribe-clearfix">
				<?php echo tribe_events_event_schedule_details( $event_id, '<h5>', '</h5>' ); ?>
				<?php if ( ! empty( $cost ) ) : ?>
					<span class="tribe-events-cost"><?php echo esc_html( $cost ) ?></span>
				<?php endif; ?>
			</div>
			<?php
				$efields =  explode( '|', get_post_meta($event_id, '_ecp_custom_6', true ) );
				if ( isset( $efields ) || ! empty( $efields ) || is_array( $efields ) ) {
					echo do_shortcode( ' [event_organization] ' );
					echo do_shortcode( ' [event_grant_project] ' );
				}
			?>
			</div>
			<div class="event-header-right">
				<?php 
				$cfields =  explode( '|', get_post_meta($event_id, '_ecp_custom_10', true ) );
				if ( isset( $efields ) || ! empty( $efields ) || is_array( $efields ) ) {
					echo do_shortcode( ' [event_contact_name] ' );
				}
				?>
			</div>
		</div>

		<!-- Event header -->
		<div id="tribe-events-header" <?php tribe_events_the_header_attributes() ?>>
			<!-- Navigation -->
			<nav class="tribe-events-nav-pagination" aria-label="<?php printf( esc_html__( '%s Navigation', 'the-events-calendar' ), $events_label_singular ); ?>">
				<ul class="tribe-events-sub-nav">
					<li class="tribe-events-nav-previous"><?php tribe_the_prev_event_link( '<span>&laquo;</span> %title%' ) ?></li>
					<li class="tribe-events-nav-next"><?php tribe_the_next_event_link( '%title% <span>&raquo;</span>' ) ?></li>
				</ul>
				<!-- .tribe-events-sub-nav -->
			</nav>
		</div>
		<!-- #tribe-events-header -->

		<?php while ( have_posts() ) :  the_post(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<!-- Event featured image, but exclude link -->
				<?php echo tribe_event_featured_image( $event_id, 'full', false ); ?>

				<!-- Event content -->
				<?php do_action( 'tribe_events_single_event_before_the_content' ) ?>
				<div class="event-body">
					<div class="tribe-events-single-event-description tribe-events-content">
						<h5>Description</h5>
						<?php 
							the_content();
						?>
						<?php
							$ovw_fields =  explode( '|', get_post_meta($event_id, '_ecp_custom_17', true ) );
							if ( isset( $ovw_fields ) || ! empty( $ovw_fields ) || is_array( $ovw_fields ) ) {
								echo do_shortcode( ' [event_pending] ' );
							}
						?>
						<!-- .tribe-events-single-event-description -->
						<div class="after-event-desc">
						<?php do_action( 'tribe_events_single_event_after_the_content' ) ?>
						</div>
					</div>
					<div class="desc-right">
						<div class="event-registration">
							<?php
								$event_registration =  explode( '|', get_post_meta($event_id, '_ecp_custom_8', true ) );
								if ( isset( $event_registration ) || ! empty( $event_registration ) || is_array( $event_registration ) ) {
									echo do_shortcode( ' [event_registration] ' );
								}
							?>
						</div>
						<div class="event-virtual">
							<?php
								$event_registration =  explode( '|', get_post_meta($event_id, '_ecp_custom_8', true ) );
								if ( isset( $event_registration ) || ! empty( $event_registration ) || is_array( $event_registration ) ) {
									echo do_shortcode( ' [event_virtual] ' );
								}
							?>
						</div>
					</div>
				</div>
								
				<!-- Event meta -->
				<?php do_action( 'tribe_events_single_event_before_the_meta' ) ?>
				<?php tribe_get_template_part( 'modules/meta' ); ?>
				<?php do_action( 'tribe_events_single_event_after_the_meta' ) ?>
			</div> <!-- #post-x -->
		<?php endwhile; ?>
	</div>
	<!-- Event footer -->
	<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"},"padding":{"bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
    <div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50)">
        <!-- wp:post-terms {"term":"post_tag","separator":"  ","className":"is-style-pill"} /-->

        <!-- wp:group {"layout":{"type":"constrained"}} -->
        <div class="wp-block-group">
            <!-- wp:spacer {"height":"var:preset|spacing|40"} -->
            <div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
            <!-- /wp:spacer -->

            <!-- wp:separator {"style":{"spacing":{"margin":{"bottom":"var:preset|spacing|40"}}},"backgroundColor":"contrast-3","className":"is-style-wide"} -->
            <hr class="wp-block-separator has-text-color has-contrast-3-color has-alpha-channel-opacity has-contrast-3-background-color has-background is-style-wide"
                style="margin-bottom:var(--wp--preset--spacing--40)" />
            <!-- /wp:separator -->

            <!-- wp:group {"tagName":"nav","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|40","top":"var:preset|spacing|40"}}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
            <nav aria-label="Posts" class="wp-block-group"
                style="padding-top:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40)">
                <!-- wp:post-navigation-link {"type":"previous","label":"Previous: ","showTitle":true,"linkLabel":true,"arrow":"arrow"} /-->

                <!-- wp:post-navigation-link {"label":"Next: ","showTitle":true,"linkLabel":true,"arrow":"arrow"} /-->
            </nav>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
    </div>
    <!-- /wp:group -->
	<!-- #tribe-events-footer -->

</div><!-- #tribe-events-content -->
