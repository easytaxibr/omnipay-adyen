<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

/**
 * Class PaymentResponseTest
 * @package Omnipay\Adyen\Message
 */
class NotificationResponseTest extends TestCase
{
    public function setUp()
    {
        $this->request = new NotificationRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );
    }

    /**
     * Sets the response object
     *
     * @param  array $data
     */
    private function setResponse($data)
    {
        $this->response = new NotificationResponse(
            $this->request,
            $data
        );
    }

    public function testIsLiveReturnsTrueWhenSetToTrue()
    {
        $this->setResponse(['live' => 'true']);
        $this->assertEquals(true, $this->response->isLive());
    }
}
