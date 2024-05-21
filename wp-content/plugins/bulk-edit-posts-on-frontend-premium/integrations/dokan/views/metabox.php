
<div class="clear"></div>
<h3 class="wpse-toggle-head"><?php _e('Dokan', vgse_frontend_editor()->textname); ?> <i class="fa fa-chevron-down"></i></h3>
<div class="wpse-toggle-content wpse-dokan-content">

	<p><?php _e('If you fill the options below, we will add a link in the Dokan dashboard, so the vendors can open the spreadsheet from that menu.', vgse_frontend_editor()->textname); ?></p>

	<div class="field">
		<label><?php _e('Menu title', vgse_frontend_editor()->textname); ?></label>
		<input type="text" name="vgse_dokan_menu_title" value="<?php echo esc_attr(get_post_meta($post->ID, 'vgse_dokan_menu_title', true)); ?>">
	</div>
	<div class="field">
		<label><?php _e('Menu position', vgse_frontend_editor()->textname); ?></label>
		<input type="number" name="vgse_dokan_menu_position" value="<?php echo esc_attr(get_post_meta($post->ID, 'vgse_dokan_menu_position', true)); ?>">
	</div>
	<div class="field">
		<label><?php _e('Menu icon', vgse_frontend_editor()->textname); ?></label>
		<span><?php _e('Enter the name of a fontawesome icon. You can view the <a href="https://fontawesome.com/cheatsheet" target="_blank">icons list here</a>. Example: edit', vgse_frontend_editor()->textname); ?></span>
		<input type="text" name="vgse_dokan_menu_icon" value="<?php echo esc_attr(get_post_meta($post->ID, 'vgse_dokan_menu_icon', true)); ?>">
	</div>

</div>
<style>
	.wpse-dokan-content label, .wpse-dokan-content input {
		display: block;
	}
</style>