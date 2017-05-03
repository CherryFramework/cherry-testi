<?php
/**
 * New shortcode registration.
 *
 * @package    Cherry_Testi
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Class for Testimonials shortcode.
 *
 * @since 1.0.0
 */
class TM_Testimonials_Shortcode {

	/**
	 * Shortcode name.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	public static $name = 'testimonials';

	/**
	 * Unique shortcode prefix.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private $prefix = 'tm_';

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_shortcode' ) );

		add_action( 'vc_before_init', array( $this, 'foo' ) );

		if ( is_admin() ) {
			$this->register_shortcode_for_builder();
		}
	}

	public function foo() {
		require_once( TM_TESTI_DIR . 'includes/ext/example.php' );
	}

	/**
	 * Registers shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcode() {
		add_shortcode( $this->get_tag(), array( $this, 'do_shortcode' ) );
	}

	/**
	 * The shortcode function.
	 *
	 * @since  1.0.0
	 * @param  array  $atts      The user-inputted arguments.
	 * @param  string $content   The enclosed content.
	 * @param  string $shortcode The shortcode tag.
	 * @return string
	 */
	public function do_shortcode( $atts, $content = null, $shortcode = 'testimonials' ) {

		// Set up the default arguments.
		$defaults = wp_list_pluck( $this->get_shortcode_atts(), 'default' );

		/**
		 * Parse the arguments.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/shortcode_atts
		 */
		$atts = shortcode_atts( $defaults, $atts, $shortcode );

		// Make sure we return and don't echo.
		$atts['echo'] = false;

		// Fix integers.
		$atts['limit'] = intval( $atts['limit'] );
		$atts['content_length'] = intval( $atts['content_length'] );

		if ( isset( $atts['size'] ) && ( 0 < intval( $atts['size'] ) ) ) {
			$atts['size'] = intval( $atts['size'] );
		} else {
			$atts['size'] = esc_attr( $atts['size'] );
		}

		$var_to_bool = array(
			'show_avatar',
			'show_email',
			'show_position',
			'show_company',
			'divider',
		);

		// Fix booleans.
		foreach ( $var_to_bool as $v ) {
			$atts[ $v ] = filter_var( $atts[ $v ], FILTER_VALIDATE_BOOLEAN );
		}

		// Whitelist.
		$atts['type'] = esc_attr( $atts['type'] );

		if ( false === in_array( $atts['type'], array( 'list', 'slider' ), true ) ) {
			$atts['type'] = 'list';
		}

		if ( 'list' === $atts['type'] ) {
			$extra_classes = array( 'tm-testi__wrap--shortcode', 'tm-testi__wrap--listing' );
		} else {
			$extra_classes = array( 'tm-testi__wrap--shortcode', 'swiper-container', 'tm-testi-slider' );
			$atts          = $this->prepare_slider_atts( $defaults, $atts );
		}

		if ( ! empty( $atts['custom_class'] ) ) {
			array_push( $extra_classes, $atts['custom_class'] );
		}

		// CSS classes.
		$atts['custom_class'] = join( ' ', $extra_classes );

		$data = TM_Testimonials_Data::get_instance();

		return $data->the_testimonials( $atts );
	}

