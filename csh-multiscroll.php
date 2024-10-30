<?php

/**
 *
 * @link              http://demo.cmssuperheroes.com
 * @since             1.0.0
 * @package           Csh_Multiscroll
 *
 * @wordpress-plugin
 * Plugin Name:       Csh Multiscroll
 * Plugin URI:        http://demo.cmssuperheroes.com/csh-plugins/csh-multiscroll
 * Description:       Support multi-scroll slide type for theme
 * Version:           1.0.0
 * Author:            Tony
 * Author URI:        http://demo.cmssuperheroes.com
 * License:           themeforest.net
 * License URI:       themeforest.net/licenses
 * Text Domain:       cshmultiscroll
 * Domain Path:       /languages
 */

// If this file is called directly, abort.

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'CSHMS_PLUGIN_VERSION', '1.0.0' );

define( 'CSHMS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CSHMS_PLUGIN_URL', plugins_url("", __FILE__) );

define( 'CSHMS_PLUGIN_ADMIN_DIR', CSHMS_PLUGIN_DIR . "/admin/" );
define( 'CSHMS_PLUGIN_ADMIN_URL', CSHMS_PLUGIN_URL . "/admin/" );

define( 'CSHMS_PLUGIN_ASSETS_DIR', CSHMS_PLUGIN_DIR . "/assets/" );
define( 'CSHMS_PLUGIN_ASSETS_URL', CSHMS_PLUGIN_URL . "/assets/" );

define( 'CSHMS_PLUGIN_INCLUDES_DIR', CSHMS_PLUGIN_DIR . "/includes/" );
define( 'CSHMS_PLUGIN_INCLUDES_URL', CSHMS_PLUGIN_URL . "/includes/" );

define( 'CSHMS_PLUGIN_TEMPLATES_DIR', CSHMS_PLUGIN_DIR . "/templates/" );
define( 'CSHMS_PLUGIN_TEMPLATES_URL', CSHMS_PLUGIN_URL . "/templates/" );

define( 'CSHMS_PLUGIN_PUBLIC_DIR', CSHMS_PLUGIN_DIR . "/public/" );
define( 'CSHMS_PLUGIN_PUBLIC_URL', CSHMS_PLUGIN_URL . "/public/" );

/* Return csh-multiscroll options data */
$cshms_options = get_option( 'csh-multiscroll' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

require CSHMS_PLUGIN_INCLUDES_DIR . 'class-csh-multiscroll.php';
require CSHMS_PLUGIN_INCLUDES_DIR . 'csh-multiscroll-utils.php';

add_action( 'plugins_loaded', 'cshms_load_textdomain' );
function cshms_load_textdomain() {
    $language_folder = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
    load_plugin_textdomain( 'cshmultiscroll', false, $language_folder);
}

register_activation_hook( __FILE__, 'cshms_install' );
register_deactivation_hook( __FILE__, 'cshms_uninstall' );








