<?php

namespace Omnipay\Adyen;

use PHPUnit\Framework\TestCase;

class RecurringDetailCollectionTest extends TestCase
{
    private $recurringDetailCollection;

    public function setUp()
    {
        $this->recurringDetailCollection = new RecurringDetailCollection([
            new RecurringDetail([
                'card' => [
                    'expiryMonth' => '10',
                    'expiryYear' => '2021',
                    'holderName' => 'Easy taxi',
                    'number' => '1407'
                ],
                'creationDate' => '2017-11-23T16:28:27+01:00'
            ]),
            new RecurringDetail([
                'card' => [
                    'expiryMonth' => '10',
                    'expiryYear' => '2021',
                    'holderName' => 'Easy taxi',
                    'number' => '1407'
                ],
                'creationDate' => '2017-11-23T16:28:27+01:00'
            ])
        ]);
    }

    public function testGetCardByNumber()
    {
        $number = '1407';
        $this->assertEquals(
            $this->recurringDetailCollection->getCardByNumber($number),
            [
                'expiryMonth' => '10',
                'expiryYear' => '2021',
                'holderName' => 'Easy taxi',
                'number' => '1407'
            ]
        );
    }
}
