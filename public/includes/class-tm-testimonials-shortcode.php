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
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Unique shortcode prefix.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private $prefix = 'tm_';

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_shortcode' ) );
	}

	/**
	 * Registers shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcode() {
		$prefix = $this->get_prefix();

		add_shortcode( $prefix . 'testimonials', array( $this, 'do_shortcode' ) );
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
		if ( isset( $atts['limit'] ) ) {
			$atts['limit'] = intval( $atts['limit'] );
		}

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

		$atts['content_length'] = intval( $atts['content_length'] );

		// CSS classes.
		$custom_class  = $atts['custom_class'];

		if ( 'list' === $atts['type']  ) {
			$extra_classes = array( 'tm-testi__wrap--shortcode', 'tm-testi__wrap--listing' );
		} else {
			$extra_classes = array( 'tm-testi__wrap--shortcode', 'swiper-container', 'tm-testi-slider' );
			$atts          = $this->prepare_slider_atts( $defaults, $atts );
		}

		if ( ! empty( $custom_class ) ) {
			array_push( $extra_classes, $custom_class );
		}

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
	 * Retrieve a shortcode attributes.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_shortcode_atts() {
		return array(
			'type' => array(
				'default' => 'list',
				'name'    => esc_html__( 'Type', 'cherry-testi' ),
				'desc'    => esc_html__( 'Layout type (`list` or `slider`)', 'cherry-testi' ),
			),
			'sup_title' => array(
				'default' => '',
				'name'    => esc_html__( 'Suptitle', 'cherry-testi' ),
				'desc'    => esc_html__( 'Text before main title', 'cherry-testi' ),
			),
			'title' => array(
				'default' => '',
				'name'    => esc_html__( 'Title', 'cherry-testi' ),
				'desc'    => esc_html__( 'Main title', 'cherry-testi' ),
			),
			'sub_title' => array(
				'default' => '',
				'name'    => esc_html__( 'Subtitle', 'cherry-testi' ),
				'desc'    => esc_html__( 'Text after main title', 'cherry-testi' ),
			),
			'limit' => array(
				'default' => 3,
				'name'    => esc_html__( 'Limit', 'cherry-testi' ),
				'desc'    => esc_html__( 'Testimonials number to show', 'cherry-testi' ),
			),
			'orderby' => array(
				'default' => 'date',
				'name'    => esc_html__( 'Order by', 'cherry-testi' ),
				'desc'    => esc_html__( 'Order testimonials by', 'cherry-testi' ),
			),
			'order' => array(
				'default' => 'DESC',
				'name'    => esc_html__( 'Order', 'cherry-testi' ),
				'desc'    => esc_html__( 'Testimonials order (`DESC` or `ASC`)', 'cherry-testi' ),
			),
			'category' => array(
				'default'  => '',
				'name'     => esc_html__( 'Category', 'cherry-testi' ),
				'desc'     => esc_html__( 'Select category to show testimonials from (use category slug, pass multiplie categories via comma)', 'cherry-testi' ),
			),
			'ids' => array(
				'default' => 0,
				'name'    => esc_html__( "Post ID's", 'cherry-testi' ),
				'desc'    => esc_html__( "Enter comma separated ID's of the testimonials that you want to show", 'cherry-testi' ),
			),
			'content_length' => array(
				'default' => 55,
				'name'    => esc_html__( 'Content Length', 'cherry-testi' ),
				'desc'    => esc_html__( 'Insert the number of words you want to show in the testimonial content.', 'cherry-testi' ),
			),
			'divider' => array(
				'default' => 'off',
				'name'    => esc_html__( 'Divider', 'cherry-testi' ),
				'desc'    => esc_html__( 'Show divider between title and testimonials', 'cherry-testi' ),
			),
			'show_avatar' => array(
				'default' => 'on',
				'name'    => esc_html__( 'Avatar', 'cherry-testi' ),
				'desc'    => esc_html__( "Show author's avatar", 'cherry-testi' ),
			),
			'size' => array(
				'default' => 100,
				'name'    => esc_html__( 'Avatar size', 'cherry-testi' ),
				'desc'    => esc_html__( 'Avatar size (in pixels)', 'cherry-testi' ),
			),
			'show_email' => array(
				'default' => 'on',
				'name'    => esc_html__( 'Email', 'cherry-testi' ),
				'desc'    => esc_html__( "Show author's email", 'cherry-testi' ),
			),
			'show_position' => array(
				'default' => 'on',
				'name'    => esc_html__( 'Position', 'cherry-testi' ),
				'desc'    => esc_html__( "Show author's position", 'cherry-testi' ),
			),
			'show_company' => array(
				'default' => 'on',
				'name'    => esc_html__( 'Company', 'cherry-testi' ),
				'desc'    => esc_html__( "Show author's company", 'cherry-testi' ),
			),
			'autoplay' => array(
				'default' => 7000,
				'name'    => esc_html__( 'Autoplay', 'cherry-testi' ),
				'desc'    => esc_html__( 'Delay between transitions, in ms (only for slider)', 'cherry-testi' ),
			),
			'effect' => array(
				'default' => 'slide',
				'name'    => esc_html__( 'Effect', 'cherry-testi' ),
				'desc'    => esc_html__( 'Could be "slide" or "fade" (only for slider)', 'cherry-testi' ),
			),
			'loop' => array(
				'default' => 'on',
				'name'    => esc_html__( 'Loop', 'cherry-testi' ),
				'desc'    => esc_html__( 'Set to on to enable continuous loop mode (only for slider)', 'cherry-testi' ),
			),
			'pagination' => array(
				'default' => 'on',
				'name'    => esc_html__( 'Pagination', 'cherry-testi' ),
				'desc'    => esc_html__( 'Show pagination (only for slider)', 'cherry-testi' ),
			),
			'navigation' => array(
				'default' => 'on',
				'name'    => esc_html__( 'Navigation', 'cherry-testi' ),
				'desc'    => esc_html__( 'Show navigation (only for slider)', 'cherry-testi' ),
			),
			'slides_per_view_phone' => array(
				'default' => 1,
				'name'    => esc_html__( 'Number of slides per view on phones', 'cherry-testi' ),
				'desc'    => esc_html__( "Slides visible at the same time on slider's containe (only for slider on phones)", 'cherry-testi' ),
			),
			'slides_per_view_tablet' => array(
				'default' => 1,
				'name'    => esc_html__( 'Number of slides per view on tablets', 'cherry-testi' ),
				'desc'    => esc_html__( "Slides visible at the same time on slider's containe (only for slider on tablets)", 'cherry-testi' ),
			),
			'slides_per_view_laptop' => array(
				'default' => 1,
				'name'    => esc_html__( 'Number of slides per view on laptops', 'cherry-testi' ),
				'desc'    => esc_html__( "Slides visible at the same time on slider's containe (only for slider on laptops)", 'cherry-testi' ),
			),
			'slides_per_view' => array(
				'default' => 1,
				'name'    => esc_html__( 'Number of slides per view on desktops', 'cherry-testi' ),
				'desc'    => esc_html__( "Slides visible at the same time on slider's containe (only for slider on desktops)", 'cherry-testi' ),
			),
			'space_between_phone' => array(
				'default' => 15,
				'name'    => esc_html__( 'Space between on phones', 'cherry-testi' ),
				'desc'    => esc_html__( 'Distance between slides in px (only for slider on phones)', 'cherry-testi' ),
			),
			'space_between_tablet' => array(
				'default' => 15,
				'name'    => esc_html__( 'Space between on tablets', 'cherry-testi' ),
				'desc'    => esc_html__( 'Distance between slides in px (only for slider on tablets)', 'cherry-testi' ),
			),
			'space_between_laptop' => array(
				'default' => 15,
				'name'    => esc_html__( 'Space between on laptops', 'cherry-testi' ),
				'desc'    => esc_html__( 'Distance between slides in px (only for slider on laptops)', 'cherry-testi' ),
			),
			'space_between' => array(
				'default' => 15,
				'name'    => esc_html__( 'Space between', 'cherry-testi' ),
				'desc'    => esc_html__( 'Distance between slides in px (only for slider on desktops)', 'cherry-testi' ),
			),
			'template' => array(
				'default' => 'default.tmpl',
				'name'    => esc_html__( 'Template', 'cherry-testi' ),
				'desc'    => esc_html__( 'Template name to use', 'cherry-testi' ),
			),
			'custom_class' => array(
				'default' => '',
				'name'    => esc_html__( 'Class', 'cherry-testi' ),
				'desc'    => esc_html__( 'Extra CSS class', 'cherry-testi' ),
			),
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
