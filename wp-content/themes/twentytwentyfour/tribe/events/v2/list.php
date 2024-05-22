<?php
/**
 * View: List View
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/list.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 5.2.1
 *
 * @var array    $events               The array containing the events.
 * @var string   $rest_url             The REST URL.
 * @var string   $rest_method          The HTTP method, either `POST` or `GET`, the View will use to make requests.
 * @var string   $rest_nonce           The REST nonce.
 * @var int      $should_manage_url    int containing if it should manage the URL.
 * @var bool     $disable_event_search Boolean on whether to disable the event search.
 * @var string[] $container_classes    Classes used for the container of the view.
 * @var array    $container_data       An additional set of container `data` attributes.
 * @var string   $breakpoint_pointer   String we use as pointer to the current view we are setting up with breakpoints.
 */

$header_classes = [ 'tribe-events-header' ];
if ( empty( $disable_event_search ) ) {
	$header_classes[] = 'tribe-events-header--has-event-search';
}

?>

<div
	<?php tribe_classes( $container_classes ); ?>
	data-js="tribe-events-view"
	data-view-rest-nonce="<?php echo esc_attr( $rest_nonce ); ?>"
	data-view-rest-url="<?php echo esc_url( $rest_url ); ?>"
	data-view-rest-method="<?php echo esc_attr( $rest_method ); ?>"
	data-view-manage-url="<?php echo esc_attr( $should_manage_url ); ?>"
	<?php foreach ( $container_data as $key => $value ) : ?>
		data-view-<?php echo esc_attr( $key ) ?>="<?php echo esc_attr( $value ) ?>"
	<?php endforeach; ?>
	<?php if ( ! empty( $breakpoint_pointer ) ) : ?>
		data-view-breakpoint-pointer="<?php echo esc_attr( $breakpoint_pointer ); ?>"
	<?php endif; ?>
>

	<div class="has-global-padding is-layout-constrained wp-block-group alignwide" id="header" style="margin:5px auto 5px auto; max-width: 1280px; padding: 10px;">
		<h1 class="has-text-align-center has-text-color has-contrast-color wp-block-post-title" style="margin-top:var(--wp--preset--spacing--30);margin-right:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30);margin-left:var(--wp--preset--spacing--30); font-style:normal;font-weight:700;">Calendar</h1>
		<?php if ( is_user_logged_in() ) { ?>
			<div class="wp-block-buttons  is-layout-flex wp-container-19">
				<div class="wp-block-button has-custom-font-size" style="font-size:16px; ">
					<a class="wp-block-button__link wp-element-button" href="/events/ta2ta-providers/add" style="border-radius:5px;background-color:var(--wp--preset--color--contrast-2); color: var(--wp--preset--color--base-2);padding: 10px 10px;">
						Add an Event
					</a>
				</div>
			</div>
			<?php } ?>
	</div>
	<!-- Note in the head of the page -->
	<div class="wp-block-uagb-container calendar-block">
		<div class="calendar-header">
			<div class="hidden-xs image-header-img">
				<span class="calendar-svg">
					<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512"><path fill="currentColor" d="M472 96h-88V40h-32v56H160V40h-32v56H40a24.028 24.028 0 0 0-24 24v336a24.028 24.028 0 0 0 24 24h432a24.028 24.028 0 0 0 24-24V120a24.028 24.028 0 0 0-24-24m-8 352H48V128h80v40h32v-40h192v40h32v-40h80Z"></path><path fill="currentColor" d="M112 224h32v32h-32zm88 0h32v32h-32zm80 0h32v32h-32zm88 0h32v32h-32zm-256 72h32v32h-32zm88 0h32v32h-32zm80 0h32v32h-32zm88 0h32v32h-32zm-256 72h32v32h-32zm88 0h32v32h-32zm80 0h32v32h-32zm88 0h32v32h-32z"></path></svg>
				</span>
			</div>
			<div class="calendar-text">
				The special conditions of the Cooperative Agreements between OVW and the individual TA providers require
				TA providers to post all pending and approved project events to the TA2TA Calendar. This allows other TA
				providers, OVW grantees, potential grantees, subgrantees, and OVW to avoid scheduling conflicts, be informed
				about TA provider events, and access in-person and online educational opportunities.
			</div>
		</div>
	</div>
	<!-- end of note -->
	
	<div class="tribe-common-l-container tribe-events-l-container" style="padding-top: 0">
		<?php $this->template( 'components/loader', [ 'text' => __( 'Loading...', 'the-events-calendar' ) ] ); ?>

		<?php $this->template( 'components/json-ld-data' ); ?>

		<?php $this->template( 'components/data' ); ?>

		<?php $this->template( 'components/before' ); ?>

		<header <?php tribe_classes( $header_classes ); ?>>
			<?php $this->template( 'components/messages' ); ?>
			<?php $this->template( 'components/messages', [ 'classes' => [ 'tribe-events-header__messages--mobile' ] ] ); ?>

			<?php $this->template( 'components/breadcrumbs' ); ?>

			<?php $this->template( 'components/events-bar' ); ?>

			<?php $this->template( 'list/top-bar' ); ?>
		</header>

		<?php $this->template( 'components/filter-bar' ); ?>

		<div class="tribe-events-calendar-list">

			<?php foreach ( $events as $event ) : ?>
				<?php $this->setup_postdata( $event ); ?>

				<?php $this->template( 'list/month-separator', [ 'event' => $event ] ); ?>

				<?php $this->template( 'list/event', [ 'event' => $event ] ); ?>

			<?php endforeach; ?>

		</div>

		<?php $this->template( 'list/nav' ); ?>

		<?php $this->template( 'components/ical-link' ); ?>

		<?php $this->template( 'components/after' ); ?>

	</div>
</div>

<?php $this->template( 'components/breakpoints' ); ?>
