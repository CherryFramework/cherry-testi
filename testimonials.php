<?php
/**
 * Plugin Name: Cherry Testimonials
 * Plugin URI:  http://www.cherryframework.com/plugins/
 * Description: A testimonials management plugin for WordPress.
 * Version:     1.0.1
 * Author:      Template Monster
 * Author URI:  http://www.cherryframework.com/
 * Text Domain: cherry-testi
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 *
 * @package   Cherry_Testi
 * @author    Template Monster
 * @license   GPL-3.0+
 * @copyright 2002-2016, Template Monster
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// If class `TM_Testimonials_Plugin` not exists.
if ( ! class_exists( 'TM_Testimonials_Plugin' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 *
	 * @since 1.0.0
	 */
	class TM_Testimonials_Plugin {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * A reference to an instance of cherry framework core class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private $core = null;

		/**
		 * The post type name.
		 *
		 * @since 1.0.0
		 * @var   string
		 */
		private $post_type_name = 'tm-testimonials';

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Internationalize the text strings used.
			add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );
			add_action( 'plugins_loaded', array( $this, 'lang' ), 2 );

			// Set up a Cherry core.
			add_action( 'after_setup_theme', require( trailingslashit( dirname( __FILE__ ) ) . 'cherry-framework/setup.php' ), 0 );
			add_action( 'after_setup_theme', array( $this, 'get_core' ), 1 );
			add_action( 'after_setup_theme', array( 'Cherry_Core', 'load_all_modules' ), 2 );
			add_action( 'after_setup_theme', array( $this, 'includes' ), 4 );

			// Registers theme support for a `post-thumbnails` feature.
			add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );

			// Load public-facing stylesheet and javascript.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 9 );

			// Register activation and deactivation hook.
			add_action( 'tm_testimonials_plugin_pre_activation', array( $this, 'cpt_registration' ) );
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Defines constants for the plugin.
		 *
		 * @since 1.0.0
		 */
		public function constants() {

			/**
			 * Set constant name for the post type name.
			 *
			 * @since 1.0.0
			 */
			define( 'TM_TESTI_NAME', 'tm_testimonials' );

			/**
			 * Set the version number of the plugin.
			 *
			 * @since 1.0.0
			 */
			define( 'TM_TESTI_VERSION', '1.0.1' );

			/**
			 * Set the name for the `meta_key` value in the `wp_postmeta` table.
			 *
			 * @since 1.0.0
			 */
			define( 'TM_TESTI_POSTMETA', '_tm_testimonial_' );

			/**
			 * Set constant path to the plugin directory.
			 *
			 * @since 1.0.0
			 */
			define( 'TM_TESTI_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

			/**
			 * Set constant path to the plugin URI.
			 *
			 * @since 1.0.0
			 */
			define( 'TM_TESTI_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

			if ( ! defined( 'TM_TESTI_TMPL_SUBDIR' ) ) {
				define( 'TM_TESTI_TMPL_SUBDIR', 'templates/shortcodes/testimonials/' );
			}
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 */
		public function lang() {
			load_plugin_textdomain( 'cherry-testi', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Loads the core functions. These files are needed before loading anything else in the
		 * themes or plugins because they have required functions for use.
		 *
		 * @since 1.0.0
		 */
		public function get_core() {

			/**
			 * Fires before loads the core.
			 *
			 * @since 1.0.0
			 */
			do_action( 'tm_testimonials_core_before' );

			global $chery_core_version;

			if ( null !== $this->core ) {
				return $this->core;
			}

			if ( 0 < sizeof( $chery_core_version ) ) {
				$core_paths = array_values( $chery_core_version );

				require_once( $core_paths[0] );
			} else {
				die( 'Class Cherry_Core not found' );
			}

			$this->core = new Cherry_Core( array(
				'base_dir' => TM_TESTI_DIR . 'cherry-framework',
				'base_url' => TM_TESTI_URI . 'cherry-framework',
				'modules'  => array(
					'cherry-js-core' => array(
						'autoload' => false,
					),
					'cherry-ui-elements' => array(
						'autoload' => false,
					),
					'cherry-interface-builder' => array(
						'autoload' => false,
					),
					'cherry-handler'  => array(
						'autoload' => false,
					),
					'cherry-post-meta' => array(
						'autoload' => false,
					),
				),
			) );

			return $this->core;
		}

		/**
		 * Loads files from the 'public/includes' folder.
		 *
		 * @since 1.0.0
		 */
		public function includes() {
			require_once( TM_TESTI_DIR . 'public/includes/class-tm-testimonials-options.php' );
			require_once( TM_TESTI_DIR . 'public/includes/class-tm-testimonials-hook.php' );
			require_once( TM_TESTI_DIR . 'public/includes/class-tm-testimonials-registration.php' );
			require_once( TM_TESTI_DIR . 'public/includes/class-tm-testimonials-page-template.php' );
			require_once( TM_TESTI_DIR . 'public/includes/class-tm-testimonials-data.php' );
			require_once( TM_TESTI_DIR . 'public/includes/class-tm-testimonials-shortcode.php' );

			// Loads admin files.
			if ( is_admin() ) {
				require_once( TM_TESTI_DIR . 'admin/includes/class-tm-testimonials-admin.php' );

				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					require_once( TM_TESTI_DIR . 'admin/includes/class-tm-testimonials-ajax.php' );
				}
			}
		}

		/**
		 * Register and enqueue public-facing stylesheet.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_assets() {
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_style( 'jquery-swiper', plugins_url( 'includes/swiper/css/swiper.min.css', __FILE__ ), array(), '3.3.1' );
			wp_enqueue_style( 'cherry-testi', plugins_url( 'public/assets/css/style.css', __FILE__ ), array( 'jquery-swiper' ), TM_TESTI_VERSION );

			wp_register_script( 'jquery-swiper', plugins_url( "includes/swiper/js/swiper.jquery{$min}.js", __FILE__ ), array( 'jquery' ), '3.3.1', true );
			wp_register_script( 'cherry-testi-public', plugins_url( "public/assets/js/public{$min}.js", __FILE__ ), array( 'jquery-swiper' ), TM_TESTI_VERSION, true );
		}

		/**
		 * Fired when the plugin is activated.
		 *
		 * @since 1.0.0
		 */
		public function activation() {
			do_action( 'tm_testimonials_plugin_pre_activation' );

			flush_rewrite_rules();

			do_action( 'tm_testimonials_plugin_activation' );
		}

		/**
		 * Fired when the plugin is deactivated.
		 *
		 * @since 1.0.0
		 */
		public function deactivation() {
			flush_rewrite_rules();
		}

		/**
		 * Loads the CPT registration.
		 *
		 * @since 1.0.0
		 */
		public function cpt_registration() {
			require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'public/includes/class-tm-testimonials-registration.php' );

			/**
			 * Call CPT registration function.
			 *
			 * @link https://codex.wordpress.org/Function_Reference/flush_rewrite_rules#Examples
			 */
			TM_Testimonials_Registration::register_post_type();
			TM_Testimonials_Registration::register_taxonomy();
		}

		/**
		 * Ensure that "post-thumbnails" support is available for those themes that don't register it.
		 *
		 * @since 1.0.0
		 */
		public function add_theme_support() {

			if ( ! current_theme_supports( 'post-thumbnails' ) ) {
				add_theme_support( 'post-thumbnails' );
			}
		}

		/**
		 * Retrieve a post type name.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_post_type_name() {
			return apply_filters( 'tm_testimonials_get_post_type_name', $this->post_type_name );
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
}

/**
 * Gets the instance of the `TM_Testimonials_Plugin` class.
 *
 * @since  1.0.0
 * @return object
 */
function tm_testimonials_plugin() {
	return TM_Testimonials_Plugin::get_instance();
}

tm_testimonials_plugin();
