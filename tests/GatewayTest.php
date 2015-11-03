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

    public function testPurchaseReturnsCorrectClass()
    {
        $card = new CreditCard(
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
        );

        $request = $this->gateway->purchase(
            [
                'amount' => '1.99',
                'currency' => 'EUR',
                'transaction_reference' => '123',
                'card' => $card
            ]
        );
        $this->assertInstanceOf('Omnipay\Adyen\Message\PaymentRequest', $request);
    }
}
