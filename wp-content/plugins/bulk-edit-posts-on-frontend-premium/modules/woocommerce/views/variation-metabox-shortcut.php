<?php defined( 'ABSPATH' ) || exit; ?>

<style>
	.wpse-variation-metabox {
		padding: 10px;
	}
</style>
<div class="notice-success is-dismissible wpse-variation-metabox">
	<?php
	printf(__('<b>WP Sheet Editor:</b> You can view all these variations and edit them all at once in our spreadsheet. <a href="%s" target="_blank">Open in a spreadsheet</a>', 'vg_sheet_editor' ), esc_url($spreadsheet_url));
	?>
</div>