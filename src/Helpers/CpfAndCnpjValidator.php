<?php
namespace Omnipay\Adyen\Helpers;

class CpfAndCnpjValidator
{
    private static $forbidden_cpf = [
        '00000000000',
        '11111111111',
        '22222222222',
        '33333333333',
        '44444444444',
        '55555555555',
        '66666666666',
        '77777777777',
        '88888888888',
        '99999999999',
    ];

    private static $forbidden_cnpj =  [
        '00000000000000',
        '11111111111111',
        '22222222222222',
        '33333333333333',
        '44444444444444',
        '55555555555555',
        '66666666666666',
        '77777777777777',
        '88888888888888',
        '99999999999999',
    ];

    /**
     * @param $value
     * @return bool
     */
    public static function isValid($value)
    {
        if (!empty($value)) {
            $length = strlen($value);

            if (!in_array($length, [11, 14])) {
                return false;
            } else {
                if ($length == 11) {
                    return static::isValidCpf($value);
                } else {
                    return static::isValidCnpj($value);
                }
            }
        }
        return false;
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isValidCpf($value)
    {
        if (in_array($value, self::$forbidden_cpf)) {
            return false;
        }

        $cpf_valid = substr($value, 0, 9);
        $sum = 0;
        $n = 11;
        for ($i = 0; $i <= 9; $i++) {
            $n = $n - 1;
            $sum = $sum + (substr($cpf_valid, $i, 1) * $n);
        };

        $rest = $sum % 11;
        if ($rest < 2) {
            $cpf_valid .= 0;
        } else {
            $cpf_valid = $cpf_valid . (11 - $rest);
        };

        // Second part
        $sum = 0;
        $n = 12;
        for ($i = 0; $i <= 10; $i++) {
            $n = $n - 1;
            $sum = $sum + (substr($cpf_valid, $i, 1) * $n);
        };

        $rest = $sum % 11;
        if ($rest < 2) {
            $cpf_valid .= 0;
        } else {
            $cpf_valid = $cpf_valid . (11 - $rest);
        }
        if ($cpf_valid != $value) {
            return false;
        }

        return true;
    }

    /**
     * @param $value
     * @return bool
     */
    public static function isValidCnpj($value)
    {
        if (in_array($value, self::$forbidden_cnpj)) {
            return false;
        }


        $cnpj = $value;

        $k = 6;
        $sum1 = 0;
        $sum2 = 0;
        for ($i = 0; $i < 13; $i++) {
            $k = $k == 1 ? 9 : $k;

            $sum2 += ($cnpj{$i} * $k);
            $k--;
            if ($i < 12) {
                if ($k == 1) {
                    $k = 9;
                    $sum1 += ($cnpj{$i} * $k);
                    $k = 1;
                } else {
                    $sum1 += ($cnpj{$i} * $k);
                }
            }
        }

        $digit1 = $sum1 % 11 < 2 ? 0 : 11 - $sum1 % 11;
        $digit2 = $sum2 % 11 < 2 ? 0 : 11 - $sum2 % 11;

        if ($cnpj{12} != $digit1 && $cnpj{13} != $digit2) {
            return false;
        }

        return true;
    }
}
