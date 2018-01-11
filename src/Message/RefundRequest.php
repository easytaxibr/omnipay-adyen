<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Class RefundRequest
 * @package Omnipay\Adyen\Message
 */
class RefundRequest extends AbstractRequest
{
    use GatewayAccessorTrait;

    /**
     * Does the refund request to adyen server,
     * and returns a Response object
     *
     * @param array $data
     * @return RefundResponse
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function sendData($data)
    {
        $response = $this->httpClient->post(
            $this->getEndpoint(),
            [],
            http_build_query($data),
            [
                'auth' => [$this->getUsername(),$this->getPassword()]
            ]
        );

        $response_data = [];
        parse_str($response->getBody(true), $response_data);

        return $this->response = new RefundResponse($this, $response_data);
    }

    /**
     * Returns the data required for the request
     * to be created
     *
     * @return array
     */
    public function getData()
    {
        return [
            'action' => "Payment.cancelOrRefund",
            'modificationRequest.merchantAccount' => $this->getMerchantAccount(),
            'modificationRequest.originalReference' => $this->getTransactionId(),
        ];
    }
}
