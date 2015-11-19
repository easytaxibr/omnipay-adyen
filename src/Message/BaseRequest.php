<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Adyen\Message\CreditCard;

/**
 * Class Request
 * @package Omnipay\Adyen
 */
class BaseRequest extends AbstractRequest
{
    use GatewayAccessorTrait;

    /**
     * Sets the complete path to the response class file used by sendData()
     *
     * @param boolean $value
     */
    public function setResponseClass($value)
    {
        $this->setParameter('response_class', $value);
    }

    /**
     * Returns the response class used by sendData()
     *
     * @return string
     */
    public function getResponseClass()
    {
        return $this->getParameter('response_class');
    }

    /**
     * Returns the data required for the request to be created
     * Should be implemented by subclasses as the data will vary for each request
     *
     * @return array
     */
    public function getData()
    {
        return [];
    }

    /**
     * Does the request to adyen server, and returns a
     * Response object
     *
     * @param array $data
     * @return Response class object
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

        $response_data = [];
        parse_str($response->getBody(true), $response_data);

        $response_class = $this->getResponseClass();
        return $this->response = new $response_class($this, $response_data);
    }

    /**
     * Applies the parameters common to all payment types
     *
     * @param array $payment_params
     * @return array
     * @throws InvalidRequestException
     */
    protected function applyBaseRequestParams(array $payment_params)
    {
        return $payment_params += [
            'action' => 'Payment.authorise',
            'paymentRequest.merchantAccount' => $this->getMerchantAccount(),
            'paymentRequest.amount.currency' => $this->getCurrency(),
            'paymentRequest.amount.value' => $this->getAmount(),
            'paymentRequest.reference' => $this->getTransactionReference()
        ];
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
     * Sets the card.
     *
     * @param CreditCard $value
     * @return AbstractRequest Provides a fluent interface
     */
    public function setCard($value)
    {
        if ($value && is_array($value)) {
            $value = new CreditCard($value);
        }

        parent::setCard($value);
    }
}
