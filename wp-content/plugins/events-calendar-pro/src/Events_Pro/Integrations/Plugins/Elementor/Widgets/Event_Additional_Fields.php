<?php
/**
 * Additional Fields Elementor Widget.
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets
 */

namespace TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use TEC\Events\Integrations\Plugins\Elementor\Widgets\Contracts\Abstract_Widget;
use TEC\Events\Integrations\Plugins\Elementor\Widgets\Traits;

/**
 * Class Event_Additional_Fields
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets
 */
class Event_Additional_Fields extends Abstract_Widget {
	use Traits\With_Shared_Controls;
	use Traits\Has_Preview_Data;
	use Traits\Event_Query;

	/**
	 * Widget slug.
	 *
	 * @since 6.4.0
	 *
	 * @var string
	 */
	protected static string $slug = 'event_additional_fields';

	/**
	 * Whether the widget has styles to register/enqueue.
	 *
	 * @since 6.4.0
	 *
	 * @var bool
	 */
	protected static bool $has_styles = true;

	/**
	 * Create the widget title.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	protected function title(): string {
		return esc_html__( 'Event Additional Fields', 'tribe-events-calendar-pro' );
	}

	/**
	 * Get the asset source for the widget.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function get_asset_source() {
		return tribe( 'events-pro.main' );
	}

	/**
	 * Get the template args for the widget.
	 *
	 * @since 6.4.0
	 *
	 * @return array The template args.
	 */
	protected function template_args(): array {
		$settings = $this->get_settings_for_display();

		return [
			'show_header' => tribe_is_truthy( $settings['show_header'] ?? true ),
			'header_text' => $this->get_header_text(),
			'header_tag'  => $settings['header_tag'] ?? 'h2',
			'fields'      => $this->get_fields(),
		];
	}

	/**
	 * Get the template args for the widget preview.
	 *
	 * @since 6.4.0
	 *
	 * @return array The template args.
	 */
	protected function preview_args(): array {
		$args = $this->template_args();

		if ( ! empty( $args['fields'] ) ) {
			return $args;
		}

		$args['fields'] = [
			'mock-1' => [
				'label' => esc_html__( 'Mock Field 1', 'tribe-events-calendar-pro' ),
				'value' => esc_html__( 'Mock Value 1', 'tribe-events-calendar-pro' ),
			],
			'mock-2' => [
				'label' => esc_html__( 'Mock Field 2', 'tribe-events-calendar-pro' ),
				'value' => esc_html__( 'Mock Value 2', 'tribe-events-calendar-pro' ),
			],
		];

		return $args;
	}

	/**
	 * Gathers and formats an array of applicable custom fields for the current event.
	 *
	 * @since 6.4.0
	 *
	 * @return array<string,mixed> The custom fields for the current event.
	 */
	protected function get_fields() {
		$all_fields = tribe_get_option( 'custom-fields', [] );

		if ( empty( $all_fields ) ) {
			return [];
		}

		$fields   = [];
		$event_id = $this->get_event_id();

		foreach ( $all_fields as $key => $field ) {
			$field_value = get_post_meta( $event_id, $field['name'], true );
			if ( empty( $field_value ) ) {
				continue;
			}

			$fields[ $field['name'] ] = [
				'label' => $field['label'] ?? '',
				'value' => $field_value,
			];
		}

		return $fields;
	}

	/**
	 * Get the CSS class for the wrapper.
	 *
	 * @since 6.4.0
	 *
	 * @return string The CSS class for the wrapper.
	 */
	public function get_wrapper_class(): string {
		return $this->get_widget_class() . '-wrapper';
	}

	/**
	 * Get the CSS class for the header.
	 *
	 * @since 6.4.0
	 *
	 * @return string The CSS class for the header.
	 */
	public function get_header_class(): string {
		return $this->get_widget_class() . '-header';
	}

	/**
	 * Get the CSS class for the field label.
	 *
	 * @since 6.4.0
	 *
	 * @return string The CSS class for the field label.
	 */
	public function get_field_label_class(): string {
		return $this->get_widget_class() . '-label';
	}

