<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

/**
 * Class PaymentResponseTest
 * @package Omnipay\Adyen\Message
 */
class AuthorizeResponseTest extends TestCase
{
    public function setUp()
    {
        $this->request = new AuthorizeRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
    }

    /**
     * Sets the response object
     *
     * @param string $result_code
     * @param  array $secure_data_info
     */
    private function setResponse(
        $result_url = 'https://test.adyen.com/hpp/generationBoleto.shtml',
        $result_code = 'Received'
    ) {
        $this->response = new AuthorizeResponse(
            $this->request,
            [
                'additionalData_boletobancario_url' => $result_url,
                'additionalData_boletobancario_data' => 'Some_data',
                'additionalData_boletobancario_expirationDate' => '2015-01-05',
                'additionalData_boletobancario_dueDate' => '2015-01-01',
                'pspReference' => '8813760397300101',
                'resultCode' => $result_code
            ]
        );
    }

    public function testIsSuccessful()
    {
        $this->setResponse();
        $this->assertFalse($this->response->isSuccessful());
    }

    public function testIsRedirect()
    {
        $this->setResponse();
        $this->assertTrue($this->response->isRedirect());
    }

    public function testIsRedirectReturnsFalseIfResultCodeIsNotReceived()
    {
        $this->setResponse('https://test.adyen.com/hpp/generationBoleto.shtml', '');
        $this->assertFalse($this->response->isRedirect());
    }

    public function testIsRedirectReturnsFalseIfRedirectUrlIsEmpty()
    {
        $this->setResponse('');
        $this->assertFalse($this->response->isRedirect());
    }

    public function testGetTransactionId()
    {
        $this->setResponse();
        $this->assertEquals('8813760397300101', $this->response->getTransactionId());
    }

    public function testGetAdditionalData()
    {
        $this->setResponse();
        $this->assertEquals('Some_data', $this->response->getAdditionalData());
    }

    public function testGetExpirationDate()
    {
        $this->setResponse();
        $this->assertEquals('2015-01-05', $this->response->getExpirationDate());
    }

    public function testGetDueDate()
    {
        $this->setResponse();
        $this->assertEquals('2015-01-01', $this->response->getDueDate());
    }

    public function testGetResultCode()
    {
        $this->setResponse();
        $this->assertEquals('Received', $this->response->getResultCode());
    }

    public function testGetRedirctUrl()
    {
        $this->setResponse();
        $this->assertEquals(
            'https://test.adyen.com/hpp/generationBoleto.shtml',
            $this->response->getRedirectUrl()
        );
    }
}
