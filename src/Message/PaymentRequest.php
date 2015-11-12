<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class Request
 * @package Omnipay\Adyen
 */
class PaymentRequest extends BaseRequest
{
    const ONE_CLICK = 'ONECLICK';
    const RECURRING = 'RECURRING';
    const ONE_CLICK_RECURRING = 'ONECLICK,RECURRING';

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
     * Optional: Sets 3d secure to enabled/disabled status
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
        $this->setResponseClass(PaymentResponse::class);

        if (!empty($type) && ($type == PaymentRequest::ONE_CLICK || $type == PaymentRequest::RECURRING)) {
            if (empty($card->getEmail())
                || empty($card->getShopperReference())
            ) {
                throw new InvalidRequestException(
                    'One Click and/or Recurring Payments require the email and shopper reference'
                );
            }
            $recurring_detail_reference = $this->getRecurringDetailReference();
            if (empty($recurring_detail_reference)) {
                $payment_params =
                    ['paymentRequest.recurring.contract' => self::ONE_CLICK_RECURRING];
                $this->addInitialSavedCardPaymentParams($card, $payment_params);
            } else {
                $payment_params = ['paymentRequest.recurring.contract' => $type];
                $this->addSuccessiveSavedCardPaymentParams($type, $card, $payment_params);
            }

        } else {
            $payment_params = $this->getPaymentParams($card);
        }

        $payment_params = $this->applyCommonPaymentParams($card, $payment_params);

        return $payment_params;
    }

    /**
     * Applies the payment params for the initial one click payment
     *
     * @param \Omnipay\Adyen\Message\CreditCard $card
     * @param array $payment_params
     */
    protected function addInitialSavedCardPaymentParams($card, array &$payment_params)
    {
        $payment_params['paymentRequest.additionalData.card.encrypted.json'] =
            $card->getAdyenCardData();
    }

    /**
     * Applies the payment parameters for successive one click payments
     *
     * @param string $type
     * @param \Omnipay\Adyen\Message\CreditCard $card
     * @param array $payment_params
     */
    protected function addSuccessiveSavedCardPaymentParams($type, $card, array &$payment_params)
    {
        if ($type == self::ONE_CLICK) {
            $payment_params += [
                'paymentRequest.selectedRecurringDetailReference' =>
                    $this->getRecurringDetailReference(),
                'paymentRequest.card.cvc' => $card->getCvv()
            ];
        } elseif ($type == self::RECURRING) {
            $payment_params +=
                [
                    "paymentRequest.shopperInteraction" => "ContAuth",
                    'paymentRequest.selectedRecurringDetailReference' => 'LATEST'
                ];
        }
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
        $payment_params += parent::applyBaseRequestParams($payment_params);
        $payment_params += [
            'paymentRequest.shopperEmail' => $card->getEmail(),
            'paymentRequest.shopperReference' => $card->getShopperReference()
        ];

        if ($this->get3dSecure()) {
            $payment_params += [
                'paymentRequest.browserInfo.userAgent' => $this->getHttpUserAgent(),
                'paymentRequest.browserInfo.acceptHeader' => $this->getHttpAccept()
            ];
        }

        return $payment_params;
    }
}
