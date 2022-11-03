<?php 

namespace BeycanPress\Tokenico\Pages;

use Beycan\WPTable\Table;
use BeycanPress\Tokenico\Models\Sale;
use BeycanPress\Tokenico\Entity\Presale;
use BeycanPress\Tokenico\PluginHero\Page;

/**
 * Sales list page
 */
class SalesList extends Page
{   
    /**
     * Class construct
     * @return void
     */
    public function __construct()
    {
        parent::__construct([
            'pageName' => esc_html__('TokenICO', 'tokenico'),
            'subMenuPageName' => esc_html__('Sales list', 'tokenico'),
            'subMenu' => true
        ]);
    }

    /**
     * @return void
     */
    public function page()
    {
        $sale = new Sale();

        // Pagination
        $limit = 10;
        $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $offset = (($paged - 1) * $limit);

        $orderQuery = ['createdAt', 'desc'];

        if (isset($_GET['id']) && $sale->delete(['id' => absint($_GET['id'])])) {
            $this->notice(esc_html__('Successfully deleted!', 'tokenico'), 'success', true);
        }

        if (isset($_GET['orderby'])) {
            $orderBy = sanitize_text_field($_GET['orderby']);
            $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'desc';
            $orderQuery = [$orderBy, $order];
        }

        if (isset($_GET['s']) && !empty($_GET['s'])) {
            $s = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null;
            $sales = $sale->search($s);
            $salesCount = $sale->searchCount($s);
        }

        if (!isset($sales)) {
            $sales = $sale->findBy([], $orderQuery, $limit, $offset);
            $salesCount = $sale->getCount();
        }

        $table = new Table(
            [
                'transactionId'     => esc_html__('Transaction id', 'tokenico'),
                'presaleId'         => esc_html__('Presale id', 'tokenico'),
                'network'           => esc_html__('Network', 'tokenico'),
                'usedWallet'        => esc_html__('Used wallet', 'tokenico'),
                'receiverAddress'   => esc_html__('Receiver address', 'tokenico'),
                'quantityPurchased' => esc_html__('Quantity purchased', 'tokenico'),
                'purchaseAmount'    => esc_html__('Purchase amount', 'tokenico'),
                'sent'              => esc_html__('Sent', 'tokenico'),
                'createdAt'         => esc_html__('Created at', 'tokenico'),
                'delete'            => esc_html__('Delete', 'tokenico')
            ],
            $sales
        );

        // Pagination
        $table->setTotalRow($salesCount);

        $table->setOptions([
            'search' => [
                'id' => 'search-box',
                'title' => esc_html__('Search...', 'tokenico')
            ]
        ]);

        $table->addHooks([
            'usedWallet' => function($sale) {
                $paymentInfo = unserialize($sale->paymentInfo);
                return isset($paymentInfo->usedWallet) ? esc_html($paymentInfo->usedWallet) : null;
            },
            'network' => function($sale) {
                $presale = new Presale($sale->presaleId);
                return $presale->network ? esc_html(json_decode($presale->network)->name) : null;
            },
            'transactionId' => function($sale) {
                $paymentInfo = unserialize($sale->paymentInfo);
                $explorerUrl = rtrim($paymentInfo->usedChain->explorerUrl, '/');
                $url = $explorerUrl.'/tx/'.$sale->transactionId;
                return '<a href="'.esc_url($url).'" target="_blank">'.esc_html($sale->transactionId).'</a>';
            },
            'receiverAddress' => function($sale) {
                $paymentInfo = unserialize($sale->paymentInfo);
                $explorerUrl = rtrim($paymentInfo->usedChain->explorerUrl, '/');
                $url = $explorerUrl.'/address/'.$paymentInfo->receiverAddress;
                return '<a href="'.esc_url($url).'" target="_blank">'.esc_html($paymentInfo->receiverAddress).'</a>';
            },
            'quantityPurchased' => function($sale) {
                $paymentInfo = unserialize($sale->paymentInfo);
                return esc_html($sale->quantityPurchased . " " . $paymentInfo->token->symbol);
            },
            'purchaseAmount' => function($sale) {
                $paymentInfo = unserialize($sale->paymentInfo);
                return esc_html($sale->purchaseAmount . " " . $paymentInfo->usedChain->nativeCurrency->symbol);
            },
            'sent' => function($sale) {
                if ($sale->sent) {
                    return esc_html__('Sent', 'tokenico');
                } else {
                    return esc_html__('No sent', 'tokenico');
                }
            },
            'delete' => function($sale) {
                if (!$sale->sent) return;
                return '<a class="button" href="'.$this->getCurrentUrl() . '&id=' . $sale->id.'">'.esc_html__('Delete', 'tokenico').'</a>';
            }
        ]);

        $table->setSortableColumns([
            'createdAt'
        ]);

        $this->viewEcho('pages/sales-list', [
            'table' => $table
        ]);
    }
}