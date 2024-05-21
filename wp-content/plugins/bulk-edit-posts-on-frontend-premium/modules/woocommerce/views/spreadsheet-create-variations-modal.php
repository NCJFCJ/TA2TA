<?php defined( 'ABSPATH' ) || exit; ?>

<!--Save changes modal-->
<div class="remodal create-variation-modal remodal-draggable" data-remodal-id="create-variation-modal" data-remodal-options="closeOnOutsideClick: false, hashTracking: false">

	<div class="modal-content">
		<form class="create-variation-form vgse-modal-form " action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
			<h3><?php _e('Variations Manager', 'vg_sheet_editor' ); ?></h3>
			<div class="vgse-variations-tool-selectors">
				<button class="button vgse-variations-tool-selector" data-target=".vgse-create-variations"><?php _e('Create variations', 'vg_sheet_editor' ); ?></button> - 
				<button class="button vgse-variations-tool-selector" data-target=".vgse-copy-variations"><?php _e('Copy variations', 'vg_sheet_editor' ); ?></button>
			</div>
			<div class="vgse-variations-tool vgse-copy-variations">
				<input type="hidden" name="vgse_variation_tool" value="copy">
				<h3><?php _e('Copy variations', 'vg_sheet_editor' ); ?> 
					<small><a href="https://wpsheeteditor.com/woocommerce-copy-variations-product/?utm_source=product&utm_medium=pro-plugin&utm_campaign=copy-variations-help" target="_blank"><?php _e('Tutorial', 'vg_sheet_editor' ); ?></a></small>
				</h3>
				<ul class="unstyled-list">
					<li>
						<label><?php _e('Copy variations and attributes from this product:', 'vg_sheet_editor' ); ?> </label>
						<select name="copy_from_product" data-remote="true" data-min-input-length="4" data-action="vgse_find_post_by_name" data-post-type="<?php echo esc_attr($post_type); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" data-placeholder="<?php _e('Select product...', 'vg_sheet_editor' ); ?>" class="select2 vgse-copy-variation-from-product">
							<option></option>
						</select>
					</li>
					<li class="individual-variations-wrapper">
						<label>
							<input type="checkbox" name="copy_individual_variations"> <?php _e('I don\'t want to copy all the variations', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e('By default we will copy all the attributes and all the variations and replace the existing variations. You can activate this option to copy individual variations and append them to the existing variations in the target product.', 'vg_sheet_editor' ); ?>">( ? )</a>
						</label>
						<br>
						<div class="individual-variations-selector-wrapper">
							<label><?php _e('Which variations do you want to copy?', 'vg_sheet_editor' ); ?> </label>
							<br>
							<select name="individual_variations[]" multiple class="select2 individual-variations-select">
								<option value="">--</option>
							</select>
						</div>
					</li>
					<li>
						<label><?php _e('The variations are for these products: ', 'vg_sheet_editor' ); ?>  <a href="#" data-wpse-tooltip="right" aria-label="<?php _e('Copy the variations into these products.', 'vg_sheet_editor' ); ?>">( ? )</a></label>
						<select name="vgse_variation_manager_source">
							<option value="">- -</option>
							<option value="individual"><?php _e('Select individual products', 'vg_sheet_editor' ); ?></option>
							<option value="search"><?php _e('Select all the products from a search', 'vg_sheet_editor' ); ?></option>
							<option value="all"><?php _e('All the products in the store', 'vg_sheet_editor' ); ?></option>
						</select>
						<label class="use-search-query-container"><input type="checkbox" value="yes"  name="use_search_query"><?php _e('I understand it will update the products from my search.', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e('For example, if you searched for posts by author = Mark using the search tool, we will update only posts with author Mark', 'vg_sheet_editor' ); ?>">( ? )</a><input type="hidden" name="filters"></label>

						<select name="<?php echo esc_html($this->post_type); ?>[]" data-remote="true" data-min-input-length="4" data-action="vgse_find_post_by_name" data-post-type="<?php echo esc_attr($post_type); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"  data-placeholder="<?php _e('Select product...', 'vg_sheet_editor' ); ?> " class="select2 individual-product-selector" multiple>
							<option></option>
						</select>
					</li>
					<li>
						<label><input type="checkbox" class="show-advanced-options"> <?php _e('Show advanced options ', 'vg_sheet_editor' ); ?></label>
						<div class="advanced-options">
							<label>
								<input type="checkbox" name="use_parent_product_price"> <?php _e('Use prices from simple product (parent) on the variations', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e('You can convert simple products into variable products. You can copy variations into a simple product and keep the prices from the simple product on the variations.', 'vg_sheet_editor' ); ?>">( ? )</a>
							</label>
							<br>
							<label>
								<input type="checkbox" name="only_copy_new_variations"> <?php _e('Only copy new variations', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e('The existing variations in the target products will not be edited nor overwritten.', 'vg_sheet_editor' ); ?>">( ? )</a>
							</label>
							<br>
							<label>
								<input type="checkbox" name="ignore_variation_image"> <?php _e('Do not copy the variation images?', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e('Enable this option if you want to copy all the variation data, except the variation image.', 'vg_sheet_editor' ); ?>">( ? )</a>
							</label> 
						</div>
						<p><?php _e('Warning: Please make a backup before copying variations in case you want to undo the changes later.', 'vg_sheet_editor' ); ?></p>
					</li>								
				</ul>
				<div class="response">
				</div>
			</div>
			<div class="vgse-variations-tool vgse-create-variations">
				<input type="hidden" name="vgse_variation_tool" value="create">
				<h3><?php _e('Create variations', 'vg_sheet_editor' ); ?> 							
					<small><a href="https://wpsheeteditor.com/woocommerce-how-to-create-product-variations-faster/?utm_source=product&utm_medium=pro-plugin&utm_campaign=create-variations-help" target="_blank"><?php _e('Tutorial', 'vg_sheet_editor' ); ?></a></small>
				</h3>
				<ul class="unstyled-list">
					<li>
						<label><?php _e('The variations are for these products: ', 'vg_sheet_editor' ); ?>  <a href="#" data-wpse-tooltip="right" aria-label="<?php _e('Copy the variations into these products.', 'vg_sheet_editor' ); ?>">( ? )</a></label>
						<select name="vgse_variation_manager_source">
							<option value="">- -</option>
							<option value="individual"><?php _e('Select individual products', 'vg_sheet_editor' ); ?></option>
							<option value="search"><?php _e('Select all the products from a search', 'vg_sheet_editor' ); ?></option>
							<option value="all"><?php _e('All the variable products', 'vg_sheet_editor' ); ?></option>
						</select>
						<label class="use-search-query-container"><input type="checkbox" value="yes"  name="use_search_query"><?php _e('I understand it will update the products from my search.', 'vg_sheet_editor' ); ?> <a href="#" data-wpse-tooltip="right" aria-label="<?php _e('For example, if you searched for posts by author = Mark using the search tool, we will update only posts with author Mark', 'vg_sheet_editor' ); ?>">( ? )</a><input type="hidden" name="filters"></label>

						<select name="<?php echo esc_attr($this->post_type); ?>[]" data-remote="true" data-min-input-length="4" data-action="vgse_find_post_by_name" data-post-type="<?php echo esc_attr($post_type); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"  data-placeholder="<?php _e('Select product...', 'vg_sheet_editor' ); ?> " class="select2 individual-product-selector" multiple>
							<option></option>
						</select>
					</li>
					<li>
						<label>
							<input type="hidden" name="link_attributes" value="no" />
							<input type="checkbox" class="link-variations-attributes" name="link_attributes" /><?php _e('Create variations for every combination of attributes?', 'vg_sheet_editor' ); ?></label>								
					</li>
					<li>
						<label><?php _e('Create this number of variations', 'vg_sheet_editor' ); ?> <input type="number" class="link-variations-number" name="number" /></label>								
					</li>
				</ul>
				<div class="response">
				</div>
			</div>

			<input type="hidden" value="vgse_create_variations" name="action">
			<input type="hidden" value="" name="wpse_job_id">
			<input type="hidden" value="<?php echo esc_attr($nonce); ?>" name="nonce">
			<input type="hidden" value="<?php echo esc_attr($post_type); ?>" name="post_type">
			<br>
			<button class="remodal-confirm" type="submit"><?php _e('Execute', 'vg_sheet_editor' ); ?> </button>
			<button data-remodal-action="confirm" class="remodal-cancel"><?php _e('Close', 'vg_sheet_editor' ); ?></button>
		</form>
	</div>
</div>