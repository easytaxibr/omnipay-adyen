<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

/**
 * Class CardRequestTest
 * @package Omnipay\Adyen\Message
 */
class CardRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new CardRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $this->request->initialize(
            [
                'merchant_account' => 'some_merchant',
                'shopper_reference' => '123654',
                'contract_type' => 'ONECLICK'
            ]
        );
    }

    public function testGetData()
    {
        $expected = [
            'action' => 'Recurring.listRecurringDetails',
            'recurringDetailsRequest.merchantAccount' => 'some_merchant',
            'recurringDetailsRequest.shopperReference' => '123654',
            'recurringDetailsRequest.recurring.contract' => 'ONECLICK',
        ];
        $this->assertEquals($expected, $this->request->getData());
    }
}
