<?php
namespace Omnipay\Adyen;

use Omnipay\Adyen\Message\CardResponse;
use Omnipay\Adyen\Message\CreditCard;
use Omnipay\Adyen\Message\AuthorizeRequest;
use Omnipay\Adyen\Message\AuthorizeResponse;
use Omnipay\Adyen\Message\NotificationResponse;
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
        $_SERVER = [
            'HTTP_USER_AGENT' => 'some_agent',
            'HTTP_ACCEPT' => 'accept',
            'REMOTE_ADDR' => '127.0.0.1'
        ];
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

    private function getAuthorizeParams()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        return [
            'test_mode' => true,
            'username' => 'some_username',
            'password' => 'some_password',
            'merchant_account' => 'some_merchant_account',
            'first_name' => 'Sally',
            'last_name' => 'Jones',
            'social_security_number' => '818.098.848-10',
            'delivery_days' => '7',
            'shopper_email' => 'sjones@test.com',
            'shopper_reference' => '2468',
            'amount' => '1.99',
            'currency' => 'EUR',
            'transaction_reference' => '123'
        ] + $_SERVER;

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

    /**
     * @dataProvider typeProvider
     */
    public function testInitialSavedCardPurchaseWithAuthorisedTransaction($type)
    {
        $this->setMockHttpResponse('authorisedPayment.txt');
        $payment_params = $this->getPaymentParams() + [
                'type' => $type,
            ];

        $response = $this->gateway->purchase($payment_params)->send();
        $this->assertOneClickResponseIsCorrect($response);
    }

    /**
     * @dataProvider typeProvider
     */
    public function testSuccessiveSavedCardPurchaseWithAuthorisedTransaction($type)
    {
        $this->setMockHttpResponse('authorisedPayment.txt');
        $payment_params = $this->getPaymentParams() + [
            'type' => $type,
            'recurring_detail_reference' => 'some_ref'
        ];
        $payment_params['card']->setEncryptedCardData('');
        $response = $this->gateway->purchase($payment_params)->send();

        $this->assertOneClickResponseIsCorrect($response);
    }

    /**
     * @dataProvider typeProvider
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage One Click and/or Recurring Payments require the email and shopper reference
     */
    public function testSavedCardPurchaseWithoutRequiredParamsThrowsException($type)
    {
        $this->setMockHttpResponse('authorisedPayment.txt');
        $payment_params = $this->getPaymentParams() + ['type' => $type];
        $payment_params['card']->setEmail('');

        $this->gateway->purchase($payment_params)->send();
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

    public function testIsRedirectIf3DSecureIsNeeded()
    {
        $this->setMockHttpResponse('redirectNeeded.txt');

        $payment_parms = $this->getPaymentParams() + ['3d_secure' => 'true'];

        $response = $this->gateway->purchase($payment_parms)->send();

        $this->assertInstanceOf(
            PaymentResponse::class,
            $response
        );
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
    }

    public function testCompletePurchase()
    {
        $this->getHttpRequest()->request->set(
            'MD',
            '123654'
        );

        $this->getHttpRequest()->request->set(
            'PaRes',
            'Some_response'
        );
        $this->setMockHttpResponse('authorisedPayment.txt');

        $response = $this->gateway->completePurchase(['server_info' => $_SERVER])->send();

        $this->assertInstanceOf(
            PaymentResponse::class,
            $response
        );
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }

    public function testCompleteAuthorize()
    {
        $response = $this->gateway->completeAuthorize(
            [
                'raw_data' => $this->getRawNotificationString()
            ]
        )->send();

        $this->assertInstanceOf(
            NotificationResponse::class,
            $response
        );
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('29625848', $response->getTransactionReference());
        $this->assertEquals('23045997725089231', $response->getAcquirerReference());
        $this->assertEquals('2015-11-11T14:08:12.54Z', $response->getEventDate());
        $this->assertEquals('2015-12-01', $response->getExpirationDate());
        $this->assertEquals('690', $response->getValue());
        $this->assertEquals('1714472508923619', $response->getTransactionId());
        $this->assertEquals('2015-11-16', $response->getDueDate());
        $this->assertEquals('BRL', $response->getCurrency());
        $this->assertEquals('boletobancario_bancodobrasil', $response->getPaymentMethod());
        $this->assertEquals(false, $response->isChargeback());
        $this->assertEquals(true, $response->isPending());
        $this->assertEquals(false, $response->isAuthorized());
    }

    /**
     * @return array
     */
    public function typeProvider()
    {
        return [
            ['ONECLICK'],
            ['RECURRING']
        ];
    }

    public function testAuthroizeReturnsCorrectClass()
    {
        $request = $this->gateway->authorize($this->getAuthorizeParams());
        $this->assertInstanceOf(AuthorizeRequest::class, $request);
    }

    public function testAuthroizeReturnsCorrectResponseClass()
    {
        $this->setMockHttpResponse('boletoTransaction.txt');
        $response = $this->gateway->authorize($this->getAuthorizeParams())->send();
        $this->assertInstanceOf(AuthorizeResponse::class, $response);
        $this->assertEquals(
            $response->getRedirectUrl(),
            'https://test.adyen.com/hpp/generationBoleto.shtml'
        );
    }

    public function testAuthroizeReturnsCorrectData()
    {
        $this->setMockHttpResponse('boletoTransaction.txt');
        $response = $this->gateway->authorize($this->getAuthorizeParams())->send();
        $this->assertEquals(
            $response->getRedirectUrl(),
            'https://test.adyen.com/hpp/generationBoleto.shtml'
        );
        $this->assertEquals(
            $response->getExpirationDate(),
            '2013-08-19'
        );
        $this->assertEquals(
            $response->getDueDate(),
            '2013-08-12'
        );
        $this->assertEquals(
            $response->getAdditionalData(),
            'AgABAQClZUyg1NqsD7nN5X1uqN4mabJ7A3FH5LgAUbqDnJ6EAQlnSAVL u7eWIXY/'
        );
    }

    /**
     * Returns the raw notification string
     *
     * @return string
     */
    private function getRawNotificationString()
    {
        return 'originalReference=&reason=&additionalData.boletobancario.url=https%3A%2F%2Flive.adyen.com%2Fhpp%2FgenerationBoleto.shtml%3Fdata%3DBQABAQAVCvpDg5OvfiSeYwiM71K19wWHs3wsCUBaQGmSBGfzxWlGl5drRrN9fFH%252FBPZuI3XXjPNRGJad5w177%252F%252Bvw7AQnZXozTKBBrX2A4%252FFSqcuEZd4u0EbLcExrhYaVTJaTAnbRq9nSnqNR6vRL1HHCutHrC5rrhBYX%252BRc3doX5Up9WQHC1Q04OPtETx9%252F0BVn6G3Ei8wb2Uo1f8VWMv1qjRJW7ZHoIzE3Jolde%252FCp1CKNP7y6%252BqtwCy6dTJOPJ50QHqxnPdxlmanhMsKDLG75Q5XJulF3bOSdfhgNpvfj866WFNhKpdfgaMey0hv%252BmRHQGQ6CYShMjXZe2GwAkJl%252Bl%252BtMEHQpox7hvjqhSqbrxHatsNQAAL%252FTKBBhtA9hJh51scQ6BeGhUgqtfQT%252BbPc4cF2GeZ0XXhyqcpw1OJm%252BkzPimJKJKUpOLmR0crPEMN55yf7RiLkU8T0GMrURfTvRMJlafq2RTiapkKdl7RdTmdlje0jW9gdFdeldEtQh8G%252Bk0oWksCxd8X0QNx6g2NwnbjrDj6qdSih%252BNi6tcRRp76yoQtDJIiC9KrP2ClNdaDvOEzH3DtaUmBjRMpJ7BhwNPPV%252FRnVlPE%252F4DHQUA7SFC8G6UfBqnjyGzRkikvDNlv6idrRhOeBTCVsjXx1T2SDXT2uigBjVYOu13VFP88jAsDZ90egLLPCpzQ0vRLOFcoATEtrnEuHB0pq6PBN0U9TjC83iW9oUdLGCudgCgtaj&merchantAccountCode=YourMerchantAccount&eventCode=PENDING&operations=&additionalData.boletobancario.barCodeReference=00190.00009+02304.599778+25089.231184+6+66140000000690&additionalData.boletobancario.data=BQABAQAVCvpDg5OvfiSeYwiM71K19wWHs3wsCUBaQGmSBGfzxWlGl5drRrN9fFH%2FBPZuI3XXjPNRGJad5w177%2F%2Bvw7AQnZXozTKBBrX2A4%2FFSqcuEZd4u0EbLcExrhYaVTJaTAnbRq9nSnqNR6vRL1HHCutHrC5rrhBYX%2BRc3doX5Up9WQHC1Q04OPtETx9%2F0BVn6G3Ei8wb2Uo1f8VWMv1qjRJW7ZHoIzE3Jolde%2FCp1CKNP7y6%2BqtwCy6dTJOPJ50QHqxnPdxlmanhMsKDLG75Q5XJulF3bOSdfhgNpvfj866WFNhKpdfgaMey0hv%2BmRHQGQ6CYShMjXZe2GwAkJl%2Bl%2BtMEHQpox7hvjqhSqbrxHatsNQAAL%2FTKBBhtA9hJh51scQ6BeGhUgqtfQT%2BbPc4cF2GeZ0XXhyqcpw1OJm%2BkzPimJKJKUpOLmR0crPEMN55yf7RiLkU8T0GMrURfTvRMJlafq2RTiapkKdl7RdTmdlje0jW9gdFdeldEtQh8G%2Bk0oWksCxd8X0QNx6g2NwnbjrDj6qdSih%2BNi6tcRRp76yoQtDJIiC9KrP2ClNdaDvOEzH3DtaUmBjRMpJ7BhwNPPV%2FRnVlPE%2F4DHQUA7SFC8G6UfBqnjyGzRkikvDNlv6idrRhOeBTCVsjXx1T2SDXT2uigBjVYOu13VFP88jAsDZ90egLLPCpzQ0vRLOFcoATEtrnEuHB0pq6PBN0U9TjC83iW9oUdLGCudgCgtaj&success=true&paymentMethod=boletobancario_bancodobrasil&currency=BRL&additionalData.boletobancario.dueDate=2015-11-16&pspReference=1714472508923619&merchantReference=29625848&value=690&additionalData.boletobancario.expirationDate=2015-12-01&live=true&eventDate=2015-11-11T14%3A08%3A12.54Z&additionalData.acquirerReference=23045997725089231';
    }
}
