<?php 
	/* Template Name: Formulario colaboradores */
	/**
 * The site's entry point.
 *
 * Loads the relevant template part,
 * the loop is executed (when needed) by the relevant template part.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();


while ( have_posts() ) : the_post();
	?>

<main <?php post_class( 'site-main' ); ?> role="main">
	<?php if ( apply_filters( 'hello_elementor_page_title', true ) ) : ?>
		<header class="page-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header>
	<?php endif; ?>
	<div class="page-content">
		<form name="contenidoColaboradores" id="contenidoColaboradores" method="post" action="">
			<p>
				<input type="text" name="tituloPost" placeholder="Escribe el titulo del post">
			</p>
			<p>
				<?php wp_editor( $post_obj->post_content, 
					'userpostcontent',
					 array('textarea_name'=>'contenido') 
					); ?>
			</p>
			<p>
				<input type="submit" value="Enviar Post">
			</p>

				<?php wp_nonce_field( contenidoColaboradores ) ?>
		</form>




		<?php the_content(); ?>
		<div class="post-tags">
			<?php the_tags( '<span class="tag-links">' . __( 'Tagged ', 'hello-elementor' ), null, '</span>' ); ?>
		</div>
		<?php wp_link_pages(); ?>
	</div>

	<?php comments_template(); ?>
</main>

	<?php
endwhile;

get_footer();


