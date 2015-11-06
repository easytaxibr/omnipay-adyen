<?php
namespace Omnipay\Adyen;

use Omnipay\Adyen\Message\CardResponse;
use Omnipay\Adyen\Message\CreditCard;
use Omnipay\Adyen\Message\PaymentRequest;
use Omnipay\Adyen\Message\PaymentResponse;
use Omnipay\Adyen\Message\RefundRequest;
use Omnipay\Adyen\Message\RefundResponse;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway(
            $this->getHttpClient(),
            $this->getHttpRequest()
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
                    'first_name' => 'Dimitriou',
                    'last_name' => 'Androas',
                    'billing_address1' => 'Simon Carmiggeltstraat',
                    'billing_address2' => '6-50',
                    'billing_post_code' => '1011 DJ',
                    'billing_city' => 'Paris',
                    'billing_state' => 'Ille dfrance',
                    'billing_country' => 'FR',
                    'email' => 'dandroas@gmail.com',
                    'shopper_reference' => '123654'
                ]
            )
        ];
    }

    public function testPurchaseReturnsCorrectClass()
    {
        $request = $this->gateway->purchase($this->getPaymentParams());
        $this->assertInstanceOf(PaymentRequest::class, $request);
    }

    public function testPurchaseWithAuthorisedTransaction()
    {
        $this->setMockHttpResponse('authorisedPayment.txt');
        $response = $this->gateway->purchase($this->getPaymentParams())->send();

        $this->assertOneClickResponseIsCorrect($response);
    }

    public function testInitialOneClickPurchaseWithAuthorisedTransaction()
    {
        $this->setMockHttpResponse('authorisedPayment.txt');
        $payment_parms = $this->getPaymentParams() + [
            'type' => 'ONECLICK',
        ];

        $response = $this->gateway->purchase($payment_parms)->send();

        $this->assertOneClickResponseIsCorrect($response);
    }

    public function testSuccessiveOneClickPurchaseWithAuthorisedTransaction()
    {
        $this->setMockHttpResponse('authorisedPayment.txt');
        $payment_parms = $this->getPaymentParams() + [
                'type' => 'ONECLICK',
                'recurring_detail_reference' => 'some_ref'
            ];

        $response = $this->gateway->purchase($payment_parms)->send();

        $this->assertOneClickResponseIsCorrect($response);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage One Click and/or Recurring Payments require the email and shopper reference
     */
    public function testOneClickPurchaseWithoutRequiredParamsThrowsException()
    {
        $this->setMockHttpResponse('authorisedPayment.txt');
        $payment_parms = $this->getPaymentParams() + ['type' => 'ONECLICK'];
        $payment_parms['card']->setEmail('');

        $this->gateway->purchase($payment_parms)->send();
    }

    public function testPurchaseWithRefusedTransaction()
    {
        $this->setMockHttpResponse('refusedPayment.txt');
        $response = $this->gateway->purchase($this->getPaymentParams())->send();

        $this->assertInstanceOf(
            PaymentResponse::class,
            $response
        );
        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(
            'Refused',
            $response->getMessage()
        );
    }

    public function testRefundReturnsCorrectClass()
    {
        $request = $this->gateway->refund([]);
        $this->assertInstanceOf(RefundRequest::class, $request);
    }

    public function testRefundWithSuccessfulTransaction()
    {
        $this->setMockHttpResponse('successfulRefund.txt');
        $response = $this->gateway->refund(
            [
                'merchant_account' => 'some_merchant_account',
                'transaction_id' => 'some_transaction_ref'
            ]
        )->send();

        $this->assertInstanceOf(
            RefundResponse::class,
            $response
        );
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(
            'some_success_ref',
            $response->getTransactionId()
        );
        $this->assertEquals(
            '[cancelOrRefund-received]',
            $response->getMessage()
        );
    }

    public function testGetCardWithSuccessfulTransaction()
    {
        $this->setMockHttpResponse('savedCard.txt');
        $response = $this->gateway->getCard(
            [
                'merchant_account' => 'some_merchant_account',
                'transaction_id' => 'some_transaction_ref',
                'contract_type' => 'ONECLICK',
                'shopper_reference' => '123654'
            ]
        )->send();

        $this->assertInstanceOf(
            CardResponse::class,
            $response
        );
        $this->assertTrue($response->isSuccessful());

        $this->assertEquals(
            'some_ref',
            $response->getRecurringDetailReference()
        );

        $this->assertEquals(
            'some@gmail.com',
            $response->getShopperEmail()
        );

        $this->assertEquals(
            '123654',
            $response->getShopperReference()
        );
    }

    /**
     * @param $response
     */
    private function assertOneClickResponseIsCorrect($response)
    {
        $this->assertInstanceOf(
            PaymentResponse::class,
            $response
        );
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(
            'some_auth_ref',
            $response->getTransactionId()
        );
        $this->assertEquals(
            '123456',
            $response->getCode()
        );
    }
}
