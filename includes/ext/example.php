<?php
/**
 * Shortcode class
 */

class TM_Testimonials_VC_Mapping {

	/**
	 * List of shortcode attributes.
	 *
	 * @var array
	 */
	public $atts = array();

	/**
	 * List of shortcode params.
	 *
	 * @var array
	 */
	public $params = array();

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.1.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Constructor for the class.
	 *
	 * @since 1.1.0
	 */
	public function __construct( $atts = array() ) {
		$this->atts = $atts;

		add_action( 'vc_before_init', array( $this, 'mapping' ) );
	}

	public function mapping() {
		vc_map( array(
			'base'           => 'testi',
			'name'           => esc_html__( 'Testimonials', 'cherry-testi' ),
			'description'    => esc_html__( 'Shortcode is used to display the testimonials', 'cherry-testi' ),
			'category'       => esc_html__( 'Cherry', 'cherry-testi' ),
			'php_class_name' => 'TM_Testimonials_VC_ShortCode', // important
			'params'         => $this->get_params(),
		) );
	}

	public function get_params() {

		if ( empty( $this->params ) ) {

			foreach ( $this->atts as $name => $attribute ) {
				$this->params[] = array(
					'type'        => 'textfield',
					'heading'     => $attribute['title'],
					'description' => $attribute['description'],
					'value'       => $attribute['value'],
					'param_name'  => $name,
				);
			}
		}

		return $this->params;
	}

	public function _get_control_type() {

		$relations = array(
			'text'     => 'textfield',
			'select'   => 'dropdown',
			'switcher' => 'dropdown',
			'slider'   => 'dropdown',
		);
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.1.0
	 * @return object
	 */
	public static function get_instance( $params = array() ) {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self( $params );
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
function tm_testimonials_vc_mapping( $atts = array() ) {
	// return TM_Testimonials_VC_Mapping::get_instance( $atts );


	$foo = TM_Testimonials_VC_Mapping::get_instance( $atts );
	error_log( var_export($foo, true) );

	return $foo;
}

/*
 * Settings array to setup shortcode "Hello world"
 * base param is required.
 *
 * Mapping examples: $PLUGIN_DIR/config/map.php
 *
 * name - used in content elements menu and shortcode edit screen.
 * base - shortcode base. Example cheh
 * class - helper class to target your shortcode in css in visual composer edit mode
 * icon - in order to add icon for your shortcode in dropdown menu, add class name here and style it in
 *          your own css file. Note: bootstrap icons supported.
 * controls - in visual composer mode shortcodes can have different controls (popup_delete, edit_popup_delete, size_delete, popup_delete, full).
				Default is full.
 * params - array which holds your shortcode params. This params will be editable in shortcode settings page.
 *
 * Available param types:
 *
 * textarea_html (only one html textarea is permitted per shortcode)
 * textfield - simple input field,
 * dropdown - dropdown element with set of available options,
 * attach_image - single image selection,
 * attach_images - multiple images selection,
 * exploded_textarea - textarea, where each line will be imploded with comma (,),
 * posttypes - checkboxes with available post types,
 * widgetised_sidebars - dropdown element with set of available widget regions,
 * textarea - simple textarea,
 * textarea_raw_html - textarea, it's content will be codede into base64 (this allows you to store raw js or raw html code).
 *
 */

// $shortcode_atts = $this->get_shortcode_atts();
// $params         = array();

// foreach ( $shortcode_atts as $name => $attribute ) {
// 	$params[] = array(
// 		'type'        => 'textfield',
// 		'heading'     => $attribute['title'],
// 		'description' => $attribute['description'],
// 		'value'       => $attribute['value'],
// 		'param_name'  => $name,
// 	);
// }

// vc_map( array(
// 	'base'           => 'testi2',
// 	'name'           => esc_html__( 'Cherry Testimonials', 'cherry-testi' ),
// 	'category'       => esc_html__( 'Cherry', 'cherry-testi' ),
// 	'php_class_name' => 'TM_Testimonials_VC_Compat',
// 	'params'         => $params,
// ) );

// $bar = array(
// 	array(
// 		'type'        => 'textfield',
// 		'holder'      => 'h2',
// 		'class'       => '',
// 		'heading'     => esc_html__( 'Foo attribute', 'cherry-testi' ),
// 		'param_name'  => 'foo',
// 		'value'       => esc_html__( "I'm foo attribute", 'cherry-testi' ),
// 		'description' => esc_html__( 'Enter foo value.', 'cherry-testi' ),
// 	),
// 	array(
// 		'type'        => 'textarea_html',
// 		'holder'      => 'div',
// 		'class'       => '',
// 		'heading'     => esc_html__( 'Text', 'cherry-testi' ),
// 		'param_name'  => 'content',
// 		'value'       => esc_html__( "I'm hello world", 'cherry-testi' ),
// 		'description' => esc_html__( 'Enter your content.', 'cherry-testi' ),
// 	),
// 	array(
// 		'type'        => 'dropdown',
// 		'heading'     => esc_html__( 'Drop down example', 'cherry-testi' ),
// 		'param_name'  => 'my_dropdown',
// 		'value'       => array( 1, 2, 'three' ),
// 		'description' => esc_html__( 'One, two or three?', 'cherry-testi' ),
// 	),
// );


// echo '<pre>';
// var_dump( $this );
// echo '</pre>';