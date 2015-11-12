<?php

namespace Omnipay\Adyen;

use Omnipay\Adyen\Message\CardRequest;
use Omnipay\Adyen\Message\GatewayAccessorTrait;
use Omnipay\Adyen\Message\AuthorizeRequest;
use Omnipay\Adyen\Message\PaymentRequest;
use Omnipay\Adyen\Message\RefundRequest;
use Omnipay\Adyen\Message\SecureRequest;
use Omnipay\Common\AbstractGateway;

/**
 * Class Gateway
 * @package Omnipay\Adyen
 */
class Gateway extends AbstractGateway
{
    use GatewayAccessorTrait;

    /**
     * Returns the name of the gateway
     *
     * @return string
     */
    public function getName()
    {
        return 'Adyen';
    }

    /**
     * Returns the default parameters
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return [
            'username' => '',
            'password' => '',
            'merchant_account' => '',
            'testMode' => true
        ];
    }

    /**
     * Returns a purchase request
     *
     * @param array $data
     * @return \Omnipay\Adyen\Message\PaymentRequest
     */
    public function purchase(array $data = [])
    {
        return $this->createRequest(
            PaymentRequest::class,
            $data
        );
    }

    /**
     * Returns an authorization request for boleto payment type
     *
     * @param array $data
     * @return \Omnipay\Adyen\Message\AuthorizeRequest
     */
    public function authorize(array $data = [])
    {
        return $this->createRequest(
            AuthorizeRequest::class,
            $data
        );
    }

    /**
     * Returns a refund-or-cancel request
     *
     * @param array $data
     * @return \Omnipay\Adyen\Message\RefundRequest
     */
    public function refund(array $data = [])
    {
        return $this->createRequest(
            RefundRequest::class,
            $data
        );
    }

    /**
     * Returns a Card Request
     *
     * @param array $data
     * @return \Omnipay\Adyen\Message\CardRequest
     */
    public function getCard(array $data = [])
    {
        return $this->createRequest(
            CardRequest::class,
            $data
        );
    }

    /**
     * Returns a 3D Secure Request
     *
     * @param array $data
     * @return \Omnipay\Adyen\Message\SecureRequest
     */
    public function completePurchase(array $data = [])
    {
        return $this->createRequest(
            SecureRequest::class,
            $data
        );
    }
}
