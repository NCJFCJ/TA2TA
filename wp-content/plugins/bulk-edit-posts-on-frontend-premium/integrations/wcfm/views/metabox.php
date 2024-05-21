
<div class="clear"></div>
<h3 class="wpse-toggle-head"><?php _e('WCFM Marketplace', vgse_frontend_editor()->textname); ?> <i class="fa fa-chevron-down"></i></h3>
<div class="wpse-toggle-content wpse-wcmp-content">

	<p><?php _e('If you fill the options below, we will add a link in the WCMP dashboard, so the vendors can open the spreadsheet from that menu.', vgse_frontend_editor()->textname); ?></p>

	<div class="field">
		<label><?php _e('Menu title', vgse_frontend_editor()->textname); ?></label>
		<input type="text" name="vgse_wcmp_menu_title" value="<?php echo esc_attr(get_post_meta($post->ID, 'vgse_wcmp_menu_title', true)); ?>">
	</div>

</div>
<style>
	.wpse-wcmp-content label, .wpse-wcmp-content input {
		display: block;
	}
</style>