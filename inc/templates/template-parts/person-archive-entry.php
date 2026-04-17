<?php
/**
 * Template part for displaying a person in the archive/directory listing.
 *
 * @package pitchfork-asulabs-helper
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'featured-person row' ); ?>>

	<div class="post-thumbnail col-lg-4">
		<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( array( 300, 300 ), array( 'class' => 'pure-img rounded-circle' ) );
		} else {
			echo '<img class="placeholder-img rounded-circle" src="' . esc_url( plugins_url( 'images/person-placeholder.svg', dirname( __DIR__, 2 ) . '/plugin-template.php' ) ) . '" alt="' . esc_attr( get_the_title() ) . '" />';
		}
		?>
	</div>

	<div class="content-wrap col-lg-8">
		<?php

			// Get person's full name from meta details
			$first  = get_post_meta( get_the_ID(), '_person_first_name', true ) ?: get_post_meta( get_the_ID(), 'person_first_name', true );
			$middle = get_post_meta( get_the_ID(), '_person_middle_name', true ) ?: get_post_meta( get_the_ID(), 'person_middle_name', true );
			$last   = get_post_meta( get_the_ID(), '_person_last_name', true ) ?: get_post_meta( get_the_ID(), 'person_last_name', true );
			$suffix = get_post_meta( get_the_ID(), '_person_suffix', true ) ?: get_post_meta( get_the_ID(), 'person_suffix', true );

			// Append spaces if not blank.
			if ( ! empty( $first ) ) {
				$first .= ' ';
			}

			if ( ! empty( $middle ) ) {
				$middle .= ' ';
			}

			echo '<h2 class="entry-title"><a class="person-name" href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . esc_html( $first . $middle . $last . ' ' . $suffix ) . '</a></h2>';

			$tagline = get_post_meta( get_the_ID(), '_person_tagline', true ) ?: get_post_meta( get_the_ID(), 'person_tagline', true );

			if ( empty( $tagline ) ) {
				// overwrite the tagline information with taxonomy information if it's blank.
				$tagline = strip_tags( get_the_term_list( get_the_ID(), 'faculty-type', '', ', ', '' ) );
			}

			echo '<p class="person-tagline">' . esc_html( $tagline ) . '</p>';
		?>

		<div class="description"><?php the_excerpt(); ?></div>

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
	</div>

</article><!-- #post-<?php the_ID(); ?> -->
