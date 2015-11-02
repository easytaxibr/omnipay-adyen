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
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->setParameter('username', $username);
    }

    /**
     * Sets the password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->setParameter('password', $password);
    }

    /**
     * Sets the merchant account
     *
     * @param string $merchant_account
     */
    public function setMerchantAccount($merchant_account)
    {
        $this->setParameter('merchant_account', $merchant_account);
    }

    /**
     * Retuns a purchase (authorisation) request
     *
     * @param array $data
     * @return \Omnipay\Adyen\Message\Request
     */
    public function purchase(array $data)
    {
        return $this->createRequest(
            '\Omnipay\Adyen\Message\Request',
            $data
        );
    }
}
