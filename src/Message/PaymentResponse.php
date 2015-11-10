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
        return $this->data['paymentResult_resultCode'] == 'Authorised';
    }

    /**
     * If 3D secure is enabled the user will need to be redirected to authorization page
     *
     * @return bool
     */
    public function isRedirect()
    {
        return $this->data['paymentResult_resultCode'] == 'RedirectShopper';
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

    /**
     * Redirect URL: this is the HTTP POST action value.
     * This URL is the endpoint for the HTML form you use to redirect the shopper
     * to the card issuer 3D Secure verification page.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->data['paymentResult_issuerUrl'];
    }

    /**
     * Returns the 3D Secure request data for the issuer
     *
     * @return string
     */
    public function getPaRequest()
    {
        return $this->data['paymentResult_paRequest'];
    }

    /**
     * Returns the payment session identifier returned by the card issuer.
     * Identifies the payment session. Used for 3D secure transactions
     *
     * @return string
     */
    public function getMD()
    {
        return $this->data['paymentResult_md'];
    }
}
