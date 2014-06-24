<?php namespace Mosaicpro\WP\Plugins\Sidebar;

use Mosaicpro\WpCore\PluginGeneric;

/**
 * Class Sidebar
 * @package Mosaicpro\WP\Plugins\Sidebar
 */
class Sidebar extends PluginGeneric
{
    /**
     * Holds a Sidebar instance
     * @var
     */
    protected static $instance;

    /**
     * Initialize the plugin
     */
    public static function init()
    {
        $instance = self::getInstance();

        // i18n
        $instance->loadTextDomain();

        // Load Plugin Templates into the current Theme
        $instance->plugin->initPluginTemplates();

        // Initialize Sidebar Admin
        $instance->initAdmin();

        // Register Sidebars
        $instance->registerSidebars();

        // Initialize Attachments Shortcodes
        $instance->initShortcodes();
    }

    /**
     * Get a Singleton instance of Sidebar
     * @return static
     */
    public static function getInstance()
    {
        if (is_null(self::$instance))
        {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Initialize Admin only resources
     * @return bool
     */
    private function initAdmin()
    {
        if (!is_admin()) return false;

        add_action('save_post', function($id)
        {
            $post_content = isset($_POST['post_content']) ? $_POST['post_content'] : false;
            if (!$post_content) return false;

            if (stripos($post_content, '[register_sidebar') === false)
                return false;

            // reset sidebars
            // delete_option('mp_sidebars');

            preg_replace_callback("/\[register_sidebar ([^\[]*)/s", function($m) use ($id)
                {
                    $attr = substr($m[1], 0, -1);
                    $attr = stripslashes($attr);
                    $sidebar = shortcode_parse_atts( $attr );

                    if (!isset($sidebar['id'])) return false;

                    // make sure sidebar id is unique
                    $sidebar_id = 'mp-sidebar-post-' . $id . '-' . $sidebar['id'];

                    // default sidebar name
                    $sidebar_name = ucwords(str_replace(['-', '_'], ' ', $sidebar_id));

                    // custom sidebar name
                    if (isset($sidebar['name'])) $sidebar_name = $sidebar['name'];
                    if (is_numeric($sidebar_name)) $sidebar_name = 'Sidebar ' . $sidebar_name;
                    $sidebar_name = '(Post ' . $id . ') ' . $sidebar_name;

                    // get existing sidebars
                    $sidebars = get_option('mp_sidebars', []);

                    // not a dupe
                    $dupe = array_where($sidebars, function($key, $value) use ($sidebar_id)
                    {
                        return $value['id'] == $sidebar_id;
                    });

                    if (count($dupe) == 0)
                    {
                        // add sidebar
                        $sidebars[] = ['id' => $sidebar_id, 'name' => $sidebar_name];
                    }
                    else
                    {
                        // update sidebar name
                        foreach($sidebars as $k => $sb)
                        {
                            if ($sb['id'] == $sidebar_id)
                            {
                                $sidebars[$k]['name'] = $sidebar_name;
                                break;
                            }
                        }
                    }

                    update_option('mp_sidebars', $sidebars);
                },
                $post_content);
        });
    }

    /**
     * Register Sidebars
     */
    private function registerSidebars()
    {
        add_action('widgets_init', function()
        {
            $sidebars = get_option('mp_sidebars', []);
            foreach($sidebars as $sidebar)
            {
                register_sidebar([
                    'id'            => $sidebar['id'],
                    'name'          => $sidebar['name'],
                    'description'   => $this->__('Widget area created dynamically.'),
                    'before_widget' => '<div id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</div>',
                    'before_title'  => '<h4 class="widgettitle">',
                    'after_title'   => '</h4>',
                ]);
            }
        });
    }

    /**
     * Initialize Sidebar Shortcodes
     */
    private function initShortcodes()
    {
        add_action('init', function()
        {
            $shortcodes = [
                'Register_Sidebar'
            ];

            foreach ($shortcodes as $sc)
            {
                require_once realpath(__DIR__) . '/Shortcodes/' . $sc . '.php';
                forward_static_call([__NAMESPACE__ . '\\' . $sc . '_Shortcode', 'init']);
            }
        });
    }
}