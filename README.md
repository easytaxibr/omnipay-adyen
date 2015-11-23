# Omnipay: Adyen

**Adyen driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Adyen support for Omnipay and uses short array syntax and thus require PHP 5.4+.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "vivastreet/omnipay-adyen": "^1"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* Adyen

### Credit Card Purchase:
A standard card payment using [Adyen CSE](https://docs.adyen.com/display/TD/Client+Side+Encryption)
```PHP
$gateway = Omnipay::create('Adyen');
$gateway->setUsername('username');
$gateway->setPassword('password');
$gateway->setMerchantAccount('merchant_account');

$card = [
    'encrypted_card_data' => 'encrypted_card_data', //From adyen CSE
    'first_name' => 'MyName',
    'last_name' => 'MySurname',
    'billing_address1' => 'MyStreet1',
    'billing_address2' => 'MyStreet2',
    'billing_post_code' => 'MyPostalCode',
    'billing_city' => 'MyCity',
    'billing_state' => 'MyState',
    'billing_country' => 'MyCountry',
    'email' => 'MyEmail@Example.com',
    'shopper_reference' => 'MyReference'
];

$response = $gateway->purchase(
    [
        'amount' => '1.99',
        'currency' => 'EUR',
        'transaction_reference' => '123',
        'card' => $card
    ]
)->send();

if ($response->isSuccessful()) {
    echo "Code: {$response->getCode()} \n";
    echo "Reference: {$response->getTransactionId()} \n";
} else {
    echo $response->getMessage();
    echo "Failed\n";
}
```

### One Click Credit Card Purchase Initial Payment:
A One Click card payment using [Adyen CSE](https://docs.adyen.com/display/TD/Client+Side+Encryption)
```PHP
$gateway = Omnipay::create('Adyen');
$gateway->setUsername('username');
$gateway->setPassword('password');
$gateway->setMerchantAccount('merchant_account');

$card = [
    'encrypted_card_data' => 'encrypted_card_data', //From adyen CSE
    'first_name' => 'MyName',
    'last_name' => 'MySurname',
    'billing_address1' => 'MyStreet1',
    'billing_address2' => 'MyStreet2',
    'billing_post_code' => 'MyPostalCode',
    'billing_city' => 'MyCity',
    'billing_state' => 'MyState',
    'billing_country' => 'MyCountry',
    'email' => 'MyEmail@Example.com',
    'shopper_reference' => 'MyReference'
];

$response = $gateway->purchase(
    [
        'type' => PaymentRequest::ONE_CLICK,
        'amount' => '1.99',
        'currency' => 'EUR',
        'transaction_reference' => '123',
        'card' => $card
    ]
)->send();

if ($response->isSuccessful()) {
    echo "Code: {$response->getCode()} \n";
    echo "Reference: {$response->getTransactionId()} \n";
} else {
    echo $response->getMessage();
    echo "Failed\n";
}
```

### One Click Credit Card Purchase Successive Payment:
```PHP
$gateway = Omnipay::create('Adyen');
$gateway->setUsername('username');
$gateway->setPassword('password');
$gateway->setMerchantAccount('merchant_account');

$card_response = $gateway->getCard(
    [
        'shopper_reference' => '789',
        'contract_type' => 'ONECLICK'
    ]
)->send();

if ($card_response->isSuccessful()) {
    $card = new CreditCard(
        [
            'cvv' => '737',
            'email' => $card_response->getShopperEmail(),
            'shopper_reference' => $card_response->getShopperReference()
        ]
    );

    $response = $gateway->purchase(
        [
            'type' => PaymentRequest::ONE_CLICK,
            'recurring_detail_reference' => $card_response->getRecurringDetailReference(),
            'amount' => '1.99',
            'currency' => 'EUR',
            'transaction_reference' => '123',
            'card' => $card
        ]
    )->send();

    if ($response->isSuccessful()) {
        echo "Code: {$response->getCode()} \n";
        echo "Reference: {$response->getTransactionId()} \n";
    } else {
        echo $response->getMessage();
        echo "Failed\n";
    }
}
```
### Recurring Credit Card Purchase Initial Payment:
A recurring card payment using [Adyen CSE](https://docs.adyen.com/display/TD/Client+Side+Encryption)

```PHP
$gateway = Omnipay::create('Adyen');
$gateway->setUsername('username');
$gateway->setPassword('password');
$gateway->setMerchantAccount('merchant_account');

$card = [
    'encrypted_card_data' => 'encrypted_card_data', //From adyen CSE
    'first_name' => 'MyName',
    'last_name' => 'MySurname',
    'billing_address1' => 'MyStreet1',
    'billing_address2' => 'MyStreet2',
    'billing_post_code' => 'MyPostalCode',
    'billing_city' => 'MyCity',
    'billing_state' => 'MyState',
    'billing_country' => 'MyCountry',
    'email' => 'MyEmail@Example.com',
    'shopper_reference' => 'MyReference'
];


$response = $gateway->purchase(
    [
        'type' => PaymentRequest::RECURRING,
        'amount' => '1.99',
        'currency' => 'EUR',
        'transaction_reference' => '123',
        'card' => $card
    ]
)->send();

if ($response->isSuccessful()) {
    echo "Code: {$response->getCode()} \n";
    echo "Reference: {$response->getTransactionId()} \n";
} else {
    echo $response->getMessage();
    echo "Failed\n";
}
```
### Recurring Credit Card Purchase Successive Payment:
```PHP
$gateway = Omnipay::create('Adyen');
$gateway->setUsername('username');
$gateway->setPassword('password');
$gateway->setMerchantAccount('merchant_account');

$card_response = $gateway->getCard(
    [
        'shopper_reference' => '456',
        'contract_type' => 'RECURRING'
    ]
)->send();

if ($card_response->isSuccessful()) {
    $card = new CreditCard(
        [
            'email' => $card_response->getShopperEmail(),
            'shopper_reference' => $card_response->getShopperReference()
        ]
    );

    $response = $gateway->purchase(
        [
            'type' => PaymentRequest::RECURRING,
            'recurring_detail_reference' => $card_response->getRecurringDetailReference(),
            'amount' => '1.99',
            'currency' => 'EUR',
            'transaction_reference' => '123',
            'card' => $card
        ]
    )->send();

    if ($response->isSuccessful()) {
        echo "Code: {$response->getCode()} \n";
        echo "Reference: {$response->getTransactionId()} \n";
    } else {
        echo $response->getMessage();
        echo "Failed\n";
    }
}
```
For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/vivastreet/omnipay-adyen/issues),
or better yet, fork the library and submit a pull request.