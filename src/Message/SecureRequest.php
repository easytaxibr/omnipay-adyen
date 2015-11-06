<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Class SecureRequest - Used to make a 3D secure request
 * @package Omnipay\Adyen\Message
 */
class SecureRequest extends AbstractRequest
{
    use GatewayAccessorTrait;

    /**
     * Does the 3d secure request to adyen server,
     * and returns a PaymentResponse object
     *
     * @param array $data
     * @return PaymentResponse
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
        )->send();

        $response_data = [];
        parse_str($response->getBody(true), $response_data);

        return $this->response = new PaymentResponse($this, $response_data);
    }

    /**
     * Returns the data required for the request
     * to be created
     *
     * @return array
     */
    public function getData()
    {
        $returned_data = $this->httpRequest->request->all();
        return [
            "action" => "Payment.authorise3d",
            "paymentRequest3d.merchantAccount" => $this->getMerchantAccount(),
            "paymentRequest3d.browserInfo.userAgent" => $_SERVER['HTTP_USER_AGENT'],
            "paymentRequest3d.browserInfo.acceptHeader" => $_SERVER['HTTP_ACCEPT'],
            "paymentRequest3d.md" => $returned_data['MD'],
            "paymentRequest3d.paResponse" => $returned_data['PaRes'],
            "paymentRequest3d.shopperIP" => $_SERVER['REMOTE_ADDR']
        ];
    }
}
