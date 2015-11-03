<?php
namespace Omnipay\Adyen;

use Omnipay\Adyen\Message\CreditCard;
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
        $this->assertInstanceOf('Omnipay\Adyen\Message\PaymentRequest', $request);
    }

    public function testPurchaseWithAuthorisedTransaction()
    {
        $this->setMockHttpResponse('authorisedPayment.txt');
        $response = $this->gateway->purchase($this->getPaymentParams())->send();

        $this->assertInstanceOf(
            '\Omnipay\Adyen\Message\PaymentResponse',
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

    public function testPurchaseWithRefusedTransaction()
    {
        $this->setMockHttpResponse('refusedPayment.txt');
        $response = $this->gateway->purchase($this->getPaymentParams())->send();

        $this->assertInstanceOf(
            '\Omnipay\Adyen\Message\PaymentResponse',
            $response
        );
        $this->assertFalse($response->isSuccessful());
        $this->assertEquals(
            'Refused',
            $response->getMessage()
        );
    }
}
