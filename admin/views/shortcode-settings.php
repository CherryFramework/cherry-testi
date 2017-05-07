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

$atts = tm_testimonials_shortcode()->get_shortcode_atts();

if ( empty( $atts ) || ! is_array( $atts ) ) {
	return;
}

$code_text = $list = '';

foreach ( $atts as $key => $attr ) {
	$default   = isset( $attr['default'] ) ? $attr['default'] : $attr['value'];
	$code_text .= sprintf( ' %s="%s"', esc_html( $key ), $default );
	$list      .= sprintf( '<li><strong class="cherry-testi-shortcode-params__name">%s</strong> - %s</li>', esc_html( $key ), $attr['description'] );
}

printf( '<code>[%1$s%2$s%]</code>', tm_testimonials_shortcode()->get_tag(), $code_text );
printf( '<h4 class="cherry-testi-shortcode-title">%s</h4><ul class="cherry-testi-shortcode-params">%s</ul>', esc_html__( 'Parameters list:', 'cherry-testi' ), $list );
