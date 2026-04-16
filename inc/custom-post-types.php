<?php
/**
 * Custom post type definitions from ASU Labs theme
 * Also includes template_include definitions for each CPT.
 *
 * @package asulabs-migration
 */

/**
 * Template include paths.
 *
 * - Person CPT --> plugin templates (single-person.php, archive-person.php)
 * - Research CPT --> page.php
 */
add_filter( 'template_include', 'asulabs_person_use_plugin_template', 10 );
function asulabs_person_use_plugin_template( $template ) {

	if ( is_singular( 'person' ) ) {
		$plugin_template = plugin_dir_path( __FILE__ ) . 'templates/single-person.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}

	if ( is_post_type_archive( 'person' ) ) {
		$plugin_template = plugin_dir_path( __FILE__ ) . 'templates/archive-person.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}

	return $template;
}

add_filter( 'template_include', 'asulabs_research_use_page_template' );
function asulabs_research_use_page_template( $template ) {

    if ( is_singular( 'research' ) ) {
        $page_template = locate_template( 'page.php' );
        if ( $page_template ) {
            return $page_template;
        }
    }

    return $template;
}

/* Research CPT (research) */
add_action( 'init', 'asulabs_transition_make_cpt_research', 10 );
function asulabs_transition_make_cpt_research() {

	if ( post_type_exists( 'research' ) ) {
        do_action('qm/debug', 'Research CPT exists.');
		return;
    }

	$labels = array(
		'name'                  => _x( 'Research', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Research', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Research', 'text_domain' ),
		'name_admin_bar'        => __( 'Research', 'text_domain' ),
		'archives'              => __( 'Research Archives', 'text_domain' ),
		'attributes'            => __( 'Research Attributes', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'All Items', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'view_items'            => __( 'View Items', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args   = array(
		'label'               => __( 'Research', 'text_domain' ),
		'description'         => __( 'Research pages', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'excerpt', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'page-attributes' ),
		'taxonomies'          => array( 'research-theme' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 21,
		'menu_icon'           => 'dashicons-admin-page',
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'show_in_rest'        => true,
	);


	register_post_type( 'research', $args );

}

/** Person CPT (person) */
add_action( 'init', 'asulabs_transition_make_cpt_person', 10);
function asulabs_transition_make_cpt_person() {

    if ( post_type_exists( 'person' ) ) {
        return;
    }

    $labels = array(
        'name'                  => _x( 'People', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Person', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'People', 'text_domain' ),
        'name_admin_bar'        => __( 'Person', 'text_domain' ),
        'archives'              => __( 'People Directory', 'text_domain' ),
        'all_items'             => __( 'All People', 'text_domain' ),
        'add_new_item'          => __( 'Add New Person', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Person', 'text_domain' ),
        'edit_item'             => __( 'Edit Person', 'text_domain' ),
        'update_item'           => __( 'Update Person', 'text_domain' ),
        'view_item'             => __( 'View Person', 'text_domain' ),
        'view_items'            => __( 'View People', 'text_domain' ),
        'search_items'          => __( 'Search People', 'text_domain' ),
        'not_found'             => __( 'No people found', 'text_domain' ),
        'not_found_in_trash'    => __( 'No people found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Profile Photo', 'text_domain' ),
        'set_featured_image'    => __( 'Set profile photo', 'text_domain' ),
        'remove_featured_image' => __( 'Remove profile photo', 'text_domain' ),
        'use_featured_image'    => __( 'Use as profile photo', 'text_domain' ),
    );

    $args = array(
        'label'               => __( 'People', 'text_domain' ),
        'description'         => __( 'Faculty and student profiles', 'text_domain' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'page-attributes' ),
        'taxonomies'          => array( 'faculty-type' ),
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => 22,
        'menu_icon'           => 'dashicons-businessman',
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => 'directory',
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'rewrite'             => array( 'slug' => 'person' ),
        'show_in_rest'        => true,
    );

	register_post_type( 'person', $args );
}

// TAX: Faculty/Student Type
add_action( 'init', 'asulabs_transition_make_faculty_type_taxonomy', 10 );
function asulabs_transition_make_faculty_type_taxonomy() {

    $labels = array(
        'name'                       => _x( 'Faculty/Student Types', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Faculty/Student Type', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Faculty/Student Type', 'text_domain' ),
        'all_items'                  => __( 'All Types', 'text_domain' ),
        'parent_item'                => __( 'Parent Type', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Type:', 'text_domain' ),
        'new_item_name'              => __( 'New Type Name', 'text_domain' ),
        'add_new_item'               => __( 'Add New Type', 'text_domain' ),
        'edit_item'                  => __( 'Edit Type', 'text_domain' ),
        'update_item'                => __( 'Update Type', 'text_domain' ),
        'view_item'                  => __( 'View Type', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate types with commas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove types', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
        'popular_items'              => __( 'Popular Types', 'text_domain' ),
        'search_items'               => __( 'Search Types', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
        'no_terms'                   => __( 'No Types', 'text_domain' ),
        'items_list'                 => __( 'Types list', 'text_domain' ),
        'items_list_navigation'      => __( 'Types list navigation', 'text_domain' ),
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'     => false,
        'show_in_rest'      => true,
    );

    register_taxonomy( 'faculty-type', array( 'person' ), $args );
}

