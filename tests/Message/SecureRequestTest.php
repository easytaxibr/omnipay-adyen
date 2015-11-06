<?php
namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CardRequestTest
 * @package Omnipay\Adyen\Message
 */
class SecureRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new SecureRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $this->request->initialize(
            [
                'merchant_account' => 'some_merchant'
            ]
        );
    }

    public function testGetData()
    {
        $_SERVER = [
            'HTTP_USER_AGENT' => 'some_agent',
            'HTTP_ACCEPT' => 'accept',
            'REMOTE_ADDR' => '127.0.0.1'
        ];

        $this->getHttpRequest()->request->set(
            'MD',
            '123654'
        );

        $this->getHttpRequest()->request->set(
            'PaRes',
            'Some_response'
        );

        $expected = [
            "action" => "Payment.authorise3d",
            "paymentRequest3d.merchantAccount" => 'some_merchant',
            "paymentRequest3d.browserInfo.userAgent" => $_SERVER['HTTP_USER_AGENT'],
            "paymentRequest3d.browserInfo.acceptHeader" => $_SERVER['HTTP_ACCEPT'],
            "paymentRequest3d.md" => '123654',
            "paymentRequest3d.paResponse" => 'Some_response',
            "paymentRequest3d.shopperIP" => $_SERVER['REMOTE_ADDR']
        ];
        $this->assertEquals($expected, $this->request->getData());
    }
}
