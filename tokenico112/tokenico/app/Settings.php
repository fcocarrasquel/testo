<?php

namespace BeycanPress\Tokenico;

use \CSF;
use \Beycan\EnvatoLicenseChecker;
use \BeycanPress\Tokenico\PluginHero\Plugin;

class Settings
{
    use PluginHero\Helpers;
    
    public function __construct()
    {
        $prefix = $this->settingKey;
        $parent = $this->pages->SalesList->slug;

        CSF::createOptions($prefix, array(

            'framework_title'         => esc_html__('Settings', 'tokenico') . ' <small>By BeycanPress</small>',

            // menu settings
            'menu_title'              => esc_html__('Settings', 'tokenico'),
            'menu_slug'               => $prefix,
            'menu_capability'         => 'manage_options',
            'menu_type'               => 'submenu',
            'menu_parent'             => $parent,
            'menu_position'           => null,
            'menu_hidden'             => false,

            // menu extras
            'show_bar_menu'           => false,
            'show_sub_menu'           => false,
            'show_network_menu'       => true,
            'show_in_customizer'      => false,

            'show_search'             => true,
            'show_reset_all'          => true,
            'show_reset_section'      => true,
            'show_footer'             => true,
            'show_all_options'        => true,
            'sticky_header'           => true,
            'save_defaults'           => true,
            'ajax_save'               => true,
            
            // database model
            'transient_time'          => 0,

            // contextual help
            'contextual_help'         => array(),

            // typography options
            'enqueue_webfont'         => false,
            'async_webfont'           => false,

            // others
            'output_css'              => false,

            // theme
            'theme'                   => 'dark',

            // external default values
            'defaults'                => array(),

        ));

        CSF::createSection($prefix, array(

            'id'     => 'general_options', 
            'title'  => esc_html__('General options', 'tokenico'),
            'icon'   => 'fa fa-cog',
            'fields' => array(
                array(
                    'id'      => 'dds',
                    'title'   => esc_html__('Data deletion status', 'tokenico'),
                    'type'    => 'switcher',
                    'default' => false,
                    'help'    => esc_html__('This setting is passive come by default. You enable this setting. All data created by the plug-in will be deleted while removing the plug-in.', 'tokenico')
                ),
                array(
                    'id' => 'infuraProjectId',
                    'type'  => 'text',
                    'title' => esc_html__('Infura Project ID', 'tokenico'),
                    'help'  => esc_html__('Please enter an infura project id for WalletConnect to work.', 'tokenico'),
                    'sanitize' => function($val) {
                        return sanitize_text_field($val);
                    }
                ),
            )
        ));

        CSF::createSection($prefix, array(
            'id'     => 'walletsMenu', 
            'title'  => esc_html__('Accepted wallets', 'tokenico'),
            'icon'   => 'fa fa-wallet',
            'fields' => array(
                array(
                    'id'     => 'acceptedWallets',
                    'type'   => 'fieldset',
                    'title'  => esc_html__('Wallets', 'tokenico'),
                    'help'   => esc_html__('Specify the wallets you want to accept payments from. (Only payment process)', 'tokenico'),
                    'fields' => array(
                        array(
                            'id'      => 'metamask',
                            'title'   => esc_html('MetaMask'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id'      => 'trustwallet',
                            'title'   => esc_html('Trust Wallet'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id'      => 'binancewallet',
                            'title'   => esc_html('Binance Wallet'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                        array(
                            'id'      => 'walletconnect',
                            'title'   => esc_html('WalletConnect'),
                            'type'    => 'switcher',
                            'default' => true,
                        ),
                    ),
                    'validate' => function($val) {
                        foreach ($val as $value) {
                            if ($value) {
                                break;
                            } else {
                                return esc_html__('You must activate at least one wallet!', 'tokenico');
                            }
                        }
                    }
                ),
            ) 
        ));

        CSF::createSection($prefix, array(
            'id'     => 'networks', 
            'title'  => esc_html__('Accepted networks', 'tokenico'),
            'icon'   => 'fa fa-link',
            'fields' => array(
                array(
                    'id'      => 'acceptedChains',
                    'title'   => esc_html__('Accepted networks', 'tokenico'),
                    'type'    => 'group',
                    'help'    => esc_html__('Add the blockchain networks you accept to receive payments.', 'tokenico'),
                    'button_title' => esc_html__('Add new', 'tokenico'),
                    'default' => [
                        [
                            'name' =>  'Main Ethereum Network',
                            'rpcUrl' =>  'https://mainnet.infura.io/v3/9aa3d95b3bc440fa88ea12eaa4456161',
                            'id' =>  1,
                            'explorerUrl' =>  'https://etherscan.io/',
                            'active' => true,
                            'nativeCurrency' => [
                                'symbol' =>  'ETH',
                                'decimals' =>  18,
                            ],
                        ],
                        [
                            'name' =>  'Binance Smart Chain',
                            'rpcUrl' =>  'https://bsc-dataseed.binance.org/',
                            'id' =>  56,
                            'explorerUrl' =>  'https://bscscan.com/',
                            'active' => true,
                            'nativeCurrency' => [
                                'symbol' =>  'BNB',
                                'decimals' =>  18,
                            ],
                        ],
                        [
                            'name' =>  'Avalanche Network',
                            'rpcUrl' =>  'https://api.avax.network/ext/bc/C/rpc',
                            'id' =>  43114,
                            'explorerUrl' =>  'https://cchain.explorer.avax.network/',
                            'active' => true,
                            'nativeCurrency' => [
                                'symbol' =>  'AVAX',
                                'decimals' =>  18,
                            ],
                        ],
                        [
                            'name' =>  'Polygon Mainnet',
                            'rpcUrl' =>  'https://rpc-mainnet.matic.network',
                            'id' =>  137,
                            'explorerUrl' =>  'https://polygonscan.com/',
                            'active' => true,
                            'nativeCurrency' => [
                                'symbol' =>  'MATIC',
                                'decimals' =>  18,
                            ]
                        ],
                        [
                            'name' =>  'Ethereum Rinkeby Testnet',
                            'rpcUrl' =>  'https://rinkeby.infura.io/v3/9aa3d95b3bc440fa88ea12eaa4456161',
                            'id' =>  4,
                            'explorerUrl' =>  'https://rinkeby.etherscan.io/',
                            'active' => false,
                            'nativeCurrency' => [
                                'symbol' =>  'ETH',
                                'decimals' =>  18,
                            ]
                        ],
                        [
                            'name' =>  'Binance Smart Chain Testnet',
                            'rpcUrl' =>  'https://data-seed-prebsc-1-s1.binance.org:8545/',
                            'id' =>  97,
                            'explorerUrl' =>  'https://testnet.bscscan.com/',
                            'active' => false,
                            'nativeCurrency' => [
                                'symbol' =>  'BNB',
                                'decimals' =>  18,
                            ]
                        ],
                        [
                            'name' =>  'Avalanche FUJI C-Chain Testnet',
                            'rpcUrl' =>  'https://api.avax-test.network/ext/bc/C/rpc',
                            'id' =>  43113,
                            'explorerUrl' =>  'https://cchain.explorer.avax-test.network',
                            'active' => false,
                            'nativeCurrency' => [
                                'symbol' =>  'AVAX',
                                'decimals' =>  18,
                            ]
                        ],
                        [
                            'name' =>  'Polygon Mumbai Testnet',
                            'rpcUrl' =>  'https://rpc-mumbai.maticvigil.com/',
                            'id' =>  80001,
                            'explorerUrl' =>  'https://mumbai.polygonscan.com',
                            'active' => false,
                            'nativeCurrency' => [
                                'symbol' =>  'MATIC',
                                'decimals' =>  18,
                            ]
                        ]
                    ],
                    'sanitize' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => &$value) {
                                $value['name'] = sanitize_text_field($value['name']);
                                $value['rpcUrl'] = sanitize_text_field($value['rpcUrl']);
                                $value['id'] = absint($value['id']);
                                $value['explorerUrl'] = sanitize_text_field($value['explorerUrl']);
                                $value['nativeCurrency']['symbol'] = strtoupper(sanitize_text_field($value['nativeCurrency']['symbol']));
                                $value['nativeCurrency']['decimals'] = absint($value['nativeCurrency']['decimals']);
                            }
                        }

                        return $val;
                    },
                    'validate' => function($val) {
                        if (is_array($val)) {
                            foreach ($val as $key => $value) {
                                if (empty($value['name'])) {
                                    return esc_html__('Network name cannot be empty.', 'tokenico');
                                } elseif (empty($value['rpcUrl'])) {
                                    return esc_html__('Network RPC URL cannot be empty.', 'tokenico');
                                } elseif (empty($value['id'])) {
                                    return esc_html__('Chain ID cannot be empty.', 'tokenico');
                                } elseif (empty($value['explorerUrl'])) {
                                    return esc_html__('Explorer URL cannot be empty.', 'tokenico');
                                } elseif (empty($value['nativeCurrency']['symbol'])) {
                                    return esc_html__('Native currency symbol cannot be empty.', 'tokenico');
                                } elseif (empty($value['nativeCurrency']['decimals'])) {
                                    return esc_html__('Native currency Decimals cannot be empty.', 'tokenico');
                                }
                            }
                        } else {
                            return esc_html__('You must add at least one blockchain network!', 'tokenico');
                        }
                    },
                    'fields'    => array(
                        array(
                            'title' => esc_html__('Network name', 'tokenico'),
                            'id'    => 'name',
                            'type'  => 'text'
                        ),
                        array(
                            'title' => esc_html__('Network RPC URL', 'tokenico'),
                            'id'    => 'rpcUrl',
                            'type'  => 'text',
                        ),
                        array(
                            'title' => esc_html__('Chain ID', 'tokenico'),
                            'id'    => 'id',
                            'type'  => 'number'
                        ),
                        array(
                            'title' => esc_html__('Explorer URL', 'tokenico'),
                            'id'    => 'explorerUrl',
                            'type'  => 'text'
                        ),
                        array(
                            'id'      => 'active',
                            'title'   => esc_html__('Active/Passive', 'tokenico'),
                            'type'    => 'switcher',
                            'help'    => esc_html__('Get paid in this network?', 'tokenico'),
                            'default' => true,
                        ),
                        array(
                            'id'     => 'nativeCurrency',
                            'type'   => 'fieldset',
                            'title'  => esc_html__('Native currency', 'tokenico'),
                            'fields' => array(
                                array(
                                    'id'    => 'symbol',
                                    'type'  => 'text',
                                    'title' => esc_html__('Symbol', 'tokenico')
                                ),
                                array(
                                    'id'    => 'decimals',
                                    'type'  => 'number',
                                    'title' => esc_html__('Decimals', 'tokenico')
                                ),
                            ),
                        )
                    ),
                ),
            ) 
        ));

        CSF::createSection($prefix, array(
            'id'     => 'license', 
            'title'  => esc_html__('License', 'tokenico'),
            'icon'   => 'fa fa-key',
            'fields' => array(
                array(
                    'id'    => 'license',
                    'type'  => 'text',
                    'title' => esc_html__('License (Purchase code)', 'tokenico'),
                    'sanitize' => function($val) {
                        return sanitize_text_field($val);
                    },
                    'validate' => function($val) {
                        $val = sanitize_text_field($val);
                        if (empty($val)) {
                            return esc_html__('License cannot be empty.', 'tokenico');
                        } elseif (strlen($val) < 36 || strlen($val) > 36) {
                            return esc_html__('License must consist of 36 characters.', 'tokenico');
                        }

                        if ($this->verifyLicense($val) !== true) {
                            return esc_html__('The license (purchase) code you entered for "Tokenico" is invalid.', 'tokenico');
                        }
                    }
                ),
            ) 
        ));

        CSF::createSection($prefix, array(
            'id'     => 'backup', 
            'title'  => esc_html__('Backup', 'tokenico'),
            'icon'   => 'fa fa-shield',
            'fields' => array(
                array(
                    'type'  => 'backup',
                    'title' => esc_html__('Backup', 'tokenico')
                ),
            ) 
        ));
    }

    public function verifyLicense($val) 
    {
		EnvatoLicenseChecker::setBearerToken('ByCEVCxTrZ4mJPtAS0tAJn1hucs8Koce');
        $licenseData = EnvatoLicenseChecker::check($val);
        if ($licenseData === false) {
            return false;
        } else {
            $tokenicol = strpos($licenseData->license, 'ular');
            update_option('tokenicol', $tokenicol !== false ? 'yes' : 'no');
            return true;
        }
    }

    public static function getAcceptedWallets() : array
    {
		$acceptedWallets = Plugin::$instance->setting('acceptedWallets');
		
		if (!$acceptedWallets) return [];
		
        $acceptedWallets = array_filter($acceptedWallets, function($val) {
            return $val;
        });

        return array_keys($acceptedWallets);
    }

    public static function getAcceptedChains() : array
    {
        $preparedChains = [];
        $acceptedChains = Plugin::$instance->setting('acceptedChains');

        if (!$acceptedChains) return $preparedChains;

        foreach ($acceptedChains as $key => $chain) {

            // Active/Passive control
            if ($chain['active'] != '1') continue;

            $id = intval($chain['id']);
            $hexId = '0x' . dechex($id);

            $chain['nativeCurrency']['symbol'] = $chain['nativeCurrency']['symbol'];
            $chain['nativeCurrency']['decimals'] =  $chain['nativeCurrency']['decimals'];

            $preparedChains[$hexId] = [
                'id' => $id,
                'hexId' => $hexId,
                'name' => $chain['name'],
                'rpcUrl' => $chain['rpcUrl'],
                'explorerUrl' => $chain['explorerUrl'],
                'nativeCurrency' => $chain['nativeCurrency']
            ];
        }
        
        return $preparedChains;
    }
}
