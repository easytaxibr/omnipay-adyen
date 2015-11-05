<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class CardResponse
 * @package Omnipay\Adyen\Message
 */
class CardResponse extends AbstractResponse
{
    /**
     * Whether or not the card was retrieved
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return !empty($this->data);
    }

    /**
     * Returns the Recurring Detail Reference
     *
     * @return string
     */
    public function getRecurringDetailReference()
    {
        return $this->data['recurringDetailsResult_details_0_recurringDetailReference'];
    }

    /**
     * Returns the Shopper Reference
     *
     * @return string
     */
    public function getShopperReference()
    {
        return $this->data['recurringDetailsResult_shopperReference'];
    }

    /**
     * Returns the Shopper Email
     *
     * @return mixed
     */
    public function getShopperEmail()
    {
        return $this->data['recurringDetailsResult_lastKnownShopperEmail'];
    }
}
