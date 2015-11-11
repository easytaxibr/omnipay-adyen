<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class Response
 * @package Omnipay\Adyen\Message
 */
class AuthorizeResponse extends AbstractResponse
{
    /**
     * Returns whether the transaction was
     * successful or not
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * If 3D secure is enabled the user will need to be redirected to authorization page
     *
     * @return bool
     */
    public function isRedirect()
    {
        return $this->getResultCode() == 'Received' && !empty($this->getRedirectUrl());
    }

    /**
     * Returns the Result code
     *
     * @return string
     */
    public function getResultCode()
    {
        return $this->data['resultCode'];
    }

    /**
     * Returns the Transaction Id (PSP Reference)
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->data['pspReference'];
    }

    /**
     * Returns the Additional Data from Adyen
     *
     * @return mixed
     */
    public function getAdditionalData()
    {
        return $this->data['additionalData.boletobancario.data'];
    }

    /**
     * Redirect URL: This is the URL of the boleto PDF
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->data['additionalData.boletobancario.url'];
    }

    /**
     * Returns the date the boleto form expires and is removed from Adyen admin
     *
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->data['additionalData.boletobancario.expirationDate'];
    }

    /**
     * Returns the due date provided to the customer to complete boleto transaction
     *
     * @return string
     */
    public function getDueDate()
    {
        return $this->data['additionalData.boletobancario.dueDate'];
    }
}
