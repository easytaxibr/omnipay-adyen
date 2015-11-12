<?php

namespace Omnipay\Adyen\Message;

/**
 * Class CardRequest
 * @package Omnipay\Adyen\Message
 */
class CardRequest extends BaseRequest
{
    /**
     * Sets the contract type
     *
     * @param string $type
     */
    public function setContractType($type)
    {
        $this->setParameter('contract_type', $type);
    }

    /**
     * Returns the contract type.
     *
     * @return string
     */
    public function getContractType()
    {
        return $this->getParameter('contract_type');
    }

    /**
     * Returns the getCard parameters
     *
     * @return array
     */
    public function getData()
    {
        $this->setResponseClass(CardResponse::class);
        return [
            "action" => "Recurring.listRecurringDetails",
            "recurringDetailsRequest.merchantAccount" => $this->getMerchantAccount(),
            "recurringDetailsRequest.shopperReference" => $this->getShopperReference(),
            "recurringDetailsRequest.recurring.contract" => $this->getContractType()
        ];
    }
}
