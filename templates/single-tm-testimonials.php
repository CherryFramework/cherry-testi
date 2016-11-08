<?php
/**
 * The Template for displaying single CPT Testimonials.
 *
 * @package    Cherry_Testi
 * @subpackage Templates
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2002-2016, Template Monster
 */

if ( ! did_action( 'get_header' ) ) {
	get_header(); ?>

	<?php do_action( 'tm_testimonials_content_before' ); ?>

		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php } ?>

<?php $args = apply_filters( 'tm_testimonials_single_template_args', array(
	'ids'            => get_the_ID(),
	'echo'           => false,
	'content_length' => -1,
	'template'       => 'default.tmpl',
	'custom_class'   => 'tm-testi-page tm-testi-page--single',
) );

// Validate `echo` param.
$args['echo'] = false;

$data = TM_Testimonials_Data::get_instance();
$item = $data->the_testimonials( $args ); ?>

<?php do_action( 'tm_testiminials_entry_before' ); ?>

<?php while ( have_posts() ) : the_post();
	echo $item;
endwhile; ?>

<?php do_action( 'tm_testiminials_entry_after' ); ?>

<?php if ( did_action( 'tm_testimonials_content_before' ) ) { ?>
				</article>
			</main><!-- .site-main -->
		</div><!-- .content-area -->

	<?php do_action( 'tm_testiminials_content_after' ); ?>

	<?php get_sidebar(); ?>

	<?php get_footer();
} ?>