	/**
	 * Get the CSS class for the field value.
	 *
	 * @since 6.4.0
	 *
	 * @return string The CSS class for the field value.
	 */
	public function get_field_value_class(): string {
		return $this->get_widget_class() . '-value';
	}

	/**
	 * Get the label for the all events back link.
	 *
	 * @since 6.4.0
	 *
	 * @return string The label for the all events back link.
	 */
	protected function get_header_text(): string {
		$label_text = esc_html__( 'Other', 'tribe-events-calendar-pro' );

		/**
		 * Filters the label text for the event additional fields widget.
		 *
		 * @since 6.4.0
		 *
		 * @param string         $label_text The label text.
		 * @param Event_Additional_Fields $this The event additional fields widget instance.
		 *
		 * @return string The filtered label text.
		 */
		return apply_filters( 'tec_events_pro_elementor_event_additional_fields_widget_label_text', $label_text, $this );
	}

	/**
	 * Register controls for the widget.
	 *
	 * @since 6.4.0
	 */
	protected function register_controls() {
		// Content tab.
		$this->content_panel();
		// Style tab.
		$this->style_panel();
	}

	/**
	 * Add content controls for the widget.
	 *
	 * @since 6.4.0
	 */
	protected function content_panel() {
		$this->content_options();

		$this->add_event_query_section();
	}

	/**
	 * Add styling controls for the widget.
	 *
	 * @since 6.4.0
	 */
	protected function style_panel() {
		$this->header_styling();
		$this->fields_label_styling();
		$this->fields_value_styling();
	}

	/**
	 * Add controls for text content of the additional fields.
	 *
	 * @since 6.4.0
	 */
	protected function content_options() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Additional Fields', 'tribe-events-calendar-pro' ),
			]
		);

		$this->add_shared_control(
			'show',
			[
				'id'    => 'show_header',
				'label' => esc_html__( 'Show Header', 'the-events-calendar' ),
			]
		);

		$this->add_shared_control(
			'tag',
			[
				'id'        => 'header_tag',
				'label'     => esc_html__( 'Header HTML Tag', 'the-events-calendar' ),
				'condition' => [
					'show_header' => 'yes',
				],
			]
		);

		$this->add_shared_control(
			'alignment',
			[
				'id'        => 'align',
				'selectors' => [
					'{{WRAPPER}} .' . $this->get_wrapper_class(),
					'{{WRAPPER}} .' . $this->get_header_class(),
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add controls for text styling of the section header.
	 *
	 * @since 6.4.0
	 */
	protected function header_styling() {
		$this->start_controls_section(
			'header_styling_title',
			[
				'label'     => esc_html__( 'Section header', 'tribe-events-calendar-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_header' => 'yes',
				],
			]
		);

		$this->add_shared_control(
			'typography',
			[
				'prefix'   => 'header',
				'selector' => '{{WRAPPER}} .' . $this->get_header_class(),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add controls for text styling of the fields.
	 *
	 * @since 6.4.0
	 */
	protected function fields_label_styling() {
		$this->start_controls_section(
			'fields_label_styling_title',
			[
				'label' => esc_html__( 'Additional Fields Label', 'tribe-events-calendar-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_shared_control(
			'typography',
			[
				'prefix'   => 'fields_label',
				'selector' => '{{WRAPPER}} .' . $this->get_field_label_class(),
			]
		);

		$this->end_controls_section();
	}
	/**
	 * Add controls for text styling of the fields.
	 *
	 * @since 6.4.0
	 */
	protected function fields_value_styling() {
		$this->start_controls_section(
			'fields_value_styling_title',
			[
				'label' => esc_html__( 'Additional Fields Value', 'tribe-events-calendar-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_shared_control(
			'typography',
			[
				'prefix'   => 'fields_value',
				'selector' => '{{WRAPPER}} .' . $this->get_field_value_class(),
			]
		);

		$this->add_control(
			'value_link_color',
			[
				'label'     => esc_html__( 'Link Color', 'tribe-events-calendar-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .' . $this->get_field_value_class() . ' a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}
}
