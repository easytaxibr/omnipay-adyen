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
     * Always returns false because the transaction isn't complete until a future point in time.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * If transaction was completed successfully there will be a redirect url provided
     * which will direct the user to the PDF needed to complete transaction
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
        return $this->data['additionalData_boletobancario_data'];
    }

    /**
     * Redirect URL: This is the URL of the boleto PDF
     *
     * @return string
     */
    public function getRedirectUrl()
    {

        return $this->data['additionalData_boletobancario_url'];
    }

    /**
     * Returns the date the boleto form expires and is removed from Adyen admin
     *
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->data['additionalData_boletobancario_expirationDate'];
    }

    /**
     * Returns the due date provided to the customer to complete boleto transaction
     *
     * @return string
     */
    public function getDueDate()
    {
        return $this->data['additionalData_boletobancario_dueDate'];
    }
}
