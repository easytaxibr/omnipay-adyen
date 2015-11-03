<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

/**
 * Class RefundRequestTest
 * @package Omnipay\Adyen\Message
 */
class RefundRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new RefundRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $this->request->initialize(
            [
                'merchant_account' => 'some_merchant_account',
                'transaction_id' => 'some_transaction_ref'
            ]
        );
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
            'action' => 'Payment.cancelOrRefund',
            'modificationRequest.merchantAccount' => 'some_merchant_account',
            'modificationRequest.originalReference' => 'some_transaction_ref'
        ];

        $this->assertEquals($expected, $this->request->getData());
    }
}
