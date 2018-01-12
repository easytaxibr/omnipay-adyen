<?php

namespace Omnipay\Adyen;

class RecurringDetail
{
    private $adyenResponse;

    public function __construct($adyenResponse)
    {
        $this->adyenResponse = $adyenResponse;
    }

    public function getAdditionalData()
    {
        return $this->adyenResponse['additionalData'];
    }

    public function getCard()
    {
        if (isset($this->adyenResponse['card'])) {
            return $this->adyenResponse['card'];
        }

        return [];
    }

    public function getAlias()
    {
        return $this->adyenResponse['alias'];
    }

    public function getAliasType()
    {
        return $this->adyenResponse['aliasType'];
    }

    public function getCreationDate()
    {
        return $this->adyenResponse['creationDate'];
    }

    public function getFirstPspReference()
    {
        return $this->adyenResponse['firstPspReference'];
    }

    public function getPaymentMethodVariant()
    {
        return $this->adyenResponse['paymentMethodVariant'];
    }

    public function getRecurringDetailReference()
    {
        return $this->adyenResponse['recurringDetailReference'];
    }

    public function getVariant()
    {
        return $this->adyenResponse['variant'];
    }
}
