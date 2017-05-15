<?php
/**
 * PHP-class for adding Testimonials-shortcode to the Visual Composer plugin.
 *
 * @package    Cherry_Testi
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

if ( ! class_exists( 'TM_Abstract_VC_Compat' ) ) {
	require_once( TM_TESTI_DIR . 'includes/ext/class-tm-abstract-vc-compat.php' );
}

class TM_Testimonials_VC_Mapping extends TM_Abstract_VC_Compat {

	/**
	 * Shortcode name.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $tag = '';

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.1.0
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Constructor for the class.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $tag, $atts ) {
		$this->tag  = $tag;
		$this->atts = $atts;

		add_action( 'vc_before_init', array( $this, 'mapping' ) );
		add_filter( 'tm_testimonials_public_scripts_ver', array( $this, 'scripts_ver' ) );
		parent::__construct();
	}

	/**
	 * Added shortcode to the Visual Composer content elements list.
	 *
	 * @since 1.1.0
	 */
	public function mapping() {
		$_params = $this->get_params();
		$params  = $this->unique_fix( $_params );

		vc_map( array(
			'base'           => $this->tag,
			'name'           => esc_html__( 'Cherry Testimonials', 'cherry-testi' ),
			'description'    => esc_html__( 'Shortcode is used to display the testimonials', 'cherry-testi' ),
			'category'       => esc_html__( 'Cherry', 'cherry-testi' ),
			'php_class_name' => 'TM_Testimonials_VC_ShortCode', // important
			'params'         => $params,
		) );
	}

	/**
	 * `Category` control-type fix.
	 *
	 * Cause, e.g. the dropdown(select) control-type is not good for selecting categories.
	 * Only for `[tm_testimonials]` shortcode.
	 *
	 * @since  1.1.0
	 * @param  array $params
	 * @return array
	 */
	public function unique_fix( $params ) {
		$params['category']['type']               = 'textfield';
		$params['slides_per_view_phone']['type']  = 'dropdown';
		$params['slides_per_view_tablet']['type'] = 'dropdown';
		$params['slides_per_view_laptop']['type'] = 'dropdown';
		$params['slides_per_view']['type']        = 'dropdown';

		$params['slides_per_view_phone']['value']  = array( 1, 2, );
		$params['slides_per_view_tablet']['value'] = array( 1, 2, 3, 4, );
		$params['slides_per_view_laptop']['value'] = array( 1, 2, 3, 4, 5, 6, );
		$params['slides_per_view']['value']        = array( 1, 2, 3, 4, 5, 6, 7, 8, );

		return $params;
	}

	/**
	 * Set dynamic script version.
	 * Don't cache javascript file `public.js` on fronted-editor mode.
	 *
	 * @since  1.1.0
	 * @param  string $version
	 * @return string
	 */
	public function scripts_ver( $version ) {

		if ( did_action( 'vc_inline_editor_page_view' ) ) {
			return time();
		}

		return $version;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.1.0
	 * @return object
	 */
	public static function get_instance( $tag, $atts ) {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self( $tag, $atts );
		}

		return self::$instance;
	}
}

if ( class_exists( 'WPBakeryShortCode' ) ) {
	class TM_Testimonials_VC_ShortCode extends WPBakeryShortCode {

		/**
		 * Thi methods returns HTML code for frontend representation of your shortcode.
		 * You can use your own html markup.
		 *
		 * @since  1.1.0
		 * @param  $atts    Shortcode attributes.
		 * @param  $content Shortcode content.
		 * @return string
		 */
		protected function content( $atts, $content = null ) {
			return tm_testimonials_shortcode()->do_shortcode( $atts, $content );
		}
	}
}

/**
 * Returns instance of TM_Testimonials_VC_Mapping.
 *
 * @since  1.1.0
 * @return object
 */
function tm_testimonials_vc_mapping( $tag, $atts ) {
	return TM_Testimonials_VC_Mapping::get_instance( $tag, $atts );
}
