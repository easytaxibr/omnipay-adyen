<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Class RefundRequest
 * @package Omnipay\Adyen\Message
 */
class RefundRequest extends AbstractRequest
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
     * Returns the username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * Sets the username
     *
     * @param string $username
     * @return RefundRequest $this
     */
    public function setUsername($username)
    {
        $this->setParameter('username', $username);
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
     * Sets the password
     *
     * @param string $password
     * @return RefundRequest $this
     */
    public function setPassword($password)
    {
        $this->setParameter('password', $password);
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
     * Sets the merchant account
     *
     * @param string $merchant_account
     * @return RefundRequest $this
     */
    public function setMerchantAccount($merchant_account)
    {
        $this->setParameter('merchant_account', $merchant_account);
        return $this;
    }

    /**
     * Does the refund request to adyen server,
     * and returns a Response object
     *
     * @param array $data
     * @return RefundResponse
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function sendData($data)
    {
        $response = $this->httpClient->post(
            $this->getEndpoint(),
            [],
            http_build_query($data),
            [
                'auth' => [$this->getUsername(),$this->getPassword()]
            ]
        )->send();

        $response_data = [];
        parse_str($response->getBody(true), $response_data);

        return $this->response = new RefundResponse($this, $response_data);
    }

    /**
     * Returns the data required for the request
     * to be created
     *
     * @return array
     */
    public function getData()
    {
        return [
            'action' => "Payment.cancelOrRefund",
            'modificationRequest.merchantAccount' => $this->getMerchantAccount(),
            'modificationRequest.originalReference' => $this->getTransactionId(),
        ];
    }
}
