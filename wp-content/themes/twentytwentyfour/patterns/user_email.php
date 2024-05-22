<?php
/**
 * Title: User email
 * Slug: twentytwentyfour/user_email
 * Categories: query
 * Keywords: user_email
 * Block Types: core/template-single
 */
$id = get_current_user_id();
$current_user = wp_set_current_user($id);
?>
<small class="pt-1"><?php echo $current_user->user_email; ?></small>