<?php defined( 'ABSPATH' ) || exit; ?>
<!--Save changes modal-->
<div class="remodal export-csv-modal" data-remodal-id="export-csv-modal" data-remodal-options="closeOnOutsideClick: false, hashTracking: false">

	<div class="modal-content">
		<?php
		$is_not_supported = apply_filters( 'vg_sheet_editor/export/is_not_supported', null, $post_type );
		if ( ! is_null( $is_not_supported ) ) {
			$message = ( is_string( $is_not_supported ) ) ? $is_not_supported : __( 'The export feature is not compatible with your website. Make sure WordPress and all the plugins and themes are up to date.' );
			?>

			<h3><?php _e( 'Export to CSV', 'vg_sheet_editor' ); ?></h3>
			<p><?php echo wp_kses_post( $message ); ?></p>
			<button data-remodal-action="confirm" class="remodal-cancel"><?php _e( 'Cancel', 'vg_sheet_editor' ); ?></button>

			<?php
		} else {
			?>
			<?php do_action( 'vg_sheet_editor/export/before_form', $post_type ); ?>
			<form class="export-csv-form vgse-modal-form " action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="POST">
				<h3><?php _e( 'Export to CSV', 'vg_sheet_editor' ); ?></h3>

				<div class="fields-to-export">
					<div class="field-wrap">
						<label><?php _e( 'What columns do you want to export?', 'vg_sheet_editor' ); ?></label>
						<select name="export_columns[]" required data-placeholder="<?php _e( 'Select column...', 'vg_sheet_editor' ); ?>" class="select2 export-columns" multiple>
							<option></option>
							<?php
							$this->render_wp_fields_export_options( $post_type );
							?>
						</select>
						<br/>
						<button class="select-active button"><?php _e( 'Select active columns', 'vg_sheet_editor' ); ?></button> 
						<button class="select-all button"><?php _e( 'Select all', 'vg_sheet_editor' ); ?></button> 
						<button class="unselect-all button"><?php _e( 'Unselect  all', 'vg_sheet_editor' ); ?></button>
					</div>

					<?php if ( empty( VGSE()->options['enable_simple_mode'] ) ) { ?>
						<div class="field-wrap">
							<label><?php _e( 'Which rows do you want to export?', 'vg_sheet_editor' ); ?></label>
							<select class="wpse-select-rows-options">
								<option value="current_search"><?php _e( 'All the rows from my current search', 'vg_sheet_editor' ); ?></option>
								<option value="selected"><?php _e( 'Rows that I selected manually with the checkbox', 'vg_sheet_editor' ); ?></option>
							</select>
						</div>

						<div class="field-wrap">
							<label class="excel-compatibility-container"><?php _e( 'What app will you use to edit this file? (optional)', 'vg_sheet_editor' ); ?><br>
								<select name="target_software">
									<?php
									foreach ( vgse_universal_sheet()->get_target_software_options() as $key => $options ) {
										foreach ( $options as $option ) {
											?>
											<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $option ); ?></option>
											<?php
										}
									}
									?>
								</select>
							</label>
						</div>
					<?php } ?>
					<?php if ( VGSE()->helpers->user_can_manage_options() ) { ?>

						<div class="field-wrap">
							<label class="save-for-later-container"><?php _e( 'Name of this export (optional)', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e( 'We will save the current search query and export settings, and you can execute this export with one click in the future using the dropdown in the export menu', 'vg_sheet_editor' ); ?>">( ? )</a></label>
							<input type="text"  name="save_for_later_name">
						</div>
					<?php } ?>					
				<?php do_action( 'vg_sheet_editor/export/after_form_fields', $post_type ); ?>
				</div>

				<?php do_action( 'vg_sheet_editor/export/before_response', $post_type ); ?>
				<div class="export-response">

				</div>

				<p class="export-actions"><a href="#" class="button pause-export button-secondary" data-action="pause"><i class="fa fa-pause"></i> <?php _e( 'Pause', 'vg_sheet_editor' ); ?></a></p>

				<input type="hidden" value="vgse_export_csv" name="action">
				<input type="hidden" value="<?php echo esc_attr( $nonce ); ?>" name="nonce">
				<input type="hidden" value="<?php echo esc_attr( $post_type ); ?>" name="post_type">
				<button type="submit" class="remodal-confirm vgse-trigger-export"><?php _e( 'Start new export', 'vg_sheet_editor' ); ?></button>
				<button data-remodal-action="confirm" class="remodal-cancel"><?php _e( 'Cancel', 'vg_sheet_editor' ); ?></button>

			</form>
		<?php } ?>
	</div>								
</div>
