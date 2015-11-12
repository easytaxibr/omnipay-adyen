<?php

namespace Omnipay\Adyen\Message;

// use Omnipay\Adyen\Message\BaseRequest;

/**
 * Class SecureRequest - Used to make a 3D secure request
 * @package Omnipay\Adyen\Message
 */
class SecureRequest extends BaseRequest
{
    /**
     * Returns the data required for the request
     * to be created
     *
     * @return array
     */
    public function getData()
    {
        $this->setResponseClass(PaymentResponse::class);
        $returned_data = $this->httpRequest->request->all();
        return [
            "action" => "Payment.authorise3d",
            "paymentRequest3d.merchantAccount" => $this->getMerchantAccount(),
            "paymentRequest3d.browserInfo.userAgent" =>
                $this->getHttpUserAgent(),
            "paymentRequest3d.browserInfo.acceptHeader" =>
                $this->getHttpAccept(),
            "paymentRequest3d.md" => $returned_data['MD'],
            "paymentRequest3d.paResponse" => $returned_data['PaRes'],
            "paymentRequest3d.shopperIP" => $this->getRemoteAddr()
        ];
    }
}
