<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class NotificationResponse
 * @package Omnipay\Adyen\Message
 */
class NotificationResponse extends AbstractResponse
{
    /**
     * Returns whether the response (of any type)
     * is successful
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return (bool)$this->data['success'];
    }

    /**
     * Returns whether it is a successful authorisation response
     *
     * @return bool
     */
    public function isAuthorized()
    {
        return ($this->data['eventCode'] == 'AUTHORISATION'
            && $this->isSuccessful());
    }

    /**
     * Returns whether it is a successful pending response
     *
     * @return bool
     */
    public function isPending()
    {
        return ($this->data['eventCode'] == 'PENDING'
            && $this->isSuccessful());
    }

    /**
     * Returns whether it is a successful chargeback response
     *
     * @return bool
     */
    public function isChargeback()
    {
        return ($this->data['eventCode'] == 'CHARGEBACK'
            && $this->isSuccessful());
    }

    /**
     * Returns the payment method of the Boleto
     * normally the facility used to issue the Boleto
     *
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->data['paymentMethod'];
    }

    /**
     * Returns the currency of the Boleto
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->data['currency'];
    }

    /**
     * Returns the due date of the boleto
     *
     * @return string
     */
    public function getDueDate()
    {
        return $this->data['additionalData_boletobancario_dueDate'];
    }

    /**
     * Returns the transaction reference (PSP reference)
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->data['pspReference'];
    }

    /**
     * Returns the merchant reference
     *
     * @return string
     */
    public function getTransactionReference()
    {
        return $this->data['merchantReference'];
    }

    /**
     * Returns the value of the Boleto
     *
     * @return float
     */
    public function getValue()
    {
        //TODO convert to major units
        return $this->data['value'];
    }

    /**
     * Returns the expiration date for the
     * Boleto
     *
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->data['additionalData_boletobancario_expirationDate'];
    }

    /**
     * Returns the date recorded on the event
     *
     * @return string
     */
    public function getEventDate()
    {
        return $this->data['eventDate'];
    }

    /**
     * Returns the acquirer reference
     *
     * @return string
     */
    public function getAcquirerReference()
    {
        return $this->data['additionalData_acquirerReference'];
    }

    /**
     * Returns if environment status
     *
     * @return boolean
     */
    public function isLive()
    {
        return $this->data['live'];
    }

    /**
     * Returns the merchant account associated with notification
     *
     * @return string
     */
    public function getMerchantAccountCode()
    {
        return $this->data['merchantAccountCode'];
    }
}
