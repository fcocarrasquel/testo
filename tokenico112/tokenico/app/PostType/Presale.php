<?php

namespace BeycanPress\Tokenico\PostType;

use \BeycanPress\Tokenico\Api;
use \BeycanPress\Tokenico\Lang;
use \BeycanPress\Tokenico\Settings;
use \BeycanPress\Tokenico\Entity\Presale as PresaleEntity;
use \BeycanPress\Tokenico\PluginHero\Helpers;
use \BeycanPress\Tokenico\Services\Contract;

class Presale
{
    use Helpers;

    /**
     * @var string|null
     */
    private $presale = null;

    public function __construct()
    {
        global $pagenow;

        add_action('init', [$this, 'init']);

        if (is_admin()) {
            add_action('init', function() {
                new Metabox();
            }, 9);
    
            $postId = isset($_GET['post']) ? $_GET['post'] : null;
            if (!is_array($postId)) {
                $this->presale = new PresaleEntity($postId);
                if (isset($_GET['post_type']) && $_GET['post_type'] != 'presale') {
                    return;
                } elseif (!isset($_GET['post_type']) && $this->presale->post_type != 'presale') {
                    return;
                }
        
                if ($pagenow == 'post.php' || $pagenow == 'post-new.php') {
                    $this->loadAssets();
                }
            }
        }
    }

    public function init()
    {
        register_post_type('presale',
            array(
                'labels' => array(
                    'name'               => esc_html__('Presales', 'tokenico'),
                    'singular_name'      => esc_html__('Presale', 'tokenico'),
                    'add_new'            => esc_html__('Add new', 'tokenico'),
                    'add_new_item'       => esc_html__('Add new presale', 'tokenico'),
                    'edit_item'          => esc_html__('Edit presale', 'tokenico'),
                    'search_items'       => esc_html__('Search presale', 'tokenico'),
                    'not_found'          => esc_html__('No presale found', 'tokenico'),
                    'not_found_in_trash' => esc_html__('No presale found in Trash', 'tokenico'),
                ),
                'public'              => true,
                'publicly_queryable'  => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'query_var'           => true,
                'exclude_from_search' => false,
                'capability_type'     => 'post',
                'rewrite'             => ['slug' => 'presale'],
                'supports'            => ['thumbnail', 'title', 'editor']
            )
        );

        if (get_option('tokenicoFlushRewrite') != 1) {
            flush_rewrite_rules(false);
            update_option('tokenicoFlushRewrite', 1);
        }

        add_filter('manage_presale_posts_columns', [$this, 'columns']);
        add_action('manage_presale_posts_custom_column', [$this, 'column'], 10, 2);
    }

    private function loadAssets()
    {
        add_action('admin_enqueue_scripts', function() {
            $this->addScript('js/multi-chain.min.js');
            $this->addScript('js/sweetalert2.js');
            $this->addStyle('css/admin.css');
            $key = $this->addScript('js/admin.js', ['jquery']);
            wp_localize_script($key, 'Tokenico', [
                'acceptedChains' => Settings::getAcceptedChains(),
                'apiUrl' => $this->api->getUrl(),
                'presaleStatus' => $this->presale->post_status,
                'versions' => Contract::getVersions(),
                'factories' => Contract::getFactories(),
                'tokenicol' => get_option('tokenicol'),
                'lang' => Lang::get()
            ]);
        });
    }

    public function columns($columns)
    {
        unset($columns['date']);
        
        $columns['shortcode'] = esc_html__('Shortcode', 'tokenico');
        $columns['token'] = esc_html__('Token', 'tokenico');
        $columns['network'] = esc_html__('Network', 'tokenico');
        $columns['contract'] = esc_html__('Contract', 'tokenico');
        $columns['totalSales'] = esc_html__('Total sales', 'tokenico');
        $columns['remainingLimit'] = esc_html__('Remaining limit', 'tokenico');
        $columns['status'] = esc_html__('Status', 'tokenico');

        $columns['date'] = esc_html__('Date');

        return $columns;
    }

    public function column($column, $presaleId)
    {

        $presale = new PresaleEntity($presaleId);
        
        if (!$presale->network) {
            echo null;
        } else {
            $network = json_decode($presale->network);
            $token = json_decode($presale->token);
            if ($column == 'shortcode') {
                echo esc_html('[tokenico-presale id="'.$presale->ID.'"]');
            } elseif ($column == 'token') {
                echo esc_html($token->name);
            } elseif ($column == 'network') {
                echo esc_html($network->name);
            } elseif ($column == 'contract') {
                echo esc_html($presale->contract ? $presale->contract : 'TokenICO');
            } elseif ($column == 'totalSales') {
                echo esc_html($presale->getTotalSales());
            } elseif ($column == 'remainingLimit') {
                echo esc_html($presale->getRemainingLimit());
            } elseif ($column == 'status') {
                $status = $presale->getStatus();
                if ($status == 'started') {
                    echo esc_html__('Presale started', 'tokenico');    
                } elseif ($status == 'ended') {
                    echo esc_html__('Presale ended', 'tokenico');   
                } else {
                    echo esc_html__('Presale not started', 'tokenico'); 
                }
            }
        }
    }
}