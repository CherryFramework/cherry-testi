<?php
/**
 * Testimonials Data class.
 *
 * @package    Cherry_Testi
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Class for Testimonials data.
 *
 * @since 1.0.0
 */
class TM_Testimonials_Data {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * The array of arguments for query.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	private $query_args = array();

	/**
	 * The array of arguments for template file.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	private $post_data = array();

	/**
	 * Holder for the main query object, while query processing.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private $wp_query = null;

	/**
	 * Sets up our actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->plugin    = tm_testimonials_plugin();
		$this->post_type = $this->plugin->get_post_type_name();

		/**
		 * Fires when you need to display testimonials.
		 *
		 * @since 1.0.0
		 */
		add_action( 'tm_get_testimonials', array( $this, 'the_testimonials' ) );
	}

	/**
	 * Display or return HTML-formatted testimonials.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments.
	 * @return string
	 */
	public function the_testimonials( $args = array() ) {
		/**
		 * Filter the array of default arguments.
		 *
		 * @since 1.0.0
		 * @param array $defaults Default arguments.
		 * @param array $args     The 'the_testimonials' function argument.
		 */
		$defaults = apply_filters( 'tm_the_testimonials_default_args', array(
			'limit'          => 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'category'       => '',
			'ids'            => 0,
			'size'           => 100,
			'content_length' => 55,
			'echo'           => true,
			'sup_title'      => '',
			'title'          => '',
			'sub_title'      => '',
			'divider'        => false,
			'show_avatar'    => true,
			'show_email'     => true,
			'show_position'  => true,
			'show_company'   => true,
			'container'      => '<div class="tm-testi__list">%s</div>',
			'before_title'   => '<h2>',
			'after_title'    => '</h2>',
			'pager'          => false,
			'template'       => 'default.tmpl',
			'custom_class'   => '',
		), $args );

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the array of arguments.
		 *
		 * @since 1.0.0
		 * @param array Arguments.
		 */
		$args = apply_filters( 'tm_the_testimonials_args', $args );

		/**
		 * Filter before the Testimonials.
		 *
		 * @since 1.0.0
		 * @param array $array The array of arguments.
		 */
		$inner = apply_filters( 'tm_testimonials_before', '', $args );

		// The Query.
		$query = $this->get_testimonials( $args );

		if ( false === $query ) {
			wp_reset_postdata();
			return;
		}

		global $wp_query;

		$this->wp_query = $wp_query;
		$wp_query       = null;
		$wp_query       = $query;

		// Fix boolean.
		if ( isset( $args['pager'] ) && ( 'true' == $args['pager'] ) ) {
			$args['pager'] = true;
		} else {
			$args['pager'] = false;
		}

		// Prepare CSS-class.
		$css_classes = array( 'tm-testi__wrap' );

		if ( ! empty( $args['slides_per_view'] ) ) {
			$css_classes[] = sprintf(
				'tm-testi__wrap--perview-%d',
				intval( $args['slides_per_view'] )
			);
		}

		if ( ! empty( $args['custom_class'] ) ) {
			$css_classes[] = esc_attr( $args['custom_class'] );
		}

		if ( ! empty( $args['template'] ) ) {
			$css_classes[] = $this->get_template_class( $args['template'] );
		}

		$css_classes = array_map( 'esc_attr', $css_classes );
		$css_classes = apply_filters( 'tm_testimonials_wrapper_classes', $css_classes, $args );

		$titles = '';

		if ( ! empty( $args['sup_title'] ) ) {
			$titles .= sprintf( '<h5 class="tm-testi__title-sup">%s</h5>', $args['sup_title'] );
		}

		if ( ! empty( $args['title'] ) ) {
			$titles .= sprintf( '<h3 class="tm-testi__title-main">%s</h3>', $args['title'] );
		}

		if ( ! empty( $args['sub_title'] ) ) {
			$titles .= sprintf( '<h5 class="tm-testi__title-sub">%s</h5>', $args['sub_title'] );
		}

		if ( ! empty( $titles ) ) {
			$title_format = apply_filters( 'tm_testimonials_title_format', '<div class="tm-testi__title">%s</div>', $inner, $args );
			$titles = sprintf( $title_format, $titles );
		}

		if ( false !== $args['divider'] ) {
			$divider = apply_filters( 'tm_testimonials_divider_format', '<hr class="tm-testi__divider">', $inner, $args );
			$titles .= $divider;
		}

		if ( false !== $args['container'] ) {
			$inner .= sprintf( $args['container'], $this->get_testimonials_loop( $query, $args ) );
		} else {
			$inner .= $this->get_testimonials_loop( $query, $args );
		}

		$inner          = apply_filters( 'tm_testimonials_loop_after', $inner, $args );
		$wrapper_format = apply_filters( 'tm_testimonials_wrapper_format', '<div class="tm-testi">%s<div class="%s">%s</div></div>', $args );
		$output         = sprintf( $wrapper_format, $titles, join( ' ', array_unique( $css_classes ) ), $inner );

		// Pagination (if we need).
		if ( true == $args['pager'] ) {
			$output .= get_the_posts_pagination( apply_filters( 'tm_testimonials_pagination_args', array(), $args ) );
		}

		$wp_query = null;
		$wp_query = $this->wp_query;

		wp_reset_postdata();

		/**
		 * Filters HTML-formatted testimonials before display or return.
		 *
		 * @since 1.0.0
		 * @param string $output The HTML-formatted testimonials.
		 * @param array  $query  List of WP_Post objects.
		 * @param array  $args   The array of arguments.
		 */
		$output = apply_filters( 'tm_testimonials_html', $output, $query, $args );

		if ( true != $args['echo'] ) {
			return $output;
		}

		// If "echo" is set to true.
		echo $output;
	}

	/**
	 * Get testimonials.
	 *
	 * @since  1.0.0
	 * @param  array $args Arguments to be passed to the query.
	 * @return array|bool  Array if true, boolean if false.
	 */
	public function get_testimonials( $args = array() ) {
		$defaults = array(
			'limit'   => 5,
			'orderby' => 'date',
			'order'   => 'DESC',
			'ids'     => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the array of arguments.
		 *
		 * @since 1.0.0
		 * @param array Arguments to be passed to the query.
		 */
		$args = apply_filters( 'tm_get_testimonials_args', $args );

		// The Query Arguments.
		$this->query_args['post_type']        = $this->post_type;
		$this->query_args['posts_per_page']   = $args['limit'];
		$this->query_args['orderby']          = $args['orderby'];
		$this->query_args['order']            = $args['order'];
		$this->query_args['suppress_filters'] = false;

		if ( ! empty( $args['category'] ) ) {
			$category = str_replace( ' ', ',', $args['category'] );
			$category = explode( ',', $category );

			$field = absint( $category[0] );
			$field = 0 === $field ? 'slug' : 'id';

			if ( is_array( $category ) ) {
				$this->query_args['tax_query'] = array(
					array(
						'taxonomy' => $this->post_type . '_category',
						'field'    => $field,
						'terms'    => $category,
					),
				);
			}
		} else {
			$this->query_args['tax_query'] = false;
		}

		if ( isset( $args['pager'] ) && ( 'true' == $args['pager'] ) ) :

			if ( get_query_var( 'paged' ) ) {
				$this->query_args['paged'] = get_query_var( 'paged' );
			} elseif ( get_query_var( 'page' ) ) {
				$this->query_args['paged'] = get_query_var( 'page' );
			} else {
				$this->query_args['paged'] = 1;
			}

		endif;

		$ids = str_replace( ' ', ',', $args['ids'] );
		$ids = explode( ',', $ids );

		if ( 0 < intval( $args['ids'] ) && 0 < count( $ids ) ) :

			$ids = array_map( 'intval', $ids );

			if ( 1 == count( $ids ) && is_numeric( $ids[0] ) && ( 0 < intval( $ids[0] ) ) ) {
				$this->query_args['p'] = intval( $args['ids'] );
			} else {
				$this->query_args['ignore_sticky_posts'] = 1;
				$this->query_args['post__in']            = $ids;
			}

		endif;

		// Whitelist checks.
		if ( ! in_array( $this->query_args['orderby'], array( 'none', 'ID', 'author', 'title', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order', 'meta_value', 'meta_value_num' ) ) ) {
			$this->query_args['orderby'] = 'date';
		}

		if ( ! in_array( strtoupper( $this->query_args['order'] ), array( 'ASC', 'DESC' ) ) ) {
			$this->query_args['order'] = 'DESC';
		}

		/**
		 * Filters the query.
		 *
		 * @since 1.0.0
		 * @param array The array of query arguments.
		 * @param array The array of arguments to be passed to the query.
		 */
		$this->query_args = apply_filters( 'tm_get_testimonials_query_args', $this->query_args, $args );

		// The Query.
		$query = new WP_Query( $this->query_args );

		if ( ! $query->have_posts() ) {
			return false;
		}

		return $query;
	}

	/**
	 * Callback to replace macros with data.
	 *
	 * @since 1.0.0
	 * @param array $matches Founded macros.
	 */
	public function replace_callback( $matches ) {

		if ( ! is_array( $matches ) ) {
			return;
		}

		if ( empty( $matches ) ) {
			return;
		}

		$key = strtolower( $matches[1] );

		// If key not found in data - return nothing.
		if ( ! isset( $this->post_data[ $key ] ) ) {
			return;
		}

		$callback = $this->post_data[ $key ];

		if ( ! is_callable( $callback ) ) {
			return;
		}

		// If found parameters and has correct callback - process it.
		if ( isset( $matches[3] ) ) {
			return call_user_func( $callback, $matches[3] );
		}

		return call_user_func( $callback );
	}

	/**
	 * Get testimonials items.
	 *
	 * @since  1.0.0
	 * @param  array $query WP_query object.
	 * @param  array $args  The array of arguments.
	 * @return string
	 */
	public function get_testimonials_loop( $query, $args ) {
		global $post;

		// Item template.
		$template = $this->get_template_by_name( $args['template'], TM_Testimonials_Shortcode::$name );

		/**
		 * Filters template for testimonials item.
		 *
		 * @since 1.0.0
		 * @param string $template.
		 * @param array  $args.
		 */
		$template = apply_filters( 'tm_testimonials_item_template', $template, $args );

		$macros    = '/%%([a-zA-Z]+[^%]{2})(=[\'\"]([a-zA-Z0-9-_\s]+)[\'\"])?%%/';
		$callbacks = $this->setup_template_data( $args );

		// CSS classes.
		$classes = array( 'tm-testi__item' );
		$classes = apply_filters( 'tm_testimonials_item_classes', $classes, $args );

		// Get settings.
		$page_slug  = tm_testimonials_plugin_get_option( 'archive_page' );
		$is_listing = false;

		if ( ( $this->wp_query->is_page( $page_slug ) && false !== $page_slug )
			|| $this->wp_query->is_post_type_archive( $this->post_type )
			|| $this->wp_query->is_tax( $this->post_type . '_category' )
			|| $this->wp_query->is_singular( $this->post_type )
			) {
			$is_listing = true;
		}

		$output = '';

		while ( $query->have_posts() ) :

			$query->the_post();

			$item_classes = array();
			$tpl          = $template;
			$post_id      = $post->ID;
			$tpl          = preg_replace_callback( $macros, array( $this, 'replace_callback' ), $tpl );

			if ( $is_listing ) {
				$item_classes = get_post_class( $classes, $post_id );
			} else {
				$item_classes = $classes;
			}

			$output .= '<div class="' . join( ' ', $item_classes ) . '"><div class="tm-testi__inner">';

				/**
				 * Filters testimonails item.
				 *
				 * @since 1.0.0
				 * @param string $tpl.
				 */
				$tpl = apply_filters( 'tm_get_testimonails_loop_item', $tpl );

				$output .= $tpl;

			$output .= '</div></div>';

			$callbacks->clear_data();

		endwhile;

		return $output;
	}

	/**
	 * Prepare template data to replace.
	 *
	 * @since 1.0.0
	 * @param array $atts Output attributes.
	 */
	function setup_template_data( $atts ) {
		require_once( TM_TESTI_DIR . 'public/includes/class-tm-testimonials-template-callbacks.php' );

		$callbacks = new TM_Testimonials_Template_Callbacks( $atts );

		$data = array(
			'avatar'   => array( $callbacks, 'get_avatar' ),
			'content'  => array( $callbacks, 'get_content' ),
			'email'    => array( $callbacks, 'get_email' ),
			'name'     => array( $callbacks, 'get_name' ),
			'url'      => array( $callbacks, 'get_url' ),
			'position' => array( $callbacks, 'get_position' ),
			'company'  => array( $callbacks, 'get_company' ),
		);

		/**
		 * Filters item data.
		 *
		 * @since 1.0.0
		 * @param array $data Item data.
		 * @param array $atts Attributes.
		 */
		$this->post_data = apply_filters( 'tm_testimonials_data_callbacks', $data, $atts );

		return $callbacks;
	}

	/**
	 * Read template (static).
	 *
	 * @since  1.0.0
	 * @return bool|WP_Error|string - false on failure, stored text on success.
	 */
	public static function get_contents( $template ) {

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			include_once( ABSPATH . '/wp-admin/includes/file.php' );
		}

		WP_Filesystem();
		global $wp_filesystem;

		// Check for existence.
		if ( ! $wp_filesystem->exists( $template ) ) {
			return false;
		}

		// Read the file.
		$content = $wp_filesystem->get_contents( $template );

		if ( ! $content ) {
			// Return error object.
			return new WP_Error( 'reading_error', 'Error when reading file' );
		}

		return $content;
	}

	/**
	 * Retrieve a *.tmpl file content.
	 *
	 * @since  1.0.0
	 * @param  string $template  File name.
	 * @param  string $shortcode Shortcode name.
	 * @return string
	 */
	public function get_template_by_name( $template, $shortcode ) {
		$file = $this->_get_template_path( $template );

		if ( false === $file ) {
			if ( file_exists( TM_TESTI_DIR . TM_TESTI_TMPL_SUBDIR . 'default.tmpl' ) ) {
				$file = TM_TESTI_DIR . TM_TESTI_TMPL_SUBDIR . 'default.tmpl';
			} else {

				/**
				 * Filters a default fallback-template.
				 *
				 * @since 1.0.0
				 * @param string $content.
				 */
				return apply_filters( 'tm_testimonials_fallback_template', '<blockquote>%%AVATAR%%<div class="tm-testi__item-body">%%CONTENT%%<footer><cite>%%NAME%%</cite> %%EMAIL%% %%POSITION%% %%COMPANY%%</footer></div></blockquote>' );
			}
		}

		$content = self::get_contents( $file );

		return $content;
	}

	/**
	 * Retrieve a tamplate path.
	 *
	 * @since  1.0.0
	 * @param  string $template File name.
	 * @return bool|string
	 */
	public function _get_template_path( $template ) {
		$template_path  = false;
		$page_templater = TM_Testimonials_Page_Template::get_instance();
		$check_dirs     = $page_templater->get_locate_dirs();

		foreach ( $check_dirs as $dir ) {
			if ( file_exists( $dir . TM_TESTI_TMPL_SUBDIR . $template ) ) {
				$template_path = $dir . TM_TESTI_TMPL_SUBDIR . $template;
				break;
			}
		}

		return $template_path;
	}

	/**
	 * Get CSS class name for shortcode by template name.
	 *
	 * @since  1.0.0
	 * @param  string $template Template name.
	 * @return string|bool
	 */
	public function get_template_class( $template ) {

		if ( ! $template ) {
			return false;
		}

		/**
		 * Filters a CSS-class prefix.
		 *
		 * @since 1.0.0
		 * @param string $prefix.
		 */
		$prefix = apply_filters( 'tm_testimonials_template_class_prefix', 'tm-testi-' );
		$class  = sprintf( '%s-%s', esc_attr( $prefix ), esc_attr( str_replace( '.tmpl', '', $template ) ) );

		return $class;
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
