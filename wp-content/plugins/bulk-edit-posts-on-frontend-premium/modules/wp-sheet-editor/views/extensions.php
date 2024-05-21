<?php defined( 'ABSPATH' ) || exit; 
if (!is_admin()) {
	return;
}
?>
<div class="extensions-list" id="extensions-list">
	<?php
	if (!VGSE()->helpers->is_editor_page() && empty($_GET['vgse_only_inactive'])) {
		?>

		<div class="extensions-group">
			<?php
			// Display all active extensions regardless of the bundle
			VGSE()->render_extensions_group(wp_list_filter($extensions, array(
				'is_active' => true,
			)));
			?>
		</div>
		<?php
	}

	foreach ($bundles as $bundle_key => $bundle) {

		if (empty($bundle['extensions'])) {
			continue;
		}
		?>
		<h3><?php echo esc_html($bundle['name']); ?></h3>
		<div class="alert alert-green" style="position: relative;"><?php
			if (!empty($bundle['percentage_off']) && $bundle['percentage_off'] !== 'no') {
				printf(__('Promotion. %d%% OFF only today. ', 'vg_sheet_editor' ), (int) $bundle['percentage_off']);
			}
			printf(__('Get the %d extensions below for just <strike>$ %s</strike> <b>$ %s</b>', 'vg_sheet_editor' ), count($bundle['extensions']), $bundle['old_price'], $bundle['price']);
			?>	
			<?php
			if (!empty($bundle['coupon'])) {
				printf(__('<br/>Use the coupon: %s', 'vg_sheet_editor' ), esc_html($bundle['coupon']));
			}
			?>
		</div>
		<p><?php _e('<b>Money back guarantee. Buy the plugin without worries</b>. We\'ll give you a refund if the plugin doesn\'t work.', 'vg_sheet_editor' ); ?></p>
		<div class="extensions-group highlighted">
			<?php VGSE()->render_extensions_group($bundle['extensions'], $bundle); ?>
		</div>
		<?php
	}
	?>

	<h3><?php _e('Other extensions', 'vg_sheet_editor' ); ?></h3><br/>
	<?php
	VGSE()->render_extensions_group(wp_list_filter($extensions, array(
		'is_active' => false,
		'bundle' => false,
	)));
	?>
</div>
<script>

	jQuery(document).ready(function () {
		var $extensions = jQuery('.extensions-list > .wpb_wrapper');
		var maxHeight = 0;
		$extensions.each(function () {
			if (jQuery(this).height() > maxHeight) {
				maxHeight = jQuery(this).height();
			}
		});
		$extensions.height(maxHeight);
	});
</script>