<?php

namespace BeycanPress\Tokenico\Services;

use \BeycanPress\Tokenico\Lang;
use \BeycanPress\Tokenico\Settings;
use \BeycanPress\Tokenico\Entity\Presale;
use \BeycanPress\Tokenico\PluginHero\Helpers;
use \BeycanPress\Tokenico\Services\Contract;

class PresaleList
{
    use Helpers;

    private $presales;

    public function initSc()
    {
        add_action('init', function(){
            add_shortcode('tokenico-presale-list', function() {
                $this->loadAssets();
                $presales = $this->getPresales();
                $chains = Settings::getAcceptedChains();
        
                return $this->view('presale/list', compact('chains', 'presales'));
            });
            
            add_shortcode('tokenico-presale', function($atts) {

                extract(shortcode_atts(array(
                    'id' => null
                ), $atts));

                if (!$id) {
                    return esc_html__('Not found id parameter!', 'tokenico');
                }

                $presale = new Presale($id);
                if ($presale->isAvailable()) {
                    if ($presale->post_status != 'publish' || $presale->post_type != 'presale') {
                        return esc_html__('Not found presale!', 'tokenico');
                    }
                } else {
                    return esc_html__('Not found presale!', 'tokenico');
                }

                $this->loadAssets($presale);
                $token = json_decode($presale->token);
                $status = $presale->getStatus();
        
                return $this->view('presale/content', compact('presale', 'token', 'status'));
            });
        });

        add_filter('the_content', function($content) {
            global $post;

            if ($post->post_type == 'presale') {
                $this->loadAssets();
                $presale = new Presale($post->ID);
                $token = json_decode($presale->token);
                $status = $presale->getStatus();
                if (isset($_GET['preview'])) {
                    $content .= esc_html__('Presales are not available for preview!', 'tokenico');
                } else {
                    $content .= $this->view('presale/content', compact('presale', 'token', 'status'));
                }
            }

            return $content;
        });
    }

    /**
     * @param integer $presaleId
     * @return Presale
     */
    protected function presaleInstance(int $presaleId) : Presale
    {
        return new Presale($presaleId);
    }

    /**
     * @return string
     */
    public function getItems() : string
    {
        return $this->view('presale/item', [
            'presales' => $this->presales
        ]);
    }

    /**
     * @param array $filter
     * @param integer $page
     * @return string
     */
    public function getPresales(array $filter = [], int $page = 1)
    {
        $args = [
            'post_type'      => 'presale',
            'post_status'    => 'publish',
            'order'          => 'DESC',
            'posts_per_page' => 9,
            'paged'          => $page,
            'meta_query'     => []
        ];

        if (isset($filter['status']) && $filter['status'] != 'all') {
            if ($filter['status'] == 'started') {
                $args['meta_query'] = [
                    'relation' => 'AND',
                    [
                        'key' => 'startDate',
                        'value' => date('Y-m-d H:i:s'),
                        'compare' => '<=',
                        'type' => 'datetime'
                    ],
                    [
                        'key' => 'endDate',
                        'value' => date('Y-m-d H:i:s'),
                        'compare' => '>=',
                        'type' => 'datetime'
                    ],
                    [
                        'key' => 'remainingLimit',
                        'value' => 0,
                        'compare' => '!='
                    ],
                ];
            } elseif ($filter['status'] == 'not-started') {
                $args['meta_query'] = [
                    'relation' => 'AND',
                    [
                        'key' => 'startDate',
                        'value' => date('Y-m-d H:i:s'),
                        'compare' => '>=',
                        'type' => 'datetime'
                    ],
                ];
            } elseif ($filter['status'] == 'ended') {
                $args['meta_query'] = [
                    'relation' => 'AND',
                    [
                        'key' => 'endDate',
                        'value' => date('Y-m-d H:i:s'),
                        'compare' => '<=',
                        'type' => 'datetime'
                    ]
                ];
            }
        }
        if (isset($filter['network']) && $filter['network'] != 'all') {
            $args['meta_query'] = array_merge([
                [
                    'key' => 'networkId',
                    'value' => $filter['network']
                ]
            ], $args['meta_query']);
        }

        $this->presales = new \WP_Query($args);
        return $this->presales;
    }

    /**
     * @return void
     */
    public function loadAssets(?Presale $presale = null)
    { 
        global $post;

        if (is_null($presale)) {
            $presale = is_single() ? new Presale($post->ID) : null;
        }

        $acceptedWallets = Settings::getAcceptedWallets();
        $contractAbi = $presale ? Contract::getAbi($presale->contract, $presale->contractVersion) : null;

        $this->addScript('js/multi-chain.min.js');
        
        $this->addScript('cryptopay/js/chunk-vendors.js');
        $this->addScript('cryptopay/js/app.js');
        $this->addStyle('cryptopay/css/app.css');

        $this->addScript('js/sweetalert2.js');
        $this->addStyle('css/bootstrap-grid.min.css');
        $this->addStyle('css/main.css');

        $key = $this->addScript('js/main.js', ['jquery']);
        wp_localize_script($key, 'Tokenico', [
            'mode' => 'presale',
            'lang' => Lang::get(),
            'apiUrl' => $this->api->getUrl(),
            'acceptedWallets' => $acceptedWallets,
            'infuraId' => $this->setting('infuraProjectId'),
            'imagesUrl' => $this->pluginUrl . 'assets/images/',
            'adminAddress' => $presale ? $presale->adminAddress : null,
            'contractAbi' => $contractAbi ? $contractAbi : null,
            'presale' => $presale ? [
                'id' => $presale->ID,
                'token' => json_decode($presale->token),
                'network' => json_decode($presale->network),
                'contractAddress' => $presale->contractAddress,
                'maxContribution' => (float) $presale->maxContribution,
                'minContribution' => (float) $presale->minContribution,
                'totalSaleLimit' => (float) $presale->totalSaleLimit,
                'exchangeRate' => (float) $presale->exchangeRate,
                'autoTransfer' => (bool) $presale->autoTransfer,
                'status' => $presale->getStatus()
            ] : null,
        ]);
    }
}