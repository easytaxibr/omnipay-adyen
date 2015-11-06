<?php

namespace Omnipay\Adyen;

use Omnipay\Adyen\Message\CardRequest;
use Omnipay\Adyen\Message\PaymentRequest;
use Omnipay\Adyen\Message\RefundRequest;
use Omnipay\Adyen\Message\RefundRequestTest;
use Omnipay\Common\AbstractGateway;

/**
 * Class Gateway
 * @package Omnipay\Adyen
 */
class Gateway extends AbstractGateway
{
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
            'testMode' => true,
        ];
    }

    /**
     * Sets the username
     *
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->setParameter('username', $username);
        return $this;
    }

    /**
     * Returns the username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * Sets the password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->setParameter('password', $password);
        return $this;
    }

    /**
     * Returns the password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * Returns the merchant account
     *
     * @param string $merchant_account
     * @return $this
     */
    public function setMerchantAccount($merchant_account)
    {
        $this->setParameter('merchant_account', $merchant_account);
        return $this;
    }

    /**
     * Returns the merchant account
     *
     * @return string
     */
    public function getMerchantAccount()
    {
        return $this->getParameter('merchant_account');
    }


    /**
     * Returns a purchase (authorisation) request
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
}
