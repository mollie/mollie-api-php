<p align="center">
  <img src="https://github.com/mollie/mollie-api-php/assets/7265703/140510a5-ede5-41bf-9d77-0d09b906e8f4" width="128" height="128"/>
</p>

<h1 align="center">Mollie API client for PHP</h1>

![mollie-api-php-header](https://github.com/mollie/mollie-api-php/assets/7265703/e79b7770-fe00-4dfe-bb8b-3d5ed221e329)

Accepting [iDEAL](https://www.mollie.com/payments/ideal/), [Apple Pay](https://www.mollie.com/payments/apple-pay), [Bancontact](https://www.mollie.com/payments/bancontact/), [SOFORT Banking](https://www.mollie.com/payments/sofort/), [Creditcard](https://www.mollie.com/payments/credit-card/), [SEPA Bank transfer](https://www.mollie.com/payments/bank-transfer/), [SEPA Direct debit](https://www.mollie.com/payments/direct-debit/), [PayPal](https://www.mollie.com/payments/paypal/), [Belfius Direct Net](https://www.mollie.com/payments/belfius/), [KBC/CBC](https://www.mollie.com/payments/kbc-cbc/), [paysafecard](https://www.mollie.com/payments/paysafecard/), [ING Home'Pay](https://www.mollie.com/payments/ing-homepay/), [Giropay](https://www.mollie.com/payments/giropay/), [EPS](https://www.mollie.com/payments/eps/), [Przelewy24](https://www.mollie.com/payments/przelewy24/), [Postepay](https://www.mollie.com/en/payments/postepay), [In3](https://www.mollie.com/payments/in3/), [Klarna](https://www.mollie.com/payments/klarna-pay-later/) ([Pay now](https://www.mollie.com/payments/klarna-pay-now/), [Pay later](https://www.mollie.com/payments/klarna-pay-later/), [Slice it](https://www.mollie.com/payments/klarna-slice-it/), [Pay in 3](https://www.mollie.com/payments/klarna-pay-in-3/)), [Giftcard](https://www.mollie.com/payments/gift-cards/) and [Voucher](https://www.mollie.com/en/payments/meal-eco-gift-vouchers) online payments without fixed monthly costs or any punishing registration procedures. Just use the Mollie API to receive payments directly on your website or easily refund transactions to your customers.

[![Build Status](https://github.com/mollie/mollie-api-php/workflows/tests/badge.svg)](https://github.com/mollie/mollie-api-php/actions)
[![Latest Stable Version](https://poser.pugx.org/mollie/mollie-api-php/v/stable)](https://packagist.org/packages/mollie/mollie-api-php)
[![Total Downloads](https://poser.pugx.org/mollie/mollie-api-php/downloads)](https://packagist.org/packages/mollie/mollie-api-php)

## Requirements ##
To use the Mollie API client, the following things are required:

+ Get yourself a free [Mollie account](https://www.mollie.com/signup). No sign up costs.
+ Now you're ready to use the Mollie API client in test mode.
+ Follow [a few steps](https://www.mollie.com/dashboard/?modal=onboarding) to enable payment methods in live mode, and let us handle the rest.
+ PHP >= 7.0
+ Up-to-date OpenSSL (or other SSL/TLS toolkit)

For leveraging [Mollie Connect](https://docs.mollie.com/oauth/overview) (advanced use cases only), we recommend also installing our [OAuth2 client](https://github.com/mollie/oauth2-mollie-php).

## Composer Installation ##

By far the easiest way to install the Mollie API client is to require it with [Composer](http://getcomposer.org/doc/00-intro.md).

    $ composer require mollie/mollie-api-php:^2.0

    {
        "require": {
            "mollie/mollie-api-php": "^2.0"
        }
    }

The version of the API client corresponds to the version of the API it implements. Check the [notes on migration](https://docs.mollie.com/migrating-v1-to-v2) to see what changes you need to make if you want to start using a newer API version.


## Manual Installation ##
If you're not familiar with using composer we've added a ZIP file to the releases containing the API client and all the packages normally installed by composer.
Download the ``mollie-api-php.zip`` from the [releases page](https://github.com/mollie/mollie-api-php/releases).

Include the ``vendor/autoload.php`` as shown in [Initialize example](https://github.com/mollie/mollie-api-php/blob/master/examples/initialize.php).

## Getting started ##

Initializing the Mollie API client, and setting your API key.

```php
$mollie = new \Mollie\Api\MollieApiClient();
$mollie->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
```

With the `MollieApiClient` you can now access any of the following endpoints by selecting them as a property of the client:

| API | Resource | Code             | Link to Endpoint file                                                            |
| -----------------------  | -----------------------  | --------------------------  | --------------------------------------------------------------------------  |
| **[Balances API](https://docs.mollie.com/reference/v2/balances-api/overview)**       | Balance | `$mollie->balances`        | [BalanceEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/BalanceEndpoint.php)            |
| | Balance Report | `$mollie->balanceReports`  | [BalanceReportEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/BalanceReportEndpoint.php) |
| | Balance Transaction | `$mollie->balanceTransactions` | [BalanceTransactionEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/BalanceTransactionEndpoint.php) |
| **[Chargebacks API](https://docs.mollie.com/reference/v2/chargebacks-api/overview)** | Chargeback |`$mollie->chargebacks` | [ChargebackEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/ChargebackEndpoint.php) |
| | Payment Chargeback | `$mollie->paymentChargebacks` | [PaymentChargebackEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/PaymentChargebackEndpoint.php) |
| **[Clients API](https://docs.mollie.com/reference/v2/clients-api/overview)** | Client | `$mollie->clients` | [ClientEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/ClientEndpoint.php) |
| **[Client Links API](https://docs.mollie.com/reference/v2/client-links-api/overview)** | Client Link | `$mollie->clientLinks` | [ClientLinkEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/ClientLinkEndpoint.php) |
| **[Customers API](https://docs.mollie.com/reference/v2/customers-api/overview)** | Customer | `$mollie->customers` | [CustomerEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/CustomerEndpoint.php) |
| | Customer Payment | `$mollie->customerPayments` | [CustomerPaymentsEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/CustomerPaymentsEndpoint.php) |
| **[Invoices API](https://docs.mollie.com/reference/v2/invoices-api/overview)** | Invoice | `$mollie->invoices` | [InvoiceEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/InvoiceEndpoint.php) |
| **[Mandates API](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/MandateEndpoint.php)** | Mandate | `$mollie->mandates` | [MandateEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/MandateEndpoint.php) |
| **[Methods API](https://docs.mollie.com/reference/v2/methods-api/overview)** | Method | `$mollie->methods` | [MethodEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/MethodEndpoint.php) |
| **[Onboarding API](https://docs.mollie.com/reference/v2/onboarding-api/overview)** | Onboarding |`$mollie->onboarding` | [OnboardingEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/OnboardingEndpoint.php) |
| **[Orders API](https://docs.mollie.com/reference/v2/orders-api/overview)** | Order | `$mollie->orders` | [OrderEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/OrderEndpoint.php) |
| | Order Line | `$mollie->orderLines` | [OrderLineEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/OrderLineEndpoint.php) |
| | Order Payment | `$mollie->orderPayments` | [OrderPaymentEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/OrderPaymentEndpoint.php) |
| **[Organizations API](https://docs.mollie.com/reference/v2/organizations-api/overview)** | Organization | `$mollie->organizations` | [OrganizationEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/OrganizationEndpoint.php) |
| | Organization Partner | `$mollie->organizationPartners` | [OrganizationPartnerEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/OrganizationPartnerEndpoint.php) |
| **[Captures API](https://docs.mollie.com/reference/v2/captures-api/overview)** | Payment Captures | `$mollie->organizations` | [PaymentCaptureEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/PaymentCaptureEndpoint.php) |
| **[Payments API](https://docs.mollie.com/reference/v2/payments-api/overview)** | Payment | `$mollie->payments` | [PaymentEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/PaymentEndpoint.php) |
| | Payment Route | `$mollie->paymentRoutes` | [PaymentRouteEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/PaymentRouteEndpoint.php) |
| **[Payment links API](https://docs.mollie.com/reference/v2/payment-links-api/overview)** | Payment Link | `$mollie->paymentLinks` | [PaymentLinkEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/PaymentLinkEndpoint.php) |
| **[Permissions API](https://docs.mollie.com/reference/v2/permissions-api/overview)** | Permission | `$mollie->permissions` | [PermissionEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/PermissionEndpoint.php) |
| **[Profile API](https://docs.mollie.com/reference/v2/profiles-api/overview)** | Profile | `$mollie->profiles` | [ProfileEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/ProfileEndpoint.php) |
| | Profile Method | `$mollie->profileMethods` | [ProfileMethodEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/ProfileMethodEndpoint.php) |
| **[Refund API](https://docs.mollie.com/reference/v2/refunds-api/overview)** | Refund | `$mollie->refunds` | [RefundEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/RefundEndpoint.php) |
| | Order Refund | `$mollie->orderRefunds` | [OrderRefundEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/OrderRefundEndpoint.php) |
| | Payment Refund | `$mollie->paymentRefunds` | [PaymentRefundEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/PaymentRefundEndpoint.php) |
| **[Settlements API](https://docs.mollie.com/reference/v2/settlements-api/overview)** | Settlement | `$mollie->settlements` | [SettlementsEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/SettlementsEndpoint.php) |
| | Settlement Capture | `$mollie->settlementCaptures` | [SettlementCaptureEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/SettlementCaptureEndpoint.php) |
| | Settlement Chargeback | `$mollie->settlementChargebacks` | [SettlementChargebackEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/SettlementChargebackEndpoint.php) |
| | Settlement Payment | `$mollie->settlementPayments` | [SettlementPaymentEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/SettlementPaymentEndpoint.php) |
| | Settlement Refund | `$mollie->settlementRefunds` | [SettlementRefundEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/SettlementRefundEndpoint.php) |
| **[Shipments API](https://docs.mollie.com/reference/v2/shipments-api/overview)** | Shipment | `$mollie->shipments` | [ShipmentEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/ShipmentEndpoint.php) |
| **[Subscriptions API](https://docs.mollie.com/reference/v2/subscriptions-api/overview)** | Subscription | `$mollie->subscriptions` | [SubscriptionEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/SubscriptionEndpoint.php) |
| **[Terminal API](https://docs.mollie.com/reference/v2/terminals-api/overview)** | Terminal | `$mollie->terminals` | [TerminalEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/TerminalEndpoint.php) |
| **[Wallets API](https://docs.mollie.com/reference/v2/wallets-api/overview)** | Wallet | `$mollie->wallets` | [WalletEndpoint](https://github.com/mollie/mollie-api-php/blob/master/src/Endpoints/WalletEndpoint.php) |

Find our full documentation online on [docs.mollie.com](https://docs.mollie.com).

## Payments ##
### Receiving Payments Workflow ###
To successfully receive a payment, these steps should be implemented:

1. Use the Mollie API client to create a payment with the requested amount, currency, description and optionally, a payment method. It is important to specify a unique redirect URL where the customer is supposed to return to after the payment is completed.

2. Immediately after the payment is completed, our platform will send an asynchronous request to the configured webhook to allow the payment details to be retrieved, so you know when exactly to start processing the customer's order.

3. The customer returns, and should be satisfied to see that the order was paid and is now being processed.

### Creating Payments ###
```php
$payment = $mollie->payments->create([
    "amount" => [
        "currency" => "EUR",
        "value" => "10.00"
    ],
    "description" => "My first API payment",
    "redirectUrl" => "https://webshop.example.org/order/12345/",
    "webhookUrl"  => "https://webshop.example.org/mollie-webhook/",
]);
```
_After creation, the payment id is available in the `$payment->id` property. You should store this id with your order._

After storing the payment id you can send the customer to the checkout using the `$payment->getCheckoutUrl()`.

```php
header("Location: " . $payment->getCheckoutUrl(), true, 303);
```
_This header location should always be a GET, thus we enforce 303 http response code_

For a payment create example, see [Example - New Payment](https://github.com/mollie/mollie-api-php/blob/master/examples/payments/create-payment.php).

#### Multicurrency ####
Since 2.0 it is now possible to create non-EUR payments for your customers.
A full list of available currencies can be found [in our documentation](https://docs.mollie.com/guides/multicurrency).

```php
$payment = $mollie->payments->create([
    "amount" => [
        "currency" => "USD",
        "value" => "10.00"
    ],
    "description" => "Order #12345",
    "redirectUrl" => "https://webshop.example.org/order/12345/",
    "webhookUrl"  => "https://webshop.example.org/mollie-webhook/",
]);
```
_After creation, the `settlementAmount` will contain the EUR amount that will be settled on your account._

### Retrieving Payments ###
We can use the `$payment->id` to retrieve a payment and check if the payment `isPaid`.

```php
$payment = $mollie->payments->get($payment->id);

if ($payment->isPaid())
{
    echo "Payment received.";
}
```

Or retrieve a collection of payments.

```php
$payments = $mollie->payments->page();
```

For an extensive example of listing payments with the details and status, see [Example - List Payments](https://github.com/mollie/mollie-api-php/blob/master/examples/payments/list-payments.php).

### Payment webhook ###
When the status of a payment changes the `webhookUrl` we specified in the creation of the payment will be called.
There we can use the `id` from our POST parameters to check te status and act upon that, see [Example - Webhook](https://github.com/mollie/mollie-api-php/blob/master/examples/payments/webhook.php).


### Fully integrated iDEAL payments ###

If you want to fully integrate iDEAL payments in your web site, some additional steps are required. First, you need to
retrieve the list of issuers (banks) that support iDEAL and have your customer pick the issuer he/she wants to use for
the payment.

Retrieve the iDEAL method and include the issuers

```php
$method = $mollie->methods->get(\Mollie\Api\Types\PaymentMethod::IDEAL, ["include" => "issuers"]);
```

_`$method->issuers` will be a list of objects. Use the property `$id` of this object in the
 API call, and the property `$name` for displaying the issuer to your customer. For a more in-depth example, see [Example - iDEAL payment](https://github.com/mollie/mollie-api-php/blob/master/examples/payments/create-ideal-payment.php)._

Create a payment with the selected issuer:

```php
$payment = $mollie->payments->create([
    "amount" => [
        "currency" => "EUR",
        "value" => "10.00"
    ],
    "description" => "My first API payment",
    "redirectUrl" => "https://webshop.example.org/order/12345/",
    "webhookUrl"  => "https://webshop.example.org/mollie-webhook/",
    "method"      => \Mollie\Api\Types\PaymentMethod::IDEAL,
    "issuer"      => $selectedIssuerId, // e.g. "ideal_INGBNL2A"
]);
```

_The `_links` property of the `$payment` object will contain an object `checkout` with a `href` property, which is a URL that points directly to the online banking environment of the selected issuer.
A short way of retrieving this URL can be achieved by using the `$payment->getCheckoutUrl()`._

### Refunding payments ###

The API also supports refunding payments. Note that there is no confirmation and that all refunds are immediate and
definitive. refunds are supported for all methods except for paysafecard and gift cards.

```php
$payment = $mollie->payments->get($payment->id);

// Refund € 2 of this payment
$refund = $payment->refund([
    "amount" => [
        "currency" => "EUR",
        "value" => "2.00"
    ]
]);
```

For a working example, see [Example - Refund payment](https://github.com/mollie/mollie-api-php/blob/master/examples/payments/refund-payment.php).

## Enabling debug mode

When debugging it can be convenient to have the submitted request available on the `ApiException`.

In order to prevent leaking sensitive request data into your local application logs, debugging is disabled by default.

To enable debugging and inspect the request:

```php
/** @var $mollie \Mollie\Api\MollieApiClient */
$mollie->enableDebugging();

try {
    $mollie->payments->get('tr_12345678');
} catch (\Mollie\Api\Exceptions\ApiException $exception) {
    $request = $exception->getRequest();
}
```

If you're logging the `ApiException`, the request will also be logged. Make sure to not retain any sensitive data in
these logs and clean up after debugging.

To disable debugging again:

```php
/** @var $mollie \Mollie\Api\MollieApiClient */
$mollie->disableDebugging();
```

Note that debugging is only available when using the default Guzzle http adapter (`Guzzle6And7MollieHttpAdapter`).

## API documentation ##
If you wish to learn more about our API, please visit the [Mollie Developer Portal](https://www.mollie.com/developers). API Documentation is available in English.

## Want to help us make our API client even better? ##

Want to help us make our API client even better? We take [pull requests](https://github.com/mollie/mollie-api-php/pulls?utf8=%E2%9C%93&q=is%3Apr), sure. But how would you like to contribute to a technology oriented organization? Mollie is hiring developers and system engineers. [Check out our vacancies](https://jobs.mollie.com/) or [get in touch](mailto:personeel@mollie.com).

## License ##
[BSD (Berkeley Software Distribution) License](https://opensource.org/licenses/bsd-license.php).
Copyright (c) 2013-2018, Mollie B.V.

## Support ##
Contact: [www.mollie.com](https://www.mollie.com) — info@mollie.com — +31 20 820 20 70
