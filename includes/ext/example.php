<?php
/**
 * Shortcode class
 */

class TM_Testimonials_VC_Mapping {

	/**
	 * Shortcode name.
	 *
	 * @since 1.1.0
	 * @var string
	 */
	public $tag = '';

	/**
	 * List of shortcode attributes.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	public $atts = array();

	/**
	 * List of shortcode params.
	 *
	 * @since 1.1.0
	 * @var array
	 */
	public $params = array();

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
		add_filter( 'tm_testimonials_vc_mapping_params', array( $this, 'convert_types_fix' ), 11, 2 );
	}

	public function mapping() {
		vc_map( array(
			'base'           => $this->tag,
			'name'           => esc_html__( 'Cherry Testimonials', 'cherry-testi' ),
			'description'    => esc_html__( 'Shortcode is used to display the testimonials', 'cherry-testi' ),
			'category'       => esc_html__( 'Cherry', 'cherry-testi' ),
			'php_class_name' => 'TM_Testimonials_VC_ShortCode', // important
			'params'         => $this->get_params(),
		) );
	}

	public function get_params() {

		if ( empty( $this->params ) ) {

			foreach ( $this->atts as $name => $attr ) {
				$params = array(
					'heading'     => $attr['title'],
					'description' => ! empty( $attr['description'] ) ? $attr['description'] : '',
					'type'        => $this->_get_attr_type( $attr ),
					'value'       => $this->_get_attr_value( $attr ),
					'param_name'  => $name,
				);

				if ( ! empty( $attr['default'] ) ) {
					$params = array_merge( $params, array( 'std' => $this->_get_attr_std( $attr ) ) );
				}

				if ( ! empty( $attr['master'] ) ) {
					$params = array_merge( $params, array( 'dependency' => $this->_get_attr_deps( $attr ) ) );
				}

				$this->params[ $name ] = $params;
			}
		}

		return apply_filters( 'tm_testimonials_vc_mapping_params', $this->params, $this->atts );
	}

	public function _get_attr_deps( $attribute ) {
		$master = $attribute['master'];
		$deps   = array();

		foreach ( $this->atts as $key => $attr ) {
			$type = $attr['type'];

			switch ( $type ) {
				case 'select':
				case 'radio':
				case 'checkbox':

					if ( ! empty( $attr['options'] ) ) {
						foreach ( $attr['options'] as $option => $data ) {

							if ( ! is_array( $data ) ) {
								continue;
							}

							if ( empty( $data['slave'] ) ) {
								continue;
							}

							if ( $master == $data['slave'] ) {
								$deps = array(
									'element' => $key,
									'value'   => $option,
								);

								break;
							}
						}
					}
					break;

				case 'switcher':

					if ( ! empty( $attr['toggle']['true_slave'] ) ) {
						$slave = $attr['toggle']['true_slave'];

						if ( $master == $slave ) {
							$deps = array(
								'element' => $key,
								'value'   => 'yes',
							);
						}
					}

					break;

				default:

					if ( ! empty( $attr['slave'] ) ) {
						$deps = array(
							'element' => $key,
							'value'   => $attr['value'],
						);
					}

					break;
			}
		}

		return $deps;
	}

	public function _get_attr_type( $attr ) {
		$type = $attr['type'];

		switch ( $type ) {

			case 'textarea':
				$vc_type = 'textarea';
				break;

			case 'switcher':
				$vc_type = 'checkbox';
				break;

			case 'select':
			case 'radio':
			case 'checkbox':
				$vc_type = 'dropdown';
				break;

			default:
				$vc_type = 'textfield';
				break;
		}

		return $vc_type;
	}

	public function _get_attr_value( $attr ) {
		$type = $attr['type'];

		switch ( $type ) {
			case 'select':
			case 'radio':
			case 'checkbox':
				$options  = empty( $attr['options'] ) ? $this->apply_options_cb( $attr ) : $attr['options'];
				$_options = array();

				foreach ( $options as $option => $data ) {

					if ( is_array( $data ) ) {
						$_options[ $option ] = $data['label'];

					} else {
						$_options[ $option ] = $data;
					}
				}

				$value = array_flip( $_options );
				break;

			case 'switcher':
				$filtered = filter_var( $attr['value'], FILTER_VALIDATE_BOOLEAN );
				$value    = $filtered ? array( esc_html__( 'Yes', 'cherry-testi' ) => 'yes' ) : false;
				break;

			default:
				$value = $attr['value'];
				break;
		}

		return $value;
	}

	public function _get_attr_std( $attr ) {
		$type = $attr['type'];

		switch ( $type ) {
			case 'switcher':
				$filtered = filter_var( $attr['default'], FILTER_VALIDATE_BOOLEAN );
				$std      = $filtered ? 'yes' : false;
				break;

			default:
				$std = $attr['default'];
				break;
		}

		return $std;
	}

	/**
	 * Apply shortcode options callback if required.
	 *
	 * @since  1.1.0
	 * @param  array $atts
	 * @return array
	 */
	private function apply_options_cb( $atts ) {

		if ( empty( $atts['options_cb'] ) || ! is_callable( $atts['options_cb'] ) ) {
			return array();
		}

		return call_user_func( $atts['options_cb'] );
	}

	public function convert_types_fix( $params, $atts ) {
		$params['category']['type'] = 'textfield';

		return $params;
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
