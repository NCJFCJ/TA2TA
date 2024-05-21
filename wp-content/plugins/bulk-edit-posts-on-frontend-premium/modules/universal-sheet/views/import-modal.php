<?php defined( 'ABSPATH' ) || exit; ?>
<!--Save changes modal-->
<div class="remodal import-csv-modal" data-remodal-id="import-csv-modal" data-remodal-options="closeOnOutsideClick: false, hashTracking: false">

	<div class="modal-content">
		<?php do_action( 'vg_sheet_editor/import/before_form', $post_type ); ?>
		<?php
		$is_not_supported = apply_filters( 'vg_sheet_editor/import/is_not_supported', null, $post_type );
		if ( ! is_null( $is_not_supported ) ) {
			$message = ( is_string( $is_not_supported ) ) ? $is_not_supported : __( 'The import feature is not compatible with your website. Make sure WordPress and all the plugins and themes are up to date.' );
			?>

			<h3><?php _e( 'Import csv', 'vg_sheet_editor' ); ?></h3>
			<p><?php echo wp_kses_post( $message ); ?></p>
			<button data-remodal-action="confirm" class="remodal-cancel"><?php _e( 'Cancel', 'vg_sheet_editor' ); ?></button>

			<?php
		} else {
			?>
			<?php do_action( 'vg_sheet_editor/import/before_form', $post_type ); ?>
			<form class="import-csv-form vgse-modal-form " id="import-csv-form" action="<?php echo esc_url( admin_url( 'admin.php?page=vgse_import_page' ) ); ?>" method="POST">
				<ul>
					<li class="step current">
						<h3><?php _e( 'Import csv', 'vg_sheet_editor' ); ?></h3>
						<?php do_action( 'vg_sheet_editor/import/before_data_sources', $post_type ); ?>
						<label><?php _e( 'Source', 'vg_sheet_editor' ); ?></label>								
						<select name="source" class="source">
						<?php
						foreach ( vgse_universal_sheet()->get_import_sources_options() as $key => $option ) {
							?>
							<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $option ); ?></option>
							<?php
						}
						?>
						</select>

						<div class="data-input csv_upload">
							<label><?php _e( 'CSV file', 'vg_sheet_editor' ); ?> </label>
							<input type="file" name="local_file" class="data" id="vgse-import-local-file"  /> 
							<button class="button button-primary button-primario vgse-upload-csv-file next-step step-nav"  data-type="local"><?php _e( 'Next', 'vg_sheet_editor' ); ?> <i class="fa fa-chevron-right"></i></button>
						</div>
						<div class="data-input csv_url">
							<label><?php _e( 'File URL', 'vg_sheet_editor' ); ?> </label>								
							<input type="text" name="file_url" placeholder="File URL" class="data" />
							<button class="button button-primary button-primario vgse-upload-csv-file next-step step-nav" data-type="url"><?php _e( 'Next', 'vg_sheet_editor' ); ?> <i class="fa fa-chevron-right"></i></button>
						</div>
						<div class="data-input paste">
							<label><?php _e( 'Copy and Paste into the spreadsheet below', 'vg_sheet_editor' ); ?> </label>
							<p>This is not recommended for large amounts of data.</p>								
							<div id="handsontable-paste"></div>
							<button class="button button-primary button-primario vgse-upload-csv-file next-step step-nav" data-type="json"><?php _e( 'Next', 'vg_sheet_editor' ); ?> <i class="fa fa-chevron-right"></i></button>
						</div>
						<div class="data-input server_file">
							<label><?php _e( 'CSV file location', 'vg_sheet_editor' ); ?> <a href="" data-wpse-tooltip="right" aria-label="<?php echo esc_attr( sprintf( __( 'You must enter a file name in this field (not full path) and upload the file to the folder %s', 'vg_sheet_editor' ), preg_replace( '/^.+(\/wp-content.+)$/', '$1', WPSE_CSV_API_Obj()->imports_dir ) ) ); ?>">( ? )</a> </label>
							<p>
							<?php
							$parts = explode( basename( WP_CONTENT_DIR ), WPSE_CSV_API_Obj()->imports_dir );
							echo esc_html( '/' . basename( WP_CONTENT_DIR ) . end( $parts ) );
							?>
							<input type="text" name="server_file" class="data" /></p>							 
							<button class="button button-primary button-primario vgse-upload-csv-file next-step step-nav"  data-type="server_file"><?php _e( 'Next', 'vg_sheet_editor' ); ?> <i class="fa fa-chevron-right"></i></button>
						</div> 
						<?php do_action( 'vg_sheet_editor/import/after_data_sources_fields', $post_type ); ?>						

						<label class=""><input type="checkbox" name="enable_advanced_source_options" class="toggle-advanced-options"> <?php _e( 'Show advanced options', 'vg_sheet_editor' ); ?></label>
						<div class="advanced-options" style="display: none">
							<div class="field">
								<label><?php _e( 'Separator', 'vg_sheet_editor' ); ?></label><br>
								<input type="text" name="separator" class="separator" value="," />
							</div>
						</div>
						<?php if ( empty( VGSE()->options['enable_simple_mode'] ) ) { ?>
							<p><?php printf( __( 'Tip. You can use the "export" tool to download a CSV and see the available columns and format.<br>You can read our <a href="%s" target="_blank">documentation here</a>.', 'vg_sheet_editor' ), VGSE()->get_site_link( 'https://wpsheeteditor.com/blog/?s=import', 'importer-documentation' ) ); ?></p>
						<?php } ?>

						<?php do_action( 'vg_sheet_editor/import/after_data_sources', $post_type ); ?>
					</li>
					<li class="map-columns step">
						<h3><?php _e( 'Select columns to import', 'vg_sheet_editor' ); ?></h3>
						<p class="one-column-detected-tip alert alert-blue"><?php printf( __( 'Important. We only detected one column in the CSV file. If this is incorrect, follow <a href="%s" target="_blank">these steps</a> to fix it', 'vg_sheet_editor' ), 'https://wpsheeteditor.com/documentation/faq/#1572924330879-e05ed559-f740234' ); ?></p>
						<p class="import-auto-map-notice"><?php _e( 'We automatically detected all the columns.', 'vg_sheet_editor' ); ?><br/><button class="button  next-step step-nav"><?php _e( 'Import all the columns', 'vg_sheet_editor' ); ?></button> <?php _e( 'or', 'vg_sheet_editor' ); ?> <button class="button import-map-select-columns"><?php _e( 'Select individual columns to import', 'vg_sheet_editor' ); ?></button></p>
						<?php if ( empty( VGSE()->options['enable_simple_mode'] ) ) { ?>
							<p><?php _e( 'Tip. If you edited information from this site, you should import the columns edited and record_id. Don\'t import columns that weren\'t modified', 'vg_sheet_editor' ); ?></p>
						<?php } ?>
						<p class="import-column-bulk-actions"><span class="csv-column-list-header"></span><span class="wp-column-list-header"><select><option value=""><?php _e( 'Bulk actions', 'vg_sheet_editor' ); ?></option><option value="unselect"><?php _e( 'Unselect all columns', 'vg_sheet_editor' ); ?></option></select></span></p>
						<p class="import-column-list-headers"><span class="csv-column-list-header"><?php _e( 'CSV Column', 'vg_sheet_editor' ); ?></span><span class="wp-column-list-header"><?php _e( 'WordPress field', 'vg_sheet_editor' ); ?></span></p>
						<div class="map-template hidden">
							<span class="csv-column-name-wrapper"><span class="csv-column-name-text"></span><small class="csv-column-name-example"><?php _e( 'Example: ', 'vg_sheet_editor' ); ?></small></span>
							<span class="dashicons dashicons-dismiss wpse-ignore-column-cross"></span> 
							<select class="" name="sheet_editor_column[]">
							<?php
							$this->render_wp_fields_import_options( $post_type );
							?>
						</select>
							<input class="csv-column-name-value" name="source_column[]" type="hidden" />
						</div>	
						<label class="remember-column-mapping"><input type="checkbox" name="remember_column_mapping"> <?php _e( 'Remember this column mapping configuration?', 'vg_sheet_editor' ); ?></label>
						<button class="button button-primary button-primario prev-step step-nav" ><i class="fa fa-chevron-left"></i> <?php _e( 'Previous', 'vg_sheet_editor' ); ?></button>
						<button class="button button-primary button-primario next-step step-nav" ><?php _e( 'Next', 'vg_sheet_editor' ); ?> <i class="fa fa-chevron-right"></i></button>
					</li>
					<li class="write-type step">
						<h3><?php _e( 'Do you want to update or create items?', 'vg_sheet_editor' ); ?></h3>
						<select name="writing_type" required>
							<option value="">- -</option>
							<option value="both"><?php _e( 'Create new items and update existing items', 'vg_sheet_editor' ); ?></option>
							<option value="all_new"><?php _e( 'Import all rows as new', 'vg_sheet_editor' ); ?></option>
							<option value="only_new"><?php _e( 'Only create new items, ignore existing items', 'vg_sheet_editor' ); ?></option>
							<option value="only_update"><?php _e( 'Update existing items, ignore new items', 'vg_sheet_editor' ); ?></option>
						</select>	
						<div class="field-find-existing-columns">						
							<h4><?php _e( 'How do we find existing items to update?', 'vg_sheet_editor' ); ?></h4>

							<?php do_action( 'vg_sheet_editor/import/before_existing_wp_check_message', $post_type ); ?>

							<p class="wp-check-message"><?php _e( 'We find rows with the same value in the CSV Field and the WP Field.<br>I.e. Products with same SKU or ID.', 'vg_sheet_editor' ); ?></p>

							<p class="wp-field-requires-ignored-column alert alert-blue"><?php _e( 'You selected a column from the CSV file below but the column is not <br>being imported. Please go to the previous step and select the column to be imported.<br>Hypothetical example, if you want to update existing products with same ID, you need to import the ID column otherwise we don\'t have the IDs to find them.', 'vg_sheet_editor' ); ?></p>
							<div class="field-wrapper">
								<label><?php _e( 'CSV Field', 'vg_sheet_editor' ); ?></label>
								<select name="existing_check_csv_field[]" class="select2 existing-check-csv-field">
									<option value="">- -</option>
								</select>	
							</div>
							<div class="field-wrapper">
								<label><?php _e( 'WordPress Field', 'vg_sheet_editor' ); ?></label>
								<select name="existing_check_wp_field[]" class="select2 existing-check-wp-field">
									<option value="">- -</option>
									<?php
									$wp_columns_to_search = implode(
										apply_filters(
											'vg_sheet_editor/import/wp_check/available_columns_options',
											VGSE()->helpers->get_post_type_columns_options(
												$post_type,
												array(
													'conditions' => array(
														'allow_search_during_import' => true,
													),
												),
												false,
												false
											),
											$post_type
										)
									);
									echo $wp_columns_to_search;
									if ( post_type_exists( $post_type ) ) {
										echo '<option value="post_name__in">' . __( 'Full URL', 'vg_sheet_editor' ) . '</option>';
									}
									?>
								</select>	
							</div>
							<!--Deactivated temporarily, I don't think this option is being used and it causes confusion-->
							<!--<div class="field-wrapper">
								<label><?php _e( 'Field 2: CSV Field', 'vg_sheet_editor' ); ?></label>
								<select name="existing_check_csv_field[]" class="select2 existing-check-csv-field">
									<option value="">- -</option>
								</select>	
							</div>
							<div class="field-wrapper">
								<label><?php _e( 'Field 2: WordPress Field', 'vg_sheet_editor' ); ?></label>
								<select name="existing_check_wp_field[]" class="select2">
									<option value="">- -</option>
							<?php echo $wp_columns_to_search; ?>
								</select>	
							</div>-->
						</div>
						<button class="button button-primary button-primario prev-step step-nav" ><i class="fa fa-chevron-left"></i> <?php _e( 'Previous', 'vg_sheet_editor' ); ?></button>								
						<button class="button button-primary button-primario next-step step-nav" ><?php _e( 'Next', 'vg_sheet_editor' ); ?> <i class="fa fa-chevron-right"></i></button>
					</li>
					<li class="preview-step step">
						<h3><?php _e( 'Final step', 'vg_sheet_editor' ); ?></h3>
						<p><?php _e( '1. Are we reading the file properly? Here is a preview of the first 5 rows from the file.', 'vg_sheet_editor' ); ?></p>
						<div id="hot-preview"></div>
						<p><?php _e( '2. Please make a backup before executing the import, so you can revert in case you used wrong settings or the file was wrong. The import will save the information directly.', 'vg_sheet_editor' ); ?></p>


						<label class=""><input type="checkbox" name="enable_advanced_source_options" class="toggle-advanced-options"> <?php _e( 'Show advanced options', 'vg_sheet_editor' ); ?></label>
						<div class="advanced-options" style="display: none">
							<div class="field">
								<label><?php _e( 'Number of rows to process per batch:', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e( 'Leave empty to use the global settings.', 'vg_sheet_editor' ); ?>">( ? )</a></label><br>
								<input type="number" name="per_page" class="per-page"/>								
							</div>
							<div class="field">
								<label><?php _e( 'Start from row number:', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e( 'If you stop an import to edit your CSV file or change the import speed, you can start a new import and continue from where you left off.', 'vg_sheet_editor' ); ?>">( ? )</a></label><br>
								<input type="number" name="start_row" class="skip-rows"/>								
							</div>
							<div class="field">
								<label><input type="checkbox" name="decode_quotes" class="decode-quotes"/> <?php _e( 'Decode quotes?', 'vg_sheet_editor' ); ?></label>								
							</div>
							<div class="field">
								<label><input type="checkbox" name="auto_retry_failed_batches" class="auto-retry-failed-batches"/> <?php _e( 'Auto retry failed batches?', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e( 'We import the file in batches (i.e. 4 rows every few seconds). When one batch fails, we normally pause the import and ask you if you want to retry or cancel the import. Select this option to auto retry. Careful, you need to select the option to update existing rows in step 3 of the import, so we can retry and skip what was imported successfully and only retry what failed, if you dont select the option to update in step 3 of the import, every retry might duplicate some previously imported rows.', 'vg_sheet_editor' ); ?>">( ? )</a></label>								
							</div>
							<?php if ( VGSE()->helpers->get_current_provider()->is_post_type ) { ?>
								<div class="field">
									<label><input type="checkbox" name="pending_post_if_image_failed" class="pending-post-if-image-failed"/> <?php _e( 'Set post status to "pending" if featured image saving failed?', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e( 'This option works only if you are importing the featured image column and saving the featured image failed.', 'vg_sheet_editor' ); ?>">( ? )</a></label>								
								</div>
							<?php } ?>
							<?php do_action( 'vg_sheet_editor/import/after_advanced_options', $post_type ); ?>
						</div>
						<?php do_action( 'vg_sheet_editor/import/after_final_step_content', $post_type ); ?>
						<br>
						<button class="button button-primary button-primario prev-step step-nav step-nav" ><i class="fa fa-chevron-left"></i> <?php _e( 'Previous', 'vg_sheet_editor' ); ?></button>
					</li>
				</ul>
				<input type="hidden" name="import_file" class="import-file">
				<input type="hidden" name="total_rows" class="total-rows">
				<input type="hidden" name="vgse_plain_mode" value="yes">
				<input type="hidden" name="import_type" value="csv">
				<input type="hidden" name="wpse_job_id" value="">
				<input type="hidden" name="action" value="vgse_import_csv">
				<input type="hidden" name="vgse_import" value="yes">
				<input type="hidden" value="<?php echo esc_attr( $nonce ); ?>" name="nonce">
				<input type="hidden" value="<?php echo esc_attr( $post_type ); ?>" name="post_type">
				<button type="submit" class="remodal-confirm"><?php _e( 'The preview is fine, start import', 'vg_sheet_editor' ); ?></button>
				<button data-remodal-action="confirm" class="remodal-cancel"><?php _e( 'Cancel import', 'vg_sheet_editor' ); ?></button>
			</form>
			<div class="import-step">
				<h3><?php _e( 'Importing', 'vg_sheet_editor' ); ?></h3>


				<?php do_action( 'vg_sheet_editor/import/before_response', $post_type ); ?>
				<div class="import-response">

				</div>

				<p class="view-log"><a href="" class="button" target="_blank"><?php _e( 'View log', 'vg_sheet_editor' ); ?></a></p>
				<p class="import-actions"><a href="#" class="button pause-import button-secondary" data-action="pause"><i class="fa fa-pause"></i> <?php _e( 'Pause', 'vg_sheet_editor' ); ?></a></p>
				<button data-remodal-action="confirm" class="remodal-cancel"><?php _e( 'Close', 'vg_sheet_editor' ); ?></button>
			</div >
			
			<?php do_action( 'vg_sheet_editor/import/after_content', $post_type ); ?>
		<?php } ?>
	</div>								
</div>
