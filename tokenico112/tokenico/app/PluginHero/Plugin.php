<?php 

namespace BeycanPress\Tokenico\PluginHero;

abstract class Plugin
{
    use Helpers;

    public static $instance;

    public static $properties;

    public static $settings = null;
    
    public function __construct(array $properties)
    {
        self::$instance = $this;

        self::$properties = (object) array_merge($properties, [
            'pluginUrl' => plugin_dir_url($properties['pluginFile']),
            'pluginDir' => plugin_dir_path($properties['pluginFile']),
            'viewDir'   => trailingslashit(plugin_dir_path($properties['pluginFile']) . 'views'),
            'phDir'     => trailingslashit(__DIR__)
        ]);
        
        $this->localization();

        if (is_admin()) {
            if (method_exists($this, 'adminProcess')) {
                $this->adminProcess();
            }
            if (method_exists($this, 'adminScripts')) {
                add_action('admin_enqueue_scripts', [$this, 'adminScripts']);
            }
        } else {
            if (method_exists($this, 'frontendProcess')) {
                $this->frontendProcess();
            }

            if (method_exists($this, 'frontendScripts')) {
                add_action('wp_enqueue_scripts', [$this, 'frontendScripts']);
            }
        }

        if (method_exists($this, 'activation')) {
            register_activation_hook($this->pluginFile, [get_called_class(), 'activation']);
        }

        if (method_exists($this, 'deactivation')) {
            register_deactivation_hook($this->pluginFile, [get_called_class(), 'deactivation']);
        }

        if (method_exists($this, 'uninstall')) {
            register_uninstall_hook($this->pluginFile, [get_called_class(), 'uninstall']);
        }
    }

    private function localization()
    {
        $languagesFolder = $this->pluginDir . 'languages';
        
        if ($this->textDomain) {
            if (!is_dir($languagesFolder)) {
                mkdir($languagesFolder);
            }
            add_action('init', function() use ($languagesFolder) {
                load_plugin_textdomain($this->textDomain, false, $languagesFolder);
            }, 8);
        } else {
            if (is_dir($languagesFolder)) {
                rmdir($languagesFolder);
            }
        }
    }
}