<?php
/**
 * Create an options page that can produce a acf/person-manual block for each entrry in the CPT
 *
 * @package asulabs-migration
 */



/**
 * Admin options page to trigger import (button).
 */
add_action( 'admin_menu', function() {
    add_options_page(
        'ASU Labs People Import',
        'ASU People Import',
        'manage_options',
        'asulabs-people-import',
        'asulabs_people_import_options_page'
    );
} );

/**
 * Render options page.
 */
function asulabs_people_import_options_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Process a POST (button click) with nonce check
    if ( isset( $_POST['asulabs_people_import_nonce'] ) ) {
        if ( wp_verify_nonce( wp_unslash( $_POST['asulabs_people_import_nonce'] ), 'asulabs_people_import_action' ) ) {
            $result = asulabs_people_import_create_draft_page();
            if ( is_wp_error( $result ) ) {
                echo '<div class="notice notice-error"><p>' . esc_html( $result->get_error_message() ) . '</p></div>';
            } else {
                $url = esc_url( get_edit_post_link( $result ) );
                echo '<div class="notice notice-success"><p>Draft created with ID ' . intval( $result ) . '. <a href="' . $url . '">Open draft in editor</a>.</p></div>';
            }
        } else {
            echo '<div class="notice notice-error"><p>Security check failed.</p></div>';
        }
    }

    // Simple UI
    ?>
    <div class="wrap">
        <h1>ASU Labs → Pitchfork: People Import</h1>
        <p>This tool will create a <strong>draft page</strong> containing one <code>acf/profile-manual</code> block per <code>people</code> post in the database. Use this to build a directory from blocks and then delete/move the blocks as desired.</p>

        <form method="post">
            <?php wp_nonce_field( 'asulabs_people_import_action', 'asulabs_people_import_nonce' ); ?>
            <p>
                <button type="submit" class="button button-primary">Create Draft Page with People Blocks</button>
            </p>
        </form>

        <h2>Notes</h2>
        <ul>
            <li>The plugin will not show the <code>people</code> post type in any admin menus.</li>
            <li>Images and media referenced by ID will remain available (same DB); if you're migrating across sites, migrate media first.</li>
            <li>The block attributes in the draft are minimal: <code>person_id</code>, <code>person_slug</code>, <code>image_id</code>, and <code>term_slugs</code>. Adjust mapping inside <code>asulabs_people_import_create_draft_page()</code> if your block requires different fields.</li>
        </ul>
    </div>
    <?php
}

/**
 * Import people and create a draft page with acf/profile-manual blocks
 * populated into ACF fields. At the bottom of the page, append a visible
 * "Debug information" background-section with paragraphs describing the import.
 *
 * Uses these ACF fields (names):
 *   uds_profilemanual_name       => full_name
 *   uds_profilemanual_title      => person_tagline (only if present)
 *   uds_profilemanual_email      => person_email
 *   uds_profilemanual_image      => featured image ID
 *   uds_profilemanual_department => comma-separated faculty-type term slugs/names
 */
