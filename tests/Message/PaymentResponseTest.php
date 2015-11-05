<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

/**
 * Class PaymentResponseTest
 * @package Omnipay\Adyen\Message
 */
class PaymentResponseTest extends TestCase
{
    public function setUp()
    {
        $this->request = new PaymentRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
    }

    /**
     * Sets the response object
     *
     * @param string $result_code
     */
    private function setResponse($result_code)
    {
        $this->response = new PaymentResponse(
            $this->request,
            [
                'paymentResult_resultCode' => $result_code,
                'paymentResult_refusalReason' => 'Not Allowed',
                'paymentResult_pspReference' => 'some_reference',
                'paymentResult_authCode' => '010'
            ]
        );
    }

    public function testIsSuccessfulReturnsTrueWhenPaymentAuthorised()
    {
        $this->setResponse('Authorised');
        $this->assertEquals(true, $this->response->isSuccessful());
    }

    public function testIsSuccessfulReturnsFalseWhenPaymentRefused()
    {
        $this->setResponse('Refused');
        $this->assertEquals(false, $this->response->isSuccessful());
    }

    public function testGetMessageReturnsCorrectMessage()
    {
        $this->setResponse('refused');
        $this->assertEquals('Not Allowed', $this->response->getMessage());
    }

    public function testGetTransactionId()
    {
        $this->setResponse('Authorised');
        $this->assertEquals('some_reference', $this->response->getTransactionId());
    }

    public function testGetCode()
    {
        $this->setResponse('Refused');
        $this->assertEquals('010', $this->response->getCode());
    }
}
