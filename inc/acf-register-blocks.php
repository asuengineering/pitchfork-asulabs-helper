<?php
/**
 * File: acf-register-blocks.php
 *
 * @package         starter_plugin
 *
 * This file is responsible for loading all of our block 'register.php' files
 * (found in the individual block folders) so that we can register our custom
 * blocks. We do this by hooking into ACF Pro's 'acf/init' action.
 */

/**
 * Register a custom block category for our blocks to live in. We hook into
 * the block_categories_all() filter to do this.
 */

if ( ! function_exists( 'pitchfork_plugin_template_custom_category' ) ) {
	/**
	 * Merges our custom category in with the others.
	 *
	 * @param array                   $categories The existing block categories.
	 * @param WP_Block_Editor_Context $editor_context Editor context.
	 */
	function pitchfork_people_custom_category( $categories, $editor_context ) {
		return array_merge(
			$categories,
			array(
				array(
					'slug'  => 'pitchfork-plugin-template',
					'title' => __( 'Pitchfork plugin', 'pitchfork-plugin-template' ),
				),
			)
		);
	}
}
// add_filter( 'block_categories_all', 'pitchfork_plugin_template_custom_category', 10, 2 );

/**
 * Register blocks.
 */

function pitchfork_plugin_template_acf_blocks_init() {

	// Icons kept in a separate file.
	require_once STARTER_PLUGIN_BASE_PATH . '/acf-block-templates/icons.php';

	// UDS Profiles, container block for directories.
	register_block_type(
		STARTER_PLUGIN_BASE_PATH . 'acf-block-templates/profiles',
		array(
			'icon'     => $block_icon->users_rectangle,
			'category' => 'pitchfork-plugin-template',
		)
	);
}
// add_action( 'acf/init', 'pitchfork_plugin_template_acf_blocks_init' );
