<?php
/**
 * Title: Current User
 * Slug: twentytwentyfour/current_user
 * Categories: query
 * Keywords: current_user
 * Block Types: core/template-single
 */

$id = get_current_user_id();
$current_user = wp_set_current_user($id);
// echo '<pre>';
// var_dump(esc_html($current_user->user_firstname));
// echo esc_html($current_user->user_firstname . ' ' . $current_user->user_lastname);
// echo '<br>';
// echo '</pre>';die();
?>
<span class="d-none d-lg-inline-block"><?php echo esc_html($current_user->user_firstname . ' ' . $current_user->user_lastname); ?></span>