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
	// $template_part = plugin_dir_path( __DIR__ ) . 'template-parts/person-featured-header.php';
	// if ( file_exists( $template_part ) ) {
	// 	include $template_part;
	// }
	?>

		<div class="display-person row gx-4 gy-4 align-items-start">

			<div class="post-thumbnail col-lg-4 person-image">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( array( 400, 400 ), array( 'class' => 'img-fluid rounded-circle' ) );
				} else {
					echo '<img src="' . esc_url( plugins_url( 'images/person-placeholder.svg', dirname( __DIR__, 2 ) . '/plugin-template.php' ) ) . '" alt="' . esc_attr( get_the_title() ) . '" class="img-fluid rounded-circle" />';
				}
				?>
			</div><!-- .post-thumbnail -->

			<div class="post-content col-lg-8">
				<header class="person-header">

					<?php
					$first  = get_post_meta( get_the_ID(), '_person_first_name', true ) ?: get_post_meta( get_the_ID(), 'person_first_name', true );
					$middle = get_post_meta( get_the_ID(), '_person_middle_name', true ) ?: get_post_meta( get_the_ID(), 'person_middle_name', true );
					$last   = get_post_meta( get_the_ID(), '_person_last_name', true ) ?: get_post_meta( get_the_ID(), 'person_last_name', true );
					$suffix = get_post_meta( get_the_ID(), '_person_suffix', true ) ?: get_post_meta( get_the_ID(), 'person_suffix', true );

					if ( ! empty( $first ) ) {
						$first .= ' ';
					}
					if ( ! empty( $middle ) ) {
						$middle .= ' ';
					}

					echo '<h1 class="person-name">' . esc_html( $first . $middle . $last . ' ' . $suffix ) . '</h1>';

					$tagline = get_post_meta( get_the_ID(), '_person_tagline', true ) ?: get_post_meta( get_the_ID(), 'person_tagline', true );
					if ( empty( $tagline ) ) {
						$tagline = strip_tags( get_the_term_list( get_the_ID(), 'faculty-type', '', ', ', '' ) );
					}
					echo '<p class="lead person-tagline">' . esc_html( $tagline ) . '</p>';
					?>

					<ul class="contact-details">
					<?php
						$email    = get_post_meta( get_the_ID(), '_person_email', true ) ?: get_post_meta( get_the_ID(), 'person_email', true );
						$phone    = get_post_meta( get_the_ID(), '_person_phone', true ) ?: get_post_meta( get_the_ID(), 'person_phone', true );
						$location = get_post_meta( get_the_ID(), '_person_location', true ) ?: get_post_meta( get_the_ID(), 'person_location', true );
						$maplink  = get_post_meta( get_the_ID(), '_person_location_url', true ) ?: get_post_meta( get_the_ID(), 'person_location_url', true );

					if ( ( ! empty( $email ) ) && ( is_email( $email ) ) ) {
						echo '<li class="email"><a href="mailto:' . esc_attr( $email ) . '" target="_blank">' . esc_html( $email ) . '</a></li>';
					}

					if ( ! empty( $phone ) ) {
						echo '<li class="phone"><a href="tel:' . esc_attr( $phone ) . '" target="_blank">' . esc_html( $phone ) . '</a></li>';
					}

					if ( ! empty( $location ) ) {
						echo '<li class="office-location">';
						if ( ( ! empty( $maplink ) ) && ( wp_http_validate_url( $maplink ) ) ) {
							echo '<a href="' . esc_url( $maplink ) . '" target="_blank" title="Office Location: Link to ASU Maps">' . esc_html( $location ) . '</a>';
						} else {
							echo esc_html( $location );
						}
						echo '</li>';
					}
					?>
					</ul>
				</header><!-- .entry-header -->

				<?php
				// Social icons if there are any present
				$isearch   = get_post_meta( get_the_ID(), '_person_isearch', true ) ?: get_post_meta( get_the_ID(), 'person_isearch', true );
				$social_fb = get_post_meta( get_the_ID(), '_person_facebook', true ) ?: get_post_meta( get_the_ID(), 'person_facebook', true );
				$social_tw = get_post_meta( get_the_ID(), '_person_twitter', true ) ?: get_post_meta( get_the_ID(), 'person_twitter', true );
				$social_li = get_post_meta( get_the_ID(), '_person_linkedin', true ) ?: get_post_meta( get_the_ID(), 'person_linkedin', true );
				$social_ig = get_post_meta( get_the_ID(), '_person_instagram', true ) ?: get_post_meta( get_the_ID(), 'person_instagram', true );

				if ( ( ! empty( $isearch ) ) && ( wp_http_validate_url( $isearch ) ) ) {
					echo '<div class="profile-links">';
					echo '<a class="isearch-url btn btn-md btn-gray" href="' . esc_url( $isearch ) . '" target="_blank" title="iSearch Profile link"><span class="fas fa-link"></span> URL</a>';

					if ( ! empty( $social_fb ) ) {
						echo '<a class="btn btn-md btn-gray" href="' . esc_url( $social_fb ) . '" target="_blank" title="Facebook"><span class="fab fa-facebook-square"></span> Facebook</a>';
					}

					if ( ! empty( $social_tw ) ) {
						echo '<a class="btn btn-md btn-gray" href="' . esc_url( $social_tw ) . '" target="_blank" title="X"><span class="fab fa-x"></span> X</a>';
					}

					if ( ! empty( $social_li ) ) {
						echo '<a class="btn btn-md btn-gray" href="' . esc_url( $social_li ) . '" target="_blank" title="LinkedIn"><span class="fab fa-linkedin-in"></span> LinkedIn</a>';
					}

					if ( ! empty( $social_ig ) ) {
						echo '<a class="btn btn-md btn-gray" href="' . esc_url( $social_ig ) . '" target="_blank" title="Instagram"><span class="fab fa-instagram"></span> Instagram</a>';
					}

					echo '</div><!-- .profile-links -->';
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
		</div><!-- .post-content -->
	</div><!-- .display-person -->
</article><!-- #post-<?php the_ID(); ?> -->
