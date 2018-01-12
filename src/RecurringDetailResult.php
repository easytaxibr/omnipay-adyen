<?php

namespace Omnipay\Adyen;

class RecurringDetailResult
{
    private $parsedData = [];

    public function __construct($parsedData)
    {
        $this->parsedData = $parsedData;
    }

    public static function fromArray($dataToParse)
    {
        $parsedData = [];
        foreach ($dataToParse as $key => $value) {
            $parsedData = self::parseRecurringDetailsResult($key, $parsedData, $value);
        }

        return new RecurringDetailResult($parsedData['recurringDetailsResult']);
    }

    public function getDetails()
    {
        return new RecurringDetailCollection($this->parsedData['details']);
    }

    public function getCreationDate()
    {
        return $this->parsedData['creationDate'];
    }

    public function getLastKnownShopperEmail()
    {
        return $this->parsedData['lastKnownShopperEmail'];
    }

    public function getShopperReference()
    {
        return $this->parsedData['shopperReference'];
    }

    private static function parseRecurringDetailsResult($key, $hash, $value)
    {
        $explodeResult = explode('_', $key, 2);

        if (count($explodeResult) == 1) {
            $hash[$explodeResult[0]] = $value;
        } else {
            list($first, $rest) = $explodeResult;
            $hash[$first] = self::parseRecurringDetailsResult(
                $rest,
                isset($hash[$first]) ? $hash[$first] : [],
                $value
            );
        }

        return $hash;
    }
}
