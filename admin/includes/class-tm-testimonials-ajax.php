<?php
/**
 * Ajax-handlers for page settings.
 *
 * @package    Cherry_Testi
 * @subpackage Admin
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

// If class `TM_Testimonials_Ajax_Handler` doesn't exists yet.
if ( ! class_exists( 'TM_Testimonials_Ajax_Handler' ) ) {

	/**
	 * TM_Testimonials_Ajax_Handler class.
	 */
	class TM_Testimonials_Ajax_Handler {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->init();
		}

		/**
		 * Init `cherry-handler` module.
		 *
		 * @since 1.0.0
		 */
		public function init() {
			tm_testimonials_plugin()->get_core()->init_module( 'cherry-handler', array(
				'id'           => 'cherry_testi_save_setting',
				'action'       => 'cherry_testi_save_setting',
				'capability'   => 'manage_options',
				'callback'     => array( $this, 'save_handler' ),
				'sys_messages' => array(
					'invalid_base_data' => esc_html__( 'Unable to process the request without nonce or server error', 'cherry-testi' ),
					'no_right'          => esc_html__( 'No capabilities for this action', 'cherry-testi' ),
					'invalid_nonce'     => esc_html__( 'Sorry, you are not allowed to save settings', 'cherry-testi' ),
					'access_is_allowed' => esc_html__( 'Settings save successfully','cherry-testi' ),
				),
			) );

			tm_testimonials_plugin()->get_core()->init_module( 'cherry-handler', array(
				'id'           => 'cherry_testi_reset_setting',
				'action'       => 'cherry_testi_reset_setting',
				'capability'   => 'manage_options',
				'callback'     => array( $this, 'reset_handler' ),
				'sys_messages' => array(
					'invalid_base_data' => esc_html__( 'Unable to process the request without nonce or server error', 'cherry-testi' ),
					'no_right'          => esc_html__( 'No capabilities for this action', 'cherry-testi' ),
					'invalid_nonce'     => esc_html__( 'Sorry, you are not allowed to save settings', 'cherry-testi' ),
					'access_is_allowed' => esc_html__( 'Settings reset successfully','cherry-testi' ),
				),
			) );
		}

		/**
		 * Handler for save settings option.
		 *
		 * @since 1.0.0
		 */
		public function save_handler() {

			if ( ! empty( $_REQUEST['data'] ) ) {
				update_option( 'cherry-testi', $_REQUEST['data'] );
			}
		}

		/**
		 * Handler for reset settings option to default.
		 *
		 * @since 1.0.0
		 */
		public function reset_handler() {
			delete_option( 'cherry-testi' );
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

TM_Testimonials_Ajax_Handler::get_instance();
