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
		add_action( 'init', array( $this, 'register_shortcode' ), -999 );

		if ( is_admin() ) {
			$this->register_shortcode_for_builder();
		}
	}

	/**
	 * Registers shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcode() {

		if ( defined( 'ELEMENTOR_VERSION' ) ) {

			require_once( TM_TESTI_DIR . 'includes/ext/class-tm-testimonials-elementor-compat.php' );

			tm_testimonials_elementor_compat( array(
				$this->get_tag() => array(
					'title' => esc_html__( 'Cherry Testi', 'cherry-test' ),
					'file'  => TM_TESTI_DIR . 'includes/ext/class-tm-testimonials-elementor-module.php',
					'class' => 'TM_Testimonials_Elementor_Widget',
					'icon'  => 'eicon-testimonial',
					'atts'  => $this->get_shortcode_atts(),
				),
			) );
		}

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
		$defaults = wp_list_pluck( $this->get_shortcode_atts(), 'value' );

		// Fix img pagination defaults
		$defaults['img_pagination'] = false;

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

		$var_to_bool = array(
			'loop',
			'pagination',
			'img_pagination',
			'navigation',
		);

		// Fix booleans.
		foreach ( $var_to_bool as $v ) {
			$atts[ $v ] = filter_var( $atts[ $v ], FILTER_VALIDATE_BOOLEAN );
		}

		// Fix slides_per_view
		foreach ( array( 'slides_per_view', 'slides_per_view_laptop', 'slides_per_view_tablet', 'slides_per_view_phone' ) as $val ) {
			$atts[ $val ] = ( 0 !== intval( $atts[ $val ] ) ) ? intval( $atts[ $val ] ) : 1;
		}

		// Fix space_between
		foreach ( array( 'space_between', 'space_between_laptop', 'space_between_tablet', 'space_between_phone' ) as $val ) {
			$atts[ $val ] = absint( $atts[ $val ] );
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
				'title'       => esc_html__( 'Type', 'cherry-testi' ),
				'description' => esc_html__( 'Layout type (`list` or `slider`)', 'cherry-testi' ),
				'options'     => array(
					'list'   => esc_html__( 'List', 'cherry-testi' ),
					'slider' => array(
						'label' => esc_html__( 'Slider', 'cherry-testi' ),
						'slave' => 'testi-slider-options',
					),
				),
				'value'   => 'list',
				'default' => 'list',
			),
			'sup_title' => array(
				'type'        => 'text',
				'value'       => '',
				'title'       => esc_html__( 'Super title', 'cherry-testi' ),
				'description' => esc_html__( 'Text before main title', 'cherry-testi' ),
				'value'       => '',
			),
			'title' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'Title', 'cherry-testi' ),
				'description' => esc_html__( 'Main title', 'cherry-testi' ),
				'value'       => '',
			),
			'sub_title' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'Subtitle', 'cherry-testi' ),
				'description' => esc_html__( 'Text after main title', 'cherry-testi' ),
				'value'       => '',
			),
			'limit' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Limit', 'cherry-testi' ),
				'description' => esc_html__( 'Testimonials number to show (-1 means that will show all testimonials)', 'cherry-testi' ),
				'value'       => 3,
				'max_value'   => 50,
				'min_value'   => -1,
			),
			'orderby' => array(
				'type'        => 'select',
				'title'       => esc_html__( 'Order by', 'cherry-testi' ),
				'description' => esc_html__( 'Order testimonials by', 'cherry-testi' ),
				'options'     => array(
					'none'          => esc_html__( 'None', 'cherry-testi' ),
					'ID'            => esc_html__( 'Post ID', 'cherry-testi' ),
					'author'        => esc_html__( 'Post author', 'cherry-testi' ),
					'title'         => esc_html__( 'Post title', 'cherry-testi' ),
					'name'          => esc_html__( 'Post slug', 'cherry-testi' ),
					'date'          => esc_html__( 'Date', 'cherry-testi' ),
					'modified'      => esc_html__( 'Last modified date', 'cherry-testi' ),
					'parent'        => esc_html__( 'Post parent', 'cherry-testi' ),
					'rand'          => esc_html__( 'Random', 'cherry-testi' ),
					'comment_count' => esc_html__( 'Comments number', 'cherry-testi' ),
					'menu_order'    => esc_html__( 'Menu order', 'cherry-testi' ),
				),
				'value'   => 'date',
				'default' => 'date',
			),
			'order' => array(
				'type'        => 'select',
				'title'       => esc_html__( 'Order', 'cherry-testi' ),
				'description' => esc_html__( 'Testimonials order', 'cherry-testi' ),
				'options'     => array(
					'ASC'  => esc_html__( 'Ascending', 'cherry-testi' ),
					'DESC' => esc_html__( 'Descending', 'cherry-testi' ),
				),
				'value'   => 'DESC',
				'default' => 'DESC',
			),
			'category' => array(
				'type'        => 'select',
				'title'       => esc_html__( 'Category', 'cherry-testi' ),
				'description' => esc_html__( 'Select category to show testimonials from (use category slug, pass multiplie categories via comma)', 'cherry-testi' ),
				'class'       => 'cherry-multi-select',
				'multiple'    => true,
				'options'     => false,
				'options_cb'  => array( $this, 'get_categories' ),
				'value'       => '',
			),
			'ids' => array(
				'type'        => 'text',
				'title'       => esc_html__( "Post ID's", 'cherry-testi' ),
				'description' => esc_html__( "Enter comma separated ID's of the testimonials that you want to show", 'cherry-testi' ),
				'value'       => '',
			),
			'content_length' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Content Length', 'cherry-testi' ),
				'description' => esc_html__( 'The number of words you want to show in the testimonial content.', 'cherry-testi' ),
				'value'       => 55,
				'max_value'   => 150,
				'min_value'   => -1,
			),
			'divider' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Divider', 'cherry-testi' ),
				'description' => esc_html__( 'Divider between title and testimonials', 'cherry-testi' ),
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'On', 'cherry-testi' ),
					'false_toggle' => esc_html__( 'Off', 'cherry-testi' ),
				),
				'value'   => 'off',
				'default' => 'off',
			),
			'show_avatar' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Avatar', 'cherry-testi' ),
				'description' => esc_html__( "Author's avatar", 'cherry-testi' ),
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'On', 'cherry-testi' ),
					'false_toggle' => esc_html__( 'Off', 'cherry-testi' ),
					'true_slave'   => 'testi-avatar-visible-true',
				),
				'value'   => 'on',
				'default' => 'on',
			),
			'size' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Avatar size', 'cherry-testi' ),
				'description' => esc_html__( 'Avatar size (in pixels)', 'cherry-testi' ),
				'master'      => 'testi-avatar-visible-true',
				'value'       => 100,
				'max_value'   => 500,
				'min_value'   => 50,
			),
			'show_email' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Email', 'cherry-testi' ),
				'description' => esc_html__( "Author's email", 'cherry-testi' ),
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'On', 'cherry-testi' ),
					'false_toggle' => esc_html__( 'Off', 'cherry-testi' ),
				),
				'value'   => 'on',
				'default' => 'on',
			),
			'show_position' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Position', 'cherry-testi' ),
				'description' => esc_html__( "Author's position", 'cherry-testi' ),
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'On', 'cherry-testi' ),
					'false_toggle' => esc_html__( 'Off', 'cherry-testi' ),
				),
				'value'   => 'on',
				'default' => 'on',
			),
			'show_company' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Company', 'cherry-testi' ),
				'description' => esc_html__( "Author's company", 'cherry-testi' ),
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'On', 'cherry-testi' ),
					'false_toggle' => esc_html__( 'Off', 'cherry-testi' ),
				),
				'value'   => 'on',
				'default' => 'on',
			),
			'autoplay' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Autoplay', 'cherry-testi' ),
				'description' => esc_html__( 'Delay between transitions, in ms (only for slider)', 'cherry-testi' ),
				'value'       => 7000,
				'max_value'   => 15000,
				'min_value'   => 500,
				'step_value'  => 500,
				'master'      => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'effect' => array(
				'type'        => 'select',
				'title'       => esc_html__( 'Effect', 'cherry-testi' ),
				'description' => esc_html__( 'Could be "slide" or "fade" (only for slider)', 'cherry-testi' ),
				'options'     => array(
					'slide' => esc_html__( 'Slide', 'cherry-testi' ),
					'fade'  => esc_html__( 'Fade', 'cherry-testi' ),
				),
				'value'   => 'slide',
				'default' => 'slide',
				'master'  => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'loop' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Loop', 'cherry-testi' ),
				'description' => esc_html__( 'Set to on to enable continuous loop mode (only for slider)', 'cherry-testi' ),
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'On', 'cherry-testi' ),
					'false_toggle' => esc_html__( 'Off', 'cherry-testi' ),
				),
				'value'   => 'on',
				'default' => 'on',
				'master'  => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'pagination' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Pagination', 'cherry-testi' ),
				'description' => esc_html__( 'Pagination (only for slider)', 'cherry-testi' ),
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'On', 'cherry-testi' ),
					'false_toggle' => esc_html__( 'Off', 'cherry-testi' ),
				),
				'value'   => 'on',
				'default' => 'on',
				'master'  => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'img_pagination' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Avatars Pagination', 'cherry-testi' ),
				'description' => esc_html__( 'Use client avatars for pagination (only for slider)', 'cherry-testi' ),
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'On', 'cherry-testi' ),
					'false_toggle' => esc_html__( 'Off', 'cherry-testi' ),
				),
				'value'       => 'on',
				'default'     => '',
			),
			'img_pagination_size' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Pagination Avatar Size', 'cherry-testi' ),
				'description' => esc_html__( 'Set width and height of the thumbnails in pagination', 'cherry-testi' ),
				'value'       => 80,
				'max_value'   => 400,
				'min_value'   => 50,
			),
			'navigation' => array(
				'type'        => 'switcher',
				'title'       => esc_html__( 'Navigation', 'cherry-testi' ),
				'description' => esc_html__( 'Navigation (only for slider)', 'cherry-testi' ),
				'toggle'      => array(
					'true_toggle'  => esc_html__( 'On', 'cherry-testi' ),
					'false_toggle' => esc_html__( 'Off', 'cherry-testi' ),
				),
				'value'   => 'on',
				'default' => 'on',
				'master'  => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'slides_per_view_phone' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Number of slides per view on small screen / phone', 'cherry-testi' ),
				'description' => esc_html__( "Slides visible at the same time on slider's containe (only for slider with 'slide' effect on small screen / phone)", 'cherry-testi' ),
				'value'       => 1,
				'max_value'   => 2,
				'min_value'   => 1,
				'master'      => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'slides_per_view_tablet' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Number of slides per view on medium screen / tablet', 'cherry-testi' ),
				'description' => esc_html__( "Slides visible at the same time on slider's containe (only for slider with 'slide' effect on medium screen / tablet)", 'cherry-testi' ),
				'value'       => 1,
				'max_value'   => 4,
				'min_value'   => 1,
				'master'      => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'slides_per_view_laptop' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Number of slides per view on large screen / desktop', 'cherry-testi' ),
				'description' => esc_html__( "Slides visible at the same time on slider's containe (only for slider with 'slide' effect on large screen / desktop)", 'cherry-testi' ),
				'value'       => 1,
				'max_value'   => 6,
				'min_value'   => 1,
				'master'      => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'slides_per_view' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Number of slides per view on extra large screen / wide desktop', 'cherry-testi' ),
				'description' => esc_html__( "Slides visible at the same time on slider's containe (only for slider with 'slide' effect on extra large screen / wide desktop)", 'cherry-testi' ),
				'value'       => 1,
				'max_value'   => 8,
				'min_value'   => 1,
				'master'      => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'space_between_phone' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Space between on small screen / phone', 'cherry-testi' ),
				'description' => esc_html__( 'Distance between slides in px (only for slider on small screen / phone)', 'cherry-testi' ),
				'value'       => 15,
				'max_value'   => 100,
				'min_value'   => 0,
				'master'      => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'space_between_tablet' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Space between on medium screen / tablet', 'cherry-testi' ),
				'description' => esc_html__( 'Distance between slides in px (only for slider on medium screen / tablet)', 'cherry-testi' ),
				'value'       => 15,
				'max_value'   => 100,
				'min_value'   => 0,
				'master'      => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'space_between_laptop' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Space between on large screen / desktop', 'cherry-testi' ),
				'description' => esc_html__( 'Distance between slides in px (only for slider on large screen / desktop)', 'cherry-testi' ),
				'value'       => 15,
				'max_value'   => 100,
				'min_value'   => 0,
				'master'      => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'space_between' => array(
				'type'        => 'slider',
				'title'       => esc_html__( 'Space between on extra large screen / wide desktop', 'cherry-testi' ),
				'description' => esc_html__( 'Distance between slides in px (only for slider on extra large screen / wide desktop)', 'cherry-testi' ),
				'value'       => 15,
				'max_value'   => 100,
				'min_value'   => 0,
				'master'      => 'testi-slider-options',
				'condition'   => array(
					'type' => array( 'slider' ),
				),
			),
			'template' => array(
				'type'        => 'select',
				'title'       => esc_html__( 'Template', 'cherry-testi' ),
				'description' => esc_html__( 'Template name to use', 'cherry-testi' ),
				'options'     => false,
				'options_cb'  => array( tm_testimonials_page_template(), 'get_templates_list' ),
				'value'       => 'default.tmpl',
				'default'     => 'default.tmpl',
			),
			'custom_class' => array(
				'type'        => 'text',
				'title'       => esc_html__( 'Class', 'cherry-testi' ),
				'description' => esc_html__( 'Extra CSS class', 'cherry-testi' ),
				'value'       => '',
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

		return $categories;
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

if ( ! function_exists( 'tm_testimonials_shortcode' ) ) {
	function tm_testimonials_shortcode() {
		return TM_Testimonials_Shortcode::get_instance();
	}

	tm_testimonials_shortcode();
}
