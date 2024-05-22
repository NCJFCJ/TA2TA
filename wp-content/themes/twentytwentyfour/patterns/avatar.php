<?php
/**
 * Title: User avatar
 * Slug: twentytwentyfour/user-avatar
 * Categories: query
 * Keywords: user-avatar
 * Block Types: core/template-single
 */
$id = get_current_user_id();
$avatar_url = get_avatar_url($id);
?>
<img src="<?php echo $avatar_url; ?>" class="user-image" alt="User Image">