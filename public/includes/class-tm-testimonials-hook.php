<?php
/**
 * Class that store callbacks for custom hook.
 *
 * @package    Cherry_Testi
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Class for Testimonials hooks.
 *
 * @since 1.0.0
 */
class TM_Testimonials_Hook {

	/**
	 * PHP-constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {}

	public static function slider_settings( $format, $args ) {

		if ( ! self::is_slider( $args ) ) {
			return $format;
		}

		if ( empty( $args['data_atts'] ) ) {
			return $format;
		}

		$data_atts = wp_json_encode( $args['data_atts'] );

		if ( false === $data_atts ) {
			return $format;
		}

		$format = "<div class='tm-testi'>%s<div class='%s' data-atts='" . $data_atts . "'>%s</div></div>";

		return $format;
	}

	/**
	 * Added a `Swiper` required wrapper.
	 *
	 * @since  1.0.0
	 * @param  array $args The array of arguments.
	 * @return array
	 */
	public static function slider_container_class( $args ) {

		if ( ! self::is_slider( $args ) ) {
			return $args;
		}

		if ( isset( $args['container'] ) ) {
			$args['container'] = '<div class="tm-testi__list swiper-wrapper">%s</div>';
		}

		return $args;
	}

	/**
	 * Added a `Swiper` required item.
	 *
	 * @since  1.0.0
	 * @param  array $classes Item CSS classes.
	 * @param  array $args    The array of arguments.
	 * @return array
	 */
	public static function slider_item_class( $classes, $args ) {

		if ( ! self::is_slider( $args ) ) {
			return $classes;
		}

		$classes[] = 'swiper-slide';

		return $classes;
	}

	/**
	 * Added a `Swiper` need pagination.
	 *
	 * @since  1.0.0
	 * @param  string $output HTML-formatted Testimonials.
	 * @param  array  $args   The array of arguments.
	 * @return string
	 */
	public static function slider_pagination( $output, $args ) {

		if ( ! self::is_slider( $args ) ) {
			return $output;
		}

		return $output . '<div class="tm-testi__pagination swiper-pagination"></div>';
	}

	/**
	 * Added a `Swiper` need navigation.
	 *
	 * @since  1.0.0
	 * @param  string $output HTML-formatted Testimonials.
	 * @param  array  $args   The array of arguments.
	 * @return string
	 */
	public static function slider_navigation( $output, $args ) {

		if ( ! self::is_slider( $args ) ) {
			return $output;
		}

		return $output . '<div class="tm-testi__button-next swiper-button-next"></div>
		<div class="tm-testi__button-prev swiper-button-prev"></div>';
	}

	/**
	 * Added a `Swiper` script.
	 *
	 * @since 1.0.0
	 * @param string $output The HTML-formatted testimonials.
	 * @param array  $query  List of WP_Post objects.
	 * @param array  $args   The array of arguments.
	 */
	public static function slider_script( $output, $query, $args ) {

		if ( ! self::is_slider( $args ) ) {
			return $output;
		}

		wp_enqueue_script( 'cherry-testi-script' );
		return $output;
	}

	/**
	 * Check if we use a slider now.
	 *
	 * @since  1.0.0
	 * @param  array   $args The array of arguments.
	 * @return boolean
	 */
	public static function is_slider( $args ) {
		return ! empty( $args['context'] ) && ( 'slider' === $args['context'] ) ? true : false;
	}
}
