<?php
/**
 * The template for displaying single person pages.
 *
 * @package pitchfork-asulabs-helper
 */

get_header();
?>
	<div id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			// Use plugin-relative path for template part
			$template_part = plugin_dir_path( __DIR__ ) . 'templates/template-parts/person-full.php';
			if ( file_exists( $template_part ) ) {
				include $template_part;
			}

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php

get_footer();