function asulabs_people_import_create_draft_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'forbidden', 'Insufficient permissions.' );
    }

    // canonical mapping: ACF field name => source key (derived / carbon meta)
    $acf_field_map = array(
        'uds_profilemanual_name'       => 'full_name',
        'uds_profilemanual_title'      => 'title',
        'uds_profilemanual_email'      => 'email',
        'uds_profilemanual_image'      => 'image_id',
        'uds_profilemanual_department' => 'faculty_type_string',
    );

    // helper: try to resolve an ACF field key from name (returns 'field_xxx' or false)
    $resolve_acf_field_key = function( $name ) {
        if ( strpos( $name, 'field_' ) === 0 ) {
            return $name;
        }
        if ( function_exists( 'acf_get_field' ) ) {
            $f = acf_get_field( $name );
            if ( $f && ! empty( $f['key'] ) ) {
                return $f['key'];
            }
        }
        if ( function_exists( 'get_field_object' ) ) {
            $f = get_field_object( $name );
            if ( $f && ! empty( $f['key'] ) ) {
                return $f['key'];
            }
        }
        return false;
    };

    // helper: fetch person meta trying underscore-prefixed and non-prefixed keys
    $get_person_meta = function( $post_id, $meta_key ) {
        $underscored = '_' . ltrim( $meta_key, '_' );
        $val = get_post_meta( $post_id, $underscored, true );
        if ( $val !== '' && $val !== null ) {
            return $val;
        }
        return get_post_meta( $post_id, $meta_key, true );
    };

    // Query all 'person' posts
    $people = get_posts( array(
        'post_type'      => 'person',
        'posts_per_page' => -1,
        'post_status'    => array( 'publish', 'private', 'draft' ),
    ) );

    if ( empty( $people ) ) {
        return new WP_Error( 'no_persons', 'No person posts found to import.' );
    }

    $blocks_content   = '';
    $debug_paragraphs = array(); // will contain HTML strings for per-person debug paragraphs

    foreach ( $people as $p ) {
        $person_id   = (int) $p->ID;
        $person_slug = $p->post_name ?: sanitize_title( $p->post_title );
        $image_id    = (int) get_post_thumbnail_id( $person_id );

        // Carbon Fields meta keys - read underscore-prefixed or non-prefixed
        $first_name  = $get_person_meta( $person_id, 'person_first_name' );
        $middle_name = $get_person_meta( $person_id, 'person_middle_name' );
        $last_name   = $get_person_meta( $person_id, 'person_last_name' );
        $suffix      = $get_person_meta( $person_id, 'person_suffix' );

        $tagline     = $get_person_meta( $person_id, 'person_tagline' ); // no fallback to post_title for tagline
        $email       = $get_person_meta( $person_id, 'person_email' );

        // Collect faculty-type terms (slugs). If present, compile to a comma-separated string.
        $faculty_type_slugs = array();
        $faculty_type_names = array();
        if ( taxonomy_exists( 'faculty-type' ) ) {
            $ft_terms = wp_get_post_terms( $person_id, 'faculty-type' );
            if ( ! is_wp_error( $ft_terms ) && ! empty( $ft_terms ) ) {
                foreach ( $ft_terms as $t ) {
                    if ( is_object( $t ) ) {
                        $faculty_type_slugs[] = $t->slug;
                        $faculty_type_names[] = $t->name;
                    } elseif ( is_string( $t ) ) {
                        $faculty_type_slugs[] = $t;
                        $faculty_type_names[] = $t;
                    }
                }
            }
        }
        // Prefer human-readable names in the string; fallback to slugs if names missing
        $faculty_type_string = '';
        if ( ! empty( $faculty_type_names ) ) {
            $faculty_type_string = implode( ', ', $faculty_type_names );
        } elseif ( ! empty( $faculty_type_slugs ) ) {
            $faculty_type_string = implode( ', ', $faculty_type_slugs );
        }
        // If still empty, keep as empty string (field will be omitted)

        // Build full name (First Middle Last, Suffix if present)
        $name_parts = array();
        if ( $first_name ) { $name_parts[] = $first_name; }
        if ( $middle_name ) { $name_parts[] = $middle_name; }
        if ( $last_name ) { $name_parts[] = $last_name; }
        $full_name = trim( implode( ' ', $name_parts ) );
        if ( $suffix ) {
            if ( $full_name !== '' ) {
                $full_name .= ', ' . $suffix;
            } else {
                $full_name = $suffix;
            }
        }
        // If still empty, fall back to post_title just for name
        if ( empty( $full_name ) ) {
            $full_name = $p->post_title;
        }

        // Block title only set if tagline exists (no fallback to post title per your request)
        $block_title = $tagline ? $tagline : null;

        // Build a source-values map
        $source = array(
            'full_name'            => $full_name,
            'title'                => $block_title,
            'email'                => $email,
            'image_id'             => $image_id,
            'post_title'           => $p->post_title,
            'person_slug'          => $person_slug,
            'faculty_type_slugs'   => $faculty_type_slugs,
            'faculty_type_names'   => $faculty_type_names,
            'faculty_type_string'  => $faculty_type_string,
        );

        // Build the ACF "data" object for this block using the canonical map
        $data = array();
        $field_key_resolution_map = array(); // record resolved key or fallback used for debug

        foreach ( $acf_field_map as $acf_field_name => $source_key ) {
            // If source doesn't exist or is empty, skip setting that field (no blank overwrites)
            if ( ! isset( $source[ $source_key ] ) || $source[ $source_key ] === '' || $source[ $source_key ] === null ) {
                $field_key_resolution_map[ $acf_field_name ] = array(
                    'used' => false,
                    'resolved_key' => null,
                    'value' => null,
                );
                continue;
            }

            $value = $source[ $source_key ];

            // Convert arrays to comma-separated strings if necessary (we want a string for department)
            if ( is_array( $value ) ) {
                $value = implode( ', ', $value );
            }

            // try to resolve to a field key (field_...) if ACF is present
            $resolved_key = $resolve_acf_field_key( $acf_field_name );
            if ( $resolved_key ) {
                $data[ $resolved_key ] = $value;
                $field_key_resolution_map[ $acf_field_name ] = array(
                    'used' => true,
                    'resolved_key' => $resolved_key,
                    'value' => $value,
                );
            } else {
                // fallback to using the field name itself in the data payload
                $data[ $acf_field_name ] = $value;
                $field_key_resolution_map[ $acf_field_name ] = array(
                    'used' => true,
                    'resolved_key' => $acf_field_name,
                    'value' => $value,
                );
            }
        }

        // Compose the ACF profile-manual block payload (vertical style)
        $block_payload = array(
            'name'      => 'acf/profile-manual',
            'data'      => $data,
            'mode'      => 'preview',
            'className' => 'is-style-vertical',
        );

        $payload_json = wp_json_encode( $block_payload );

        // Optional heading for readability in the editor
        $blocks_content .= "<!-- wp:heading --><h2>" . esc_html( $p->post_title ) . "</h2><!-- /wp:heading -->\n\n";

        // Append the ACF profile block (self-closing) with the correct payload
        $blocks_content .= "<!-- wp:acf/profile-manual {$payload_json} /-->\n\n";

        // Build a debug paragraph for this person (HTML string)
        $debug_lines = array();
        $debug_lines[] = "<strong>Person ID:</strong> " . esc_html( $person_id );
        $debug_lines[] = "<strong>Post title:</strong> " . esc_html( $p->post_title );
        $debug_lines[] = "<strong>Person slug:</strong> " . esc_html( $person_slug );
        $debug_lines[] = "<strong>Featured image ID:</strong> " . esc_html( $image_id );
        $debug_lines[] = "<strong>Derived full name:</strong> " . esc_html( $full_name );
        $debug_lines[] = "<strong>Tagline (person_tagline):</strong> " . ( $tagline ? esc_html( $tagline ) : '<em>(none)</em>' );
        $debug_lines[] = "<strong>Email (person_email):</strong> " . ( $email ? esc_html( $email ) : '<em>(none)</em>' );
        $debug_lines[] = "<strong>Faculty-type terms (string):</strong> " . ( $faculty_type_string ? esc_html( $faculty_type_string ) : '<em>(none)</em>' );

        $debug_lines[] = "<strong>Resolved ACF fields:</strong>";
        foreach ( $field_key_resolution_map as $fname => $info ) {
            $used = $info['used'] ? 'yes' : 'no';
            $key  = $info['resolved_key'] ? esc_html( $info['resolved_key'] ) : '<em>(none)</em>';
            $val  = isset( $info['value'] ) && $info['value'] !== '' && $info['value'] !== null ? esc_html( $info['value'] ) : '<em>(none)</em>';
            $debug_lines[] = "- " . esc_html( $fname ) . ": used=" . esc_html( $used ) . "; key=" . $key . "; value=" . $val;
        }

        // join into a single paragraph (use <br> for line breaks)
        $debug_paragraphs[] = '<p>' . implode( '<br>', $debug_lines ) . '</p>';
    }

    // After all profile blocks, append the debug background-section containing heading + paragraphs
    // Build inner content: heading block + paragraph blocks (one per person)
    $debug_inner = '';

    // Heading
    $debug_inner .= "<!-- wp:heading --><h2 class=\"wp-block-heading\">Debug information</h2><!-- /wp:heading -->\n\n";

    // Paragraph blocks for each collected debug paragraph
    foreach ( $debug_paragraphs as $para_html ) {
        // Insert as a paragraph block; $para_html already contains <p>..</p>
        // We'll embed the raw HTML inside the paragraph block container
        // so that the editor displays it as a standard paragraph.
        // To avoid double-wrapping, strip outer <p> tags when nesting in block comment.
        $inner_text = preg_replace( '#^<p>(.*)</p>$#s', '$1', $para_html );
        $debug_inner .= "<!-- wp:paragraph --><p>{$inner_text}</p><!-- /wp:paragraph -->\n\n";
    }

    // Compose the outer ACF background-section block wrapper.
    // Use the provided example payload with field_603819ea114a9 => "none"
    $bg_payload = array(
        'name' => 'acf/background-section',
        'data' => array( 'field_603819ea114a9' => 'none' ),
        'mode' => 'preview',
        'backgroundColor' => 'gray-2',
    );

    $bg_payload_json = wp_json_encode( $bg_payload );

    // Wrap debug_inner with the opening and closing ACF background-section comment tags
    $blocks_content .= "<!-- wp:acf/background-section {$bg_payload_json} -->\n";
    $blocks_content .= $debug_inner;
    $blocks_content .= "<!-- /wp:acf/background-section -->\n\n";

    // Insert a new draft page
    $post_args = array(
        'post_title'   => 'People Import (Draft) - ' . date_i18n( 'Y-m-d H:i:s' ),
        'post_status'  => 'draft',
        'post_type'    => 'page',
        'post_content' => $blocks_content,
    );

    $new_post_id = wp_insert_post( $post_args, true );

    if ( is_wp_error( $new_post_id ) ) {
        return $new_post_id;
    }

    // store mapping for traceability
    $mapping = wp_list_pluck( $people, 'ID' );
    update_post_meta( $new_post_id, 'asulabs_people_imported_ids', $mapping );

    return (int) $new_post_id;
}
