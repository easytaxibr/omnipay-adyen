<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

/**
 * Class PaymentRequestTest
 * @package Omnipay\Adyen\Message
 */
class PaymentRequestTest extends TestCase
{
    public function setUp()
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
            $this->getPaymentParams(),
            [
                'test_mode' => true,
                'username' => 'some_username',
                'password' => 'some_password',
                'merchant_account' => 'some_merchant_account'
            ],
            $_SERVER
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
    private function getPaymentParams()
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

    public function testGetAmountCovertsToMinorUnits()
    {
        $this->request->setAmount('10.99');
        $this->assertEquals('1099', $this->request->getAmount());
    }

    public function testGetEndpointReturnsTestIfTestMode()
    {
        $this->request->setTestMode(true);
        $this->assertEquals(
            $this->request->getEndPoint(),
            'https://pal-test.adyen.com/pal/adapter/httppost'
        );
    }

    public function testGetEndpointReturnsLiveIfNoTestMode()
    {
        $this->request->setTestMode(false);
        $this->assertEquals(
            $this->request->getEndPoint(),
            'https://pal-live.adyen.com/pal/adapter/httppost'
        );
    }

    public function testGetDataReturnsExpectedFieldsAndValues()
    {
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

    public function testGetDataReturnsExpectedFieldsAndValuesWhen3dSecureIsEnabled()
    {
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
