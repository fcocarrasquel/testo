<?php

namespace BeycanPress\Tokenico;

use \Beycan\Response;
use \BeycanPress\Tokenico\Models\Sale;
use \BeycanPress\Tokenico\Entity\Presale;

class Api extends PluginHero\Api
{
    /**
     * @var object
     */
    private $order;

    public function __construct()
    {
        $this->addRoutes([
            'tokenico-api' => [
                'get-presales' => [
                    'callback' => 'getPresales',
                    'methods' => ['GET']
                ],
                'filter-presales' => [
                    'callback' => 'filterPresales',
                    'methods' => ['GET']
                ],
                'pre-purchase-check' => [
                    'callback' => 'prePurchaseCheck',
                    'methods' => ['POST']
                ],
                'pre-claim-check' => [
                    'callback' => 'preClaimCheck',
                    'methods' => ['POST']
                ],
                'claim-successful' => [
                    'callback' => 'claimSuccessful',
                    'methods' => ['POST']
                ],
                'save-sale' => [
                    'callback' => 'saveSale',
                    'methods' => ['POST']
                ],
                'get-dates' => [
                    'callback' => 'getDates',
                    'methods' => ['GET']
                ]
            ]
        ]);
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function getPresales($request)
    {
        $page = absint($request->get_param('page'));
        $filter = array_map('sanitize_text_field', $request->get_param('filter'));

        $presaleList = new Services\PresaleList();
        $presales = $presaleList->getPresales($filter, $page);

        Response::success(null, $presaleList->getItems());
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function filterPresales($request)
    {
        $filter = array_map('sanitize_text_field', $request->get_param('filter'));

        $presaleList = new Services\PresaleList();
        $presales = $presaleList->getPresales($filter);

        Response::success(null, [
            'content' => $presaleList->getItems(),
            'maxPage' => $presales->max_num_pages
        ]);
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function saveSale($request)
    {
        $paymentInfo = $this->validatePaymentInfo($request->get_param('paymentInfo'));
        $presale = new Presale($paymentInfo->presaleId);
        
        $presale->setMeta('totalSales', $presale->totalSales + $paymentInfo->paymentPrice);
        $presale->setMeta('remainingLimit', $presale->totalSaleLimit - $presale->totalSales);

        (new Sale())->insert([
            'transactionId' => $paymentInfo->transactionId,
            'presaleId' => $paymentInfo->presaleId,
            'receiverAddress' => $paymentInfo->receiverAddress,
            'quantityPurchased' => ($paymentInfo->paymentPrice * $presale->exchangeRate),
            'purchaseAmount' => $paymentInfo->paymentPrice,
            'sent' => $presale->autoTransfer,
            'paymentInfo' => serialize($paymentInfo)
        ]);

        Response::success();
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function prePurchaseCheck($request)
    {
        $paymentInfo = $this->validatePaymentInfo($request->get_param('paymentInfo'));
        $this->validatePurchase($paymentInfo);

        Response::success();
    }

    /**
     * @param string $paymentInfo
     * @return object
     */
    public function validatePaymentInfo($paymentInfo)
    {
        $paymentInfo = !is_null($paymentInfo) ? $this->parseJson($paymentInfo) : false;
        
        if (!$paymentInfo || !isset($paymentInfo->presaleId)) {
            Response::error(esc_html__('Please enter a valid data.', 'tokenico'));
        }

        $presale = get_post($paymentInfo->presaleId);
        if (!$presale || $presale->post_type != 'presale') {
            Response::error(esc_html__('No such presale was found!', 'tokenico'));
        }

        return $paymentInfo;
    }

    /**
     *
     * @param object $paymentInfo
     * @return void
     */
    public function validatePurchase(object $paymentInfo) : void
    {
        $presale = new Presale($paymentInfo->presaleId);

        if ($presale->getStatus() == 'notStarted') {
            Response::error(esc_html__('Presale not started', 'tokenico'), [
                'redirect' => 'reload'
            ]);
        } elseif ($presale->getStatus() == 'ended') {
            Response::error(esc_html__('Presale ended', 'tokenico'), [
                'redirect' => 'reload'
            ]);
        }

        if ($presale->totalSales == $presale->totalSaleLimit) {
            Response::error(esc_html__('This presale has reached the total sales limit!', 'tokenico'), [
                'redirect' => 'reload'
            ]);
        }

        $paymentPrice = (float) strval($paymentInfo->paymentPrice);
        $remainingLimit = ($presale->totalSaleLimit - $presale->totalSales);
        if ($paymentPrice > $remainingLimit) {
            Response::error(esc_html__('Your amount exceeds the remaining sale limit!', 'tokenico'));
        }

        $purchaseAmount = (new Sale())->getPurchaseAmount($paymentInfo->receiverAddress, $paymentInfo->presaleId);

        if ($paymentPrice < $presale->minContribution && !$purchaseAmount) {
            Response::error(esc_html__('You are exceeding the minimum purchase limit!', 'tokenico'));
        }

        if ($purchaseAmount) {
            $userLimit = (float) strval($presale->maxContribution - $purchaseAmount);
            if ($paymentPrice > $userLimit) {
                Response::error(sprintf(esc_html__('The purchase amount exceeds the maximum participation rate. Currently, you can purchase a maximum of %s %s amount of.', 'tokenico'), $userLimit, $paymentInfo->usedChain->nativeCurrency->symbol));
            }
        } else {
            if ($paymentPrice > $presale->maxContribution) {
                Response::error(esc_html__('You are exceeding the maximum purchase limit!', 'tokenico'));
            }
        }

    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function claimSuccessful($request)
    {
        $presaleId = $request->get_param('presaleId');
        $receiverAddress = $request->get_param('receiverAddress');

        $this->validateClaim($presaleId, $receiverAddress);

        (new Sale())->update([
            'sent' => true
        ], [
            'receiverAddress' => $receiverAddress,
            'presaleId' => $presaleId,
            'sent' => false
        ]);

        Response::success();
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function preClaimCheck($request)
    {
        $presaleId = $request->get_param('presaleId');
        $receiverAddress = $request->get_param('receiverAddress');

        $this->validateClaim($presaleId, $receiverAddress);
        $purchaseAmount = (new Sale())->getQuantityPurchased($receiverAddress, $presaleId);

        Response::success(null, compact('purchaseAmount'));
    }

    /**
     * @param integer $presaleId
     * @param string $receiverAddress
     * @return void
     */
    public function validateClaim(int $presaleId, string $receiverAddress) : void
    {
        if (!$receiverAddress) {
            Response::error(esc_html__('Please enter a address!', 'tokenico'));
        }

        if (!$presaleId) {
            Response::error(esc_html__('Please enter a valid data.', 'tokenico'));
        }

        $presale = get_post($presaleId);
        if (!$presale || $presale->post_type != 'presale') {
            Response::error(esc_html__('No such presale was found!', 'tokenico'));
        }

        $sale = (new Sale())->findBy([
            'receiverAddress' => $receiverAddress,
            'presaleId' => $presaleId,
            'sent' => false
        ]);

        if (!$sale) {
            Response::error(esc_html__('You haven\'t bought any tokens! Or all your purchased tokens have been sent!', 'tokenico'));
        }
    }

    /**
     * @param WP_REST_Request $request
     * @return void
     */
    public function getDates($request)
    {
        $startDate = strtotime($request->get_param('startDate'));
        $endDate = strtotime($request->get_param('endDate'));

        Response::success(null, compact('startDate', 'endDate'));
    }
}
