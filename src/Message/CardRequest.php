<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Class CardRequest
 * @package Omnipay\Adyen\Message
 */
class CardRequest extends AbstractRequest
{
    use GatewayAccessorTrait;

    /**
     * Sets the shopper reference
     *
     * @param string $reference
     */
    public function setShopperReference($reference)
    {
        $this->setParameter('shopper_reference', $reference);
    }

    /**
     * Returns the shopper reference
     *
     * @return string
     */
    public function getShopperReference()
    {
        return $this->getParameter('shopper_reference');
    }

    /**
     * Sets the contract type
     *
     * @param string $type
     */
    public function setContractType($type)
    {
        $this->setParameter('contract_type', $type);
    }

    /**
     * Returns the contract type.
     *
     * @return string
     */
    public function getContractType()
    {
        return $this->getParameter('contract_type');
    }

    /**
     * Returns the getCard parameters
     *
     * @return array
     */
    public function getData()
    {
        return [
            "action" => "Recurring.listRecurringDetails",
            "recurringDetailsRequest.merchantAccount" => $this->getMerchantAccount(),
            "recurringDetailsRequest.shopperReference" => $this->getShopperReference(),
            "recurringDetailsRequest.recurring.contract" => $this->getContractType()
        ];
    }

    /**
     * Does the getCard request to adyen
     *
     * @param array $data
     * @return CardResponse
     */
    public function sendData($data)
    {
        $response = $this->httpClient->post(
            $this->getEndpoint(),
            [],
            http_build_query($data),
            [
                'auth' => [$this->getUsername(), $this->getPassword()]
            ]
        )->send();

        $response_data = [];
        parse_str($response->getBody(true), $response_data);

        return $this->response = new CardResponse($this, $response_data);
    }
}
