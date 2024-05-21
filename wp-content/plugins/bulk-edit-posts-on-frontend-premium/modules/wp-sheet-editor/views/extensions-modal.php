<?php defined( 'ABSPATH' ) || exit; ?>
<div class="remodal remodal-extensions" data-remodal-id="modal-extensions" data-remodal-options="closeOnOutsideClick: false">

	<div class="modal-content">
		<h3><?php _e('Extend the spreadsheet', 'vg_sheet_editor' ); ?></h3>		
		<?php VGSE()->render_extensions_list(); ?>
		<div class="clear"></div>
	</div>
	<br>
	<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Close', 'vg_sheet_editor' ); ?></button>
</div>