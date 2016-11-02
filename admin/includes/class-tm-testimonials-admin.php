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
		$menu_slug       = $this->get_menu_slug();

		// Load post meta boxes on the post editing screen.
		add_action( 'load-post.php', array( $this, 'init_post_meta' ) );
		add_action( 'load-post-new.php', array( $this, 'init_post_meta' ) );

		// Only run our customization on the 'edit.php' page in the admin.
		add_action( 'load-edit.php', array( $this, 'load_edit' ) );
		// add_action( 'admin_head-tm-testimonials_page_settings', array( $this, 'print_settings_styles' ) );

		add_action( "load-{$this->post_type}_page_{$menu_slug}", array( $this, 'init_modules' ) );

		// add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 0 );
		add_action( 'admin_menu', array( $this, '_add_submenu_page' ) );

		add_action( 'tm_testimonials_plugin_activation', array( $this, 'create_options' ) );
	}

	public function init_modules() {
		$this->plugin->get_core()->init_module( 'cherry-js-core' );
		$this->plugin->get_core()->init_module( 'cherry-interface-builder' );
	}

	/**
	 * Init `cherry-post-meta` module on the `Add New Testimonial` and `Edit Testimonial` screens.
	 *
	 * @since 1.0.0
	 */
	public function init_post_meta() {
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

	public function _add_submenu_page() {
		add_submenu_page(
			'edit.php?post_type=' . $this->plugin->get_post_type_name(),
			esc_html__( 'Settings', 'cherry-testi' ),
			esc_html__( 'Settings', 'cherry-testi' ),
			'manage_options',
			$this->get_menu_slug(),
			array( $this, 'settings_callback' )
		);
	}

	public function settings_callback() {
		$builder = $this->plugin->get_core()->modules['cherry-interface-builder'];

		$builder->register_section( array(
			'option_section' => array(
				'type'   => 'section',
				'scroll' => false,
				'title'  => esc_html__( 'Settings', 'cherry-testi' ),
			) )
		);

		$builder->register_form(
				array(
					'option_form' => array(
					'type'              => 'form',
					'parent'            => 'option_section',
					'action'            => 'my_action.php',
				),
			)
		);

		$builder->register_settings(
			array(
				'ui_elements' => array(
					'type'              => 'settings',
					'parent'            => 'option_section',
					'title'             => esc_html__( 'Title', 'text-domain' ),
					'description'       => esc_html__( 'Description', 'text-domain' ),
				),
				'bi_elements' => array(
					'type'              => 'settings',
					'parent'            => 'option_section',
					'title'             => esc_html__( 'Title', 'text-domain' ),
					'description'       => esc_html__( 'Description', 'text-domain' ),
				),
			)
		);

		$builder->register_component(
			array(
				'accordion' => array(
					'type'              => 'component-accordion',
					'parent'            => 'bi_elements',
					'title'             => esc_html__( 'Title', 'text-domain' ),
					'description'       => esc_html__( 'Description', 'text-domain' ),
				),
				'toggle' => array(
					'type'              => 'component-toggle',
					'parent'            => 'bi_elements',
					'title'             => esc_html__( 'Title', 'text-domain' ),
					'description'       => esc_html__( 'Description', 'text-domain' ),
				),
				'tab_vertical' => array(
					'type'              => 'component-tab-vertical',
					'parent'            => 'bi_elements',
					'title'             => esc_html__( 'Title', 'text-domain' ),
					'description'       => esc_html__( 'Description', 'text-domain' ),
				),
				'tab_horizontal' => array(
					'type'              => 'component-tab-horizontal',
					'parent'            => 'bi_elements',
					'title'             => esc_html__( 'Title', 'text-domain' ),
					'description'       => esc_html__( 'Description', 'text-domain' ),
				),
			)
		);

		$builder->register_control(
				array(
					'checkbox' => array(
						'type'        => 'checkbox',
						'parent'      => 'ui_elements',
						'title'       => esc_html__( 'Title', 'text-domain' ),
						'description' => esc_html__( 'Description', 'text-domain' ),
						'class'       => '',
						'value'       => array(
							'checkbox' => 'true',
						),
						'options' => array(
							'checkbox' => esc_html__( 'Check Me', 'text-domain' ),
						),
					),
					'checkbox_multi' => array(
						'type'        => 'checkbox',
						'parent'      => 'ui_elements',
						'title'       => esc_html__( 'Title', 'text-domain' ),
						'description' => esc_html__( 'Description', 'text-domain' ),
						'class'       => '',
						'value'       => array(
							'checkbox-0' => 'false',
							'checkbox-1' => 'false',
							'checkbox-2' => 'false',
							'checkbox-3' => 'true',
							'checkbox-4' => 'true',
						),
						'options' => array(
							'checkbox-0' => array(
								'label' => esc_html__( 'Check Me #1', 'text-domain' ),
							),
							'checkbox-1' => esc_html__( 'Check Me #2', 'text-domain' ),
							'checkbox-2' => esc_html__( 'Check Me #3', 'text-domain' ),
							'checkbox-3' => esc_html__( 'Check Me #4', 'text-domain' ),
							'checkbox-4' => esc_html__( 'Check Me #5', 'text-domain' ),
						),
					),
				)
			);

		$builder->render();
	}

	/**
	 * Add a submenu page.
	 *
	 * @since 1.0.0
	 */
	public function add_submenu_page() {
		$this->plugin->get_core()->init_module( 'cherry-js-core' );
		$this->plugin->get_core()->init_module( 'cherry-page-builder' );
		$this->plugin->get_core()->init_module( 'cherry-interface-builder' );
		$this->plugin->get_core()->init_module( 'cherry-ui-elements', array(
			'ui_elements' => array(
				'text',
				'select',
				'stepper',
			),
		) );

		$page_object = $this->plugin->get_core()->modules['cherry-page-builder']->make(
			'settings',
			esc_html__( 'Settings', 'cherry-testi' ),
			'edit.php?post_type=' . $this->plugin->get_post_type_name()
		)->set( array(
			'sections' => $this->get_sections(),
			'settings' => $this->get_settings(),
		) );

		add_filter( 'cherry_core_js_ui_init_settings', array( $this, 'init_ui_js' ), 10 );
	}

	/**
	 * Adds a custom filter on 'request' when viewing the `Testimonials` screen in the admin.
	 *
	 * @since 1.0.0
	 */
	public function load_edit() {
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
			$post_columns['taxonomy-' . $this->post_type . '_category'],
			$post_columns['date']
		);

		// Add custom columns and overwrite the 'date' column.
		$post_columns['thumbnail']    = esc_html__( 'Avatar', 'cherry-testi' );
		$post_columns['author_name']  = esc_html__( 'Author', 'cherry-testi' );
		$post_columns['position']     = esc_html__( 'Position', 'cherry-testi' );
		$post_columns['company_name'] = esc_html__( 'Company Name', 'cherry-testi' );
		$post_columns['shortcode']    = esc_html__( 'Shortcode', 'cherry-testi' );

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
	 * Retrieve a sections.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_sections() {
		return apply_filters( 'tm_testimonials_get_page_sections', array(
			'tm_testimonials_general' => array(
				'slug' => 'tm_testimonials_general',
				'name' => esc_html__( 'General Settings', 'cherry-testi' ),
			),
		) );
	}

	/**
	 * Retrieve a settings.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_settings() {
		return apply_filters( 'tm_testimonials_get_page_settings', array(
			'tm_testimonials_general' => array(
				array(
					'slug'  => 'page',
					'title' => esc_html__( 'Testimonails page', 'cherry-testi' ),
					'type'  => 'select',
					'field' => array(
						'id'           => 'page',
						'options'      => $this->get_pages(),
						'inline_style' => 'width:auto',
						'value'        => apply_filters( 'tm_testimonials_index_page_slug', 0 ),
					),
				),
				array(
					'slug'  => 'posts_per_page',
					'title' => esc_html__( 'Number of post to show per page', 'cherry-testi' ),
					'type'  => 'stepper',
					'field' => array(
						'id'        => 'posts_per_page',
						'min_value' => 1,
						'value'     => TM_Testimonials_Page_Template::$posts_per_page,
					),
				),
			),
		) );
	}

	/**
	 * Add/update options on plugin activation.
	 *
	 * @since 1.0.0.
	 */
	public function create_options() {
		$all_settings = $this->get_settings();

		foreach ( $all_settings as $key => $settings ) {
			$value = $this->_get_option_pair( $settings );
			update_option( $key, $value );
		}
	}

	/**
	 * Retrieve a plugin options from settings.
	 *
	 * @since  1.0.0
	 * @param  array $settings Plugin section's settings.
	 * @return array
	 */
	public function _get_option_pair( $settings ) {
		$option = array();

		foreach ( $settings as $key => $value ) {

			if ( empty( $value['slug'] ) ) {
				continue;
			}

			if ( empty( $value['field'] ) || ! is_array( $value['field'] ) ) {
				continue;
			}

			if ( empty( $value['field']['value'] ) ) {
				continue;
			}

			$option[ $value['slug'] ] = $value['field']['value'];
		}

		return $option;
	}

	/**
	 * Retrieve a set of all pages (key - page slug, value - page title).
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function get_pages() {
		$all_pages = get_pages( apply_filters( 'tm_testimonials_get_pages_args', array(
				'hierarchical' => 1,
				'parent'       => -1,
				'post_status'  => 'publish',
			)
		) );

		$pages = array( esc_attr__( '&mdash;&nbsp;Select&nbsp;&mdash;', 'cherry-testi' ) );

		foreach ( $all_pages as $page ) {
			$pages[ $page->post_name ] = $page->post_title;
		}

		return $pages;
	}

	public function get_menu_slug() {
		return apply_filters( 'tm_testimonials_menu_slug', 'settings' );
	}

	/**
	 * Init UI elements JS.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function init_ui_js( $settings ) {
		$settings['auto_init'] = true;
		$settings['targets']   = array( 'body' );

		return $settings;
	}

	/**
	 * Print styles.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function print_styles() { ?>
		<style type="text/css">
			.tm-testi-ui-container{ margin-top: 0; margin-bottom: 0; }
		</style>
	<?php }

	/**
	 * Print styles.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function print_settings_styles() { ?>
		<style type="text/css">
			.cherry-settings-tabs select.cherry-ui-select,
			.cherry-settings-tabs .cherry-ui-stepper input[type=number],
			.cherry-settings-tabs input.cherry-ui-text { background-color: #fff; }
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
