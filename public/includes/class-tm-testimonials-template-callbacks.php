<?php
/**
 * Define callback functions for templater.
 *
 * @package    Cherry_Testi
 * @subpackage Public
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

/**
 * Callbacks for macroses.
 *
 * @since 1.0.0
 */
class TM_Testimonials_Template_Callbacks {

	/**
	 * Shortcode attributes array.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	public $atts = array();

	/**
	 * Current post meta.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	public $post_meta = null;

	/**
	 * Class constructor.
	 *
	 * @since 1.0.0
	 * @param array $atts Set of attributes.
	 */
	public function __construct( $atts ) {
		$this->atts = $atts;
	}

	/**
	 * Get post meta.
	 *
	 * @since 1.0.0
	 */
	public function get_meta() {
		if ( null === $this->post_meta ) {
			global $post;

			$this->post_meta = get_post_meta( $post->ID, TM_TESTI_POSTMETA, true );
		}

		return $this->post_meta;
	}

	/**
	 * Clear post data after loop iteration.
	 *
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function clear_data() {
		$this->post_meta = null;
	}

	/**
	 * Get post thumbnail.
	 *
	 * @since 1.0.0
	 */
	public function get_avatar() {
		global $post;

		if ( isset( $this->atts['show_avatar'] ) && false === $this->atts['show_avatar'] ) {
			return;
		}

		$size   = ! empty( $this->atts['size'] ) ? $this->atts['size'] : 100;
		$avatar = $this->get_image( $post->ID, $size );

		return apply_filters( 'tm_testimonials_avatar_template_callbacks', $avatar, $post->ID, $this->atts );
	}

	/**
	 * Get post content.
	 *
	 * @since 1.0.0
	 */
	public function get_content() {
		global $post;

		$_content       = apply_filters( 'tm_testimonials_content', get_the_content( '' ), $post );
		$content_length = intval( $this->atts['content_length'] );

		if ( ! $_content || 0 == $content_length ) {
			return;
		}

		if ( -1 == $content_length || post_password_required() ) {
			$content = apply_filters( 'the_content', $_content );
			$content = str_replace( ']]>', ']]&gt;', $content );
		} else {
			/* wp_trim_excerpt analog */
			$content = strip_shortcodes( $_content );
			$content = apply_filters( 'the_content', $content );
			$content = str_replace( ']]>', ']]&gt;', $content );
			$content = wp_trim_words( $content, $content_length, apply_filters( 'tm_testimonials_content_more', '&hellip;', $this->atts, TM_Testimonials_Shortcode::$name ) );
			$content = '<p>' . $content . '</p>';
		}

		return apply_filters( 'tm_testimonials_content_template_callbacks', $content, $post->ID, $this->atts );
	}

	/**
	 * Get testimonial's email.
	 *
	 * @since 1.0.0
	 */
	public function get_email() {
		global $post;

		if ( isset( $this->atts['show_email'] ) && false === $this->atts['show_email'] ) {
			return;
		}

		$post_meta = $this->get_meta();

		if ( empty( $post_meta['email'] ) ) {
			return;
		}

		$email = '<a href="mailto:' . antispambot( $post_meta['email'], 1 ) . '" class="tm-testi__item-email">' . antispambot( $post_meta['email'] ) . '</a>';

		return apply_filters( 'tm_testimonials_email_template_callbacks', $email, $post->ID, $this->atts );
	}

	/**
	 * Get testimonial's name.
	 *
	 * @since 1.0.0
	 */
	public function get_name() {
		global $post;

		$post_meta = $this->get_meta();
		$format    = '<span class="%3$s">%1$s</span>';

		if ( ! empty( $post_meta['url'] ) ) {
			$format = '<a class="%3$s" href="%2$s" target="_blank">%1$s</a>';
		}

		$name = sprintf( $format,
			esc_html( get_the_title( $post->ID ) ),
			esc_url( $post_meta['url'] ),
			'tm-testi__item-name'
		);

		return apply_filters( 'tm_testimonials_author_name_template_callbacks',
			$name,
			$post->ID,
			$this->atts
		);
	}

	/**
	 * Get testimonial's url.
	 *
	 * @since 1.0.0
	 */
	public function get_url() {
		global $post;

		$post_meta = $this->get_meta();

		if ( empty( $post_meta['url'] ) ) {
			return;
		}

		return apply_filters( 'tm_testimonials_url_template_callbacks',
			esc_url( $post_meta['url'] ),
			$post->ID,
			$this->atts
		);
	}

	/**
	 * Get author's position.
	 *
	 * @since 1.0.0
	 */
	public function get_position() {
		global $post;

		if ( isset( $this->atts['show_position'] ) && false === $this->atts['show_position'] ) {
			return;
		}

		$post_meta = $this->get_meta();

		if ( empty( $post_meta['position'] ) ) {
			return;
		}

		$format = apply_filters( 'tm_testimonials_position_format_callbacks',
			'<span class="tm-testi__item-position">%s</span>',
			$post->ID,
			$this->atts
		);

		return apply_filters( 'tm_testimonials_position_template_callbacks',
			sprintf( $format, esc_html( $post_meta['position'] ) ),
			$post->ID,
			$this->atts
		);
	}

	/**
	 * Get company name.
	 *
	 * @since 1.0.0
	 */
	public function get_company() {
		global $post;

		if ( isset( $this->atts['show_company'] ) && false === $this->atts['show_company'] ) {
			return;
		}

		$post_meta = $this->get_meta();

		if ( empty( $post_meta['company'] ) ) {
			return;
		}

		$format = apply_filters( 'tm_testimonials_company_format_callbacks',
			'<span class="tm-testi__item-company">%s</span>',
			$post->ID,
			$this->atts
		);

		return apply_filters( 'tm_testimonials_company_template_callbacks',
			sprintf( $format, esc_html( $post_meta['company'] ) ),
			$post->ID,
			$this->atts
		);
	}

	/**
	 * Get the image for the given ID. If no featured image, check for Gravatar e-mail.
	 *
	 * @since  1.0.0
	 * @param  int              $id   The post ID.
	 * @param  string|array|int $size The image dimension.
	 * @return string
	 */
	public function get_image( $id, $size ) {
		$class = 'tm-testi__item-avatar';

		if ( has_post_thumbnail( $id ) ) {

			// If not a string or an array, and not an integer, default to 150x9999.
			if ( ( is_int( $size ) || ( 0 < intval( $size ) ) ) && ! is_array( $size ) ) {
				$size = array( intval( $size ), intval( $size ) );
			} elseif ( ! is_string( $size ) && ! is_array( $size ) ) {
				$size = array( 100, 100 );
			}

			return get_the_post_thumbnail( intval( $id ), $size, array( 'class' => $class . ' avatar' ) );
		}

		$post_meta = $this->get_meta();

		if ( empty( $post_meta['email'] ) ) {
			return;
		}

		$email = $post_meta['email'];

		if ( ! is_email( $email ) ) {
			return;
		}

		return get_avatar( $email, $size, '', '', array( 'class' => $class ) );
	}
}
