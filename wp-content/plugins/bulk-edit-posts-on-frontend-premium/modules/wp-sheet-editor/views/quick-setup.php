<?php
/**
 * Template used for the quick setup page.
 */
defined( 'ABSPATH' ) || exit;

$nonce = wp_create_nonce('bep-nonce');

$current_post_type = VGSE()->helpers->get_provider_from_query_string();
update_option('vgse_disable_quick_setup', true);

$welcome_url = apply_filters('vg_sheet_editor/welcome_url', null);
if ($welcome_url) {
	?>
	<script>window.location.href = '<?php echo esc_url($welcome_url); ?>';</script>
	<?php
	exit();
}
?>
<style>
	#setting-error-tgmpa {
		display: none;
	}
</style>
<div class="remodal-bg quick-setup-page-content" id="vgse-wrapper" data-nonce="<?php echo esc_attr($nonce); ?>">
	<div class="">
		<div class="">
			<h2 class="hidden"><?php _e('Sheet Editor', 'vg_sheet_editor' ); ?></h2>
			<a href="https://wpsheeteditor.com/?utm_source=wp-admin&utm_medium=quick-setup-logo" target="_blank"><img src="<?php echo esc_url(VGSE()->logo_url); ?>" class="vg-logo"></a>
		</div>
		<div class="steps-container">
			<ul class="progressbar">
				<li class="active">Setup</li>
				<li>Enable modules</li>
				<li>Start editing</li>
			</ul>
		</div>
		<div class="setup-screen setup-step active">
			<h2><?php _e('Welcome to WP Sheet Editor', 'vg_sheet_editor' ); ?></h2>
			<?php do_action('vg_sheet_editor/quick_setup_page/quick_setup_screen/before_content'); ?>
			<p><?php _e('You can start using the spreadsheet editor in just 5 minutes. Please follow these steps.', 'vg_sheet_editor' ); ?></p>

			<?php
			ob_start();
			require 'post-types-form.php';
			$post_types_form = ob_get_clean();
			$steps = array();
			$steps['enable_post_types'] = '<p>' . __('Select the information that you want to edit with the spreadsheet editor.', 'vg_sheet_editor' ) . '</p>' . $post_types_form;

			$steps = apply_filters('vg_sheet_editor/quick_setup_page/setup_steps', $steps);

			if (!empty($steps)) {
				echo '<ol class="steps">';
				foreach ($steps as $key => $step_content) {
					?>
					<li><?php echo $step_content; // WPCS: XSS ok.   ?></li>		
					<?php
				}

				echo '</ol>';
			}
			?>

			<button class="button button-primary button-primary save-all-trigger"><?php _e('Save and continue', 'vg_sheet_editor' ); ?></button>

			<?php do_action('vg_sheet_editor/quick_setup_page/quick_setup_screen/after_content'); ?>
		</div>

		<div class="modules setup-step">
			<h2><?php _e('Available components', 'vg_sheet_editor' ); ?></h2>
			<p><?php _e('The spreadsheet editor is very powerful and it has a lot of features. In this step you can enable the features that you need.', 'vg_sheet_editor' ); ?></p>			

			<a class="button button-primary button-primary" href="#extensions-list">Enable Advanced Spreadsheet Features</a> - <button class="button save-all-trigger"><i class="fa fa-chevron-right"></i> Continue with the Basic Spreadsheet Now</button>
			<hr/>
			<?php VGSE()->render_extensions_list();
			?>	
			<button class="button button-primary button-primary save-all-trigger"><?php _e('Continue', 'vg_sheet_editor' ); ?></button> - 
			<button class="button step-back"><?php _e('Go back', 'vg_sheet_editor' ); ?></button>
		</div>
		<div class="usage-screen setup-step">
			<h2><?php _e('The Spreadsheet is ready.', 'vg_sheet_editor' ); ?></h2>
			<div class="post-types-enabled">
				<?php
				$post_types = VGSE()->helpers->get_enabled_post_types();

				if (!empty($post_types)) {
					foreach ($post_types as $key => $post_type_name) {
						if (is_numeric($key)) {
							$key = $post_type_name;
						}
						?>
						<a class="button post-type-<?php echo esc_attr($key); ?>" href="<?php
						echo VGSE()->helpers->get_editor_url($key);
						?>"><?php _e('Edit ' . $post_type_name . 's', 'vg_sheet_editor' ); ?></a>		
						   <?php
					   }
				   }
				   ?>
			</div>
			<hr>
			<a class="button settings-button" href="<?php echo esc_url(VGSE()->helpers->get_settings_page_url()); ?>"><i class="fa fa-cog"></i> <?php _e('Settings', 'vg_sheet_editor' ); ?></a>
			<button class="button step-back"><?php _e('Go back', 'vg_sheet_editor' ); ?></button>

			<?php do_action('vg_sheet_editor/quick_setup_page/usage_screen/after_content'); ?>
		</div>


		<div class="clear"></div>	
		<hr/>
		<p><?php _e('Tip. We can help you with the spreadsheet setup. Get instant help in the live chat during business hours', 'vg_sheet_editor' ); ?> <a class="button help-button" href="<?php echo esc_url(VGSE()->get_support_links('contact_us', 'url', 'quick-setup-bottom')); ?>" target="_blank" ><i class="fa fa-envelope"></i> <?php _e('Need help? Contact us', 'vg_sheet_editor' ); ?></a></p>

		<?php do_action('vg_sheet_editor/quick_setup_page/after_content'); ?>
	</div>
</div>
			<?php
		