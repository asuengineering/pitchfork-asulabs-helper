<?php
/**
 * - Enqueue plugin scripts.
 * - Register additional scripts to be loaded with individual blocks.
 *
 * @package pitchfork-plugin-template
 */

// Example function below will need to be edited depending on the context of the plugin.
// Possible hooks: enqueue_block_assets (both), enqueue_block_editor_assets
// Old school hooks: wp_enqueue_scripts, wp_enqueue_style

add_action( 'enqueue_block_assets', 'starter_plugin_enqueue_block_scripts' );
function starter_plugin_enqueue_block_scripts() {

		$the_plugin     = get_plugin_data( plugin_dir_path( __DIR__ ) . 'plugin-template.php' );
		$the_version    = $the_plugin['Version'];
		$plugin_version = $the_version . '.' . filemtime( plugin_dir_path( __DIR__ ) . 'dist/css/plugin.css' );

	wp_enqueue_style( 'pitchfork-plugin-template-style', plugin_dir_url( __DIR__ ) . 'dist/css/plugin.css', array(), $plugin_version);

}
