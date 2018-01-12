<?php

namespace Omnipay\Adyen;

use PHPUnit\Framework\TestCase;

class RecurringDetailResultTest extends TestCase
{
    private $adyenApiResponse;
    private $parsedAdyenApiResponse;

    public function setUp()
    {
        $this->adyenApiResponse = [
            'recurringDetailsResult_creationDate' => '2017-11-23T16:28:27+01:00',
            'recurringDetailsResult_details_0_additionalData_cardBin' => '530551',
            'recurringDetailsResult_details_0_alias' => '09876543321123456',
            'recurringDetailsResult_details_0_aliasType' => 'Default',
            'recurringDetailsResult_details_0_card_expiryMonth' => '10',
            'recurringDetailsResult_details_0_card_expiryYear' => '2021',
            'recurringDetailsResult_details_0_card_holderName' => 'Easy taxi',
            'recurringDetailsResult_details_0_card_number' => '1407',
            'recurringDetailsResult_details_0_creationDate' => '2017-11-23T16:28:27+01:00',
            'recurringDetailsResult_details_0_firstPspReference' => 'asdfghjkl1234567',
            'recurringDetailsResult_details_0_paymentMethodVariant' => 'mccredit',
            'recurringDetailsResult_details_0_recurringDetailReference' => '1234567890123456',
            'recurringDetailsResult_details_0_variant' => 'mc',
            'recurringDetailsResult_lastKnownShopperEmail' => 'p@easytaxi.com.br',
            'recurringDetailsResult_shopperReference' => '123456789012345678901234'
        ];

        $this->parsedAdyenApiResponse =[
            "creationDate" => "2017-11-23T16:28:27+01:00",
            "details" => [
                [
                    "additionalData" => [
                        "cardBin" => "530551"
                    ],
                    "alias" => "09876543321123456",
                    "aliasType" => "Default",
                    "card" => [
                        "expiryMonth" => "10",
                        "expiryYear" => "2021",
                        "holderName" => "Easy taxi",
                        "number" => "1407"
                    ],
                    "creationDate" => "2017-11-23T16:28:27+01:00",
                    "firstPspReference" => "asdfghjkl1234567",
                    "paymentMethodVariant" => "mccredit",
                    "recurringDetailReference" => "1234567890123456",
                    "variant" => "mc"
                ]
            ],
            "lastKnownShopperEmail" => "p@easytaxi.com.br",
            "shopperReference" => "123456789012345678901234"
        ];
    }

    public function testConstructFromArray()
    {
        $this->assertEquals(
            RecurringDetailResult::fromArray($this->adyenApiResponse),
            new RecurringDetailResult($this->parsedAdyenApiResponse)
        );
    }

    public function testGetCreationDate()
    {
        $recurringDetail = RecurringDetailResult::fromArray($this->adyenApiResponse);
        $this->assertEquals(
            $recurringDetail->getCreationDate(),
            "2017-11-23T16:28:27+01:00"
        );
    }

    public function testGetLastKnownShopperEmail()
    {
        $recurringDetail = RecurringDetailResult::fromArray($this->adyenApiResponse);
        $this->assertEquals(
            $recurringDetail->getLastKnownShopperEmail(),
            "p@easytaxi.com.br"
        );
    }

    public function testGetShopperReference()
    {
        $recurringDetail = RecurringDetailResult::fromArray($this->adyenApiResponse);
        $this->assertEquals(
            $recurringDetail->getShopperReference(),
            "123456789012345678901234"
        );
    }
}
