<?php

namespace BeycanPress\Tokenico\Entity;

use \BeycanPress\Tokenico\PluginHero\Entity;

class Presale extends Entity
{
    private $currencySymbol = null;

    private $networkName = null;

    public function __construct($entityId)
    {
        parent::__construct($entityId);

        if ($this->network) {
            $this->currencySymbol = json_decode($this->network)->nativeCurrency->symbol;
            $this->networkName = json_decode($this->network)->name;
        }
    }

    public function getStatus() : string
    {
		$utcTime = strtotime(wp_date("Y-m-d H:i:s", null, new \DateTimeZone('UTC')));
        if (
            strtotime($this->startDate) <= $utcTime && 
            strtotime($this->endDate) >= $utcTime && 
            $this->totalSales < $this->totalSaleLimit
        ) {
            return 'started';
        } elseif (
            strtotime($this->endDate) <= $utcTime || 
            $this->totalSales == $this->totalSaleLimit
        ) {
            return 'ended';
        } else {
            return 'notStarted';
        } 
    }

    public function getNetworkName()
    {
        return esc_html($this->networkName);
    }

    public function getTotalSaleLimit()
    {
        return esc_html($this->totalSaleLimit . ' ' . $this->currencySymbol);
    }

    public function getTotalSales()
    {
        return esc_html($this->totalSales . ' ' . $this->currencySymbol);
    }

    public function getRemainingLimit()
    {
        return esc_html($this->remainingLimit . ' ' . $this->currencySymbol);
    }

    public function getMinContribution()
    {
        return esc_html($this->minContribution . ' ' . $this->currencySymbol);
    }

    public function getMaxContribution()
    {
        return esc_html($this->maxContribution . ' ' . $this->currencySymbol);
    }

    public function getExchangeRate()
    {
		$tokenSymbol = $this->token ? json_decode($this->token)->symbol : null;
        return esc_html(1 . ' ' . $this->currencySymbol . ' = ' . $this->exchangeRate . ' ' . $tokenSymbol);
    }

    public function getStartDate()
    {
        return esc_html(date_i18n(get_option('date_format') . ' H:i', strtotime($this->startDate)));
    }

    public function getEndDate()
    {
        return esc_html(date_i18n(get_option('date_format') . ' H:i', strtotime($this->endDate)));
    }
}
