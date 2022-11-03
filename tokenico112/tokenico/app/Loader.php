<?php

namespace BeycanPress\Tokenico;

class Loader extends PluginHero\Plugin
{
    public function __construct($pluginFile)
    {
        $api = new Api();

        parent::__construct([
            'pluginFile' => $pluginFile,
            'pluginKey' => 'tokenico',
            'textDomain' => 'tokenico',
            'settingKey' => 'tokenicoSettings',
            'pluginVersion' => '1.1.2',
            'api' => $api
        ]);

        if ($this->setting('license')) {
            new PostType\Presale();
        } else {
            $this->adminNotice(esc_html__('In order to use the "Tokenico" Plugin, please enter your license (purchase) code in the license field in the settings section.', 'tokenico'), 'error');
            delete_option('tokenicoFlushRewrite');
        }

    }

    public function adminProcess()
    {
        new Pages\SalesList();

        if (in_array('walletconnect', Settings::getAcceptedWallets()) && $this->setting('infuraProjectId') == '') {
            $this->adminNotice(esc_html__('Please enter an infura project id for WalletConnect to work. - TokenICO', 'cryptopay'), 'error');
        }

        require_once $this->pluginDir . 'includes/csf/csf.php';
        add_action('init', function(){
            new Settings();
        }, 9);
    }

    public function frontEndProcess()
    {
        if ($this->setting('license')) {
            (new Services\PresaleList())->initSc();
        }
    }

    public static function activation()
    {
        (new Models\Sale())->create();
    }

    public static function deactivation()
    {
        delete_option('tokenicoFlushRewrite');
    }

    public static function uninstall()
    {
        $settings = get_option(self::$instance->settingKey);
        if (isset($settings['dds']) && $settings['dds']) {
            delete_option(self::$instance->settingKey);
            (new Models\Sale())->drop();
        }
    }
}