	/**
	 * Prepare attributes for `slider` shortcode type.
	 *
	 * @since  1.0.0
	 * @param  array $defaults The default arguments.
	 * @param  array $atts     The user-inputted arguments.
	 * @return array
	 */
	public function prepare_slider_atts( $defaults, $atts ) {
		$atts['context'] = 'slider';

		// Fix boolean.
		if ( isset( $atts['loop'] ) && ( 'on' == $atts['loop'] ) ) {
			$atts['loop'] = true;
		} else {
			$atts['loop'] = false;
		}

		if ( isset( $atts['pagination'] ) && ( 'on' == $atts['pagination'] ) ) {
			$atts['pagination'] = true;
		} else {
			$atts['pagination'] = false;
		}

		if ( isset( $atts['navigation'] ) && ( 'on' == $atts['navigation'] ) ) {
			$atts['navigation'] = true;
		} else {
			$atts['navigation'] = false;
		}

		// Fix slides_per_view
		foreach ( array( 'slides_per_view', 'slides_per_view_laptop', 'slides_per_view_tablet', 'slides_per_view_phone' ) as $val ) {
			$atts[ $val ] = ( 0 !== intval( $atts[ $val ] ) ) ? intval( $atts[ $val ] ) : 1;
		}

		// Fix space_between
		foreach ( array( 'space_between', 'space_between_laptop', 'space_between_tablet', 'space_between_phone' ) as $val ) {
			$atts[ $val ] = ( 0 !== intval( $atts[ $val ] ) ) ? intval( $atts[ $val ] ) : 15;
		}

		$atts['data_atts'] = apply_filters( 'tm_testimonials_slider_data_atts', array(
			'autoplay'      => intval( $atts['autoplay'] ),
			'effect'        => sanitize_key( $atts['effect'] ),
			'loop'          => (bool) $atts['loop'],
			'slidesPerView' => $atts['slides_per_view'],
			'spaceBetween'  => $atts['space_between'],
			'breakpoints'   => array(
				'1199' => array(
					'slidesPerView' => $atts['slides_per_view_laptop'],
					'spaceBetween'  => $atts['space_between_laptop'],
				),
				'991' => array(
					'slidesPerView' => $atts['slides_per_view_tablet'],
					'spaceBetween'  => $atts['space_between_tablet'],
				),
				'767' => array(
					'slidesPerView' => $atts['slides_per_view_phone'],
					'spaceBetween'  => $atts['space_between_phone'],
				),
			),
		), $defaults, $atts );

		add_filter( 'tm_testimonials_wrapper_format', array( 'TM_Testimonials_Hook', 'slider_settings' ), 10, 2 );
		add_filter( 'tm_the_testimonials_args',       array( 'TM_Testimonials_Hook', 'slider_container_class' ) );
		add_filter( 'tm_testimonials_item_classes',   array( 'TM_Testimonials_Hook', 'slider_item_class' ), 10, 2 );
		add_filter( 'tm_testimonials_html',           array( 'TM_Testimonials_Hook', 'slider_script' ), 10, 3 );
		add_filter( 'tm_testimonials_loop_after',     array( 'TM_Testimonials_Hook', 'slider_pagination' ), 10, 2 );
		add_filter( 'tm_testimonials_loop_after',     array( 'TM_Testimonials_Hook', 'slider_navigation' ), 10, 2 );

		return $atts;
	}

	/**
	 * Register shortcode for shortcodes builder.
	 *
	 * @since 1.1.0
	 */
	public function register_shortcode_for_builder() {
		tm_testimonials_plugin()->get_core()->init_module( 'cherry5-insert-shortcode', array() );

		cherry5_register_shortcode(
			array(
				'title'       => esc_html__( 'Testimonials', 'cherry-testi' ),
				'description' => esc_html__( 'A testimonials management plugin for WordPress', 'cherry-testi' ),
				'icon'        => '<span class="dashicons dashicons-testimonial"></span>',
				'slug'        => 'cherry-testi-plugin',
				'shortcodes'  => array(
					array(
						'title'       => esc_html__( 'Testimonials', 'cherry-testi' ),
						'description' => esc_html__( 'Shortcode is used to display the testimonials', 'cherry-testi' ),
						'icon'        => '<span class="dashicons dashicons-testimonial"></span>',
						'slug'        => $this->get_tag(),
						'options'     => $this->get_shortcode_atts(),
					),
				),
			)
		);
	}

