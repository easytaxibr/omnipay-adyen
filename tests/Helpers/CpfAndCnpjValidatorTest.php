<?php
namespace Omnipay\Adyen\Helpers;

use Omnipay\Adyen\Helpers\CpfAndCnpjValidator;
use Omnipay\Tests\TestCase;

class CpfAndCnpjValidatorTest extends TestCase
{
    public function isValidDataProvider()
    {
        return [
            ['95481629461'],
            ['30583253555'],
            ['71479260720'],
            ['45535558000100'],
            ['81168126000171'],
            ['63475411000153'],
            ['62080821000132'],
            ['12345678909']
        ];
    }

    public function isInvalidDataProvider()
    {
        return [
            ['12335698765434'],
            ['GB82WEST-123456987654'],
            ['3456987654'],
            ['88888888888888'],
            ['77777777777'],
            ['777777'],
            ['63475411000953'],
            ['62080821000'],
            ['']
        ];
    }

    /**
     * @dataProvider isValidDataProvider
     */
    public function testIsValidReturnsTrueForValidFormat($number)
    {
        $this->assertTrue(CpfAndCnpjValidator::isValid($number));
    }

    /**
     * @dataProvider isInvalidDataProvider
     */
    public function testIsInvalidReturnsFalseForInValidFormat($number)
    {
        $this->assertFalse(CpfAndCnpjValidator::isValid($number));
    }
    public function testIsValidReturnsFalseForTooLong()
    {
        $this->assertFalse(CpfAndCnpjValidator::isValid('12335698765434'));
    }

    public function testIsValidReturnsFalseForInvalidChar()
    {
        $this->assertFalse(CpfAndCnpjValidator::isValid('GB82WEST-123456987654'));
    }

    public function testIsValidReturnsFalseForTooShortChar()
    {
        $this->assertFalse(CpfAndCnpjValidator::isValid('3456987654'));
    }
}
