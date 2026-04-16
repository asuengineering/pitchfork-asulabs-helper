<?php
/**
 * Template part for displaying a full profile of a person (from the person CPT).
 *
 * @package pitchfork-asulabs-helper
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php
	// Include the person featured header template
	$template_part = plugin_dir_path( __DIR__ ) . 'template-parts/person-featured-header.php';
	if ( file_exists( $template_part ) ) {
		include $template_part;
	}
	?>

	<div class="entry-content">

		<?php
		the_content(
			sprintf(
				wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'asufaculty' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);
		?>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
