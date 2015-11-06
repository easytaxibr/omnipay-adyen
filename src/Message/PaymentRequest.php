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
    use GatewayAccessorTrait;

    const ONE_CLICK = 'ONECLICK';

    /**
     * Sets the type of payment eg. one click
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->setParameter('type', $type);
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
     * Optional: Sets the 3d secure to enabled status
     *
     * @param boolean $value
     */
    public function set3dSecure($value)
    {
        $this->setParameter('3d_secure', $value);
    }

    /**
     * Returns the whether 3D Secure is enabled
     *
     * @return string
     */
    public function get3dSecure()
    {
        return $this->getParameter('3d_secure');
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
                $this->addInitialOneClickPaymentParams($card, $payment_params);
            } else {
                $this->addSuccessiveOneClickPaymentParams($card, $payment_params);
            }

        } else {
            $payment_params = $this->getPaymentParams($card);
        }

        $payment_params = $this->applyCommonPaymentParams($card, $payment_params);

        return $payment_params;
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

        $response_data = [];
        parse_str($response->getBody(true), $response_data);

        return $this->response = new PaymentResponse($this, $response_data);
    }

    /**
     * Applies the payment params for the initial one click payment
     *
     * @param \Omnipay\Adyen\Message\CreditCard $card
     * @param array $payment_params
     */
    protected function addInitialOneClickPaymentParams($card, array &$payment_params)
    {
        $payment_params['paymentRequest.additionalData.card.encrypted.json'] =
        $card->getAdyenCardData();
    }

    /**
     * Applies the payment parameters for successive one click payments
     *
     * @param \Omnipay\Adyen\Message\CreditCard $card
     * @param array $payment_params
     */
    protected function addSuccessiveOneClickPaymentParams($card, array &$payment_params)
    {
        $payment_params += [
            'paymentRequest.selectedRecurringDetailReference' =>
            $this->getRecurringDetailReference(),
            'paymentRequest.card.cvc' => $card->getCvv()
        ];
    }

    /**
     * Returns the payment parameters for standard
     * credit card payments
     *
     * @param \Omnipay\Adyen\Message\CreditCard $card
     * @return array
     */
    protected function getPaymentParams($card)
    {
        $payment_params = [
            'paymentRequest.card.billingAddress.street' => $card->getBillingAddress1(),
            'paymentRequest.card.billingAddress.postalCode' => $card->getPostcode(),
            'paymentRequest.card.billingAddress.city' => $card->getCity(),
            'paymentRequest.card.billingAddress.houseNumberOrName' => $card->getBillingAddress2(),
            'paymentRequest.card.billingAddress.stateOrProvince' => $card->getState(),
            'paymentRequest.card.billingAddress.country' => $card->getCountry(),
            'paymentRequest.additionalData.card.encrypted.json' => $card->getAdyenCardData()
        ];
        return $payment_params;
    }

    /**
     * Applies the parameters common to all payment types
     *
     * @param \Omnipay\Adyen\Message\CreditCard $card
     * @param array $payment_params
     * @return array
     * @throws InvalidRequestException
     */
    protected function applyCommonPaymentParams($card, array $payment_params)
    {
        $payment_params += [
            'action' => 'Payment.authorise',
            'paymentRequest.merchantAccount' => $this->getMerchantAccount(),
            'paymentRequest.amount.currency' => $this->getCurrency(),
            'paymentRequest.amount.value' => $this->getAmount(),
            'paymentRequest.reference' => $this->getTransactionReference(),
            'paymentRequest.shopperEmail' => $card->getEmail(),
            'paymentRequest.shopperReference' => $card->getShopperReference()
        ];

        if ($this->get3dSecure()) {
            $payment_params += [
                'paymentRequest.browserInfo.userAgent' => $_SERVER['HTTP_USER_AGENT'],
                'paymentRequest.browserInfo.acceptHeader' => $_SERVER['HTTP_ACCEPT']
            ];
        }

        return $payment_params;
    }
}
