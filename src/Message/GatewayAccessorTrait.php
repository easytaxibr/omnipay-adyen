<?php

namespace Omnipay\Adyen\Message;

/**
 * Class GatewayAccessorTrait
 * @package Omnipay\Adyen\Message
 */
trait GatewayAccessorTrait
{
    /**
     * @var string
     */
    protected $test_endpoint = 'https://pal-test.adyen.com/pal/adapter/httppost';
    /**
     * @var string
     */
    protected $live_endpoint = 'https://pal-live.adyen.com/pal/adapter/httppost';

    /**
     * Sets the username, received from gateway
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
     * Sets the password, received from the gateway
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
     * Sets the merchant account, received from the gateway
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
     * Returns the API endpoint based on whether
     * test mode is flagged or not.
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->getTestMode()
            ? $this->test_endpoint
            : $this->live_endpoint;
    }

    /**
     * Optional: Set $_SERVER['HTTP_USER_AGENT']
     * Needed if 3d secure is enabled and an
     * Omnipay\Adyen\Message\SecureRequest is being performed
     *
     * @param boolean $value
     */
    public function setHttpUserAgent($value)
    {
        $this->setParameter('HTTP_USER_AGENT', $value);
    }

    /**
     * Returns $_SERVER['HTTP_USER_AGENT']
     *
     * @return string
     */
    public function getHttpUserAgent()
    {
        return $this->getParameter('HTTP_USER_AGENT');
    }

    /**
     * Optional: Set $_SERVER['HTTP_ACCEPT']
     * Needed if 3d secure is enabled and an
     * Omnipay\Adyen\Message\SecureRequest is being performed
     *
     * @param boolean $value
     */
    public function setHttpAccept($value)
    {
        $this->setParameter('HTTP_ACCEPT', $value);
    }

    /**
     * Returns $_SERVER['HTTP_ACCEPT']
     *
     * @return string
     */
    public function getHttpAccept()
    {
        return $this->getParameter('HTTP_ACCEPT');
    }

    /**
     * Optional: Set $_SERVER['REMOTE_ADDR']
     * Needed if 3d secure is enabled and an
     * Omnipay\Adyen\Message\SecureRequest is being performed
     *
     * @param boolean $value
     */
    public function setRemoteAddr($value)
    {
        $this->setParameter('REMOTE_ADDR', $value);
    }

    /**
     * Returns $_SERVER['REMOTE_ADDR']
     *
     * @return string
     */
    public function getRemoteAddr()
    {
        return $this->getParameter('REMOTE_ADDR');
    }
}
