<?php
/**
 * Plugin Name:     Pitchfork Plugin Template
 * Plugin URI:      https://github.com/asuengineering
 * Description:     (text)
 * Author:          ASU Engineering
 * Author URI:      https://engineering.asu.edu
 * Version:         0.0.1
 *
 * @package         starter_plugin
 * Text Domain:     (text)
 *
 * GitHub Plugin URI: https://github.com/asuengineering
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Variable for root directory of this plugin.
define( 'STARTER_PLUGIN_BASE_PATH', plugin_dir_path( __FILE__ ) );

// Function: Activate.
// Function: Deactivate.
// Function: Execute plugin.

// Enqueue scripts.
require_once STARTER_PLUGIN_BASE_PATH . '/inc/enqueue-scripts.php';

// ACF configurations.
require_once STARTER_PLUGIN_BASE_PATH . '/inc/acf-config.php';
require_once STARTER_PLUGIN_BASE_PATH . '/inc/acf-register-blocks.php';
