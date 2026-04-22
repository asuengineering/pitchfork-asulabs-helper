<?php
/**
 * Plugin Name:     ASU Labs - Migration Helper
 * Plugin URI:      https://github.com/asuengineering/pitchfork-asulabs-helper
 * Description:     Redefine custom post types associated with the ASU Labs theme so that they persist when the theme itself is deactivated.
 * Author:          ASU Engineering
 * Author URI:      https://engineering.asu.edu
 * Version:         0.1
 *
 * @package         asulabs-migration
 * Text Domain:     asulabs-migration
 *
 * GitHub Plugin URI: https://github.com/asuengineering/pitchfork-asulabs-helper
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Variable for root directory of this plugin.
define( 'ASULABS_MIGRATION_BASE_PATH', plugin_dir_path( __FILE__ ) );

// Function: Activate.
// Function: Deactivate.
// Function: Execute plugin.

// Enqueue scripts.
require_once ASULABS_MIGRATION_BASE_PATH . '/inc/enqueue-scripts.php';

// ACF configurations.
require_once ASULABS_MIGRATION_BASE_PATH . '/inc/custom-post-types.php';
require_once ASULABS_MIGRATION_BASE_PATH . '/inc/people-block-creation.php';
// require_once ASULABS_MIGRATION_BASE_PATH . '/inc/acf-register-blocks.php';
