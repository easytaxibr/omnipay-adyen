<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;

/**
 * Class Request
 * @package Omnipay\Adyen
 */
class PaymentRequest extends AbstractRequest
{
    const ONE_CLICK = 'ONECLICK';

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
     * Sets the type of payment eg. one click
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->setParameter('type', $type);
        return $this;
    }

    /**
     * Returns the type of payment eg. one click
     *
     * @return string
     */
    public function getType()
    {
        return $this->getParameter('type');
    }

    /**
     * Sets the recurring detail reference
     *
     * @param string $reference
     */
    public function setRecurringDetailReference($reference)
    {
        $this->setParameter('recurring_detail_reference', $reference);
    }

    /**
     * Returns the recurring detail reference
     *
     * @return string
     */
    public function getRecurringDetailReference()
    {
        return $this->getParameter('recurring_detail_reference');
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
     * @throws InvalidRequestException
     * @return array
     */
    public function getData()
    {
        $card = $this->getCard();
        $type = $this->getType();

        if (!empty($type) && $type == PaymentRequest::ONE_CLICK) {
            if (empty($card->getEmail())
                || empty($card->getShopperReference())
            ) {
                throw new InvalidRequestException(
                    'One Click and/or Recurring Payments require the email and shopper reference'
                );
            }
            $payment_params = ['paymentRequest.recurring.contract' => $type];
            $recurring_detail_reference = $this->getRecurringDetailReference();
            if (empty($recurring_detail_reference)) {
                //Initial One Click Payment
                $payment_params += [
                    'paymentRequest.additionalData.card.encrypted.json' => $card->getAdyenCardData()
                ];
            } else {
                //Successive One Click Payment
                $payment_params += [
                    'paymentRequest.selectedRecurringDetailReference' => $this->getRecurringDetailReference(),
                    'paymentRequest.card.cvc' => $card->getCvv()
                ];
            }

        } else {
            $payment_params = [
                "paymentRequest.card.billingAddress.street" => $card->getBillingAddress1(),
                "paymentRequest.card.billingAddress.postalCode" => $card->getPostcode(),
                "paymentRequest.card.billingAddress.city" => $card->getCity(),
                "paymentRequest.card.billingAddress.houseNumberOrName" => $card->getBillingAddress2(),
                "paymentRequest.card.billingAddress.stateOrProvince" => $card->getState(),
                "paymentRequest.card.billingAddress.country" => $card->getCountry()
            ];
        }

        return $payment_params += [
            "action" => "Payment.authorise",
            "paymentRequest.merchantAccount" => $this->getMerchantAccount(),
            "paymentRequest.amount.currency" => $this->getCurrency(),
            "paymentRequest.amount.value" => $this->getAmount(),
            "paymentRequest.reference" => $this->getTransactionReference(),
            "paymentRequest.shopperEmail" => $card->getEmail(),
            "paymentRequest.shopperReference" => $card->getShopperReference(),
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