	/**
	 * Retrieve a shortcode prefix.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_prefix() {
		/**
		 * Filters a shortcode prefix.
		 *
		 * @since 1.0.0
		 * @param string $prefix Shortcode prefix.
		 */
		return apply_filters( 'tm_testimonials_shortcode_prefix', $this->prefix );
	}

	/**
	 * Retrieve a shortcode tag.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_tag() {
		$tag = $this->get_prefix() . self::$name;

		/**
		 * Filters a shortcode tag.
		 *
		 * @since 1.0.0
		 * @param string $name Shortcode tag.
		 */
		return apply_filters( $tag . '_shortcode_name', $tag );
	}

	/**
	 * Retrieve a shortcode attributes.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_shortcode_atts() {
		return apply_filters( 'tm_testimonials_get_shortcode_atts', array(
			'type' => array(
				'type'        => 'select',
				'value'       => 'list',
				'title'       => esc_html__( 'Type', 'cherry-testi' ),
				'description' => esc_html__( 'Layout type (`list` or `slider`)', 'cherry-testi' ),
				'options'     => array(
					'list'   => esc_html__( 'List', 'cherry-testi' ),
					'slider' => esc_html__( 'Slider', 'cherry-testi' ),
				),
			),
			'sup_title' => array(
				'type'        => 'text',
				'value'       => '',
				'title'       => esc_html__( 'Suptitle', 'cherry-testi' ),
				'description' => esc_html__( 'Text before main title', 'cherry-testi' ),
			),
			'title' => array(
				'type'        => 'text',
				'value'       => '',
				'title'       => esc_html__( 'Title', 'cherry-testi' ),
				'description' => esc_html__( 'Main title', 'cherry-testi' ),
			),
			'sub_title' => array(
				'type'        => 'text',
				'value'       => '',
				'title'       => esc_html__( 'Subtitle', 'cherry-testi' ),
				'description' => esc_html__( 'Text after main title', 'cherry-testi' ),
			),
			'limit' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Limit', 'cherry-testi' ),
				'description' => esc_html__( 'Testimonials number to show', 'cherry-testi' ),
				'max_value'   => 50,
				'min_value'   => -1,
				'value'       => 3,
			),
			'orderby' => array(
				'type'        => 'select',
				'value'       => 'date',
				'title'       => esc_html__( 'Order by', 'cherry-testi' ),
				'description' => esc_html__( 'Order testimonials by', 'cherry-testi' ),
				'options'     => array(
					'date' => esc_html__( 'Date', 'cherry-testi' ),
				),
			),
			'order' => array(
				'type'        => 'select',
				'value'       => 'DESC',
				'title'       => esc_html__( 'Order', 'cherry-testi' ),
				'description' => esc_html__( 'Testimonials order (`DESC` or `ASC`)', 'cherry-testi' ),
				'options'     => array(
					'ASC'  => esc_html__( 'ASC', 'cherry-testi' ),
					'DESC' => esc_html__( 'DESC', 'cherry-testi' ),
				),
			),
			'category' => array(
				'type'        => 'select',
				'value'       => '',
				'multiple'    => true,
				'title'       => esc_html__( 'Category', 'cherry-testi' ),
				'description' => esc_html__( 'Select category to show testimonials from (use category slug, pass multiplie categories via comma)', 'cherry-testi' ),
				'class'       => 'cherry-multi-select',
				'options'     => false,
				'options_cb'  => array( $this, 'get_categories' ),
			),
			'ids' => array(
				'type'        => 'text',
				'value'       => '',
				'title'       => esc_html__( "Post ID's", 'cherry-testi' ),
				'description' => esc_html__( "Enter comma separated ID's of the testimonials that you want to show", 'cherry-testi' ),
			),
			'content_length' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Content Length', 'cherry-testi' ),
				'description' => esc_html__( 'The number of words you want to show in the testimonial content.', 'cherry-testi' ),
				'max_value'   => 150,
				'min_value'   => -1,
				'value'       => 55,
			),
			'divider' => array(
				'type'        => 'switcher',
				'value'       => 'off',
				'title'       => esc_html__( 'Divider', 'cherry-testi' ),
				'description' => esc_html__( 'Divider between title and testimonials', 'cherry-testi' ),
				'options'     => array(
					'on'  => esc_html__( 'On', 'cherry-testi' ),
					'off' => esc_html__( 'Off', 'cherry-testi' ),
				),
			),
			'show_avatar' => array(
				'type'        => 'switcher',
				'value'       => 'on',
				'title'       => esc_html__( 'Avatar', 'cherry-testi' ),
				'description' => esc_html__( "Author's avatar", 'cherry-testi' ),
				'options'     => array(
					'on'  => esc_html__( 'On', 'cherry-testi' ),
					'off' => esc_html__( 'Off', 'cherry-testi' ),
				),
			),
			'size' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Avatar size', 'cherry-testi' ),
				'description' => esc_html__( 'Avatar size (in pixels)', 'cherry-testi' ),
				'max_value'   => 500,
				'min_value'   => 50,
				'value'       => 100,
			),
			'show_email' => array(
				'type'        => 'switcher',
				'value'       => 'on',
				'title'       => esc_html__( 'Email', 'cherry-testi' ),
				'description' => esc_html__( "Author's email", 'cherry-testi' ),
				'options'     => array(
					'on'  => esc_html__( 'On', 'cherry-testi' ),
					'off' => esc_html__( 'Off', 'cherry-testi' ),
				),
			),
			'show_position' => array(
				'type'        => 'switcher',
				'value'       => 'on',
				'title'       => esc_html__( 'Position', 'cherry-testi' ),
				'description' => esc_html__( "Author's position", 'cherry-testi' ),
				'options'     => array(
					'on'  => esc_html__( 'On', 'cherry-testi' ),
					'off' => esc_html__( 'Off', 'cherry-testi' ),
				),
			),
			'show_company' => array(
				'type'        => 'switcher',
				'value'       => 'on',
				'title'       => esc_html__( 'Company', 'cherry-testi' ),
				'description' => esc_html__( "Author's company", 'cherry-testi' ),
				'options'     => array(
					'on'  => esc_html__( 'On', 'cherry-testi' ),
					'off' => esc_html__( 'Off', 'cherry-testi' ),
				),
			),
			'autoplay' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Autoplay', 'cherry-testi' ),
				'description' => esc_html__( 'Delay between transitions, in ms (only for slider)', 'cherry-testi' ),
				'max_value'   => 15000,
				'min_value'   => 500,
				'value'       => 7000,
				'step_value'  => 500,
			),
			'effect' => array(
				'type'        => 'select',
				'value'       => 'slide',
				'title'       => esc_html__( 'Effect', 'cherry-testi' ),
				'description' => esc_html__( 'Could be "slide" or "fade" (only for slider)', 'cherry-testi' ),
				'options'     => array(
					'slide' => esc_html__( 'Slide', 'cherry-testi' ),
					'fade'  => esc_html__( 'Fade', 'cherry-testi' ),
				),
			),
			'loop' => array(
				'type'        => 'switcher',
				'value'       => 'on',
				'title'       => esc_html__( 'Loop', 'cherry-testi' ),
				'description' => esc_html__( 'Set to on to enable continuous loop mode (only for slider)', 'cherry-testi' ),
				'options'     => array(
					'on'  => esc_html__( 'On', 'cherry-testi' ),
					'off' => esc_html__( 'Off', 'cherry-testi' ),
				),
			),
			'pagination' => array(
				'type'        => 'switcher',
				'value'       => 'on',
				'title'       => esc_html__( 'Pagination', 'cherry-testi' ),
				'description' => esc_html__( 'Pagination (only for slider)', 'cherry-testi' ),
				'options'     => array(
					'on'  => esc_html__( 'On', 'cherry-testi' ),
					'off' => esc_html__( 'Off', 'cherry-testi' ),
				),
			),
			'navigation' => array(
				'type'        => 'switcher',
				'value'       => 'on',
				'title'       => esc_html__( 'Navigation', 'cherry-testi' ),
				'description' => esc_html__( 'Navigation (only for slider)', 'cherry-testi' ),
				'options'     => array(
					'on'  => esc_html__( 'On', 'cherry-testi' ),
					'off' => esc_html__( 'Off', 'cherry-testi' ),
				),
			),
			'slides_per_view_phone' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Number of slides per view on phones', 'cherry-testi' ),
				'description' => esc_html__( "Slides visible at the same time on slider's containe (only for slider on phones)", 'cherry-testi' ),
				'max_value'   => 2,
				'min_value'   => 1,
				'value'       => 1,
			),
			'slides_per_view_tablet' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Number of slides per view on tablets', 'cherry-testi' ),
				'description' => esc_html__( "Slides visible at the same time on slider's containe (only for slider on tablets)", 'cherry-testi' ),
				'max_value'   => 4,
				'min_value'   => 1,
				'value'       => 1,
			),
			'slides_per_view_laptop' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Number of slides per view on laptops', 'cherry-testi' ),
				'description' => esc_html__( "Slides visible at the same time on slider's containe (only for slider on laptops)", 'cherry-testi' ),
				'max_value'   => 6,
				'min_value'   => 1,
				'value'       => 1,
			),
			'slides_per_view' => array(
				'type'        => 'slider',
				'value'       => 1,
				'title'       => esc_html__( 'Number of slides per view on desktops', 'cherry-testi' ),
				'description' => esc_html__( "Slides visible at the same time on slider's containe (only for slider on desktops)", 'cherry-testi' ),
				'max_value'   => 8,
				'min_value'   => 1,
				'value'       => 1,
			),
			'space_between_phone' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Space between on phones', 'cherry-testi' ),
				'description' => esc_html__( 'Distance between slides in px (only for slider on phones)', 'cherry-testi' ),
				'max_value'   => 100,
				'min_value'   => 0,
				'value'       => 15,
			),
			'space_between_tablet' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Space between on tablets', 'cherry-testi' ),
				'description' => esc_html__( 'Distance between slides in px (only for slider on tablets)', 'cherry-testi' ),
				'max_value'   => 100,
				'min_value'   => 0,
				'value'       => 15,
			),
			'space_between_laptop' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Space between on laptops', 'cherry-testi' ),
				'description' => esc_html__( 'Distance between slides in px (only for slider on laptops)', 'cherry-testi' ),
				'max_value'   => 100,
				'min_value'   => 0,
				'value'       => 15,
			),
			'space_between' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Space between', 'cherry-testi' ),
				'description' => esc_html__( 'Distance between slides in px (only for slider on desktops)', 'cherry-testi' ),
				'max_value'   => 100,
				'min_value'   => 0,
				'value'       => 15,
			),
			'template' => array(
				'type'        => 'select',
				'value'       => 'default.tmpl',
				'title'       => esc_html__( 'Template', 'cherry-testi' ),
				'description' => esc_html__( 'Template name to use', 'cherry-testi' ),
				'options'     => false,
				'options_cb'  => array( tm_testimonials_page_template(), 'get_templates_list' ),
			),
			'custom_class' => array(
				'type'        => 'text',
				'value'       => '',
				'title'       => esc_html__( 'Class', 'cherry-testi' ),
				'description' => esc_html__( 'Extra CSS class', 'cherry-testi' ),
			),
		), $this );
	}

	/**
	 * Returns categories list.
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_categories() {
		$post_type  = tm_testimonials_plugin()->get_post_type_name();
		$tax_name   = $post_type . '_category';
		$utility    = tm_testimonials_plugin()->get_core()->init_module( 'cherry-utility', array() );
		$categories = $utility->utility->satellite->get_terms_array( $tax_name, 'slug' );

		if ( empty( $categories ) ) {
			return array();
		}

		return array_merge(
			array( '' => esc_html__( 'From All', 'cherry-testi' ) ),
			$categories
		);
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

TM_Testimonials_Shortcode::get_instance();
