<?php

namespace Omnipay\Adyen\Message;

/**
 * Class CreditCard
 * @package Omnipay\Adyen
 */
class CreditCard extends \Omnipay\Common\CreditCard
{
    /**
     * Sets the Client Side Encrypted Card Data
     *
     * @param string $value
     */
    public function setEncryptedCardData($value)
    {
        $this->setParameter('encrypted_card_data', $value);
    }

    /**
     * Returns the Client Side Encrypted Data
     *
     * @return string
     */
    public function getAdyenCardData()
    {
        return $this->getParameter('encrypted_card_data');
    }

    /**
     * Sets the shopper reference
     *
     * @param string $reference
     */
    public function setShopperReference($reference)
    {
        $this->setParameter('shopper_reference', $reference);
    }

    /**
     * Returns the shopper reference
     *
     * @return string
     */
    public function getShopperReference()
    {
        return $this->getParameter('shopper_reference');
    }
}
