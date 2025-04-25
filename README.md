<p align="center">
  <img src="https://github.com/mollie/mollie-api-php/assets/7265703/140510a5-ede5-41bf-9d77-0d09b906e8f4" width="128" height="128"/>
</p>

<h1 align="center">Mollie API client for PHP</h1>

<div align="center">

[![Build Status](https://github.com/mollie/mollie-api-php/workflows/tests/badge.svg)](https://github.com/mollie/mollie-api-php/actions)
[![Latest Stable Version](https://poser.pugx.org/mollie/mollie-api-php/v/stable)](https://packagist.org/packages/mollie/mollie-api-php)
[![Total Downloads](https://poser.pugx.org/mollie/mollie-api-php/downloads)](https://packagist.org/packages/mollie/mollie-api-php)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mollie/mollie-api-php/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mollie/mollie-api-php/actions?query=workflow%3A"Fix+Code+Style"+branch%3Amain)

</div>

Accepting [iDEAL](https://www.mollie.com/payments/ideal/), [Apple Pay](https://www.mollie.com/payments/apple-pay), [Bancontact](https://www.mollie.com/payments/bancontact/), [SOFORT Banking](https://www.mollie.com/payments/sofort/), [Creditcard](https://www.mollie.com/payments/credit-card/), [SEPA Bank transfer](https://www.mollie.com/payments/bank-transfer/), [SEPA Direct debit](https://www.mollie.com/payments/direct-debit/), [PayPal](https://www.mollie.com/payments/paypal/), [Belfius Direct Net](https://www.mollie.com/payments/belfius/), [KBC/CBC](https://www.mollie.com/payments/kbc-cbc/), [paysafecard](https://www.mollie.com/payments/paysafecard/), [ING Home'Pay](https://www.mollie.com/payments/ing-homepay/), [Giropay](https://www.mollie.com/payments/giropay/), [EPS](https://www.mollie.com/payments/eps/), [Przelewy24](https://www.mollie.com/payments/przelewy24/), [Postepay](https://www.mollie.com/en/payments/postepay), [In3](https://www.mollie.com/payments/in3/), [Klarna](https://www.mollie.com/payments/klarna-pay-later/) ([Pay now](https://www.mollie.com/payments/klarna-pay-now/), [Pay later](https://www.mollie.com/payments/klarna-pay-later/), [Slice it](https://www.mollie.com/payments/klarna-slice-it/), [Pay in 3](https://www.mollie.com/payments/klarna-pay-in-3/)), [Giftcard](https://www.mollie.com/payments/gift-cards/) and [Voucher](https://www.mollie.com/en/payments/meal-eco-gift-vouchers) online payments without fixed monthly costs or any punishing registration procedures. Just use the Mollie API to receive payments directly on your website or easily refund transactions to your customers.

## Requirements ##
To use the Mollie API client, the following things are required:

+ Get yourself a free [Mollie account](https://www.mollie.com/signup). No sign up costs.
+ Now you're ready to use the Mollie API client in test mode.
+ Follow [a few steps](https://www.mollie.com/dashboard/?modal=onboarding) to enable payment methods in live mode, and let us handle the rest.
+ PHP >= 7.4
+ cUrl >= 7.19.4
+ Up-to-date OpenSSL (or other SSL/TLS toolkit)

For leveraging [Mollie Connect](https://docs.mollie.com/oauth/overview) (advanced use cases only), we recommend also installing our [OAuth2 client](https://github.com/mollie/oauth2-mollie-php).

## Installation ##
### Using Composer ###

The easiest way to install the Mollie API client is by using [Composer](http://getcomposer.org/doc/00-intro.md). You can require it with the following command:

```bash
composer require mollie/mollie-api-php
```

## Usage ##

Initializing the Mollie API client, and setting your API key.

```php
$mollie = new \Mollie\Api\MollieApiClient();
$mollie->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
```

Find our full documentation online on [docs.mollie.com](https://docs.mollie.com).

#### Example usage ####
```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;

/** @var Mollie\Api\Resources\Payment $payment */
$payment = $mollie->send(new CreatePaymentRequest(
    description: 'My first API payment',
    amount: new Money('EUR', '10.00'),
    redirectUrl: 'https://webshop.example.org/order/12345/',
    webhookUrl: 'https://webshop.example.org/mollie-webhook/'
));
```

## Documentation
For an in-depth understanding of our API, please explore the [Mollie Developer Portal](https://www.mollie.com/developers). Our API documentation is available in English.

For detailed documentation about using this PHP client, see the following guides:

- [Endpoint Collections](docs/endpoint-collections.md) - Learn how to interact with all available API endpoints.
- [HTTP Adapters](docs/http-adapters.md) - Information on customizing HTTP communication.
- [Idempotency](docs/idempotency.md) - Best practices and setup for idempotent requests.
- [Payments](docs/payments.md) - Comprehensive guide on handling payments.
- [Requests](docs/requests.md) - Overview and usage of request objects in the API client.
- [Responses](docs/responses.md) - Handling and understanding responses from the API.
- [Testing](docs/testing.md) - Guidelines for testing with the Mollie API client.
- [Debugging](docs/debugging.md) - How to debug API requests and responses safely.

These guides provide in-depth explanations and examples for advanced usage of the client.

## Recipes

The Mollie API client comes with a variety of recipes to help you understand how to implement various API features. These recipes are a great resource for learning how to integrate Mollie payments into your application.

Here are some of the key recipes included:

- **Payments**: Demonstrates how to handle various payment scenarios.
  - [Create a payment](docs/recipes/payments/create-payment.md)
  - [Create a capturable payment](docs/recipes/payments/create-capturable-payment.md)
  - [Handle webhooks](docs/recipes/payments/handle-webhook.md)
  - [Refund payments](docs/recipes/payments/refund-payment.md)

- **Customers**: Shows how to manage customers and their payments.
  - [Manage customers](docs/recipes/customers/manage-customers.md)
  - [Customer payments](docs/recipes/customers/customer-payments.md)

- **Subscriptions and Recurring Payments**:
  - [Manage mandates](docs/recipes/mandates/manage-mandates.md)
  - [Manage subscriptions](docs/recipes/subscriptions/manage-subscriptions.md)

For a full list of recipes, please refer to the [recipes directory](docs/recipes/).

These recipes are designed to help you integrate Mollie into your application. Make sure to use your test API keys when testing the integration.

## Upgrading

Please see [UPGRADING](UPGRADING.md) for details.

## Contributing to Our API Client ##
Would you like to contribute to improving our API client? We welcome [pull requests](https://github.com/mollie/mollie-api-php/pulls?utf8=%E2%9C%93&q=is%3Apr). But, if you're interested in contributing to a technology-focused organization, Mollie is actively recruiting developers and system engineers. Discover our current [job openings](https://jobs.mollie.com/) or [reach out](mailto:personeel@mollie.com).

## License ##
[BSD (Berkeley Software Distribution) License](https://opensource.org/licenses/bsd-license.php).
Copyright (c) 2013-2018, Mollie B.V.

## Support ##
Contact: [www.mollie.com](https://www.mollie.com) — info@mollie.com — +31 20 820 20 70
