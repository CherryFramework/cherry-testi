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
		add_action( 'init', array( $this, 'register_shortcodes' ) );
	}

	/**
	 * Registers shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcodes() {
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
		$defaults = array(
			'type'            => 'list', // list or slider
			'sup_title'       => '',
			'title'           => '',
			'sub_title'       => '',
			'limit'           => 3,
			'orderby'         => 'date',
			'order'           => 'DESC',
			'category'        => '',
			'ids'             => 0,
			'size'            => 100,
			'content_length'  => 55,
			'divider'         => 'off',
			'show_avatar'     => 'on',
			'show_email'      => 'on',
			'show_position'   => 'on',
			'show_company'    => 'on',
			'autoplay'        => 7000,
			'effect'          => 'slide',
			'loop'            => 'on',
			'pagination'      => 'on',
			'navigation'      => 'on',
			'slides_per_view' => 1,
			'space_between'   => 15,
			'template'        => 'default.tmpl',
			'custom_class'    => '',
		);

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

		$data_defaults = apply_filters( 'tm_testimonials_slider_data_defaults', array(
			'autoplay'      => 0,
			'effect'        => 'slide',
			'loop'          => false,
			'slidesPerView' => 1,
			'spaceBetween'  => 0,
		), $defaults, $atts );

		$atts['data_atts'] = apply_filters( 'tm_testimonials_slider_data_atts', array(
			'autoplay'      => intval( $atts['autoplay'] ),
			'effect'        => sanitize_key( $atts['effect'] ),
			'loop'          => (bool) $atts['loop'],
			'slidesPerView' => intval( $atts['slides_per_view'] ),
			'spaceBetween'  => intval( $atts['space_between'] ),
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
