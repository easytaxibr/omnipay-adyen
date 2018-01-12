<?php

namespace Omnipay\Adyen;

class RecurringDetailCollection
{
    private $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    public function getAdditionalData()
    {
        return $this->adyenResponse['additionalData'];
    }

    public function getCardByNumber($number)
    {
        foreach($this->details as $recurringDetail) {
            $card = $recurringDetail->getCard();
            if ($card['number'] == $number) {
                return $card;
            }
        }

        return null;
    }
}
