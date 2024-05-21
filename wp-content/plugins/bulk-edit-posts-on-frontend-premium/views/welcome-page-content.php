<?php
defined( 'ABSPATH' ) || exit;

$frontend_editor_instance = vgse_frontend_editor();

$frontend_editor_instance->auto_setup();
$first_editor_id = $frontend_editor_instance->_get_first_post();
$post_edit_url = admin_url('post.php?action=edit&post=' . $first_editor_id);

if (bepof_fs()->can_use_premium_code__premium_only()) {
	$post_edit_url = admin_url('edit.php?post_type=vgse_editors');
}
?>
<script>
	window.location.href = <?php echo json_encode(esc_url_raw($post_edit_url)); ?>;
</script>
<?php
exit();
?>


<p><?php _e('Thank you for installing our plugin. You can start using it in 5 minutes. Please follow these steps:', $frontend_editor_instance->textname); ?></p>

<?php
// Disable core plugin welcome page.
add_option('vgse_welcome_redirect', 'no');

$steps = array();

$missing_plugins = array();

if ($first_editor_id) {
	$steps['use_shortcode'] = '<p>' . sprintf(__('Add this shortcode to a full-width page: [vg_sheet_editor editor_id="%d"] and it works automatically.', $frontend_editor_instance->textname), (int) $first_editor_id) . '</p>';
	$steps['settings'] = '<p>' . sprintf(__('<a href="%s" target="_blank" class="button quick-settings-button">Quick Settings</a>', $frontend_editor_instance->textname), esc_url($post_edit_url)) . '</p>';
} else {
	$steps['create_first_editor'] = '<p>' . sprintf(__('Fill the settings. <a href="%s" target="_blank" class="button">Click here</a>', $frontend_editor_instance->textname), esc_url(admin_url('post-new.php?post_type=' . VGSE_EDITORS_POST_TYPE))) . '</p>';
}

$steps = apply_filters('vg_sheet_editor/frontend_editor/welcome_steps', $steps);

if (!empty($steps)) {
	echo '<ol class="steps">';
	foreach ($steps as $key => $step_content) {
		if (empty($step_content)) {
			continue;
		}
		?>
		<li class="<?php echo sanitize_html_class($key); ?>"><?php echo wp_kses_post($step_content); ?></li>		
		<?php
	}

	echo '</ol>';
}
?>	
<style>
	.steps li {
		display: none;
	}
	.steps li:first-child {
		display: block;
	}
</style>