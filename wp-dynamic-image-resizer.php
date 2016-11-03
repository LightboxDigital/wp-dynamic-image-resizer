<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://lightboxdigital.co.uk
 * @since             1.0.0
 * @package           Dynamic_Image_Resizer
 *
 * @wordpress-plugin
 * Plugin Name:       Dynamic Image Resizer
 * Plugin URI:        https://github.com/LightboxDigital/wp-dynamic-image-resizer
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Lightbox Digital
 * Author URI:        https://lightboxdigital.co.uk
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       dynamic-image-resizer
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// If the class already exists, abort.
if ( class_exists( 'Dynamic_Image_Resizer' ) ) {
	return;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dynamic-image-resizer.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_dynamic_image_resizer() {
	$plugin = new Dynamic_Image_Resizer();
	$plugin->run();
}

run_dynamic_image_resizer();
