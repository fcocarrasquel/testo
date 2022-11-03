<?php 

namespace BeycanPress\Tokenico\PluginHero;

/**
 * Contains the commonly used ones for this plugin
 */
trait Helpers
{   
    public function __get($property)
    {
        if (is_null(Plugin::$properties)) return;
        return isset(Plugin::$properties->$property) ? Plugin::$properties->$property : null;
    }

    public function addPage(object $page)
    {
        if (is_null(Plugin::$properties)) return;
        $className = (new \ReflectionClass($page))->getShortName();
        if (!isset(Plugin::$properties->pages)) {
            Plugin::$properties->pages = (object) [];
        }
        Plugin::$properties->pages->$className = $page;
    }

    public function addApi(object $api)
    {
        if (is_null(Plugin::$properties)) return;
        $className = (new \ReflectionClass($api))->getShortName();
        if (!isset(Plugin::$properties->apis)) {
            Plugin::$properties->apis = (object) [];
        }
        Plugin::$properties->apis->$className = $api;
    }

    /**
     * @param string $viewName Directory name within the folder
     * @return void
     */
    public function view(string $viewName, array $args = [])
    {
        extract($args);
        ob_start();
        include $this->viewDir . $viewName . '.php';
        return ob_get_clean();
    }

    /**
     * @param string $viewName Directory name within the folder
     * @return void
     */
    public function viewEcho(string $viewName, array $args = [])
    {
        extract($args);
        ob_start();
        include $this->viewDir . $viewName . '.php';
        echo ob_get_clean();
    }

    /**
     * Easy use for get_option
     * @param string $setting
     * @return mixed
     */
    public function setting(string $setting = null)
    {
        if (is_null(Plugin::$settings)) {
            $settings = get_option($this->settingKey); 
            Plugin::$settings = $settings;
        } else {
            $settings = Plugin::$settings;
        }

        if (is_null($setting)) {
            return $settings;
        }

        if (isset($settings[$setting])) :
            return $settings[$setting];
        else :
            return null;
        endif;
    }

    /**
     * @param string $viewName Directory name within the folder
     * @return void
     */
    public function getTemplate(string $templateName, array $args = [])
    {
        extract($args);
        ob_start();
        include $this->phDir . 'Templates/' . $templateName . '.php';
        echo ob_get_clean();
    }

    /**
     * @param string $fileName php file name
     * @return string
     */
    public function getFilePath(string $fileName)
    {
        return $this->pluginDir . $fileName . '.php';
    }

    /**
     * @param string get image url in asset images folder
     * @return string
     */
    public function getImageUrl(string $imageName)
    {
        return $this->pluginUrl . 'assets/images/' . $imageName;
    }

    /**
     * @param string $type error, success more
     * @param string $notice notice to be given
     * @param bool $dismissible in-dismissible button show and hide
     * @return void
     */
    public function notice(string $notice, string $type = 'success', bool $dismissible = false)
    {
        $this->getTemplate('notice', [
            'type' => $type,
            'notice' => $notice,
            'dismissible' => $dismissible
        ]);
    }   

    /**
     * @param string $type error, success more
     * @param string $notice notice to be given
     * @param bool $dismissible in-dismissible button show and hide
     * @return void
     */
    public function adminNotice(string $notice, string $type = 'success', bool $dismissible = false)
    {
        add_action('admin_notices', function() use ($notice, $type, $dismissible) {
            $this->notice($notice, $type, $dismissible);
        });
    }   
    
    /**
     * Ajax action hooks
     * @param string $action ajax function name
     * @return void
     */
    public function ajaxAction(string $action)
    {
        add_action('wp_ajax_'.$action , [$this, $action]);
        add_action('wp_ajax_nopriv_'.$action , [$this, $action]);
    }

    /**
     * New nonce create method
     * @param string|null $externalKey
     * @return void
     */
    public function createNewNonce(?string $externalKey = null)
    {
        $key = $this->pluginKey . '_nonce' . $externalKey;
        return wp_create_nonce($key);
    }

    /**
     * Nonce control mehod
     * @param string|null $externalKey
     * @return void
     */
    public function checkNonce(?string $externalKey = null)
    {
        $key = $this->pluginKey . '_nonce' . $externalKey;
        check_ajax_referer($key, 'nonce');
    }

    /**
     * New nonce field create method
     * @param string|null $externalKey
     * @return void
     */
    public function createNewNonceField(?string $externalKey = null)
    {
        $key = $this->pluginKey . '_nonce' . $externalKey;
        wp_nonce_field($key, 'nonce');
    }

    /**
     * Nonce field control method
     * @param string|null $externalKey
     * @return void
     */
    public function checkNonceField(?string $externalKey = null)
    {
        $key = $this->pluginKey . '_nonce' . $externalKey;
        if (!isset($_POST['nonce'])) return false;
        return @wp_verify_nonce($_POST['nonce'], $key) ? true : false;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        $siteURL = explode('/', get_site_url());
        $requestURL = explode('/', $_SERVER['REQUEST_URI']);
        $currentURL = array_unique(array_merge($siteURL, $requestURL));
        return implode('/', $currentURL);
    }

    /**
     * @param string $date
     * @return string
     */
    public function dateToTimeAgo(string $date)
    {
        return human_time_diff(strtotime(wp_date('Y-m-d H:i:s')), strtotime($date));
    }

    /**
     * @param int|string|float $number
     * @param int $decimals
     * @return float
     */
    public function toFixed($number, int $decimals) {
        return floatval(number_format($number, $decimals, '.', ""));
    }

    /**
     * @param string $jsonString
     * @param bool $array
     * @return object|array
     */
    public function parseJson(string $jsonString, bool $array = false) {
        return json_decode(html_entity_decode(stripslashes($jsonString)), $array);
    }

    /**
     *
     * @param string $content
     * @return string
     */
    function catchShortcode(string $content)
    {
        global $shortcode_tags;
        $tagnames = array_keys($shortcode_tags);
        $tagregexp = join( '|', array_map('preg_quote', $tagnames) );
    
        // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcodes()
        $pattern = '(.?)\[('.$tagregexp.')\b(.*?)(?:(\/))?\](?:(.+?)\[\/\2\])?(.?)';

        return preg_replace_callback('/'.$pattern.'/s', 'do_shortcode_tag', $content);
    }
	
	/**
     * @param string $path
     * @param array $deps
     * @return string
     */
    public function addScript(string $path, array $deps = []) : string
    {
        $key = explode('/', $path);
        wp_enqueue_script(
            $key = $this->pluginKey . '-' . end($key),
            $this->pluginUrl . 'assets/' . $path,
            $deps,
            $this->pluginVersion,
            true
        );
        
        return $key;
    }

    /**
     * @param string $path
     * @param array $deps
     * @return string
     */
    public function addStyle(string $path, array $deps = []) : string
    {
        $key = explode('/', $path);
        wp_enqueue_style(
            $key = $this->pluginKey . '-' . end($key),
            $this->pluginUrl . 'assets/' . $path,
            $deps,
            $this->pluginVersion
        );
        
        return $key;
    }
    
}