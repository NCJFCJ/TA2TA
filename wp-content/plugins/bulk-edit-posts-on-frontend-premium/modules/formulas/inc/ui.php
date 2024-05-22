<?php defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'WPSE_Formulas_UI' ) ) {

	class WPSE_Formulas_UI {

		private static $instance = false;

		private function __construct() {

		}

		function init() {

			add_action( 'vg_sheet_editor/editor/before_init', array( $this, 'register_toolbar_items' ) );
			add_action( 'vg_sheet_editor/after_enqueue_assets', array( $this, 'register_assets' ) );
			add_filter( 'vg_sheet_editor/js_data', array( $this, 'add_bulk_selector_column' ) );
		}

		function add_bulk_selector_column( $args ) {
			if ( ! apply_filters( 'vg_sheet_editor/formulas/is_bulk_selector_column_allowed', true, $args ) ) {
				return $args;
			}
			$new_columns = array(
				'wpseBulkSelector' => array(
					'type'              => 'checkbox',
					'columnSorting'     => false,
					'checkedTemplate'   => true,
					'uncheckedTemplate' => false,
					'data'              => 'wpseBulkSelector',
				),
			);

			$args['columnsUnformat'] = array_merge( $new_columns, $args['columnsUnformat'] );
			$args['columnsFormat']   = array_merge( $new_columns, $args['columnsFormat'] );
			$args['startCols']++;
			$args['colWidths']  = array_merge( array( 'wpseBulkSelector' => 40 ), $args['colWidths'] );
			$args['colHeaders'] = array_merge( array( 'wpseBulkSelector' => '<input type="checkbox" class="bulk-selector" data-row="0" data-col="0" />' ), $args['colHeaders'] );
			if ( ! empty( $args['custom_handsontable_args'] ) ) {
				$args['custom_handsontable_args'] = json_decode( $args['custom_handsontable_args'], true );
			}
			if ( is_array( $args['custom_handsontable_args'] ) ) {
				if ( ! empty( $args['custom_handsontable_args']['fixedColumnsLeft'] ) ) {
					$args['custom_handsontable_args']['fixedColumnsLeft']++;
				}
				$args['custom_handsontable_args'] = json_encode( $args['custom_handsontable_args'] );
			}

			return $args;
		}

		/**
		 * Render formulas modal html
		 */
		function render_formulas_form( $current_post_type ) {
			$nonce          = wp_create_nonce( 'bep-nonce' );
			$extension      = VGSE()->helpers->get_extension_by_post_type( $current_post_type );
			$tutorials_url  = 'https://wpsheeteditor.com/blog/?utm_source=' . $current_post_type . '&utm_medium=pro-plugin&utm_campaign=formulas-top-help';
			$tutorials_url .= ( ! empty( $extension['extension_id'] ) ) ? '&vg_tax%5Bplugin%5D=' . (int) $extension['extension_id'] : '';

			if ( VGSE()->helpers->user_can_manage_options() ) {
				// Used by the send_email action
				wp_enqueue_editor();
			}
			?>


			<div class="remodal remodal4 modal-formula remodal-draggable" data-remodal-id="modal-formula" data-remodal-options="closeOnOutsideClick: false, hashTracking: false">

				<div class="modal-content">
					<h3><?php _e( 'Bulk Edit', 'vg_sheet_editor' ); ?></h3>

					<form action="" method="POST" class="vgse-modal-form be-formulas active" id="vgse-create-formula">
						<p class="formula-tool-description"><?php _e( 'Using this tool you can update thousands of rows at once', 'vg_sheet_editor' ); ?> <?php if ( is_admin() && VGSE()->helpers->user_can_manage_options() ) { ?>
								<a class="help-button" href="<?php echo esc_url( $tutorials_url ); ?>" target="_blank" ><?php _e( 'Need help? Check our tutorials', 'vg_sheet_editor' ); ?></a>
							<?php } ?></p>

						<ul class="unstyled-list">
							<li class="posts-query">
								<p><?php _e( 'Select the rows that you want to update.', 'vg_sheet_editor' ); ?> 
									<select class="wpse-select-rows-options" required>
										<option value="">--</option>
										<option value="current_search"><?php _e( 'Edit all the rows from my current search (including non-visible rows).', 'vg_sheet_editor' ); ?></option>
										<option value="new_search"><?php _e( 'I want to search rows to update and edit all the search results', 'vg_sheet_editor' ); ?></option>
										<option value="selected"><?php _e( 'Edit the rows that I selected manually in the spreadsheet.', 'vg_sheet_editor' ); ?></option>
										<option value="previously_selected"><?php _e( 'Edit the same rows that I manually selected previously', 'vg_sheet_editor' ); ?></option>
									</select>
									<button class="wpse-formula-post-query button"><?php _e( 'Make another search', 'vg_sheet_editor' ); ?></button>
								</p>
								<label class="use-search-query-container">
									<input type="hidden" name="filters">
									<input type="hidden" name="filters_found_rows">
								</label>	 
							</li>
							<li class="multiple-column-selector">
								<label><?php _e( 'What field do you want to edit?', 'vg_sheet_editor' ); ?></label>
								<select name="columns[]" required data-placeholder="<?php _e( 'Select column...', 'vg_sheet_editor' ); ?>" class="select2" multiple></select>
								<?php if ( is_admin() && VGSE()->helpers->user_can_manage_options() && empty( VGSE()->options['enable_simple_mode'] ) ) { ?>
									<br/><span class="formula-tool-missing-column-tip"><small><?php _e( 'A column is missing? <a href="#" data-remodal-target="modal-columns-visibility">Enable it</a>', 'vg_sheet_editor' ); ?></small></span>
								<?php } ?>
								<div class="column-selector hidden">
									<select name="column" required data-placeholder="<?php _e( 'Select column...', 'vg_sheet_editor' ); ?>">
										<option></option>
									</select>
								</div>
							</li>
							<li class="formula-builder">
							</li>	
							<?php if ( empty( VGSE()->options['enable_simple_mode'] ) ) { ?>
								<li class="use-slower-execution-field">
									<label><input type="checkbox" value="yes" name="use_slower_execution"><?php _e( 'Use slower execution method?', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e( 'The default way uses a faster execution method, but it might not work in all the cases. Use this option when the default way doesn\'t work or doesn\'t update all the posts.', 'vg_sheet_editor' ); ?>">( ? )</a></label>		
								</li>
							<?php } ?>
							<?php do_action( 'vg_sheet_editor/formulas/after_form_fields', $current_post_type ); ?>
							<li class="rows-to-be-updated-total">
								<?php printf( __( '%s rows will be edited.', 'vg_sheet_editor' ), '<span class="total-count"></span>' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e( 'You can select the rows for the edit in the first option of this form.', 'vg_sheet_editor' ); ?>">( ? )</a>
							</li>
							<li class="vgse-formula-actions">
													
							<textarea required class="be-txt-input formula-textarea" name="be-formula" readonly="readonly"></textarea>
								<input type="hidden" value="apply_formula" name="action">		
								<input type="hidden" value="<?php echo esc_attr( $current_post_type ); ?>" name="post_type">						
								<input type="hidden" value="<?php echo esc_attr( $nonce ); ?>" name="nonce">
								<button type="submit" class="remodal-confirm submit">
								<?php
								if ( is_admin() && VGSE()->helpers->user_can_manage_options() ) {
									_e( 'I have a database backup, Execute Now', 'vg_sheet_editor' );
								} else {
									_e( 'Execute Now', 'vg_sheet_editor' );
								}
								?>
									</button>
								<button data-remodal-action="confirm" class="remodal-cancel"><?php _e( 'Cancel', 'vg_sheet_editor' ); ?></button>
								<br/>
								<?php if ( is_admin() && VGSE()->helpers->user_can_manage_options() && empty( VGSE()->options['enable_simple_mode'] ) ) { ?>
									<div class="alert alert-blue backup-alert"><?php _e( '<p>1- Please backup your database before executing, the changes are not reversible.</p><p>2- Make sure the bulk edit settings are correct before executing.</p>', 'vg_sheet_editor' ); ?></div>
								<?php } else { ?>
									<div class="alert alert-blue backup-alert"><?php _e( 'Careful. The changes are not reversible. Please double check proceeding.', 'vg_sheet_editor' ); ?></div>
								<?php } ?>
							</li>
						</ul>
					</form>

					<div class="vgse-execute-formula" style="display: none">
						<p class="edit-running"><?php _e( 'The bulk edit is running. Please dont close this window until the process has finished.', 'vg_sheet_editor' ); ?></p>
						<?php if ( is_admin() && VGSE()->helpers->user_can_manage_options() ) { ?>
							<p class="speed-tip hidden"><?php printf( __( '<b>Tip:</b> The formula execution is too slow? <a href="%1$s" target="_blank">Save <b>more posts</b> per batch</a><br/>Are you getting errors when executing the formula? <a href="%2$s" target="_blank">Save <b>less posts</b> per batch</a>', 'vg_sheet_editor' ), esc_url( VGSE()->helpers->get_settings_page_url() ), esc_url( VGSE()->helpers->get_settings_page_url() ) ); ?></p>
						<?php } ?>

						<p class="formula-run-controls"><a href="#" class="button pause-formula-execution button-secondary" data-action="pause"><i class="fa fa-pause"></i> <?php _e( 'Pause', 'vg_sheet_editor' ); ?></a> - <button class="button go-back-formula-execution button-secondary" data-action="go-back"><i class="fa fa-angle-left"></i> <?php _e( 'Go back', 'vg_sheet_editor' ); ?></button></p>
						<div class="be-response">
							<p><?php _e( 'Processing...', 'vg_sheet_editor' ); ?></p>
						</div>
						<button data-remodal-action="confirm" class="remodal-cancel"><?php _e( 'Close', 'vg_sheet_editor' ); ?></button>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Register toolbar item
		 */
		function register_toolbar_items( $editor ) {

			$post_types = $editor->args['enabled_post_types'];
			foreach ( $post_types as $post_type ) {
				$editor->args['toolbars']->register_item(
					'run_formula',
					array(
						'type'                  => 'button',
						'allow_in_frontend'     => true,
						// Removed tooltip because there's no position that works well
						// Top: it displays it below on top of the dropdown when the header is fixed
						// At the sides, it hides the other toolbar items
						//                  'help_tooltip' => __('Edit thousands of rows at once', 'vg_sheet_editor' ),
						'content'               => __( 'Bulk Edit', 'vg_sheet_editor' ),
						'icon'                  => 'fa fa-terminal',
						'extra_html_attributes' => 'data-remodal-target="modal-formula"',
						'footer_callback'       => array( $this, 'render_formulas_form' ),
						'css_class'             => 'wpse-disable-if-unsaved-changes',
					),
					$post_type
				);

				$quick_actions = array(
					'edit'   => array(
						'label'                  => __( 'Edit', 'vg_sheet_editor' ),
						'columns'                => null,
						'allow_to_select_column' => true,
						'type_of_edit'           => null,
						'values'                 => array(),
						'wp_handler'             => false,
					),
					'delete' => array(
						'label'                  => __( 'Delete', 'vg_sheet_editor' ),
						'columns'                => array( 'post_status', 'comment_approved', 'wpse_status' ),
						'allow_to_select_column' => false,
						'type_of_edit'           => 'set_value',
						'values'                 => array( 'delete' ),
						'wp_handler'             => false,
					),
				);

				if ( $editor->provider->is_post_type ) {
					$quick_actions['remove_duplicates_by_title_latest']         = array(
						'label'                  => __( 'Remove duplicates by title (delete the latest)', 'vg_sheet_editor' ),
						'columns'                => array( 'post_title' ),
						'allow_to_select_column' => false,
						'type_of_edit'           => 'remove_duplicates',
						'values'                 => array( 'delete_latest' ),
						'wp_handler'             => false,
					);
					$quick_actions['remove_duplicates_by_title_oldest']         = array(
						'label'                  => __( 'Remove duplicates by title (delete the oldest)', 'vg_sheet_editor' ),
						'columns'                => array( 'post_title' ),
						'allow_to_select_column' => false,
						'type_of_edit'           => 'remove_duplicates',
						'values'                 => array( 'delete_oldest' ),
						'wp_handler'             => false,
					);
					$quick_actions['remove_duplicates_by_title_content_latest'] = array(
						'label'                  => __( 'Remove duplicates with same title and content (delete the latest)', 'vg_sheet_editor' ),
						'columns'                => array( 'post_title' ),
						'allow_to_select_column' => false,
						'type_of_edit'           => 'remove_duplicates_title_content',
						'values'                 => array( 'delete_latest' ),
						'wp_handler'             => false,
					);
					$quick_actions['remove_duplicates_by_title_content_oldest'] = array(
						'label'                  => __( 'Remove duplicates with same title and content (delete the oldest)', 'vg_sheet_editor' ),
						'columns'                => array( 'post_title' ),
						'allow_to_select_column' => false,
						'type_of_edit'           => 'remove_duplicates_title_content',
						'values'                 => array( 'delete_oldest' ),
						'wp_handler'             => false,
					);
				}

				// We could add support for bulk actions registered for the wp tables
				// but some of them require JS for custom handling and they would be broken
				// so we will enable specific actions through extensions as needed
				/* $actions_from_wp_list = apply_filters('bulk_actions-edit-' . $post_type, array());
				  foreach ($actions_from_wp_list as $bulk_action => $label) {
				  $quick_actions[$bulk_action] = array(
				  'label' => sprintf(__('%s (third party)', 'vg_sheet_editor' ), $label),
				  'columns' => array(),
				  'allow_to_select_column' => false,
				  'type_of_edit' => null,
				  'values' => array(),
				  'wp_handler' => true,
				  );
				  } */
				$quick_actions         = apply_filters( 'vg_sheet_editor/formulas/quick_actions', $quick_actions, $post_type, $editor );
				$quick_actions['more'] = array(
					'label'                  => __( 'More options', 'vg_sheet_editor' ),
					'columns'                => null,
					'allow_to_select_column' => true,
					'type_of_edit'           => null,
					'values'                 => array(),
					'wp_handler'             => false,
				);
				$action_links          = array();
				foreach ( $quick_actions as $bulk_action => $action ) {
					$action_links[] = '<div class="button-container"><button class="quick-bulk-action button" type="button" data-action="' . htmlentities( json_encode( $action ), ENT_QUOTES, 'UTF-8' ) . '"  data-action="' . esc_attr( $bulk_action ) . '">' . esc_html( $action['label'] ) . '</button></div>';
				}

				if ( ! empty( $action_links ) ) {
					$editor->args['toolbars']->register_item(
						'quick_bulk_edits',
						array(
							'type'              => 'html',
							'content'           => implode( '', $action_links ),
							'allow_in_frontend' => true,
							'parent'            => 'run_formula',
						),
						$post_type
					);
				}
			}
		}

		/**
		 * Register frontend assets
		 */
		function register_assets() {
			wp_enqueue_style( 'formulas_css', vgse_formulas_init()->plugin_url . 'assets/css/styles.css', '', VGSE()->version, 'all' );
			wp_enqueue_script( 'formulas_js', vgse_formulas_init()->plugin_url . 'assets/js/init.js', array(), VGSE()->version, false );

			$formulas_data = $this->get_formulas_data();
			wp_localize_script( 'formulas_js', 'vgse_formulas_data', $formulas_data );
		}
		/**
		 * Register frontend assets
		 */
		function get_formulas_data() {
			$current_post = VGSE()->helpers->get_provider_from_query_string();

			$remove_duplicates_meta_keys = array_filter( array_map( 'trim', explode( ',', VGSE()->get_option( 'allow_formula_remove_duplicates_meta_keys', '' ) ) ) );

			$formulas_data = array(
				'texts'           => array(
					'formula_required'          => __( 'The bulk edit is missing important information, please fill the form.', 'vg_sheet_editor' ),
					'action_select_label'       => __( 'Select type of edit', 'vg_sheet_editor' ),
					'action_select_placeholder' => __( '- -', 'vg_sheet_editor' ),
					'wrong_formula'             => __( 'You entered an invalid formula. Please double check or contact us.', 'vg_sheet_editor' ),
				),
				'default_actions' =>
				array(
					'remove_terms'                    => array(
						'label'                  => __( 'Remove terms from posts', 'vg_sheet_editor' ),
						'description'            => sprintf( __( 'Enter one or multiple term names separated with %s or new lines. Enter the full hierarchy like "Parent > Child" to remove subcategories', 'vg_sheet_editor' ), VGSE()->helpers->get_term_separator() ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateSetValueFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'        => 'select',
								'html_attrs' => array(
									'data-placeholder' => '--',
									'data-tags'        => true,
									'multiple'         => 'multiple',
									'class'            => 'select2',
									'data-remote'      => 'true',
									'data-action'      => 'vgse_get_taxonomy_terms',
									'data-extra_ajax_parameters' => '{"taxonomy_key": "{column_key}"}',
								),
							),
						),
					),
					'add_time'                        =>
					array(
						'label'                  => __( 'Add time to existing dates', 'vg_sheet_editor' ),
						'description'            => __( 'Add hours, days, weeks, months, or years to the existing dates.<br>If the existing date is empty, we will use the current date as a base.', 'vg_sheet_editor' ),
						'jsCallback'             => 'vgseGenerateReplaceFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'fields_relationship'    => 'AND',
						'input_fields'           =>
						array(
							array(
								'tag'        => 'input',
								'html_attrs' => array(
									'type' => 'number',
								),
								'label'      => __( 'Number', 'vg_sheet_editor' ),
							),
							array(
								'tag'        => 'select',
								'html_attrs' =>
								array(
									'multiple' => false,
								),
								'options'    => '<option value="minute">' . __( 'Minutes', 'vg_sheet_editor' ) . '</option><option value="day">' . __( 'Days', 'vg_sheet_editor' ) . '</option><option value="week">' . __( 'Weeks', 'vg_sheet_editor' ) . '</option><option value="month">' . __( 'Months', 'vg_sheet_editor' ) . '</option><option value="year">' . __( 'Years', 'vg_sheet_editor' ) . '</option>',
								'label'      => __( 'Time unit', 'vg_sheet_editor' ),
							),
						),
					),
					'reduce_time'                     =>
					array(
						'label'                  => __( 'Deduct time from existing dates', 'vg_sheet_editor' ),
						'description'            => __( 'Deduct a number of hours, days, weeks, months, or years to the existing dates.<br>If the existing date is empty, we will use the current date as a base.', 'vg_sheet_editor' ),
						'jsCallback'             => 'vgseGenerateReplaceFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'fields_relationship'    => 'AND',
						'input_fields'           =>
						array(
							array(
								'tag'        => 'input',
								'html_attrs' => array(
									'type' => 'number',
								),
								'label'      => __( 'Number', 'vg_sheet_editor' ),
							),
							array(
								'tag'        => 'select',
								'html_attrs' =>
								array(
									'multiple' => false,
								),
								'options'    => '<option value="minute">' . __( 'Minutes', 'vg_sheet_editor' ) . '</option><option value="day">' . __( 'Days', 'vg_sheet_editor' ) . '</option><option value="week">' . __( 'Weeks', 'vg_sheet_editor' ) . '</option><option value="month">' . __( 'Months', 'vg_sheet_editor' ) . '</option><option value="year">' . __( 'Years', 'vg_sheet_editor' ) . '</option>',
								'label'      => __( 'Time unit', 'vg_sheet_editor' ),
							),
						),
					),
					'math'                            =>
					array(
						'label'                  => __( 'Math operation', 'vg_sheet_editor' ),
						'description'            => __( 'Update existing value with the result of a math operation.<br>The result is rounded to the 2 nearest decimals. I.e. 3.845602 becomes 3.85', 'vg_sheet_editor' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateMathFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'         => 'input',
								'html_attrs'  => array(
									'type' => 'text',
								),
								'label'       => __( 'Math formula', 'vg_sheet_editor' ),
								'description' => __( 'Example 1: $current_value$ + 2 * 5. <br/>Example 2: $_regular_price$ * 0.7 (Set regular price - 30%)', 'vg_sheet_editor' ),
								// tooltip is an allowed parameter here
							),
						),
					),
					'decrease_by_percentage'          =>
					array(
						'label'                  => __( 'Decrease by percentage', 'vg_sheet_editor' ),
						'description'            => __( 'Decrease the existing value by a percentage.<br>The result is rounded to the 2 nearest decimals. I.e. 3.845602 becomes 3.85', 'vg_sheet_editor' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateDecreasePercentageFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'         => 'input',
								'html_attrs'  => array(
									'type' => 'number',
									'step' => '0.01',
								),
								'label'       => __( 'Decrease by', 'vg_sheet_editor' ),
								'description' => __( 'Enter the percentage number.', 'vg_sheet_editor' ),
							),
						),
					),
					'decrease_by_number'              =>
					array(
						'label'                  => __( 'Decrease by number', 'vg_sheet_editor' ),
						'description'            => __( 'Decrease the existing value by a number.<br>The result is rounded to the 2 nearest decimals. I.e. 3.845602 becomes 3.85', 'vg_sheet_editor' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateDecreaseFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'         => 'input',
								'html_attrs'  => array(
									'type' => 'number',
									'step' => '0.01',
								),
								'label'       => __( 'Decrease by', 'vg_sheet_editor' ),
								'description' => __( 'Enter the number.', 'vg_sheet_editor' ),
							),
						),
					),
					'increase_by_percentage'          =>
					array(
						'label'                  => __( 'Increase by percentage', 'vg_sheet_editor' ),
						'description'            => __( 'Increase the existing value by a percentage.<br>The result is rounded to the 2 nearest decimals. I.e. 3.845602 becomes 3.85', 'vg_sheet_editor' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateIncreasePercentageFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'         => 'input',
								'html_attrs'  => array(
									'type' => 'number',
									'step' => '0.01',
								),
								'label'       => __( 'Increase by', 'vg_sheet_editor' ),
								'description' => __( 'Enter the percentage number.', 'vg_sheet_editor' ),
							),
						),
					),
					'increase_by_number'              =>
					array(
						'label'                  => __( 'Increase by number', 'vg_sheet_editor' ),
						'description'            => __( 'Increase the existing value by a number.<br>The result is rounded to the 2 nearest decimals. I.e. 3.845602 becomes 3.85', 'vg_sheet_editor' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateIncreaseFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'         => 'input',
								'html_attrs'  => array(
									'type' => 'number',
									'step' => '0.01',
								),
								'label'       => __( 'Increase by', 'vg_sheet_editor' ),
								'description' => __( 'Enter the number.', 'vg_sheet_editor' ),
							),
						),
					),
					'set_value'                       =>
					array(
						'label'                  => __( 'Set value', 'vg_sheet_editor' ),
						'description'            => sprintf( __( 'Replace existing value with this value. <a class="formulas-action-tip-link" href="%s" target="_blank">Read more</a>', 'vg_sheet_editor' ), vgse_formulas_init()->documentation_url ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateSetValueFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag' => 'textarea',
							),
						),
					),
					'set_random_value'                =>
					array(
						'label'                  => __( 'Set random value', 'vg_sheet_editor' ),
						'description'            => __( 'Replace existing value with a random value from this list. Enter the values separated with | if you want to select from a predefined list, or enter 2 dates separated with > if you want to select any date from that range', 'vg_sheet_editor' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateSetRandomValueFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'        => 'textarea',
								// We need to set html_attrs so the JS won't add the class and add formatting to this field
								'html_attrs' => array(
									'' => '',
								),
							),
						),
					),
					'remove_everything_after'         =>
					array(
						'label'                  => __( 'Remove everything after some text', 'vg_sheet_editor' ),
						'description'            => sprintf( __( 'I.e. A value like "My great title (234343)" can become "My great title" by removing everything after " ("', 'vg_sheet_editor' ), vgse_formulas_init()->documentation_url ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateExcerptFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'   => 'input',
								'label' => __( 'Remove everything after this text', 'vg_sheet_editor' ),
							),
							array(
								'label'   => __( 'Remove the text too?', 'vg_sheet_editor' ),
								'tag'     => 'select',
								'options' => '<option value="yes">' . __( 'Yes, remove everything after the text, including the text', 'vg_sheet_editor' ) . '</option>' . '<option value="no">' . __( 'No, remove everything after the text, without including the text', 'vg_sheet_editor' ) . '</option>',
							),
						),
					),
					'remove_everything_before'        =>
					array(
						'label'                  => __( 'Remove everything before some text', 'vg_sheet_editor' ),
						'description'            => sprintf( __( 'I.e. A value like "(234343) My great title" can become "My great title" by removing everything before ") "', 'vg_sheet_editor' ), vgse_formulas_init()->documentation_url ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateExcerptFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'   => 'input',
								'label' => __( 'Remove everything before this', 'vg_sheet_editor' ),
							),
							array(
								'label'   => __( 'Remove the text from above too?', 'vg_sheet_editor' ),
								'tag'     => 'select',
								'options' => '<option value="yes">' . __( 'Yes', 'vg_sheet_editor' ) . '</option>' . '<option value="no">' . __( 'No', 'vg_sheet_editor' ) . '</option>',
							),
						),
					),
					'replace'                         =>
					array(
						'label'                  => __( 'Replace', 'vg_sheet_editor' ),
						'description'            => sprintf( __( 'Replace a word, phrase, or number with a new value. <a class="formulas-action-tip-link" href="%s" target="_blank">Read more</a>', 'vg_sheet_editor' ), vgse_formulas_init()->documentation_url ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateReplaceFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'   => 'textarea',
								'label' => __( 'Replace this', 'vg_sheet_editor' ),
							),
							array(
								'tag'   => 'textarea',
								'label' => __( 'With this', 'vg_sheet_editor' ),
							),
						),
					),
					'generate_excerpt'                =>
					array(
						'label'                  => __( 'Generate excerpt', 'vg_sheet_editor' ),
						'description'            => sprintf( __( 'If the column has a very long text, we will remove the html and shorten it to a number of words.', 'vg_sheet_editor' ), vgse_formulas_init()->documentation_url ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateExcerptFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'         => 'input',
								'html_attrs'  => array(
									'type' => 'number',
									'step' => '1',
									'min'  => 1,
								),
								'label'       => __( 'Maximum number of words', 'vg_sheet_editor' ),
								'description' => __( 'Enter the number.', 'vg_sheet_editor' ),
							),
						),
					),
					'capitalize_words'                =>
					array(
						'label'                  => __( 'Capitalize words', 'vg_sheet_editor' ),
						'description'            => sprintf( __( 'Capitalize the first letter of every word in the field. I.e. convert "my title" into "My Title".', 'vg_sheet_editor' ), vgse_formulas_init()->documentation_url ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateCapitalizeWordsFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'        => 'input',
								'html_attrs' => array(
									// We hide the input because we dont need user input
									// and the JS requires at least one html input to generate the formula
									'style' => 'display: none;',
								),
							),
						),
					),
					'clear_value'                     =>
					array(
						'label'                  => __( 'Clear value', 'vg_sheet_editor' ),
						'description'            => sprintf( __( 'Remove the existing value and leave the field empty. <a class="formulas-action-tip-link" href="%s" target="_blank">Read more</a>', 'vg_sheet_editor' ), vgse_formulas_init()->documentation_url ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateClearValueFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'        => 'input',
								'html_attrs' => array(
									// We hide the input because we dont need user input
									// and the JS requires at least one html input to generate the formula
									'style' => 'display: none;',
								),
							),
						),
					),
					'remove_duplicates'               =>
					array(
						'label'                  => __( 'Remove duplicates', 'vg_sheet_editor' ),
						'description'            => sprintf( __( '<a class="formulas-action-tip-link" href="%s" target="_blank">Read more</a>', 'vg_sheet_editor' ), 'https://wpsheeteditor.com/blog/?s=duplicate' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateClearValueFormula',
						'allowed_column_keys'    => array_merge( $remove_duplicates_meta_keys, array( 'post_title', 'post_author', 'post_content', 'post_date', 'post_excerpt', '_sku' ) ),
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'label'   => __( 'Which duplicates do you want to delete?', 'vg_sheet_editor' ),
								'tag'     => 'select',
								'options' => '<option value="delete_latest">' . __( 'Delete the newest items and keep the oldest item', 'vg_sheet_editor' ) . '</option>' . '<option value="delete_oldest">' . __( 'Delete the old items and keep the newest item', 'vg_sheet_editor' ) . '</option>',
							),
						),
					),
					'remove_duplicates_title_content' =>
					array(
						'label'                  => __( 'Remove duplicates with same title and content', 'vg_sheet_editor' ),
						'description'            => sprintf( __( '<a class="formulas-action-tip-link" href="%s" target="_blank">Read more</a>', 'vg_sheet_editor' ), 'https://wpsheeteditor.com/blog/?s=duplicate' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateClearValueFormula',
						'allowed_column_keys'    => array( 'post_title', 'post_content' ),
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'label'   => __( 'Which duplicates do you want to delete?', 'vg_sheet_editor' ),
								'tag'     => 'select',
								'options' => '<option value="delete_latest">' . __( 'Delete the newest items and keep the oldest item', 'vg_sheet_editor' ) . '</option>' . '<option value="delete_oldest">' . __( 'Delete the old items and keep the newest item', 'vg_sheet_editor' ) . '</option>',
							),
						),
					),
					'append'                          =>
					array(
						'label'                  => __( 'Append', 'vg_sheet_editor' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateAppendFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'   => 'textarea',
								'label' => __( 'Enter the value to append to the existing value.', 'vg_sheet_editor' ),
							),
						),
					),
					'prepend'                         =>
					array(
						'label'                  => __( 'Prepend', 'vg_sheet_editor' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGeneratePrependFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'        => 'input',
								'html_attrs' => array(
									'type' => 'text',
								),
								'label'      => __( 'Enter the value to prepend to the existing value.', 'vg_sheet_editor' ),
							),
						),
					),
					'custom'                          =>
					array(
						'label'                  => __( 'Custom formula', 'vg_sheet_editor' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateCustomFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'        => 'input',
								'html_attrs' => array(
									'type' => 'text',
								),
								'label'      => sprintf( __( 'Only for advanced users. <a class="formulas-action-tip-link" href="%s" target="_blank">Read more.</a>', 'vg_sheet_editor' ), vgse_formulas_init()->documentation_url ),
							),
						),
					),
					'send_email'                      =>
					array(
						'label'                  => __( 'Send email', 'vg_sheet_editor' ),
						'description'            => __( '<p>Send email notifications to users in bulk, and create advanced segments using the advanced search.</p><p>For example, you can filter a spreadsheet of customers/users/forum users/affiliates/blog commenters by country, city, state, orders count, etc and send them important emails.</p><p>We recommend that you use a SMTP plugin to improve the email deliverability and take into account the users consent and privacy laws.</p>', 'vg_sheet_editor' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGenerateSetValueFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'        => 'input',
								'html_attrs' => array(
									'type' => 'text',
								),
								'label'      => __( 'Email subject', 'vg_sheet_editor' ) . ' <a href="#" data-wpse-tooltip="right" aria-label="' . esc_attr__( 'You can use variables to insert the value of any column.', 'vg_sheet_editor' ) . '">( ? )</a>',
							),
							array(
								'tag'        => 'textarea',
								'html_attrs' => array(
									'id' => 'formula-send-email-editor',
								),
								'label'      => __( 'Email message', 'vg_sheet_editor' ) . ' <a href="#" data-wpse-tooltip="right" aria-label="' . esc_attr__( 'We recommend that you create a message that looks like it was written personally, you should add your logo, message, and email signature. You can use variables to insert the value of any column.', 'vg_sheet_editor' ) . '">( ? )</a>',
							),
							array(
								'tag'        => 'input',
								'html_attrs' => array(
									'type' => 'email',
								),
								'label'      => __( 'Reply to', 'vg_sheet_editor' ) . ' <a href="#" data-wpse-tooltip="right" aria-label="' . esc_attr__( 'People will reply to this email address', 'vg_sheet_editor' ) . '">( ? )</a>',
							),
							array(
								'label'   => __( 'Only send one email per email address?', 'vg_sheet_editor' ) . ' <a href="#" data-wpse-tooltip="right" aria-label="' . esc_attr__( 'One email per user means that if 3 rows have the same email address, only one row will trigger an email and the others will be skipped. One email per row means that if 3 rows have the same email address, we\'ll send 3 emails to the same address (one per row).', 'vg_sheet_editor' ) . '">( ? )</a>',
								'tag'     => 'select',
								'options' => '<option value="per_user">' . __( 'Send one email per user', 'vg_sheet_editor' ) . '</option>' . '<option value="per_row">' . __( 'Send one email per row', 'vg_sheet_editor' ) . '</option>',
							),
						),
					),
					'php_function'                    =>
					array(
						'label'                  => __( 'PHP Function', 'vg_sheet_editor' ),
						'fields_relationship'    => 'AND',
						'jsCallback'             => 'vgseGeneratePHPFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'input_fields'           =>
						array(
							array(
								'tag'        => 'input',
								'html_attrs' => array(
									'type' => 'text',
								),
								'label'      => sprintf( __( 'For advanced users. Enter the name of your PHP function and we will pass 2 parameters: $current_data (string with current value of the column) and $row_id, the function must accept the 2 parameters and return the modified value as a string. We will automatically save it if the value changed. <a class="formulas-action-tip-link" href="%s#php-functions" target="_blank">Read more.</a>', 'vg_sheet_editor' ), vgse_formulas_init()->documentation_url ),
							),
						),
					),
					'merge_columns'                   =>
					array(
						'label'                  => __( 'Copy from other columns', 'vg_sheet_editor' ),
						'fields_relationship'    => 'OR',
						'jsCallback'             => 'vgseGenerateMergeFormula',
						'allowed_column_keys'    => null,
						'disallowed_column_keys' => array(),
						'description'            => __( 'Copy the value of other fields into this field.<br/>Example, copy "sale price" into the "regular price" field.', 'vg_sheet_editor' ),
						'input_fields'           =>
						array(
							array(
								'tag'        => 'select',
								'html_attrs' =>
								array(
									'multiple' => false,
									'class'    => 'select2',
								),
								'options'    => '<option value="">(none)</option>',
								'label'      => __( 'Copy from this column', 'vg_sheet_editor' ),
							),
						// Removed to simplify the form and move this to a separate "type of edit" in the future
						/* array(
						  'tag' => 'textarea',
						  'label' => __('Copy from multiple columns', 'vg_sheet_editor' ),
						  'description' => __("Example: 'Articles written by \$post_author\$ on \$post_date\$' = 'Articles written by Adam on 24-12-2017'.<br/>Another example: '\$category\$-\$_regular_price\$ EUR' would be 'Videos - 25 EUR'", 'vg_sheet_editor' ),
						  ), */
						),
					),
				),
				'columns_actions' =>
				array(
					'text'                   => array(
						'set_value'                       => 'default',
						'replace'                         => 'default',
						'clear_value'                     => 'default',
						'remove_duplicates'               => 'default',
						'remove_duplicates_title_content' => 'default',
						'append'                          => 'default',
						'prepend'                         => 'default',
						'capitalize_words'                => 'default',
						'generate_excerpt'                => 'default',
						'remove_everything_after'         => 'default',
						'remove_everything_before'        => 'default',
						'merge_columns'                   => 'default',
						'set_random_value'                => 'default',
						'custom'                          => 'default',
					),
					'email'                  => array(
						'set_value'                       => 'default',
						'replace'                         => 'default',
						'clear_value'                     => 'default',
						'remove_duplicates'               => 'default',
						'remove_duplicates_title_content' => 'default',
						'append'                          => 'default',
						'prepend'                         => 'default',
						'capitalize_words'                => 'default',
						'generate_excerpt'                => 'default',
						'remove_everything_after'         => 'default',
						'remove_everything_before'        => 'default',
						'merge_columns'                   => 'default',
						'set_random_value'                => 'default',
						'custom'                          => 'default',
					),
					'date'                   => array(
						'set_value'         => 'default',
						'add_time'          => 'default',
						'reduce_time'       => 'default',
						'replace'           => 'default',
						'clear_value'       => 'default',
						'remove_duplicates' => 'default',
						'append'            => 'default',
						'prepend'           => 'default',
						'capitalize_words'  => 'default',
						'generate_excerpt'  => 'default',
						'merge_columns'     => 'default',
						'set_random_value'  => 'default',
						'custom'            => 'default',
					),
					'boton_tiny'             => array(
						'set_value'        => 'default',
						'replace'          => 'default',
						'clear_value'      => 'default',
						'append'           => 'default',
						'prepend'          => 'default',
						'capitalize_words' => 'default',
						'generate_excerpt' => 'default',
						'merge_columns'    => 'default',
						'set_random_value' => 'default',
						'custom'           => 'default',
					),
					'boton_gallery_multiple' =>
					array(
						'set_value'        =>
						array(
							'description'         => __( 'We will replace the existing media file(s) with these file(s).', 'vg_sheet_editor' ),
							'fields_relationship' => 'OR',
							'input_fields'        =>
							array(
								array(
									'tag'        => 'a',
									'html_attrs' =>
									array(
										'data-multiple' => true,
										'class'         => 'wp-media button',
									),
									'label'      => __( 'Upload the files', 'vg_sheet_editor' ),
								),
								array(
									'tag'         => 'input',
									'html_attrs'  => array(
										'type' => 'url',
									),
									'label'       => __( 'File URLs', 'vg_sheet_editor' ),
									'description' => __( 'Enter the URLs separated by commas. They can be from your own site.', 'vg_sheet_editor' ),
								),
							),
						),
						'prepend'          =>
						array(
							'description'         => __( 'We will prepend the new file(s) to the existing media file(s).', 'vg_sheet_editor' ),
							'fields_relationship' => 'OR',
							'input_fields'        =>
							array(
								array(
									'tag'        => 'a',
									'html_attrs' =>
									array(
										'data-multiple' => true,
										'class'         => 'wp-media button',
									),
									'label'      => __( 'Upload the files', 'vg_sheet_editor' ),
								),
								array(
									'tag'         => 'input',
									'html_attrs'  => array(
										'type' => 'url',
									),
									'label'       => __( 'File URLs', 'vg_sheet_editor' ),
									'description' => __( 'Enter the URLs separated by commas. They can be from your own site.', 'vg_sheet_editor' ),
								),
							),
						),
						'append'           =>
						array(
							'description'         => __( 'We will append the new file(s) to the existing media file(s).', 'vg_sheet_editor' ),
							'fields_relationship' => 'OR',
							'input_fields'        =>
							array(
								array(
									'tag'        => 'a',
									'html_attrs' =>
									array(
										'data-multiple' => true,
										'class'         => 'wp-media button',
									),
									'label'      => __( 'Upload the files', 'vg_sheet_editor' ),
								),
								array(
									'tag'         => 'input',
									'html_attrs'  => array(
										'type' => 'url',
									),
									'label'       => __( 'File URLs', 'vg_sheet_editor' ),
									'description' => __( 'Enter the URLs separated by commas. They can be from your own site.', 'vg_sheet_editor' ),
								),
							),
						),
						'replace'          =>
						array(
							'description'         => __( 'Replace a media file with other file', 'vg_sheet_editor' ),
							'fields_relationship' => 'AND',
							'input_fields'        =>
							array(
								array(
									'tag'         => 'input',
									'html_attrs'  => array(
										'type' => 'url',
									),
									'label'       => __( 'Replace these files', 'vg_sheet_editor' ),
									'description' => __( 'Enter the URLs separated by commas. They must be from your own site.', 'vg_sheet_editor' ),
								),
								array(
									'tag'         => 'input',
									'html_attrs'  => array(
										'type' => 'url',
									),
									'label'       => __( 'With these files', 'vg_sheet_editor' ),
									'description' => __( 'Enter the URLs separated by commas. They must be from your own site.', 'vg_sheet_editor' ),
								),
							),
						),
						'clear_value'      => 'default',
						'set_random_value' => 'default',
						'custom'           => 'default',
					),
					'boton_gallery'          =>
					array(
						'set_value'        =>
						array(
							'description'         => __( 'We will replace the existing media file with this file.', 'vg_sheet_editor' ),
							'fields_relationship' => 'OR',
							'input_fields'        =>
							array(
								array(
									'tag'        => 'a',
									'html_attrs' =>
									array(
										'data-multiple' => false,
										'class'         => 'wp-media button',
									),
									'label'      => __( 'Upload the file', 'vg_sheet_editor' ),
								),
								array(
									'tag'         => 'input',
									'html_attrs'  => array(
										'type' => 'text', // we don't use type=url to allow saving using filenames too
									),
									'label'       => __( 'File URL', 'vg_sheet_editor' ),
									'description' => __( 'Enter the URL. It can be an URL from your own site (Example http://site.com/wp-content/uploads/2016/01/file.jpg) or an external URL.', 'vg_sheet_editor' ),
								),
							),
						),
						'replace'          =>
						array(
							'label'               => __( 'Replace', 'vg_sheet_editor' ),
							'description'         => __( 'Replace a media file with other file', 'vg_sheet_editor' ),
							'fields_relationship' => 'AND',
							'input_fields'        =>
							array(
								array(
									'tag'         => 'input',
									'html_attrs'  => array(
										'type' => 'url',
									),
									'label'       => __( 'Replace this file', 'vg_sheet_editor' ),
									'description' => __( 'Enter the URL. It must be an URL from your own site. Example: http://site.com/wp-content/uploads/2016/01/file.jpg', 'vg_sheet_editor' ),
								),
								array(
									'tag'         => 'input',
									'html_attrs'  => array(
										'type' => 'url',
									),
									'label'       => __( 'With this file', 'vg_sheet_editor' ),
									'description' => __( 'Enter the URL. It must be an URL from your own site. Example: http://site.com/wp-content/uploads/2016/01/file.jpg', 'vg_sheet_editor' ),
								),
							),
						),
						'clear_value'      => 'default',
						'set_random_value' => 'default',
						'custom'           => 'default',
					),
					'number'                 =>
					array(
						'set_value'              =>
						array(
							'input_fields' =>
							array(
								array(
									'tag'        => 'input',
									'html_attrs' => array(
										'type' => 'number',
										'step' => '0.01',
									),
								),
							),
						),
						'clear_value'            => 'default',
						'increase_by_number'     => 'default',
						'increase_by_percentage' => 'default',
						'decrease_by_number'     => 'default',
						'decrease_by_percentage' => 'default',
						'math'                   => 'default',
						'merge_columns'          => 'default',
						'set_random_value'       => 'default',
						'custom'                 => 'default',
					),
					'post_terms'             =>
					array(
						'merge_columns'    => 'default',
						'set_value'        =>
						array(
							'description'  => __( 'We will replace the existing terms with these terms.', 'vg_sheet_editor' ),
							'input_fields' =>
							array(
								array(
									'tag'         => 'input',
									'description' => sprintf( __( 'Enter the new terms separated by %s . You can add hierarchy like parent > child > child', 'vg_sheet_editor' ), VGSE()->helpers->get_term_separator() ),
								),
							),
						),
						'replace'          =>
						array(
							'description'         => sprintf( __( 'Replace some term(s) with new term(s). <a class="formulas-action-tip-link" href="%s" target="_blank">Read more</a>', 'vg_sheet_editor' ), vgse_formulas_init()->documentation_url ),
							'fields_relationship' => 'AND',
							'input_fields'        =>
							array(
								array(
									'label'       => __( 'Replace this', 'vg_sheet_editor' ),
									'description' => __( 'You must enter the full hierarchy like parent > child', 'vg_sheet_editor' ),
									'tag'         => 'select',
									'html_attrs'  => array(
										'data-placeholder' => '--',
										'data-tags'        => true,
										'class'            => 'select2',
										'data-remote'      => 'true',
										'data-action'      => 'vgse_get_taxonomy_terms',
										'data-extra_ajax_parameters' => '{"taxonomy_key": "{column_key}"}',
									),
								),
								array(
									'label'       => __( 'With this', 'vg_sheet_editor' ),
									'description' => __( 'You must enter the full hierarchy like parent > child', 'vg_sheet_editor' ),
									'tag'         => 'select',
									'html_attrs'  => array(
										'data-placeholder' => '--',
										'data-tags'        => true,
										'class'            => 'select2',
										'data-remote'      => 'true',
										'data-action'      => 'vgse_get_taxonomy_terms',
										'data-extra_ajax_parameters' => '{"taxonomy_key": "{column_key}"}',
									),
								),
							),
						),
						'append'           =>
						array(
							'input_fields' =>
							array(
								array(
									'label'       => __( 'Terms', 'vg_sheet_editor' ),
									'description' => __( 'Enter the full hierarchy like parent > child', 'vg_sheet_editor' ),
									'tag'         => 'select',
									'html_attrs'  => array(
										'data-placeholder' => '--',
										'data-tags'        => true,
										'class'            => 'select2',
										'data-remote'      => 'true',
										'data-action'      => 'vgse_get_taxonomy_terms',
										'data-extra_ajax_parameters' => '{"taxonomy_key": "{column_key}"}',
										'multiple'         => 'multiple',
									),
								),
							),
						),
						'clear_value'      => 'default',
						'capitalize_words' => 'default',
						'set_random_value' => 'default',
						'custom'           => 'default',
					),
				),
			);

			if ( VGSE()->helpers->user_can_manage_options() ) {
				foreach ( $formulas_data['columns_actions'] as $key => $actions ) {
					$formulas_data['columns_actions'][ $key ]['php_function'] = 'default';
				}

				$formulas_data['columns_actions']['email']['send_email'] = 'default';
			}
			if ( post_type_exists( $current_post ) ) {
				$formulas_data['columns_actions']['post_terms'] = array_merge(
					array(
						'remove_terms' => 'default',
					),
					$formulas_data['columns_actions']['post_terms']
				);
			}

			return apply_filters( 'vg_sheet_editor/formulas/form_settings', $formulas_data, $current_post );
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new WPSE_Formulas_UI();
				self::$instance->init();
			}
			return self::$instance;
		}

		function __set( $name, $value ) {
			$this->$name = $value;
		}

		function __get( $name ) {
			return $this->$name;
		}

	}

}

if ( ! function_exists( 'WPSE_Formulas_UI_Obj' ) ) {

	function WPSE_Formulas_UI_Obj() {
		return WPSE_Formulas_UI::get_instance();
	}
}
WPSE_Formulas_UI_Obj();
