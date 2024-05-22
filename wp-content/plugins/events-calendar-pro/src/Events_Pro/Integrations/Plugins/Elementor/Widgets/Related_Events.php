<?php
/**
 * Related Events Elementor Widget.
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets
 */

namespace TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets;

use Elementor\Controls_Manager;
use TEC\Events\Integrations\Plugins\Elementor\Widgets\Contracts\Abstract_Widget;
use TEC\Events\Integrations\Plugins\Elementor\Widgets\Traits;

/**
 * Class Widget_Related_Events
 *
 * @since 6.4.0
 *
 * @package TEC\Events_Pro\Integrations\Plugins\Elementor\Widgets
 */
class Related_Events extends Abstract_Widget {
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
	protected static string $slug = 'event_related';

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
		return esc_html__( 'Related Events', 'tribe-events-calendar-pro' );
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
		$event_id = $this->get_event_id();
		$events   = tribe_get_related_posts( 3, $event_id );
		$settings = $this->get_settings_for_display();

		return [
			'show_header'         => tribe_is_truthy( $settings['show_section_header'] ?? false ),
			'show_thumbnail'      => tribe_is_truthy( $settings['show_thumbnail'] ?? false ),
			'show_event_title'    => tribe_is_truthy( $settings['show_event_title'] ?? false ),
			'show_event_datetime' => tribe_is_truthy( $settings['show_event_datetime'] ?? false ),
			'header_tag'          => $settings['header_tag'] ?? 'h2',
			'datetime_tag'        => $settings['datetime_tag'] ?? 'p',
			'event_title_tag'     => $settings['event_title_tag'] ?? 'h3',
			'events'              => $events,
		];
	}

	/**
	 * Get the template args for the widget preview.
	 *
	 * @since 6.4.0
	 *
	 * @return array The template args for the preview.
	 */
	protected function preview_args(): array {
		$args = $this->template_args();

		if ( ! empty( $args['events'] ) ) {
			return $args;
		}

		// Rather than mock three complete events, we'll just grab up to three existing events.
		$args['events'] = tribe_get_events( [ 'posts_per_page' => 3 ] );

		return $args;
	}

	/**
	 * Get the class used for the related events container.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function get_container_class() {
		$class = $this->get_widget_class() . '-container';

		/**
		 * Filters the class used for the related events container.
		 *
		 * @since 6.4.0
		 *
		 * @param string          $class The class used for the related events container.
		 * @param Abstract_Widget $this  The widget instance.
		 *
		 * @return string
		 */
		return apply_filters( 'tec_events_pro_elementor_event_related_events_widget_container_class', $class, $this );
	}

	/**
	 * Get the class used for the related events header.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function get_header_class() {
		$class = $this->get_widget_class() . '-header';

		/**
		 * Filters the class used for the related events header.
		 *
		 * @since 6.4.0
		 *
		 * @param string          $class The class used for the related events header.
		 * @param Abstract_Widget $this  The widget instance.
		 *
		 * @return string
		 */
		return apply_filters( 'tec_events_pro_elementor_event_related_events_widget_header_class', $class, $this );
	}

	/**
	 * Get the class used for the related events list.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function get_list_class() {
		$class = $this->get_widget_class() . '-list';

		/**
		 * Filters the class used for the related events list.
		 *
		 * @since 6.4.0
		 *
		 * @param string          $class The class used for the related events list.
		 * @param Abstract_Widget $this  The widget instance.
		 *
		 * @return string
		 */
		return apply_filters( 'tec_events_pro_elementor_event_related_events_widget_list_class', $class, $this );
	}

	/**
	 * Get the class used for the related events image link.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function get_image_link_class() {
		$class = $this->get_widget_class() . '-image-link';

		/**
		 * Filters the class used for the related events image link.
		 *
		 * @since 6.4.0
		 *
		 * @param string          $class The class used for the related events image link.
		 * @param Abstract_Widget $this  The widget instance.
		 *
		 * @return string
		 */
		return apply_filters( 'tec_events_pro_elementor_event_related_events_widget_image_link_class', $class, $this );
	}

	/**
	 * Get the class used for the related events title link.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function get_title_link_class() {
		$class = $this->get_widget_class() . '-title-link';

		/**
		 * Filters the class used for the related events title link.
		 *
		 * @since 6.4.0
		 *
		 * @param string          $class The class used for the related events title link.
		 * @param Abstract_Widget $this  The widget instance.
		 *
		 * @return string
		 */
		return apply_filters( 'tec_events_pro_elementor_event_related_events_widget_title_link_class', $class, $this );
	}

	/**
	 * Get the class used for the related events list item.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function get_list_item_class() {
		$class = $this->get_widget_class() . '-list-item';

		/**
		 * Filters the class used for the related events list item.
		 *
		 * @since 6.4.0
		 *
		 * @param string          $class The class used for the related events list item.
		 * @param Abstract_Widget $this  The widget instance.
		 *
		 * @return string
		 */
		return apply_filters( 'tec_events_pro_elementor_event_related_events_widget_list_item_class', $class, $this );
	}

	/**
	 * Get the class used for the related events thumbnail.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function get_thumbnail_class() {
		$class = $this->get_widget_class() . '-thumbnail';

		/**
		 * Filters the class used for the related events thumbnail.
		 *
		 * @since 6.4.0
		 *
		 * @param string          $class The class used for the related events thumbnail.
		 * @param Abstract_Widget $this  The widget instance.
		 *
		 * @return string
		 */
		return apply_filters( 'tec_events_pro_elementor_event_related_events_widget_thumbnail_class', $class, $this );
	}

	/**
	 * Get the class used for the related events info.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function get_info_class() {
		$class = $this->get_widget_class() . '-info';

		/**
		 * Filters the class used for the related events info.
		 *
		 * @since 6.4.0
		 *
		 * @param string          $class The class used for the related events info.
		 * @param Abstract_Widget $this  The widget instance.
		 *
		 * @return string
		 */
		return apply_filters( 'tec_events_pro_elementor_event_related_events_widget_info_class', $class, $this );
	}

	/**
	 * Get the class used for the related events title.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function get_title_class() {
		$class = $this->get_widget_class() . '-title';

		/**
		 * Filters the class used for the related events title.
		 *
		 * @since 6.4.0
		 *
		 * @param string          $class The class used for the related events title.
		 * @param Abstract_Widget $this  The widget instance.
		 *
		 * @return string
		 */
		return apply_filters( 'tec_events_pro_elementor_event_related_events_widget_title_class', $class, $this );
	}

	/**
	 * Get the class used for the related events date-time.
	 *
	 * @since 6.4.0
	 *
	 * @return string
	 */
	public function get_datetime_class() {
		$class = $this->get_widget_class() . '-datetime';

		/**
		 * Filters the class used for the related events date-time.
		 *
		 * @since 6.4.0
		 *
		 * @param string          $class The class used for the related events date-time.
		 * @param Abstract_Widget $this  The widget instance.
		 *
		 * @return string
		 */
		return apply_filters( 'tec_events_pro_elementor_event_related_events_widget_datetime_class', $class, $this );
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

		$this->event_title_content_options();

		$this->event_datetime_content_options();

		$this->add_event_query_section();
	}

	/**
	 * Add styling controls for the widget.
	 *
	 * @since 6.4.0
	 */
	protected function style_panel() {
		// Styling options.
		$this->style_section_header();

		$this->style_event_title();

		$this->style_event_datetime();
	}

	/**
	 * Add controls for text content of the related events.
	 *
	 * @since 6.4.0
	 */
	protected function content_options() {
		$this->start_controls_section(
			'section_title',
			[
				'label' => $this->get_title(),
			]
		);

		// Show Related Events Header control.
		$this->add_shared_control(
			'show',
			[
				'id'    => 'show_section_header',
				'label' => esc_html__( 'Show Header', 'the-events-calendar' ),
			]
		);

		$this->add_shared_control(
			'tag',
			[
				'id'        => 'header_tag',
				'label'     => esc_html__( 'Header HTML Tag', 'the-events-calendar' ),
				'default'   => 'h2',
				'condition' => [
					'show_section_header' => 'yes',
				],
			]
		);

		// Show Thumbnail control.
		$this->add_shared_control(
			'show',
			[
				'id'    => 'show_thumbnail',
				'label' => esc_html__( 'Show Event Thumbnail', 'the-events-calendar' ),
			]
		);

		// Show Event Title control.
		$this->add_shared_control(
			'show',
			[
				'id'    => 'show_event_title',
				'label' => esc_html__( 'Show Event Title', 'the-events-calendar' ),
			]
		);

		// Show Date/Time control.
		$this->add_shared_control(
			'show',
			[
				'id'    => 'show_event_datetime',
				'label' => esc_html__( 'Show Event Date & Time', 'the-events-calendar' ),
			]
		);

		$this->add_shared_control(
			'alignment',
			[
				'id'        => 'align',
				'selectors' => [ '{{WRAPPER}} .' . $this->get_list_class() ],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add controls for text content of the event title.
	 *
	 * @since 6.4.0
	 */
	protected function event_title_content_options() {
		$this->start_controls_section(
			'event_title_content_options',
			[
				'label'     => esc_html__( 'Event Title', 'tribe-events-calendar-pro' ),
				'condition' => [
					'show_event_title' => 'yes',
				],
			]
		);

		$this->add_shared_control(
			'tag',
			[
				'id'    => 'event_title_tag',
				'label' => esc_html__( 'HTML Tag', 'the-events-calendar' ),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Add controls for text content of the event datetime.
	 *
	 * @since 6.4.0
	 */
	protected function event_datetime_content_options() {
		$this->start_controls_section(
			'datetime_content_options',
			[
				'label'     => esc_html__( 'Event Datetime', 'tribe-events-calendar-pro' ),
				'condition' => [
					'show_event_datetime' => 'yes',
				],
			]
		);

		$this->add_shared_control(
			'tag',
			[
				'id'      => 'datetime_tag',
				'label'   => esc_html__( 'HTML Tag', 'the-events-calendar' ),
				'default' => 'p',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Assembles the styling controls for the related events header.
	 *
	 * @since 6.4.0
	 *
	 * @return void
	 */
	protected function style_section_header() {
		$this->start_controls_section(
			'header_styling',
			[
				'label'     => esc_html__( 'Section Header', 'tribe-events-calendar-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_section_header' => 'yes',
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
	 * Assembles the styling controls for the event title.
	 *
	 * @since 6.4.0
	 *
	 * @return void
	 */
	protected function style_event_title() {
		$this->start_controls_section(
			'title_styling',
			[
				'label'     => esc_html__( 'Event Title', 'tribe-events-calendar-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_event_title' => 'yes',
				],
			]
		);

		$this->add_shared_control(
			'typography',
			[
				'prefix'   => 'title',
				'selector' => '{{WRAPPER}} .' . $this->get_title_link_class(),
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Assembles the styling controls for the event datetime.
	 *
	 * @since 6.4.0
	 *
	 * @return void
	 */
	protected function style_event_datetime() {
		$this->start_controls_section(
			'datetime_styling',
			[
				'label'     => esc_html__( 'Event Datetime', 'tribe-events-calendar-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_event_datetime' => 'yes',
				],
			]
		);

		$this->add_shared_control(
			'typography',
			[
				'prefix'   => 'datetime',
				'selector' => '{{WRAPPER}} .' . $this->get_datetime_class(),
			]
		);

		$this->end_controls_section();
	}
}
