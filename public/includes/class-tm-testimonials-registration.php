<?php
/**
 * New post type and taxonomy registration.
 *
 * @package    Cherry_Testi
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Class for register post types.
 *
 * @since 1.0.0
 */
class TM_Testimonials_Registration {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'init', array( __CLASS__, 'register_taxonomy' ) );

		// Filter the "enter title here" text.
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 11, 2 );

		$this->plugin    = tm_testimonials_plugin();
		$this->post_type = $this->plugin->get_post_type_name();
	}

	/**
	 * Register the new post type.
	 *
	 * @since 1.0.0
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public static function register_post_type() {
		$plugin    = tm_testimonials_plugin();
		$post_type = $plugin->get_post_type_name();

		// Labels used when displaying the posts.
		$labels = array(
			'name'                  => esc_html__( 'Testimonials',                   'cherry-testi' ),
			'singular_name'         => esc_html__( 'Testimonial',                    'cherry-testi' ),
			'menu_name'             => esc_html__( 'Testimonials',                   'cherry-testi' ),
			'name_admin_bar'        => esc_html__( 'Testimonial',                    'cherry-testi' ),
			'add_new'               => esc_html__( 'Add New',                        'cherry-testi' ),
			'add_new_item'          => esc_html__( 'Add New Testimonial',            'cherry-testi' ),
			'edit_item'             => esc_html__( 'Edit Testimonial',               'cherry-testi' ),
			'new_item'              => esc_html__( 'New Testimonial',                'cherry-testi' ),
			'view_item'             => esc_html__( 'View Testimonial',               'cherry-testi' ),
			'search_items'          => esc_html__( 'Search Testimonials',            'cherry-testi' ),
			'not_found'             => esc_html__( 'No testimonials found',          'cherry-testi' ),
			'not_found_in_trash'    => esc_html__( 'No testimonials found in trash', 'cherry-testi' ),
			'all_items'             => esc_html__( 'Testimonials',                   'cherry-testi' ),
			'featured_image'        => esc_html__( 'Author Avatar',                  'cherry-testi' ),
			'set_featured_image'    => esc_html__( 'Set Avatar',                     'cherry-testi' ),
			'remove_featured_image' => esc_html__( 'Remove',                         'cherry-testi' ),
		);

		// What features the post type supports.
		$supports = array(
			'title',
			'editor',
			'author',
			'thumbnail',
		);

		// The rewrite handles the URL structure.
		$rewrite = array(
			'slug'       => 'testimonials',
			'with_front' => false,
			'pages'      => true,
			'feeds'      => true,
			'ep_mask'    => EP_PERMALINK,
		);

		$args = array(
			'labels'              => $labels,
			'supports'            => $supports,
			'description'         => '',
			'public'              => true,
			'publicly_queryable'  => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'exclude_from_search' => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => null,
			'menu_icon'           => 'dashicons-testimonial',
			'can_export'          => true,
			'delete_with_user'    => false,
			'hierarchical'        => false,
			'has_archive'         => 'testimonials',
			'query_var'           => $post_type,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'rewrite'             => $rewrite,
		);

		$args = apply_filters( 'tm_testimonials_post_type_args', $args );

		register_post_type( $post_type, $args );
	}

	/**
	 * Register the Testimonial Category taxonomy.
	 *
	 * @since 1.0.0
	 * @link  https://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	public static function register_taxonomy() {
		$plugin    = tm_testimonials_plugin();
		$post_type = $plugin->get_post_type_name();

		// Labels used when displaying taxonomy and terms.
		$labels = array(
			'name'                       => esc_html__( 'Testimonial Categories', 'cherry-testi' ),
			'singular_name'              => esc_html__( 'Testimonial Category',   'cherry-testi' ),
			'menu_name'                  => esc_html__( 'Categories',             'cherry-testi' ),
			'name_admin_bar'             => esc_html__( 'Category',               'cherry-testi' ),
			'search_items'               => esc_html__( 'Search Categories',      'cherry-testi' ),
			'popular_items'              => esc_html__( 'Popular Categories',     'cherry-testi' ),
			'all_items'                  => esc_html__( 'All Categories',         'cherry-testi' ),
			'edit_item'                  => esc_html__( 'Edit Category',          'cherry-testi' ),
			'view_item'                  => esc_html__( 'View Category',          'cherry-testi' ),
			'update_item'                => esc_html__( 'Update Category',        'cherry-testi' ),
			'add_new_item'               => esc_html__( 'Add New Category',       'cherry-testi' ),
			'new_item_name'              => esc_html__( 'New Category Name',      'cherry-testi' ),
			'parent_item'                => esc_html__( 'Parent Category',        'cherry-testi' ),
			'parent_item_colon'          => esc_html__( 'Parent Category:',       'cherry-testi' ),
			'separate_items_with_commas' => null,
			'add_or_remove_items'        => null,
			'choose_from_most_used'      => null,
			'not_found'                  => null,
		);

		// The rewrite handles the URL structure.
		$rewrite = array(
			'slug'         => $post_type . '/category',
			'with_front'   => false,
			'hierarchical' => true,
			'ep_mask'      => EP_NONE,
		);

		$args = array(
			'public'            => true,
			'show_ui'           => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'query_var'         => $post_type . '_category',
			'labels'            => $labels,
			'rewrite'           => $rewrite,
		);

		$args = apply_filters( 'tm_testimonials_taxonomy_args', $args );

		register_taxonomy( $post_type . '_category', array( $post_type ), $args );
	}

	/**
	 * Custom "enter title here" text.
	 *
	 * @since  1.0.0
	 * @param  string $title Placeholder text.
	 * @param  object $post  Post object.
	 * @return string
	 */
	public function enter_title_here( $title, $post ) {

		if ( $this->post_type === $post->post_type ) {
			return esc_html__( "Enter author's name", 'cherry-testi' );
		}

		return $title;
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

TM_Testimonials_Registration::get_instance();
