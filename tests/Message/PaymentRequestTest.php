<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

/**
 * Class PaymentRequestTest
 * @package Omnipay\Adyen\Message
 */
class PaymentRequestTest extends TestCase
{
    private function getRequest($type = null)
    {
        $this->request = new PaymentRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $_SERVER = [
            'HTTP_USER_AGENT' => 'some_agent',
            'HTTP_ACCEPT' => 'accept'
        ];

        $request_params = array_merge(
            $this->getStandardPaymentParams(),
            [
                'test_mode' => true,
                'username' => 'some_username',
                'password' => 'some_password',
                'merchant_account' => 'some_merchant_account'
            ],
            $_SERVER
        );

        if (!empty($type)) {
            $request_params['type'] = $type;
        }

        $this->request->initialize(
            $request_params
        );
    }

    private function getSuccessiveSavedCardRequest($type)
    {
        $this->request = new PaymentRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $request_params = array_merge(
            $this->getSuccessiveSavedCardParams(),
            [
                'type' => $type,
                'test_mode' => true,
                'username' => 'some_username',
                'password' => 'some_password',
                'merchant_account' => 'some_merchant_account'
            ]
        );

        $this->request->initialize(
            $request_params
        );
    }

    /**
     * Returns the payment params
     *
     * @return array
     */
    private function getStandardPaymentParams()
    {
        return [
            'amount' => '1.99',
            'currency' => 'EUR',
            'transaction_reference' => '123',
            'card' => new CreditCard(
                [
                    'encrypted_card_data' => 'some_gibberish',
                    'first_name' => 'Simon',
                    'last_name' => 'Silly',
                    'billing_address1' => 'Simon Carmiggeltstraat',
                    'billing_address2' => '6-50',
                    'billing_post_code' => '1011 DJ',
                    'billing_city' => 'Paris',
                    'billing_state' => 'Ille dfrance',
                    'billing_country' => 'FR',
                    'email' => 'some@gmail.com',
                    'shopper_reference' => '123654'
                ]
            )
        ];
    }

    private function getSuccessiveSavedCardParams()
    {
        return [
            'amount' => '1.99',
            'currency' => 'EUR',
            'transaction_reference' => '123',
            'recurring_detail_reference' => '456',
            'card' => new CreditCard(
                [
                    'cvv' => '111',
                    'email' => 'some@gmail.com',
                    'shopper_reference' => '123654'
                ]
            )
        ];
    }

    public function testGetAmountCovertsToMinorUnits()
    {
        $this->getRequest();
        $this->request->setAmount('10.99');
        $this->assertEquals('1099', $this->request->getAmount());
    }

    public function testGetEndpointReturnsTestIfTestMode()
    {
        $this->getRequest();
        $this->request->setTestMode(true);
        $this->assertEquals(
            $this->request->getEndPoint(),
            'https://pal-test.adyen.com/pal/adapter/httppost'
        );
    }

    public function testGetEndpointReturnsLiveIfNoTestMode()
    {
        $this->getRequest();
        $this->request->setTestMode(false);
        $this->assertEquals(
            $this->request->getEndPoint(),
            'https://pal-live.adyen.com/pal/adapter/httppost'
        );
    }

    public function testGetDataReturnsExpectedFieldsAndValuesForStandardPayment()
    {
        $this->getRequest();
        $expected = array_merge(
            $this->getStandardPaymentDetails(),
            [
                'paymentRequest.card.billingAddress.street' => 'Simon Carmiggeltstraat',
                'paymentRequest.card.billingAddress.postalCode' => '1011 DJ',
                'paymentRequest.card.billingAddress.city' => 'Paris',
                'paymentRequest.card.billingAddress.houseNumberOrName' => '6-50',
                'paymentRequest.card.billingAddress.stateOrProvince' => 'Ille dfrance',
                'paymentRequest.card.billingAddress.country' => 'FR',
                'paymentRequest.additionalData.card.encrypted.json' => 'some_gibberish'
            ]
        );

        $this->assertEquals($expected, $this->request->getData());
    }

