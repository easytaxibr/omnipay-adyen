<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Class CardRequest
 * @package Omnipay\Adyen\Message
 */
class CardRequest extends AbstractRequest
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
     * Sets the username, received from gateway
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->setParameter('username', $username);
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
     */
    public function setPassword($password)
    {
        $this->setParameter('password', $password);
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
     */
    public function setMerchantAccount($merchant_account)
    {
        $this->setParameter('merchant_account', $merchant_account);
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
     * Sets the shopper reference
     *
     * @param string $reference
     */
    public function setShopperReference($reference)
    {
        $this->setParameter('shopper_reference', $reference);
    }

    /**
     * Returns the shopper reference
     *
     * @return string
     */
    public function getShopperReference()
    {
        return $this->getParameter('shopper_reference');
    }

    /**
     * Sets the contract type
     *
     * @param string $type
     */
    public function setContractType($type)
    {
        $this->setParameter('contract_type', $type);
    }

    /**
     * Returns the contract type.
     *
     * @return string
     */
    public function getContractType()
    {
        return $this->getParameter('contract_type');
    }

    /**
     * Returns the getCard parameters
     *
     * @return array
     */
    public function getData()
    {
        return [
            "action" => "Recurring.listRecurringDetails",
            "recurringDetailsRequest.merchantAccount" => $this->getMerchantAccount(),
            "recurringDetailsRequest.shopperReference" => $this->getShopperReference(),
            "recurringDetailsRequest.recurring.contract" => $this->getContractType()
        ];
    }

    /**
     * Does the getCard request to adyen
     *
     * @param array $data
     * @return CardResponse
     */
    public function sendData($data)
    {
        $response = $this->httpClient->post(
            $this->getEndpoint(),
            [],
            http_build_query($data),
            [
                'auth' => [$this->getUsername(), $this->getPassword()]
            ]
        )->send();

        $response_data = [];
        parse_str($response->getBody(true), $response_data);

        return $this->response = new CardResponse($this, $response_data);
    }
}
