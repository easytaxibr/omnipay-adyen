<?php

namespace Omnipay\Adyen;

class RecurringDetailCollection
{
    private $details;

    public function add(RecurringDetail $recurringDetail)
    {
         $this->details[] = $recurringDetail;
    }

    public function getByCardNumber($number)
    {
        foreach($this->details as $recurringDetail) {
            $card = $recurringDetail->getCard();
            if ($card['number'] == $number) {
                return $recurringDetail;
            }
        }

        return null;
    }
}
