<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

/**
 * Class PaymentRequestTest
 * @package Omnipay\Adyen\Message
 */
class PaymentRequestTest extends TestCase
{
    private function getStandardRequest()
    {
        $this->request = new PaymentRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $request_params = array_merge(
            $this->getStandardPaymentParams(),
            [
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

    private function getInitialOneClickRequest()
    {
        $this->request = new PaymentRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $request_params = array_merge(
            $this->getStandardPaymentParams(),
            [
                'type' => PaymentRequest::ONE_CLICK,
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

    private function getInitialRecurringRequest()
    {
        $this->request = new PaymentRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $request_params = array_merge(
            $this->getStandardPaymentParams(),
            [
                'type' => PaymentRequest::RECURRING,
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


    private function getSuccessiveOneClickRequest()
    {
        $this->request = new PaymentRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $request_params = array_merge(
            $this->getSuccessiveOneClickParams(),
            [
                'type' => PaymentRequest::ONE_CLICK,
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

    private function getSuccessiveRecurringRequest()
    {
        $this->request = new PaymentRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $request_params = array_merge(
            $this->getSuccessiveOneClickParams(),
            [
                'type' => PaymentRequest::RECURRING,
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

    private function getSuccessiveOneClickParams()
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
        $this->getStandardRequest();
        $this->request->setAmount('10.99');
        $this->assertEquals('1099', $this->request->getAmount());
    }

    public function testGetEndpointReturnsTestIfTestMode()
    {
        $this->getStandardRequest();
        $this->request->setTestMode(true);
        $this->assertEquals(
            $this->request->getEndPoint(),
            'https://pal-test.adyen.com/pal/adapter/httppost'
        );
    }

    public function testGetEndpointReturnsLiveIfNoTestMode()
    {
        $this->getStandardRequest();
        $this->request->setTestMode(false);
        $this->assertEquals(
            $this->request->getEndPoint(),
            'https://pal-live.adyen.com/pal/adapter/httppost'
        );
    }

    public function testGetDataReturnsExpectedFieldsAndValuesForStandardPayment()
    {
        $this->getStandardRequest();
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
            'paymentRequest.additionalData.card.encrypted.json' => 'some_gibberish'
        ];

        $this->assertEquals($expected, $this->request->getData());
    }

    public function testGetDataReturnsExpectedFieldsAndValuesForInitialOneClickPayment()
    {
        $this->getInitialOneClickRequest();
        $expected = [
            'action' => 'Payment.authorise',
            'paymentRequest.merchantAccount' => 'some_merchant_account',
            'paymentRequest.amount.currency' => 'EUR',
            'paymentRequest.amount.value' => 199,
            'paymentRequest.reference' => '123',
            'paymentRequest.shopperEmail' => 'some@gmail.com',
            'paymentRequest.shopperReference' => '123654',
            'paymentRequest.additionalData.card.encrypted.json' => 'some_gibberish',
            'paymentRequest.recurring.contract' => 'ONECLICK'
        ];

        $this->assertEquals($expected, $this->request->getData());
    }

    public function testGetDataReturnsExpectedFieldsAndValuesForSuccessiveOneClickPayment()
    {
        $this->getSuccessiveOneClickRequest();
        $expected = [
            'action' => 'Payment.authorise',
            'paymentRequest.merchantAccount' => 'some_merchant_account',
            'paymentRequest.amount.currency' => 'EUR',
            'paymentRequest.amount.value' => 199,
            'paymentRequest.reference' => '123',
            'paymentRequest.shopperEmail' => 'some@gmail.com',
            'paymentRequest.shopperReference' => '123654',
            'paymentRequest.selectedRecurringDetailReference' => '456',
            'paymentRequest.card.cvc' => '111',
            'paymentRequest.recurring.contract' => 'ONECLICK'
        ];

        $this->assertEquals($expected, $this->request->getData());
    }

    public function testGetDataReturnsExpectedFieldsAndValuesForInitialRecurringPayment()
    {
        $this->getInitialRecurringRequest();
        $expected = [
            'action' => 'Payment.authorise',
            'paymentRequest.merchantAccount' => 'some_merchant_account',
            'paymentRequest.amount.currency' => 'EUR',
            'paymentRequest.amount.value' => 199,
            'paymentRequest.reference' => '123',
            'paymentRequest.shopperEmail' => 'some@gmail.com',
            'paymentRequest.shopperReference' => '123654',
            'paymentRequest.additionalData.card.encrypted.json' => 'some_gibberish',
            'paymentRequest.recurring.contract' => 'RECURRING'
        ];

        $this->assertEquals($expected, $this->request->getData());
    }

    public function testGetDataReturnsExpectedFieldsAndValuesForSuccessiveRecurringPayment()
    {
        $this->getSuccessiveRecurringRequest();
        $expected = [
            'action' => 'Payment.authorise',
            'paymentRequest.merchantAccount' => 'some_merchant_account',
            'paymentRequest.amount.currency' => 'EUR',
            'paymentRequest.amount.value' => 199,
            'paymentRequest.reference' => '123',
            'paymentRequest.shopperEmail' => 'some@gmail.com',
            'paymentRequest.shopperReference' => '123654',
            'paymentRequest.selectedRecurringDetailReference' => 'LATEST',
            'paymentRequest.recurring.contract' => 'RECURRING',
            'paymentRequest.shopperInteraction' => 'ContAuth'
        ];

        $this->assertEquals($expected, $this->request->getData());
    }
}
