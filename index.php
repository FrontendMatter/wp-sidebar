<?php namespace Mosaicpro\WP\Plugins\Sidebar;

/*
Plugin Name: MP Sidebar
Plugin URI: http://mosaicpro.biz
Description: Create dynamic sidebars and widget areas through various methods, including shortcodes.
Version: 1.0
Author: MosaicPro
Author URI: http://mosaicpro.biz
Text Domain: mp-sidebar
*/

// If this file is called directly, exit.
if ( ! defined( 'WPINC' ) ) { die; }

use Mosaicpro\HtmlGenerators\Core\IoC;
use Mosaicpro\WpCore\Plugin;

// Plugin libraries
$libraries = [
    'Sidebar'
];

// load is_plugin_active helper from the admin
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Plugin initialization
add_action('plugins_loaded', function() use ($libraries)
{
    if (!class_exists('Mosaicpro\\HtmlGenerators\\Core\\IoC') || !class_exists('Mosaicpro\\WpCore\\PluginActivated'))
        return;

    // Get the Container from IoC
    $app = IoC::getContainer();

    // Bind the Plugin to the Container
    $app->bindShared('plugin', function()
    {
        return new Plugin( __FILE__ );
    });

    // Load libraries
    foreach ($libraries as $library)
        require_once dirname(__FILE__) . '/library/' . $library . '.php';

    // Initialize libraries
    foreach ($libraries as $library)
        forward_static_call_array([ __NAMESPACE__ . '\\' . $library, 'init' ], []);
}, 11);

// Plugin activation
register_activation_hook(__FILE__, function() use ($libraries)
{
    // Let the Plugin components know they are being executed in the Plugin activation hook
    defined('MP_PLUGIN_ACTIVATING') || define('MP_PLUGIN_ACTIVATING', true);

    foreach ($libraries as $library)
        require_once dirname(__FILE__) . '/library/' . $library . '.php';

    foreach ($libraries as $library)
        forward_static_call_array([ __NAMESPACE__ . '\\' . $library, 'activate' ], []);
});