<?php
/**
 * Shortcode settings view.
 *
 * @package    Cherry_Testi
 * @subpackage Views
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

$shortcode = TM_Testimonials_Shortcode::get_instance();
$atts      = $shortcode->get_shortcode_atts();

if ( empty( $atts ) || ! is_array( $atts ) ) {
	return;
}

$code_text = $list = '';

foreach ( $atts as $key => $att ) {
	$code_text .= sprintf( ' %s="%s"', esc_html( $key ), $att['default'] );
	$list .= sprintf( '<li><strong class="cherry-testi-shortcode-params__name">%s</strong> - %s</li>', esc_html( $key ), $att['desc'] );
}

printf( '<code>[%1$s%2$s%3$s]</code>', $shortcode->get_prefix(), $shortcode::$name, $code_text );
printf( '<h4 class="cherry-testi-shortcode-title">%s</h4><ul class="cherry-testi-shortcode-params">%s</ul>', esc_html__( 'Parameters list:', 'cherry_testi' ), $list );
