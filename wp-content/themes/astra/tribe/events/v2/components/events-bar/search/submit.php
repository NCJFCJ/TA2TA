<?php
/**
 * View: Events Bar Search Submit Input
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/components/events-bar/search/submit.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://evnt.is/1aiy
 *
 * @version 4.9.10
 *
 */
?>
<button
	class="tribe-common-c-btn tribe-events-c-search__button"
	type="submit"
	name="submit-bar"
>
	<?php $this->template( 'components/icons/search', [ 'classes' => [ 'tribe-events-c-events-bar__search-button-icon-svg second' ] ] ); ?>	
	<div class="search-text-event-0"><?php printf( esc_html__( 'Find %s', 'the-events-calendar' ), tribe_get_event_label_plural() ); ?></div>
</button>
