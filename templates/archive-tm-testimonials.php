<?php
/**
 * The archive index page for CPT Tesimonials.
 *
 * @package    Cherry_Testi
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

global $wp_query;

if ( ! did_action( 'get_header' ) ) {
	get_header(); ?>

	<?php do_action( 'tm_testimonials_content_before' ); ?>

		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
<?php } ?>

<?php $args = apply_filters( 'tm_testimonials_archive_template_args', array(
	'limit'          => TM_Testimonials_Page_Template::$posts_per_page,
	'category'       => ! empty( $wp_query->query_vars['term'] ) ? $wp_query->query_vars['term'] : '',
	'content_length' => 55,
	'pager'          => 'true',
	'template'       => 'default.tmpl',
	'custom_class'   => 'tm-testi-page tm-testi-page--archive',
) );
$data = TM_Testimonials_Data::get_instance(); ?>

<?php do_action( 'tm_testiminials_entry_before' ); ?>

<?php $data->the_testimonials( $args ); ?>

<?php do_action( 'tm_testiminials_entry_after' ); ?>

<?php if ( did_action( 'tm_testimonials_content_before' ) ) { ?>
			</main><!-- .site-main -->
		</div><!-- .content-area -->

	<?php do_action( 'tm_testiminials_content_after' ); ?>

	<?php get_sidebar(); ?>

	<?php get_footer();
} ?>
