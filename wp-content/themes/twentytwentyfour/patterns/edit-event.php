<?php
/**
 * Title: Edit Event
 * Slug: twentytwentyfour/edit-event
 * Keywords: event, edit
 * Categories: event, text
 * 
 */
?>

<?php 
//get_header();
$event_to_edit = isset($_GET['post_id']) ? $_GET['post_id'] : NULL;

echo do_shortcode( '[tribe_community_events view="edit_event" id=' . $event_to_edit .']');

?>