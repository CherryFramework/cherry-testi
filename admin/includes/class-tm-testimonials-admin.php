<?php
/**
 * Sets up the admin functionality for the plugin.
 *
 * @package    Cherry_Testi
 * @subpackage Admin
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Class for Testimonials admin functionality.
 *
 * @since 1.0.0
 */
class TM_Testimonials_Admin {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {
		$this->plugin    = tm_testimonials_plugin();
		$this->post_type = $this->plugin->get_post_type_name();

		// Load post meta boxes on the post editing screen.
		add_action( 'load-post.php', array( $this, 'metabox' ) );
		add_action( 'load-post-new.php', array( $this, 'metabox' ) );

		// Only run our customization on the 'edit.php' page in the admin.
		add_action( 'load-edit.php', array( $this, 'manage_columns' ) );

		// Modify the quick links in admin table list.
		add_filter( 'post_row_actions', array( $this, 'modify_row_actions' ), 10, 2 );
	}

	/**
	 * Init `cherry-post-meta` module on the `Add New Testimonial` and `Edit Testimonial` screens.
	 *
	 * @since 1.0.0
	 */
	public function metabox() {
		$screen = get_current_screen();

		if ( empty( $screen->post_type ) || $this->post_type !== $screen->post_type ) {
			return;
		}

		// Print custom styles.
		add_action( 'admin_head', array( $this, 'print_styles' ) );

		/**
		 * Filter the array of metabox's fields.
		 *
		 * @since 1.0.0
		 */
		$metabox_args = apply_filters( 'tm_testimonials_metabox_args', array(
			'id'            => 'tm-testi-options',
			'title'         => esc_html__( 'Testimonial Options', 'cherry-testi' ),
			'page'          => $this->post_type,
			'context'       => 'side',
			'priority'      => 'core',
			'callback_args' => false,
			'single'        => array( 'key' => TM_TESTI_POSTMETA ),
			'fields'        => array(
				'email' => array(
					'id'          => TM_TESTI_POSTMETA . 'email',
					'name'        => TM_TESTI_POSTMETA . '[email]',
					'type'        => 'text',
					'label'       => esc_html__( 'Email:', 'cherry-testi' ),
					'placeholder' => esc_html__( 'email@demolink.org', 'cherry-testi' ),
					'value'       => '',
					'master'      => 'tm-testi-ui-container',
				),
				'url' => array(
					'id'          => TM_TESTI_POSTMETA . 'url',
					'name'        => TM_TESTI_POSTMETA . '[url]',
					'type'        => 'text',
					'label'       => esc_html__( 'URL:', 'cherry-testi' ),
					'placeholder' => esc_html__( 'http://demolink.org', 'cherry-testi' ),
					'value'       => '',
					'master'      => 'tm-testi-ui-container',
				),
				'position' => array(
					'id'          => TM_TESTI_POSTMETA . 'position',
					'name'        => TM_TESTI_POSTMETA . '[position]',
					'type'        => 'text',
					'label'       => esc_html__( 'Position:', 'cherry-testi' ),
					'placeholder' => esc_html__( 'CEO/Founder', 'cherry-testi' ),
					'value'       => '',
					'master'      => 'tm-testi-ui-container',
				),
				'company' => array(
					'id'          => TM_TESTI_POSTMETA . 'company',
					'name'        => TM_TESTI_POSTMETA . '[company]',
					'type'        => 'text',
					'label'       => esc_html__( 'Company Name:', 'cherry-testi' ),
					'placeholder' => esc_html__( 'Demo &amp; Co', 'cherry-testi' ),
					'value'       => '',
					'master'      => 'tm-testi-ui-container',
				),
			),
		) );

		$this->plugin->get_core()->init_module( 'cherry-post-meta', $metabox_args );
	}

	/**
	 * Adds a custom filter on 'request' when viewing the `Testimonials` screen in the admin.
	 *
	 * @since 1.0.0
	 */
	public function manage_columns() {
		$screen = get_current_screen();

		if ( empty( $screen->post_type ) || $this->post_type !== $screen->post_type ) {
			return;
		}

		// Modify the columns on the `Testimonials` screen.
		add_filter( "manage_edit-{$this->post_type}_columns", array( $this, 'columns' ) );
		add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'custom_column' ), 10, 2 );
	}

	/**
	 * Filters the columns on the `Testimonials` screen.
	 *
	 * @since  1.0.0
	 * @param  array $post_columns An array of column name => label.
	 * @return array
	 */
	public function columns( $post_columns ) {

		unset(
			$post_columns['author'],
			$post_columns[ 'taxonomy-' . $this->post_type . '_category' ],
			$post_columns['date']
		);

		// Add custom columns and overwrite the 'date' column.
		$post_columns['thumbnail']    = esc_html__( 'Avatar', 'cherry-testi' );
		$post_columns['author_name']  = esc_html__( 'Author Name', 'cherry-testi' );
		$post_columns['position']     = esc_html__( 'Position', 'cherry-testi' );
		$post_columns['company_name'] = esc_html__( 'Company Name', 'cherry-testi' );
		$post_columns[ 'taxonomy-' . $this->post_type . '_category' ] = esc_html__( 'Category', 'cherry-testi' );
		$post_columns['shortcode'] = esc_html__( 'Shortcode', 'cherry-testi' );

		// Return the columns.
		return $post_columns;
	}

	/**
	 * Add output for custom columns on the "menu items" screen.
	 *
	 * @since  1.0.0
	 * @param  string $column  The name of the column to display.
	 * @param  int    $post_id The ID of the current post.
	 */
	public function custom_column( $column, $post_id ) {
		static $prefix = '';

		if ( '' === $prefix ) {
			$shortcode = TM_Testimonials_Shortcode::get_instance();
			$prefix    = $shortcode->get_prefix();
		}

		require_once( TM_TESTI_DIR . 'public/includes/class-tm-testimonials-template-callbacks.php' );

		$callbacks = new TM_Testimonials_Template_Callbacks( array( 'size' => 50 ) );

		switch ( $column ) {
			case 'author_name':
				$name = $callbacks->get_name();
				echo empty( $name ) ? '&mdash;' : $name;
				break;

			case 'thumbnail':
				$avatar = $callbacks->get_avatar();
				echo empty( $avatar ) ? '&mdash;' : $avatar;
				break;

			case 'position':
				$position = $callbacks->get_position();
				echo empty( $position ) ? '&mdash;' : $position;
				break;

			case 'company_name':
				$company_name = $callbacks->get_company();
				echo empty( $company_name ) ? '&mdash;' : $company_name;
				break;

			case 'shortcode':
				echo '<input style="width:100%" type="text" onfocus="this.select();" readonly="readonly" value="[' . $prefix . 'testimonials ids=&quot;' . $post_id . '&quot;]">';
				break;

			default :
				break;
		}
	}

	/**
	 * Modify the quick links.
	 *
	 * @since  1.0.1
	 * @param  array   $actions An array of row action links.
	 * @param  WP_Post $post    The post object.
	 * @return array
	 */
	public function modify_row_actions( $actions, $post ) {

		if ( $post->post_type == $this->post_type && isset( $actions['inline hide-if-no-js'] ) ) {

			// Remove `Quick Edit`.
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}

	/**
	 * Print styles.
	 *
	 * @since 1.0.0
	 */
	public function print_styles() {
?>
<style type="text/css">
	.tm-testi-ui-container { padding-right: 0; padding-left: 0; }
	.tm-testi-ui-container .cherry-control__content { flex: auto; }
</style>
<?php }

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

TM_Testimonials_Admin::get_instance();
