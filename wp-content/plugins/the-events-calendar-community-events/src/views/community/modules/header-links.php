<?php
// Don't load directly
defined( 'WPINC' ) or die;

/**
 * Header links for edit forms.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/header-links.php
 *
 * @link https://evnt.is/1ao4 Help article for Community Events & Tickets template files.
 *
 * @since  3.1
 * @since 4.8.2 Updated template link.
 *
 * @version 4.8.2
 *
 */

$post_id = get_the_ID();

$message_edit = sprintf(
	/* Translators: %s - Event (singular) */
	__( 'Edit %s', 'tribe-events-community' ),
	tribe_get_event_label_singular()
);

$message_add = sprintf(
	/* Translators: %s - Event (singular) */
	__( 'Add New %s', 'tribe-events-community' ),
	tribe_get_event_label_singular()
);

$message_view_submitted = sprintf(
	/* Translators: %s - Events (plural) */
	__( 'View Your Submitted %s', 'tribe-events-community' ),
	tribe_get_event_label_plural()
);

?>

<header class="my-events-header">
	<h2 class="my-events">
		<?php

		if ( $post_id && tribe_is_event( $post_id ) ) {
			echo esc_html( $message_edit );
		} elseif ( $post_id && tribe_is_organizer( $post_id ) ) {
			esc_html_e( 'Edit Organizer', 'tribe-events-community' );
		} elseif ( $post_id && tribe_is_venue( $post_id ) ) {
			esc_html_e( 'Edit Venue', 'tribe-events-community' );
		} else {
			echo esc_html( $message_add );
		}
		?>
	</h2>

	<?php if ( is_user_logged_in() ) : ?>
	<a
		href="<?php echo esc_url( tribe_community_events_list_events_link() ); ?>"
		class="tribe-button tribe-button-secondary"
	>
		<?php echo esc_html( $message_view_submitted ); ?>
	</a>
	<?php endif; ?>
</header>

<?php echo tribe_community_events_get_messages();
