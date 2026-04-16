<?php
/**
 * Template part for displaying a fancy header for a person within the website.
 *
 * @package pitchfork-asulabs-helper
 */
?>

<header class="entry-header">

	<div class="display-person row gx-4 gy-4 align-items-start">

		<div class="post-thumbnail col-lg-4 col-md-5 person-image">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( array( 400, 400 ), array( 'class' => 'img-fluid rounded-circle' ) );
			} else {
				echo '<img src="' . esc_url( plugins_url( 'images/person-placeholder.svg', dirname( __DIR__, 2 ) . '/plugin-template.php' ) ) . '" alt="' . esc_attr( get_the_title() ) . '" class="img-fluid rounded-circle" />';
			}
			?>
		</div><!-- .post-thumbnail -->

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

		echo '<div class="profile-metadata col-lg-8 col-md-7">';
		echo '<div class="entry-title">';
		echo '<h1 class="person-name">' . esc_html( $first . $middle . $last . ' ' . $suffix ) . '</h1>';

		$tagline = get_post_meta( get_the_ID(), '_person_tagline', true ) ?: get_post_meta( get_the_ID(), 'person_tagline', true );
		if ( empty( $tagline ) ) {
			$tagline = strip_tags( get_the_term_list( get_the_ID(), 'faculty-type', '', ', ', '' ) );
		}
		echo '<p class="lead person-tagline">' . esc_html( $tagline ) . '</p>';
		echo '</div>';
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

		</div><!-- .profile-metadata -->

	</div><!-- .display-person -->

</header><!-- .entry-header -->

<?php
	$isearch   = get_post_meta( get_the_ID(), '_person_isearch', true ) ?: get_post_meta( get_the_ID(), 'person_isearch', true );
	$social_fb = get_post_meta( get_the_ID(), '_person_facebook', true ) ?: get_post_meta( get_the_ID(), 'person_facebook', true );
	$social_tw = get_post_meta( get_the_ID(), '_person_twitter', true ) ?: get_post_meta( get_the_ID(), 'person_twitter', true );
	$social_li = get_post_meta( get_the_ID(), '_person_linkedin', true ) ?: get_post_meta( get_the_ID(), 'person_linkedin', true );
	$social_ig = get_post_meta( get_the_ID(), '_person_instagram', true ) ?: get_post_meta( get_the_ID(), 'person_instagram', true );

if ( ( ! empty( $isearch ) ) && ( wp_http_validate_url( $isearch ) ) ) {
	echo '<div class="isearch-profile"><div class="container">';
	echo '<a class="isearch-url" href="' . esc_url( $isearch ) . '" target="_blank" title="iSearch Profile link">' . esc_html( $isearch ) . '</a>';
	echo '<span class="social">';

	if ( ! empty( $social_fb ) ) {
		echo '<a class="icon" href="' . esc_url( $social_fb ) . '" target="_blank" title="Facebook"><i class="fab fa-facebook-square"></i></a>';
	}

	if ( ! empty( $social_tw ) ) {
		echo '<a class="icon" href="' . esc_url( $social_tw ) . '" target="_blank" title="Twitter"><i class="fab fa-twitter-square"></i></a>';
	}

	if ( ! empty( $social_li ) ) {
		echo '<a class="icon" href="' . esc_url( $social_li ) . '" target="_blank" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>';
	}

	if ( ! empty( $social_ig ) ) {
		echo '<a class="icon" href="' . esc_url( $social_ig ) . '" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>';
	}

	echo '</span>';
	echo '</div></div>';
}
?>
