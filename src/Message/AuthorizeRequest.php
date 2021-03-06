<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Adyen\Helpers\CpfAndCnpjValidator;

/**
 * Class AuthorizeRequest
 * @package Omnipay\Adyen
 */
class AuthorizeRequest extends BaseRequest
{
    /**
     * Sets user's first name
     *
     * @param boolean $value
     */
    public function setFirstName($value)
    {
        $this->setParameter('first_name', $value);
    }

    /**
     * Returns user's first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->getParameter('first_name');
    }

    /**
     * Sets user's last name
     *
     * @param boolean $value
     */
    public function setLastName($value)
    {
        $this->setParameter('last_name', $value);
    }

    /**
     * Returns user's last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->getParameter('last_name');
    }

    /**
     * Validates that the provided social security number matches expect Cpf/Cnpj format
     *
     * @return boolean
     */
    public function validateSocialSecurityNumber()
    {
        return CpfAndCnpjValidator::isValid($this->getSocialSecurityNumber());
    }

    /**
     * Sets the shopper email
     *
     * @param string $value
     */
    public function setShopperEmail($value)
    {
        $this->setParameter('shopper_email', $value);
    }

    /**
     * Returns the shopper email
     *
     * @return string
     */
    public function getShopperEmail()
    {
        return $this->getParameter('shopper_email');
    }

    /**
     * Optional: Sets the delivery days.
     * This is the number of days a user will be have to complete the transaction.
     * If not set Adyen's System will set deliveryDate to today + 5 days
     *
     * @param string $value
     */
    public function setDeliveryDays($value)
    {
        $this->setParameter('delivery_days', $value);
    }

    /**
     * Returns delivery days
     *
     * @return string
     */
    public function getDeliveryDays()
    {
        return $this->getParameter('delivery_days');
    }

    /**
     * Returns ISO 8601 formatted deliveryDate
     *
     * @return string|null
     */
    public function formatDeliveryDate()
    {
        if ($this->getDeliveryDays() !== null && !empty($this->getDeliveryDays())) {
            return date(
                'Y-m-d\Th:i:s:u',
                mktime(
                    date("H"),
                    date("i"),
                    date("s"),
                    date("m"),
                    date("j") + (int) $this->getDeliveryDays(),
                    date("Y")
                )
            );
        } else {
            return null;
        }
    }

    /**
     * Defines a value for this field to specify how to process the transaction.
     * For boleto payments, the corresponding value usually follows this format:
     * boletobancario_<issuer_bank_name>
     *
     * @param string $value
     */
    public function setSelectedBrand($value)
    {
        $this->setParameter('selected_brand', $value);
    }

    /**
     * Returns delivery days
     *
     * @return string
     */
    public function getSelectedBrand()
    {
        return $this->getParameter('selected_brand');
    }

    /**
     * Returns the data required for the request
     * to be created
     *
     * @throws InvalidRequestException
     * @return array
     */
    public function getData()
    {
        $this->setResponseClass(AuthorizeResponse::class);
        if ($this->validateSocialSecurityNumber()) {
            $payment_params = [
               "paymentRequest.shopperEmail" => $this->getShopperEmail(),
               "paymentRequest.shopperReference" => $this->getShopperReference(),
               "paymentRequest.shopperIP" => $this->getRemoteAddr(),
               "paymentRequest.shopperName.firstName" => $this->getFirstName(),
               "paymentRequest.shopperName.lastName" => $this->getLastName(),
               "paymentRequest.socialSecurityNumber" => $this->getSocialSecurityNumber(),
               "paymentRequest.selectedBrand" => $this->getSelectedBrand(),
               "paymentRequest.deliveryDate" => $this->formatDeliveryDate() !== null
                    ? $this->formatDeliveryDate()
                    : ''
            ];
            $payment_params += parent::applyBaseRequestParams($payment_params);
            return $payment_params;
        } else {
            throw new InvalidRequestException(
                'Cpf / Cnpj Error: Number Not Valid'
            );
        }
    }
}