    public function testGetDataReturnsExpectedFieldsAndValuesForStandardPaymentWithOffset()
    {
        $this->getRequest();
        $this->request->setFraudOffset(-999);
        $expected = array_merge(
            $this->getStandardPaymentDetails(),
            [
                'paymentRequest.card.billingAddress.street' => 'Simon Carmiggeltstraat',
                'paymentRequest.card.billingAddress.postalCode' => '1011 DJ',
                'paymentRequest.card.billingAddress.city' => 'Paris',
                'paymentRequest.card.billingAddress.houseNumberOrName' => '6-50',
                'paymentRequest.card.billingAddress.stateOrProvince' => 'Ille dfrance',
                'paymentRequest.card.billingAddress.country' => 'FR',
                'paymentRequest.additionalData.card.encrypted.json' => 'some_gibberish',
                'paymentRequest.fraudOffset' => -999
            ]
        );

        $this->assertEquals($expected, $this->request->getData());
    }

   public function testGetDataReturnsExpectedFieldsAndValuesForInitialOneClickPayment()
   {
        $this->getRequest(PaymentRequest::ONE_CLICK);
        $expected = array_merge(
            $this->getStandardPaymentDetails(),
            [
                'paymentRequest.additionalData.card.encrypted.json' => 'some_gibberish',
                'paymentRequest.recurring.contract' => PaymentRequest::ONE_CLICK_RECURRING
            ]
        );

        $this->assertEquals($expected, $this->request->getData());
    }

    public function testGetDataReturnsExpectedFieldsAndValuesForSuccessiveOneClickPayment()
    {
        $this->getSuccessiveSavedCardRequest(PaymentRequest::ONE_CLICK);
        $expected = array_merge(
            $this->getStandardPaymentDetails(),
            [
                'paymentRequest.selectedRecurringDetailReference' => '456',
                'paymentRequest.card.cvc' => '111',
                'paymentRequest.recurring.contract' => PaymentRequest::ONE_CLICK
            ]
        );

        $this->assertEquals($expected, $this->request->getData());
    }

    public function testGetDataReturnsExpectedFieldsAndValuesForInitialRecurringPayment()
    {
        $this->getRequest(PaymentRequest::RECURRING);
        $expected = array_merge(
            $this->getStandardPaymentDetails(),
            [
                'paymentRequest.additionalData.card.encrypted.json' => 'some_gibberish',
                'paymentRequest.recurring.contract' => PaymentRequest::ONE_CLICK_RECURRING
            ]
        );

        $this->assertEquals($expected, $this->request->getData());
    }

    public function testGetDataReturnsExpectedFieldsAndValuesForSuccessiveRecurringPayment()
    {
        $this->getSuccessiveSavedCardRequest(PaymentRequest::RECURRING);
        $expected = array_merge(
            $this->getStandardPaymentDetails(),
            [
                'paymentRequest.selectedRecurringDetailReference' => 'LATEST',
                'paymentRequest.recurring.contract' => PaymentRequest::RECURRING,
                'paymentRequest.shopperInteraction' => 'ContAuth'
            ]
        );

        $this->assertEquals($expected, $this->request->getData());
    }

    private function getStandardPaymentDetails()
    {
        return [
            'action' => 'Payment.authorise',
            'paymentRequest.merchantAccount' => 'some_merchant_account',
            'paymentRequest.amount.currency' => 'EUR',
            'paymentRequest.amount.value' => 199,
            'paymentRequest.reference' => '123',
            'paymentRequest.shopperEmail' => 'some@gmail.com',
            'paymentRequest.shopperReference' => '123654'
        ];
    }

    public function testGetDataReturnsExpectedFieldsAndValuesWhen3dSecureIsEnabled()
    {
        $this->getRequest();
        $this->request->set3dSecure(true);
        $expected = [
            'action' => 'Payment.authorise',
            'paymentRequest.merchantAccount' => 'some_merchant_account',
            'paymentRequest.amount.currency' => 'EUR',
            'paymentRequest.amount.value' => 199,
            'paymentRequest.reference' => '123',
            'paymentRequest.shopperEmail' => 'some@gmail.com',
            'paymentRequest.shopperReference' => '123654',
            'paymentRequest.card.billingAddress.street' => 'Simon Carmiggeltstraat',
            'paymentRequest.card.billingAddress.postalCode' => '1011 DJ',
            'paymentRequest.card.billingAddress.city' => 'Paris',
            'paymentRequest.card.billingAddress.houseNumberOrName' => '6-50',
            'paymentRequest.card.billingAddress.stateOrProvince' => 'Ille dfrance',
            'paymentRequest.card.billingAddress.country' => 'FR',
            'paymentRequest.additionalData.card.encrypted.json' => 'some_gibberish',
            'paymentRequest.browserInfo.userAgent' => 'some_agent',
            'paymentRequest.browserInfo.acceptHeader' => 'accept'
        ];

        $this->assertEquals($expected, $this->request->getData());
    }
}
