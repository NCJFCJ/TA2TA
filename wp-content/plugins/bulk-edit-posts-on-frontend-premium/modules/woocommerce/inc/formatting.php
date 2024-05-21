<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPSE_WC_Products_Data_Formatting' ) ) {

	class WPSE_WC_Products_Data_Formatting {

		private static $instance                = false;
		var $wc_lookuptable_after_save_synced   = array();
		var $wc_subscriptions_after_save_synced = array();
		var $wc_after_save_prices_synced        = array();

		private function __construct() {

		}

		function init() {

			add_action( 'vg_sheet_editor/save_rows/after_saving_cell', array( $this, 'product_cell_updated_on_spreadsheet' ), 10, 7 );
			add_action( 'vg_sheet_editor/save_rows/after_saving_post', array( $this, 'product_updated_on_spreadsheet' ), 10, 4 );
			add_action( 'vg_sheet_editor/formulas/execute_formula/after_execution_on_field', array( $this, 'product_updated_with_formula' ), 10, 8 );
			add_filter( 'vg_sheet_editor/load_rows/output', array( $this, 'format_sale_dates' ), 10, 3 );
			add_filter( 'vg_sheet_editor/provider/post/update_item_meta', array( $this, 'filter_cell_data_for_saving' ), 10, 3 );

			if ( version_compare( WC()->version, '3.9.0' ) >= 0 ) {
				add_action( 'vg_sheet_editor/formulas/fast_post_deleted', array( $this, 'remove_products_from_lookup_table' ), 10, 2 );
			}

			add_filter( 'vg_sheet_editor/formulas/early_validation', array( $this, 'notify_if_regular_price_hidden_before_formula' ), 10, 6 );
			add_filter( 'vg_sheet_editor/formulas/variables/column_value_before_formula_replacement', array( $this, 'formula_fix_convert_terms_to_attribute_format' ), 10, 6 );
			add_filter( 'vg_sheet_editor/formulas/sql_execution/can_execute', array( $this, 'disable_sql_formulas_when_converting_to_product' ), 10, 6 );
		}

		function disable_sql_formulas_when_converting_to_product( $allowed, $formula, $column, $post_type, $spreadsheet_columns, $raw_form_data ) {
			$is_set_value = $raw_form_data['action_name'] === 'set_value' && $raw_form_data['formula_data'][0] === 'product';
			$is_replace   = $raw_form_data['action_name'] === 'replace' && count( $raw_form_data['formula_data'] ) === 2 && $raw_form_data['formula_data'][1] === 'product';
			if ( $column['key'] === 'post_type' && ( $is_replace || $is_set_value ) ) {
				$allowed = false;

			}
			return $allowed;
		}

		function formula_fix_convert_terms_to_attribute_format( $column_value, $formula, $column_key, $post_id, $cell_args, $post_type ) {
			// If copying from a global attribute column into a custom attribute
				// column, replace the term separator with the attribute separator used by WC
			if ( $post_type === 'product' && strpos( $column_key, 'pa_' ) === 0 && strpos( $cell_args['key'], 'wpseca' ) === 0 && class_exists( 'WooCommerce' ) ) {
				$separator    = VGSE()->helpers->get_term_separator();
				$column_value = str_replace( $separator, ' ' . WC_DELIMITER, $column_value );
			}

			return $column_value;
		}

		function notify_if_regular_price_hidden_before_formula( $error, $raw_form_data, $column_settings, $spreadsheet_columns, $post_type, $formula ) {
			$visible_columns = VGSE()->helpers->get_provider_columns( $post_type );
			if ( $post_type === VGSE()->WC->post_type && strpos( $formula, '=MATH(' ) !== false && strpos( $formula, '$_regular_price$' ) !== false && ! isset( $visible_columns['_regular_price'] ) ) {
				$error = new WP_Error( 'vgse', __( 'The "Regular price" column is hidden now. Please enable it in the columns manager before running this bulk edit.', 'vg_sheet_editor' ) );
			}
			return $error;
		}

		function remove_products_from_lookup_table( $posts, $post_type ) {
			global $wpdb;
			if ( $post_type !== VGSE()->WC->post_type ) {
				return;
			}
			$post_ids                 = wp_list_pluck( $posts, 'ID' );
			$where_query_placeholders = implode( ', ', array_fill( 0, count( $post_ids ), '%d' ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wc_product_meta_lookup WHERE product_id IN ($where_query_placeholders)", $post_ids ) );
		}

		function filter_cell_data_for_saving( $new_value, $id, $key ) {
			if ( get_post_type( $id ) !== VGSE()->WC->post_type ) {
				return $new_value;
			}

			if ( $key === '_featured' ) {
				if ( $new_value === 'no' ) {
					wp_remove_object_terms( $id, 'featured', 'product_visibility' );
				} elseif ( $new_value ) {
					wp_set_object_terms( $id, 'featured', 'product_visibility', true );
				}
				delete_transient( 'wc_featured_products' );
			}
			if ( $key === '_stock' && ! empty( $new_value ) ) {
				update_post_meta( $id, '_manage_stock', 'yes' );
			}

			return $new_value;
		}

		/**
		 * Compatibility for the WooCommerce Subscriptions plugin, it requires prices synchronization
		 */
		function _sync_subscription_prices( $product_id, $modified_data = array() ) {
			if ( ! class_exists( 'WC_Subscriptions' ) ) {
				return;
			}
			$product_lookup_keys = array( '_price', '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to' );
			if ( array_intersect( $modified_data, $product_lookup_keys ) && ! in_array( $product_id, $this->wc_subscriptions_after_save_synced ) ) {

				$_POST['variable_regular_price'] = get_post_meta( $product_id, '_regular_price', true );
				// Sync the min variation price
				if ( WC_Subscriptions::is_woocommerce_pre( '3.0' ) ) {
					$variable_subscription = wc_get_product( $product_id );
					$variable_subscription->variable_product_sync();
				} else {
					WC_Product_Variable::sync( $product_id );
				}

				$this->wc_subscriptions_after_save_synced[] = $product_id;
				$this->clear_wc_caches( $product_id );
			}
		}

		/**
		 * Sync with product lookup table
		 *
		 * WC 3.6 introduces a new lookup table, we need to sync some fields after every change.
		 * @see https://woocommerce.wordpress.com/2019/04/01/performance-improvements-in-3-6/
		 */
		function _sync_product_lookup_table( $product_id, $modified_data = array() ) {
			global $wpdb;

			$fields_that_dont_require_sync = array( 'ID', 'wpse_downloadable_file_urls', 'wpse_downloadable_file_names', 'id' );
			$modified_data                 = array_diff( $modified_data, $fields_that_dont_require_sync );
			if ( empty( $modified_data ) ) {
				return;
			}

			$product_exists_in_lookup_table = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wc_product_meta_lookup WHERE product_id = %d", $product_id ) );

			$product_lookup_keys        = array( '_price', '_regular_price', '_sale_price', '_sale_price_dates_from', '_sale_price_dates_to', '_sku', '_stock', '_stock_status', '_manage_stock', '_downloadable', '_virtual', '_thumbnail_id', 'total_sales' );
			$lookup_already_synced      = in_array( $product_id, $this->wc_lookuptable_after_save_synced, true );
			$update_lookup_table_always = ! empty( VGSE()->options['update_lookup_table_always'] );
			$sync_required              = ! $product_exists_in_lookup_table || array_intersect( $modified_data, $product_lookup_keys ) || $update_lookup_table_always;

			$product = wc_get_product( $product_id );
			if ( ! $product ) {
				return;
			}

			// Disable the recounting of terms because it's very expensive and super slow on big stores,
			// And we just want to sync the lookup, prices, stock; not terms.
			add_filter( 'woocommerce_product_recount_terms', '__return_false', 999 );

			$taxonomy_keys = wc_get_attribute_taxonomy_names();
			if ( array_intersect( $modified_data, $taxonomy_keys ) && class_exists( '\Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore' ) ) {
				wc_get_container()->get( \Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore::class )->on_product_changed( $product );
			}

			// Hooks required by some plugins that sync stock or deal with stock
			if ( in_array( '_stock', $modified_data, true ) ) {
				if ( $product->is_type( 'variation' ) ) {
					do_action( 'woocommerce_variation_set_stock', $product );
				} else {
					do_action( 'woocommerce_product_set_stock', $product );
				}
			}

			if ( ! $lookup_already_synced && $sync_required ) {

				// We resave the regular price to force WC to execute the internal, protected method update_lookup_table()
				$regular_price = get_post_meta( $product_id, '_regular_price', true );

				// Composite products: Disable the price syncing because composite rewrites the sheet values sometimes
				add_filter( 'woocommerce_composite_update_price_meta', '__return_false' );
				$product->set_regular_price( 999999999999999 );
				$product->save();
				$product->set_regular_price( $regular_price );
				$product->save();
				$this->wc_lookuptable_after_save_synced[] = $product_id;
			} else {
				// @todo Clear WC caches only when editing WC core fields, now it clears for all edits, including unrelated custom fields
				$this->clear_wc_caches( $product_id );
			}
		}

		function clear_wc_caches( $product ) {
			if ( is_int( $product ) ) {
				$product = wc_get_product( $product );
			}
			// Bail if product doesn't exist, in case it was deleted before clearing caches
			if ( ! is_object( $product ) ) {
				return;
			}
			if ( ! function_exists( 'wc_delete_product_transients' ) || ! class_exists( 'WC_Cache_Helper' ) ) {
				return;
			}
			wc_delete_product_transients( $product->get_id() );
			if ( $product->get_parent_id( 'edit' ) ) {
				wc_delete_product_transients( $product->get_parent_id( 'edit' ) );
				if ( version_compare( WC()->version, '3.9.0' ) >= 0 ) {
					WC_Cache_Helper::invalidate_cache_group( 'product_' . $product->get_parent_id( 'edit' ) );
				} else {
					WC_Cache_Helper::incr_cache_prefix( 'product_' . $product->get_parent_id( 'edit' ) );
				}
			}
			if ( version_compare( WC()->version, '3.6.0' ) >= 0 ) {
				WC_Cache_Helper::invalidate_attribute_count( array_keys( $product->get_attributes() ) );
			}
			if ( version_compare( WC()->version, '3.9.0' ) >= 0 ) {
				WC_Cache_Helper::invalidate_cache_group( 'product_' . $product->get_id() );
			} else {
				WC_Cache_Helper::incr_cache_prefix( 'product_' . $product->get_id() );
			}
		}

		/**
		 * Convert spreadsheet rows array to WC API format
		 * @param array $rows
		 * @return array
		 */
		function convert_row_to_api_format( $rows = array() ) {
			$products = array();
			$out      = array();

			$rows = VGSE()->helpers->add_post_type_to_rows( $rows );

			$parent_products = wp_list_filter(
				$rows,
				array(
					'post_type' => VGSE()->WC->post_type,
				)
			);

			$parent_products_ids = wp_list_pluck( $parent_products, 'ID' );

			$variations_rows = wp_list_filter(
				$rows,
				array(
					'post_type' => 'product_variation',
				)
			);

			foreach ( $variations_rows as $data_obj ) {
				$id        = (int) $data_obj['ID'];
				$post_obj  = get_post( $id );
				$parent_id = $post_obj->post_parent;

				if ( ! isset( $products[ $parent_id ] ) ) {
					$products[ $parent_id ] = array();
				}
				$products[ $parent_id ][ $id ] = $data_obj;

				if ( ! in_array( $parent_id, $parent_products_ids ) && ! isset( $parent_products[ $parent_id ] ) ) {
					$parent_products[ $parent_id ] = array(
						'ID' => $parent_id,
					);
				}
			}

			if ( empty( $parent_products ) ) {
				return $out;
			}

			foreach ( $parent_products as $parent_product ) {
				// Es necesario solo cuando $parent_product es un objeto
				// $parent_product = get_object_vars($parent_product);

				$new_data = array();
				if ( isset( $parent_product['ID'] ) ) {
					$new_data['ID'] = $parent_product['ID'];
				}
				if ( isset( $parent_product['post_title'] ) ) {
					$new_data['name'] = $parent_product['post_title'];
				}
				if ( isset( $parent_product['_downloadable_files'] ) ) {
					$new_data['downloads'] = ( is_string( $parent_product['_downloadable_files'] ) ) ? array_values( json_decode( wp_unslash( $parent_product['_downloadable_files'] ), true ) ) : $parent_product['_downloadable_files'];
					if ( ! empty( $new_data['downloads'] ) ) {
						$new_data['downloadable'] = true;
					}
				}
				if ( isset( $parent_product['_download_type'] ) ) {
					$new_data['download_type'] = ( ! empty( $parent_product['_download_type'] ) ) ? $parent_product['_download_type'] : 'standard';
				}
				if ( isset( $parent_product['product_shipping_class'] ) ) {
					$term                       = get_term_by( 'name', $parent_product['product_shipping_class'], 'product_shipping_class' );
					$new_data['shipping_class'] = $term->slug;
				}
				if ( isset( $parent_product['_download_expiry'] ) ) {
					$new_data['download_expiry'] = (int) $parent_product['_download_expiry'];
				}
				if ( isset( $parent_product['_download_limit'] ) ) {
					$new_data['download_limit'] = $parent_product['_download_limit'];
				}
				if ( isset( $parent_product['post_name'] ) ) {
					$new_data['slug'] = $parent_product['post_name'];
				}
				if ( isset( $parent_product['content'] ) ) {
					$new_data['description'] = $parent_product['content'];
				}
				if ( isset( $parent_product['date'] ) ) {
					$new_data['date_created'] = $parent_product['date'];
				}
				if ( isset( $parent_product['excerpt'] ) ) {
					$new_data['short_description'] = $parent_product['excerpt'];
				}
				if ( isset( $parent_product['status'] ) ) {
					$new_data['status'] = $parent_product['status'];
				}
				if ( isset( $parent_product['comment_status'] ) ) {
					$new_data['reviews_allowed'] = VGSE()->WC->_do_booleable( $parent_product['comment_status'] );
				}

				$taxonomies = get_object_taxonomies( VGSE()->WC->post_type, 'objects' );
				$separator  = VGSE()->helpers->get_term_separator();

				if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
					foreach ( $taxonomies as $taxonomy ) {
						if ( strpos( $taxonomy->name, 'pa_' ) === false ) {
							continue;
						}
						$taxonomy_key = $taxonomy->name;

						if ( isset( $parent_product[ $taxonomy_key ] ) ) {
							if ( ! isset( $new_data['attributes'] ) ) {
								$new_data['attributes'] = array();
							}
							$brands   = explode( $separator, $parent_product[ $taxonomy_key ] );
							$outbrand = array();
							foreach ( $brands as $marca ) {
								$brand      = get_term_by( 'name', $marca, $taxonomy_key );
								$outbrand[] = $brand;
							}

							$new_data['attributes'][] = array(
								'name'   => $taxonomy_key,
								'option' => $outbrand,
							);
						}
					}
				}
				if ( isset( $parent_product['_thumbnail_id'] ) ) {
					$new_data['images'] = array(
						array(
							'id'       => $parent_product['_thumbnail_id'],
							'position' => 0,
						),
					);
				}
				if ( isset( $parent_product['product_cat'] ) ) {
					$cats   = explode( $separator, $parent_product['product_cat'] );
					$outcat = array();
					foreach ( $cats as $cate ) {
						$cat      = get_term_by( 'name', $cate, 'product_cat' );
						$outcat[] = $cat;
					}

					$new_data['categories'] = $outcat;
				}
				if ( isset( $parent_product['product_tag'] ) ) {
					$tags   = explode( $separator, $parent_product['product_tag'] );
					$outtag = array();
					foreach ( $tags as $eti ) {
						$tag      = get_term_by( 'name', $eti, 'product_tag' );
						$outtag[] = $tag;
					}

					$new_data['tags'] = $outtag;
				}
				if ( isset( $parent_product['_sku'] ) ) {
					$new_data['sku'] = $parent_product['_sku'];
				}
				if ( isset( $parent_product['_regular_price'] ) ) {
					$new_data['regular_price'] = $parent_product['_regular_price'];
				}
				if ( isset( $parent_product['_sale_price'] ) ) {
					$new_data['sale_price'] = $parent_product['_sale_price'];
				}
				if ( isset( $parent_product['_weight'] ) ) {
					$new_data['weight'] = $parent_product['_weight'];
				}
				if ( isset( $parent_product['_height'] ) ) {
					$new_data['dimensions']['height'] = $parent_product['_height'];
				}
				if ( isset( $parent_product['_length'] ) ) {
					$new_data['dimensions']['length'] = $parent_product['_length'];
				}
				if ( isset( $parent_product['_width'] ) ) {
					$new_data['dimensions']['width'] = $parent_product['_width'];
				}
				if ( isset( $parent_product['_manage_stock'] ) ) {
					$new_data['manage_stock'] = VGSE()->WC->_do_booleable( $parent_product['_manage_stock'] );
				}
				if ( isset( $parent_product['_stock_status'] ) ) {
					$new_data['stock_status'] = $parent_product['_stock_status'];
				}
				if ( isset( $parent_product['_stock'] ) ) {
					$new_data['stock_quantity'] = $parent_product['_stock'];
				}
				if ( isset( $parent_product['_visibility'] ) ) {
					$new_data['visible'] = $parent_product['_visibility'];
				}
				if ( isset( $parent_product['_product_image_gallery'] ) ) {
					if ( ! isset( $new_data['images'] ) ) {
						$new_data['images'] = array();
					}
					$gallery = explode( ',', $parent_product['_product_image_gallery'] );

					foreach ( $gallery as $image_index => $image_id ) {
						$new_data['images'][] = array(
							'id'       => (int) $image_id,
							'position' => $image_index + 1,
						);
					}
				}
				if ( isset( $parent_product['_downloadable'] ) ) {
					$new_data['downloadable'] = VGSE()->WC->_do_booleable( $parent_product['_downloadable'] );
				}
				if ( isset( $parent_product['_virtual'] ) ) {
					$new_data['virtual'] = VGSE()->WC->_do_booleable( $parent_product['_virtual'] );
				}
				if ( isset( $parent_product['_sale_price_dates_from'] ) ) {
					if ( ! empty( $parent_product['_sale_price_dates_from'] ) ) {
						$parent_product['_sale_price_dates_from'] = date( 'Y-m-d', strtotime( $parent_product['_sale_price_dates_from'] ) );
					}
					$new_data['date_on_sale_from'] = $parent_product['_sale_price_dates_from'];
				}
				if ( isset( $parent_product['_sale_price_dates_to'] ) ) {
					if ( ! empty( $parent_product['_sale_price_dates_to'] ) ) {
						$parent_product['_sale_price_dates_to'] = date( 'Y-m-d', strtotime( $parent_product['_sale_price_dates_to'] ) );
					}
					$new_data['date_on_sale_to'] = $parent_product['_sale_price_dates_to'];
				}
				if ( isset( $parent_product['_sold_individually'] ) ) {
					$new_data['sold_individually'] = VGSE()->WC->_do_booleable( $parent_product['_sold_individually'] );
				}
				if ( isset( $parent_product['_featured'] ) ) {
					$new_data['featured'] = VGSE()->WC->_do_booleable( $parent_product['_featured'] );
				}
				if ( isset( $parent_product['_backorders'] ) ) {
					$new_data['backorders'] = $parent_product['_backorders'];
				}
				if ( isset( $parent_product['_purchase_note'] ) ) {
					$new_data['purchase_note'] = $parent_product['_purchase_note'];
				}

				if ( isset( $products[ $parent_product['ID'] ] ) ) {
					foreach ( $products[ $parent_product['ID'] ] as $index => $variation ) {
						// Skip variation if we received the id and post type only
						if ( count( $variation ) < 3 && isset( $variation['ID'] ) && isset( $variation['post_type'] ) ) {
							continue;
						}
						if ( ! isset( $new_data['variations'] ) ) {
							$new_data['variations'] = array();
						}
						if ( isset( $variation['ID'] ) ) {
							$new_data['variations'][ $index ]['id'] = $variation['ID'];
						}
						if ( isset( $variation['_tax_class'] ) ) {
							$new_data['variations'][ $index ]['tax_class'] = $variation['_tax_class'];
						}

						if ( isset( $variation['_variation_description'] ) ) {
							$new_data['variations'][ $index ]['description'] = $variation['_variation_description'];
						}
						if ( isset( $variation['post_title'] ) ) {
							$new_data['variations'][ $index ]['name'] = $variation['post_title'];
						}
						if ( isset( $variation['_downloadable_files'] ) ) {
							$new_data['variations'][ $index ]['downloads'] = ( is_string( $variation['_downloadable_files'] ) ) ? array_values( json_decode( wp_unslash( $variation['_downloadable_files'] ), true ) ) : $variation['_downloadable_files'];
							if ( ! empty( $new_data['variations'][ $index ]['downloads'] ) ) {
								$new_data['variations'][ $index ]['downloadable'] = true;
							}
						}
						if ( isset( $variation['_download_type'] ) ) {
							$new_data['variations'][ $index ]['download_type'] = ( ! empty( $variation['_download_type'] ) ) ? $variation['_download_type'] : 'standard';
						}
						if ( isset( $variation['product_shipping_class'] ) ) {
							$term = get_term_by( 'name', $variation['product_shipping_class'], 'product_shipping_class' );
							$new_data['variations'][ $index ]['shipping_class'] = $term->slug;
						}
						if ( isset( $variation['_download_expiry'] ) ) {
							$new_data['variations'][ $index ]['download_expiry'] = (int) $variation['_download_expiry'];
						}
						if ( isset( $variation['_download_limit'] ) ) {
							$new_data['variations'][ $index ]['download_limit'] = $variation['_download_limit'];
						}
						if ( isset( $variation['post_name'] ) ) {
							$new_data['variations'][ $index ]['slug'] = $variation['post_name'];
						}

						if ( isset( $variation['_vgse_variation_enabled'] ) ) {
							$new_data['variations'][ $index ]['status'] = VGSE()->WC->_do_booleable( $variation['_vgse_variation_enabled'] ) ? 'publish' : 'private';
						}
						if ( isset( $variation['comment_status'] ) ) {
							$new_data['variations'][ $index ]['reviews_allowed'] = VGSE()->WC->_do_booleable( $variation['comment_status'] );
						}
						$taxonomies = get_object_taxonomies( VGSE()->WC->post_type, 'objects' );

						if ( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
							foreach ( $taxonomies as $taxonomy ) {
								if ( strpos( $taxonomy->name, 'pa_' ) === false ) {
									continue;
								}
								$taxonomy_key = $taxonomy->name;

								if ( isset( $variation[ $taxonomy_key ] ) ) {
									if ( ! isset( $new_data['variations'][ $index ]['attributes'] ) ) {
										$new_data['variations'][ $index ]['attributes'] = array();
									}
									// We save only the first term
									$brand_raw  = current( explode( $separator, $variation[ $taxonomy_key ] ) );
									$brand      = get_term_by( 'name', $brand_raw, $taxonomy_key );
									$brand_slug = $brand->slug;

									$new_data['variations'][ $index ]['attributes'][ $taxonomy_key ] = array(
										'id'     => wc_attribute_taxonomy_id_by_name( $taxonomy_key ),
										'name'   => $taxonomy_key,
										'option' => $brand_slug,
									);
								}
							}
						}
						if ( isset( $variation['_thumbnail_id'] ) ) {
							$new_data['variations'][ $index ]['image'] = ( ! empty( $variation['_thumbnail_id'] ) ) ? array(
								'id'       => $variation['_thumbnail_id'],
								'position' => 0,
							) : 0;
						}
						if ( isset( $variation['_sku'] ) ) {
							$new_data['variations'][ $index ]['sku'] = $variation['_sku'];
						}
						if ( isset( $variation['_regular_price'] ) ) {
							$new_data['variations'][ $index ]['regular_price'] = $variation['_regular_price'];
						}
						if ( isset( $variation['_sale_price'] ) ) {
							$new_data['variations'][ $index ]['sale_price'] = $variation['_sale_price'];
						}
						if ( isset( $variation['_weight'] ) ) {
							$new_data['variations'][ $index ]['weight'] = $variation['_weight'];
						}
						if ( isset( $variation['_height'] ) ) {
							$new_data['variations'][ $index ]['dimensions']['height'] = $variation['_height'];
						}
						if ( isset( $variation['_length'] ) ) {
							$new_data['variations'][ $index ]['dimensions']['length'] = $variation['_length'];
						}
						if ( isset( $variation['_width'] ) ) {
							$new_data['variations'][ $index ]['dimensions']['width'] = $variation['_width'];
						}
						if ( isset( $variation['_manage_stock'] ) ) {
							$new_data['variations'][ $index ]['manage_stock'] = VGSE()->WC->_do_booleable( $variation['_manage_stock'] );
						}
						if ( isset( $variation['_stock_status'] ) ) {
							$new_data['variations'][ $index ]['stock_status'] = $variation['_stock_status'];
						}
						if ( isset( $variation['_stock'] ) ) {
							$new_data['variations'][ $index ]['stock_quantity'] = (int) $variation['_stock'];
						}
						if ( isset( $variation['_visibility'] ) ) {
							$new_data['variations'][ $index ]['visible'] = $variation['_visibility'];
						}
						if ( isset( $variation['_downloadable'] ) ) {
							$new_data['variations'][ $index ]['downloadable'] = VGSE()->WC->_do_booleable( $variation['_downloadable'] );
						}
						if ( isset( $variation['_virtual'] ) ) {
							$new_data['variations'][ $index ]['virtual'] = VGSE()->WC->_do_booleable( $variation['_virtual'] );
						}
						if ( isset( $variation['_sale_price_dates_from'] ) ) {
							if ( ! empty( $variation['_sale_price_dates_from'] ) ) {
								$variation['_sale_price_dates_from'] = date( 'Y-m-d', strtotime( $variation['_sale_price_dates_from'] ) );
							}
							$new_data['variations'][ $index ]['date_on_sale_from'] = $variation['_sale_price_dates_from'];
						}
						if ( isset( $variation['_sale_price_dates_to'] ) ) {
							if ( ! empty( $variation['_sale_price_dates_to'] ) ) {
								$variation['_sale_price_dates_to'] = date( 'Y-m-d', strtotime( $variation['_sale_price_dates_to'] ) );
							}
							$new_data['variations'][ $index ]['date_on_sale_to'] = $variation['_sale_price_dates_to'];
						}
						if ( isset( $variation['_sold_individually'] ) ) {
							$new_data['variations'][ $index ]['sold_individually'] = VGSE()->WC->_do_booleable( $variation['_sold_individually'] );
						}
						if ( isset( $variation['_backorders'] ) ) {
							$new_data['variations'][ $index ]['backorders'] = $variation['_backorders'];
						}
						if ( isset( $variation['menu_order'] ) ) {
							$new_data['variations'][ $index ]['menu_order'] = $variation['menu_order'];
						}
						if ( isset( $variation['_purchase_note'] ) ) {
							$new_data['variations'][ $index ]['purchase_note'] = $variation['_purchase_note'];
						}
					}
				}

				// Skip product if we received the id only
				if ( count( $new_data ) === 1 && isset( $new_data['ID'] ) ) {
					continue;
				}
				$out[] = $new_data;
			}
			return $out;
		}

		function format_sale_dates( $data, $query, $spreadsheet_columns ) {
			if ( $query['post_type'] !== VGSE()->WC->post_type || ( ! isset( $spreadsheet_columns['_sale_price_dates_from'] ) && ! isset( $spreadsheet_columns['_sale_price_dates_to'] ) ) ) {
				return $data;
			}

			foreach ( $data as $row_index => $row ) {
				if ( ! empty( $row['_sale_price_dates_from'] ) && is_numeric( $row['_sale_price_dates_from'] ) ) {
					$data[ $row_index ]['_sale_price_dates_from'] = wp_date( 'Y-m-d', $row['_sale_price_dates_from'] );
				}
				if ( ! empty( $row['_sale_price_dates_to'] ) && is_numeric( $row['_sale_price_dates_to'] ) ) {
					$data[ $row_index ]['_sale_price_dates_to'] = wp_date( 'Y-m-d', $row['_sale_price_dates_to'] );
				}
			}
			return $data;
		}

		/**
		 * The product was updated
		 * @param string $post_type
		 * @param int $post_id
		 * @param string $key
		 * @param mixed $new_value
		 * @param array $cell_args
		 * @param array $spreadsheet_columns
		 * @return null
		 */
		function product_cell_updated_on_spreadsheet( $post_type, $post_id, $key, $new_value, $cell_args, $spreadsheet_columns, $row ) {
			if ( $post_type !== VGSE()->WC->post_type ) {
				return;
			}

			vgse_init_WooCommerce_Attrs()->_sync_product_terms( $post_id, $new_value, $key, $cell_args['data_type'], $row );
		}

		/**
		 * The product was updated using a formula
		 * @param int $post_id
		 * @param string $initial_data
		 * @param string $modified_data
		 * @param string $column
		 * @param string $formula
		 * @param string $post_type
		 * @param array $cell_args
		 * @param array $spreadsheet_columns
		 * @return null
		 */
		function product_updated_with_formula( $post_id, $initial_data, $modified_data, $column, $formula, $post_type, $cell_args, $spreadsheet_columns ) {
			$is_product_sheet                   = $post_type === VGSE()->WC->post_type;
			$is_post_type_converting_to_product = post_type_exists( $post_type ) && ! $is_product_sheet && $column === 'post_type' && $initial_data !== VGSE()->WC->post_type && $modified_data === VGSE()->WC->post_type;

			if ( ! $is_product_sheet && ! $is_post_type_converting_to_product ) {
				return;
			}

			vgse_init_WooCommerce_Attrs()->_sync_product_terms( $post_id, $modified_data, $column, $cell_args['data_type'] );
			$this->_sync_product_lookup_table( $post_id, array( $column ) );
			$this->_sync_subscription_prices( $post_id, array( $column ) );
			$this->_maybe_migrate_variation_to_product( $post_id, array( $column => $modified_data ) );
			$this->_maybe_migrate_product_to_variation( $post_id, array( $column => $modified_data ) );
		}

		function product_updated_on_spreadsheet( $product_id, $item, $data, $post_type ) {
			if ( ! in_array( $post_type, array( VGSE()->WC->post_type, 'product_variation' ) ) ) {
				return;
			}
			$this->_sync_product_lookup_table( $product_id, array_keys( $item ) );
			$this->_sync_subscription_prices( $product_id, array_keys( $item ) );
			$this->_maybe_migrate_variation_to_product( $product_id, $item );
			$this->_maybe_migrate_product_to_variation( $product_id, $item );
		}

		function _maybe_migrate_product_to_variation( $product_id, $row ) {
			global $wpdb;

			if ( empty( $row['post_type'] ) || $row['post_type'] !== 'product_variation' ) {
				return;
			}

			// When we convert a product into a variation and the product has a status not supported by variations, automatically change it to "enabled variation" (publish)
			if ( ! in_array( get_post_status( $product_id ), array( 'trash', 'publish', 'private' ), true ) ) {
				wp_update_post(
					array(
						'ID'          => $product_id,
						'post_status' => 'publish',
					)
				);
			}

			$product_attributes   = get_post_meta( $product_id, '_product_attributes', true );
			$variation_attributes = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE 'attribute_%' AND meta_value <> ''", $product_id ) );
			if ( ! empty( $product_attributes ) && ! $variation_attributes ) {
				foreach ( $product_attributes as $attribute_key => $attribute_settings ) {
					if ( strpos( $attribute_key, 'pa_' ) === 0 ) {
						$terms = wp_get_object_terms(
							$product_id,
							$attribute_key,
							array(
								'fields' => 'slugs',
								'number' => 1,
							)
						);
					} else {
						$terms = explode( WC_DELIMITER, $attribute_settings['value'] );
					}
					$term_value = ( ! empty( $terms ) ) ? current( $terms ) : '';
					update_post_meta( $product_id, 'attribute_' . $attribute_key, $term_value );
				}
				delete_post_meta( $product_id, '_product_attributes' );
			}
		}

		function _maybe_migrate_variation_to_product( $product_id, $row ) {
			global $wpdb;
			if ( get_post_type( $product_id ) !== 'product' || empty( $row['post_type'] ) || $row['post_type'] !== 'product' ) {
				return;
			}

			$post = get_post( $product_id );
			if ( $post->post_parent < 1 ) {
				return;
			}

			$variation_meta = get_post_meta( $product_id );
			$parent_meta    = get_post_meta( $post->post_parent );
			$parent         = get_post( $post->post_parent );

			// We set the type as simple early because it has product_variation type and wc_get_product causes errors
			wp_set_object_terms( $product_id, 'simple', 'product_type' );
			$product = wc_get_product( $product_id );
			if ( ! $product ) {
				return;
			}

			$product->set_description( $parent->post_content );

			$short_description = ( ! empty( $variation_meta['_variation_description'] ) ) ? $variation_meta['_variation_description'][0] : $parent->post_excerpt;
			$product->set_short_description( $short_description );

			$product_cats_ids = wc_get_product_term_ids( $parent->ID, 'product_cat' );
			$product->set_category_ids( $product_cats_ids );

			$product_tags_ids = wc_get_product_term_ids( $parent->ID, 'product_tag' );
			$product->set_tag_ids( $product_tags_ids );

			$attributes = array();
			$position   = 1;

			if ( ! empty( $parent_meta['_product_attributes'][0] ) ) {
				$parent_meta['_product_attributes'][0] = maybe_unserialize( $parent_meta['_product_attributes'][0] );
			}
			foreach ( $variation_meta as $meta_key => $meta_values ) {
				if ( strpos( $meta_key, 'attribute_' ) === 0 && ! empty( $meta_values ) ) {
					$attribute_value = $meta_values[0];
					$attribute_key   = str_replace( 'attribute_', '', $meta_key );
					$attribute_name  = ( ! empty( $parent_meta['_product_attributes'][0][ $attribute_key ] ) ) ? $parent_meta['_product_attributes'][0][ $attribute_key ]['name'] : $attribute_key;
					$options         = array();
					if ( taxonomy_exists( $attribute_key ) ) {
						$attribute_id   = wc_attribute_taxonomy_id_by_name( $attribute_key );
						$attribute_term = get_term_by( 'slug', $attribute_value, $attribute_key );
						if ( is_object( $attribute_term ) && ! is_wp_error( $attribute_term ) ) {
							$options = array( $attribute_term->term_id );
						}
					} else {
						$attribute_id = 0;
						$options      = array( $attribute_value );
					}
					$attribute = new WC_Product_Attribute();
					$attribute->set_id( $attribute_id );
					$attribute->set_name( $attribute_name );
					$attribute->set_options( $options );
					$attribute->set_position( $position );
					$attribute->set_visible( true );
					$attribute->set_variation( false );
					$attributes[] = $attribute;
					$position++;
				}

				foreach ( $meta_values as $meta_value ) {
					if ( $meta_value === 'parent' ) {
						update_post_meta( $product_id, $meta_key, get_post_meta( $post->post_parent, $meta_key, true ) );
					}
				}
			}
			$product->set_attributes( $attributes );
			$product->save();

			// Copy all meta data from the parent that doesn't exist on the variation
			// This will transfer the featured image if the variation doesn't have image
			// and other fields from other plugins
			foreach ( $parent_meta as $meta_key => $meta_values ) {
				// Omit the gallery because it might have images of other variations
				if ( $meta_key === '_product_image_gallery' ) {
					continue;
				}
				$meta_value       = current( $meta_values );
				$new_product_meta = get_post_meta( $product_id, $meta_key, true );
				if ( ! empty( $meta_value ) && empty( $new_product_meta ) ) {
					update_post_meta( $product_id, $meta_key, $meta_value );
				}
			}

			// The post_type is not being updated by wp_update_post for some reason
			$result = wp_update_post(
				array(
					'ID'          => $product_id,
					'post_parent' => '0',
					'post_name'   => '',
				),
				true
			);
		}

		function prepare_featured_value_for_display( $value, $post, $key, $column_settings ) {
			$terms = VGSE()->helpers->get_current_provider()->get_item_terms( $post->ID, 'product_visibility' );
			$value = strpos( $terms, 'featured' ) !== false ? 'featured' : 'no';
			return $value;
		}

		function prepare_linked_product_value_for_database( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			$products = array_map( 'trim', explode( ',', wp_unslash( $data_to_save ) ) );
			$ids      = array();
			foreach ( $products as $product_id ) {
				if ( is_numeric( $product_id ) && get_post_status( $product_id ) ) {
					$ids[] = (int) $product_id;
				} else {
					$product_id = wc_get_product_id_by_sku( $product_id );
					if ( $product_id ) {
						$ids[] = $product_id;
					}
				}
			}
			return $ids;
		}

		function prepare_linked_product_value_for_display( $value, $post, $key, $column_settings ) {
			if ( ! empty( $value ) && is_array( $value ) ) {
				$ids    = array_filter( $value );
				$values = array();
				foreach ( $ids as $id ) {
					$sku = get_post_meta( $id, '_sku', true );
					if ( ! empty( $sku ) ) {
						$values[] = $sku;
					} else {
						$values[] = $id;
					}
				}
				$value = implode( ', ', $values );
			} else {
				$value = '';
			}
			return $value;
		}

		function save_sale_date( $post_id, $cell_key, $data_to_save, $post_type, $cell_args, $spreadsheet_columns ) {
			$type = str_replace( '_sale_price_dates_', '', $cell_key );
			$date = '';
			if ( ! empty( $data_to_save ) ) {
				$date = $type === 'from' ? $data_to_save . ' 00:00:00' : $data_to_save . ' 23:59:59';
			}

			$product = wc_get_product( $post_id );
			if ( $type === 'from' ) {
				$product->set_date_on_sale_from( $date );
			} else {
				$product->set_date_on_sale_to( $date );
			}
			$product->save();
		}

		/**
		 * Creates or returns an instance of this class.
		 */
		static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WPSE_WC_Products_Data_Formatting();
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

if ( ! function_exists( 'WPSE_WC_Products_Data_Formatting_Obj' ) ) {

	function WPSE_WC_Products_Data_Formatting_Obj() {
		return WPSE_WC_Products_Data_Formatting::get_instance();
	}
}
WPSE_WC_Products_Data_Formatting_Obj();
