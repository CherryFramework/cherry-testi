<?php
/**
 * New page templates registration.
 *
 * @package    Cherry_Testi
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Class for including page templates.
 *
 * @since 1.0.0
 */
class TM_Testimonials_Page_Template {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Number of post to show per page.
	 *
	 * @since 1.0.0
	 * @var   integer
	 */
	public static $posts_per_page = 6;

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->plugin    = tm_testimonials_plugin();
		$this->post_type = $this->plugin->get_post_type_name();

		// Add a filter to load a custom templates.
		add_filter( 'template_include', array( $this, 'get_view_template' ) );
		add_filter( 'single_template', array( $this, 'get_single_template' ) );

		// Set `posts_per_page` for archive index page.
		add_action( 'pre_get_posts', array( $this, 'set_posts_per_archive_page' ) );

		/**
		 * Filter posts per archive page value.
		 *
		 * @since 1.0.0
		 * @var   int
		 */
		self::$posts_per_page = apply_filters(
			'tm_testimonials_posts_per_page',
			tm_testimonials_plugin_get_option( 'posts_per_page' )
		);
	}

	/**
	 * Set `posts_per_page` for archive index page.
	 *
	 * @since 1.0.0
	 * @param object $query Main query.
	 */
	public function set_posts_per_archive_page( $query ) {
		if ( ! is_admin()
			&& $query->is_main_query()
			&& ( $query->is_post_type_archive( $this->post_type ) || $query->is_tax( $this->post_type . '_category' ) )
			) {
			$query->set( 'posts_per_page', self::$posts_per_page );
		}
	}

	/**
	 * Includes a new template on an archive, taxonomy or page selected in setting `Testimonails page`.
	 *
	 * @since 1.0.0
	 */
	public function get_view_template( $template ) {
		global $post;

		$page_slug = tm_testimonials_plugin_get_option( 'archive_page' );

		if ( ( is_page( $page_slug ) && false !== $page_slug )
			|| is_post_type_archive( $this->post_type )
			|| is_tax( $this->post_type . '_category' )
			) {

			// Archive index page template name.
			$testimonials_template = "templates/archive-{$this->post_type}.php";

			$check_dirs = $this->get_locate_dirs();

			foreach ( $check_dirs as $dir ) {
				if ( file_exists( $dir . $testimonials_template ) ) {
					return $dir . $testimonials_template;
				}
			}
		}

		return $template;
	}

	/**
	 * Adds a custom single template.
	 *
	 * @since 1.0.0
	 */
	public function get_single_template( $template ) {
		global $post;

		if ( $post->post_type !== $this->post_type ) {
			return $template;
		}

		// Single page template name.
		$single_template = "templates/single-{$this->post_type}.php";

		$check_dirs = $this->get_locate_dirs();

		foreach ( $check_dirs as $dir ) {
			if ( file_exists( $dir . $single_template ) ) {
				return $dir . $single_template;
			}
		}

		return $template;
	}

	/**
	 * Retrieve a path's of locate directories.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_locate_dirs() {
		$upload_dir = wp_upload_dir();

		return apply_filters( 'tm_testimonials_get_locate_dirs', array(
			trailingslashit( $upload_dir['basedir'] ),
			trailingslashit( get_stylesheet_directory() ),
			trailingslashit( get_template_directory() ),
			TM_TESTI_DIR,
		) );
	}

	/**
	 * Retrieve available templates list.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_templates_list() {
		return apply_filters( 'tm_testimonials_templates_list', array(
			'default.tmpl'       => 'default.tmpl',
			'boxed.tmpl'         => 'boxed.tmpl',
			'speech-bubble.tmpl' => 'speech-bubble.tmpl',
		) );
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

TM_Testimonials_Page_Template::get_instance();
