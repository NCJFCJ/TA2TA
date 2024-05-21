<?php
namespace Barn2\Plugin\Posts_Table_Pro;


/**
 * Search Box Widget
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Search_Widget extends \WP_Widget {

	/**
	 * Initialize the widget
	 */
	public function __construct() {
		$options = [
			'description' => esc_html__( 'A global search box for your posts table.', 'posts-table-pro' ),
        ];

		parent::__construct( 'ptp-search-widget', esc_html__( 'Posts Table Pro: Search Box', 'posts-table-pro' ), $options );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

        wp_enqueue_style( 'posts-table-pro-search-box' );

		/* phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped */
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		echo Search_Handler::get_search_box_html( 'widget', $instance['placeholder'], $instance['button_text'] );

		echo $args['after_widget'];
		/* phpcs:enable */
	}

	/**
	 * Back-end widget form.
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Posts search', 'posts-table-pro' );
		$placeholder = ! empty( $instance['placeholder'] ) ? $instance['placeholder'] : esc_html__( 'Search posts...', 'posts-table-pro' );
		$button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : esc_html__( 'Search', 'posts-table-pro' );

		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'posts-table-pro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>"><?php esc_attr_e( 'Placeholder:', 'posts-table-pro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'placeholder' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'placeholder' ) ); ?>" type="text" value="<?php echo esc_attr( $placeholder ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_attr_e( 'Button Text:', 'posts-table-pro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" type="text" value="<?php echo esc_attr( $button_text ); ?>">
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = [];

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['placeholder'] = sanitize_text_field( $new_instance['placeholder'] );
		$instance['button_text'] = sanitize_text_field( $new_instance['button_text'] );

		return $instance;
	}
}
