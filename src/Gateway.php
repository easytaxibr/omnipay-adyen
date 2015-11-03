<?php

namespace Omnipay\Adyen;

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
     * @param $username
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
     * @param $password
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
     * @param $merchant_account
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
    public function purchase($data)
    {
        return $this->createRequest(
            '\Omnipay\Adyen\Message\PaymentRequest',
            $data
        );
    }
}
