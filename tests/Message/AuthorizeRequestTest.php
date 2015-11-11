<?php

namespace Omnipay\Adyen\Message;

use Omnipay\Tests\TestCase;

/**
 * Class PaymentRequestTest
 * @package Omnipay\Adyen\Message
 */
class AuthorizeRequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new AuthorizeRequest(
            $this->getHttpClient(),
            $this->getHttpRequest()
        );

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $this->request->initialize(
            [
                'test_mode' => true,
                'username' => 'some_username',
                'password' => 'some_password',
                'merchant_account' => 'some_merchant_account',
                'first_name' => 'Sally',
                'last_name' => 'Jones',
                'social_security_number' => '818.098.848-10',
                'delivery_days' => '7',
                'shopper_email' => 'sjones@test.com',
                'shopper_reference' => '2468',
                'amount' => '1.99',
                'currency' => 'EUR',
                'transaction_reference' => '123'
            ]
            + $_SERVER
        );
    }

    public function testGetData()
    {
        $expected_results = [
            'action' => 'Payment.authorise',
            'paymentRequest.merchantAccount' => 'some_merchant_account',
            'paymentRequest.amount.currency' => 'EUR',
            'paymentRequest.amount.value' => 199,
            'paymentRequest.reference' => '123',
            "paymentRequest.shopperEmail" => 'sjones@test.com',
            "paymentRequest.shopperReference" => '2468',
            "paymentRequest.shopperIP" => '127.0.0.1',
            "paymentRequest.shopperName.firstName" => 'Sally',
            "paymentRequest.shopperName.lastName" => 'Jones',
            "paymentRequest.socialSecurityNumber" => '81809884810',
            "paymentRequest.selectedBrand" => 'boletobancario_santander',
            "paymentRequest.deliveryDate" => $this->request->formatDeliveryDate(),
        ];
        $this->assertEquals($this->request->getData(), $expected_results);
    }

    public function testGetDataWhenDeliveryDaysIsEmpty()
    {
        $this->request->setDeliveryDays('');
        $expected_results = [
            'action' => 'Payment.authorise',
            'paymentRequest.merchantAccount' => 'some_merchant_account',
            'paymentRequest.amount.currency' => 'EUR',
            'paymentRequest.amount.value' => 199,
            'paymentRequest.reference' => '123',
            "paymentRequest.shopperEmail" => 'sjones@test.com',
            "paymentRequest.shopperReference" => '2468',
            "paymentRequest.shopperIP" => '127.0.0.1',
            "paymentRequest.shopperName.firstName" => 'Sally',
            "paymentRequest.shopperName.lastName" => 'Jones',
            "paymentRequest.socialSecurityNumber" => '81809884810',
            "paymentRequest.selectedBrand" => 'boletobancario_santander',
            "paymentRequest.deliveryDate" => '',
        ];
        $this->assertEquals($this->request->getData(), $expected_results);
    }

    /**
    * @expectedException \Omnipay\Common\Exception\InvalidRequestException
    * @expectedExceptionMessage Cpf / Cnpj Error: Number Not Valid
    **/
    public function testGetDataThrowsErrorWhenSecurityNumberIsInvalid()
    {
        $this->request->setSocialSecurityNumber('121.131');
        $this->request->getData();
    }
}
