<?php

namespace BeycanPress\Tokenico\Models;

use Beycan\Moodel\AbstractModel;

/**
 * Sale table model
 */
class Sale extends AbstractModel 
{
    public function __construct()
    {
        parent::__construct([
            'transactionId' => [
                'type' => 'string',
                'length' => 70,
                'index' => [
                    'type' => 'unique'
                ]
            ],
            'presaleId' => [
                'type' => 'integer',
            ],
            'receiverAddress' => [
                'type' => 'string',
                'length' => 70,
            ],
            'quantityPurchased' => [
                'type' => 'float',
            ],
            'purchaseAmount' => [
                'type' => 'float',
            ],
            'sent' => [
                'type' => 'boolean'
            ],
            'paymentInfo' => [
                'type' => 'text'
            ],
            'createdAt' => [
                'type' => 'timestamp',
                'default' => 'current_timestamp',
            ],
        ]);
    }

    
    public function search(string $text) : array
    {
        return $this->getResults(str_ireplace(
            '%s', 
            '%' . $this->db->esc_like($text) . '%', "
            SELECT * FROM {$this->tableName} 
            WHERE transactionId LIKE '%s' 
            OR presaleId LIKE '%s' 
            OR receiverAddress LIKE '%s'
            OR paymentInfo LIKE '%s'
			ORDER BY id DESC
        "));
    }

    public function searchCount(string $text) : int
    {
        return (int) $this->getVar(
            str_ireplace(
            '%s', 
            '%' . $this->db->esc_like($text) . '%', "
            SELECT COUNT(id) FROM {$this->tableName} 
            WHERE transactionId LIKE '%s' 
            OR presaleId LIKE '%s' 
            OR receiverAddress LIKE '%s'
            OR paymentInfo LIKE '%s'
        "));
    }

    public function getPurchaseAmount(string $receiverAddress, int $presaleId) : float
    {
        return (float) $this->getVar(
            $this->prepare(
                "SELECT SUM(purchaseAmount) FROM {$this->tableName} 
                WHERE receiverAddress = '%s' AND presaleId = %d",
                [$receiverAddress, $presaleId]
            )
        );
    }

    public function getQuantityPurchased(string $receiverAddress, int $presaleId) : float
    {
        return (float) $this->getVar(
            $this->prepare(
                "SELECT SUM(quantityPurchased) FROM {$this->tableName} 
                WHERE receiverAddress = '%s' AND presaleId = %d",
                [$receiverAddress, $presaleId]
            )
        );
    }
}