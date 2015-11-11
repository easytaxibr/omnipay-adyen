<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Class NotificationRequest
 * @package Omnipay\Adyen\Message
 */
class NotificationRequest extends AbstractRequest
{
    use GatewayAccessorTrait;

    /**
     * Sets the raw data recieved in the notification
     *
     * @param string $raw_data
     */
    public function setRawData($raw_data)
    {
        $this->setParameter('raw_data', $raw_data);
    }

    /**
     * Returns the raw notification data
     *
     * @return string
     */
    public function getRawData()
    {
        return $this->getParameter('raw_data');
    }

    /**
     * Returns the data required for the request
     * to be created
     *
     * @return array
     */
    public function getData()
    {
        $request_data = [];
        parse_str($this->getRawData(), $request_data);
        return $request_data;
    }

    /**
     * Creates a NotificationResponse for the request
     *
     * @param array $data
     * @return NotificationResponse
     */
    public function sendData($data)
    {
        return $this->response = new NotificationResponse($this, $data);
    }
}
