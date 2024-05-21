<?php
/*
	Plugin Name: PDF Thumbnail Generator
	Plugin URI: https://wp-speedup.eu
	Description: Generates thumbnail for PDF files
	Version: 1.2
	Author: KubiQ
	Author URI: https://kubiq.sk
	Text Domain: pdf-thumbnail-generator
	Domain Path: /languages
*/

defined('ABSPATH') || exit;

if( ! class_exists('pdf_thumbnail_generator') ){
	class pdf_thumbnail_generator{
		var $settings;

		function __construct(){
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
			add_action( 'admin_menu', array( $this, 'plugin_menu_link' ) );
			add_action( 'init', array( $this, 'plugin_init' ) );
			add_action( 'add_attachment', array( $this, 'generate_thumbnail' ), 11, 1 );
			add_action( 'delete_attachment', array( $this, 'delete' ) );
			add_filter( 'wp_mime_type_icon', array( $this, 'wp_mime_type_icon' ), 10, 3 );

			add_shortcode( 'pdf_thumbnail', function( $atts ){
				if( is_admin() ) return true;
				
				if( ! isset( $atts['id'] ) || ! intval( $atts['id'] ) ) return false;
			
				return get_pdf_thumbnail_image( $atts['id'] );
			});

			add_shortcode( 'pdf_thumbnail_url', function( $atts ){
				if( is_admin() ) return true;
				
				if( ! isset( $atts['id'] ) || ! intval( $atts['id'] ) ) return false;
			
				return $this->get_url( $atts['id'] );
			});
		}

		function activate(){
			if( ! extension_loaded('imagick') ){
				esc_html_e( 'Imagick is missing on your server. PDF Thumbnail Generator can not work without it.', 'pdf-thumbnail-generator' );
				exit;
			}
		}

		function plugins_loaded(){
			load_plugin_textdomain( 'pdf-thumbnail-generator', FALSE, basename( __DIR__ ) . '/languages/' );
		}

		function plugin_menu_link(){
			add_submenu_page(
				'options-general.php',
				__( 'PDF Thumbnails', 'pdf-thumbnail-generator' ),
				__( 'PDF Thumbnails', 'pdf-thumbnail-generator' ),
				'manage_options',
				basename( __FILE__ ),
				array( $this, 'admin_options_page' )
			);
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'filter_plugin_actions' ), 10, 2 );
		}

		function filter_plugin_actions( $links, $file ){
			array_unshift( $links, '<a href="options-general.php?page=' . basename( __FILE__ ) . '">' . __( 'Settings', 'pdf-thumbnail-generator' ) . '</a>' );
			return $links;
		}

		function plugin_init(){
			$this->settings = array_merge(
				array(
					'max_width' => 1024,
					'max_height' => 1024,
					'quality' => 80,
					'type' => 'png',
				),
				get_option( 'pdf_thumbnail_generator_settings', array() )
			);
		}

		function admin_options_page(){
			global $wpdb;
			if( isset( $_GET['generate'] ) ){ ?>
				<div class="wrap">
					<h2><?php _e( 'Generating PDF thumbnails...', 'pdf-thumbnail-generator' ) ?></h2>
					<div id="pdf-list"><?php
						$generated_thumbs = 0;
						$pdfs = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE {$wpdb->posts}.post_type = 'attachment' AND {$wpdb->posts}.post_mime_type = 'application/pdf'");
						if( $pdfs ){
							foreach( $pdfs as $pdf ){
								$regenerate = $_GET['generate'] == 'all' ? true : false;
								$thumbnail = get_post_meta( $pdf, '_pdf_thumbnail', true );
								if( ! $thumbnail || $regenerate ){
									$generated = $this->generate_thumbnail( $pdf, $regenerate );
									$thumbnail = get_post_meta( $pdf, '_pdf_thumbnail', true );
									if( $thumbnail && $generated ){
										$generated_thumbs++;
										echo '<div>' . sprintf( esc_html__( 'New thumbnail was generated for %d', 'pdf-thumbnail-generator' ), $pdf ) . '</div>';
									}else{
										echo '<div>' . sprintf( esc_html__( 'Thumbnail already exists for %d', 'pdf-thumbnail-generator' ), $pdf ) . '</div>';
									}
								}else{
									echo '<div>' . sprintf( esc_html__( 'Thumbnail already exists for %d', 'pdf-thumbnail-generator' ), $pdf ) . '</div>';
								}
							}
						}

						echo '<div>' . sprintf( esc_html__( 'Generated thumbnails: %d', 'pdf-thumbnail-generator' ), $generated_thumbs ) . '</div>';

						echo '<br><a href="' . remove_query_arg('generate') . '" class="button button-primary">' . __( 'Back to the settings', 'pdf-thumbnail-generator' ) . '</a>'; ?>
					</div>
				</div><?php
			}else{
				$show_update_notice = false;
				if( isset( $_POST['plugin_sent'] ) ){
					if( check_admin_referer( 'save_these_settings', 'settings_nonce' ) ){

						$this->settings = array();
						$this->settings['max_width'] = intval( $_POST['max_width'] );
						$this->settings['max_height'] = intval( $_POST['max_height'] );
						$this->settings['quality'] = intval( $_POST['quality'] );
						$this->settings['type'] = $_POST['type'] == 'jpg' ? 'jpg' : 'png';

						update_option( 'pdf_thumbnail_generator_settings', $this->settings );
						$show_update_notice = true;
					}
				} ?>
				<div class="wrap">
					<h2><?php _e( 'PDF Thumbnails', 'pdf-thumbnail-generator' ) ?></h2>
					<?php if( $show_update_notice ) echo '<div class="below-h2 updated"><p>' . __( 'Settings saved.', 'pdf-thumbnail-generator' ) . '</p></div>'; ?>
					<form method="post" action="<?php echo admin_url( 'options-general.php?page=' . basename( __FILE__ ) ) ?>">
						<input type="hidden" name="plugin_sent" value="1">
						<?php wp_nonce_field( 'save_these_settings', 'settings_nonce' ) ?>
						<table class="form-table">
							<tr>
								<th>
									<label for="q_field_1"><?php _e( 'Max width', 'pdf-thumbnail-generator' ) ?></label> 
								</th>
								<td>
									<input type="number" name="max_width" value="<?php echo intval( $this->settings['max_width'] ) ?>" id="q_field_1"> px
								</td>
							</tr>
							<tr>
								<th>
									<label for="q_field_2"><?php _e( 'Max height', 'pdf-thumbnail-generator' ) ?></label> 
								</th>
								<td>
									<input type="number" name="max_height" value="<?php echo intval( $this->settings['max_height'] ) ?>" id="q_field_2"> px
								</td>
							</tr>
							<tr>
								<th>
									<label for="q_field_3"><?php _e( 'Quality', 'pdf-thumbnail-generator' ) ?></label> 
								</th>
								<td>
									<input type="number" min="1" max="100" name="quality" value="<?php echo intval( $this->settings['quality'] ) ?>" id="q_field_3"> %&emsp;<small>(1-100)</small>
								</td>
							</tr>
							<tr>
								<th>
									<label><?php _e( 'Type', 'pdf-thumbnail-generator' ) ?></label> 
								</th>
								<td>
									<label>
										<input type="radio" name="type" value="png" <?php echo $this->settings['type'] == 'png' ? 'checked' : '' ?>> png
									</label>
									&emsp;
									<label>
										<input type="radio" name="type" value="jpg" <?php echo $this->settings['type'] == 'jpg' ? 'checked' : '' ?>> jpg
									</label>
								</td>
							</tr>
						</table>
						<p class="submit"><input type="submit" class="button button-primary button-large" value="<?php _e( 'Save', 'pdf-thumbnail-generator' ) ?>"></p>
					</form>

					<h3><?php esc_html_e( 'Generate thumbnails for already uploaded PDFs', 'pdf-thumbnail-generator' ) ?></h3>

					<p><?php esc_html_e( 'If you changed some settings, please save them firstly.', 'pdf-thumbnail-generator' ) ?></p>

					<a href="<?php echo add_query_arg( 'generate', 'missing' ) ?>" class="button button-primary">
						<?php esc_html_e( 'Generate missing PDF thumbnails', 'pdf-thumbnail-generator' ) ?>
					</a>
					&emsp;
					<a href="<?php echo add_query_arg( 'generate', 'all' ) ?>" class="button button-primary">
						<?php esc_html_e( 'Regenerate all PDF thumbnails', 'pdf-thumbnail-generator' ) ?>
					</a>
				</div><?php
			}
		}

		function generate_thumbnail( $pdf_id, $regenerate = false ){
			if( get_post_mime_type( $pdf_id ) === 'application/pdf' ){

				$thumbnail = get_post_meta( $pdf_id, '_pdf_thumbnail', true );
				if( $thumbnail ){
					if( $regenerate ){
						$this->delete( $pdf_id );
					}else{
						return false;
					}
				}

				set_time_limit( 0 );

				$max_width = apply_filters( 'pdf_thumbnail_max_width', $this->settings['max_width'], $pdf_id );
				$max_width = intval( $max_width );
				$max_height = apply_filters( 'pdf_thumbnail_max_height', $this->settings['max_height'], $pdf_id );
				$max_height = intval( $max_height );
				$quality = apply_filters( 'pdf_thumbnail_quality', $this->settings['quality'], $pdf_id );
				$quality = intval( $quality );
				$type = apply_filters( 'pdf_thumbnail_type', $this->settings['type'], $pdf_id );
				$type = $type == 'jpg' ? 'jpg' : 'png';

				$page_number = apply_filters( 'pdf_thumbnail_page_number', 0, $pdf_id );
				$page_number = intval( $page_number );

				$resolution = ceil( max( $max_height, $max_width ) * 0.16 );

				$bgcolor = apply_filters( 'pdf_thumbnail_bgcolor', 'white', $pdf_id );

				$filepath = get_attached_file( $pdf_id );

				$new_filename = sanitize_file_name( basename( $filepath ) . '.' . $type );
				$new_filename = wp_unique_filename( dirname( $filepath ), $new_filename );
				$new_filename = apply_filters( 'pdf_thumbnail_filename', $new_filename, $pdf_id );

				$new_filepath = str_replace( basename( $filepath ), $new_filename, $filepath );

				try{
					$imagick = new Imagick();
					$imagick->setResolution( $resolution, $resolution );
					$imagick->readimage( $filepath . '[' . $page_number . ']' );
					$imagick->setCompressionQuality( $quality );
					$imagick->scaleImage( $max_width, $max_height, true );
					$imagick->setImageFormat( $type ); 
					$imagick->setImageBackgroundColor( $bgcolor );
					if( method_exists( 'Imagick', 'setImageAlphaChannel' ) ){
						if( defined('Imagick::ALPHACHANNEL_REMOVE') ){
							$imagick->setImageAlphaChannel( Imagick::ALPHACHANNEL_REMOVE );
						}else{
							$imagick->setImageAlphaChannel( 11 );
						}
					}
					if( method_exists( 'Imagick','mergeImageLayers' ) ){
						$imagick->mergeImageLayers( Imagick::LAYERMETHOD_FLATTEN );
					}else{
						$imagick = $imagick->flattenImages();
					}
					$imagick = apply_filters( 'pdf_thumbnail_imagick', $imagick, $pdf_id );
					$imagick->stripImage();
					$imagick->writeImage( $new_filepath );
					$imagick->clear();
					update_post_meta( $pdf_id, '_pdf_thumbnail', $new_filename );
					do_action( 'pdf_thumbnail_generated', $new_filepath, $pdf_id );
				}catch( ImagickException $err ){
					error_log( $err );
				}catch( Exception $err ){
					error_log( $err );
				}

				return true;
			}
		}

		function wp_mime_type_icon( $icon, $mime, $pdf_id ){
			if( $mime === 'application/pdf' && strpos( $_SERVER['REQUEST_URI'], '/wp-admin/upload.php' ) === false ){
				$thumbnail_url = $this->get_url( $pdf_id );
				if( $thumbnail_url ){
					return $thumbnail_url;
				}
			}
			return $icon;
		}

		function delete( $pdf_id ){
			if( get_post_mime_type( $pdf_id ) === 'application/pdf' ){
				$thumbnail_filepath = $this->get_path( $pdf_id );
				if( $thumbnail_filepath ){
					unlink( $thumbnail_filepath );
				}
			}
		}

		function get_path( $pdf_id ){
			if( get_post_mime_type( $pdf_id ) === 'application/pdf' ){
				$thumbnail = get_post_meta( $pdf_id, '_pdf_thumbnail', true );
				if( $thumbnail ){
					$filepath = get_attached_file( $pdf_id );
					$thumbnail_filepath = str_replace( basename( $filepath ), $thumbnail, $filepath );
					if( file_exists( $thumbnail_filepath ) ){
						return $thumbnail_filepath;
					}
				}
			}
			return false;
		}

		function get_url( $pdf_id ){
			if( get_post_mime_type( $pdf_id ) === 'application/pdf' ){
				$thumbnail = get_post_meta( $pdf_id, '_pdf_thumbnail', true );
				if( $thumbnail ){
					$filepath = get_attached_file( $pdf_id );
					$thumbnail_filepath = str_replace( basename( $filepath ), $thumbnail, $filepath );
					if( file_exists( $thumbnail_filepath ) ){
						$thumbnail_url = wp_get_attachment_url( $pdf_id );
						return str_replace( basename( $filepath ), $thumbnail, $thumbnail_url );
					}
				}
			}
			return false;
		}

	}

	$pdf_thumbnail = new pdf_thumbnail_generator();
	register_activation_hook( __FILE__, array( $pdf_thumbnail, 'activate' ) );

	if( ! function_exists('get_pdf_thumbnail_url') ){
		function get_pdf_thumbnail_url( $pdf_id ){
			global $pdf_thumbnail;
			return $pdf_thumbnail->get_url( $pdf_id );
		}
	}

	if( ! function_exists('get_pdf_thumbnail_path') ){
		function get_pdf_thumbnail_path( $pdf_id ){
			global $pdf_thumbnail;
			return $pdf_thumbnail->get_path( $pdf_id );
		}
	}

	if( ! function_exists('get_pdf_thumbnail_image_src') ){
		function get_pdf_thumbnail_image_src( $pdf_id ){
			global $pdf_thumbnail;
			$data = false;
			$thumbnail_url = $pdf_thumbnail->get_url( $pdf_id );
			if( $thumbnail_url ){
				$thumbnail_path = $pdf_thumbnail->get_path( $pdf_id );
				$info = getimagesize( $thumbnail_path );
				$data = array( $thumbnail_url, $info[0], $info[1] );
			}
			return $data;
		}
	}

	if( ! function_exists('get_pdf_thumbnail_image') ){
		function get_pdf_thumbnail_image( $pdf_id ){
			global $pdf_thumbnail;
			$html = '';
			$thumbnail_url = $pdf_thumbnail->get_url( $pdf_id );
			if( $thumbnail_url ){
				$thumbnail_path = $pdf_thumbnail->get_path( $pdf_id );
				$info = getimagesize( $thumbnail_path );
				$default_attr = array(
					'src' => $thumbnail_url,
					'class' => 'pdf-thumbnail',
					'alt' => esc_attr( trim( get_the_title( $pdf_id ) ) ),
					'width' => $info[0],
					'height' => $info[1],
				);
		
				if( wp_lazy_loading_enabled( 'img', 'wp_get_attachment_image' ) ){
					$default_attr['loading'] = wp_get_loading_attr_default('wp_get_attachment_image');
				}
		
				if( array_key_exists( 'loading', $default_attr ) && ! $default_attr['loading'] ){
					unset( $default_attr['loading'] );
				}

				$attr = apply_filters( 'get_pdf_thumbnail_image_attributes', $default_attr, $pdf_id );
		
				$attr = array_map( 'esc_attr', $attr );
				$html = '<img';
		
				foreach( $attr as $name => $value ){
					$html .= ' ' . $name . '="' . $value . '"';
				}
		
				$html .= '>';
			}
			return $html;
		}
	}
}