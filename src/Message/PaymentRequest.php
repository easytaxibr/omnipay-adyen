<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Class Request
 * @package Omnipay\Adyen
 */
class PaymentRequest extends AbstractRequest
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
     */
    public function setUsername($username)
    {
        $this->setParameter('username', $username);
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
     * Sets the merchant account, received from the gateway
     *
     * @param $merchant_account
     */
    public function setMerchantAccount($merchant_account)
    {
        $this->setParameter('merchant_account', $merchant_account);
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
     * @return string
     */
    public function getMerchantAccount()
    {
        return $this->getParameter('merchant_account');
    }

    /**
     * Converts a price to minor unit
     *
     * @param int $amount
     * @param int $decimal_length
     * @return int
     */
    private function convertPriceToMinorUnits($amount, $decimal_length)
    {
        //Wrapping in string to preserve the decimals
        $amount = (("{$amount}") * (pow(10, $decimal_length)));
        return intval("$amount");
    }

    /**
     * OVERRIDE: Format an amount to minor units
     *
     * @param int|float $amount
     * @return int
     */
    public function formatCurrency($amount)
    {
        return $this->convertPriceToMinorUnits(
            $amount,
            $this->getCurrencyDecimalPlaces()
        );
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
     * Returns the data required for the request
     * to be created
     *
     * @return array
     */
    public function getData()
    {
        $card = $this->getCard();

        return [
            "action" => "Payment.authorise",
            "paymentRequest.merchantAccount" => $this->getMerchantAccount(),
            "paymentRequest.amount.currency" => $this->getCurrency(),
            "paymentRequest.amount.value" => $this->getAmount(),
            "paymentRequest.reference" => $this->getTransactionReference(),
            "paymentRequest.shopperEmail" => $card->getEmail(),
            "paymentRequest.shopperReference" => $card->getShopperReference(),

            "paymentRequest.card.billingAddress.street" => $card->getBillingAddress1(),
            "paymentRequest.card.billingAddress.postalCode" => $card->getPostcode(),
            "paymentRequest.card.billingAddress.city" => $card->getCity(),
            "paymentRequest.card.billingAddress.houseNumberOrName" => $card->getBillingAddress2(),
            "paymentRequest.card.billingAddress.stateOrProvince" => $card->getState(),
            "paymentRequest.card.billingAddress.country" => $card->getCountry(),

            'paymentRequest.additionalData.card.encrypted.json' => $card->getAdyenCardData()
        ];
    }

    /**
     * Does the request to adyen server, and returns a
     * Response object
     *
     * @param array $data
     * @return PaymentResponse
     * @throws \Omnipay\Common\Exception\InvalidRequestException
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

        parse_str($response->getBody(true), $response);

        return $this->response = new PaymentResponse($this, $response);
    }
}
