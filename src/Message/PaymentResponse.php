<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Class Response
 * @package Omnipay\Adyen\Message
 */
class PaymentResponse extends AbstractResponse
{
    /**
     * Returns whether the transaction was
     * successful or not
     *
     * @return bool
     */
    public function isSuccessful()
    {
        if ($this->data['paymentResult_resultCode'] == 'Refused') {
            return false;
        } elseif ($this->data['paymentResult_resultCode'] == 'Authorised') {
            return true;
        }
    }

    /**
     * Returns the Refusal Reason
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->data['paymentResult_refusalReason'];
    }

    /**
     * Returns the Transaction Id (PSP Reference)
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->data['paymentResult_pspReference'];
    }

    /**
     * Returns the Auth Code.
     *
     * @return mixed
     */
    public function getCode()
    {
        return $this->data['paymentResult_authCode'];
    }
}
