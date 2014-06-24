<?php namespace Mosaicpro\WP\Plugins\Sidebar;

use Mosaicpro\WpCore\Shortcode;

/**
 * Class Register_Sidebar_Shortcode
 * @package Mosaicpro\WP\Plugins\Sidebar
 */
class Register_Sidebar_Shortcode extends Shortcode
{
    /**
     * Holds a Register_Sidebar_Shortcode instance
     * @var
     */
    protected static $instance;

    /**
     * Add the Shortcode to WP
     */
    public function addShortcode()
    {
        add_shortcode('register_sidebar', function($atts)
        {
            global $post;

            $atts = shortcode_atts( [
                'id' => false
            ], $atts );

            extract($atts);

            if (!$id) return '';

            ob_start();
            dynamic_sidebar('mp-sidebar-post-' . $post->ID . '-' . $id);
            $sidebar = ob_get_clean();

            return $sidebar;
        });
    }
}