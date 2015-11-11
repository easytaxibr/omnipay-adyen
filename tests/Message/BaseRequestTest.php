<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

/**
 * Class PaymentRequestTest
 * @package Omnipay\Adyen\Message
 */
class BaseRequestTest extends TestCase
{
    public function setUp($type = null)
    {
        $this->request = new BaseRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $this->request->initialize([
            'currency' => 'EUR'
        ]);
    }

    public function testGetData()
    {
        $this->assertEquals($this->request->getData(), []);
    }

    public function adapterUnitsDataProvider()
    {
        return [
            [
                '11.11',
                '1111'
            ],
            [
                '1111.11',
                '111111'
            ],
            [
                '100.00',
                '10000'
            ]
        ];
    }

    /**
     * @dataProvider adapterUnitsDataProvider
     */
    public function testConvertPriceToMinorUnits($amount, $expected)
    {
        $this->assertEquals($this->request->formatCurrency($amount), $expected);
    }
}
