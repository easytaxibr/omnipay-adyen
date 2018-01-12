<?php

namespace Omnipay\Adyen;

use PHPUnit\Framework\TestCase;

class RecurringDetailTest extends TestCase
{
    private $recurringDetail;

    public function setUp()
    {
        $this->recurringDetail = new RecurringDetail($this->adyenResponse());
    }

    private function adyenResponse()
    {
        return [
            'additionalData' => ['cardBin' => '530551'],
            'alias' => '1234567890098765',
            'aliasType' => 'Default',
            'card' => [
                'expiryMonth' => '10',
                'expiryYear' => '2021',
                'holderName' => 'Easy taxi',
                'number' => '1407'
            ],
            'creationDate' => '2017-11-23T16:28:27+01:00',
            'firstPspReference' => 'asdfghjkl1234567',
            'paymentMethodVariant' => 'mccredit',
            'recurringDetailReference' => '1234567890123456',
            'variant' => 'mc'
        ];
    }

    public function testGetAdditionalData()
    {
        $this->assertEquals(
            $this->recurringDetail->getAdditionalData(),
            ['cardBin' => '530551']
        );
    }

    public function testGetCard()
    {
        $this->assertEquals(
            $this->recurringDetail->getCard(),
            [
                'expiryMonth' => '10',
                'expiryYear' => '2021',
                'holderName' => 'Easy taxi',
                'number' => '1407'
            ]
        );
    }

    public function testGetCardWhenUserHasNoCardRegistered()
    {
        $adyenResponseWithNoCards = $this->adyenResponse();
        unset($adyenResponseWithNoCards['card']);

        $recurringDetail = new RecurringDetail($adyenResponseWithNoCards);
        $this->assertEquals(
            $recurringDetail->getCard(),
            []
        );
    }

    public function testGetAlias()
    {
        $this->assertEquals($this->recurringDetail->getAlias(), '1234567890098765');
    }

    public function testGetAliasType()
    {
        $this->assertEquals($this->recurringDetail->getAliasType(), 'Default');
    }

    public function testGetCreationDate()
    {
        $this->assertEquals($this->recurringDetail->getCreationDate(), '2017-11-23T16:28:27+01:00');
    }

    public function testFirstPspReference()
    {
        $this->assertEquals($this->recurringDetail->getFirstPspReference(), 'asdfghjkl1234567');
    }

    public function testGetPaymentMethodVariant()
    {
        $this->assertEquals($this->recurringDetail->getPaymentMethodVariant(), 'mccredit');
    }

    public function testGetRecurringDetailReference()
    {
        $this->assertEquals($this->recurringDetail->getRecurringDetailReference(), '1234567890123456');
    }

    public function testGetVariant()
    {
        $this->assertEquals($this->recurringDetail->getVariant(), 'mc');
    }

}
