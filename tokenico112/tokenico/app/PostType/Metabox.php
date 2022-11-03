<?php

namespace BeycanPress\Tokenico\PostType;

use \CSF;
use \BeycanPress\Tokenico\Entity\Presale;
use \BeycanPress\Tokenico\PluginHero\Helpers;

class Metabox
{
    use Helpers;

    public function __construct()
    {
        $postId = isset($_GET['post']) ? $_GET['post'] : null;
        if (!is_array($postId)) {
            $presale = new Presale($postId);
            
            $presaleData = 'presaleData';
            CSF::createMetabox($presaleData, array(
                'title'     => esc_html__('Presale options', 'tokenico'),
                'post_type' => 'presale',
                'data_type' => 'unserialize',
            ));

            $network = json_decode($presale->network);
            CSF::createSection($presaleData, array(
                'fields' => array(
                    array(
                        'id'    => 'importantNote',
                        'type'  => 'content',
                        'title' => esc_html__('Important note :', 'tokenico'),
                        'class' => 'important-note-content',
                        'content' => sprintf(esc_html__('The token you want to sell must be in the wallet where you deploy the contract, equal to the total amount you will sell. Example You will sell  worth 10 COINs. You will give 10 TOKENs for 1 COIN. That is, you must have a minimum of 100 TOKENs in your wallet. %s Also, please check that your server time is correct according to the time zone. If not true. Although the pre-sale starts on your site, it will not start on the blockchain network. This produces an unexpected error message!', 'tokenico'), '<br><br>')
                    ),
                    array(
                        'id'    => 'totalSales',
                        'type'  => 'number',
                        'title' => esc_html__('Total sales :', 'tokenico'),
                        'default' => 0
                    ),
                    array(
                        'id'    => 'remainingLimit',
                        'type'  => 'number',
                        'title' => esc_html__('Remaining limit :', 'tokenico'),
                        'default' => 0
                    ),
                    array(
                        'id'    => 'token',
                        'type'  => 'text',
                        'title' => esc_html__('Token :', 'tokenico')
                    ),
                    array(
                        'id'    => 'networkId',
                        'type'  => 'text',
                        'title' => esc_html__('Network Id :', 'tokenico')
                    ),
                    array(
                        'id'    => 'contractVersion',
                        'type'  => 'text',
                        'title' => esc_html__('Contract version :', 'tokenico')
                    ),
                    array(
                        'id'    => 'adminAddress',
                        'type'  => 'text',
                        'title' => esc_html__('Admin address :', 'tokenico')
                    ),
                    array(
                        'id'    => 'network',
                        'type'  => 'text',
                        'title' => esc_html__('Network :', 'tokenico')
                    ),
                    array(
                        'id'    => 'networkName',
                        'type'  => 'content',
                        'class' => 'network-name-content',
                        'title' => esc_html__('Network name :', 'tokenico'),
                        'content' => isset($network->name) ? $network->name : null
                    ),
                    array(
                        'id' => 'contract',
                        'type'  => 'select',
                        'title' => esc_html__('Contract :', 'tokenico'),
                        'options' => [
                            'TokenICO' => 'TokenICO'
                        ],
                        'default' => 'TokenICO'
                    ),
                    array(
                        'id'    => 'contractAddress',
                        'type'  => 'text',
                        'title' => esc_html__('Presale contract address :', 'tokenico')
                    ),
                    array(
                        'id'    => 'tokenAddress',
                        'type'  => 'text',
                        'title' => esc_html__('Token contract address :', 'tokenico'),
                        'sanitize' => function($val) {
                            return sanitize_text_field($val);
                        },
                        'validate' => function($val) {
                            $val = sanitize_text_field($val);
                            if (empty($val)) {
                                return esc_html__('Token address cannot be empty.', 'tokenico');
                            } elseif (strlen($val) < 42 || strlen($val) > 42) {
                                return esc_html__('Token address must consist of 42 characters.', 'tokenico');
                            }
                        }
                    ),
                    array(
                        'id'    => 'totalSaleLimit',
                        'type'  => 'number',
                        'title' => esc_html__('Total sale limit :', 'tokenico'),
                        'desc' => esc_html__('Total sales limit. That is, how much will be sold in total (The native coin of the network where you will publish the contract.)', 'tokenico'),
                        'attributes' => [
                            'min' => 0
                        ],
                        'sanitize' => function($val) {
                            return floatval($val);
                        },
                        'validate' => function($val) {
                            $val = floatval($val);
                            if (empty($val)) {
                                return esc_html__('Total limit cannot be empty.', 'tokenico');
                            } elseif ($val < 0) {
                                return esc_html__('Total limit cannot be less than 0!', 'tokenico');
                            }
                        }
                    ),
                    array(
                        'id'    => 'minContribution',
                        'type'  => 'number',
                        'title' => esc_html__('Min contribution :', 'tokenico'),
                        'desc' => esc_html__('Minimum purchase limit for a user. (The native coin of the network where you will publish the contract.)', 'tokenico'),
                        'attributes' => [
                            'min' => 0
                        ],
                        'sanitize' => function($val) {
                            return floatval($val);
                        },
                        'validate' => function($val) {
                            $val = floatval($val);
                            if (empty($val)) {
                                return esc_html__('Min contribution cannot be empty.', 'tokenico');
                            } elseif ($val < 0) {
                                return esc_html__('Min contribution cannot be less than 0!', 'tokenico');
                            }
                        }
                    ),
                    array(
                        'id'    => 'maxContribution',
                        'type'  => 'number',
                        'title' => esc_html__('Max contribution :', 'tokenico'),
                        'desc' => esc_html__('Maximum purchase limit for a user. (The native coin of the network where you will publish the contract.)', 'tokenico'),
                        'attributes' => [
                            'min' => 0
                        ],
                        'sanitize' => function($val) {
                            return floatval($val);
                        },
                        'validate' => function($val) {
                            $val = floatval($val);
                            if (empty($val)) {
                                return esc_html__('Max contribution cannot be empty.', 'tokenico');
                            } elseif ($val < 0) {
                                return esc_html__('Max contribution cannot be less than 0!', 'tokenico');
                            }
                        }
                    ),
                    array(
                        'id'    => 'exchangeRate',
                        'type'  => 'number',
                        'title' => esc_html__('Exchange rate :', 'tokenico'),
                        'desc' => esc_html__('Example: 1 COIN = 100000 TOKEN (The native coin of the network where you will publish the contract.)', 'tokenico'),
                        'attributes' => [
                            'min' => 0
                        ],
                        'sanitize' => function($val) {
                            return floatval($val);
                        },
                        'validate' => function($val) {
                            $val = floatval($val);
                            if (empty($val)) {
                                return esc_html__('Exchange rate cannot be empty.', 'tokenico');
                            } elseif ($val < 0) {
                                return esc_html__('Exchange rate cannot be less than 0!', 'tokenico');
                            }
                        }
                    ),
                    array(
                        'id'    => 'startDate',
                        'type'  => 'text',
                        'title' => esc_html__('Start date :', 'tokenico'),
                        'desc' => esc_html__('It\'s date to start the presale. (Adjust the time according to the UTC time zone)', 'tokenico'),
                        'attributes' => [
                            'type' => 'datetime-local',
                            'autocomplete' => 'off'
                        ],
                        'validate' => function($val) {
                            if (empty($val)) {
                                return esc_html__('Start date cannot be empty.', 'tokenico');
                            }
                        },
                    ),
                    array(
                        'id'    => 'endDate',
                        'type'  => 'text',
                        'title' => esc_html__('End date :', 'tokenico'),
                        'desc' => esc_html__('It\'s date to end the presale. (Adjust the time according to the UTC time zone)', 'tokenico'),
                        'attributes' => [
                            'type' => 'datetime-local',
                            'autocomplete' => 'off'
                        ],
                        'validate' => function($val) {
                            if (empty($val)) {
                                return esc_html__('End date cannot be empty.', 'tokenico');
                            }
                        },
                    ),
                    array(
                        'id'    => 'autoTransfer',
                        'type'  => 'switcher',
                        'title' => esc_html__('Auto transfer :', 'tokenico'),
                        'desc' => esc_html__('It will be transferred automatically when the payment is completed. If it is closed, they can get the tokens they bought with the claim button after the presale is over.', 'tokenico'),
                        'default' => true
                    ),
                )
            ));

            if ($presale->post_type == 'presale' && $presale->post_status == 'publish') {
                $presaleStatus = 'presaleStatus';
                CSF::createMetabox($presaleStatus, array(
                    'title'     => esc_html__('Presale status', 'tokenico'),
                    'post_type' => 'presale',
                    'data_type' => 'unserialize',
                    'context'   => 'side',
                ));
        
                if ($presale->network) {
                    CSF::createSection($presaleStatus, array(
                        'fields' => array(
                            array(
                                'id'    => 'totalSales',
                                'type'  => 'content',
                                'title' => esc_html__('Total sales :', 'tokenico'),
                                'content' => $presale->getTotalSales()
                            ),
                            array(
                                'id'    => 'remainingLimit',
                                'type'  => 'content',
                                'title' => esc_html__('Remaining limit :', 'tokenico'),
                                'content' => $presale->getRemainingLimit()
                            )
                        )
                    ));
                }
            }
        }
    }
}