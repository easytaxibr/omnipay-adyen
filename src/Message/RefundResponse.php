<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class RefundResponse
 * @package Omnipay\Adyen\Message
 */
class RefundResponse extends AbstractResponse
{
    /**
     * Returns whether the transaction was
     * successful or not.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->data['modificationResult_response'] == '[cancelOrRefund-received]';

    }

    /**
     * Returns the response message obtained from Adyen
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->data['modificationResult_response'];
    }

    /**
     * Returns the Transaction Id (PSP Reference)
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->data['modificationResult_pspReference'];
    }
}
