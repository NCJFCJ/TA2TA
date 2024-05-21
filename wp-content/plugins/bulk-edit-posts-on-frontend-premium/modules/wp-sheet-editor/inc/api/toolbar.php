<?php defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Sheet_Editor_Toolbar' ) ) {

	class WP_Sheet_Editor_Toolbar {

		private $registered_items = array();

		function __construct() {

		}

		function remove_item( $key, $toolbar_key, $provider ) {
			if ( isset( $this->registered_items[ $provider ][ $toolbar_key ][ $key ] ) ) {
				unset( $this->registered_items[ $provider ][ $toolbar_key ][ $key ] );
			}
		}

		/**
		 * Register toolbar item
		 * @param string $key
		 * @param array $args
		 * @param string $provider
		 */
		function register_item( $key, $args = array(), $provider = 'post', $update_existing = false ) {
			if ( $update_existing && isset( $this->registered_items[ $provider ][ $args['toolbar_key'] ][ $key ] ) ) {
				$args = wp_parse_args( $args, $this->registered_items[ $provider ][ $args['toolbar_key'] ][ $key ] );
			}
			$defaults = array(
				'type'                       => 'button', // html | switch | button
				'icon'                       => '', // Font awesome icon name , including font awesome prefix: fa fa-XXX. Only for type=button.
				'help_tooltip'               => '', // help message, accepts html with entities encoded.
				'tooltip_size'               => '',
				'tooltip_position'           => 'down',
				'content'                    => '', // if type=button : button label | if type=html : html string.
				'css_class'                  => '', // .button will be added to all items also.
				'key'                        => $key,
				'extra_html_attributes'      => '', // useful for adding data attributes
				'container_id'               => '',
				'label'                      => $args['content'],
				'id'                         => '',
				'url'                        => '',
				'allow_in_frontend'          => true,
				'allow_to_hide'              => true,
				'container_class'            => '',
				'default_value'              => '1', // only if type=switch - 1=on , 0=off
				'toolbar_key'                => 'primary',
				'container_extra_attributes' => '',
				'parent'                     => null,
				'footer_callback'            => null, // PHP callable that returns the popup html
				'required_capability'        => null,
				'require_click_to_expand'    => false,
				'live_refresh'               => 0, // Number of seconds for the interval to get the toolbar content via ajax
			);

			$args = wp_parse_args( $args, $defaults );

			if ( ! empty( $args['help_tooltip'] ) && strlen( $args['help_tooltip'] ) < 20 ) {
				$args['tooltip_size'] = 'small';
			}

			if ( empty( $provider ) ) {
				$provider = 'post';
			}

			if ( empty( $args['key'] ) ) {
				return;
			}

			if ( empty( $this->registered_items[ $provider ] ) ) {
				$this->registered_items[ $provider ] = array();
			}
			if ( empty( $this->registered_items[ $provider ][ $args['toolbar_key'] ] ) ) {
				$this->registered_items[ $provider ][ $args['toolbar_key'] ] = array();
			}
			$this->registered_items[ $provider ][ $args['toolbar_key'] ][ $key ] = $args;
		}

		/**
		 * Get individual toolbar item
		 * @return array
		 */
		function get_item( $item_key, $provider = 'post', $toolbar_key = 'primary' ) {
			$provider_items = $this->get_provider_items( $provider, $toolbar_key );
			if ( isset( $provider_items[ $item_key ] ) ) {
				return $provider_items[ $item_key ];
			} else {
				return false;
			}
		}

		/**
		 * Get individual toolbar item as html
		 * @return string
		 */
		function get_rendered_item( $item_key, $provider = 'post', $toolbar_key = 'primary' ) {
			$item = $this->get_item( $item_key, $provider, $toolbar_key );

			if ( ! empty( $item['required_capability'] ) && ! WP_Sheet_Editor_Helpers::current_user_can( $item['required_capability'] ) ) {
				return '';
			}
			$content = '';
			if ( $item['type'] === 'button' ) {
				$content .= '<button name="' . esc_attr( $item['key'] ) . '" class="button ' . esc_attr( $item['css_class'] ) . '" ' . $item['extra_html_attributes'] . '  id="' . esc_attr( $item['id'] ) . '" >';
				if ( ! empty( $item['icon'] ) ) {
					$content .= '<i class="' . esc_attr( $item['icon'] ) . '"></i> ';
				}
				$content .= esc_html( $item['content'] ) . '</button>';

				if ( ! empty( $item['url'] ) ) {
					$content = str_replace( '<button', '<a href="' . esc_url( $item['url'] ) . '" ', $content );
					$content = str_replace( '</button', '</a', $content );
				}
			} elseif ( $item['type'] === 'html' ) {
				$content .= is_callable( $item['content'] ) ? call_user_func( $item['content'], $item, $provider ) : $item['content'];
			} elseif ( $item['type'] === 'switch' ) {
				$content .= '<input type="checkbox" ';
				if ( $item['default_value'] ) {
					$content .= ' value="1" checked';
				} else {
					$content .= ' value="0" ';
				}
				$content .= ' id="' . esc_attr( $item['id'] ) . '"  data-labelauty="' . esc_html( $item['content'] ) . '" class="' . esc_attr( $item['css_class'] ) . '" ' . $item['extra_html_attributes'] . ' /> ';
			}

			if ( empty( $content ) ) {
				return false;
			}

			if ( ! empty( $item['help_tooltip'] ) ) {
				$tooltip_position = ! empty( $item['parent'] ) ? 'right' : 'down';
				if ( ! empty( $item['tooltip_position'] ) ) {
					$tooltip_position = $item['tooltip_position'];
				}
				$item['container_extra_attributes'] .= '  data-wpse-tooltip="' . esc_attr( $tooltip_position ) . '" aria-label="' . $item['help_tooltip'] . '" ';
				if ( ! empty( $item['tooltip_size'] ) ) {
					$item['container_extra_attributes'] .= '  data-wpse-tooltip-size="' . esc_attr( $item['tooltip_size'] ) . '" ';
				}
			}

			if ( ! empty( $item['require_click_to_expand'] ) ) {
				$item['container_class'] .= ' require-click-to-expand ';
			}
			$out = '<div class="button-container ' . esc_attr( $item['key'] ) . '-container ' . esc_attr( $item['container_class'] ) . '" id="' . esc_attr( $item['container_id'] ) . '" ' . $item['container_extra_attributes'] . '>' . $content;

			// Render child items
			if ( empty( $item['parent'] ) ) {
				$all_items      = $this->get_provider_items( $provider, null );
				$all_flat_items = array();
				foreach ( $all_items as $all_toolbar_key => $all_toolbar_items ) {
					$all_flat_items = array_merge( $all_flat_items, $all_toolbar_items );
				}
				$child_items = wp_list_filter( $all_flat_items, array( 'parent' => $item['key'] ) );

				if ( ! empty( $child_items ) ) {
					$rendered_children = '';
					foreach ( $child_items as $child_item ) {
						$rendered_children .= $this->get_rendered_item( $child_item['key'], $provider, $child_item['toolbar_key'] );
					}
					$out .= '<div class="toolbar-submenu">' . $rendered_children . '</div>';
				}
			}

			if ( ! empty( $item['footer_callback'] ) && is_callable( $item['footer_callback'] ) ) {
				add_action( 'vg_sheet_editor/editor_page/after_content', $item['footer_callback'] );
			}

			$out .= '</div>';
			return $out;
		}

		static function render_base_modal_html( $live_refresh, $ajax_action, $extra_large = false, $html_class = '' ) {
			?>
			<div class="lazy-modal-content remodal 
			<?php
			if ( $html_class ) {
				echo implode( ' ', array_map( 'sanitize_html_class', explode( ' ', $html_class ) ) );
			}
			?>
			<?php
			if ( $extra_large ) {
							echo 'remodal-extra-large';}
			?>
" data-live-refresh="<?php echo (int) $live_refresh; ?>" data-ajax-action="<?php echo esc_attr( $ajax_action ); ?>" data-remodal-id="<?php echo esc_attr( $ajax_action ); ?>" data-remodal-options="closeOnOutsideClick: false, hashTracking: false">
				<div class="modal-content">

			</div>
			</div>
			<?php
		}

		/**
		 * Get all toolbar items by post type rendered as html
		 * @return string
		 */
		function get_rendered_provider_items( $provider, $toolbar_key = 'primary' ) {
			$items = $this->get_provider_items( $provider, $toolbar_key );

			if ( ! $items ) {
				return false;
			}

			$parent_items = wp_list_filter( $items, array( 'parent' => null ) );

			$out = '';
			foreach ( $parent_items as $item_key => $item ) {
				$rendered_item = $this->get_rendered_item( $item_key, $provider, $toolbar_key );

				if ( ! empty( $rendered_item ) ) {
					$out .= $rendered_item;
				}
			}

			return $out;
		}

		/**
		 * Get all toolbar items
		 * @return array
		 */
		function get_items() {
			$items = apply_filters( 'vg_sheet_editor/toolbar/get_items', $this->registered_items );

			return $items;
		}

		/**
		 * Get all toolbar items by post type
		 * @return array
		 */
		function get_provider_items( $provider, $toolbar_key = 'primary' ) {
			$items = $this->get_items();

			$out = false;

			if ( ! isset( $items[ $provider ] ) ) {
				return $out;
			}

			if ( empty( $toolbar_key ) ) {
				$out = $items[ $provider ];
			}

			if ( ! empty( $toolbar_key ) && isset( $items[ $provider ][ $toolbar_key ] ) ) {
				$out = $items[ $provider ][ $toolbar_key ];
			}
			return $out;
		}

		function __set( $name, $value ) {
			$this->$name = $value;
		}

		function __get( $name ) {
			return $this->$name;
		}

	}

}
